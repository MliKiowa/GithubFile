<?php
$config["sitekey"]=md5("qiaoqianwu123");//请修改密钥  随机填写
$config["router"]["git"]=0;//绑定配置 $config["github"][数字]
//-----------
$config["github"][0]["site"]="https://blog.xcyd.fun";//设置后开启回源地址
$config["github"][0]["webp"]=true;//webp 即对图片压缩
$config["github"][0]["path"]="";
//例如要回源地址为 http://a.com/a/b/c.jpg 
//对分发站点请求地址http://x.com/git/a/b/c.jpg
// 此处设置 /a/b  访问时只需 http://x.com/git/c.jpg。即可访问
//请注意一旦使用请不要修改

//以下按照1.0插件配置
$config["github"][0]["username"]="MQiaoqian";//账号
$config["github"][0]["repos"]="MCDN";//仓库
$config["github"][0]["Parameter"]=false;//忽略url参数缓存
$confug["github"][0]["CacheTime"]=3600*24*7;//缓存时间 一周 秒为时间计算
$config["github"][0]["token"]="token";//此处为token
//----------