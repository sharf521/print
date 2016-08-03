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
        if(empty($this->user_id)){
            $url=urlencode($_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
            redirect("wxapi/oauth/?url={$url}");
            exit;
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