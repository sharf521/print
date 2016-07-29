<?php
namespace App\Controller\Admin;

use App\Model\Permission;
use System\Lib\DB;

class PermissionController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new Permission();
    }

    function index()
    {
        if (isset($_POST['order'])) {
            $id = $_POST['id'];
            $order = $_POST['order'];
            foreach ($id as $key => $val) {
                DB::table('permissions')->where('id=?')->bindValues($val)->limit(1)->update(array('order' => intval($order[$key])));
            }
            show_msg(array('操作成功', '', $this->base_url('permission')));
        } else {
            $data['result'] = $this->model->getlist();
            $this->view('permission', $data);
        }
    }

    function add($data)
    {
        if ($_POST) {
            $this->model->add($_POST);
            show_msg(array('添加成功', '', $this->base_url('permission')));
            //$this->redirect('permission');
        } else {
            $this->view('permission');
        }
    }

    function edit()
    {
        if ($_POST) {
            $this->model->edit($_POST);
            show_msg(array('修改成功', '', $this->base_url('permission')));
            //$this->redirect('permission');
        } else {
            $data['row'] = DB::table('permissions')->where('id=?')->bindValues($_GET['id'])->row();
            $this->view('permission', $data);
        }
    }

    function delete()
    {
        DB::table('permissions')->where('id=?')->bindValues($_GET['id'])->limit(1)->delete();
        show_msg(array('删除成功', '', $this->base_url('permission')));
        //$this->redirect('permission');
    }
}