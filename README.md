# GithubFile

Language: 
**`简体中文`** 
[`English`](https://github.com/Mlikiowa/GithubFile/blob/main/ReadmeEn.md)

[![PHP Worker](https://github.com/MliKiowa/GithubFile/actions/workflows/php.yml/badge.svg?style=flat-square)](https://github.com/MliKiowa/GithubFile/actions/workflows/php.yml)
[![](https://img.shields.io/github/license/MliKiowa/GithubFile?style=flat-square)](https://github.com/MliKiowa/GithubFile/blob/master/LICENSE)
[![GitHub issues](https://img.shields.io/github/issues/MliKiowa/GithubFile?style=flat-square)](https://github.com/MliKiowa/GithubFile/issues)
[![GitHub forks](https://img.shields.io/github/forks/MliKiowa/GithubFile?style=flat-square)](https://github.com/MliKiowa/GithubFile/network)
[![GitHub stars](https://img.shields.io/github/stars/MliKiowa/GithubFile?style=flat-square)](https://github.com/MliKiowa/GithubFile/stargazers)
# 重构警告
插件正在进行大规模重写 如果出现错误 请提出issue 以便维护
Typecho怎么三天五头的更新 还是不向下兼容更新
目前仍处于维护状态 如果你使用出现了问题请发生mail到nineto0@163.com 反馈
# 介绍
依托Github进行储存使用第三方代理服务提供图片访问的Typecho插件

# 使用方法
1.下载插件 在 Typecho/usr/plugin 目录解压

2.解压出的目录更名*GithubFile*

3.启用插件 并按规则填写配置

4.上传文件 查看相应github的库中是否提交文件 如果能够看见安装完成
# 版本日志
## Ver 1.3.9
- [x] 兼容最新版Typecho

- [x] 修复少量问题
## Ver 1.3.8
- [x] 自定义上传路径
## Ver 1.3.4
- [x] 修改默认图片加速源
## Ver 1.3.3
1.重构插件
## Ver 1.3.2
1.兼容最新版Typecho 1.2 和 PHP7
## Ver 1.2

1.更新为Token认证(支持personal token 或者 oauth token)
## Ver 1.1
### 已更新
1.支持了basic认证方式

2.使用typecho内置http方式替代

## 计划中
1.支持personal token方式认证

2.优化日志整体包括安全性问题

3.对用户配置验证

# 推荐运行环境
PHP Version：7.2 及其以上

PHP Need Support：Curl or Socket

# 安全性须知
插件代码为公开开源

认证过程基于token

Typecho<->Plugin<->Github

# 图片代理源(图片镜像)
1.自建代理
2.第三方代理
