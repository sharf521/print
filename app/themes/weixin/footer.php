<script charset="utf-8" src="/plugin/layer.mobile/layer.js"></script>
<?php
if(session('msg'))
{
    ?>
    <script>
        layer.open({
            content: '<?=session('msg')?>',
            style: 'font-size:2.8rem;padding: 2rem 1.5rem;line-height: 2.2rem;',
            time: 2
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
            style: 'font-size:2.8rem;padding: 2rem 1.5rem;line-height: 2.2rem;'
        });
    </script>
    <?
}
?>
</body>
</html>