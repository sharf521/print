<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/25
 * Time: 15:38
 */

namespace App\Controller;


use App\Model\Cart;
use App\Model\Order;
use App\Model\UserAddress;
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
        $card_id=$request->cart_id;//array[]
        if(empty($card_id)){
            redirect()->back()->with('error','至少选择一件商品');
        }
        $cart_ids=implode(',',$card_id);

        $address_id=(int)$request->get('address_id');
        if($address_id==0){
            $data['address']=$address->where('user_id=? and is_default=1')->bindValues($this->user_id)->first();
        }else{
            $data['address']=$address->where('user_id=? and id='.$address_id)->bindValues($this->user_id)->first();
        }

        $carts=$cart->where("buyer_id=? and id in({$cart_ids})")->bindValues($this->user_id)->orderBy('seller_id')->get();
        //按店铺分组
        $result_carts=array();
        foreach ($carts as $i=>$cart) {
            $result_carts[$cart->seller_id][]=$cart;
        }
        foreach($result_carts as $seller_id=>$carts){
            foreach ($carts as $i=>$cart) {
                if ($cart->spec_id != 0) {
                    $spec = $cart->GoodsSpec();
                    if ($spec->spec_1 != '') {
                        $result_carts[$seller_id][$i]->spec_1=$spec->spec_1;
                    }
                    if ($spec->spec_2 != '') {
                        $result_carts[$seller_id][$i]->spec_2=$spec->spec_2;
                    }
                    $result_carts[$seller_id][$i]->price = $spec->price;
                    $result_carts[$seller_id][$i]->stock_count = $spec->stock_count;
                } else {
                    $goods = $cart->Goods();
                    $result_carts[$seller_id][$i]->stock_count = $goods->stock_count;
                }
            }
        }
        $data['result_carts']=$result_carts;
        if($_POST){
            try{
                DB::beginTransaction();



                //$order=new Order();
                //$order->Add($user_id,$goods,$spec_id,$quantity);

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