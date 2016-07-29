<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/27
 * Time: 11:05
 */

namespace App\Controller;

use App\Model\AccountLog;
use System\Lib\DB;
use System\Lib\Request;

class PayController extends Controller
{
    private $pid = 32;
    private $md5Key = 'bf2049cfe3b249f7148f4dd65fdcd06b';

    public function __construct()
    {

    }

    public function recharge(Request $request)
    {
        $user_id = (int)$request->post('user_id');
        $host = $_SERVER['HTTP_HOST'];
        $money = (float)$request->post('money');
        if ($user_id == 0) {
            echo '参数错误';
            exit();
        }
        $MerPriv = $user_id . '#' . $host;//商户私有数据项
        $OrdAmt = sprintf("%.2f", $money);
        $para = array(
            "OrdAmt" => $OrdAmt,
            "Pid" => $this->pid,
            "MerPriv" => $MerPriv,
            "UsrSn" => time() . rand(10000, 99999)
        );
        $para['Sign'] = $this->md5_sign($para, $this->md5Key);
        $para['GateId'] = $request->post('GateId');//网关号
        $para['returl'] = 'http://' . $host . '/pay/result.php';
        $para['bgreturl'] = 'http://' . $host . '/pay/result.php';
        $fee = round_money($para['OrdAmt'] * 0.003,2);
        $data = array(
            'trade_no' => $para['UsrSn'],
            'user_id' => $user_id,
            'status' => 0,
            'money' => $para['OrdAmt'],
            'fee' => $fee,
            'payment' => $para['GateId'],
            'type' => 1,
            'remark' => '',
            'created_at' => time(),
            'addip' => ip()
        );
        DB::table('account_recharge')->insert($data);
        $sHtml = "<form id='fupaysubmit' name='fupaysubmit' action='http://pay.fuyuandai.com/gar/RecvMerchant.php' method='post' style='display:none'>";
        while (list ($key, $val) = each($para)) {
            $sHtml .= "<input type='hidden' name='" . $key . "' value='" . $val . "'/>";
        }
        //submit按钮控件请不要含有name属性
        $sHtml = $sHtml . "<input type='submit'></form>";
        $sHtml = $sHtml . "<script>document.forms['fupaysubmit'].submit();</script>";
        echo $sHtml;
    }

    public function result(Request $request, AccountLog $accountLog)
    {
        $MerPriv = $request->post('MerPriv');
        $OrdAmt = (float)$request->post('OrdAmt');
        $UsrSn = $request->post('UsrSn');
        $arr = array(
            "OrdAmt" => sprintf("%.2f", $OrdAmt),
            "Pid" => (int)$request->post('Pid'),
            "MerPriv" => $MerPriv,
            'TrxId' => $request->post('TrxId'),
            "UsrSn" => $UsrSn
        );
        if ($request->post('Sign') != $this->md5_sign($arr, $this->md5Key)) {
            echo 'error,签名错误';
            exit();
        }
        //判断缓存中是否有 创建交易cache缓存文件
        $path = ROOT . "/public/data/pay_cache/" . date("Y-m") . "/";
        if (!file_exists($path)) {
            mkdir($path, 0777);
        }
        $file = $path . $UsrSn;
        $fp = fopen($file, 'w+');
        chmod($file, 0777);
        if (flock($fp, LOCK_EX | LOCK_NB)) //设定模式独占锁定和不堵塞锁定
        {
            ////////////start处理////////////////////////////
            $MerPriv = explode('#', $MerPriv);
            $user_id = (int)$MerPriv[0];
            $host = $MerPriv[1];
            $row = DB::table('account_recharge')->where("user_id={$user_id} and trade_no={$UsrSn}")->row();
            if ($row) {
                if ($row['status'] == 0) {
                    DB::table('account_recharge')->where("user_id={$user_id} and trade_no={$UsrSn}")->update(array('status' => 1));
                    $log = array();
                    $log['user_id'] = $row['user_id'];
                    $log['type'] = 1;
                    $log['funds_available'] = $row['money'] - $row['fee'];
                    $log['remark'] = "在线充值：" . $row['id'];
                    $accountLog->addLog($log);
                }

            }
            echo "<!--";
            echo "RECV_ORD_ID_" . $UsrSn;
            echo "-->";
            echo "<script>window.location='../index.php/member/account/rechargeLog';</script>";
            exit();
            ///////////结束处理/////////////////////////////
            flock($fp, LOCK_UN);
        }
        fclose($fp);
    }

    private function md5_sign($para, $key = '')
    {
        $prestr = $this->getsignstr($para);
        $sign = md5($prestr . $key);
        return $sign;
    }

    //参数排序
    private function argSort($para)
    {
        ksort($para);
        reset($para);
        return $para;
    }

    private function getsignstr($para)
    {
        $para = $this->argSort($para);
        $arg = "";
        while (list ($key, $val) = each($para)) {
            //$arg.=$key."=".$val."&";
            $arg .= $key . "=" . urlencode($val) . "&";
        }
        //去掉最后一个&字符
        $arg = substr($arg, 0, count($arg) - 2);
        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }
        return $arg;
    }


}