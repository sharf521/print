<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/26
 * Time: 12:13
 */

namespace App\Model;


class UserAddress extends Model
{
    protected $table='user_address';
    public function __construct()
    {
        parent::__construct();
    }
}