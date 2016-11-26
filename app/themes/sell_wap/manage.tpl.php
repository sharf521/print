<?php require 'header.php';?>
    <div class="m_header">
        <a class="m_header_l" href="<?=url('/member')?>"><i class="iconfont">&#xe604;</i></a>
        <a class="m_header_r"></a>
        <h1>卖家中心</h1>
    </div>

    <script type="text/javascript">
        $(function(){
            $('.weui-tabbar__item').on('click', function () {
                $(this).addClass('weui-bar__item_on').siblings('.weui-bar__item_on').removeClass('weui-bar__item_on');
            });
        });
    </script>
<?php require 'footer.php';?>