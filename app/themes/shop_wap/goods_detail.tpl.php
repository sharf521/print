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
        <a href="javascript:;showBottomBuyBox()" class="opt3">立即购买</a>
    </div>
    <script>
        goods_detail_js();
    </script>
    <div class="weui-mask hide"></div>
    <div class="bottom_buy_box" id="bottom_buy_box">
        <form method="post" name="form_order">
            <dl>
                <dt class="buy_box_title">
                <h4><?=$goods->name?></h4>
                <span>¥<em id="goods_price"><?=$goods->price?></em></span>
                <i class="iconfont">&#xe725;</i>
                </dt>
                <? if($goods->is_have_spec) : ?>
                <dd class="clearFix">
                    <? $specs=$goods->GoodsSpec();?>
                    <script>
                        $(function(){
                            var specs = new Array();
                            <? foreach($specs as $spec) :?>
                            specs.push(new spec(<?=$spec->id?>, '<?=$spec->spec_1?>', '<?=$spec->spec_2?>', <?=$spec->price?>, <?=$spec->stock_count?>));
                            <? endforeach;?>
                            goodsSpec=new GoodsSpec(specs);
                            if(goodsSpec.specQty==1){
                                goodsSpec.setFormValue();
                            }
                        });
                    </script>
                    <input type="hidden" name="spec_id" id="spec_id">
                    <div id="specBox_1" class="spec_choose clearFix"></div>
                    <div id="specBox_2" class="spec_choose clearFix"></div>
                </dd>
                <? endif;?>
                <dd class="clearFix choose">
                    <div class="stock_count">
                        <span>购买数量：</span><br>
                        剩余<span id="goods_stock_count"><?=$goods->stock_count?></span>件</div>
                    <div class="wrap-input">
                        <span class="btn-reduce">-</span>
                        <input class="text" value="1"  maxlength="5" type="text" name="quantity" onkeyup="value=value.replace(/[^0-9]/g,'')">
                        <span class="btn-add">+</span>
                    </div>
                </dd>
            </dl>
            <div class="buy_box_opts">
                <a href="javascript:;" class="opt1">加入购物车</a>
                <a href="javascript:;" class="opt2">立即购买</a>
            </div>
        </form>
        <script>
            $('#bottom_buy_box .opt2').on('click',function(){
                var form=document.forms['form_order'];
                var quantity=form.quantity;
                var tag=true;
                if(Number(quantity.value)==0){
                    $(quantity).focus();
                    layer.open({
                        content: '请正确选择数量',
                        skin: 'msg',
                        time:1
                    });
                    tag=false;
                }
                if(tag){
                    form.submit();
                }
            })
        </script>
    </div>
<?php require 'footer.php';?>