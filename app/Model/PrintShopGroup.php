<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/21
 * Time: 21:46
 */

namespace app\Model;


class PrintShopGroup extends  Model
{
    protected $table='print_shop_group';
    public function __construct()
    {
        parent::__construct();
    }

    public function PrintShop()
    {
        return $this->hasOne('\App\Model\PrintShop','id','shop_id');
    }
}