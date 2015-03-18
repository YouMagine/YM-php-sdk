<?php

if (!function_exists('curl_version')) {
    die('It seems cURL for PHP is not properly installed or configured. '
       .'Please check the installation instructions for '
       .'<a href="http://curl.haxx.se/download.html" target="_blank">cURL</a> and/or the '
       .'<a href="http://php.net/manual/en/curl.setup.php" target="_blank">PHP cURL extension</a>');
}

switch (session_status()) {
    case PHP_SESSION_DISABLED:
        die('API example requires a working session!');
        break;
    case PHP_SESSION_NONE:
        session_start();
        break;
}

class HttpClient {

    protected $request = null;
    protected $response = null;
    private $curl;

    protected $protocol;
    protected $host;
    protected $subDomain;
    protected $virtualDirectory;
    protected $extension = '';

    public static function createUploadFile ($filepath, $filename, $mimetype) {
        if (function_exists('curl_file_create')) {
            return curl_file_create($filepath, $mimetype, $filename);
        }

        return "@$filepath;filename=$filename;type=$mimetype";
    }
    
    public function __construct(array $options) {
        $options += array(
            'host'              => null,
            'https'             => false,
            'virtualDirectory'  => ''
        );
        
        $this->host = $options['host'];
        $this->protocol = ($options['https'] ? 'https' : 'http');
        $this->virtualDirectory = $options['virtualDirectory'];
    }
    
    public function getLastResponse () {
        return $this->response;
    }
    
    public function getLastRequest () {
        return $this->request;
    }

    public function debug () {
        print_r($this->getLastRequest());
        print_r($this->getLastResponse());
    }
    
    protected function mandatoryQueryParameters () {
        return array();
    }
    
    protected function get ($resource, array $query = array()) {
        return $this->request('GET', $resource, array(), $query);
    }
    
    protected function post ($resource, array $params = array()) {
        return $this->request('POST', $resource, $params, array());
    }
    
    protected function put ($resource, array $params = array()) {
        return $this->request('PUT', $resource, $params, array());
    }
    
    protected function delete ($resource, array $params = array()) {
        return $this->request('DELETE', $resource, $params, array());
    }
    
    private function request ($method, $resource, array $params = array(), array $query = array()) {
        $subDomain = ($this->subDomain ? "$this->subDomain." : '');
        $extension = ($this->extension ? ".$this->extension" : '');

        $apiRoot = "$this->protocol://$subDomain$this->host$this->virtualDirectory/";
        $query += $this->mandatoryQueryParameters();
        $url = $apiRoot.$resource.$extension.'?'.http_build_query($query);
        $this->doRequest($method, $url, $params);
        return $this->response->data;
    }
    
    private function doRequest($method, $url, array $params = array()) {
        $this->initializeRequest($url);
        $this->setRequestMethod($method);
        $this->setRequestParams($params);
        $body = curl_exec($this->curl);
        $status = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        $this->response->status = $status;
        $this->processResponse($body);
        curl_close($this->curl);
        return $this->response;
    }
    
    private function initializeRequest ($url) {
        $this->curl = curl_init($url);
        
        curl_setopt_array($this->curl, array(
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_FOLLOWLOCATION  => true
        ));

        if (defined('CURLOPT_SAFE_UPLOAD')) {
            curl_setopt($this->curl, CURLOPT_SAFE_UPLOAD, true);
        }
        
        $this->request = new stdClass();
        $this->request->url = $url;
        
        $this->response = new stdClass();
        $this->response->data = null;
    }
    
    private function setRequestMethod ($method) {
        $httpMethod = strtoupper($method);
        $this->request->method = $httpMethod;
        
        switch ($httpMethod) {
            
            case 'GET':
                break;
            
            case 'POST':
                curl_setopt($this->curl, CURLOPT_POST, true);
                break;

            case 'PATCH':
            case 'PUT':
            case 'DELETE':
                curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $httpMethod);
                break;
            
        }
    }
    
    private function setRequestParams (array $requestParams) {
        if (!empty($requestParams)) {
            $flattenedRequestParams = array();
            $this->flattenRequestParams($requestParams, $flattenedRequestParams);
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $flattenedRequestParams);
        }
        
        $this->request->params = $requestParams;
    }

    private function flattenRequestParams (array $source, &$destination, $prefix = '') {
        foreach ($source as $key => $value) {
            $name = ($prefix ? $prefix.'['.$key.']' : "$key");

            if (is_array($value)) {
                $this->flattenRequestParams($value, $destination, $name);
            } else {
                $destination[$name] = $value;
            }
        }
    }
    
    private function processResponse ($body) {
        if ($body === false) {
            $this->response->error = curl_error($this->curl);
        } else {
            $this->response->body = $body;
            $this->response->data = json_decode($body);
        }
    }
    
}

class YouMagine extends HttpClient {

    const HOST = 'youmagine.com';

    const API_VERSION_1 = 'v1';
    const API_VERSION_LEGACY = 'legacy';

    const API_LATEST_STABLE_VERSION = self::API_VERSION_1;
    const API_LATEST_VERSION = self::API_VERSION_1;

    private static $apiVersionPaths = array(
        self::API_VERSION_1         => '/v1',
        self::API_VERSION_LEGACY    => ''
    );

    private $authToken = null;
    private $application;
    private $user;

    private static $uploadErrorExplanations = array(
        UPLOAD_ERR_OK           => 'There is no error, the file uploaded with success.',
        UPLOAD_ERR_INI_SIZE     => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
        UPLOAD_ERR_FORM_SIZE    => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
        UPLOAD_ERR_PARTIAL      => 'The uploaded file was only partially uploaded.',
        UPLOAD_ERR_NO_FILE      => 'No file was uploaded.',
        UPLOAD_ERR_NO_TMP_DIR   => 'Missing a temporary folder.',
        UPLOAD_ERR_CANT_WRITE   => 'Failed to write file to disk.',
        UPLOAD_ERR_EXTENSION    => 'A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help.'
    );

    public static function explainUploadError ($uploadError) {
        return self::$uploadErrorExplanations[$uploadError];
    }
    
    public function __construct ($application, array $options = array()) {
        $options += array(
            'host'      => self::HOST,
            'https'     => true,
            'version'   => self::API_LATEST_STABLE_VERSION
        );

        if (array_key_exists($options['version'], self::$apiVersionPaths)) {
            $options['virtualDirectory'] = self::$apiVersionPaths[$options['version']];
        } else {
            throw new InvalidArgumentException("Invalid API version selected: '".$options['version']."'");
        }

        unset($options['version']);
        parent::__construct($options);
        $this->application = $application;
        $this->subDomain = 'api';
        
        if (isset($_SESSION[__CLASS__])) {
            $storedSession = unserialize($_SESSION[__CLASS__]);
            $this->authToken = $storedSession['authToken'];
            $this->user = $storedSession['user'];
        }
    }
    
    public function __destruct() {
        $storedSession = array(
            'authToken' => $this->authToken,
            'user'      => $this->user
        );
        
        $_SESSION[__CLASS__] = serialize($storedSession);
    }
    
    public function authorize () {
        if (!isset($_POST)) {
            die('Error when authorizing: no POST data available');
        }
        
        $data = $_POST;
        $this->user = json_decode($data['user']);
        $this->authToken = $data['authentication_token'];
    }
    
    public function isAuthorized () {
        return $this->authToken !== null;
    }
    
    public function getAuthorizeUrl ($redirectUrl, $denyUrl) {
        $query = http_build_query(array(
            'redirect_url'  => $redirectUrl,
            'deny_url'      => $denyUrl
        ));
        
        return "$this->protocol://$this->host/integrations/$this->application/authorized_integrations/new?$query";
    }
    
    public function getAuthToken () {
        return $this->authToken;
    }
    
    public function getUser () {
        return $this->user;
    }
    
    public function clearSession () {
        $this->authToken = null;
        $this->user = null;
    }
    
    public function designs () {
        return $this->get('designs');
    }
    
    public function designsWithContext ($userSlugOrID) {
        return $this->get("users/$userSlugOrID/designs");
    }
    
    public function designCategories () {
        return $this->get('design_categories');
    }
    
    public function createDesign ($data) {
        return $this->post('designs', array(
            'design' => $data
        ));
    }
    
    public function addDesignImage ($designSlugOrId, array $data) {
        return $this->post("designs/$designSlugOrId/images", array(
            'image' => $data
        ), array(
            'Content-Type' => 'multipart/form-data'
        ));
    }

    public function addDesignDocument ($designSlugOrId, array $data) {
        return $this->post("designs/$designSlugOrId/documents", array(
            'document' => $data
        ), array(
            'Content-Type' => 'multipart/form-data'
        ));
    }
    
    protected function mandatoryQueryParameters() {
        return array('auth_token' => $this->authToken);
    }

}

