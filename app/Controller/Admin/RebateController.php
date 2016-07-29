<?php
namespace App\Controller\Admin;

use App\Model\Rebate;
use System\Lib\DB;

class RebateController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new Rebate();
    }

    function index()
    {
        $arr = array(
            'typeid' => (int)$_GET['typeid'],
            'startdate' => $_GET['startdate'],
            'enddate' => $_GET['enddate'],
            'status' => $_GET['status'],
            'user_id' => (int)$_GET['user_id'],
            'page' => (int)$_REQUEST['page'],
            'epage' => 10
        );
        $data['result'] = $this->model->getRebateByPage($arr);
        $this->view('rebate', $data);
    }

    function add($data)
    {
        if ($_POST) {
            $post = array(
                'user_id' => $_POST['user_id'],
                'site_id' => $_POST['site_id'],
                'typeid' => $_POST['typeid'],
                'money' => $_POST['money']
            );
            $this->model->addRebate($post);
            show_msg(array('添加成功', '', $this->base_url('rebate')));
        } else {
            $this->view('rebate', $data);
        }
    }

    function calRebate()
    {
        $return=$this->model->calRebate();
        if ($return === true) {
            show_msg(array('完成', '', $this->base_url('rebate')));
        } else {
            show_msg(array('失败！！'));
        }
    }

    function delete()
    {
        show_msg(array('删除成功', '', $this->base_url('usertype')));
        //$this->redirect('usertype');
    }

    function rebatelist()
    {
        $arr = array(
            'typeid' => (int)$_GET['typeid'],
            'startdate' => $_GET['startdate'],
            'enddate' => $_GET['enddate'],
            'user_id' => (int)$_GET['user_id'],
            'page' => (int)$_REQUEST['page'],
            'epage' => 10
        );
        $data['result'] = $this->model->getRebateListByPage($arr);
        $this->view('rebate', $data);
    }

    function rebatelog()
    {
        $arr = array(
            'typeid' => $_GET['typeid'],
            'startdate' => $_GET['startdate'],
            'enddate' => $_GET['enddate'],
            'user_id' => (int)$_GET['user_id'],
            'rebate_id' => (int)$_GET['rebate_id'],
            'money' => (float)$_GET['money'],
            'page' => (int)$_REQUEST['page'],
            'epage' => 10
        );
        $data['result'] = $this->model->getRebateLogByPage($arr);
        $this->view('rebate', $data);
    }
}