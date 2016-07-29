<?php
namespace App\Model;

use System\Lib\DB;

class Permission extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    function getlist()
    {
        $result = DB::table('permissions')->orderBy("`order`,id")->all();
        //结果转换为特定格式
        $items = array();
        foreach ($result as $row) {
            $items[$row['id']] = $row;
        }
        return genTree5($items);
    }

    function add($data = array())
    {
        $arr['pid'] = (int)$data['pid'];
        $arr['name'] = $data['name'];
        $arr['desc'] = $data['desc'];
        $arr['url'] = $data['url'];
        $arr['cmvalue'] = str_replace('：', ':', $data['cmvalue']);
        $arr['order'] = (int)$data['order'];
        return DB::table('permissions')->insert($arr);
    }

    function edit($data = array())
    {
        $id = (int)$data['id'];
        $arr['pid'] = (int)$data['pid'];
        $arr['name'] = $data['name'];
        $arr['desc'] = $data['desc'];
        $arr['url'] = $data['url'];
        $arr['cmvalue'] = str_replace('：', ':', $data['cmvalue']);
        $arr['order'] = (int)$data['order'];
        return DB::table('permissions')->where('id=?')->bindValues($id)->limit(1)->update($arr);
    }
}