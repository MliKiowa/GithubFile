<?php
function _Get_config($name, $default) {
    static $result = true;
    if($result === true){
    $_db = Typecho_Db::get();
    $result = $_db->fetchAll($_db->select('value')->from('table.options')->where('name = ?', 'plugin:GithubFile'));
    }
    if (!isset($result[0]['value'])) {
        return $default;
    }
    $_options = unserialize($result[0]['value']);
    return (isset($_options[$name]) ? $_options[$name] : $default);
}
function _Get_tmp_repos($tmp_path) {
    if (@file_exists($tmp_path . '/repos.json')) {
        $_file = fopen($tmp_path . '/repos.json', 'r');
        $_repos = ( array )json_decode(fread($_file, filesize($tmp_path . '/repos.json')));
        fclose($_file);
        $repos = array();
        if (isset($_repos['msg'])) {
            return array('' => '');
        }
        foreach ($_repos as $key => $value) {
            $repos = array_merge($repos, array(($value->name) => ($value->name)));
        }
        return $repos;
    }
    return array('' => '');
}
function debug_write_log($data){
        if (!defined('_Cache_PATH') or !_Get_config('debug_log',false)){return;}      
        $file_log = fopen(_Cache_PATH.'/'.date('m-d-H-i',time()).'.log', 'a+');
        fwrite($file_log,$data);
        fclose($file_log);
 }