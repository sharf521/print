<?php
namespace App\Controller\Auth;

use System\Lib\Controller as BaseController;
use System\Lib\DB;

class AuthController extends BaseController
{
    protected $appid;
    protected $appsecret;
    public function __construct()
    {
        global $_G;
        parent::__construct();
        $this->control	=$_G['class'];
        $this->func		=$_G['func'];
        if (strpos(strtolower($_SERVER['HTTP_HOST']), 'wap.') === false) {
            $this->is_wap = false;
            $this->template = 'auth';
        } else {
            $this->is_wap = true;
            $this->template = 'auth_wap';
        }
    }
    public function error()
    {
        echo 'not find page';
    }


    //签名
    protected function checkSign($data)
    {
        $row=DB::table('app')->where('appid=?')->bindValues($data['appid'])->row();
        $this->appsecret=$row['appsecret'];
        $this->appid=$row['id'];
        if(empty($this->appsecret)){
            echo 'check sign with appid error!';
            exit;
        }
        if($data['sign'] !=$this->getSign($data)){
            echo 'check sign error';
            exit;
        }
    }
    protected function getSign($data)
    {
        if(isset($data['sign'])){
            unset($data['sign']);
        }
        ksort($data);
        $jsonStr = json_encode($data);
        $str = strtoupper(md5($jsonStr.$this->appsecret));
        return $str;
    }
}


