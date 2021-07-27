# GithubStatic
一款基于github仓库实现附件储存的typecho插件
各位这个插件估计还得改几次才能正常使用
服务器我一个人维护太困难了 如果能够提供支持不甚感谢

[![](https://img.shields.io/github/license/MliKiowa/GithubFile)](https://github.com/MliKiowa/GithubFile/blob/master/LICENSE)
[![Test](https://github.com/MliKiowa/GithubFile/actions/workflows/php.yml/badge.svg)](https://github.com/MliKiowa/GithubFile/actions/workflows/php.yml)
# 运行环境
PHP推荐版本：7.0及其以上

PHP需要扩展：Curl（后期将兼容不支持Curl）

操作系统:Linux

# 如何使用？

1.下载插件

2.解压到插件目录，将插件文件夹更名GithubFile

3.启用插件，到设置面板获取Token

4.填入其它插件设置

5.开启伪静态(未开启可导致授权失败)

6.上传附件即可使用插件

# 如何搭建授权服务器?

1.下载Auth.php

2.放置到网站主目录

3.申请github oauth 获取client_id client_secret

4.打开Auth.php修改相应参数 (https_open 在https可开启)

5.到插件设置修改插件的授权服务器

# 反馈
推荐开issue 这样比较能让我舒适

By Telegram : [Mlikiowa](https://t.me/Mlikiowa)

By Mail : 946735494@qq.com

By QQ : 946735494
