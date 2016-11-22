<?php require 'header.php';?>
    <div class="swiper-container">
        <div class="swiper-wrapper" style="height: 300px;">
            <? foreach($images as $img) : ?>
                <div class="swiper-slide" style="text-align: center">
                    <img src="<?=$img->image_url?>" style="max-width: 100%; height: 100%">
                </div>
            <? endforeach;?>
        </div>
        <div class="swiper-pagination"></div>
    </div>
    <div class="goods-header">
        <h2 class="title"><?=$goods->name?></h2>
        <div class="goods-price">
            <span>￥</span><i><?=$goods->price?></i>
        </div>
        <div class="stock-detail">
            运费: ¥<?=$goods->shipping_fee?> &nbsp;　&nbsp;剩余:<?=$goods->stock_count?>
        </div>
    </div>

    <div class="weui-panel" style="margin-bottom: 60px;">
        <div class="weui-panel__hd">详细说明</div>
        <article class="weui-article">
            <?=nl2br($GoodsData->content)?>
        </article>
    </div>

    <div class="bottom_opts">
        <a href="javascript:;" class="opt1">
            <i class="iconfont">&#xe698;</i>
            <p>购物车</p>
        </a>
        <a href="javascript:;" class="opt2">加入购物车</a>
        <a href="javascript:;showBuy()" class="opt3">立即购买</a>
    </div>
    <script>
        $(function(){
            var mySwiper = new Swiper('.swiper-container',{
                loop : true,
                autoplay:4800,
                autoplayDisableOnInteraction : false,
                pagination : '.swiper-pagination',
                paginationClickable :true,
            });
        });
        var index;
        function showBuy() {
            index=layer.open({
                type: 1
                ,content:$('#div_showBuy').html()
                ,anim: 'up'
                ,style: 'position:fixed; bottom:0; left:0; width: 100%; border:none;'
            });
        }
        function buy_reduce(o) {
            var input=$(o).parent().find('input');
            var num=Number(input.val());
            if(num>1){
                input.val(num-1);
            }
        }
        function buy_add(o) {
            var input=$(o).parent().find('input');
            var num=Number(input.val());
            input.val(num+1);
        }
    </script>
<div id="div_showBuy" class="hide">
    <div class="buy_box">
        <dl>
            <dt class="buy_box_title">
            <h4>q水库附近思考思考速度的反对</h4>
            <span>￥30.00</span>
            <i class="iconfont" onclick="layer.close(index);">&#xe725;</i>
            </dt>
            <dd></dd>
            <dd class="clearFix choose">
                <div class="stock_count">
                    <span>购买数量：</span><br>
                    剩余<span>10</span>件</div>
                <div class="wrap-input">
                    <span class="btn-reduce" onclick="buy_reduce(this);">-</span>
                    <input class="text" value="1"  maxlength="5" type="text" name="mun" onkeyup="value=value.replace(/[^0-9]/g,'')">
                    <span class="btn-add" onclick="buy_add(this);">+</span>
                </div>
            </dd>
        </dl>
        <div class="buy_box_opts">
            <a href="javascript:;" class="opt1">加入购物车</a>
            <a href="javascript:;" class="opt2">立即购买</a>
        </div>
    </div>
</div>
<?php require 'footer.php';?>