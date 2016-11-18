<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/18
 * Time: 15:31
 */

namespace App\Controller\Member;

use App\Model\Goods;
use System\Lib\Request;

class GoodsController extends MemberController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Goods $goods)
    {
        $data['result']=$goods->where("user_id=?")->bindValues($this->user_id)->orderBy('id desc')->pager();
        $this->view('goods',$data);
    }

    public function add(Goods $goods,Request $request)
    {

        $this->view('goods',$data);
    }
}