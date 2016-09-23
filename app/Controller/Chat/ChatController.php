<?php

namespace App\Controller\Chat;
use System\Lib\Controller as BaseController;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/23
 * Time: 17:03
 */
class ChatController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->template='chat';
    }
}