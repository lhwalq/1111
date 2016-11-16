<?php

/**
 * 彩票
 * addtime 2016/03/07
 */

namespace Duipi\Controller;

use Think\Controller;

class LotteryController extends BaseController {

    private $prize = array();

    public function __construct() {
        #                      奖项     说明     红包价格   概率
        $this->prize[] = array('七等奖', '1元红包', 1, array(800, 1000));
        $this->prize[] = array('六等奖', '2元红包', 2, array(180, 1000));
        $this->prize[] = array('五等奖', '3元红包', 3, array(10, 1000));
        $this->prize[] = array('四等奖', '4元红包', 4, array(7, 1000));
        $this->prize[] = array('三等奖', '6元红包', 6, array(2, 1000));
        $this->prize[] = array('二等奖', '8元红包', 8, array(1, 1000));
        $this->prize[] = array('一等奖', '10元红包', 10, array(0, 0));
    }

    public function _initialize() {
        if (!ismobile()) {
            $daodao = D("fenlei")->where(array("model" => 1, "parentid" => 0))->order("`order` DESC")->LIMIT("15")->select();
            $guanggao = D("guanggao_shuju")->where(array("title" => "首页头", "parentid" => 0))->find();
            $lou = '-22';
            $louname = '0';
            $user_arr = $this->huode_user_arr();
            $user_name = $this->huode_user_name($user_arr, 'username');
            $pinpin = D("pinpai")->where(array("status" => "Y"))->order("`order` DESC")->select();
            $this->assign("huiyuan", $user_arr);
            $this->assign("user_name", $user_name);
            $this->assign("pinpin", $pinpin);
            $this->assign("louname", $louname);
            $this->assign("lou", $lou);
            $this->assign("guanggao", $guanggao);
            $this->assign("daodao", $daodao);
        }
    }

    public function index() {
        parent::__construct();
        $LotteryList = D("activity_lottery")->order("id DESC")->limit(100)->select();

        $user = $this->getUserInfo();

        if (!$user) {
            $msg = '您还没有<a class="blue" href="'.__ROOT__.'/User/login">登录</a>，无法参与抽奖哦';
        } else if ($user['score'] > 1000) {
            $msg = '您拥有的福分足够抽奖啦！';
        } else {
            $msg = '抱歉，您还没有抽奖机会快去赚福分吧！';
        }
        $this->assign("LotteryList", $LotteryList);
        $this->assign("msg", $msg);
        $this->assign("user", $user);
        $this->autoShow("lottery_index");
    }

    public function submit() {

        function _return($ok, $desc, $round = 0, $left = 0) {
            $data = array();
            $data['ok'] = $ok;
            $data['desc'] = $desc;
            $data['round'] = $round;
            $data['left'] = $left;
            echo json_encode($data);
            die;
        }

        $user = $this->getUserInfo();
        if (!$user) {
            _return(false, '您还没有登陆，无法参与抽奖哦');
        } else if ($user['score'] <= C("zhuanpank")) {
            _return(false, '抱歉，您的抽奖次数用完了。');
        }

        $p = $this->probability();
        if ($p == -1) {
            _return(true, '哎呀，姿势不对吧，竟然没中奖！', -1);
        } else {
            list($title, $desc, $money) = $this->prize[$p];
            $round = $this->round($p);
            $left = $user['score'] - C("zhuanpank");

            $db_user = D("yonghu");
            $db_user->where(array("uid" => "{$user['uid']}"))->setInc('money', $money);
            $db_user->where(array("uid" => "{$user['uid']}"))->setDec('score', C("zhuanpank"));

            $add_data = array("uid" => "{$user['uid']}", "prize" => "$p", "money" => "$money", "time" => time(), "title" => "$title", "desc" => "$desc");
            D("activity_lottery")->add($add_data);

            $add_data = array("uid" => "{$user['uid']}", "type" => "1", "pay" => "账户", "content" => "大转盘抽奖[{$title}]红包", "money" => "$money", "time" => time());
            D("yonghu_zhanghao")->add($add_data);

            _return(true, '恭喜' . $desc . "已到账！", $round, $left);
        }
    }

# 获取范围

    private function round($p) {
        $width = 360 / 7;
        $a = $p * $width;
        $b = $a + $width;
        return mt_rand($a + 10, $b - 10);
    }

# 随机一个概率出来

    private function probability() {
        //return rand(0,6);
        $probability_all = array(0, 0);
        foreach ($this->prize as $i => $val) {
            list($title, $desc, $money, $probability) = $val;
            $probability_all[0] += $probability[0];
            $probability_all[1] += $probability[1];
        }
        if (empty($this->prize)) {
            return -1;
        }


        $probability_all[1] = intval($probability_all[1] / count($this->prize));

        $yes = mt_rand(1, $probability_all[1]);

        $prize = -1;
        if ($probability_all[0] <= 0 || $probability_all[1] <= 0 || $yes > $probability_all[0]) {
            
        } else {
            $list = array();
            $add = 0;
            $total = 0;
            foreach ($this->prize as $i => $val) {
                list($title, $desc, $money, $probability) = $val;
                if ($probability[0] <= 0) {
                    continue;
                }
                $total = $add += $probability[0];
                $list[$add] = $i;
            }

            $yes = mt_rand(1, $total);
            foreach ($list as $k => $v) {
                if ($yes <= $k) {
                    $prize = $v;
                    break;
                }
            }
        }
        return $prize;
    }

}
