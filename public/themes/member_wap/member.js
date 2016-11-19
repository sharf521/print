//goods js
function uploadGoodsImg() {
    var lay=layer.open({
        type: 2
        ,content: '上传中'
    });
    $.ajaxFileUpload({
        url:'/index.php/upload/save?type=goods',
        fileElementId :'uploaderInput',
        dataType:'json',
        success: function (res,status){
            layer.close(lay);
            if(res.code == '0'){
                var imgId=res.id;
                var path=res.url+'?'+Math.random();
                $('#imgids').val($('#imgids').val()+imgId+',');
                var _str='<li class="weui-uploader__file goods_add_uploaderLi" style="background-image:url('+path+')">' +
                    "<i class='weui-icon-cancel' onclick=delGoodsImg(this,'"+imgId+"')></i></li>";
                $("#uploaderFiles").append(_str);
            }else{
                alert(res.msg);
            }
        },
        error: function (result, status, e){
            layer.close(lay);
            alert(e);
        }
    });
}
function delGoodsImg(o,id) {
    $.get("/index.php/upload/del?type=goods&id=" + id, {}, function (str) { });
    $(o).parent().remove();
}
function goodsAdd_js()
{
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
}
///goods js end
