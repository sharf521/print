<?php require 'header.php'; ?>
    <script type="text/javascript" src="/data/js/category.js?<?= rand(1, 100) ?>"></script>
    <script>
        function comupload_success(path, type) {
            $('#' + type).val(path);
            var _str = "<a href='" + path + "' target='_blank'><img src='" + path + "' height='100'/></a>";
            $("#span_" + type).html(_str);
        }
    </script>
<? if ($this->func == 'index') { ?>
    <div class="main_title">
        <span>文章管理</span>列表
        <a href="<?= url('article/add') ?>" class="but1">新增</a>
    </div>
    <form method="get">
        <div class="search">
            文章类型：<select name="categorypath">
                <option value="">&nbsp;&nbsp;请选择</option>
                <?= $cates ?>
            </select>


            关键字：<input type="text" name="keyword" value="<?= $_GET['keyword'] ?>"/>
            <input type="submit" class="but2" value="查询"/>
        </div>
    </form>
    <div class="main_content">
        <form method="post">
            <table class="table">
                <tr class="bt">
                    <th>ID</th>
                    <th>分类</th>
                    <th>标题</th>
                    <th>添加时间</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                <? $subsite[0] = "全部分站";
                foreach ($result['list'] as $row) {
                    ?>
                    <tr>
                        <td><?= $row->id ?></td>
                        <td><?= $row->Category()->name ?></td>
                        <td class="l"><?= $row->title ?></td>
                        <td><?= $row->addtime ?></td>
                        <td><? if ($row->status == '1') {
                                echo '显示';
                            } else {
                                echo '隐藏';
                            } ?></td>
                        <td>
                            <a href="<?= url("article/change/?id={$row->id}&page={$_GET['page']}") ?>"><?= ($row->status == '1') ? '隐藏' : '显示' ?></a>
                            <a href="<?= url("article/edit/?id={$row->id}&page={$_GET['page']}") ?>">修改</a>
                            <a href="<?= url("article/delete/?id={$row->id}&page={$_GET['page']}") ?>"
                               onclick="return confirm('确定要删除吗？')">删除</a>
                        </td>
                    </tr>
                <? } ?>
            </table>
            <? if (empty($result['total'])) {
                echo "无记录！";
            } else {
                ?>
                <?
                echo $result['page'];
            } ?>
        </form>
    </div>

<? } elseif ($this->func == 'add' || $this->func == 'edit') { ?>
    <div class="main_title">
        <span>文章管理</span><? if ($this->func == 'add') { ?>新增<? } else { ?>编辑<? } ?>
        <?= $this->anchor('article', '列表', 'class="but1"'); ?>
    </div>
    <script src="/plugin/js/ajaxfileupload.js"></script>
    <div class="main_content">
        <form method="post">
            <input type="hidden" name="id" value="<?= $row->id ?>"/>
            <table class="table_from">
                <tr>
                    <td>标题：</td>
                    <td><input type="text" name="title" value="<?= $row->title ?>" size="50"/></td>
                </tr>
                <tr>
                    <td>文章类型：</td>
                    <td>
                        <div id="div_category">
                            <select name="categoryid[]" id="category1" class="multiple" multiple="multiple"
                                    onchange="getsel(1,this.value)">
                                <? foreach ($cates as $var) { ?>
                                    <option value='<?= $var['id'] ?>' <? if ($var['id'] == $row->category_id) {
                                        echo 'selected';
                                    } ?>><?= $var['name'] ?></option>
                                <? } ?>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>显示图片：</td>
                    <td>
                        <input type="hidden" name="picture" id="article" value="<?= $row->picture ?>"/>
                <span id="upload_span_article">
                    <? if ($row->picture != '') { ?>
                        <a href="<?= $row->picture; ?>" target="_blank"><img src="<?= $row->picture ?>"
                                                                             align="absmiddle" width="100"/></a>
                    <? } ?>
                </span>
                        <div class="upload-upimg">
                            <span class="_upload_f">上传图片</span>
                            <input type="file" id="upload_article" name="files"
                                   onchange="upload_image('article','article')"/>
                        </div>
                    </td>
                </tr>


                <tr>
                    <td valign="top">内容：</td>
                    <td><? ueditor(array('value' => $row->content)); ?></td>
                </tr>
                <tr>
                    <td>状态：</td>
                    <td><label><input type="radio" name="status" value="1" checked="checked"/>显示</label>
                        <label><input type="radio" name="status" value="0" <? if ($row->status == '0') {
                                echo 'checked';
                            } ?>/>隐藏</label>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" class="but3" value="保存"/>
                        <input type="button" class="but3" value="返回" onclick="window.history.go(-1)"/></td>
                </tr>
            </table>
        </form>
    </div>

    <script language="javascript">
        $.ajaxSetup({async: false});
        <?=$row->sel?>
    </script>
<? } ?>
<?php require 'footer.php'; ?>