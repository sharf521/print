<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/26
 * Time: 11:43
 */

namespace App\Controller\SellManage;


class IndexController extends SellController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data['title_herder']='卖家中心';
        $this->view('manage', $data);
    }
}