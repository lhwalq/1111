<?php

/**
 * admin
 * addtime 2016/03/15
 */

namespace Duipi\Controller;

use Think\Controller;

class AdminController extends BaseController {

    /**
     * admin导航
     * @return type
     */
    function headerment($ments = null) {
        $html = '';
        $html_l = '';
        $URL = trim($this->get_LOCAL_url(), '/');

        if (is_array($ments)) {
            $ment = $ments;
        } else {
            if (!isset($this->ment))
                return false;
            $ment = $this->ment;
        }
        foreach ($ment as $k => $v) {

            if ($v[2] == $URL) {
                $html_l = '<h3 class="nav_icon">' . $v[1] . '</h3><span class="span_fenge lr10"></span>';
            }
            if (!isset($v[3])) {
                $html.='<a href="' . $v[2] . '">' . $v[1] . '</a>';
                $html.='<span class="span_fenge lr5">|</span>';
            }
        }
        return $html_l . $html;
    }

    /**
     * 验证admini权限
     * @param type $uid
     * @param type $ashell
     * @return boolean
     */
    final protected function CheckAdminInfo($uid = null, $ashell = null) {
        $db = new \Think\Model;
        if ($uid && $ashell) {
            $CheckId = $this->encrypt($uid, 'DECODE');
            $CheckAshell = $this->encrypt($ashell, 'DECODE');
        } else {
            $CheckId = $this->encrypt(cookie("AID"), 'DECODE');
            $CheckAshell = $this->encrypt(cookie("ASHELL"), 'DECODE');
        }
        if (!$CheckId || !$CheckAshell) {
            return false;
        }
        $info = $this->db->YOne("SELECT * FROM `@#_manage` WHERE `uid` = '$CheckId'");

        //权限控制开始
        $liebiao = $_SERVER['REQUEST_URI'];
        $shujuku = explode(",", $info['qx']);
        $houzhui = intval($this->segment(4));
        if (is_numeric($houzhui)) {
            $liebiao = str_replace($houzhui, "", $liebiao);
        }
        $houzhui2 = substr($liebiao, -1);
        if ($houzhui2 == '/') {
            $liebiao = substr($liebiao, 0, strlen($liebiao) - 1);
        }
        if (in_array($liebiao, $shujuku)) {
            echo "<script>alert('无权限,请联系总管理员');exit;</script>";
            exit;
        }
        $tempLieBiao = '/index.php' . $liebiao;
        if (in_array($tempLieBiao, $shujuku)) {
            echo "<script>alert('无权限,请联系总管理员');exit;</script>";
            exit;
        }
        //权限控制结束
        //老的权限删除
        if (!$info)
            return false;
        $infoshell = md5($info['username'] . $info['userpass']) . md5($_SERVER['HTTP_USER_AGENT']);
        $this->AdminInfo = $info;
        return true;
    }

    public function index() {
        $info = $this->getAdminInfo();
        $path = "@@@";
        if (C("charset") == 'utf-8') {
            $path.= 'utf8/';
        } elseif (C("charset") == 'gbk') {
            $path.= 'gbk/';
        }
        $stauts = 1;
        $upfile_url = $path;
        $version = C("release");
        //获取压缩包			
        $content = @file_get_contents($upfile_url);
        $pathlist = false;
        if (!$content) {
            $stauts = -1;
        } else {
            //数组的位置
            $key = -1;
            $allpathlist = $pathlist = array();
            preg_match_all("/>(patch_[\w_]+\.zip)</", $content, $allpathlist);
            $allpathlist = $allpathlist[1];
            //获取可供当前版本升级的压缩包
            foreach ($allpathlist as $k => $v) {
                if (strstr($v, 'patch_' . $version)) {
                    $key = $k;
                    break;
                }
            }
            $key = $key < 0 ? 9999 : $key;
            foreach ($allpathlist as $k => $v) {
                if ($k >= $key) {
                    $pathlist[$k] = $v;
                }
            }
        }
        $upfile_num = count($pathlist);
        $this->assign("menu", R("Menu/getAdminMenu"));
        $this->assign("info", $info);
        $this->display("admin.index");
    }

    public function login() {
        if (IS_AJAX || IS_POST) {
            $location = C("URL_DOMAIN") . "admin/index";
            $message = array("error" => false, 'text' => $location);
            $weername = I("username", "");
            $password = I("password", "");
            $code = strtoupper(I("code", ""));
            if (empty($weername)) {
                $message['error'] = true;
                $message['text'] = "请输入用户名!";
                echo json_encode($message);
                exit;
            }
            if (empty($password)) {
                $message['error'] = true;
                $message['text'] = "请输入密码!";
                echo json_encode($message);
                exit;
            }
            if (C("website_off")) {
                if (empty($code)) {
                    $message['error'] = true;
                    $message['text'] = "请输入验证码!";
                    echo json_encode($message);
                    exit;
                }
                if (md5($code) != cookie('checkcode')) {
                    $message['error'] = true;
                    $message['text'] = "验证码输入错误";
                    echo json_encode($message);
                    exit;
                }
            }
            $db_manage = D("manage");
            $info = $db_manage->where(array("username" => "$weername"))->find();
            $data = array();
            $data['user_ip'] = $this->huode_ip_dizhi();
            $data['login_time'] = time();
            $data['username'] = $weername;
            $data['code'] = "1";
            if (!$info) {
                $message['error'] = true;
                $message['text'] = "登录失败,请检查用户名或密码!";
                echo json_encode($message);
                $data['zhuangtai'] = "登录失败,用户名不存在";
                D("yonghurz")->add($data);
                exit;
            }
            if ($info['userpass'] != md5(md5($password))) {
                $message['error'] = true;
                $message['text'] = "登陆失败!";
                $data['zhuangtai'] = "登录失败";
                D("yonghurz")->add($data);
                echo json_encode($message);
                exit;
            }
            if (!$message['error']) {
                cookie("AID", $this->encrypt($info['uid'], 'ENCODE'));
                cookie("ASHELL", $this->encrypt(md5($info['username'] . $info['userpass']) . md5($_SERVER['HTTP_USER_AGENT'])));
                $gg = $this->encrypt(md5($info['username'] . $info['userpass']) . md5($_SERVER['HTTP_USER_AGENT']));
                $time = time();
                $ip = $this->huode_ip();
                $db_manage->where(array("uid" => "{$info['uid']}"))->save(array("logintime" => "$time", "loginip" => "$ip"));
            }
            echo json_encode($message);
            $data['zhuangtai'] = "登录成功";
            D("yonghurz")->add($data);
            exit;
        } else {
            $this->display("user.login");
        }
    }

    public function out() {
        cookie("AID", null);
        cookie("ASHELL", null);
        $this->note("退出成功", C("URL_DOMAIN") . '/admin/login');
    }

    public function Tdefault() {
        $info = $this->getAdminInfo();
        $SysInfo = $this->GetSysInfo();
        $db = new \Think\Model;
        $db_version = $db->query('select version()');
        $db_version = $db_version ? $db_version[0]['version()'] : @mysql_get_server_info();
        $SysInfo['MysqlVersion'] = $db_version;
        $versions = C("version");
        $banben_arr = explode(",", $this->encrypt("DfvfGG5belBaAwcAC1GK3r+wSoSF3Gz0965sLlKgbis04z59aabCQkGlQABwcEU1wDg1GUVcCC7BTWi+KC263Wu7EV19Pv2JiGG+rt", "DECODE", "G_BANBEN_TYPE"));
        $banben_num = C("YYS_BANBEN_NUMBER");
        if (isset($banben_arr[$banben_num])) {
            $banben_txt = $banben_arr[$banben_num];
        } else {
            if ($banben_num == -1) {
                $banben_txt = base64_decode("5pyq5o6I5p2D");
            } else if ($banben_num == -2) {
                $banben_txt = base64_decode("5o6I5p2D5Yiw5pyf");
            } else {
                $banben_txt = base64_decode("5pyq5o6I5p2D");
            }
        }
        $this->assign("SysInfo", $SysInfo);
        $this->assign("info", $info);
        $this->display("admin.default");
    }

    public function lists() {
        $num = 20;
        $db_manage = D("manage");
        $zongji = $db_manage->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");
        $AdminList = $db_manage->limit(($fenyenum - 1) * $num, $num)->select();
        $this->assign("AdminList", $AdminList);
        $this->assign("ment", $this->returnAdmin());
        $this->assign("zongji", $zongji);
        $this->assign("fenye", $fenye);
        $this->display("admin/user.list");
    }

    public function returnAdmin() {
        $ment = array(
            array("lists", "管理员管理", C("URL_DOMAIN") . "admin/lists"),
            array("reg", "添加管理员", C("URL_DOMAIN") . "admin/edit"),
            array("right", "权限码列表", C("URL_DOMAIN") . "admin/rightList"),
            array("right", "权限码添加", C("URL_DOMAIN") . "admin/rightEdit"),
            array("edit", "修改管理员", C("URL_DOMAIN") . "admin/reg", 'hide'),
            array("reg", "管理员日志", C("URL_DOMAIN") . "admin/adminlists"),
        );
        return $ment;
    }

    public function save() {
        $path = C("URL_DOMAIN") . "admin/lists";
        $uid = I("id", 0);
        $pmid = isset($_POST['mid']) ? intval($_POST['mid']) : 0;
        $infoguanli = $this->getAdminInfo();
        if ($infoguanli['xianzhi'] == 1) {
            $this->note("非超级管理员不能操作!", $path);
        }
        $password1 = $_POST['password'];
        $password2 = $_POST['pwdconfirm'];
        if (empty($password2) || ($password1 != $password2)) {
            $this->note("2次密码不一致!");
        }
        $password = md5(md5($password2));
        $data = array("userpass" => "$password", "xianzhi" => "$pmid");
        if (!$uid) {
            $weername = $this->safe_replace($_POST['username']);
            if ($weername != $_POST['username'] || empty($weername)) {
                $this->note("用户名格式错误!");
            }
            if ($this->strlen($weername) > 15) {
                $this->note("用户名长度为2-15个字符,1个汉字等于2个字符!");
            }
            if (!$this->checkemail($_POST['email'])) {
                $this->note("邮箱格式错误!");
            }
            $data["mid"] = "$pmid";
            $data["username"] = "$weername";
            $data["loginip"] = $this->huode_ip();
            $data["useremail"] = $_POST[email];
            $data["addtime"] = time();
            $data["logintime"] = "0";
        }
        if ($uid) {
            $ok = D("manage")->where(array("uid" => "$uid"))->save($data);
        } else {
            $ok = D("manage")->add($data);
        }

        if ($ok) {
            $this->note("操作成功!");
        }
        $this->note("操作失败!");
    }

    //修改
    public function edit() {
        $uid = I("id", 0);
        if (intval($uid) > 0) {
            $info = D("manage")->where(array("uid" => "$uid"))->find();
            if (!$info) {
                $this->note("参数错误");
            }
            $this->assign("info", $info);
        }
        $this->assign("ment", $this->returnAdmin());
        $this->display("admin/user.edit");
    }

    public function del() {
        $path = C("URL_DOMAIN") . '/admin/lists';
        $infoguanli = $this->getAdminInfo();
        if ($infoguanli['xianzhi'] == 1) {
            $this->note("非超级管理员不能删除用户!", $path);
        }
        $uid = I("id", 0);
        if ($uid <= 1) {
            $this->note("参数错误");
        }
        $res = D("manage")->where(array("uid" => "$uid"))->delete();
        if ($res) {
            $this->note("删除成功!", $path);
        } else {
            $this->note("删除失败!", $path);
        }
    }

    //后台用户名验证
    public function musername() {
        if (isset($_POST['ajax'])) {
            $weernamelen = 15;
            $pusername = isset($_POST['username']) ? $_POST['username'] : '';
            $changdu = $this->strlen($pusername);
            if ($changdu > $weernamelen || $changdu <= 0) {
                echo 'no';
            } else {
                echo 'yes';
            }
        }
    }

    //权限控制

    public function edit1() {
        $uid = I("id", 0);
        $info = D("manage")->where(array("uid" => "$uid"))->find();
        if (!is_array($info)) {
            $this->note("用户不存在!");
        }
        //权限写入开始
        $infoguanli = $this->getAdminInfo();
        if ($infoguanli["xianzhi"] == '1') {
            $this->note("您不是超级管理员,无权限修改!");
        }
        $infoinfo = explode(",", $info['qx']);
        if (isset($_POST['qx'])) {
            $prom = $_POST['prom'];
            $zz = '';
            foreach ($prom as $val) {
                $zz.=$val . ',';
            }
            $fanwei = substr($zz, 0, strlen($zz) - 1);
            $uid = intval($this->segment(4));
            $ok = $this->db->Query("UPDATE `@#_manage` SET `qx`='$fanwei' WHERE (`uid`='$uid')");
            if ($ok) {
                $this->note("权限设置成功!");
            } else {
                $this->note("权限设置失败!");
            }
        }
        $this->assign($infoinfo);
        //获取权限码分组形势
        $rightData = D("right")->where(array("is_del" => 0))->order("name asc")->select();
        $rightArray = array();
        $rightUndefined = array();
        foreach ($rightData as $key => $item) {
            preg_match('/\[.*?\]/', $item['name'], $localPre);
            if (isset($localPre[0])) {
                $arrayKey = trim($localPre[0], '[]');
                $rightArray[$arrayKey][] = $item;
            } else {
                $rightUndefined[] = $item;
            }
        }
        // dump(stripos($info['qx'],$rightArray['商品'][0]["right"]));
        //echo stripos($info['qx'],$rightArray["商品"]['right']);
        $this->assign("roleRow", $info);
        $this->assign("ment", $this->returnAdmin());
        $this->assign("id", $uid);
        $this->assign("rightArray", $rightArray);
        $this->assign("rightUndefined", $rightUndefined);
        $this->display("user.edit1");
    }

    public function role_edit_act() {
        $id = I("id");
        $right = I("right");
        $data = implode(',', $right);
        $str = "";
        if ($right) {
            $ret = D("right")->where("id in($data) and is_del=0")->select();
            foreach ($ret as $key => $value) {
                $str .= $value['right'] . ",";
            }
            if ($str != "") {
                $str = substr($str, 0, strlen($str) - 1);
            }
        }
        $arr['qx'] = $str;
        $res = D("manage")->where(array("uid" => $id))->save($arr);
        if ($res) {
            $this->note("修改成功");
        }
        $this->note("修改失败");
    }

    public function getClass() {
        $name = I("name");
        $data = getAction("Duipi", $name);
        $res = array("code" => 0, "msg" => "未找到", "data" => array());
        if (!$data) {
            echo json_encode($res);
        }
        $res['data'] = $data;
        $res['code'] = 1;
        $res['msg'] = "成功";
        echo json_encode($res);
    }

    public function rightList() {
        $right = D("right")->where(array("is_del" => 0))->order("name")->select();
        $this->assign("right", $right);
        $this->assign("ment", $this->returnAdmin());
        $this->display("user.right_list");
    }

    public function rightEdit() {
        $id = I("id");
        $right = D("right")->where(array("is_del" => 0, 'id' => $id))->find();
        if ($right) {
            $this->assign("right", explode(",", $right['right']));
            $this->assign("rightName", $right['name']);
            $this->assign("id", $id);
        }
        $this->assign("name", getController("Duipi"));
        $this->assign("ment", $this->returnAdmin());
        $this->display("user.right");
    }

    public function rightSave() {
        $id = I("id");
        $data = I("data");
        $data = array_unique($data);
        if (!$data) {
            $this->note("请添加权限");
        }
        $arr['name'] = I("name");
        $arr['is_del'] = 0;
        if (IS_POST) {
            $arr['right'] = implode(',', $data);
            if ($id) {
                $res = D("right")->where(array("id" => $id))->save($arr);
            } else {
                $res = D("right")->add($arr);
            }
        }
        if ($res) {
            $this->note("操作成功", C("URL_DOMAIN") . "admin/rightList");
        }
        $this->note("操作失败");
    }

    public function rightDel() {
        $id = I("id", 0);
        if ($id > 0) {
            $res = D("right")->where(array("id" => $id))->delete();
        }
        if (!$res) {
            $this->note("操作失败");
        }
        $this->note("操作成功", C("URL_DOMAIN") . "admin/rightList");
    }

    public function adminlists() {
        $db = new \Think\Model;
        if ($_POST['btnSave']) {
            if (empty($_POST['id'])) {
                echo"<script>alert('必须选择一个产品,才可以删除!');history.back(-1);</script>";
                exit;
            } else {
                /* 如果要获取全部数值则使用下面代码 */
                $id = implode(",", $_POST['id']);
                $db->startTrans();
                $str = $db->table("yys_yonghurz")->where("id in ($id)")->delete();
                if ($str) {
                    $this->note("删除成功!");
                    exit;
                } else {
                    $this->note("删除失败!");
                    exit;
                }
            }
        }
        $num = 20;
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $type = I("type");
        if ($type == 'guanli') {
            $num = 20;
            $zongji = D("yonghurz")->where(array("code" => 1))->count();
            $fenye->config($zongji, $num, $fenyenum, "0");
            $huiyuans = D("yonghurz")->where(array("code" => 1))->order("id desc")->limit(($fenyenum - 1) * $num, $num)->select();
        } else {
            $zongji = D("yonghurz")->count();
            $fenye->config($zongji, $num, $fenyenum, "0");
            $huiyuans = D("yonghurz")->order("id desc")->limit(($fenyenum - 1) * $num, $num)->select();
        }
        $this->assign("zongji", $zongji);
        $this->assign("ment", $this->returnAdmin());
        $this->assign("huiyuans", $huiyuans);
        $this->assign("fenye", $fenye);
        $this->display("admin/member.adminlists");
    }

}
