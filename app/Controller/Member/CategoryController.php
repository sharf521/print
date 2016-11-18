<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/18
 * Time: 15:31
 */

namespace App\Controller\Member;


use App\Model\ShopCategory;
use System\Lib\Request;

class CategoryController extends MemberController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(ShopCategory $shopCategory)
    {
        $data['cates']=$shopCategory->getListTree($this->user_id);
        $this->view('category',$data);
    }

    public function add(ShopCategory $shopCategory,Request $request)
    {
        if($_POST){
            $pid=(int)$request->post('pid');
            $name=$request->post('name');
            if(empty($name)){
                redirect()->back()->with('error','名称不能为空！');
            }
            $path='';
            if($pid!=0){
                $path=(new ShopCategory())->find($pid)->path;
            }
            $shopCategory->user_id=$this->user_id;
            $shopCategory->pid=$pid;
            $shopCategory->path=$path;
            $shopCategory->name=$name;
            $shopCategory->showorder=10;
            $id=$shopCategory->save(true);
            $cate=$shopCategory->find($id);
            $cate->path=$cate->path.$id.',';
            $cate->save();
            redirect('category')->with('msg','添加成功！');
        }else{
            $data=array();
            $this->view('category',$data);
        }
    }

    public function edit(ShopCategory $shopCategory,Request $request)
    {
        $id=(int)$request->get('id');
        $cate=$shopCategory->findOrFail($id);
        if($_POST){
            $name=$request->post('name');
            if(empty($name)){
                redirect()->back()->with('error','名称不能为空！');
            }
            $cate->name=$name;
            $cate->save();
            redirect('category')->with('msg','修改成功！');
        }else{
            $data['cate']=$cate;
            $this->view('category',$data);
        }
    }

    public function del(ShopCategory $shopCategory,Request $request)
    {
        $shopCategory=$shopCategory->findOrFail($request->get('id'));
        if($shopCategory->user_id==$this->user_id){
            $sonCount=(new ShopCategory())->where("pid={$shopCategory->id}")->value("count(*)",'int');
            if($sonCount==0){
                $shopCategory->delete();
                redirect('category')->with('msg','册除成功！');
            }else{
                redirect()->back()->with('error','请先删除子分类！');
            }
        }else{
            redirect('category')->with('error','操作失败！');
        }
    }
}