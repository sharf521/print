<?php
namespace App\Controller;

use System\Lib\Controller as BaseController;

class Controller extends BaseController
{
    protected $user_id;
    protected $username;
    public function __construct()
    {
        parent::__construct();
        $this->user_id = session('user_id');
        $this->username = session('username');
        $this->user_typeid = session('usertype');
        $host = strtolower($_SERVER['HTTP_HOST']);
        if (strpos($host, 'wap.') === false) {
            $this->is_wap = false;
            $this->template = 'default_wap';
        } else {
            $this->is_wap = true;
            $this->template = 'default_wap';
        }
    }
}