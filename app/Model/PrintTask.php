<?php

namespace App\Model;


class PrintTask extends Model
{
    protected $table='print_task';
    public function __construct()
    {
        parent::__construct();
    }

    public function User()
    {
        return $this->hasOne('\App\Model\User','id','user_id');
    }

    public function UserReply()
    {
        return $this->hasOne('\App\Model\User','id','reply_uid');
    }

    public function PrintOrder()
    {
        return $this->hasMany('\App\Model\PrintOrder','task_id','id');
    }
}