<?php
namespace App\Controller;

use App\Model\UserWx;
use EasyWeChat\Message\Text;
class WxapiController extends Controller
{
    private $UserWx;
    private $app;
    public function __construct()
    {
        parent::__construct();
        $this->UserWx=new UserWx();
        $this->app=$this->UserWx->app;
    }

    public function index()
    {
        $server = $this->app->server;
        $server->setMessageHandler(function ($message) {
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
        $server->serve()->send();
    }
    private function event($message)
    {
        $userServer=$this->app->user;
        //$msg['Event']=='subscribe' || $msg['Event']=='SCAN'
        if(isset($message->EventKey)){
            $EventKey=$message->EventKey;
            if($message->Event=='subscribe')
            {
                //$EventKey=substr($EventKey,8);
            }
            $typeid=substr($EventKey,-2);//类型id			
            $_str=substr($EventKey,0,-2);
            $subsiteid=substr($_str,-2);//分站id
            $txt=(int)substr($_str,0,-2);//内容				
        }
        
        if($message->Event=='subscribe'){
            $user=$userServer->get($message->FromUserName);
            $user_wx=$this->UserWx->where("openid={$user->openid}")->first();
            $user_wx->subscribe=1;
            $user_wx->openid=$user->openid;
            $user_wx->nickname=$user->nickname;
            $user_wx->sex=$user->sex;
            $user_wx->city=$user->city;
            $user_wx->country=$user->country;
            $user_wx->province=$user->province;
            $user_wx->language=$user->language;
            $user_wx->headimgurl=$user->headimgurl;
            $user_wx->subscribe_time=$user->subscribe_time;
            $user_wx->unionid=$user->unionid;
            $user_wx->remark=$user->remark;
            $user_wx->groupid=$user->groupid;
            $user_wx->tagid_list=$user->tagid_list;
            $user_wx->save();
            return "您好！欢迎终于等到你了!";
        }elseif($message->Event=='unsubscribe'){
            
        }
    }

    private function text($message){
        return new Text(['content' => '您好！overtrue。']);
    }
}