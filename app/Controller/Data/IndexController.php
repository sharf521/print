<?php
namespace App\Controller\Data;

use System\Lib\Controller as BaseController;
use System\Lib\DB;

class IndexController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->base_url = '/index.php/data/';
        $this->template = 'data';
        $this->control = ($this->input->get(1) != '') ? $this->input->get(1) : 'index';
        $this->func = ($this->input->get(2) != '') ? $this->input->get(2) : 'index';
    }

    public function index()
    {
        echo 'index';
    }

    public function error()
    {
        echo 'not find page';
    }

    public function member()
    {
        $platform_id = $this->input->get(3);
        $html = $this->curl('http://www.zhuzhan.cn/webservices/?act=member', $platform_id);
        $html = iconv('gbk', 'utf-8', $html);
        preg_match_all("/<tr.*>(.*)<\/tr>/iUs", $html, $arr_tr);
        $result = array();
        foreach ($arr_tr[0] as $tr) {
            $row = array();
            preg_match_all("/<td.*>(.*)<\/td>/iUs", $tr, $arr_td);
            foreach ($arr_td[1] as $td) {
                array_push($row, strip_tags($td));
            }
            array_push($result, $row);
        }
        unset($result[0]);
        print_r($result);
    }

    public function login()
    {
        $platform_id=$this->input->get(3);
        $platform=DB::table('platform')->where('id=?')->bindValues($platform_id)->row();
        $_params = explode("\r\n", trim($platform['content']));
        foreach ($_params as $fun) {
            if ($fun != '') {
                $_t = explode(':', $fun);
                $params[$_t[0]]=$_t[1];
            }
        }
        if($_POST){
            $this->curl($platform['login_url'],$platform_id,$_POST);
        }else{
            if(empty($platform['randcode_url'])) {
                $this->curl($platform['login_url'],$platform_id,$_POST);
            }else{
                echo '<form method="post">';
                foreach ($params as $k=>$v) {
                    echo "{$k}：<input type='text' name='{$k}' value='{$v}'/><br>";
                }
                if($platform['randcode_url']){
                    ?>
                    <img src="<?=$this->base_url("index/code/{$platform_id}/")?>" alt="点击刷新" onclick="this.src='<?=$this->base_url("index/code/{$platform_id}/")?>?t=' + Math.random();" align="absmiddle" style="cursor:pointer">
                    <?
                }
                echo '<input type="submit" value="ok"></form>';
            }
        }
    }

    public function code()
    {
        $platform_id=$this->input->get(3);
        $randcode_url=DB::table('platform')->where('id=?')->bindValues($platform_id)->value('randcode_url');
        $img = $this->curl($randcode_url,$platform_id);
        echo $img;
    }

    protected function curl($url,$platform_id, $data = array())
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
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        }
        $cookie_file = dirname(__FILE__) . '/../../../public/data/cookie/platform_'.$platform_id.'.txt';
        //$cookie_file = dirname(__FILE__) . '/platform_2.txt';
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


    protected function preg_substr($start, $end, $str) // 正则截取函数
    {
        $temp = preg_split($start, $str);
        $content = preg_split($end, $temp[1]);
        return $content[0];
    }

    protected function str_substr($start, $end, $str) // 字符串截取函数
    {
        $temp = explode($start, $str, 2);
        $content = explode($end, $temp[1], 2);
        return $content[0];
    }
}


