<?php require 'header.php';?>
<div class="warpcon">
    <?php require 'left.php';?>
    <div class="warpright">
        <?php if($this->func=='recharge') : ?>
            <div class="box">
                <ul class="nav-tabs">
                    <li class="active"><a href="<?=url('account/recharge')?>">我要充值</a></li>
                    <li><a href="<?=url('account/rechargeLog')?>">充值记录</a></li>
                </ul>
                <form id="formpay" method="post" onSubmit="return card();" action="/pay/recharge" target="_blank">
                    <input type="hidden" name="user_id" value="<?=$user->id?>">
                    <table class="table_from">
                        <tr>
                            <td align="right">用户名：</td><td><?=$user->username?></td>
                        </tr>
                        <tr>
                            <td align="right">充值金额：</td><td><input id="money" name="money" type="text" size="8" onKeyUp="value=value.replace(/[^0-9.]/g,'')"/>&nbsp;&nbsp;元</td>
                        </tr>
                        <tr>
                            <td align="right">充值方式：</td><td><label><input id="type1" name="type" type="radio" value="1" onClick="changetype(1)" checked="checked"/> 在线充值</label>
                                <label><input id="type2" name="type" type="radio" value="2" onClick="changetype(2)"/> 线下充值</label></td>
                        </tr>
                        <tr id="xianshang">
                            <td align="right">充值银行：</td>
                            <td>
                                <table>
                                    <tr>
                                        <td ><label><input type="radio" name="GateId" value="25" checked="checked"/>
                                                <img align="absmiddle" src="/themes/images/bank/ICBC_OUT.gif" border="0"/></label></td>
                                        <td><label><input type="radio" name="GateId" value="29"/>
                                                <img align="absmiddle" src="/themes/images/bank/ABC_OUT.gif" border="0"/></label></td>
                                        <td ><label><input type="radio" name="GateId" value="27"/>
                                                <img align="absmiddle" src="/themes/images/bank/CCB_OUT.gif" border="0"/></label></td>
                                    </tr>
                                    <tr>
                                        <td><label><input type="radio" name="GateId" value="28"/>
                                                <img align="absmiddle" src="/themes/images/bank/CMB_OUT.gif" border="0"/></label></td>
                                        <td><label><input type="radio" name="GateId" value="12"/>
                                                <img align="absmiddle" src="/themes/images/bank/CMBC_OUT.gif" border="0"/></label></td>
                                        <td><label><input type="radio" name="GateId" value="13"/>
                                                <img align="absmiddle" src="/themes/images/bank/hx.jpg" border="0"/></label></td>
                                    </tr>
                                    <tr>
                                        <td><label><input type="radio" name="GateId" value="33"/>
                                                <img align="absmiddle" src="/themes/images/bank/CITIC_OUT.gif" border="0"/></label></td>
                                        <td><label><input type="radio" name="GateId" value="36"/>
                                                <img align="absmiddle" src="/themes/images/bank/CEB_OUT.gif" border="0"/></label></td>
                                        <td><label><input type="radio" name="GateId" value="09"/>
                                                <img align="absmiddle" src="/themes/images/bank/CIB_OUT.gif" border="0"/></label></td>
                                    </tr>
                                    <tr>
                                        <td><label><input type="radio" name="GateId" value="PSBC"/>
                                                <img align="absmiddle" src="/themes/images/bank/yz.jpg" border="0"/></label></td>
                                        <td><label><input type="radio" name="GateId" value="45">
                                                <img align="absmiddle" src="/themes/images/bank/BOC_OUT.gif" border="0"/></label></td>
                                        <td><label><input type="radio" name="GateId" value="21"/>
                                                <img align="absmiddle" src="/themes/images/bank/COMM_OUT.gif" border="0"/></label></td>
                                    </tr>
                                    <tr>
                                        <td><label><input type="radio" name="GateId" value="GDB" />
                                                <img align="absmiddle" src="/themes/images/bank/GDB_OUT.gif" border="0"/></label></td>
                                        <td><label><input type="radio" name="GateId" value="16">
                                                <img align="absmiddle" src="/themes/images/bank/pf.jpg" border="0"/></label></td>
                                        <td></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr id="xianxia" style="display:none">
                            <td align="right">线下充值：</td>
                            <td>
                                <table cellpadding="4" cellspacing="1">
                                    <tr>
                                        <td><input type="radio" name="payment" value="建设银行" checked="checked"/></td><td>郑州璞胜金投电子商务有限公司  </td><td>中国建设银行郑州市金海支行</td><td>4100 1511 0100 5027 4673</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr id="xianxiabz" style="display:none">
                            <td align="right">备注：</td><td><textarea  id="remark" name="remark" cols="60" rows="5"></textarea>*必填</td>
                        </tr>
                        <tr>
                            <td></td><td><input type="submit" value="确认提交"/></td>
                        </tr>
                    </table>
                </form>
                <ul class="prompt">
                    <h4>温馨提示：</h4>
                    <li>1.	线下充值  单笔金额不低于1000元，有效充值登记时间为:周一至周五的9:30到17:00，充值成功请跟我们的客服联系；</li>
                    <li>2.	线下充值备注  请注明您的用户名，转账银行卡号和转账流水号，以及转账时间。</li>
                </ul>
            </div>
        <?php elseif ($this->func=='rechargeLog'): ?>
            <div class="box">
                <ul class="nav-tabs">
                    <li><a href="<?=url('account/recharge')?>">我要充值</a></li>
                    <li class="active"><a href="<?=url('account/rechargeLog')?>">充值记录</a></li>
                </ul>
                <div class="search">
                    <form  method="get">
                        记录时间：
                        <input  name="starttime" type="text" value="<?=$_GET['starttime']?>" onClick="javascript:WdatePicker();" class="Wdate">
                        到
                        <input  name="endtime" type="text" value="<?=$_GET['endtime']?>" onClick="javascript:WdatePicker();" class="Wdate">
                        <input  type="submit" value="查询" />
                    </form>
                </div>
                <? if(!empty($result['total'])){?>
                    <table class="table">
                        <tr>
                            <th>充值时间</th>
                            <th>充值类型</th>
                            <th>充值金额</th>
                            <th>手续费</th>
                            <th>到账金额</th>
                            <th>充值备注</th>
                            <th>审核备注</th>
                            <th>状态</th>
                        </tr>
                        <? foreach($result['list'] as $row){?>
                            <tr>
                                <td align="center"><?=$row->created_at?></td>
                                <td align="center"><? if($row->type==1){echo "在线";}else{echo "线下";}?></td>
                                <td>￥<?=(float)$row->money?></td>
                                <td>￥<?=(float)$row->fee?></td>
                                <td style="color:#F00;"><? if($row->status==1){?>￥<?=$row->money-$row->fee?><? }?></td>
                                <td><?=nl2br($row->remark)?></td>
                                <td><?=nl2br($row->verify_remark)?></td>
                                <td align="center"><? if ($row->status == 0) {
                                        echo "待审核";
                                    } elseif ($row->status == 1) {
                                        echo "充值成功";
                                    } elseif ($row->status == 2) {
                                        echo "审核未通过";
                                    } ?></td>
                            </tr>
                        <? }?>
                    </table>
                <? }else{?>
                    <div class="alert-warning" role="alert">无记录！</div>
                <? }?>
                <?=$result['page'];?>
            </div>
            <?php elseif ($this->func=='log'): ?>
            <div class="box">
                <h3>资金记录：</h3>
                <div class="search">
                    <form  method="get">
                        记录时间：
                        <input  name="starttime" type="text" value="<?=$_GET['starttime']?>" onClick="javascript:WdatePicker();" class="Wdate">
                        到
                        <input  name="endtime" type="text" value="<?=$_GET['endtime']?>" onClick="javascript:WdatePicker();" class="Wdate">
                        <input  type="submit" value="查询" />
                    </form>
                </div>
                <? if(!empty($result['total'])){?>
                    <table class="table">
                        <tr>
                            <th>时间</th>
                            <th>类型</th>
                            <th>变动</th>
                            <th>当前</th>
                            <th>备注</th>
                        </tr>
                        <? foreach($result['list'] as $row){
                            ?>
                            <tr>
                                <td><?=$row->created_at?></td>
                                <td><?=$row->getLinkPage('account_type',$row->type);?></td>
                                <td class="fl"><?=$row->change?></td>
                                <td class="fl"><?=$row->now?></td>
                                <td class="fl"><?=nl2br($row->remark)?></td>
                            </tr>
                        <? }?>
                    </table>
                <? }else{?>
                    <div class="alert-warning" role="alert">无记录！</div>
                <? }?>
                <?=$result['page'];?>
            </div>
        <?php endif;?>
    </div>
</div>
<?php require 'footer.php';?>
