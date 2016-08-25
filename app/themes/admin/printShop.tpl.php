<?php require 'header.php'; ?>
<?php if ($this->func == 'index') : ?>
    <div class="main_title">
        <span>店铺管理</span>
    </div>
    <form method="get">
        <div class="search">
            关键字：<input name="q" value="<?=$_GET['q']?>">
            时间：<input  name="starttime" type="text" value="<?=$_GET['starttime']?>" onClick="javascript:WdatePicker();" class="Wdate">
            到
            <input  name="endtime" type="text" value="<?=$_GET['endtime']?>" onClick="javascript:WdatePicker();" class="Wdate">
            <input type="submit" class="but2" value="查询"/>
        </div>
    </form>
    <form method="post">
        <table class="table">
            <tr>
                <th></th>
                <th>ID</th>
                <th>user_id</th>
                <th>昵称</th>
                <th></th>
                <th>名称</th>
                <th>图片</th>
                <th>地址</th>
                <th>介绍</th>
                <th>添加时间</th>
                <th>操作</th>
            </tr>
            <?
            foreach ($printShop['list'] as $item) {
                $user=$item->User();
                ?>
                <tr>
                    <td><input type="checkbox" name="id[]" value="<?=$item->id?>"></td>
                    <td><?= $item->id ?></td>
                    <td><?=$user->id?></td>
                    <td><?=$user->nickname?></td>
                    <td><img src="<?=substr($user->headimgurl,0,-1)?>64" width="50"></td>
                    <td><?= $item->name ?></td>
                    <td><img src="<?= $item->picture ?>" width="50"></td>
                    <td class="fl"><?=$item->address ?></td>
                    <td class="fl"><?= nl2br($item->remark) ?></td>
                    <td><?= $item->created_at ?></td>
                    <td><a href="<?= url("printShop/edit/?id={$item->id}&page={$_GET['page']}") ?>">编辑</a>
                        <a href="<?= url("printShop/delete/?id={$item->id}&page={$_GET['page']}") ?>" onclick="return confirm('确定要删除吗？')">删除</a>
                    </td>
                </tr>
            <? } ?>
            <tr><td colspan="11" class="l"> 添加到：
                <select name="group_id">
                    <? foreach ($printGroup as $group) : ?>
                        <option value="<?=$group->id?>"><?=$group->name?></option>
                    <? endforeach;?>
                </select> <input type="submit" value="保存">
                </td></tr>
        </table>
    </form>
    <? if (empty($printShop['total'])) {
        echo "无记录！";
    } else {
        echo $printShop['page'];
    } ?>
<? elseif ($this->func=='edit') : ?>
    <script src="/plugin/js/ajaxfileupload.js?111"></script>
    <div class="main_content">
        <div class="main_title">
            <span>店铺管理</span>
        </div>
        <form method="post">
            <table class="table_from">
                <tr><td>名称：</td><td><input type="text" name="name" size="50"  value="<?=$shop->name?>"></td></tr>
                <tr><td>图片：</td><td>
                        <input type="hidden" name="picture" id="picture" value="<?=$shop->picture?>"/>
						<span id="upload_span_picture">
                            <? if ($shop->picture != '') { ?>
                                <a href="<?= $shop->picture ?>" target="_blank"><img
                                        src="<?= $shop->picture ?>" align="absmiddle" width="100"/></a>
                            <? } ?>
                        </span>
                        <div class="upload-upimg">
                            <span class="_upload_f">上传文件</span>
                            <input type="file" id="upload_picture" name="files"
                                   onchange="upload_image('picture','shop')"/>
                        </div>
                    </td></tr>
                <tr><td>介绍：</td><td><textarea name="remark" cols="50" rows="5"><?=$shop->remark?></textarea></td></tr>
                <tr><td>位置：</td><td><input type="text" name="address" size="50"  value="<?=$shop->address?>"></td></tr>
                <tr><td></td><td>
                        <input type="submit" value="保存">
                        <input type="button" value="返回" onclick="window.location='<?=url("printShop/?page={$_GET['page']}")?>'"></td></tr>
            </table>
        </form>
    </div>
<? endif ?>
<?php require 'footer.php'; ?>
