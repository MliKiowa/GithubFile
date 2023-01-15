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
    public static function GetConfig($name, $default)
    {
        static $result = true;
        if ($result === true) {
            $_db = \Typecho\Db::get();
            $result = $_db->fetchAll($_db->select('value')->from('table.options')->where('name = ?', 'plugin:GithubFile'));
        }
        if (!isset($result[0]['value'])) {
            return $default;
        }
        $_options = unserialize($result[0]['value']);
        return ($_options[$name] ?? $default);
    }
   public static function replaceCode($string,$replace)
   {
       $regex = "/\[(.*?)\]/";
       preg_match_all($regex, $string, $matches);
       for($i = 0; $i < count($matches[1]); $i++)
       {
           $match = $matches[1][$i];
           $newValue = $replace[$match];
           $string = str_replace($matches[0][$i], $newValue, $string);
       }
       return $string;
   }

}
