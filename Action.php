<?php
namespace TypechoPlugin\GithubFile;
use Typecho\Plugin;
/**
 *插件Action处理部分
 * Class类名称(GithubFile_Action)
 * @package GithubFile
 * @author Mlikiowa<nineto0@163.com>
 * @version 1.1.0
 * @since 1.0.0
 */
defined('_CACHE_PATH') or define('_CACHE_PATH', dirname(__FILE__) . '/cache/');
defined('_TMP_PATH') or define('_TMP_PATH', dirname(__FILE__) . '/cache/tmp/');
defined('_LOG_PATH') or define('_LOG_PATH', dirname(__FILE__) . '/cache/log/');
class Action extends Typecho_Widget implements Widget_Interface_Do
{
    /**
     * Notes:接收处理动作
     * User: Mlikiowa<nineto0@163.com>
     * Date: 2022-06-22
     * Time:17:02
     */
    public function action()
    {
        $this->on($this->request->is('do=delLog'))->delLog();
    }

    /**
     * Notes:删除日志
     * User: Mlikiowa<nineto0@163.com>
     * Date: 2022-06-22
     * Time:17:03
     */
    public function delLog()
    {
        $this->isPass();
        $filename = $this->request->from('filename') ['filename'];
        @unlink(_LOG_PATH . 'cache/' . $filename);
        $this->widget('Widget_Notice')->set(_t('文件已经删除'), 'success');
        $this->response->goBack();
    }

    /**
     * Notes:
     * User: Mlikiowa<nineto0@163.com>
     * Date: 2022-06-22
     * Time:17:03
     */
    public function isPass()
    {
        $user = \Typecho\Widget::widget('Widget_User');
        if (!$user->pass('administrator')) {
            throw new Exception("Error");
        }
    }
} 
