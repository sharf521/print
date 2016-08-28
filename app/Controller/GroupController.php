<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/25
 * Time: 17:20
 */

namespace App\Controller;

use app\Model\PrintShopGroup;
use App\Model\User;
use App\WeChat;
use System\Lib\DB;
use System\Lib\Request;


class GroupController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function detail(Request $request,PrintShopGroup $shopGroup,WeChat $weChat,User $user)
    {
        $id=$request->get('id');
        $user_id=(int)$request->get('user_id');
        $shopGroup=$shopGroup->findOrFail($id);
        $group=$shopGroup->Group();
        $group->picture='http://'.$_SERVER['HTTP_HOST'].$group->picture;

        $shopList=DB::table('print_shop_group sg')->select("s.*")
            ->leftJoin('print_shop s','sg.shop_id=s.id')
            ->leftJoin('user u','s.user_id=u.id')
            ->where("group_id=?")->bindValues($id)
            ->groupBy("u.invite_count desc")
            ->all(\PDO::FETCH_OBJ);

        $data['user']=$user->find($user_id);
        $data['group']=$group;
        $data['shopList']=$shopList;

        $data['qrcodeSrc']=$weChat->qrcode($user_id.'01');

        $weChat=new WeChat();
        $js = $weChat->app->js;
        $data['config']=$js->config(array('checkJsApi','onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo','onMenuShareQZone'), false);
        $this->view('group', $data);
    }
}