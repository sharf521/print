<?php

namespace App\Model;


class Order extends Model
{
    protected $table='order';
    public function __construct()
    {
        parent::__construct();
    }

    public function OrderGoods()
    {
        return $this->hasMany('\App\Model\OrderGoods','order_sn','order_sn');
    }
}