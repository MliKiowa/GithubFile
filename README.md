# GithubStatic
一款基于github仓库实现附件储存的typecho插件

[![](https://img.shields.io/github/license/MliKiowa/GithubStatic)](https://github.com/MliKiowa/GithubStatic/blob/master/LICENSE)
[![Test](https://github.com/MliKiowa/GithubStatic/actions/workflows/php.yml/badge.svg)](https://github.com/MliKiowa/GithubStatic/actions/workflows/php.yml)
# 运行环境
PHP推荐版本：7.0及其以上

PHP需要扩展：Curl（后期将兼容不支持Curl）

操作系统:Linux

# 如何使用？

1.下载插件

2.解压到插件目录，将插件文件夹更名GithubStatic

3.启用插件，到设置面板获取Token

4.填入其它插件设置

5.开启伪静态(未开启可导致授权失败)

6.上传附件即可使用插件

# 如何获取Debug信息？
1.插件设置用开启记录Debug设置

2.到插件的Github诊断面板开启Debug设置

3.在新窗口上传图片或复现错误操作

4.关闭相应设置将得到错误信息

# 如何搭建授权服务器?
1.下载Auth.php

2.放置到网站主目录

3.申请github oauth 获取client_id client_secret

4.打开Auth.php修改相应参数 (https_open 在https可开启)

5.到插件设置修改插件的授权服务器

# 反馈
By Telegram : [Mlikiowa](https://t.me/Mlikiowa)

By Mail : 946735494@qq.com

By QQ : 946735494
