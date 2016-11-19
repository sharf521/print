<?php require 'header.php';?>
    <div class="m_header">
        <a class="m_header_l" href="<?=url('')?>"><i class="m_icogohisr"></i></a>
        <a class="m_header_r" href="<?=url('goods/add')?>">添加</a>
        <h1>商品管理</h1>
    </div>
    <div class="my-navbar margin_header">
        <div class="my-navbar__item my-navbar__item_on">
            选项一
        </div>
        <div class="my-navbar__item">
            选项二
        </div>
        <div class="my-navbar__item">
            选项二
        </div>
        <div class="my-navbar__item">
            选项二
        </div>
    </div>

    <ul class="commoditylist_content">
        <li>
            <a href="/view/product/details.html?goodid=8ff32637-6c7f-445d-86bc-f0b8d21d5e3e">
              <span class="imgspan">
                    <img class="" src="http://tong.img.tongtongmall.com/3f1fe28a6a7642a1929503641551fc32?imageView2/4/quality/30">
                </span>
                <div>
                    <p class="cd_title">法国ALPHANOVA艾罗若华护臀膏100ml法国100ml </p>
                    <p class="cd_money">
                        <span>￥</span>
                        <var>79.</var>
                        <span>00</span>
                    </p>
                    <p class="cd_sales">0购买 100.00%好评</p>
                </div>
            </a>
        </li>
        <li>
            <a href="/view/product/details.html?goodid=74d0fa25-d879-48f8-8646-dc7f4b6f5a74">
              <span class="search_list_q">

                    <img class="" src="http://tong.img.tongtongmall.com/177f6c828be1485a9e685ebe2ce1fef9?imageView2/4/quality/30">
                </span>
                <div>
                    <p class="cd_title">法国艾罗若华100%有机纯天然公主泡泡浴200ml </p>
                    <p class="cd_money">
                        <span>￥</span>
                        <var>86.</var>
                        <span>00</span>
                    </p>
                    <p class="cd_sales">0购买 100.00%好评</p>
                </div>
            </a>
        </li>
    </ul>


<a href="<?=url('goods/add')?>" class="weui-btn weui-btn_primary">添加商品</a>

    <div class="weui-tabbar">
        <a href="javascript:;" class="weui-tabbar__item weui-bar__item_on">
            <img src="./images/icon_tabbar.png" alt="" class="weui-tabbar__icon">
            <p class="weui-tabbar__label">微信</p>
        </a>
        <a href="javascript:;" class="weui-tabbar__item">
            <img src="./images/icon_tabbar.png" alt="" class="weui-tabbar__icon">
            <p class="weui-tabbar__label">通讯录</p>
        </a>
        <a href="javascript:;" class="weui-tabbar__item">
            <img src="./images/icon_tabbar.png" alt="" class="weui-tabbar__icon">
            <p class="weui-tabbar__label">发现</p>
        </a>
        <a href="javascript:;" class="weui-tabbar__item">
            <img src="./images/icon_tabbar.png" alt="" class="weui-tabbar__icon">
            <p class="weui-tabbar__label">我</p>
        </a>
    </div>
<?php require 'footer.php';?>