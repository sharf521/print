<?php
namespace System\Lib;

class Input
{
	public $param=array();
    function __construct()
    {
        //index.php/class/func
        $_path=$_SERVER['PATH_INFO'];
        $arr=explode("/",trim($_path,'/'));
        $pre='';
        //index.php/class/func/a/1/b/2  --> $_GET[a]=1 $_GET[b]=2
        foreach($arr as $i=>$v)
        {
            $v=strip_tags(trim($v));
            $par[$i]=$v;
            //index.php/class/func/a/1/b/2
            //a和b位置 不能为数字
            if($i>1 && $i%2==0 && !is_numeric($v))
            {
                $v=addslashes(strip_tags(trim($arr[$i+1])));
                $par[$arr[$i]]=$v;
                $_GET[$arr[$i]] =$v;
            }
        }
        $this->param=$par;
        foreach(array('_GET','_POST','_COOKIE','_REQUEST') as $key)
        {
            if (isset($$key)){
                foreach($$key as $_key => $_value){
                    $_value=strip_tags($_value);
                    $$key[$_key] = $this->safe_str($_value);
                }
            }
        }
    }
	public function get($key,$type='')
	{
        $val=isset($this->param[$key])?$this->param[$key]:'';
        if($type!==''){
            if($type=='int'){
                $val=(int)$val;
            }
            elseif($type=='float'){
                $val=(float)$val;
            }
            elseif($type===true){
                $val=strip_tags($val);
            }
        }
		return $val;
	}
    public function post($key,$type=''){
        $val='';
        if(isset($_POST[$key])){
            $val=$_POST[$key];
        }
        if($type!==''){
            if($type=='int'){
                $val=(int)$val;
            }
            elseif($type=='float'){
                $val=(float)$val;
            }
            elseif($type===true){
                $val=strip_tags($val);
            }
        }
        return $val;
    }
	private function safe_str($str)
	{
		if(!get_magic_quotes_gpc())	{
			if( is_array($str) ) {
				foreach($str as $key => $value) {
					$str[$key] = safe_str($value);
				}
			}else{
				$str = addslashes($str);
			}
		}
		return $str;
	}

}