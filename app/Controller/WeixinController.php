<?php
namespace App\Controller;

use App\Model\LinkPage;
use App\Model\PrintTask;
use App\Model\User;
use App\WeChat;
use EasyWeChat\Payment\Order;
use System\Lib\DB;
use System\Lib\Request;

class WeixinController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $agent = addslashes($_SERVER['HTTP_USER_AGENT']);
        if(strpos($agent, 'MicroMessenger') === false && strpos($agent, 'Windows Phone') === false)
        {
            //echo '非微信浏览器不能访问';
            //die('Sorry！非微信浏览器不能访问');
        }
        if(empty($this->user_id)){
            $url=urlencode($_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
            redirect("wxapi/oauth/?url={$url}");
            exit;
        }
        $this->template = 'weixin';
    }

    public function taskAdd(Request $request,LinkPage $linkPage,PrintTask $printTask)
    {
        if($_POST){
            $print_type=$request->post('print_type');
            $remark=$request->post('remark');
            $tel=$request->post('tel');
            if (empty($print_type)) {
                redirect()->back()->with('error', '请选择类型');
            }
            if (empty($remark)) {
                redirect()->back()->with('error', '请填写具体要求');
            }
            if (empty($tel)) {
                redirect()->back()->with('error', '请填写联系电话');
            }
            $printTask->user_id=$this->user_id;
            $printTask->print_type=$print_type;
            $printTask->remark=$remark;
            $printTask->tel=$tel;
            $printTask->status=1;
            $printTask->save();
            redirect('weixin/orderList')->with('msg', '下单成功！<br>稍后工作人员会联系您。<br>您也可以在微信里留言。！<br>');
        }else{
            $data['print_type']=$linkPage->echoLink('print_type','',array('type'=>'radio'));
            $data['title_herder']='我要下单';
            $this->view('print',$data);
        }
    }

    public function orderList(PrintTask $printTask)
    {
        $where = " user_id=$this->user_id";
        if (!empty($_GET['print_type'])) {
            $where .= " and print_type='{$_GET['print_type']}'";
        }
        $data['title_herder']='我的订单';
        $task = $printTask->where($where)->orderBy('id desc')->pager($_GET['page'], 10);
        $data['task']=$task;
        $this->view('print',$data);
    }


    public function orderPay(Request $request,PrintTask $printTask)
    {
        $id=$request->get('task_id');
        $page=$request->get('page');
        $task=$printTask->findOrFail($id);
        if($task->user_id!=$this->user_id && $task->status != 3){
            redirect()->back()->with('error','权限异常！');
        }

        $data['task']=$task;

        $openid=DB::table('user')->where('id=?')->bindValues($this->user_id)->value('openid');
        $weChat=new WeChat();
        $app=$weChat->app;
        $payment = $app->payment;
        $attributes = [
            'trade_type'       => 'JSAPI', // JSAPI，NATIVE，APP...
            'body'             => '支付订单',
            'out_trade_no'     => time().rand(10000,99999),
            'total_fee'        => math($task->money,100,'*',2),
            'attach'=>$task->id,
            'openid'=>$openid,
            'notify_url'       => "http://{$_SERVER['HTTP_HOST']}/index.php/wxapi/payNotify/"
        ];
        $order=new Order($attributes);
        $result = $payment->prepare($order);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $js = $app->js;
            $data['config']=$js->config(array('chooseWXPay','openAddress','checkJsApi'), false);
            $pay=$weChat->getPayParams($result->prepay_id);
            $data['pay']=$pay;
            $task->out_trade_no=$attributes['out_trade_no'];
            $task->save();
        }
        $data['title_herder']='支付中。。';
        $this->view('printPay',$data);
    }

    public function orderShow(Request $request,PrintTask $printTask)
    {
        $id=$request->get('task_id');
        $task=$printTask->findOrFail($id);
        $data['task']=$task;
        $data['title_herder']='订单详情';
        $this->view('print',$data);
    }

    public function union()
    {
        echo '联盟页';
    }
    
    public function saveAddress(Request $request,PrintTask $printTask)
    {
        $task_id=(int)$request->get('task_id');
        $task=$printTask->findOrFail($task_id);
        if($task->user_id == $this->user_id){
            $task->shipping_name=$request->post('name');
            $task->shipping_tel=$request->post('tel');
            $task->shipping_address=$request->post('address');
            $task->save();
        }
    }
    
    public function invite(WeChat $weChat,User $user)
    {
        if($this->user_id==163)
        {
            echo 1;
            $this->user_id=165;
        }
        $user=$user->find($this->user_id);
        if($user->type_id==5){
            //客服生成永久二维码
            $data['qrcodeSrc']=$weChat->qrcode($this->user_id.'01',true);
        }else{
            $data['qrcodeSrc']=$weChat->qrcode($this->user_id.'01');
        }
        $data['invites']=$user->where("invite_userid=?")->bindValues($this->user_id)->orderBy('id desc')->get();
        $data['title_herder']='邀请商家';
        $this->view('invite',$data);
    }
}