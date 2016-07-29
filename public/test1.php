<?php
error_reporting(E_ALL & ~E_NOTICE);
//error_reporting(7);
# 基础抬头 其中第三项释放的信息在浏览器debug时可见.
header('Content-language: zh');
header('Content-type: text/html; charset=utf-8');
header('X-Powered-By: JAVA');

# 设置php文件永远不缓存. 可以在后面进行叠加影响的.
header('Pragma: no-cache');
header('Cache-Control: private',false); // required for certain browsers
header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
header('Expires: '.gmdate('D, d M Y H:i:s') . ' GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');


session_cache_limiter('private,must-revalidate');
session_start();
date_default_timezone_set('Asia/Shanghai');//时区配置
//print_r($_SESSION);
# 设置执行时间,内部字符编码.
set_time_limit($set_time = 3600);

if($_GET['func']=='code'){
    $img=curl('http://www.zhuzhan.cn/webservices/include/code.php');
    echo $img;
    exit;
}


if($_POST){
    curl('http://www.zhuzhan.cn/webservices/?act=login',$_POST);
    echo curl('http://www.zhuzhan.cn/webservices/?act=member');
}


?>
<form method="post">
    用户名：<input type="text" name="userid" value="admin"><br>
    密码：<input type="text" name="password" value="123456"><br>
    <input type="text" name="randcode"><br>
    <img src="?func=code" alt="点击刷新" onclick="this.src='?func=code&t=' + Math.random();" align="absmiddle" style="cursor:pointer">
    <input type="submit" value="ok">
    <input type="hidden" name="func" value="login"/>

</form>

<?php


function curl($url,$data=array())
{
    $ssl = substr($url, 0, 8) == "https://" ? TRUE : FALSE;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    if($data){
        $post_fields='';
        foreach ($data as $key => $val) {
            $post_fields .= sprintf("%s=%s&", $key, $val);
        }
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    }
    $cookie_file=dirname(__FILE__).'/bb.txt';
    //$cookie_file = tempnam('./temp','cookie');
    //if($login){
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    //}else{
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    //}
    if ($ssl) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    }
    $html=curl_exec($ch);
    curl_close($ch);
    return $html;
}