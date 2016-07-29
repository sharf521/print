<?php
namespace App\Controller\Member;

use App\Model\AccountLog;
use App\Model\AccountRecharge;
use System\Lib\DB;
use System\Lib\Request;

class AccountController extends MemberController
{
    public function index()
    {

    }

    //线下冲值
    public function recharge()
    {
        if ($_POST) {
            $error = "";
            if (empty($_POST['money'])) {
                $error .= "充值金额不能为空<br>";
            }
            if ($_POST['money'] < 1000) {
                $error .= "充值金额不能低于1000元<br>";
            }
            if (empty($_POST['remark'])) {
                $error .= "充值备注必填<br>";
            }
            if ($error != "") {
                redirect()->back()->with('error', $error);
            } else {
                $data = array(
                    'trade_no' => time() . rand(1000, 9999),
                    'user_id' =>$this->user_id,
                    'status' => 0,
                    'money' => sprintf("%.2f", (float)$_POST['money']),
                    'fee' => 0,
                    'payment' => $_POST['payment'],
                    'type' => 2,
                    'remark' => $_POST['remark'],
                    'created_at'=>time(),
                    'addip' => ip()
                );
                DB::table('account_recharge')->insert($data);
                redirect()->back()->with('msg', '操作成功，等待财务审核！');
            }
        } else {
            $data['user']=$this->user;
            $this->view('account',$data);
        }
    }

    public function rechargeLog(AccountRecharge $recharge,Request $request,AccountLog $accountLog)
    {
//        $log = array();
//        $log['user_id'] = 1;
//        $log['type'] = 1;
//        $log['funds_available'] = 10;
//        $log['remark'] = "在线充值：";
//        $log['label']='AA';
//        $accountLog->addLog($log);

        $page=$request->get('page');
        $starttime=$request->get('starttime');
        $endtime=$request->get('endtime');
        $where=" user_id=".$this->user_id;
        if(!empty($starttime)){
            $where.=" and created_at>=".strtotime($starttime);
        }
        if(!empty($endtime)){
            $where.=" and created_at<".strtotime($endtime);
        }
        $data['result']=$recharge->where($where)->orderBy('id desc')->pager($page);
        $this->view('account',$data);
    }

    //提现
    public function cash()
    {
        if ($_POST) {

        } else {
            $this->view('account');
        }
    }

    public function cashLog()
    {
        $this->view('account');
    }

    //资金流水
    public function log(Request $request,AccountLog $accountLog)
    {
        $arr=array(
            'user_id'=>$this->user_id,
            'starttime'=>$request->get('starttime'),
            'endtime'=>$request->get('$endtime')
        );
        $data['result']=$accountLog->getList($arr);
        $this->view('account',$data);
    }
}