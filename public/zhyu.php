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


$data = array(
    "username" => '超管员',
    "password" => 'cjgly'
);


curl('http://219.148.152.30:8082/auths/web/login.action', $data);

$html = curl('http://219.148.152.30:8082/module/admin/library/mngGrpList.action?tsgResources.typecd=2');
//$html = iconv('gbk', 'utf-8', $html);

$html = str_substr('<form action="/module/admin/library/mngGrpDel.action?tsgResources.typecd=2" method="post" name="rec">', '</form>', $html);

preg_match_all("/<tr.*>(.*)<\/tr>/iUs", $html, $arr_tr);
$result = array();
foreach ($arr_tr[0] as $tr) {
    $row = array();
    preg_match_all("/<td.*>(.*)<\/td>/iUs", $tr, $arr_td);
    foreach ($arr_td[1] as $td) {
        array_push($row, trim(strip_tags($td)));
    }
    array_push($result, $row);
}
unset($result[0]);
$_data=array();
$key=0;
foreach($result as $v){
    if($v[1]!='' && $v[2]!=''){
        $_data[$key]['id']=$v[1];
        $_data[$key]['name']=$v[2];
    }
    $key++;
}
//echo json_encode($_data);
print_r($_data);


function curl($url, $data = array())
{
    $ssl = substr($url, 0, 8) == "https://" ? TRUE : FALSE;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
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
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    }
    $html = curl_exec($ch);
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