<?php

namespace App\Model;


class PrintTask extends Model
{
    protected $table='print_task';
    public function __construct()
    {
        parent::__construct();
    }

    public function printOrder()
    {
        $this->hasMany('\App\Model\PrintOrder','id','task_id');
    }
}