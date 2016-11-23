<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/22
 * Time: 11:31
 */

namespace App\Controller;


use App\Model\Goods;
use App\Model\Order;
use App\Model\OrderGoods;
use System\Lib\Request;

class GoodsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->template='shop_wap';
    }

    public function detail(Goods $goods,Request $request)
    {
        $user_id=$this->user_id;
        $goods=$goods->findOrFail($request->get('id'));
        if($_POST){
            if(empty($user_id)){
                redirect()->back()->with('error','您还没有登陆！');
                return;
            }
            $quantity=(int)$request->post('quantity');
            if($quantity==0){
                redirect()->back()->with('error','购买数量不能为空！');
                return;
            }
            $order=new Order();
            $order_sn=time().rand(10000,99999);
            $order->order_sn=$order_sn;
            $order->buyer_id=$user_id;
            $order->buyer_name=$this->username;
            $order->seller_id=$goods->user_id;
            $order->goods_money=math($goods->price,$quantity,'*',2);
            $order->order_money=$order->goods_money;
            $order->status=1;
            $order->save();
            $orderGoods=new OrderGoods();
            $orderGoods->order_sn=$order_sn;
            $orderGoods->goods_id=$goods->id;
            $orderGoods->goods_name=$goods->name;
            $orderGoods->price=$goods->price;
            $orderGoods->quantity=$quantity;
            $orderGoods->goods_image=$goods->image_url;
            $orderGoods->save();
            redirect('/member/order')->with('msg','己ok！');
        }else{
            $data['goods']=$goods;
            $data['images']=$goods->GoodsImage();
            $data['GoodsData']=$goods->GoodsData();
            $this->view('goods_detail',$data);
        }

    }
}