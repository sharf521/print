<?php
namespace App\Controller;

use App\Model\Article;
use System\Lib\Request;

class ArticleController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public  function  index()
    {

    }

    public function detail(Request $request,Article $article)
    {
        $id=$request->get(2);
        $row=$article->findOrFail($id);
        $row->content=$row->ArticleData()->content;
        $data['article']=$row;
        $data['title_herder']=$row->title;
        $this->view('article',$data);
    }
}