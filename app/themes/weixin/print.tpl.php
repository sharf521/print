<?php require 'header.php';?>
<? if($this->func=='taskAdd') : ?>
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
    <div class="header_tit">我的订单 <a class="header_right" href="<?=url('weixin/taskAdd')?>">我要下单</a></div>
        <br>
        <? foreach ($task['list'] as $row) : ?>
            <div class="div_box clearFix taskList">
                <? if($row->paymoney>0) : ?>
                <a href="<?=url("weixin/orderShow/?task_id={$row->id}")?>">
                <? endif?>
                    <div class="remark_title clearFix">
                        <span class="type"><?= $row->print_type ?></span>
                        <span class="time"><?= $row->created_at ?></span>
                    </div>
                    <div class="remark clearFix"><?= nl2br($row->remark) ?></div>
                <? if($row->paymoney>0) : ?>
                </a>
                <? endif?>
                <div class="taskBot clearFix">
                    <? if($row->money>0) : ?>
                    <span class="money1">￥<?= $row->money ?></span>
                    <?
                    endif;
                    if ($row->status == 3) {
                        ?>
                        <a class="but_pay" href="<?= url("weixin/orderPay/?task_id={$row->id}&page={$_GET['page']}") ?>">付款</a>
                        <?
                    }else{
                        echo '<span class="status">'.$row->getLinkPageName('print_status',$row->status).'</span>';
                    }
                    ?></div>
            </div>
        <? endforeach; ?>
    <? if(empty($task['total'])){echo "<div class='alert-warning'>没有找到匹配的记录！</div>";}else{echo $task['page'];}?>
    <? elseif ($this->func=='orderShow') : ?>
        <!-- 待支付 -->
        <div class="header_tit">订单详情：<?=$task->getLinkPageName('print_status',$task->status)?></div><br>
    <div class="div_box clearFix taskList">
        <div class="remark_title clearFix">
            <span class="type"><?= $task->print_type ?></span>
            <span class="time"><?= $task->created_at ?></span>
        </div>
        <div class="remark clearFix"><?= nl2br($task->remark) ?></div>
    </div>
        <?php if ($task->paymoney > 0) : ?>
            <div class="div_box">
                <table class="table_box">
                    <tr><td >支付金额：</td><td><?=$task->paymoney?></td></tr>
                    <tr><td >订单号：</td><td><?=$task->out_trade_no?></td></tr>
                    <tr><td >支付时间：</td><td><?=date('Y-m-d H:i:s',$task->paytime)?></td></tr>
                    <tr><td >收货人：</td><td><?=$task->shipping_name?></td></tr>
                    <tr><td >联系电话：</td><td><?=$task->shipping_tel?></td></tr>
                    <tr><td >收货地址：</td><td><?=$task->shipping_address?></td></tr>
                </table>
            </div>
        <?php
        endif;
        if ($task->status >=5) : ?>
            <div class="div_box">
                <table class="table_box">
                    <tr><td >快递公司：</td><td><?=$task->shipping_company?></td></tr>
                    <tr><td >快递单号：</td><td><?=$task->shipping_no?></td></tr>
                    <tr><td >发货时间：</td><td><? if($task->shipping_time!=0){
                                echo date('Y-m-d H:i:s',$task->shipping_time);
                            }?></td></tr>
                </table>
            </div>
        <? endif ?>
    </div>
    <? endif;?>
<?php require 'footer.php';?>