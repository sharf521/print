<?php
namespace App\Controller;

use App\Model\PrintTask;
use App\Model\User;
use App\Model\UserWx;
use App\WeChat;
use EasyWeChat\Message\Text;
use EasyWeChat\Message\Raw;
use System\Lib\DB;
use System\Lib\Request;

class WxapiController extends Controller
{
    private $UserWx;
    private $app;
    private $weChat;
    public function __construct()
    {
        parent::__construct();
        $this->UserWx=new UserWx();
        $this->weChat=new WeChat();
        $this->app=$this->weChat->app;
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
        if(isset($message->EventKey)){  //$msg['Event']=='subscribe' || $msg['Event']=='SCAN'
            $eventKey=$message->EventKey;
            $typeid=substr($eventKey,-2);//类型id
            $txt=substr($eventKey,8,-2);//内容EventKey":"qrscene_201
        }

        if($message->Event=='subscribe'){
            $this->addUser($message->FromUserName,intval($txt));
            return new Text(['content' =>'您好，终于等到你了！']);
        }elseif($message->Event=='unsubscribe'){
            $arr=array(
                'subscribe'=>0,
            );
            DB::table('user_wx')->where("openid=?")->bindValues($message->FromUserName)->update($arr);
        }elseif($message->Event=='CLICK'){
            if($message->EventKey=='menu_print'){
                $content=' 亲，请发要求给我们，稍后会有客服和您微信直接联系。'."\r\n".'也可以点击：【<a href="http://'.$_SERVER['HTTP_HOST'].'/index.php/weixin/taskAdd/">我要下单</a>】'."\r\n".'产品介绍及报价：【<a href="http://'.$_SERVER['HTTP_HOST'].'/article/detail/1">点 击</a>】'."\r\n".'感谢您的支持！';
                return new Text(['content' =>$content]);
            }
        }
    }

    public function createMenu()
    {
        $menu = $this->app->menu;
        $buttons = [
            [
                "type" => "click",
                "name" => "我要印",
                "key"  => "menu_print"
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
                "name" => "产品/价格",
                "url"  => "http://{$_SERVER['HTTP_HOST']}/index.php/article/detail/1"
            ]
        ];
        $menu->add($buttons);
    }

    private function text($message)
    {
        if($message->Content=='邀请'){
            $url="http://{$_SERVER['HTTP_HOST']}/index.php/weixin/invite";
            $url=$this->weChat->shorten($url);
            return new Text(['content' => $url]);
        }
        elseif($message->Content=='商铺'){
            $url="http://{$_SERVER['HTTP_HOST']}/index.php/shop";
            $url=$this->weChat->shorten($url);
            return new Text(['content' => $url]);
        }else{
            $openid=$message->FromUserName;
            $user=new User();
            $user=$user->where('openid=?')->bindValues($openid)->first();
            $invite_name=$user->Invite()->name;
            if(!empty($invite_name)){
                $staff = $this->app->staff; // 客服管理
                $result=$staff->onlines();
                $result=json_decode($result,true);
                $kf_online_list=$result['kf_online_list'];
                $online_array=array();
                foreach ($kf_online_list as $online){
                    array_push($online_array,$online['kf_id']);
                    //$online_array[$online['kf_id']]=$online['kf_account'];
                }
                $result=$staff->lists();
                $result=json_decode($result,true);
                $kf_list=$result['kf_list'];
                foreach ($kf_list as $kf){
                    if($kf['kf_nick']==$invite_name  && in_array($kf['kf_id'],$online_array)){
                        //创建会话
                        $session = $this->app->staff_session;
                        $session->create($kf['kf_account'],$message->FromUserName);
                        break;
                    }
                }
            }
            $message = new Raw('<xml>
            <ToUserName><![CDATA['.$message->FromUserName.']]></ToUserName>
            <FromUserName><![CDATA['.$message->ToUserName.']]></FromUserName>
            <CreateTime>'.time().'</CreateTime>
            <MsgType><![CDATA[transfer_customer_service]]></MsgType>
            </xml>');
            return $message;
        }
    }

    public function test()
    {
        $staff = $this->app->staff; // 客服管理
        $result=$staff->lists();
        $result=json_decode($result,true);
        $kf_list=$result['kf_list'];
        print_r($kf_list);
        $result=$staff->onlines();
        $result=json_decode($result,true);
        print_r($result['kf_online_list']);

        $session = $this->app->staff_session; // 客服会话管理
        $session->create('kf2001@gh_eaa8b99402a9','oHzjfwvtq80ycSaDwSTm-ZCeLQQs');


        /*
         *
 [kf_account] => kf2001@gh_eaa8b99402a9
            [kf_headimgurl] => http://mmbiz.qpic.cn/mmbiz_jpg/RflZvibIfCaQOkT6mOt3oR7qOArHTy8lD0gyt62EbTML8BG3Gu5rfAfJTNpickL3L5hBSL2SRR6anEhgAohsic7iaw/300?wx_fmt=jpeg
            [kf_id] => 2001
            [kf_nick] => 综合接待
            [kf_wx] => gdl521


        [kf_account] => kf2001@gh_eaa8b99402a9
            [status] => 1
            [kf_id] => 2001
            [accepted_case] => 0


         * */

    }

    private function addUser($openid,$invite_userid=0)
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
        $user->headimgurl=$userInfo->headimgurl;
        $user->nickname=$userInfo->nickname;
        if($invite_userid!=0 && intval($user->id)==0){
            $invite=new User();
            $invite=$invite->find($invite_userid);
            if(!empty($invite)){
                $user->invite_userid=$invite->id;
                $user->invite_path=$invite->invite_path.$invite_userid.',';
                //更新邀请人的邀请数量
                $invite->invite_count=$invite->invite_count+1;
                $invite->save();
            }
        }
        if(intval($user->type_id)==0){
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