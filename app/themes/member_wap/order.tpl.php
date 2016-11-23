<?php require 'header.php';?>
    <div class="m_header">
        <a class="m_header_l" href="<?=url('')?>"><i class="iconfont">&#xe604;</i></a>
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
    <div class="order_box  " data_dealid="17830227709"><div class="order_head"> <a ordertype="22" target_href="//wqs.jd.com/order/n_detail_v2.shtml?deal_id=17830227709&amp;bid=&amp;deal_refer_uin=0&amp;new=1&amp;jddeal=1&amp;isoldpin=0#wechat_redirect" page="2" sendpay="00000000100000000000000002001000030000100000000000600000000001010000000000000000000000000000000100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000010000000111" biztype="0" class="oh_content"> <p class="pState"><span>状<i></i>态：</span><em class="co_blue">已签收</em></p>  <p><span>总<i></i>价：</span><em class="co_red">¥34.40</em></p> </a>        <a href="javascript:void(0);" class="oh_btn bg_6" commlist="1076926238,,1,1076926238,1,0,0$1028194556,,1,1028194556,1,0,0" tag="repeatBuy" venderid="40964" shopid="38566" commoditytype="0" biztype="0" ordertype="22" sendpay="00000000100000000000000002001000030000100000000000600000000001010000000000000000000000000000000100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000010000000111">再次购买</a>    <a href="javascript:;" class="oh_btn bg_2" id="evaluate_17830227709" dealid="17830227709" style="display:none;" page="2" ptag="7129.2.2">去评价</a> <a href="javascript:;" class="oh_btn bg_1" id="share_17830227709" style="display:none;" dealid="17830227709" page="2">分享赚京豆</a>    </div><a target_href="//wq.jd.com/mshop/gethomepage?venderId=40964" class="order_shopBar" page="2"><em> 博库网旗舰店 </em></a><div class="order_item">          <img class="image" src="//img10.360buyimg.com/n4/g13/m07/13/04/rbehullicpiiaaaaaaktem6ia4aaainmabjfnsaapos176.jpg" width="50" height="50" onload="reportSpeedTime();" target_href="//wqitem.jd.com/item/view?sku=1076926238&amp;bid=" page="2" ks_mark="y"> <a ordertype="22" target_href="//wqs.jd.com/order/n_detail_v2.shtml?deal_id=17830227709&amp;bid=&amp;deal_refer_uin=0&amp;new=1&amp;jddeal=1&amp;isoldpin=0#wechat_redirect" page="2" sendpay="00000000100000000000000002001000030000100000000000600000000001010000000000000000000000000000000100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000010000000111" biztype="0" class="oi_content"> <div>  <span id="skuTitle">咔嚓咔嚓挖掘机/蒲公英汽车绘本系列</span></div> <p><span class="count">1 件</span><span class="price"></span></p> </a> </div><div class="order_item">          <img class="image" src="//img10.360buyimg.com/n4/g15/m07/00/0f/rbehwlhqc24iaaaaaav5a_fvgtiaabomqopz-eabxkb153.jpg" width="50" height="50" onload="reportSpeedTime();" target_href="//wqitem.jd.com/item/view?sku=1028194556&amp;bid=" page="2" ks_mark="y"> <a ordertype="22" target_href="//wqs.jd.com/order/n_detail_v2.shtml?deal_id=17830227709&amp;bid=&amp;deal_refer_uin=0&amp;new=1&amp;jddeal=1&amp;isoldpin=0#wechat_redirect" page="2" sendpay="00000000100000000000000002001000030000100000000000600000000001010000000000000000000000000000000100000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000010000000111" biztype="0" class="oi_content"> <div>  <span id="skuTitle">自私的皮球(我们的日子为什么是这样过的)</span></div> <p><span class="count">1 件</span><span class="price"></span></p> </a> </div></div>
    <div class="order_list">
        <? foreach ($orders['list'] as $order) :
            $goods=$order->OrderGoods();
            ?>
            <dl>
                <dt class="order_list_header"></dt>
                <dd class="order_list_store"></dd>
                <dd>
                    <ul>
                        <li>
                            <a href="<?=url("/order/detail/?id={$goods->id}")?>">
              <span class="imgspan">
                    <img src="/themes/images/blank.gif" data-echo="<?=$goods->image_url?>">
                </span>
                                <div class="info">
                                    <p class="cd_title"><?=$goods->name?></p>
                                    <p class="cd_money">
                                        <span>￥</span>
                                        <var><?=$goods->price?></var>
                                    </p>
                                    <p class="cd_sales">库存：<?=$goods->stock_count?></p>
                                </div>
                            </a>
                        </li>
                    </ul>
                </dd>

                <div class="operat"><i class="iconfont" onclick="showMenu(<?=$goods->id?>)">&#xe73a;</i></div>
            </dl>
        <? endforeach;?>
    </div>
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