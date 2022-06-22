<?php
/**
 *插件调试面板
 * @package GithubFile
 * @author Mlikiowa<nineto0@163.com>
 * @version 1.1.0
 * @since 1.0.0
 */
function getTable($tid, $filename): string
{
    return '<tr id="log-' . $tid . '"><td >' . $filename . '</td><td><a href="/usr/plugins/GithubFile/cache/log/' . $filename . '">下载</a>&bull;<a lang="你确认要删除吗?" href="/action/GithubFile?do=del_log&filename=' . $filename . '">删除</a></td></tr>';
}

include 'header.php';
include 'menu.php';
?>
<div class='main'>
    <div class='body container'>
        <div class='typecho-page-title'>
            <h2>日志记录</h2>
        </div>
        <div class='row typecho-page-main' role='main'>
            <div class='col-mb-12 typecho-list'>
                <h4 class='typecho-list-table-title'>日志信息</h4>
                <div class='typecho-table-wrap'>
                    <table class='typecho-list-table'>
                        <colgroup>
                            <col width='60%'/>
                            <col width='40%'/>
                        </colgroup>
                        <thead>
                        <tr>
                            <th>文件名称</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $count = 0;
                        $path = __DIR__ . '/cache/log';
                        $files = scandir($path);
                        $fileItem = [];
                        foreach ($files as $v) {
                            $newPath = $path . DIRECTORY_SEPARATOR . $v;
                            if (is_file($newPath)) {
                                $count++;
                                echo getTable($count, $v);
                            }
                        }

                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include 'copyright.php';
include 'common-js.php';
include 'form-js.php';
include 'footer.php';
?>
