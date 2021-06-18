

<!DOCTYPE html>
<html lang="zh-cn">
<head><title>
	FTP在线文件管理-天达云
</title><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" /><meta name="renderer" content="webkit" /><meta name="viewport" content="width=device-width, initial-scale=1" /><link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet" /><link href="css/ftpStyle.css" rel="stylesheet" />
    <script src="/js/jquery-3.2.1.min.js"></script>
    <script src="/bootstrap/js/bootstrap.min.js"></script>
    <style>
        .leftSelectBox {
            text-align: center;
            vertical-align: middle !important;
        }
    </style>
    <script>
        //if (window.navigator.cookieEnabled) {
        //    alert("浏览器配置错误，Cookie可用！");
        //   //return true;
        //}
        //else {
        //    alert("浏览器配置错误，Cookie不可用！");
        //   // return false;
        //}


        //遍历是否选中文件
        function getChecked() {
            // Check that at least 1 checkbox is checked
            var nr_checkboxes_checked = 0;
            var tt = document.getElementsByTagName("input");
            for (var i = 0; i < tt.length; i++) {
                if (tt[i].type == "checkbox") {
                    if (tt[i].checked == true) {
                        nr_checkboxes_checked++;
                    }
                    if (tt[i].checked == false) {
                    }
                }
            }
            if (nr_checkboxes_checked == 0) {
                //alert('请选择至少的一个目录或者文件!');
                return false;
            }
            else
                return true;
        }
        function checkOnce(obj) {
            if (!obj.checked) {
                $("#checkBoxALL1").prop("checked", false);
                $("#checkBoxALL2").prop("checked", false);
            }
            else {
                checkAllSelectALL(obj);
            }
            // 
        }
        function checkAllSelectALL() {
            var tt = document.getElementsByTagName("input");

            var chkTrue = 0;
            var chkFalse = 0;

            for (var i = 0; i < tt.length; i++) {
                if (tt[i].type == "checkbox") {
                    if (tt[i].id != "checkBoxALL1" && tt[i].id != "checkBoxALL2") {
                        if (tt[i].checked == true) {
                            chkTrue++;
                        }
                        if (tt[i].checked == false) {
                            chkFalse++;
                        }
                    }

                }
            }
            if (chkFalse == 0) {//没有选中为0  将 attr 改成  prop：问题成功解决
                $("#checkBoxALL1").prop("checked", true);
                $("#checkBoxALL2").prop("checked", true);
            }
        }

        function loadtext(ftpip, ftpusername, ftppassword, ftpdir, ftpfilename, filetype, api) {
            if (filetype == ".jpg" || filetype == ".jpeg" || filetype == ".png" || filetype == ".gif" || filetype == ".bmp" || filetype == ".swf" || filetype == ".ico") {
                $("#precodearea").removeClass("pre-scrollable");
            }
            else
                $("#precodearea").addClass("pre-scrollable");
            var xmlhttp;
            if (window.XMLHttpRequest) {
                // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else {
                // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }

            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    trade_state = xmlhttp.responseText;
                    //  alert(trade_state);
                    if (filetype == ".jpg" || filetype == ".jpeg" || filetype == ".png" || filetype == ".gif" || filetype == ".bmp" || filetype == ".swf" || filetype == ".ico") {
                        $("#precodearea").html("<img class='img-responsive' alt='' src=\"" + trade_state + "\"  />");
                    }
                    else//不是图片
                    {
                        if (api == 'view') {
                            $("#precodearea").html(trade_state);
                            //$("#precodearea").
                        }
                        else
                            $("#txtEditorText")[0].innerHTML = trade_state;
                    }
                }
            }
            // xmlhttp.open("get", "/css/UI/denglu.png?random=" + Math.random(), true);
            xmlhttp.open("get", "/GetFilename.aspx?ftpip=" + ftpip + "&ftpusername=" + ftpusername + "&ftppassword=" + ftppassword + "&ftpdir=" + ftpdir + "&ftpfilename=" + ftpfilename + "&api=" + api + "&random=" + Math.random(), true);
            //下面这句话必须有
            //把标签/值对添加到要发送的头文件。
            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xmlhttp.send(null);
        }
        function ftpdownload() {
            var getChk = getChecked();
            if (!getChk) {
                alert('请选择一个文件，你当前没有选中文件。!');
                return;
            }
            $('#myModalDownload').modal();
        }
        function ftpdownloadHide() {
            $('#myModalDownload').modal('hide');
        }
        //上传文件
        function ftpupload() {
            var url = $("#thisUrl").text();
            if (url == '' || url == "/log") {
                alert('当前目录不可以上传文件，请选择web目录或db目录再上传。');
                return;
            }
            $('#myModalUpload').modal();
        }
        //设置操作的html
        function ftphtml(filename) {
            // $(".tablelast").html('<a href="#" onclick="ftprename(\'' + filename + '\')">重命名</a>  <a href="#" onclick="ftprename(\'' + filename + '\')">解压</a>  <a href="#" onclick="ftprename(\'' + filename + '\')">编辑</a>');
        }
        function ftphome() {
            window.location.href = "?sid=ddfddac0ee534a96bb5f148c59945ac1&ftp=/web";
        }

        //登录FTP
        function ftplogin() {
            $('#myModalLogin').modal();
        }
        //编辑框
        function ftptexteditor(filename, filetype, api) {
            $("#precodearea").html('');//清空编辑框
            if (filetype == '.dir')
                return;
            var ftpdir = $("#thisUrl").text();
            $('#myModalTextEditor').modal();
            if (api == "view") {
                $("#precodearea").html("<img class='img-responsive' alt='' src='/css/loading.gif'  />");
                $("#BtnTextUpdate").hide();//隐藏按钮
                $("#myModalTextEditorView").show();
                $("#myModalTextEditorEdit").hide();//
                $("#mmyModalTextEditorLabel").html("查看文件：<small>" + ftpdir + "/" + filename + "</small>");
            }
            else {
                $("#labUnzipFilenameOp").attr("value", filename);//设置文件名
                $("#BtnTextUpdate").show();//显示按钮
                $("#myModalTextEditorView").hide();
                $("#myModalTextEditorEdit").show();//
                $("#mmyModalTextEditorLabel").html("编辑文件：<small>" + ftpdir + "/" + filename + "</small>");
            }

            loadtext('127.0.0.1', 'qiaoqian', '15ee38a4d1ffd00b3ecc96a4dcb1fb6a', ftpdir, encodeURIComponent(filename), filetype, api);//进行文件名UTF-8编码
        }
        //重命名
        function ftprename(oldfilename) {
            //$("#txtOldFileName").text(oldfilename);
            $("#txtFileNameOld")[0].value = oldfilename;
            //$("#strFilenameNew")[0].value = "";
            $('#myModalRename').modal();
        }
        //ftp刷新文件
        function ftprefresh() {
            $('#btnRefresh').trigger("click");
        }
        //ftp在线上传关闭修改清空输入框内容
        function ftpEditorClearText() {
            $("#txtEditorText")[0].innerHTML = "";
        }
        //打包文件
        function ftpzip() {
            $('#myModalZip').modal();
        }
        //解压文件
        function ftpunzip() {
            var getChk = getChecked();
            $("#labUnzipFilenameOp").attr("value", "");
            if (!getChk) {
                alert('请选择压缩包文件，支持ZIP格式与RAR格式。');
                return;
            }
            $('#myModalUnzip').modal();
        }
        //解压文件2 操作中
        function ftpunzipOp(filename, filetype) {
            var getChk = getChecked();
            $("#labUnzipFilenameOp").attr("value", filename);
            var arr = filename.split('.');
            //  alert(arr[1]);
            if (filetype != ".rar" && filetype != ".zip") {
                alert('请选择压缩包文件，支持ZIP格式与RAR格式。');
                return;
            }
            $('#myModalUnzip').modal();
        }
        //打包文件
        function ftpzip() {
            $('#myModalZip').modal();
        }

        //新建文件夹
        function ftpadddir() {
            var url = $("#thisUrl").text();
            if (url == '' || url == "/log") {
                alert('当前目录不可以新建文件夹，请选择web目录或db目录下再操作新建。');
                return;
            }
            $('#myModalAddDir').modal();
        }
        //重置FTP写入权限
        function ftpchmod() {
            $('#myModalChmod').modal();
        }
        //复制文件
        function ftpcopy() {
            var getChk = getChecked();
            if (!getChk) {
                alert('请选择至少的一个目录或者文件!');
                return;
            }
            $('#myModalCopy').modal();
        }
        //移动文件
        function ftpmove() {
            var getChk = getChecked();
            if (!getChk) {
                alert('请选择至少的一个目录或者文件!');
                return;
            }
            $('#myModalMove').modal();
        }
        //删除文件
        function ftpdel() {
            var getChk = getChecked();
            if (!getChk) {
                alert('请选择至少的一个目录或者文件!');
                return;
            }
            $('#myModalDel').modal();
        }
        function checkAll(obj) {
            var tt = document.getElementsByTagName("input");
            for (var i = 0; i < tt.length; i++) {
                if (tt[i].type == "checkbox") {
                    if (obj.checked == true) {
                        tt[i].checked = true;
                        // tt[i].disabled = true;
                    }
                    if (obj.checked == false) {
                        tt[i].checked = false;
                    }
                }
            }
        }
        //组拼
        function formatOpHtml(filename, filetype) {
            var rest = '<small>';
            filetype = filetype.toLowerCase();

            if (filetype == ".jpg" || filetype == ".jpeg" || filetype == ".png" || filetype == ".gif" || filetype == ".bmp" || filetype == ".swf" || filetype == ".ico") {
                rest = rest + '<a href="#" onclick="ftptexteditor(\'' + filename + '\',\'' + filetype + '\',\'view\')">查看</a>&nbsp;&nbsp;'
            }
            var isTextFile = false;
            if ((filetype == ".txt") || (filetype == ".shtml") || (filetype == ".cshtml") || (filetype == ".asp") || (filetype == ".aspx") || (filetype == ".html") || (filetype == ".htm") || (filetype == ".php") || (filetype == ".jsp") || (filetype == ".css") || (filetype == ".config") || (filetype == ".inc") || (filetype == ".js") || (filetype == ".java") || (filetype == ".ini") || (filetype == ".htaccess") || (filetype == ".cs") || (filetype == ".asax") || (filetype == ".cfg") || (filetype == ".xml") || (filetype == ".sql") || (filetype == ".conf") || (filetype == ".c") || (filetype == ".asmx") || (filetype == ".ashx") || (filetype == ".vb") || (filetype == ".vbs") || (filetype == ".rhtml") || (filetype == ".shtml") || (filetype == ".bat") || (filetype == ".csproj") || (filetype == ".vbproj") || (filetype == ".vjsproj") || (filetype == ".sln") || (filetype == ".suo"))
                isTextFile = true;
            if (isTextFile)//如果是文本可以查看类型
            {
                rest = rest + '<a href="#" onclick="ftptexteditor(\'' + filename + '\',\'' + filetype + '\',\'view\')">查看</a>&nbsp;&nbsp;'
            }
            rest = rest + '<a href="#" onclick="ftprename(\'' + filename + '\')">重命名</a>&nbsp;&nbsp;'
            if (filetype == ".rar" || filetype == ".zip") {
                rest = rest + '<a href="#" onclick="ftpunzipOp(\'' + filename + '\',\'' + filetype + '\')">解压</a>&nbsp;&nbsp;'
            }

            if (isTextFile)//如果是文本可以查看类型
            {
                rest = rest + '<a href="#" onclick="ftptexteditor(\'' + filename + '\',\'' + filetype + '\',\'edit\')">编辑</a>&nbsp;&nbsp;'
            }
            return rest + '</small>';
        }

    </script>
</head>
<body>
    <form method="post" action="./?sid=ddfddac0ee534a96bb5f148c59945ac1&amp;ftp=%2fweb%2fusr%2fplugins%2fGithubStatic" id="form1" enctype="multipart/form-data">
<div class="aspNetHidden">
<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="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" />
</div>

<div class="aspNetHidden">

	<input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="CA0B0334" />
	<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="/wEdACtHm1GG5P+FynYso39+PIwHNmn7yRvBmUPzvI8uXv9Zt0wcNeM5AjuQFTNZPuCJctnevV/P9G9zlcIAMvRLkG40g61Hc6NcUYlFRIL0MU9aqUB8L3ngelYd0O057xDkSK92OBkZ6KLLrdmhwdz5dSUwudzPo79zFaghlzUc5yP/XSwbiLDNKcwjqFg28J6TFE5Kg1px8OE9aPrYPJMd84b25R7VcIfFk5rKawXyv+GsjpCkwunoXo2hJs0ozQbcKGA88nKfQgerOPsja31mWLI+8mEtM6glVMbYvi+OiqI/fkeaRAe5M5sQ/jXl4tQgoQRHP/1Ruo5OOQiQLUqHhDnDSuJ2UovGNVHs3SHjsfTIw+b4Q6H60kkXFrZhz7SVbeBNgpiZKV9r3NlsW7pZ2peV8Tey8fQOIQaDe7vfsweao5glhucoNDjF1C2jXamVyncdtGnzV4abYaWEUhMmIMHK0hAh4qsHT5Yp4mbMNudc7X2+gsep8ElCTcnaqGPfkesLpwG7s4QYlTnVrwoI/kW8Rcoe8HFdnw0rpZXCAdry+yUYseninFXjCbrYWea9cwQ7NUUKR80UR5u6yE2WnSdL1bp5OGn0Z/zQvNsHZG/2GEIhrgeuOWJmOLdle32uwkx+Nhe+qe1Ok8Y9YFgekThK0hIlpaRJntK0HZUAbjUymXv4CE/hlwmujuAgK951NJ+uIsyOFc2T8sUYe6XequGee8rfUT/G403d7gYHZ1s0y38h8HxENoUyK1bChERHCzIOkt7A25zFViHKxs4GUP90uBE4zJu6m0ymKcEezkIVFeVyetSn2TalbguMrE/IuQV+L7qTs5NJZJo+mri6JaTSivIRdhrqgdRJDz530SNCM6wVADBfUbq9MVkO9ww0i20JcoATpewhLYr0mkvQWI/+kc+8XmW60KkczjNgIhzBjtTmn/E0eP+E01ubtscwVy0=" />
</div>
        <div class="modal-dialog" role="document">
            <div class="modal fade" id="myModalCopy" tabindex="-1" role="dialog" aria-labelledby="myModalCopyLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="myModalCopyLabel">复制文件</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="txt_departmentname">请输入复制文件到那个文件夹中，如果指定文件夹不存在，则自动创建。</label>
                            </div>
                            <div class="form-group">
                                <label for="txt_departmentname">请输入目录</label>
                                <input name="txtCopy" type="text" value="/web/usr/plugins/GithubStatic" id="txtCopy" class="form-control" placeholder="请输入文件复制目标的目录" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span>关闭</button>
                            <input type="submit" name="BtnCopy" value="确认复制" id="BtnCopy" class="btn btn-primary" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="myModalMove" tabindex="-1" role="dialog" aria-labelledby="myModalMoveLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="myModalMoveLabel">移动文件</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="txt_departmentname">请输入移动文件到那个文件夹中，如果指定文件夹不存在，则自动创建。</label>
                            </div>
                            <div class="form-group">
                                <label for="txt_departmentname">请输入目录</label>
                                <input name="txtMove" type="text" value="/web/usr/plugins/GithubStatic" id="txtMove" class="form-control" placeholder="请输入文件移动目标的目录" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span>关闭</button>
                            <input type="submit" name="BtnMove" value="确认移动" id="BtnMove" class="btn btn-primary" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="myModalTextEditor" tabindex="-1" role="dialog" aria-labelledby="mmyModalTextEditorLabel">
                <div class="modal-body" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="ftpEditorClearText()"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="mmyModalTextEditorLabel">查看文件</h4>
                        </div>
                        <div class="modal-body" id="myModalTextEditorView">
                            <pre class="prettyprint linenums">      
                           <ol class="linenums pre-scrollable" id="precodearea">
                            </ol></pre>
                        </div>
                        <div class="modal-body" id="myModalTextEditorEdit">
                            <textarea name="txtEditorText" rows="2" cols="20" id="txtEditorText" class="txtEditorText">
</textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal" onclick="ftpEditorClearText()"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span>关闭</button>
                            <input type="submit" name="BtnTextUpdate" value="确认更新" id="BtnTextUpdate" class="btn btn-primary" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="myModalDel" tabindex="-1" role="dialog" aria-labelledby="myModalDelLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="myModalDelLabel">删除文件提示</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="txt_departmentname">请确认要删除选择的文件吗？<br />
                                    <small>注：如果某个文件删除不了。<br />
                                        1、可能是程序一直占用着，请在用户中心-主机管理-管理-回收应用程序池-下后，再删除。<br />
                                        2、停止网站再删除。请在用户中心-主机管理-管理-主机相关信息-停止网站，再删除。</small></label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span>取消</button>
                            <input type="submit" name="btnDelete" value="确认" id="btnDelete" class="btn btn-danger" />

                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="myModalChmod" tabindex="-1" role="dialog" aria-labelledby="myModalChmodLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="myModalChmodLabel">重置文件权限提示</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="txt_departmentname">
                                    能解决由权限不足引起的文件不能写入，不能创建文件夹，不能上传文件等问题。
                          <br />
                                    <small>注：若是空间容量不足导致的请升级空间大小，具体使用量在用户中心-主机管理-查看空间使用容量，谢谢。</small></label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span>取消</button>
                            <input type="submit" name="BtnResetChmod" value="确认" id="BtnResetChmod" class="btn btn-primary" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="myModalDownload" tabindex="-1" role="dialog" aria-labelledby="myModalDownloadLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="myModalDownloadLabel">下载文件提示</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="txt_departmentname">请确认要下载所选中文件吗？(注：只能是单个文件，不能是文件夹)</label>
                            </div>
                            
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span>取消</button>

                            <input type="submit" name="btnFastDownload" value="确认下载" id="btnFastDownload" class="btn btn-primary" />

                            <input type="submit" name="btnDownload" value="下载通道2" id="btnDownload" class="btn btn-default" />

                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="myModalAddDir" tabindex="-1" role="dialog" aria-labelledby="myModalAddDirLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="myModalAddDirLabel">新建文件夹</h4>
                        </div>
                        <div class="modal-body">

                            <div class="form-group">
                                <label for="txt_departmentname">请输入新建文件夹名字  当前目录：/web/usr/plugins/GithubStatic</label>
                                <input name="txtAddDirName" type="text" id="txtAddDirName" class="form-control" placeholder="请输入新建文件夹的名字" />
                            </div>
                            <div class="form-group">
                                <input type="submit" name="btnCreateDirectory" value="确认新建" id="btnCreateDirectory" class="btn btn-primary" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="myModalLogin" tabindex="-1" role="dialog" aria-labelledby="myModalLoginLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="myModalLoginLabel">登录FTP</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="txt_departmentname">请输入FTP地址</label>
                                <input name="txtFtpIP" type="text" id="txtFtpIP" class="form-control" placeholder="请输入FTP地址" />
                            </div>
                            <div class="form-group">
                                <label for="txt_departmentname">请输入FTP用户名</label>
                                <input name="txtFtpname" type="text" id="txtFtpname" class="form-control" placeholder="请输入FTP用户名" />
                            </div>
                            <div class="form-group">
                                <label for="txt_departmentname">请输入FTP密码</label>
                                <input name="txtFtppassword" type="text" id="txtFtppassword" class="form-control" placeholder="请输入FTP密码" />
                            </div>
                            <div class="form-group">
                                <input type="submit" name="BtnLoginFTP" value="登录" id="BtnLoginFTP" class="btn btn-primary" />
                                <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span>取消</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="myModalUpload" tabindex="-1" role="dialog" aria-labelledby="myModalUploadLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="myModalUploadLabel">上传文件</h4>
                        </div>
                        <div class="modal-body">

                            <div class="form-group">
                                <label for="txt_departmentname">请选择上传文件 /web/usr/plugins/GithubStatic <small class="warning">注：最大不要超过2G文件，建议500M以内，最大响应时间2小时，上传进度条看 浏览器 ↙左下角。</small></label>
                                <input name="file1" type="file" id="file1" class="form-control" placeholder="请点击选择文件上传" />
                            </div>
                            <div class="form-group">
                                <input type="submit" name="btnUpload" value="确认上传" id="btnUpload" class="btn btn-primary" />
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="modal fade" id="myModalZip" tabindex="-1" role="dialog" aria-labelledby="myModalZipLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="myModalZipLabel">打包文件操作</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="txt_departmentname">请确认打包/WEB所有文件及文件夹到/DB下吗？</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span>取消</button>
                            <input type="submit" name="btnFileZipWebToDb" value="确认打包" id="btnFileZipWebToDb" class="btn btn-primary" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="myModalUnzip" tabindex="-1" role="dialog" aria-labelledby="myModalUnzipLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="myModalUnzipLabel">解压文件提示</h4>
                            
                        </div>
                        <div class="modal-body">
                            <div class="form-group">

                                <label for="txt_departmentname">请确认文件解压吗？会覆盖旧文件！支持RAR格式与ZIP格式解压(支持同时解压多个压缩包)，压缩包请勿有密码，否则解压失败。</label>
                                <br />
                                <label for="txt_departmentname">
                                    <span class="Radio button"><input id="radToweb" type="radio" name="sss" value="radToweb" checked="checked" /><label for="radToweb">解压到/web网站目录</label></span>
                                    <span class="Radio button"><input id="radToCurrUrl" type="radio" name="sss" value="radToCurrUrl" /><label for="radToCurrUrl">解压到当前目录</label></span></label>
                                <br />
                                目录：<label for="txt_departmentname"><input name="txtUnzipUrl" type="text" value="/web" id="txtUnzipUrl" class="form-control" placeholder="请输入解压到目录路径" /></label>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span>取消</button>
                            <input type="submit" name="btnFileUnZip" value="确认解压" id="btnFileUnZip" class="btn btn-primary" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="myModalRename" tabindex="-1" role="dialog" aria-labelledby="myModalRenameLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <h4 class="modal-title" id="myModalRenameLabel">重命名操作</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="txt_departmentname">原始旧名称</label>
                                <input name="txtFileNameOld" type="text" id="txtFileNameOld" class="form-control" placeholder="原始旧名称" />
                                <label for="txt_departmentname">请输新名称</label>
                                <input name="txtFileNameNew" type="text" id="txtFileNameNew" class="form-control" placeholder="请输新文件名或文件夹名字" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span>关闭</button>
                            <input type="submit" name="btnRename" value="确认修改" id="btnRename" class="btn btn-primary" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="container">

            <div class="navbar navbar-default navbar-fixed-top" style="padding-top: 8px;">

                <button type="button" class="btn btn-default" onclick="ftphome()"><span class="glyphicon glyphicon-home"></span></button>
                <button type="button" class="btn btn-primary" onclick="ftpupload()"><span class="glyphicon glyphicon-upload"></span>上传文件</button>
                <button type="button" class="btn btn-default" onclick="ftpadddir()"><span class="glyphicon glyphicon-folder-close"></span>新建文件夹</button>
                <div class="btn-group">
                    <button type="button" class="btn btn-default" onclick="ftprefresh()"><span class="glyphicon glyphicon-refresh"></span>刷新</button>
                    <button type="button" class="btn btn-default" onclick="ftpunzip()"><span class="glyphicon glyphicon-resize-full"></span>解压</button>
                    <button type="button" class="btn btn-default" onclick="ftpzip()"><span class="glyphicon glyphicon-resize-small"></span>整站打包</button>
                    <button type="button" class="btn btn-default" onclick="ftpdownload()"><span class="glyphicon glyphicon-download-alt"></span>下载</button>
                    <button type="button" class="btn btn-default" onclick="ftpdel()"><span class="glyphicon glyphicon-remove-circle"></span>删除</button>
                    <button type="button" class="btn btn-default" onclick="ftpmove()"><span class="glyphicon glyphicon-move"></span>移动</button>
                    <button type="button" class="btn btn-default" onclick="ftpcopy()"><span class="glyphicon glyphicon-copyright-mark"></span>复制</button>
                    <button type="button" class="btn btn-default" onclick="ftpchmod()"><span class="glyphicon glyphicon-retweet"></span>重置权限</button>
                    <span id="labFtpServerIP"></span>
                    <span id="labFtpUsername"></span>
                    <span id="labFtpPassword"></span>
                    <span id="labFtpdirectory"></span>
                    <span id="labFtpRemotePath"></span>
                    
                </div>
            </div>
        </div>
        <br />

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">文件列表  <code>ftp用户名：qiaoqian</code></h3>
            </div>
            <div class="panel-body">
                <div><span class="glyphicon glyphicon-globe"></span><span id="thisUrl">/web/usr/plugins/GithubStatic</span></div>
                <table class="table table-bordered table-striped" id="BrowseForm">
                    <thead>
                        <tr>
                            <th class="leftSelectBox">
                                <input name="checkBoxALL1" id="checkBoxALL1"  type="checkbox" onclick="checkAll(this)" /></th>
                            <th>文件名</th>
                            <th>类型</th>
                            <th>大小</th>
                            <th>权限</th>
                            <th>创建时间</th>
                            <th>操作</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="tdGoBack"></td>
                            <td colspan="6" class="tdGoBack"><span class="glyphicon glyphicon-arrow-up"></span>
                                <input type="submit" name="btnGoback" value="返回上一层" id="btnGoback" class="btn btn-link" />
                            </td>
                        </tr>
                        
                                <tr>
                                    <td class="leftSelectBox">
                                        <label>
                                            <input id="Repeater1_CheckBox1_0" type="checkbox" name="Repeater1$ctl00$CheckBox1" onclick="checkOnce(this);" /></label></td>
                                    <td><span class="glyphicon glyphicon-folder-open"></span>  <a href="?sid=ddfddac0ee534a96bb5f148c59945ac1&ftp=/web/usr/plugins/GithubStatic/.github">.github</a></td>
                                    <td>
                                        <span id="Repeater1_labFileType_0">dir</span></td>
                                    <td></td>
                                    <td>-rwxrwxrwx </td>
                                    <td>2021-06-18 12:49</td>
                                    <td class="tablelast">
                                        
                                        

                                        

                                        
                                    </td>
                                </tr>
                            
                                <tr>
                                    <td class="leftSelectBox">
                                        <label>
                                            <input id="Repeater1_CheckBox1_1" type="checkbox" name="Repeater1$ctl01$CheckBox1" onclick="checkOnce(this);" /></label></td>
                                    <td><span class="glyphicon glyphicon-folder-open"></span>  <a href="?sid=ddfddac0ee534a96bb5f148c59945ac1&ftp=/web/usr/plugins/GithubStatic/cache">cache</a></td>
                                    <td>
                                        <span id="Repeater1_labFileType_1">dir</span></td>
                                    <td></td>
                                    <td>-rwxrwxrwx </td>
                                    <td>2021-06-18 13:16</td>
                                    <td class="tablelast">
                                        
                                        

                                        

                                        
                                    </td>
                                </tr>
                            
                                <tr>
                                    <td class="leftSelectBox">
                                        <label>
                                            <input id="Repeater1_CheckBox1_2" type="checkbox" name="Repeater1$ctl02$CheckBox1" onclick="checkOnce(this);" /></label></td>
                                    <td>Action.php</td>
                                    <td>
                                        <span id="Repeater1_labFileType_2">.php</span></td>
                                    <td>1.43 KB</td>
                                    <td>-rwxrwxrwx </td>
                                    <td>2021-06-18 12:49</td>
                                    <td class="tablelast">
                                        
                                        

                                        

                                        
                                    </td>
                                </tr>
                            
                                <tr>
                                    <td class="leftSelectBox">
                                        <label>
                                            <input id="Repeater1_CheckBox1_3" type="checkbox" name="Repeater1$ctl03$CheckBox1" onclick="checkOnce(this);" /></label></td>
                                    <td>Auth.php</td>
                                    <td>
                                        <span id="Repeater1_labFileType_3">.php</span></td>
                                    <td>2.12 KB</td>
                                    <td>-rwxrwxrwx </td>
                                    <td>2021-06-18 12:49</td>
                                    <td class="tablelast">
                                        
                                        

                                        

                                        
                                    </td>
                                </tr>
                            
                                <tr>
                                    <td class="leftSelectBox">
                                        <label>
                                            <input id="Repeater1_CheckBox1_4" type="checkbox" name="Repeater1$ctl04$CheckBox1" onclick="checkOnce(this);" /></label></td>
                                    <td>Debug.php</td>
                                    <td>
                                        <span id="Repeater1_labFileType_4">.php</span></td>
                                    <td>2.38 KB</td>
                                    <td>-rwxrwxrwx </td>
                                    <td>2021-06-18 12:49</td>
                                    <td class="tablelast">
                                        
                                        

                                        

                                        
                                    </td>
                                </tr>
                            
                                <tr>
                                    <td class="leftSelectBox">
                                        <label>
                                            <input id="Repeater1_CheckBox1_5" type="checkbox" name="Repeater1$ctl05$CheckBox1" onclick="checkOnce(this);" /></label></td>
                                    <td>Helper.php</td>
                                    <td>
                                        <span id="Repeater1_labFileType_5">.php</span></td>
                                    <td>4.32 KB</td>
                                    <td>-rwxrwxrwx </td>
                                    <td>2021-06-18 13:31</td>
                                    <td class="tablelast">
                                        
                                        

                                        

                                        
                                    </td>
                                </tr>
                            
                                <tr>
                                    <td class="leftSelectBox">
                                        <label>
                                            <input id="Repeater1_CheckBox1_6" type="checkbox" name="Repeater1$ctl06$CheckBox1" onclick="checkOnce(this);" /></label></td>
                                    <td>LICENSE</td>
                                    <td>
                                        <span id="Repeater1_labFileType_6"></span></td>
                                    <td>1.04 KB</td>
                                    <td>-rwxrwxrwx </td>
                                    <td>2021-06-18 12:49</td>
                                    <td class="tablelast">
                                        
                                        

                                        

                                        
                                    </td>
                                </tr>
                            
                                <tr>
                                    <td class="leftSelectBox">
                                        <label>
                                            <input id="Repeater1_CheckBox1_7" type="checkbox" name="Repeater1$ctl07$CheckBox1" onclick="checkOnce(this);" /></label></td>
                                    <td>Plugin.php</td>
                                    <td>
                                        <span id="Repeater1_labFileType_7">.php</span></td>
                                    <td>10.07 KB</td>
                                    <td>-rwxrwxrwx </td>
                                    <td>2021-06-18 14:09</td>
                                    <td class="tablelast">
                                        
                                        

                                        

                                        
                                    </td>
                                </tr>
                            
                                <tr>
                                    <td class="leftSelectBox">
                                        <label>
                                            <input id="Repeater1_CheckBox1_8" type="checkbox" name="Repeater1$ctl08$CheckBox1" onclick="checkOnce(this);" /></label></td>
                                    <td>README.md</td>
                                    <td>
                                        <span id="Repeater1_labFileType_8">.md</span></td>
                                    <td>1.13 KB</td>
                                    <td>-rwxrwxrwx </td>
                                    <td>2021-06-18 12:49</td>
                                    <td class="tablelast">
                                        
                                        

                                        

                                        
                                    </td>
                                </tr>
                            
                    </tbody>

                    <tfoot>
                        <tr>
                            <th class="leftSelectBox">
                                 <input name="checkBoxALL2" id="checkBoxALL2" type="checkbox" onclick="checkAll(this)" />
                            </th>
                            <th colspan="6">全选 / 全不选</th>

                        </tr>
                    </tfoot>
                </table>

            </div>
        </div>
        <div style="display: none">
            <input name="labUnzipFilenameOp" type="text" id="labUnzipFilenameOp" />
            <input name="txtViewFilename" type="text" id="txtViewFilename" />
            <input name="txtViewFiletype" type="text" id="txtViewFiletype" />
            <input type="submit" name="btnView" value="" id="btnView" />
            <input type="submit" name="btnRefresh" value="" id="btnRefresh" />
            <button type="button" class="btn btn-primary" onclick="ftplogin()"><span class="glyphicon glyphicon-log-in"></span>登录</button>
            <input type="submit" name="btnFileZip" value="选中打包" id="btnFileZip" class="btn btn-primary" />
            <span class="glyphicon glyphicon-file"></span>
            <input type="submit" name="btnCreateFile" value="新建文件" id="btnCreateFile" class="btn btn-primary" />
            <script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://"); document.write(unescape("%3Cspan id='cnzz_stat_icon_1262268349'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s22.cnzz.com/stat.php%3Fid%3D1262268349' type='text/javascript'%3E%3C/script%3E"));</script>

        </div>
    </form>
    <script>
        $(function () {
            $("table tbody tr:gt(0)").each(function (i) {
                // alert("这是第" + i + "行内容");
                var url = $("#thisUrl").text();
                if (url == '') {
                    $(":checkbox").attr("disabled", true);
                    $(".tablelast").html("");
                    return;
                }
                $(this).children("td").each(function (j) {
                    if ((j == 1) && ($(this).text().trim() != '')) {
                        var filename = $(this).text().trim();//文件名称
                        //alert('filename='+filename);
                        var filetype = $(this).next("td").text().trim();//文件类型
                        // alert('filetype=' + filetype);
                        $(this).next("td").next("td").next("td").next("td").next("td").html(formatOpHtml(filename, filetype));
                    }
                });
            });
        });

        var urlcu = $("#thisUrl").text();
        $("#radToweb").click(function () {
            $("#txtUnzipUrl")[0].value = "/web";
        });
        $("#radToCurrUrl").click(function () {
            $("#txtUnzipUrl")[0].value = urlcu;
        });

    </script>
</body>

</html>
