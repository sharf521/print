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
    //$img=curl('http://center.test.cn:8000/plugin/code/');
   // echo $img;
    exit;
}


if($_POST){
    $params=array(
        'loginname'=>$_POST['loginname'],
        'loginpwd'=>$_POST['loginpwd'],
        'valicode'=>$_POST['valicode']
    );
    $url='https://passport.jd.com/uc/loginService?uuid='+$_POST['uuid']+'&ltype=logout&r=0.32593581785856346&version=2015';
    echo curl($url,$_POST);
    echo curl('http://order.jd.com/center/list.action');
}

$login_url="https://passport.jd.com/new/login.aspx";
$loginPostUrl='https://passport.jd.com/uc/loginService?uuid=8d00d7ac-d711-4b14-b686-cb62fe79a53a&ltype=logout&r=0.32593581785856346&version=2015';

$login_html=curl($login_url);
$login_form=str_substr('<form id="formlogin" method="post" onsubmit="return false;">', "</form>", $login_html);
$login_form=iconv('gbk','utf-8',$login_form);
?>
    <form id="txt_data" style="display: none"><?= $login_form ?></form>
    <script src="/plugin/js/jquery.js"></script>
    <script type="text/javascript">
//        $(document).ready(function () {
//            var uuid = $('#txt_data #uuid').val();
//           // document.login_form.uuid.value = uuid;
//            //document.login_form.machineNet.value = $('#txt_data #machineNet').val();
//            $("#submit").click(function () {
//                document.login_form.action='https://passport.jd.com/uc/loginService?uuid='+uuid+'&ltype=logout&r=0.32593581785856346&version=2015';
//                document.login_form.submit();
//            });
//        });
    </script>
    <form method="post" name="login_form">

        <?= $login_form ?>
        <input type="submit" value="ok">
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

/*
if($_GET['id']<=8&&$_GET['id']){
     $id=$_GET['id'];
    $conn=file_get_contents("http://www.93moli.com/news_list_4_$id.html");//获取页面内容

  $pattern="/<li><a title=\"(.*)\" target=\"_blank\" href=\"(.*)\">/iUs";//正则

  preg_match_all($pattern, $conn, $arr);//匹配内容到arr数组

  //print_r($arr);die;

  foreach ($arr[1] as $key => $value) {//二维数组[2]对应id和[1]刚好一样,利用起key
    $url="http://www.93moli.com/".$arr[2][$key];
    $sql="insert into list(title,url) value ('$value', '$url')";
    mysql_query($sql);

    //echo "<a href='content.php?url=http://www.93moli.com/$url'>$value</a>"."<br/>";
  }
   $id++;




// ---------------- 使用实例 ----------------
$str = iconv("UTF-8", "GB2312", file_get_contents("http://www.mycodes.net"));
echo ('标题: ' . str_substr("<title>", "</title>", $str)); // 通过字符串提取标题
echo ('作者: ' . preg_substr("/userid=\d+\">/", "/<\//", $str)); // 通过正则提取作者
echo ('内容: ' . str_substr('<div class="content">', '</div>', $str)); //内容当然不可以少
*/


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