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
            $spec_1=$request->post('spec_1');
            $shipping_fee=(float)$request->post('shipping_fee');
            $content=$request->post('content');
            $shop_cateid=(int)$request->post('shop_category');
            $is_have_spec=(int)$request->post('is_have_spec');
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
                $goods->is_have_spec=$is_have_spec;
                $goods->shipping_fee=(float)$shipping_fee;
                $goods->sale_count=0;
                $goods->status=2;
                $goods_id=$goods->save(true);
                $goodsData->goods_id=$goods_id;
                $goodsData->content=$content;
                $goodsData->save();
                $goodsImage->where("user_id=? and id in({$imgids})")->bindValues($this->user_id)->update(array('goods_id'=>$goods_id));
                $goods=$goods->find($goods_id);
                //$goods->image_url=$goodsImage->where("goods_id=?")->bindValues($goods_id)->first()->image_url;
                $goods->image_url=$goods->GoodsImage()[0]->image_url;
                if($is_have_spec==1 && is_array($spec_1)){
                    $stock_total=0;
                    foreach($spec_1 as $i=>$v){
                        $spec=new GoodsSpec();
                        $spec->goods_id=$goods->id;
                        $spec->spec_1=$spec_1[$i];
                        $spec->price=(float)$price[$i];
                        $spec->stock_count=(int)$stock_count[$i];
                        $spec->save();
                        if($stock_total==0){
                            $goods->price=$spec->price;
                        }
                        $stock_total+=$spec->stock_count;
                    }
                    $goods->stock_count=$stock_total;
                }
                $goods->save();
                DB::commit();
                redirect('goods/list_status2')->with('msg', '添加成功！');
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

    public function edit(Goods $goods,GoodsImage $goodsImage,Request $request)
    {
        $goods=$goods->findOrFail($request->get('id'));
        if($_POST){
            $imgids=trim($request->post('imgids'),',');
            $name=$request->post('name');
            $price=$request->post('price');
            $stock_count=$request->post('stock_count');
            $spec_1=$request->post('spec_1');
            $spec_id=$request->post('spec_id');
            $shipping_fee=(float)$request->post('shipping_fee');
            $content=$request->post('content');
            $shop_cateid=(int)$request->post('shop_category');
            $is_have_spec=(int)$request->post('is_have_spec');
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
                $goods->shop_cateid=$shop_cateid;
                $goods->shop_catepath=$shop_catepath;
                $goods->image_url='';
                $goods->name=$name;
                $goods->price=(float)$price;
                $goods->stock_count=(int)$stock_count;
                $goods->is_have_spec=$is_have_spec;
                $goods->shipping_fee=(float)$shipping_fee;
                $goods->save();
                $goodsData=$goods->GoodsData();
                $goodsData->content=$content;
                $goodsData->save();
                $goodsImage->where("user_id=? and id in({$imgids})")->bindValues($this->user_id)->update(array('goods_id'=>$goods->id));
                $goods->image_url=$goods->GoodsImage()[0]->image_url;
                $array_spec=array(0);
                if($is_have_spec==1 && is_array($spec_1)){
                    $stock_total=0;
                    foreach($spec_1 as $i=>$v){
                        $spec=(new GoodsSpec())->find($spec_id[$i]);
                        $spec->goods_id=$goods->id;
                        $spec->spec_1=$spec_1[$i];
                        $spec->price=(float)$price[$i];
                        $spec->stock_count=(int)$stock_count[$i];
                        if($spec->is_exist){
                            $spec->save();
                            array_push($array_spec,$spec_id[$i]);
                        }else{
                            $_id=$spec->save(true);
                            array_push($array_spec,$_id);
                        }
                        if($stock_total==0){
                            $goods->price=$spec->price;
                        }
                        $stock_total+=$spec->stock_count;
                    }
                    $goods->stock_count=$stock_total;
                }
                DB::table('goods_spec')->where("goods_id={$goods->id} and id not in(".implode(',',$array_spec).")")->delete();
                $goods->save();
                DB::commit();
                if($goods->stock_count==0){
                    redirect('goods/list_stock0')->with('msg', '修改成功！');
                }
                if($goods->status==1){
                    redirect('goods')->with('msg', '修改成功！');
                }
                if($goods->status==2){
                    redirect('goods/list_status2')->with('msg', '修改成功！');
                }
            }catch(\Exception $e){
                DB::rollBack();
                $error = "Failed: " . $e->getMessage();
                redirect()->back()->with('error', $error);
            }
        }else{
            $data['goods']=$goods;
            if($goods->is_have_spec){
                $data['specs']=$goods->GoodsSpec();
            }else{
                $data['specs']=array();
            }
            $data['images']=$goods->GoodsImage();
            $data['GoodsData']=$goods->GoodsData();
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
                redirect('goods/list_status2')->with('msg', '己下架！');
            }elseif($goods->status==2){
                $goods->status=1;
                $goods->save();
                redirect('goods')->with('msg','己上架！');
            }
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