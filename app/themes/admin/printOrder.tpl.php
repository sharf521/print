<?php require 'header.php'; ?>
<?php if ($this->func == 'checkOrder') : ?>
    <div class="main_title">
        <span>订单审核</span>
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
            <th>ID</th>
            <th>定做要求</th>
            <th>价格</th>
            <th>外联厂家</th>
            <th>本成价</th>
            <th>添加时间</th>
            <th>状态</th>
            <th></th>
        </tr>
        <?
        $printOrder = new \App\Model\PrintOrder();
        foreach ($orderList['list'] as $order) {
            $item = $printOrder->find($order['id']);
            ?>
            <tr>
                <td><?= $item->id ?></td>
                <td class="fl"><?= nl2br($item->remark) ?></td>
                <td><?= $item->money ?></td>
                <td><?= $item->company ?></td>
                <td><?= $item->company_money ?></td>
                <td><?= $item->created_at ?></td>
                <td><?= $item->getLinkPageName('check_status', $item->status) ?></td>
                <td>
                    <? if ($item->status != 2) : ?>
                        <a href="<?= url("printTask/checkOrder/?id={$item->id}&status=2&page={$_GET['page']}") ?>"
                           onclick="return confirm('确定要操作吗？')">审核通过</a>&nbsp;&nbsp;
                        <a href="<?= url("printTask/checkOrder/?id={$item->id}&status=3&page={$_GET['page']}") ?>"
                           onclick="return confirm('确定要操作吗？')">审核不通过</a>&nbsp;&nbsp;

                        <a href="<?= url("printTask/checkOrderDo/?id={$item->id}&page={$_GET['page']}") ?>">编辑</a>
                    <? endif ?>
                </td>
            </tr>
        <? } ?>
    </table>
    <? if (empty($orderList['total'])) {
        echo "无记录！";
    } else {
        echo $orderList['page'];
    } ?>
    <? elseif ($this->func=='checkOrderDo') : ?>
    <div class="main_content">
        <h3>审核订单</h3>
        <form method="post">
            <input type="hidden" name="id" value="<?=$order->id?>">
            <input type="hidden" name="page" value="<?=$_GET['page']?>">
            <table class="table_from">
                <tr><td>定做要求：</td><td><textarea name="remark" cols="40" rows="4"><?=$order->remark?></textarea></td></tr>
                <tr><td>价格：</td><td><input type="text" name="money" value="<?=$order->money?>"></td></tr>
                <tr><td>外联厂家：</td><td><?=$print_company?></td></tr>
                <tr><td>厂家本成价：</td><td><input type="text" name="company_money" value="<?=$order->company_money?>"></td></tr>
                <tr><td></td><td>
                        <input type="submit" value="保存">
                        <input type="button" value="返回" onclick="window.location='<?=url("printTask/checkOrder/?page={$_GET['page']}")?>'"></td></tr>
            </table>
        </form>
    </div>
<? endif ?>
<?php require 'footer.php'; ?>
