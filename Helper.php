<?php
/**
 *插件辅助功能实现部分
 * Class类名称(GithubFile_Helper)
 * @package GithubFile
 * @author Mlikiowa<nineto0@163.com>
 * @version 1.1.0
 * @since 1.0.0
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

class GithubFile_Helper implements Typecho_Plugin_Interface
{
    public static function GetConfig($name, $default):mixed
    {
        static $result = true;
        if ($result === true) {
            $_db = Typecho_Db::get();
            $result = $_db->fetchAll($_db->select('value')->from('table.options')->where('name = ?', 'plugin:GithubFile'));
        }
        if (!isset($result[0]['value'])) {
            return $default;
        }
        $_options = unserialize($result[0]['value']);
        return ($_options[$name] ?? $default);
    }

}
