<?php
/**
* Action
*
* @author : Mlikiowa
* @time : 2021.6.17
* @version : 6.0.2
*/
require dirname(__FILE__) . "/Helper.php";
class GithubStatic_Action extends Typecho_Widget implements Widget_Interface_Do
{
  public function action()
    { 
      $this->on($this->request->is('do=Recache'))->Recache();        
      $this->on($this->request->is('do=GithubAuth'))->GithubAuth(); 
    }
  public function  Recache(){
      $_options = Helper::options()->plugin('GithubStatic');
      $temp_repos = dirname(__FILE__) . "/cache/repos.json";
      if(file_exists($temp_repos){unlink($temp_repos)};
      $temp_file = fopen($temp_repos,"w+");
      fwrite($temp_file, Github_repos_all($_options->username,$_options->token));
      fclose($temp_file);
     }
      public function GithubAuth(){
         $username = Github_user_login($this->request->from('token')["token"])->login;
         if(empty($username)){          
           echo "获取失败 中断位置：username ";
           exit;
        }
        $new_options = array("token" => $this->request->from('token')["token"] , "username" => $username);      
        Helper::configPlugin('GithubStatic' , $new_options);
        $this->Recache();
        header('HTTP/1.1 301 Moved Permanently'); 
        header('Location: /admin/options-plugin.php?config=GithubStatic');  
        exit;
        }
        public function init()
        {
           }
    
    }
