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
        $goods=$goods->findOrFail($request->get('id'));
        if($_POST){
            $data=array(
                'is_direct_buy'=>1,
                'buyer_id'=>$this->user_id,
                'goods_id'=>$goods->id,
                'spec_id'=>$request->post('spec_id'),
                'quantity'=>$request->post('quantity')
            );
            $return=(new Cart())->add($data);
            if($return['code']!='0'){
                redirect()->back()->with('error',$return['msg']);
                return;
            }else{
                redirect('order/confirm/?cart_id[]='.$return['cart_id']);
            }

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

    public function getQuantity(Goods $goods,Request $request)
    {
        $goods=$goods->findOrFail($request->get('id'));
        if($goods->is_have_spec){
            $Spec=(new GoodsSpec())->find($request->get('spec_id'));
            if($Spec->goods_id=$goods->id){
                echo $Spec->stock_count;
            }else{
                echo 0;
            }
        }else{
            echo $goods->stock_count;
        }
    }


}