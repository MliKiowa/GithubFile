<?
require dirname(__FILE__)."/Helper.php";
//引入辅助资源
class GithubStatic_Action extends Typecho_Widget implements Widget_Interface_Do
{
  private $_options;
  public function action()
    { 
    
    $this->on($this->request->is('recache=1'))->Recache();        
      $this->on($this->request->is('do=GithubAuth'))->GithubAuth(); 
    }
    public function  Recache(){
    //刷新缓存
      $this->init();
      if(file_exists(dirname(__FILE__)."/cache/repos.json"))unlink(dirname(__FILE__)."/cache/repos.json");
      $temp_file=fopen(dirname(__FILE__)."/cache/repos.json","w+");
      fwrite($temp_file, Github_repos_all($this->_options->username,$this->_options->token));
      fclose($temp_file);
     }
      public function GithubAuth(){
        $this->init();
         $username=Github_user_login($this->request->from('token')["token"])->login;
         if(empty($username)){
           //为空为token失效
           //应该中断授权
        }
        $_options=array("token"=>$this->request->from('token')["token"],"username"=>$username);

        $this->_options->username=$username;
        $this->_options->token=$this->request->from('token')["token"];
        Helper::configPlugin('GithubStatic', $_options);
        $this->Recache();//主动刷新缓存
        header('HTTP/1.1 301 Moved Permanently');    //发出301头部
        header('Location: /admin/options-plugin.php?config=GithubStatic');    //跳转到你希望的地址格式
        exit;
        }
        public function init()
        {
          if(!isset($this->_options))$this->_options = Helper::options()->plugin('GithubStatic');
        }
    
    }
