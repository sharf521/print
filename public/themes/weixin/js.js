//上传图片
function upload_image(id,type)
{
    $('#upload_span_'+id).html('上传中...');
    $.ajaxFileUpload({
        url:'/index.php/plugin/ajaxFileUpload?type='+type,
        fileElementId :'upload_'+id,
        dataType:'json',
        success: function (result,status){
            if(result.status == 'success'){
                var path=result.data+'?'+Math.random();
                $('#'+id).val(path);
                var _str="<a href='"+path+"' target='_blank'><img src='"+path+"' height='100'/></a>";
                $('#upload_span_'+id).html(_str);
            }else{
                alert(result.data);
            }
        },
        error: function (result, status, e){
            alert(e);
        }
    });
    return false;
}
