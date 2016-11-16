<?php

/**
 * 后台圈子
 * addtime 2016/03/29
 */

namespace Duipi\Controller;

use Think\Controller;

class CircleController extends BaseController {

    public function _initialize() {
        $ment = array(
            array("index", "圈子管理", C("URL_DOMAIN") . "Circle/index"),
            array("addcate", "添加圈子", C("URL_DOMAIN") . "Circle/quanzi_edit"),
            array("addcate", "待审核", C("URL_DOMAIN") . "Circle/shenhe_list/type/tiezi"),
        );
        $this->assign("ment", $ment);
    }

    /* 审核帖子 */

    public function shenhe_list() {
        $types = I("type", "");
        $where = "1=1";
        if ($types == 'tiezi') {
            $where = array("tiezi" => '0', "shenhe" => "N");
        } else {
            $where = "tiezi != '0' and shenhe = 'N'";
        }
        $num = 20;
        $zongji = D("quan_tiezi")->where($where)->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");
        $glist = D("quan_tiezi")->where($where)->limit(($fenyenum - 1) * $num, $num)->select();
        $this->assign("glist", $glist);
        $this->assign("fenye", $fenye);
        $this->assign("zongji", $zongji);
        $this->assign("types", $types);
        $this->display("admin/quanzi.shenhe");
    }

    /* 显示全部圈子 */

    public function index() {
        $quanzi = D("quan")->select();
        $this->assign("quanzi", $quanzi);
        $this->display("admin/quanzi.list");
    }

    /* 保存圈子 */

    public function save() {
        if ($_POST['title'] == null)
            $this->note("圈子名不能为空", null, 3);
        $biaoti = htmlspecialchars($_POST['title']);
        $guanli = htmlspecialchars($_POST['guanli']);
        $glfatie = htmlspecialchars($_POST['glfatie']);
        $huifu = htmlspecialchars($_POST['huifu']);
        $shenhe = htmlspecialchars($_POST['shenhe']);

        $checkemail = $this->checkemail($guanli);
        $checkemobile = $this->checkmobile($guanli);
        if ($checkemail === false && $checkemobile === false) {
            $this->note("圈子管理员信息填写错误");
        }
        $res = D("yonghu")->where("email='$guanli' or mobile ='$guanli'")->field("uid")->find();
        if (empty($res)) {
            $this->note("圈子管理员不存在");
        } else {
            $guanli = $res['uid'];
        }

        $jiaru = $_POST['jiaru'];
        $jianjie = htmlspecialchars($_POST['jianjie']);
        $gongao = htmlspecialchars($_POST['gongao']);
        $time = time();
        $img = htmlspecialchars($_POST['img']);

        $id = I("id", 0);
        $data = array("title" => "$biaoti", "img" => "$img", "glfatie" => "$glfatie", "huifu" => "$huifu", "shenhe" => "$shenhe", "guanli" => "$guanli", "jianjie" => "$jianjie", "gongao" => "$gongao", "jiaru" => "$jiaru", "time" => "$time");
        if ($id) {
            $res = D("quan")->where(array("id" => $id))->save($data);
        } else {
            $res = D("quan")->add($data);
        }
        if ($res) {
            $this->note("操作成功");
        } else {
            $this->note("操作失败");
        }
    }

    /* 圈子修改 */

    public function quanzi_edit() {
        $id = I("id", 0);
        if ($id) {
            $quanzi = D("quan")->where(array("id" => $id))->find();
            $huiyuan = D("yonghu")->where(array("uid" => $quanzi['guanli']))->field("email,mobile")->find();
            $this->assign("quanzi", $quanzi);
            $this->assign("huiyuan", $huiyuan);
        }
        $this->display("admin/quanzi.update");
    }

    /* 显示圈子里面全部帖子 */

    public function tiezi() {
        $qzid = I("id", 0);
        if (!$qzid)
            $this->note("参数错误");
        $num = 20;
        $zongji = D("quan_tiezi")->where(array("qzid" => $qzid,"tiezi"=>0))->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");
        $dongtai = D("quan_tiezi")->where(array("qzid" => $qzid,"tiezi"=>0))->limit(($fenyenum - 1) * $num, $num)->select();

        $this->assign("dongtai", $dongtai);
        $this->assign("fenye", $fenye);
        $this->assign("zongji", $zongji);
        $this->display("admin/quanzi.tiezi");
    }

    /* 帖子修改 */

    public function tiezi_update() {
        $id = I("id", 0);
        if (isset($_POST["submit"])) {
            $biaoti = $_POST['title'];
            $neirong = $_POST['neirong'];
            $zhiding = $_POST['zhiding'];
            if ($biaoti == null || $neirong == null) {
                $this->note("不能为空");
            }
            D("quan_tiezi")->where(array("id" => $id))->save(array("title" => "$biaoti", "neirong" => "$neirong", "zhiding" => "$zhiding"));
            $this->note("修改成功", C("URL_DOMAIN") . "circle/index");
        }
        $dongtai = D("quan_tiezi")->where(array("id" => $id))->find();
        $this->assign("dongtai", $dongtai);
        $this->display("admin/quanzi.tiezi.update");
    }

    //显示全部留言
    public function liuyan() {
        $id = I("id");
        $dongtai = D("quan_tiezi")->select();
        $num = 20;
        $zongji = D("quan_tiezi")->where(array("tiezi" => $id))->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");
        $hueifu = D("quan_tiezi")->where(array("tiezi" => $id))->limit(($fenyenum - 1) * $num, $num)->select();
        $this->assign("hueifu", $hueifu);
        $this->assign("fenye", $fenye);
        $this->assign("dongtai", $dongtai);
        $this->assign("zongji", $zongji);
        $this->display("admin/quanzi.liuyan");
    }

    /* 删除圈子或者帖子或者回复 */

    public function delete() {
        $deltype = I("type", "");
        $id = I("id", 0);
        if (!in_array($deltype, array("quanzi", "quanzi_tiezi", "quanzi_hueifu")) || !$id) {
            $this->note("参数错误!");
        }
        if ($deltype == 'quanzi') {
            $q = D("quan")->where(array("id" => $id))->delete();
            $q = D("quan_tiezi")->where(array("qzid" => $id))->delete();
        }
        if ($deltype == 'quanzi_tiezi') {
            D("quan_tiezi")->where(array("id" => $id))->delete();
        }
        if ($deltype == 'quanzi_hueifu') {
            D("quan_tiezi")->where(array("id" => $id))->delete();
        }
        $this->note("删除成功");
    }

    /* 帖子或者回复审核 */

    public function shenhe() {
        $id = I("id", 0);
        D("quan_tiezi")->where(array("id" => $id))->save(array("shenhe" => "Y"));
        $this->note("审核成功");
    }

}

?>