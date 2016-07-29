<?php
require 'header.php';
if ($this->func == 'index') {
    ?>
    <div class="main_title">
        <span>管理</span>列表


        <?= $this->anchor('algorithm/listByDays', '结算', 'class="but1"') ?>
        <?= $this->anchor('algorithm/getLog', '获取', 'class="but1"') ?>
    </div>
    <form method="get">
        <div class="search">
            金额：<input type="text" size="10" name="money" value="<?= $_GET['money'] ?>">&nbsp;&nbsp;
            用户ID：<input type="text" size="10" name="user_id" value="<?= $_GET['user_id'] ?>">&nbsp;&nbsp;
            时间：<input type="text" size="10" name="startdate" value="<?= $_GET['startdate'] ?>" class="Wdate"
                      onclick="javascript:WdatePicker();">-
            <input type="text" size="10" name="enddate" value="<?= $_GET['enddate'] ?>" class="Wdate"
                   onclick="javascript:WdatePicker();">
            <input type="submit" class="but2" value="查询"/>
        </div>
    </form>
    <table class="table">
        <tr class="bt">
            <th>ID</th>
            <th>用户ID</th>
            <th>金额</th>
            <th>时间</th>
            <th>状态</th>
        </tr>
        <?php
        $arr_status = array('未处理', '己处理');
        foreach ($result['list'] as $row) {
            ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['user_id'] ?></td>
                <td><?= (float)$row['money'] ?></td>
                <td><?= $row['addtime'] ?></td>
                <td><?= $arr_status[$row["status"]] ?></td>
            </tr>
        <? } ?>
    </table>
    <?php if (empty($result['total'])) {
        echo "无记录！";
    } else {
        echo $result['page'];
    } ?>
    <hr>
    总计：<?php echo $result['money_total']; ?>
    <?
}elseif($this->func=='listByDays'){
    ?>
    <div class="main_title">
        <span>管理</span>列表
        <?= $this->anchor('algorithm/index', '列表', 'class="but1"') ?>
    </div>
    <form method="get">
        <div class="search">
            时间：<input type="text" size="10" name="startdate" value="<?= $_GET['startdate'] ?>" class="Wdate"
                      onclick="javascript:WdatePicker();">-
            <input type="text" size="10" name="enddate" value="<?= $_GET['enddate'] ?>" class="Wdate"
                   onclick="javascript:WdatePicker();">
            <input type="submit" class="but2" value="查询"/>
        </div>
    </form>
    <table class="table">
        <tr class="bt">
            <th>时间</th>
            <th>结算积分</th>
            <th>状态</th>
            <th></th>
        </tr>
        <?php
        $arr_status = array('未处理', '己处理');
        foreach ($result as $row) {
            ?>
            <tr>
                <td><?= $row['date'] ?></td>
                <td><?= (float)$row['money'] ?></td>
                <td><?= $arr_status[$row["status"]] ?></td>
                <td>
                    <?php
                    $date=$row['date'];
                    $date1=date('Y-m-d',strtotime($date)+3600*24);
                    echo $this->anchor('algorithm/index?startdate='.$date.'&enddate='.$date1, '查看');
                    echo ' | ';
                    if($row['status']==0 && $row['date']<=date('Y-m-d')){
                        echo "<a href=\"javascript:algorithmSend('{$date}')\">结算</a>";
                    }
                    ?>
                </td>
            </tr>
        <? } ?>
    </table>
    <?php if (empty($result)) {
        echo "无记录！";
    } ?>
<?php
}
require 'footer.php';