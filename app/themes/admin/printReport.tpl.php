<?php require 'header.php'; ?>
<?php if ($this->func == 'index') : ?>
    <div class="main_title">
        <span>工单管理</span>
    </div>
    <form method="get">
        <div class="search">
            关键字：<input name="q" value="<?=$_GET['q']?>">
            外联厂家：<?=$print_company?>
            时间：<input  name="starttime" type="text" value="<?=$_GET['starttime']?>" onClick="javascript:WdatePicker();" class="Wdate">
            到
            <input  name="endtime" type="text" value="<?=$_GET['endtime']?>" onClick="javascript:WdatePicker();" class="Wdate">
            <input type="submit" class="but2" value="查询"/>
        </div>
    </form>
    <table class="table">
        <tr>
            <th>订单ID</th>
            <th>类型</th>
            <th>客户名称</th>
            <th>付款时间</th>
            <th>接待人</th>
            <th>邀请人</th>
            <th>快递公司</th>
            <th>快递单号</th>
            <th>快递金额</th>
            <th>订做要求</th>
            <th>价格</th>
            <th>外联厂商</th>
            <th>外协金额</th>
            <th>状态</th>
        </tr>
        <?
        $user = new \App\Model\User();
        $invite = new \App\Model\User();
        $task_id=0;
        foreach ($orderList['list'] as $item) {
            $user=$user->find($item->user_id);
            $reply=$user->find($item->reply_uid);
            $invite=$invite->find($user->invite_userid);
            ?>
            <tr>
                <? if($task_id!=$item->id) : ?>
                    <td><a href="<?=url('printTask/show/?task_id='.$item->id)?>"><?= $item->id ?></a></td>
                    <td><?= $item->print_type ?></td>
                    <td><?= $user->nickname ?></td>
                    <td><?= $item->paytime ?></td>
                    <td><?= $reply->nickname ?></td>
                    <td><?=$invite->nickname?></td>
                    <td><?= $item->shipping_company ?></td>
                    <td><?= $item->shipping_no ?></td>
                    <td><?= $item->shipping_fee ?></td>
                <? else : ?>
                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                <? endif?>

                <td class="fl" style="width: 300px;"><?= nl2br($item->order_remark) ?></td>
                <td><?= $item->order_money ?></td>
                <td><?= $item->company ?></td>
                <td><?= $item->company_money ?></td>
                <td><?= $user->getLinkPageName('check_status', $item->order_status) ?></td>
            </tr>
        <?
            $task_id=$item->id;
        } ?>
    </table>
    <? if (empty($orderList['total'])) {
        echo "无记录！";
    } else {
        echo $orderList['page'];
    } ?>
<? endif ?>
<?php require 'footer.php'; ?>
