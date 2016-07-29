<div class="footer">Copyright 2012-2020 Inc.,All rights reserved. 豫ICP备11027012号<br>
    技术支持：郑州璞胜金投电子商务科技有限公司</div>
<script charset="utf-8" src="/plugin/layer/layer.js"></script>
<script>
    <?php if (session('msg')){?>
    layer.msg('<?= session('msg') ?>', {
        offset: '200px',
        icon: 1,
        time: 1000
    });
    <? }
    if (session('error')){?>
    layer.msg('<?= session('error') ?>', {
        offset: '200px',
        icon: 2,
        time: 2000
    });
    <?php } ?>
//    $(document).ready(function () {
//        $("form").submit(function (e) {
//            $(":submit", this).css("background-color", "#cccccc");
//            $("input[type='submit']", this).attr("disabled", true);
//            return true;
//        });
//    });
</script>
</body>
</html>