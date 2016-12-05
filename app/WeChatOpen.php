<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/2
 * Time: 11:58
 */

namespace App;

use EasyWeChat\Foundation\Application;
class WeChatOpen
{
    public $app;
    public $options;
    public function __construct()
    {
        $this->options = [
            'debug' => true,
            'app_id' => 'wx0453db85b190df07',
            'secret' => 'a0845f7bca562a55aa47a07f1b043dcd',
            'token' => 'jaskdfjqweir234934jkzxc8asdfdf',
            'aes_key' => '12345678jddsdjaskdfjqweir234934jkzxc8asdfdf', // 可选
            'log' => [
                'level' => 'debug',
                'file' => ROOT.'/public/data/easywechatopen.log', // XXX: 绝对路径！！！！
            ],
            'oauth' => [
                'scopes'   => ['snsapi_userinfo'],
                'callback' => 'http://'.$_SERVER['HTTP_HOST'].'/index.php/wxapi/oauth_callback',
            ],
            'guzzle' => [
                'timeout' => 4.0, // 超时时间（秒）
                // 'verify' => false, // 关掉 SSL 认证（强烈不建议！！！）
            ],
            // payment
            'payment' => [
                'merchant_id'        => '1373665102',
                'key'                => 'kfjakdfjakldsfjkasdq1234123411as',
                'cert_path'          => '/etc/ca/weixin/apiclient_cert.pem', // XXX: 绝对路径！！！！
                'key_path'           => '/etc/ca/weixin/apiclient_cert.p12',      // XXX: 绝对路径！！！！
                'notify_url'         => '',       // 你也可以在下单时单独设置来想覆盖它
                // 'device_info'     => '013467007045764',
                // 'sub_app_id'      => '',
                // 'sub_merchant_id' => '',
                // ...
            ],
        ];
        $this->app=new Application($this->options);
    }

    public function curl_url($url, $data = array())
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

    public function getPayParams($prepay_id)
    {
        $pay=array();
        $pay['appId']=$this->options['app_id'];
        $time=time();
        $pay['timeStamp']="$time";
        $pay['nonceStr']=$this->getNonceStr();
        $pay['package']="prepay_id={$prepay_id}";
        $pay['signType']='MD5';
        $sign=$this->MakeSign($pay);
        $arr=array(
            'timestamp'=>$pay['timeStamp'],
            'nonceStr'=>$pay['nonceStr'],
            'package'=>$pay['package'],
            'signType'=>$pay['signType'],
            'paySign'=>$sign
        );
        return $arr;
    }

    /**
     *
     * 产生随机字符串，不长于32位
     * @param int $length
     * @return 产生的随机字符串
     */
    private function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }
    /**
     * 格式化参数格式化成url参数
     */
    private function ToUrlParams($values)
    {
        $buff = "";
        foreach ($values as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 生成签名
     */
    private function MakeSign($values)
    {
        //签名步骤一：按字典序排序参数
        ksort($values);
        $string = $this->ToUrlParams($values);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=".$this->options['payment']['key'];
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    /**
     * 生成二维码
     */
    public function qrcode($txt,$forever=false)
    {
        $qrcode = $this->app->qrcode;
        if($forever){
            $result=$qrcode->forever($txt);//永久二维码
        }else{
            $result = $qrcode->temporary($txt, 6 * 24 * 3600);
        }
        $ticket = $result->ticket;// 或者 $result['ticket']
        $url = $qrcode->url($ticket);
        return $url;
    }

    /* 短地址*/
    public function shorten($uri)
    {
        $url = $this->app->url;
        $shortUrl=$url->shorten($uri);
        return $shortUrl->short_url;
    }
}