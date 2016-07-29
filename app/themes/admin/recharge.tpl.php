<?php require 'header.php'; ?>
<? if ($this->func == 'index') : ?>
    <div class="main_title">
        <span>管理</span>列表
    </div>
    <div class="search">
        <form>
            充值类型：<select name="type">
                <option value=""<? if($_GET['type']==""){?> selected="selected"<? }?>>请选择</option>
                <option value="1"<? if($_GET['type']=="1"){?> selected="selected"<? }?>>在线充值</option>
                <option value="2"<? if($_GET['type']=="2"){?> selected="selected"<? }?>>线下充值</option>
            </select>
            <? if($this->user['type_id']==2){?>
                归属分站：
                <select name="subsite_id">
                    <option value=""<? if($_GET['subsite_id']==""){?> selected="selected"<? }?>>请选择</option>
                    <? foreach($subsite as $key_se=>$row_se){?>
                        <option value="<?=$key_se?>"<? if($_GET['subsite_id']==$key_se){?> selected="selected"<? }?>><?=$row_se?></option>
                    <? }?>
                </select>
            <? }?>
            状态：<select name="status">
                <option value=""<? if($_GET['status']==""){?> selected="selected"<? }?>>请选择</option>
                <option value="1"<? if($_GET['status']=="1"){?> selected="selected"<? }?>>充值成功</option>
                <option value="2"<? if($_GET['status']=="2"){?> selected="selected"<? }?>>审核未通过</option>
                <option value="0"<? if($_GET['status']=="0"){?> selected="selected"<? }?>>待审核</option>
            </select>
            用户名：<input type="text" name="username" value="<?=$_GET['username']?>"/>
            充值时间：<input type="text" name="starttime" value="<?=$_GET['starttime']?>" class="Wdate" onclick="javascript:WdatePicker();" size="10"/>
            到<input type="text" name="endtime" value="<?=$_GET['endtime']?>" class="Wdate" onclick="javascript:WdatePicker();" size="10"/>
            <input type="submit" class="but2" value="查询" />
        </form>
    </div>
    <div class="main_content">
        <? if(!empty($result['total'])){?>
            <table class="table">
                <tr>
                    <th>ID</th>
                    <th>流水号</th>
                    <th>用户名</th>
                    <th>真实姓名</th>
                    <th>充值类型</th>
                    <th>充值金额</th>
                    <th>手续费</th>
                    <th>到账金额</th>
                    <th>充值时间</th>
                    <th>备注</th>
                    <th>审核时间</th>
                    <th>审核备注</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                <? foreach($result['list'] as $row){?>
                    <tr>
                        <td><?=$row->id?></td>
                        <td><?=$row->trade_no?></td>
                        <td><?=$row->user()->username?></td>
                        <td><?=$row->user()->name?></td>
                        <td><? if($row->type==1){echo "在线";}else{echo "线下";}?></td>
                        <td>￥<?=(float)$row->money?></td>
                        <td>￥<?=(float)$row->fee?></td>
                        <td class="fl"><? if($row->status==1){?>￥<?=$row->money-$row->fee?><? }?></td>
                        <td><?=$row->created_at?></td>
                        <td class="fl"><?=nl2br($row->remark)?></td>
                        <td><? if($row->verify_time!=0){echo date('Y-m-d H:i:s',$row->verify_time);}?></td>
                        <td class="fl"><?=nl2br($row->verify_remark)?></td>
                        <td><? if ($row->status == 0) {
                                echo "待审核";
                            } elseif ($row->status == 1) {
                                echo "充值成功";
                            } elseif ($row->status == 2) {
                                echo "未通过";
                            } ?>
                        </td>
                        <td>
                            <?
                            if($row->status=="0")
                            {
                                ?>
                                <a href="<?=url("recharge/edit/?id={$row->id}&page={$_GET['page']}")?>">审核</a>
                                <?
                            }
                            ?>
                        </td>
                    </tr>
                <? }?>
            </table>
        <? }else{?>
            <div class="alert-warning" role="alert">无记录！</div>
        <? }?>
        <?=$result['page'];?>
    </div>
<? elseif ($this->func == 'edit') : ?>
    <div class="main_title">
        <span>管理</span><? if ($this->func == 'add') { ?>新增<? } else { ?>编辑<? } ?>
        <a href="<?= url('recharge') ?>" class="but1">返回列表</a>
    </div>
    <div class="main_content">
        <form method="post">
            <input type="hidden" name="id" value="<?=$_GET['id']?>"/>
            <table class="table_from">
                <tr><td>流水号：</td><td><?=$row->trade_no?></td></tr>
                <tr><td>用户名：</td><td><?=$row->user()->username?></td></tr>
                <tr><td>真实姓名：</td><td><?=$row->user()->name?></td></tr>
                <tr><td>充值类型：</td><td><? if($row->type==1){echo "在线充值";}else{echo "线下充值";}?></td></tr>
                <tr><td>所属银行：</td><td><? if($row->type==1){echo "在线充值";}else{echo "线下充值";}?></td></tr>
                <tr><td>充值金额：</td><td>￥<?=$row->fee?></td></tr>
                <tr><td>手续费：</td><td>￥<?=$row->fee?></td></tr>
                <tr><td>到账金额：</td><td>￥<?=$row->money-$row->fee?></td></tr>
                <tr><td>充值时间：</td><td><?=$row->money?></td></tr>
                <tr><td>状态：</td><td><? if($row->status==0){echo "待审核";}elseif($row->status==1){echo "充值成功";}elseif($row->status==2){echo "审核未通过";}?></td></tr>
                <tr><td></td><td></td></tr>
                <tr><td>审核：</td><td>
                        <label><input type="radio" name="status" value="1"/>审核通过</label>
                        <label><input type="radio" name="status" value="2"/>审核不通过</label></td></tr>
                <tr><td>审核备注：</td><td><textarea name="verify_remark" cols="45" rows="5"></textarea></td></tr>
                <tr><td></td><td><input type="submit" class="but3" value="确认审核" />
                        <input type="button" class="but3" value="返回" onclick="window.history.go(-1)"/></td></tr>
            </table>
        </form>
    </div>
<? endif; ?>
<?php require 'footer.php'; ?>