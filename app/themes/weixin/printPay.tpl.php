<?php require 'header.php'; ?>
    <div class="header_tit">请填写收件人信息：</div>
    <table class="table_from">
        <tr>
            <td width="13rem">收货人：</td>
            <td><input type="text" id="name"><span></span></td>
        </tr>
        <tr>
            <td>电话：</td>
            <td><input type="text" id="tel"><span></span></td>
        </tr>
        <tr>
            <td>地址：</td>
            <td><input type="text" id="address"><span></span></td>
        </tr>
    </table>
    <div align="center"><input type="button" value="选择地址" id="btnAddress" class="but1" style="width: 80%"></div>
    <br>
    <table width="100%">
        <tr>
            <td style="font-size: 3rem">总计：<span style=" color: #ff8000">￥<?= $task->money ?></span></td>
            <td align="right"><input type="button" value="确定支付" id="butPay" class="submit"></td>
        </tr>
    </table>

    <script src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" charset="utf-8">
        wx.config(<?=$config?>);
        wx.ready(function () {
            $("#butPay").click(function () {
                if (validate_address()) {
                    $.post("/index.php/weixin/saveAddress/?task_id=" +<?=$task->id?>, {
                        'name': $('#name').val(),
                        'tel': $('#tel').val(),
                        'address': $('#address').val()
                    }, function (str) {
                    });
                } else {
                    return;
                }
                wx.chooseWXPay({
                    timestamp: '<?=$pay['timestamp']?>',
                    nonceStr: '<?=$pay['nonceStr']?>',
                    package: '<?=$pay['package']?>',
                    signType: 'MD5',
                    paySign: '<?=$pay['paySign']?>',
                    success: function (res) {
                        alert('支付成功！');
                        window.location = '/index.php/weixin/orderList/';
                    }
                });
            });
            $('#btnAddress').click(function () {
                wx.openAddress({
                    success: function (res) {
                        $('#name').val(res.userName);
                        $('#tel').val(res.telNumber);
                        $('#address').val(res.provinceName + res.cityName + res.countryName + res.detailInfo);
                        validate_address();
                    },
                    cancel: function (res) {
                        alert('用户取消拉出地址');
                    },
                    fail: function (res) {
                        alert(JSON.stringify(res));
                    }
                });
            });
        });

        function validate_address() {
            var flag = true;
            if ($('#name').val() == '') {
                $('#name').next('span').html('<font style="color:#f00">姓名不能为空!</font>');
                flag = false;
            } else {
                $('#name').next('span').html('');
            }
            if ($('#tel').val() == '') {
                $('#tel').next('span').html('<font style="color:#f00">电话不能为空!</font>');
                flag = false;
            } else {
                $('#tel').next('span').html('');
            }
            if ($('#address').val() == '') {
                $('#address').next('span').html('<font style="color:#f00">地址不能为空!</font>');
                flag = false;
            } else {
                $('#address').next('span').html('');
            }
            return flag;
        }
    </script>
<?php require 'footer.php'; ?>