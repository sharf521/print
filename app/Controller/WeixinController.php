<?php
namespace App\Controller;

use App\Model\LinkPage;
use App\Model\PrintTask;
use App\WeChat;
use EasyWeChat\Payment\Order;
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
    
    

    public function orderAdd(Request $request,LinkPage $linkPage,PrintTask $printTask)
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

    public function orderList(PrintTask $printTask,LinkPage $linkPage)
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

    public function orderShow(Request $request,PrintTask $printTask)
    {
        $id=$request->get('task_id');
        $page=$request->get('page');
        $task=$printTask->findOrFail($id);
        if($task->user_id!=$this->user_id && $task->status != 3){
            redirect()->back()->with('error','权限异常！');
        }
        if($_POST){
            $weChat=new WeChat();
            $app=$weChat->app;
            $payment = $app->payment;

            $attributes = [
                'trade_type'       => 'JSAPI', // JSAPI，NATIVE，APP...
                'body'             => 'iPad mini 16G 白色',
                'detail'           => 'iPad mini 16G 白色',
                'out_trade_no'     => '1217752501201407033233368018',
                'total_fee'        => 8,
                'notify_url'       => 'http://print.yuantuwang.com/weixin/order-notify', // 支付结果通知网址，如果不设置则会
            ];
            $order=new Order($attributes);
            $result=$payment->pay($order);
            var_dump($result);

        }else{

            $data['task']=$task;
            $data['order']=$task->PrintOrder();
            $data['title_herder']='我的订单';
            $this->view('print',$data);
        }
    }

    public function union()
    {
        echo '联盟页';
    }
}