<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/19
 * Time: 10:24
 */

namespace App\Model;


class UploadLog extends Model
{
    protected $table='upload_log';
    public function __construct()
    {
        parent::__construct();
    }
}