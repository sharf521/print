<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>WeUI</title>
    <link rel="stylesheet" href="//res.wx.qq.com/open/libs/weui/1.0.2/weui.css"/>
    <script src="/plugin/js/jquery.js"></script>
    <style type="text/css">
        *{max-height: 9999999px;}

        /*定义滚动条高宽及背景 高宽分别对应横竖滚动条的尺寸*/
        ::-webkit-scrollbar
        {
            width: 0;
            height: 0;
            background-color: #fff;
            display: none;
        }
        @media only screen and (min-width:800px){
            body{ width:760px; margin:0 auto; background:#f8f8f8;}
        }
        ul,li{list-style: none}
        .hide{display: none}
        #addSpecBtn{ padding-left: 1em; margin-top: .5em;font-size: 17px; font-weight: bold; color: #1AAD19}
        .spec_item{border: 1px solid #d9d9d9; width: 90%; margin: 10px 0; background-color: #ffffff}
        .spec_del{position: absolute; width: 30px; right: -15px; background-color: #fff; border-radius: 50%;}

        .weui-tabbar{left: 0;}
        .my-navbar {
            display: -webkit-box;
            display: -webkit-flex;
            display: flex;
            z-index: 500;
            top: 0;
            width: 100%;
            background-color: #fafafa;
        }
        .my-navbar:after {
            content: " ";
            position: absolute;
            left: 0;
            bottom: 0;
            right: 0;
            height: 1px;
            border-bottom: 1px solid #CCCCCC;
            color: #CCCCCC;
            -webkit-transform-origin: 0 100%;
            transform-origin: 0 100%;
            -webkit-transform: scaleY(0.5);
            transform: scaleY(0.5);
        }
        .my-navbar__item {
            position: relative;
            display: block;
            -webkit-box-flex: 1;
            -webkit-flex: 1;
            flex: 1;
            padding: 5px 0;
            text-align: center;
            font-size: 15px;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
        }
        .my-navbar__item:active {
            background-color: #ededed;
        }
        .my-navbar__item.my-navbar__item_on {
            color: #1AAD19;
            border-bottom: 2px solid #1AAD19;
        }


        .commoditylist_content {
            width: 100%; margin-top: 5px;
            height: auto; border-top: 1px solid #f3f3f3;
        }
        .commoditylist_content a {
            border: none;
            text-decoration: none
        }

        .commoditylist_content li {
            height: auto;
            background: #fff;
            position: relative;
            padding: .5rem 1rem;
            border-bottom: 1px solid #f3f3f3
        }
        
        .commoditylist_content li .imgspan{
            display: inline-block;
            position: relative
        }

        .commoditylist_content li img {
            width: 100px;
            height: 100px
        }

        .commoditylist_content li div {
            position: absolute;
            left: 116px;
            top: .5rem;
            bottom: .5rem;
            right: 1rem;
            padding-left: .8rem
        }

        .commoditylist_content li div .cd_title {
            color: #000000;
            font-weight: 400;
            font-size: 17px;
            width: auto;
            overflow: hidden;
            text-overflow: ellipsis;
            word-wrap: normal;
            word-wrap: break-word;
            word-break: break-all;
            max-height: 50px;
        }

        .commoditylist_content .cd_ensure {
            font-size: 12px;
            color: #666;
            margin-top: 3px;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis
        }

        .commoditylist_content .cd_money {
            font-size: 18px;
            color: red;
        }

        .commoditylist_content .cd_money span {
            font-size: 12px;
            color: red
        }

        .commoditylist_content .cd_money label {
            background: #ffc956;
            color: #fff;
            border-radius: 3px;
            font-size: 10px;
            margin-left: 7px;
            padding: 0 2px
        }

        .commoditylist_content .cd_sales {
            font-size: 12px;
            color: #666;
            position: absolute;
            bottom: 0.5rem;
        }
    </style>
</head>
<body ontouchstart>