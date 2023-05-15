<?php
namespace TypechoPlugin\GithubFile;

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

class Handler {
    public static $gapi; // 提取公共变量

    public static function __callStatic($method, $args) 
    {
        static $initialized = false;
        if (!$initialized) {
            self::$gapi = Api::getInstance(); // 获取单例实例
            self::$gapi->setApi(PluginHelper::getConfig('ApiMirror', ''));
            self::$gapi->setToken(PluginHelper::getConfig('ApiMirror', ''));
            $initialized = true;
        }

        // 在执行任何静态方法之前执行这里的代码   
        // 执行我们要调用的静态方法
        if (method_exists(__CLASS__, $method)) {
            call_user_func_array([__CLASS__, $method], $args);
        }
    }

    public static function uploadHandle(array $file)
    {
        if (empty($file['name'])) {
            return false;
        }

        $ext = self::getSafeName($file['name']);

        if (!self::checkFileType($ext)) {
            return false;
        }     
        $date = new Date();
        $path = Common::url(
            defined('__TYPECHO_UPLOAD_DIR__') ? __TYPECHO_UPLOAD_DIR__ : self::UPLOAD_DIR,
            defined('__TYPECHO_UPLOAD_ROOT_DIR__') ? __TYPECHO_UPLOAD_ROOT_DIR__ : __TYPECHO_ROOT_DIR__
        ) ;

        //创建上传目录
        if (!is_dir($path) && !self::makeUploadDir($path)) { // 合理使用参数默认值和三目运算符
            return false;
        }

        //获取文件名
        //未来添加规则生成
        //原始文件名 时间戳 等       
        $fileName = sprintf('%u', crc32(uniqid())) . '.' . $ext;
        $path = $path . '/' . $fileName;

        if (isset($file['tmp_name'])) {
            //移动上传文件
            if (!@move_uploaded_file($file['tmp_name'], $path)) {
                return false;
            }
        } elseif (isset($file['bytes']) || isset($file['bits'])) { // 合理使用参数默认值和三目运算符
            //直接写入文件
            if (!file_put_contents($path, $file['bytes'] ?? $file['bits'])) {
                return false;
            }
        } else {
            return false;
        }

        if (!isset($file['size'])) {
            $file['size'] = filesize($path);
        }
     
        $mime = Common::mimeContentType($path);
        $Gpath = PluginHelper::replaceCode(
            PluginHelper::getConfig("FileRule",""),
            array( "TimeStamp"=>sprintf("%010d", time()),           
                   "FileMd5"=>md5_file($path),
                   "FileOrginalName"=>$file["name"],
                   "ext"=>$ext,
                   "FileName"=>$fileName
                 )
                 );
        if (file_exists($path)) {
        // 读取文件内容，并进行 Base64 编码
        $upfile = file_get_contents($path); 
        // 删除文件
        @unlink($path);
        } else {return false;}
        //随后删除本地文件
        $Username = PluginHelper::getConfig('Username', '');
      $   Repo = PluginHelper::getConfig('Repo', '');
        if (!self::$gapi->uploadFiles($Username, $Repo, $gpath, $upfile)) {
            self::$gapi->updateFiles($Username, $Repo, $gpath, $upfile, $Api->getSha($Username, $Repo, $gpath));
        }
     
        //返回相对存储路径
        return [
            'name' => $file['name'],
            'path' => (defined('__TYPECHO_UPLOAD_DIR__') ? __TYPECHO_UPLOAD_DIR__ : self::UPLOAD_DIR)
                . '/' . $date->year . '/' . $date->month . '/' . $fileName,
            'size' => $file['size'],
            'type' => $ext,
            'mime' => $mime
        ];
    }                            

    private static function getSafeName(string & $name) {
        $name = str_replace(['"', '<', '>'], '', $name);
        $name = str_replace('\\', '/', $name);
        $name = false === strpos($name, '/') ? ('a' . $name) : str_replace('/', '/a', $name);
        $info = pathinfo($name);
        $name = substr($info['basename'], 1);
        return isset($info['extension']) ? strtolower($info['extension']) : '';
    }

    public static function deleteHandle(array $content) {
        $options = \Typecho\Widget::widget('Widget_Options')->plugin('GithubFile');
        return $gapi->delFiles($options->Username, $options->Repo,substr ($content['attachment']->path, 1), $Api->getSha($options->Username, $options->Repo, $content['attachment']->path));
    }

    public static function attachmentHandle(array $content) {
        $options = \Typecho\Widget::widget('Widget_Options')->plugin('GithubFile');        
        $codearr = array(
            "file"=>substr ($content['attachment']->path, 1),
            "cdn"=> Helper::GetConfig('Cdn', 'https://fastly.jsdelivr.net/gh/'),
            "user"=>$options->Username,
            "repo"=>$options->Repo
        );
        $url = Helper::replaceCode($options->MirroPath,$codearr);
        return $url;
    }

    public static function modifyHandle(array $content, array $file)
    {
        if (empty($file['name']) || $content['attachment']->type != self::getSafeName($file['name'])) {
            return false;
        }

        $path = Common::url(
            $content['attachment']->path,
            defined('__TYPECHO_UPLOAD_ROOT_DIR__') ? __TYPECHO_UPLOAD_ROOT_DIR__ : __TYPECHO_ROOT_DIR__
        );
        $dir = dirname($path);

        //创建上传目录
        if (!is_dir($dir) && !self::makeUploadDir($dir)) { // 合理使用参数默认值和三目运算符
            return false;
        }

        if (isset($file['tmp_name']) || isset($file['bytes']) || isset($file['bits'])) { // 合理使用参数默认值和三目运算符
            @unlink($path);

            //移动或写入文件
            if (
                (isset($file['tmp_name']) && !@move_uploaded_file($file['tmp_name'], $path)) ||
                ((isset($file['bytes']) || isset($file['bits'])) && !file_put_contents($path, $file['bytes'] ?? $file['bits']))
            ) {
                return false;
            }
        } else {
            return false;
        }

        if (!isset($file['size'])) {
            $file['size'] = filesize($path);
        }

        //返回相对存储路径
        return [
            'name' => $content['attachment']->name,
            'path' => $content['attachment']->path,
            'size' => $file['size'],
            'type' => $content['attachment']->type,
            'mime' => $content['attachment']->mime
        ];
    }
     private static function makeUploadDir(string $path): bool
    {
        $path = preg_replace("/\\\+/", '/', $path);
        $current = rtrim($path, '/');
        $last = $current;

        while (!is_dir($current) && false !== strpos($path, '/')) {
            $last = $current;
            $current = dirname($current);
        }

        if ($last == $current) {
            return true;
        }
        if (!@mkdir($last, 0755)) {
            return false;
        }

        return self::makeUploadDir($path);
    }
    
    public static function attachmentDataHandle(array $content): string
    {    
        return file_get_contents(
            Common::url(
                $content['attachment']->path,
                defined('__TYPECHO_UPLOAD_ROOT_DIR__') ? __TYPECHO_UPLOAD_ROOT_DIR__ : __TYPECHO_ROOT_DIR__
            )
        );
    }
}
