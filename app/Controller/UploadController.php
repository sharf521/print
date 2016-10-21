<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/19
 * Time: 16:40
 */

namespace App\Controller;


class UploadController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function save()
    {
        if($_FILES['file']['size']<=0){
            return $this->_error('error');
        }
        $type = $_GET['type'];
        $name = time() . rand(1000, 9000);
        $path = '/data/upload/' . date('Ym') . '/';
        if($type=='chat'){
            $path = '/data/upload/chat/' . date('Ym');
        }else{
            $user_id = $this->user_id;
            if (empty($user_id)) {
                return $this->_error('超时，请重新登陆');
            }
        }
        if ($type == 'article') {
            $path = 'upload/article/' . date('Ym');
        }elseif ($type == 'headimgurl') {
            $name = 'face';
            $path = '/data/upload/' . ceil($user_id / 2000) . '/' . $user_id . '/';
        } elseif ($type == 'card1' || $type == 'card2') {
            $name = $type;
            $path = '/data/upload/' . ceil($user_id / 2000) . '/' . $user_id . '/';
        }
        //创建文件夹
        $_path = ROOT . '/public' . $path;
        if (!file_exists($_path)) {
            if (!mkdir($_path, 0777, true)) {
                echo $_path;
                return $this->_error('Can not create directory');
            }
        }
        if (empty($_FILES['file']['tmp_name'])) {
            return $this->_error('文件大小超过最大限额');
        }
        if ($_FILES['file']['size'] > 1048576 * 5) {
            return $this->_error('文件超过限额，最大5M');
        }
        if ($_FILES['file']['name'] != '') {
            if (function_exists('exif_imagetype')) {
                if (exif_imagetype($_FILES['file']['tmp_name']) < 1) {
                    return $this->_error('not a imagetype');
                }
            } else {
                $ext = $this->getext($_FILES['file']['name']);
                if (!in_array($ext, array(".gif", ".png", ".jpg", ".jpeg", ".bmp"))) {
                    return $this->_error('type error');
                }
            }
        }
        $filename = $name . $ext;
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $_path . $filename)) {
            $this->_error('can not move to tempath');
        } else {
            if($type=='chat'){
                $data = array(
                    'code' => '0',
                    'msg'=>'',
                    'data'=>array(
                        'src' => $filename,
                        'name' => $path . $filename
                    )
                );
            }else{
                $data = array(
                    'code' => '0',
                    'url' => $path . $filename
                );
            }
            echo json_encode($data);
        }
    }

    private function getext($filename)
    {
        return strtolower(strrchr($filename, "."));
    }

    private function _error($msg = '')
    {
        $data = array(
            'code' => 'fail',
            'msg' => "Error：{$msg}"
        );
        echo json_encode($data);
        exit;
    }
}