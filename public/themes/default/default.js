function nav(obj,childUl,childBox){
    var box = $(obj);
    var oLi = $("li",childUl);
    var oDiv = $(childBox);
    function showTab(){
        oLi.each(function(i){
            if($(oLi[i]).hasClass("active"))
            {
                oDiv.addClass("hide");
                $(oDiv[i]).removeClass("hide");
            }
        })
    }
    showTab();
    oLi.mouseenter(function(){
        var index = oLi.index(this);
        $(oLi[index]).addClass("hover2").siblings().removeClass("hover2");
        oDiv.addClass("hide");
        $(oDiv[index]).removeClass("hide");
    })
    box.mouseleave(function(){
        oLi.removeClass("hover2");
        showTab()
    })
}
function index_js()
{
    $(document).ready(function(){
        nav('.nav','.nav-list','.navlist');
        $(".scrollloading").scrollLoading();
    });
}
(function(win,doc){
    var s = doc.createElement("script"), h = doc.getElementsByTagName("head")[0];
    if (!win.alimamatk_show) {
        s.charset = "gbk";
        s.async = true;
        s.src = "http://a.alimama.cn/tkapi.js";
        h.insertBefore(s, h.firstChild);
    };
    var o = {
        pid: "mm_17793662_5638292_17934415",/*推广单元ID，用于区分不同的推广渠道*/
        appkey: "",/*通过TOP平台申请的appkey，设置后引导成交会关联appkey*/
        unid: "",/*自定义统计字段*/
        type: "click" /* click 组件的入口标志 （使用click组件必设）*/
    };
    win.alimamatk_onload = win.alimamatk_onload || [];
    win.alimamatk_onload.push(o);
})(window,document);





