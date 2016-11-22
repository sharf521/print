<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title><?php if(!empty($title_herder)){echo $title_herder.'-';}?><?=app('\App\Model\System')->getCode('webname');?></title>
    <script src="/plugin/js/jquery.js"></script>
    <link rel="stylesheet" href="//res.wx.qq.com/open/libs/weui/1.0.2/weui.css"/>
    <link rel="stylesheet" href="/plugin/Swiper/css/swiper.min.css"/>
    <script src="/plugin/Swiper/js/swiper.min.js"></script>
    <link rel="stylesheet" href="/themes/shop_wap/base.css"/>
    <script src="/themes/shop_wap/base.js"></script>
</head>
<body ontouchstart>