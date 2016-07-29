<div class="warpleft">
    <h3>我的信息</h3>
    <ul>
        <li><a href="<?=url('user/userInfo')?>"  <? if($this->func=='userInfo'){echo 'class="whover"';}?>>个人信息</a></li>
        <li><a href="<?=url('user/bank')?>" <? if($this->func=='bank'){echo 'class="whover"';}?>>银行帐号</a></li>
    </ul>
    <h3>我的资金</h3>
    <ul>
        <li><a href="<?=url('account/recharge')?>" <? if($this->func=='recharge'){echo 'class="whover"';}?>>充值管理</a></li>
        <li><a href="<?=url('account/cash')?>" <? if($this->func=='cash'){echo 'class="whover"';}?>>提现管理</a></li>
        <li><a href="<?=url('account/log')?>" <? if($this->func=='log'){echo 'class="whover"';}?>>资金记录</a></li>
        <li><a href="useralldd.html">积分兑换现金</a></li>
    </ul>
    <h3>密码管理</h3>
    <ul>
        <li><a href="<?=url('user/changePwd')?>" <? if($this->func=='changePwd'){echo 'class="whover"';}?>>修改密码</a></li>
        <li><a href="<?=url('user/changePayPwd')?>" <? if($this->func=='changePayPwd'){echo 'class="whover"';}?>>修改支付密码</a></li>
        <li class="active"><a href="<?=url('user/getPayPwd')?>" <? if($this->func=='getPayPwd'){echo 'class="whover"';}?>>找回支付密码</a></li>
    </ul>
</div>