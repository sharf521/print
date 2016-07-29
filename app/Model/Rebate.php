<?php
namespace App\Model;

use System\Lib\DB;

class Rebate extends Model
{
    public function __construct()
    {
        ini_set("max_execution_time", "1800000");
        ini_set('default_socket_timeout', 600000);
        parent::__construct();
        $this->table = $this->dbfix . 'rebate';
        $this->fields = array('id', 'site_id', 'user_id', 'typeid', 'money', 'addtime', 'status', 'money_rebate', 'success_time');
        /**
         * [rebate_date] => 2016-02-04
         * [rabate1_dividend_ratio] => 0.005
         * [rabate1_dividend_equity] => 500
         * [rabate2_dividend_ratio] => 0.05
         * [rabate2_dividend_equity] => 120
         * [rebate_probability] => 0
         */
        $this->config = DB::table('rebate_config')->lists('v', 'k');
    }

    function addRebate($data)
    {
        if (empty($data['user_id']) || empty($data['typeid']) || empty($data['money'])) {
            $return = '参数错误！';
        } else {
            $arr = array(
                'site_id' => (int)$data['site_id'],
                'typeid' => (int)$data['typeid'],
                'user_id' => (int)$data['user_id'],
                'money' => (float)($data['money']),
                'addtime' => date('Y-m-d H:i:s'),
                'status' => 0,
                'money_rebate' => 0
            );
            $id = DB::table('rebate')->insertGetId($arr);
            $user = DB::table('rebate_user')->where("user_id={$arr['user_id']}")->row();
            if (!$user) {
                DB::table('rebate_user')->insert(array('user_id' => $arr['user_id'], 'money_last' => 0, 'money_30time' => 0));
            }
            if ($id > 0) {
                $return = true;
            } else {
                $return = '内部错误';
            }
        }
        return $return;
    }

    //计算
    function calRebate()
    {
        //global $_G;
        //$rebate_date=$_G['system']['rebate_date'];
        $rebate_date = $this->config['rebate_date'];
        $today = date('Y-m-d');
        $i = 0;
        while ($today > $rebate_date && $i < 1000) {//最多1000次
            $rebate_date = date('Y-m-d', strtotime($rebate_date) + 3600 * 24);
            try {
                DB::beginTransaction();
                DB::table('rebate_config')->where("k='rebate_date'")->limit(1)->update(array('v' => $rebate_date));
                $this->calEveryDay($rebate_date);
                $this->calDividend($rebate_date);
                $this->calRebateList($rebate_date);
                $this->calRebate_30TimesReturn($rebate_date);
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                echo "Failed: " . $e->getMessage();
                return false;
            }
            $i++;
        }
        return true;
    }

    //生成排队位置
    private function calRebateList($rebate_date)
    {
        $result = DB::table('rebate')->select('id,user_id,money,addtime,money_rebate')->where("status=0 and addtime<'{$rebate_date}'")->all();
        foreach ($result as $row) {
            DB::table('rebate')->where("id={$row['id']}")->limit(1)->update(array('status' => 1));//改为己处理
//            if($row['typeid']==3){
//                $row['money']=$row['money']*2;
//            }
            $user_money_last = DB::table('rebate_user')->where("user_id={$row['user_id']}")->value('money_last');
            $money = bcadd($row['money'], floatval($user_money_last), 5);
            $nums500 = bcdiv($money, 500);//500排队个数
            if ($nums500 > 0) {
                $money_100 = bcsub($money, bcmul($nums500, 500), 5);// $money-$nums*500 计算排队100的金额  bcmod 结果为整数，所以不能使用
                $this->calRebateListDo($row, $nums500, 1, $money_100);
            } else {
                $money_100 = $money;
            }
            $nums100 = bcdiv($money_100, 100);//100排队个数
            if ($nums100 > 0) {
                $money_last = bcsub($money_100, bcmul($nums100, 100), 5);
                $this->calRebateListDo($row, $nums100, 2, $money_last);
            } else {
                $money_last = $money_100;
            }
            //更新用户的未排队金额
            DB::table('rebate_user')->where("user_id={$row['user_id']}")->limit(1)->update(array('money_last' => $money_last));
        }
    }

    /*
     * 开始排队
     * $quantity:点位数量
     * $typeid: 1:500队，2:100队
     * $money_last:剩余金额--log查看
     * */
    private function calRebateListDo($rebate, $quantity, $typeid, $money_last)
    {
        if ($typeid == 1) {
            $position_size = 60;
            $position_money = 500;
        } else {
            $position_size = 70;
            $position_money = 100;
        }
        $rebate_last = DB::table('rebate_list')->select('position_end,position_last')->where("typeid={$typeid}")->orderBy('id desc')->row();
        if (!$rebate_last) {
            $rebate_last['position_end'] = 0;
            $rebate_last['position_last'] = 0;
        }
        $to_quantity = bcdiv($quantity + $rebate_last['position_last'], $position_size);//应返个数()
        $position_last_new = bcmod($quantity + $rebate_last['position_last'], $position_size);//不够60个 剩余的个数
        $rebate_list = array(
            'rebate_id' => $rebate['id'],
            'user_id' => $rebate['user_id'],
            'typeid' => $typeid,
            'addtime' => date('Y-m-d H:i:s'),
            'position_quantity' => $quantity,
            'position_start' => $rebate_last['position_end'] + 1,
            'position_end' => $rebate_last['position_end'] + $quantity,// 7 位：1--7
            'position_last' => $position_last_new,
            'money_last' => $money_last,
            'status' => 1
        );
        $rebate_list['id'] = DB::table('rebate_list')->insertGetId($rebate_list);
        //整数倍返
        $this->calRebate_Just60Return($rebate, $rebate_list);

        //排队奖励
        $result = DB::table('rebate_list')->where("typeid={$typeid} and status=1")->orderBy('id')->limit("0,$to_quantity")->all();
        foreach ($result as $rList) {
            if ($to_quantity >= $rList['position_quantity']) {
                $to_quantity -= $rList['position_quantity'];
                $quantity_ying = $rList['position_quantity'];
                //rebate_list 己完成
                $arr = array(
                    'status' => 2,
                    'position_quantity' => 0,
                    'success_time' => date('Y-m-d H:i:s')
                );
            } else {
                $quantity_ying = $to_quantity;
                $to_quantity = 0;
                $arr = array(
                    'position_quantity' => $rList['position_quantity'] - $quantity_ying
                );
            }
            DB::table('rebate_list')->where("id={$rList['id']}")->limit(1)->update($arr);//更新剩余返还位数

            $money_all = bcmul($position_money, $quantity_ying);//500的倍数

            //返还给用户
            $arr = array('rebate_list_in' => $rebate_list['id'], 'rebate_list_out' => $rList['id'], 'rebate_id' => $rList['rebate_id']);
            $this->rebateMoney($money_all, $rList['user_id'], '1,3,1,' . $typeid . ',', $arr);
            if ($to_quantity == 0) {
                break;
            }
        }
    }

    /*
     * 返还给用户
     * $money_all:返还的金额
     * $user_id:返还的用户id
     * $typeid:类型 1,3,1,
     * */
    private function rebateMoney($money, $user_id, $typeid, $data)
    {
        $date = isset($data['date']) ? $data['date'] : date('Y-m-d H:i:s');
        $where = "user_id={$user_id} and status!=2";
        if (isset($data['rebate_id'])) {
            $rebate_id = (int)$data['rebate_id'];
            $where .= " and id<={$rebate_id}";
        }
        $restult = DB::table('rebate')->select('id,user_id,money,money_rebate')->where($where)->orderBy('id')->all();
        foreach ($restult as $row) {
            $rebate_log = array(
                'user_id' => $row['user_id'],
                'rebate_id' => $row['id'],
                'rebate_list_in' => (int)$data['rebate_list_in'],
                'rebate_list_out' => (int)$data['rebate_list_out'],
                'typeid' => $typeid,
                'addtime' => $date
            );
            $arr_rebate = array();
            $money_yu = $row['money'] - $row['money_rebate'];
            if ($money >= $money_yu) {
                $rebate_log['money'] = $money_yu;
                $arr_rebate['status'] = 2;
                $arr_rebate['success_time'] = $date;
                $money = $money - $rebate_log['money'];
            } else {
                $rebate_log['money'] = $money;
                $money = 0;
            }
            if ($rebate_log['money'] > 0) {
                DB::table('rebate_log')->insert($rebate_log);//结算日志
            }
            //更新己返还金额
            $arr_rebate['money_rebate'] = $row['money_rebate'] + $rebate_log['money'];
            DB::table('rebate')->where("id={$row['id']}")->limit(1)->update($arr_rebate);
            if ($money == 0) {
                break;
            }
        }
        return true;
    }

    /**
     * 整数倍返  60返1，70返1
     * @param $rebate  消费记录
     * @param $rebate_list  消费记录进入的排队
     */
    private function calRebate_Just60Return($rebate, $rebate_list)
    {
        //整数倍返 概率0 到1
        if (rand(1, 100) <= $this->config['rebate_probability'] * 100) {
            if ($rebate_list['typeid'] == 1) {
                $position_size = 60;
                $position_money = 500;
            } else {
                $position_size = 70;
                $position_money = 100;
            }
            $position_start_nextpos = (bcdiv($rebate_list['position_start'], $position_size) + 1) * $position_size;//下一个整位置
            if (bcmod($rebate_list['position_start'], $position_size) == 0 || bcmod($rebate_list['position_end'], $position_size) == 0 || $position_start_nextpos < $rebate_list['position_end']) {
                $times = bcdiv($rebate_list['position_end'] - $position_start_nextpos, $position_size) + 1;//多少个整位置
                $rebate_money = $position_money;
                $rebate_quantity = 1;
                for ($i = 1; $i < $times; $i++) {
                    if (rand(1, 100) <= $this->config['rebate_probability'] * 100) {
                        $rebate_money += $position_money;
                        $rebate_quantity++;
                    }
                }
                $rebate_log = array(
                    'user_id' => $rebate_list['user_id'],
                    'rebate_id' => $rebate_list['rebate_id'],
                    'rebate_list_in' => $rebate_list['id'],
                    'rebate_list_out' => $rebate_list['id'],
                    'typeid' => '1,3,2,' . $rebate_list['typeid'] . ',',
                    'addtime' => date('Y-m-d H:i:s')
                );
                $arr_rebate = array();
                if ($rebate_money >= $rebate['money']) {
                    $rebate_log['money'] = $rebate['money'];
                    $arr_rebate['status'] = 2;
                    $arr_rebate['success_time'] = date('Y-m-d H:i:s');
                } else {
                    $rebate_log['money'] = $rebate_money;
                }
                if ($rebate_log['money'] > 0) {
                    DB::table('rebate_log')->insert($rebate_log);//结算日志
                }
                //更新己返还金额
                $arr_rebate['money_rebate'] = $rebate_log['money'];
                DB::table('rebate')->where("id={$rebate['id']}")->limit(1)->update($arr_rebate);

                //减少待返位置
                $arr = array(
                    'position_quantity' => $rebate_list['position_quantity'] - $rebate_quantity
                );
                if ($arr == 0) {
                    $arr['status'] = 2;
                }
                DB::table('rebate_list')->where("id={$rebate_list['id']}")->limit(1)->update($arr);//更新剩余返还位数
            }
        }
    }

    //30倍返 12队列和31队列 只判断第一个未完成的记录
    private function calRebate_30TimesReturn($rebate_date)
    {
        $result = DB::table('rebate')->select('user_id,sum(money-money_rebate) as money_norebate')->where("typeid!=1 and status=1 and addtime<'{$rebate_date}'")->groupBy('user_id')->orderBy('null')->all();
        foreach ($result as $row) {
            $user_id = $row['user_id'];
            $money_norebate = floatval($row['money_norebate']);
            $rebate = DB::table('rebate_user u')->select('u.money_30time,r.id,r.money,r.money_rebate')
                ->leftJoin('rebate r', 'u.user_id=r.user_id')
                ->where("r.typeid!=1 and r.status=1 and r.user_id={$user_id} and r.addtime<'{$rebate_date}'")
                ->orderBy('r.id')->row();
            if ($rebate) {
                $money_30 = bcmul($rebate['money'], 30, 5);
                if ($money_norebate - $rebate['money_30time'] - $rebate['money'] >= $money_30) {
                    $rebate_log = array(
                        'user_id' => $user_id,
                        'money' => $rebate['money'] - $rebate['money_rebate'],
                        'rebate_id' => $rebate['id'],
                        'typeid' => "1,3,3,",
                        'addtime' => date('Y-m-d')
                    );
                    DB::table('rebate_log')->insert($rebate_log);//结算日志
                    //更新己返还金额
                    $arr_rebate['status'] = 2;
                    $arr_rebate['success_time'] = date('Y-m-d');
                    $arr_rebate['money_rebate'] = $rebate['money'];
                    DB::table('rebate')->where("id={$rebate['id']}")->limit(1)->update($arr_rebate);
                    //更新30倍返金额
                    $arr = array('money_30time' => bcadd($rebate['money_30time'], $money_30, 5));
                    DB::table('rebate_user')->where("user_id={$user_id}")->limit(1)->update($arr);
                }
            }
        }
    }

    //分红  前天的营业额 分前天之前的 包含前天
    private function calDividend($rebate_date)
    {
        $rabate1_dividend_ratio = (float)$this->config['rabate1_dividend_ratio'];//16分红比例  0.005
        $rabate1_dividend_equity = (float)$this->config['rabate1_dividend_equity'];//16股权大小 500
        $rabate2_dividend_ratio = (float)$this->config['rabate2_dividend_ratio'];//12分红比例  0.05
        $rabate2_dividend_equity = (float)$this->config['rabate2_dividend_equity'];//12股权大小 120
        $day2 = date('Y-m-d', strtotime($rebate_date) - 3600 * 24 * 2);
        $day1 = date('Y-m-d', strtotime($rebate_date) - 3600 * 24 * 1);
        //16和31队列的16
        $one16_money = 0;//16队列每份分红金额
        $totals16 = DB::table('rebate')->where("typeid!=2 and addtime>'{$day2}' and addtime<'{$day1}'")->value('sum(money)', 'float');//前天营业额
        if ($totals16 > 0) {
            $total = bcmul($totals16, $rabate1_dividend_ratio, 5);//分红总金额
            $result16 = DB::table('rebate')->select('user_id,sum(money) as money')->where("typeid!=2 and status!=2 and money>={$rabate1_dividend_equity} and addtime<'{$day2} 23:59:59'")->groupBy('user_id')->orderBy('null')->all();
            $nums16 = 0;//16总份数
            foreach ($result16 as $k => $row) {
                $_num = bcdiv($row['money'], $rabate1_dividend_equity);
                $nums16 += $_num;
                $result16[$k]['num16'] = $_num;
            }
            if ($nums16 > 0) {
                $one16_money = bcdiv($total, $nums16, 5);//16队列每份分红金额
            }
        }
        //15队列和31队列
        $one15_money = 0;//16队列每份分红金额
        $totals15 = DB::table('rebate')->where("typeid!=1 and addtime>'{$day2}' and addtime<'{$day1}'")->value('sum(money)', 'float');//前天营业额
        if ($totals15 > 0) {
            $total = bcmul($totals15, $rabate2_dividend_ratio, 5);//分红总金额
            $result15 = DB::table('rebate')->select('user_id,sum(money) as money')->where("typeid!=1 and status!=2 and money>={$rabate2_dividend_equity} and addtime<'{$day2} 23:59:59'")->groupBy('user_id')->orderBy('null')->all();
            $nums15 = 0;//15总分数
            foreach ($result15 as $k => $row) {
                $_num = bcdiv($row['money'], $rabate2_dividend_equity);
                $nums15 += $_num;
                $result15[$k]['num15'] = $_num;
            }
            if ($nums15 > 0) {
                $one15_money = bcdiv($total, $nums15, 5);//15队列每份分红金额
            }
        }
        //16 分红 包含31
        if ($one16_money > 0) {
            foreach ($result16 as $row) {
                $money = bcmul($one16_money, $row['num16'], 5);
                $this->rebateMoney($money, $row['user_id'], "1,2,1,", array('date' => $rebate_date));
            }
        }
        //15 分红 包含31
        if ($one15_money > 0) {
            foreach ($result15 as $row) {
                $money = bcmul($one15_money, $row['num15'], 5);
                $this->rebateMoney($money, $row['user_id'], "1,2,2,", array('date' => $rebate_date));
            }
        }
    }

    //天天返 今天结算昨天的数据  参数为 今天的日期
    private function calEveryDay($rebate_date)
    {
        $day500 = date('Y-m-d', strtotime($rebate_date) - 3600 * 24 * 500);
        $day60 = date('Y-m-d', strtotime($rebate_date) - 3600 * 24 * 60);
        $result = DB::table('rebate')->select('id,user_id,typeid,money,addtime,money_rebate')->where("typeid!=2 and status!=2 and addtime>'{$day500}' and addtime<'{$rebate_date}'")->all();
        foreach ($result as $row) {
            $rebate_log = array(
                'user_id' => $row['user_id'],
                'rebate_id' => $row['id'],
                'typeid' => '1,1,' . $row['typeid'] . ',',
                'addtime' => $rebate_date  //应该结算日期的
            );
            if (substr($row['addtime'], 0, 10) >= $day60) {
                $money = bcmul($row['money'], 0.002, 5);
            } else {
                $money = bcdiv(bcmul($row['money'], 0.005, 5), 440, 5);
            }
            $arr_rebate = array();

            if ($money >= $row['money'] - $row['money_rebate']) {
                $rebate_log['money'] = $row['money'] - $row['money_rebate'];
                $arr_rebate['status'] = 2;
                $arr_rebate['success_time'] = $rebate_date;
            } else {
                $rebate_log['money'] = $money;
            }
            if ($rebate_log['money'] > 0) {
                DB::table('rebate_log')->insert($rebate_log);//结算日志
            }
            //更新己返还金额
            $arr_rebate['money_rebate'] = $row['money_rebate'] + $rebate_log['money'];
            DB::table('rebate')->where("id={$row['id']}")->limit(1)->update($arr_rebate);
        }
        return true;
    }

    function getRebateAll()
    {

    }

    function getRebateByPage($data)
    {
        $_select = "r.*";
        $where = "where 1=1";
        if (!empty($data['typeid'])) {
            $where .= " and r.typeid={$data['typeid']}";
        }
        if (!empty($data['startdate'])) {
            $where .= " and r.addtime>='{$data['startdate']}'";
        }
        if (!empty($data['enddate'])) {
            $where .= " and r.addtime<'{$data['enddate']}'";
        }
        if (!empty($data['user_id'])) {
            $where .= " and r.user_id={$data['user_id']}";
        }
        if ($data['status'] != '') {
            $where .= " and r.status={$data['status']}";
        }
        $sql = "select SELECT from {$this->dbfix}rebate r left join {$this->dbfix}user u on r.user_id=u.id {$where} ORDER LIMIT";

        $_order = isset($data['order']) ? ' order by ' . $data['order'] : 'order by r.id desc';
        //总条数
        $row = DB::get_one(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array('count(1) as num,sum(money) as moneys', '', ''), $sql));
        $total = $row['num'];
        $moneys = $row['moneys'];

        $epage = empty($data['epage']) ? 10 : $data['epage'];
        $page = $data['page'];
        if (!empty($page)) {
            $index = $epage * ($page - 1);
        } else {
            $index = 0;
            $page = 1;
        }
        if ($index > $total) {
            $index = 0;
            $page = 1;
        }
        $limit = " limit {$index}, {$epage}";
        //echo str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select, $_order, $limit), $sql);
        $list = DB::get_all(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select, $_order, $limit), $sql));
        global $pager;
        $pager->page = $page;
        $pager->epage = $epage;
        $pager->total = $total;
        return array(
            'list' => $list,
            'moneys' => $moneys,
            'total' => $total,
            'page' => $pager->show()
        );
    }

    function getRebateListByPage($data)
    {
        $_select = "rl.*,r.money,r.money_rebate";
        $where = "where 1=1";
        if (!empty($data['typeid'])) {
            $where .= " and rl.typeid={$data['typeid']}";
        }
        if (!empty($data['user_id'])) {
            $where .= " and rl.user_id={$data['user_id']}";
        }
        if (!empty($data['startdate'])) {
            $where .= " and rl.addtime>='{$data['startdate']}'";
        }
        if (!empty($data['enddate'])) {
            $where .= " and rl.addtime<'{$data['enddate']}'";
        }
        $sql = "select SELECT from {$this->dbfix}rebate_list rl left join {$this->dbfix}rebate r on rl.rebate_id=r.id
 left join {$this->dbfix}user u on rl.user_id=u.id {$where} ORDER LIMIT";

        $_order = isset($data['order']) ? ' order by ' . $data['order'] : 'order by rl.id desc';
        //总条数
        $row = DB::get_one(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array('count(1) as num', '', ''), $sql));
        $total = $row['num'];

        $epage = empty($data['epage']) ? 10 : $data['epage'];
        $page = $data['page'];
        if (!empty($page)) {
            $index = $epage * ($page - 1);
        } else {
            $index = 0;
            $page = 1;
        }
        if ($index > $total) {
            $index = 0;
            $page = 1;
        }
        $limit = " limit {$index}, {$epage}";
        //echo str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select, $_order, $limit), $sql);
        $list = DB::get_all(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select, $_order, $limit), $sql));
        global $pager;
        $pager->page = $page;
        $pager->epage = $epage;
        $pager->total = $total;
        return array(
            'list' => $list,
            'total' => $total,
            'page' => $pager->show()
        );
    }

    function getRebateLogByPage($data)
    {
        $_select = "rl.*";
        $where = "where 1=1";
        if (!empty($data['typeid'])) {
            $where .= " and rl.typeid like '{$data['typeid']}%'";
        }
        if (!empty($data['user_id'])) {
            $where .= " and rl.user_id={$data['user_id']}";
        }
        if (!empty($data['money'])) {
            $where .= " and rl.money={$data['money']}";
        }
        if (!empty($data['startdate'])) {
            $where .= " and rl.addtime>='{$data['startdate']}'";
        }
        if (!empty($data['enddate'])) {
            $where .= " and rl.addtime<'{$data['enddate']}'";
        }
        if (!empty($data['rebate_id'])) {
            $where .= " and rl.rebate_id={$data['rebate_id']}";
        }
        $sql = "select SELECT from {$this->dbfix}rebate_log rl  left join {$this->dbfix}user u on rl.user_id=u.id {$where} ORDER LIMIT";

        $_order = isset($data['order']) ? ' order by ' . $data['order'] : 'order by rl.id desc';
        //总条数
        $row = DB::get_one(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array('count(1) as num,sum(money) as moneys', '', ''), $sql));
        $total = $row['num'];
        $moneys = $row['moneys'];

        $epage = empty($data['epage']) ? 10 : $data['epage'];
        $page = $data['page'];
        if (!empty($page)) {
            $index = $epage * ($page - 1);
        } else {
            $index = 0;
            $page = 1;
        }
        if ($index > $total) {
            $index = 0;
            $page = 1;
        }
        $limit = " limit {$index}, {$epage}";
        //echo str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select, $_order, $limit), $sql);
        $list = DB::get_all(str_replace(array('SELECT', 'ORDER', 'LIMIT'), array($_select, $_order, $limit), $sql));
        global $pager;
        $pager->page = $page;
        $pager->epage = $epage;
        $pager->total = $total;
        return array(
            'list' => $list,
            'moneys' => $moneys,
            'total' => $total,
            'page' => $pager->show()
        );
    }

}

