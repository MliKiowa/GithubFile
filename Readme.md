# GithubFile

Language: 
**`简体中文`** 
[`English`](https://github.com/Mlikiowa/GithubFile/blob/dev/Readme_En.md)


[![](https://img.shields.io/github/license/MliKiowa/GithubFile?style=flat-square)](https://github.com/MliKiowa/GithubFile/blob/master/LICENSE)
[![GitHub issues](https://img.shields.io/github/issues/MliKiowa/GithubFile?style=flat-square)](https://github.com/MliKiowa/GithubFile/issues)
[![GitHub forks](https://img.shields.io/github/forks/MliKiowa/GithubFile?style=flat-square)](https://github.com/MliKiowa/GithubFile/network)
[![GitHub stars](https://img.shields.io/github/stars/MliKiowa/GithubFile?style=flat-square)](https://github.com/MliKiowa/GithubFile/stargazers)

# 介绍
基于Github与cdn进行加速图片

# 使用方法
1.下载插件 在 Typecho/usr/plugin 目录解压

2.解压出的目录更名GithubFile

3.启用插件 并按规则填写配置

4.上传文件 查看相应github的库中是否提交文件 如果能够看见安装完成
# 版本日志
## Ver 1.1
### 已更新
1.支持了basic认证方式

2.使用typecho内置http方式替代

## 计划中
1.支持personal token方式认证

2.优化日志整体包括安全性问题

3.对用户配置验证

4.独立路由入口与编辑器入口

# 更新日志
## 2022.7.5

1.支持PHP 8.1 8.0 环境

# 推荐运行环境
PHP Version：8.0 及其以上

PHP Need Support：Curl or Socket

# 安全性须知
插件代码为公开开源，Github账号保存于typecho数据库中

认证过程基于Basic方式，personal token在后续支持 oauth永久移除

Typecho<->Plugin<->Github

# 对于Github 加速的CDN问题
目前cdn.jsdelivr.net的回源github了 基本凉了

目前推荐使用以下cdn替代

https://fastly.jsdelivr.net/gh

https://cdn.statically.io/gh
