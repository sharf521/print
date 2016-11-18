<?php
namespace App\Model;

class Goods extends Model
{
    protected $table='goods';
    public function __construct()
    {
        parent::__construct();
    }

    public function GoodsData()
    {
        return $this->hasOne('\App\Model\GoodsData','id','id');
    }
}