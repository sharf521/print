<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/27
 * Time: 12:54
 */

namespace App\Model;


use System\Lib\DB;

class AccountLog extends Model
{
    protected $table='account_log';
    function addLog($data)
    {
        $insert=false;
        if(isset($data['user_id'])){
            $user_id=(int)$data['user_id'];
        }elseif (isset($data['openid'])){
            $user_id=DB::table('app_user')->where("openid=?")->bindValues($data['openid'])->value('user_id','int');
        }
        if($user_id>0){
            $fp = fopen(ROOT."/public/data/money.txt" ,'w+');
            if(flock($fp , LOCK_EX))
            {
                $account=DB::table('account')->where("user_id={$user_id}")->row();
                if(empty($account)){
                    $insert=true;
                    $account=array(
                        'user_id'=>$user_id,
                        'funds_available'=>0,
                        'funds_freeze'=>0,
                        'integral_available'=>0,
                        'integral_freeze'=>0,
                        'security_deposit'=>0,
                        'turnover_available'=>0,
                        'turnover_credit'=>0,
                        'created_at'=>time()
                    );
                }
                $log=array(
                    'user_id'=>$user_id,
                    'pay_order_id'=>(int)$data['pay_order_id'],
                    'app_order_no'=>$data['app_order_no'],
                    'type'=>$data['type'],
                    'remark'=>$data['remark'],
                    'label'=>$data['label'],
                    'created_at'=>time(),
                    'addip'=>ip()
                );
                $arr_col=array('funds_available','funds_freeze','integral_available','integral_freeze','security_deposit','turnover_available','turnover_credit');
                foreach ($arr_col as $col){
                    if(isset($data[$col])){
                        $log[$col]=$data[$col];
                        $account[$col]=math($account[$col],$data[$col],'+',2);
                        $log[$col.'_now']=$account[$col];
                    }else{
                        $log[$col]=0;
                    }
                }
                $account['signature']=$this->sign($account);
                if($insert){
                    DB::table('account')->insert($account);
                }else{
                    DB::table('account')->where("user_id={$user_id}")->limit(1)->update($account);
                }
                $log['signature']=$this->sign($log);
                $return= DB::table('account_log')->insert($log);
                flock($fp,LOCK_UN);
            }
            fclose($fp);
            return $return;
        }else{
            return 'no param user_id';
        }
    }

    public function getList($data=array())
    {
        $where=" 1=1";
        if(!empty($data['starttime'])){
            $where.=" and created_at>=".strtotime($data['starttime']);
        }
        if(!empty($data['endtime'])){
            $where.=" and created_at<".strtotime($data['endtime']);
        }
        $result=$this->where($where)->orderBy('id desc')->pager(intval($_GET['page']));
        foreach ($result['list'] as $index=>$value){
            $change='';
            $now='';
            if($value->funds_available!=0){
                if($value->funds_available>0){
                    $change.="可用资金：+{$value->funds_available}<br>";
                }else{
                    $change.="可用资金：{$value->funds_available}<br>";
                }
                $now.="当前可用资金：{$value->funds_available_now}<br>";
            }
            if($value->funds_freeze!=0){
                if($value->funds_freeze>0){
                    $change.="冻结资金：+{$value->funds_freeze}<br>";
                }else{
                    $change.="冻结资金：{$value->funds_freeze}<br>";
                }
                $now.="当前冻结资金：{$value->funds_freeze_now}<br>";
            }
            if($value->integral_available!=0){
                if($value->integral_available>0){
                    $change.="可用积分：+{$value->integral_available}<br>";
                }else{
                    $change.="可用积分：{$value->integral_available}<br>";
                }
                $now.="当前可用积分：{$value->integral_available_now}<br>";
            }
            if($value->integral_freeze!=0){
                if($value->integral_freeze>0){
                    $change.="冻结积分：+{$value->integral_freeze}<br>";
                }else{
                    $change.="冻结积分：{$value->integral_freeze}<br>";
                }
                $now.="当前冻结积分：{$value->integral_freeze_now}<br>";
            }
            if($value->integral_freeze!=0){
                if($value->turnover_available>0){
                    $change.="保证金：+{$value->turnover_available}<br>";
                }else{
                    $change.="保证金：{$value->turnover_available}<br>";
                }
                $now.="当前保证金：{$value->turnover_available}<br>";
            }
            if($value->integral_freeze!=0){
                if($value->security_deposit>0){
                    $change.="可用周转金：+{$value->security_deposit}.<br>";
                }else{
                    $change.="可用周转金：{$value->security_deposit}.<br>";
                }
                $now.="可用周转金：{$value->security_deposit_now}<br>";
            }
            if($value->integral_freeze!=0){
                if($value->turnover_credit){
                    $change.="周转金额度：+{$value->turnover_credit}.<br>";
                }else{
                    $change.="周转金额度：{$value->turnover_credit}.<br>";
                }
                $now.="当前周转金额度：{$value->turnover_credit_now}<br>";
            }
            $result['list'][$index]->change=$change;
            $result['list'][$index]->now=$now;
        }
        return $result;
    }


    private function sign($signature)
    {
        $md5key=app('\App\Model\System')->getCode('md5key');
        if (isset($signature['id'])) {
            unset($signature['id']);
        }
        if (isset($signature['signature'])) {
            unset($signature['signature']);
        }
        if (isset($signature['created_at'])) {
            unset($signature['created_at']);
        }
        ksort($signature);
        $jsonStr = json_encode($signature);
        $str = md5($jsonStr.$md5key);
        return strtoupper($str);
    }
}