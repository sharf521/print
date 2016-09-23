<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/23
 * Time: 17:07
 */

namespace App\Controller\Chat;


use App\Model\User;
use System\Lib\Request;

class IndexController extends ChatController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(User $user,Request $request)
    {

        $id=$request->id;
        $data['user']=$user->findOrFail($id);
        $this->view('index',$data);
    }

    public function init(User $user,Request $request)
    {
        $id=$request->id;
        $user=$user->findOrFail($id);
        // 建立连接
        $client = stream_socket_client('tcp://121.41.30.46:7273');
        if(!$client)exit("can not connect");
        // 模拟超级用户，以文本协议发送数据，注意Text文本协议末尾有换行符（发送的数据中最好有能识别超级用户的字段），
        //这样在Event.php中的onMessage方法中便能收到这个数据，然后做相应的处理即可

        fwrite($client, '{"type":"init","id":"'.$id.'", "username":"'.$user->username.'","avatar":"'.$user->headimgurl.'", "sign":"******"}'."\n");
        echo json_encode(array('code'=>0));
    }

//init
    public function getList()
    {
        ?>
        {
        "code": 0
        ,"msg": ""
        ,"data": {
        "mine": {
        "username": "纸飞机"
        ,"id": "100000"
        ,"status": "online"
        ,"sign": "在深邃的编码世界，做一枚轻盈的纸飞机"
        ,"avatar": "http://cdn.firstlinkapp.com/upload/2016_6/1465575923433_33812.jpg"
        }
        ,"friend": [{
        "groupname": "前端码屌"
        ,"id": 1
        ,"online": 2
        ,"list": [{
        "username": "贤心"
        ,"id": "100001"
        ,"avatar": "http://tp1.sinaimg.cn/1571889140/180/40030060651/1"
        ,"sign": "这些都是测试数据，实际使用请严格按照该格式返回"
        },{
        "username": "徐小峥"
        ,"id": "666666"
        ,"avatar": "http://tp2.sinaimg.cn/1783286485/180/5677568891/1"
        ,"sign": "代码在囧途，也要写到底"
        }]
        },{
        "groupname": "我心中的女神"
        ,"id": 3
        ,"online": 1
        ,"list": [{
        "username": "林心如"
        ,"id": "76543"
        ,"avatar": "http://tp3.sinaimg.cn/1223762662/180/5741707953/0"
        ,"sign": "我爱贤心"
        },{
        "username": "佟丽娅"
        ,"id": "4803920"
        ,"avatar": "http://tp4.sinaimg.cn/1345566427/180/5730976522/0"
        ,"sign": "我也爱贤心吖吖啊"
        }]
        }]
        ,"group": [{
        "groupname": "前端群"
        ,"id": "101"
        ,"avatar": "http://tp2.sinaimg.cn/2211874245/180/40050524279/0"
        },{
        "groupname": "Fly社区官方群"
        ,"id": "102"
        ,"avatar": "http://tp2.sinaimg.cn/5488749285/50/5719808192/1"
        }]
        }
        }
        <?
    }

    ////查看群员接口
    public function getMembers()
    {
        ?>
        {
        "code": 0
        ,"msg": ""
        ,"data": {
        "owner": {
        "username": "贤心"
        ,"id": "100001"
        ,"avatar": "http://tp1.sinaimg.cn/1571889140/180/40030060651/1"
        ,"sign": "这些都是测试数据，实际使用请严格按照该格式返回"
        }
        ,"list": [{
        "username": "Z_子晴"
        ,"id": "108101"
        ,"avatar": "http://tva3.sinaimg.cn/crop.0.0.512.512.180/8693225ajw8f2rt20ptykj20e80e8weu.jpg"
        ,"sign": "微电商达人"
        },{
        "username": "Lemon_CC"
        ,"id": "102101"
        ,"avatar": "http://tp2.sinaimg.cn/1833062053/180/5643591594/0"
        ,"sign": ""
        },{
        "username": "长泽梓Azusa"
        ,"id": "100001222"
        ,"sign": "我是日本女艺人长泽あずさ"
        ,"avatar": "http://tva1.sinaimg.cn/crop.0.0.180.180.180/86b15b6cjw1e8qgp5bmzyj2050050aa8.jpg"
        },{
        "username": "大鱼_MsYuyu"
        ,"id": "12123454"
        ,"avatar": "http://tp1.sinaimg.cn/5286730964/50/5745125631/0"
        ,"sign": "我瘋了！這也太準了吧  超級笑點低"
        },{
        "username": "佟丽娅"
        ,"id": "4803920"
        ,"avatar": "http://tp4.sinaimg.cn/1345566427/180/5730976522/0"
        ,"sign": "我也爱贤心吖吖啊"
        }]
        }
        }
        <?
    }
}