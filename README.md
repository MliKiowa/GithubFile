# GithubFile
一款基于github仓库实现附件储存的typecho插件

插件逻辑整体调整 *授权服务器没了* 建议各位使用personal token填入

[![](https://img.shields.io/github/license/MliKiowa/GithubFile)](https://github.com/MliKiowa/GithubFile/blob/master/LICENSE)
[![Test](https://github.com/MliKiowa/GithubFile/actions/workflows/php.yml/badge.svg)](https://github.com/MliKiowa/GithubFile/actions/workflows/php.yml)
[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2FMliKiowa%2FGithubFile.svg?type=shield)](https://app.fossa.com/projects/git%2Bgithub.com%2FMliKiowa%2FGithubFile?ref=badge_shield)
# 推荐运行环境
PHP Version：8.0 及其以上

PHP Http Support：Curl or Socket

# 关于jsdelivr凉了的问题
使用镜像
github中数据一般无论如何都不会丢失 所以放心使用
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
# 不可用访问GithubApi
切换GithubAPI为 https://v2.kkpp.cc/

该Api已经失效(使用第三方Api请注意Api安全)
# 查找错误
1.开启调试模式

2.执行错误操作

3.打开日志记录面板下载日志
# 联系
By Mail : 1627126029@qq.com

By QQ : 1627126029

# 安全性提示
本插件通过第三方Oauth如果你对此方法介意 
1. 可以获取官方personsal key填入token
2. 搭建自己的授权服务器
# 插件状态
(Main) END OF FILE


## License
[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2FMliKiowa%2FGithubFile.svg?type=large)](https://app.fossa.com/projects/git%2Bgithub.com%2FMliKiowa%2FGithubFile?ref=badge_large)