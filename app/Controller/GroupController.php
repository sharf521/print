<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/25
 * Time: 17:20
 */

namespace App\Controller;

use app\Model\PrintShopGroup;
use App\WeChat;
use System\Lib\Request;


class GroupController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }


    public function detail(Request $request,PrintShopGroup $shopGroup,WeChat $weChat)
    {
        $id=$request->get('id');
        $user_id=$request->get('user_id');
        $shopGroup=$shopGroup->findOrFail($id);
        $group=$shopGroup->Group();
        $shopList=$shopGroup->where("group_id=?")->bindValues($id)->get();
        $data['user_id']=$user_id;
        $data['group']=$group;
        $data['shopList']=$shopList;

        $data['qrcodeSrc']=$weChat->qrcode($user_id.'01');

        $this->view('group', $data);
    }
}