<?php
namespace App\Controller\Admin;

use App\Model\ZJ;

class ZjController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new ZJ();
    }

    function index()
    {
        $arr=array(
            'user_id'		=>(int)$_GET['user_id'],
            'id'		=>(int)$_GET['id'],
            'money'		=>(int)$_GET['money'],
            'plate'		=>(int)$_GET['plate'],
            'page'			=>(int)$_REQUEST['page'],
            'epage'			=>10
        );
        $data['result']=$this->model->getZjByPage($arr);
        $this->view('zj',$data);
    }
    function add($data)
    {
        if($_POST)
        {
            $post=array(
                'user_id'=>$_POST['user_id']
            );
            $return=$this->model->add($post);
            $return=json_decode($return,true);
            if($return['code']==200){
                show_msg(array('添加成功','',$this->base_url('zj')));
            }
            else{
                show_msg(array($return['msg']));
            }
        }
        else
        {
            $this->view('zj',$data);
        }
    }
//    function calAdd1000()
//    {
//        $return=$this->model->calAdd1000();
//        if($return===true){
//            show_msg(array('完成','',$this->base_url('zj')));
//        }else{
//            show_msg(array('失败！！'));
//        }
//    }
    function calZj(){
        $return=$this->model->calZj();
        if($return===true){
            show_msg(array('完成','',$this->base_url('zj')));
        }else{
            show_msg(array('失败！！'));
        }
    }
    function  zjlog(){
        $arr=array(
            'typeid'		=>$_GET['typeid'],
            'user_id'		=>(int)$_GET['user_id'],
            'zj_id'		=>(int)$_GET['zj_id'],
            'in_zj_id'		=>(int)$_GET['in_zj_id'],
            'money'		=>(int)$_GET['money'],
            'plate'		=>(int)$_GET['plate'],
            'page'			=>(int)$_REQUEST['page'],
            'epage'			=>10
        );
        $data['result']=$this->model->getZjLogByPage($arr);
        $this->view('zj',$data);
    }
}