<?php
namespace App\Model;

use System\Lib\DB;

class User extends Model
{
    protected $table = 'user';

    public function __construct()
    {
        parent::__construct();
        $this->fields = array('name', 'username', 'password', 'addtime', 'status', 'lastip', 'portrait', 'times', 'zf_password', 'email', 'tel', 'qq', 'address');
    }

    function logout()
    {
        session()->remove('user_id');
        session()->remove('username');
        session()->remove('usertype');
        session()->remove('permission_id');
    }

    function login($data)
    {
        $user=array();
        if ($data['direct'] == '1') {
            if (isset($data['id'])) {
                $user = DB::table('user')->where("id=?")->bindValues($data['id'])->row();
            } elseif (isset($data['openid'])) {
                $user = DB::table('user')->where("openid=?")->bindValues($data['openid'])->row();
            }
        } else {
            $user = DB::table('user')->where("username=?")->bindValues($data['username'])->row();
            $id = (int)$user['id'];
            if ($id == 0) {
                return '用户名或密码错误';
            } elseif ($user['password'] != md5(md5($data['password']) . $user['salt'])) {
                return '用户名或密码错误！';
            }
        }
        if (!empty($user)) {
            if ($data['admin'] == true) {
                $usertype = DB::table('usertype')->select('id,permission_id,is_admin')->where("id={$user['type_id']}")->row();
                if ($usertype['is_admin'] != 1) {
                    return '会员禁止登陆！';
                }
                session()->set('usertype', $usertype['id']);
                session()->set('permission_id', $usertype['permission_id']);
            } else {
                session()->set('usertype', 0);
                session()->set('permission_id', '');
            }
            session()->set('user_id', $user['id']);
            session()->set('username', $user["username"]);
            return true;
        } else {
            return '未知错误!';
        }
    }

    function register($data)
    {
        $check = $this->checkUserName($data['username']);
        if ($check !== true) {
            return $check;
        }
        if (strlen($data['password']) > 15 || strlen($data['password']) < 6) {
            return "密码长度6位到15位！";
        }
        if ($data['password'] != $data['sure_password']) {
            return "两次输入密码不同！";
        }
        $check = $this->checkEmail($data['email']);
        if ($check !== true) {
            return $check;
        }
        $salt = rand(100000, 999999);
        $data = array(
            'type_id' => 1,
            'username' => $data['username'],
            'password' => md5(md5($data['password']) . $salt),
            'zf_password' => md5(md5($data['password']) . $salt),
            'created_at' => time(),
            'status' => 0,
            'email' => $data['email'],
            'salt' => $salt,
            'invite_userid' => 0
        );
        $id = DB::table('user')->insertGetId($data);
        if (is_numeric($id) && $id > 0) {
            session()->set('user_id', $id);
            session()->set('username', $data["username"]);
            return true;
        } else {
            return $id;
        }
    }

    function getlist($data = array())
    {
        $_select = " u.*,ut.name as typename,uu.username invite_name";
        $where = " 1=1";
        if (!empty($data['type_id'])) {
            $where .= " and u.type_id={$data['type_id']}";
        }
        if (!empty($data['username'])) {
            $where .= " and u.username like '{$data['username']}%'";
        }
        return DB::table('user u')->select($_select)
            ->leftJoin('user uu', 'u.invite_userid=uu.id')
            ->leftJoin('usertype ut', 'u.type_id=ut.id')
            ->where($where)
            ->page($data['page'], $data['epage']);
    }

    //修改密码
    public function updatePwd($data)
    {
        $user = $this->findOrFail($data['id']);
        if (strlen($data['password']) > 15 || strlen($data['password']) < 6) {
            return "密码长度6位到15位！";
        } elseif (isset($data['old_password'])) {
            if ($user->password != md5(md5($data['old_password']) . $user->salt)) {
                return '原密码错误！';
            }
        }
        $user->password = md5(md5($data['password']) . $user->salt);
        return $user->save();
    }

    //修改支付密码
    public function updateZfPwd($data)
    {
        $user = $this->findOrFail($data['id']);
        if (strlen($data['zf_password']) > 15 || strlen($data['zf_password']) < 6) {
            return "支付密码长度6位到15位！";
        } elseif (isset($data['old_password'])) {
            if ($user->zf_password != md5(md5($data['old_password']) . $user->salt)) {
                return '原密码错误！';
            }
        }
        $user->zf_password = md5(md5($data['zf_password']) . $user->salt);
        return $user->save();
    }

    //实名认证显示信息
    function userinfoone($data)
    {
        $select = "u.*,i.*,b.account";
        $where = "where u.id=" . $data['id'];
        $sql = "select {$select} from {$this->dbfix}user u 
left join {$this->dbfix}userinfo i on u.id=i.user_id 
left join {$this->dbfix}account_bank b on u.id=b.user_id {$where}";
        return $this->mysql->get_one($sql);
    }

    //用户管理编辑
    function edit($data = array())
    {
        $id = (int)$data['id'];
        unset($data['id']);
        $data = $this->filterFields($data, $this->fields);
        return DB::table('user')->where('id=?')->bindValues($id)->limit(1)->update($data);
    }

    public function checkEmail($email)
    {
        if (empty($email)) {
            return '电子邮件不能为空';
        }
        $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,5}(\\.[a-z]{2})?)$/i";
        if (preg_match($pattern, $email)) {
            $id = DB::table('user')->where("email=?")->bindValues($email)->value('id', 'int');
            if ($id > 0) {
                return '该 电子邮件 已经被注册';
            }
            return true;
        } else {
            return "电子邮件 格式有误！";
        }
    }

    public function checkUserName($username)
    {
        if (strlen($username) < 5 || strlen($username) > 30) {
            return "用户名长度5位到15位！";
        } else {
            $id = DB::table('user')->where("username=?")->bindValues($username)->value('id', 'int');
            if ($id > 0) {
                return '用户名已经存在';
            }
            return true;
        }
    }

    /**
     * @return \App\Model\UserType
     */
    public function UserType()
    {
        return $this->hasOne('App\Model\UserType', 'id', 'type_id');
    }
}