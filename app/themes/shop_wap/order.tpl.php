<?php require 'header.php';?>
    <div class="m_header">
        <a class="m_header_l" href="javascript:history.go(-1)"><i class="iconfont">&#xe604;</i></a>
        <a class="m_header_r" href=""></a>
        <h1>我的订单</h1>
    </div>
<div class="order_address margin_header">
    <h4>收货地址</h4>
    <a href="<?=url('/member/address/?redirect_url='.$this->self_url)?>">
        <? if($address->is_exist) : ?>
        <p><?=$address->region_name?> <?=$address->address?></p>
        <p><strong><?=$address->name?></strong><?=$address->phone?></p>
        <? else : ?>
            <div class="noadres"><p>暂无收货地址！</p></div>
        <? endif;?>
    </a>
</div>

<form method="post">
    <?  foreach ($result_carts as $i=>$carts) : ?>
        <div class="order_box">
            <a class="order_shopBar"><i class="iconfont">&#xe854;</i><em>我的小店<?=$cart->seller_id?></em></a>
            <? foreach($carts as $cart): ?>
                <div class="order_item clearFix">
                    <img class="image" src="<?=$cart->goods_image?>">
                    <div class="oi_content">
                        <a><?=$cart->goods_name?></a>
                        <p><?
                            if($cart->spec_1!=''){
                                echo "<span class='spec'>{$cart->spec_1}</span>";
                            }
                            if($cart->spec_2!=''){
                                echo "<span class='spec'>{$cart->spec_2}</span>";
                            }
                            ?>
                            <span class="count">数量：<?=$cart->quantity?></span></p>
                    </div>
                </div>
            <? endforeach;?>
            <textarea name="buyer_remark" class="weui-textarea" style="background-color: #efefef; margin-top: 8px;font-size: 14px;" placeholder="请输入文本" rows="2">订单备注,选填.</textarea>
        </div>
    <? endforeach;?>
    <div class="weui-btn-area">
        <input class="weui-btn weui-btn_primary" type="submit" value="提交订单">
    </div>
</form>
<?php require 'footer.php';?>