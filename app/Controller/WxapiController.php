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
            return "您好！欢迎终于等到你了!".$userInfo->nickname;
        }elseif($message->Event=='unsubscribe'){
            $arr=array(
                'subscribe'=>0,
            );
            DB::table('user_wx')->where("openid=?")->bindValues($message->FromUserName)->update($arr);
            return "您走了!";
        }
    }

    public function test()
    {
        $menu = $this->app->menu;
        $menus = $menu->current();
        print_r($menus);
        exit;
        $buttons = [
            [
                "type" => "click",
                "name" => "今日歌曲",
                "key"  => "V1001_TODAY_MUSIC"
            ],
            [
                "name"       => "菜单",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "搜索",
                        "url"  => "http://www.soso.com/"
                    ],
                    [
                        "type" => "view",
                        "name" => "视频",
                        "url"  => "http://v.qq.com/"
                    ],
                    [
                        "type" => "click",
                        "name" => "赞一下我们",
                        "key" => "V1001_GOOD"
                    ],
                ],
            ],
        ];
        $menu->add($buttons);
        echo 111;
    }

    private function text($message){
        return new Text(['content' => '您好！overtrue。']);
    }
}