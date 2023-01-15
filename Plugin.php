<?php
namespace TypechoPlugin\GithubFile;

use Typecho\Plugin\PluginInterface;
use Typecho\Widget\Helper\Form;
use Utils\Helper;
use Typecho\Widget\Helper\Form\Element\Radio;
use Typecho\Widget\Helper\Form\Element\Text;
use Widget\Options;
use Typecho\Plugin\Exception as PluginException;

/**
 * Use GitHub Repos To Update Attachment
 *
 * @package GithubFile
 * @author Mlikiowa<nineto0@163.com>
 * @version 1.3.3
 * @license MIT License
 * @link https://github.com/MliKiowa/GithubFile
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
defined('_CACHE_PATH') or define('_CACHE_PATH', dirname(__FILE__) . '/cache/');
defined('_TMP_PATH') or define('_TMP_PATH', dirname(__FILE__) . '/cache/tmp/');
defined('_LOG_PATH') or define('_LOG_PATH', dirname(__FILE__) . '/cache/log/');

class Plugin implements PluginInterface
{
    public static function personalConfig( Form $form )
    {
    unset ($form);
    }
    public static function activate()
    {
        /**
         * 判断是否可用HTTP库 CURL库
         * 此处说明，并非使用Typecho_Http_Client，由于并未提供PUT等操作弃用 使用从Typecho抽离修改的库提供支持
         */
        if (basename(dirname(__FILE__)) !== 'GithubFile') {
            throw new PluginException(_t('The plugin name must be GithubFile'));
        }
        //生成相关目录
        if (!file_exists(_CACHE_PATH)) {
            @mkdir(_CACHE_PATH);  
            @mkdir(_TMP_PATH);
            @mkdir(_LOG_PATH);
        }
        //挂接钩子
        Helper::addAction( 'GithubFile', 'GithubFile_Action' );
        //上传
        \Typecho\Plugin::factory('Widget_Upload')->uploadHandle = ['GithubFile_Handler', 'uploadHandle'];
        //修改
        \Typecho\Plugin::factory('Widget_Upload')->modifyHandle = ['GithubFile_Handler', 'modifyHandle'];
        //删除
        \Typecho\Plugin::factory('Widget_Upload')->deleteHandle = ['GithubFile_Handler', 'deleteHandle'];
        //路径参数处理
        \Typecho\Plugin::factory('Widget_Upload')->attachmentHandle = ['GithubFile_Handler', 'attachmentHandle'];
        //文件内容数据
        \Typecho\Plugin::factory('Widget_Upload')->attachmentDataHandle = ['GithubFile_Handler', 'attachmentDataHandle'];
        return _t('可以使用啦~');
    }

    public static function deactivate()
    {
        if (\Typecho\Widget::widget('Widget_Options')->plugin('GithubFile')->debug_log) {
            Helper::removePanel(1, 'GithubFile/LogList.php');
        }
        Helper::removeAction('GithubFile');
        return _t('Disabled~');
    }

    public static function configHandle($config, $isInit)
    {
        if (!$isInit) {
            if (($config['DebugLog']) !== (\Typecho\Widget::widget('Widget_Options')->plugin('GithubFile')->DebugLog)) {
                if ($config['DebugLog']) {
                    Helper::addPanel(1, 'GithubFile/LogList.php', '插件日志', '日志内容', 'administrator');
                } else {
                    Helper::removePanel(1, 'GithubFile/LogList.php');
                }
            }
        }
        Helper::configPlugin('GithubFile', $config);
    }

    public static function config(Form $form)
    {
        //账号储存
        $t = new Text('Username', null, null, _t('Username'), _t(''));
        $form->addInput($t->addRule('required', _t('不能为空哦~')));
        $t = new Text('token', null, null, _t('token'), _t(''));
        $form->addInput($t->addRule('required', _t('不能为空哦~')));     
       //储存路径设置
        $t = new Text('Repo',null, null, _t('仓库名'), _t(''));
        $form->addInput($t);
        $t = new Text('Path', null, '/GithubFile/', _t('储存路径'), _t('需要以/结束 否则触发错误'));
        $form->addInput($t->addRule('required', _t('不能为空哦~')));
        //调试设置
        $t = new Radio('DebugLog', array(true => '开启', false => '关闭'), false, _t('调试设置'), _t('启用后记录调试日志'));
        $form->addInput($t);
        //镜像设置
        $t = new Text('Mirror', null, 'https://api.github.com', _t('API_Mirror'), _t('加速API提供Mirror'));
        $form->addInput($t->addRule('required', _t('不能为空哦~')));
        $t = new Text('Cdn', null, 'https://cdn.zenless.top/gh/', _t('File_Mirror'), _t('加速提供文件'));
        $form->addInput($t->addRule('required', _t('不能为空哦~')));
        //短代码目录生成
        $t = new Text('MirroPath', null, 'https://api.github.com', _t('加速目录规则'), _t('生成的链接设置 可选[day][year][month][user][repo][file]'));
        $form->addInput($t->addRule('required', _t('不能为空哦~')));
        $t = new Text('RealPath', null, 'https://api.github.com', _t('实际目录规则'), _t('上传到github时的目录设置 可选如上'));
        $form->addInput($t->addRule('required', _t('不能为空哦~')));

    }
}
