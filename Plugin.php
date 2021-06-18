<?php
if(!defined( '__TYPECHO_ROOT_DIR__' )) exit;
/**
* 利用Github一键式启用附件插件
*
* @package GithubStatic
* @author 乔千
* @version 6.0.2
* @link https://www.yundreams.cn
*/

require   dirname( __FILE__ ) . '/Helper.php';
class GithubStatic_Plugin implements Typecho_Plugin_Interface
 {
    public static $action = 'GithubStatic';
    public static function activate()
 {
        /**
        * 判断是否可用HTTP库
        * 此处说明，并非使用Typecho_Http_Client，由于并未提供PUT等操作弃用 使用Helper.php中辅助函数提供API。
        */
        if ( false == Typecho_Http_Client::get() ) {
            throw new Typecho_Plugin_Exception( _t( '哇噗, 你的服务器貌似并不支持Curl!' ) );
        }
        Helper::addPanel(1, 'GithubStatic/Debug.php', _t("Github诊断面板"), _t('Github诊断面板'),'administrator');
        Helper::addAction(self::$action, 'GithubStatic_Action' );
        if ( !file_exists( dirname( __FILE__ ) . '/cache/' ) ) {
            mkdir( dirname( __FILE__ ) . '/cache/' );
        }
        //创建缓存
        Typecho_Plugin::factory( 'Widget_Upload' )->uploadHandle = array( 'GithubStatic_Plugin', 'uploadHandle' );
        //修改
        Typecho_Plugin::factory( 'Widget_Upload' )->modifyHandle = array( 'GithubStatic_Plugin', 'modifyHandle' );
        //删除
        Typecho_Plugin::factory( 'Widget_Upload' )->deleteHandle = array( 'GithubStatic_Plugin', 'deleteHandle' );
        //路径参数处理
        Typecho_Plugin::factory( 'Widget_Upload' )->attachmentHandle = array( 'GithubStatic_Plugin', 'attachmentHandle' );
        //文件内容数据
        Typecho_Plugin::factory( 'Widget_Upload' )->attachmentDataHandle = array( 'GithubStatic_Plugin', 'attachmentDataHandle' );
        return _t( '可以使用啦~' );
    }

    public static function deactivate()
 {
        Helper::removePanel(1, 'GithubStatic/Debug.php');
        return _t( '已经关闭啦~' );
    }
    public static function personalConfig( Typecho_Widget_Helper_Form $form )
 {
    }
    public static function uploadHandle( $file )
 {
        if ( empty( $file['name'] ) ) return false;
        //获取扩展名
        $ext = self::getSafeName( $file['name'] );
        //判定是否是允许的文件类型
        if ( !Widget_Upload::checkFileType( $ext ) ) return false;

        //获取文件名 如果需要可修改规则
        //注意流
        $filePath = date( 'Y' ) . '/' . date( 'm' ) . '/' . date( 'd' ) . '/';
        $fileName = time() . '.' . $ext;

        //上传文件的路径+名称
        $newPath = $filePath.$fileName;
        //获取插件参数
        $options = Typecho_Widget::widget( 'Widget_Options' )->plugin( 'GithubStatic' );
        //如果没有临时文件，则使用流上传
        if ( isset( $file['tmp_name'] ) ) {
            $srcPath = $file['tmp_name'];
            $handle = fopen( $srcPath, 'r' );
            $contents = fread( $handle, $file['size'] );
        } else if ( isset( $file['bytes'] ) ) {
            $contents = $file['bytes'] ;
        } else if ( isset( $file['bits'] ) ) {
            $contents = $file['bits'] ;
        } else {
            return false;
        }

        if ( !isset( $file['size'] ) ) {
            $file['size'] = filesize( $file['tmp_name'] );           
        }

        //$contents 获取二进制数据流
        if ( !Github_files_upload( $options->username, $options->token, $options->repo, $options->path.$newPath, $contents ) ) {
            Github_files_updata( $options->username, $options->token, $options->repo, $options->path.$newPath, $contents, Github_get_sha( $options->username, $options->repo,  $options->path.$newPath, $options->token ) );
        }
        //使用newPath并不连接$options->path URL连接时拼接
        return array(
            'name' => $file['name'],
            'path' => $newPath,
            'size' => $file['size'],
            'type' => $ext,
            'mime' => isset( $file['mime'] ) ? $file['mime'] : Typecho_Common::mimeContentType( $newPath ),
        );
    }
    public static function config( Typecho_Widget_Helper_Form $form )
 {
        $auth_server = Typecho_Widget::widget( 'Widget_Options' )->plugin( 'GithubStatic' )->auth_server;
        $auth_server = (isset($auth_server) or !empty($auth_server)) ? $auth_server : "http://dev.yundreams.cn";
        echo '<a href="'.$auth_server.'/Auth.php?source_site=';
        Helper::options()->siteUrl();
        echo '" >点击获取Token  </a>';     
        echo '<a href="/action/GitStatic?do=Recache" >   点击获取刷新缓存</a>';
             

        $t = new Typecho_Widget_Helper_Form_Element_Text( 'token',
        null, null,
        _t( 'Token' ),
        _t( '请登录Github获取' ) );
        $form->addInput( $t->addRule( 'required', _t( 'token不能为空哦~' ) ) );

        $t = new Typecho_Widget_Helper_Form_Element_Text( 'username',
        null, null,
        _t( '用户名' ),
        _t( '' ) );
        $form->addInput( $t->addRule( 'required', _t( '不能为空哦~' ) ) );

        $repos = array();
        $new_repos = array();
        //目录存在不一定代表缓存刷新
        if ( @file_exists( dirname( __FILE__ ).'/cache/repos.json' ) ) {
            $temp_file = fopen( dirname( __FILE__ ).'/cache/repos.json', 'r' );
            $repos_json = ( array )json_decode( fread( $temp_file, filesize( dirname( __FILE__ ).'/cache/repos.json' ) ) );
            fclose( $temp_file );
            foreach ( $repos_json as $key => $value ) {
                $new_repos = array_merge( $new_repos, array( $value->name =>$value->name ) );
            }
        }

        $t = new Typecho_Widget_Helper_Form_Element_Radio(
            'repo',
            $new_repos,
            'blog',
            _t( '仓库名' ),
            _t( '' )
        );
        $form->addInput( $t );

        $t = new Typecho_Widget_Helper_Form_Element_Text( 'path',
        null, '/Githubstatic/',
        _t( '储存路径' ),
        _t( '需要以/结束 否则触发错误' ) );
        $form->addInput( $t->addRule( 'required', _t( '不能为空哦~' ) ) );

        $t = new Typecho_Widget_Helper_Form_Element_Radio( 'debug',
        array(true=>"启用",false=>"关闭"),false,
        _t( 'Debng Mode' ),
        _t( '开启后将会启用调试模式' ) );
        $form->addInput( $t );

        $t = new Typecho_Widget_Helper_Form_Element_Text( 'auth_sever',
        null, "http://dev.yundreams.cn"，
        _t( 'Server' ),
        _t( '填写授权服务器如授权服务器宕机请切换' ) );
        $form->addInput( $t->addRule( 'required', _t( '不能为空哦~' ) ) );

       $t = new Typecho_Widget_Helper_Form_Element_Text( 'api_mirror',
        null, "https://api.github.com",
        _t( 'API_Mirror' ),
        _t( '可加速API操作如请默认' ) );
        $form->addInput( $t->addRule( 'required', _t( '不能为空哦~' ) ) );


    }
    private static function getSafeName( &$name )
 {
        $name = str_replace( array( '"', '<', '>' ), '', $name );
        $name = str_replace( '\\', '/', $name );
        $name = false === strpos( $name, '/' ) ? ( 'a' . $name ) : str_replace( '/', '/a', $name );
        $info = pathinfo( $name );
        $name = substr( $info['basename'], 1 );
        return isset( $info['extension'] ) ? strtolower( $info['extension'] ) : '';
    }

    public static function attachmentDataHandle($content)
    {
        //获取设置参数
        $options = Typecho_Widget::widget( 'Widget_Options' )->plugin( 'GithubStatic' );
        return   file_get_contents(Typecho_Common::url($content['attachment']->path, 'https://cdn.jsdelivr.net/gh/'. $options->username.'/'.$options->repo.$options->path ));   
    }

    public static function deleteHandle(array $content)
    {
        $options = Typecho_Widget::widget( 'Widget_Options' )->plugin( 'GithubStatic' );
        $ret = Github_files_del( $options->username, $options->token, $options->repo, $options->path.$content['attachment']->path, Github_get_sha( $options->username, $options->repo, $options->path.$content['attachment']->path, $options->token ) );
        return $ret;
    }
    public static function attachmentHandle( array $content )
 {
        $options = Typecho_Widget::widget( 'Widget_Options' )->plugin( 'GithubStatic' );
        return Typecho_Common::url($content['attachment']->path, 'https://cdn.jsdelivr.net/gh/'. $options->username.'/'.$options->repo.$options->path);
    }
    public static function modifyHandle( $content, $file )
 {
        if ( empty( $file['name'] ) ) return false;
        //获取扩展名
        $ext = self::getSafeName( $file['name'] );
        //判定是否是允许的文件类型
        if ( !Widget_Upload::checkFileType( $ext ) ) return false;
        //获取设置参数
        $options = Typecho_Widget::widget( 'Widget_Options' )->plugin( 'GithubStatic' );
        //获取文件路径
        $path = $content['attachment']->path;
        //获得上传文件

        if ( isset( $file['tmp_name'] ) ) {
            $srcPath = $file['tmp_name'];
            $handle = fopen( $srcPath, 'r' );
            $contents = fread( $handle, $file['size'] );
        } else if ( isset( $file['bytes'] ) ) {
            $contents = $file['bytes'] ;
        } else if ( isset( $file['bits'] ) ) {
            $contents = $file['bits'] ;
        } else {
            return false;
        }

        if ( !isset( $file['size'] ) ) {
            $file['size'] = filesize( $file['tmp_name'] );           
        }
        if ( !Github_files_upload( $options->username, $options->token, $options->repo, ($options->Path).$Path, $contents ) ) {
            Github_files_updata( $options->username, $options->token, $options->repo, ($options->path).$Path, $contents, Github_get_sha( $options->username, $options->repo, ($options->path).$Path, $options->token ) );
        }
        //使用newPath并不连接$options->path URL连接时拼接
        return array(
            'name' => $file['name'],
            'path' => $Path,
            'size' => $file['size'],
            'type' => $ext,
            'mime' => isset( $file['mime'] ) ? $file['mime'] : Typecho_Common::mimeContentType( $Path ),
        );

    }
}
