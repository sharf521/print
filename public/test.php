<?php



$url='http://webservices.vcivc.cn:8000/index.php/api';
$site_id=1;
$site_key="B86FB8864DF1F6F5386F5E70362139ED";

/**
 * 获取奖励列表
 * id:起始id
 * size：获取条数
 * */
//$params=array(
//    'time'=>time(),
//    'site_id'=>$site_id,
//    'id'=>10,
//    'size'=>10
//);
//$params['sign']=getSignature($params,$site_key);
//$html=curl_url($url.'/algorithm/rebate_list',$params);
//echo $html;
//echo '<hr>';


/**
 * 添加奖励
 * user_id:用户id （int）
 * money：奖励积分
 * typeid：1消费
 * */
 $params=array(
     'time'=>time(),
     'site_id'=>$site_id,
     'user_id'=>2,
     'money'=>100,
     'typeid'=>1
 );
 $params['sign']=getSignature($params,$site_key);
 print_r($params);
 $html=curl_url($url.'/algorithm/rebate_add',$params);
echo $html;



function getSignature($data, $MD5key)
{
    // $sign_params = array(
    //     'site_id' => $data['site_id'],
    //     'time' => $data['time'],
    //     'user_id' => $data['user_id'],
    //     "money" => sprintf("%.5f", $data['money']),
    // );
    $sign_params = $data;
    $sign_str = "";
    ksort($sign_params);
    foreach ($sign_params as $key => $val) {
        $sign_str .= sprintf("%s=%s&", $key, $val);
    }
    // echo  $sign_str;print '<br/><br/><br/>';
    return strtoupper(md5($sign_str . strtoupper(md5($MD5key))));
}
//curl请求函数
function curl_url($url, $data = array())
{
    $ssl = substr($url, 0, 8) == "https://" ? TRUE : FALSE;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    if ($data) {
        if (is_array($data)) {
            curl_setopt($ch, CURLOPT_POST, 1);
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data))
            );
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    if ($ssl) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    }
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}