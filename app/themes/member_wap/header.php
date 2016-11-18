<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title><?php if(!empty($title_herder)){echo $title_herder.'-';}?><?=app('\App\Model\System')->getCode('webname');?></title>
    <link rel="stylesheet" href="//res.wx.qq.com/open/libs/weui/1.0.2/weui.css"/>
    <script src="/plugin/js/jquery.js"></script>
    <link rel="stylesheet" href="/themes/member_wap/member.css"/>
</head>
<body ontouchstart>