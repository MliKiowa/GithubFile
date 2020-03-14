<?
//测试token 49e22b37f6795a9264909642c5afcc123cc4e2fb
//https://api.github.com/users/solomonxie
/**
* [errorLog 打印错误日志记录]
* @param [type] $message [打印日志记录]
* @param [type] $file [日志文件名]
* @return [type] [description]
*/
function errorLog($message,$file)
  {
    //将日志文件放在根目录下/log/日期的文件夹名
    $log_dir=$_SERVER['DOCUMENT_ROOT']."/log/".date('Ymd')."/";
    //判断是否存在文件夹，没有则创建
    if(!is_dir($log_dir)){
      @mkdir($log_dir,0777,true);
    }
    //将错误日志记录写入文件中
    $file=$log_dir.$file;
    if(is_array($message)){
      $arr=explode(".",$file);
      if($arr[1]=='php'){
        error_log("<?php \n return ".var_export($message, true)."\n", 3,$file);
      }else{
        error_log(var_export($message, true)."\n", 3,$file);
      } 
    }else{
      error_log($message."\n\n", 3,$file); 
    } 
  }
  function api_push($username,$token,$curl_url,$data,$method){
      $curl_token_auth = 'Authorization: token ' . $token;
      $ch = curl_init($curl_url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'User-Agent: $username', $curl_token_auth ));
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
      curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
      $response = curl_exec($ch); 
      curl_close($ch);
      if(file_exists("debug.lock")){errorLog($response,"api");}
      // $response = json_decode($response);
      return $response;
    }
    function api_get($url)
      {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url); //设置访问的url地址
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'User-Agent: GitStatic'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);//不输出内容
        $result = curl_exec($ch);
        curl_close ($ch);
        return $result;
      }

      function user_info($username) {return json_decode(api_get("https://api.github.com/users/".$username)); }
        //不存在会返回一个message Not Found 获取用户基本信息

        function repos_all($username) {return json_decode(api_get("https://api.github.com/users/".$username."/repos")); }
          //获取所有repos

          function repos_info($username,$reposname) {return json_decode(api_get("https://api.github.com/repos/".$username."/".$reposname)); }
            //获取repos info

            function repos_path($username,$reposname,$path) {return json_decode(api_get("https://api.github.com/repos/".$username."/".$reposname."/contents/".$path)); }
              //获取repos 目录内容

              function files_upload($username,$token,$repos,$path,$files)
                {
                  $data=array("message"=>"upload a new file","content"=>base64_encode($files));
                  // echo "https://api.github.com/repos/".$username."/".$repos."/contents".$path;
                  $json=(array)json_decode(api_push($username,$token,"https://api.github.com/repos/".$username."/".$repos."/contents".$path,json_encode($data),"PUT"));
                  // var_dump($json);
                  return !isset($json["message"]);
                  //上传需要判断失败或者成功
                } 
                function files_updata($username,$token,$repos,$path,$files,$sha)
                  {
                    $data=array("message"=>"upload a new file","content"=>base64_encode($files),"sha"=>$sha);
                    $json=(array)json_decode(api_push($username,$token,"https://api.github.com/repos/".$username."/".$repos."/contents".$path,json_encode($data),"PUT"));
                    //更新需要判断失败或者成功
                    // var_dump($data);
                    return !isset($json["message"]);
                  }
                  function files_del($username,$token,$repos,$path,$sha)
                    {
                      $data=array("message"=>"upload a new file","sha"=>$sha);
                      $json=(array)json_decode(api_push($username,$token, "https://api.github.com/repos/".$username."/".$repos."/contents".$path,json_encode($data),"DELETE"));
                      //var_dump($json);
                      return !isset($json["message"]);
                      //删除需要判断失败或者成功
                    }
                    function get_sha($username,$repos,$path){
                        $json=(array)repos_path($username,$repos,$path);
                        // var_dump($json);
                        return $json["sha"];
                      }

                      // echo files_updata("MQiaoqian","b3e7642a7fb88fe78ec9845d51688dca558cfc16","MCDN","/6484.txt","测试",get_sha("MQiaoqian","MCDN","6484.txt"));

                      // files_upload("MQiaoqian","b3e7642a7fb88fe78ec9845d51688dca558cfc16","MCDN","/demo.txt","testiebdbskej");
                      // $json=(array)repos_path("MQiaoqian","MCDN","demo.txt");
                      // var_dump($json["sha"]);
                      //echo get_sha("MQiaoqian","MCDN","demo.txt"); 
                      //https://api.github.com/users/用户名/repos
                      //api_push("MQiaoqian","b3e7642a7fb88fe78ec9845d51688dca558cfc16","/repos/solomonxie/gists/contents/2.jpg","");
                      // var_dump(api_get("https://api.github.com/users/MQiaoqian"));
                      // var_dump(repos_all("MQiaoqian"));

