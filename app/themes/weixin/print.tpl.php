<?php require 'header.php';?>
<? if($this->func=='orderAdd') : ?>
    <div class="header_tit">我要下单</div>
    <div class="container">
        <form method="post">
            <table class="table_from">
                <tr><td>类型：</td><td><?=$print_type?></td></tr>
                <tr><td>要求：</td><td><textarea name="remark" rows="5"></textarea></td></tr>
                <tr><td>电话：</td><td><input type="text" name="tel"></td></tr>
            </table>
            <input type="submit" value="提 交" class="submit">
        </form>
    </div>
    <? elseif ($this->func=='orderList') :  ?>
    <div class="header_tit">我的订单</div>
    <!--<ul class="orderList">
        <? foreach ($task['list'] as $row) : ?>
            <li class="clearFix">
                <div class="div_status">
                    状态：<span class="status1"><?= $row->getLinkPageName('print_status', $row->status) ?></span><br>
                    总价：<span class="money1">￥<?= $row->money ?></span>
                </div>
                <div class="div_operat">
                    <?
                    if ($row->status == 3) {
                        ?>
                        <a class="but1" href="<?= url("weixin/orderShow/?task_id={$row->id}&page={$_GET['page']}") ?>">支付</a>
                        <?
                    }
                    ?>
                </div>
                <div class="clear"></div>
                <div class="order clearFix">
                    <?
                    $order=$row->PrintOrder();
                    foreach ($order as $o) : ?>
                        <div class="remark_title">
                            <span class="type"><?= $row->print_type ?></span>
                            <span class="time"><?= $row->created_at ?></span>
                        </div>
                        <div class="remark"><?= nl2br($row->remark) ?></div>
                    <? endforeach; ?>
                </div>
            </li>
        <? endforeach; ?>
    </ul>-->
    <table class="table">
        <tr class="bt">
            <th>类型</th>
            <th>要求</th>
            <th>时间</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        <?
        foreach($task['list'] as $row)
        {
            ?>
            <tr>
                <td><?=$row->print_type?></td>
                <td><?=nl2br($row->remark)?></td>
                <td><?=$row->created_at?></td>
                <td><?=$row->getLinkPageName('print_status',$row->status)?></td>
                <td>
                    <a href="<?=url("weixin/orderShow/?task_id={$row->id}&page={$_GET['page']}")?>">详情</a>
                    <?
                    if($row->status==3){
                        ?>
                        <a class="but1" href="<?=url("weixin/orderShow/?task_id={$row->id}&page={$_GET['page']}")?>">支付</a>
                        <?
                    }
                    ?>
                </td>
            </tr>
        <? }?>
    </table>
    <? if(empty($task['total'])){echo "无记录！";}else{echo $task['page'];}?>
    <? elseif ($this->func=='orderShow') : ?>
    <div class="header_tit">我的订单</div>
    <div class="container">
        <table class="table">
            <tr><th>定做要求</th><th>价格</th><th>添加时间</th></tr>
            <?
            $money=0;
            foreach ($order as $item){
                $money=math($money,$item->money,'+',2);
                ?>
                <tr>
                    <td><?= nl2br($item->remark) ?></td>
                    <td><?=(float)$item->money?></td>
                    <td><?= $item->created_at ?></td>
                </tr>
                <?
            }
            ?>
        </table>
        <form method="post">
            <table class="table_from">
                <tr><td>总计：</td><td>￥<?=$money?></td></tr>
            </table>
            <input type="hidden" name="money" value="<?=$money?>">
            <input type="text" name="address" onclick="wxAddress()">
            <input type="button" value="确定支付" onclick="wxpay()" class="submit">
        </form>
    </div>

    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" charset="utf-8">
        wx.config(<?=$config?>);
        function wxpay() {
            wx.ready(function(){
                wx.chooseWXPay({
                    timestamp: '<?=$pay['timestamp']?>',
                    nonceStr: '<?=$pay['nonceStr']?>',
                    package: '<?=$pay['package']?>',
                    signType: 'MD5',
                    paySign: '<?=$pay['paySign']?>',
                    success: function (res) {
                        alert('支付成功！');
                        window.location='/index.php/weixin/orderList/?'+ res;
                    }
                });
            });
        }
        function wxAddress() {
            wx.ready(function(){
                wx.openAddress({
                    trigger: function (res) {
                        alert('用户开始拉出地址');
                    },
                    success: function (res) {
                        alert('用户成功拉出地址');
                        alert(JSON.stringify(res));
                    },
                    cancel: function (res) {
                        alert('用户取消拉出地址');
                    },
                    fail: function (res) {
                        alert(JSON.stringify(res));
                    }
                });
            });
        }
    </script>

    <? endif;?>
<?php require 'footer.php';?>