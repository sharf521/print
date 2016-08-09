<?php
namespace App\Controller;

use App\Model\PrintTask;
use App\Model\User;
use App\Model\UserWx;
use App\WeChat;
use EasyWeChat\Message\Text;
use System\Lib\DB;
use System\Lib\Request;

class WxapiController extends Controller
{
    private $UserWx;
    private $app;
    public function __construct()
    {
        parent::__construct();
        $this->UserWx=new UserWx();
        $weChat=new WeChat();
        $this->app=$weChat->app;
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
            $this->addUser($message->FromUserName);
            return new Text(['content' =>"您好！终于等到您了!"]);
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
                "type" => "view",
                "name" => "我要下单",
                "url"  => "http://{$_SERVER['HTTP_HOST']}/index.php/weixin/orderAdd"
            ],
            [
                "name" => "用户中心",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "订单列表",
                        "url"  => "http://{$_SERVER['HTTP_HOST']}/index.php/weixin/orderList"
                    ],
                    [
                        "type" => "view",
                        "name" => "联盟商家",
                        "url"  => "http://{$_SERVER['HTTP_HOST']}/index.php/weixin/union"
                    ]
                ],
            ],
            [
                "type" => "view",
                "name" => "产品、价格",
                "url"  => "http://{$_SERVER['HTTP_HOST']}/index.php/article/detail/1"
            ]
        ];
        $menu->add($buttons);
    }

    private function text($message){
        return new Text(['content' => '您好！overtrue。']);
    }

    public function test()
    {
        echo phpinfo();
    }

    private function addUser($openid)
    {
        $userServer=$this->app->user;
        $userInfo=$userServer->get($openid);
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
    }

    public function oauth(Request $request)
    {
        $url=$request->get('url');
        
        //没有登陆时去授权
        if (empty($this->user_id)) {
            session()->set('target_url',$url);
            $oauth = $this->app->oauth;
            $oauth->redirect()->send();
            exit;
        }else{
            redirect($url);
        }
    }

    public function oauth_callback(User $user)
    {
        $oauth = $this->app->oauth;
        $oUser = $oauth->user()->toArray();
        $this->addUser($oUser['id']);
        $arr=array(
            'direct'=>1,
            'openid'=>$oUser['id']
        );
        $result=$user->login($arr);
        if($result===true){
            $target_url=session('target_url');
            redirect($target_url); // 跳转
        }else{
            echo '请关注页面';
        }
    }
    
    public function payNotify()
    {
        $response = $this->app->payment->handleNotify(function($notify, $successful){
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $id=(int)$notify->attach;
            $out_trade_no=$notify->out_trade_no;
            $task=new PrintTask();
            $order =$task->find($id);
            if (!$order) { // 如果订单不存在
                return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }
            // 如果订单存在
            // 检查订单是否已经更新过支付状态
            if ($order->status!=3 || $order->out_trade_no !=$out_trade_no) {
                return true; // 已经支付成功了就不再更新了
            }
            // 用户是否支付成功
            if ($successful) {
                $order->paytime = time();
                $order->paymoney=(float)math($notify->total_fee,100,'/',2);
                $order->status = 4;
            } else { // 用户支付失败
                $order->status = 'paid_fail';
            }
            $order->save(); // 保存订单
            return true; // 返回处理完成
        });
        $response->send();
    }
}