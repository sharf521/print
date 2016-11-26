<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/26
 * Time: 12:12
 */

namespace App\Controller\Member;


use App\Model\UserAddress;
use System\Lib\Request;

class AddressController extends MemberController
{
    public function __construct()
    {
        parent::__construct();
        if(isset($_GET['redirect_url']) && $_GET['redirect_url']!=''){
            $this->redirect_url=$_GET['redirect_url'];
        }else{
            $this->redirect_url='address';
        }
    }
    public function index(UserAddress $address,Request $request)
    {
        $result=$address->where("user_id=?")->bindValues($this->user_id)->get();
        $data['result']=$result;
        $this->view('address',$data);
    }

    public function add(UserAddress $address,Request $request)
    {
        if($_POST){
            $address->user_id=$this->user_id;
            $address->province=0;
            $address->city=0;
            $address->county=0;
            $address->region_name=$request->post('province').'-'.$request->post('city').'-'.$request->post('county');
            $address->name=$request->post('name');
            $address->phone=$request->post('phone');
            $address->address=$request->post('address');
            $count=$address->where("user_id=?")->bindValues($this->user_id)->value('count(id)');
            if($count==0){
                $address->is_default=1;
            }else{
                $address->is_default=0;
            }
            $address->save();
            redirect($this->redirect_url)->with('msg','添加成功！');
        }else{
            $this->view('address');
        }
    }

    public function setDefault(UserAddress $address,Request $request)
    {
        $address=$address->findOrFail($request->get('id'));
        if($address->user_id==$this->user_id){
            $address->where('user_id=?')->bindValues($this->user_id)->update(array('is_default'=>0));
            $address->is_default=1;
            $address->save();
        }
        redirect('address')->with('msg','操作完成！');
    }

    public function del(UserAddress $address,Request $request)
    {
        $address=$address->findOrFail($request->get('id'));
        if($address->user_id==$this->user_id){
            $address->delete();
            if($address->is_default==1){
                $address->where('user_id=?')->bindValues($this->user_id)->limit(1)->update(array('is_default'=>1));
            }
        }
        redirect('address')->with('msg','操作完成！');
    }
}