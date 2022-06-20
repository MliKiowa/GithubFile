<?php
defined('_TMP_PATH') or define('_TMP_PATH', dirname(__FILE__) . '/cache/tmp');
defined('_Cache_PATH') or define('_Cache_PATH', dirname(__FILE__) . '/cache');
defined('_Log_Path') or define('_Log_Path', dirname(__FILE__) . '/cache/need');
class GithubFile_Action extends Typecho_Widget implements Widget_Interface_Do {
    public function action() {
        $this->on( $this->request->is( 'do=Recache' ) )->Recache();
        $this->on( $this->request->is( 'do=DelLog' ) )->DelLog();
    }

    public function del_log() {
        $this->is_pass();
        $filename = $this->request->from( 'filename' ) ['filename'];
        @unlink( __DIR__.'/cache/'.  $filename );        
        $this->widget( 'Widget_Notice' )->set( _t( '文件已经删除' ), 'success' );
        
        $this->response->goBack();
    }

    public function Recache() {
        $this->is_pass();
        $_options = Helper::options()->plugin( 'GithubFile' );
        $file_repos = _TMP_PATH . '/repos.json';
        if ( file_exists( $file_repos ) ) {
            unlink( $file_repos );
        }
        $file_repos = fopen( $file_repos, 'w+' );
        GithubFile_Api::set_api( GithubFile_Helper::GetConfig( 'Mirror', 'https://api.github.com' ) );
GithubFile_Api::SetUser( GithubFile_Helper::GetConfig( 'Username', '' ),GithubFile_Helper::GetConfig( 'Password', '' ) );
        fwrite( $file_repos, GithubFile_Api::ReposAll( $_options->Username ) );
        fclose( $file_repos );
        header( 'HTTP/1.1 301 Moved Permanently' );
        header( 'Location: /admin/options-plugin.php?config=GithubFile' );
        exit;
    }
    public function is_pass() {
        $user = Typecho_Widget::widget( 'Widget_User' );
        if ( !$user->pass( 'administrator' ) ) {
            die( '未登录用户!' );
        }
    }
} 
