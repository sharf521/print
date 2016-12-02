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
        $xml = $GLOBALS["HTTP_RAW_POST_DATA"];
        if(empty($xml))
        {
            $xml = file_get_contents("php://input");
        }
        if(!empty($xml))
        {
            $xml = new SimpleXMLElement($xml);
            if(is_array($xml)){
                foreach ($xml as $key => $value) {
                    $data[$key] = strval($value);
                }
            }
        }

        $msg=json_encode($data);
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
    }
}