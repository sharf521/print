<?php require 'header.php'; ?>
    <div class="header_tit">邀请商家：</div>
    <div class="qrcode_div">
        <img src="<?=$qrcodeSrc?>">
    </div>
    <div class="invite_box">
        <h3>己邀请列表</h3>
        <? if(empty($invites)) : ?>
            <div class='alert-warning'>没有找到匹配的记录！</div>
        <? endif;?>
        <ul>
            <? foreach ($invites as $user) : ?>
            <li class="clearFix">
                <img src="<?=substr($user->UserWX()->headimgurl,0,-1)?>96">
                <div class="invite_info">
                    <?=$user->nickname?><br>
                    <span class="time"><?=$user->created_at?></span></div>
                <?
                $shops = $user->PrintShop();
                if (empty($shops)) : ?>
                    <a class="invite_btn but1" href="<?= url("shop/add/?user_id={$user->id}") ?>">添加商户</a>
                <? endif; ?>
            </li>
            <? endforeach;?>
        </ul>
    </div>
<?php require 'footer.php'; ?>