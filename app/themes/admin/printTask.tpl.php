<?php require 'header.php';?>
<?php if($this->func=='index')  : ?>
    <div class="main_title">
        <span>列单管理</span>列表
    </div>
    <form method="get">
        <div class="search">
            类型：<?=$print_type?>
            用户名：<input type="text" name="username" size="5" value="<?= $_GET['username'] ?>"/>
            昵称：<input type="text" name="nickname" size="5" value="<?= $_GET['nickname'] ?>"/>
            时间：<input  name="starttime" type="text" value="<?=$_GET['starttime']?>" onClick="javascript:WdatePicker();" class="Wdate">
            到
            <input  name="endtime" type="text" value="<?=$_GET['endtime']?>" onClick="javascript:WdatePicker();" class="Wdate">
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
            <th>时间</th>
            <th>接单人</th>
            <th>支付金额</th>
            <th>支付流水号</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        <?
        foreach($list as $row)
        {
            $time='';
            if($row->reply_time!=0){
                $time.= '接单时间：'.date('Y-m-d H:i:s',$row->reply_time)."\r\n";
            }
            if($row->paytime!=0){
                $time.= '支付时间：'.date('Y-m-d H:i:s',$row->paytime);
            }
            ?>
            <tr>
                <td><?=$row->id?></td>
                <td><?=$row->User()->id?>/<?=$row->User()->UserWx()->nickname?></td>
                <td><?=$row->print_type?></td>
                <td><?=nl2br($row->remark)?></td>
                <td><?=$row->tel?></td>
                <td title="<?=$time?>"><?=substr($row->created_at,2,-3)?></td>
                <td><?=$row->UserReply()->UserWx()->nickname?></td>
                <td><?=(float)$row->paymoney?></td>
                <td><?=$row->out_trade_no?></td>
                <td><?=$row->getLinkPageName('print_status',$row->status)?></td>
                <td>
                    <a href="<?=url("printTask/show/?task_id={$row->id}&page={$_GET['page']}")?>">详情</a>
                    <? if($row->status <4 ) : ?>
                    <a href="<?=url("printTask/taskDel/?task_id={$row->id}&page={$_GET['page']}")?>"
                       onclick="return confirm('确定要删除吗？')">删除</a>
                    <? endif ?>
                </td>
            </tr>
        <? }?>
    </table>
    <? if(empty($total)){echo "无记录！";}else{echo $page;}?>

<?php
elseif ($this->func=='taskAdd') :
?>
    <div class="main_title">
        <span>列单管理</span>
    </div>
    <div class="main_content">
        <h3><?=$_GET['nickname']?> 添加订单</h3>
        <form method="post">
            <table class="table_from">
                <tr><td>类型：</td><td><?=$print_type?></td></tr>
                <tr><td>要求：</td><td><textarea name="remark" rows="5" cols="50"></textarea></td></tr>
                <tr><td>电话：</td><td><input type="text" name="tel"></td></tr>
                <tr><td></td><td>
                        <input type="submit" value="添加">
                        <input type="button" value="返回" onclick="window.history.go(-1)"></td></tr>
            </table>
        </form>
    </div>
<?
elseif ($this->func=='show') : ?>
    <div class="main_title">
        <span>列单管理</span>列表
        <a class="but1" href="<?=url("printTask/index/?paeg={$_GET['page']}")?>">返回</a>
    </div>
    <div class="main_content">
        <table class="table">
            <tr><th>基本信息</th><th>支付</th><th>物流</th></tr>
            <tr>
                <td>
                    <form method="post" action="<?=url('printTask/editTask')?>">
                        <input type="hidden" name="task_id" value="<?=$task->id?>">
                        <input type="hidden" name="page" value="<?=$_GET['page']?>">
                        <table class="table_from">
                            <tr><td>用户：</td><td>
                                    <?=$task->user_id?>/<?=$task->User()->UserWx()->nickname?>
                                    <img src="<?=substr($task->User()->UserWx()->headimgurl,0,-1)?>64" width="50"></td></tr>
                            <tr><td>类型：</td><td><?=$task->print_type?></td></tr>
                            <tr><td>要求：</td><td><textarea name="remark" cols="40" rows="5"><?=$task->remark?></textarea></td></tr>
                            <tr><td>电话：</td><td><?=$task->tel?></td></tr>
                            <tr><td>添加时间：</td><td><?=$task->created_at?></td></tr>
                            <tr><td>价格：</td><td><?=$task->money?></td></tr>
                            <tr><td>状态：</td><td><?=$task->getLinkPageName('print_status',$task->status)?></td></tr>
                            <tr><td></td><td><input type="submit" value="保存"></td></tr>
                        </table>
                    </form>
                </td>
                <td valign="top">
                    <table class="table_from">
                        <tr><td style="width: 150px;">支付金额：</td><td><?=$task->paymoney?></td></tr>
                        <tr><td style="width: 150px;">流水号：</td><td><?=$task->out_trade_no?></td></tr>
                        <tr><td style="width: 150px;">支付时间：</td><td>
                                <? if ($task->paytime != 0) {
                                    echo date('Y-m-d H:i:s', $task->paytime);
                                } ?>
                            </td></tr>
                        <tr><td style="width: 150px;">收货人：</td><td><?=$task->shipping_name?></td></tr>
                        <tr><td style="width: 150px;">联系电话：</td><td><?=$task->shipping_tel?></td></tr>
                        <tr><td style="width: 150px;">收货地址：</td><td><?=$task->shipping_address?></td></tr>
                    </table>
                </td>
                <td valign="top">
                    <form method="post" action="<?=url('printTask/editShipping')?>">
                        <input type="hidden" name="task_id" value="<?=$task->id?>">
                        <input type="hidden" name="page" value="<?=$_GET['page']?>">
                        <table class="table_from">
                            <tr><td>快递公司：</td><td><?=$task->shipping_company?></td></tr>
                            <tr><td>快递单号：</td><td><input type="text" name="shipping_no" value="<?=$task->shipping_no?>"></td></tr>
                            <tr><td>快递费用：</td><td><input type="text" name="shipping_fee" value="<?=$task->shipping_fee?>"></td></tr>
                            <tr><td>发货时间：</td><td><? if($task->shipping_time!=0){
                                        echo date('Y-m-d H:i:s',$task->shipping_time);
                                    }?></td></tr>
                            <tr><td></td><td>
                                    <input type="submit" value="保存"></td></tr>
                        </table>
                    </form>
                </td>
            </tr>
        </table>
    </div>
    <? if(!empty($order)) :?>
        <div class="main_content">
        <form method="post" action="<?=url('printTask/orderEdit')?>">
            <input type="hidden" name="task_id" value="<?=$task->id?>">
            <input type="hidden" name="page" value="<?=$_GET['page']?>">
            <table class="table">
                <tr><th>ID</th><th>定做要求</th><th>价格</th><th>外联厂家</th><th>成本价</th><th>添加时间</th><th></th></tr>
                <?
                $linkPage=new \App\Model\LinkPage();
                foreach ($order as $item){
                    ?>
                    <tr>
                        <td><input type="hidden" name="id[]" value="<?= $item->id ?>"><?= $item->id ?></td>
                        <td><textarea name="remark[]" rows="4" cols="50"><?= $item->remark ?></textarea></td>
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
                    <input type="submit" value=" 保 存 "></td></tr>
            </table>
        </form>
    </div>
    <? endif?>

    <div class="main_content">
        <h3>添加订单</h3>
        <form method="post"action="<?=url('printTask/orderAdd')?>">
            <input type="hidden" name="task_id" value="<?=$task->id?>">
            <input type="hidden" name="page" value="<?=$_GET['page']?>">
            <table class="table_from">
                <tr><td>定做要求：</td><td><textarea name="remark" cols="45" rows="5"></textarea></td></tr>
                <tr><td>价格：</td><td><input type="text" name="money"></td></tr>
                <tr><td>外联厂家：</td><td><?=$print_company?></td></tr>
                <tr><td>厂家成本价：</td><td><input type="text" name="company_money"></td></tr>
                <tr><td></td><td>
                        <input type="submit" value="添加">
                        <input type="button" value="返回" onclick="window.location='<?=url("printTask/index/?page={$_GET['page']}")?>'"></td></tr>
            </table>
        </form>
    </div>
<?php endif;
require 'footer.php';