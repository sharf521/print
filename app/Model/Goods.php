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

    public function GoodsImage()
    {
        return $this->hasMany('\App\Model\GoodsImage','goods_id','id',"status=1");
    }

    public function GoodsSpec()
    {
        return$this->hasMany('\App\Model\GoodsSpec','goods_id','id');
    }
}