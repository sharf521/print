<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/2
 * Time: 17:27
 */

namespace App\Model;


class WeChatTicket extends Model
{
    protected $table='wechat_ticket';
    public function __construct()
    {
        parent::__construct();
    }
}