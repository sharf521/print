<?php
namespace App\Controller\Member;

use App\Model\AppUser;
use App\Model\User;
use System\Lib\DB;
use System\Lib\Request;

class IndexController extends MemberController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['title_herder']='帐户中心';
        $this->view('manage', $data);
    }

    public function logout(User $user)
    {
        $user->logout();
        redirect('/login');
        exit;
    }
}