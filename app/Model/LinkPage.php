<?php
namespace App\Model;

use System\Lib\DB;

class LinkPage extends Model
{

    private $result=null;
    function __construct()
    {
        parent::__construct();
    }

    public function getLinkPage()
    {
        if($this->result==null){
            $arr = array();
            $_result = DB::table('linkpage_type a')->select('a.code,b.value,b.name')->join('linkpage b', 'a.id=b.typeid')->orderBy('b.showorder asc')->all();
            foreach ($_result as $_row) {
                $arr[$_row['code']][$_row['value']] = $_row['name'];
            }
            $_result = null;
            return $arr;
        }
        else{
            return $this->result;
        }
    }

    public function echoLink($code,$val='',$data=array())
    {
        $linkpage = $this->getLinkPage();
        $name = isset($data['name']) ? $data['name'] : $code;
        $title = isset($data['title']) ? $data['title'] : '请选择';
        $attr = isset($data['attr']) ? $data['attr'] : '';
        $html='';
        if ($data['type'] == 'checkbox') {
            foreach ($linkpage[$code] as $i => $v) {
                $_chk = '';
                if (is_array($val) && in_array($i, $val)) {
                    $_chk = 'checked';
                }
                $html.= "<label><input type='checkbox' name='{$name}[]' {$_chk} value='{$i}'>{$v}</label>&nbsp;&nbsp;";
            }
        }elseif($data['type'] == 'radio'){
            $index=0;
            foreach ($linkpage[$code] as $i => $v) {
                $_chk = '';
                if ($index==0 || "$i" == "$val"){
                    $_chk = 'checked';
                }
                $html.= "<label><input type='radio' name='{$name}' {$_chk} value='{$i}'>{$v}</label>&nbsp;&nbsp;";
                $index++;
            }
        } else {
            $html.= "<select name='{$name}' {$attr}><option value=''>{$title}</option>";
            foreach ($linkpage[$code] as $i => $v) {
                if ("$i" == "$val")
                    $html.= "<option value='{$i}' selected>{$v}</option>";
                else
                    $html.= "<option value='{$i}'>{$v}</option>";
            }
            $html.= "</select>";
        }
        return $html;
    }

    function getlist($data = array())
    {
        global $pager;
        $_select = " * ";
        $sql = "select SELECT from {$this->dbfix}linkpage_type ORDER LIMIT";
        $_order = isset($data['order']) ? ' order by ' . $data['order'] : 'order by showorder,id';

        //总条数
        $row = DB::get_one(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array('count(1) as num', '', ''), $sql));
        $total = $row['num'];

        $epage = empty($data['epage']) ? 10 : $data['epage'];
        $page = $data['page'];
        if (!empty($page)) {
            $index = $epage * ($page - 1);
        } else {
            $index = 0;
            $page = 1;
        }
        if ($index > $total) {
            $index = 0;
            $page = 1;
        }
        $limit = " limit {$index}, {$epage}";
        $list = DB::get_all(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select, $_order, $limit), $sql));
        global $pager;
        $pager->page = $page;
        $pager->epage = $epage;
        $pager->total = $total;
        return array(
            'list' => $list,
            'total' => $total,
            'page' => $pager->show()
        );
    }

    /*//根据code获取联动列表
    function getlistbycode($data)
    {
        $sql="select b.* from {$this->dbfix}linkpage_type a,{$this->dbfix}linkpage b where a.id=b.typeid and a.code='{$data['code']}' order by b.showorder asc";
        return $this->mysql->get_all($sql);
    }
    function getlistid($data)
    {
        $sql="select b.id from {$this->dbfix}linkpage_type a,{$this->dbfix}linkpage b where a.id=b.typeid and a.code='{$data['code']}' and b.name='{$data['name']}' and b.value='{$data['value']}' limit 1";
        $row=$this->mysql->get_one($sql);
        return $row['id'];
    }*/
    function linklist($data = array())
    {
        global $pager;
        $_select = " * ";
        $where = "where 1=1";
        if (!empty($data['typeid'])) {
            $where .= " and typeid={$data['typeid']}";
        }
        $sql = "select SELECT from {$this->dbfix}linkpage {$where} ORDER LIMIT";
        $_order = isset($data['order']) ? ' order by ' . $data['order'] : 'order by showorder,id';

        //总条数
        $row = DB::get_one(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array('count(1) as num', '', ''), $sql));
        $total = $row['num'];

        $epage = empty($data['epage']) ? 10 : $data['epage'];
        $page = $data['page'];
        if (!empty($page)) {
            $index = $epage * ($page - 1);
        } else {
            $index = 0;
            $page = 1;
        }
        if ($index > $total) {
            $index = 0;
            $page = 1;
        }
        $limit = " limit {$index}, {$epage}";
        $list = DB::get_all(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select, $_order, $limit), $sql));
        global $pager;
        $pager->page = $page;
        $pager->epage = $epage;
        $pager->total = $total;
        return array(
            'list' => $list,
            'total' => $total,
            'page' => $pager->show()
        );
    }

    private function set($post)
    {
        $post['name'] = (strip_tags($post['name']));
        $post['code'] = (strip_tags($post['code']));
        $post['showorder'] = (strip_tags($post['showorder']));
        return $post;
    }

    public function edit($post)
    {
        $id = $post['id'];
        $post = $this->set($post);
        DB::table('linkpage_type')->where('id=?')->bindValues($id)->limit(1)->update($post);
    }

    public function add($post)
    {
        $post = $this->set($post);
        $post['showorder'] = ($post['showorder']) ? $post['showorder'] : 10;
        $post['createdate'] = date('Y-m-d H:i:s');
        return DB::table('linkpage_type')->insert($post);
    }

    public function link_add($data)
    {
        $data['createdate'] = date('Y-m-d H:i:s');
        $data['showorder'] = ($data['showorder']) ? $data['showorder'] : 10;
        return DB::table('linkpage')->insert($data);
    }

    public function Action($data = array())
    {
        $name = $data['name'];
        $code = $data['code'];
        $showorder = $data['showorder'];
        $type = isset($data['type']) ? $data['type'] : "";
        unset($data['type']);
        $riqi = date('Y-m-d H:i:s');
        if ($type == "add") {

            foreach ($name as $key => $val) {
                if ($val != "") {
                    $sql = "insert into {$this->dbfix}linkpage_type set `name`='" . $name[$key] . "',`code`='" . $code[$key] . "',`showorder`='" . $showorder[$key] . "', `createdate`='" . $riqi . "', `status`=1 ";
                    DB::query($sql);
                }
            }
        } else {
            $typeid = $data['typeid'];
            $value = $data['value'];
            foreach ($name as $key => $val) {
                if ($val != "") {
                    $sql = "insert into {$this->dbfix}linkpage set `typeid`='" . $typeid . "',`name`='" . $name[$key] . "',`value`='" . $value[$key] . "',`showorder`='" . $showorder[$key] . "', `createdate`='" . $riqi . "', `status`=1 ";
                    DB::query($sql);
                }
            }
        }
        return true;
    }
}