<?php
namespace App\Controller\Api;

use App\Model\AccountLog;
use System\Lib\DB;

class UserController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }

    //获取用户信息
    public function info()
    {
        $data=$this->data;
        $row = DB::table('app_user au')->select('u.*')
            ->leftJoin('user u', 'au.user_id=u.id')
            ->where('au.app_id=? and au.openid=?')
            ->bindValues(array($this->appid, $data['openid']))
            ->row();
        if($row){
            $user=array(
                'openid' => $data['openid'],
                'username' => $row['username'],
                'portrait' => $row['portrait'],
                'qq' => $row['qq'],
                'tel' => $row['tel'],
                'address' => $row['address'],
                'invite_userid' => $row['invite_userid'],
                'email' =>$row['email']
            );
            return $this->returnSuccess($user);
        }else{
            return $this->returnError('not find openid：'.$data['openid']);
        }
    }

    //获取用户资金
    public function fund()
    {
        $data=$this->data;
        $row = DB::table('app_user au')->select('a.*')
            ->leftJoin('account a', 'au.user_id=a.user_id')
            ->where('au.app_id=? and au.openid=?')
            ->bindValues(array($this->appid, $data['openid']))
            ->row();
        if($row){
            unset($row['user_id']);
            unset($row['signature']);
            unset($row['created_at']);
            $row['openid']=$data['openid'];
            return $this->returnSuccess($row);
        }else{
            return $this->returnError('not find openid：'.$data['openid']);
        }
    }

    //支出、收入 改变用户资金
    public function receivables(AccountLog $accountLog)
    {
        $data=$this->data;
        $pay_order=array(
            'app_id'=>$this->appid,
            'openid'=>$data['openid'],
            'user_id'=>(int)$data['user_id'],
            'body'=>$data['body'],
            'app_order_no'=>$data['order_no'],
            'type'=>$data['type'],
            'status'=>0,
            'remark'=>$data['remark'],
            'label'=>$data['label'],
            'data'=>json_encode($data['data']),
            'signature'=>$data['sign'],
            'addip'=>ip(),
            'created_at'=>time()
        );
        try {
            DB::beginTransaction();

            $pay_order_id=DB::table('pay_order')->insertGetId($pay_order);
            foreach ($data['data'] as $item){
                $item['pay_order_id']=$pay_order_id;
                $item['app_order_no']=$data['app_order_no'];
                $item['label']=$data['label'];
                $accountLog->addLog($item);
            }
            DB::table('pay_order')->where("id={$pay_order_id}")->limit(1)->update(array('status'=>1));

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->returnError("Failed: " .$e->getMessage());
        }
        return $this->returnSuccess();
    }
}


/*
$params=array(
    'appid'=>'shop',
    'time'=>time(),
    'order_no'=>time().rand(10000,99999),
    'openid'=>'',
    'user_id'=>'',
    'body'=>'test body',
    'type'=>1,
    'remark'=>'test remark',
    'label'=>'label',
    'data'=>array(
        array(
            'openid'=>'3321411135799d72d66280403804743',
            'type'=>1,
            'remark'=>'收入',
            'funds_available'=>10,
            'integral_available'=>100
        ),
        array(
            'openid'=>'5910888675799dc46de8a2857962257',
            'type'=>2,
            'remark'=>'消费了',
            'funds_available'=>-10,
            'integral_available'=>-100
        )
    )
);*/