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
            if($request->post('act')=='orderAdd'){
                $order=new PrintOrder();
                $order->task_id=$task->id;
                $order->reply_id=$this->user_id;
                $order->remark=$_POST['remark'];
                $order->money=(float)$request->post('money');
                $order->company=$request->post('company');
                $order->company_money=(float)$request->post('company_money');
                $order->save();

                $task->status=3;
                $task->save();
                redirect($url)->with('msg','添加成功！');
            }elseif($request->post('act')=='orderEdit'){
                $id = $request->post('id');
                foreach ($id as $key => $val) {
                    if(!empty($val)){
                        $arr=array(
                            'remark'=>$request->post('remark')[$key],
                            'money'=>(float)$request->post('money')[$key],
                            'company'=>$request->post('company')[$key],
                            'company_money'=>(float)$request->post('company_money')[$key]
                        );
                        DB::table('print_order')->where('id=?')->bindValues($val)->update($arr);
                    }
                }
                redirect($url)->with('msg','保存成功！');
            }
        }else{
            if($task->status==1){
                $task->status=2;
                $task->reply_uid=$this->user_id;
                $task->reply_time=time();
                $task->save();
            }
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
        if($this->user_typeid!=2 && $order->reply_id!=$this->user_id){
            redirect()->back()->with('error','权限异常！');
        }else{
            $order->delete($id);
            $url="printTask/show/?task_id={$request->get('task_id')}&page={$request->get('page')}";
            redirect($url)->with('msg','删除成功！');
        }
    }
}