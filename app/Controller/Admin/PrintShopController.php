<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/21
 * Time: 16:46
 */

namespace app\Controller\Admin;


use app\Model\PrintShop;
use System\Lib\Request;

class PrintShopController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }


    public function index(Request $request,PrintShop $printShop)
    {
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
        $this->view('printShop', $data);
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
            if (empty($name)) {
                redirect()->back()->with('error', '请填写名称');
            }
            if (empty($picture)) {
                redirect()->back()->with('error', '请上传图片');
            }
            if (empty($remark)) {
                redirect()->back()->with('error', '请填写介绍');
            }
            $shop->picture = $picture;
            $shop->remark = $remark;
            $shop->name = $name;
            $shop->save();
            $url="printShop/?page={$page}";
            redirect($url)->with('msg','保存成功！');
        }else{
            $data['shop']=$shop;
            $this->view('printShop', $data);
        }
    }
}