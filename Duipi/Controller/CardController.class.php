<?php

/**
 * 卡密系统
 * addtime 2016/03/28
 */

namespace Duipi\Controller;

use Think\Controller;

class CardController extends BaseController {

    public function _initialize() {
        $this->getAdminRight(CONTROLLER_NAME, ACTION_NAME);
        $ment = array(
            array("list", "充值卡列表", C("URL_DOMAIN") . "card/lists"),
            array("create", "批量生成卡密", C("URL_DOMAIN") . "card/create"),
            array("edit", "添加卡密", C("URL_DOMAIN") . "card/edit"),
        );
        $this->assign("ment", $ment);
    }

    public function lists() {
        $zongji = D("card")->count();
        $num = 20;
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");
        $lists = D("card")->order("id DESC")->limit(($fenyenum - 1) * $num, $num)->select();
        $this->assign("zongji", $zongji);
        $this->assign("fenye", $fenye);
        $this->assign("lists", $lists);
        $this->display("admin/card.list");
    }

    //添加
    public function save() {
        $id = I("id", 0);
        $title = htmlspecialchars($_POST['title']);
        $pwd = htmlspecialchars($_POST['pwd']);
        $money = htmlspecialchars($_POST['money']);

        $data = array("title" => "$title", "pwd" => "$pwd", "money" => "$money");
        if ($id) {
            D("card")->where(array("id" => "$id"))->save($data);
        } else {
            $data['status'] = 0;
            $data['time'] = time();
            D("card")->add($data);
        }

        $this->note("操作成功", C("URL_DOMAIN") . '/card/lists');
    }

    //修改
    public function edit() {
        $id = I("id", 0);
        if (intval($id) > 0) {
            $info = D("card")->where(array("id" => "$id"))->find();
            if (!$info)
                $this->note("参数错误");
            $this->assign("info", $info);
        }
        $this->display("admin/card.edit");
    }

    //navdel
    public function delete() {
        $id = I("id", 0);
        if (intval($id) <= 0) {
            $this->note("参数错误");
        }
        $res = D("card")->where(array("id" => "$id"))->delete();
        if ($res) {
            $this->note("操作成功", C("URL_DOMAIN") . 'card/lists');
        } else
            $this->note("删除失败");
    }

    public function create() {
        if (IS_POST) {
            $title = I("title", "");
            $number = I("number", 0);
            $money = I("money", 0);
            for ($index = 0; $index < $number; $index++) {
                $sjs = $this->genRandomString2(8);
                $pwd = $this->genRandomString(12);
                $data = array("title" => $title . $sjs, "pwd" => $pwd, "money" => $money);
                D("card")->add($data);
            }
            $this->note("操作成功", C("URL_DOMAIN") . 'card/lists');
        }
        $this->display("admin/card.create");
    }

    public function cardRecharge() {
        //数据库加字段操作  不需要
//        $row = get_one("desc `$config[tablepre]yonghu` `ex1yuan`");
//        if (!is_array($row)) {
//            mysql_query("ALTER TABLE  `$config[tablepre]yonghu` ADD  `ex1yuan` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `login_time` ;");
//        }
        //更新数据库结构
//        $_POST = escapeArr($_POST);
//        $checkKey = array("uid" => "会员id@clear;cut:11;int", "title" => "卡号@clear;cut:32", "pwd" => "卡密@clear;cut:32");
//        $_POST = safecheckArr($_POST, $checkKey, "../?/member/home/userrecharge");
        $uid = I("uid", 0);
        $title = I("title");
        $pwd = I("pwd");
        //过滤	
        $huiyuan = D("yonghu")->where(array("uid" => $uid))->find();
        if (!$huiyuan["uid"] || $uid <= 0) {
            $this->autoNote("该会员不存在！");
            die();
        }
        $card = D("card")->where(array("title" => $title, "status" => 0))->find();
        if ($card["money"] == 1 && $huiyuan["ex1yuan"] == 1) {
            $this->autoNote("您已经体验过1元卡了！");
            die();
        }
        if (!$card["id"]) {
            $this->autoNote("该卡号不存在！");
            die();
        }
        if ($card["pwd"] != $pwd) {
            $this->autoNote("该卡密不正确！");
            die();
        }
        //充值订单
        D("yonghu_addmoney_record")->add(array("uid" => $huiyuan["uid"], "code" => $card[title], "money" => $card[money], "pay_type" => '卡号充值', "status" => '已付款', "score" => '0', "scookies" => '0', "time" => time()));
        //充值记录
        D("yonghu_zhanghao")->add(array("uid" => $huiyuan["uid"], "type" => '1', "pay" => '账户', "content" => $card["title"] . "充值卡充值", "money" => $card["money"], "time" => time()));
        $newmoney = $huiyuan["money"] + $card["money"];
        $saveData = array("money" => $newmoney);
        if ($card["money"] == 1) {
            //更新一元体验卡状态
            $saveData['ex1yuan'] = 1;
        }
        //更新余额
        D("yonghu")->where(array("uid" => $uid))->save($saveData);
        //更新卡状态为已用
        $res = D("card")->where(array("title" => $title))->save(array("status" => 1));
        if ($res) {
            $this->autoNote("充值完成！");
        }
        $this->autoNote("失败！");
    }

}

?>