<?php
namespace App\Controller\Data;

use System\Lib\DB;

class AlgorithmController extends IndexController
{
    public function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        echo 111;
    }




    function login1()
    {
        if($_POST){
            $params=array(
                'userid'=>$_POST['userid'],
                'password'=>$_POST['password'],
                'func'=>$_POST['func'],
                'randcode'=>$_POST['randcode']
            );
            $this->curl('http://www.zhuzhan.cn/webservices/?act=login',$params);
            echo $this->curl('http://www.zhuzhan.cn/webservices/?act=member');
        }
        ?>
        <form method="post">
            用户名：<input type="text" name="userid"><br>
            密码：<input type="text" name="password"><br>
            <input type="text" name="randcode"><br>
            <img src="<?=$this->base_url('algorithm/code')?>" alt="点击刷新" onclick="this.src='<?=$this->base_url('algorithm/code')?>?t=' + Math.random();" align="absmiddle" style="cursor:pointer">
            <input type="submit" value="ok">

            <input type="hidden" name="func" value="login">
        </form>
<?
    }


}
