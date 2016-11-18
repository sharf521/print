<?php
namespace App\Controller\Member;

use App\Model\AccountBank;
use App\Model\LinkPage;
use App\Model\Region;
use System\Lib\Request;

class UserController  extends MemberController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        
    }
    public function userInfo(Request $request)
    {
        if ($_POST) {
            $this->user->tel = $request->post('tel');
            $this->user->qq = $request->post('qq');
            $this->user->address = $request->post('address');
            $this->user->headimgurl=$request->post('headimgurl');
            $this->user->save();
            redirect()->back()->with('msg', '保存成功！');
        } else {
            $data['user'] = $this->user;
            $data['title_herder']='个人信息';
            $this->view('user', $data);
        }
    }
    public function realName(Request $request,Region $region)
    {
        $user= $this->user;
        $userInfo=$user->UserInfo();
        if($_POST){
            $name=$request->post('name');
            $sex=(int)$request->post('sex');
            $card_no=$request->post('card_no');
            $province=(int)$request->post('province');
            $city=(int)$request->post('city');
            $county=(int)$request->post('county');
            $card_pic1=$request->post('card_pic1');
            $card_pic2=$request->post('card_pic2');
            if(empty($name)){
                redirect()->back()->with('error', '姓名不能为空！');
            }
            if(! $userInfo->isIdCard($card_no)){
                redirect()->back()->with('error', '请输入正确的身份证号！');
            }
            if(empty($province) || empty($city) || empty($county)){
                redirect()->back()->with('error', '请选择籍贯！');
            }
            if(empty($card_pic1) || empty($card_pic2)){
                redirect()->back()->with('error', '请上传身份证照片！');
            }

            $userInfo->name=$name;
            $userInfo->sex=$sex;
            $userInfo->card_no=$card_no;
            $userInfo->province=$province;
            $userInfo->city=$city;
            $userInfo->county=$county;
            $userInfo->card_pic1=$card_pic1;
            $userInfo->card_pic2=$card_pic2;
            $userInfo->card_status=1;
            $userInfo->user_id=$this->user_id;
            $userInfo->save();
            redirect()->back()->with('msg', '操作成功，等待管理员审核！');
        }else{
            $userInfo->provinceName=$region->getName($userInfo->province);
            $userInfo->cityName=$region->getName($userInfo->city);
            $userInfo->countyName=$region->getName($userInfo->county);
            $data['provinceArray']=$region->getList(0);
            $data['userInfo']=$userInfo;
            $data['user'] = $user;

            $data['title_herder']='实名认证';
            $this->view('realName', $data);
        }
    }
    
    public function bank(AccountBank $accountBank,Request $request,LinkPage $linkPage)
    {
        $bank=$accountBank->find($this->user->id);
        if($_POST){
            $bank->user_id=$this->user->id;
            $bank->bank = $request->post('bank');
            $bank->branch = $request->post('branch');
            $bank->card_no = $request->post('card_no');
            if ($bank->save()) {
                redirect()->back()->with('msg', '保存成功！');
            } else {
                redirect()->back()->with('error', '保存失败！');
            }
        }else{
            $bank->selBank=$linkPage->echoLink('account_bank',$bank->bank,array('name'=>'bank'));
            $data['bank']=$bank;
            $data['userInfo']=$this->user->UserInfo();
            $data['title_herder']='我的银行卡';
            $this->view('user',$data);
        }
    }
}