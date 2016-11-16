<?php

/**
 * 用户
 * addtime 2016/02/18
 */

namespace Duipi\Controller;

use Think\Controller;

class UserController extends BaseController {

    public function _initialize() {
        $this->getAdminRight(CONTROLLER_NAME, ACTION_NAME);
//        dump(CONTROLLER_NAME."/".ACTION_NAME);exit;
        $filter = array("login", "wxlogin", "mobilecheck2", "wx_callback", "sendmobcode", "mobileregbind", "userlogin", "register", "mobilecheck", "checkemail", 'sendmobile', "emailok", "emailcheck", "checkname", "userMobile", "uname", "userphotoup", "singphotoup", "wxloginpc", "wx_callbackpc", "fwxlogin", "fwx_callback");
        //ACTION_NAME != "login" && ACTION_NAME != "register" && ACTION_NAME != "mobilecheck" && ACTION_NAME != "checkemail" && ACTION_NAME != "sendmobile" && ACTION_NAME != "emailok" && ACTION_NAME != "emailcheck" && ACTION_NAME != "checkname" && ACTION_NAME != "userMobile"
        $this->userinfo = $this->getUserInfo();
        if ($this->userinfo) {
            $this->assign("huiyuan", $this->userinfo);
        } else if ((!in_array(ACTION_NAME, $filter) && ACTION_NAME != 'mobileregsn' && ACTION_NAME != 'wxlogin' && ACTION_NAME != 'wx_callback')) {
            $this->autoNote("请先登录", C("URL_DOMAIN") . "user/login");
        }
        if (!ismobile()) {
            $this->assign("huiyuan", $this->userinfo);
        }
    }

    public function wxlogin() {
        $user = $this->userinfo;
        session_start();
        $state = md5(uniqid(rand(), TRUE));
        $_SESSION["wxState"] = $state;
        $redirect_uri = urlencode(C("URL_DOMAIN") . "/user/wx_callback/");
        $appid = C("appid");
        $wxurl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_userinfo&state=$state#wechat_redirect";
        header("Location: $wxurl");
    }

//微信登陆返回页面
    public function wx_callback() {
        $id = I("id", 0);
        session_start();

        if (I('state') != $_SESSION['wxState']) {
            $this->notemobile("登录验证失败!", C("URL_DOMAIN") . "/mobile/user/login");
        }

        $code = I('code');
        $appid = C('appid');
        $secret = C('secret');
        $dengluid = C('dengluid');
        $response = file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code=$code&grant_type=authorization_code");
        $jsondecode = json_decode($response, true);
        $wx_openid = $jsondecode["openid"];
        $unionid = $jsondecode["unionid"];
        if (empty($unionid)) {
            $unionid = $wx_openid;
        }

        if (empty($wx_openid)) {
            $this->notemobile("绑定出错，请联系管理员。");
            die;
        }
        $access_token = $jsondecode["access_token"];
        $response = file_get_contents("https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$wx_openid");
        $jsondecode = json_decode($response, true);
        $weixin = new \Claduipi\Wechat\weixin1;
        $iipp = $_SERVER["REMOTE_ADDR"];
        $tit = C('web_name_two');

        $template = array('touser' => $wx_openid,
            'template_id' => $dengluid,
            'url' => C("URL_DOMAIN") . "/user/home",
            'topcolor' => "#7B68EE",
            'data' => array('first' => array('value' => "您好，欢迎登陆" . $tit,
                    'color' => "#743A3A",
                ),
                'keyword1' => array('value' => date('Y-m-d h:i:s', time()),
                    'color' => "#FF0000",
                ),
                'keyword2' => array('value' => $this->huode_ip_dizhi($iipp),
                    'color' => "#0000FF",
                ),
                'remark' => array('value' => "\\n如非本人登陆,请修改密码！",
                    'color' => "#008000",
                ),
            )
        );
        $weixin->send_template_message($template);


        $nickname = $jsondecode["nickname"];
        $go_user_info = M('yonghu_band')->where(array("b_code" => "$unionid", "b_type" => "weixin"))->find();
        if (!empty($go_user_info[b_id]) && !empty($go_user_info[b_data]) && empty($go_user_info[b_uid])) {
            $this->M('yonghu_band')->where(array("b_id" => "$go_user_info[b_id]"))->delete();
        }
        session_start();
        $decode = session("uu");
        $decode2 = session("yaoqing2") ? session("yaoqing2") : 0;
        $decode3 = session("yaoqing3") ? session("yaoqing3") : 0;
        $decode4 = session("yaoqing4") ? session("yaoqing4") : 0;

        $session_id = session_id();
        $ip = $this->huode_ip_dizhi();
        $time = time();
        if (!$go_user_info) {
            $jsondecode["headimgurl"] = str_replace('/0', "/64", $jsondecode["headimgurl"]);
            $ttmm = $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/photo/';
            $bb = session_id();
            $imgg = $bb . '.png';
            $this->put_file_from_url_content($jsondecode["headimgurl"], $imgg, $ttmm);
            // $wximg = file_get_contents($jsondecode["headimgurl"]); 
            //	$ttmm=$_SERVER['DOCUMENT_ROOT'].'/love/uploads/photo/'.time().'.jpg';
            //file_put_contents($ttmm,$wximg); 
            $userpass = md5("123456");
            $go_user_img = 'photo/' . $bb . '.png';
            //$go_user_img  ='photo/member.jpg';
            $go_user_time = time();

            $data = array(
                "username" => $nickname,
                "password" => $userpass,
                "img" => $go_user_img,
                "band" => 'weixin',
                "time" => $go_user_time,
                "money" => '9999',
                "first" => '1',
                "code" => $procode,
                "yaoqing" => $decode,
                "yaoqing2" => $decode2,
                "yaoqing3" => $decode3,
                "yaoqing4" => $decode4,
                "session_id" => $session_id,
                "user_ip" => $ip,
                "login_time" => $time
            );
            $res = D("yonghu")->add($data);
            if ($res) {
                $uid = $res;
            }
            $data2 = array(
                "b_uid" => $uid,
                "b_type" => 'weixin',
                "b_code" => $unionid,
                "b_time" => $go_user_time,
                "b_data" => $wx_openid
            );

            $res2 = D("yonghu_band")->add($data2);
            $member = D('yonghu')->where(array("uid" => $uid))->find();
            if ($res && $res2 && $member) {
                $se1 = cookie("uid", $this->encrypt($member['uid']), 60 * 60 * 24 * 7);
                $se2 = cookie("ushell", $this->encrypt(md5($member['uid'] . $member['password'] . $member['mobile'] . $member['email'])), 60 * 60 * 24 * 7);
                $callback_url = C("URL_DOMAIN") . "/user/home/";
                header("Location:$callback_url");
            }
        } else {
            $uid = $go_user_info["b_uid"];
            //限制登陆补丁
            //限制登陆补丁
            $member = D('yonghu')->where(array("uid" => $uid))->find();

            $se1 = cookie("uid", $this->encrypt($member['uid']), 60 * 60 * 24 * 7);
            $se2 = cookie("ushell", $this->encrypt(md5($member['uid'] . $member['password'] . $member['mobile'] . $member['email'])), 60 * 60 * 24 * 7);



            if (!$member['mobile']) {
                $callback_url = C("URL_DOMAIN") . "/user/home/";

                header("Location:$callback_url");
            } else {
                $callback_url = C("URL_DOMAIN") . "/user/home/";
                header("Location:$callback_url");
            }
        }
    }
//分享系统威信登陆
public function fwxlogin() {
        $user = $this->userinfo;
        session_start();
        $state = md5(uniqid(rand(), TRUE));
        $_SESSION["wxState"] = $state;
        $redirect_uri = urlencode(C("URL_DOMAIN") . "/user/fwx_callback/");
        $appid = C("appid");
        $wxurl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_userinfo&state=$state#wechat_redirect";
        header("Location: $wxurl");
    }

//微信登陆返回页面
    public function fwx_callback() {
        $id = I("id", 0);
        session_start();

        if (I('state') != $_SESSION['wxState']) {
            $this->notemobile("登录验证失败!", C("URL_DOMAIN") . "/f.php/Home/Index/ucenter");
        }

        $code = I('code');
        $appid = C('appid');
        $secret = C('secret');
        $dengluid = C('dengluid');
        $response = file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code=$code&grant_type=authorization_code");
        $jsondecode = json_decode($response, true);
        $wx_openid = $jsondecode["openid"];
        $unionid = $jsondecode["unionid"];
        if (empty($unionid)) {
            $unionid = $wx_openid;
        }

        if (empty($wx_openid)) {
            $this->notemobile("绑定出错，请联系管理员。");
            die;
        }
        $access_token = $jsondecode["access_token"];
        $response = file_get_contents("https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$wx_openid");
        $jsondecode = json_decode($response, true);
        $weixin = new \Claduipi\Wechat\weixin1;
        $iipp = $_SERVER["REMOTE_ADDR"];
        $tit = C('web_name_two');

        $template = array('touser' => $wx_openid,
            'template_id' => $dengluid,
            'url' => C("URL_DOMAIN") . "/f.php/Home/Index/ucenter",
            'topcolor' => "#7B68EE",
            'data' => array('first' => array('value' => "您好，欢迎登陆" . $tit,
                    'color' => "#743A3A",
                ),
                'keyword1' => array('value' => date('Y-m-d h:i:s', time()),
                    'color' => "#FF0000",
                ),
                'keyword2' => array('value' => $this->huode_ip_dizhi($iipp),
                    'color' => "#0000FF",
                ),
                'remark' => array('value' => "\\n如非本人登陆,请修改密码！",
                    'color' => "#008000",
                ),
            )
        );
        $weixin->send_template_message($template);


        $nickname = $jsondecode["nickname"];
        $go_user_info = M('yonghu_band')->where(array("b_code" => "$unionid", "b_type" => "weixin"))->find();
        if (!empty($go_user_info[b_id]) && !empty($go_user_info[b_data]) && empty($go_user_info[b_uid])) {
            $this->M('yonghu_band')->where(array("b_id" => "$go_user_info[b_id]"))->delete();
        }
        session_start();
        $decode = session("uu");
        $decode2 = session("yaoqing2") ? session("yaoqing2") : 0;
        $decode3 = session("yaoqing3") ? session("yaoqing3") : 0;
        $decode4 = session("yaoqing4") ? session("yaoqing4") : 0;

        $session_id = session_id();
        $ip = $this->huode_ip_dizhi();
        $time = time();
        if (!$go_user_info) {
            $jsondecode["headimgurl"] = str_replace('/0', "/64", $jsondecode["headimgurl"]);
            $ttmm = $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/photo/';
            $bb = session_id();
            $imgg = $bb . '.png';
            $this->put_file_from_url_content($jsondecode["headimgurl"], $imgg, $ttmm);
            // $wximg = file_get_contents($jsondecode["headimgurl"]); 
            //	$ttmm=$_SERVER['DOCUMENT_ROOT'].'/love/uploads/photo/'.time().'.jpg';
            //file_put_contents($ttmm,$wximg); 
            $userpass = md5("123456");
            $go_user_img = 'photo/' . $bb . '.png';
            //$go_user_img  ='photo/member.jpg';
            $go_user_time = time();

            $data = array(
                "username" => $nickname,
                "password" => $userpass,
                "img" => $go_user_img,
                "band" => 'weixinfx',
                "time" => $go_user_time,
                "money" => '9999',
                "first" => '1',
                "code" => $procode,
                "yaoqing" => $decode,
                "yaoqing2" => $decode2,
                "yaoqing3" => $decode3,
                "yaoqing4" => $decode4,
                "session_id" => $session_id,
                "user_ip" => $ip,
                "login_time" => $time
            );
            $res = D("yonghu")->add($data);
            if ($res) {
                $uid = $res;
            }
            $data2 = array(
                "b_uid" => $uid,
                "b_type" => 'weixin',
                "b_code" => $unionid,
                "b_time" => $go_user_time,
                "b_data" => $wx_openid
            );

            $res2 = D("yonghu_band")->add($data2);
            $member = D('yonghu')->where(array("uid" => $uid))->find();
			
            if ($res && $res2 && $member) {
				$_SESSION['userdata']=$member;
				$_SESSION['id']=$member['uid'];
                $se1 = cookie("uid", $this->encrypt($member['uid']), 60 * 60 * 24 * 7);
                $se2 = cookie("ushell", $this->encrypt(md5($member['uid'] . $member['password'] . $member['mobile'] . $member['email'])), 60 * 60 * 24 * 7);
			
                $callback_url = C("URL_DOMAIN") . "/f.php/Home/Index/ucenter";
                header("Location:$callback_url");
            }
        } else {
            $uid = $go_user_info["b_uid"];
			
            //限制登陆补丁
            //限制登陆补丁
            $member = D('yonghu')->where(array("uid" => $uid))->find();
			$_SESSION['userdata']=$member;
				$_SESSION['id']=$member['uid'];
            $se1 = cookie("uid", $this->encrypt($member['uid']), 60 * 60 * 24 * 7);
            $se2 = cookie("ushell", $this->encrypt(md5($member['uid'] . $member['password'] . $member['mobile'] . $member['email'])), 60 * 60 * 24 * 7);
			


            if (!$member['mobile']) {
                $callback_url = C("URL_DOMAIN") . "/f.php/Home/Index/ucenter";

                header("Location:$callback_url");
            } else {
                $callback_url = C("URL_DOMAIN") . "/f.php/Home/Index/ucenter";
                header("Location:$callback_url");
            }
        }
    }

	//分享系统威信登陆结束
    private function create_code($len = 6) {
        $randpwd = '';
        for ($i = 0; $i < $len; $i++) {
            $randpwd .= chr(mt_rand(33, 126));
        }
        $randpwd = base64_encode($randpwd);
        return $randpwd;
    }

    public function wxloginpc() {
        $user = $this->userinfo;
        file_put_contents('t.txt', "\n\r\r\n-----pro:" . $pro, FILE_APPEND);


        $wx_set = C("appid1"); //福分/经验
        $code = $this->create_code();

        if (!$user) {
            $code = $this->create_code();
            if ($pro) {
                cookie("procode", $pro);
                $pu = D("activity_code")->where(array("code" => $pro))->find();
                ;
                //	$this->db->YOne("select * from `@#_activity_code` where `code`='$pro'");
                if (empty($pu)) {
                    $pu = D("yonghu")->where(array("code" => $pro))->find();
                    //$this->db->YOne("select * from `@#_yonghu` where `code`='$pro'");
                }
            } else {
                $pro = cookie("procode");
                $pu = D("activity_code")->where(array("code" => $pro))->find();
                //	$this->db->YOne("select * from `@#_activity_code` where `code`='$pro'");
            }
            $p_mobile = $pu['mobile'] ? $pu['mobile'] : '';
            D("activity_code")->add(array("code" => $code, "status" => 0, "pro" => $p_mobile));
            //$this->db->Query("insert into `@#_activity_code`(`code`,`status`,`pro`) values('$code',0,'{$p_mobile}')");
        } else {
            if (empty($user['code'])) {
                $user['code'] = $this->create_code();
                D("yonghu")->where(array("uid" => $user['uid']))->save(array("cade" => $user['code']));
                //$this->db->YOne("update `@#_yonghu` set code='{$user['code']}' where `uid`='{$user['uid']}'");
            }
        }


        if (!empty($user) && !empty($pro) && $pro == $user['code']) {
            $mylink = '';
            include templates("mobile/index", "activity_share");
            die;
        }

        session_start();
        $state = md5(uniqid(rand(), TRUE));
        $_SESSION["wxState"] = $state;
        $redirect_uri = urlencode("" . C("fanhui") . "/user/wx_callbackpc/");
        $wxurl = "https://open.weixin.qq.com/connect/qrconnect?appid=" . C("appid1") . "&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_login&state=$state#wechat_redirect";


        header("Location: $wxurl");
    }

    public function wx_callbackpc() {
        session_start();

        $appid = C('appid1');
        $secret = C('secret1');

        if ($_GET["state"] != $_SESSION["wxState"]) {
            $this->note("登录验证失败!", "" . $wx_set['fanhui'] . "/user/login");
        }


        $code = $_GET["code"];

        //  $procode = $code;
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appid . "&secret=" . $secret . "&code=" . $code . "&grant_type=authorization_code";

        $response = file_get_contents($url);

        $jsondecode = json_decode($response, true);

        $wx_openid = $jsondecode["openid"];

        if (empty($wx_openid)) {
            $this->note("绑定出错，请联系管理员。");
            die;
        }

        $access_token = $jsondecode["access_token"];
        $response = file_get_contents("https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$wx_openid");

        $jsondecode = json_decode($response, true);
        $unionid = $jsondecode["unionid"];

        if (empty($unionid)) {
            $unionid = $wx_openid;
        }



        $nickname = $jsondecode["nickname"];
        $go_user_info = D("yonghu_band")->where(array("b_code" => $unionid, "b_type" => 'weixin'))->find();
        if (!$go_user_info) {
            //$wximg = file_get_contents($jsondecode["headimgurl"]); 
            //$ttmm=$_SERVER['DOCUMENT_ROOT'].'/love/uploads/photo/'.time().'.jpg';
            //file_put_contents($ttmm,$wximg); 
            $userpass = md5("123456");
            $go_user_img = 'photo/member.jpg';
            $go_user_time = time();

            $q1 = D("yonghu")->add(array("username" => $nickname, "password" => $userpass, "img" => $go_user_img, "band" => 'weixin', "time" => $go_user_time, "money" => 0, "first" => 1, "code" => $procode, "yaoqing" => $decode, "yaoqing2" => $decode2, "yaoqing3" => $decode3, "yaoqing4" => $decode4, "session_id" => $session_id, "user_ip" => $ip, "login_time" => $time));


            $uid = $q1;
            //var_dump($uid);
            //exit;
            D("yonghu_band")->add(array("b_uid" => $uid, "b_type" => 'weixin', "b_code" => $unionid, "b_time" => $go_user_time, "b_data" => $wx_openid));

            $member = D("yonghu")->field("uid,password,mobile,email")->where(array("uid" => $uid))->find();

            $se1 = cookie("uid", $this->encrypt($member['uid']), 60 * 60 * 24 * 7);
            $se2 = cookie("ushell", $this->encrypt(md5($member['uid'] . $member['password'] . $member['mobile'] . $member['email'])), 60 * 60 * 24 * 7);
            $callback_url = __ROOT__ . "/user/mailchecking";
            $callback_url = __ROOT__ . "/user/home";



            header("Location:$callback_url");
        } else {
            $uid = $go_user_info["b_uid"];
            $member = D("yonghu")->field("uid,password,mobile,email")->where(array("uid" => $uid))->find();
            $se1 = cookie("uid", $this->encrypt($member['uid']), 60 * 60 * 24 * 7);
            $se2 = cookie("ushell", $this->encrypt(md5($member['uid'] . $member['password'] . $member['mobile'] . $member['email'])), 60 * 60 * 24 * 7);

            if (!$member['mobile']) {
                $callback_url = __ROOT__ . "/user/home";
                header("Location:$callback_url");
            } else {
                $callback_url = __ROOT__ . "/user/home";
                header("Location:$callback_url");
            }
        }
    }

    //返回登录页面
    public function login() {
        if ($this->userinfo) {
            header("Location:" . C("URL_DOMAIN") . "/user/home/");
            exit;
        }
        $weer = $this->userinfo;
//        if (!I("code", "")) {
//            $_yys = C("param_arr");
//            $url =  C("URL_DOMAIN") . '/' . $_yys['url'];
//            $url = rtrim($url, '/');
//            $url .= '/' . base64_encode(trim(G_YYS_REFERER));
//            if ($url != $this->get_LOCAL_url()) {
//                header("Location:" . $url);
//                exit;
//            }
//        }
        if (isset($_POST['submit'])) {
            $weername = $_POST['username'];
            $password = md5($_POST['password']);
            $code = md5(strtoupper($_POST['verify']));
            $logintype = '';
            $db_user = D("yonghu");
            if (strpos($weername, '@') == false) {
                //手机				
                $logintype = 'mobile';
                if (!$this->checkmobile($weername)) {
                    $this->note("手机格式不正确!");
                }
            } else {
                //邮箱
                $logintype = 'email';
                if (!$this->checkemail($weername)) {
                    $this->note("邮箱格式不正确!");
                }
            }
            $huiyuan = $db_user->where(array("$logintype" => "$weername", "password" => "$password"))->find();
            if (!$huiyuan) {
                $this->note("帐号不存在错误!");
            }
            $check = $logintype . 'code';
            if ($huiyuan[$check] != 1) {
                $this->note("帐号未认证");
            }
            if (!is_array($huiyuan)) {
                $this->note("帐号或密码错误", NULL, 3);
            } else {
                $time = time();
                $weer_ip = $this->huode_ip_dizhi();
                $session_id = session_id();
                $db_user->where(array("uid" => "{$huiyuan["uid"]}"))->save(array("session_id" => "$session_id", "user_ip" => "$ip", "login_time" => "$time"));
                //限制登陆补丁
                cookie("uid", $this->encrypt($huiyuan['uid']), 60 * 60 * 24 * 7);
                cookie("ushell", $this->encrypt(md5($huiyuan['uid'] . $huiyuan['password'] . $huiyuan['mobile'] . $huiyuan['email'])), 60 * 60 * 24 * 7);
            }
            header("Location:" . C("URL_DOMAIN") . "index/index");
            exit;
        }
        $this->autoShow("user.login");
    }

    /**
     * 获取地址
     */
    public function getChildArea() {
        $areaID = I("areaID", 0);
        $db = new \Think\Model;
        $city = $db->table("city")->where(array("pid" => (int) $areaID))->select();
        $yys['code'] = 0;
        foreach ($city as $key => $val) {
            $yys['data'][$key]['areaID'] = $val['id'];
            $yys['data'][$key]['areaName'] = $val['name'];
            $yys['data'][$key]['areaPID'] = $val['pid'];
            $yys['data'][$key]['areaZip'] = $val['Zone'];
            $yys['data'][$key]['areaRank'] = 0;
        }
        echo json_encode($yys);
    }

    //手机签到
    public function qiandao() {
        $is_post = I("submit", 0);
        # 签到时间限制（不能夸天哦。。）
        $time_start = '00:01';
        $time_stop = '23:59';
        # 每日签到增加福分
        $time_score = C("fufen_yongjinqd");
        # 连续签到的天数
        $time_day = 30;
        # 连续签到增加的福分
        $time_day_score = rand(10000, 15000);
        $yonghu = $this->userinfo;
        if ($is_post) {
            if ($yonghu['sign_in_date'] == date('Y-m-d')) {
                $this->autoNote("您今天已经过签到了。", C("URL_DOMAIN") . "/user/qiandao");
            } else if (strtotime(date('Y-m-d') . $time_start) > time() || strtotime(date('Y-m-d') . $time_stop) < time()) {
                $this->autoNote("现在不是签到时间！签到时间为{$time_start}点到{$time_stop}点", C("URL_DOMAIN") . "/user/qiandao");
            } else {
                $mysql_model = new \Think\Model;
                if ($yonghu['sign_in_date'] == date('Y-m-d', strtotime('-1 day'))) {
                    # 连续签到
                    if ($yonghu['sign_in_time'] >= $time_day) {
                        $yonghu['sign_in_time'] = 0;
                    }
                    $sign_in_time = $yonghu['sign_in_time'] + 1;
                    $sign_in_time_all = $yonghu['sign_in_time_all'] + 1;
                    $sign_in_date = date('Y-m-d');
                    $score = $yonghu['score'] + $time_score;
                    if ($sign_in_time >= $time_day) {
                        # 领取大礼包了
                        $score += $time_day_score;
                        $big = true;
                    } else {
                        $big = false;
                    }
                    $zhanghao_data = array("uid" => "{$yonghu['uid']}", "type" => '1', "pay" => '福分', "content" => '每日签到', "money" => "$time_score", "time" => time());
                    $mysql_model->table("yys_yonghu_zhanghao")->add($zhanghao_data);
                    $user_data = array('score' => "$score ", 'sign_in_time' => "$sign_in_time", 'sign_in_time_all' => "$sign_in_time_all", 'sign_in_date' => "$sign_in_date");
                    $mysql_model->table("yys_yonghu")->where(array('uid' => "{$yonghu['uid']}"))->save($user_data);
                    if ($big) {
                        $zhanghao_data = array("uid" => "{$yonghu['uid']}", "type" => '1', "pay" => '福分', "content" => '签到大礼包', "money" => "$time_score", "time" => time());
                        $mysql_model->table("yys_yonghu_zhanghao")->add($zhanghao_data);
                        $this->autoNote("签到成功，成功领取{$time_score}福分。<br />恭喜您获得{$time_day_score}福分的大礼包。<br />您的当前福分为{$score}", C("URL_DOMAIN") . "/user/qiandao", 10);
                    } else {
                        $this->autoNote("签到成功，成功领取{$time_score}福分。<br />您的当前福分为{$score}。<br />再连续签到" . ($time_day - $sign_in_time) . "天就能领取大礼包啦，加油！！！", C("URL_DOMAIN") . "/user/qiandao");
                    }
                } else {
                    $sign_in_time = 1;
                    $sign_in_time_all = $yonghu['sign_in_time_all'] + 1;
                    $sign_in_date = date('Y-m-d');
                    $score = $yonghu['score'] + $time_score;
                    $zhanghao_data = array("uid" => "{$yonghu['uid']}", "type" => '1', "pay" => '福分', "content" => '每日签到', "money" => "$time_score", "time" => time());
                    $user_data = array('score' => "$score", 'sign_in_time' => "$sign_in_time", 'sign_in_time_all' => "$sign_in_time_all", 'sign_in_date' => "$sign_in_date");
                    $mysql_model->table("yys_yonghu_zhanghao")->add($zhanghao_data);
                    $mysql_model->table("yys_yonghu")->where(array('uid' => "{$yonghu['uid']}"))->save($user_data);
                    $this->autoNote("签到成功，成功领取{$time_score}福分。<br />您的当前福分为{$score}", C("URL_DOMAIN") . "/user/qiandao");
                }
            }
            die;
        }
        if (!$yonghu['sign_in_date']) {
            $yonghu['sign_in_date'] = '-';
        } else if ($yonghu['sign_in_date'] != date('Y-m-d') && $yonghu['sign_in_date'] != date('Y-m-d', strtotime('-1 day'))) {
            $yonghu['sign_in_time'] = 0;
        }
        $this->assign("time_start", $time_start);
        $this->assign("time_score", $time_score);
        $this->assign("time_stop", $time_stop);
        $this->assign("yonghu", $yonghu);
        $this->autoShow("user.qiandao");
    }

    //获得的商品页面
    public function orderlist() {

        $huiyuan = $this->userinfo;
        $uid = $huiyuan['uid'];
        $biaoti = "获得的商品 - " . C("web_name");

        if (!ismobile()) {
            $zongji = D("yonghu_yys_record")->where("uid='$uid' and huode>'10000000'")->count();
            $fenye = new \Claduipi\Tools\page;
            if (isset($_GET['p'])) {
                $fenyenum = $_GET['p'];
            } else {
                $fenyenum = 1;
            }
            foreach ($record as $ckey => $cord) {
                $jiexiao = $this->huode_shop_if_jiexiao($cord['shopid']);
                if (!$jiexiao) {
                    unset($record[$ckey]);
                }
            }
            $fenye->config($zongji, 10, $fenyenum, "0");
            $record = D("yonghu_yys_record")->where("uid='$uid' and huode>'10000000'")->order("`id` DESC")->limit(($fenyenum - 1) * 10, 10)->select();
            $this->assign("zongji", $zongji);
            $this->assign("fenye", $fenye);
            $this->assign("record", $record);
        }

        $this->assign("uid", $uid);
        $this->assign("biaoti", $biaoti);
        $this->autoShow("user.orderlist");
    }

    public function orderlistzg() {
        $webname = $this->_yys['web_name'];
        $huiyuan = $this->userinfo;
        $uid = $huiyuan['uid'];
        $biaoti = "获得的商品";
        $this->assign("uid", $uid);
        $this->display("mobile/user.orderlistzg");
    }

    public function getMemberCenterUserWinListzg() {
        $fidx = I("FIdx", 0) - 1;
        $eidx = I("EIdx", 0);
        $xiangmuid = I("codeid", 0);
        $huiyuan = $this->userinfo;
        $uid = $huiyuan['uid'];
        $db = new \Think\Model;
        $orderlist['listItems'] = $db->table("yys_shangpinzg")->where("q_uid='$huiyuan[uid]' and q_showtime!='Y'")->field("id,sid,cateid,brandid,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,fahuo")->order("q_end_time DESC")->limit($fidx, $eidx)->select();
        $count = D("shangpinzg")->where("q_uid='$huiyuan[uid]' and q_showtime!='Y'")->select();

        if (!$orderlist['listItems']) {
            $yyslist['code'] = -1;
            $yyslist['tips'] = "暂无记录";
        } else {
            $yyslist['code'] = 0;
            $yyslist['totalCount'] = count($count) - 1;
            foreach ($orderlist['listItems'] as $key => $val) {
                $shopshop = $db->table("yys_yonghu_yys_recordzg")->where("shopid='{$val['id']}' and shopqishu='{$val['qishu']}' and uid='{$huiyuan['uid']}'")->field("uid,shopid,status,id,company_code,company")->find();
                $shaidanyn = $db->table("yys_shai")->where(array("sd_shopid" => "{$shopshop['shopid']}", "sd_userid" => "{$shopshop['uid']}"))->field("sd_id")->find();
                $status = @explode(",", $shopshop['status']);
                if ($status[3] == '未提交地址') {
                    $yyslist['listItems'][$key]['status'] = 0;
                } else if ($status[3] == '已提交地址') {
                    if ($status[1] == '未发货') {
                        $yyslist['listItems'][$key]['status'] = 1;
                    }
                } else if ($status[1] == '已发货' && $status[2] != '已完成' && $status[2] != '已作废') {
                    $yyslist['listItems'][$key]['status'] = 2;
                } else if ($status[1] == '已发货' && $status[2] != '未完成' && $status[2] != '已作废') {
                    $yyslist['listItems'][$key]['status'] = 3;
                    if (empty($shaidanyn)) {
                        $yyslist['listItems'][$key]['status'] = 4;
                    }
                } else if ($status[2] == '已作废') {
                    $yyslist['listItems'][$key]['status'] = 4;
                }
                $yyslist['listItems'][$key]['codeID'] = $shopshop['id'];
                $yyslist['listItems'][$key]['codeIDs'] = $val['id'];
                $yyslist['listItems'][$key]['goodsPic'] = $val['thumb'];
                $yyslist['listItems'][$key]['goodsName'] = $val['title'];
                $yyslist['listItems'][$key]['codeRNO'] = $val['q_user_code'];
                $yyslist['listItems'][$key]['codeRTime'] = $this->microt($val['q_end_time']);
                $yyslist['listItems'][$key]['orderState'] = $yyslist['listItems'][$key]['status'];
                $yyslist['listItems'][$key]['orderNo'] = $shopshop['id'];
                $yyslist['listItems'][$key]['codePeriod'] = $val['qishu'];
                if (empty($shaidanyn)) {
                    $yyslist['listItems'][$key]['IsPostSingle'] = 1;
                } else {
                    $yyslist['listItems'][$key]['IsPostSingle'] = 0;
                }
                $yyslist['listItems'][$key]['yuanjia'] = $val['yuanjia'];
                $yyslist['listItems'][$key]['codePrice'] = $val['money'];
                $yyslist['listItems'][$key]['codeType'] = $val['shopid'];
                $yyslist['listItems'][$key]['orderActDesc'] = $val['shopid'];
                $yyslist['listItems'][$key]['orderAddTime'] = $val['shopid'];
                $yyslist['listItems'][$key]['actAddTime'] = $val['shopid'];
                $yyslist['listItems'][$key]['goodsID'] = $val['id'];
                $yyslist['listItems'][$key]['shenyurenshu'] = $val['shenyurenshu'];
                $yyslist['listItems'][$key]['canyurenshu'] = $val['canyurenshu'];
                $yyslist['listItems'][$key]['buyNum'] = $val['shopid'];
                $yyslist['listItems'][$key]['orderType'] = 0;
                $yyslist['listItems'][$key]['ordersaleprice'] = $val['money'];
                $yyslist['listItems'][$key]['company_code'] = $shopshop['company_code'];
                $yyslist['listItems'][$key]['leixing'] = $val['leixing'];
                $yyslist['listItems'][$key]['ka'] = $val['cardId'];
                $yyslist['listItems'][$key]['mi'] = $val['cardPwd'];
                $yyslist['listItems'][$key]['company'] = $shopshop['company'];
                $yyslist['listItems'][$key]['shaidan'] = $shaidanyn;
            }
        }
        echo json_encode($yyslist);
    }

    /**
     * 获得的商品ajax
     */
    public function getMemberCenterUserWinList() {
        $fidx = I("FIdx", 0) - 1;
        $eidx = I("EIdx", 0);
        $xiangmuid = I("codeid", 0);
        $huiyuan = $this->userinfo;
        $uid = $huiyuan['uid'];
        $db = new \Think\Model;
        $orderlist['listItems'] = $db->table("yys_shangpin")->where("q_uid='{$huiyuan['uid']}' and q_showtime!='Y'")->field("id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,fahuo")->order("q_end_time DESC")->limit($fidx, $eidx)->select();
        if (!$orderlist['listItems']) {
            $yyslist['code'] = -1;
            $yyslist['tips'] = "暂无记录";
        } else {
            $yyslist['code'] = 0;
            $yyslist['totalCount'] = count($orderlist['listItems']);
            foreach ($orderlist['listItems'] as $key => $val) {
                $shopshop = $db->table("yys_yonghu_yys_record")->where("shopid='{$val['id']}' and shopqishu='{$val['qishu']}' and uid='{$huiyuan['uid']}' and huode >'10000000'")->field("uid,shopid,status,id,company_code,company")->find();
                $shaidanyn = $db->table("yys_shai")->where(array("sd_shopid" => "{$shopshop['shopid']}", "sd_userid" => "{$shopshop['uid']}"))->field("sd_id")->find();
                $status = @explode(",", $shopshop['status']);
                if ($status[3] == '未提交地址') {
                    $yyslist['listItems'][$key]['status'] = 0;
                } else if ($status[3] == '已提交地址') {
                    if ($status[1] == '未发货') {
                        $yyslist['listItems'][$key]['status'] = 1;
                    }
                } else if ($status[1] == '已发货' && $status[2] != '已完成' && $status[2] != '已作废') {
                    $yyslist['listItems'][$key]['status'] = 2;
                } else if ($status[1] == '已发货' && $status[2] != '未完成' && $status[2] != '已作废') {
                    $yyslist['listItems'][$key]['status'] = 3;
                    if (empty($shaidanyn)) {
                        $yyslist['listItems'][$key]['status'] = 4;
                    }
                } else if ($status[2] == '已作废') {
                    $yyslist['listItems'][$key]['status'] = 4;
                }
                $yyslist['listItems'][$key]['codeID'] = $shopshop['id'];
                $yyslist['listItems'][$key]['codeIDs'] = $val['id'];
                $yyslist['listItems'][$key]['goodsPic'] = $val['thumb'];
                $yyslist['listItems'][$key]['goodsName'] = $val['title'];
                $yyslist['listItems'][$key]['codeRNO'] = $val['q_user_code'];
                $yyslist['listItems'][$key]['codeRTime'] = $this->microt($val['q_end_time']);
                $yyslist['listItems'][$key]['orderState'] = $yyslist['listItems'][$key]['status'];
                $yyslist['listItems'][$key]['orderNo'] = $shopshop['id'];
                $yyslist['listItems'][$key]['codePeriod'] = $val['qishu'];
                if (empty($shaidanyn)) {
                    $yyslist['listItems'][$key]['IsPostSingle'] = 1;
                } else {
                    $yyslist['listItems'][$key]['IsPostSingle'] = 0;
                }
                $yyslist['listItems'][$key]['yuanjia'] = $val['yuanjia'];
                $yyslist['listItems'][$key]['codePrice'] = $val['money'];
                $yyslist['listItems'][$key]['codeType'] = $val['shopid'];
                $yyslist['listItems'][$key]['orderActDesc'] = $val['shopid'];
                $yyslist['listItems'][$key]['orderAddTime'] = $val['shopid'];
                $yyslist['listItems'][$key]['actAddTime'] = $val['shopid'];
                $yyslist['listItems'][$key]['goodsID'] = $val['id'];
                $yyslist['listItems'][$key]['buyNum'] = $val['shopid'];
                $yyslist['listItems'][$key]['orderType'] = 0;
                $yyslist['listItems'][$key]['ordersaleprice'] = $val['money'];
                $yyslist['listItems'][$key]['company_code'] = $shopshop['company_code'];
                $yyslist['listItems'][$key]['leixing'] = $val['leixing'];
                $yyslist['listItems'][$key]['ka'] = $val['cardId'];
                $yyslist['listItems'][$key]['mi'] = $val['cardPwd'];
                $yyslist['listItems'][$key]['company'] = $shopshop['company'];
                $yyslist['listItems'][$key]['shaidan'] = $shaidanyn;
            }
        }
        echo json_encode($yyslist);
    }

    public function orderDetail() {
        $huiyuan = $this->userinfo;
        $crodid = intval(I("crodid", 0));
        $db = new \Think\Model;
        $records = $db->table("yys_yonghu_yys_record")->where(array("id" => "$crodid", "uid" => "{$huiyuan['uid']}"))->find();
        //dump($records['status']);
        if ($records['status'] == '已付款,未发货,未完成,未提交地址') {
            $fhid = 0;
        } elseif ($records['status'] == '已付款,未发货,未完成,已提交地址') {
            $fhid = 1;
        } elseif ($records['status'] == '已付款,已发货,待收货') {
            $fhid = 2;
        } elseif ($records['status'] == '已付款,已发货,已完成') {
            $fhid = 3;
        } elseif ($records['status'] == '已付款,已发货,已作废') {
            $fhid = 4;
        }
        //特殊处理  编辑状态 
        $fhid = I("ised", 0) ? 0 : $fhid;
        //if (!ismobile()) {
        $tupian = $db->table("yys_shangpin")->where(array("id" => "{$records['shopid']}"))->find();
        $status = @explode(",", $records['status']);
        $uid = $huiyuan['uid'];
        $huiyuan_dizhi = $db->table("yys_yonghu_dizhi")->where(array("uid" => "{$huiyuan['uid']}"))->select();
        $dizhi_sta = $db->table("yys_yonghu_yys_record")->where(array("id" => $crodid))->find();
        foreach ($huiyuan_dizhi as $k => $v) {
            $huiyuan_dizhi[$k] = $this->htmtguolv($v);
        }
        $count = count($huiyuan_dizhi);
        $zongji = D("yonghu_yys_record")->where("uid='{$this->userinfo["uid"]}' and `huode`>'10000000'")->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, 10, $fenyenum, "0");
        $record = D("yonghu_yys_record")->where("uid='{$this->userinfo["uid"]}' and `huode`>'10000000'")->order("id desc")->limit(($fenyenum - 1) * 10, 10)->select();
        foreach ($record as $ckey => $cord) {
            $jiexiao = $this->huode_shop_if_jiexiao($cord['shopid']);
            if (!$jiexiao) {
                unset($record[$ckey]);
            }
        }
        $this->assign("tupian", $tupian);
        $this->assign("dizhi_sta", $dizhi_sta);
        $this->assign("records", $records);

        $this->assign('list', $record); // 赋值数据集
        $this->assign('fenye', $fenye); // 赋值分页输出
//        } else {
//            $huiyuan_dizhi = $db->table("yys_yonghu_dizhi")->where(array("uid" => "{$huiyuan['uid']}", "default" => "Y"))->find();
//           
//        }
        $this->assign("fhid", $fhid);
        $this->assign("huiyuan_dizhi", $huiyuan_dizhi);
        $this->assign("crodid", $crodid);
        $this->autoShow("user.orderDetail");
    }

    public function orderDetailzg() {
        $huiyuan = $this->userinfo;
        $crodid = intval(I("id", 0));
        $db = new \Think\Model;
        $records = $db->table("yys_yonghu_yys_recordzg")->where(array("id" => "$crodid", "uid" => "{$huiyuan['uid']}"))->find();
        $dizhi = D("yonghu_yys_recordzg")->where(array("uid" => $huiyuan["uid"], "status" => "已付款,未发货,未完成,已提交地址"))->find();

        //dump($records['status']);
        if ($records['status'] == '已付款,未发货,未完成,未提交地址') {
            $fhid = 0;
        } elseif ($records['status'] == '已付款,未发货,未完成,已提交地址') {
            $fhid = 1;
        } elseif ($records['status'] == '已付款,已发货,待收货') {
            $fhid = 2;
        } elseif ($records['status'] == '已付款,已发货,已完成') {
            $fhid = 3;
        } elseif ($records['status'] == '已付款,已发货,已作废') {
            $fhid = 4;
        }
        //特殊处理  编辑状态 
        $fhid = I("ised", 0) ? 0 : $fhid;
        $tupian = $db->table("yys_shangpinzg")->where(array("id" => "{$records['shopid']}"))->find();
        $status = @explode(",", $records['status']);
        $uid = $huiyuan['uid'];
        $huiyuan_dizhi = $db->table("yys_yonghu_dizhi")->where(array("uid" => "{$huiyuan['uid']}"))->select();
        $dizhi_sta = $db->table("yys_yonghu_yys_recordzg")->where(array("id" => $crodid))->find();
        foreach ($huiyuan_dizhi as $k => $v) {
            $huiyuan_dizhi[$k] = $this->htmtguolv($v);
        }
        $count = count($huiyuan_dizhi);
        $zongji = D("yonghu_yys_recordzg")->where("uid='{$this->userinfo["uid"]}' and `huode`>'10000000'")->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, 10, $fenyenum, "0");
        $record = D("yonghu_yys_recordzg")->where("uid='{$this->userinfo["uid"]}' and `huode`>'10000000'")->order("id desc")->limit(($fenyenum - 1) * 10, 10)->select();
        foreach ($record as $ckey => $cord) {
            $jiexiao = $this->huode_shop_if_jiexiao($cord['shopid']);
            if (!$jiexiao) {
                unset($record[$ckey]);
            }
        }
        $this->assign("tupian", $tupian);
        $this->assign("dizhi_sta", $dizhi_sta);
        $this->assign("dizhi", $dizhi);
        $this->assign("records", $records);
        $this->assign('list', $record); // 赋值数据集
        $this->assign('fenye', $fenye); // 赋值分页输出
        $this->assign("fhid", $fhid);
        $this->assign("huiyuan_dizhi", $huiyuan_dizhi);
        $this->assign("crodid", $crodid);
        $this->autoShow("user.orderDetailzg");
    }

    public function editUserContact() {
        $mysql_model = new \Think\Model;
        $huiyuan = $this->userinfo;
        $uid = $huiyuan['uid'];
        $jiedao = I("Address", 0);
        $shouhuoren = I("Name", 0);
        $OrderAddress = I("OrderAddress", 0);
        $mobile = I("Phone", 0);
        $retArr = explode(' ', $OrderAddress);
        $sheng = $retArr[0];
        $shi = $retArr[1];
        $xian = $retArr[2];
        $youbian = I("Zip", 0);
        $DID = I("DID", 0);
        $time = time();
        $iii = I("OID", 0);
        $recordmmm = $mysql_model->table("yys_yonghu_yys_record")->where(array("id" => $iii, "uid" => "{$huiyuan['uid']}"))->find();
        $status = $recordmmm['leixing'] == 0 ? "已付款,未发货,未完成,已提交地址" : "已付款,已发货,已完成";
        $inser_dizhi_data = array("uid" => "$uid", "sheng" => "$sheng", "shi" => "$shi", "xian" => "$xian", "jiedao" => "$jiedao", "youbian" => "$youbian", "shouhuoren" => "$shouhuoren", "tell" => "$tell", "mobile" => "$mobile", "qq" => "$qq", "default" => "$default", "time" => "$time", "email" => "$email");
        $ii1 = $mysql_model->table("yys_yonghu_dizhi")->add($inser_dizhi_data);
        $updata_record_data = array('shouhuo' => "1", 'status' => "$status", 'qq' => "$qq", 'youbian' => "$youbian", 'shipRemark' => "$shipRemark", 'shipTime' => "$shipTime", 'email' => "$email", 'tell' => "$tell", 'shouhuoren' => "$shouhuoren", 'mobile' => "$mobile", 'sheng' => "$sheng", 'shi' => "$shi", 'xian' => "$xian", 'jiedao' => "$jiedao", 'fhtime' => "$time", 'wei' => "0");
        $ii2 = $mysql_model->table("yys_yonghu_yys_record")->where(array("id" => "$iii"))->save($updata_record_data);
        if ($ii1) {
            $list['code'] = 0;
        }
        echo json_encode($list);
    }

    public function editUserContactzg() {
        $mysql_model = new \Think\Model;
        $huiyuan = $this->userinfo;
        $uid = $huiyuan['uid'];
        $jiedao = I("Address", 0);
        $shouhuoren = I("Name", 0);
        $OrderAddress = I("OrderAddress", 0);
        $mobile = I("Phone", 0);
        $retArr = explode(' ', $OrderAddress);
        $sheng = $retArr[0];
        $shi = $retArr[1];
        $xian = $retArr[2];
        $youbian = I("Zip", 0);
        $DID = I("DID", 0);
        $time = time();
        $iii = I("OID", 0);
        $recordmmm = $mysql_model->table("yys_yonghu_yys_recordzg")->where(array("id" => $iii, "uid" => "{$huiyuan['uid']}"))->find();
        //$shopinfoss=D("shangpinzg")->where(array("id"=>$recordmmm["shopid"]))->find();
        $status = $recordmmm['leixing'] == 0 ? "已付款,未发货,未完成,已提交地址" : "已付款,已发货,已完成";
        $inser_dizhi_data = array("uid" => "$uid", "sheng" => "$sheng", "shi" => "$shi", "xian" => "$xian", "jiedao" => "$jiedao", "youbian" => "$youbian", "shouhuoren" => "$shouhuoren", "tell" => "$tell", "mobile" => "$mobile", "qq" => "$qq", "default" => "$default", "time" => "$time", "email" => "$email");
        $ii1 = $mysql_model->table("yys_yonghu_dizhi")->add($inser_dizhi_data);
        $updata_record_data = array('shouhuo' => "1", 'status' => "$status", 'qq' => "$qq", 'youbian' => "$youbian", 'shipRemark' => "$shipRemark", 'shipTime' => "$shipTime", 'email' => "$email", 'tell' => "$tell", 'shouhuoren' => "$shouhuoren", 'mobile' => "$mobile", 'sheng' => "$sheng", 'shi' => "$shi", 'xian' => "$xian", 'jiedao' => "$jiedao", 'fhtime' => "$time", 'wei' => "0");
        $ii2 = $mysql_model->table("yys_yonghu_yys_recordzg")->where(array("id" => "$iii"))->save($updata_record_data);
        if ($ii1) {
            $list['code'] = 0;
        }
        echo json_encode($list);
    }

    /**
     * 获取订单收货地址ajax
     */
    public function getAddrByID() {
        $id = I("cid", 0);
        $record = D("yonghu_yys_record");
        $chaxun = $record->where(array("id" => "$id"))->find();
        $yyslist['code'] = 0;
        $yyslist[data][0][contactID] = $chaxun['uid'];
        $yyslist[data][0][contactUserID] = $chaxun['uid'];
        $yyslist[data][0][contactName] = $chaxun['shouhuoren'];
        $yyslist[data][0][contactAddress] = $chaxun['mobile'];
        $yyslist[data][0][contactZip] = $chaxun['youbian'];
        $yyslist[data][0][contactMobile] = $chaxun['mobile'];
        $yyslist[data][0][contactTel] = $chaxun['tell'];
        $yyslist[data][0][contactDefault] = $chaxun['mobile'];
        $yyslist[data][0][areaAID] = $chaxun['mobile'];
        $yyslist[data][0][areaBID] = $chaxun['mobile'];
        $yyslist[data][0][areaCID] = $chaxun['mobile'];
        $yyslist[data][0][areaDID] = $chaxun['mobile'];
        $yyslist[data][0][areaAName] = $chaxun['sheng'];
        $yyslist[data][0][areaBName] = $chaxun['shi'];
        $yyslist[data][0][areaCName] = $chaxun['xian'];
        $yyslist[data][0][areaDName] = $chaxun['jiedao'];
        echo json_encode($yyslist);
    }

    public function address() {
        $huiyuan_dizhi = D("yonghu_dizhi")->where(array("uid" => $this->userinfo['uid']))->select(); //, "default" => "Y"
        foreach ($huiyuan_dizhi as $k => $v) {
            $huiyuan_dizhi[$k] = $this->htmtguolv($v);
        }
        $count = count($huiyuan_dizhi);
        $this->assign("biaoti", "收货地址");
        $this->assign("count", $count);
        $this->assign("huiyuan_dizhi", $huiyuan_dizhi);
        $this->display("index/user.address");
    }

    public function morenaddress() {
        //$huiyuan_dizhi = D("yonghu_dizhi")->where(array("uid" => $this->userinfo['uid']))->select();
        $id = I("id");
        $id = abs(intval($id));
        //       $huiyuan_dizhiss = D("yonghu_dizhi")->where(array("id" => $id))->find();
//        foreach ($huiyuan_dizhi as $dizhi) {
//            if ($dizhi['default'] == 'Y') {
//                $uuu = I("uuu");
//                $mysql_model->Query("UPDATE `@#_yonghu_yys_record` SET `shouhuo`='1',`qq`='$huiyuan_dizhiss[qq]',`youbian`='$huiyuan_dizhiss[youbian]',`shouhuoren`='$huiyuan_dizhiss[shouhuoren]',`mobile`='$huiyuan_dizhiss[mobile]',`sheng`='$huiyuan_dizhiss[sheng]',`shi`='$huiyuan_dizhiss[shi]',`xian`='$huiyuan_dizhiss[xian]',`jiedao`='$huiyuan_dizhiss[jiedao]' where id='" . $uuu . "'");
//                $mysql_model->Query("UPDATE `@#_yonghu_dizhi` SET `default`='N' where uid='" . $huiyuan['uid'] . "'");
//            }
//        }
        //       dump($id);exit;
        D("yonghu_dizhi")->where(array("uid" => $this->userinfo['uid']))->save(array("default" => 'N'));
        if (isset($id)) {
            D("yonghu_dizhi")->where(array("id" => $id))->save(array("default" => 'Y'));
            echo $this->note("修改成功", C("URL_DOMAIN") . "user/address", 3);
        }
    }

    public function deladdress() {
        $huiyuan = $this->userinfo;
        $id = I("id");
        $id = abs(intval($id));
        $dizhi = D("yonghu_dizhi")->where(array("id" => $id, "uid" => $this->userinfo['uid']))->find();
        if (!empty($dizhi)) {
            D("yonghu_dizhi")->where(array("id" => $id, "uid" => $this->userinfo['uid']))->delete();
            header("location:" . C("URL_DOMAIN") . "user/address");
        } else {
            echo $this->note("删除失败", C("URL_DOMAIN") . "user/address", 0);
        }
    }

    public function useraddress() {
        $huiyuan = $this->userinfo;
        $uid = $huiyuan['uid'];
        $iii = I("id");
        if (isset($_POST['submit'])) {
            foreach ($_POST as $k => $v) {
                $_POST[$k] = $this->htmtguolv($v);
            }
            $sheng = isset($_POST['sheng']) ? $_POST['sheng'] : "";
            $shi = isset($_POST['shi']) ? $_POST['shi'] : "";
            $xian = isset($_POST['xian']) ? $_POST['xian'] : "";
            $jiedao = isset($_POST['jiedao']) ? $_POST['jiedao'] : "";
            $youbian = isset($_POST['youbian']) ? $_POST['youbian'] : "";
            $shouhuoren = isset($_POST['shouhuoren']) ? $_POST['shouhuoren'] : "";
            $tell = isset($_POST['tell']) ? $_POST['tell'] : "";
            $mobile = isset($_POST['mobile']) ? $_POST['mobile'] : "";
            $email = isset($_POST['email']) ? $_POST['email'] : "";
            $qq = isset($_POST['qq']) ? $_POST['qq'] : "";
            $time = time();
            if ($sheng == null or $jiedao == null or $shouhuoren == null or $mobile == null) {
                echo "带星号不能为空;";
                exit;
            }
            if (!$this->checkmobile($mobile)) {
                echo "手机号错误;";
                exit;
            }
            $huiyuan_dizhi = D("yonghu_dizhi")->where(array("uid" => $huiyuan['uid']))->find();
            if (!$huiyuan_dizhi) {
                $default = "Y";
            } else {
                $default = "N";
            }

            $data = array(
                "uid" => $uid,
                "sheng" => $sheng,
                "shi" => $shi,
                "xian" => $xian,
                "jiedao" => $jiedao,
                "youbian" => $youbian,
                "shouhuoren" => $shouhuoren,
                "tell" => $tell,
                "qq" => $qq,
                "default" => $default,
                "email" => $email,
                "mobile" => $mobile,
                "time" => $time
            );
            $res = D("yonghu_dizhi")->add($data);
            $this->note("收货地址添加成功", C("URL_DOMAIN") . "user/address", 3);
            //暂时没用到
            //$mysql_model->Query("UPDATE `@#_yonghu_yys_record` SET shouhuo='1',qq='$qq',youbian='$youbian',shouhuoren='$shouhuoren',mobile='$mobile',sheng='$sheng',shi='$shi',xian='$xian',jiedao='$jiedao' where id='" . $iii . "'");
        }
    }

    public function updateddress() {
        $uid = $this->userinfo['uid'];
        $id = I("id");
        $id = abs(intval($id));
        if (isset($_POST['submit'])) {
            $sheng = isset($_POST['sheng']) ? $_POST['sheng'] : "";
            $shi = isset($_POST['shi']) ? $_POST['shi'] : "";
            $xian = isset($_POST['xian']) ? $_POST['xian'] : "";
            $jiedao = isset($_POST['jiedao']) ? $_POST['jiedao'] : "";
            $youbian = isset($_POST['youbian']) ? $_POST['youbian'] : "";
            $shouhuoren = isset($_POST['shouhuoren']) ? $_POST['shouhuoren'] : "";
            $tell = isset($_POST['tell']) ? $_POST['tell'] : "";
            $mobile = isset($_POST['mobile']) ? $_POST['mobile'] : "";
            $qq = isset($_POST['qq']) ? $_POST['qq'] : "";
            $email = isset($_POST['email']) ? $_POST['email'] : "";
            $time = time();
            if ($sheng == null or $jiedao == null or $shouhuoren == null or $mobile == null) {
                echo "带星号不能为空;";
                exit;
            }
            if (!$this->checkmobile($mobile)) {
                echo "手机号错误;";
                exit;
            }
            $data = array(
                "uid" => $uid,
                "sheng" => $sheng,
                "shi" => $shi,
                "xian" => $xian,
                "jiedao" => $jiedao,
                "youbian" => $youbian,
                "shouhuoren" => $shouhuoren,
                "tell" => $tell,
                "qq" => $qq,
                "email" => $email,
                "mobile" => $mobile
            );
            $res = D("yonghu_dizhi")->where(array("id" => $id))->save($data);
            $this->note("修改成功", C("URL_DOMAIN") . "user/address", 3);
        }
    }

    //个人设置
    public function userphoto() {
        $uid = cookie('uid');
        $wehell = cookie('ushell');
        $this->assign("biaoti", "修改头像");
        $this->assign("uid", $uid);
        $this->assign("wehell", $wehell);
        $this->display("index/user.photo");
    }

    //头像上传
    public function userphotoup() {
        if (!empty($_FILES)) {
            $uid = isset($_POST['uid']) ? $_POST['uid'] : NULL;
            $wehell = isset($_POST['ushell']) ? $_POST['ushell'] : NULL;
            $wangpanid = isset($_POST['wangpanid']) ? $_POST['wangpanid'] : NULL;
            $uids = $this->encrypt($uid, 'DECODE');
            $login = $this->checkuser($uid, $wehell);
            $upload = new \Claduipi\Tools\upload;
            $url = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR;
            if (!$wangpanid) {
                $upload->upload_config(array('png', 'jpg', 'jpeg'), 500000, 'touimg');
                $upload->go_upload($_FILES['Filedata'], true);
                $files = $_POST['typeCode'];
                if (!$upload->ok) {
                    echo $upload->error;
                } else {
                    $img = $upload->filedir . "/" . $upload->filename;
                    $size = getimagesize($url . "/touimg/" . $img);
                    $max = 300;
                    $w = $size[0];
                    $h = $size[1];
                    if ($w > 300 or $h > 300) {
                        if ($w > $h) {
                            $w2 = $max;
                            $h2 = intval($h * ($max / $w));
                            $upload->thumbs($w2, $h2, true);
                        } else {
                            $h2 = $max;
                            $w2 = intval($w * ($max / $h));
                            $upload->thumbs($w2, $h2, true);
                        }
                    }
                    echo "touimg/" . $img;
                }
            } else {
                $upload->upload_config(array('png', 'jpg', 'jpeg'), 500000, 'wangpan');
                $upload->go_uploadwp($_FILES['Filedata'], true, $uids);
                $files = $_POST['typeCode'];
                if (!$upload->ok) {
                    echo $upload->error;
                } else {
                    // $img = $url . $upload->filedir . "/" . $upload->filename;
                    $img = $upload->filedir . "/" . $upload->filename;
                    $size = getimagesize($url . "/wangpan/" . $uids . "/" . $img);
                    $max = 300;
                    $w = $size[0];
                    $h = $size[1];
                    if ($w > 300 or $h > 300) {
                        if ($w > $h) {
                            $w2 = $max;
                            $h2 = intval($h * ($max / $w));
                            $upload->thumbs($w2, $h2, true);
                        } else {
                            $h2 = $max;
                            $w2 = intval($w * ($max / $h));
                            $upload->thumbs($w2, $h2, true);
                        }
                    }
                    echo "/wangpan/" . $uids . "/" . $img;
                }
            }
        }
    }

    //头像裁剪
    public function userphotoinsert() {
        $uid = $this->userinfo['uid'];
        if (isset($_POST["submit"])) {
            $tname = trim(str_ireplace(" ", "", $_POST['img']));
            $tname = $this->htmtguolv($tname);
            $url = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR;
            if (!file_exists($url . $tname)) {
                $this->note("头像修改失败", C("URL_DOMAIN") . "user/userphoto", 3);
            }
            $x = (int) $_POST['x'];
            $y = (int) $_POST['y'];
            $w = (int) $_POST['w'];
            $h = (int) $_POST['h'];
            $point = array("x" => $x, "y" => $y, "w" => $w, "h" => $h);
            $upload = new \Claduipi\Tools\upload;
            $upload->thumbs(160, 160, false, $url . $tname, $point);
            $upload->thumbs(80, 80, false, $url . $tname, $point);
            $upload->thumbs(30, 30, false, $url . $tname, $point);
            D("yonghu")->where(array("uid" => $uid))->save(array("img" => $tname));
            $this->note("头像修改成功", C("URL_DOMAIN") . "user/userphoto", 3);
        }
    }

//qq绑定
    public function qqclock() {
        $this->display("index/user.qqclock");
    }

    //个人资料
    public function userModify() {
        $huiyuan_qq = D("yonghu_band")->where(array("b_uid" => $this->userinfo['uid']))->find();
        $this->assign("biaoti", "编辑个人资料");
        $this->assign("huiyuan_qq", $huiyuan_qq);
        $this->display("index/user.modify");
    }

    public function confirmAddr() {
        $yys['code'] = 0;
        echo json_encode($yys);
    }

    //我的一元云购记录
    public function userbuylist() {
        $huiyuan = $this->userinfo;
        $uid = $huiyuan['uid'];
        $biaoti = "一元云购记录";
        if (!ismobile()) {
            $zongji = D("yonghu_yys_record")->where(array("uid" => $uid))->count();
            $fenye = new \Claduipi\Tools\page;
            if (isset($_GET['p'])) {
                $fenyenum = $_GET['p'];
            } else {
                $fenyenum = 1;
            }
            $fenye->config($zongji, 10, $fenyenum, "0");
            $record = D("yonghu_yys_record")->where(array("uid" => $uid))->order("`id` DESC")->limit(($fenyenum - 1) * 10, 10)->select();
            $this->assign("zongji", $zongji);
            $this->assign("fenye", $fenye);
            $this->assign("record", $record);
        }
        $this->assign('biaoti', $biaoti);
        $this->assign('uid', $uid);
        $this->autoShow("user.userbuylist");
    }

    /**
     * 我的一元速购记录ajax
     */
    public function getUserBuyListNew() {
        $huiyuan = $this->userinfo;
        $FIdx = I("FIdx", 0) - 1;
        $EIdx = 10; //safe_replace($this->segment(5));
        $isCount = I("isCount", 0);
        $state = I("state", 0);
        $db = new \Think\Model;
        $filed = "q_user_code,shenyurenshu,uid,q_showtime,shopid as codeid,shopname as goodsname,thumb as goodsPic,sum(gonumber) as buynum,shopqishu as codeperiod,canyurenshu as codesales,zongrenshu as codequantity,uid as userweb,q_end_time,q_uid,sum(gonumber) as gonumber";
        $where = "";
        if ($state == -1) {
            //参与一元速购的商品 全部...
            $where = array("a.uid" => "{$huiyuan['uid']}");
        } elseif ($state == 1) {
            //参与一元速购的商品 进行中...
            $where = "a.uid='{$huiyuan['uid']}' and b.q_end_time is null";
        } else {
            //参与一元速购的商品 已揭晓...
            $where = "a.uid='{$huiyuan['uid']}' and b.q_end_time is not null";
        }
        $yyslistall['listItems'] = $db->table("yys_yonghu_yys_record a")->join("yys_shangpin b on a.shopid=b.id")->where($where)->field($filed)->group("shopid")->order("a.time desc")->limit($FIdx, $EIdx)->select();
        if (!empty($yyslistall['listItems'])) {
            $yyslistall['code'] = 0;
            $yyslistall['count'] = count($yyslistall['listItems']);
            foreach ($yyslistall['listItems'] as $key => $val) {
                $yyslistall['listItems'][$key]['codeRTime'] = $this->microt($val['q_end_time']);
                $yyslistall['listItems'][$key]['codetype'] = '0';
                $yyslistall['listItems'][$key]['q_user_code'] = $val[q_user_code];
                $yyslistall['listItems'][$key]['username'] = $this->huode_user_name($val['q_uid']);
                if ($val['q_end_time'] != '' && $val['q_showtime'] == 'N') {
                    //商品已揭晓
                    $yyslistall['listItems'][$key]['codeState'] = 3;
                    continue;
                } elseif ($val['q_end_time'] != '' && $val['q_showtime'] == 'Y') {
                    //商品购买次数已满
                    $yyslistall['listItems'][$key]['codeState'] = 2;
                    continue;
                } else {
                    $yyslistall['listItems'][$key]['codeState'] = 1;
                    continue;
                }
            }
        } else {
            $yyslistall['code'] = 1;
        }
        $yyslistall['count'] = count($db->table("yys_yonghu_yys_record a")->join("yys_shangpin b on a.shopid=b.id")->where($where)->field($filed)->group("shopid")->select());
        echo json_encode($yyslistall);
    }

    //我的直购记录
    public function getUserBuyListNewzg() {
        $huiyuan = $this->userinfo;
        $FIdx = I("FIdx", 0) - 1;
        $EIdx = 10; //safe_replace($this->segment(5));
        $isCount = I("isCount", 0);
        $state = I("state", 0);
        $db = new \Think\Model;
        $filed = "q_user_code,shenyurenshu,uid,q_showtime,shopid as codeid,shopname as goodsname,thumb as goodsPic,sum(gonumber) as buynum,shopqishu as codeperiod,canyurenshu as codesales,zongrenshu as codequantity,uid as userweb,q_end_time,q_uid,sum(gonumber) as gonumber";
        $where = "";
        if ($state == -1) {
            //参与一元速购的商品 全部...
            $where = array("a.uid" => "{$huiyuan['uid']}");
        } elseif ($state == 1) {
            //参与一元速购的商品 进行中...
            $where = "a.uid='{$huiyuan['uid']}' and b.q_end_time is null";
        } else {
            //参与一元速购的商品 已揭晓...
            $where = "a.uid='{$huiyuan['uid']}' and b.q_end_time is not null";
        }
        $yyslistall['listItems'] = $db->table("yys_yonghu_yys_recordzg a")->join("yys_shangpinzg b on a.shopid=b.id")->where($where)->field($filed)->group("shopid")->order("a.time desc")->limit($FIdx, $EIdx)->select();
        if (!empty($yyslistall['listItems'])) {
            $yyslistall['code'] = 0;
            $yyslistall['count'] = count($yyslistall['listItems']);
            foreach ($yyslistall['listItems'] as $key => $val) {
                $yyslistall['listItems'][$key]['codeRTime'] = $this->microt($val['q_end_time']);
                $yyslistall['listItems'][$key]['codetype'] = '0';
                $yyslistall['listItems'][$key]['q_user_code'] = $val[q_user_code];
                $yyslistall['listItems'][$key]['username'] = $this->huode_user_name($val['q_uid']);
                if ($val['q_end_time'] != '' && $val['q_showtime'] == 'N') {
                    //商品已揭晓
                    $yyslistall['listItems'][$key]['codeState'] = 3;
                    continue;
                } elseif ($val['q_end_time'] != '' && $val['q_showtime'] == 'Y') {
                    //商品购买次数已满
                    $yyslistall['listItems'][$key]['codeState'] = 2;
                    continue;
                } else {
                    $yyslistall['listItems'][$key]['codeState'] = 1;
                    continue;
                }
            }
        } else {
            $yyslistall['code'] = 1;
        }
        $yyslistall['count'] = count($db->table("yys_yonghu_yys_record a")->join("yys_shangpin b on a.shopid=b.id")->where($where)->field($filed)->group("shopid")->select());
        echo json_encode($yyslistall);
    }

    /**
     * 用户首页 积分明细
     */
    public function jifen() {
        $this->display("mobile/user.jifen"); // 输出模板
    }

    /**
     * 用户首页 购买商品详情
     */
    public function buyDetail() {
        $huiyuan = $this->userinfo;
        $xiangmuid = intval(I("goodsid", 0));
        $xiangmulist = D("yonghu_yys_record a")->field("*,a.time as timego,sum(gonumber) as gonumber")->join("yys_shangpin b on a.shopid=b.id")->where("a.uid='$huiyuan[uid]' and b.id='$xiangmuid'")->group("a.id")->order("a.time")->select();
        if (!empty($xiangmulist)) {
            if ($xiangmulist[0]['q_end_time'] != '' && $xiangmulist[0]['q_showtime'] != 'Y') {
                //商品已揭晓
                $xiangmulist[0]['codeState'] = '已揭晓...';
                $xiangmulist[0]['class'] = 'z-ImgbgC02';
            } elseif ($xiangmulist[0]['shenyurenshu'] == 0) {
                //商品购买次数已满
                $xiangmulist[0]['codeState'] = '已满员...';
                $xiangmulist[0]['class'] = 'z-ImgbgC01';
            } else {
                //进行中
                $xiangmulist[0]['codeState'] = '进行中...';
                $xiangmulist[0]['class'] = 'z-ImgbgC01';
            }
            $bl = ($xiangmulist[0]['canyurenshu'] / $xiangmulist[0]['zongrenshu']) * 100;
        }
        $gg = 0;
        foreach ($xiangmulist as $val) {
            $gg+=$val['gonumber'];
        }
        $this->assign("bl", $bl);
        $this->assign("gg", $gg);
        $this->assign("xiangmulist", $xiangmulist);
        $this->display("mobile/user.userbuyDetail");
    }

    /**
     * 账户管理 用户首页
     */
    public function userbalance() {
        //$biaoti = "账户记录";
        if (ismobile()) {
            $account = D("yonghu_zhanghao")->where(array("uid" => "{$this->userinfo['uid']}", "pay" => "账户"))->order("time DESC")->select();
            $czsum = 0;
            $xfsum = 0;
            if (!empty($account)) {
                foreach ($account as $key => $val) {
                    if ($val['type'] == 1) {
                        $czsum+=$val['money'];
                    } else {
                        $xfsum+=$val['money'];
                    }
                }
            }
            $this->assign("czsum", $czsum);
            $this->assign("xfsum", $xfsum);
            $this->display("mobile/user.userbalance");
        } else {
            $huiyuan = $this->userinfo;
            $uid = $huiyuan['uid'];
            $biaoti = "账户记录 - " . C("web_name");
            $zongji = D("yonghu_zhanghao")->where(array('uid' => $uid, 'pay' => '账户'))->count();
            $fenye = new \Claduipi\Tools\page;
            if (isset($_GET['p'])) {
                $fenyenum = $_GET['p'];
            } else {
                $fenyenum = 1;
            }
            $fenye->config($zongji, 20, $fenyenum, "0");
            $account = D("yonghu_zhanghao")->where(array("uid" => "{$this->userinfo['uid']}", "pay" => "账户"))->order("time DESC")->limit(($fenyenum - 1) * 20, 20)->select();
            $this->assign("huiyuan", $huiyuan);
            $this->assign("account", $account);
            $this->assign("zongji", $zongji);
            $this->assign("fenye", $fenye);
            $this->display("index/user.userbalance");
        }
    }

    //修改密码
    public function password() {
        $this->assign("biaoti", "密码修改");
        $this->autoShow("user.password");
    }

    /**
     * 退出登录
     */
    public function cook_end() {
        cookie("uid", null);
        cookie("ushell", null);
        $this->autoNote("退出成功", C("URL_DOMAIN") . "/mobile/index");
    }

    public function guanzhu2() {
        $code = $_GET['code'];
        $yaoqing = System::DOWN_App_config("user_fufen");
        $weixin = file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . C("appid") . "&secret=" . C("secret") . "&code=" . $code . "&grant_type=authorization_code"); //通过code换取网页授权access_token
        $jsondecode = json_decode($weixin, true);
        $wx_openid = $jsondecode["openid"];
        $appid = C("appid");
        $secret = C("secret");
        $jssdk = new \Claduipi\Wechat\JSSDK($appid, $secret);
        $access_token = $jssdk->getAccessToken();
        $response = file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$wx_openid&lang=zh_CN");
        $rr = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$wx_openid&lang=zh_CN";
        $jsondecode = json_decode($response, true);
        $subscribe = $jsondecode[subscribe];
        $huiyuan = $this->userinfo;
        if ($subscribe != 1) {
            D("yonghu")->where(array("uid" => $huiyuan['uid']))->save(array("gonggonghao" => 0));
            header("location: " . C("URL_DOMAIN") . "user/gong/");
            exit;
        } else {
            $guanzhu = C("fufen_guanzhu");
            if (empty($huiyuan["gonggonghao"])) {
                D("yonghu")->where(array("uid" => $huiyuan['uid']))->save(array("gonggonghao" => 1, "score" => $guanzhu));

                header("location: " . C("URL_DOMAIN") . "user/home/");
            }
        }
        //include templates("/mobile/member","guanzhu");
    }
    public function gong() {
        $this->display("mobile/index.gong");
        include templates("mobile/index", "gong");
    }

    /**
     * 用户中心
     */
    public function home() {
        //判断关注公共号
        if (C('gonggonghao') && !$this->userinfo["gonggonghao"] && $this->userinfo["band"] == 'weixin' && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            $appid = C("appid");
            $wxurl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=http://$_SERVER[SERVER_NAME]/user/guanzhu2&response_type=code&scope=snsapi_base&state=$state#wechat_redirect";
            header("Location: $wxurl");
        }
        //判断结束
        $huiyuan = $this->userinfo;
        if (empty($huiyuan['mobile']) && C('wxb')) {
            header("Location:" . C("URL_DOMAIN") . "user/mobilebind");
            exit;
        }
        $biaoti = "用户中心";
        //获取一元云购等级  一元云购新手  一元云购小将==
        $quanzi = D("quan_tiezi")->order("id DESC")->limit("5")->select();
        $dongtai = D("quan_tiezi")->where(array("qzid" => 1))->where("title is not null")->order("time DESC")->limit("10")->select();
        $huiyuandj = D("yonghu_group")->select();
        $jingyan = $huiyuan['jingyan'];
        if (!empty($huiyuandj)) {
            foreach ($huiyuandj as $key => $val) {
                if ($jingyan >= $val['jingyan_start'] && $jingyan <= $val['jingyan_end']) {
                    $huiyuan['yungoudj'] = $val['name'];
                }
            }
        }
        $dengji_1 = D("yonghu_group")->where("jingyan_start <= '$jingyan' and jingyan_end >= $jingyan")->find();
        $max_jingyan_id = $dengji_1['groupid'];
        $dengji_2 = D("yonghu_group")->where("groupid > '$max_jingyan_id'")->order("groupid asc")->find();
        if ($dengji_2) {
            $dengji_x = $dengji_2['jingyan_start'] - $jingyan;
        } else {
            $dengji_x = $dengji_1['jingyan_end'] - $jingyan;
        }
        $this->assign("keys", "我的云购");
        $this->assign("quanzi", $quanzi);
        $this->assign("huiyuan", $huiyuan);
        $this->assign("dongtai", $dongtai);
        $this->assign("dengji_1", $dengji_1);
        $this->assign("dengji_2", $dengji_2);
        $this->assign("dengji_x", $dengji_x);
        $this->assign("biaoti", $biaoti);
        $this->autoShow("user.index");
    }

    //登录成功后
    public function loginok() {
        $weer['Code'] = 0;
        echo json_encode($weer);
    }

    //login
    public function userlogin() {
        $weername = I("username", "");
        $password = md5(I("password", ""));
        $verify = md5(strtoupper(I("verify", "")));
        $logintype = '';
        if (strpos($weername, '@') == false) {
            //手机				
            $logintype = 'mobile';
        } else {
            //邮箱
            $logintype = 'email';
        }
        $db_user = D("yonghu");
        $huiyuan = $db_user->where(array("$logintype" => "$weername", "password" => "$password"))->find();

        if (!$huiyuan) {
            //帐号不存在错误
            $weer['state'] = 1;
            $weer['num'] = -2;
        }
        $check = $logintype . "code";




        if (!is_array($huiyuan)) {
            //帐号或密码错误
            $weer['state'] = 1;
            $weer['num'] = -1;
        } else {
            //登录成功 
            //限制登陆补丁
            $session_id = session_id();
            $ip = $this->huode_ip_dizhi();
            $time = time();
            if ($huiyuan[$check] != 1) {
                $weer['state'] = 2; //未验证
            } else if ($huiyuan[dongjie]) {

                cookie("uid", "", time() - 3600);
                cookie("ushell", "", time() - 3600);
                $weer['state'] = 1; //未验证
                $weer['num'] = -3; //未验证
                //var_dump($weer['dongjie');
//exit;
            } else {
                $db_user->where(array("uid" => "{$huiyuan["uid"]}"))->save(array("session_id" => "$session_id", "user_ip" => "$ip", "login_time" => "$time"));
                //限制登陆补丁
                cookie("uid", $this->encrypt($huiyuan['uid']), 60 * 60 * 24 * 7);
                cookie("ushell", $this->encrypt(md5($huiyuan['uid'] . $huiyuan['password'] . $huiyuan['mobile'] . $huiyuan['email'])), 60 * 60 * 24 * 7);
                $weer['state'] = 0;
            }
        }
        //	var_dump($weer['state']);
        //exit;
        echo json_encode($weer);
    }

    //返回注册页面
    public function register() {
        if ($this->userinfo) {
            header("Location:" . C("URL_DOMAIN") . "/user/home");
            exit;
        }
        if (isset($_POST['submit'])) {
            $name = isset($_POST['name']) ? $_POST['name'] : "";
            $weerpassword = isset($_POST['userpassword']) ? $_POST['userpassword'] : "";
            $weerpassword2 = isset($_POST['userpassword2']) ? $_POST['userpassword2'] : "";
            $code = md5(strtoupper($_POST['verify']));
            if ($name == null or $weerpassword == null or $weerpassword2 == null) {
                $this->note("帐号密码不能为空", null, 3);
            }
            if (!($this->checkmobile($name) or $this->checkemail($name))) {
                $this->note("帐号不是手机或邮箱", null, 3);
            }
            if (strlen($weerpassword) < 6 || strlen($weerpassword) > 20) {
                $this->note("密码小于6位或大于20位", null, 3);
            }
            if ($weerpassword != $weerpassword2) {
                $this->note("两次密码不一致", null, 3);
            }
            $regtype = null;
            if ($this->checkmobile($name)) {
                $config_mobile = C("mobile");
                $regtype = 'mobile';
                $cfg_mobile_type = 'cfg_mobile_' . $config_mobile['cfg_mobile_on'];
                $config_mobile = $config_mobile[$cfg_mobile_type];
                if (empty($config_mobile['mid']) && empty($config_email['mpass'])) {
                    $this->note("系统短信配置不正确!");
                }
            }
            if ($this->checkemail($name)) {
                $regtype = 'email';
                if (!C('user') && !C('pass')) {
                    $this->note("系统邮箱配置不正确!");
                }
            }
            //验证注册类型
            $regtypes = 'reg_' . $regtype;
            if (empty($regtype) || C($regtypes) == 0) {
                if ($regtype == 'email') {
                    $this->note("网站未开启邮箱注册!", null, 3);
                }
                if ($regtype == 'mobile') {
                    $this->note("网站未开启手机注册!", null, 3);
                }
                $this->note("您注册的类型不正确!", null, 3);
            }
            $huiyuan = D("yonghu")->where("$regtype = '$name' or reg_key = '$name'")->find();
            if (is_array($huiyuan) && $huiyuan[$regtype] == $name) {
                $this->note("该账号已被注册!");
            }
            $register_type = 'def';
            if (is_array($huiyuan) && $huiyuan['reg_key'] == $name) {
                $b_uid = $huiyuan['uid'];
                $b_user = D("yonghu_band")->where(array("b_uid" => $b_uid))->find();
                if (is_array($b_user)) {
                    $this->note("该账号已被注册!");
                }
                $register_type = 'for'; //未注册成功在次注册
            }
            $time = time();
            $ip = $this->huode_ip_dizhi();
            $weerpassword = md5($weerpassword);
            $codetype = $regtype . 'code';
            $decode = $this->encrypt(I("code", 0), "DECODE");
            $data = array("password" => $weerpassword, "user_ip" => $ip, "img" => "photo/member.jpg", "emailcode" => "-1", "mobilecode" => "-1", "reg_key" => $name, "yaoqing" => $decode, "time" => $time);

            $res = D("yonghu")->add($data);
            if ($res) {
                $check_code = serialize(array("name" => $name, "time" => $time));
                $check_code = $this->encrypt($check_code, "ENCODE", '', 3600 * 24);
                header("Location: " . C("URL_DOMAIN") . "/user/" . $regtype . "check" . "/code/" . $check_code . "/mobile/" . $name);
                exit;
            } else {
                $this->note("注册失败!");
            }
        }
        $biaoti = "注册" . C("web_name");
        $this->autoShow("user.register");
    }

//重新发送验证码
    public function sendmobileAjax() {
        $name = I("code");
        $huiyuan = D("yonghu")->where(array("mobile" => $name))->find();
        if (!$huiyuan) {
            $sendmobile['state'] = 1;
            echo json_encode($sendmobile);
            exit;
        }
        $checkcode = explode("|", $huiyuan['mobilecode']);
        $times = time() - $checkcode[1];
        if ($times > 120) {
            $sendok = R("Tools/send_mobile_reg_code", array($name, $huiyuan['uid']));
            if ($sendok[0] != 1) {
                $sendmobile['state'] = 1;
                echo json_encode($sendmobile);
                exit;
            }
            //成功
            $sendmobile['state'] = 0;
            echo json_encode($sendmobile);
            exit;
        } else {
            $sendmobile['state'] = 1;
            echo json_encode($sendmobile);
            exit;
        }
    }

    //重发手机验证码
    public function sendmobile() {
        $check_code = $this->encrypt(I("code"), "DECODE");
        $check_code = unserialize($check_code);
        if (!$check_code || !isset($check_code['name']) || !isset($check_code['time'])) {
            $this->note("参数不正确或者验证已过期!", C("URL_DOMAIN") . 'user/register');
        }
        $name = $check_code['name'];

        $huiyuan = D("yonghu")->where(array("reg_key" => $check_code["name"], "time" => $check_code["time"]))->find();
        if (!$huiyuan)
            $this->note("参数不正确!");
        if ($huiyuan['mobilecode'] == '1') {
            $this->note("该账号验证成功,请直接登录！", C("URL_DOMAIN") . 'user/login');
        }
        $checkcode = explode("|", $huiyuan['mobilecode']);
        $times = time() - $checkcode[1];
        if ($times > 120) {
            $sendok = R("Tools/send_mobile_reg_code", array($huiyuan['reg_key'], $huiyuan['uid']));
            if ($sendok[0] != 1) {
                $this->note("短信发送失败,代码:" . $sendok[1]);
                exit;
            }
            $this->note("正在重新发送...", C("URL_DOMAIN") . "user/mobilecheck/code/" . I("code"));
        } else {
            $this->note("重发时间间隔不能小于2分钟!", C("URL_DOMAIN") . "user/mobilecheck/code/" . I("code"));
        }
    }

    public function mobilecheck() {
        if (ismobile()) {
            $biaoti = "验证手机";
            $name = I("code");

            $huiyuan = D("yonghu")->where(array("mobile" => $name))->find();
            if (!$huiyuan)
                $this->notemobile("参数不正确!");
            if ($huiyuan['mobilecode'] == 1) {
                $this->notemobile("该账号验证成功", C("URL_DOMAIN") . "/mobile/mobile");
            }
            $content = $name;
            $this->assign("name", $name);
        } else {
            $biaoti = "手机认证 - " . C("web_name");
            $check_code = $this->encrypt(I("code"), "DECODE");
            $check_code = unserialize($check_code);


            if (!$check_code || !isset($check_code['name']) || !isset($check_code['time'])) {
                $this->note("参数不正确或者验证已过期!", C("URL_DOMAIN") . 'user/register');
            }
            $name = $check_code['name'];
            $huiyuan = D("yonghu")->where(array("reg_key" => $check_code["name"], "time" => $check_code["time"]))->find();
            if (!$huiyuan)
                $this->note("未知的来源!", C("URL_DOMAIN") . 'user/register');
            if ($huiyuan['mobilecode'] == '1') {
                $this->note("该账号验证成功", C("URL_DOMAIN") . 'user/login');
            }
            $content = $huiyuan['reg_key'];
        }

        if ($huiyuan['mobilecode'] == '-1') {
            $sendok = R("Tools/send_mobile_reg_code", array($content, $huiyuan['uid']));
            if ($sendok[0] != 1) {
                $this->note($sendok[1]);
            }
            header("location:" . C("URL_DOMAIN") . "user/mobilecheck/code/" . I("code"));
            exit;
        }

        if (isset($_POST['submit'])) {
            $checkcodes = isset($_POST['checkcode']) ? $_POST['checkcode'] : $this->note("参数不正确!");
            if (strlen($checkcodes) != 6)
                $this->note("验证码输入不正确!");
            $weercode = explode("|", $huiyuan['mobilecode']);
            if ($checkcodes != $weercode[0])
                $this->note("验证码输入不正确!");
            $time = time();
            if ($huiyuan['yaoqing']) {
                $yaoqinguid = $huiyuan['yaoqing'];
                //福分、经验添加
                if (C('f_visituser')) {
                    D("yonghu_zhanghao")->add(array("uid" => $yaoqinguid, "type" => "1", "pay" => "福分", "content" => "邀请好友奖励", "money" => C("f_visituser"), "time" => $time));
                }
                D("yonghu")->where(array("uid" => $yaoqinguid))->setInc('score', C("f_visituser"));
                D("yonghu")->where(array("uid" => $yaoqinguid))->setInc('jingyan', C("z_visituser"));
            }
            D("yonghu")->where(array("uid" => $huiyuan["uid"]))->save(array("mobilecode" => "1", "mobile" => $huiyuan['reg_key']));
            //2014-11-26  lq 手机注册时，自动加福分和经验
            D("yonghu_zhanghao")->add(array("uid" => $huiyuan["uid"], "type" => "1", "pay" => "福分", "content" => "手机认证完善奖励", "money" => C("f_phonecode"), "time" => $time));
            D("yonghu_zhanghao")->add(array("uid" => $huiyuan["uid"], "type" => "1", "pay" => "经验", "content" => "手机认证完善奖励", "money" => C("z_phonecode"), "time" => $time));
            D("yonghu")->where(array("uid" => $huiyuan['uid']))->setInc('score', C("f_phonecode"));
            D("yonghu")->where(array("uid" => $huiyuan['uid']))->setInc('jingyan', C("z_phonecode"));
            cookie("uid", $this->encrypt($huiyuan['uid']), 60 * 60 * 24 * 7);
            cookie("ushell", $this->encrypt(md5($huiyuan['uid'] . $huiyuan['password'] . $huiyuan['reg_key'] . $huiyuan['email'])), 60 * 60 * 24 * 7);
            $this->note("验证成功", C("URL_DOMAIN") . "user/login");
        }
        $enname = substr($name, 0, 3) . '****' . substr($name, 7, 10);
        $time = 120;
        $this->assign("weerid", $weerid);
        $this->assign("enname", $enname);
        $this->assign("namestr", I("code"));
        $this->autoShow("user.mobilecheck");
    }

    /* 用户注册邮箱注册验证邮件发送 */

    public function emailcheck() {
        $biaoti = "邮箱验证 -" . C("web_name");
        $check_code = $this->encrypt(I("code"), "DECODE");
        $check_code = unserialize($check_code);
        if (!$check_code || !isset($check_code['name']) || !isset($check_code['time'])) {
            $this->note("参数不正确或者验证已过期!", C("URL_DOMAIN") . 'user/register');
        }
        $info = D("yonghu")->where(array("reg_key" => $check_code["name"], "time" => $check_code["time"]))->find();
        if (!$info)
            $this->note("未知的来源!", C("URL_DOMAIN") . 'user/register');
        $youjianurl = explode("@", $info['reg_key']);
        $name = $info['reg_key'];
        $enname = I("code");
        $reg_message = '';
        if ($info['emailcode'] == '1')
            $this->note("恭喜您,验证成功!", C("URL_DOMAIN") . 'user/login');
        if ($info['emailcode'] == '-1') {
            $reg_message = R("Tools/send_email_reg", array($info['reg_key'], $info['uid']));
        } elseif ((time() - $check_code['time']) > 3600) {
            //未验证时间大于1小时 重发邮件
            $reg_message = R("Tools/send_email_reg", array($info['reg_key'], $info['uid']));
        }
        $this->assign("weerid", $weerid);
        $this->assign("youjianurl", $youjianurl);
        $this->assign("name", $name);
        $this->assign("enname", $enname);
        $this->assign("reg_message", $reg_message);
        $this->display("index/user.emailcheck");
    }

    /*
      邮箱验证成功页面
     */

    public function emailok() {
        $check_code = $this->encrypt(I("code"), "DECODE");

        if (!is_array($check_code)) {
            $c = explode(",", $check_code);
            $check_code = array();
            $check_code['email'] = $c['0'];
            $check_code['name'] = $c['1'];
            $check_code['time'] = $c['2'];
            $huiyuan = D("yonghu")->where(array("uid" => $check_code['name']))->find();
            $huiyuan['reg_key'] = $check_code['email'];
            if (!isset($check_code['email']) || !isset($check_code['name']) || !isset($check_code['time'])) {

                $this->note("未知的来源!", C("URL_DOMAIN") . 'user/register');
            }
        } else {
            $check_code = unserialize($check_code);
            $huiyuan = D("yonghu")->where(array("reg_key" => $check_code["email"], "time" => $check_code["time"]))->find();
            if (!isset($check_code['email']) || !isset($check_code['code']) || !isset($check_code['time'])) {

                $this->note("未知的来源!", C("URL_DOMAIN") . 'user/register');
            }
        }





        $sql_code = $check_code['code'] . '|' . $check_code['time'];


        /* if(!$huiyuan)$this->note("未知的来源!",C("URL_DOMAIN"),'/register'); */
        $timec = time() - $check_code['time'];
        if ($timec < (3600 * 24)) {
            $biaoti = "邮件激活成功";
            $tiebu = "完成注册";
            $success = "邮件激活成功";
            $this->assign("success", $success);
            $this->assign("tiebu", $tiebu);
            if ($huiyuan['yaoqing']) {
                $yaoqinguid = $huiyuan['yaoqing'];
                //福分、经验添加
                if (C('f_visituser')) {
                    D("yonghu_zhanghao")->add(array("uid" => $yaoqinguid, "type" => "1", "pay" => "福分", "content" => "邀请好友奖励", "money" => C("f_visituser"), "time" => $time));
                }
                D("yonghu")->where(array("uid" => $yaoqinguid))->setInc('score', C("f_visituser"));
                D("yonghu")->where(array("uid" => $yaoqinguid))->setInc('jingyan', C("z_visituser"));
            }

            D("yonghu")->where(array("uid" => $huiyuan["uid"]))->save(array("emailcode" => "1", "email" => $huiyuan['reg_key']));
//            //2014-11-26  lq 手机注册时，自动加福分和经验
//            D("yonghu_zhanghao")->add(array("uid" => $huiyuan["uid"], "type" => "1", "pay" => "福分", "content" => "手机认证完善奖励", "money" => C("f_phonecode"), "time" => $time));
//            D("yonghu_zhanghao")->add(array("uid" => $huiyuan["uid"], "type" => "1", "pay" => "经验", "content" => "手机认证完善奖励", "money" => C("z_phonecode"), "time" => $time));
//            D("yonghu")->where(array("uid" => $huiyuan['uid']))->setInc('score', C("f_phonecode"));
//            D("yonghu")->where(array("uid" => $huiyuan['uid']))->setInc('jingyan', C("z_phonecode"));
            cookie("uid", $this->encrypt($huiyuan['uid']), 60 * 60 * 24 * 7);
            cookie("ushell", $this->encrypt(md5($huiyuan['uid'] . $huiyuan['password'] . $huiyuan['mobile'] . $huiyuan['reg_key'])), 60 * 60 * 24 * 7);
            $this->display("index/user.emailok");
        } else {
            $biaoti = "邮箱验证失败";
            $tiebu = "验证失败,请重发验证邮件";
            $guoqi = "对不起，验证码已过期或不正确！";
            D("yonghu")->where(array("uid" => $huiyuan["uid"]))->save(array("emailcode" => "-1"));
            $name = array("name" => $huiyuan['reg_key'], "time" => $huiyuan['time']);
            $name = $this->encrypt(serialize($name), "ENCODE");
            $this->assign("name", $name);
            $this->assign("guoqi", $guoqi);
            $this->display("index/user.emailok");
        }
    }

    /*
     * 	重发验证邮件
     */

    public function sendemail() {
        $check_code = $this->encrypt(I("code"), "DECODE");
        $check_code = unserialize($check_code);
        if (!$check_code || !isset($check_code['name']) || !isset($check_code['time'])) {
            $this->note("参数不正确或者验证已过期1!", C("URL_DOMAIN") . 'user/register');
        }
        $huiyuan = D("yonghu")->where(array("reg_key" => $check_code["name"], "time" => $check_code["time"]))->find();
        if (!$huiyuan)
            $this->note("错误的来源!", C("URL_DOMAIN") . 'user/register');
        if ($huiyuan['emailcode'] == '1')
            $this->note("邮箱已验证", C("URL_DOMAIN") . 'user/index');
        D("yonghu")->where(array("uid" => $huiyuan["uid"]))->save(array("emailcode" => "-1"));
        $this->note("正在重新发送...", C("URL_DOMAIN") . "user/emailcheck/" . I("codes"));
        exit;
    }

    //充值记录
    public function getUserRecharge() {
        $huiyuan = $this->userinfo;
        $FIdx = I("FIdx", 0);
        $EIdx = 10; //safe_replace($this->segment(5));
        $db_yonghu_zhanghao = D("yonghu_zhanghao");
        $Rechargelist = $db_yonghu_zhanghao->where(array("uid" => "{$huiyuan['uid']}", "pay" => "账户", "type" => "1"))->order("time DESC")->select();
        $Recharge['listItems'] = $db_yonghu_zhanghao->where(array("uid" => "{$huiyuan['uid']}", "pay" => "账户", "type" => "1"))->order("time DESC")->limit($FIdx, $EIdx)->select();
        if (empty($Recharge['listItems'])) {
            $Recharge['code'] = 1;
        } else {
            foreach ($Recharge['listItems'] as $key => $val) {
                $Recharge['listItems'][$key]['time'] = date("Y-m-d H:i:s", $val['time']);
            }
            $Recharge['code'] = 0;
        }
        $Recharge['count'] = count($Rechargelist);
        echo json_encode($Recharge);
    }

    //消费记录
    public function getUserConsumption() {
        $huiyuan = $this->userinfo;
        $FIdx = I("FIdx", 0);
        $EIdx = 10; //safe_replace($this->segment(5));
        $db_yonghu_zhanghao = D("yonghu_zhanghao");
        $Consumptionlist = $db_yonghu_zhanghao->where(array("uid" => "{$huiyuan['uid']}", "pay" => "账户", "type" => "-1"))->select();
        $Consumption['listItems'] = $db_yonghu_zhanghao->where(array("uid" => "{$huiyuan['uid']}", "pay" => "账户", "type" => "-1"))->order("time DESC")->limit($FIdx, $EIdx)->select();
        if (empty($Consumption['listItems'])) {
            $Consumption['code'] = 1;
        } else {
            foreach ($Consumption['listItems'] as $key => $val) {
                $Consumption['listItems'][$key]['time'] = date("Y-m-d H:i:s", $val['time']);
            }
            $Consumption['code'] = 0;
        }
        $Consumption['count'] = count($Consumptionlist);
        echo json_encode($Consumption);
    }

    //充值记录
    public function getUserRecharge1() {
        $huiyuan = $this->userinfo;
        $FIdx = I("FIdx", 0);
        $EIdx = 10; //safe_replace($this->segment(5));
        $db = D("yonghu_zhanghao1");
        $Rechargelist = $db->where(array("uid" => "{$huiyuan['uid']}", "pay" => "积分"))->select();
        $Recharge['listItems'] = $db->where(array("uid" => "{$huiyuan['uid']}", "pay" => "积分"))->order("time DESC")->limit($FIdx, $EIdx)->select();
        if (empty($Recharge['listItems'])) {
            $Recharge['code'] = 1;
        } else {
            foreach ($Recharge['listItems'] as $key => $val) {
                $Recharge['listItems'][$key]['time'] = date("Y-m-d H:i:s", $val['time']);
            }
            $Recharge['code'] = 0;
        }
        $Recharge['count'] = count($Rechargelist);
        echo json_encode($Recharge);
    }

    /**
     * 积分明细ajax
     */
    public function getUserConsumption1() {
        $huiyuan = $this->userinfo;
        $FIdx = I("FIdx", 0);
        $EIdx = 10; //safe_replace($this->segment(5));
        $db = D("yonghu_cashout1");
        $Consumptionlist = $db->where(array("uid" => "{$huiyuan['uid']}"))->select();
        $Consumption['listItems'] = $db->where(array("uid" => "{$huiyuan['uid']}"))->order("time DESC")->limit($FIdx, $EIdx)->select();
        if (empty($Consumption['listItems'])) {
            $Consumption['code'] = 1;
        } else {
            foreach ($Consumption['listItems'] as $key => $val) {
                $Consumption['listItems'][$key]['time'] = date("Y-m-d H:i:s", $val['time']);
                $Consumption['listItems'][$key]['shenhe'] = $val['shenhe'];
                $Consumption['listItems'][$key]['username'] = $val['username'];
                $Consumption['listItems'][$key]['bankname'] = $val['bankname'];
            }
            $Consumption['code'] = 0;
        }
        $Consumption['count'] = count($Consumptionlist);
        echo json_encode($Consumption);
    }

    //修改密码

    public function updateUserPwd() {
        $huiyuan = $this->userinfo;
        $oldpass = htmlspecialchars(I("userOldPwd", ""));
        $newpass = md5(htmlspecialchars(I("userNewPwd", "")));
        $db = D("yonghu");
        $cha = $db->where(array("uid" => "{$huiyuan['uid']}"))->find();
        if ($cha['password'] == md5($oldpass)) {
            $password = $db->where(array("uid" => "{$huiyuan['uid']}"))->save(array("password" => "$newpass"));
            $yys['code'] = 0;
            cookie("uid", null);
            cookie("ushell", null);
        } else if ($cha['password'] != md5($oldpass)) {
            $yys['code'] = 1;
        } else {
            $yys['code'] = 5;
        }
        echo json_encode($yys);
    }

//pc
    public function userpassword() {
        $huiyuan = $this->userinfo;
        $password = isset($_POST['password']) ? $_POST['password'] : "";
        $weerpassword = isset($_POST['userpassword']) ? $_POST['userpassword'] : "";
        $weerpassword2 = isset($_POST['userpassword2']) ? $_POST['userpassword2'] : "";
        if ($password == null or $weerpassword == null or $weerpassword2 == null) {
            echo "密码不能为空;";
            exit;
        }
        if (strlen($_POST['password']) < 6 || strlen($_POST['password']) > 20) {
            echo "密码不能小于6位或者大于20位";
            exit;
        }
        if ($_POST['userpassword'] !== $_POST['userpassword2']) {
            echo "二次密码不一致";
            exit;
        }
        $password = md5($password);
        $weerpassword = md5($weerpassword);
        if ($huiyuan['password'] != $password) {
            echo $this->note("原密码错误", null, 3);
        } else {
            D("yonghu")->where(array("uid" => "{$huiyuan['uid']}"))->save(array("password" => "$weerpassword"));
            echo $this->note("密码修改成功", C("URL_DOMAIN") . "user/login", 3);
        }
    }

    //晒单         
    public function singlelist() {
        if (!ismobile()) {
            $huiyuan = $this->userinfo;
            $biaoti = "我的晒单";
            $cord = D("yonghu_yys_record")->where(array('uid' => $huiyuan[uid], 'houde' => '10000000'))->select();
            //已晒单		
            $shaidan = D("shai")->where(array('sd_userid' => $huiyuan[uid]))->order('sd_time')->limit(10)->select();
            $sd_id = $r_id = array();
            foreach ($shaidan as $sd) {
                $sd_id[] = $sd['sd_shopid'];
            }
            foreach ($cord as $rd) {
                if (!in_array($rd['shopid'], $sd_id)) {
                    $r_id[] = $rd['shopid'];
                }
            }
            if (!empty($r_id)) {
                $rd_id = implode(",", $r_id);
                $rd_id = trim($rd_id, ',');
            } else {
                $rd_id = "0";
            }
            if ($rd_id) {
                $zongji = D("yonghu_yys_record")->where("shopid in ($rd_id) and uid='$huiyuan[uid]' and huode>'10000000'")->count();
            } else {
                $zongji = 0;
            }

            $fenye = new \Claduipi\Tools\page;
            if (isset($_GET['p'])) {
                $fenyenum = $_GET['p'];
            } else {
                $fenyenum = 1;
            }
            $fenye->config($zongji, 10, $fenyenum, "0");
            if ($rd_id) {
                $record = D("yonghu_yys_record")->field('shopid,id,leixing')->where("shopid in ($rd_id) and uid=$huiyuan[uid] and huode>'10000000'")->order('id desc')->limit(($fenyenum - 1) * $num, $num)->select();
            } else {
                $record = array();
            }
            $this->assign("fenyenum", $fenyenum);
            $this->assign("shaidan", $shaidan);
            $this->assign("record", $record);
            $this->assign("fenye", $fenye);
            $this->assign("zongji", $zongji);
        }

        $this->autoShow("user.singlelist");
    }

    //晒单上传
    public function singphotoup() {
        if (!empty($_FILES)) {
            $upload = new \Claduipi\Tools\upload;
            $upload->upload_config(array('png', 'jpg', 'jpeg', 'gif'), 1000000, 'shaidan');
            $upload->go_upload($_FILES['Filedata']);
            if (!$upload->ok) {
                echo $this->note($upload->error, null, 3);
            } else {
                $img = $upload->filedir . "/" . $upload->filename;
                $size = getimagesize(YYS_UPLOADS_PATH . "/shaidan/" . $img);
                $max = 700;
                $w = $size[0];
                $h = $size[1];
                if ($w > 700) {
                    $w2 = $max;
                    $h2 = $h * ($max / $w);
                    $upload->thumbs($w2, $h2, 1);
                }
                echo trim("shaidan/" . $img);
            }
        }
    }

    public function singdel() {
        $action = isset($_GET['action']) ? $_GET['action'] : null;
        $filename = isset($_GET['filename']) ? $_GET['filename'] : null;
        if ($action == 'del' && !empty($filename)) {
            $filename = __PUBLIC__ . 'shaidan/' . $filename;
            $size = getimagesize($filename);
            $filetype = explode('/', $size['mime']);
            if ($filetype[0] != 'image') {
                return false;
                exit;
            }
            unlink($filename);
            exit;
        }
    }

    //添加晒单
    public function singleinsert() {
        if (ismobile()) {
            $huiyuan = $this->userinfo;
            $uid = cookie('uid');
            $wehell = cookie('ushell');
            $biaoti = "添加晒单";
            $showtime = $uid . date("YmdHisms");
//            $lujing = $_SERVER['DOCUMENT_ROOT'] . "/love/uploads/shaidan/" . date("Ymd") . "/";
//            $url = $_SERVER['DOCUMENT_ROOT'] . "/love/uploads/shaidan/" . date("Ymd") . "/" . $showtime;
            $dest_folder = __PUBLIC__ . "shaidan\\" . date("Ymd") . "\\";   //上传图片保存的路径 图片放在跟你upload.php同级的picture文件夹里
            $arr = array();  //定义一个数组存放上传图片的名称方便你以后会用的，如果不用那就不写
            $count = 0;
            if (!file_exists($dest_folder)) {
                mkdir($dest_folder);
            }
            $ggs = "";
            foreach ($_FILES["Filedata"]["error"] as $key => $error) {
                if ($error == UPLOAD_ERR_OK) {
                    $tmp_name = $_FILES["Filedata"]["tmp_name"][$key];
                    $name = $_FILES["Filedata"]["name"][$key];
                    $name1 = str_replace('.', '', $name);
                    $uploadfile = $dest_folder . $key . $name1 . $showtime . '.png';
                    $url1 = "shaidan/" . date("Ymd") . "/" . $key . $name1 . $showtime;
                    $uploadfile1 = $url1 . '.png';
                    move_uploaded_file($tmp_name, $uploadfile);
                    $arr[$count] = $uploadfile;
                    $ggs.=$uploadfile1 . ";";
                    $count++;
                }
            }
            $recordid = I("id", 0);
            $shopid = $recordid;
            $shaidan = D("yonghu_yys_record")->where(array('shopid' => $recordid, 'uid' => $huiyuan['uid']))->find();
            if (!$shaidan) {
                $this->note("该商品您不可晒单!");
            }
            $shaidanyn = D("shai")->where(array('sd_shopid' => $recordid, 'sd_userid' => $huiyuan['uid']))->find();
            if ($shaidanyn) {
                $this->note("不可重复晒单!");
            }
            $ginfo = D("shangpin")->where(array('id' => $recordid))->field('id,sid,qishu')->find();
            if (!$ginfo) {
                $this->note("该商品已不存在!");
            }
            if (isset($_POST['submit'])) {
                if ($_POST['title'] == null)
                    $this->note("标题不能为空");
                if ($_POST['content'] == null)
                    $this->note("内容不能为空");
                $upload = new \Claduipi\Tools\upload;
                $img = $_POST['fileurl_tmp'];
                $num = $count;
                $pic = "";
                for ($i = 0; $i < $num; $i++) {
                    $pic.=trim($img[$i]) . ";";
                }
                $src = trim($img[0]);
                $size = getimagesize(__PUBLIC__ . $src);
                $width = 220;
                $height = $size[1] * ($width / $size[0]);

                $src_houzhui = $upload->thumbs($width, $height, false, __PUBLIC__ . $src);
                $thumbs = $this->tubimg($src, $width, $height);
                $sd_userid = $this->userinfo['uid'];
                $sd_shopid = $recordid;
                $sd_title = $_POST['title'];
                $sd_thumbs = $url1 . '.png';
                if (empty($ggs)) {
                    $this->note("图片不能为空");
                    exit;
                }
                $tags = array(
                    "'<iframe[^>]*?>.*?</iframe>'is",
                    "'<frame[^>]*?>.*?</frame>'is",
                    "'<script[^>]*?>.*?</script>'is",
                    "'<head[^>]*?>.*?</head>'is",
                    "'<title[^>]*?>.*?</title>'is",
                    "'<meta[^>]*?>'is",
                    "'<link[^>]*?>'is",
                    "'<p[^>]*?>'is",
                    "'</p[^>]*?>'is",
                );
                $sd_content1 = stripslashes($_POST['content']);
                $sd_content = preg_replace($tags, "", $sd_content1);
                $sd_photolist = $ggs;
                $sd_time = time();
                $sd_ip = $this->huode_ip_dizhi();
                D("shai")->add(array("sd_userid" => $sd_userid, "sd_shopid" => $sd_shopid, "sd_ip" => $sd_ip, "sd_title" => $sd_title, "sd_thumbs" => $sd_thumbs, "sd_content" => $sd_content, "sd_photolist" => $sd_photolist, "sd_time" => $sd_time));
                $this->notemobile("晒单分享成功", C("URL_DOMAIN") . "user/singlelist");
            }
            if ($recordid > 0) {

                $shaidingdan = D("yonghu_yys_record")->where(array('id' => $recordid))->find();
                $shopid = $shaidingdan['shopid'];
                $this->assign("ginfo", $ginfo);
                $this->assign("wehell", $wehell);
                $this->assign("wehell", $wehell);
                $this->assign("wehell", $wehell);
                $this->assign("uid", $uid);
                $this->assign("recordid", $recordid);
                $this->display("mobile/user.singleinsert");
                exit;
            } else {
                $this->note("页面错误");
            }
        }


        $huiyuan = $this->userinfo;
        $uid = cookie('uid');
        $wehell = cookie('ushell');

        $biaoti = "添加晒单";
        $recordid = I("id", 0);
        $shopid = $recordid;
        $shaidan = D("yonghu_yys_record")->where(array('shopid' => $recordid, 'uid' => $huiyuan['uid']))->find();
        if (!$shaidan) {
            $this->note("该商品您不可晒单!");
        }
        $shaidanyn = D("shai")->where(array('sd_shopid' => $recordid, 'sd_userid' => $huiyuan['uid']))->find();
        if ($shaidanyn) {
            $this->note("不可重复晒单!");
        }
        $ginfo = D("shangpin")->where(array('id' => $recordid))->field('id,sid,qishu')->find();
        if (!$ginfo) {
            $this->note("该商品已不存在!");
        }
        if (isset($_POST['submit'])) {
            if ($_POST['title'] == null)
                $this->note("标题不能为空");
            if ($_POST['content'] == null)
                $this->note("内容不能为空");
            if (!isset($_POST['fileurl_tmp'])) {
                $this->note("图片不能为空");
            }
            $upload = new \Claduipi\Tools\upload;
            $img = $_POST['fileurl_tmp'];
            $num = count($img);
            $pic = "";
            for ($i = 0; $i < $num; $i++) {
                $pic.=trim($img[$i]) . ";";
            }
            $src = trim($img[0]);
            if (!file_exists(__PUBLIC__ . $src)) {
                $this->note("晒单图片不正确");
            }
            $size = getimagesize(__PUBLIC__ . $src);
            $width = 220;
            $height = $size[1] * ($width / $size[0]);

            $src_houzhui = $upload->thumbs($width, $height, false, __PUBLIC__ . $src);
            $thumbs = $src . "_" . intval($width) . intval($height) . "." . $src_houzhui;
            $sd_userid = $this->userinfo['uid'];
            $sd_shopid = $ginfo['id'];
            $sd_shopsid = $ginfo['sid'];
            $sd_qishu = $ginfo['qishu'];
            $sd_title = $this->htmtguolv($_POST['title']);
            $sd_thumbs = $thumbs;
            $tags = array(
                "'<iframe[^>]*?>.*?</iframe>'is",
                "'<frame[^>]*?>.*?</frame>'is",
                "'<script[^>]*?>.*?</script>'is",
                "'<head[^>]*?>.*?</head>'is",
                "'<title[^>]*?>.*?</title>'is",
                "'<meta[^>]*?>'is",
                "'<link[^>]*?>'is",
            );
            $sd_content1 = stripslashes($_POST['content']);
            $sd_content = preg_replace($tags, "", $sd_content1);
            $sd_photolist = $pic;
            $sd_time = time();
            $sd_ip = $this->huode_ip_dizhi();
            D("shai")->add(array("sd_userid" => $sd_userid, "sd_shopid" => $sd_shopid, "sd_shopsid" => $sd_shopsid, "sd_qishu" => $sd_qishu, "sd_ip" => $sd_ip, "sd_title" => $sd_title, "sd_thumbs" => $sd_thumbs, "sd_content" => $sd_content, "sd_photolist" => $sd_photolist, "sd_time" => $sd_time));
            $this->note("晒单分享成功", C("URL_DOMAIN") . "user/singlelist");
        }
        $this->assign("wehell", $wehell);
        $this->assign("uid", $uid);
        $this->assign("recordid", $recordid);
        $this->display("index/member.singleinsert");
    }

    //获取晒单
    public function getUserPostList() {
        $FIdx = I("FIdx", 0);
        $EIdx = 10; //safe_replace($this->segment(5));
        $huiyuan = $this->userinfo;
        $db = D("shai a");
        $post = $db->join("yys_shangpin b on a.sd_shopid=b.id")->where(array("a.sd_userid" => "{$huiyuan['uid']}"))->order("a.sd_time desc")->select();
        $postlist['listItems'] = $db->join("yys_shangpin b on a.sd_shopid=b.id")->where(array("a.sd_userid" => "{$huiyuan['uid']}"))->order("a.sd_time desc")->limit($FIdx, $EIdx)->select();
        if (empty($postlist['listItems'])) {
            $postlist['code'] = 1;
        } else {
            foreach ($postlist['listItems'] as $key => $val) {
                $postlist['listItems'][$key]['sd_time'] = date('Y-m-d H:i', $val['sd_time']);
            }
            $postlist['code'] = 0;
        }
        $postlist['postCount'] = count($post);
        echo json_encode($postlist);
    }

    //获取未晒单
    public function getUserUnPostList() {
        $FIdx = I("FIdx", 0);
        $EIdx = 10; //safe_replace($this->segment(5));
        $huiyuan = $this->userinfo;
        //获得的商品
        $orderlist = D("yonghu_yys_record a")->join("yys_shangpin b on a.shopid=b.id")->where(array("b.q_uid" => "{$huiyuan['uid']}"))->order("a.time desc")->select();
        //获取晒单
        $postlist = D("shai a")->join("yys_shangpin b on a.sd_shopid=b.id")->where(array("a.sd_userid" => "{$huiyuan['uid']}"))->order("a.sd_time desc")->select();
        $huoid = '';
        $sd_id = $r_id = array();
        foreach ($postlist as $sd) {
            $sd_id[] = $sd['sd_shopid'];
        }
        foreach ($orderlist as $rd) {
            if (!in_array($rd['shopid'], $sd_id)) {
                $r_id[] = $rd['shopid'];
            }
        }
        if (!empty($r_id)) {
            $rd_id = implode(",", $r_id);
            $rd_id = trim($rd_id, ',');
        } else {
            $rd_id = "0";
        }
        //未晒单
        $unpost = D("shangpin")->where("id in($rd_id)")->order("id")->select();
        $unpostlist['listItems'] = D("shangpin")->where("id in($rd_id)")->order("id")->limit($FIdx, $EIdx)->select();
        if (empty($unpostlist['listItems'])) {
            $unpostlist['code'] = 1;
        } else {
            foreach ($unpostlist['listItems'] as $key => $val) {
                $unpostlist['listItems'][$key]['q_end_time'] = $this->microt($val['q_end_time']);
            }
            $unpostlist['code'] = 0;
        }
        $unpostlist['unPostCount'] = count($unpost);
        echo json_encode($unpostlist);
    }

    public function jifenduihuan() {
        $huiyuan = $this->userinfo;
        $uid = $huiyuan['uid'];
        $time = time();
        $biaoti = "充值到余额";
        $amount = I("amount", "");
        $pay_zhifu_name = '积分';
        if ($amount) {
            //$amount='0.1';
            if (floor($amount) != $amount) {
                $this->notemobile("不能是小数", C("URL_DOMAIN") . "/user/jifenduihuan", 3);
                exit;
            }
            if ($amount > $huiyuan['money1'] || $huiyuan['money1'] <= 0) {
                $this->notemobile("积分不足", C("URL_DOMAIN") . "/user/jifenduihuan", 3);
                exit;
            }
            $db_user = D("yonghu");
            $db_user->where(array("uid" => "{$huiyuan['uid']}"))->setDec('money1', $amount);
            $db_user->where(array("uid" => "{$huiyuan['uid']}"))->setInc('money', $amount);
            D("yonghu_zhanghao1")->add(array("uid" => "$uid", "type" => "-1", "pay" => "$pay_zhifu_name", "content" => "积分转账到余额", "money" => "$amount", "time" => "$time"));
            $this->notemobile("充值到余额成功", C("URL_DOMAIN") . "/user/jifenduihuan", 3);
        }
        $this->display("mobile/user.jifenduihuan");
    }

    //转帐

    function zhuanzhang() {
        $yonghu = $this->userinfo;
        $title = "转帐";
        if (isset($_POST['submit1'])) {
            $tmoney = I("money", 0);
            $tuser = I("txtBankName", 0);
            $tuser1 = I("txtBankName1", 0);
            if ($tuser != $tuser1) {
                $this->notemobile("请确认转帐帐号正确", null, 3);
            }
            if ($tmoney <= 0) {
                $this->notemobile("非法金额", null, 3);
            }
            if (empty($tmoney) || empty($tuser))
                $this->notemobile("转入用户和金额不得为空", null, 3);
            if ($tmoney > $yonghu['money'])
                $this->notemobile("转入金额不得大于帐户余额", null, 3);
            $db_user = D("yonghu");
            $user = $db_user->where(array("email" => "$tuser"))->find();
            if (empty($user))
                $user = $db_user->where(array("mobile" => "$tuser"))->find();
            if (empty($user))
                $this->notemobile("转入用户不存在", null, 3);
            $uid = $yonghu['uid'];
            $tuid = $user['uid'];
            if ($uid == $tuid)
                $this->notemobile("不能给自己转帐", null, 3);
            $time = time();
            $cmoney = $yonghu['money'] - $tmoney;
            $ctmoney = $user['money'] + $tmoney;
            $name = $this->huode_user_name($uid, 'username', 'all');
            $tname = $this->huode_user_name($tuid, 'username', 'all');
            $db_user->where(array("uid" => "$uid"))->save(array("money" => "$cmoney"));
            $db_user->where(array("uid" => "$tuid"))->save(array("money" => "$ctmoney"));
            $db_record = D("yonghu_op_record");
            $inser_data = array("uid" => "$uid", "username" => "$name", "deltamoney" => "-$tmoney", "premoney" => "{$yonghu['money']}", "money" => "$cmoney", "time" => $time, "guanlian" => $tname);
            $db_record->add($inser_data);
            $inser_data = array("uid" => "$tuid", "username" => "$tname", "deltamoney" => "$tmoney", "premoney" => "{$user['money']}", "money" => "$ctmoney", "time" => $time, "guanlian" => $tname);
            $db_record->add($inser_data);
            $db_zhanghao = D("yonghu_zhanghao");
            $inser_data = array("uid" => "$uid", "type" => "-1", "pay" => "账户", "content" => "转出到{$tname}", "money" => "$tmoney", "time" => "$time");
            $db_zhanghao->add($inser_data);
            $inser_data = array("uid" => "$tuid", "type" => "1", "pay" => "账户", "content" => "由{$name}转入", "money" => "$tmoney", "time" => "$time");
            $db_zhanghao->add($inser_data);
            $this->notemobile("给" . $tname . "的" . $tmoney . "元冲值成功!", null, 3);
        }
        $this->assign("yonghu", $yonghu);
        $this->display("mobile/user.zhuanzhang");
    }

    //佣金提交
    public function memberCenterApplyToBank() {
        $huiyuan = $this->userinfo;
        $money = htmlspecialchars(I('money'));
        $userName = htmlspecialchars(I('userName'));
        $bankName = htmlspecialchars(I('bankName'));
        $subBank = htmlspecialchars(I('subBank'));
        $bankNo = htmlspecialchars(I('bankNo'));
        $phone = htmlspecialchars(I('phone'));
        $db_user = D("yonghu");
        $cha = $db_user->where(array("uid" => "{$huiyuan['uid']}"))->find();
        $ye = $db_user->where(array("uid" => "{$huiyuan['uid']}"))->field("yongjin")->find();
        $time = time();
        $cashouthdtotal = $ye['yongjin'];  //佣金余额
        $uid = $huiyuan['uid'];
        if ($money < 100) {
            $yys['code'] = -1;
        } elseif ($cashouthdtotal < $money) {
            $yys['code'] = 0;
        } else {
            //插入提现申请表  这里不用在佣金表中插入记录 等后台审核才插入
            $cashout_data = array("uid" => "$uid", "money" => "$money", "username" => "$userName", "bankname" => "$bankName", "branch" => "$subBank", "banknumber" => "$bankNo", "linkphone" => "$phone", "time" => $time);
            $mrecode1 = D("yonghu_cashout")->add($cashout_data);
            $leavemoney = $huiyuan['money'] + $money;
            $yongjins = $huiyuan['yongjin'] - $money;
            $mrecode2 = $db_user->where(array("uid" => "$uid"))->save(array("money" => "$leavemoney", "yongjin" => "$yongjins"));
            if ($mrecode1 && $mrecode2) {
                $yys['code'] = 1;
            }
        }
        echo json_encode($yys);
    }

    public function memberCenterApplyToAccount() {
        $huiyuan = $this->userinfo;
        $db_user = D("yonghu");
        $zhuanru1 = $db_user->where("uid={$huiyuan['uid']}")->setInc('money', floor($huiyuan['yongjin'])); // 用户的积分加1$User->where('id=5')->setDec('score',5); // 用户的积分减
        $zhuanru = $db_user->where("uid={$huiyuan['uid']}")->setDec('yongjin', floor($huiyuan['yongjin']));
        $type = 1;
        $pay = "佣金";
        $time = time();
        $content = "使用佣金充值到一元云购账户";
        $data = array("uid" => "{$huiyuan['uid']}", "type" => "$type", "pay" => "$pay", "content" => "$content", "money" => "{$huiyuan['yongjin']}", "time" => $time);
        $account = D("yonghu_zhanghao")->add($data);
        if ($zhuanru && $account && $zhuanru1) {
            $yys['code'] = 0;
            $yys['money'] = floor($huiyuan['yongjin']);
        } else {
            $yys['code'] = 10;
        }
        echo json_encode($yys);
    }

    public function userrecharge() {
        if (ismobile()) {
            $type = "1";
        } else {
            $type = "0";
        }
        $paydata = D("payment")->where(array("pay_start" => "1", "mobile" => $type))->select();
        $this->assign("paydata", $paydata);
        $this->autoShow("user.recharge");
    }

    //圈子管理
    public function joingroup() {
        $huiyuan = $this->userinfo;
        $biaoti = "加入的圈子";
        $addgroup = rtrim($huiyuan['addgroup'], ",");
        if ($addgroup) {
            $group = D("quan")->where(array('id' => $addgroup))->select();
        } else {
            $group = null;
        }
        $this->assign("group", $group);
        $this->display("index/member.joingroup");
    }

    public function topic() {
        $huiyuan = $this->userinfo;
        $biaoti = "圈子话题";
        $tiezi = D("quan_tiezi")->where(array('hueiyuan' => $huiyuan[uid]))->select();
        $hueifu = D("quan_hueifu")->where(array('hueiyuan' => $huiyuan[uid]))->select();
        $this->display("index/user.topic");
    }

    //直购记录
    public function userbuylistzg() {
        $huiyuan = $this->userinfo;
        $uid = $huiyuan['uid'];
        if (ismobile()) {
            $biaoti = "一元云购记录";
            $this->display("mobile/user.userbuylistzg");
            exit;
        }
        $biaoti = "一元云购记录 - " . C("web_name");
        $zongji = D("yonghu_yys_recordzg")->where(array('uid' => $uid))->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, 10, $fenyenum, "0");
        $record = D("yonghu_yys_recordzg")->where(array('uid' => $uid))->order('id DESC')->limit(($fenyenum - 1) * 10, 10)->select();
        $this->assign("record", $record);
        $this->assign("fenye", $fenye);
        $this->assign("zongji", "$zongji");
        $this->assign("user", $user);
        $this->display("index/user.userbuylistzg");
    }

    public function integralmsg() {
        $huiyuan = $this->userinfo;
        $uid = $huiyuan['uid'];
        $biaoti = "账户记录 - " . C("web_name");
        $zongji = D("yonghu_zhanghao1")->where(array('uid' => $uid, 'pay' => '积分'))->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, 20, $fenyenum, "0");
        $account = D("yonghu_zhanghao1")->where(array('uid' => $uid, 'pay' => '积分'))->order('time DESC')->limit(($fenyenum - 1) * 20, 20)->select();
        $this->assign("huiyuan", $huiyuan);
        $this->assign("zongji", $zongji);
        $this->assign("fenye", $fenye);
        $this->assign("account", $account);
        $this->display("index/user.integralmsg");
    }

    //积分转换
    public function cashierSignDeposit() {
        $huiyuan = $this->userinfo;
        $uid = $huiyuan['uid'];
        $time = time();
        $biaoti = "充值到余额";
        $amount = isset($_POST['amount']) ? $_POST['amount'] : "";
        $pay_zhifu_name = '积分';
        if (isset($_POST['amount'])) {
            if (floor($amount) != $amount) {
                $this->note("不能是小数", C('URL_DOMAIN') . "user/cashierSignDeposit", 3);
                exit;
            }
            if ($amount > $huiyuan['money1'] || $huiyuan['money1'] <= 0) {
                $this->note("积分不足", C('URL_DOMAIN') . "user/cashierSignDeposit", 3);
                exit;
            }
            D("yonghu")->where(array('uid' => $huiyuan['uid']))->setDec('money1', $amount);
            D("yonghu")->where(array('uid' => $huiyuan['uid']))->setInc('money', $amount);
            D("yonghu_zhanghao1")->add(array('uid' => $uid, 'type' => '-1', 'pay' => $pay_zhifu_name, 'content' => '积分转账到余额', 'money' => $amount, 'time' => $time));
            $this->note("充值到余额成功", C('URL_DOMAIN') . "user/cashierSignDeposit", 3);
        }
        $this->assign("huiyuan", $huiyuan);
        $this->display("index/user.cashierSignDeposit");
    }

    //积分提现支付宝
    public function mentionnow() {
        $huiyuan = $this->userinfo;
        $uid = $huiyuan['uid'];
        $branch = "";
        $banknumber = "";
        $linkphone = "";
        $iii = I('f', 4);
        $jj = isset($_POST['selectAddrID']) ? $_POST['selectAddrID'] : "";
        $recordmmm = D("yonghu_yys_record")->where(array('id' => $iii, 'uid' => $huiyuan[uid]))->find();
        if (isset($_POST['J_submit'])) {
            foreach ($_POST as $k => $v) {
                $_POST[$k] = $this->htmtguolv($v);
            }

            $amount = isset($_POST['amount']) ? $_POST['amount'] : "";
            $alipayname = isset($_POST['alipayname']) ? $_POST['alipayname'] : "";
            $alipayusername = isset($_POST['alipayusername']) ? $_POST['alipayusername'] : "";
            $time = time();


            $shopinfoss = D("shangpin")->where(array('id' => $recordmmm[shopid]))->find();
            if ($amount < 100) {
                $this->note("积分提现不能小于100", C('URL_DOMAIN') . "user/cashierSignDeposit", 3);
                exit;
            }
            if ($amount > $huiyuan['money1'] || !is_numeric($amount)) {
                $this->note("积分无效,不能提现", C('URL_DOMAIN') . "user/cashierSignDeposit", 3);
                exit;
            }
            $amountsss = $amount * 0.01;
            if ($amountsss < 10) {
                $shouxufei = $amount * 0.01;
            } else {
                $shouxufei = '10';
            }
            $hhhh = $amount + $shouxufei;
            if ($hhhh > $huiyuan['money1']) {
                $this->note("积分不足,不能提现", C('URL_DOMAIN') . "user/cashierSignDeposit", 3);
                exit;
            }
            //手续费
            D("yonghu")->where(array('uid' => $huiyuan['uid']))->setDec('money1', ($amount + $shouxufei));
            D("yonghu_cashout1")->add(array('uid' => $uid, 'money' => $amount, 'username' => $alipayname, 'bankname' => $alipayusername, 'branch' => $branch, 'banknumber' => $banknumber, 'linkphone' => $linkphone, 'time' => $time, 'shehe' => '0'));
            $this->note("提现申请成功,请等待审核", C('URL_DOMAIN') . "user/cashierSignDeposit", 3);
        }
    }

    //积分转换记录
    public function withdrawRecord() {
        $huiyuan = $this->userinfo;
        $uid = $huiyuan['uid'];
        $recount = 0;
        //查询提现记录	 
        $jfwitem = D("yonghu_cashout1")->where(array('uid' => $uid))->order('time DESC')->limit(0, 30)->select();
        if (!empty($recordarr)) {
            $recount = 1;
        }
        $this->assign("jfwitem", $jfwitem);
        $this->display("index/user.withdrawRecord");
    }

    //账户福分
    public function userfufen() {
        $huiyuan = $this->userinfo;
        $uid = $huiyuan['uid'];
        $biaoti = "账户福分 - " . C("web_name");
        $zongji = D("yonghu_zhanghao")->where(array('uid' => $uid))->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, 20, $fenyenum, "0");
        $account = D("yonghu_zhanghao")->where(array('uid' => $uid))->order('time DESC')->limit(($fenyenum - 1) * 20, 20)->select();
        $this->assign("huiyuan", $huiyuan);
        $this->assign("account", $account);
        $this->assign("fenye", $fenye);
        $this->assign("zongji", $zongji);
        $this->display("index/user.userfufen");
    }

    //个人主页
    public function uname() {
        $tab = I("d");
        $bb = I("tab");
        if ($tab > 1000000000) {
            $aa = $tab - 1000000000;
        } else {
            $aa = $tab;
        }
        $biaoti = "个人主页";
        $index = $aa;
        $huiyuan = D("yonghu")->where(array('uid' => $index))->find();
        if ($huiyuan) {
            if ($bb == "userraffle") {
                $huiyuango = D("yonghu_yys_record")->where("uid='$index' and `huode` > '10000000'")->order('id DESC')->limit(0, 10)->select();
            } else {
                $huiyuango = D("yonghu_yys_record")->where(array('uid' => $index))->order('id DESC')->limit(0, 10)->select();
            }
            $this->assign("huiyuango", $huiyuango);
            $this->assign("huiyuan1", $huiyuan);
            $this->assign("bb", $bb);
            $this->display("index/us.index");
        } else {
            $this->note("页面错误");
        }
    }

    //邀请好友
    public function invitefriends() {
        $huiyuan = $this->userinfo;
        $uid = cookie('uid');
        $notinvolvednum = 0;  //未参加一元云购的人数
        $involvednum = 0;     //参加预购的人数
        $involvedtotal = 0;   //邀请人数		  
        //查询邀请好友信息		
        $invifriends = D("yonghu")->where(array('yaoqing' => $huiyuan[uid]))->order('time DESC')->select();
        $involvedtotal = count($invifriends);
        for ($i = 0; $i < count($invifriends); $i++) {
            $sqluid = $invifriends[$i]['uid'];
            $sqname = $this->huode_user_name($invifriends[$i]);
            $invifriends[$i]['sqlname'] = $sqname;
            //查询邀请好友的消费明细		   
            $accounts[$sqluid] = D("yonghu_zhanghao")->where(array('uid' => $sqluid))->order('time DESC')->select();
            //判断哪个好友有消费		
            if (empty($accounts[$sqluid])) {
                $notinvolvednum +=1;
                $records[$sqluid] = '未参与一元云购';
            } else {
                $involvednum +=1;
                $records[$sqluid] = '已参与一元云购';
            }
        }
        $lianjie = D("yongjin")->order('id DESC')->limit(1)->select();
        $friednsURL = "";
        foreach ($lianjie as $key => $val) {

            if ($huiyuan[yaoqing] && empty($huiyuan[yaoqing2]) && empty($huiyuan[yaoqing3])) {
                $friednsURL = $val[link] . '?yaoqing2=' . $huiyuan[yaoqing] . '&yaoqing=' . $huiyuan[uid];
            } else if ($huiyuan[yaoqing] && $huiyuan[yaoqing2] && empty($huiyuan[yaoqing3])) {
                $friednsURL = $val[link] . '?yaoqing=' . $huiyuan[uid] . '&yaoqing2=' . $huiyuan[yaoqing] . '&yaoqing3=' . $huiyuan[yaoqing2];
            } else if ($huiyuan[yaoqing] && $huiyuan[yaoqing2] && $huiyuan[yaoqing3]) {
                $friednsURL = $val[link] . '?yaoqing=' . $huiyuan[uid] . '&yaoqing2=' . $huiyuan[yaoqing] . '&yaoqing3=' . $huiyuan[yaoqing2];
            } else {
                $friednsURL = $val[link] . '?yaoqing=' . $huiyuan[uid];
            }
        }
        $this->assign("lianjie", $lianjie);
        $this->assign("records", $records);
        $this->assign("friednsURL", $friednsURL);
        $this->assign("invifriends", $invifriends);
        $this->assign("involvedtotal", $involvedtotal);
        $this->assign("involvednum", $involvednum);
        $this->display("index/user.invitefriends");
    }

    //佣金明细
    public function commissions() {
//ini_set("display_errors", "On");
//error_reporting(E_ALL | E_STRICT);
        $tab = I("d");
        $huiyuan = $this->userinfo;
        $uid = $huiyuan['uid'];
        $hh = D("yonghu")->where(array('uid' => $uid))->find();
        $recodetotal = 0;   // 判断是否为空
        $shourutotal = 0;
        $zhichutotal = 0;
        if ($tab == 0) {
            $invifriends2 = D("yonghu a")->join("yys_yonghu_yys_record b on a.uid = b.uid")->where("yaoqing=$huiyuan[uid] and id is not null")->order("b.time DESC")->select();
        } else if ($tab == 2) {
            $invifriends2 = D("yonghu a")->join("yys_yonghu_yys_record b on a.uid = b.uid")->where("yaoqing2=$huiyuan[uid] and id is not null")->order("b.time DESC")->select();
        } else if ($tab == 3) {
            $invifriends2 = D("yonghu a")->join("yys_yonghu_yys_record b on a.uid = b.uid")->where("yaoqing3=$huiyuan[uid] and id is not null")->order("b.time DESC")->select();
        }
        $this->assign("hh", $hh);
        $this->assign("huiyuan", $huiyuan);
        $this->assign("invifriends2", $invifriends2);
        $this->assign("involvednum", $involvednum);
        $this->display("index/member.commissions");
    }

    //申请提现
    public function cashout() {
        $huiyuan = $this->userinfo;
        $uid = $huiyuan['uid'];
        $hh = D("yonghu")->where(array('uid' => $huiyuan[uid]))->find();
        $zongji = 0;
        $shourutotal = 0;
        $zhichutotal = 0;
        $cashoutdjtotal = 0;
        $cashouthdtotal = 0;
        //查询邀请好友id
        $invifriends = D("yonghu")->where(array('yaoqing' => $huiyuan[uid]))->order('time DESC')->select();
        //查询佣金收入
        for ($i = 0; $i < count($invifriends); $i++) {
            $sqluid = $invifriends[$i]['uid'];
            //查询邀请好友给我反馈的佣金  
            $recodes[$sqluid] = D("yonghu_recodes")->where(array('uid' => $sqluid, 'type' => 1))->order('time DESC')->select();
        }
        //查询佣金消费(提现,充值)	
        $zhichu = D("yonghu_recodes")->where(array('uid' => $uid, 'type' => 1))->order('time DESC')->select();
        //查询被冻结金额		  
        $cashoutdj = D("yonghu_cashout")->field('SUM(money) as summoney ')->where(array('uid' => $uid) and 'auditstatus' != 1)->order('time DESC')->find();
        if (!empty($recodes)) {
            foreach ($recodes as $key => $val) {
                foreach ($val as $key2 => $val2) {
                    $shourutotal+=$val2['money'];  //总佣金收入	 
                }
            }
        }
        if (!empty($zhichu)) {
            foreach ($zhichu as $key => $val3) {
                $zhichutotal+=$val3['money']; //总支出的佣金		  
            }
        }
        $cashouthdtotal = $zongji = $hh[yongjin];
        if (isset($_POST['submit1'])) { //提现	     
            $money = abs(intval($_POST['money']));
            $weername = htmlspecialchars($_POST['txtUserName']);
            $bankname = htmlspecialchars($_POST['txtBankName']);
            $branch = htmlspecialchars($_POST['txtSubBank']);
            $banknumber = htmlspecialchars($_POST['txtBankNo']);
            $linkphone = htmlspecialchars($_POST['txtPhone']);
            $time = time();
            $type = -3;  //收取1/消费-1/充值-2/提现-3
            if ($zongji < 100) {
                $this->note("佣金金额大于100元才能提现！");
                exit;
            } elseif ($zongji < $money) {
                $this->note("输入额超出总佣金金额！");
                exit;
            } else {
                //插入提现申请表  这里不用在佣金表中插入记录 等后台审核才插入
                $j1 = D("yonghu_cashout")->data(array('uid' => $uid, 'money' => $money, 'username' => $weername, 'bankname' => $bankname, 'branch' => $branch, 'banknumber' => $banknumber, 'linkphone' => $linkphone, 'time' => $time))->add();
                $j2 = D("yonghu")->where(array('uid' => $huiyuan[uid]))->setDec('yongjin', $money);
                if ($j1 && $j2) {
                    $this->note("申请成功！请等待审核！");
                }
            }
        }
        if (isset($_POST['submit2'])) {//充值			
            $money = abs(intval($_POST['txtCZMoney']));
            $type = 1;
            $pay = "佣金";
            $time = time();
            $content = "使用佣金充值到一元云购账户";

            if ($money <= 0 || $money > $zongji) {
                $this->note("佣金金额输入不正确！");
                exit;
            }
            if ($cashouthdtotal < $money) {
                $this->note("输入额超出活动佣金金额！");
                exit;
            }
            //插入记录
            $account = D("yonghu_zhanghao")->data(array('uid' => $uid, 'type' => $type, 'pay' => $pay, 'content' => $content, 'money' => $money, 'time' => $time))->add();
            // 查询是否有该记录
            if ($account) {
                //修改剩余金额
                $leavemoney = $huiyuan['money'] + $money;
                $leavemoney1 = $huiyuan['yongjin'] - $money;
                $mrecode = D("yonghu")->where(array('uid' => $uid))->save(array('money' => $leavemoney, 'yongjin' => $leavemoney1));
                //在佣金表中插入记录		 
                $recode = D("yonghu_recodes")->data(array('uid' => $uid, 'type' => '-2', 'content' => $content, 'money' => $money, 'time' => $time))->add();
                $this->note("充值成功！");
            } else {
                $this->note("充值失败！");
            }
        }
        $this->assign("hh", $hh);
        $this->assign("cashouthdtotal", $cashouthdtotal);
        $this->display("index/user.cashout");
    }

    //直购佣金
    public function commissionszg() {
//ini_set("display_errors", "On");
//error_reporting(E_ALL | E_STRICT);
        $tab = I('d');
        $huiyuan = $this->userinfo;
        $uid = $huiyuan['uid'];
        $hh = D('yonghu')->where(array('uid' => $huiyuan[uid]))->find();
        $recodetotal = 0;   // 判断是否为空
        $shourutotal = 0;
        $zhichutotal = 0;
        $invifriends2 = D("yonghu a")->join("yys_yonghu_yys_recordzg b on a.uid = b.uid ")->where(array('yaoqing' . "$tab" => $huiyuan[uid]))->order("b.time DESC")->select();
        $this->assign("hh", $hh);
        $this->assign("huiyuan", $huiyuan);
        $this->assign("invifriends2", $invifriends2);
        $this->display("index/member.commissionszg");
    }

    //提现记录
    public function commissions4() {
        $huiyuan = $this->userinfo;
        $uid = $huiyuan['uid'];
        $hh = D("yonghu")->where(array('uid' => $huiyuan[uid]))->find();
        $recodetotal = 0;   // 判断是否为空
        $shourutotal = 0;
        $zhichutotal = 0;
        $invifriends = D("yonghu")->where(array('yaoqing' => $huiyuan[uid]))->order('time DESC')->select();
        //查询佣金表
        //自己提现或充值
        $recodes[$uid] = D("yonghu_recodes")->where(array('uid' => $uid, 'type' => '-2'))->order('time DESC')->select();
        $weer[$uid]['username'] = R('base/huode_user_name', ($huiyuan));
        $recodearr = '';
        $i = 0;
        if (!empty($recodes)) {
            foreach ($recodes as $key => $val) {
                $weername[$key] = $weer[$key]['username'];
                foreach ($val as $key2 => $val2) {
                    $recodearr[$i] = $val2;
                    $i++;
                }
            }
        }
        $recodetotal = count($recodes);
        //查询   累计收入：元    累计(提现/充值)：元    佣金余额：元
        if (!empty($recodes)) {
            foreach ($recodes as $key => $val) {
                if ($uid == $key) {
                    foreach ($val as $key2 => $val2) {
                        $zhichutotal+=$val2['money'];  //总佣金支出		 
                    }
                } else {
                    foreach ($val as $key3 => $val3) {

                        $shourutotal+=$val3['money'];  //总佣金收入		 
                    }
                }
            }
        }
        $zongji = $shourutotal - $zhichutotal;  //计算佣金余额	 
        $this->assign("recodetotal", $recodetotal);
        $this->assign("hh", $hh);
        $this->assign("recodearr", $recodearr);
        $this->display("index/member.commissions4");
    }

    //提现记录
    public function record() {
        $huiyuan = $this->userinfo;
        $uid = $huiyuan['uid'];
        $recount = 0;
        $fufen = C("user_fufen", '', 'member');
        $recordarr = D("yonghu_cashout")->where(array('uid' => $uid))->order('time DESC')->limit('0,30')->select();
        if (!empty($recordarr)) {
            $recount = 1;
        }
        $this->assign("fufen", $fufen);
        $this->assign("recount", $recount);
        $this->assign("recordarr", $recordarr);
        $this->display("index/member.record");
    }

    //一元云购记录详细
    public function userbuydetail() {
        $biaoti = "一元云购详情";
        $crodid = I("id");
        $record = D("yonghu_yys_record")->where(array("id" => $crodid, "uid" => $this->userinfo['uid']))->find();
        if (!$record) {
            $this->note("页面错误", C("URL_DOMAIN") . "user/userbuylist", 3);
        }
        $shopinfo = D("shangpin")->where(array("id" => $record['shopid']))->find();
        $record['thumb'] = $shopinfo['thumb'];
        if ($crodid > 0) {
            $this->assign("shopinfo", $shopinfo);
            $this->assign("record", $record);
            $this->display("index/member.userbuydetail");
        } else {
            $this->note("页面错误", C("URL_DOMAIN") . "user/userbuylist", 3);
        }
    }

    public function orderDetailsb() {
        $huiyuan = $this->userinfo;
        $uid = $huiyuan['uid'];
        $iii = I("hidOrderid");
        $jj = isset($_POST['selectAddrID']) ? $_POST['selectAddrID'] : "";
        $huiyuan_dizhisss = D("yonghu_dizhi")->where(array('id' => $jj))->find();
        $recordmmm = D("yonghu_yys_record")->where(array('id' => $iii, "uid" => $huiyuan['uid']))->find();
        $recordmmmqq = D("shangpin")->where(array('id' => $recordmmm["shopid"]))->find();
        $rsrs1 = D("configs")->where(array('name' => "web_key1"))->find();
        $rsrs2 = D("configs")->where(array('name' => "web_key2"))->find();
        if (isset($_POST['btnSubmitCart'])) {
            foreach ($_POST as $k => $v) {
                $_POST[$k] = $this->htmtguolv($v);
            }
            $sheng111 = isset($_POST['sheng']) ? $_POST['sheng'] : "";
            if (!empty($sheng111)) {
                $sheng = isset($_POST['sheng']) ? $_POST['sheng'] : "";
                $shi = isset($_POST['shi']) ? $_POST['shi'] : "";
                $xian = isset($_POST['xian']) ? $_POST['xian'] : "";
                $jiedao = isset($_POST['jiedao']) ? $_POST['jiedao'] : "";
                $youbian = isset($_POST['youbian']) ? $_POST['youbian'] : "";
                $shouhuoren = isset($_POST['shouhuoren']) ? $_POST['shouhuoren'] : "";
                $tell = isset($_POST['tell']) ? $_POST['tell'] : "";
                $mobile = isset($_POST['mobile']) ? $_POST['mobile'] : "";
                $email = isset($_POST['email']) ? $_POST['email'] : "";
            } else {
                $sheng = $huiyuan_dizhisss['sheng'];
                $shi = $huiyuan_dizhisss['shi'];
                $xian = $huiyuan_dizhisss['xian'];
                $jiedao = $huiyuan_dizhisss['jiedao'];
                $youbian = $huiyuan_dizhisss['youbian'];
                $shouhuoren = $huiyuan_dizhisss['shouhuoren'];
                $tell = $huiyuan_dizhisss['tell'];
                $mobile = $huiyuan_dizhisss['mobile'];
                $email = $huiyuan_dizhisss['email'];
            }

            if (!$sheng) {
                $this->note("省不能为空");
                exit;
            }
            if (!$shi) {
                $this->note("市不能为空");
                exit;
            }
            if (!$xian) {
                $this->note("县不能为空");
                exit;
            }
            if (!$shouhuoren) {
                $this->note("收货人不能为空");
                exit;
            }
            if (!$jiedao) {
                $this->note("详细地址不能为空");
                exit;
            }
            if (!$mobile) {
                $this->note("手机不能为空");
                exit;
            }

            $qq = isset($_POST['qq']) ? $_POST['qq'] : "";

            $shipTime = isset($_POST['shipTime']) ? $_POST['shipTime'] : "";
            $shipRemark = isset($_POST['shipRemark']) ? $_POST['shipRemark'] : "";
            $kaka = isset($_POST['kaka']) ? $_POST['kaka'] : "";
            $time = time();

            if ($kaka == 1) {
                if (empty($qq)) {
                    $this->note("直冲类商品QQ不能为空", C("URL_DOMAIN") . "user/orderlist", 3);
                    exit;
                } elseif (strlen($qq) < 5 || !is_numeric($qq)) {
                    $this->note("QQ号码无效", C("URL_DOMAIN") . "user/orderDetail/id/{$recordmmm['id']}", 3);
                    exit;
                }
                //QQ直冲
                //QQ冲值接口关闭
                $weerid = $rsrs1['value'];
                $weerpws = strtolower(md5($rsrs2['value']));
                //要充值的商品编号
                $cardid = "220612";
                //要充值的数量
                $cardnum = $recordmmmqq['yuanjia'];
                //外部订单号，唯一性
                $sporder_id = $time;
                //格式：年月日时分秒 如：20141209093450
                $sporder_time = $time;
                //game_userid=xxx@162.com$xxx001 xxx@162.com是通行证,xxx001是玩家账号
                $game_userid = $qq;
                //游戏玩家密码(可以为空)
                $game_userpsw = "";
                //区服没有则不写
                $game_area = "";
                $game_srv = "";
                //该参数将异步返回充值结果，若不填写该地址，则不会回调
                $ret_url = "http://xxxx";
                //版本号固定值
                $version = "6.0";
                //默认的秘钥是OFCARD，可联系商务修改，若已经修改过的，请使用修改过的。
                $keystr = "OFCARD";
                $md5_str_param = $weerid . $weerpws . $cardid . $cardnum . $sporder_id . $sporder_time . $game_userid . $game_area . $game_srv . $keystr;
                $md5_str = strtoupper(md5($md5_str_param));
                if (!empty($game_area) or ! empty($game_srv)) {
                    //编码传输
                    $game_area = urlencode($game_area);
                    $game_srv = urlencode($game_srv);
                }
                $url = "http://api2.ofpay.com/onlineorder.do?userid=" . $weerid . "&userpws=" . $weerpws . "&cardid=" . $cardid . "&cardnum=" . $cardnum . "&game_area=" . $game_area . "&game_srv=" . $game_srv
                        . "&sporder_id=" . $sporder_id . "&sporder_time=" . $sporder_time . "&game_userid=" . $game_userid . "&md5_str=" . $md5_str . "&version=" . $version . "&ret_url=" . $ret_url;
                //发送http请求
                $contents = $this->http_request($url);
                $res = simplexml_load_string($contents);
                $retcode = $res->retcode;
                $err_msg = $res->err_msg;
                if ($retcode == "1") {
                    $orderid = $res->orderid;
                    $cardid = $res->cardid;
                    $cardnum = $res->cardnum;
                    $ordercash = $res->ordercash;
                    $cardname = $res->cardname;
                    $sporder_id = $res->sporder_id;
                    $game_area = $res->game_area;
                    $game_srv = $res->game_srv;
                    $game_userid = $res->game_userid;
                    $game_state = $res->game_state;
                }
            }
            $huiyuan_dizhi = D("yonghu_dizhi")->where(array("uid" => $huiyuan['uid']))->find();
            if (!$huiyuan_dizhi) {
                $default = "Y";
            } else {
                $default = "N";
            }
            $shopinfoss = D("shangpin")->where(array("id" => $recordmmm["shopid"]))->find();
            if ($recordmmm['leixing'] == 0) {

                $status = '已付款,未发货,未完成,已提交地址';
            } else {
                $status = '已付款,已发货,已完成';
            }
            D("yonghu_yys_record")->where(array("id" => $iii))->save(array("shouhuo" => "1", "status" => $status, "qq" => $qq, "youbian" => $youbian, "shipRemark" => $shipRemark, "shipTime" => $shipTime, "email" => $email, "tell" => $tell, "shouhuoren" => $shouhuoren, "mobile" => $mobile, "sheng" => $sheng, "shi" => $shi, "xian" => $xian, "jiedao" => $jiedao, "fhtime" => $time, "wei" => '0'));
            $count = D("yonghu_dizhi")->where(array('uid' => $huiyuan['uid']))->count();
            if (intval($count) < 10) {
                D("yonghu_dizhi")->add(array("uid" => $uid, "sheng" => $sheng, "shi" => $shi, "xian" => $xian, "jiedao" => $jiedao, "youbian" => $youbian, "shouhuoren" => $shouhuoren, "tell" => $tell, "mobile" => $mobile, "qq" => $qq, "default" => $default, "time" => $time));
            }
            $this->note("添加成功", C("URL_DOMAIN") . "user/orderDetail/crodid/{$recordmmm['id']}", 3);
        }
    }

    public function orderDetailsbzg() {
        $huiyuan = $this->userinfo;
        $uid = $huiyuan['uid'];
        $iii = I("hidOrderid");
        $jj = isset($_POST['selectAddrID']) ? $_POST['selectAddrID'] : "";
        $huiyuan_dizhisss = D("yonghu_dizhi")->where(array('id' => $jj))->find();
        $recordmmm = D("yonghu_yys_recordzg")->where(array('id' => $iii, "uid" => $huiyuan['uid']))->find();
        $recordmmmqq = D("shangpinzg")->where(array('id' => $recordmmm["shopid"]))->find();
        $rsrs1 = D("configs")->where(array('name' => "web_key1"))->find();
        $rsrs2 = D("configs")->where(array('name' => "web_key2"))->find();
        if (isset($_POST['btnSubmitCart'])) {
            foreach ($_POST as $k => $v) {
                $_POST[$k] = $this->htmtguolv($v);
            }
            $sheng111 = isset($_POST['sheng']) ? $_POST['sheng'] : "";
            if (!empty($sheng111)) {
                $sheng = isset($_POST['sheng']) ? $_POST['sheng'] : "";
                $shi = isset($_POST['shi']) ? $_POST['shi'] : "";
                $xian = isset($_POST['xian']) ? $_POST['xian'] : "";
                $jiedao = isset($_POST['jiedao']) ? $_POST['jiedao'] : "";
                $youbian = isset($_POST['youbian']) ? $_POST['youbian'] : "";
                $shouhuoren = isset($_POST['shouhuoren']) ? $_POST['shouhuoren'] : "";
                $tell = isset($_POST['tell']) ? $_POST['tell'] : "";
                $mobile = isset($_POST['mobile']) ? $_POST['mobile'] : "";
                $email = isset($_POST['email']) ? $_POST['email'] : "";
            } else {
                $sheng = $huiyuan_dizhisss['sheng'];
                $shi = $huiyuan_dizhisss['shi'];
                $xian = $huiyuan_dizhisss['xian'];
                $jiedao = $huiyuan_dizhisss['jiedao'];
                $youbian = $huiyuan_dizhisss['youbian'];
                $shouhuoren = $huiyuan_dizhisss['shouhuoren'];
                $tell = $huiyuan_dizhisss['tell'];
                $mobile = $huiyuan_dizhisss['mobile'];
                $email = $huiyuan_dizhisss['email'];
            }

            if (!$sheng) {
                $this->note("省不能为空");
                exit;
            }
            if (!$shi) {
                $this->note("市不能为空");
                exit;
            }
            if (!$xian) {
                $this->note("县不能为空");
                exit;
            }
            if (!$shouhuoren) {
                $this->note("收货人不能为空");
                exit;
            }
            if (!$jiedao) {
                $this->note("详细地址不能为空");
                exit;
            }
            if (!$mobile) {
                $this->note("手机不能为空");
                exit;
            }

            $qq = isset($_POST['qq']) ? $_POST['qq'] : "";

            $shipTime = isset($_POST['shipTime']) ? $_POST['shipTime'] : "";
            $shipRemark = isset($_POST['shipRemark']) ? $_POST['shipRemark'] : "";
            $kaka = isset($_POST['kaka']) ? $_POST['kaka'] : "";
            $time = time();

            if ($kaka == 1) {
                if (empty($qq)) {
                    $this->note("直冲类商品QQ不能为空", C("URL_DOMAIN") . "user/orderlist", 3);
                    exit;
                } elseif (strlen($qq) < 5 || !is_numeric($qq)) {
                    $this->note("QQ号码无效", C("URL_DOMAIN") . "user/orderDetailzg/id/{$recordmmm['id']}", 3);
                    exit;
                }
                //QQ直冲
                //QQ冲值接口关闭
                $weerid = $rsrs1['value'];
                $weerpws = strtolower(md5($rsrs2['value']));
                //要充值的商品编号
                $cardid = "220612";
                //要充值的数量
                $cardnum = $recordmmmqq['yuanjia'];
                //外部订单号，唯一性
                $sporder_id = $time;
                //格式：年月日时分秒 如：20141209093450
                $sporder_time = $time;
                //game_userid=xxx@162.com$xxx001 xxx@162.com是通行证,xxx001是玩家账号
                $game_userid = $qq;
                //游戏玩家密码(可以为空)
                $game_userpsw = "";
                //区服没有则不写
                $game_area = "";
                $game_srv = "";
                //该参数将异步返回充值结果，若不填写该地址，则不会回调
                $ret_url = "http://xxxx";
                //版本号固定值
                $version = "6.0";
                //默认的秘钥是OFCARD，可联系商务修改，若已经修改过的，请使用修改过的。
                $keystr = "OFCARD";
                $md5_str_param = $weerid . $weerpws . $cardid . $cardnum . $sporder_id . $sporder_time . $game_userid . $game_area . $game_srv . $keystr;
                $md5_str = strtoupper(md5($md5_str_param));
                if (!empty($game_area) or ! empty($game_srv)) {
                    //编码传输
                    $game_area = urlencode($game_area);
                    $game_srv = urlencode($game_srv);
                }
                $url = "http://api2.ofpay.com/onlineorder.do?userid=" . $weerid . "&userpws=" . $weerpws . "&cardid=" . $cardid . "&cardnum=" . $cardnum . "&game_area=" . $game_area . "&game_srv=" . $game_srv
                        . "&sporder_id=" . $sporder_id . "&sporder_time=" . $sporder_time . "&game_userid=" . $game_userid . "&md5_str=" . $md5_str . "&version=" . $version . "&ret_url=" . $ret_url;
                //发送http请求
                $contents = $this->http_request($url);
                $res = simplexml_load_string($contents);
                $retcode = $res->retcode;
                $err_msg = $res->err_msg;
                if ($retcode == "1") {
                    $orderid = $res->orderid;
                    $cardid = $res->cardid;
                    $cardnum = $res->cardnum;
                    $ordercash = $res->ordercash;
                    $cardname = $res->cardname;
                    $sporder_id = $res->sporder_id;
                    $game_area = $res->game_area;
                    $game_srv = $res->game_srv;
                    $game_userid = $res->game_userid;
                    $game_state = $res->game_state;
                }
            }
            $huiyuan_dizhi = D("yonghu_dizhi")->where(array("uid" => $huiyuan['uid']))->find();
            if (!$huiyuan_dizhi) {
                $default = "Y";
            } else {
                $default = "N";
            }
            $shopinfoss = D("shangpin")->where(array("id" => $recordmmm["shopid"]))->find();
            if ($recordmmm['leixing'] == 0) {

                $status = '已付款,未发货,未完成,已提交地址';
            } else {
                $status = '已付款,已发货,已完成';
            }
            D("yonghu_yys_recordzg")->where(array("id" => $iii))->save(array("shouhuo" => "1", "status" => $status, "qq" => $qq, "youbian" => $youbian, "shipRemark" => $shipRemark, "shipTime" => $shipTime, "email" => $email, "tell" => $tell, "shouhuoren" => $shouhuoren, "mobile" => $mobile, "sheng" => $sheng, "shi" => $shi, "xian" => $xian, "jiedao" => $jiedao, "fhtime" => $time, "wei" => '0'));
            $count = D("yonghu_dizhi")->where(array('uid' => $huiyuan['uid']))->count();
            if (intval($count) < 10) {
                D("yonghu_dizhi")->add(array("uid" => $uid, "sheng" => $sheng, "shi" => $shi, "xian" => $xian, "jiedao" => $jiedao, "youbian" => $youbian, "shouhuoren" => $shouhuoren, "tell" => $tell, "mobile" => $mobile, "qq" => $qq, "default" => $default, "time" => $time));
            }
            $this->note("添加成功", C("URL_DOMAIN") . "user/orderDetailzg/id/{$recordmmm['id']}", 3);
        }
    }

    public function excorderdetail() {
        $huiyuan = $this->userinfo;
        $crodid = intval(I("id"));
        $iii = I("id");
        $records = D("yonghu_yys_record")->where(array("id" => $crodid, "uid" => $huiyuan['uid']))->find();
        if ($records['status'] == '已付款,未发货,未完成,未提交地址') {
            $fhid = 0;
        } elseif ($records['status'] == '已付款,未发货,未完成,已提交地址') {
            $fhid = 1;
        } elseif ($records['status'] == '已付款,已发货,待收货') {
            $fhid = 2;
        } elseif ($records['status'] == '已付款,已发货,已完成') {
            $fhid = 3;
        } elseif ($records['status'] == '已付款,已发货,已作废') {
            $fhid = 4;
        }
        $status = @explode(",", $records['status']);
        $ii = I("id");
        $this->assign("records", $records);
        $this->assign("fhid", $fhid);
        $this->assign("status", $status);
        $this->assign("iii", $iii);
        $this->assign("ii", $ii);
        $this->autoShow("member.excorderdetail");
    }

    public function excorderdetailsb() {
        $huiyuan = $this->userinfo;
        $uid = $huiyuan['uid'];
        $iii = I("hidOrderid");
        $recordmmm = D("yonghu_yys_record")->where(array("id" => $iii, "uid" => $huiyuan['uid']))->find();
        if (isset($_POST['btnSubmitCart'])) {
            foreach ($_POST as $k => $v) {
                $_POST[$k] = $this->htmtguolv($v);
            }
            $time = time();
            $shopinfoss = D("shangpin")->where(array("id" => $recordmmm["shopid"]))->find();
            if (empty($shopinfoss['yuanjia'])) {
                $this->autoNote("未设置转换原价", C("URL_DOMAIN") . "user/excorderdetail/id/{$recordmmm['id']}", 3);
                exit;
            }
            if ($recordmmm['leixing'] == 0) {
                $jia = $shopinfoss['yuanjia'] * C("fufen_yongjinqd0");
            } else if ($recordmmm['leixing'] == 1) {
                $jia = $shopinfoss['yuanjia'] * C("fufen_yongjinqd1");
            } else {
                $jia = $shopinfoss['yuanjia'] * C("fufen_yongjinqd2");
            }
            $pay_zhifu_name = '积分';
            if ($recordmmm["wei"] == 1) {
                $this->autoNote("您已经转换过哦", C("URL_DOMAIN") . "user/excorderdetail/id/{$recordmmm['id']}", 3);
                exit;
            }
            D("yonghu")->where(array("uid" => $huiyuan['uid']))->setInc('money1', $jia);
            D("yonghu_yys_record")->where(array("id" => $iii, "uid" => $huiyuan['uid']))->save(array("status" => '已付款,已发货,已完成', "wei" => "1"));
            D("yonghu_zhanghao1")->add(array("uid" => $uid, "type" => "-1", "pay" => $pay_zhifu_name, "content" => "众购商品id({$shopinfoss['id']})转换获得积分", "money" => $jia, "time" => $time));
            $this->autoNote("兑换成功", C("URL_DOMAIN") . "user/orderlist", 3);
        }
    }

    //网盘
    public function wangpan() {
        $uid = $this->userinfo['uid'];
        $uids = $uid;
        $huiyuan = $this->userinfo;
        $biaoti = "网盘上传";
        $uid = cookie('uid');
        $wehell = cookie('ushell');
        $this->assign("biaoti", $biaoti);
        $this->assign("wehell", $wehell);
        $this->assign("uids", $uids);
        $this->assign("uid", $uid);
        $this->assign("huiyuan", $huiyuan);
        $this->display("index/member.wangpan");
    }

    //网盘OK
    public function wangpanok() {
        $uid = I("uid");
        $hui = D("yonghu")->field("wpimg")->where(array('uid' => $uid))->find();
        $gimg = $hui["wpimg"];
        $gimg = substr($gimg, 0, strlen($gimg) - 1);
        $geshu = explode(";", $gimg);
        $huiyuan = $this->userinfo;
        $biaoti = $this->huode_user_name($uid) . "的网盘内容";
        $this->assign("biaoti", $biaoti);
        $this->assign("wehell", $wehell);
        $this->assign("uid", $uid);
        $this->assign("geshu", $geshu);
        $this->display("index/member.wangpanok");
    }

    //网盘上传
    public function wangpan1() {
        //dump($_POST);exit;
        $uid = $this->userinfo['uid'];
        if (isset($_POST["submit"])) {
            $gimg = $this->userinfo["wpimg"];
            $gimg = substr($gimg, 0, strlen($gimg) - 1);
            $geshu = explode(";", $gimg);
            $ji = D("yonghu_yys_record")->where(array("uid" => $uid))->count();
            $jj = $this->userinfo["money"] + $ji;
            if (count($geshu) >= $jj) {
                $this->note("您只可上传" . "$jj" . "张图片", C("URL_DOMAIN") . "user/wangpan", 3);
            }
            $tname = trim(str_ireplace(" ", "", $_POST['img']));
            $tname = $this->htmtguolv($tname);
//            $mulu = YYS_UPLOADS . $tname;
//            $mulus = $_SERVER['DOCUMENT_ROOT'] . "/love/uploads/wangpan/" . $uid;
//            $x = (int) $_POST['x'];
//            $y = (int) $_POST['y'];
//            $w = (int) $_POST['w'];
//            $h = (int) $_POST['h'];
//            $point = array("x" => $x, "y" => $y, "w" => $w, "h" => $h);
//            System::DOWN_sys_class('upload', 'sys', 'no');
            $gimg = $this->userinfo["wpimg"];
            $gname = $tname . ';' . $gimg;
            D("yonghu")->where(array("uid" => $uid))->save(array("wpimg" => $gname));
            $this->note("网盘上传图片成功", C("URL_DOMAIN") . "user/wangpan", 3);
        }
    }

    //检测用户是否已注册
    public function checkname() {
        //$config_email = System::DOWN_sys_config("email");
        // $config_mobile = System::DOWN_sys_config("mobile");
        $weer = array();
        $name = I("name");
        $regtype = null;
        if ($this->checkmobile($name)) {
            $regtype = 'mobile';
            $mobile = C('mobile');
            $cfg_mobile_type = 'cfg_mobile_' . $mobile['cfg_mobile_on'];
            $config_mobile = $mobile[$cfg_mobile_type];
            if (empty($config_mobile['mid']) && empty($config_mobile['mpass'])) {
                $weer['state'] = 2; //_notemobile("系统短息配置不正确!");
                echo json_encode($weer);
                exit;
            }
        }
        $huiyuan = D("yonghu")->where(array("mobile" => $name))->find();
        if (is_array($huiyuan)) {
            if ($huiyuan['mobilecode'] == 1 || $huiyuan['emailcode'] == 1) {
                $weer['state'] = 1; //_notemobile("该账号已被注册");
            } else {
                D("yonghu")->where(array("mobile" => $name))->delete();
                $weer['state'] = 0;
            }
        } else {
            $weer['state'] = 0; //表示数据库里没有该帐号
        }
        echo json_encode($weer);
    }

    //将数据注册到数据库
    public function userMobile() {
        $name = I("username");
        $pass = md5(I("password"));
        $verify = md5(strtoupper(I("verify")));

        if ($verify != cookie('checkcode')) {
            $weerMobile['state'] = -1;
            echo json_encode($weerMobile);
            exit;
        }
        $time = time();
        session_start();
        $decode = session("uu");
        $decode2 = session("yaoqing2") ? session("yaoqing2") : 0;
        $decode3 = session("yaoqing3") ? session("yaoqing3") : 0;
        //邮箱验证 -1 代表未验证， 1 验证成功 都不等代表等待验证
        $data = array("mobile" => $name, "password" => $pass, "img" => "photo/member.jpg", "emailcode" => "-1", "mobilecode" => "-1", "yaoqing" => $decode, "yaoqing2" => $decode2, "yaoqing3" => $decode3, "time" => $time);

        if (D("yonghu")->add($data)) {
            $weerMobile['state'] = 0;
        } else {
            $weerMobile['state'] = 1;
        }
        echo json_encode($weerMobile);
    }

    //验证输入的手机验证码
    public function mobileregsn() {

        $mobile = I("mobile");
        $checkcodes = I("code");
        $huiyuan = D("yonghu")->where(array("mobile" => $mobile))->find();
        if (strlen($checkcodes) != 6) {
            $mobileregsn['state'] = 1;
            echo json_encode($mobileregsn);
            exit;
        }
        $weercode = explode("|", $huiyuan['mobilecode']);
        if ($checkcodes != $weercode[0]) {
            $mobileregsn['state'] = 1;
            echo json_encode($mobileregsn);
            exit;
        }
        D("yonghu")->where(array("uid" => $huiyuan["uid"]))->setInc('score', C("f_phonecode"));
        D("yonghu")->where(array("uid" => $huiyuan["uid"]))->setInc('jingyan', C("z_phonecode"));
        D("yonghu")->where(array("uid" => $huiyuan["uid"]))->save(array("mobilecode" => 1));
        cookie("uid", $this->encrypt($huiyuan['uid']), 60 * 60 * 24 * 7);
        cookie("ushell", $this->encrypt(md5($huiyuan['uid'] . $huiyuan['password'] . $huiyuan['mobile'] . $huiyuan['email'])), 60 * 60 * 24 * 7);
        $mobileregsn['state'] = 0;
        $mobileregsn['str'] = 1;
        echo json_encode($mobileregsn);
    }

    //改昵称
    function nicheng() {
        $yonghu = $this->userinfo;
        $uid = $yonghu["uid"];
        $showtime = $uid . date("YmdH");

        $lujing11 = __PUBLIC__;
        $dest_folder = __PUBLIC__ . "touimg\\" . date("Ymd") . "\\";    //上传图片保存的路径 图片放在跟你upload.php同级的picture文件夹里
        $arr = array();  //定义一个数组存放上传图片的名称方便你以后会用的，如果不用那就不写
        $count = 0;
        if (!file_exists($dest_folder)) {
            mkdir($dest_folder);
        }
        $ggs = "";
        foreach ($_FILES["Filedata"]["error"] as $key => $error) {
            if ($error == UPLOAD_ERR_OK) {
                $tmp_name = $_FILES["Filedata"]["tmp_name"][$key];
                $name = $_FILES["Filedata"]["name"][$key];
                $name1 = str_replace('.', '', $name);
                $uploadfile = $dest_folder . $name1 . $showtime . '.png';

                $url1 = "touimg/" . date("Ymd") . "/" . $name1 . $showtime;
                $uploadfile1 = $url1 . '.png';
                move_uploaded_file($tmp_name, $uploadfile);
                $arr[$count] = $uploadfile;
                // $files=substr($uploadfile,3); //如果你到底的图片名称不是你所要的你可以用截取字符得到
                $ggs.=$uploadfile1;
                $ggs1 = $lujing11 . $ggs;
                $ggs2 = $lujing11 . $ggs;
                $abcc = substr($ggs, -4);
                $nnimage = $ggs1 . "_160160" . $abcc;
                $nnimage1 = $ggs1 . "_3030" . $abcc;
                $nnimage2 = $ggs1 . "_8080" . $abcc;
                $nnimage3 = $ggs1 . "_30" . $abcc;
                copy($ggs1, $nnimage);
                copy($ggs1, $nnimage1);
                copy($ggs1, $nnimage2);
                copy($ggs1, $nnimage3);
                //echo $files."<br />"; 
                // echo $arr[$count]."<br />";
            }
        }
        if (isset($_POST['submit'])) {
            $nicheng = $this->htmtguolv(trim($_POST['txtnicheng']));
            $nicheng = str_ireplace("'", "", $nicheng);
            $qianming = $this->htmtguolv(trim($_POST['qianming']));
            $reg_user_str = D("linshi")->where(array("key" => "member_name_key"))->find();
            $reg_user_str = explode(",", $reg_user_str['value']);
            if (is_array($reg_user_str) && !empty($nicheng)) {
                foreach ($reg_user_str as $rv) {
                    if ($rv == $nicheng) {
                        $this->notemobile("此昵称禁止使用!");
                    }
                }
            }
            if (ismobile()) {
                $q1 = D("yonghu")->where(array("uid" => $uid))->save(array("img" => $ggs, "username" => $nicheng));
            } else {
                $q1 = D("yonghu")->where(array("uid" => $uid))->save(array("qianming" => $qianming, "username" => $nicheng));
            }
            $isset_user = D("yonghu_zhanghao")->where("(content='手机认证完善奖励' or content='完善昵称奖励') and type='1' and uid='$uid' and (pay='经验' or pay='福分')")->find();
            if (!$isset_user) {
                $time = time();
                $data = array("uid" => $uid, "type" => "1", "pay" => "福分", "content" => "完善昵称奖励", "money" => C("f_overziliao"), "time" => $time);
                D("yonghu_zhanghao")->add($data);
                $data['pay'] = "经验";
                $data['money'] = C("z_overziliao");
                D("yonghu_zhanghao")->add($data);
                D("yonghu")->where(array("uid" => $uid))->setInc('score', C("f_overziliao"));
                D("yonghu")->where(array("uid" => $uid))->setInc('jingyan', C("z_overziliao"));
            }

            if ($q1) {
                $this->autoNote("修改成功", C("URL_DOMAIN") . "user/home", 3);
            } else {
                $this->autoNote("修改失败", C("URL_DOMAIN") . "user/home", 3);
            }
        }
        $this->assign("yonghu", $yonghu);
        $this->display("mobile/user.nicheng");
    }

    public function mobilechecking() {
        $huiyuan = $this->userinfo;
        if ($huiyuan['mobile'] && $huiyuan['mobilecode'] == 1) {
            $this->note("您的手机已经验证成功,请勿重复验证！");
        }
        $this->display("index/member.mobilechecking");
    }

    //手机验证
    public function mobilesuccess() {
        $biaoti = "手机验证";
        $huiyuan = $this->userinfo;
        if (isset($_POST['submit'])) {
            $mobile = isset($_POST['mobile']) ? $_POST['mobile'] : "";
            if (!$this->checkmobile($mobile) || $mobile == null) {
                $this->note("手机号错误", null, 3);
            }

            $huiyuan2 = D("yonghu")->where("mobile='$mobile' and `uid` != {$huiyuan["uid"]}")->field("mobilecode,uid,mobile")->find();
            if ($huiyuan2 && $huiyuan2['mobilecode'] == 1) {
                $this->note("手机号已被注册！");
            }
            if ($huiyuan['mobilecode'] != 1) {
                //验证码
                $ok = R("Tools/send_mobile_reg_code", array($mobile, $huiyuan['uid']));
                if ($ok[0] != 1) {
                    $this->note("发送失败,失败状态:" . $ok[1]);
                } else {
                    cookie("mobilecheck", base64_encode($mobile));
                }
            }
            $time = 120;
            $this->assign("mobile", $mobile);
            $this->display("index/member.mobilesuccess");
        }
    }

    //邮箱验证
    public function mailchecking() {
        $huiyuan = $this->userinfo;
        $biaoti = "邮箱验证";
        if ($huiyuan['email'] && $huiyuan['emailcode'] == 1) {
            $this->note("您的邮箱已经验证成功,请勿重复验证！");
        }
        $this->display("index/member.mailchecking");
    }

    //发送验证邮件
    public function sendsuccess() {
        if (!isset($_POST['submit']))
            $this->note("参数错误", C("URL_DOMAIN") . 'user/mailchecking');
        if (!isset($_POST['email']) || empty($_POST['email']))
            $this->note("邮箱地址不能为空!", C("URL_DOMAIN") . 'user/mailchecking');
        if (!$this->checkemail($_POST['email']))
            $this->note("邮箱格式错误!", C("URL_DOMAIN") . 'user/mailchecking');
        if (!C('user') || !C('pass')) {
            $this->note("系统邮箱配置不正确!", C("URL_DOMAIN") . 'user/mailchecking');
        }

        $huiyuan = $this->userinfo;
        $email = $_POST['email'];

        $huiyuan2 = D("yonghu")->where("email='$email' and `uid` != {$huiyuan["uid"]}")->find();
        if (!empty($huiyuan2) && $huiyuan2['emailcode'] == 1) {
            $this->note("该邮箱已经存在，请选择另外的邮箱验证！", C("URL_DOMAIN") . 'user/mailchecking');
        }

        $strcode1 = $email . "," . $huiyuan['uid'] . "," . time();
        $strcode = $this->encrypt($strcode1);

        $tit = $this->_cfg['web_name_two'] . "激活注册邮箱";
        $content = '<span>请在24小时内绑定邮箱</span>，点击链接：<a href="' . C("URL_DOMAIN") . 'user/emailok/code/' . $strcode . '">'; //emailcheckingok
        $content.=C("URL_DOMAIN") . 'user/emailok/code/' . $strcode . '</a>';
        $succ = R("Tools/sendemail", array($email, '', $tit, $content, 'yes', 'no'));
        if ($succ == 'no') {
            $this->note("邮件发送失败!", C("URL_DOMAIN") . 'user/mailchecking', 30);
        } else {
            $this->display("index/member.sendsuccess");
        }
    }

    function mobilebind() {
        $member = $this->userinfo;
        if (empty($member)) {
            _notemobile("请登陆");
            exit();
        }
        $this->display("mobile/mobilebind");
        $this->assign("member", $member);
    }

    //手机绑定开始
    public function mobileregbind() {
        $mobile = I("mobile", 0);
        $checkcodes = I("ckcode", 0);
        $huiyuan = $this->userinfo;
        $member = D("yonghu")->where("uid = $huiyuan[uid] and mobilecode !=1")->find();

        //$this->db->YOne("SELECT * FROM `@#_yonghu` WHERE `uid` = $huiyuan[uid] and `mobilecode`!=1 LIMIT 1");
        if (!$member) {
            $mobileregsn['state'] = "该账号己绑定手机!!";
            echo json_encode($mobileregsn);
            exit;
        }
        $isbind = D("yonghu")->where(array("mobile" => $mobile))->find();
        //	dump($isbind);
        //$this->db->YOne("SELECT * FROM `@#_yonghu` WHERE `mobile` = '$mobile' LIMIT 1");
        if ($isbind) {
            $mobileregsn['state'] = "该手机已被使用!";
            echo json_encode($mobileregsn);
            exit;
        }
        if (strlen($checkcodes) != 6) {
            //_notemobile("验证码输入不正确!");
            $mobileregsn['state'] = "验证码输入不正确";
            echo json_encode($mobileregsn);
            exit;
        }
        $usercode = explode("|", $member['mobilecode']);
        if ($checkcodes != $usercode[0]) {
            //_notemobile("验证码输入不正确!");
            $mobileregsn['state'] = "验证码输入不正确";
            echo json_encode($mobileregsn);
            exit;
        }
        $time = time();

        $find = 0;
        if ($member['code']) {
            $codeInfo = D("activity_code")->where(array("code" => $member[code], "status" => "0"))->find();
            //	$this->db->YOne("SELECT * FROM `@#_activity_code` WHERE `code` = '{$member['code']}' and `status`=0 LIMIT 1");
            if ($codeInfo) {
                $find = 1;
            }
        }

        if ($find) {
            $procode = $codeInfo['pro'];

            if ($procode) {
                $p_u = D("yonghu")->field('uid,mobile')->where(array("mobile" => $procode))->find();
                //$this->db->YOne("select uid,mobile from `@#_yonghu` where `mobile`='{$procode}'");
            }
            if ($p_u) {
                D("yonghu")->where(array("uid" => $member[uid]))->save(array("mobilecode" => "1", "mobile" => $mobile));
                //$this->db->Query("UPDATE `@#_yonghu` SET mobilecode='1',mobile='{$mobile}' where `uid`='$member[uid]'");
                $this->notemobile("绑定成功");
                exit;
            } else {
                D("yonghu")->where(array("uid" => $member[uid]))->save(array("mobilecode" => "1", "mobile" => $mobile));
                //	$this->db->Query("UPDATE `@#_yonghu` SET mobilecode='1',mobile='{$mobile}' where `uid`='$member[uid]'");
            }
            D("activity_code")->where(array("id" => $codeInfo[id]))->save(array("status" => "1", "mobile" => $mobile, "date" => now()));
            //$this->db->Query("UPDATE `@#_activity_code` SET `status`=1,`mobile`='$mobile',`date`=now() where `id`='$codeInfo[id]'");
        } else {
            D("yonghu")->where(array("uid" => $member[uid]))->save(array("mobilecode" => "1", "mobile" => $mobile));
            //		$this->db->Query("UPDATE `@#_yonghu` SET mobilecode='1',mobile='{$mobile}' where `uid`='$member[uid]'");
        }
//福分经验奖励


        $time = time();
        D("yonghu_zhanghao")->add(array("uid" => $huiyuan[uid], "type" => "1", "pay" => "福分", "content" => "手机认证完善奖励", "money" => C('f_phonecode'), "time" => $time));
        //$this->db->Query("insert into `@#_yonghu_zhanghao` (`uid`,`type`,`pay`,`content`,`money`,`time`) values ('$huiyuan[uid]','1','福分','手机认证完善奖励','$config[f_phonecode]','$time')");
        D("yonghu_zhanghao")->add(array("uid" => $huiyuan[uid], "type" => "1", "pay" => "经验", "content" => "手机认证完善奖励", "money" => c('z_phonecode'), "time" => $time));
        //$this->db->Query("insert into `@#_yonghu_zhanghao` (`uid`,`type`,`pay`,`content`,`money`,`time`) values ('$huiyuan[uid]','1','经验','手机认证完善奖励','$config[z_phonecode]','$time')");
        D("yonghu")->where(array("uid" => $huiyuan[uid]))->setInc("score", C('f_phonecode'));
        D("yonghu")->where(array("uid" => $huiyuan[uid]))->setInc("jingyan", C('z_phonecode'));
        //$this->db->Query("UPDATE `@#_yonghu` SET `score`=`score`+'$config[f_phonecode]',`jingyan`=`jingyan`+'$config[z_phonecode]' where uid='".$huiyuan['uid']."'");
//福分经验奖励结束
        $member['mobile'] = $mobile;
        cookie("uid", $this->encrypt($member['uid']));
        cookie("ushell", $this->encrypt(md5($member['uid'] . $member['password'] . $member['mobile'] . $member['email'])));

        $mobileregsn['state'] = 0;
        $mobileregsn['str'] = 1;

        echo json_encode($mobileregsn);
    }

    public function sendmobcode() {

        $name = I("name", 0);
        $member = $this->userinfo;

        //echo json_encode($member);
        if (!$member) {
            //_message("参数不正确!");
            $sendmobile['state'] = 1;
            echo json_encode($sendmobile);
            exit;
        }
        $checkcode = explode("|", $member['mobilecode']);
        $times = time() - $checkcode[1];
        if ($times > 120) {

            $sendok = R("Tools/send_mobile_reg_code", array($name, $member['uid']));
            if ($sendok[0] != 1) {
                //_message($sendok[1]);exit;
                $sendmobile['state'] = times;
                echo json_encode($sendmobile);
                exit;
            }
            //成功
            $sendmobile['state'] = "ok";
            echo json_encode($sendmobile);
            exit;
        } else {
            $sendmobile['state'] = 120 - $times;
            echo json_encode($sendmobile);
            exit;
        }
    }

    public function mobilecheck2() {
        $huiyuan = $this->userinfo;
        if (isset($_POST['submit'])) {
            $shoujimahao = base64_decode(cookie("mobilecheck"));
            if (!$this->checkmobile($shoujimahao))
                $this->note("手机号码错误!");

            $checkcodes = isset($_POST['code']) ? $_POST['code'] : $this->note("参数不正确!");

            if (strlen($checkcodes) != 6)
                $this->note("验证码输入不正确!");
            $weercode = explode("|", $huiyuan['mobilecode']);

            if ($checkcodes != $weercode[0])
                $this->note("验证码输入不正确!");
            D("yonghu")->where(array("uid" => $huiyuan[uid]))->save(array("mobilecode" => 1, "mobile" => $shoujimahao));
            //$this->db->Query("UPDATE `@#_yonghu` SET `mobilecode`='1',`mobile` = '$shoujimahao' where `uid`='$huiyuan[uid]'");
            //福分、经验添加			
            $isset_user = D("yonghu_zhanghao")->field("uid")->where("content='手机认证完善奖励' and type='1' and uid='$huiyuan[uid]' and (pay='经验' or pay='福分')")->select;
            //	$this->db->Ylist("select `uid` from `@#_yonghu_zhanghao` where `content`='手机认证完善奖励' and `type`='1' and `uid`='$huiyuan[uid]' and (`pay`='经验' or `pay`='福分')");
            if (empty($isset_user)) {
                $time = time();
                D("yonghu_zhanghao")->add(array('uid' => $huiyuan[uid], 'type' => 1, 'pay' => '福分', 'content' => '手机认证完善奖励', 'money' => C('f_phonecode'), 'tiem' => $time));
                // $this->db->Query("insert into `@#_yonghu_zhanghao` (`uid`,`type`,`pay`,`content`,`money`,`time`) values ('$huiyuan[uid]','1','福分','手机认证完善奖励','$config[f_phonecode]','$time')");
                D("yonghu_zhanghao")->add(array('uid' => $huiyuan[uid], 'type' => 1, 'pay' => '福分', 'content' => '手机认证完善奖励', 'money' => C('z_phonecode'), 'tiem' => $time));
                // $this->db->Query("insert into `@#_yonghu_zhanghao` (`uid`,`type`,`pay`,`content`,`money`,`time`) values ('$huiyuan[uid]','1','经验','手机认证完善奖励','$config[z_phonecode]','$time')");
                D("yonghu")->where(array("uid" => $huiyuan[uid]))->setInc("score", C('f_phonecode'));
                D("yonghu")->where(array("uid" => $huiyuan[uid]))->setInc("jingyan", C('z_phonecode'));
                //$this->db->Query("UPDATE `@#_yonghu` SET `score`=`score`+'$config[f_phonecode]',`jingyan`=`jingyan`+'$config[z_phonecode]' where uid='" . $huiyuan['uid'] . "'");
            }
            cookie("uid", $this->encrypt($huiyuan['uid']));
            cookie("ushell", $this->encrypt(md5($huiyuan['uid'] . $huiyuan['password'] . $huiyuan['mobile'] . $huiyuan['email'])));
//福分、经验添加			
            $isset_user = D("yonghu_zhanghao")->field("uid")->where("content='手机认证完善奖励' and type='1' and uid='$huiyuan[uid]' and (pay='经验' or pay='福分')")->select;
            if (empty($isset_user)) {

                $time = time();

                D("yonghu_zhanghao")->add(array('uid' => $huiyuan[uid], 'type' => 1, 'pay' => '福分', 'content' => '手机认证完善奖励', 'money' => C('f_phonecode'), 'tiem' => $time));
                D("yonghu_zhanghao")->add(array('uid' => $huiyuan[uid], 'type' => 1, 'pay' => '福分', 'content' => '手机认证完善奖励', 'money' => C('z_phonecode'), 'tiem' => $time));
                D("yonghu")->where(array("uid" => $huiyuan[uid]))->setInc("score", C('f_phonecode'));
                D("yonghu")->where(array("uid" => $huiyuan[uid]))->setInc("jingyan", C('z_phonecode'));
            }
            $this->note("验证成功", __ROOT__ . "/user/home");
        } else {
            $this->note("页面错误", null, 3);
        }
    }

}
