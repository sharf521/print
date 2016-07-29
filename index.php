<?php
$t1 = microtime(true);
//error_reporting(E_ALL & ~E_NOTICE);
error_reporting(7);
header('Content-language: zh');
header('Content-type: text/html; charset=utf-8');
header('X-Powered-By: JAVA');
header('Pragma: no-cache');
header('Cache-Control: private',false); // required for certain browsers
header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
header('Expires: '.gmdate('D, d M Y H:i:s') . ' GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
session_cache_limiter('private,must-revalidate');
session_start();
date_default_timezone_set('Asia/Shanghai');//时区配置
set_time_limit($set_time = 3600);

define('ROOT', __DIR__);
$_G = array();
require ROOT . '/vendor/autoload.php';


$options = [
    'debug' => true,
    'app_id' => 'wx2dc7b9baa7afd65b',
    'secret' => '56d196f91373e6c3acadba655f2ba5cd',
    'token' => 'print',
    // 'aes_key' => null, // 可选
    'log' => [
        'level' => 'debug',
        'file' => ROOT.'/public/data/easywechat.log', // XXX: 绝对路径！！！！
    ]
    //...
];

// 使用配置来初始化一个项目。
$app=new \EasyWeChat\Foundation\Application($options);
$response = $app->server->serve();
// 将响应输出
$response->send(); // Laravel 里请使用：return $response;

exit;
require ROOT . '/system/Autoloader.php';
use System\Lib\DB;
DB::instance('db1');
require ROOT . '/system/function.php';
require ROOT . '/system/helper.php';
$pager = app('\System\Lib\Page');
$request=app('\System\Lib\Request');
$_G['system'] = DB::table('system')->orderBy("`showorder`,id")->lists('value', 'code');
//$houtai=app('\App\Model\System')->getCode('houtai');
$_G['class'] = ($request->get(0) != '') ? $request->get(0) : 'index';
$_G['func'] = ($request->get(1) != '') ? $request->get(1) : 'index';
$_path='';
if ($_G['class'] == 'api') {
    $_path='Api';
}elseif ($_G['class'] == 'auth'){
    $_path='Auth';
}elseif ($_G['class'] == 'member') {
    $_path='Member';
} elseif ($_G['class'] == $_G['system']['houtai']) {
    $_path='Admin';
}
if($_path==''){
    if (file_exists(ROOT . '/app/Controller/' . ucfirst($_G['class']) . 'Controller.php')) {
        $_classpath = "\\App\\Controller\\" . ucfirst($_G['class']) . "Controller";
        $method = $_G['func'];
    } else {
        $_classpath='\App\Controller\IndexController';
        $method = $_G['class'];
    }
}else{
    $_G['class'] = ($request->get(1) != '') ? $request->get(1) : 'index';
    $_G['func'] = ($request->get(2) != '') ? $request->get(2) : 'index';
    if (file_exists(ROOT . '/app/Controller/'.$_path.'/' . ucfirst($_G['class']) . 'Controller.php')) {
        $_classpath = "\\App\\Controller\\" .$_path.'\\'. ucfirst($_G['class']) . "Controller";
        $method = $_G['func'];
    } else {
        $_classpath = "\\App\\Controller\\" .$_path."\\IndexController";

        $method = $_G['class'];
    }
}
controller($_classpath,$method);
$t2 = microtime(true);
//echo '<hr>耗时'.round($t2-$t1,3).'秒';