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
                $_turnover_available=0;//额外变动的周转金
                foreach ($arr_col as $col){
                    if(isset($data[$col])){
                        if($col=='funds_available'){//入帐
                            if($data['funds_available']>0){
                                //是否欠周转金
                                $owe=(float)math($account['turnover_credit'],$account['turnover_available'],'-',2);
                                if($owe>0){
                                    /*
                                     * 充值金额可以还清欠款
                                     * 周转金:增加所欠的欠款
                                     * 可用资金:增加 还清欠款后 剩余的金额
                                     * */
                                    if($data['funds_available']>=$owe){
                                        $data['funds_available']=math($data['funds_available'],$owe,'-',2);
                                        $_turnover_available=$owe;
                                    }else{
                                        $data['funds_available']=0;
                                        $_turnover_available=$data['funds_available'];
                                    }
                                }
                            }else{ //出帐
                                if(in_array($data['type'],array(14,15))){
                                    //买pos,买车 可以使用周转金
                                    $owe=(float)math($account['funds_available'],$data['funds_available'],'+',2);
                                    if($owe<0){
                                        //出现欠款 把可用资金减为0
                                        $data['funds_available']='-'.$account['funds_available'];
                                        $_turnover_available=$owe;
                                    }
                                }
                            }
                        }
                        $log[$col]=$data[$col];
                        $account[$col]=math($account[$col],$data[$col],'+',5);
                        $log[$col.'_now']=$account[$col];
                    }else{
                        $log[$col]=0;
                    }
                }
                if($_turnover_available!=0){
                    $log['turnover_available']=math($log['turnover_available'],$_turnover_available,'+',2);
                    $account['turnover_available']=math($account['turnover_available'],$_turnover_available,'+',2);
                    $log['turnover_available_now']=$account['turnover_available'];
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
        if(!empty($data['label'])){
            $where.=" and label='{$data['label']}'";
        }
        if(!empty($data['label'])){
            $where.=" and label='{$data['label']}'";
        }
        if(!empty($data['user_id'])){
            $where.=" and user_id='{$data['user_id']}'";
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
            if($value->security_deposit!=0){
                if($value->security_deposit>0){
                    $change.="保证金：+{$value->security_deposit}<br>";
                }else{
                    $change.="保证金：{$value->security_deposit}<br>";
                }
                $now.="当前保证金：{$value->security_deposit}<br>";
            }
            if($value->turnover_available!=0){
                if($value->turnover_available>0){
                    $change.="可用周转金：+{$value->turnover_available}.<br>";
                }else{
                    $change.="可用周转金：{$value->turnover_available}.<br>";
                }
                $now.="可用周转金：{$value->turnover_available_now}<br>";
            }
            if($value->turnover_credit!=0){
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

    public function user()
    {
       return $this->hasOne('User','id','user_id');
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