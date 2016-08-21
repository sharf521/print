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

    function index(PrintTask $printTask,LinkPage $linkPage,Request $request)
    {
        if($this->user_typeid==2){
            $where = " 1=1 ";
        }else{
            $where = " (status=1 || reply_uid={$this->user_id} )";
        }

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
        $starttime=$request->get('starttime');
        $endtime=$request->get('endtime');
        if(!empty($starttime)){
            $where.=" and created_at>=".strtotime($starttime);
        }
        if(!empty($endtime)){
            $where.=" and created_at<".strtotime($endtime);
        }
        $data = $printTask->where($where)->orderBy('id desc')->pager($_GET['page'], 10);
        $data['print_type']=$linkPage->echoLink('print_type',$_GET['print_type']);
        $this->view('printTask', $data);
    }

    public function show(Request $request,PrintTask $printTask,LinkPage $linkPage)
    {
        $id=$request->get('task_id');
        $task=$printTask->findOrFail($id);
        if($this->user_typeid!=2 && $task->status!=1 && $task->reply_uid!=$this->user_id){
            redirect()->back()->with('error','权限异常！');
        }
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

    public function taskAdd(Request $request,PrintTask $printTask,LinkPage $linkPage)
    {
        $user_id=$request->id;
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
            $printTask->user_id=$user_id;
            $printTask->print_type=$print_type;
            $printTask->remark=$remark;
            $printTask->tel=$tel;
            $printTask->status=2;
            $printTask->reply_uid=$this->user_id;
            $printTask->reply_time=time();
            $printTask->save();
            redirect('printTask/')->with('msg', '下单成功！');
        }else{
            $data['print_type']=$linkPage->echoLink('print_type','',array('type'=>'radio'));
            $data['title_herder']='我要下单';
            $this->view('printTask',$data);
        }
    }

    public function editTask(Request $request,PrintTask $printTask)
    {
        $id=$request->post('task_id');
        $page=$request->post('page');
        $task=$printTask->findOrFail($id);
        if($task->status > 3){
            //支付之前
            redirect()->back()->with('error','禁止该操作，状态异常！');
        }
        $task->remark=$request->post('remark');
        $task->save();
        $url="printTask/show/?task_id={$id}&page={$page}";
        redirect($url)->with('msg','保存成功！');
    }

    public function orderAdd(Request $request,PrintTask $printTask)
    {
        if($_POST){
            $task_id=$request->post('task_id');
            $page=$request->post('page');
            $task=$printTask->findOrFail($task_id);
            if($task->status >=4 ){
                //支付之前可以操作
                redirect()->back()->with('error','禁止该操作，状态异常！');
            }
            $order=new PrintOrder();
            $order->task_id=$task->id;
            $order->reply_id=$this->user_id;
            $order->remark=$_POST['remark'];
            $order->money=(float)$request->post('money');
            $order->company=$request->post('company');
            $order->company_money=(float)$request->post('company_money');
            $order->save();

            $task->status=3;
            $task->money=math($task->money,$order->money,'+',2);
            $task->save();
            $url="printTask/show/?task_id={$task_id}&page={$page}";
            redirect($url)->with('msg','添加成功！');
        }
    }
    public function orderEdit(Request $request,PrintTask $printTask)
    {
        if($_POST){
            $task_id=$request->post('task_id');
            $page=$request->post('page');
            $task=$printTask->findOrFail($task_id);
            if($task->status >=4 ){
                //支付之前可以操作
                redirect()->back()->with('error','禁止该操作，状态异常！');
            }
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
            $url="printTask/show/?task_id={$task_id}&page={$page}";
            redirect($url)->with('msg','保存成功！');
        }
    }

    public function taskDel(Request $request,PrintTask $printTask)
    {
        $id=$request->get('task_id');
        $task=$printTask->findOrFail($id);
        if($this->user_typeid!=2 && $task->reply_uid!=$this->user_id){
            redirect()->back()->with('error','权限异常！');
        }
        $tag=$printTask->del($id);
        if($tag !==true){
            redirect()->back()->with('error',$tag);
        }
        $url="printTask/?page={$request->get('page')}";
        redirect($url)->with('msg','删除成功！');
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


    
}