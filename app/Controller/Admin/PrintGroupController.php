<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/21
 * Time: 20:57
 */

namespace app\Controller\Admin;


use app\Model\PrintGroup;
use app\Model\PrintShopGroup;
use System\Lib\Request;

class PrintGroupController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(PrintGroup $printGroup)
    {
        $data['printGroup']=$printGroup->orderBy('id desc')->pager($_GET['page'],10);
        $this->view('printGroup',$data);
    }

    public function shopList(Request $request,PrintShopGroup $shopGroup)
    {
        $id=$request->get('id');
        $list=$shopGroup->where("group_id=?")->bindValues($id)->get();
        $data['list']=$list;
        $this->view('printGroup',$data);
    }

    public function add(Request $request)
    {
        if($_POST){
            $printGroup=new PrintGroup();
            $printGroup->name=$request->post('name');
            $printGroup->picture=$request->post('picture');
            $printGroup->remark=$request->post('remark');
            $printGroup->save();
            redirect('printGroup')->with('msg','添加成功！');
        }else{
            $this->view('printGroup');
        }
    }
    public function edit(Request $request,PrintGroup $printGroup)
    {
        $printGroup=$printGroup->findOrFail($request->id);
        if($_POST){
            $printGroup->name=$request->post('name');
            $printGroup->picture=$request->post('picture');
            $printGroup->remark=$request->post('remark');
            $printGroup->save();
            redirect('printGroup')->with('msg','保存成功！');
        }else{
            $data['group']=$printGroup;
            $this->view('printGroup',$data);
        }
    }
    public function delete(Request $request,PrintGroup $printGroup)
    {
        $printGroup=$printGroup->findOrFail($request->id);
        $printGroup->delete($printGroup->id);
        redirect()->back()->with('msg','删除成功！');
    }
}