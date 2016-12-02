<?php

include_once "wxBizMsgCrypt.php";

$AppSecret='a0845f7bca562a55aa47a07f1b043dcd';

// 第三方发送消息给公众平台
$encodingAesKey = "12345678jddsdjaskdfjqweir234934jkzxc8asdfdf";
$token = "jaskdfjqweir234934jkzxc8asdfdf";
$timeStamp = time();
$nonce = "xxxxxx";
$appId = "wx0453db85b190df07";
$text = "<xml><ToUserName><![CDATA[oia2Tj我是中文jewbmiOUlr6X-1crbLOvLw]]></ToUserName><FromUserName><![CDATA[gh_7f083739789a]]></FromUserName><CreateTime>1407743423</CreateTime><MsgType><![CDATA[video]]></MsgType><Video><MediaId><![CDATA[eYJ1MbwPRJtOvIEabaxHs7TX2D-HV71s79GUxqdUkjm6Gs2Ed1KF3ulAOA9H1xG0]]></MediaId><Title><![CDATA[testCallBackReplyVideo]]></Title><Description><![CDATA[testCallBackReplyVideo]]></Description></Video></xml>";


$pc = new WXBizMsgCrypt($token, $encodingAesKey, $appId);
$encryptMsg = '';
$errCode = $pc->encryptMsg($text, $timeStamp, $nonce, $encryptMsg);
if ($errCode == 0) {
	print("加密后: " . $encryptMsg . "\n");
} else {
	print($errCode . "\n");
}

$xml_tree = new DOMDocument();
$xml_tree->loadXML($encryptMsg);
$array_e = $xml_tree->getElementsByTagName('Encrypt');
$array_s = $xml_tree->getElementsByTagName('MsgSignature');
$encrypt = $array_e->item(0)->nodeValue;
$msg_sign = $array_s->item(0)->nodeValue;

$format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
$from_xml = sprintf($format, $encrypt);

// 第三方收到公众号平台发送的消息
$msg = '';
$errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $from_xml, $msg);
if ($errCode == 0) {
	print("解密后: " . $msg . "\n");
} else {
	print($errCode . "\n");
}

//{"signature":"d7bbf875fcccc15fe6383d72e72796bf737707c6","timestamp":"1480665274","nonce":"1996835672","encrypt_type":"aes","msg_signature":"b49d922578bb2a7672b5312b3ab18946be9230de"}}
$from_xml = "<xml>
    <AppId><![CDATA[wx0453db85b190df07]]></AppId>
    <Encrypt><![CDATA[HfdlKfX+AQcZ1YRGKZ8r+Gp42Ces2x8SaiQGQzKGSMwP8kJqujDPzQiod83XNpF2KUXN5byQTjlpj0XXdeUb33zDcJnQNIuumC6yIRmfAT7GWyOySY2ZcKOwWoBKtt/yEBaodJrg7UU9vIlLgT3ApxotG0Ve47Fi/+31YtuIMdbHhidmCvmMhLRHiKHHpq0KGcx+6PP5Q9mgIPysWxvp5MfQqjh2YSNMPfwGRf4XE0MAXAi6YoqzpOQpkjVTRSDw2Glg5S8cA+biCTE9C9Vmr7Geet3MrLZfZJd3/aExiGQfggL/lxaqm+gWvh/fRAcPwQDfDMiOO9hP5fTsMMsBCLLqfd+aX/yUiNu/eW4UwTtUQqwhIAi3MWcRRzz/dVpFt2esPJ1x55VYk7uNQVbnwGTbI4heA9gnWaY3oaXsIiqznhoQgpIFuEBnXmC8CQwKSOZrkjiYsEcl6VyWV7EvSA==]]></Encrypt>
</xml>";

$format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
$from_xml = sprintf($format, $from_xml);

echo $from_xml;
echo '<hr>';
$msg = '';
$errCode = $pc->decryptMsg('b49d922578bb2a7672b5312b3ab18946be9230de', '1480665274', '1996835672', $from_xml, $msg);
if ($errCode == 0) {
	print("解密后: " . $msg . "\n");
} else {
	print($errCode . "\n");
}