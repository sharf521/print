<?php require 'header.php';?>
    <div class="m_header">
        <a class="m_header_l" href="javascript:history.go(-1)"><i class="iconfont">&#xe604;</i></a>
        <a class="m_header_r" href=""></a>
        <h1>我的购物车</h1>
    </div>

    <div class="margin_header">
        <?  foreach ($result_carts as $i=>$carts) : ?>
            <div class="cart_box">
                <a class="shopBar"><i class="iconfont">&#xe854;</i><em>我的小店<?=$cart->seller_id?></em></a>
                <? foreach($carts as $cart): ?>
                    <div class="goods_item clearFix">
                        <input class="checkbox"  type="checkbox" checked name="cart_id[]" value="<?=$cart->id?>">

                        <i class="weui-icon-checked"></i>
                        <img class="image" src="<?=$cart->goods_image?>">
                        <div class="oi_content" style="float: left">
                            <a href="<?=url("goods/detail/?id={$cart->goods_id}")?>"><?=$cart->goods_name?></a>
                            <p><?
                                if($cart->spec_1!=''){
                                    echo "<span class='spec'>{$cart->spec_1}</span>";
                                }
                                if($cart->spec_2!=''){
                                    echo "<span class='spec'>{$cart->spec_2}</span>";
                                }
                                ?>
                                <span class="count">¥<?=$cart->price?></span></p>
                        </div>
                        <div class="wrap-input">
                            <span class="btn-reduce">-</span>
                            <input class="text" value="<?=$cart->quantity?>"  maxlength="5" type="text" name="quantity" onkeyup="value=value.replace(/[^0-9]/g,'')">
                            <span class="btn-add">+</span>
                        </div>
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
<script>
    $(function () {
        cart_js();
    });
</script>
<?php require 'footer.php';?>