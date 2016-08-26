<?php require 'header.php';?>
<div class="page-content">
    <div class="rich_media_title"><?=$group->name?></div>
    <div class="rich_media_meta_list">
        <span class="date"><?=date('Y-m-d')?></span>
        <span class="nickname"><?=app('\App\Model\System')->getCode('webname');?></span>
    </div>
    <div class="ca_d_table">
        <?=$group->remark?>
    </div>
    <div class="qrcode_div">
        <img src="<?=$qrcodeSrc?>" width="50%">
    </div>
    <div class="shop_list">
        <? if(empty($shopList)) : ?>
            <div class='alert-warning'>没有找到匹配的记录！</div>
        <? endif;?>
        <ul>
            <? foreach ($shopList as $item) :
                $shop=$item->Shop();
                if(! $shop->is_exist){continue;}
                ?>
                <li class="clearFix">
                    <img class="img" src="<?=$shop->picture?>">
                    <div class="shop_info clearFix">
                        <div class="shop_title">
                            <?= $shop->name ?>
                        </div>
                        <div class="shop_remark">
                            <?= nl2br($shop->remark) ?><br>
                            电话：<?=$shop->tel?><br>
                            地址：<?=$shop->address?><br>
                        </div>
                    </div>
                </li>
            <? endforeach; ?>
        </ul>
    </div>
</div>

<?php require 'footer.php';?>