<?php
namespace App\Controller\Auth;

use App\Model\User;
use System\Lib\DB;
use System\Lib\Request;

class IndexController extends AuthController
{
    public function __construct()
    {
        parent::__construct();
    }

    //index.php/auth/login/?appid=shop&redirect_uri=http://www.yuantuwang.com/&sign=1F07E77550FEBAD99EC245CBDBC7EF29
    public function login(Request $request,User $user)
    {
        $get=array(
            'appid'=>$request->appid,
            'sign'=>$request->sign,
            'redirect_uri'=>$request->redirect_uri
        );
        $this->checkSign($get);
        if($_POST){
            $request->checkToken();
            $data = array(
                'username' => $request->post('username'),
                'password' => $request->post('password')
            );
            $result = $user->login($data);
            if ($result === true) {
                $id=session('user_id');
                $openid=DB::table('app_user')->where("user_id={$id} and app_id=?")->bindValues($this->appid)->value('openid');
                if(empty($openid)){
                    $openid = $this->createOpenId();
                    $arr=array(
                        'user_id'=>$id,
                        'app_id'=>$this->appid,
                        'openid'=>$openid,
                        'created_at'=>time()
                    );
                    DB::table('app_user')->insert($arr);
                }
                $sign=$this->getSign(array('openid'=>$openid));
                if(strpos($get['redirect_uri'],'?')===false){
                    $url=$get['redirect_uri'].'?openid='.$openid.'&sign='.$sign;
                }else{
                    $url=$get['redirect_uri'].'&openid='.$openid.'&sign='.$sign;
                }
                redirect($url);
            } else {
                $error = $result;
            }
            redirect()->back()->with('error',$error);
        }else{
            $data['_url']="/auth/register/?appid={$get['appid']}&redirect_uri={$get['redirect_uri']}&sign={$get['sign']}";
            $this->view('login',$data);
        }
    }

    public function register(Request $request,User $user)
    {
        $get=array(
            'appid'=>$request->appid,
            'sign'=>$request->sign,
            'redirect_uri'=>$request->redirect_uri
        );
        $this->checkSign($get);
        if($_POST){
            $request->checkToken();
            $data = array(
                'username' => $request->post('username'),
                'email'=>$request->post('email'),
                'password' => $request->post('password'),
                'sure_password'=>$request->post('sure_password'),
            );
            $result = $user->register($data);
            if ($result === true) {
                $id=session('user_id');
                $openid = $this->createOpenId();
                $arr=array(
                    'user_id'=>$id,
                    'app_id'=>$this->appid,
                    'openid'=>$openid,
                    'created_at'=>time()
                );
                DB::table('app_user')->insert($arr);
                $sign=$this->getSign(array('openid'=>$openid));
                if(strpos($get['redirect_uri'],'?')===false){
                    $url=$get['redirect_uri'].'?openid='.$openid.'&sign='.$sign;
                }else{
                    $url=$get['redirect_uri'].'&openid='.$openid.'&sign='.$sign;
                }
                redirect($url);
            } else {
                $error = $result;
            }
            redirect()->back()->with('error',$error);
        }else{
            $data['_url']="/auth/login/?appid={$get['appid']}&redirect_uri={$get['redirect_uri']}&sign={$get['sign']}";
            $this->view('register',$data);
        }
    }

    private function createOpenId()
    {
        $uuid = uniqid(rand(100000000,999999999),true);
        $openid = str_replace('.','',$uuid);
        return $openid;
    }
}