<script charset="utf-8" src="/plugin/layer_mobile/layer.js"></script>
<script src="/plugin/js/echo.min.js"></script>
<script>
    <?php  if(session('msg')) : ?>
    layer.open({
        content: '<?=addslashes(session('msg'))?>',
        skin: 'msg',
        time:3
    });
    <? endif;
    if(session('error')) : ?>
    layer.open({
        content: '<?=addslashes(session('error'))?>',
        skin: 'msg',
        time:5
    });
    <? endif; ?>
    echo.init();
</script>
</body>
</html>