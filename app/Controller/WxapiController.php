<?php
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
        $this->server->setMessageHandler(function ($message) {
            switch ($message->MsgType) {
                case 'event':
                    return $this->event($message);
                    break;
                case 'text':
                    return $this->text($message);
                    break;
                case 'image':
                    # 图片消息...
                    break;
                case 'voice':
                    # 语音消息...
                    break;
                case 'video':
                    # 视频消息...
                    break;
                case 'location':
                    # 坐标消息...
                    break;
                case 'link':
                    # 链接消息...
                    break;
                // ... 其它消息
                default:
                    # code...
                    break;
            }
        });
        $this->server->serve()->send();
    }
    private function event($message)
    {
        if($message->Event=='subscribe'){
            return "您好！欢迎终于等到你了!";
        }
    }

    private function text($message){
        //return "您好！欢迎关注我!";
        return new Text(['content' => '您好！overtrue。']);
    }
}