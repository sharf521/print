<?php require 'header.php';?>
    <!--googpic-->
    <div class="swiper-container">
        <div class="swiper-wrapper" style="height: 320px;">
            <? foreach($images as $img) : ?>
            <div class="swiper-slide" style="text-align: center"><a href="#"><img src="<?=$img->image_url?>" style="max-width: 100%; max-height: 100%"></a></div>
            <? endforeach;?>
        </div>
        <div class="swiper-pagination"></div>
    </div>
    <!--googpic end-->
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
        <div class="weui-panel__bd">
            <div class="weui-media-box weui-media-box_text">
                <p class="weui-media-box__desc"><?=nl2br($GoodsData->content)?></p>
            </div>
        </div>
    </div>

<div class="bottom_opts">
    <a href="javascript:;" class="opt1">
        <i class="iconfont">&#xe698;</i>
        <p>购物车</p>
    </a>
    <a href="javascript:;" class="opt2">加入购物车</a>

    <a href="javascript:;" class="opt3">立即购买</a>
</div>
    <script>
        //首页幻灯图
        $(function(){
            var mySwiper = new Swiper('.swiper-container',{
                loop : true,
                autoplay:4800,
                autoplayDisableOnInteraction : false,
                pagination : '.swiper-pagination',
                paginationClickable :true,
            });
        });
    </script>
<?php require 'footer.php';?>