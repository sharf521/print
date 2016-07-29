<?php
namespace App\Model;

use System\Lib\DB;

class Category extends Model
{
    protected $table = 'category';
    function __construct()
    {
        parent::__construct();
    }

    function getlist($data)
    {
        $where = " 1=1";
        if ($data['pid'] !== '') {
            $where .= " and pid='{$data['pid']}'";
        }
        return DB::table('category')->where($where)->orderBy("`showorder`,id")->all();
    }

    function getListTree($data=array())
    {
        $where = " 1=1";
        if (isset($data['pid'])) {
            $where .= " and pid='{$data['pid']}'";
        }
        if (isset($data['path'])) {
            $where .= " and path like '{$data['path']}%'";
        }
        $result = DB::table('category')->where($where)->orderBy("`showorder`,id")->all();
        //结果转换为特定格式
        $items = array();
        foreach ($result as $row) {
            $items[$row['id']] = $row;
        }
        return genTree5($items);
    }

    function getNames($data)
    {
        $where = " where 1=1";
        if (isset($data['pid'])) {
            $where .= " and pid='{$data['pid']}'";
        }
        return DB::table('category')->where($where)->orderBy("`showorder`,id")->lists('name','id');
//        $sql = "select id,name from {$this->dbfix}category {$where}  order by `showorder`,id";
//        $result = $this->mysql->get_all($sql);
//        //结果转换为特定格式
//        $items = array();
//        foreach ($result as $row) {
//            $items[$row['id']] = $row['name'];
//        }
//        return $items;
    }



    function add($data = array())
    {
        $arr['pid'] = (int)$data['pid'];
        $arr['name'] = $data['name'];
        $arr['title'] = $data['title'];
        $arr['keyword'] = $data['keyword'];
        $arr['remark'] = $data['remark'];
        $arr['showorder'] = (int)$data['showorder'];
        $arr['addtime'] = date('Y-m-d H:i:s');
        $arr['aside1'] = $data['aside1'];
        $arr['aside2'] = $data['aside2'];
        $arr['aside3'] = $data['aside3'];
        $id = Db::table('category')->insertGetId($arr);
        if ($data['pid'] == 0) {
            $row['path'] = $id . ",";
            $row['level'] = 1;
            DB::table('category')->where("id={$id}")->limit(1)->update($row);
        } else {
            $data1 = DB::table('category')->where("id=?")->bindValues($data['pid'])->row();
            $row1['path'] = $data1['path'] . $id . ",";
            $row1['level'] = $data1['level'] + 1;
            DB::table('category')->where("id={$id}")->limit(1)->update($row1);
        }
    }

    function edit($data = array())
    {
        $id = (int)$data['id'];
        $arr['name'] = $data['name'];
        $arr['title'] = $data['title'];
        $arr['keyword'] = $data['keyword'];
        $arr['remark'] = $data['remark'];
        $arr['showorder'] = (int)$data['showorder'];
        $arr['aside1'] = $data['aside1'];
        $arr['aside2'] = $data['aside2'];
        $arr['aside3'] = $data['aside3'];
        return DB::table('category')->where("id={$id}")->limit(1)->update($arr);
    }

    function echoOption($data)
    {
        $pid = $data['pid'];
        $id = (int)$data['id'];
        $path = $data['path'];
        $result = $this->getlist(array('pid' => $pid));
        $count = count($result);

        $num = 1;
        $ss = '';
        foreach ($result as $row) {
            $str = '';
            for ($i = 1; $i < $row['level']; $i++) {
                $str .= '&nbsp;&nbsp;';
            }
            if ($row['level'] == 1)
                $name = $row['name'];
            else {
                if ($num == $count)
                    $name = $str . '└' . $row['name'];
                else
                    $name = $str . '├' . $row['name'];
            }
            $sel = $row['id'] == $id ? 'selected' : '';
            if ($sel != 'selected')
                $sel = $row['path'] == $path ? 'selected' : '';
            $ss .= "<option value=" . $row['path'] . " $sel>$name</option>\r\n";

            $ss .= $this->echoOption(array('pid' => $row['id'], 'id' => $id, 'path' => $path));

            $num++;
        }
        return $ss;
    }

    function createjs()
    {
        $str = "var cate_arr=new Array();\r\n";
        $result1 = DB::table('category')->select('pid')->groupBy('pid')->all();
        foreach ($result1 as $row1) {
            $parentid = $row1['pid'];
            $result = $this->getlist(array('pid' => $parentid));
            $t = "";
            foreach ($result as $row) {
                if ($t == "")
                    $t = $row["id"] . "#" . $row["name"];
                else
                    $t .= "[SER]" . $row["id"] . "#" . $row["name"];
            }
            $str .= "cate_arr[$parentid]='$t';\r\n";
            $result = null;
        }
        $result1 = null;
        $fp = fopen('data/js/category.js', 'w');
        fputs($fp, $str);
        fclose($fp);
    }
}