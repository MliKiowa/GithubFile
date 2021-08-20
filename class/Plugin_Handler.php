<?php
require_once dirname( dirname( __FILE__ ) ) . '/func/Helper.php';
require_once 'GithubApi.php';

class Plugin_Handler extends Typecho_Widget {
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
        $api = new GithubApi();
        $result = $api->set_api( _Get_config( 'mirror', 'https://api.github.com' ) );
        $api->set_token( _Get_config( 'token', '' ) );
        //$contents 获取二进制数据流
        if ( !$api->files_upload( $options->username, $options->repo, $options->path . $newPath, $contents ) ) {
            $api->files_updata( $options->username, $options->repo, $options->path . $newPath, $contents, $api->get_sha( $options->username, $options->repo, $options->path . $newPath ) );
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
        return Typecho_Common::url( $content['attachment']->path, _Get_config( 'cdn', 'https://cdn.jsdelivr.net/gh/' ) . $options->username . '/' . $options->repo . $options->path );
    }
    public static function deleteHandle( array $content ) {
        $options = Typecho_Widget::widget( 'Widget_Options' )->plugin( 'GithubFile' );
        $api = new GithubApi();
        $result = $api->set_api( _Get_config( 'mirror', 'https://api.github.com' ) );
        $api->set_token( _Get_config( 'token', '' ) );
        $ret = $api->files_del( $options->username, $options->repo, $options->path . $content['attachment']->path, $api->get_sha( $options->username, $options->repo, $options->path . $content['attachment']->path ) );
        return $ret;
    }
    public static function attachmentHandle( array $content ) {
        $options = Typecho_Widget::widget( 'Widget_Options' )->plugin( 'GithubFile' );
        return Typecho_Common::url( $content['attachment']->path, _Get_config( 'cdn', 'https://cdn.jsdelivr.net/gh/' ) . $options->username . '/' . $options->repo . $options->path );
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
        $api = new GithubApi();
        $result = $api->set_api( _Get_config( 'mirror', 'https://api.github.com' ) );
        $api->set_token( _Get_config( 'token', '' ) );
        if ( !$api->files_upload( $options->username, $options->repo, $options->path . $newPath, $contents ) ) {
            $api->files_updata( $options->username, $options->repo, $options->path . $newPath, $contents, $api->get_sha( $options->username, $options->repo, $options->path . $newPath ) );
        }
        //使用newPath并不连接$options->path URL连接时拼接
        return array( 'name' => $file['name'], 'path' => $Path, 'size' => $file['size'], 'type' => $ext, 'mime' => isset( $file['mime'] ) ? $file['mime'] : Typecho_Common::mimeContentType( $Path ), );
    }
}
