<?
/**
* 使用该文件可以调用github api
*
* @author : Mlikiowa
* @time : 2021.6.17
* @version : 4.0
*/
function do_debug($url,$result)
{
    if(file_exists(dirname( __FILE__ ) . "/cache/debug.lock")){
      $file_debug_lock= fopen(dirname( __FILE__ ) . "/cache/debug.lock", "r");
      $file_lock_num=fread($file_debug_ock,filesize(dirname( __FILE__ )."/cache/debug.lock"));
      $file_debug_data = fopen(dirname( __FILE__ )."/cache/".$file_lock_num.".debug", "a+");
      fwrite($file_debug_data, "\n--------------------------------------------\n"."url：".$url."\n");
      fwrite($file_debug_data, "result".$result."\n--------------------------------------------\n");  
      fclose($file_debug_data);
      fclose($file_debug_lock);
  }
}
function request_api($url, $data=null,$token="",  $method='GET', $header = array(""), $https=true, $timeout = 5){
    $method = strtoupper($method);
    $ch = curl_init();
    $token_array=array('Authorization: token  '.$token,'User-Agent: GithubStatic');
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if($https){
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }
    if ($method != "GET") {
        if($method == 'POST'){
            curl_setopt($ch, CURLOPT_POST, true);
        }
        if ($method == 'PUT' || strtoupper($method) == 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); 
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    $header=array_merge($header,$token_array);//合并请求header
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    $result = curl_exec($ch);
    //调试部分 插件所有标准API请求都会通过于此
    do_debug($url,$result);   
    curl_close($ch);
    return $result;
    }
function Github_user_info($username,$token) {
  return json_decode(request_api("https://api.github.com/users/".$username,array(),$token)); }
function Github_user_login($token) {
  return json_decode(request_api("https://api.github.com/user", array(),$token)); }
function Github_repos_all($username,$token) {
  return request_api("https://api.github.com/users/".$username."/repos",array(),$token,"GET"); }
function Github_repos_info($username,$reposname,$token) {
  return json_decode(request_api("https://api.github.com/repos/".$username."/".$reposname,array(),$token)); }
function Github_repos_path($username,$reposname,$path,$token) {
  return json_decode(request_api("https://api.github.com/repos/".$username."/".$reposname."/contents".$path,array(),$token)); }
function Github_files_upload($username,$token,$repos,$path,$files)
            {
              $data=array("message"=>"upload by GithubStatic","content"=>base64_encode($files));
              $json=(array)json_decode(request_api("https://api.github.com/repos/".$username."/".$repos."/contents".$path,json_encode($data),$token,"PUT"));
              return !isset($json["message"]);
            } 
function Github_files_updata($username,$token,$repos,$path,$files,$sha)
              {
                $data=array("message"=>"updata by GithubStatic","content"=>base64_encode($files),"sha"=>$sha);
                $json=(array)json_decode(request_api("https://api.github.com/repos/".$username."/".$repos."/contents".$path,json_encode($data),$token,"PUT"));
                return !isset($json["message"]);
              }
function Github_files_del($username,$token,$repos,$path,$sha){
                  $data=array("message"=>"del by GithubStatic","sha"=>$sha);
                  $json=(array)json_decode(request_api("https://api.github.com/repos/".$username."/".$repos."/contents".$path,json_encode($data),$token,"DELETE"));
                  return !isset($json["message"]);}
function Github_get_sha($username,$repos,$path,$token){
                    $json=(array)Github_repos_path($username,$repos,$path,$token);
                    return $json["sha"];
                  }
 