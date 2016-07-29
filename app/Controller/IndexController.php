<?php
namespace App\Controller;

use EasyWeChat\Foundation\Application;

class IndexController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($a = 100, $b, $c = 300)
    {
        $options = [
            'debug' => true,
            'app_id' => app('\App\Model\System')->getCode('appId'),
            'secret' => app('\App\Model\System')->getCode('appsecret'),
            'token' => 'easywechat',
            // 'aes_key' => null, // 可选
            'log' => [
                'level' => 'debug',
                'file' => ROOT.'/public/data/easywechat.log', // XXX: 绝对路径！！！！
            ],
            //...
        ];

        // 使用配置来初始化一个项目。
        $app=new Application($options);
        $response = $app->server->serve();
        // 将响应输出
        $response->send(); // Laravel 里请使用：return $response;

        echo 111;
    }
}