<?php require 'header.php';?>
    <div class="m_header">
        <a class="m_header_l" href="javascript:history.go(-1)"><i class="iconfont">&#xe604;</i></a>
        <a class="m_header_r" href=""></a>
        <h1>我的购物车</h1>
    </div>

    <? if(count($result_carts)==0) : ?>
    <div class="cart_empty margin_header">
        购物车内还没有商品！<br>
        <a href="javascript:;" class="weui-btn weui-btn_plain-primary weui-btn_mini">去逛逛</a>
    </div>
    <? else : ?>
    <div class="margin_header" style="margin-bottom: 60px">
        <?  foreach ($result_carts as $i=>$carts) : ?>
            <div class="cart_box">
                <a class="shopBar"><i class="iconfont">&#xe854;</i><em>我的小店<?=$cart->seller_id?></em></a>
                <? foreach($carts as $cart): ?>
                    <div class="goods_item clearFix">
                        <input class="checkbox"  type="checkbox" checked name="cart_id[]" value="<?=$cart->id?>">
                        <a href="<?=url("goods/detail/?id={$cart->goods_id}")?>">
                            <img class="image" src="<?=$cart->goods_image?>">
                            <div class="oi_content" style="float: left">
                                <?=$cart->goods_name?>
                                <p><?
                                    if($cart->spec_1!=''){
                                        echo "<span class='spec'>{$cart->spec_1}</span>";
                                    }
                                    if($cart->spec_2!=''){
                                        echo "<span class='spec'>{$cart->spec_2}</span>";
                                    }
                                    ?>
                                    <span class="count">¥<?=$cart->price?></span></p>
                            </div></a>
                        <div class="wrap-input">
                            <span class="btn-reduce">-</span>
                            <input class="text" value="<?=$cart->quantity?>"  maxlength="5" type="text" name="quantity" onkeyup="value=value.replace(/[^0-9]/g,'')">
                            <span class="btn-add">+</span>
                        </div>
                        <i class="iconfont del" data-id="<?=$cart->id?>">&#xe69d;</i>
                    </div>
                <? endforeach;?>
                <div class="cart_foot">小计：<em>¥<span class="shop_total" shop_id="<?=$i?>"></span></em></div>
            </div>
        <? endforeach;?>
        <div class="cart_bottom">
            <label><input type="checkbox" class="checkall" checked><br>全选</label>
            <div class="total">
                <p>总计：<strong id="totalPrice">¥<span></span></strong><small>(不含运费)</small></p>
                <a href="javascript:;" class="btn_pay">去结算<em id="totalNum">(<span></span>件)</em></a>
            </div>
        </div>
    </div>
    <? endif;?>
<script>
    $(function () {
        cart_js();
    });
</script>
<?php require 'footer.php';?>