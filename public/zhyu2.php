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



//$data = array(
//    "username" => '超管员',
//    "password" => 'cjgly'
//);
//$html= curl_request('http://219.148.152.30:8082/auths/web/login.action', $data,'',true);
//$cookes=$html['cookie'];
//$html = curl_request('http://219.148.152.30:8082/module/admin/library/mngGrpList.action?tsgResources.typecd=2','',$cookes);
//print_r( $html);
//exit;
//$html=curl_request('https://passport.eteams.cn/');
//$lt=str_substr('<input type="hidden" name="lt" value="','" />',$html);
//$execution=str_substr('<input type="hidden" name="execution" value="','" />',$html);
//$data = array(
//    'lt'=>$lt,
//    'execution'=>$execution,
//    '_eventId'=>'submit',
//    "username" => '13910969157',
//    "password" => 'klins123'
//);
//$html=curl_request('https://passport.eteams.cn/login?service=https://www.eteams.cn', $data,'',true);
//$cookes=$html['cookie'];
//echo 'COOK:'.($cookes);
//
//exit;
//
//$url="https://www.eteams.cn/?ticket=TGT-544845-BdtRu1JD5bSzIlVCybo3qoaEYJ9kf9xZUlkIlhfvcUS4iYM55P-www.eteams.cn";
//$cookes='JSESSIONID=2B0B50684EBA1B303844B88C8ED6563;ETEAMS_TGC=TGT-542235-fkNlfRMbHWRReHfptTPVCulD6jK2ONugGfgzjQN5eKtsTE15Gl-www.eteams.cn;JSESSIONID=ADB0EFF976B21144D4BFE4BA016FFEFA;ROUTEID=.r1';
//
//$html=curl($url);
//print_r($html);
//exit;




$content=curl_request('https://passport.eteams.cn/','','',true);
$cookie=$content['cookie'];
$html=$content['content'];
$lt=str_substr('<input type="hidden" name="lt" value="','" />',$html);
$execution=str_substr('<input type="hidden" name="execution" value="','" />',$html);
$data = array(
    'lt'=>$lt,
    'execution'=>$execution,
    '_eventId'=>'submit',
    "username" => '13910969157',
    "password" => 'klins123'
);
$content=curl_request('https://passport.eteams.cn/login?service=https://www.eteams.cn',$data,$cookie,true);
$location=$content['location'];
$content=curl_request($location,'','',true);
$cookie=$content['cookie'];

$content=curl_request('https://www.eteams.cn/users/myfollow/4524818543213705133.json','',$cookie,true);

echo '____________________';
print_r($content);

//参数1：访问的URL，参数2：post数据(不填则为GET)，参数3：提交的$cookies,参数4：是否返回$cookies
function curl_request($url,$post='',$cookie='', $returnHeader=0){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
    //curl_setopt($curl, CURLOPT_REFERER, "http://XXX");
    if($post) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
    }
    if($cookie) {
        echo $cookie;
        curl_setopt($curl, CURLOPT_COOKIE, $cookie);
    }
    curl_setopt($curl, CURLOPT_HEADER, $returnHeader);
    curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    if (substr($url, 0, 8) == "https://") {
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    }
    $data = curl_exec($curl);
    if (curl_errno($curl)) {
        return curl_error($curl);
    }
    curl_close($curl);
    if($returnHeader){
        list($header, $body) = explode("\r\n\r\n", $data, 2);
        preg_match_all("/location:(.*)/i", $header, $matches);
        $info['location']=trim($matches[1][0]);
        // 解析COOKIE
        preg_match_all("/set\-cookie:([^;]*)/i", $header, $matches);
        // 后面用CURL提交的时候可以直接使用
        // curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        $info['cookie']  =implode(';',$matches[1]);
        $info['content'] = $body;
        return $info;
    }else{
        return $data;
    }
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