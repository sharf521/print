<?php
namespace App\Controller;

use App\Model\LinkPage;
use App\Model\PrintTask;
use App\Model\User;
use App\Model\UserWx;
use System\Lib\Request;

class WeixinController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $agent = addslashes($_SERVER['HTTP_USER_AGENT']);
        if(strpos($agent, 'MicroMessenger') === false && strpos($agent, 'Windows Phone') === false)
        {
            //echo '非微信浏览器不能访问';
            //die('Sorry！非微信浏览器不能访问');
        }
        if(empty($this->user_id)){
            $url=urlencode($_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
            redirect("wxapi/oauth/?url={$url}");
            exit;
        }
        $this->template = 'weixin';
    }
    
    

    public function orderAdd(Request $request,LinkPage $linkPage,PrintTask $printTask)
    {
        if($_POST){
            $print_type=$request->post('print_type');
            $remark=$request->post('remark');
            $tel=$request->post('tel');
            if (empty($print_type)) {
                redirect()->back()->with('error', '请选择类型');
            }
            if (empty($remark)) {
                redirect()->back()->with('error', '请填写具体要求');
            }
            if (empty($tel)) {
                redirect()->back()->with('error', '请填写联系电话');
            }
            $printTask->print_type=$print_type;
            $printTask->remark=$remark;
            $printTask->tel=$tel;
            $print_type->status=0;
            $printTask->save();
            redirect('weixin/orderList')->with('msg', '下单成功，稍后工作人员会联系您，您也可以在微信里留言。');
        }else{
            $data['print_type']=$linkPage->echoLink('print_type','',array('type'=>'radio'));
            $data['title_herder']='我要下单';
            $this->view('print',$data);
        }
    }

    public function orderList()
    {
        $data['title_herder']='我的订单';
        $this->view('print',$data);
    }

    public function union()
    {
        echo '联盟页';
    }
}