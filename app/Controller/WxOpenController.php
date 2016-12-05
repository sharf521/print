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
use EasyWeChat\Message\Text;
use System\Lib\Request;

include ROOT."/public/extended/wx/wxBizMsgCrypt.php";

class WxOpenController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->WeChatOpen=new WeChatOpen();
        $this->app=$this->WeChatOpen->app;
        $this->component_appid=$this->WeChatOpen->options['app_id'];
        $this->component_appsecret=$this->WeChatOpen->options['secret'];
    }

    public function index()
    {
        $redirect_uri='http://'.$_SERVER['HTTP_HOST'].url('wxOpen/auth_code');
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
        }
        $this->log($html);
    }

    //wxOpen/event/wx02560f146a566747
    public function event(Request $request,WeChatAuth $auth)
    {
        $app_id=$request->get(2);
        //$auth=$auth->findOrFail($app_id);
        //$this->app['access_token']->setToken($auth->authorizer_access_token);

        $server=$this->WeChatOpen->app->server;
        $server->setMessageHandler(function ($message) {
            switch ($message->MsgType) {
                case 'event':
                    return new Text(['content' => $message->Event.'from_callback']);
                    //return $this->event($message);
                    break;
                case 'text':
                    return $this->text($message);
                    break;
                default:
                    # code...
                    break;
            }
        });
        $server->serve()->send();

        $msg=$server->getMessage();
        $msg=json_encode($msg);
        $this->log($msg);
    }

    private function text($message)
    {
        if($message->Content=='TESTCOMPONENT_MSG_TYPE_TEXT'){
/*            include ROOT."/public/extended/wx/wxBizMsgCrypt.php";
            $pc = new WXBizMsgCrypt($this->WeChatOpen->options['token'], $this->WeChatOpen->options['aes_key'], $this->component_appid);
            $ticket=(new WeChatTicket())->first();
            // file:http://print.yuantuwang.com/wxOpen/event/wx02560f146a566747?signature=604d456e091a0d8b610994d8fc6ea2f0ac24ba35&timestamp=1480759705&nonce=536707496&openid=on0aqs51hEudNQsGESP3GWEMYe78&encrypt_type=aes&msg_signature=9506d80a0a74ead57cd0d8641681170003e41ce1

            $text="<xml>
<ToUserName><![CDATA[{$message->FromUserName}]]></ToUserName>
<FromUserName><![CDATA[{$message->ToUserName}]]></FromUserName>
<CreateTime>".time()."</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[TESTCOMPONENT_MSG_TYPE_TEXT_callback]]></Content>
</xml>";
            $errCode = $pc->encryptMsg($text, $_GET['timestamp'], $_GET['nonce'], 'aes');*/

/*            $xml="<xml>
<ToUserName><![CDATA[{$message->FromUserName}]]></ToUserName>
<FromUserName><![CDATA[{$message->ToUserName}]]></FromUserName>
<CreateTime>".time()."</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[TESTCOMPONENT_MSG_TYPE_TEXT_callback]]></Content>
</xml>";


            $msg=$xml;
            $file_path = ROOT . "/public/data/wx/";
            if (!is_dir($file_path)) {
                mkdir($file_path, 0777, true);
            }
            $filename = $file_path . date("Ym") . "TESTCOMPONENT_MSG_TYPE_TEXT.log";
            $fp = fopen($filename, "a+");
            $time = date('Y-m-d H:i:s');
            $file = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
            $str = "time:{$time} \t{error:" . $msg . "}\t file:{$file}\t\r\n";
            fputs($fp, $str);
            fclose($fp);
            return new Raw($xml);*/
            return new Text(['content' => 'TESTCOMPONENT_MSG_TYPE_TEXT_callback']);
        }
        if(substr($message->Content,0,16)=='QUERY_AUTH_CODE:'){
            $query_auth_code=substr($message->Content,16);
            $redirect_uri='http://'.$_SERVER['HTTP_HOST'].url("wxOpen/auth_code/?auth_code={$query_auth_code}");
            $this->log("\r\n AAA".$redirect_uri);
            $html=$this->curl_url($redirect_uri);
            $this->log($html);

            $str=$query_auth_code."_from_api";
            //发送消息
            $staff = $this->app->staff; // 客服管理
            $message=new Text(['content' =>$str]);
            $staff->message($message)->to($message->FromUserName)->send();
        }
    }

    //平台接收消息
    public function ticket(WeChatTicket $chatTicket,Request $request)
    {
        $server=$this->WeChatOpen->app->server;
        $msg=$server->getMessage();
        $this->log(json_encode($msg));
        //10分钟推送一次
        if($msg['InfoType']=='component_verify_ticket'){
            $chatTicket=$chatTicket->first();
            $chatTicket->timestamp=$request->timestamp;
            $chatTicket->nonce=$request->nonce;
            $chatTicket->encrypt_type=$request->encrypt_type;
            $chatTicket->msg_signature=$request->msg_signature;
            $chatTicket->CreateTime=$msg['CreateTime'];
            $chatTicket->InfoType=$msg['InfoType'];
            $chatTicket->ComponentVerifyTicket=$msg['ComponentVerifyTicket'];
            $chatTicket->save();
            //token
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
        }elseif($msg['InfoType']=='authorized'){
            $AuthorizationCode=$request->AuthorizationCode;
            $redirect_uri='http://'.$_SERVER['HTTP_HOST'].url("wxOpen/auth_code/?auth_code={$AuthorizationCode}");
            $this->log($redirect_uri);
            $html=$this->curl_url($redirect_uri);
            $this->log($html);
            $txt="{$AuthorizationCode}_from_api";
            //发送消息
            $staff = $this->app->staff; // 客服管理
            $message=new Text(['content' =>$txt]);
            $staff->message($message)->to($message->FromUserName)->send();
        }
        echo 'success';
    }

    public function log($msg)
    {
        $file_path = ROOT . "/public/data/wx/";
        if (!is_dir($file_path)) {
            mkdir($file_path, 0777, true);
        }
        $filename = $file_path . date("Ym") . "wxopen.log";
        $fp = fopen($filename, "a+");
        $time = date('Y-m-d H:i:s');
        $file = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
        $str = "time:{$time} \r\n{$file}\r\n{ $msg}\r\n\r\n";
        fputs($fp, $str);
        fclose($fp);
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