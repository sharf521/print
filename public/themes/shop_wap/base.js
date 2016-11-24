function showBottomBuyBox() {
    $('.weui-mask').show();
    $('#bottom_buy_box').slideDown(150);
}
function goods_detail_js()
{
    $(function(){
        var mySwiper = new Swiper('.swiper-container',{
            loop : true,
            autoplay:4800,
            autoplayDisableOnInteraction : false,
            pagination : '.swiper-pagination',
            paginationClickable :true,
        });

        $('.weui-mask').on('click',function(){
            hideBuyBox();
        });

        $('#bottom_buy_box dt').find('i').on('click',function(){
            hideBuyBox();
        });

        function hideBuyBox(){
            $('.weui-mask').hide();
            $('#bottom_buy_box').slideUp(150);
        }

        $('#bottom_buy_box .btn-reduce').on('click',function(){
            var input=$(this).parent().find('input');
            var num=Number(input.val());
            if(num>1){
                input.val(num-1);
            }
        });
        $('#bottom_buy_box .btn-add').on('click', function () {
            var input=$(this).parent().find('input');
            var num=Number(input.val());
            if(num < Number($('#goods_stock_count').html())){
                input.val(num+1);
            }
        });
    });
}
function spec(id, spec1, spec2, price, stock) {
    this.id = id;
    this.spec1 = spec1;
    this.spec2 = spec2;
    this.price = price;
    this.stock = stock;
}
function GoodsSpec(specs)
{
    this.spec1_name='';
    this.spec2_name='';
    this.specQty=1;
    this.spec1=new Array();
    for (var x in specs) {
        var spec = specs[x];
        if($.inArray(spec.spec1,this.spec1)==-1){
            this.spec1.push(spec.spec1);
        }
        if(this.specQty==1 && spec.spec2!=''){
            this.specQty=2;
        }
    }
    this.initSpec2=function(){
        if(this.specQty==2){
            $("#specBox_2").html('');
            this.spec2_name='';
            for (var x in specs){
                if(specs[x].spec1==this.spec1_name){
                    $("#specBox_2").append("<span onclick='selectSpec(2,this)'>" + specs[x].spec2 + "</span>");
                }
            }
        }
    };
    var tag=false;
    for (var x in this.spec1){
        var _c='';
        if(tag==false) {
            tag = true;
            _c = 'active';
            this.spec1_name=this.spec1[x];
            if(this.specQty==2){
                this.initSpec2();
            }
        }
        $("#specBox_1").append("<span class='"+_c+"' onclick='selectSpec(1,this)'>" + this.spec1[x] + "</span>");
    };
    this.getSpec=function(){
        for (var x in specs){
            var spec=specs[x];
            if(this.specQty==1){
                if(spec.spec1==this.spec1_name){
                    return spec;
                    break;
                }
            }else{
                if(spec.spec1==this.spec1_name && spec.spec2==this.spec2_name){
                    return spec;
                    break;
                }
            }
        }
        return null;
    };
    this.setFormValue=function(){
        var spec=this.getSpec();
        if(spec!=null){
            $('#spec_id').val(spec.id);
            $('#goods_price').html(spec.price);
            $('#goods_stock_count').html(spec.stock);
        }
    };
}
function selectSpec(type,obj){
    var obj=$(obj);
    obj.addClass('active').siblings().removeClass('active');
    if(type==1){
        goodsSpec.spec1_name=obj.html();
        goodsSpec.initSpec2();
    }else{
        goodsSpec.spec2_name=obj.html();
    }
    goodsSpec.setFormValue();
}