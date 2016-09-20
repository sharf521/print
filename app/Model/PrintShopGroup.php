<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/21
 * Time: 21:46
 */

namespace App\Model;


class PrintShopGroup extends  Model
{
    protected $table='print_shop_group';
    public function __construct()
    {
        parent::__construct();
    }

    public function Shop()
    {
        return $this->hasOne('\App\Model\PrintShop','id','shop_id');
    }

    /**
     * @return \App\Model\PrintGroup
     */
    public function Group()
    {
        return $this->hasOne('\App\Model\PrintGroup','id','group_id');
    }
}