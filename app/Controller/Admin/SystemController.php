<?php
namespace App\Controller\Admin;

use App\Model\System;
use System\Lib\DB;

class SystemController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->model=new System();
    }
    function index()
    {
        if(isset($_POST['showorder']))
        {
            $id = $_POST['id'];
            $value = $_POST['value'];
            $showorder = $_POST['showorder'];
            foreach ($id as $key => $val)
            {
               // $sql = "update {$this->dbfix}system set `value`='".$value[$key]."',`showorder`='".intval($showorder[$key])."' where id=$val limit 1";
                //$this->mysql->query($sql);
                $arr=array(
                    'value'=>$value[$key],
                    'showorder'=>intval($showorder[$key])
                );
                DB::table('system')->where("id={$val}")->limit(1)->update($arr);
            }
            show_msg(array('操作成功','',$this->base_url('system')));
        }
        else
        {
            $data['result']=DB::table('system')->orderBy("`showorder`,id")->all();
            $this->view('system',$data);
        }
    }
    function add($data)
    {
        if($_POST)
        {
            $this->model->add($_POST);
            show_msg(array('添加成功','',$this->base_url('system')));
            //$this->redirect('system');
        }
        else
        {
            $this->view('system',$data);
        }
    }
    function edit()
    {
        if($_POST)
        {
            $this->model->edit($_POST);
            show_msg(array('修改成功','',$this->base_url('system')));
            //$this->redirect('system');
        }
        else
        {
            $data['row']=DB::table('system')->where('id=?')->bindValues($_GET['id'])->row();
            $this->view('system',$data);
        }
    }
    function delete()
    {
        DB::table('system')->where('id=?')->bindValues($_GET['id'])->limit(1)->delete();
        show_msg(array('删除成功','',$this->base_url('system')));
        //$this->redirect('system');
    }
}