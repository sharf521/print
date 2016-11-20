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
use App\Model\ShopCategory;
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
        $data['result']=$goods->where("user_id=? and status=1 and stock_count>0")->bindValues($this->user_id)->orderBy('id desc')->pager();
        $this->view('goods_list',$data);
    }

    public function list_stock0(Goods $goods)
    {
        $data['result']=$goods->where("user_id=? and status=1 and stock_count=0")->bindValues($this->user_id)->orderBy('id desc')->pager();
        $this->view('goods_list',$data);
    }

    public function list_status2(Goods $goods)
    {
        $data['result']=$goods->where("user_id=? and status=2")->bindValues($this->user_id)->orderBy('id desc')->pager();
        $this->view('goods_list',$data);
    }

    public function add(Goods $goods,GoodsData $goodsData,GoodsImage $goodsImage,Request $request)
    {
        if($_POST){
            $imgids=trim($request->post('imgids'),',');
            $name=$request->post('name');
            $price=$request->post('price');
            $stock_count=$request->post('stock_count');
            $spec_name=$request->post('spec_name');
            $shipping_fee=(float)$request->post('shipping_fee');
            $content=$request->post('content');
            $shop_cateid=(int)$request->post('shop_category');
            if($shop_cateid!=0){
                $shop_catepath=(new ShopCategory())->find($shop_cateid)->path;
            }
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
                $goods->shop_cateid=$shop_cateid;
                $goods->shop_catepath=$shop_catepath;
                $goods->image_url='';
                $goods->name=$name;
                $goods->price=(float)$price;
                $goods->stock_count=(int)$stock_count;
                $goods->shipping_fee=(float)$shipping_fee;
                $goods->sale_count=0;
                $goods->status=2;
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
            $data['cates']=(new ShopCategory())->where("user_id=?")->bindValues($this->user_id)->get();
            $this->view('goods_form',$data);
        }

    }

    public function change(Goods $goods,Request $request)
    {
        $goods=$goods->findOrFail($request->get('id'));
        if($goods->user_id==$this->user_id){
            if($goods->status==1){
                $goods->status=2;
                $goods->save();
            }elseif($goods->status==2){
                $goods->status=1;
                $goods->save();
            }
            redirect('goods')->with('msg','操作成功！');
        }else{
            redirect('goods')->with('error','操作失败！');
        }
    }

    public function del(Goods $goods,Request $request)
    {
        $goods=$goods->findOrFail($request->get('id'));
        if($goods->user_id==$this->user_id){
            $goods->status=-1;
            $goods->save();
            redirect('goods')->with('msg','册除成功！');
        }else{
            redirect('goods')->with('error','操作失败！');
        }
    }
}