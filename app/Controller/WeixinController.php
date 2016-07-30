<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/30
 * Time: 11:16
 */

namespace App\Controller;


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
    }
    
    public function member()
    {
        
    }
}