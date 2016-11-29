<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/29
 * Time: 10:36
 */

namespace App\Controller;


use App\Model\Cart;
use System\Lib\Request;

class CartController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->template='shop_wap';
    }
    
    public function index(Cart $cart)
    {
        if($_GET){
            
        }else{
            $data['result_carts']=$cart->getList(array('buyer_id'=>$this->user_id));
            $this->view('cart',$data);
        }
    }

    public function add(Cart $cart,Request $request)
    {
        $data=array(
            'buyer_id'=>$this->user_id,
            'goods_id'=>$request->post('goods_id'),
            'spec_id'=>$request->post('spec_id'),
            'quantity'=>$request->post('quantity')
        );
        $return=$cart->add($data);
        echo json_encode($return);
    }

    public function getSelectedMoney(Request $request,Cart $cart)
    {
        $ids=trim($request->get('cart_ids'));
        $cart_id=explode(',',$ids);
        if(count($cart_id)>0 && $ids!=''){
            $arr=array(
                'buyer_id'=>$this->user_id,
                'cart_id'=>$cart_id
            );
            $carts_result=$cart->getList($arr);
            $result=array();
            $result['total']=0;
            $result['nums']=0;
            foreach ($carts_result as $seller_id=>$carts){
                $result[$seller_id]=0;
                foreach ($carts as $cart){
                    $_t=math($cart->price,$cart->stock_count,'*',2);
                    $result[$seller_id]=math($result[$seller_id],$_t,'+',2);
                    $result['nums']++;
                }
                $result['total']=math($result['total'],$result[$seller_id],'+',2);
            }
            echo json_encode($result);
        }
    }
}