<?php 
namespace TypechoPlugin\GithubFile;
use Utils\Helper;
use Typecho\Widget\Helper\Form;
use Widget\Options;
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
class Plugin implements \Typecho\Plugin\PluginInterface
{
    const CACHE_PATH = 'cache';
    const TMP_PATH = self::CACHE_PATH . '/tmp';
    const LOG_PATH = self::CACHE_PATH . '/log';
    /**
     * @var array 存储路径相关属性
     */
    protected static $pathProps = [
        'repo', 'mirrPath', 'realPath', 'username', 'token'
    ];
    /**
     * 激活插件时初始化相关目录
     */
    public static function activate()
    {
        if (basename(dirname(__FILE__)) !== 'GithubFile') {
            throw new \Typecho\Plugin\Exception(_t('The plugin name must be GithubFile'));
        }
        if (!is_dir(self::CACHE_PATH)) {
            mkdir(self::CACHE_PATH);
            mkdir(self::TMP_PATH);
            mkdir(self::LOG_PATH);
        }
        Helper::addAction('GithubFile', [__CLASS__, 'handle']);
        $uploadFactory = \Typecho\Plugin::factory('Widget_Upload');
        $uploadFactory->on('uploadHandle', [__NAMESPACE__ . '\GithubFile_Handler', 'uploadHandle']);
        $uploadFactory->on('modifyHandle', [__NAMESPACE__ . '\GithubFile_Handler', 'modifyHandle']);
        $uploadFactory->on('deleteHandle', [__NAMESPACE__ . '\GithubFile_Handler', 'deleteHandle']);
        $uploadFactory->on('attachmentHandle', [__NAMESPACE__ . '\GithubFile_Handler', 'attachmentHandle']);
        $uploadFactory->on('attachmentDataHandle', [__NAMESPACE__ . '\GithubFile_Handler', 'attachmentDataHandle']);
        return _t('可以使用啦~');
    }
    public static function deactivate()
    {
        $debugLog = \Typecho\Widget::widget('Widget_Options')->plugin('GithubFile')->debug_log;
        if ($debugLog) {
            Helper::removePanel(1, 'GithubFile/LogList.php');
        }
        Helper::removeAction('GithubFile');
        return _t('Disabled~');
    }
    public static function handle()
    {
        if (!isset($_POST['_SERVER']) && !isset($_POST['_FILES'])) {
            throw new \Typecho\Plugin\Exception('请不要手工执行上传！');
        }
        return \Typecho\Plugin::factory(GithubFile_Handler::class)->handle();
    }
    public static function config(Form $form)
    {
        $form->addInput(new Form\Element\Text('Username', null, null, _t('Username'), _t(''))->addRule('required', _t('不能为空哦~')));
        $form->addInput(new Form\Element\Text('token', null, null, _t('token'), _t(''))->addRule('required', _t('不能为空哦~')));
        foreach (self::$pathProps as $key) {
            $form->addInput(new Form\Element\Text(ucfirst($key), null, null, _t(ucfirst($key)), _t(''))->addRule('required', _t('不能为空哦~')));
        }
        //调试设置
        $t = new Form\Element\Radio('DebugLog', array(true => '开启', false => '关闭'), false, _t('调试设置'), _t('启用后记录调试日志'));
        $form->addInput($t);
        //镜像设置
        $t = new Form\Element\Text('Mirror', null, 'https://api.github.com', _t('API_Mirror'), _t('加速API提供Mirror'));
        $form->addInput($t->addRule('required', _t('不能为空哦~')));
        $t = new Form\Element\Text('Cdn', null, 'https://cdn.zenless.top/gh', _t('File_Mirror'), _t('加速提供文件 结尾不能带有/'));
        $form->addInput($t->addRule('required', _t('不能为空哦~')));
        //短代码目录生成
        $t = new Form\Element\Text('MirroPath', null, '[cdn]/[user]/[repo]/[file]', _t('镜像加速目录规则'), _t('生成的链接设置 提示: 可选[user][repo][file][cdn]'));
        $form->addInput($t->addRule('required', _t('不能为空哦~')));
        $t = new Form\Element\Text('RealPath', null, '/[file]', _t('实际上传目录规则'), _t('上传到github时的目录设置 设置可为/你的目录/[file] 必须包括/[file]'));
        $form->addInput($t->addRule('required', _t('不能为空哦~')));
        //图片压缩设置 未实装
        $t = new Form\Element\Radio('ImgCompress', array(true => '开启', false => '关闭'), false, _t('图片压缩'), _t('未实装 敬请期待'));
        $form->addInput($t);
    }
    public static function personalConfig(Form $form)
    {
    }    
}
