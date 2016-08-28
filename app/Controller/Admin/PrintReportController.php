<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/28
 * Time: 21:21
 */

namespace app\Controller\Admin;


use App\Model\LinkPage;
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
}