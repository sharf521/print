<?php
namespace App\Controller\Admin;

use App\Model\App;
use System\Lib\DB;
use System\Lib\Request;

class AppController extends AdminController
{

    public function index(App $app)
    {
        $data['list']=$app->get();
        $this->view('app',$data);
    }

    public function add(Request $request)
    {
        if($_POST){
            $app=new App();
            $app->name=$request->post('name');
            $app->appid=$request->post('appid');
            $app->appsecret=$request->post('appsecret');
            $app->domain=$request->post('domain');
            $app->save();
            redirect('app')->with('msg','添加成功！');
        }else{
            $this->view('app');
        }
    }
    public function edit(Request $request,App $app)
    {
        $app=$app->findOrFail($request->id);
        if($_POST){
            $app->name=$request->post('name');
            $app->appid=$request->post('appid');
            $app->appsecret=$request->post('appsecret');
            $app->domain=$request->post('domain');
            $app->save();
            redirect('app')->with('msg','保存成功！');
        }else{
            $data['row']=$app;
            $this->view('app',$data);
        }
    }
    public function delete(Request $request,App $app)
    {
        $app=$app->findOrFail($request->id);
        $app->delete($app->id);
        redirect('app')->with('msg','删除成功！');
    }
}