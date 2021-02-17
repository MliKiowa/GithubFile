<?
require dirname(__FILE__)."/Helper.php";
//引入辅助资源
class GithubStatic_Action extends Typecho_Widget implements Widget_Interface_Do
{
  private $_db;
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
         $result = $this->_db->fetchAll($this->_db->select('value')->from('table.options')->where('name = ?', "plugin:GithubStatic"));        
         if(!isset($result[0]["value"])) $this->widget('Widget_Notice')->set("数据库错误", 'fail' );
         $array_options=unserialize($result[0]["value"]);
         $array_options["token"]=$this->request->from('token')["token"];
         $username=Github_user_login($this->request->from('token')["token"])->login;
         $array_options["username"]= $username;
         $this->_options->username=$username;
         $this->_options->token= $array_options["token"];
         $this->_db->query($this->_db->update('table.options')->rows(array('value'=>serialize($array_options)))->where('name = ?',"plugin:GithubStatic"));
         $this->widget('Widget_Notice')->set("授权成功啦~", 'sucess' );  //此处暂时存在问题
         $this->Recache();//主动刷新缓存
         header('HTTP/1.1 301 Moved Permanently');    //发出301头部
         header('Location: /admin/options-plugin.php?config=GithubStatic');    //跳转到你希望的地址格式
         exit;
        }
      public function init()
        {
          $this->_db = Typecho_Db::get();
         $this->_options = Helper::options()->plugin('GithubStatic');
        }
    }
