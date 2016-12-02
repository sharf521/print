<?php
namespace App\Controller;

class IndexController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $msg=json_encode($_REQUEST);
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
    }
}