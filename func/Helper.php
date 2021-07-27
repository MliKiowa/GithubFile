<?php
function _Get_config($name,$default){
         $_db = Typecho_Db::get();
         $result = $_db->fetchAll($this->_db->select('value')->from('table.options')->where('name = ?', "plugin:GithubFile"));        
         if(!isset($result[0]["value"])){return "";}
         $_options=unserialize($result[0]["value"]);
         return (isset($_options[$name])?$_options[$name]:$default);        
}
function _Get_tmp_repos($tmp_path){
if (@file_exists( $tmp_path."/repos.json" ) ) {
            $_file = fopen( $tmp_path.'/repos.json', 'r' );
            $_repos = ( array )json_decode( fread( $_file, filesize($tmp_path.'/repos.json' ) ) );
            fclose( $_file );
            foreach ( $_repos as $key => $value ) {
                $repos = array_merge($repos, array( $value->name =>$value->name ) );
                }
                return $repos;
          }          
          return "缓存获取失败";
}
                
                