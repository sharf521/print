<?php require 'header.php';?>
    <div class="m_header">
        <a class="m_header_l" href="<?=url('')?>"><i class="iconfont">&#xe604;</i></a>
        <a class="m_header_r"></a>
        <h1>用户中心</h1>
    </div>
    <br><br>

    <div class="weui-flex margin_header">
        <div class="weui-flex__item">
            <a href="<?=url('category')?>" style="text-align: center; width: 100%; height: 100%; display: block">
                <i class="iconfont" style="font-size: 22px; color: #999999">&#xe600;</i>
                <p class="weui-grid__label">分类管理</p>
            </a>
        </div>
        <div class="weui-flex__item">
            <a href="<?=url('goods')?>" style="text-align: center; width: 100%; height: 100%; display: block">
                <i class="iconfont" style="font-size: 22px; color: #999999">&#xe643;</i>
                <p class="weui-grid__label">商品管理</p>
            </a>
        </div>
        <div class="weui-flex__item">
            <a href="<?=url('order')?>" style="text-align: center; width: 100%; height: 100%; display: block">
                <i class="iconfont" style="font-size: 22px; color: #999999">&#xe89d;</i>
                <p class="weui-grid__label">我的订单</p>
            </a>
        </div>
    </div>

    <div class="weui-grids margin_header">
        <a href="<?=url('category')?>" class="weui-grid">
            <div class="weui-grid__icon">
                <i class="iconfont" style="font-size: 22px; color: #999999">&#xe600;</i>
            </div>
            <p class="weui-grid__label">分类管理</p>
        </a>
        <a href="<?=url('goods')?>" class="weui-grid">
            <div class="weui-grid__icon">
                <i class="iconfont" style="font-size: 22px; color: #999999">&#xe643;</i>
            </div>
            <p class="weui-grid__label">商品管理</p>
        </a>
    </div>

    <div class="weui-tabbar">
        <a href="javascript:;" class="weui-tabbar__item weui-bar__item_on">
            <i class="iconfont weui-tabbar__icon">&#xe64f;</i>
            <p class="weui-tabbar__label">首页</p>
        </a>
        <a href="javascript:;" class="weui-tabbar__item">
            <i class="iconfont weui-tabbar__icon">&#xe698;</i>
            <p class="weui-tabbar__label">购物车</p>
        </a>
        <a href="javascript:;" class="weui-tabbar__item">
            <i class="iconfont weui-tabbar__icon">&#xe89d;</i>
            <p class="weui-tabbar__label">我的订单</p>
        </a>
        <a href="javascript:;" class="weui-tabbar__item">
            <i class="iconfont weui-tabbar__icon">&#xe6fc;</i>
            <p class="weui-tabbar__label">我</p>
        </a>
    </div>
    <script type="text/javascript">
        $(function(){
            $('.weui-tabbar__item').on('click', function () {
                $(this).addClass('weui-bar__item_on').siblings('.weui-bar__item_on').removeClass('weui-bar__item_on');
            });
        });
    </script>
<?php require 'footer.php';?>