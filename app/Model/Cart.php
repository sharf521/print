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
}