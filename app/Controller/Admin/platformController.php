<?php
namespace App\Controller\Admin;

use App\Model\Platform;
use System\Lib\DB;

class PlatformController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->Platform=new Platform();
    }

    //列表
    function index()
    {
        if ($_POST) {
            $id = $_POST['id'];
            $showorder = $_POST['showorder'];
            foreach ($id as $key => $val) {
                //$sql = "update {$this->dbfix}platform_type set `name`='" . $name[$key] . "',`code`='" . $code[$key] . "',`showorder`='" . intval($showorder[$key]) . "' where id=$val limit 1";
                //DB::query($sql);
                $arr=array(
                    'showorder'=>intval($showorder[$key])
                );
                DB::table('platform')->where("id=$val")->limit(1)->update($arr);
            }
            show_msg(array('操作成功', '', $this->base_url('platform')));
        } else {
            $data['result'] =DB::table('platform')->orderBy('showorder,id')->page($_GET['page'],10);
            $this->view('platform', $data);
        }
    }

    //类型编辑
    function edit()
    {
        $id = (int)$_REQUEST['id'];
        if ($_POST) {
            $arr=array(
                'name'=>$_POST['name'],
                'content'=>$_POST['content'],
                'login_url'=>$_POST['login_url'],
                'randcode_url'=>$_POST['randcode_url'],
                'login_minutes'=>$_POST['login_minutes'],
                'showorder'=> $_POST['showorder']
            );
            DB::table('platform')->where('id=?')->bindValues($id)->limit(1)->update($arr);
            show_msg(array('操作成功', '', $this->base_url('platform')));
        } else {
            $data = DB::table('platform')->where('id=?')->bindValues($id)->row();
            $this->view('platform', $data);
        }
    }

    //类型添加
    function add()
    {
        if ($_POST) {
            $arr=array(
                'name'=>$_POST['name'],
                'content'=>$_POST['content'],
                'login_url'=>$_POST['login_url'],
                'randcode_url'=>$_POST['randcode_url'],
                'login_minutes'=>$_POST['login_minutes'],
                'showorder'=> ($_POST['showorder']) ? $_POST['showorder'] : 10,
                'addtime'=>date('Y-m-d H:i:s')
            );
            DB::table('platform')->insert($arr);
            show_msg(array('操作成功', '', $this->base_url('platform')));
        } else {
            $this->view('platform');
        }
    }

    //类型删除
    function drop()
    {
        DB::table('platform')->where('id=?')->bindValues($_GET['id'])->limit(1)->delete();
        show_msg(array('删除成功', '', $this->base_url('platform')));
    }

    //子菜单列表
    function linklist()
    {
        if ($_POST) {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $value = $_POST['value'];
            $showorder = $_POST['showorder'];
            foreach ($id as $key => $val) {
                $arr = array(
                    'name' => $name[$key],
                    'value' => $value[$key],
                    'showorder' => intval($showorder[$key])
                );
                DB::table('platform')->where('id=?')->bindValues($val)->limit(1)->update($arr);
            }
            show_msg(array('操作成功', '', $this->base_url('platform/linklist/?id=' . $_GET['id'])));
        } else {
            $id = $_GET['id'];
            $arr = array(
                'typeid' => (int)$_GET['id'],
                'page' => (int)$_REQUEST['page'],
                'epage' => 30,
            );
            $result = $this->model->linklist($arr);
            $result['typename'] = DB::table('platform_type')->where('id=?')->bindValues($id)->value('name');
            $this->view('platform', $result);
        }
    }

    //子菜单添加
    function link_add()
    {
        $id = $_POST['typeid'];
        if ($_POST['name']) {
            $data = array(
                'typeid' => $_POST['typeid'],
                'name' => $_POST['name'],
                'value' => $_POST['value'],
                'showorder' => $_POST['showorder']
            );
            $this->model->link_add($data);
            show_msg(array('操作成功', '', $this->base_url('platform/linklist/?id=' . $id)));
        } else {
            $this->view('platform');
        }
    }

    //子菜单删除
    function link_drop()
    {
        $id = $_GET['typeid'];
        DB::table('platform')->where('id=?')->bindValues($_GET['id'])->limit(1)->delete();
        show_msg(array('删除成功', '', $this->base_url('platform/linklist/?id=' . $id)));
    }

    //子菜单批量添加
    function link_action()
    {
        $id = $_POST['typeid'];
        if (isset($_POST['name'])) {
            $data['type'] = "ling_add";
            $data['typeid'] = $_POST['typeid'];
            $data['name'] = $_POST['name'];
            $data['value'] = $_POST['value'];
            $data['showorder'] = $_POST['showorder'];
            $result = $this->model->Action($data);
            if ($result !== true) {
                $msg = array($result);
            } else {
                show_msg(array('操作成功', '', $this->base_url('platform/linklist/?id=' . $id)));
            }
        } else {
            show_msg(array('操作错误', '', $this->base_url('platform/linklist/?id=' . $id)));
        }
    }
}