<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 */

namespace app\Controller;

use app\Model\PrintShop;
use System\Lib\Request;
use App\WeChat;

class ShopController extends WeixinController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Request $request, PrintShop $shop)
    {
        $user_id=(int)$request->get('user_id');
        if($user_id==0){
            $user_id=$this->user_id;
        }
        if ($_POST) {
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
            $shop->user_id = $user_id;
            $shop->picture = $picture;
            $shop->remark = $remark;
            $shop->name = $name;
            $shop->save();
            if(isset($_GET['user_id'])){
                redirect('weixin/invite')->with('msg', '添加成功！');
            }else{
                redirect('shop')->with('msg', '添加成功！');
            }
        } else {
            $data['title_herder'] = '商户联盟';
            $list=$shop->where("user_id=?")->bindValues($this->user_id)->get();
            $data['list']=$list;

            $weChat=new WeChat();
            $js = $weChat->app->js;
            $data['config']=$js->config(array('chooseWXPay','openAddress','checkJsApi','getLocation'), false);
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
            redirect('shop')->with('msg','保存成功！');
        }else{
            $data['shop']=$shop;
            $this->view('shop', $data);
        }
    }
}