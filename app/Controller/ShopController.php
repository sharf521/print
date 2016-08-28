<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 */

namespace app\Controller;

use app\Model\PrintShop;
use app\Model\PrintShopGroup;
use App\Model\User;
use System\Lib\Request;
use App\WeChat;

class ShopController extends WeixinController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(PrintShop $shop,User $user)
    {
        $list=$shop->where("user_id=?")->bindValues($this->user_id)->get();
        $data['user']=$user->find($this->user_id);
        $data['list']=$list;
        $data['title_herder'] = '商户列表';
        $this->view('shop', $data);
    }

    public function add(Request $request, PrintShop $shop)
    {
        $user_id=(int)$request->get('user_id');
        if($user_id==0){
            $user_id=$this->user_id;
        }
        if ($_POST) {
            $name = $request->post('name');
            $picture = $request->post('picture');
            $remark = $request->post('remark');
            $address = $request->post('address');
            $tel = $request->post('tel');
            if (empty($name)) {
                redirect()->back()->with('error', '店名不能为空！');
            }
            if (empty($picture)) {
                redirect()->back()->with('error', '门头照片不能为空！');
            }
            if (empty($remark)) {
                redirect()->back()->with('error', '主营不能为空！');
            }
            if (empty($tel)) {
                redirect()->back()->with('error', '电话不能为空！');
            }
            if (empty($address)) {
                redirect()->back()->with('error', '地址不能为空！');
            }
            $shop->user_id = $user_id;
            $shop->picture = $picture;
            $shop->remark = $remark;
            $shop->name = $name;
            $shop->tel=$tel;
            $shop->address=$address;
            $shop->save();
            if(isset($_GET['user_id'])){
                redirect('weixin/invite')->with('msg', '添加成功！');
            }else{
                redirect('shop')->with('msg', '添加成功！');
            }
        } else {
            $data['title_herder'] = '添加商户';
            $weChat=new WeChat();
            $js = $weChat->app->js;
            $data['config']=$js->config(array('chooseWXPay','openAddress','checkJsApi','getLocation','chooseImage'), true);
            $this->view('shop', $data);
        }
    }
    
    public function edit(Request $request,PrintShop $printShop)
    {
        $id=$request->id;
        $page=$request->page;
        $shop=$printShop->findOrFail($id);
        if($shop->user_id != $this->user_id){
            redirect()->back()->with('error', '数据异常！');
        }
        if($_POST){
            $name = $request->post('name');
            $picture = $request->post('picture');
            $remark = $request->post('remark');
            $address = $request->post('address');
            $tel = $request->post('tel');
            if (empty($name)) {
                redirect()->back()->with('error', '店名不能为空！');
            }
            if (empty($picture)) {
                redirect()->back()->with('error', '门头照片不能为空！');
            }
            if (empty($remark)) {
                redirect()->back()->with('error', '主营不能为空！');
            }
            if (empty($tel)) {
                redirect()->back()->with('error', '电话不能为空！');
            }
            if (empty($address)) {
                redirect()->back()->with('error', '地址不能为空！');
            }
            $shop->picture = $picture;
            $shop->remark = $remark;
            $shop->name = $name;
            $shop->tel=$tel;
            $shop->address=$address;
            $shop->save();
            redirect('shop')->with('msg','保存成功！');
        }else{
            $data['shop']=$shop;
            $this->view('shop', $data);
        }
    }

    public function delete(PrintShop $printShop,Request $request)
    {
        $shop=$printShop->findOrFail($request->get('id'));
        if($shop->user_id==$this->user_id){
            $shop->delete();
        }
        redirect()->back()->with('msg','删除完成!');
    }

}