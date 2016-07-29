<?php
namespace App\Controller\Admin;

use App\Model\Category;
use App\Model\UserType;
use System\Lib\DB;

class CategoryController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new Category();
    }

    //列表
    function index(Category $category)
    {
       // $userType=$userType->find(1);


//
//       var_dump($userType);
//            $userType->name=$userType->name.'亲爱的会员';
//            var_dump($userType->save());
////            echo '<hr>';
//            $ut=new UserType();
//            $ut->name='亲爱的会员';
//            var_dump($ut->save());

//        var_dump($user->first()->UserType()->name);
//        exit;
//        //$a=$user->where('user_id=2010')->first();
//        //$b=$a->UserType()->name;
//        //var_dump($b);
//
//        $ut=new UserType();
//        $a=$ut->find(1)->User();
//        var_dump($a);
//        exit;
//
//        $a=$user->all();
//        foreach ($a as $item)
//        {
//            var_dump($item->user_id);
//        }
//        var_dump($a);
        $pid = (int)$_GET['pid'];
        if (isset($_POST['showorder'])) {
            $id = $_POST['id'];
            $showorder = $_POST['showorder'];
            foreach ($id as $key => $val) {
                DB::table('category')->where("id={$val}")->limit(1)->update(array('showorder' => intval($showorder[$key])));
            }
            $category->createjs();
            show_msg(array('操作成功', '', $this->base_url('category/?pid=' . $_GET['pid'])));
        } else {
            $data['list'] = $category->getlist(array('pid' => $pid));
            if ($pid != 0) {
                $row = DB::table('category')->where("id={$pid}")->row();
                $pid = $row['pid'];
                $data['level'] = $row['level'];
            }
            $data['pid'] = $pid;
            $this->view('category', $data);
        }
    }

    function add(Category $category)
    {
        if ($_POST) {
            $category->add($_POST);
            show_msg(array('添加成功', '', $this->base_url('category/?pid=' . $_GET['pid'])));
            //$this->redirect('permission');
            $category->createjs();
        } else {
            $this->view('category');
        }
    }

    function edit(Category $category)
    {
        if ($_POST) {
            $pid = $_POST['pid'];
            $category->edit($_POST);

            //redirect("category/?pid={$pid}")->back();
            //redirect()->back()->with();
            //redirect("category/?pid={$pid}")->with('key',array('a'=>1,'b'=>2));



            //show_msg(array('修改成功', '', $this->base_url('category/?pid=' . $pid)));

            //$this->redirect('permission');
            $category->createjs();
            redirect("category/?pid={$pid}")->with('msg','修改成功！');
        } else {
            $data['row'] = DB::table('category')->where("id=?")->bindValues($_GET['id'])->row();
            $this->view('category', $data);
        }
    }

    function delete(Category $category)
    {
        $id = (int)$_GET['id'];
        $list = $category->getlist(array('pid' => $id));
        if ($list) {
            show_msg(array('存在子分类，先删除子分类！'));
            exit;
        }
        DB::table('category')->where("id=?")->bindValues($id)->limit(1)->delete();
        redirect('category')->with('msg','删除成功');
        $category->createjs();
    }
}
