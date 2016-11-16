<?php

/**
 * 第三方接口QQ登录
 * addtime 20160629
 */

namespace Duipi\Controller;

use Think\Controller;

vendor("qq.qqConnectAPI");

class QqloginController extends BaseController {

    private $qc;
    private $db;
    private $qq_openid;

    public function _initialize() {
        $this->qq_header();
        $this->qc = new \QC();
    }

    private function qq_header() {
        $memberone = $this->getUserInfo();
        if ($memberone) {
            header("Location:" . C("URL_DOMAIN") . "user/home");
        }
    }

    //qq登录
    public function index() {
        $this->qc->qq_login();
    }

    //qq回调
    public function callback() {
        $qq_asc = $this->qc->qq_callback();
        $qq_openid = $this->qc->get_openid();
        $this->qc = new \QC($qq_asc, $qq_openid);
        if (empty($qq_openid)) {
            header("Location:" . C("URL_DOMAIN") . "index/index");
            exit;
        }
        $this->qq_openid = $qq_openid;
        $go_user_info = D("yonghu_band")->where(array("b_code" => $qq_openid, "b_type" => "qq"))->find();
        if (!$go_user_info) {
            $this->qq_add_member();
        } else {
            $uid = intval($go_user_info['b_uid']);
            $this->qq_set_member($uid, 'login_bind');
        }
    }

    private function qq_add_member() {
        $go_user_info = $this->qc->get_user_info();
        $huiyuanone = $this->getUserInfo();
        if ($huiyuanone) {
            $go_user_id = $huiyuanone['uid'];
            $qq_openid = $this->qq_openid;
            $go_user_time = time();
            D("yonghu_band")->add(array("b_uid" => $go_user_id, "b_type" => "qq", "b_code" => $qq_openid, "b_time" => $go_user_time));
            $this->note("QQ绑定成功", C("URL_DOMAIN") . "user/home");
            return;
        }

        $go_user_time = time();
        if (!$go_user_info) {
            $go_user_info = array('nickname' => 'QU' . $go_user_time . rand(0, 9));
        }

        $go_y_user = D("yonghu")->where(array("username" => $go_user_info['nickname']))->find();
        if ($go_y_user) {
            $go_user_info['nickname'] .= rand(0, 9);
        }
        $go_user_name = $go_user_info['nickname'];
        $go_user_img = 'photo/member.jpg';
        $go_user_pass = md5('123456');
        $qq_openid = $this->qq_openid;
        $db = new \Think\Model;
        $db->startTrans();
        session_start();
        $session_id = session_id();

        $q1 = D("yonghu")->add(array("username" => $go_user_name, "password" => $go_user_pass, "img" => $go_user_img, "time" => $go_user_time, "session_id" => $session_id, "band" => "qq"));
        $go_user_id = $q1;
        $q2 = D("yonghu_band")->add(array("b_uid" => $go_user_id, "b_type" => "qq", "b_code" => $qq_openid, "b_time" => $go_user_time));
        if ($q1 && $q2) {
            $db->commit();
            $this->qq_set_member($go_user_id, 'add');
        } else {
            $db->rollback();
            $this->note("登录失败!", C("URL_DOMAIN") . "user/home");
        }
    }

    private function qq_set_member($uid = null, $type = 'bind_add_login') {
        $huiyuanone = $this->getUserInfo();
        if ($huiyuanone) {
            $this->note("该QQ号已经被其他用户所绑定！", C("URL_DOMAIN") . 'user/login');
        }
        $huiyuan = D("yonghu")->where(array("uid" => $uid))->field("uid,password,mobile,email")->find();
        cookie('uid', null);
        cookie('ushell', null);
        cookie('UID', null);
        cookie('USHELL', null);
        session_start();
        $session_id = session_id();

        D("yonghu")->where(array("uid" => $uid))->save(array("session_id" => $session_id));
        $s1 = cookie("uid", $this->encrypt($huiyuan['uid']), 60 * 60 * 24 * 7);
        $s2 = cookie("ushell", $this->encrypt(md5($huiyuan['uid'] . $huiyuan['password'] . $huiyuan['mobile'] . $huiyuan['email'])), 60 * 60 * 24 * 7);
        header('location:' . C("URL_DOMAIN") . "user/home");
//        $domain = System::DOWN_sys_config('yuming');
//        if (isset($domain[$_SERVER['HTTP_HOST']])) {
//            if ($domain[$_SERVER['HTTP_HOST']]['m'] == 'mobile') {
//                $callback_url = LOCAL_PATH . "/mobile/home";
//                header('location:' . LOCAL_PATH . '/mobile/home');
//            } else {
//                $callback_url = LOCAL_PATH . "/member/home";
//                header('location:' . LOCAL_PATH . '/member/home');
//            }
//        } else {
//            $callback_url = LOCAL_PATH . "/member/home";
//            header('location:' . LOCAL_PATH . '/member/home');
//        }
    }

    public function qq_set_config() {
        System::DOWN_App_class("admin", G_ADMIN_DIR, 'no');
        $objadmin = new admin();
        $config = System::DOWN_App_config("connect");
        if (isset($_POST['dosubmit'])) {
            $qq_off = intval($_POST['type']);
            $qq_id = $_POST['id'];
            $qq_key = $_POST['key'];
            $config['qq'] = array("off" => $qq_off, "id" => $qq_id, "key" => $qq_key);
            $html = var_export($config, true);
            $html = "<?php return " . $html . "; ?>";
            $path = dirname(__FILE__) . '/control/connect.ini.php';
            if (!is_writable($path))
                _note('Please chmod  connect.ini.php  to 0777 !');
            $ok = file_put_contents($path, $html);
            _note("配置更新成功!");
        }

        $config = $config['qq'];
        include $this->dwt(DOWN_M, 'qq_set_config');
    }

}
