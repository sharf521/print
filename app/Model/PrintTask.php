<?php

namespace App\Model;


use System\Lib\DB;

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

    public function del($id)
    {
        $task=$this->findOrFail($id);
        if($task->status >=4 ){
            //支付之前可以操作
            return '禁止删除，状态异常！';
        }
        $task->delete($id);
        DB::table('print_order')->where('task_id=?')->bindValues($id)->delete();
        return true;
    }
}