<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/25
 * Time: 15:38
 */

namespace App\Controller;


use App\Model\Cart;
use System\Lib\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->template='shop_wap';
    }

    //确认订单
    public function confirm(Request $request,Cart $cart)
    {
        $card_id=$request->cart_id;//array[]
        if(empty($card_id)){
            redirect()->back()->with('error','至少选择一件商品');
        }
        $cart_ids=implode(',',$card_id);
        $carts=$cart->where("buyer_id=? and id in({$cart_ids})")->bindValues($this->user_id)->orderBy('seller_id')->get();
        $data['carts']=$carts;
        $this->view('order',$data);
    }
}