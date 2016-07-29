<script charset="utf-8" src="/plugin/layer/layer.js"></script>
<?php
if(session('msg'))
{
    ?>
    <script>
        layer.msg('<?=session('msg')?>', {
            offset: '200px',
            icon: 1,
            time: 1000
        });
    </script>
    <?
}
if(session('error'))
{
    ?>
    <script>
        layer.msg('<?=session('error')?>', {
            offset: '200px',
            icon: 2,
            time: 2000
        });
    </script>
    <?
}
?>
</body>
</html>