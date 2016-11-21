<?php require 'header.php';?>
    <div class="m_header">
        <a class="m_header_l" href="<?=url('goods')?>"><i class="iconfont">&#xe604;</i></a>
        <a class="m_header_r"></a>
        <h1><?=$this->func == 'add'?'新增':'编辑'; ?>商品</h1>
    </div>
<form method="post" id="goods_form">
    <div class="weui-cells weui-cells_form margin_header">
        <div class="weui-cell">
            <div class="weui-cell__bd">
                <input class="weui-input" type="text" name="name" value="<?=$goods->name?>" placeholder="请输入商品名称"/>
            </div>
            <div class="weui-cell__ft">
                <i class="weui-icon-warn"></i>
            </div>
        </div>
    </div>
        <div class="weui-cells">
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <div class="weui-uploader">
                        <div class="weui-uploader__bd">
                            <ul class="weui-uploader__files" id="uploaderFiles">
                                <?
                                $imgids=',';
                                foreach ($images as $img) :
                                    $imgids.=$img->id.',';
                                    ?>
                                    <li class="weui-uploader__file goods_add_uploaderLi" style="background-image:url(<?=$img->image_url?>)"><i class='weui-icon-cancel' onclick=delGoodsImg(this,'<?=$img->id?>')></i></li>
                                <? endforeach;?>
                            </ul>
                            <input type="hidden" name="imgids" id="imgids" value="<?=$imgids?>">
                            <div class="weui-uploader__input-box">
                                <input id="uploaderInput" name="file" class="weui-uploader__input" type="file" accept="image/*" onchange="uploadGoodsImg()"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <input type="hidden" name="is_have_spec" id="is_have_spec" value="<?=$goods->is_have_spec?>">
    <div class="weui-cells weui-cells_form" id="specBox_no">
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">价格</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" name="price" type="number" onkeyup="value=value.replace(/[^0-9.]/g,'')" placeholder="请输入价格" value="<?=$goods->price?>"/>
            </div>
            <div class="weui-cell__ft">
                <i class="weui-icon-warn"></i>
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">库存</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="number" name="stock_count" onkeyup="value=value.replace(/[^0-9]/g,'')" placeholder="请输入库存数" value="<?=$goods->stock_count?>"/>
            </div>
            <div class="weui-cell__ft">
                <i class="weui-icon-warn"></i>
            </div>
        </div>
    </div>
    <div id="specBox">
        <? if($goods->is_have_spec) : ?>
            <script>
                $(function() {
                    $('#specBox_no').hide();
                });
            </script>
            <? foreach ($specs as $spec) : ?>
                <div class="spec_item">
                    <input type="hidden" name="spec_id[]" value="<?=$spec->id?>" />
                    <div class="weui-cell">
                        <label class="weui-label">规格</label>
                        <input class="weui-input" type="text" name="spec_1[]" value="<?=$spec->spec_1?>" placeholder="输入商品规格，如颜色、尺寸"/>
                    </div>
                    <div class="weui-cell" style="position: relative">
                        <label class="weui-label">价格</label>
                        <input class="weui-input" type="number" name="price[]" value="<?=$spec->price?>" onkeyup="value=value.replace(/[^0-9.]/g,'')" placeholder="请输入价格"/>
                        <i class="spec_del weui-icon-cancel"></i>
                    </div>
                    <div class="weui-cell">
                        <label class="weui-label">库存</label>
                        <input class="weui-input" type="number" name="stock_count[]" value="<?=$spec->stock_count?>" onkeyup="value=value.replace(/[^0-9]/g,'')" placeholder="请输入库存数"/>
                    </div>
                </div>
            <? endforeach;?>
        <? endif;?>
    </div>
    <div id="addSpecBtn">+ 添加商品规格</div>
    <div class="weui-cells weui-cells_form">
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">运费</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="number" name="shipping_fee" onkeyup="value=value.replace(/[^0-9.]/g,'')" value="<?=$goods->shipping_fee?>"/>
            </div>
        </div>
    </div>
    <div class="weui-cells__title">介绍</div>
    <div class="weui-cells weui-cells_form">
        <div class="weui-cell">
            <div class="weui-cell__bd">
                <textarea class="weui-textarea" name="content" id="content" placeholder="请输入详细介绍" rows="3"><?=$GoodsData->content?></textarea>
            </div>
            <div class="weui-cell__ft">
                <i class="weui-icon-warn"></i>
            </div>
        </div>
    </div>

    <div class="weui-cells weui-cells_form">
        <div class="weui-cell weui-cell_select weui-cell_select-after">
            <div class="weui-cell__hd"><label class="weui-label">分类</label></div>
            <div class="weui-cell__bd">
                <select name="shop_category" class="weui-select">
                    <option value="0" selected>默认</option>
                    <? foreach ($cates as $cate) :?>
                    <option value="<?=$cate->id?>" <? if($cate->id==$goods->shop_cateid){echo 'selected';}?>><?=$cate->name?></option>
                    <? endforeach;?>
                </select>
            </div>
            <div class="weui-cell__ft">
                <i class="weui-icon-warn"></i>
            </div>
        </div>
    </div>
    <div class="weui-btn-area">
        <input class="weui-btn weui-btn_primary" type="submit" value="保存">
    </div>
</form>
    <script src="/plugin/js/ajaxfileupload.js?111"></script>
    <script type="text/javascript">
        goodsAdd_js();
    </script>
<div id="spec_item_hide" class="hide">
    <div class="spec_item">
        <div class="weui-cell">
            <label class="weui-label">规格</label>
            <input class="weui-input" type="text" name="spec_1[]" placeholder="输入商品规格，如颜色、尺寸"/>
        </div>
        <div class="weui-cell" style="position: relative">
            <label class="weui-label">价格</label>
            <input class="weui-input" type="number" name="price[]" value="0.00" onkeyup="value=value.replace(/[^0-9.]/g,'')" placeholder="请输入价格"/>
            <i class="spec_del weui-icon-cancel"></i>
        </div>
        <div class="weui-cell">
            <label class="weui-label">库存</label>
            <input class="weui-input" type="number" name="stock_count[]" value="0" onkeyup="value=value.replace(/[^0-9]/g,'')" placeholder="请输入库存数"/>
        </div>
    </div>
</div>
<?php require 'footer.php';?>