<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/1
 * Time: 18:07
 */

namespace App\Controller;

use App\Model\WeChatTicket;
use App\WeChatOpen;
use System\Lib\Request;

class WxOpenController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $AppID='wx0453db85b190df07';
        $AppSecret='a0845f7bca562a55aa47a07f1b043dcd';
        $redirect_uri='http://print.yuantuwang.com/index/error';
        $url="https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid={$AppID}&pre_auth_code={$AppSecret}&redirect_uri={$redirect_uri}";
        echo "<a href='{$url}'>授权</a>";
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

    public function ticket(WeChatTicket $chatTicket,Request $request)
    {
        $WeChatOpen=new WeChatOpen();
        $server=$WeChatOpen->app->server;
        $msg=$server->getMessage();
        $chatTicket->timestamp=$request->timestamp;
        $chatTicket->nonce=$request->nonce;
        $chatTicket->encrypt_type=$request->encrypt_type;
        $chatTicket->msg_signature=$request->msg_signature;
        $chatTicket->CreateTime=$msg['CreateTime'];
        $chatTicket->InfoType=$msg['InfoType'];
        $chatTicket->ComponentVerifyTicket=$msg['ComponentVerifyTicket'];
        $chatTicket->save();

        $msg=json_encode($msg);
        $file_path = ROOT . "/public/data/wx/";
        if (!is_dir($file_path)) {
            mkdir($file_path, 0777, true);
        }
        $filename = $file_path . date("Ym") . "ticket.log";
        $fp = fopen($filename, "a+");
        $time = date('Y-m-d H:i:s');
        $file = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
        $str = "time:{$time} \t{error:" . $msg . "}\t file:{$file}\t\r\n";
        fputs($fp, $str);
        fclose($fp);
        echo 'success';
    }
}