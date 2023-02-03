<?php
namespace TypechoPlugin\GithubFile;
/**
 *插件处理上传等实际逻辑部分
 * Class类名称(GithubFile_Handler)
 * @package GithubFile
 * @author Mlikiowa<nineto0@163.com>
 * @version 1.1.0
 * @since 1.0.0
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
class Handler {
    /**
     * Notes:
     * User: Mlikiowa<nineto0@163.com>
     * Date: 2022-06-22
     * Time:17:51
     * @param $file
     * @return array|bool
     */
    public static function uploadHandle($file) {
        $options = \Typecho\Widget::widget('Widget_Options')->plugin('GithubFile');
        if (empty($file['name'])) return false;
        //获取扩展名
        $ext = self::getSafeName($file['name']);
        //判定是否是允许的文件类型
        if (!\Widget\Upload::checkFileType($ext)) return false;
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
        $Api = new Api();
        $Api->setApi(Helper::GetConfig('Mirror', 'https://api.github.com'));
        $Api->SetUser(Helper::GetConfig('token', ''));
        $codearr = array("file"=>$newPath,"cdn"=> Helper::GetConfig('Cdn', 'https://fastly.jsdelivr.net/gh/'),"user"=>$options->Username,"repo"=>$options->Repo);
        $realpath = Helper::replaceCode($options->RealPath,$codearr);  
        if (!$Api->uploadFiles($options->Username, $options->Repo, $realpath, $contents)) {
            $Api->updateFiles($options->Username, $options->Repo, $realpath, $contents, $Api->getSha($options->Username, $options->Repo, $realpath));
        }
        //使用newPath并不连接$options->path URL连接时拼接
        return array('name' => $file['name'], 'path' => $realpath, 'size' => $file['size'], 'type' => $ext, 'mime' => $file['mime'] ?? self::get_mime_type($newPath),);
    }
    /**
     * Notes:
     * User: Mlikiowa<nineto0@163.com>
     * Date: 2022-06-22
     * Time:17:51
     * @param $name
     * @return string
     */
    private static function getSafeName(string & $name) {
        $name = str_replace(['"', '<', '>'], '', $name);
        $name = str_replace('\\', '/', $name);
        $name = false === strpos($name, '/') ? ('a' . $name) : str_replace('/', '/a', $name);
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
    public static function attachmentDataHandle($content) {
        $options = \Typecho\Widget::widget('Widget_Options')->plugin('GithubFile');
        //获取设置参数
        $codearr = array("file"=>substr ($content['attachment']->path, 1),"mirror"=> Helper::GetConfig('Cdn', 'https://fastly.jsdelivr.net/gh/'),"user"=>$options->Username,"repo"=>$options->Repo);
        $url = Helper::replaceCode($options->MirroPath,$codearr);
        return $url;
//废弃代码 Typecho_Common::url($content['attachment']->path, Helper::GetConfig('Cdn', 'https://fastly.jsdelivr.net/gh/') . $options->Username . '/' . $options->Repo . $options->Path);    
      }
    public static function get_mime_type($filename) {
        $pext = pathinfo($filename);
        if (!in_array('extension', $pext)) {
            return 'application/octet-stream';
        }
        $ext = $pext['extension'];
        static $mime_types = array('apk' => 'application/vnd.android.package-archive', '3gp' => 'video/3gpp', 'ai' => 'application/postscript', 'aif' => 'audio/x-aiff', 'aifc' => 'audio/x-aiff', 'aiff' => 'audio/x-aiff', 'asc' => 'text/plain', 'atom' => 'application/atom+xml', 'au' => 'audio/basic', 'avi' => 'video/x-msvideo', 'bcpio' => 'application/x-bcpio', 'bin' => 'application/octet-stream', 'bmp' => 'image/bmp', 'cdf' => 'application/x-netcdf', 'cgm' => 'image/cgm', 'class' => 'application/octet-stream', 'cpio' => 'application/x-cpio', 'cpt' => 'application/mac-compactpro', 'csh' => 'application/x-csh', 'css' => 'text/css', 'dcr' => 'application/x-director', 'dif' => 'video/x-dv', 'dir' => 'application/x-director', 'djv' => 'image/vnd.djvu', 'djvu' => 'image/vnd.djvu', 'dll' => 'application/octet-stream', 'dmg' => 'application/octet-stream', 'dms' => 'application/octet-stream', 'doc' => 'application/msword', 'dtd' => 'application/xml-dtd', 'dv' => 'video/x-dv', 'dvi' => 'application/x-dvi', 'dxr' => 'application/x-director', 'eps' => 'application/postscript', 'etx' => 'text/x-setext', 'exe' => 'application/octet-stream', 'ez' => 'application/andrew-inset', 'flv' => 'video/x-flv', 'gif' => 'image/gif', 'gram' => 'application/srgs', 'grxml' => 'application/srgs+xml', 'gtar' => 'application/x-gtar', 'gz' => 'application/x-gzip', 'hdf' => 'application/x-hdf', 'hqx' => 'application/mac-binhex40', 'htm' => 'text/html', 'html' => 'text/html', 'ice' => 'x-conference/x-cooltalk', 'ico' => 'image/x-icon', 'ics' => 'text/calendar', 'ief' => 'image/ief', 'ifb' => 'text/calendar', 'iges' => 'model/iges', 'igs' => 'model/iges', 'jnlp' => 'application/x-java-jnlp-file', 'jp2' => 'image/jp2', 'jpe' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'jpg' => 'image/jpeg', 'js' => 'application/x-javascript', 'kar' => 'audio/midi', 'latex' => 'application/x-latex', 'lha' => 'application/octet-stream', 'lzh' => 'application/octet-stream', 'm3u' => 'audio/x-mpegurl', 'm4a' => 'audio/mp4a-latm', 'm4p' => 'audio/mp4a-latm', 'm4u' => 'video/vnd.mpegurl', 'm4v' => 'video/x-m4v', 'mac' => 'image/x-macpaint', 'man' => 'application/x-troff-man', 'mathml' => 'application/mathml+xml', 'me' => 'application/x-troff-me', 'mesh' => 'model/mesh', 'mid' => 'audio/midi', 'midi' => 'audio/midi', 'mif' => 'application/vnd.mif', 'mov' => 'video/quicktime', 'movie' => 'video/x-sgi-movie', 'mp2' => 'audio/mpeg', 'mp3' => 'audio/mpeg', 'mp4' => 'video/mp4', 'mpe' => 'video/mpeg', 'mpeg' => 'video/mpeg', 'mpg' => 'video/mpeg', 'mpga' => 'audio/mpeg', 'ms' => 'application/x-troff-ms', 'msh' => 'model/mesh', 'mxu' => 'video/vnd.mpegurl', 'nc' => 'application/x-netcdf', 'oda' => 'application/oda', 'ogg' => 'application/ogg', 'ogv' => 'video/ogv', 'pbm' => 'image/x-portable-bitmap', 'pct' => 'image/pict', 'pdb' => 'chemical/x-pdb', 'pdf' => 'application/pdf', 'pgm' => 'image/x-portable-graymap', 'pgn' => 'application/x-chess-pgn', 'pic' => 'image/pict', 'pict' => 'image/pict', 'png' => 'image/png', 'pnm' => 'image/x-portable-anymap', 'pnt' => 'image/x-macpaint', 'pntg' => 'image/x-macpaint', 'ppm' => 'image/x-portable-pixmap', 'ppt' => 'application/vnd.ms-powerpoint', 'ps' => 'application/postscript', 'qt' => 'video/quicktime', 'qti' => 'image/x-quicktime', 'qtif' => 'image/x-quicktime', 'ra' => 'audio/x-pn-realaudio', 'ram' => 'audio/x-pn-realaudio', 'ras' => 'image/x-cmu-raster', 'rdf' => 'application/rdf+xml', 'rgb' => 'image/x-rgb', 'rm' => 'application/vnd.rn-realmedia', 'roff' => 'application/x-troff', 'rtf' => 'text/rtf', 'rtx' => 'text/richtext', 'sgm' => 'text/sgml', 'sgml' => 'text/sgml', 'sh' => 'application/x-sh', 'shar' => 'application/x-shar', 'silo' => 'model/mesh', 'sit' => 'application/x-stuffit', 'skd' => 'application/x-koan', 'skm' => 'application/x-koan', 'skp' => 'application/x-koan', 'skt' => 'application/x-koan', 'smi' => 'application/smil', 'smil' => 'application/smil', 'snd' => 'audio/basic', 'so' => 'application/octet-stream', 'spl' => 'application/x-futuresplash', 'src' => 'application/x-wais-source', 'sv4cpio' => 'application/x-sv4cpio', 'sv4crc' => 'application/x-sv4crc', 'svg' => 'image/svg+xml', 'swf' => 'application/x-shockwave-flash', 't' => 'application/x-troff', 'tar' => 'application/x-tar', 'tcl' => 'application/x-tcl', 'tex' => 'application/x-tex', 'texi' => 'application/x-texinfo', 'texinfo' => 'application/x-texinfo', 'tif' => 'image/tiff', 'tiff' => 'image/tiff', 'tr' => 'application/x-troff', 'tsv' => 'text/tab-separated-values', 'txt' => 'text/plain', 'ustar' => 'application/x-ustar', 'vcd' => 'application/x-cdlink', 'vrml' => 'model/vrml', 'vxml' => 'application/voicexml+xml', 'wav' => 'audio/x-wav', 'wbmp' => 'image/vnd.wap.wbmp', 'wbxml' => 'application/vnd.wap.wbxml', 'webm' => 'video/webm', 'wml' => 'text/vnd.wap.wml', 'wmlc' => 'application/vnd.wap.wmlc', 'wmls' => 'text/vnd.wap.wmlscript', 'wmlsc' => 'application/vnd.wap.wmlscriptc', 'wmv' => 'video/x-ms-wmv', 'wrl' => 'model/vrml', 'xbm' => 'image/x-xbitmap', 'xht' => 'application/xhtml+xml', 'xhtml' => 'application/xhtml+xml', 'xls' => 'application/vnd.ms-excel', 'xml' => 'application/xml', 'xpm' => 'image/x-xpixmap', 'xsl' => 'application/xml', 'xslt' => 'application/xslt+xml', 'xul' => 'application/vnd.mozilla.xul+xml', 'xwd' => 'image/x-xwindowdump', 'xyz' => 'chemical/x-xyz', 'zip' => 'application/zip');
        return isset($mime_types[$ext]) ? $mime_types[$ext] : 'application/octet-stream';
    }
    /**
     * Notes:
     * User: Mlikiowa<nineto0@163.com>
     * Date: 2022-06-22
     * Time:17:51
     * @param array $content
     * @return bool
     */
    public static function deleteHandle(array $content) {
        $options = \Typecho\Widget::widget('Widget_Options')->plugin('GithubFile');
        $Api = new Api();
        $Api->setApi(Helper::GetConfig('Mirror', 'https://api.github.com'));
        $Api->SetUser(Helper::GetConfig('token', ''));
        return $Api->delFiles($options->Username, $options->Repo,substr ($content['attachment']->path, 1), $Api->getSha($options->Username, $options->Repo, $content['attachment']->path));
    }
    /**
     * Notes:
     * User: Mlikiowa<nineto0@163.com>
     * Date: 2022-06-22
     * Time:17:51
     * @param array $content
     * @return mixed
     */
    public static function attachmentHandle(array $content) {
        $options =  \Typecho\Widget::widget('Widget_Options')->plugin('GithubFile');        
        $codearr = array("file"=>substr ($content['attachment']->path, 1),"cdn"=> Helper::GetConfig('Cdn', 'https://fastly.jsdelivr.net/gh/'),"user"=>$options->Username,"repo"=>$options->Repo);
        $url = Helper::replaceCode($options->MirroPath,$codearr);
        return $url;
//废弃代码 return \Typecho\Common::url($content['attachment']->path, Helper::GetConfig('Cdn', 'https://fastly.jsdelivr.net/gh/') . $options->Username . '/' . $options->Repo . $options->Path);
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
    public static function modifyHandle($content, $file) {
        $options =  \Typecho\Widget::widget('Widget_Options')->plugin('GithubFile');
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
        $Api = new Api();
        $Api->setApi(Helper::GetConfig('Mirror', 'https://api.github.com'));
        $Api->SetUser(Helper::GetConfig('token', ''));       
        if (!$Api->updateFiles($options->Username, $options->Repo, $path, $contents,$Api->getSha($options->Username, $options->Repo, $path))) {
            $Api->uploadFiles($options->Username, $options->Repo, $path, $contents);
        }
        //使用newPath并不连接$options->path URL连接时拼接
        return array('name' => $file['name'], 'path' => $path, 'size' => $file['size'], 'type' => $ext, 'mime' => $file['mime']  ??  self::get_mime_type($path),);
    }
}
