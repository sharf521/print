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
        <div class="header_tit">添加收货地址：</div>
            <table class="table_from">
                <tr><td>收货人：</td><td><input type="text" id="name"></td></tr>
                <tr><td>电话：</td><td><input type="text" id="tel"></td></tr>
                <tr><td>地址：</td><td><input type="text" id="address"></td></tr>
            </table>
        <div align="center"> <input type="button"  value="选择地址" id="btnAddress" class="but1" style="width: 80%"></div>
       <br>


        <table width="100%">
            <tr><td style="font-size: 3rem">总计：<span style=" color: #ff8000">￥<?=$money?></span></td><td align="right"><input type="button" value="确定支付" id="butPay" class="submit"></td></tr>
        </table>
    </div>


    <script src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" charset="utf-8">
        wx.config(<?=$config?>);
        wx.ready(function () {
            $("#butPay").click(function () {
                wx.chooseWXPay({
                    timestamp: '<?=$pay['timestamp']?>',
                    nonceStr: '<?=$pay['nonceStr']?>',
                    package: '<?=$pay['package']?>',
                    signType: 'MD5',
                    paySign: '<?=$pay['paySign']?>',
                    success: function (res) {
                        alert('支付成功！');
                        window.location = '/index.php/weixin/orderList/';
                    }
                });
            });
            $('#btnAddress').click(function () {
                wx.openAddress({
                    success: function (res) {
                        //JSON.stringify(res);
                        $('#name').val(res.userName);
                        $('#tel').val(res.telNumber);
                        $('#address').val(res.provinceName + res.cityName + res.countryName + res.detailInfo);
                    },
                    cancel: function (res) {
                        alert('用户取消拉出地址');
                    },
                    fail: function (res) {
                        alert(JSON.stringify(res));
                    }
                });
            });
        })
    </script>

    <? endif;?>
<?php require 'footer.php';?>