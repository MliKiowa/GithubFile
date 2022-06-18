<?php
if ( !defined( '__TYPECHO_ROOT_DIR__' ) ) exit;
class GithubFile_Helper implements Typecho_Plugin_Interface {
	public static function GetConfig( $name, $default ) {
    static $result = true;
    if ( $result === true ) {
        $_db = Typecho_Db::get();
        $result = $_db->fetchAll( $_db->select( 'value' )->from( 'table.options' )->where( 'name = ?', 'plugin:GithubFile' ) );
    }
    if ( !isset( $result[0]['value'] ) ) {
        return $default;
    }
    $_options = unserialize( $result[0]['value'] );
    return ( isset( $_options[$name] ) ? $_options[$name] : $default );
}

}