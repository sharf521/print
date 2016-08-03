<?php
namespace App\Controller;

use App\Model\User;
use App\Model\UserWx;
use System\Lib\Request;

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
        if(! in_array($this->func,array('oauth','oauth_callback'))){
            if(empty($this->user_id)){
                $url=urlencode($_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
                redirect("weixin/oauth/?url={$url}");
                exit;
            }
        }
        $this->UserWx=new UserWx();
        $this->app=$this->UserWx->app;
    }
    
    public function oauth(Request $request)
    {
        $url=$request->get('url');
        //没有登陆时去授权
        if (empty($this->user_id)) {
            session()->set('target_url',$url);
            $oauth = $this->app->oauth;
            $oauth->redirect()->send();
            exit;
        }
        redirect($url);
    }
    
    public function oauth_callback(User $user)
    {
        $oauth = $this->app->oauth;
        $oUser = $oauth->user()->toArray();
        $arr=array(
            'direct'=>1,
            'openid'=>$oUser['id']
        );
        $result=$user->login($arr);
        if($result===true){
            $target_url=session('target_url');
            redirect($target_url); // 跳转
        }else{
            echo '请关注页面';
        }
    }

    public function orderAdd()
    {
        echo '下单页面';
    }

    public function orderList()
    {
        echo '订单列表页';
    }

    public function union()
    {
        echo '联盟页';
    }
}