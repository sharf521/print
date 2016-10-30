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
            $user=(new User())->addWeChatUser($message->FromUserName,intval($txt));
            if(! isset($user->invite_openid)){
                return new Text(['content' =>'您好，终于等到你了.']);
            }else{
                //发送消息
                $staff = $this->app->staff; // 客服管理
                $message=new Text(['content' => "您好，终于等到你了！"]);
                $staff->message($message)->to($user->openid)->send();

                //您成功的为  投了一票，感谢您的支持！
                $message=new Text(['content' => "{$user->nickname}，您己经成功关注商家印联，推荐人是：{$user->invite_nickname}"]);
                $staff->message($message)->to($user->openid)->send();

                //发送给邀请人
                $message=new Text(['content' => "您成功邀请了：{$user->nickname}，一共邀请：{$user->invite_invite_count}人。"]);
                $staff->message($message)->to($user->invite_openid)->send();
            }
        }elseif($message->Event=='unsubscribe'){
            $arr=array(
                'subscribe'=>0,
            );
            DB::table('user_wx')->where("openid=?")->bindValues($message->FromUserName)->update($arr);
        }elseif($message->Event=='CLICK'){
            if($message->EventKey=='menu_print'){
                $content=' 亲，请发要求给我们，稍后会有客服和您微信直接联系。
也可以点击：【<a href="http://'.$_SERVER['HTTP_HOST'].'/index.php/weixin/taskAdd/">我要下单</a>】
产品介绍及报价：【<a href="http://'.$_SERVER['HTTP_HOST'].'/article/detail/1">点 击</a>】
感谢您的支持！';
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
                        "url"  => "http://{$_SERVER['HTTP_HOST']}/index.php/shop"
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
        $arr=array(
            'direct'=>1,
            'openid'=>$oUser['id']
        );
        $result=$user->login($arr);
        if($result===true){
            $user->addWeChatUser($oUser['id']);
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
            $task =$task->find($id);
            if (!$task) { // 如果订单不存在
                return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }
            // 如果订单存在
            // 检查订单是否已经更新过支付状态
            if ($task->status!=3 || $task->out_trade_no !=$out_trade_no) {
                return true; // 已经支付成功了就不再更新了
            }
            // 用户是否支付成功
            if ($successful) {
                $task->paytime = time();
                $task->paymoney=(float)math($notify->total_fee,100,'/',2);
                $task->status = 4;
                $task->save(); // 保存
                //消息start
                $notice = $this->app->notice;
                $templateId = 'tmGk3uxIeNke-tG7zBbHVzrxuHI_zB_cdKm69ZWfmm4';
                $url = "http://{$_SERVER['HTTP_HOST']}/index.php/weixin/orderShow/?task_id={$task->id}";
                $data = array(
                    "first"  => "您好，您的订单【{$task->print_type}】已付款成功！",
                    "keyword1"   => $out_trade_no,
                    "keyword2"  => date('Y-m-d H:i'),
                    "keyword3"  => $task->paymoney,
                    "keyword4"  => '微信支付',
                    "remark" => "感谢您的惠顾。",
                );
                $openid=$task->User()->openid;
                $notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($openid)->send();
                //消息end
            } else {
                // 用户支付失败
            }
            return true; // 返回处理完成
        });
        $response->send();
    }


    public function test()
    {



        $notice = $this->app->notice;
        $templateId = 'HS0gHwMEKEqskA4btwP47QYNF35KvbK0N7YoMnWs6G8';
        $url = "http://{$_SERVER['HTTP_HOST']}/index.php/weixin/orderShow/?task_id=";
        $openid='oHzjfwvtq80ycSaDwSTm-ZCeLQQs';
        $data = array(
            "first"  => "恭喜你购买成功！",
            "keyword1"   => "不干胶",
            "keyword2"  => "快递公司",
            "keyword3"  => "快递单号",
            "keyword4"  => date('Y-m-d H:i'),
            "remark" => "请注意查收！",
        );
        $notice->uses($templateId)->withUrl($url)->andData($data)->andReceiver($openid)->send();


        exit;

        $result=$staff->lists();
        $result=json_decode($result,true);
        $kf_list=$result['kf_list'];
        print_r($kf_list);
        $result=$staff->onlines();
        $result=json_decode($result,true);
        print_r($result['kf_online_list']);
        exit;
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
}