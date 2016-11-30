<?php
namespace App\Model;

class Goods extends Model
{
    protected $table='goods';
    public function __construct()
    {
        parent::__construct();
    }

    public function addSpec($spec_id=0)
    {
        if($this->is_have_spec){
            $spec=(new GoodsSpec())->find($spec_id);
            if($spec->goods_id==$this->id){
                $this->spec_1=$spec->spec_1;
                $this->spec_2=$spec->spec_2;
                $this->stock_count=$spec->stock_count;
                $this->price=$spec->price;
            }else{
                $this->stock_count=0;
                $this->price=0;
            }
        }
        return $this;
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