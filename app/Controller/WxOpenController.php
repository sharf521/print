<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/1
 * Time: 18:07
 */

namespace App\Controller;

use App\Model\WeChatAuth;
use App\Model\WeChatTicket;
use App\WeChatOpen;
use System\Lib\Request;

include ROOT."/public/extended/wx/wxBizMsgCrypt.php";

class WxOpenController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->WeChatOpen=new WeChatOpen();
        $this->component_appid=$this->WeChatOpen->options['app_id'];
        $this->component_appsecret=$this->WeChatOpen->options['secret'];
    }

    public function index(WeChatTicket $chatTicket)
    {
        $redirect_uri='http://'.$_SERVER['HTTP_HOST'].url('wxopen/auth_code');
        $code=$this->getPreAuthCode();
        $url="https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid={$this->component_appid}&pre_auth_code={$code}&redirect_uri={$redirect_uri}";
        echo $url;
        echo "<a href='{$url}'>授权</a>";
    }

    //授权返回
    public function auth_code(Request $request)
    {
        //?auth_code=queryauthcode@@@9QJDTmdBO731Nz9_I-DyLgb-EOygA8WedAmM_h4LaXSxebJODjNYAWRVL9x-OKRzEOQQGSAzkOAaB5vkd-Po9A&expires_in=3600
        $auth_code=$request->get('auth_code');


        $ticket=(new WeChatTicket())->first();
        $url="https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token={$ticket->component_access_token}";
        $arr=array(
            'component_appid'=>$this->component_appid,
            'authorization_code'=>$auth_code
        );
        $html=$this->curl_url($url,json_encode($arr));
        $json=json_decode($html);
        if(isset($json->authorization_info)){
            $json=$json->authorization_info;
            $auth=(new WeChatAuth())->find($json->authorizer_appid);
            $auth->user_id=$this->user_id;
            $auth->auth_code=$auth_code;
            $auth->expires_in=time()+3000;
            $auth->authorizer_appid=$json->authorizer_appid;
            $auth->authorizer_access_token=$json->authorizer_access_token;
            $auth->authorizer_refresh_token=$json->authorizer_refresh_token;
            $auth->authorizer_expires_in=time()+7000;
            $auth->func_info=serialize($json->func_info);
            $auth->save();
        }else{
            echo $html;
            exit;
        }
    }
    
    public function event()
    {
        $msg=json_encode($_REQUEST);
        $file_path = ROOT . "/public/data/wx/";
        if (!is_dir($file_path)) {
            mkdir($file_path, 0777, true);
        }
        $filename = $file_path . date("Ym") . "event.log";
        $fp = fopen($filename, "a+");
        $time = date('Y-m-d H:i:s');
        $file = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
        $str = "time:{$time} \t{error:" . $msg . "}\t file:{$file}\t\r\n";
        fputs($fp, $str);
        fclose($fp);
    }

    //10分钟推送一次
    public function ticket(WeChatTicket $chatTicket,Request $request)
    {
        $server=$this->WeChatOpen->app->server;
        $msg=$server->getMessage();
        $chatTicket=$chatTicket->first();
        $chatTicket->timestamp=$request->timestamp;
        $chatTicket->nonce=$request->nonce;
        $chatTicket->encrypt_type=$request->encrypt_type;
        $chatTicket->msg_signature=$request->msg_signature;
        $chatTicket->CreateTime=$msg['CreateTime'];
        $chatTicket->InfoType=$msg['InfoType'];
        $chatTicket->ComponentVerifyTicket=$msg['ComponentVerifyTicket'];
        $chatTicket->save();

        if($chatTicket->token_expires_in<time()){
            $arr=array(
                'component_appid'=>$this->component_appid,
                'component_appsecret'=>$this->component_appsecret,
                'component_verify_ticket'=>$chatTicket->ComponentVerifyTicket
            );
            $html=$this->curl_url('https://api.weixin.qq.com/cgi-bin/component/api_component_token',json_encode($arr));
            $html=json_decode($html);
            $chatTicket->component_access_token=$html->component_access_token;
            $chatTicket->token_expires_in=time()+6000;
            $chatTicket->save();
        }

        $msg=json_encode($msg);
        $file_path = ROOT . "/public/data/wx/";
        if (!is_dir($file_path)) {
            mkdir($file_path, 0777, true);
        }
        $filename = $file_path . date("Ym") . "ticket.log";
        $fp = fopen($filename, "a+");
        $time = date('Y-m-d H:i:s');
        $file = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
        $str = "time:{$time} \t{error:" . $msg . "}\t file:{$file}\t\r\n\r\n";
        fputs($fp, $str);
        fclose($fp);
        echo 'success';
    }

    public function getPreAuthCode()
    {
        $chatTicket=(new WeChatTicket())->first();
        $url="https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token={$chatTicket->component_access_token}";
        $arr=array("component_appid"=>$this->component_appid);
        $html=$this->curl_url($url,json_encode($arr));
        $json=json_decode($html);
        if(isset($json->pre_auth_code)){
            return $json->pre_auth_code;
        }else{
            echo $html;
            exit;
        }
    }
    
    private function curl_url($url, $data = array())
    {
        $ssl = substr($url, 0, 8) == "https://" ? TRUE : FALSE;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        if ($data) {
            if (is_array($data)) {
                curl_setopt($ch, CURLOPT_POST, 1);
            } else {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($data))
                );
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        if ($ssl) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        }
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}