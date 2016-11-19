<script charset="utf-8" src="/plugin/layer_mobile/layer.js"></script>
<?php
if(session('msg'))
{
    ?>
    <script>
        layer.open({
            content: '<?=session('msg')?>',
            skin: 'msg',
            time:3
        });
    </script>
    <?
}
if(session('error'))
{
    ?>
    <script>
        layer.open({
            content: '<?=session('error')?>',
            skin: 'msg',
            time:5
        });
    </script>
    <?
}
?>
</body>
</html>