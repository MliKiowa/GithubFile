<?
require   dirname( __FILE__ ).'/Helper.php';
$options = Typecho_Widget::widget( 'Widget_Options' )->plugin( 'GithubStatic' );
$_GET["do"]=!isset($_GET["do"])?"":$_GET["do"];
$file_lock = dirname( __FILE__ ) . "/cache/debug.lock";
function generate_html_basic($var1,$var2,$var3){echo $var1;echo '<a href="';Helper::options()->siteUrl();echo $var2;echo '" >'.$var3.'</a>';}
function generate_html($debug){
     if($debug)
     {
      generate_html_basic("Debug开启","admin/extending.php?panel=GithubStatic%2FDebug.php&do=close_debug","点击关闭Debug抓取");
         }
 else{
       generate_html_basic("Debug关闭","admin/extending.php?panel=GithubStatic%2FDebug.php&do=copen_debug","点击开启Debug抓取");
          }
}

if(!$options->debug){ echo json_encode(array("result"=>-1,"msg"=>"Not Open Debug"));exit;}
function handle_iconfig_inspect()
{
 if(!isset($options->username) or empty($options->username)){echo "username为空";exit;}
 if(!isset($options->token) or empty($options->token)){echo "token为空";exit;}
 if(!isset($options->repo) or empty($options->repo)){echo "repo为空";exit;}
 if(empty(Github_user_login($options->token)->login)){echo "登录状态异常";exit;}
 echo "配置测试正常";
}                                                                                                 
function handle_open_debug(){
    if(!file_exists($file_lock))file_put_contents($file_lock , (string)rand(1,1000));    
    generate_html(true);
    exit;
 }
 
function handle_open_close()
{
    if(!file_exists($file_lock)){generate_html(false);exit;}
    $tmp_file = fopen($file_lock , "r");
    $file_lock_num=fread($tmp_file,filesize($file_lock));
    fclose($tmp_file);
    
    $tmp_file = fopen(dirname( __FILE__ )."/cache/".$file_lock_num.".debug", "r") or die("Unable to open file!");
    $debug_text=fread($tmp_file,filesize(dirname( __FILE__ )."/cache/".$file_lock_num.".debug"));
    fclose($tmp_file);
    
    unlink($file_lock);
    unlink(dirname( __FILE__ )."/cache/".$file_lock_num.".debug");    
    echo $debug_text;
    generate_html(false);
}

function handle_found_exists(){
    generate_html(false);
    generate_html_basic("","admin/extending.php?panel=GithubStatic%2FDebug.php&do=config_inspect","点击寻找配置错误");   
}
$do_fun = "handle_" . $_GET["do"];
if(function_exists($do_fun){$do_fun();}else{ handle_found_exists();}
