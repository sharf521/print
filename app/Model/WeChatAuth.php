<?php

namespace App\Model;

class WeChatAuth extends Model
{
    protected $table='wechat_auth';
    protected $primaryKey='authorizer_appid';
    public function __construct()
    {
        parent::__construct();
    }
}