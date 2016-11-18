<?php require 'header.php';?>
<div class="weui-cells__title">表单</div>
<form method="post">
    <div class="weui-cells weui-cells_form">
        <div class="page__bd">
            <div class="weui-gallery" id="gallery">
                <span class="weui-gallery__img" id="galleryImg"></span>
                <div class="weui-gallery__opr">
                    <a href="javascript:" class="weui-gallery__del">
                        <i class="weui-icon-delete weui-icon_gallery-delete"></i>
                    </a>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <div class="weui-uploader">
                        <div class="weui-uploader__hd">
                            <p class="weui-uploader__title">图片上传</p>
                            <div class="weui-uploader__info"></div>
                        </div>
                        <div class="weui-uploader__bd">
                            <ul class="weui-uploader__files" id="uploaderFiles">
                                <li class="weui-uploader__file weui-uploader__file_status" style="background-image:url(./images/pic_160.png)">
                                    <div class="weui-uploader__file-content">
                                        <i class="weui-icon-warn"></i>
                                    </div>
                                </li>
                            </ul>
                            <div class="weui-uploader__input-box">
                                <input id="uploaderInput" class="weui-uploader__input" type="file" accept="image/*" multiple />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">名称</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="text"  placeholder="请输入商品名称"/>
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
                <input class="weui-input" type="number" onkeyup="value=value.replace(/[^0-9.]/g,'')" value="0.00"/>
            </div>
        </div>
    </div>
    <div class="weui-cells__title">介绍</div>
    <div class="weui-cells weui-cells_form">
        <div class="weui-cell">
            <div class="weui-cell__bd">
                <textarea class="weui-textarea" placeholder="请输入详细介绍" rows="3"></textarea>
            </div>
        </div>
        <div class="weui-btn-area">
            <a class="weui-btn weui-btn_primary" href="javascript:" id="showTooltips">确定</a>
        </div>
    </div>
</form>
<script type="text/javascript">
    $(function(){
        var tmpl = '<li class="weui-uploader__file" style="background-image:url(#url#)"></li>',
            $gallery = $("#gallery"), $galleryImg = $("#galleryImg"),
            $uploaderInput = $("#uploaderInput"),
            $uploaderFiles = $("#uploaderFiles")
            ;

        $uploaderInput.on("change", function(e){
            var src, url = window.URL || window.webkitURL || window.mozURL, files = e.target.files;
            for (var i = 0, len = files.length; i < len; ++i) {
                var file = files[i];

                if (url) {
                    src = url.createObjectURL(file);
                } else {
                    src = e.target.result;
                }

                $uploaderFiles.append($(tmpl.replace('#url#', src)));
            }
        });
        $uploaderFiles.on("click", "li", function(){
            $galleryImg.attr("style", this.getAttribute("style"));
            $gallery.fadeIn(100);
        });
        $gallery.on("click", function(){
            $gallery.fadeOut(100);
        });


        $('#addSpecBtn').on("click",function(e){
            $('#nospec_price').hide();
            $('#nospec_stock_count').hide();
            var tem=$('#spec_item_hide .spec_item').clone(true);
            $('#specBox').append(tem);
        });
        $('.spec_item .spec_del').on("click",function(e){
            $(this).parent().parent('.spec_item').remove();
            if($('#specBox').html()==''){
                $('#nospec_price').show();
                $('#nospec_stock_count').show();
            }
        });
    });
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