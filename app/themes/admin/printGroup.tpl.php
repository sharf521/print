<?php require 'header.php'; ?>
<? if ($this->func == 'index') : ?>
    <div class="main_title">
        <span>管理</span>列表
        <a href="<?= url('printGroup/add/') ?>" class="but1">添 加</a>
    </div>
    <div class="main_content">
        <form method="post">
            <table class="table">
                <tr class="bt">
                    <th>ID</th>
                    <th>名称</th>
                    <th>图片</th>
                    <th>说明</th>
                    <th>添加时间</th>
                    <th>操作</th>
                </tr>
                <? foreach ($printGroup['list'] as $row) { ?>
                    <tr>
                        <td><?= $row->id ?></td>
                        <td><?= $row->name ?></td>
                        <td><img src="<?= $row->picture ?>" width="50"></td>
                        <td><?= nl2br($row->remark) ?></td>
                        <td><?= $row->created_at ?></td>
                        <td>
                            <a href="<?= url("printGroup/shopList/?id={$row->id}&page={$_GET['page']}") ?>">店铺列表</a>
                            <a href="<?= url("printGroup/edit/?id={$row->id}&page={$_GET['page']}") ?>">修改</a>
                            <a href="<?= url("printGroup/delete/?id={$row->id}&page={$_GET['page']}") ?>"
                               onclick="return confirm('确定要删除吗？')">删除</a>
                        </td>
                    </tr>
                <? } ?>
            </table>
        </form>
    </div>
    <? if (empty($printGroup['total'])) {
        echo "无记录！";
    } else {
        echo $printGroup['page'];
    } ?>
<? elseif ($this->func == 'add' || $this->func == 'edit') :  ?>
    <script src="/plugin/js/ajaxfileupload.js?111"></script>
    <div class="main_title">
        <span>管理</span><? if ($this->func == 'add') { ?>新增<? } else { ?>编辑<? } ?>
        <a href="<?= url('printGroup') ?>" class="but1">返回列表</a>
    </div>
    <div class="main_content">
        <form method="post">
            <table class="table_from">
                <tr><td>名称：</td><td><input type="text" name="name"  value="<?=$group->name?>"></td></tr>
                <tr><td>图片：</td><td>
                        <input type="hidden" name="picture" id="picture"
                               value="<?=$group->picture?>"/>
						<span id="upload_span_picture">
                            <? if ($group->picture != '') { ?>
                                <a href="<?= $group->picture ?>" target="_blank"><img
                                        src="<?= $group->picture ?>" align="absmiddle" width="100"/></a>
                            <? } ?>
                        </span>
                        <div class="upload-upimg">
                            <span class="_upload_f">上传文件</span>
                            <input type="file" id="upload_picture" name="files"
                                   onchange="upload_image('picture','shop')"/>
                        </div>
                    </td></tr>
                <tr><td>介绍：</td><td><textarea name="remark" cols="50" rows="5"><?=$group->remark?></textarea></td></tr>
                <tr><td></td><td>
                        <input type="submit" value="保存">
                        <input type="button" value="返回" onclick="window.history.go(-1)"></td></tr>
            </table>
        </form>
    </div>
<?php elseif($this->func=='shopList') : ?>
    <div class="main_title">
        <span>管理</span>列表 <a href="<?= url('printGroup') ?>" class="but1">返回列表</a>
    </div>
    <div class="main_content">
        <table class="table">
            <tr>
                <th>ID</th>
                <th>店铺ID/名称</th>
                <th>图片</th>
                <th>介绍</th>
                <th>添加时间</th>
                <th>分组时间</th>
                <th></th>
            </tr>
            <?
            foreach ($list as $item) {
                $shop=$item->PrintShop();
                if(! $shop->is_exist){continue;}
                ?>
                <tr>
                    <td><?= $item->id ?></td>
                    <td><?= $shop->id ?>/<?= $shop->name ?></td>
                    <td><img src="<?= $shop->picture ?>" width="50"></td>
                    <td class="fl"><?= nl2br($shop->remark) ?></td>
                    <td><?= $shop->created_at ?></td>
                    <td><?= $item->created_at ?></td>
                    <td><a href="<?= url("printGroup/shopListDel/?id={$item->id}") ?>" onclick="return confirm('确定要移除吗？')">移除</a></td>
                </tr>
            <? } ?>
        </table>
        <? if (empty($list)) {
            echo "无记录！";
        }?>
    </div>    
<? endif; ?>
<?php require 'footer.php'; ?>