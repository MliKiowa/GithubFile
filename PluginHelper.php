<?php
namespace TypechoPlugin\GithubFile;

/**
 * 插件辅助功能类 PluginHelper
 *
 * @package GithubFile
 * @version 1.4.0
 * @link https://github.com/Mlikiowa/typecho-github-file/issues
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

class PluginHelper
{
    /**
     * 获取指定键对应的配置值
     * @param string $key 键名
     * @param mixed|null $default 默认值（允许为空）
     * @return mixed 配置值或默认值
     */
    public static function getConfig($key, $default = null)
    {
        static $config = null;
        if ($config === null) {
            // 初始化插件配置
            $config = Helper::options()->plugin('GithubFile')->toArray();
        }

        // 获取指定键对应的配置值
        return isset($config[$key]) ? $config[$key] : $default;
    }

    /**
     * 根据数组中的键值替换文本中相应的变量名
     *
     * @param string $content 源文本
     * @param array $array 替换用的数组
     * @return string 替换后的文本
     */
    public static function replaceCode($content, $array)
    {
        // 将数组中的键值对转换为变量和值
        extract($array);
        // 获取数组中的键名，并给键名加上花括号
        $keys = array_map(function ($key) {
            return '[' . $key . ']';
        }, array_keys($array));
        // 替换文本中的变量名，返回替换后的文本
        return str_replace($keys, array_values($array), $content);
    }
}
