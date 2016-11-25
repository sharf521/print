<?php require 'header.php';?>
    <div class="m_header">
        <a class="m_header_l" href="<?=url('')?>"><i class="iconfont">&#xe604;</i></a>
        <a class="m_header_r" href=""></a>
        <h1>我的订单</h1>
    </div>
<div class="order_address margin_header">
    <h4>收货地址</h4>
    <p>河南郑州市郑东新区东风南路与七里河交叉口绿地之窗云峰座A座804</p>
    <p><strong>乔少工</strong>13937127756</p>
</div>

<form method="post">
<?  foreach ($carts as $i=>$cart){
        if($cart->spec_id!=0){
            $spec=$cart->GoodsSpec();
            $spec_string=$spec->spec_1.'&nbsp;&nbsp;'.$spec->spec_2;
            $goods_count=$spec->stock_count;
        }else{
            $goods=$cart->Goods();
            $goods_count=$goods->stock_count;
        }
    ?>
        <? if($carts[$i]->seller_id!=$carts[$i-1]->seller_id){?>
            <div class="order_box">
            <a class="order_shopBar"><i class="iconfont">&#xe854;</i><em>我的小店<?=$cart->seller_id?></em></a>
        <? }?>
        <div class="order_item clearFix">
            <img class="image" src="<?=$cart->goods_image?>">
            <div class="oi_content">
                <a><?=$cart->goods_name?></a>
                <p><?=$spec_string?><span class="count">数量：<?=$cart->quantity?></span></p>
            </div>
        </div>
        <?
        if($carts[$i]->seller_id!=$carts[$i+1]->seller_id){
            ?>
             <textarea class="weui-textarea" style="background-color: #efefef; margin-top: 8px;font-size: 14px;" placeholder="请输入文本" rows="2">订单备注,选填.</textarea>
             </div>
            <?
        }
    }?>
    <div class="weui-btn-area">
        <input class="weui-btn weui-btn_primary" type="submit" value="提交订单">
    </div>
</form>
    <script type="text/javascript">
        function showMenu(id) {
            var $androidActionSheet = $('#androidActionsheet');
            $androidActionSheet.show();
            $androidActionSheet.find('.weui-mask').on('click',function () {
                $androidActionSheet.hide();
            });
            $androidActionSheet.find('.change').on('click',function () {
                location.href='<?=url("goods/change/?id=")?>'+id;
            });
            $androidActionSheet.find('.edit').on('click',function () {
                location.href='<?=url("goods/edit/?id=")?>'+id;
            });
            $androidActionSheet.find('.del').on('click',function () {
                layer.open({
                    content: '您确定要删除吗？'
                    ,btn: ['删除', '取消']
                    ,yes: function(index){
                        location.href='<?=url("goods/del/?id=")?>'+id;
                        layer.close(index);
                    }
                });
                $androidActionSheet.fadeOut(200);
            });
        }
    </script>
<?php require 'footer.php';?>