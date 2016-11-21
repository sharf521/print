<?php require 'header.php';?>
    <div class="m_header">
        <a class="m_header_l" href="<?=url('')?>"><i class="iconfont">&#xe604;</i></a>
        <a class="m_header_r" href="<?=url('goods/add')?>">添加</a>
        <h1>商品管理</h1>
    </div>
    <div class="my-navbar margin_header">
        <div class="my-navbar__item <? if($this->func=='index'){echo 'my-navbar__item_on';}?>">
            <a href="<?=url('goods')?>">出售中</a>
        </div>
        <div class="my-navbar__item <? if($this->func=='list_stock0'){echo 'my-navbar__item_on';}?>">
            <a href="<?=url('goods/list_stock0')?>">售罄的</a>
        </div>
        <div class="my-navbar__item <? if($this->func=='list_status2'){echo 'my-navbar__item_on';}?>">
            <a href="<?=url('goods/list_status2')?>">仓库中</a>
        </div>
    </div>

    <ul class="commoditylist_content">
        <? foreach ($result['list'] as $goods) : ?>
        <li>
            <a href="<?=url("goods/edit/?id={$goods->id}")?>">
              <span class="imgspan">
                    <img src="/themes/images/blank.gif" data-echo="<?=$goods->image_url?>">
                </span>
                <div class="info">
                    <p class="cd_title"><?=$goods->name?></p>
                    <p class="cd_money">
                        <span>￥</span>
                        <var><?=$goods->price?></var>
                    </p>
                    <p class="cd_sales">库存：<?=$goods->stock_count?></p>
                </div>
            </a>
            <div class="operat"><i class="iconfont" onclick="showMenu(<?=$goods->id?>)">&#xe73a;</i></div>
        </li>
        <? endforeach;?>
    </ul>
    <? if($result['total']==0) : ?>
    <div class="weui-msg">
        <div class="weui-msg__icon-area"><i class="weui-icon-warn weui-icon_msg-primary"></i></div>
        <div class="weui-msg__text-area">
            <h2 class="weui-msg__title">没有任何商品。。</h2>
            <p class="weui-msg__desc"></p>
        </div>
    </div>

    <? endif;?>

    <div class="weui-btn-area">
        <a href="<?=url('goods/add')?>" class="weui-btn weui-btn_primary">添加商品</a>
    </div>


    <div class="weui-tabbar">
        <a href="javascript:;" class="weui-tabbar__item weui-bar__item_on">
            <img src="./images/icon_tabbar.png" alt="" class="weui-tabbar__icon">
            <p class="weui-tabbar__label">微信</p>
        </a>
        <a href="javascript:;" class="weui-tabbar__item">
            <img src="./images/icon_tabbar.png" alt="" class="weui-tabbar__icon">
            <p class="weui-tabbar__label">通讯录</p>
        </a>
        <a href="javascript:;" class="weui-tabbar__item">
            <img src="./images/icon_tabbar.png" alt="" class="weui-tabbar__icon">
            <p class="weui-tabbar__label">发现</p>
        </a>
        <a href="javascript:;" class="weui-tabbar__item">
            <img src="./images/icon_tabbar.png" alt="" class="weui-tabbar__icon">
            <p class="weui-tabbar__label">我</p>
        </a>
    </div>

    <div class="weui-skin_android" id="androidActionsheet" style="display: none">
        <div class="weui-mask"></div>
        <div class="weui-actionsheet">
            <div class="weui-actionsheet__menu">
                <? if($this->func=='index') : ?>
                    <div class="weui-actionsheet__cell change">下架</div>
                <? endif;?>
                <? if($this->func=='list_status2') :?>
                    <div class="weui-actionsheet__cell change">上架</div>
                <? endif;?>
                <div class="weui-actionsheet__cell edit">编辑</div>
                <div class="weui-actionsheet__cell del">删除</div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        function showMenu(id) {
            var $androidActionSheet = $('#androidActionsheet');
            $androidActionSheet.show();
            $androidActionSheet.find('.weui-mask').on('click',function () {
                $androidActionSheet.hide();
            });
            $androidActionSheet.find('.change').on('click',function () {
                location.href='<?=url("goods/change/?id=")?>'+id;
            });
            $androidActionSheet.find('.edit').on('click',function () {
                location.href='<?=url("goods/edit/?id=")?>'+id;
            });
            $androidActionSheet.find('.del').on('click',function () {
                layer.open({
                    content: '您确定要删除吗？'
                    ,btn: ['删除', '取消']
                    ,yes: function(index){
                        location.href='<?=url("goods/del/?id=")?>'+id;
                        layer.close(index);
                    }
                });
                $androidActionSheet.fadeOut(200);
            });
        }
    </script>
<?php require 'footer.php';?>