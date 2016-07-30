<?php
namespace App\Model;

use EasyWeChat\Foundation\Application;
class UserWx extends Model
{
    protected $table='user_wx';
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
            ]
        ];
        $this->app=new Application($options);
    }


}