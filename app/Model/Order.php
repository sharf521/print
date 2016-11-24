<?php

namespace App\Model;


class Order extends Model
{
    protected $table='order';
    public function __construct()
    {
        parent::__construct();
    }

    public function Add($user_id,Goods $goods,$spec_id=0,$quantity=1)
    {
        if(empty($user_id)){
            throw new \Exception('您还没有登陆！');
        }
        if($quantity==0){
            throw  new \Exception('购买数量不能为空！');
        }
        if($goods->is_have_spec==1 && $spec_id==0){
            throw  new \Exception('请选择规格！');
        }
        $stock_count=$goods->stock_count;
        $goods_price=$goods->price;
        if($spec_id!=0){
            $Spec=(new GoodsSpec())->findOrFail($spec_id);
            $goods_price=$Spec->price;
            $stock_count=$Spec->stock_count;
        }

        if($stock_count<$quantity){
            throw  new \Exception("库存不足，仅剩{$stock_count}件！");
        }

        $order=new Order();
        $order_sn=time().rand(10000,99999);
        $order->order_sn=$order_sn;
        $order->buyer_id=$user_id;
        $order->buyer_name=$this->username;
        $order->seller_id=$goods->user_id;
        $order->goods_money=math($goods_price,$quantity,'*',2);
        $order->order_money=$order->goods_money;
        $order->status=1;
        $order->save();
        $orderGoods=new OrderGoods();
        $orderGoods->order_sn=$order_sn;
        $orderGoods->goods_id=$goods->id;
        $orderGoods->goods_name=$goods->name;
        $orderGoods->quantity=$quantity;
        $orderGoods->goods_image=$goods->image_url;
        $orderGoods->price=$goods_price;
        $orderGoods->spec_id=$spec_id;
        $orderGoods->spec_1='';
        $orderGoods->spec_2='';
        if($spec_id!=0){
            $orderGoods->spec_1=$Spec->spec_1;
            $orderGoods->spec_2=$Spec->spec_2;
        }
        $orderGoods->save();
    }

    public function OrderGoods()
    {
        return $this->hasMany('\App\Model\OrderGoods','order_sn','order_sn');
    }
}