<?php require 'header.php';?>
<div class="weui-cells__title">表单</div>
<form method="post">
    <div class="weui-cells weui-cells_form">
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
                            <div class="weui-uploader__input-box">
                                <input id="uploaderInput" name="file" class="weui-uploader__input" type="file" accept="image/*" onchange="upload()"/>
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
    <script src="/plugin/js/ajaxfileupload.js?111"></script>
<script type="text/javascript">
    function upload() {
        $.ajaxFileUpload({
            url:'/index.php/upload/save?type=goods',
            fileElementId :'uploaderInput',
            dataType:'json',
            success: function (res,status){
                if(res.code == '0'){
                    var path=res.url+'?'+Math.random();
                    //$('#'+id).val(path);
                    var _str='<li class="weui-uploader__file goods_add_uploaderLi" style="background-image:url('+path+')">' +
                        '<i class="weui-icon-cancel" onclick="del_img(this)"></i></li>';
                    $("#uploaderFiles").append(_str);
                }else{
                    alert(res.msg);
                }
            },
            error: function (result, status, e){
                alert(e);
            }
        });
    }
    function del_img(o) {
        $(o).parent().remove();
    }
    $(function(){
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