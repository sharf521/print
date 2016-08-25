<?php require 'header.php';?>
<? if($this->func=='index') : ?>
    <script src="/plugin/js/ajaxfileupload.js?111"></script>
    <div class="header_tit">商铺联盟</div>
    <div class="container">
        <h3>添加商铺</h3>
        <form method="post">
            <table class="table_from">
                <tr><td>名称：</td><td><input type="text" name="name"></td></tr>
                <tr><td>图片：</td><td>
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
                <tr><td>介绍：</td><td><textarea name="remark" rows="5"></textarea></td></tr>
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
                        var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90 34.761806
                        var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。113.76333
                        var speed = res.speed; // 速度，以米/每秒计
                        var accuracy = res.accuracy; // 位置精度
                        $.post("/index.php/plugin/getAddress/", {
                            'lat': latitude,
                            'lon': longitude
                        }, function (str) { alert(str) });
                    }
                });
            });
        </script>

    </div>

    <div class="shop_list">
        <h3>己邀请列表</h3>
        <ul>
            <? foreach ($list as $row) : ?>
                <li class="clearFix">
                    <img class="img" src="<?=$row->picture?>">
                    <div class="shop_info clearFix">
                        <div class="shop_title"><?= $row->name ?> <a href="<?= url("shop/edit/?id={$row->id}") ?>" class="edit">编辑</a></div>
                        <div class="shop_remark"><?= nl2br($row->remark) ?></div>
                    </div>
                </li>
            <? endforeach; ?>
        </ul>
    </div>


<? elseif ($this->func=='edit') :  ?>
    <script src="/plugin/js/ajaxfileupload.js?111"></script>
    <div class="header_tit">编辑店铺</div>
    <div class="container">
        <form method="post">
            <table class="table_from">
                <tr><td>名称：</td><td><input type="text" name="name"  value="<?=$shop->name?>"></td></tr>
                <tr><td>图片：</td><td>
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
                <tr><td>介绍：</td><td><textarea name="remark" rows="5"><?=$shop->remark?></textarea></td></tr>
            </table>
            <input type="submit" value="提 交" class="submit">
        </form>
    </div>
<? endif;?>
<?php require 'footer.php';?>