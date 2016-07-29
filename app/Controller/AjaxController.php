<?php
namespace App\Controller;

use App\Model\Algorithm;
use System\Lib\DB;

class AjaxController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function aa()
    {
        echo 111;
    }

    public function getFbbTree()
    {
        /*
                $id=intval($_REQUEST['id']);
                if($id!=0){
                    //$row=$this->mysql->one('fbb',array('id'=>$id));
                }
                $row=$this->mysql->get_one("select * from {$this->dbfix}fbb order by id limit 1");
                if(empty($row)){
                    echo 'no user';
                    return;
                }
                $user_ids=explode(',',$row['pids']);


                $first_userid=$user_ids[0];

                $first_row=$this->mysql->one('fbb',array('id'=>$first_userid));
                $path=$first_row['pids'];
        */
        $data = $_REQUEST;
        $where = "where 1=1";
        if (!empty($data['user_id'])) {
            $where .= " and user_id={$data['user_id']}";
        }
        if (!empty($data['money'])) {
            $where .= " and money={$data['money']}";
        }
        if (!empty($data['id'])) {
            $pids = DB::table('fbb')->where('id=?')->bindValues($data['id'])->value('pids');
            $where .= " and  pids like '{$pids}%'";
        }

        //$sql="select id,user_id,money,pid,addtime from {$this->dbfix}fbb where status=1 and pids like '{$path}%' order by id";
        $sql = "select id,user_id,money,pid,addtime from {$this->dbfix}fbb {$where} order by id";
        $result2 = DB::get_all($sql);
        // echo json_encode(array_merge($result,$result2));
        echo json_encode($result2);
        /*  foreach($result as $k=>$v)
         {
             $result[$k]['tuijianid']=$v['lishuid'];
         }
         //获取最上层id
         $u_id=$user_id;
         for($i=0;$i<$plevel;$i++)
         {
             if($type==0)
                 $sql="select tuijianid as u_id from {member} where user_id=$u_id limit 1";
             else
                 $sql="select lishuid as u_id from {member} where user_id='$u_id' limit 1";
             $row=$db->get_one($sql);
             if(!empty($row['u_id']))
             {
                 $u_id=$row['u_id'];
             }
             else
             {
                 break;
             }
         }

         $arr_ui=array($u_id);
         getarrid($type,$u_id);
         $str=implode(',',$arr_ui);

         $sql="select a.user_id,a.user_name,a.tuijianid,a.lishuid,b.checktime from {member} a join {my_webserv} b on a.user_id=b.user_id where a.user_id in($str) order by b.checktime";

 */

    }

    public function getZjTree()
    {
        $data = $_REQUEST;
//        $row = $this->mysql->get_one("select * from {$this->dbfix}zj order by id limit 1");
//        if (empty($row)) {
//            echo 'no user';
//            return;
//        }
//        $user_ids = explode(',', $row['pids']);
//
//
//        $first_id = $user_ids[0];
//
//        $first_row = $this->mysql->one('zj', array('id' => $first_id));
//        $path = $first_row['pids'];

        $where = "where 1=1";
        if (!empty($data['user_id'])) {
            $where .= " and user_id={$data['user_id']}";
        }
        if (!empty($data['plate'])) {
            $where .= " and plate={$data['plate']}";
        }
        if (!empty($data['id'])) {
            $pids = DB::table('zj')->where('id=?')->bindValues($data['id'])->value('pids');
            $where .= " and  pids like '{$pids}%'";
        }

        //$sql="select id,user_id,money,pid,addtime from {$this->dbfix}zj where pids like '{$path}%' order by id";
        $sql = "select id,user_id,money,pid,addtime from {$this->dbfix}zj {$where} order by id";
        $result2 = DB::get_all($sql);
        //print_r($result2);
        echo json_encode($result2);

    }

    public function algorithmSend()
    {
        $data['date']=$_POST['date'];
        $algorith=new Algorithm();
        $return=$algorith->send($data);
        if($return===true){
            echo '完成';
        }else{
            echo $return;
        }
    }
}