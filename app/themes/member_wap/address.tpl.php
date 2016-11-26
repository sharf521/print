<?php require 'header.php';?>
<? if($this->func=='index') : ?>
    <div class="m_header">
        <a class="m_header_l" href="<?=url('')?>"><i class="iconfont">&#xe604;</i></a>
        <a class="m_header_r"></a>
        <h1>收货地址</h1>
    </div>
    <div class="address_list margin_header">
        <?
        if(count($result)==0){
            echo '<div class="noadres"><p>暂无收货地址！</p></div>';
        }
        ?>
        <? if($this->redirect_url!='') : ?>
        <ul id="addrList">
            <? foreach($result as $adds) : ?>
                <li data_id="<?=$adds->id?>">
                    <? if($adds->is_default) : ?>
                        <span class="madres madrati">默认地址</span>
                    <? endif;?>
                    <p><?=$adds->region_name?> <?=$adds->address?></p>
                    <p><?=$adds->name?> <?=$adds->phone?></p>
                </li>
            <? endforeach; ?>
        </ul>
        <? else : ?>
            <ul>
                <? foreach($result as $adds) : ?>
                    <li>
                        <? if($adds->is_default) : ?>
                            <span class="madres madrati">默认地址</span>
                        <? else : ?>
                            <a class="madres" href="<?=url("address/setDefault/?id={$adds->id}")?>">设为默认</a>
                        <? endif;?>
                        <span class="maddel" onclick="address_del(<?=$adds->id?>)">删除</span>
                        <p><?=$adds->region_name?> <?=$adds->address?></p>
                        <p><?=$adds->name?> <?=$adds->phone?></p>
                    </li>
                <? endforeach; ?>
            </ul>
        <? endif;?>
    </div>
    <div class="weui-btn-area">
        <a class="weui-btn weui-btn_primary" href="<?=url('address/add/?redirect_url='.$this->self_url)?>">添加收货地址</a>
    </div>
    <script>
        $(function(){
            $('#addrList li').on('click',function(){
                var id=$(this).attr('data_id');
                window.location="<?=$this->redirect_url?>&address_id="+id;
            })
        });
        function address_del(id){
            layer.open({
                content: '您确定要删除吗？'
                ,btn: ['删除', '取消']
                ,yes: function(index){
                    location.href='<?=url("address/del/?id=")?>'+id;
                    layer.close(index);
                }
            });
        }
    </script>
<? elseif($this->func=='add') : ?>
    <div class="m_header">
        <a class="m_header_l" href="javascript:history.go(-1)"><i class="iconfont">&#xe604;</i></a>
        <a class="m_header_r"></a>
        <h1>添加收货地址</h1>
    </div>
    <div class="address_form">
        <form id="contactform" method="post">
            <table class="mad_fmtab" width="100%">
                <tbody>
                <tr>
                    <th colspan="2">添加收货地址</th>
                </tr>
                <tr>
                    <td width="70">省份：</td>
                    <td><select id="s_province" name="province"></select></td>
                </tr>
                <tr>
                    <td>城市：</td>
                    <td><select id="s_city" name="city" ></select></td>
                </tr>
                <tr>
                    <td>地区：</td>
                    <td><select id="s_county" name="county"></select></td>
                </tr>
                <tr>
                    <td>详细地址：</td>
                    <td><textarea role="2" name="address" placeholder="不需要重复填写省/市/县"></textarea><span></span></td>
                </tr>
                <tr>
                    <td>收货人：</td>
                    <td><input name="name" placeholder="请填写真实姓名" type="text"><span></span></td>
                </tr>
                <tr>
                    <td>联系电话：</td>
                    <td>
                        <input name="phone" type="tel" onkeyup="value=value.replace(/[^0-9.]/g,'')" placeholder="手机号码必须填" value=""><span></span></td>
                </tr>
                <tr>
                    <td colspan="2"><input type="submit" value="添加" class="adctbuton"></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
    <!--地区选择-->
    <script class="resources library" src="/plugin/js/area.js" type="text/javascript"></script>
    <script type="text/javascript">_init_area();</script>
    <script src="/plugin/js/jquery.validation.min.js"></script>
    <script>
        $(document).ready(function(){
            $('#contactform').validate({
                onkeyup: false,
                errorPlacement: function(error, element){
                    element.nextAll('span').first().after(error);
                },
                submitHandler:function(form){
                    ajaxpost('contactform', '', '', 'onerror');
                },
                rules: {
                    address: {
                        required: true,
                    },
                    name: {
                        required: true,
                    },
                    phone: {
                        required: true,
                        rangelength:[11,11],
                    },
                },
                messages: {
                    address: {
                        required: '* 地址不能为空',
                    },
                    name: {
                        required: '* 收货人不能为空',
                    },
                    phone: {
                        required: '* 联系电话不能为空',
                        rangelength: '* 请填写正确的手机号码',
                    },
                }
            });
        });
    </script>
<? endif;?>
<?php require 'footer.php';?>