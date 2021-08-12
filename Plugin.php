<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * 利用Github Repos提供附件支持
 *
 * @package GithubFile
 * @author 无绘
 * @version 1.0.3
 * @link https://github.com/MliKiowa/GithubFile
 */
defined('_TMP_PATH') or define('_TMP_PATH', dirname(__FILE__) . '/tmp');
defined('_Cache_PATH') or define('_Cache_PATH', dirname(__FILE__) . '/cache');
require_once 'func/Helper.php';
require_once 'class/Plugin_Handler.php';
class GithubFile_Plugin implements Typecho_Plugin_Interface {
    public static function _handler() {
        $array = debug_backtrace();
        unset($array[0]);
        $func = $array[2]['function'];
        $arg = func_get_args();
        debug_write_log("function:".$func."\r\n param: ".print_r($arg,true)."\r\n");                      
       return Plugin_Handler::$func(...$arg);
    }
    public static function activate() {
        /**
         * 判断是否可用HTTP库 CURL库
         * 此处说明，并非使用Typecho_Http_Client，由于并未提供PUT等操作弃用 使用从Typecho抽离修改的库提供支持
         */
        if (basename(dirname(__FILE__)) !== 'GithubFile') {
            throw new Typecho_Plugin_Exception(_t('插件目录名必须为 GithubFile'));
        }
        if (false === Typecho_Http_Client::get()) {          
           // throw new Typecho_Plugin_Exception( _t( '哇噗, 你的服务器貌似并不支持curl!' ) );            
        }
        Helper::addAction('GithubFile', 'GithubFile_Action');
        if (!file_exists(dirname(__FILE__) . '/tmp/')) {
            mkdir(dirname(__FILE__) . '/tmp/');
        }
        //缓存目录
        if (!file_exists(dirname(__FILE__) . '/cache/')) {
            mkdir(dirname(__FILE__) . '/cache/');
        }
        //其它数据 不合适已放弃以下方案
        //Typecho_Plugin::factory('index.php')->begin = ['GithubFile_Plugin', 'handler_begin'];
        //创建缓存
        Typecho_Plugin::factory('Widget_Upload')->uploadHandle = ['GithubFile_Plugin', '_handler'];
        //修改
        Typecho_Plugin::factory('Widget_Upload')->modifyHandle = ['GithubFile_Plugin', '_handler'];
        //删除
        Typecho_Plugin::factory('Widget_Upload')->deleteHandle = ['GithubFile_Plugin', '_handler'];
        //路径参数处理
        Typecho_Plugin::factory('Widget_Upload')->attachmentHandle = ['GithubFile_Plugin', '_handler'];
        //文件内容数据
        Typecho_Plugin::factory('Widget_Upload')->attachmentDataHandle = ['GithubFile_Plugin', '_handler'];
        return _t('可以使用啦~');
    }
    public static function deactivate() {
    if(Typecho_Widget::widget('Widget_Options')->plugin('GithubFile')->debug_log)
    { Helper::removePanel(1, 'GithubFile/LogList.php');}        
        Helper::removeAction('GithubFile');
        return _t('已经关闭啦~');
    }
    public static function personalConfig(Typecho_Widget_Helper_Form $form) {
    }
    public static function configHandle($config, $isInit)
    {
    if (!$isInit) {
    if(($config['debug_log']) !== (Typecho_Widget::widget('Widget_Options')->plugin('GithubFile')->debug_log)){
         if ($config['debug_log']) {
         Helper::addPanel(1, 'GithubFile/LogList.php', '插件日志', '日志内容', 'administrator');         
        }else{Helper::removePanel(1, 'GithubFile/LogList.php');}
        }
}
        Helper::configPlugin('GithubFile', $config);
    }
    public static function config(Typecho_Widget_Helper_Form $form) {
        $_Server = _Get_config('server', 'http://gitauth.moennar.cn');
        echo '<a href="' . $_Server . '/auth.php?source_site=';
        Helper::options()->siteUrl();
        echo '" >点击获取Token  </a>';
        echo '<a href="/action/GithubFile?do=Recache" >   点击获取刷新缓存</a>';
        $t = new Typecho_Widget_Helper_Form_Element_Text('token', null, null, _t('Token'), _t('请登录Github获取'));
        $form->addInput($t->addRule('required', _t('不能哦~')));
        $t = new Typecho_Widget_Helper_Form_Element_Text('username', null, null, _t('用户名'), _t(''));
        $form->addInput($t->addRule('required', _t('不能为空哦~')));
        $t = new Typecho_Widget_Helper_Form_Element_Radio('repo', _Get_tmp_repos(_TMP_PATH), 'None', _t('仓库名'), _t(''));
        $form->addInput($t);
        $t = new Typecho_Widget_Helper_Form_Element_Text('path', null, '/GithubFile/', _t('储存路径'), _t('需要以/结束 否则触发错误'));
        $form->addInput($t->addRule('required', _t('不能哦~')));
        $t = new Typecho_Widget_Helper_Form_Element_Radio('debug_log', array(true => '开启',false => '关闭'), false, _t('调试设置'), _t('启用后记录调试日志'));
        $form->addInput($t);
        $t = new Typecho_Widget_Helper_Form_Element_Text('server', null, 'http://gitauth.moennar.cn', _t('Server'), _t('填写授权服务器 如授权失败请及时到Github获取'));
        $form->addInput($t->addRule('required', _t('不能哦~')));
        $t = new Typecho_Widget_Helper_Form_Element_Text('mirror', null, 'https://api.github.com', _t('API_Mirror'), _t('加速API提供Mirror'));
        $form->addInput($t->addRule('required', _t('不能哦~')));
        $t = new Typecho_Widget_Helper_Form_Element_Text('cdn', null, 'https://cdn.jsdelivr.net/gh/', _t('File_Mirror'), _t('加速提供文件'));
        $form->addInput($t->addRule('required', _t('不能哦~')));
    }
}
