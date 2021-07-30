<?php
require_once dirname(__FILE__)."/HTTP/Client.php";
class GithubApi{
private $_token;
private $_api;
public function set_token($token)
{$this->_token=$token;}
public function set_api($api)
{$this->_api=$api;}
public function __construct(){}  
public function __destruct(){}
public function user_info($username) {
  return json_decode(self::request_api( "/users/".$username)); }

public function user_login() {
  return json_decode(self::request_api("/user")); }

public function repos_all($username) {
  return self::request_api("/users/".$username."/repos"); }

public function repos_info($username,$reposname) {
  return json_decode(self::request_api( "/repos/".$username."/".$reposname)); }

public function repos_path($username,$reposname,$path) {
  return json_decode(self::request_api( "/repos/".$username."/".$reposname."/contents".$path)); }

public function files_upload($username,$repos,$path,$files)
            {
              $data=array("message"=>"upload by GithubStatic","content"=>base64_encode($files));
              $json=(array)json_decode(self::request_api(  "/repos/".$username."/".$repos."/contents".$path,json_encode($data),"PUT"));
              return !isset($json["message"]);
            } 

public function files_updata($username,$repos,$path,$files,$sha)
              {
                $data=array("message"=>"updata by GithubStatic","content"=>base64_encode($files),"sha"=>$sha);
                $json=(array)json_decode(self::request_api(  "/repos/".$username."/".$repos."/contents".$path,json_encode($data),"PUT"));
                return !isset($json["message"]);
              }

public function files_del($username,$repos,$path,$sha){
                  $data=array("message"=>"del by GithubStatic","sha"=>$sha);
                  $json=(array)json_decode(self::request_api(  "/repos/".$username."/".$repos."/contents".$path,json_encode($data),"DELETE"));                 
                  return !isset($json["message"]);}

public function get_sha($username,$repos,$path){
                    $json=(array)self::repos_path($username,$repos,$path);
                    return $json["sha"];
                  }
public  function request_api($main_url,$param="",$method = "GET")
{
        $http=_Http_Client::get();
	    $http->setMethod($method);
	    $http->setHeader('User-Agent', 'Github File Plugin ');
	    $http->setHeader('Authorization', 'token '.$this->_token);
	    $http->setData($param);
	    $result = $http->send($this->_api.$main_url);
	    //var_dump($result);
	    return $result;
}
 }                 