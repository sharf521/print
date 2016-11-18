<?php
namespace App\Controller\Member;

use App\Model\Account;
use App\Model\AccountCash;
use App\Model\AccountLog;
use App\Model\AccountRecharge;
use App\Model\Rebate;
use App\Model\System;
use App\Model\User;
use System\Lib\DB;
use System\Lib\Request;

class AccountController extends MemberController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index(Account $account)
    {
        $data['account'] =$account->find($this->user_id);
        $this->view('account',$data);
    }

    //线下冲值
    public function recharge(Request $request)
    {
        if ($_POST) {
            $error = "";
            $money=(float)$request->post('money');
            if ($money==0) {
                $error .= "充值金额不能为空<br>";
            }
            if ($money < 1000 || $money>50000) {
                $error .= "线下充值金额在1千至5万之间<br>";
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
                    'money' => sprintf("%.2f",$money),
                    'fee' => 0,
                    'payment' => $_POST['payment'],
                    'type' => 2,
                    'remark' => $_POST['remark'],
                    'created_at'=>time(),
                    'addip' => ip()
                );
                DB::table('account_recharge')->insert($data);
                redirect('account/rechargeLog')->with('msg', '操作成功，等待财务审核！');
            }
        } else {
            $data['title_herder']='我要充值';
            $data['user']=$this->user;
            $this->view('accountRecharge',$data);
        }
    }

    public function rechargeLog(AccountRecharge $recharge,Request $request)
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
        $data['title_herder']='充值记录';
        $this->view('accountRecharge',$data);
    }

    //提现
    public function cash(System $system,Request $request,AccountCash $accountCash)
    {
        $cash_rate=(float)$system->getCode('cash_rate');
        $account=$this->user->Account();
        $bank=$this->user->Bank();
        if ($_POST) {
            $total=(float)$request->post('total');
            if($total < 50 || $total > 50000){
                redirect()->back()->with('error','提现范围50元-50000元！');
            }
            if($total > $account->funds_available){
                redirect()->back()->with('error','提现金额超过可提现金额！');
            }
            $checkPwd=$this->user->checkPayPwd($request->post('zf_password'),$this->user);
            if($checkPwd!==true){
                redirect()->back()->with('error','支付密码错误！');
            }
            $fee=round_money(math($total,$cash_rate,'*',3),2,2);
            if($fee<5){$fee=5;}
            $accountCash->user_id=$this->user_id;
            $accountCash->name=$this->user->name;
            $accountCash->bank=$bank->bank;
            $accountCash->branch=$bank->branch;
            $accountCash->card_no=$bank->card_no;
            $accountCash->total=$total;
            $accountCash->credited=math($total,$fee,'-',2);
            $accountCash->fee=$fee;
            $accountCash->status=1;
            $accountCash->addip=ip();
            $insertId=$accountCash->save(true);

            $account=new Account();
            $log = array();
            $log['user_id'] = $this->user_id;
            $log['type'] = 'cash_apply';
            $log['funds_available'] ='-'.$total;
            $log['funds_freeze']=$total;
            $log['label'] = "cash_{$insertId}";
            $log['remark'] = "提现ID：{$insertId}";
            $account->addLog($log);
            redirect('account/cashLog')->with('msg','申请提现成功，静等审核！');
        } else {
            $data['cash_rate']=$cash_rate;
            $data['account']=$account;
            $data['bank']=$bank;
            $data['title_herder']='我要提现';
            $this->view('accountCash',$data);
        }
    }

    public function cashLog(Request $request,AccountCash $accountCash)
    {
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
        $data['result']=$accountCash->where($where)->orderBy('id desc')->pager($page);

        $data['title_herder']='提现记录';
        $this->view('accountCash',$data);
    }

    //资金流水
    public function log(Request $request,AccountLog $accountLog)
    {
        $arr=array(
            'user_id'=>$this->user_id,
            'starttime'=>$request->get('starttime'),
            'endtime'=>$request->get('endtime')
        );
        $data['result']=$accountLog->getList($arr);
        $data['title_herder']='资金流水';
        $this->view('account',$data);
    }
}