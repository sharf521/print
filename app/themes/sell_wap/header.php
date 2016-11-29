<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title><?php if(!empty($title_herder)){echo $title_herder.'-';}?><?=app('\App\Model\System')->getCode('webname');?></title>
    <link rel="stylesheet" href="//res.wx.qq.com/open/libs/weui/1.1.0/weui.css"/>
    <link rel="stylesheet" href="/themes/sell_wap/sell.css?<?=rand(1000,9999)?>"/>
    <script src="/plugin/js/jquery.js"></script>
    <script src="/themes/sell_wap/sell.js?<?=rand(1000,9999)?>"></script>
</head>
<body ontouchstart>