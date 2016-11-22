<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/22
 * Time: 11:31
 */

namespace App\Controller;


use App\Model\Goods;
use System\Lib\Request;

class GoodsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->template='shop_wap';
    }

    public function detail(Goods $goods,Request $request)
    {
        $data['goods']=$goods->findOrFail($request->get('id'));
        $data['images']=$goods->GoodsImage();
        $data['GoodsData']=$goods->GoodsData();
        $this->view('goods_detail',$data);
    }
}