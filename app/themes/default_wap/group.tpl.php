<?php require 'header.php';?>
<div class="page-content">
    <div class="rich_media_title"><?=$group->name?></div>
    <div class="rich_media_meta_list">
        <span class="date"><?=date('Y-m-d')?></span>
        <span class="nickname"><?=app('\App\Model\System')->getCode('webname');?></span>
    </div>
    <div class="content_txt">
        <?=nl2br($group->remark)?>
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
    <script src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" charset="utf-8">
        wx.config(<?=$config?>);
        wx.ready(function () {
            wx.onMenuShareTimeline({
                title: '<?=$group->name?>', // 分享标题
                link: window.location.href, // 分享链接
                imgUrl: '<?=$group->picture?>', // 分享图标
                success: function () {
                },
                cancel: function () {
                }
            });
            wx.onMenuShareAppMessage({
                title: '<?=$group->name?>', // 分享标题
                desc: '<?=$group->remark?>', // 分享描述
                link: window.location.href, // 分享链接
                imgUrl: '<?=$group->picture?>', // 分享图标
                type: '', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
        });
    </script>


<?php require 'footer.php';?>