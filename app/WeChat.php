<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/5
 * Time: 17:46
 */

namespace App;

use EasyWeChat\Foundation\Application;
class WeChat
{
    public $app;
    public function __construct()
    {
        $options = [
            'debug' => true,
            'app_id' => app('System')->getCode('appid'),
            'secret' => app('System')->getCode('appsecret'),
            'token' => 'print',
            // 'aes_key' => null, // 可选
            'log' => [
                'level' => 'debug',
                'file' => ROOT.'/public/easywechat.log', // XXX: 绝对路径！！！！
            ],
            'oauth' => [
                'scopes'   => ['snsapi_userinfo'],
                'callback' => 'http://'.$_SERVER['HTTP_HOST'].'/index.php/wxapi/oauth_callback',
            ],
            'guzzle' => [
                'timeout' => 4.0, // 超时时间（秒）
                // 'verify' => false, // 关掉 SSL 认证（强烈不建议！！！）
            ]
        ];
        $this->app=new Application($options);
    }
}