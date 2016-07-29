<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/26
 * Time: 15:03
 */

namespace App\Model;


class AccountRecharge extends Model
{
    protected $table='account_recharge';


    public function user()
    {
        return $this->hasOne('\App\Model\User','id','user_id');
    }
}