<?php
namespace App\Controller\Admin;

use App\Model\Article;
use App\Model\Category;
use System\Lib\DB;
use System\Lib\Request;

class ArticleController extends AdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(Article $article, Category $category)
    {
        $where = ' status>-1 ';
        if (!empty($_GET['categorypath'])) {
            $where .= " and category_path like '{$_GET['categorypath']}%'";
        }
        if (!empty($_GET['keyword'])) {
            $where .= " and title like '%{$_GET['keyword']}%'";
        }
        $result = $article->orderBy('id desc')->where($where)->pager($_GET['page']);
        $data['result'] = $result;
        $data['cates'] = $category->echoOption(array('pid' => 1, 'path' => $_GET['categorypath']));
        $this->view('article', $data);
    }

    //添加文章
    function add(Category $category,Article $article,Request $request)
    {
        if ($_POST) {
            $errormsg = "";
            if ($_POST['title'] == "") {
                $errormsg .= "文章标题不能为空<br>";
            }
            if ($_POST['categoryid'] == "") {
                $errormsg .= "文章分类必选<br>";
            }
            if (!empty($_POST['lable'])) {
                $id=$article->where('lable=?')->bindValues($request->post('lable'))->value();
                if ($id) {
                    $errormsg .= "该标签已存在<br>";
                }
            }
            if ($errormsg != "") {
                show_msg(array($errormsg));
                exit;
            }
            //分类start
            $arr_category = $_POST['categoryid'];
            $categoryid = $arr_category[count($arr_category) - 1];
            if (empty($categoryid)) {
                //最后一个元素为空取末第二个
                $categoryid = $arr_category[count($arr_category) - 2];
            }
            $categoryid = (int)$categoryid;
            if ($categoryid != 0) {
                $categorypath = $category->find($categoryid)->path;
            }
            //分类end

            //添加文章信息
            $arr = array();
            $arr['user_id'] = $this->user_id;
            $arr['title'] = $_POST['title'];
            $arr['typeid'] = (int)$_POST['typeid'];
            $arr['category_id'] = $categoryid;
            $arr['category_path'] = $categorypath;
            $arr['lable'] = $_POST['lable'];
            $arr['status'] = (int)$_POST['status'];
            $arr['picture'] = $_POST['picture'];
            $arr['addtime'] = date('Y-m-d H:i:s');
            $artice_id =DB::table('article')->insertGetId($arr);

            //添加文章内容
            $arr = array();
            $arr['id'] = $artice_id;
            $arr['content'] = $_POST['content'];
            $result = DB::table('article_data')->insert($arr);
            if ($result) {
                redirect('article')->with('msg','添加成功!!');
            }else{
                redirect()->back()->with('error','添加失败');
            }
        } else {
            //一级分类
            $data['cates'] = $category->getlist(array('pid' => 1));
            $this->view('article', $data);
        }
    }

    //修改文章
    function edit(Category $category,Article $article,Request $request)
    {
        $id = (int)$request->id;
        if ($_POST) {
            $errormsg = "";
            if ($_POST['title'] == "") {
                $errormsg .= "文章标题不能为空<br>";
            }
            if ($_POST['categoryid'] == "") {
                $errormsg .= "文章分类必选<br>";
            }
            if ($errormsg != "") {
                show_msg(array($errormsg));
                exit;
            }
            //分类start
            $arr_category = $_POST['categoryid'];
            $categoryid = $arr_category[count($arr_category) - 1];
            if (empty($categoryid)) {
                //最后一个元素为空取末第二个
                $categoryid = $arr_category[count($arr_category) - 2];
            }
            $categoryid = (int)$categoryid;
            if ($categoryid != 0) {
                $categorypath = $category->find($categoryid)->path;
            }
            //分类end

            //修改文章信息
            $arr = array();
            $arr['user_id'] = $this->user_id;
            $arr['title'] = $_POST['title'];
            $arr['typeid'] = (int)$_POST['typeid'];
            $arr['category_id'] = $categoryid;
            $arr['category_path'] = $categorypath;
            $arr['lable'] = $_POST['lable'];
            $arr['status'] = (int)$_POST['status'];
            $arr['picture'] = $_POST['picture'];
            $arr['edittime'] = date('Y-m-d H:i:s');
            $result=Db::table('article')->where("id={$id}")->limit(1)->update($arr);

            $arr = array();
            $arr['content'] = $_POST['content'];
            $result=Db::table('article_data')->where("id={$id}")->limit(1)->update($arr);
            if ($result) {
                redirect('article/?page='.$request->get('page'))->with('msg','保存成功!!');
            }else{
                redirect()->back()->with('error','保存失败!');
            }
        } else {
            //一级分类
            $data['cates'] = $category->getlist(array('pid' => 1));
            $data['row'] = $article->findOrFail($id);
            $data['row']->content = $data['row']->ArticleData()->content;
            $categorypath = explode(',', $data['row']->category_path);
            array_shift($categorypath);
            array_pop($categorypath);
            $i = 1;
            $str = '';
            foreach ($categorypath as $c) {
                $sel = "getsel($i,$c);";
                $str .= $sel;
                $i++;
            }
            $data['row']->sel = $str;
            $this->view('article', $data);
        }
    }

    //文章状态切换
    public function change(Article $article, Request $request)
    {
        $id = (int)$request->get('id');
        $page = (int)$request->get('page');
        $art = $article->findOrFail($id);
        if ($art->status == '1') {
            $art->status = 0;
        } else {
            $art->status = 1;
        }
        //var_dump($art);
        $art->save();
        redirect('article/?page=' . $page)->with('msg', '操作成功！');
    }

    //删除文章
    public function delete(Article $article, Request $request)
    {
        $id = $request->get('id', 'int');
        $page = $request->get('page', 'int');
        $art = $article->findOrFail($id);
        $art->status = -1;
        
        if ($art->save()) {
            redirect('article/?page=' . $page)->with('msg', '删除成功！');
        } else {
            redirect('article/?page=' . $page)->with('error', '删除失败！');
        }
    }
}