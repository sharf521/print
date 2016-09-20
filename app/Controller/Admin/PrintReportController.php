<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/28
 * Time: 21:21
 */

namespace App\Controller\Admin;


use App\Model\LinkPage;
use App\Model\PrintTask;
use App\Model\User;
use System\Lib\Request;
use System\Lib\DB;

class PrintReportController extends AdminController
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
        $data['orderList']=DB::table('print_task t')->select("t.*,o.remark as order_remark,o.money as order_money,o.company,o.company_money,o.status as order_status")
            ->join('print_order o','o.task_id=t.id')
            ->where($where)
            ->orderBy('t.id desc')
            ->page($_GET['page'],10,\PDO::FETCH_OBJ);
        $data['print_company']=$linkPage->echoLink('print_company',$company,array('name'=>'company'));
        $this->view('printReport', $data);
    }
    
    public function excel(Request $request,User $User)
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
        $data['orderList']=DB::table('print_task t')->select("t.*,o.remark as order_remark,o.money as order_money,o.company,o.company_money,o.status as order_status")
            ->join('print_order o','o.task_id=t.id')
            ->where($where)
            ->orderBy('t.id desc')
            ->all(\PDO::FETCH_OBJ);


        $data_array=array();
        $task_id=0;
        foreach ($data['orderList'] as $item) {
            $user=$User->find($item->user_id);
            $reply=$User->find($item->reply_uid);
            $invite=$User->find($user->invite_userid);
            $_arr=array();
            if($task_id!=$item->id){
                array_push($_arr,$item->id);
                array_push($_arr,$item->print_type);
                array_push($_arr,$user->nickname);
                array_push($_arr,date('y-m-d H:i',$item->paytime));
                array_push($_arr,$reply->nickname);
                array_push($_arr,$invite->nickname);
                array_push($_arr,$item->shipping_company);
                array_push($_arr,':'.$item->shipping_no);
                array_push($_arr,$item->shipping_fee);
            }else{
                array_push($_arr,'','','','','','','','','');
            }
            array_push($_arr,$item->order_remark);
            array_push($_arr,$item->order_money);
            array_push($_arr,$item->company);
            array_push($_arr,$item->company_money);
            array_push($_arr,$user->getLinkPageName('check_status', $item->order_status) );
            $task_id=$item->id;
            array_push($data_array,$_arr);
        }
        $title=array('订单ID','类型','客户名称','付款时间','接待人','邀请人','快递公司','快递单号','快递金额','订做要求','价格','外联厂商','外协金额','状态');
        excel('工单报表'.date('y-m-d'),$title,$data_array);
    }
}