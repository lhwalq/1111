<?php

/**
 * 用户
 * addtime 2016/02/18
 */

namespace Duipi\Controller;

use Think\Controller;

class MemberController extends BaseController {

    public function _initialize() {
        $this->getAdminRight(CONTROLLER_NAME, ACTION_NAME);
        $this->admininfo = $this->getAdminInfo();
        if (!$this->admininfo) {
            $this->note("请先登录", C("URL_DOMAIN") . "admin/login");
        }
    }

    /**
      显示会员
      根据第四个参数显示不同类型会员
      @def 		默认会员
      @del 		删除会员
      @noreg 		未认证会员
      @b_qq 		QQ绑定会员
      @b_weibo 	微博绑定会员
      @b_taobao 	淘宝绑定会员
      @day_new	今日新增
      @day_shop	今日消费
      ....
      第五个参数排序字段
      uid,money,time,jingyan,score
      ....
      第六个参数排序类型
      desc,asc
      ....
     */
    public function lists() {

        $db = new \Think\Model;
        if ($_POST['btnSave']) {
            if (empty($_POST['id'])) {
                echo"<script>alert('必须选择一个产品,才可以删除!');history.back(-1);</script>";
                exit;
            } else {
                /* 如果要获取全部数值则使用下面代码 */
                $id = implode(",", $_POST['id']);
                $db->startTrans();
                $user = $db->table("yys_yonghu")->where("uid in ($id)")->select();
                foreach ($array as $key => $value) {
                    unset($user[$key]["id"]);
                }
                $str1 = $db->table("yys_yonghu_del")->add($user);
                $str = $db->table("yys_yonghu")->where("uid in ($id)")->delete();
                if ($str1 && $str) {
                    $db->commit();
                    $this->note("删除成功!");
                    exit;
                } else {
                    $db->rollback();
                    $this->note("删除失败!");
                    exit;
                }
            }
        }
        //批量加机器人
        if ($_POST['btnjiqiren']) {
            if (empty($_POST['id'])) {
                echo"<script>alert('必须选择一个产品,才可以删除!');history.back(-1);</script>";
                exit;
            } else {
                /* 如果要获取全部数值则使用下面代码 */
                $id = implode(",", $_POST['id']);
                $db->startTrans();
                $str1 = $db->table("yys_yonghu")->where("uid in ($id)")->save(array("huiyuan" => "1"));
                if ($str1) {
                    $db->commit();
                    $this->note("机器人设置成功!");
                    exit;
                } else {
                    $db->rollback();
                    $this->note("机器人设置失败!");
                    exit;
                }
            }
        }
        //批量加机器人结束
        //批量取消机器人
        if ($_POST['btnjiqiren1']) {
            if (empty($_POST['id'])) {
                echo"<script>alert('必须选择一个产品,才可以删除!');history.back(-1);</script>";
                exit;
            } else {
                /* 如果要获取全部数值则使用下面代码 */
                $id = implode(",", $_POST['id']);
                $db->startTrans();
                $str1 = $db->table("yys_yonghu")->where("uid in ($id)")->save(array("huiyuan" => "0"));
                if ($str1) {
                    $db->commit();
                    $this->note("机器人设置成功!");
                    exit;
                } else {
                    $db->rollback();
                    $this->note("机器人设置失败!");
                    exit;
                }
            }
        }
        //批量取消机器人结束
        //批量dongjie
        if ($_POST['btndongjie']) {
            if (empty($_POST['id'])) {
                echo"<script>alert('必须选择一个产品,才可以删除!');history.back(-1);</script>";
                exit;
            } else {
                /* 如果要获取全部数值则使用下面代码 */
                $id = implode(",", $_POST['id']);
                $db->startTrans();
                $str1 = $db->table("yys_yonghu")->where("uid in ($id)")->save(array("dongjie" => "1"));
                if ($str1) {
                    $db->commit();
                    $this->note("帐号冻结成功!");
                    exit;
                } else {
                    $db->rollback();
                    $this->note("帐号冻结失败!");
                    exit;
                }
            }
        }
        //批量dongjie
        //批量解冻
        if ($_POST['btndongjie1']) {
            if (empty($_POST['id'])) {
                echo"<script>alert('必须选择一个产品,才可以删除!');history.back(-1);</script>";
                exit;
            } else {
                /* 如果要获取全部数值则使用下面代码 */
                $id = implode(",", $_POST['id']);
                $db->startTrans();
                $str1 = $db->table("yys_yonghu")->where("uid in ($id)")->save(array("dongjie" => "0"));
                if ($str1) {
                    $db->commit();
                    $this->note("帐号解冻成功!");
                    exit;
                } else {
                    $db->rollback();
                    $this->note("帐号解冻失败!");
                    exit;
                }
            }
        }
        //批量解冻
        $weer_type = I("type", "def");
        $weer_ziduan = I("ziduan", "uid");
        $weer_order = I("order", "desc");


        $weer_type_arr = array("def" => "默认会员", "del" => "删除会员", "noreg" => "未认证会员", "day_new" => "今日新增", "day_shop" => "今日消费", "b_qq" => "QQ绑定会员", "b_weibo" => "微博绑定会员", "b_taobao" => "淘宝绑定会员");
        if (!isset($weer_type_arr[$weer_type])) {
            $weer_type = "def";
        }
        if ($weer_type == 'del') {
            $table = "yys_yonghu_del";
        } else {
            $table = "yys_yonghu";
        }

        $weer_ziduan_arr = array("uid" => "会员ID", "money" => "账户金额", "score" => "账户福分", "jingyan" => "会员经验", "time" => "注册时间", "login_time" => "登陆时间");
        if (!isset($weer_ziduan_arr[$weer_ziduan])) {
            $weer_ziduan = "uid";
        }


        if ($weer_order != "desc" && $weer_order != "asc") {
            $weer_order = 'desc';
            $weer_order_cn = "倒序显示";
        } else {
            $weer_order_cn = "正序显示";
        }

        $sql_where = '';
        switch ($weer_type) {
            case 'def':
                $sql_where = "((emailcode = '1' or mobilecode = '1') or band is not null)";
                break;
            case 'del':
                $sql_where = '1=1';
                break;
            case 'noreg':
                $sql_where = "emailcode <> '1' and mobilecode <> '1' and band is null";
                break;
            case 'b_qq':
                $sql_where = "band LIKE '%qq%'";
                break;
            case 'b_weibo':
                $sql_where = "band LIKE '%weibo%'";
                break;
            case 'b_taobao':
                $sql_where = "band LIKE '%taobao%'";
                break;
            case 'day_new':
                $day_time = strtotime(date("Y-m-d"));
                $sql_where = "time > '$day_time'";
                break;
            case 'day_shop':
                $day_time = strtotime(date("Y-m-d")) . '.000';
                $uids = '';
                $conutc = $db->table("yys_yonghu_yys_record")->where("time > '$day_time'")->select();
                foreach ($conutc as $c) {
                    $uids .= "'" . $c['uid'] . "',";
                }
                $uids = trim($uids, ",");
                if (!empty($uids)) {
                    $sql_where = "uid in($uids)";
                } else {
                    $sql_where = "uid in('0')";
                }
                break;
            default:
                $sql_where = "emailcode = '1' or mobilecode = '1'";
                break;
        }
        //会员查找
        if (isset($_POST['submit'])) {
            $sousuo = htmlspecialchars(trim($_POST['sousuo']));
            $content = htmlspecialchars(trim($_POST['content']));

            if (empty($sousuo) || empty($content)) {
                $this->note("参数错误");
            }
            $huiyuans = array();
            if ($sousuo == 'id') {
                $sql_where .= " and uid = '$content'";
            }
            if ($sousuo == 'nickname') {
                $sql_where .= " and username LIKE '%$content%'";
            }
            if ($sousuo == 'email') {
                $sql_where .= " and email LIKE '%$content%'";
            }
            if ($sousuo == 'mobile') {
                $sql_where .= " and mobile LIKE '%$content%'";
            }
        }

        $select_where = "当前查看{$weer_type_arr[$weer_type]} - 使用{$weer_ziduan_arr[$weer_ziduan]} - {$weer_order_cn}";
        $num = 20;
        $zongji = $db->table($table)->where($sql_where)->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");
        $huiyuans = $db->table($table)->where($sql_where)->order("$weer_ziduan $weer_order")->limit(($fenyenum - 1) * $num, $num)->select();
        /* 删除会员 */
        $member_del_num = $db->table("yys_yonghu_del")->count();
        /* 今日新增 */
        $time = strtotime(date("Y-m-d"));
        $member_new_num = $db->table("yys_yonghu")->where("time > '$time'")->count();
        $ment = $this->returnMent();
        $this->assign("ment", $ment);
        $this->assign("table", $table);
        $this->assign("member_del_num", $member_del_num);
        $this->assign("member_new_num", $member_new_num);
        $this->assign("select_where", $select_where);
        $this->assign("weer_type", $weer_type);
        $this->assign("zongji", $zongji);
        $this->assign("huiyuans", $huiyuans);
        $this->assign("fenye", $fenye);
        $this->display("admin/member.lists");
    }

    public function returnMent() {
        $ment = array(
            array("lists", "会员列表", C("URL_DOMAIN") . "member/lists"),
//            array("lists", "查找会员", C("URL_DOMAIN") . "user/select"),
            array("insert", "添加会员", C("URL_DOMAIN") . "member/insert"),
            array("insert", "会员配置", C("URL_DOMAIN") . "config/userconfig"),
            array("insert", "会员福利配置", C("URL_DOMAIN") . "config/member_fufen"),
            array("insert", "充值记录", C("URL_DOMAIN") . "order/recharge"),
            array("insert", "机器人头像设置", C("URL_DOMAIN") . "member/touxiang"),
        );
        return $ment;
    }

//添加会员
    public function insert() {

        $huiyuan_allgroup = D("yonghu_group")->field("groupid,name")->select();
        if (isset($_POST['submit'])) {
            $weername = htmlspecialchars(trim($_POST['username']));
            if (empty($weername)) {
                $this->note("用户名不能为空");
                exit;
            }
            $password = htmlspecialchars(trim($_POST['password']));
            if (empty($password)) {
                $this->note("密码不能为空");
                exit;
            } else {
                $password = md5($password);
            }

            $img = htmlspecialchars($_POST['thumb']);
            $youjian = htmlspecialchars(trim($_POST['email']));
            $mobile = htmlspecialchars(trim($_POST['mobile']));

            $money = htmlspecialchars(trim($_POST['money']));
            $jingyan = htmlspecialchars(trim($_POST['jingyan']));
            $score = htmlspecialchars(trim($_POST['score']));
            $youjiancode = htmlspecialchars(trim($_POST['emailcode']));
            $mobilecode = htmlspecialchars(trim($_POST['mobilecode']));
            $qianming = htmlspecialchars(trim($_POST['qianming']));
            $huiyuangroup = htmlspecialchars(trim($_POST['membergroup']));
            $time = time();

            $db_user = D("yonghu");
            $info = $db_user->where("email = '$youjian' or mobile = '$mobile'")->find();
            $info1 = $db_user->where("email = '$youjian'")->find();
            $info2 = $db_user->where("mobile = '$mobile'")->find();


            if ($info1 && $info2) {
                $this->note("该会员已经存在！");
            }
            $data = array("username" => "$weername", "img" => "$img", "email" => "$youjian", "mobile" => "$mobile", "password" => "$password", "money" => "$money", "jingyan" => "$jingyan", "score" => "$score", "emailcode" => "$youjiancode", "mobilecode" => "$mobilecode", "qianming" => "$qianming", "groupid" => "$huiyuangroup", "time" => "$time");
            $res = $db_user->add($data);
            if ($res) {
                $time = time();
                $uid = $res;
                $ip = $this->huode_ip_dizhi();
                if ($money) {
                    $hou1 = "金额:" . $money;
                }
                if ($score) {
                    $hou2 = "福分:" . $score;
                }
                if ($yongjin) {
                    $hou3 = "佣金:" . $yongjin;
                }
                $zhuangtai = "管理员增加帐号" . $hou1 . $hou2 . $hou3;
                if (!$hou1 && !$hou2 && !$hou3) {
                    $zhuangtai = "管理员只增加了用户";
                }
                D("yonghurz")->add(array("username" => $uid, "user_ip" => $ip, "login_time" => time(), "tishi" => $zhuangtai));
                $this->note("增加成功");
            } else {
                $this->note("增加失败");
            }
        }
        $ment = $this->returnMent();
        $this->assign("ment", $ment);
        $this->assign("huiyuan_allgroup", $huiyuan_allgroup);
        $this->display("admin/member_insert");
    }

    //修改会员
    public function modify() {
        $uid = I("id", 0);
        $huiyuan = D("yonghu")->where(array("uid" => $uid))->find();

        $huiyuan_group = D("yonghu_group")->where(array("groupid" => $huiyuan['groupid']))->find();

        $huiyuan_allgroup = D("yonghu_group")->field("groupid,name")->select();

        if (!empty($huiyuan['addgroup'])) {
            $huiyuan['addgroup'] = trim($huiyuan['addgroup'], ',');
            $addgroup = explode(',', $huiyuan['addgroup']);
            for ($i = 0; $i < count($addgroup); $i++) {
                $quanzi = D("quan")->where(array("id" => $addgroup[$i]))->field("title")->find();
                $quanziname[] = $quanzi['title'];
            }
        }


        if (isset($_POST['submit'])) {

            $img = htmlspecialchars($_POST['thumb']);
            $weername = htmlspecialchars(trim($_POST['username']));
            $youjian = htmlspecialchars(trim($_POST['email']));
            $mobile = htmlspecialchars(trim($_POST['mobile']));
            $password = htmlspecialchars(trim($_POST['password']));
            $yongjin = htmlspecialchars(trim($_POST['yongjin']));
            $yaoqing = htmlspecialchars(trim($_POST['yaoqing']));
            $yaoqing2 = htmlspecialchars(trim($_POST['yaoqing2']));
            $yaoqing3 = htmlspecialchars(trim($_POST['yaoqing3']));

            if (empty($password)) {
                $password = $huiyuan['password'];
            } else {
                $password = md5($password);
            }
            $money = sprintf('%.2f', trim($_POST['money']));
            $jingyan = htmlspecialchars(trim($_POST['jingyan']));
            $score = htmlspecialchars(trim($_POST['score']));
            $youjiancode = htmlspecialchars(trim($_POST['emailcode']));
            $mobilecode = htmlspecialchars(trim($_POST['mobilecode']));
            $qianming = htmlspecialchars(trim($_POST['qianming']));
            $huiyuangroup = htmlspecialchars(trim($_POST['membergroup']));

            if ($money != $huiyuan['money']) {
                if ($money > $huiyuan['money']) {
                    $content_money = $money - $huiyuan['money'];
                    $content_num = '1';
                } else {
                    $content_money = $huiyuan['money'] - $money;
                    $content_num = '-1';
                }
                $time = time();
                D("yonghu_zhanghao")->save(array("uid" => $huiyuan['uid'], "type" => $content_num, "pay" => "账户", "content" => "管理员修改金额", "money" => $content_money, "time" => $time));
            }

            $res = D("yonghu")->where(array("uid" => $uid))->save(array("username" => $weername, "email" => "$youjian", "mobile" => "$mobile", "password" => "$password", "money" => "$money", "jingyan" => "$jingyan", "score" => "$score", "emailcode" => "$youjiancode", "mobilecode" => "$mobilecode", "img" => "$img", "groupid" => "$huiyuangroup", "qianming" => "$qianming", "yongjin" => "$yongjin", "yaoqing" => "$yaoqing", "yaoqing2" => "$yaoqing2", "yaoqing3" => "$yaoqing3"));
            if ($res) {
                $this->note("修改成功");
            } else {
                $this->note("修改失败");
            }
        }
        $ment = $this->returnMent();
        $this->assign("ment", $ment);
        $this->assign("huiyuan_allgroup", $huiyuan_allgroup);
        $this->assign("huiyuan", $huiyuan);
        $this->assign("huiyuan_group", $huiyuan_group);
        $this->display("admin/member_modify");
    }

    //恢复会员
    public function huifu() {
        $uid = I("id", 0);
        $db = new \Think\Model;
        $db->startTrans();
        $user = $db->table("yys_yonghu_del")->where(array("uid" => "$uid"))->find();
        $q1 = $db->table("yys_yonghu")->add($user);
        $q2 = $db->table("yys_yonghu_del")->where(array("uid" => "$uid"))->delete();
        if ($q1 && $q2) {
            $db->commit();
            $this->note("恢复成功");
        } else {
            $db->rollback();
            $this->note("恢复失败");
        }
    }

    //删除会员
    public function del() {
        $uid = I("id", 0);
        $db = new \Think\Model;
        $db->startTrans();

        $user = $db->table("yys_yonghu")->where(array("uid" => "$uid"))->find();
//        print_r($user);exit;
        $q1 = $db->table("yys_yonghu_del")->add($user);
        $q2 = $db->table("yys_yonghu")->where(array("uid" => "$uid"))->delete();
        $q3 = $db->table("yys_yonghu_band")->where(array("b_uid" => "$uid"))->delete();

        if ($q1 && $q2) {
            $db->commit();
            $this->note("删除成功");
        } else {
            $db->rollback();
            $this->note("删除失败");
        }
    }

    public function del_true() {
        $uid = I("id", 0);
        $db = new \Think\Model;
        $q1 = $db->table("yys_yonghu_del")->where(array("uid" => "$uid"))->delete();
        if ($q1) {
            $this->note("删除成功");
        } else {
            $this->note("删除失败");
        }
    }

    public function moni() {
        $p_key = I("id", 0);
        $uid = $p_key;
        $gg = D("yonghu")->where(array("uid" => "$uid"))->find();
        cookie("uid", null);
        cookie("ushell", null);
        $se1 = cookie("uid", $this->encrypt($uid), 60 * 60 * 24 * 7);
        $se2 = cookie("ushell", $this->encrypt(md5($uid . $gg['password'] . $gg['mobile'] . $gg['email'])), 60 * 60 * 24 * 7);
        header("Location:" . C("URL_DOMAIN") . "/user/home");
        exit;

        exit;
    }

    //头像设置
    public function touxiang() {
        $db = new \Think\Model;
        $db->startTrans();
        $shezhi = D("yonghu")->where("huiyuan='1'")->select();

        foreach ($shezhi as $key => $v) {
            $q1 = D("yonghu")->where(array("uid" => "{$v['uid']}"))->save(array("img" => "photo/member ($key).jpg"));
        }
        if ($q1) {
            $db->commit();
            $this->note("头像批量设置成功!");
        }
        $db->rollback();
        $this->note("头像批量设置失败!");
    }

    //会员组
    public function member_group() {
        $ment = array(
            array("member_group", "会员组", C("URL_DOMAIN") . "member/member_group"),
            array("member_add_group", "添加会员组", C("URL_DOMAIN") . "member/member_edit_group"),
        );
        $huiyuans = D("yonghu_group")->select();
        $this->assign("huiyuans", $huiyuans);
        $this->assign("ment", $ment);
        $this->display("admin/member.member_group");
    }

    //修改会员组
    public function group_save() {
        $id = I("id", 0);
        $name = htmlspecialchars(trim($_POST['name']));
        $jingyan_start = htmlspecialchars(trim($_POST['jingyan_start']));
        $jingyan_end = htmlspecialchars(trim($_POST['jingyan_end']));
        if (empty($name) || empty($jingyan_start) || empty($jingyan_end)) {
            $this->note('会员组或者经验值不能为空');
        } elseif ($jingyan_start >= $jingyan_end) {
            $this->note('开始经验不能大于结束经验');
        } elseif ($jingyan_end <= $jingyan_start) {
            $this->note('结束经验不能小于开始经验');
        }
        $data = array('name' => "$name", 'jingyan_start' => "$jingyan_start", 'jingyan_end' => "$jingyan_end");
        if ($id) {
            $res = D("yonghu_group")->where(array("groupid" => "$id"))->save($data);
        } else {
            $res = D("yonghu_group")->add($data);
        }
        if ($res) {
            $this->note("操作成功");
        } else {
            $this->note("操作失败");
        }
    }

    //删除会员组
    public function group_del() {
        $id = I("id", 0);
        $res = D("yonghu_group")->where(array("groupid" => "$id"))->delete();
        if ($res) {
            $this->note("删除成功");
        } else {
            $this->note("删除失败");
        }
    }

    //增加会员组
    public function member_edit_group() {
        $id = I("id", 0);
        if ($id) {
            $huiyuans = D("yonghu_group")->where(array("groupid" => "$id"))->find();
            $this->assign("huiyuans", $huiyuans);
        }
        $this->display("admin/member.group_modify");
    }

}
