<?php require 'header.php';?>
    <div class="m_header">
        <a class="m_header_l" href="javascript:history.go(-1)"><i class="iconfont">&#xe604;</i></a>
        <a class="m_header_r" href=""></a>
        <h1>我的购物车</h1>
    </div>

    <form method="post">
        <?  foreach ($result_carts as $i=>$carts) : ?>
            <div class="order_box">

                <a class="order_shopBar"><i class="iconfont">&#xe854;</i><em>我的小店<?=$cart->seller_id?></em></a>
                <? foreach($carts as $cart): ?>
                    <div class="order_item clearFix">
                        <input class="checkbox"  type="checkbox" goods_id="<?=$cart->goods_id?>" spec_id="<?=$cart->spec_id?>" checked name="cart_id[]" value="<?=$cart->id?>">

                        <i class="weui-icon-checked"></i>
                        <img class="image" src="<?=$cart->goods_image?>">
                        <div class="oi_content" style="float: left">
                            <a><?=$cart->goods_name?></a>
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
                        <span class="goods_stock_count hide"><?=$cart->stock_count?></span>
                        <div class="wrap-input">
                            <span class="btn-reduce">-</span>
                            <input class="text" value="<?=$cart->quantity?>"  maxlength="5" type="text" name="quantity" onkeyup="value=value.replace(/[^0-9]/g,'')">
                            <span class="btn-add">+</span>
                        </div>
                    </div>
                <? endforeach;?>
            </div>
        <? endforeach;?>
        <div class="cart_bottom">
            <p><label><input type="checkbox" class="checkall" checked>全选</label></p>
            <div class="total">
                <p>总计：<strong id="totalPrice">¥<span></span></strong><small>(不含运费)</small></p>
                <a href="javascript:;" class="btn_pay">去结算<em id="totalNum">(<span></span>件)</em></a>
            </div>
        </div>
    </form>
<script>


    $(function () {
        getCartedMoney();
        $("input[name='cart_id[]']").on('click',function () {
            getCartedMoney();
        });
        $('.checkall').on('click',function () {
            $("input[name='cart_id[]']").attr('checked',this.checked);
            getCartedMoney();
        });

        $('.wrap-input .btn-reduce').on('click',function(){
            var input=$(this).parent().find('input');
            var num=Number(input.val());
            if(num>1){
                input.val(num-1);
            }
        });
        $('.wrap-input .btn-add').on('click', function () {
            var input=$(this).parent().find('input');
            var num=Number(input.val());
            var max=Number($(this).parent().prev('.goods_stock_count').html());

            //var chkbox=$(this).parents('.order_item').find('input:checkbox');
           // $.get("/index.php/goods/getQuantity?id="+chkbox.attr('goods_id')+"&spec_id="+chkbox.attr('spec_id'), function (data) { });

            if(num < max){
                input.val(num+1);

            }
        });
    });

    //勾选商品
    function getCartedMoney() {
        var cart_id = "";
        var allchecked = true;
        $("input:checkbox[name='cart_id[]']").each(function (i) {
            if ($(this).attr('checked')) {
                if (cart_id == "") {
                    cart_id = $(this).val();
                } else {
                    cart_id += ("," + $(this).val());
                }
            } else {
                if (allchecked == true) {
                    allchecked = false;
                }
            }
        });
        $(".checkall").attr("checked", allchecked);
        $.get("/index.php/cart/getSelectedMoney?cart_ids=" + cart_id, function (data) {
            if (data != "") {
                var data = eval('(' + data + ")");
                $("#totalPrice span").html(data.total);
                $("#totalNum span").html(data.nums);
            } else {
                $("#totalPrice span").html(0);
                $("#totalNum span").html(0);
            }
        });
    }
</script>
<?php require 'footer.php';?>