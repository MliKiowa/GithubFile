<?php
namespace TypechoPlugin\GithubFile;
/**
 *插件辅助功能实现部分
 * Class类名称(GithubFile_Helper)
 * @package GithubFile
 * @author Mlikiowa<nineto0@163.com>
 * @version 1.1.0
 * @since 1.0.0
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
class Helper
{
   public static function GetConfig($key,$default)
   {
    static $config = null;
    if ($config === null) {
        $config = Helper::options()->plugin('GithubFile');
    }
    return isset($config->$key) ? $config->$key :$default ;
    }
   public static function replaceCode($content, $array)
{
    extract($array); // 将数组中的键值对转换为变量和值
    $keys = array_keys($array); // 获取数组中的键名
    $keys = array_map(function ($key) {
        return '[' . $key . ']'; // 给键名加上花括号
    }, $keys);
    return str_replace($keys, $array, $content); // 替换文本中的变量名
}
}
