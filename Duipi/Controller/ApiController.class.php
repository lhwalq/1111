<?php

/**
 * 用户
 * addtime 2016/02/18
 */

namespace Duipi\Controller;

use Think\Controller;

class ApiController extends BaseController {

    public function _initialize() {
        $this->getAdminRight(CONTROLLER_NAME, ACTION_NAME);
//        dump(CONTROLLER_NAME."/".ACTION_NAME);exit;
        $filter = array("login", "wxlogin", "mobilecheck2", "wx_callback", "sendmobcode", "mobileregbind", "userlogin", "register", "mobilecheck", "checkemail", 'sendmobile', "emailok", "emailcheck", "checkname", "userMobile", "uname","userphotoup","singphotoup","wxloginpc","wx_callbackpc");
        //ACTION_NAME != "login" && ACTION_NAME != "register" && ACTION_NAME != "mobilecheck" && ACTION_NAME != "checkemail" && ACTION_NAME != "sendmobile" && ACTION_NAME != "emailok" && ACTION_NAME != "emailcheck" && ACTION_NAME != "checkname" && ACTION_NAME != "userMobile"
        $this->userinfo = $this->getUserInfo();
        if ($this->userinfo) {
            $this->assign("huiyuan", $this->userinfo);
        } else if ((!in_array(ACTION_NAME, $filter) && ACTION_NAME != 'mobileregsn' && ACTION_NAME != 'swxlogin' && ACTION_NAME != 'swx_callback')) {
            $this->autoNote("请先登录", C("URL_DOMAIN") . "/Suser/login");
        }
        if (!ismobile()) {
            $this->assign("huiyuan", $this->userinfo);
        }
    }

    public function swxlogin() {
        $user = $this->userinfo;
        session_start();
        $state = md5(uniqid(rand(), TRUE));
        $_SESSION["wxState"] = $state;
        $redirect_uri = urlencode(C("URL_DOMAIN") . "/api/swx_callback/");
        $appid = C("appid");
        $wxurl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_userinfo&state=$state#wechat_redirect";
        header("Location: $wxurl");
    }

//微信登陆返回页面
    public function swx_callback() {
        $id = I("id", 0);
        session_start();

        if (I('state') != $_SESSION['wxState']) {
            $this->notemobile("登录验证失败!", C("URL_DOMAIN") . "/Suser/login");
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
 $iipp=$_SERVER["REMOTE_ADDR"];
 $tit=C('web_name_two');

	  $template = array('touser' => $wx_openid,
                      'template_id' => $dengluid,
                      'url' => C("URL_DOMAIN") ."/user/home",
                      'topcolor' => "#7B68EE",
                      'data' => array('first'    => array('value' => "您好，欢迎登陆".$tit,
                                                         'color' => "#743A3A",
                                                        ),
                                      'keyword1' => array('value' =>  date('Y-m-d h:i:s',time()),
                                                         'color' => "#FF0000",
                                                        ),
                                       'keyword2'     => array('value' => $this->huode_ip_dizhi($iipp),
                                                         'color' => "#0000FF",
                                                        ),
                                      'remark'     => array('value' => "\\n如非本人登陆,请修改密码！",
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
                "band" => 'weixinsc',
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
                $callback_url = C("URL_DOMAIN") . "/Suser/home/";
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
                $callback_url = C("URL_DOMAIN") . "/Suser/home/";

                header("Location:$callback_url");
            } else {
                $callback_url = C("URL_DOMAIN") . "/Suser/home/";
                header("Location:$callback_url");
            }
        }
    }

	


}
