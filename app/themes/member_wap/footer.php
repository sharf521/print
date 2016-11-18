<script charset="utf-8" src="/plugin/layer_mobile/layer.js"></script>
<?php
if(session('msg'))
{
    ?>
    <script>
        layer.open({
            content: '<?=session('msg')?>',
            style: '',
            time:10000
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
            style: ''
        });
    </script>
    <?
}
?>
</body>
</html>