<?php
namespace App\Controller\Admin;

use App\Model\Permission;
use App\Model\User;

class IndexController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $permission_id = $this->permission_id;
        //获得所有菜单
        $permission = new Permission();
        $data['menu'] = $permission->getlist();
        if ($permission_id != 'ALL') {
            //如果不是超级管理，反序列化成数组
            $permission_id = unserialize($permission_id);
            if (empty($permission_id['menu'])) {
                $permission_id['menu'] = array();
            }
            if (empty($permission_id['submenu'])) {
                $permission_id['submenu'] = array();
            }
            foreach ($data['menu'] as $key => $menu) {
                //如果角色权限里没有该一级菜单，则移除该一级菜单。
                if (!in_array($menu['id'], $permission_id['menu'])) {
                    unset($data['menu'][$key]);
                }
                if (isset($menu['son']) && is_array($menu['son'])) {
                    foreach ($menu['son'] as $i => $submenu) {
                        //如果角色权限里没有该二级菜单，则移除该二级菜单。
                        if (!in_array($submenu['id'], $permission_id['submenu'])) {
                            unset($data['menu'][$key]['son'][$i]);
                        }
                    }
                }
            }
        }
        $this->view('manage', $data);
        exit;
    }

    function logout(User $user)
    {
        $user->logout();
        $this->redirect('login');
        exit;
    }

    public function login(User $user)
    {
        if ($_POST) {
            if ($_POST['valicode'] != $_SESSION['randcode']) {
                $error = '验证码不正确！';
            } else {
                $data = array(
                    'admin' => true,
                    'username' => trim($_POST['username']),
                    'password' => $_POST['password']
                );
                $result = $user->login($data);
                if ($result === true) {
                    redirect('index');
                } else {
                    $error = $result;
                }
            }
            redirect()->back()->with('error',$error);
        } else {
            $this->view('login');
        }
    }

    //修改密码
    function changepwd(User $user)
    {
        if ($_POST) {
            $id= $this->user_id;
            if ($_POST['password'] != $_POST['sure_password']) {
                $error = "两次输入密码不同！";
            } else {
                $post = array(
                    'id' => $id,
                    'old_password' => $_POST['old_password'],
                    'password' => $_POST['password'],
                );
                $result=$user->updatePwd($post);
                if ($result === true) {
                    redirect()->back()->with('msg','修改成功!');
                } else {
                    $error = $result;
                }
            }
            redirect()->back()->with('error',$error);
        } else {
            $this->view('pwd');
        }
    }
}