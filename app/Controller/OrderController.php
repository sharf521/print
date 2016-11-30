<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/25
 * Time: 15:38
 */

namespace App\Controller;


use App\Model\Cart;
use App\Model\Goods;
use App\Model\Order;
use App\Model\OrderGoods;
use App\Model\UserAddress;
use System\Lib\DB;
use System\Lib\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->template='shop_wap';
    }

    //确认订单
    public function confirm(Request $request,Cart $cart,UserAddress $address)
    {
        $user_id=$this->user_id;
        $cart_id=$request->cart_id;//array[]
        if(empty($cart_id)){
            redirect()->back()->with('error','至少选择一件商品');
        }
        $address_id=(int)$request->get('address_id');
        if($address_id==0){
            $data['address']=$address->where('user_id=? and is_default=1')->bindValues($this->user_id)->first();
        }else{
            $data['address']=$address->where('user_id=? and id='.$address_id)->bindValues($this->user_id)->first();
        }
        $arr=array(
            'buyer_id'=>$this->user_id,
            'cart_id'=>$cart_id
        );
        $carts_result=$cart->getList($arr);
        $data['result_carts']=$carts_result;
        if($_POST){
            try{
                DB::beginTransaction();

                $goods=new Goods();
                foreach ($carts_result as $seller_id=>$carts){
                    $order=new Order();
                    $order_sn=time().rand(10000,99999);
                    $order->order_sn=$order_sn;
                    $order->buyer_id=$user_id;
                    $order->buyer_name=$this->username;
                    $order->seller_id=$seller_id;
                    $order_money=0;
                    foreach ($carts as $cart){
                        $goods=$goods->findOrFail($cart->goods_id);
                        $goods=$goods->addSpec($cart->spec_id);
                        if($goods->stock_count==0){
                            throw  new \Exception("己卖完了！");
                        }
                        if($cart->quantity > $goods->stock_count){
                            throw  new \Exception("库存不足，仅剩{$goods->stock_count}件！");
                        }
                        $orderGoods=new OrderGoods();
                        $orderGoods->order_sn=$order_sn;
                        $orderGoods->goods_id=$goods->id;
                        $orderGoods->goods_name=$goods->name;
                        $orderGoods->goods_image=$goods->image_url;
                        $orderGoods->spec_id=$cart->spec_id;
                        $orderGoods->quantity=$cart->quantity;
                        $orderGoods->price=$goods->price;
                        $orderGoods->spec_1=$goods->spec_1;
                        $orderGoods->spec_2=$goods->spec_2;
                        $orderGoods->save();
                        $order_money=math($order_money,math($goods->price,$cart->quantity,'*',2),'+',2);
                    }
                    $order->goods_money=$order_money;
                    $order->order_money=$order->goods_money;
                    $order->status=1;
                    $order->save();
                }

                DB::commit();
            }catch(\Exception $e){
                $error=$e->getMessage();
                redirect()->back()->with('error',$error);
                DB::rollBack();
            }
            redirect('/member/order')->with('msg','己ok！');
        }else{
            $this->view('order',$data);
        }
    }
}