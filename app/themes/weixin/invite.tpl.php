<?php require 'header.php'; ?>
    <div class="header_tit">邀请商家：</div>
    <div class="qrcode_div">
        <img src="<?=$qrcodeSrc?>">
    </div>
    <div class="invite_box">
        <h3>己邀请列表</h3>
        <ul>
            <? foreach ($invites as $user) : ?>
            <li class="clearFix">
                <img src="<?=substr($user->UserWX()->headimgurl,0,-1)?>64" width="50">
                <div class="invite_info">
                    <?=$user->nickname?><br>
                    <span class="time"><?=$user->created_at?></span></div>
            </li>
            <? endforeach;?>
        </ul>
    </div>
<?php require 'footer.php'; ?>