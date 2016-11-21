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
    $('.weui-uploader__input-box').css('border','1px solid #d9d9d9');
}
function delGoodsImg(o,id) {
    $.get("/index.php/upload/del?type=goods&id=" + id, {}, function (str) { });
    var ids = $('#imgids').val();
    $('#imgids').val(ids.replace(','+id+',',','));
    $(o).parents('li').remove();
}
function goodsAdd_js()
{
    $(function(){
        $('#addSpecBtn').on("click",function(e){
            $('#is_have_spec').val('1');
            $('#specBox_no').hide();
            var tem=$('#spec_item_hide .spec_item').clone(true);
            $('#specBox').append(tem);
        });
        $('.spec_item .spec_del').on("click",function(e){
            $(this).parent().parent('.spec_item').remove();
            if($('#specBox input').length==0){
                $('#is_have_spec').val('0');
                $('#specBox_no').show();
            }
        });

        $("#goods_form").on('submit',function(){
            if(form_validate(this,'name')==false){
                return false;
            }
            if(form_validate(this,'imgids')==false){
                return false;
            }
            if($('#is_have_spec').val()=='0'){
                if(form_validate(this,'price')==false){
                    return false;
                }
                if(form_validate(this,'stock_count')==false){
                    return false;
                }
            }else{
                var _tag=true;
                $('#specBox input').each(function(i){
                    if($(this).val()==''){
                        $(this).next('.weui-cell__ft').remove();
                        $(this).after('<div class="weui-cell__ft"><i class="weui-icon-warn"></i></div>');
                        $(this).focus();
                        _tag=false;
                    }else{
                        $(this).next('.weui-cell__ft').remove();
                    }
                });
                if(_tag==false){
                    return false;
                }
            }
            if($('#content').val()==''){
                $('#content').parents('.weui-cell').addClass('weui-cell_warn');
                $('#content').focus();
                return false;
            }else{
                $('#content').parents('.weui-cell').removeClass('weui-cell_warn');
            }
            return true;
        });
        function form_validate(form,oName){
            var o=$(form).find("input[name="+oName+"]");
            if(oName=='name' || oName=='price' || oName=='stock_count'){
                if(o.val()==''){
                    o.parents('.weui-cell').addClass('weui-cell_warn');
                    o.focus();
                    return false;
                }else{
                    o.parents('.weui-cell').removeClass('weui-cell_warn');
                }
            }
            if(oName=='imgids'){
                if(o.val()==''){
                    o.next('.weui-uploader__input-box').css('border','1px solid #f00');
                    return false;
                }
            }
        }
    });
}
///goods js end
