<?php require 'header.php';
if ($this->func == 'index') {
    ?>
    <div class="main_title">
        <span>修改密码</span>
    </div>
    <div class="main_content">
        <form method="post" onsubmit="return setdisabled();">
            <table class="table_from">
                <tr>
                    <td>原密码：</td>
                    <td><input type="password" name="old_password"/></td>
                </tr>
                <tr>
                    <td>新密码：</td>
                    <td><input type="password" name="password"/> 密码长度6位到15位</td>
                </tr>
                <tr>
                    <td>确认新密码：</td>
                    <td><input type="password" name="sure_password"/></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input class="but3" value="保存" type="submit"/></td>
                </tr>
            </table>
        </form>
    </div>
<? } ?>
<?php require 'footer.php'; ?>
