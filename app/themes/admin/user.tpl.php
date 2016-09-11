<?php
require 'header.php';
if($this->func=='index'){?>
    <div class="main_title">
        <span>用户管理</span>列表 <?= $this->anchor('user/add/', '添加'); ?>
    </div>
    <form method="get">
        <div class="search">
            用户类型：<select name="type_id" id="type_id">
                <option value="">请选择</option>
                <?
                foreach ($usertype as $utype) {
                    ?>
                    <option value="<?= $utype['id'] ?>" <? if ($utype['id'] == $_GET['type_id']) {
                        echo ' selected';
                    } ?>><?= $utype['name'] ?></option>
                    <?
                }
                ?>
            </select>
            用户名：<input type="text" name="username" value="<?= $_GET['username'] ?>"/>
            邀请人ID:<input type="text" size="4" name="invite_userid" value="<?= $_GET['invite_userid'] ?>"/>

            时间：<input  name="starttime" type="text" value="<?=$_GET['starttime']?>" onClick="javascript:WdatePicker();" class="Wdate">
            到
            <input  name="endtime" type="text" value="<?=$_GET['endtime']?>" onClick="javascript:WdatePicker();" class="Wdate">
            <input type="submit" class="but2" value="查询"/>
        </div>
    </form>
        <table class="table">
        	<tr class="bt">
            	<th>ID/用户名</th>
                <th>用户类型</th>
                <th>头像</th>
                <th>昵称</th>
                <th>openid</th>
                <th>邀请人数</th>
                <th>性别</th>
                <th>城市</th>
                <th>邀请人</th>
                <th>注册时间</th>
                <th>操作</th>
            </tr>
            <?
            $arr_sex=array('','男','女');
            foreach($list as $row)
			{
                $UserWx=$row->UserWx();
                $Invite=$row->Invite();
			?>
            <tr>
            	<td><?=$row->id?>/<?=$row->username?></td>
                <td><?=$row->UserType()->name?></td>
                <td>
                    <? if($UserWx->headimgurl!=''):?>
                    <img src="<?=substr($UserWx->headimgurl,0,-1)?>64" width="50">
                    <? endif;?>
                </td>
                <td><?=$row->nickname?></td>
                <td><?=$row->openid?></td>
                <td><?=$row->invite_count?></td>
                <td><?=$arr_sex[$UserWx->sex]?></td>
                <td><?=$UserWx->province?>-<?=$UserWx->city?></td>
                <td><?=$Invite->id?>/<?=$Invite->nickname?></td>
                <td><?=$row->created_at?></td>
                <td>
                    <?
                    if ($row->id == "1") {
                        echo "ADMIN用户禁止操作";
                    } else {
                        echo $this->anchor('user/edit/?id=' . $row->id, '编辑');
                        echo '&nbsp;|&nbsp;';
                        echo $this->anchor('user/edittype/?id=' . $row->id, '修改用户类型');
                        echo '&nbsp;|&nbsp;';
                        echo $this->anchor('user/updatepwd/?id=' . $row->id, '修改密码');
                    }
                    ?>
                    <a href="<?=url("printTask/taskAdd/?id={$row->id}&page={$_GET['page']}&nickname={$row->nickname}")?>">添加订单</a>
                    </td>
            </tr>
            <? }?>
        </table>
		<? if(empty($total)){echo "无记录！";}else{echo $page;}?>
<? }elseif($this->func=='add'){?>
    <div class="main_title">
        <span>用户管理</span>添加
		<?=$this->anchor('user','列表','class="but1"');?>
    </div>
    <form method="post">
    	<input type="hidden" name="id" value="<?=$row['id']?>"/>
    	<div class="form1">
            <ul>
              <li><label>用户名：</label><input type="text" name="username" class="input1" onblur="validate_register(this)"/><span class="validate_tip">*用户名5-15位字符</span></li>
              <li><label>邮箱：</label><input type="text" name="email" class="input4" onblur="validate_register(this)"/><span class="validate_tip">*邮箱</span></li>
                <li><label>昵称：</label><input type="text" name="nickname" ></li>
              <li><label>密码：</label><input type="password" name="password" class="input2" onblur="validate_register(this)"/><span class="validate_tip">*密码长度6-15位</span></li>
              <li><label>确认密码：</label><input type="password" name="sure_password" class="input2" onblur="validate_register(this)"/><span class="validate_tip">*确认密码</span></li>              
          </ul>
            <input type="submit" class="but3" value="保存" />
            <input type="button" class="but3" value="返回" onclick="window.history.go(-1)"/>
        </div>
    </form>
<?
}
elseif($this->func=='edit'){?>
    <div class="main_title">
        <span>用户管理</span>编辑
		<?=$this->anchor('user','列表','class="but1"');?>
    </div>
    <form method="post">
    	<div class="form1">
            <ul>
                <li><label>用户名：</label><?=$user->username?></li>
                <li><label>真实姓名：</label><input type="text" name="name" value="<?=$user->name?>"/></li>
                <li><label>昵称：</label><input type="text" name="nickname" value="<?=$user->nickname?>"/></li>
                <li><label>电话：</label><input type="text" name="tel" value="<?=$user->tel?>"/></li>
                <li><label>QQ：</label><input type="text" name="qq" value="<?=$user->qq?>"/></li>
                <li><label>地址：</label><input type="text" name="address" value="<?=$user->address?>"/></li>
            </ul>
            <input type="submit" class="but3" value="保存" />
            <input type="button" class="but3" value="返回" onclick="window.history.go(-1)"/>
        </div>
    </form>
<?
}
elseif($this->func=='updatepwd'){?>
    <div class="main_title">
        <span>用户管理</span>修改密码
        <?=$this->anchor('user','列表','class="but1"');?>
    </div>
    <form method="post">
        <div class="form1">
            <ul>
                <li><label>用户编号：</label><?=$row['id']?></li>
                <li><label>用户名：</label><?=$row['username']?></li>
                <li><label>新密码：</label><input type="text" name="password" /></li>
                <li><label>确认新密码：</label><input type="text" name="sure_password" /></li>
            </ul>
            <input type="hidden" name="id" value="<?=$row['id']?>"/>
            <input type="hidden" name="username" value="<?=$row['username']?>"/>
            <input type="submit" class="but3" value="保存" />
            <input type="button" class="but3" value="返回" onclick="window.history.go(-1)"/>
        </div>
    </form>
<?
}
elseif($this->func=='edittype')
{
?>
    <div class="main_title">
        <span>用户管理</span>修改用户类型
		<?=$this->anchor('user','列表','class="but1"');?>
    </div>
    <form method="post">
    	<input type="hidden" name="id" value="<?=$row['id']?>"/>
    	<div class="form1">
            <ul>
                <li><label>用户名：</label><?=$row['username']?></li>
                <li><label>用户类型：</label>
                <select name="type_id" id="type_id">
                	<option value="">请选择</option>
                	<?
                    	foreach($usertype as $utype)
						{
							?>
                            <option value="<?=$utype['id']?>" <? if($utype['id']==$row['type_id']){echo ' selected';}?>><?=$utype['name']?></option>  
                            <?
						}
					?>   
                </select>   
                </li>
            </ul>
            <input type="submit" class="but3" value="保存" />
            <input type="button" class="but3" value="返回" onclick="window.history.go(-1)"/>
        </div>
    </form>
<?
}
elseif($this->func=='thaw')
{
?>
    <div class="main_title">
        <span>用户管理</span>解冻保证金
		<?=$this->anchor('user','列表','class="but1"');?>
    </div>
    <form method="post">
    	<input type="hidden" name="id" value="<?=$row['id']?>"/>
    	<div class="form1">
            <ul>
                <li><label>用户名：</label><?=$row['username']?></li>
                <li><label>可用金额：</label><?=($row['use_money']!=0)?'￥'.$row['use_money']:'N/A'?></li>
                <li><label>保证金：</label><?=($row['baozheng_money']!=0)?'￥'.$row['baozheng_money']:'N/A'?></li>
                <li><label>解冻金额：</label><input type="text" name="money" onKeyUp="value=value.replace(/[^0-9.]/g,'')"/>元</li>
                <li><label>解冻备注：</label><textarea name="thaw_remark" cols="45" rows="5"></textarea>*必填</li>
            </ul>
            <input type="submit" class="but3" value="保存" />
            <input type="button" class="but3" value="返回" onclick="window.history.go(-1)"/>
        </div>
    </form>
<?
}
elseif($this->func=='frozen')
{
?>
    <div class="main_title">
        <span>用户管理</span>冻结保证金
		<?=$this->anchor('user','列表','class="but1"');?>
    </div>
    <form method="post">
    	<input type="hidden" name="id" value="<?=$row['id']?>"/>
    	<div class="form1">
            <ul>
                <li><label>用户名：</label><?=$row['username']?></li>
                <li><label>可用金额：</label><?=($row['use_money']!=0)?'￥'.$row['use_money']:'N/A'?></li>
                <li><label>保证金：</label><?=($row['baozheng_money']!=0)?'￥'.$row['baozheng_money']:'N/A'?></li>
                <li><label>冻结金额：</label><input type="text" name="money" onKeyUp="value=value.replace(/[^0-9.]/g,'')"/>元</li>
                <li><label>冻结备注：</label><textarea name="frozen_remark" cols="45" rows="5"></textarea>*必填</li>
            </ul>
            <input type="submit" class="but3" value="保存" />
            <input type="button" class="but3" value="返回" onclick="window.history.go(-1)"/>
        </div>
    </form>
<?
}
require 'footer.php';