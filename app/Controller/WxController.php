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
        $ip=$this->ip();
        $file = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
        $str = "time:{$time}\t ip:{$ip}}\t{error:" . $msg . "}\t file:{$file}\t\r\n";
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
        $ip=$this->ip();
        $file = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER["REQUEST_URI"];
        $str = "time:{$time}\t ip:{$ip}}\t{error:" . $msg . "}\t file:{$file}\t\r\n";
        fputs($fp, $str);
        fclose($fp);
    }
}