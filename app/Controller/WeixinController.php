<?php
namespace App\Controller;

use App\Model\User;
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
echo $this->func;
        if(! in_array($this->func,array('oauth','oauth_callback'))){
            if(empty($this->user_id)){
                $url=urlencode($_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
                redirect("weixin/oauth/?redirect_uri={$url}");
                exit;
            }
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
            echo 'no user';
        }
    }

    public function member()
    {
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