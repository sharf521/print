<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/27
 * Time: 23:12
 */

namespace App\Controller\Admin;


use App\Model\AccountLog;
use App\Model\AccountRecharge;
use App\Model\User;
use System\Lib\DB;
use System\Lib\Request;

class RechargeController extends AdminController
{
    public function index(AccountRecharge $recharge,Request $request,User $user)
    {
        $where=" 1=1";
        $page=$request->get('page');
        $type=$request->get('type');
        $status=$request->get('status');
        $username=$request->get('username');
        $starttime=$request->get('starttime');
        $endtime=$request->get('endtime');
        if(!empty($username)){
            $user=$user->where("username='{$username}'")->first();
            $where.=" and user_id='{$user->id}'";
        }
        if(!empty($status)){
            $where.=" and status=".$status;
        }
        if(!empty($type)){
            $where.=" and type=".$type;
        }
        if(!empty($starttime)){
            $where.=" and created_at>=".strtotime($starttime);
        }
        if(!empty($endtime)){
            $where.=" and created_at<".strtotime($endtime);
        }
        $data['result']=$recharge->where($where)->orderBy('id desc')->pager($page);
        $this->view('recharge',$data);
    }

    public function edit(AccountRecharge $accountRecharge,Request $request,AccountLog $accountLog)
    {
        $row=$accountRecharge->findOrFail($request->id);
        if ($_POST) {
            if (empty($_POST['status'])) {
                redirect()->back()->with('error', '审核状态必选');
            } elseif (empty($_POST['verify_remark'])) {
                redirect()->back()->with('error', '审核备注不能为空');
            } elseif ($row->status == 0) {
                $arr = array();
                $arr['verify_userid'] = $this->user_id;
                $arr['id'] = $_POST['id'];
                $arr['status'] = $_POST['status'];
                $arr['verify_time'] = time();
                $arr['verify_remark'] = $_POST['verify_remark'];
                DB::table('account_recharge')->where("id={$request->id}")->limit(1)->update($arr);
                if ($_POST['status'] == 1) {
                    $log = array(
                        'user_id' => $row->user_id,
                        'type' => $row->type,
                        'funds_available' => math($row->money, $row->fee, '-'),
                        'remark' => '审核充值：' . $row->id
                    );
                    $accountLog->addLog($log);
                }
                redirect('recharge/?page='.$request->page)->with('msg', '操作成功！');
            } else {
                redirect()->back()->with('error', '己审核！！');
            }
        } else {
            $data['row'] = $row;
            $this->view('recharge', $data);
        }
    }
}