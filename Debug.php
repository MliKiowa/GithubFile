<?
require   dirname( __FILE__ ).'/Helper.php';
$options = Typecho_Widget::widget( 'Widget_Options' )->plugin( 'GithubStatic' );
if(!$options->debug){ echo json_encode(array("result"=>-1,"msg"=>"Not Open Debug"));exit;}
$_GET["do"]=!isset($_GET["do"])?"":$_GET["do"];

if($_GET["do"]=="find"){
 if(!isset($options->username) or empty($options->username)){echo "username为空";exit;}
 if(!isset($options->token) or empty($options->token)){echo "token为空";exit;}
 if(!isset($options->repo) or empty($options->repo)){echo "repo为空";exit;}
 if(empty(Github_user_login($options->token)->login)){echo "登录状态异常";exit;}
 echo "测试正常";
}else if($_GET["do"]=="opendebug"){
    if(file_exists(dirname( __FILE__ )."/cache/debug.lock")){
        echo "Debug已开启";
        echo '<a href="';
        Helper::options()->siteUrl();
        echo 'admin/extending.php?panel=GithubStatic%2FDebug.php&do=closedebug" >点击关闭Debug抓取</a>';
        exit;
    }
    $lockfile = fopen(dirname( __FILE__ )."/cache/debug.lock", "w+") or die("Unable to lock debug!");
    fwrite($lockfile, (string)rand(1,1000));
    fclose($lockfile);
    echo "Debug开启";
    echo '<a href="';
    Helper::options()->siteUrl();
    echo 'admin/extending.php?panel=GithubStatic%2FDebug.php&do=closedebug" >点击关闭Debug抓取</a>';
}else if($_GET["do"]=="closedebug"){
    if(!file_exists(dirname( __FILE__ )."/cache/debug.lock")){
        echo "debug已关闭";
        echo '<a href="';
        Helper::options()->siteUrl();
        echo 'admin/extending.php?panel=GithubStatic%2FDebug.php&do=opendebug" >点击开启Debug抓取</a>';
        exit;
    }
    $lockfile = fopen(dirname( __FILE__ )."/cache/debug.lock", "r") or die("Unable to open file!");
    $locknum=fread($lockfile,filesize(dirname( __FILE__ )."/cache/debug.lock"));
    fclose($lockfile);

    $debugfile = fopen(dirname( __FILE__ )."/cache/".$locknum.".debug", "r") or die("Unable to open file!");
    $debugtext=fread($debugfile,filesize(dirname( __FILE__ )."/cache/".$locknum.".debug"));
    fclose($debugfile);
    unlink(dirname( __FILE__ )."/cache/debug.lock");
    unlink(dirname( __FILE__ )."/cache/".$locknum.".debug");
    echo $debugtext;
    echo '<a href="';
    Helper::options()->siteUrl();
    echo 'admin/extending.php?panel=GithubStatic%2FDebug.php&do=opendebug" >点击开启Debug抓取</a>';
}else{
    echo '<a href="';
Helper::options()->siteUrl();
echo 'admin/extending.php?panel=GithubStatic%2FDebug.php&do=opendebug" >点击开启Debug抓取</a>';
echo '<a href="';
Helper::options()->siteUrl();
echo 'admin/extending.php?panel=GithubStatic%2FDebug.php&do=find" >点击寻找配置错误</a>';
}
