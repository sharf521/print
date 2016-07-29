<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/29
 * Time: 23:16
 */

namespace App\Controller;

use EasyWeChat\Foundation\Application;
use EasyWeChat\Message\Text;

class WxapiController extends Controller
{
    protected $server;
    public function __construct()
    {
        parent::__construct();
        $options = [
            'debug' => true,
            'app_id' => 'wx2dc7b9baa7afd65b',
            'secret' => '56d196f91373e6c3acadba655f2ba5cd',
            'token' => 'print',
            // 'aes_key' => null, // 可选
            'log' => [
                'level' => 'debug',
                'file' => ROOT.'/public/easywechat.log', // XXX: 绝对路径！！！！
            ]
        ];
        $app=new Application($options);
        $this->server = $app->server;
    }
    public function index()
    {
        ob_start();
        $options = [
            'debug' => true,
            'app_id' => 'wx2dc7b9baa7afd65b',
            'secret' => '56d196f91373e6c3acadba655f2ba5cd',
            'token' => 'print',
            // 'aes_key' => null, // 可选
            'log' => [
                'level' => 'debug',
                'file' => ROOT.'/public/easywechat.log', // XXX: 绝对路径！！！！
            ]
        ];
        $app=new \EasyWeChat\Foundation\Application($options);
        $app->server->serve()->send();

        $phpinfo = ob_get_contents();
        file_put_contents("log1.txt",$phpinfo);
        ob_clean();
        echo $phpinfo;
        exit;
    }

    public function index1()
    {
        ob_start();
        $this->server->setMessageHandler(function ($message) {
            //return "您好！欢迎关注我!";
            return new Text(['content' => '您好！overtrue。']);
        });
        $this->server->serve()->send();
        $phpinfo = ob_get_contents();
        file_put_contents("log1.txt",$phpinfo);
        ob_clean();
        echo $phpinfo;

        exit;
    }
}