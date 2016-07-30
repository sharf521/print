<?php
namespace App\Controller;

use App\Model\User;
use App\Model\UserWx;
use EasyWeChat\Message\Text;
use System\Lib\DB;

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
//        if(isset($message->EventKey)){
//            $EventKey=$message->EventKey;
//            if($message->Event=='subscribe')
//            {
//                //$EventKey=substr($EventKey,8);
//            }
//            $typeid=substr($EventKey,-2);//类型id			
//            $_str=substr($EventKey,0,-2);
//            $subsiteid=substr($_str,-2);//分站id
//            $txt=(int)substr($_str,0,-2);//内容				
//        }        
        if($message->Event=='subscribe'){
            $userInfo=$userServer->get($message->FromUserName);
            $user_wx=$this->UserWx->where("openid=?")->bindValues($userInfo->openid)->first();
            $user_wx->subscribe = $userInfo->subscribe;
            $user_wx->openid = $userInfo->openid;
            $user_wx->nickname = $userInfo->nickname;
            $user_wx->sex = $userInfo->sex;
            $user_wx->city = $userInfo->city;
            $user_wx->country = $userInfo->country;
            $user_wx->province = $userInfo->province;
            $user_wx->language = $userInfo->language;
            $user_wx->headimgurl = $userInfo->headimgurl;
            $user_wx->subscribe_time = $userInfo->subscribe_time;
            $user_wx->unionid = $userInfo->unionid;
            $user_wx->remark = $userInfo->remark;
            $user_wx->groupid = $userInfo->groupid;
            $user_wx->tagid_list =json_encode($userInfo->tagid_list);
            $user_wx->save();
            $user=new User();
            $user=$user->where("openid=?")->bindValues($userInfo->openid)->first();
            $user->openid=$userInfo->openid;
            $user->headimgurl=$userInfo->subscribe_time;
            $user->nickname=$userInfo->nickname;
            if(empty($user->type_id)){
                $user->type_id=1;
            }
            $user->save();
            return new Text(['content' =>"{$user->nickname}，您好！终于等到您了!"]);
        }elseif($message->Event=='unsubscribe'){
            $arr=array(
                'subscribe'=>0,
            );
            DB::table('user_wx')->where("openid=?")->bindValues($message->FromUserName)->update($arr);
        }elseif($message->Event=='CLICK'){
            if($message->EventKey=='menu_order'){
                return new Text(['content' =>"下单页!"]);
            }elseif($message->EventKey=='menu_user'){
                return new Text(['content' =>"用户中心!"]);
            }else{
                return new Text(['content' =>$message->EventKey]);
            }
        }
    }

    public function createMenu()
    {
        $menu = $this->app->menu;
        $buttons = [
            [
                "type" => "click",
                "name" => "我要下单",
                "key"  => "menu_order"
            ],
            [
                "type" => "view",
                "name" => "个人中心",
                "url"  => "http://print.yuantuwang.com/index.php/weixin/oauth"
            ]
        ];
        $menu->add($buttons);
    }

    private function text($message){
        return new Text(['content' => '您好！overtrue。']);
    }
}