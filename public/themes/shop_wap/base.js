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

        $('#bottom_buy_box .buy_box_title').find('i').on('click',function(){
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
            input.val(num+1);
        });
    });
}