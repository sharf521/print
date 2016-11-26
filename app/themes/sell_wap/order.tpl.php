<?php require 'header.php';?>
    <div class="m_header">
        <a class="m_header_l" href="<?=url('/member')?>"><i class="iconfont">&#xe604;</i></a>
        <a class="m_header_r" href=""></a>
        <h1>我的订单</h1>
    </div>
    <div class="my-navbar margin_header">
        <div class="my-navbar__item <? if($this->func=='index'){echo 'my-navbar__item_on';}?>">
            <a href="<?=url('order')?>">全部订单</a>
        </div>
        <div class="my-navbar__item <? if($this->func=='status1'){echo 'my-navbar__item_on';}?>">
            <a href="<?=url('order/status1')?>">待付款</a>
        </div>
        <div class="my-navbar__item <? if($this->func=='status3'){echo 'my-navbar__item_on';}?>">
            <a href="<?=url('order/status3')?>">待发货</a>
        </div>
        <div class="my-navbar__item <? if($this->func=='status4'){echo 'my-navbar__item_on';}?>">
            <a href="<?=url('order/status4')?>">待收货</a>
        </div>
    </div>


<? foreach ($orders['list'] as $order) :
    $goods=$order->OrderGoods();
    ?>
    <div class="order_box">
        <div class="order_head">
            <div class="oh_content">
                <p class="pState"><span>状<i></i>态：</span><em class="co_blue">已签收</em></p>
                <p><span>总<i></i>价：</span><em class="co_red">¥<?=$order->order_money?></em></p>
            </div>
            <a href="javascript:void(0);" class="oh_btn">再次购买</a>
        </div>
        <a class="order_shopBar"><i class="iconfont">&#xe854;</i><em>我的小店</em></a>
        <? foreach ($goods as $g) : ?>
        <div class="order_item">
            <img class="image" src="<?=$g->goods_image?>">
            <div class="oi_content">
                <a><?=$g->goods_name?></a>
                <p><span class="count"><?=$g->quantity?> 件</span></p>
            </div>
        </div>
        <? endforeach;?>
    </div>
<? endforeach;?>

<? if($orders['total']==0) : ?>
    <div class="weui-msg">
        <div class="weui-msg__icon-area"><i class="weui-icon-warn weui-icon_msg-primary"></i></div>
        <div class="weui-msg__text-area">
            <h2 class="weui-msg__title">没有任何记录。。</h2>
            <p class="weui-msg__desc"></p>
        </div>
    </div>
<? endif;?>
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