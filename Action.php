<?php
/**
 * Action
 *
 * @author : Mlikiowa
 * @time : 2021.8.30
 * @version : 1.0.2
 */
defined("_TMP_PATH") or define("_TMP_PATH", dirname(__FILE__) . "/tmp");
defined("_Cache_PATH") or define("_Cache_PATH", dirname(__FILE__) . "/cache");
require_once dirname(__FILE__) . "/class/GithubApi.php";
require_once dirname(__FILE__) . "/func/Helper.php";
class GithubFile_Action extends Typecho_Widget implements Widget_Interface_Do {
    public function action() {
        $this->on($this->request->is("do=Recache"))->Recache();
        $this->on($this->request->is("do=GithubAuth"))->GithubAuth();
        $this->on($this->request->is("do=del_log"))->del_log();
     // $this->on($this->request->is("do=ConfigCheck"))->ConfigCheck(); //二次验证
        
    }
    public function del_log() {
        $this->is_pass();
        $filename = $this->request->from("filename") ["filename"];
        @unlink( __DIR__."/cache/".  $filename);
       /** 提示信息 */
        $this->widget('Widget_Notice')->set(_t("文件已经删除"), 'success');
        /** 转向原页 */
        $this->response->goBack();
        }
    public function Recache() {
        $this->is_pass();
        $_options = Helper::options()->plugin("GithubFile");
        $file_repos = _TMP_PATH . "/repos.json";
        if (file_exists($file_repos)) {
            unlink($file_repos);
        }
        $file_repos = fopen($file_repos, "w+");
        $api = new GithubApi();
        $api->set_api(_Get_config("mirror", "https://api.github.com"));
        $api->set_token($_options->token);
        fwrite($file_repos, $api->repos_all($_options->username));
        fclose($file_repos);
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: /admin/options-plugin.php?config=GithubFile");
        exit;
    }
    public function GithubAuth() {
        $this->is_pass();
        $api = new GithubApi();
        $api->set_api(_Get_config("mirror", "https://api.github.com"));
        $api->set_token($this->request->from("token") ["token"]);
        $username = $api->user_login()->login;
        if (empty($username)) {
            die("哇噗, 你的授权失败了,因为登录获取用户名失败");
        }
        $_options = array("token" => $this->request->from("token") ["token"], "username" => $username);
        Helper::configPlugin("GithubFile", $_options);
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: /action/GithubFile?do=Recache");
        //此处使用跳转缘由 Helper::options()没有刷新
        //修改无跳转
        
    }
    public function is_pass() {
        $user = Typecho_Widget::widget("Widget_User");
        if (!$user->pass("administrator")) {
            die("未登录用户!");
        }
    }
}
