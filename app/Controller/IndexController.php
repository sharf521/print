<?php
namespace App\Controller;

use App\WeChatOpen;

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

    public function index()
    {
        $xml = $GLOBALS["HTTP_RAW_POST_DATA"];
        if(empty($xml))
        {
            $xml = file_get_contents("php://input");
        }
        $xml="<xml>
    <AppId><![CDATA[wx0453db85b190df07]]></AppId>
    <Encrypt><![CDATA[9solOVH+6l2DrFwu6fBVz2kfB4OaA49JKdhemsiOgU2XGEv6dcdBJrmgNsCXjdncQ3FtKMzNi42/p2d3+VKN3+oG69GOBFdcJOj3f+cQbrPmfwHwGxmK9C4i9YeqfgdqDf95Ar8gwBqiK1q0fz728Wi9FgoEoziTqvBV9oTwqiWX3nP/OIsIwRmm5Zky6ypyX4UBkbt+L5YJN+cpAPxt95ezDv8ycKuIeaxWjABpBHn5DZ6DybPDC+XYzTcpzxmXyI7l/fslp+b03TGqREr+o6w9d3bnVBNKLnKsSiMJ7iFIfZe/4aWG2u4JsftnSoS6mOw7wwBG+/DIEAZNJcKfkBygQU7fFvGdcKLXcFjYnYHuCgbpvgQ7o0N9wJTsxEbqkWh4Q5nJAsPCNuU3OdG4hl1Z3M+WvF+/nyGefhcLQnsarjjosU1CEZKhpmmCm/pr+gFY3IpApIqiLcIapYGfxA==]]></Encrypt>
</xml>";

        $data=array();
        if(!empty($xml))
        {
            $xml = new \SimpleXMLElement($xml);
                foreach ($xml as $key => $value) {
                    $data[$key] = strval($value);
                }
        }
        print_r($data);
        exit;

        $msg=$xml;
        $msg.='\r\n'.json_encode($_REQUEST);
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