<?php
if ( !defined( '__TYPECHO_ROOT_DIR__' ) ) exit;
class GithubFile_Handler implements Typecho_Plugin_Interface {
	public static Router(){
	$array = debug_backtrace();
    unset($array[0]);
    $func = $array[2]['function'];
    $arg = func_get_args();
    $ret = self::$func( ...$arg );
    return $ret;
	}
    public static function uploadHandle( $file ) {
        $options = Typecho_Widget::widget( 'Widget_Options' )->plugin( 'GithubFile' );
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
        $newPath = $filePath . $fileName;
        //获取插件参数
        //如果没有临时文件，则使用流上传
        if ( isset( $file['tmp_name'] ) ) {
            $srcPath = $file['tmp_name'];
            $handle = fopen( $srcPath, 'r' );
            $contents = fread( $handle, $file['size'] );
        } else if ( isset( $file['bytes'] ) ) {
            $contents = $file['bytes'];
        } else if ( isset( $file['bits'] ) ) {
            $contents = $file['bits'];
        } else {
            return false;
        }
        if ( !isset( $file['size'] ) ) {
            $file['size'] = filesize( $file['tmp_name'] );
        }
         GithubFile_Api::set_api( GithubFile_Helper::GetConfig( 'Mirror', 'https://api.github.com' ) );
         GithubFile_Api::SetUser( GithubFile_Helper::GetConfig( 'Username', '' ),GithubFile_Helper::GetConfig( 'Password', '' ) );
        //$contents 获取二进制数据流
        if ( !  GithubFile_Api::FilesUpload( $options->Username, $options->Repo, $options->Path . $newPath, $contents ) ) {
            GithubFile_Api::FilesUpdata( $options->Username, $options->Repo, $options->Path . $newPath, $contents, GithubFile_Api::GetSha( $options->Username, $options->Repo, $options->Path . $newPath ) );
        }
        //使用newPath并不连接$options->path URL连接时拼接
        return array( 'name' => $file['name'], 'path' => $newPath, 'size' => $file['size'], 'type' => $ext, 'mime' => isset( $file['mime'] ) ? $file['mime'] : Typecho_Common::mimeContentType( $newPath ), );
    }
    private static function getSafeName( &$name ) {
        $name = str_replace( array( '"', '<', '>' ), '', $name );
        $name = str_replace( '\\', '/', $name );
        $name = false === strpos( $name, '/' ) ? ( 'a' . $name ) : str_replace( '/', '/a', $name );
        $info = pathinfo( $name );
        $name = substr( $info['basename'], 1 );
        return isset( $info['extension'] ) ? strtolower( $info['extension'] ) : '';
    }
    public static function attachmentDataHandle( $content ) {
        $options = Typecho_Widget::widget( 'Widget_Options' )->plugin( 'GithubFile' );
        //获取设置参数
        return Typecho_Common::url( $content['attachment']->path, GithubFile_Helper::GetConfig(  'Cdn', 'https://cdn.jsdelivr.net/gh/' ) . $options->Username . '/' . $options->Repo . $options->Path );
    }
    public static function deleteHandle( array $content ) {
        $options = Typecho_Widget::widget( 'Widget_Options' )->plugin( 'GithubFile' );
        GithubFile_Api::set_api( GithubFile_Helper::GetConfig( 'Mirror', 'https://api.github.com' ) );
        GithubFile_Api::SetUser( GithubFile_Helper::GetConfig( 'Username', '' ),GithubFile_Helper::GetConfig( 'Password', '' ) );
        $ret = GithubFile_Api::FilesDel( $options->Username, $options->Repo, $options->Path . $content['attachment']->path, GithubFile_Api::GetSha( $options->Username, $options->Repo, $options->Path . $content['attachment']->path ) );
        return $ret;
    }
    public static function attachmentHandle( array $content ) {
        $options = Typecho_Widget::widget( 'Widget_Options' )->plugin( 'GithubFile' );
        return Typecho_Common::url( $content['attachment']->path,  GithubFile_Helper::GetConfig(  'Cdn', 'https://cdn.jsdelivr.net/gh/' ) . $options->Username . '/' . $options->Repo . $options->Path );
    }
    public static function modifyHandle( $content, $file ) {
        $options = Typecho_Widget::widget( 'Widget_Options' )->plugin( 'GithubFile' );
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
            $contents = $file['bytes'];
        } else if ( isset( $file['bits'] ) ) {
            $contents = $file['bits'];
        } else {
            return false;
        }
        if ( !isset( $file['size'] ) ) {
            $file['size'] = filesize( $file['tmp_name'] );
        }
        //$contents 获取二进制数据流
        GithubFile_Api::set_api( GithubFile_Helper::GetConfig( 'Mirror', 'https://api.github.com' ) );
        GithubFile_Api::SetUser( GithubFile_Helper::GetConfig( 'Username', '' ),GithubFile_Helper::GetConfig( 'Password', '' ) );
        if ( !GithubFile_Api::FilesUpload( $options->Username, $options->Repo, $options->Path . $newPath, $contents ) ) {
            GithubFile_Api::FilesUpdata( $options->Username, $options->Repo, $options->Path . $newPath, $contents, GithubFile_Api::GetSha( $options->Username, $options->Repo, $options->Path . $newPath ) );
        }
        //使用newPath并不连接$options->path URL连接时拼接
        return array( 'name' => $file['name'], 'path' => $path, 'size' => $file['size'], 'type' => $ext, 'mime' => isset( $file['mime'] ) ? $file['mime'] : Typecho_Common::mimeContentType( $path ), );
    }
}