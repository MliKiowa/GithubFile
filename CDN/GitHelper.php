<?php
function remote_file_exists($url) { 
    $executeTime = ini_get('max_execution_time'); 
    ini_set('max_execution_time', 0); 
    $headers = @get_headers($url); 
    ini_set('max_execution_time', $executeTime); 
    if ($headers) { 
      $head = explode(' ', $headers[0]); 
      if ( !empty($head[1]) && intval($head[1]) < 400) return true; 
    } 
    return false; 
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
        var_dump($response);
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
                    $data=array("message"=>"upload a new file","content"=>$files);
                    // echo "https://api.github.com/repos/".$username."/".$repos."/contents".$path;
                    $json=(array)json_decode(api_push($username,$token,"https://api.github.com/repos/".$username."/".$repos."/contents".$path,json_encode($data),"PUT"));
                    // var_dump($json);
                    return !isset($json["message"]);
                    //上传需要判断失败或者成功
                  } 
                  function files_updata($username,$token,$repos,$path,$files,$sha)
                    {
                      $data=array("message"=>"upload a new file","content"=>$files,"sha"=>$sha);
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
                        function git_upload_ex()
                          {
                          }
