<?php require 'header.php';?>
<?php if($this->func=='index') : ?>
    <div class="main_title">
        <span>列单管理</span>列表
    </div>
    <form method="get">
        <div class="search">
            类型：<?=$print_type?>
            用户名：<input type="text" name="username" size="5" value="<?= $_GET['username'] ?>"/>
            昵称：<input type="text" name="nickname" size="5" value="<?= $_GET['nickname'] ?>"/>
            <input type="submit" class="but2" value="查询"/>
        </div>
    </form>
    <table class="table">
        <tr class="bt">
            <th>id</th>
            <th>UID/昵称</th>
            <th>类型</th>
            <th>要求</th>
            <th>电话</th>
            <th>添加时间</th>
            <th>接单人</th>
            <th>接单时间</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        <?
        foreach($list as $row)
        {
            ?>
            <tr>
                <td><?=$row->id?></td>
                <td><?=$row->User()->id?>/<?=$row->User()->UserWx()->nickname?></td>
                <td><?=$row->print_type?></td>
                <td><?=nl2br($row->remark)?></td>
                <td><?=$row->tel?></td>
                <td><?=$row->created_at?></td>
                <td><?=$row->UserReply()->UserWx()->nickname?></td>
                <td><? if($row->reply_time!=0){
                        echo date('Y-m-d H:i:s',$row->reply_time);
                    }?></td>
                <td><?=$row->getLinkPageName('print_status',$row->status)?></td>
                <td><a href="<?=url("printTask/show/?task_id={$row->id}&page={$_GET['page']}")?>">详情</a>
                </td>
            </tr>
        <? }?>
    </table>
    <? if(empty($total)){echo "无记录！";}else{echo $page;}?>
<?php
elseif ($this->func=='show') : ?>
    <div class="main_title">
        <span>列单管理</span>列表
        <a class="but1" href="<?=url("printTask/index/?paeg={$_GET['page']}")?>">返回</a>
    </div>
    <? if(!empty($order)) :?>
    <div class="main_content">
        <form method="post">
            <table class="table">
                <tr><th>ID</th><th>定做要求</th><th>价格</th><th>外联厂家</th><th>本成价</th><th>添加时间</th><th></th></tr>
                <?
                $linkPage=new \App\Model\LinkPage();
                foreach ($order as $item){
                    ?>
                    <tr>
                        <td><input type="hidden" name="id[]" value="<?= $item->id ?>"><?= $item->id ?></td>
                        <td><textarea name="remark[]"><?= $item->remark ?></textarea></td>
                        <td><input type="text" name="money[]" value="<?= $item->money ?>"></td>
                        <td><?= $linkPage->echoLink('print_company', $item->company, array('name' => 'company[]')) ?></td>
                        <td><input type="text" name="company_money[]" value="<?= $item->company_money ?>"></td>
                        <td><?= $item->created_at ?></td>
                        <td>
                            <a href="<?= url("printTask/orderDel/?id={$item->id}&page={$_GET['page']}&task_id={$task->id}") ?>"
                               onclick="return confirm('确定要删除吗？')">删除</a></td>
                    </tr>
                    <?
                }
                ?>
                <tr><td colspan="7">
                    <input type="hidden" value="orderEdit" name="act">
                    <input type="submit" value=" 保 存 "></td></tr>
            </table>
        </form>
    </div>
    <? endif?>

    <div class="main_content">
        <h3>添加订单</h3>
        <form method="post">
            <input type="hidden" name="act" value="orderAdd">
            <table class="table_from">
                <tr><td>定做要求：</td><td><textarea name="remark" cols="40" rows="4"></textarea></td></tr>
                <tr><td>价格：</td><td><input type="text" name="money"></td></tr>
                <tr><td>外联厂家：</td><td><?=$print_company?></td></tr>
                <tr><td>厂家本成价：</td><td><input type="text" name="company_money"></td></tr>
                <tr><td></td><td>
                        <input type="submit" value="添加">
                        <input type="button" value="返回" onclick="window.location='<?=url("printTask/index/?page={$_GET['page']}")?>'"></td></tr>
            </table>
        </form>
    </div>

<?php endif;
require 'footer.php';