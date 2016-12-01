<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/24
 * Time: 18:01
 */

namespace App\Model;

class Cart extends Model
{
    protected $table='cart';
    public function __construct()
    {
        parent::__construct();
    }
    
    public function add($data=array())
    {
        $buyer_id=(int)$data['buyer_id'];
        $goods_id=(int)$data['goods_id'];
        $spec_id=(int)$data['spec_id'];
        $quantity=(int)$data['quantity'];
        $goods=(new Goods())->findOrFail($goods_id);
        if($goods->is_have_spec==1 && $spec_id==0){
            return $this->returnError('请选择规格！');
        }
        $goods=$goods->addSpec($spec_id);
        $stock_count=$goods->stock_count;
        if($stock_count<$quantity){
            return $this->returnError("库存不足，仅剩{$stock_count}件！");
        }
        $session_id='';
        if($buyer_id==0){
            $session_id=session_id();
            $cart=$this->where("buyer_id=0 and session_id='$session_id' and goods_id={$goods_id} and spec_id={$spec_id}")->first();
        }else{
            $cart=$this->where("buyer_id={$buyer_id} and goods_id={$goods_id} and spec_id={$spec_id}")->first();
        }
        if($cart->is_exist){
            $cart->quantity=$cart->quantity + $quantity;
            $cart->save();
            return $this->returnSuccess(array('cart_id'=>$cart->id));
        }else{
            $cart->buyer_id=$buyer_id;
            $cart->seller_id=$goods->user_id;
            $cart->goods_id=$goods->id;
            $cart->goods_name=$goods->name;
            $cart->goods_image=$goods->image_url;
            $cart->spec_id=$spec_id;
            $cart->quantity=$quantity;
            $cart->session_id=$session_id;
            $cart_id=$this->save(true);
            return $this->returnSuccess(array('cart_id'=>$cart_id));
        }
    }

    public function getList($data=array())
    {
        $buyer_id=(int)$data['buyer_id'];
        $cart_id=$data['cart_id'];//多个id数组

        if($buyer_id==0){
            $session_id=session_id();
            $where=" buyer_id=0 and session_id='$session_id' ";
        }else{
            $where=" buyer_id={$buyer_id} ";
        }
        if(is_array($cart_id)){
            $cart_ids=implode(',',$cart_id);
            $where.=" and id in({$cart_ids})";
        }
        $carts=$this->where($where)->orderBy('seller_id')->get();
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
                    $result_carts[$seller_id][$i]->price = $goods->price;
                    $result_carts[$seller_id][$i]->stock_count = $goods->stock_count;
                }
            }
        }
        return $result_carts;
    }

    public function Goods()
    {
        return $this->hasOne('\App\Model\Goods','id','goods_id');
    }

    public function GoodsSpec()
    {
        return $this->hasOne('\App\Model\GoodsSpec','id','spec_id');
    }
}