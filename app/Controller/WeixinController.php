<?php
namespace App\Controller;

use App\Model\UserWx;

class WeixinController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $agent = addslashes($_SERVER['HTTP_USER_AGENT']);
        if(strpos($agent, 'MicroMessenger') === false && strpos($agent, 'Windows Phone') === false)
        {
            echo '非微信浏览器不能访问';
            //die('Sorry！非微信浏览器不能访问');
        }
        $this->UserWx=new UserWx();
        $this->app=$this->UserWx->app;
    }
    
    public function oauth()
    {
        $oauth = $this->app->oauth;
        if (empty($_SESSION['wechat_user'])) {
            //$_SESSION['target_url'] = 'user/profile';
            $oauth->redirect()->send();
        }
        $user = $_SESSION['wechat_user'];
        redirect('weixin/member/?');
    }
    
    public function oauth_callback()
    {
        $oauth = $this->app->oauth;
        $user = $oauth->user();
        $_SESSION['wechat_user'] = $user->toArray();
        $targetUrl = empty($_SESSION['target_url']) ? '/index.php/weixin/member/' : $_SESSION['target_url'];
        header('location:'. $targetUrl); // 跳转到 user/profile
    }

    public function member()
    {
        echo 'member';
    }
}