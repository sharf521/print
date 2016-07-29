<?php
namespace App\Controller\Admin;

use App\Model\Permission;
use App\Model\UserType;
use System\Lib\DB;

class UsertypeController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->model=new UserType();
        $this->permission=new Permission();
    }
    function index()
    {
        $data['result']=DB::table('usertype')->all();
        $this->view('usertype',$data);
    }
    function add($data)
    {
        if($_POST)
        {
           // $return=m('usertype/add',$_POST);
            $this->model->add($_POST);
            show_msg(array('添加成功','',$this->base_url('usertype')));
            //$this->redirect('usertype');
        }
        else
        {
            $data['permission']=$this->permission->getlist();
            $data['permission_id']=array();
            $data['permission_id']['menu']=array();
            $data['permission_id']['submenu']=array();
            $data['permission_id']['func']=array();
            $this->view('usertype',$data);
        }
    }
    function edit()
    {
        if($_REQUEST['id']=="2")
        {
            show_msg(array('超级管理员禁止操作','',$this->base_url('usertype')));
            exit;
        }
        if($_POST)
        {
            $this->model->edit($_POST);
            show_msg(array('修改成功','',$this->base_url('usertype')));
            //$this->redirect('usertype');
        }
        else
        {
            //$data['row']=m('usertype/getone',array('id'=>(int)$_GET['id']));
            $data['row']=DB::table('usertype')->where('id=?')->bindValues($_GET['id'])->row();
            $data['permission']=$this->permission->getlist();
            $data['permission_id']=unserialize($data['row']['permission_id']);
            if(!is_array($data['permission_id']['menu']))	$data['permission_id']['menu']=array();
            if(!is_array($data['permission_id']['submenu']))$data['permission_id']['submenu']=array();
            if(!is_array($data['permission_id']['func']))	$data['permission_id']['func']=array();
            $this->view('usertype',$data);
        }
    }
    function delete()
    {
        DB::table('usertype')->where('id=?')->bindValues($_GET['id'])->limit(1)->delete();
        show_msg(array('删除成功','',$this->base_url('usertype')));
        //$this->redirect('usertype');
    }
}