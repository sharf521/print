<?php
error_reporting(E_ALL & ~E_NOTICE);
//error_reporting(7);
# 基础抬头 其中第三项释放的信息在浏览器debug时可见.
header('Content-language: zh');
header('Content-type: text/html; charset=utf-8');
header('X-Powered-By: JAVA');

# 设置php文件永远不缓存. 可以在后面进行叠加影响的.
header('Pragma: no-cache');
header('Cache-Control: private', false); // required for certain browsers
header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');


session_cache_limiter('private,must-revalidate');
session_start();
date_default_timezone_set('Asia/Shanghai');//时区配置
//print_r($_SESSION);
# 设置执行时间,内部字符编码.
set_time_limit($set_time = 3600);

$html=curl('https://passport.eteams.cn/');
$lt=str_substr('<input type="hidden" name="lt" value="','" />',$html);
$execution=str_substr('<input type="hidden" name="execution" value="','" />',$html);
$data = array(
    'lt'=>$lt,
    'execution'=>$execution,
    '_eventId'=>'submit',
    "username" => '13910969157',
    "password" => 'klins123'
);
curl('https://passport.eteams.cn/login?service=https://www.eteams.cn', $data);
echo curl('https://www.eteams.cn/users/myfollow/4524818543213705133.json');



function curl($url, $data = array())
{
    $ssl = substr($url, 0, 8) == "https://" ? TRUE : FALSE;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $headers = array(
        'User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.107 Safari/537.36',
        'Referer:https://eteams.cn/',
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    if ($data) {
        $post_fields = '';
        foreach ($data as $key => $val) {
            $post_fields .= sprintf("%s=%s&", $key, $val);
        }
        curl_setopt($ch, CURLOPT_POST, 1);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }
    $cookie_file = dirname(__FILE__) . '/bb.txt';
    //$cookie_file = tempnam('./temp','cookie');
    //if($login){
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    //}else{
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    //}
    if ($ssl) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    }
    $html = curl_exec($ch);//print_r(curl_getinfo($ch));
    curl_close($ch);
	
	
    return $html;
}



function preg_substr($start, $end, $str) // 正则截取函数
{
    $temp = preg_split($start, $str);
    $content = preg_split($end, $temp[1]);
    return $content[0];
}


function str_substr($start, $end, $str) // 字符串截取函数
{
    $temp = explode($start, $str, 2);
    $content = explode($end, $temp[1], 2);
    return $content[0];
}