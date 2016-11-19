<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/18
 * Time: 15:31
 */

namespace App\Controller\Member;

use App\Model\Goods;
use App\Model\GoodsData;
use App\Model\GoodsImage;
use App\Model\GoodsSpec;
use System\Lib\DB;
use System\Lib\Request;

class GoodsController extends MemberController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Goods $goods)
    {
        $data['result']=$goods->where("user_id=?")->bindValues($this->user_id)->orderBy('id desc')->pager();
        $this->view('goods_list',$data);
    }

    public function add(Goods $goods,GoodsData $goodsData,GoodsImage $goodsImage,Request $request)
    {
        if($_POST){
            print_r($_POST);
            $imgids=trim($request->post('imgids'),',');
            $name=$request->post('name');
            $price=$request->post('price');
            $stock_count=$request->post('stock_count');
            $spec_name=$request->post('spec_name');
            $shipping_fee=(float)$request->post('shipping_fee');
            $content=$request->post('content');
            if(empty($imgids)){
                redirect()->back()->with('error','图片不能为空！');
            }
            if(empty($name)){
                redirect()->back()->with('error','名称不能为空！');
            }

            try{
                DB::beginTransaction();
                $goods->user_id=$this->user_id;
                $goods->supply_goods_id=0;
                $goods->category_id=0;
                $goods->category_path='';
                $goods->shop_cateid=0;
                $goods->shop_catepath='';
                $goods->image_url='';
                $goods->name=$name;
                $goods->price=(float)$price;
                $goods->stock_count=(int)$stock_count;
                $goods->shipping_fee=(float)$shipping_fee;
                $goods->sale_count=0;
                $goods->status=0;
                $goods_id=$goods->save(true);

                $goodsData->goods_id=$goods_id;
                $goodsData->content=$content;
                $goodsData->save();

                $goodsImage->where("user_id=? and id in({$imgids})")->bindValues($this->user_id)->update(array('goods_id'=>$goods_id));

                $goods=$goods->find($goods_id);
                $goods->image_url=$goodsImage->where("goods_id=?")->bindValues($goods_id)->first()->image_url;
                if(is_array($spec_name)){
                    $stock_total=0;
                    foreach($spec_name as $i=>$v){
                        $spec=new GoodsSpec();
                        $spec->goods_id=$goods_id;
                        $spec->name=$spec_name[$i];
                        $spec->price=(float)$price[$i];
                        $spec->stock_count=(int)$stock_count[$i];
                        if($spec->name!='' && $spec->stock_count!=0){
                            $spec->save();
                            if($stock_total==0){
                                $goods->price=$spec->price;
                            }
                            $stock_total+=$spec->stock_count;
                        }
                    }
                    $goods->stock_count=$stock_total;
                }
                $goods->save();

                DB::commit();
                redirect('goods')->with('msg', '添加成功！');
            }catch(\Exception $e){
                DB::rollBack();
                $error = "Failed: " . $e->getMessage();
                redirect()->back()->with('error', $error);
            }
        }else{
            $this->view('goods_form',$data);
        }

    }
}