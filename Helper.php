<?
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
                  //var_dump(Github_user_login(" f2bf384b91302c4ea6eed5d8ee2ed14543d6e40c"));
//var_dump(Github_get_sha("YueZhiNai","yun","bash.sh","09bbeb19b801720da0fa4b5507f33cdd6a81c8a0"));         
//Github_files_del("YueZhiNai","09bbeb19b801720da0fa4b5507f33cdd6a81c8a0","yun","/bash.sh",
//Github_get_sha("YueZhiNai","yun","/bash.sh","09bbeb19b801720da0fa4b5507f33cdd6a81c8a0"));
 