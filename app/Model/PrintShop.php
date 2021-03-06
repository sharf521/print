<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/21
 * Time: 14:04
 */

namespace App\Model;


class PrintShop extends Model
{
    protected $table='print_shop';
    public function __construct()
    {
        parent::__construct();
    }

    public function User()
    {
        return $this->hasOne('\App\Model\User','id','user_id');
    }

    public function ShopGroup()
    {
        return $this->hasMany('\App\Model\PrintShopGroup','shop_id','id');
    }

}