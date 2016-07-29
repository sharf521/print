<?php require 'header.php'; ?>
<div class="topbox clearFix">
    <h3><i class="fa fa-home"></i>管理后台</h3>
    <ul class="nav">
        <?
        //输出一级菜单
        $num = 0;
        foreach ($menu as $i => $m) {
            $num++;
            if ($num == 1)
                echo "<li class='checkit'>{$m['name']}</li>";
            else
                echo "<li>{$m['name']}</li>";
        }
        ?>
    </ul>
    <ul class="topnav">
        <li class="nihao">您好，<?= $this->username ?>！</li>
        <li class="tuichu"><?= $this->anchor('changepwd', '[修改密码]', 'target="iframe_main"') ?></li>
        <li class="tuichu"><?= $this->anchor('logout', '[退出]') ?></li>
    </ul>
</div>
<div class="neirong">
    <div class="leftpanel">
        <?
        $num = 0;
        foreach ($menu as $i => $m) {
            $num++;
            //每个一级菜单输出一个div
            ?>
            <div class="menu <? if ($num > 1) {
                echo 'hide';
            } ?>">
                <h1><?= $menu[$i]['name'] ?></h1>
                <ul>
                    <?php
                    //显示左侧二级菜单
                    if (isset($m['son']) && is_array($m['son'])) {
                        //$num1=0;
                        foreach ($m['son'] as $li) {
                            ?>
                            <li><a href="<?= url($li['url']) ?>" target="iframe_main"><?= $li['name'] ?></a></li>
                            <?
                        }
                    }
                    ?>
                </ul>
            </div>
            <?
        }
        ?>
    </div>
    <div class="rightpanel">
        <iframe marginheight="0" width="100%" marginwidth="0" frameborder="0" id="iframe_main" name="iframe_main"
                src=""></iframe>
    </div>
    <div class="clear"></div>
</div>
<script>
    $(document).ready(function () {
        initwh();
        init_menu();
        $(window).resize(function () {
            initwh();
        });
    })
</script>
</body>
</html>
