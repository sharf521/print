<?php
namespace App\Controller\Admin;

use App\Model\LinkPage;
use App\Model\PrintOrder;
use App\Model\PrintTask;
use System\Lib\DB;
use System\Lib\Request;

class PrintTaskController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    function index(PrintTask $printTask, PrintOrder $printOrder,LinkPage $linkPage)
    {
        $where = " 1=1";
        if (!empty($_GET['print_type'])) {
            $where .= " and print_type='{$_GET['print_type']}'";
        }
        if (!empty($_GET['username'])) {
            $user_id=DB::table('user')->where('username=?')->bindValues($_GET['username'])->value('id','int');
            $where .= " and user_id='{$user_id}'";
        }
        if (!empty($_GET['nickname'])) {
            $user_id=DB::table('user_wx w')
                ->leftJoin('user u','u.openid=w.openid')
                ->where('w.nickname=?')->bindValues($_GET['nickname'])->value('u.id','int');
            $where .= " and user_id='{$user_id}'";
        }
        $data = $printTask->where($where)->orderBy('id desc')->pager($_GET['page'], 10);
        $data['print_type']=$linkPage->echoLink('print_type',$_GET['print_type']);
        $this->view('printTask', $data);
    }

    public function show(Request $request,PrintTask $printTask,LinkPage $linkPage)
    {
        $id=$request->get('task_id');
        $page=$request->get('page');
        $task=$printTask->findOrFail($id);
        if($this->user_typeid!=2 && $task->status!=1 && $task->reply_uid!=$this->user_id){
            redirect()->back()->with('error','权限异常！');
        }
        $url="printTask/show/?task_id={$request->get('task_id')}&page={$request->get('page')}";
        if($_POST){
            if($task->status >=4 ){
                //支付之前可以操作
                redirect()->back()->with('error','禁止该操作，状态异常！');
            }

            if($request->post('act')=='orderAdd'){
                $order=new PrintOrder();
                $order->task_id=$task->id;
                $order->reply_id=$this->user_id;
                $order->remark=$_POST['remark'];
                $order->money=(float)$request->post('money');
                $order->company=$request->post('company');
                $order->company_money=(float)$request->post('company_money');
                $order->status=1;
                $order->save();

                $task->status=3;
                $task->money=math($task->money,$order->money,'+',2);
                $task->save();
                redirect($url)->with('msg','添加成功！');
            }elseif($request->post('act')=='orderEdit'){
                $id = $request->post('id');
                $money=0;
                foreach ($id as $key => $val) {
                    if(!empty($val)){
                        $arr=array(
                            'remark'=>$request->post('remark')[$key],
                            'money'=>(float)$request->post('money')[$key],
                            'company'=>$request->post('company')[$key],
                            'company_money'=>(float)$request->post('company_money')[$key]
                        );
                        $money=math($money,$arr['money'],'+',2);
                        DB::table('print_order')->where('id=?')->bindValues($val)->update($arr);
                    }
                }
                $task->money=$money;
                $task->save();
                redirect($url)->with('msg','保存成功！');
            }
        }else{
            if($task->status==1){
                $task->status=2;
                $task->reply_uid=$this->user_id;
                $task->reply_time=time();
                $task->save();
            }
            $task->shipping_company=$linkPage->echoLink('shipping_company',$task->shipping_company);
            $data['task']=$task;
            $data['order']=$task->PrintOrder();
            $data['print_company']=$linkPage->echoLink('print_company','',array('name'=>'company'));
            $this->view('printTask', $data);
        }
    }

    public function orderDel(Request $request,PrintOrder $printOrder)
    {
        $id=$request->get('id');
        $order=$printOrder->findOrFail($id);
        $printTask=new PrintTask();
        $task=$printTask->find($order->task_id);
        if($this->user_typeid!=2){
            if($order->reply_id!=$this->user_id){
                redirect()->back()->with('error','权限异常！');
            }
            if($task->status >=4){
                //支付之前可以操作
                redirect()->back()->with('error','禁止该操作，状态异常！');
            }
        }else{
            $order->delete($id);


            $orders=$task->PrintOrder();
            $money=0;
            foreach ($orders as $o){
                $money=math($money,$o->money,'+',2);
            }
            $task->money=$money;
            $task->save();

            $url="printTask/show/?task_id={$request->get('task_id')}&page={$request->get('page')}";
            redirect($url)->with('msg','删除成功！');
        }
    }
    
    
    public function editShipping(Request $request,PrintTask $printTask)
    {
        $id=$request->post('task_id');
        $page=$request->post('page');
        $task=$printTask->findOrFail($id);
        if($task->status!=4 && $task->status!=5){
            //待发或己发货
            redirect()->back()->with('error','禁止该操作，状态异常！');
        }
        $task->shipping_company=$request->post('shipping_company');
        $task->shipping_no=$request->post('shipping_no');
        $task->shipping_fee=(float)$request->post('shipping_fee');
        $task->shipping_time=time();
        $task->status=5;
        $task->save();
        $url="printTask/show/?task_id={$id}&page={$page}";
        redirect($url)->with('msg','保存成功！');
    }

    public function checkOrder(Request $request,PrintOrder $printOrder)
    {
        if(isset($_GET['id'])){
            $id=$request->get('id');
            $status=$request->get('status');
            $order=$printOrder->findOrFail($id);
            if($order->status==1 && in_array($status,array(2,3))){
                $order->status=$status;
                $order->save();
                redirect()->back()->with('msg','操成成功！');
            }else{
                redirect()->back()->with('error','异常');
            }
        }else{
            $data['orderList']=DB::table('print_order o')->select("o.*")
                ->leftJoin('print_task t','o.task_id=t.id')
                ->where("t.status>=4")
                ->orderBy('o.id desc')
                ->page($_GET['page'],10);
            $this->view('printTask', $data);
        }
    }
}