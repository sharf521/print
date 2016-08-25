<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/21
 * Time: 16:46
 */

namespace app\Controller\Admin;


use app\Model\PrintGroup;
use app\Model\PrintShop;
use app\Model\PrintShopGroup;
use System\Lib\Request;

class PrintShopController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request,PrintShop $printShop,PrintGroup $printGroup)
    {
        if($_POST){
            $shop_id_arr=$request->post('id');
            if(empty($shop_id_arr)){
                redirect()->back()->with('error','请选择商铺！');
            }
            $group_id=(int)$request->post('group_id');
            $shop_group=new PrintShopGroup();
            foreach ($shop_id_arr as $id){
                $id=(int)$id;
                if($id!=0){
                    $shop_group=$shop_group->where("group_id={$group_id} && shop_id={$id}")->first();
                    if(!$shop_group->is_exist){
                        $shop_group->group_id=$group_id;
                        $shop_group->shop_id=$id;
                        $shop_group->save();
                    }
                }
            }
            redirect()->back()->with('msg','操作完成 ！');
        }else{
            $where=" 1=1";
            $q=$request->get('q');
            $starttime=$request->get('starttime');
            $endtime=$request->get('endtime');
            if(!empty($starttime)){
                $where.=" and created_at>=".strtotime($starttime);
            }
            if(!empty($endtime)){
                $where.=" and created_at<".strtotime($endtime);
            }
            if(!empty($q)){
                $where.=" and name like '%{$q}%'";
            }
            $data['printShop'] = $printShop->where($where)->orderBy('id desc')->pager($_GET['page'], 10);
            $data['printGroup']=$printGroup->orderBy('id desc')->get();
            $this->view('printShop', $data);
        }
    }

    public function delete(PrintShop $printShop,Request $request)
    {
        $shop=$printShop->findOrFail($request->get('id'));
        $shop->delete();
        redirect()->back()->with('msg','删除完成!');
    }

    public function edit(Request $request,PrintShop $printShop)
    {
        $id=$request->id;
        $page=$request->page;
        $shop=$printShop->findOrFail($id);
        if($_POST){
            $name = $request->post('name');
            $picture = $request->post('picture');
            $remark = $request->post('remark');
            $address = $request->post('address');
            if (empty($name)) {
                redirect()->back()->with('error', '请填写名称');
            }
            if (empty($picture)) {
                redirect()->back()->with('error', '请上传图片');
            }
            if (empty($remark)) {
                redirect()->back()->with('error', '请填写介绍');
            }
            if (empty($address)) {
                redirect()->back()->with('error', '请填写所在地址');
            }
            $shop->picture = $picture;
            $shop->remark = $remark;
            $shop->name = $name;
            $shop->address = $address;
            $shop->save();
            $url="printShop/?page={$page}";
            redirect($url)->with('msg','保存成功！');
        }else{
            $data['shop']=$shop;
            $this->view('printShop', $data);
        }
    }
}