<?php
namespace App\Controller;

use System\Lib\Request;

class PluginController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    //验证码
    public function code()
    {
        //session_start();
        $width = 50;    //先定义图片的长、宽
        $height = isset($_REQUEST['height']) ? $_REQUEST['height'] : 18;
        $rand_str = "";
        $codeSet = '346789ABCDEFGHJKLMNPQRTUVWXY';
        $fontSize = 25;     // 验证码字体大小(px)
        $useCurve = true;   // 是否画混淆曲线
        $useNoise = true;   // 是否添加杂点
        $imageH = 0;        // 验证码图片宽
        $imageL = 0;        // 验证码图片长
        $length = 4;        // 验证码位数
        for ($i = 0; $i < 4; $i++) {
            $rand_str .= chr(mt_rand(48, 57));
        }
        if (function_exists("imagecreate")) {
            $_SESSION["randcode"] = strtolower($rand_str);//注册session
            $img = imagecreate($width, $height);//生成图片
            imagecolorallocate($img, 255, 255, 255);  //图片底色，ImageColorAllocate第1次定义颜色PHP就认为是底色了
//    $black = imagecolorallocate($img,127,157,185);
            $black = imagecolorallocate($img, 0, 0, 0);     //此条及以下三条为设置的颜色
            $white = imagecolorallocate($img, 255, 255, 255);
            $gray = imagecolorallocate($img, 200, 200, 200);
            $red = imagecolorallocate($img, 255, 0, 0);
            for ($i = 0; $i < 10; $i++) {
                //杂点颜色
                $noiseColor = imagecolorallocate($img, mt_rand(150, 190), mt_rand(150, 180), mt_rand(150, 180));
                for ($j = 0; $j < 5; $j++) {
                    // 绘杂点
                    imagestring($img, $j, mt_rand(-10, $height), mt_rand(-20, $width), $codeSet[mt_rand(0, 27)], $noiseColor);
                    // 杂点文本为随机的字母或数字
                }
            }
            for ($i = 0; $i < 4; $i++) { //加入文字
                imagestring($img, mt_rand(3, 6), $i * 10 + 6, mt_rand(2, 5), $rand_str[$i], imagecolorallocate($img, mt_rand(0, 89), mt_rand(0, 89), mt_rand(0, 89)));
            }
            //	imagerectangle($img,0,0,$width-1,$height-1,$black);//先成一黑色的矩形把图片包围
            if (function_exists("imagejpeg")) {
                header("content-type:image/jpeg\r\n");
                imagejpeg($img);
            } else {
                header("content-type:image/png\r\n");
                imagepng($img);
            }
            imagedestroy($img);
        } else {
            $_SESSION["randcode"] = "1234";
            header("content-type:image/jpeg\r\n");
            $fp = fopen("./randcode.bmp", "r");
            echo fread($fp, filesize("./validate.bmp"));
            fclose($fp);
        }
    }

    public function ajaxFileUpload()
    {
        $type=$_GET['type'];
        $name=time().rand(1000,9000);
        $user_id=$this->user_id;
        if(empty($user_id)){
            $data = array(
                'status' => 'fail',
                'data' => '超时，请重新登陆'
            );
            echo json_encode($data);
            exit;
        }
        $path = '/data/upload/' . date('Ym').'/';
        if ($type == 'article') {
            $path = 'upload/article/' . date('Ym');
        }elseif ($type=='headimgurl'){
            $name='face';
            $path='/data/upload/'.ceil($user_id/2000).'/'.$user_id.'/';
        }elseif ($type=='shop'){
            $path='/data/upload/'.ceil($user_id/2000).'/'.$user_id.'/'.date('Ym').'/';
        }
        //创建文件夹
        $_path=ROOT.'/public'.$path;
        if (!file_exists($_path)) {
            if (!mkdir($_path, 0777, true)) {
                $data = array(
                    'status' => 'fail',
                    'data' => '失败：Can not create directory'
                );
                echo json_encode($data);
                exit;
            }
        }
        if($_FILES['files']['name']!=''){
            $storage = new \Upload\Storage\FileSystem($_path,true);
            $file = new \Upload\File('files', $storage);
            $file->setName($name);
            $file->addValidations(array(
                new \Upload\Validation\Mimetype(array('image/png', 'image/gif','image/jpeg')),
                // Ensure file is no larger than 5M (use "B", "K", M", or "G")
                new \Upload\Validation\Size('5M'),
            ));
            try {
                if($file->upload()){
                    $data['data']=$path.$file->getNameWithExtension();
                    $data['status'] = 'success';
                    echo json_encode($data);
                }
            } catch (\Exception $e) {
                $errors = $file->getErrors();
                $data = array(
                    'status' => 'fail',
                    'data' => '失败：'.json_encode($errors)
                );
                echo json_encode($data);
                exit;
            }
        }
    }
}