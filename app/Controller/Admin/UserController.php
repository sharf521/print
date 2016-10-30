<?php
namespace App\Controller\Admin;

use App\Model\User;
use App\Model\UserType;
use App\WeChat;
use System\Lib\DB;
use System\Lib\Request;

class UserController extends AdminController
{
    protected $User;

    public function __construct()
    {
        parent::__construct();
        $this->User = new User();
    }

    function index(User $user,UserType $userType,Request $request)
    {
        $where = " 1=1";
        if (!empty($_GET['type_id'])) {
            $where .= " and type_id={$_GET['type_id']}";
        }
        if (!empty($_GET['username'])) {
            $where .= " and username like '{$_GET['username']}%'";
        }
        if (!empty($_GET['invite_userid'])) {
            $where .= " and invite_userid='{$_GET['invite_userid']}'";
        }

        $starttime=$request->get('starttime');
        $endtime=$request->get('endtime');
        if(!empty($starttime)){
            $where.=" and created_at>=".strtotime($starttime);
        }
        if(!empty($endtime)){
            $where.=" and created_at<".strtotime($endtime);
        }

        $data =$user->where($where)->orderBy('id desc')->pager($_GET['page'],10);
        $data['usertype'] = $userType->getList();



        $this->view('user', $data);
    }

    //同步微信用户
    public function syncUser()
    {
        $weChat=new WeChat();
        $userService=$weChat->app->user;
        $users = $userService->lists();
        $openids=$users['data']['openid'];
        $user=new User();
        foreach ($openids as $openid){
            $user=$user->where("openid=?")->bindValues($openid)->first();
            if(! $user->is_exist){
                echo '新添加：'.$openid.'<br>';
                $user->addWeChatUser($openid);
            }
        }
        echo '完成';
    }

    function add()
    {
        if ($_POST) {
            $returnmsg = $this->User->register($_POST);
            if ($returnmsg === true) {
                show_msg(array("添加成功！", '', $this->base_url('user')));
            } else {
                show_msg(array($returnmsg));
            }
            //$this->redirect('usertype');
        } else {
            $this->view('user');
        }
    }

    //编辑用户资料
    function edit(User $user)
    {
        if ($_REQUEST['id'] == "1") {
            show_msg(array('超级管理员禁止操作', '', $this->base_url('user')));
            exit;
        }
        $user=$user->findOrFail($_GET['id']);
        if ($_POST) {
            $user->name=$_POST['name'];
            $user->nickname=$_POST['nickname'];
            $user->tel=$_POST['tel'];
            $user->qq=$_POST['qq'];
            $user->address=$_POST['address'];
            $user->save();

            if($user->openid!=''){
                $weChat=new WeChat();
                $userService=$weChat->app->user;
                $userService->remark($user->openid, $user->name);
            }

            show_msg(array('修改成功', '', $this->base_url('user')));
            //$this->redirect('usertype');
        } else {
            $data['user'] =$user;
            $this->view('user', $data);
        }
    }

    //修改用户类型
    function edittype()
    {
        if ($_POST) {
//            if(!empty($_POST['invite_userid']))
//            {
//                $invite_userid = DB::table('user')->where('id=?')->bindValues($_POST['invite_userid'])->value('id', 'int');
//                if ($invite_userid == 0) {
//                    show_msg(array('邀请人ID不正确'));exit;
//                }
//            }
            $arr = array(
                'type_id' => (int)$_POST['type_id']
            );
            DB::table('user')->where('id=?')->bindValues($_GET['id'])->limit(1)->update($arr);
            show_msg(array('修改成功', '', $this->base_url('user')));
            //$this->redirect('usertype');
        } else {
            $UserType = new UserType();
            $data['usertype'] = $UserType->getList();
            $data['row'] = DB::table('user')->where('id=?')->bindValues($_GET['id'])->row();
            $this->view('user', $data);
        }
    }

    function updatepwd(User $user,Request $request)
    {
        if ($_POST) {
            if ($_POST['password'] != $_POST['sure_password']) {
                $error='两次输入密码不同';
            }else{
                $post = array(
                    'id' => $request->post('id'),
                    'password' => $request->post('password'),
                );
                $return=$user->updatePwd($post);
                if($return===true){
                    redirect('user')->with('msg','修改成功！');
                }else{
                    $error=$return;
                }
            }
            redirect()->back()->with('error',$error);
        } else {
            $data['row'] = DB::table('user')->where('id=?')->bindValues($_GET['id'])->row();
            $this->view('user', $data);
        }
    }
}