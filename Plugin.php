<?
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
* 远去的究竟是什么呢
*
* @package GitStatic
* @author 乔千
* @version 2.0.0
* @link https://blog.mumuli.cn
*/
require(__DIR__ . DIRECTORY_SEPARATOR . 'GitHelper.php');

class GitStatic_Plugin implements Typecho_Plugin_Interface
{
  const UPLOAD_DIR = '/usr/uploads' ;
  public static function activate()
    {
      Typecho_Plugin::factory('Widget_Upload')->uploadHandle = array('GitStatic_Plugin', 'uploadHandle');
      Typecho_Plugin::factory('Widget_Upload')->modifyHandle = array('GitStatic_Plugin', 'modifyHandle');
      Typecho_Plugin::factory('Widget_Upload')->deleteHandle = array('GitStatic_Plugin', 'deleteHandle');
      Typecho_Plugin::factory('Widget_Upload')->attachmentHandle = array('GitStatic_Plugin', 'attachmentHandle');
      //Typecho_Plugin::factory('Widget_Upload')->attachmentDataHandle = array('GitStatic_Plugin', 'attachmentDataHandle');
      //目前开放四个接口
      return _t("启用成功啦！快先设置下吧。");
    }
    public static function deactivate()
      {
        return _t("关闭啦，不能享受加速了唉");
      }
      public static function config(Typecho_Widget_Helper_Form $form)
        {
          $desc = new Typecho_Widget_Helper_Form_Element_Text('desc', NULL, '', _t('插件使用说明：'),
          _t('<ol>
 <li>插件可以验证配置的正确性，请确认配置信息正确，否则不能正常使用。<br></li>
          </ol>'));
 $form->addInput($desc);

          $token = new Typecho_Widget_Helper_Form_Element_Text('token',
          null, null,
          _t('Git仓库token'),
          _t('请登录Github获取'));
          $form->addInput($token->addRule('required', _t('token不能为空！')));

          $username = new Typecho_Widget_Helper_Form_Element_Text('username',
          NULL, Null,
          _t('用户名：'),
          _t('例如MQiaoqian'));
          $form->addInput($username->addRule('required', _t('用户名不能为空！')));

          $repos = new Typecho_Widget_Helper_Form_Element_Text('repos',
          NULL, Null,
          _t('仓库名：'),
          _t('例如MCDN'));
          $form->addInput($repos->addRule('required', _t('储存桶不能为空！')));
          echo '<script>
 window.onload = function() 
            {
              document.getElementsByName("desc")[0].type = "hidden";
            }
            </script>';
 }
          public static function uploadHandle($file) 
            { 
              if (empty($file['name'])) return false;

              $ext = self::getSafeName($file['name']);
              //判定是否是允许的文件类型
              if (!Widget_Upload::checkFileType($ext)) return false;
              $options = Typecho_Widget::widget('Widget_Options')->plugin('GitStatic');
              //设置
              //获取文件名
              $date = new Typecho_Date($options->gmtTime);
              $fileDir = self::getUploadDir() . '/' . $date->year . '/' . $date->month;
              $fileName = sprintf('%u', crc32(uniqid())) . '.' . $ext;
              $path = $fileDir . '/' . $fileName;
              //获得上传文件
              $uploadfile = self::getUploadFile($file);
              //如果没有临时文件，则退出
              if (!isset($uploadfile)) {
                return false;
              }
              /* 上传 */
              //初始化

              try {
                if (isset($file['tmp_name'])) { 
                  $result=files_upload($options->username,$options->token,$options->repos,"/".substr($path,1), file_get_contents($uploadfile));
                }else{
                  $result=files_upload($options->username,$options->token,$options->repos,"/".substr($path,1),$uploadfile);
                } 
                if(!$result)
                { 
                  $result=files_updata($options->username,$options->token,$options->repos,"/".substr($path,1), file_get_contents($uploadfile),get_sha($options->username,$options->repos,"/".substr($path,1)));
                  //尝试更新文件 
                  if(!$result) {self::checkset();return false;}
                }
                // $result = $ossClient->uploadFile($options->bucket, substr($path,1), file_get_contents($uploadfile));
              } catch (Exception $e) {
                // print_r($e);
                return false;
              }

              if (!isset($file['size'])){
                //未考虑 $file['size'] = $fileInfo['size_upload'];
              }

              //返回相对存储路径
              return array(
              'name' => $file['name'],
              'path' => $path,
              'size' => $file['size'],
              'type' => $ext,
              'mime' => (isset($file['tmp_name']) ? Typecho_Common::mimeContentType($file['tmp_name']) : $file['mime'])
              );
            }
            private static function getUploadFile($file) {
                // return isset($file['tmp_name']) ? $file['tmp_name'] : (isset($file['bytes']) ? $file['bytes'] : (isset($file['bits']) ? $file['bits'] : ''));
                return isset($file['tmp_name']) ? $file['tmp_name'] : (isset($file['bytes']) ? base64_decode($file['bytes']) : (isset($file['bits']) ? $file['bits'] : ''));
              }
              public static function personalConfig(Typecho_Widget_Helper_Form $form)
                {
                }

                private static function getSafeName(&$name) {
                    $name = str_replace(array('"', '<', '>'), '', $name);
                    $name = str_replace('\\', '/', $name);
                    $name = false === strpos($name, '/') ? ('a' . $name) : str_replace('/', '/a', $name);
                    $info = pathinfo($name);
                    $name = substr($info['basename'], 1);
                    return isset($info['extension']) ? strtolower($info['extension']) : '';
                  }
                  private static function getUploadDir() {
                      if(defined('__TYPECHO_UPLOAD_DIR__'))
                      {
                        return __TYPECHO_UPLOAD_DIR__;
                      }
                    else{
                        return self::UPLOAD_DIR;
                      }
                    }
                    public static function attachmentHandle(array $content) {

                        //$options = Typecho_Widget::widget('Widget_Options')->plugin('GitStatic');
                        return Typecho_Common::url($content['attachment']->path, self::getDomain());
                      }
                      private static function getDomain() {
                          $options = Typecho_Widget::widget('Widget_Options')->plugin('GitStatic');
                          // $domain = 'https://' . $options->bucket . '.' . $options->region . '.aliyuncs.com';
                          $domain= "https://cdn.jsdelivr.net/gh/".$options->username."/".$options->repos;
                          return $domain;
                        }
                        public static function checkset()
                          {
                            //配置可能存在错误
                            $lockfile = fopen("debug.lock", "w");
                            fclose($lockfile);
                          }
                          public static function deleteHandle(array $content) {
                              //获取设置参数
                              $options = Typecho_Widget::widget('Widget_Options')->plugin('GitStatic');
                              //初始化
                              try {
                                $result=files_del($options->username,$options->token,$options->repos,$content['attachment']->path,get_sha($options->username,$options->repos,$content['attachment']->path)); 
                                if(!$result)return false;
                              } catch (Exception $e) {
                                return false;
                              }
                              return true;
                            }
                            public static function modifyHandle($content, $file) {
                                if (empty($file['name'])) {
                                  return false;
                                }

                                //获取扩展名
                                $ext = self::getSafeName($file['name']);
                                //判定是否是允许的文件类型
                                if (!Widget_Upload::checkFileType($ext)) return false;
                                //获取设置参数
                                $options = Typecho_Widget::widget('Widget_Options')->plugin('GitStatic');
                                //获取文件路径
                                $path = $content['attachment']->path;
                                //获得上传文件
                                $uploadfile = self::getUploadFile($file);
                                //如果没有临时文件，则退出
                                if (!isset($uploadfile)) {
                                  return false;
                                }

                                /* 上传到 */
                                //初始化

                                try {
                                  if (isset($file['tmp_name'])) {
                                    $result=files_updata($options->username,$options->token,$options->repos,"/".substr($path,1), file_get_contents($uploadfile),get_sha($options->username,$options->repos,"/".substr($path,1)));
                                  }else{
                                    $result=files_updata($options->username,$options->token,$options->repos,"/".substr($path,1), $uploadfile,get_sha($options->username,$options->repos,"/".substr($path,1)));

                                  }
                                  //尝试更新文件 
                                  if(!$result) {self::checkset();return false;}
                                  //$result = $ossClient->uploadFile($options->bucket, substr($path,1), $uploadfile);
                                } catch (Exception $e) {
                                  return false;
                                }

                                if (!isset($file['size'])){
                                  //$fileInfo = $result['info'];
                                  //$file['size'] = $fileInfo['size_upload'];未考虑
                                }

                                //返回相对存储路径
                                return array(
                                'name' => $content['attachment']->name,
                                'path' => $content['attachment']->path,
                                'size' => $file['size'],
                                'type' => $content['attachment']->type,
                                'mime' => (isset($file['tmp_name']) ? Typecho_Common::mimeContentType($file['tmp_name']) : $file['mime'])
                                );
                              }
                            }
