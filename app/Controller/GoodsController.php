<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/22
 * Time: 11:31
 */

namespace App\Controller;


use App\Model\Cart;
use App\Model\Goods;
use App\Model\GoodsSpec;
use App\Model\Order;
use App\Model\OrderGoods;
use System\Lib\DB;
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
            $spec_id=(int)$request->post('spec_id');
            $quantity=(int)$request->post('quantity');
            $stock_count=$goods->stock_count;
            if($spec_id!=0){
                $Spec=(new GoodsSpec())->findOrFail($spec_id);
                $stock_count=$Spec->stock_count;
            }
            if($goods->is_have_spec==1 && $spec_id==0){
                redirect()->back()->with('error',"请选择规格！");
            }
            if($stock_count<$quantity){
                redirect()->back()->with('error',"库存不足，仅剩{$stock_count}件！");
            }
            $cart=new Cart();
            $cart->buyer_id=$user_id;
            $cart->seller_id=$goods->user_id;
            $cart->goods_id=$goods->id;
            $cart->goods_name=$goods->name;
            $cart->goods_image=$goods->image_url;
            $cart->spec_id=$spec_id;
            $cart->quantity=$quantity;
            $cart->session_id='';
            if(empty($user_id)){
                $cart->session_id=session_id();
            }
            $cart_id=$cart->save(true);
            redirect('/member/order/confirm/?cart_id[]='.$cart_id);
/*            try{
                DB::beginTransaction();

                $order=new Order();
                $order->Add($user_id,$goods,$spec_id,$quantity);

                DB::commit();
            }catch(\Exception $e){
                $error=$e->getMessage();
                redirect()->back()->with('error',$error);
                DB::rollBack();
            }
            redirect('/member/order')->with('msg','己ok！');*/
        }else{
            $data['goods']=$goods;
            $data['images']=$goods->GoodsImage();
            $data['GoodsData']=$goods->GoodsData();
            $this->view('goods_detail',$data);
        }

    }
}