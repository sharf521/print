<?php
namespace App\Model;

use System\Lib\DB;

class Algorithm extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table=$this->dbfix.'algorithm_log';
    }

    public function collectLog()
    {
        try {
            DB::beginTransaction();

            $lastDate=DB::table('algorithm_log')->orderBy('addtime desc')->value('addtime');
            $date = date('Y-m-d', strtotime($lastDate) + 3600 * 24);
            $tables=array('fbb_log','zj_log','rebate_log');
            foreach($tables as $table){
                $result=DB::table($table)->select('user_id,sum(money) as money,substring(addtime,1,10) as addtime')->where("addtime>'{$date}'")->orderBy("id")->groupBy('substring(addtime,1,10),user_id')->all();
                foreach($result as $row){
                    if ($table != 'rebate_log') {
                        $row['money'] = bcmul($row['money'], 2.52, 5);
                    }
                    $arr=array(
                        'user_id'=>$row['user_id'],
                        'addtime'=>$row['addtime']
                    );
                    $_one=DB::table('algorithm_log')->where($arr)->row();
                    if($_one){
                        $_update=array(
                            'money'=>bcadd($row['money'],$_one['money'],5)
                        );
                        DB::table('algorithm_log')->where("id={$_one['id']}")->limit(1)->update($_update);
                    }else{
                        $arr['money']=$row['money'];
                        $arr['status']=0;
                        DB::table('algorithm_log')->insert($arr);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            echo "Failed: " . $e->getMessage();
            return false;
        }
        return true;
    }

    ///////////////////////////////////////////////////
    public function getLogByPage($data)
    {
        $_select = "*";
        $where = "1=1";
        if (!empty($data['user_id'])) {
            $where .= " and user_id={$data['user_id']}";
        }
        if (!empty($data['money'])) {
            $where .= " and money={$data['money']}";
        }
        if (!empty($data['startdate'])) {
            $where .= " and addtime>='{$data['startdate']}'";
        }
        if (!empty($data['enddate'])) {
            $where .= " and addtime<'{$data['enddate']}'";
        }
        $result = DB::table('algorithm_log')->select($_select)->where($where)->orderBy("id desc")->page($data['page'], $data['epage']);
        $result['money_total']=DB::table('algorithm_log')->where($where)->value("sum(money)");
        return $result;
    }


    //按天统计
    public function listByDays($data)
    {
        $where = "1=1";
        if (!empty($data['startdate'])) {
            $where .= " and addtime>='{$data['startdate']}'";
        }
        if (!empty($data['enddate'])) {
            $where .= " and addtime<'{$data['enddate']}'";
        }
        $result = DB::table('algorithm_log')->select("substring(addtime,1,10) as date,sum(money) as money,status")->where($where)->groupBy('date')->all();
        return $result;
    }

    /**
     * 结算
     * @param $data
     * @return bool
     */
    public function send_do($data)
    {
        global $_G;
        $post=array();
        $post['remark']=$data['date'];
        $post['users']=array();
        $startdate=$data['date'];
        $enddate=date('Y-m-d',strtotime($startdate)+3600*24);
        $result = DB::table('algorithm_log')->select('id,user_id,money')->where("status=0 and addtime>=? and addtime<?")->bindValues(array($startdate,$enddate))->all();
        $i=0;
        foreach($result as $row){
            array_push($post['users'], array('user_id' => $row['user_id'], 'fund' => 0, 'integral' => $row['money']));
            //更新状态
            $_arr=array(
                'send_money'=>$row['money'],
                'send_date'=>date('Y-m-d H:i:s'),
                'status'=>1
            );
            DB::table('algorithm_log')->where("id={$row['id']}")->limit(1)->update($_arr);
        }
        /* $data = [
             'remark' => 'sss',
            'users' =>[
                [
                    'user_id' => 1,
                    'fund' => 0,
                    'integral' => 20,
                ],
                [
                    'user_id' => 2,
                    'fund' => 0,
                    'integral' => 200,
                ],
            ],
        ];*/
        ksort($post);
        foreach ($post['users'] as $i=>$u){
            ksort($post['users'][$i]);
        }
        $post['sign'] = strtoupper(md5(json_encode($post)));

        $html=curl_url($_G['system']['payurl'].'/open/accept',array('data'=>json_encode($post)));
        $result=json_decode($html,true);
        if($result['status']!='success'){
            return $html;
        }
        return true;
    }

    public function send($data)
    {
        try {
            DB::beginTransaction();
            $return = $this->send_do($data);
            if ($return === true) {
                DB::commit();
            } else {
                DB::rollBack();
            }
            return $return;
        } catch (\Exception $e) {
            DB::rollBack();
            echo "Failed: " . $e->getMessage();
            return false;
        }
        return true;
    }



}