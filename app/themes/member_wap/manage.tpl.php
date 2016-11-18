<?php require 'header.php';?>
    <div class="m_header">
        <a class="m_header_l" href="javascript:history.go(-1);"><i class="m_icogohisr"></i></a>
        <a class="m_header_r" href="m_seach.html"><i class="m_ico m_icosearch"></i></a>
        <h1></h1>
    </div>
<br><br><br>


    <div class="weui-cells__title">带说明、跳转的列表项</div>
    <div class="weui-cells">
        <a class="weui-cell weui-cell_access" href="<?=url('category')?>">
            <div class="weui-cell__bd">
                <p>分类管理</p>
            </div>
            <div class="weui-cell__ft">编辑、添加</div>
        </a>
        <a class="weui-cell weui-cell_access" href="<?=url('goods')?>">
            <div class="weui-cell__bd">
                <p>商品管理</p>
            </div>
            <div class="weui-cell__ft">编辑、添加</div>
        </a>

    </div>

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
    <script type="text/javascript">
        $(function(){
            $('.weui-tabbar__item').on('click', function () {
                $(this).addClass('weui-bar__item_on').siblings('.weui-bar__item_on').removeClass('weui-bar__item_on');
            });
        });
    </script>
<?php require 'footer.php';?>