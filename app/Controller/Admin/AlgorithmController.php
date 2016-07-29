<?php
namespace App\Controller\Admin;

use App\Model\Algorithm;
use System\Lib\DB;

class AlgorithmController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->Algorithm=new Algorithm();
    }

    public function index()
    {
        $arr = array(
            'user_id' => (int)$_GET['user_id'],
            'money' => (int)$_GET['money'],
            'startdate' => $_GET['startdate'],
            'enddate' => $_GET['enddate'],
            'page' => (int)$_REQUEST['page'],
            'epage' => 10
        );
        $data['result']=$this->Algorithm->getLogByPage($arr);
        $this->view('algorithm',$data);
    }

    public function getLog()
    {
        $return = $this->Algorithm->collectLog();
        if ($return === true) {
            show_msg(array('完成', '', $this->base_url('algorithm')));
        } else {
            show_msg(array('失败！！'));
        }
    }

    //按天小计
    public function listByDays()
    {
        if(!isset($_GET['startdate'])){
            $_GET['startdate']=date('Y-m-d',strtotime(date('Y-m-d'))-3600*24*2);
        }
        $arr = array(
            'startdate' => $_GET['startdate'],
            'enddate' => $_GET['enddate']
        );
        $data['result']=$this->Algorithm->listByDays($arr);
        $this->view('algorithm',$data);
    }

}