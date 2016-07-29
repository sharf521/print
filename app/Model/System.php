<?php
namespace App\Model;

use System\Lib\DB;

class System extends Model
{
    private $result=null;
    public function __construct()
    {
        parent::__construct();
        $this->result=DB::table('system')->orderBy("`showorder`,id")->lists('value','code');
    }

    function lists()
    {
//        $_system = $this->getlist();
//        foreach ($_system as $key => $value){
//            $system[$value['code']] = $value['value'];
//        }
//        return $system;
    }

    public function getCode($code)
    {
        return $this->result[$code];
    }

    function add($data=array())
    {
        $arr['code'] = $data['code'];
        $arr['name'] = $data['name'];
        $arr['value'] = $data['value'];
        $arr['showorder'] = (int)$data['showorder'];
        $arr['style'] = (int)$data['style'];
        return DB::table('system')->insert($arr);
    }
    function edit($data=array())
    {
        $id=(int)$data['id'];
        $arr['code'] = $data['code'];
        $arr['name'] = $data['name'];
        $arr['value'] = $data['value'];
        $arr['showorder'] = (int)$data['showorder'];
        $arr['style'] = (int)$data['style'];
        return DB::table('system')->where('id=?')->bindValues($id)->limit(1)->update($arr);
    }
}