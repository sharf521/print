<?php require 'header.php';?>

<div class="warpcon">
    <?php require 'left.php';?>
    <div class="warpright">
        <div class="jiben">
            <div class="jbtx">
                <div class="touxiang"><img src="/themes/member/images/no-img.jpg"></div>
                <div class="toutext">
                    <h2>万万</h2>
                    <p><img src="/themes/member/images/zuans.png" width="16" height="13">普通会员</p>
                </div>
            </div>

            <div class="zhjin">
                <div class="zhjinle">
                    <p>可用资金：<span><?=(float)$account['funds_available']?></span></p>
                    <p>可用积分：<span><?=(float)$account['integral_available']?></span></p>
                    <p>周转金：<span> <?=(float)$account['turnover_available']?></span></p>
                    <p>保证金：<span> <?=(float)$account['security_deposit']?></span></p>
                </div>
                <div class="zhjinle">
                    <p>冻结资金：<span> <?=(float)$account['funds_freeze']?></span></p>
                    <p>冻结积分：<span> <?=(float)$account['integral_freeze']?></span></p>
                    <p>周转金额度：<span> <?=(float)$account['turnover_credit']?></span></p>
                </div>
                <div class="zhjinri">
                    <p><a href="#" class="chongzhi">充值</a></p>
                    <p><a href="#" class="tixian">提现</a></p>
                </div>

            </div>
        </div>
    </div>
</div>

<?php require 'footer.php';?>
