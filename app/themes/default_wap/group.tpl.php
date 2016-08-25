<?php require 'header.php';?>
    <div class="m_regtilinde"><?=$group->name?></div>
    <br>
    <div class="ca_d_table">
        <?=$group->remark?>
    </div>
    <div class="qrcode_div">
        <img src="<?=$qrcodeSrc?>">
    </div>
    <div class="shop_list">
        <? if(empty($shopList)) : ?>
            <div class='alert-warning'>没有找到匹配的记录！</div>
        <? endif;?>
        <ul>
            <? foreach ($shopList as $item) :
                $shop=$item->Shop();
                ?>
                <li class="clearFix">
                    <img class="img" src="<?=$shop->picture?>">
                    <div class="shop_info clearFix">
                        <div class="shop_title">
                            <?= $shop->name ?>
                        </div>
                        <div class="shop_remark"><?= nl2br($shop->remark) ?></div>
                    </div>
                </li>
            <? endforeach; ?>
        </ul>
    </div>
<?php require 'footer.php';?>