<?php
/**
 *插件处理上传等实际逻辑部分
 * Class类名称(GithubFile_Handler)
 * @package GithubFile
 * @author Mlikiowa<nineto0@163.com>
 * @version 1.1.0
 * @since 1.0.0
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

class GithubFile_Handler
{
    /**
     * Notes:
     * User: Mlikiowa<nineto0@163.com>
     * Date: 2022-06-22
     * Time:17:51
     * @return mixed
     */
    public static function Router(): mixed
    {
        $array = debug_backtrace();
        unset($array[0]);
        $func = $array[2]['function'];
        $arg = func_get_args();
        return self::$func(...$arg);
    }

    /**
     * Notes:
     * User: Mlikiowa<nineto0@163.com>
     * Date: 2022-06-22
     * Time:17:51
     * @param $file
     * @return array|bool
     */
    public static function uploadHandle($file): array|bool
    {
        $options = Typecho_Widget::widget('Widget_Options')->plugin('GithubFile');
        if (empty($file['name'])) return false;
        //获取扩展名
        $ext = self::getSafeName($file['name']);
        //判定是否是允许的文件类型
        if (!Widget_Upload::checkFileType($ext)) return false;
        //获取文件名 如果需要可修改规则
        //注意流
        $filePath = date('Y') . '/' . date('m') . '/' . date('d') . '/';
        $fileName = time() . '.' . $ext;
        //上传文件的路径+名称
        $newPath = $filePath . $fileName;
        //获取插件参数
        //如果没有临时文件，则使用流上传
        if (isset($file['tmp_name'])) {
            $srcPath = $file['tmp_name'];
            $handle = fopen($srcPath, 'r');
            $contents = fread($handle, $file['size']);
        } else if (isset($file['bytes'])) {
            $contents = $file['bytes'];
        } else if (isset($file['bits'])) {
            $contents = $file['bits'];
        } else {
            return false;
        }
        if (!isset($file['size'])) {
            $file['size'] = filesize($file['tmp_name']);
        }
        $Api = new GithubFile_Api();
        $Api->setApi(GithubFile_Helper::GetConfig('Mirror', 'https://api.github.com'));
        $Api->SetUser(GithubFile_Helper::GetConfig('token', ''));
        if (!$Api->uploadFiles($options->Username, $options->Repo, $options->Path . $newPath, $contents)) {
            $Api->updateFiles($options->Username, $options->Repo, $options->Path . $newPath, $contents, $Api->getSha($options->Username, $options->Repo, $options->Path . $newPath));
        }
        //使用newPath并不连接$options->path URL连接时拼接
        return array('name' => $file['name'], 'path' => $newPath, 'size' => $file['size'], 'type' => $ext, 'mime' => $file['mime'] ?? Typecho_Common::mimeContentType($newPath),);
    }

    /**
     * Notes:
     * User: Mlikiowa<nineto0@163.com>
     * Date: 2022-06-22
     * Time:17:51
     * @param $name
     * @return string
     */
    private static function getSafeName(&$name): string
    {
        $name = str_replace(array('"', '<', '>'), '', $name);
        $name = str_replace('\\', '/', $name);
        $name = !str_contains($name, '/') ? ('a' . $name) : str_replace('/', '/a', $name);
        $info = pathinfo($name);
        $name = substr($info['basename'], 1);
        return isset($info['extension']) ? strtolower($info['extension']) : '';
    }

    /**
     * Notes:
     * User: Mlikiowa<nineto0@163.com>
     * Date: 2022-06-22
     * Time:17:51
     * @param $content
     * @return mixed
     */
    public static function attachmentDataHandle($content)
    {
        $options = Typecho_Widget::widget('Widget_Options')->plugin('GithubFile');
        //获取设置参数
        return Typecho_Common::url($content['attachment']->path, GithubFile_Helper::GetConfig('Cdn', 'https://fastly.jsdelivr.net/gh/') . $options->Username . '/' . $options->Repo . $options->Path);
    }

    /**
     * Notes:
     * User: Mlikiowa<nineto0@163.com>
     * Date: 2022-06-22
     * Time:17:51
     * @param array $content
     * @return bool
     */
    public static function deleteHandle(array $content)
    {
        $options = Typecho_Widget::widget('Widget_Options')->plugin('GithubFile');
        $Api = new GithubFile_Api();
        $Api->setApi(GithubFile_Helper::GetConfig('Mirror', 'https://api.github.com'));
        $Api->SetUser(GithubFile_Helper::GetConfig('token', ''));
       return $Api->delFiles($options->Username, $options->Repo, $options->Path . $content['attachment']->path, $Api->getSha($options->Username, $options->Repo, $options->Path . $content['attachment']->path));
    }

    /**
     * Notes:
     * User: Mlikiowa<nineto0@163.com>
     * Date: 2022-06-22
     * Time:17:51
     * @param array $content
     * @return mixed
     */
    public static function attachmentHandle(array $content)
    {
        $options = Typecho_Widget::widget('Widget_Options')->plugin('GithubFile');
        return Typecho_Common::url($content['attachment']->path, GithubFile_Helper::GetConfig('Cdn', 'https://fastly.jsdelivr.net/gh/') . $options->Username . '/' . $options->Repo . $options->Path);
    }

    /**
     * Notes:
     * User: Mlikiowa<nineto0@163.com>
     * Date: 2022-06-22
     * Time:17:51
     * @param $content
     * @param $file
     * @return array|false
     */
    public static function modifyHandle($content, $file): bool|array
    {
        $options = Typecho_Widget::widget('Widget_Options')->plugin('GithubFile');
        if (empty($file['name'])) return false;
        //获取扩展名
        $ext = self::getSafeName($file['name']);
        //判定是否是允许的文件类型
        if (!Widget_Upload::checkFileType($ext)) return false;
        //获取文件路径
        $path = $content['attachment']->path;
        //获得上传文件
        if (isset($file['tmp_name'])) {
            $srcPath = $file['tmp_name'];
            $handle = fopen($srcPath, 'r');
            $contents = fread($handle, $file['size']);
        } else if (isset($file['bytes'])) {
            $contents = $file['bytes'];
        } else if (isset($file['bits'])) {
            $contents = $file['bits'];
        } else {
            return false;
        }
        if (!isset($file['size'])) {
            $file['size'] = filesize($file['tmp_name']);
        }
        //$contents 获取二进制数据流
        $Api = new GithubFile_Api();
        $Api->setApi(GithubFile_Helper::GetConfig('Mirror', 'https://api.github.com'));
        $Api->SetUser(GithubFile_Helper::GetConfig('token', ''));
        if (!$Api->updateFiles($options->Username, $options->Repo, $path, $contents, $Api->getSha($options->Username, $options->Repo, $path))) {
            $Api->uploadFiles($options->Username, $options->Repo, $path, $contents);
        }
        //使用newPath并不连接$options->path URL连接时拼接
        return array('name' => $file['name'], 'path' => $path, 'size' => $file['size'], 'type' => $ext, 'mime' => $file['mime'] ?? Typecho_Common::mimeContentType($path),);
    }
}
