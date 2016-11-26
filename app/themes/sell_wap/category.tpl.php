<?php require 'header.php';?>
<?php if($this->func=='index') : ?>
    <div class="m_header">
        <a class="m_header_l" href="<?=url('/member')?>"><i class="iconfont">&#xe604;</i></a>
        <a class="m_header_r" href="<?=url('category/add')?>">添加</a>
        <h1>分类管理</h1>
    </div>
    <div class="weui-cells__title margin_header">分类管理</div>
    <div class="weui-cells">
        <? foreach($cates as $cate) : ?>
        <div class="weui-cell">
            <div class="weui-cell__bd"><?=$cate['name']?></div>
            <div class="weui-cell__ft"><a href="<?=url("category/add/?pid={$cate['id']}")?>"></a>
                <a href="<?=url("category/edit/?id={$cate['id']}")?>">编辑</a>
                <a href="javascript:cateDel(<?=$cate['id']?>)">删除</a></div>
            <? /* if(isset($cate['son']) && is_array($cate['son'])) :
                echo '<ul>';
                foreach($cate['son'] as $son) : ?>
                    <li>
                        <?=$son['name']?>
                        <div class="operat">
                            <a href="<?=url("category/edit/?id={$son['id']}")?>">编辑</a>
                            <a href="javascript:cateDel(<?=$son['id']?>)">删</a>
                        </div>
                    </li>
                    <?
                endforeach;
                echo '</ul>';
            endif; */ ?>
        </div>
        <? endforeach;?>
    </div>

    <div class="weui-btn-area">
        <a class="weui-btn weui-btn_primary" href="<?=url('category/add')?>" id="showTooltips">添加分类</a>
    </div>
    <script>
        function cateDel(id)
        {
            layer.open({
                content: '您确定要删除吗？'
                ,btn: ['删除', '取消']
                ,yes: function(index){
                    location.href='<?=url("category/del/?id=")?>'+id;
                    layer.close(index);
                }
            });
        }
    </script>
<?php elseif($this->func=='add' || $this->func=='edit') : ?>
    <div class="m_header">
        <a class="m_header_l" href="<?=url('category')?>"><i class="iconfont">&#xe604;</i></a>
        <a class="m_header_r"></a>
        <h1>分类管理</h1>
    </div>
    <div class="weui-cells__title margin_header"><?=$this->func == 'add'?'新增':'编辑'; ?>分类</div>
    <form method="post">
        <input type="hidden" name="pid" value="<?=(int)$_GET['pid']?>"/>
        <ul class="my_cells">
            <li>
                <div class="my_cells_hd">名称</div>
                <div class="my_cells_bd"><input class="weui-input" name="name" value="<?=$cate->name?>" type="text"  placeholder="请输入分类名称"/></div>
            </li>
        </ul>

        <div class="weui-btn-area">
            <input class="weui-btn weui-btn_primary" type="submit" value="保存">
        </div>
    </form>
<?php endif;?>
<?php require 'footer.php';?>