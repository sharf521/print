<?php
namespace App\Controller;

use App\Model\LinkPage;
use App\Model\PrintTask;
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





        }else{
            $data['task']=$task;
            $data['order']=$task->PrintOrder();
            $data['title_herder']='我的订单';


            $openid=DB::table('user')->where('id=?')->bindValues($this->user_id)->value('openid');
            $weChat=new WeChat();
            $app=$weChat->app;
            $payment = $app->payment;
            $attributes = [
                'trade_type'       => 'JSAPI', // JSAPI，NATIVE，APP...
                'body'             => 'iPad mini 16G 白色',
                'detail'           => 'iPad mini 16G 白色',
                'out_trade_no'     => time().rand(10000,99999),
                'total_fee'        => 8,
                'attach'=>'attach',
                'product_id'=>1,
                'openid'=>$openid,
                'notify_url'       => 'http://print.yuantuwang.com/weixin/'
            ];
            $order=new Order($attributes);
            $result = $payment->prepare($order);
            if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
                $js = $app->js;
                $data['config']=$js->config(array('chooseWXPay'), true,false,false);
                print_r($data['config']);
                $data['pay']=$result;
            }
            $this->view('print',$data);
        }
    }

    public function union()
    {
        echo '联盟页';
    }
}