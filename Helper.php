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
