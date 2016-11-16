<?php

/**
 * 前台圈子
 * addtime 2016/03/29
 */

namespace Duipi\Controller;

use Think\Controller;

class GroupController extends BaseController {

    public function _initialize() {
        $filter = array();
        $this->userinfo = $this->getUserInfo();
        if ($this->userinfo) {
            $this->assign("huiyuan", $this->userinfo);
        } else if (!in_array(ACTION_NAME, $filter)) {
            //$this->autoNote("请先登录", C("URL_DOMAIN") . "user/login");
        }
    }

    /* 会员是否加入该圈子 */

    private function user_add_group($qzid = 0) {
        if (!$this->userinfo)
            return false;
        $addids = trim($this->userinfo['addgroup'], ",") . ',';
        if (strpos($addids, $qzid . ',') === false) {
            return false;
        }
        return true;
    }

    public function index() {
        $huiyuan = $this->userinfo;
        $biaoti = '圈子列表_' . C("web_name");
        $quanzi = D("quan")->select();
        $dongtai = D("quan_tiezi")->group("hueiyuan")->order("id desc")->limit("5")->select();
        $hueifu = D("quan_hueifu")->group("hueiyuan")->order("id DESC")->limit("12")->select();
        $this->assign("quanzi", $quanzi);
        $this->assign("hueifu", $hueifu);
        $this->assign("dongtai", $dongtai);
        $this->display("index/group.index");
    }

    public function show() {
        $id = abs(intval(I("id")));
        $quanzi = D("quan")->where(array("id" => $id))->find();
        if (!$quanzi) {
            $this->note("还没有建立改圈子");
        }
        $biaoti = $quanzi['title'] . "_" . C("web_name");
        $guanjianzi = $quanzi['jianjie'];
        $miaoshu = $quanzi['gongao'];
        /* 是否加入圈子 */
        if (!$this->user_add_group($id)) {
            $addgroup = false;
        } else {
            $addgroup = true;
        }
        /* 是否加入圈子 */

        $num = 10;

        $zongji = D("quan_tiezi")->where(array("qzid" => $id, "tiezi" => '0', "shenhe" => 'Y'))->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");
        $qz = D("quan_tiezi")->where(array("qzid" => $id, "tiezi" => '0', "shenhe" => 'Y'))->order("zhiding DESC,id DESC")->limit(($fenyenum - 1) * $num, $num)->select();
        $this->assign("fenye", $fenye);
        $this->assign("qz", $qz);
        $this->assign("addgroup", $addgroup);
        $this->assign("quanzi", $quanzi);
        $this->assign("zongji", $zongji);
        $this->display("index/group.list");
    }

    /* 加入圈子与退出 */

    public function goqz() {
        $uid = $this->userinfo['uid'];
        if (!$uid)
            exit;
        $qzid = intval(I("id"));
        $text = I("text");

        $chengyuan = D("quan")->where(array("id" => $qzid))->find();
        if (!$chengyuan)
            return;

        if ($text == "退出") {
            if (!$this->user_add_group($qzid)) {
                return;
            }
            $iqroup = str_ireplace($qzid . ",", "", $this->userinfo['addgroup']);
            $cy = $chengyuan['chengyuan'] - 1;
        } else {
            if ($this->user_add_group($qzid)) {
                return;
            }
            $iqroup = $this->userinfo['addgroup'] . $qzid . ',';
            $cy = $chengyuan['chengyuan'] + 1;
        }
        D("yonghu")->where(array("uid" => $uid))->save(array("addgroup" => $iqroup));
        D("quan")->where(array("id" => $qzid))->save(array("chengyuan" => $cy));
    }

    /* 发表圈子帖子 */

    public function showinsert() {
        if (!$this->userinfo)
            $this->note("未登录", C("URL_DOMAIN") . "user/login");
        if (isset($_POST['submit'])) {
            $uid = $this->userinfo['uid'];
            $biaoti = htmlspecialchars($_POST['title']);
            $neirong = $_POST['neirong'];
            $qzid = intval($_POST['qzid']);
            /* 是否加入圈子 */
            if (!$this->user_add_group($qzid)) {
                $this->note("您还未加入该圈子");
            }
            /* 验证码 */
            $group_syzm = cookie("checkcode");
            $group_pyzm = isset($_POST['group_code']) ? strtoupper($_POST['group_code']) : '';
            if (empty($group_pyzm)) {
                $this->note("请输入验证码");
            }
            if ($group_syzm != md5($group_pyzm)) {
                $this->note("验证码不正确");
            }
            /* 验证码 */
            $quanzi = D("quan")->where(array("id" => $qzid))->find();
            if (!$quanzi)
                $this->note("该圈子不存在");
            if ($quanzi['glfatie'] == 'N' && $quanzi['guanli'] != $uid) {
                $this->note($quanzi['title'] . ": 会员不能发帖!");
            }
            if ($biaoti == null || $neirong == null)
                $this->note("不能为空");
            $time = time();

            $dongtai = D("quan_tiezi")->where(array("hueiyuan" => $uid, "qzid" => $qzid, "title" => $biaoti, "neirong" => $neirong))->find();
            if ($dongtai)
                $this->note("不能重复提交");

            if ($quanzi['shenhe'] == 'Y') {
                $shenhe = 'N';
            } else {
                $shenhe = 'Y';
            }
            $data = array("hueiyuan" => $uid, "qzid" => $qzid, "title" => $biaoti, "neirong" => $neirong, "zuihou" => $uid, "shenhe" => $shenhe, "time" => $time);
            D("quan_tiezi")->add($data);
            D("quan")->where(array("id" => $qzid))->setInc("tiezi", 1);
            $this->note("添加成功");
        }
    }

    /* 帖子回复显示页 */

    public function nei() {
        $uid = $this->userinfo['uid'];
        $id = abs(intval(I("id")));
        if (!$id)
            $this->note("页面错误");

        $dongtai = D("quan_tiezi")->where(array("id" => $id))->find();
        if (!$dongtai)
            $this->note("页面错误");

        $dianji = $dongtai['dianji'] + 1;
        D("quan_tiezi")->where(array("id" => $id))->save(array("dianji" => $dianji));
        $biaoti = $dongtai['title'] . "_" . C("web_name");
        $guanjianzi = $dongtai['title'];
        $miaoshu = $this->htmtguolv($this->strcut($dongtai['neirong'], 0, 250));

        $huiyuan = D("yonghu")->where(array("uid" => $dongtai["hueiyuan"]))->find();
        $quanzi = D("quan")->where(array("id" => $dongtai["qzid"]))->find();

        $num = 10;
        $zongji = D("quan_tiezi")->where(array("tiezi" => $id))->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");
        $hueifu = D("quan_tiezi")->where(array("tiezi" => $id, "shenhe" => 'Y'))->order("id DESC")->limit(($fenyenum - 1) * $num, $num)->select();
        $this->assign("fenye", $fenye);
        $this->assign("num", $num);
        $this->assign("hueifu", $hueifu);
        $this->assign("quanzi", $quanzi);
        $this->assign("dongtai", $dongtai);
        $this->assign("zongji", $zongji);
        $this->display("index/group.nei");
    }

    /* 发表帖子回复 */

    public function hueifuinsert() {
        $uid = $this->userinfo['uid'];
        if ($uid == null)
            $this->note("未登录");
        if (!isset($_POST['submit'])) {
            exit;
        }
        $group_syzm = cookie("checkcode");
        $group_pyzm = isset($_POST['group_code']) ? strtoupper($_POST['group_code']) : '';
        if (empty($group_pyzm)) {
            $this->note("请输入验证码");
        }
        if ($group_syzm != md5($group_pyzm)) {
            $this->note("验证码不正确");
        }
        $qzid = intval($_POST['qzid']);
        $qzinfo = D("quan")->where(array("id" => $qzid))->find();
        if (!$qzinfo || $qzinfo['huifu'] == 'N') {
            $this->note("该圈子禁用回复!");
        }
        $hueifu = $this->htmtguolv($_POST['hueifu']);
        if ($hueifu == null)
            $this->note("内容不能为空");
        $tzid = intval($_POST['tzid']);
        if ($tzid <= 0)
            $this->note("错误");
        $hftime = time();
        if ($qzinfo['shenhe'] == 'Y') {
            $shenhe = 'N';
        } else {
            $shenhe = 'Y';
        }
        $data = array("hueiyuan" => $uid, "qzid" => $qzid, "tiezi" => $tzid, "neirong" => $hueifu, "zuihou" => $uid, "shenhe" => $shenhe, "time" => $hftime);
        $res = D("quan_tiezi")->add($data);
        D("quan_tiezi")->where(array("id" => $tzid))->setInc("hueifu", 1);
        if ($qzinfo['shenhe'] == 'Y') {
            $this->note("添加成功,需要管理员审核");
        }
        $this->note("添加成功");
    }

}

?>