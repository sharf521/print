<?php
namespace App\Controller;

use EasyWeChat\Foundation\Application;

class IndexController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function wxapi()
    {
        $options = [
            'debug' => true,
            'app_id' => 'wx2dc7b9baa7afd65b',
            'secret' => '56d196f91373e6c3acadba655f2ba5cd',
            'token' => 'print',
            // 'aes_key' => null, // 可选
            'log' => [
                'level' => 'debug',
                'file' => ROOT.'/public/data/easywechat.log', // XXX: 绝对路径！！！！
            ]
        ];
        $app=new \EasyWeChat\Foundation\Application($options);
        $response = $app->server->serve();
        $response->send();
    }
}