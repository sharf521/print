<?php
namespace App\Model;

use System\Lib\DB;

class FBB extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table=$this->dbfix.'fbb';
        $this->fields=array('id','site_id','user_id','money','income','pid','pids','position','addtime','status');
    }

    function add($data)
    {
        if(empty($data['user_id']) || empty($data['money'])){
            $return=array('code'=>0,'msg'=>'参数错误！');
        }
        else{
            $pid=(int)$data['pid'];
            $arr=array(
                'site_id' => 1,
                'user_id' => (int)$data['user_id'],
                'pid' => $pid,
                'pids'=>'',
                'money' => (float)($data['money']),
                'income'=>0,
                'addtime' => date('Y-m-d H:i:s'),
                'status' => 0
            );
            $row=DB::table('fbb')->where('user_id=?')->bindValues($data['user_id'])->row();
            if($row){
                $return=array('code'=>1,'msg'=>'用户己购买！');
                return json_encode($return);
            }
            $pids='';
            if ($pid != 0) {
                $row=DB::table('fbb')->where('id=?')->bindValues($pid)->row();
                if (!$row) {
                    $return = array('code' => 2, 'msg' => 'pid错误！');
                    return json_encode($return);
                } else {
                    $pids = $row['pids'];
                    //$_row = $this->mysql->get_one("select count(id) as count1 from {$this->dbfix}fbb where pid={$pid}");
                    //$arr['position'] = $_row['count1'] + 1;
                    $count1 = DB::table('fbb')->where("pid={$pid}")->value('count(id) as count1');
                    $arr['position'] = intval($count1) + 1;
                }
            }
            //$result=$this->mysql->insert("fbb",$arr);
            //$id=$this->mysql->insert_id();
            $id=DB::table('fbb')->insertGetId($arr);
            $pids=$pids.$id.',';
            DB::table('fbb')->where("id={$id}")->limit(1)->update(array('pids'=>$pids));
            if($id){
                $return=array('code'=>200,'msg'=>'ok');
            }
            else{
                $return=array('code'=>0,'msg'=>'内部错误');
            }
        }
        return json_encode($return);
    }

    function calFbb()
    {
        try {
            DB::beginTransaction();
            $this->calFbbDo();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            echo "Failed: " . $e->getMessage();
            return false;
        }
        return true;
    }
    private function calFbbDo()
    {
        $where="status=0 and addtime<'".date('Y-m-d')."'";
        $result=DB::table('fbb')->select('id,user_id,pids,`position`,money')->where($where)->orderBy('id')->all();
        foreach ($result as $row) {
            DB::table('fbb')->where("id={$row['id']}")->limit(1)->update(array('status' => 1));//设为己处理
            $pids = rtrim($row['pids'], ',');//去除最后一个，
            if (!empty($pids)) {
                $arr_pid = explode(',', $pids);
                array_pop($arr_pid);
                $arr_pid = array_reverse($arr_pid);
                $arr_pos = array();//上面所有元素的位置
                $i = 1;
                foreach ($arr_pid as $pid) {
                    $prow=DB::table('fbb')->where("id={$pid}")->row();
                    $arr_pos[$i] = $prow['position'];
                    //$money = $row['money'] < $prow['money'] ? $row['money'] : $prow['money'];
                    $money = $row['money'];
                    $fbb_log = array(
                        'user_id' => $prow['user_id'],
                        'money'=>0,
                        'fbb_id' => $prow['id'],
                        'in_fbb_id' => $row['id'],
                        'in_user_id' => $row['user_id'],
                        'layer' => $i,
                        'typeid' => '2,1',
                        'addtime' => date('Y-m-d H:i:s')
                    );
                    if ($i == 1) {
                        if ($row['position'] == 1) {
                            $fbb_log['money'] = bcmul($money, 0.2, 5);//400
                        } else {
                            $fbb_log['money'] = bcmul($money, 0.65, 5);//1300
                        }
                    } elseif ($i == 2) {
                        if ($row['position'] == 1 && $arr_pos[1] >= 2) {
                            $fbb_log['money'] = bcmul($money, 0.5, 5);//1000
                        } else {
                            // $fbb_log['money'] = bcmul($money, 0.01, 5);//20
                        }
                    } else {
                        if ($this->isFbb2_1($row['position'], $arr_pos)) {
                            $fbb_log['money'] = bcmul($money, 0.5, 5);//1000
                        } elseif ($this->isFbb2_2_1($row['position'], $arr_pos)) {
                            $fbb_log['money'] = bcmul($money, 0.1, 5);//200
                        } else {
                            //$fbb_log['money']=0;
                        }
                    }
                    if ($fbb_log['money'] != 0) {
                        DB::table('fbb')->where("id={$prow['id']}")->limit(1)->update(array('income' => bcadd($fbb_log['money'], $prow['income'], 5)));
                        DB::table('fbb_log')->insert($fbb_log);
                    }
                    //见点给
                    if ($i <= 5) {
                        $fbb_log['money'] = bcmul($money, 0.01, 5);//20
                    } else {
                        $fbb_log['money'] = bcmul($money, 0.005, 5);//10
                    }
                    DB::query("update {$this->dbfix}fbb set income=income+{$fbb_log['money']} where id={$prow['id']} limit 1");
                    DB::table('fbb_log')->insert($fbb_log);
                    if ($i >= 15) {
                        break;
                    }
                    $i++;
                }
            }
        }
    }


    private function isFbb2_1($my_pos,$arr_pos){
        if($my_pos!=1) return false;//当前位置必须是上级的第一个推荐
        array_pop($arr_pos);//删除最后一个元素
        $last1=array_pop($arr_pos);//删除最后一个元素，返回最后一个
        $return=true;
        foreach($arr_pos as $pos){
            if($pos!=1){
                $return=false;
                break;
            }
        }
        if($return && $last1>=2){
            return true;
        }
        return false;
    }
    private function isFbb2_2_1($my_pos,$arr_pos){
        if($my_pos!=1) return false;
        array_pop($arr_pos);//删除最后一个元素
        $last1=array_pop($arr_pos);//删除最后一个元素
        $last2=array_pop($arr_pos);//最后第二个
        $return=true;
        foreach($arr_pos as $pos){
            if($pos!=1){
                $return=false;
                break;
            }
        }
        if($return && $last2==2 && $last1>=2){
            return true;
        }
        return false;
    }

    ///////////////////////////////////////////////////
    function getFbbByPage($data)
    {
        $_select="r.*";
        $where="1=1";
        if(!empty($data['user_id']))
        {
            $where.=" and r.user_id={$data['user_id']}";
        }
        if(!empty($data['money']))
        {
            $where.=" and r.money={$data['money']}";
        }
        if(!empty($data['id']))
        {
            $pids=DB::table('fbb')->where("id=?")->bindValues($data['id'])->value('pids');
            $where.=" and  r.pids like '{$pids}%'";
        }
        $result=DB::table('fbb r')->select($_select)->leftJoin("user u","r.user_id=u.id")->where($where)->orderBy("r.id desc")->page($data['page'],$data['epage']);
        return $result;
    }
    function getFbbLogByPage($data){
        $_select="fl.*";
        $where="where 1=1";
        if(!empty($data['user_id']))
        {
            $where.=" and fl.user_id={$data['user_id']}";
        }
        if(!empty($data['money']))
        {
            $where.=" and fl.money={$data['money']}";
        }
        if(!empty($data['fbb_id']))
        {
            $where.=" and fl.fbb_id={$data['fbb_id']}";
        }
        if(!empty($data['in_fbb_id']))
        {
            $where.=" and fl.in_fbb_id={$data['in_fbb_id']}";
        }
        $sql = "select SELECT from {$this->dbfix}fbb_log fl {$where} ORDER LIMIT";

        $_order=isset($data['order'])?' order by '.$data['order']:'order by fl.id desc';
        //总条数
        $row=DB::get_one(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array('count(1) as num', '', ''), $sql));
        $total = $row['num'];

        $epage = empty($data['epage'])?10:$data['epage'];
        $page=$data['page'];
        if(!empty($page))
        {
            $index = $epage * ($page - 1);
        }
        else
        {
            $index=0;$page=1;
        }
        if($index>$total){$index=0;$page=1;}
        $limit = " limit {$index}, {$epage}";
        // echo str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select, $_order, $limit), $sql);
        $list = DB::get_all(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select, $_order, $limit), $sql));
        global $pager;
        $pager->page=$page;
        $pager->epage=$epage;
        $pager->total=$total;
        return array(
            'list' => $list,
            'total' => $total,
            'page' => $pager->show()
        );
    }



}