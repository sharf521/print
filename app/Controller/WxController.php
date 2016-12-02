<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/1
 * Time: 18:07
 */

namespace App\Controller;


class WxController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $msg=json_encode($_REQUEST);
        $file_path = ROOT . "/public/data/wx/";
        if (!is_dir($file_path)) {
            mkdir($file_path, 0777, true);
        }
        $filename = $file_path . date("Ym") . "_index.log";
        $fp = fopen($filename, "a+");
        $time = date('Y-m-d H:i:s');
        $file = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
        $str = "time:{$time} \t{error:" . $msg . "}\t file:{$file}\t\r\n";
        fputs($fp, $str);
        fclose($fp);
    }

    public function index()
    {
        $AppID='wx0453db85b190df07';
        $AppSecret='a0845f7bca562a55aa47a07f1b043dcd';
        $redirect_uri='http://print.yuantuwang.com/index/error';
        $url="https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid={$AppID}&pre_auth_code={$AppSecret}&redirect_uri={$redirect_uri}";
        echo "<a href='{$url}'>授权</a>";
    }

    public function aa()
    {
        //http://print.yuantuwang.com/?signature=e7dff96e86c6ffcc9f7c04cec84a0e4c3fc30b2f&timestamp=1480653875&nonce=1472022172&encrypt_type=aes&msg_signature=c716ace25cf138c8989d8f3b0c01e5bc8a516bab
    }

    public function error()
    {
        $msg=json_encode($_REQUEST);
        $file_path = ROOT . "/public/data/wx/";
        if (!is_dir($file_path)) {
            mkdir($file_path, 0777, true);
        }
        $filename = $file_path . date("Ym") . ".log";
        $fp = fopen($filename, "a+");
        $time = date('Y-m-d H:i:s');
        $file = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
        $str = "time:{$time} \t{error:" . $msg . "}\t file:{$file}\t\r\n";
        fputs($fp, $str);
        fclose($fp);
    }
}