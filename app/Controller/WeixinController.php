<?php
namespace App\Controller;

use App\Model\UserWx;
use Symfony\Component\HttpFoundation\Request;

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
    
    public function oauth(Request $request)
    {
        $redirect_uri=$request->get('redirect_uri');
        //没有登陆时去授权
        if (empty($this->user_id)) {
            session()->set('target_url',$redirect_uri);
            $oauth = $this->app->oauth;
            $oauth->redirect()->send();
            exit;
        }
        redirect($redirect_uri);
    }
    
    public function oauth_callback()
    {
        $oauth = $this->app->oauth;
        $user = $oauth->user()->toArray();
        var_dump($user);
        exit;
        $target_url=session('target_url');
        redirect($target_url); // 跳转
    }

    public function member()
    {
        print_r($_SESSION);
        echo 'member';
    }

    public function orderAdd()
    {
        echo 'orderAdd';
    }

    public function orderList()
    {
        echo 'orderList';
    }

    public function union()
    {
        echo 'union';
    }
}