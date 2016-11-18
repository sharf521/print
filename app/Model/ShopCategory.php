<?php
namespace App\Model;


class ShopCategory extends Model
{
    protected $table='shop_category';
    public function __construct()
    {
        parent::__construct();
    }

    public function getListTree($user_id)
    {
        $result=$this->where("user_id=?")->bindValues($user_id)->orderBy("`showorder`,id")->get(\PDO::FETCH_ASSOC);
        //结果转换为特定格式
        $items = array();
        foreach ($result as $row) {
            $items[$row['id']] = $row;
        }
        return $this->genTree5($items);
    }

    private function genTree5($items)
    {
        $tree = array(); //格式化好的树
        foreach ($items as $item)
            if (isset($items[$item['pid']]))
                $items[$item['pid']]['son'][] = &$items[$item['id']];
            else
                $tree[] = &$items[$item['id']];
        return $tree;
    }
}