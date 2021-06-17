<?
//搭建Auth服务器
$cilent_id="acf033e585648b1d8c0b";
$clinet_secret="3f1bc7ac79e509064ac7050216e536167a4c3bde";
function convertUrlQuery($query)
{ 
    $queryParts = explode('&', $query); 
     
    $params = array(); 
    foreach ($queryParts as $param) 
    { 
        $item = explode('=', $param); 
        $params[$item[0]] = $item[1]; 
    } 
     
    return $params; 
}
function request_curl($url,$data='') 
{       
     // 创建一个新cURL资源
        $ch = curl_init();        // 设置URL和相应的选项
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   
          if (strlen($data) > 0) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }        // 抓取URL并把它传递给浏览器
        $html = curl_exec($ch);        // 关闭cURL资源，并且释放系统资源
        curl_close($ch);        
        return $html;
}
//代码开始
$code=$_GET["code"];
if($code=="" )
       {      
        if(isset($_GET["url"])){$_host=parse_url(urldecode($_GET["url"]))["host"];}else{$_host=parse_url($_SERVER['HTTP_REFERER'])["host"];}
        setcookie("authurl", $_host, time() + 180);
        $AuthUrl="https://github.com/login/oauth/authorize?client_id=".$cilent_id."&scope=user%20repo";
        $html="<html><head><meta charset='UTF-8'><title>请不要关闭窗口 Ver=2</title></head><body><script>function goTo(url){window.location.href=url;}setTimeout('goTo(\"".$AuthUrl."\")',5000);</script></body></html>";
        echo $html;
        exit;
        }
//代码太乱以后整理       
        $url="https://github.com/login/oauth/access_token?client_id=".$cilent_id."&client_secret=".$clinet_secret."&code=".$code;
        $result=convertUrlQuery(request_curl($url));
        $AuthUrl="http://".$_COOKIE["authurl"]."/action/GithubStatic?do=GithubAuth&token=".$result["access_token"];
        $html="<html><head><meta charset='UTF-8'><title>授权成功，请不要关闭窗口</title></head><body><script>function goTo(url){window.location.href=url;}setTimeout('goTo(\"".$AuthUrl."\")',5000);</script></body></html>";
        echo $html;
