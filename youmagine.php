<?php

switch (session_status()) {
    case PHP_SESSION_DISABLED:
        die('API example requires a working session!');
        break;
    case PHP_SESSION_NONE:
        session_start();
        break;
}

class HttpApiClient {
    
    protected $request = null;
    protected $response = null;
    protected $protocol;
    protected $host;
    protected $virtualDirectory;
    
    private $curl;
    
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
        $apiRoot = "$this->protocol://api.$this->host$this->virtualDirectory/";
        $query += $this->mandatoryQueryParameters();
        $url = $apiRoot.$resource.'.json'.'?'.http_build_query($query);
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
            
            case 'PUT':
            case 'DELETE':
                curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $httpMethod);
                break;
            
        }
    }
    
    private function setRequestParams ($requestParams) {
        if (!empty($requestParams)) {
            $requestString = http_build_query($requestParams);
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $requestString);
        }
        
        $this->request->params = $requestParams;
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

class YouMagine extends HttpApiClient {

    const HOST = 'youmagine.com';
    const API_VERSION_1 = 'v1';
    const API_LATEST_VERSION = self::API_VERSION_1;

    private $authToken = null;
    private $application;
    private $user;
    
    public function __construct ($application, array $options = array()) {
        $options += array(
            'host'      => self::HOST,
            'https'     => true,
            'version'   => self::API_LATEST_VERSION
        );

        $options['virtualDirectory'] = '/'.$options['version'];
        unset($options['version']);
        
        parent::__construct($options);
        $this->application = $application;
        
        
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
        $this->post("designs/$designSlugOrId/images", array('image' => $data));
    }
    
    protected function mandatoryQueryParameters() {
        return array('auth_token' => $this->authToken);
    }

}

