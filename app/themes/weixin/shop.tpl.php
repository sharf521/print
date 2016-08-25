<?php require 'header.php';?>
<script src="/plugin/js/ajaxfileupload.js?111"></script>
<? if($this->func=='index') : ?>
    <div class="header_tit">店铺列表<a class="header_right" href="<?=url('shop/add')?>">添加</a></div>
    <div class="shop_list">
        <? if(empty($list)) : ?>
            <div class='alert-warning'>没有找到匹配的记录！</div>
        <? endif;?>
        <ul>
            <? foreach ($list as $shop) : ?>
                <li class="clearFix">
                    <img class="img" src="<?=$shop->picture?>">
                    <div class="shop_info clearFix">
                        <div class="shop_title">
                            <?= $shop->name ?>
                            <span class="edit">
                                <a href="<?= url("shop/edit/?id={$shop->id}") ?>">修改</a>
                                <a href="<?= url("shop/delete/?id={$shop->id}") ?>" onclick="return confirm('确定要删除吗？')"></a>
                                <?
                                $shopGroup=$shop->ShopGroup();
                                foreach ($shopGroup as $_item){
                                    ?>
                                    <a href="<?=url("/group/detail/?id={$_item->group_id}&user_id={$shop->user_id}") ?>">联盟</a>
                                    <?
                                }
                                ?>
                            </span>
                        </div>
                        <div class="shop_remark"><?= nl2br($shop->remark) ?></div>
                    </div>
                </li>
            <? endforeach; ?>
        </ul>
    </div>
<? elseif ($this->func=='add') : ?>
    <div class="header_tit">添加商铺</div>
    <div class="container">
        <form method="post">
            <table class="table_from">
                <tr><td>店名：</td><td><input type="text" name="name"></td></tr>
                <tr><td>门头：</td><td>
                        <input type="hidden" name="picture" id="picture"
                               value=""/>
						<span id="upload_span_picture">
                        </span>
                        <div class="upload-upimg">
                            <span class="_upload_f">上传文件</span>
                            <input type="file" id="upload_picture" name="files"
                                   onchange="upload_image('picture','shop')"/>
                        </div>
                    </td></tr>
                <tr><td>主营：</td><td><textarea name="remark" rows="5"></textarea></td></tr>
                <tr><td>地址：</td><td><input type="text" name="address" id="address"></td></tr>
            </table>
            <input type="submit" value="提 交" class="submit">
        </form>
        <script src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js" type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript" charset="utf-8">
            wx.config(<?=$config?>);
            wx.ready(function () {
                wx.getLocation({
                    type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
                    success: function (res) {
                        var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
                        var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
                        //var speed = res.speed; // 速度，以米/每秒计
                        //var accuracy = res.accuracy; // 位置精度
                        $.post("/index.php/plugin/getAddress/", {
                            'lat': latitude,
                            'lon': longitude
                        }, function (str) {
                            $('#address').val(str);
                        });
                    }
                });
            });
        </script>
    </div>
<? elseif ($this->func=='edit') :  ?>
    <div class="header_tit">修改店铺</div>
    <div class="container">
        <form method="post">
            <table class="table_from">
                <tr><td>店名：</td><td><input type="text" name="name"  value="<?=$shop->name?>"></td></tr>
                <tr><td>门头：</td><td>
                        <input type="hidden" name="picture" id="picture"
                               value="<?=$shop->picture?>"/>
						<span id="upload_span_picture">
                            <? if ($shop->picture != '') { ?>
                                <a href="<?= $shop->picture ?>" target="_blank"><img
                                        src="<?= $shop->picture ?>" align="absmiddle" width="100"/></a>
                            <? } ?>
                        </span>
                        <div class="upload-upimg">
                            <span class="_upload_f">上传文件</span>
                            <input type="file" id="upload_picture" name="files"
                                   onchange="upload_image('picture','shop')"/>
                        </div>
                    </td></tr>
                <tr><td>主营：</td><td><textarea name="remark" rows="5"><?=$shop->remark?></textarea></td></tr>
                <tr><td>地址：</td><td><input type="text" name="address" value="<?=$shop->address?>"></td></tr>
            </table>
            <input type="submit" value="提 交" class="submit">
        </form>
    </div>
<? endif;?>
<?php require 'footer.php';?>