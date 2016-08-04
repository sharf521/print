<?php require 'header.php';?>
    <div class="header_tit">我要下单</div>
    <div class="container">
        <form method="post">
            <table class="table_from">
                <tr><td>类型：</td><td><?=$print_type?></td></tr>
                <tr><td>要求：</td><td><textarea name="remark" rows="5"></textarea></td></tr>
                <tr><td>电话：</td><td><input type="text" name="tel"></td></tr>
            </table>
            <input type="submit" value="提 交">
        </form>
    </div>
<?php require 'footer.php';?>