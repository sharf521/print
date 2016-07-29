function changetype(type) {
    if (type == 1) {
        document.getElementById("formpay").action = "/pay/recharge";
        document.getElementById("formpay").target='_blank';
        document.getElementById("xianxia").style.display = "none";
        document.getElementById("xianxiabz").style.display = "none";
        document.getElementById("xianshang").style.display = "";
    }
    if (type == 2) {
        document.getElementById("formpay").action = "";
        document.getElementById("formpay").target='';
        document.getElementById("xianshang").style.display = "none";
        document.getElementById("xianxia").style.display = "";
        document.getElementById("xianxiabz").style.display = "";
    }
}
function card() {
    if (document.getElementById("money").value == "") {
        alert("充值金额不能为空!");
        return false;
    }
    if (document.getElementById("type1").checked == true) {
        if (document.getElementById("money").value < 50) {
            alert("充值金额不能小于50元");
            return false;
        }
    }
    if (document.getElementById("type2").checked == true) {
        if (document.getElementById("money").value < 1000) {
            alert("充值金额不能小于1000元");
            return false;
        }
        if (document.getElementById("remark").value == "") {
            alert("充值备注不能为空!");
            return false;
        }
    }
    if (document.getElementById("valicode").value == "") {
        alert("验证码不能为空!");
        return false;
    }
    return true;
}
