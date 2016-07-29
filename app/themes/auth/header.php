<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>购物中心—<?= $this->subsite['title'] ?></title>
    <meta name="Description" content="<?= $this->subsite['description'] ?>"/>
    <meta name="Keywords" content="<?= $this->subsite['keywords'] ?>"/>
    <link href="<?= $tpldir ?>style.css" rel="stylesheet"/>
    <script language="javascript" src="/plugin/js/jquery.js"></script>
    <script language="javascript" src="<?= $tpldir ?>js/default.js"></script>
    <script language="javascript" src="/plugin/js/jquery.scrollLoading.js"></script>
    <script>$(function () {
            $(".scrollloading").scrollLoading();
        });</script>
</head>
<body>
<div class="headbox">
    <div class="headcontaner">
        <div class="head">
            <? if ($this->username) { ?>
                <span>您好，<?= $this->username ?>，欢迎来到<?= $this->subsite['title'] ?>！</span><a class="a2"
                                                                                             href="/member/logout">退出</a>
            <? } else { ?>
                <a class="a2" href="/login">登录</a><span>或</span>
                <a class="a3" href="/register">注册新会员</a>
            <? } ?>
        </div>
        <ul class="topnav">
            <li><a href="/goods/shop_cart">我的购物车</a>&nbsp; &nbsp;▏</li>
            <li><a href="/search">保单查询</a>&nbsp; &nbsp;▏</li>
            <li><a href="/member">用户中心</a>&nbsp; &nbsp;▏</li>
            <li><a href="/information/zhishi">帮助中心</a></li>
        </ul>
    </div>
</div>
<div class="menubox">
    <img src="<?= $this->subsite['logo'] ?>" height="60px;"/>
    <ul class="menu">
        <li><a href="/">网站首页</a></li>
        <li><a href="/information/zhishi">车险服务</a></li>
        <li><a href="/information/cheap">巨划算</a></li>
        <li><a href="/goods/lists/31">商品中心</a></li>
        <li><a href="/member">用户中心</a></li>
        <!--<li><a href="/goods">热卖商品</a></li>-->
    </ul>
    <ul class="tle">
        <div class="phone"></div>
        <li>热线服务</li>
        <li><?= $this->subsite['tel'] ?></li>
    </ul>
</div>