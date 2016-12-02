<?php
namespace App\Controller;

use App\Model\WeChatTicket;
use App\WeChatOpen;
use System\Lib\Request;

class IndexController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $WeChatOpen=new WeChatOpen();
        $server=$WeChatOpen->app->server;
        $xml=json_encode($server->getMessage());
        $file_path = ROOT . "/public/data/wx/";
        if (!is_dir($file_path)) {
            mkdir($file_path, 0777, true);
        }
        $filename = $file_path . date("Ym") . "construct.log";
        $fp = fopen($filename, "a+");
        $time = date('Y-m-d H:i:s');
        $file = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
        $str = "time:{$time} \t{error:" . $xml . "}\t file:{$file}\t\r\n";
        fputs($fp, $str);
        fclose($fp);
    }

    public function ticket(WeChatTicket $chatTicket,Request $request)
    {
        $WeChatOpen=new WeChatOpen();
        $server=$WeChatOpen->app->server;
        $msg=json_encode($server->getMessage());
        $chatTicket->timestamp=$request->timestamp;
        $chatTicket->nonce=$request->nonce;
        $chatTicket->encrypt_type=$request->encrypt_type;
        $chatTicket->msg_signature=$request->msg_signature;
        $chatTicket->CreateTime=$msg['CreateTime'];
        $chatTicket->InfoType=$msg['InfoType'];
        $chatTicket->component_verify_ticket=$msg['component_verify_ticket'];
        $chatTicket->save();
        $xml = $GLOBALS["HTTP_RAW_POST_DATA"];
        if(empty($xml))
        {
            $xml = file_get_contents("php://input");
        }
        $msg=$xml;
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

    public function index()
    {
        $xml = $GLOBALS["HTTP_RAW_POST_DATA"];
        if(empty($xml))
        {
            $xml = file_get_contents("php://input");
        }
        $msg=$xml;
        $file_path = ROOT . "/public/data/wx/";
        if (!is_dir($file_path)) {
            mkdir($file_path, 0777, true);
        }
        $filename = $file_path . date("Ym") . "_index_index11.log";
        $fp = fopen($filename, "a+");
        $time = date('Y-m-d H:i:s');
        $file = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
        $str = "time:{$time} \t{error:" . $msg . "}\t file:{$file}\t\r\n";
        fputs($fp, $str);
        fclose($fp);
        echo 'success';
    }
}