<!DOCTYPE html >
<html lang="zh-cmn-Hans">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>登录</title>
    <link rel="stylesheet" href="/themes/default/reset.css"/>
    <link href="/plugin/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/themes/default/zh_index.css"/>
    <script charset="utf-8" src="/plugin/js/My97DatePicker/WdatePicker.js"></script>
</head>
<body style="background:url(/themes/default/images/qpbj.jpg) no-repeat center; background-size:cover;">
<div class="mainbox">
    <div class="left_reason fl">
        <div class="link_logo"><img src="/themes/default/images/link.png" /></div>
    </div>
    <div class="mill loginbox fl">
        <form id="login_form" method="post">
            <h3>会员注册</h3>
            <div class="from_cont">
                <p><input type="text" name="username"  placeholder="请输入账号"/><b></b></p>
                <p><input type="text" name="email"  placeholder="请输入邮箱"/><b></b></p>
                <p><input type="text" name="invite_user"  placeholder="推荐人"/><b></b></p>
                <p><input type="password" name="password"  placeholder="请输入密码" /><b></b></p>
                <p><input type="password" name="sure_password"  placeholder="确认密码"/><b></b></p>
            </div>
            <p class="tip_most"><span>没有账号？<a href="<?=$_url?>">去登陆</a></span></p>
            <p class="smit_btn"><input type="submit" value="登&nbsp;&nbsp;录" /></p>
            <input type="hidden" name="_token"  value="<?=_token();?>"/>
        </form>
    </div>
    <a class="back_link" href="<?=$_url?>">返回登陆页</a>
    <p class="clear"></p>
</div>
<script src="/plugin/js/jquery.js"></script>
<script src="/plugin/js/jquery.validation.min.js"></script>
<script>
    $(document).ready(function(){

        $('#login_form').validate({
            onkeyup: false,
            errorPlacement: function(error, element){
                element.nextAll('b').first().after(error);
            },
            submitHandler:function(form){
                ajaxpost('login_form', '', '', 'onerror');
            },
            rules: {
                email: {
                    required: true,
//                    email:true,
                },
                password: {
                    required: true,
                },

            },
            messages: {
                username: {
                    required: '<i class="fa fa-exclamation-circle"></i>请填写账号',
                    email: '<i class="fa fa-exclamation-circle"></i>请填写正确的邮箱',
                },
                password: {
                    required: '<i class="fa fa-exclamation-circle"></i>请填写密码',
                },
            }
        });
    });
</script>
<?php require 'footer.php';?>