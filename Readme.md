Language: 
**`简体中文`** 
[`English`](https://github.com/Mlikiowa/GithubFile/blob/dev/README_EN.md)


[![](https://img.shields.io/github/license/MliKiowa/GithubFile?style=flat-square)](https://github.com/MliKiowa/GithubFile/blob/master/LICENSE)
[![GitHub issues](https://img.shields.io/github/issues/MliKiowa/GithubFile?style=flat-square)](https://github.com/MliKiowa/GithubFile/issues)
[![GitHub forks](https://img.shields.io/github/forks/MliKiowa/GithubFile?style=flat-square)](https://github.com/MliKiowa/GithubFile/network)
[![GitHub stars](https://img.shields.io/github/stars/MliKiowa/GithubFile?style=flat-square)](https://github.com/MliKiowa/GithubFile/stargazers)

# 介绍
基于Github的Hook与Action Cl持续集成结合服务器进行图片的传输与计算。

# 可扩展开发
1.图片压缩和切割

2.多图持续上传

# 工作原理
由服务器主动hook调动Github Action后，Action主动下载服务器待上传图片，传输完成后返回服务器，异步给前端
