<?php
/**
* 使用该文件可以搭建一个属于自己的插件授权服务器
*
* @author : 无绘
* @time : 2021.8.27
* @version : 4.0
*/
//请抽取本文件 class目录 verify.php 文件到Server
require "class/GithubApi.php";
$config = array(
"client_id" => "acf033e585648b1d8c0b",
"client_secret" => "3f1bc7ac79e509064ac7050216e536167a4c3bde",
"https_open" => false
);
function generate_html($html_tittle,$redirect_url){return  "<html><head><meta charset='UTF-8'><title>" . $html_tittle . "</title></head><body><h1>请等待系统处理...</h1><script>function goTo(url){window.location.href=url;}setTimeout('goTo(\"".$redirect_url."\")',5000);</script></body></html>";}
$_code = isset($_GET["code"]) ? $_GET["code"] : "";
if($_code == "")
{      
        $auth_host = isset($_GET["source_site"]) ? parse_url(urldecode($_GET["source_site"]))["host"] : parse_url($_SERVER['HTTP_REFERER'])["host"];
        setcookie("auth_site", $auth_host, time() + 180);
        $github_url = "https://github.com/login/oauth/authorize?client_id=". $config["client_id"]. "&scope=user%20repo";
        echo generate_html("请不要关闭窗口", $github_url);
        exit;
}   
setcookie("auth_site", "",time()-1);
$github_url = "https://github.com/login/oauth/access_token?client_id=" . $config["client_id"] . "&client_secret=" . $config["client_secret"] . "&code=".$github_code;      
$http=_Http_Client::get();
$http->setMethod("GET");   
parse_str( $http->send($github_url),$auth_result);
$github_url =  ( $config -> https_open ? "https://" : "http://" ) . $_COOKIE["auth_site"] . "/action/GithubFile?do=GithubAuth&token=" . $auth_result["access_token"];
if(empty($_COOKIE["auth_site"])){
echo "<h1>授权成功啦</h2><br>Github Token:<h2>".$auth_result["access_token"]."</h2>";
}else{
echo generate_html("授权成功，请不要关闭窗口", $github_url);
}
