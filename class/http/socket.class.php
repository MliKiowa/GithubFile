<?php
 
/**
 * 使用PHP Socket 编程模拟Http post和get请求
 * @author koma
 */
 
class Http{
  private $sp = "\r\n"; //这里必须要写成双引号
  private $protocol = 'HTTP/1.1'; 
  private $requestLine = "";
  private $requestHeader = "";
  private $requestBody = "";
  private $requestInfo = "";
  private $fp = null;
  private $urlinfo = null;
  private $header = array();
  private $body = "";
  private $responseInfo = "";
  private static $http = null; //Http对象单例     

  private function __construct() {}    
  
  public static function create() { 
    if ( self::$http === null ) {
      self::$http = new Http(); 
    }
    return self::$http;
  }    
 
  public function init($url) {
    $this->parseurl($url); 
    $this->header['Host'] = $this->urlinfo['host'];
    return $this; 
  }

  public function get($header = array()) {
    $this->header = array_merge($this->header, $header);
    return $this->request('GET');
  }   
 
  public function post($header = array(), $body = array()) {
    $this->header = array_merge($this->header, $header);
    if ( !empty($body) ) {
      $this->body = http_build_query($body);
      $this->header['Content-Type'] = 'application/x-www-form-urlencoded';
      $this->header['Content-Length'] = strlen($this->body);
    }
    return $this->request('POST'); 
  }
  
  private function request($method) { 
    $header = ""; 
    $this->requestLine = $method.' '.$this->urlinfo['path'].'?'.$this->urlinfo['query'].' '.$this->protocol;
    foreach ( $this->header as $key => $value ) { 
     $header .= $header == "" ? $key.':'.$value : $this->sp.$key.':'.$value;
    } 
    $this->requestHeader = $header.$this->sp.$this->sp;
    $this->requestInfo = $this->requestLine.$this->sp.$this->requestHeader;
    if ( $this->body != "" ) { 
      $this->requestInfo .= $this->body; 
    }
    $port = isset($this->urlinfo['port']) ? isset($this->urlinfo['port']) : '80'; 
    $this->fp = fsockopen($this->urlinfo['host'], $port, $errno, $errstr);
    if ( !$this->fp ) {
     echo $errstr.'('.$errno.')'; 
      return false;
    } 
    if ( fwrite($this->fp, $this->requestInfo) ) { 
      $str = ""; 
      while ( !feof($this->fp) ) {
        $str .= fread($this->fp, 1024);
      } 
      $this->responseInfo = $str; 
    }
    fclose($this->fp); 
    return $this->responseInfo;
  }
  private function parseurl($url) { 
    $this->urlinfo = parse_url($url); 
  }
 
}
 