<?php
include dirname(__FILE__)."/GitHelper.php";
include dirname(__FILE__)."/config.php";
header("Access-control-Allow-Origin:*");
global $real_url;global $path_parse;global $query_method;global $path_array;global $path_url;global $_config;global $extension_webp;
$query_method=isset($GET["method"])?$GET["method"]:"default";
$path_parse=parse_url($_SERVER['REQUEST_URI']);
$path_array=explode("/",substr($path_parse["path"],1));

$path_url=strstr(trim($_SERVER['REQUEST_URI'], '/'),"/");//有参数
$real_url=parse_url($path_url)["path"];//无参数
//var_dump($path_parse);
//var_dump($path_array);
//var_dump($path_url);
//die;

isset($config["router"][$path_array[0]])?:_die(10001);//非目录中断
$_config=$config["github"][$config["router"][$path_array[0]]];

ignore_user_abort(true); 
set_time_limit(0);
//fastcgi_finish_request();
$extension_webp=array("png"=>"png","gif"=>"gif","jpeg"=>"jpeg","jpg"=>"jpeg");
function_exists("method_".$query_method)?call_user_func("method_".$query_method):_die(10002);

function method_default()
  {
    //默认函数
    $CacheFile=dirname(__FILE__)."/hash/".md5($GLOBALS["_Config"]["Parameter"]?$GLOBALS["path_url"]:$GLOBALS["real_url"]).".md5";
    $file_name=pathinfo($GLOBALS["real_url"]);
    if(($GLOBALS["_config"]["CacheTime"] ==0 or time()-@filemtime($CacheFile)<$GLOBALS["_config"]["CacheTime"]) && file_exists($CacheFile))
    {
      $Cache_json=json_decode(file_get_contents($CacheFile)); 
   //var_dump($Cache_json);
   //die;
      if($Cache_json->webp)
      {
        $_path=$file_name["dirname"]=="/"? $file_name["dirname"].$file_name["filename"].".webp":$file_name["dirname"]."/".$file_name["filename"].".webp";
      }else{
        $_path=$GLOBALS["path_url"];
      }
      //已缓存 立即导航
      header('Location: https://cdn.jsdelivr.net/gh/'.$GLOBALS["_config"]["username"]."/".$GLOBALS["_config"]["repos"].$GLOBALS["_config"]["path"].$_path);
      die;
    }
    
    header('Location: '.$GLOBALS["_config"]["site"].$GLOBALS["path_url"]);
  fastcgi_finish_request();  
    
    if(isset($GLOBALS["extension_webp"][$file_name["extension"]])&&$GLOBALS["_config"]["webp"])
    {
      $Cache_json=array("webp"=>true);      $temp_func="imagecreatefrom".$GLOBALS["extension_webp"][$file_name["extension"]];
      $_path=$file_name["dirname"]=="/"? $file_name["dirname"].$file_name["filename"].".webp":$file_name["dirname"]."/".$file_name["filename"].".webp";
      $img_file = fopen('img/'.$file_name["basename"], "w+");
        fwrite($img_file,file_get_contents($GLOBALS["_config"]["site"].$GLOBALS["_config"]["path"].$GLOBALS["path_url"]));
 
      fclose($img_file);
      $temp_img = $temp_func('img/'.$file_name["basename"]);
      imagewebp($temp_img,'img/'.$file_name["filename"].".webp");
      imagedestroy($temp_img);
      $file_data=file_get_contents('img/'.$file_name["filename"].".webp");
     // if(file_exists('img/'.$file_name["filename"].".webp")) //@unlink('img/'.$file_name["filename"].".webp");
      //if(file_exists( 'img/'.$file_name["basename"])) @unlink( 'img/'.$file_name["basename"]);
    }else
    {
      $Cache_json=array("webp"=>false);
      $_path=$GLOBALS["path_url"];     $file_data=file_get_contents($GLOBALS["_config"]["site"].$GLOBALS["_config"]["path"].$GLOBALS["path_url"]);
    }  $result=files_upload($GLOBALS["_config"]["username"],$GLOBALS["_config"]["token"],$GLOBALS["_config"]["repos"],$_path,base64_encode($file_data));
    var_dump($result);
    if(!$result)$result=files_updata($GLOBALS["_config"]["username"],$GLOBALS["_config"]["token"],$GLOBALS["_config"]["repos"],$_path, base64_encode($file_data),get_sha($GLOBALS["_config"]["username"],$GLOBALS["_config"]["repos"],$_path));
    //再次出错建议写出日志
    $Cache_json["result"]=$result;
    file_put_contents($CacheFile,json_encode($Cache_json));

    //var_dump($GLOBALS["path_parse"]);


  }
  function method_upload()
    {
    }
    function _die($error){echo "不存在";die;}