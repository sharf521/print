<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/21
 * Time: 13:08
 */

namespace app\Controller\Admin;

use App\Model\LinkPage;
use App\Model\PrintOrder;
use System\Lib\DB;
use System\Lib\Request;

class PrintOrderController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index(Request $request,LinkPage $linkPage)
    {
        $where=" t.status>=4";
        $q=$request->get('q');
        $company=$request->get('company');
        $starttime=$request->get('starttime');
        $endtime=$request->get('endtime');
        if(!empty($starttime)){
            $where.=" and t.created_at>=".strtotime($starttime);
        }
        if(!empty($endtime)){
            $where.=" and t.created_at<".strtotime($endtime);
        }
        if(!empty($q)){
            $where.=" and o.remark like '%{$q}%'";
        }
        if(!empty($company)){
            $where.=" and company='{$company}'";
        }
        $data['orderList']=DB::table('print_order o')->select("o.*")
            ->leftJoin('print_task t','o.task_id=t.id')
            ->where($where)
            ->orderBy('o.id desc')
            ->page($_GET['page'],10);
        $data['print_company']=$linkPage->echoLink('print_company',$company,array('name'=>'company'));
        $this->view('printOrder', $data);
    }

    public function check(Request $request,PrintOrder $printOrder)
    {
        if(isset($_GET['id'])){
            $id=$request->get('id');
            $status=$request->get('status');
            $order=$printOrder->findOrFail($id);
            if($order->status !=2 && in_array($status,array(2,3))){
                $order->status=$status;
                $order->save();
                redirect()->back()->with('msg','操成成功！');
            }else{
                redirect()->back()->with('error','异常');
            }
        }
    }

    public function edit(Request $request,PrintOrder $printOrder,LinkPage $linkPage)
    {
        $id=$request->id;
        $page=$request->page;
        $type=$request->get('type');
        $order=$printOrder->findOrFail($id);
        if($order->status ==2){
            redirect()->back()->with('error','己审核，禁止操作！');
        }
        if($_POST){
            $order->remark=$_POST['remark'];
            $order->company=$request->post('company');
            $order->company_money=(float)$request->post('company_money');
            $order->save();

            if($type=='printOrder'){
                $url="printOrder/?page={$page}";
            }elseif ($type=='show'){
                $url="printTask/show/?task_id={$order->task_id}?page={$page}";
            }
            redirect($url)->with('msg','保存成功！');
        }else{
            $data['print_company']=$linkPage->echoLink('print_company',$order->company,array('name'=>'company'));
            $data['order']=$order;
            $this->view('printOrder', $data);
        }
    }
}