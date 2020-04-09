<?
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
* 没有想念过什么,不曾期待着什么
*
* @package GitStatic
* @author 乔千
* @version 2.0.0
* @link https://blog.mumuli.cn
*/
class GitStatic_Plugin implements Typecho_Plugin_Interface
{
  public static function activate()
    {
     // Typecho_Plugin::factory('Widget_Upload')->uploadHandle = array('GitStatic_Plugin', 'uploadHandle');
      //Typecho_Plugin::factory('Widget_Upload')->modifyHandle = array('GitStatic_Plugin', 'modifyHandle');
     // Typecho_Plugin::factory('Widget_Upload')->deleteHandle = array('GitStatic_Plugin', 'deleteHandle');
      Typecho_Plugin::factory('Widget_Upload')->attachmentHandle = array('GitStatic_Plugin', 'attachmentHandle');
      //Typecho_Plugin::factory('Widget_Upload')->attachmentDataHandle = array('GitStatic_Plugin', 'attachmentDataHandle'); 
      return _t("启用成功啦！快先设置下吧。");
    }
    public static function deactivate()
      {
        return _t("关闭啦，不能享受加速了唉");
      }
      public static function personalConfig(Typecho_Widget_Helper_Form $form)
        {

        }
        public static function config(Typecho_Widget_Helper_Form $form)
          {
            $t = new Typecho_Widget_Helper_Form_Element_Text('text_a', NULL, '', _t('插件功能介绍：'),
            _t('<ol>
 <li>插件是一款基于jsdelivr开发的静态资源加速插件</li>
            <li>需要仔细阅读文档配置插件否则不能运行</li>
            <li>操作规范:删除修改必备份 出现问题看文档</li>
            </ol>'));
 $form->addInput($t);

            $t = new Typecho_Widget_Helper_Form_Element_Text('text_b', NULL, '', _t('插件使用说明：'),
            _t('<ol>
 <li><a href="https://jq.qq.com/?_wv=1027&k=5pK3hCm">加入官方内测群，和作者py吧</a></li>
            <li><a href="https://">官方的各种姿势的教程</a></li>
            <li><a href="https://">非官方的各种魔改插件指南</a></li>
            </ol>'));
 $form->addInput($t);

            $t = new Typecho_Widget_Helper_Form_Element_Text('serverurl',
            null, null,
            _t('源加速服务器:'),
            _t('填写服务器地址,没有请移步教程搭建喵'));
            $form->addInput($t->addRule('required', _t('源不能为空喵')));
            echo '<script>
 window.onload = function() 
              {
                document.getElementsByName("text_a")[0].type = "hidden";
                document.getElementsByName("text_b")[0].type = "hidden";

              }
              </script>';
 } 
            public static function attachmentHandle(array $content)
              { 
                $options = Typecho_Widget::widget('Widget_Options')->plugin('GitStatic'); 
                return Typecho_Common::url($content['attachment']->path,$options->serverurl);
              }

            }