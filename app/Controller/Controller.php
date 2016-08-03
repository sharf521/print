<?php
namespace App\Controller;

use System\Lib\Controller as BaseController;

class Controller extends BaseController
{
    public function __construct()
    {
        parent::__construct();
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