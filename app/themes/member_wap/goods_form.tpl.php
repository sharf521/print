<?php require 'header.php';?>
    <div class="m_header">
        <a class="m_header_l" href="<?=url('goods')?>"><i class="iconfont">&#xe604;</i></a>
        <a class="m_header_r"></a>
        <h1>添加商品</h1>
    </div>
<form method="post">
    <div class="weui-cells weui-cells_form margin_header">
        <div class="page__bd">
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <div class="weui-uploader">
                        <div class="weui-uploader__hd">
                            <p class="weui-uploader__title">图片上传</p>
                            <div class="weui-uploader__info"></div>
                        </div>
                        <div class="weui-uploader__bd">
                            <ul class="weui-uploader__files" id="uploaderFiles">

                            </ul>
                            <input type="hidden" name="imgids" id="imgids" value="">
                            <div class="weui-uploader__input-box">
                                <input id="uploaderInput" name="file" class="weui-uploader__input" type="file" accept="image/*" onchange="uploadGoodsImg()"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">名称</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="text" name="name"  placeholder="请输入商品名称"/>
            </div>
        </div>
        <div class="weui-cell" id="nospec_price">
            <div class="weui-cell__hd"><label class="weui-label">价格</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" name="price" type="number" onkeyup="value=value.replace(/[^0-9.]/g,'')" placeholder="请输入价格"/>
            </div>
        </div>
        <div class="weui-cell" id="nospec_stock_count">
            <div class="weui-cell__hd"><label class="weui-label">库存</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="number" name="stock_count" onkeyup="value=value.replace(/[^0-9]/g,'')" placeholder="请输入库存数"/>
            </div>
        </div>
    </div>
    <div id="specBox"></div>
    <div id="addSpecBtn">+ 添加商品规格</div>
    <div class="weui-cells weui-cells_form">
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">运费</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="number" name="shipping_fee" onkeyup="value=value.replace(/[^0-9.]/g,'')" value="0.00"/>
            </div>
        </div>
    </div>
    <div class="weui-cells__title">介绍</div>
    <div class="weui-cells weui-cells_form">
        <div class="weui-cell">
            <div class="weui-cell__bd">
                <textarea class="weui-textarea" name="content" placeholder="请输入详细介绍" rows="3"></textarea>
            </div>
        </div>
    </div>

    <div class="weui-cells weui-cells_form">
        <div class="weui-cell weui-cell_select weui-cell_select-after">
            <div class="weui-cell__hd"><label class="weui-label">分类</label></div>
            <div class="weui-cell__bd">
                <select name="shop_category" class="weui-select">
                    <option value="0">默认</option>
                    <? foreach ($cates as $cate) :?>
                    <option value="<?=$cate->id?>"><?=$cate->name?></option>
                    <? endforeach;?>
                </select>
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
            <input class="weui-input" type="text" name="spec_name[]" placeholder="输入商品规格，如颜色、尺寸"/>
        </div>
        <div class="weui-cell" style="position: relative">
            <label class="weui-label">价格</label>
            <input class="weui-input" type="number" name="price[]" onkeyup="value=value.replace(/[^0-9.]/g,'')" placeholder="请输入价格"/>
            <i class="spec_del weui-icon-cancel"></i>
        </div>
        <div class="weui-cell">
            <label class="weui-label">库存</label>
            <input class="weui-input" type="number" name="stock_count[]" onkeyup="value=value.replace(/[^0-9]/g,'')" placeholder="请输入库存数"/>
        </div>
    </div>
</div>
<?php require 'footer.php';?>