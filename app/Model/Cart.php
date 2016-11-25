<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/24
 * Time: 18:01
 */

namespace App\Model;


class Cart extends Model
{
    protected $table='cart';
    public function __construct()
    {
        parent::__construct();
    }

    public function Goods()
    {
        return $this->hasOne('\App\Model\Goods','id','goods_id');
    }

    public function GoodsSpec()
    {
        return $this->hasOne('\App\Model\GoodsSpec','id','spec_id');
    }
}