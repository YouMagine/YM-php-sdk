<?php

function url ($url = null, array $params = array()) {
    if (func_num_args() == 1 && is_array($url)) {
        $params = $url;
        $url = '';
    }
    
    $parts = parse_url($url);
    $currentRequestUri = parse_url($_SERVER['REQUEST_URI']);
    
    if (empty($parts['path'])) {
        unset($parts['path']);
    }
    
    $parts += array(
        'scheme'    => empty($_SERVER['HTTPS']) ? 'http' : 'https',
        'host'      => $_SERVER['HTTP_HOST'],
        'path'      => $currentRequestUri['path'],
        'query'     => ''
    );
    
    if ($parts['query']) {
        parse_str($parts['query'], $parsedQuery);
        $params += $parsedQuery;
    }
    
    $url = $parts['scheme'].'://'.$parts['host'].$parts['path'];
    
    if (count($params)) {
        $url .= '?'.http_build_query($params);
    }
    
    return $url;
}
