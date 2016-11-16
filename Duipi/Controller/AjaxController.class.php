<?php

/**
 * 商品
 * addtime 2016/02/18
 */

namespace Duipi\Controller;

use Think\Controller;

class AjaxController extends BaseController {

    private $Mcartlist;

    public function __construct() {
        parent::__construct();
        //查询购物车的信息
        $Mcartlist = cookie("Cartlist");
        $this->Cartlist = cookie('Cartlist');
        $this->Mcartlist = json_decode(stripslashes($Mcartlist), true);
    }

    public function app_pay_lock() {
        $pay_lock = D("configs")->where(array("name" => "app_pay"))->find();

        if ($pay_lock['value'] == 1) {
            $info['status'] = 1;
        } else {
            //$info['status']='您没法购买商品';
            $info['status'] = 1;
        }

        echo json_encode($info);
    }

//未完成
    function callbackWechat() {
		
      //  $procode = $this->segment(4);
        $wx_openid = I("unionid");
file_put_contents('hh2.txt',$wx_openid);
        if (empty($wx_openid)) {
            _note("绑定出错，请联系管理员。");
            die();
        }
        //http://localhost/mobile/mobile/callbackAPICloud?openid=4444444&nickname=yuker4&headimgurl=http://wx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/46
        //http://localhost/mobile/mobile/callbackAPICloud?openid=36987456&nickname=yuker
        $nickname = I("nickname");

		$go_user_info = M('yonghu_band')->where(array("b_code" => "$wx_openid", "b_type" => "weixin"))->limit("1")->find();
       
         $ip = $this->huode_ip_dizhi();
        $time = time();

        if (!$go_user_info) {
            // $wxImg = file_get_contents($_GET["headimgurl"]);
            $timeStamp = time();
            //$localImg = $_SERVER['DOCUMENT_ROOT'] . '/love/uploads/photo/' . $timeStamp . '.jpg';
            //file_put_contents($localImg, $wxImg);
            $userpass = md5("123456");
            $go_user_img = 'photo/member.jpg';
            $go_user_time = time();
            
$data = array(
                "username" => $nickname,
                "password" => $userpass,
                "img" => $go_user_img,
                "band" => 'app',
                "time" => $go_user_time,
                "money" => '9999',
                "first" => '1',
                "code" => $procode,
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
                "b_code" => $wx_openid,
                "b_time" => $go_user_time 
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
            
           $se3 = D("yonghu")->where("uid = $uid")->save(array("user_ip" => "$ip", "login_time" => "$time"));
	
$member = D('yonghu')->where(array("uid" => $uid))->field('uid,password,mobile,email')->find();
           $se1 = cookie("uid", $this->encrypt($member['uid']), 60 * 60 * 24 * 7);
            $se2 = cookie("ushell", $this->encrypt(md5($member['uid'] . $member['password'] . $member['mobile'] . $member['email'])), 60 * 60 * 24 * 7);



          

                $callback_url = C("URL_DOMAIN") . "/user/home/";

                header("Location:$callback_url");
           
        }
    }

    //微信用户登录
    public function weChatLogin() {
        $uid = I("uid");
        $password = I("password");


        $member = D("yonghu")->where(array("uid" => $uid, "password" => $password))->find();

        if (!$member) {
            //帐号不存在错误
            $user['state'] = 1;
            $user['content'] = '账号或密码错误';
        } else if (!is_array($member)) {
            //帐号不存在错误
            $user['state'] = 1;
            $user['content'] = '账号或密码错误';
        } else {
            //登录成功 
            $user['state'] = 0;
            $user['userid'] = $member['uid'];
            $user['headimg'] = __PUBLIC__ . "/uploads/" . $member['img'];
            $user['username'] = $member['username'];
            $user['password'] = $member['password'];
            $user['money'] = $member['money'];
        }
        echo json_encode($user);
    }

    public function app_login() {
        $username = I("username");
        $password = md5(i("password"));

        $logintype = '';
        if (strpos($username, '@') == false) {
            //手机				
            $logintype = 'mobile';
        } else {
            //邮箱
            $logintype = 'email';
        }

        $member = D("yonghu")->where(array("$logintype" => $username, "password" => $password))->find();
        if (!$member) {
            //帐号不存在错误
            $user['state'] = 1;
            $user['content'] = '账号或密码错误';
        } else if (!is_array($member)) {
            //帐号不存在错误
            $user['state'] = 1;
            $user['content'] = '账号或密码错误';
        } else {
            //登录成功 
            $user['state'] = 0;
            $user['userid'] = $member['uid'];
            $user['headimg'] = __PUBLIC__ . "/uploads/" . $member['img'];
            $user['username'] = $member['username'];
            $user['password'] = $member['password'];
            $user['money'] = $member['money'];
        }
        echo json_encode($user);
    }

    //未完成
    public function more() {
        //exit;
        $webname = $this->_yys['web_name'];
        $key = "揭晓结果";
        $xiangmuid = intval($this->segment(4));
        $xiangmu = $this->db->YOne("select id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,fahuo from `@#_shangpin` where `id`='$xiangmuid' and `q_end_time` is not null LIMIT 1");



        $xiangmulist = $this->db->Ylist("select id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,fahuo from `@#_shangpin` where `sid`='$xiangmu[sid]' order by `qishu` DESC");
        $fenlei = $this->db->YOne("select * from `@#_fenlei` where `cateid` = '$xiangmu[cateid]' LIMIT 1");
        $pinpai = $this->db->YOne("select * from `@#_pinpai` where `id` = '$xiangmu[brandid]' LIMIT 1");

        //一元云购中奖码
        $q_yonghu = unserialize($xiangmu['q_user']);
        $q_yonghu_code_len = strlen($xiangmu['q_user_code']);
        $q_yonghu_code_arr = array();
        for ($q_i = 0; $q_i < $q_yonghu_code_len; $q_i++) {
            $q_yonghu_code_arr[$q_i] = substr($xiangmu['q_user_code'], $q_i, 1);
        }

        //期数显示
        $wangqiqishu = '';
        if (empty($xiangmulist[0]['q_end_time'])) {
            $wangqiqishu.='<li><a href="' . LOCAL_PATH . '/mobile/mobile/item/' . $xiangmulist[0]['id'] . '">' . "第" . $xiangmulist[0]['qishu'] . "期</a><b></b></li>";
            array_shift($xiangmulist);
        }

        foreach ($xiangmulist as $qitem) {
            if ($qitem['id'] == $xiangmuid) {

                $wangqiqishu.='<li><a class="hover" href="javascript:;"><s class="fl"></s>' . "第" . $qitem['qishu'] . "期</a><b></b></li>";
            } else {
                $wangqiqishu.='<li><a href="' . LOCAL_PATH . '/mobile/mobile/dataserver/' . $qitem['id'] . '" ><s class="fl"></s>第' . $qitem['qishu'] . '期</a></li>';
            }
        }

        //总一元云购次数
        $weer_shop_number = 0;
        //用户一元云购时间
        $weer_shop_time = 0;
        //得到一元云购码
        $weer_shop_codes = '';

        $weer_shop_list = $this->db->Ylist("select * from `@#_yonghu_yys_record` where `uid`= '$xiangmu[q_uid]' and `shopid` = '$xiangmuid' and `shopqishu` = '$xiangmu[qishu]'");
        foreach ($weer_shop_list as $weer_shop_n) {
            $weer_shop_number += $weer_shop_n['gonumber'];
            if ($weer_shop_n['huode']) {
                $weer_shop_time = $weer_shop_n['time'];
                $weer_shop_codes = $weer_shop_n['goucode'];
            }
        }

        $h = abs(date("H", $xiangmu['q_end_time']));
        $i = date("i", $xiangmu['q_end_time']);
        $s = date("s", $xiangmu['q_end_time']);
        $w = substr($xiangmu['q_end_time'], 11, 3);
        $weer_shop_time_add = $h . $i . $s . $w;
        $tt2 = $xiangmu['q_end_cp'];
        if (_yys('ssc')) {

            function calc($m, $n, $x) {

                switch ($x) {

                    case 'mod':
                        if ($n != 0) {
                            $t = bcmod($m, $n);
                        } else {
                            return $errors[0];
                        }
                        break;
                }
                $t = preg_replace("/\..*0+$/", '', $t);
                return $t;
            }

            $weer_shop_fmod = calc(($tt2 + $xiangmu['q_counttime']), $xiangmu['canyurenshu'], 'mod');
        } else {
            $weer_shop_fmod = fmod($xiangmu['q_counttime'], $xiangmu['canyurenshu']);
        }

        if ($xiangmu['q_content']) {
            $xiangmu['q_content'] = unserialize($xiangmu['q_content']);
        }
        $xiangmu['picarr'] = unserialize($xiangmu['picarr']);

        //记录	 


        $gorecode = $this->db->YOne("select * from `@#_yonghu_yys_record` where `shopid`='" . $xiangmuid . "' AND `shopqishu`='" . $xiangmu['qishu'] . "' and `uid`= '$xiangmu[q_uid]'ORDER BY id DESC LIMIT 6");

        $shopitem = 'dataserverfun';
        $curtime = time();
        //晒单数
        $shopid = $this->db->YOne("select id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,fahuo from `@#_shangpin` where `id`='$xiangmuid'");
        $yyslist = $this->db->Ylist("select id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,fahuo from `@#_shangpin` where `sid`='$shopid[sid]'");
        $shop = '';
        foreach ($yyslist as $list) {
            $shop.=$list['id'] . ',';
        }
        $id = trim($shop, ',');
        if ($id) {
            $shaidingdan = $this->db->Ylist("select * from `@#_shai` where `sd_shopid` IN ($id)");
            $sum = 0;
            foreach ($shaidingdan as $sd) {
                $shaidingdan_hueifu = $this->db->Ylist("select * from `@#_shai_hueifu` where `sdhf_id`='$sd[sd_id]'");
                $sum = $sum + count($shaidingdan_hueifu);
            }
        } else {
            $shaidingdan = 0;
            $sum = 0;
        }
        $xiangmuxq = 0;
        if (!empty($xiangmuzx)) {
            $xiangmuxq = 1;
        }

        include templates("mobile/index", "moreapp");
    }

    //计算详情
    public function runDesc() {

        $shopid = I('shopid', 0);
        $xiangmu = D("shangpin")->field("id,sid,cateid,brandid,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,q_end_qishu,fahuo")->where("id=$shopid and q_end_time is not null")->find();


        $tt2 = $xiangmu['q_end_cp'];
        if (C('ssc')) {

            function calc($m, $n, $x) {

                switch ($x) {

                    case 'mod':
                        if ($n != 0) {
                            $t = bcmod($m, $n);
                        } else {
                            return $errors[0];
                        }
                        break;
                }
                $t = preg_replace("/\..*0+$/", '', $t);
                return $t;
            }

            $weer_shop_fmod = calc(($tt2 + $xiangmu['q_counttime']), $xiangmu['canyurenshu'], 'mod');
        } else {
            $weer_shop_fmod = fmod($xiangmu['q_counttime'], $xiangmu['canyurenshu']);
        }


        $curtime = time();

        if ($xiangmu['q_content']) {
            $xiangmu['contcode'] = 0;
            $xiangmu['itemlist'] = unserialize($xiangmu['q_content']);

            foreach ($xiangmu['itemlist'] as $key => $val) {
                // 	$xiangmu['itemlist'][$key]['time']	=microt($val['time']);		 				 
                $h = date("H", $val['time']);
                $i = date("i", $val['time']);
                $s = date("s", $val['time']);
                list($timesss, $msss) = explode(".", $val['time']);
            }
        } else {
            $xiangmu['contcode'] = 1;
        }

        if (!empty($xiangmu)) {
            $xiangmu['code'] = 0;
        } else {
            $xiangmu['code'] = 1;
        }

//var_dump($xiangmu['itemlist']);
        $yyslist['code'] = 0;
        $yyslist['id'] = $xiangmu['id'];
        $yyslist['shopCode'] = $weer_shop_fmod;
        $yyslist['countTime'] = $xiangmu['q_counttime'];
        $yyslist['cyrs'] = $xiangmu['canyurenshu'];
        foreach ($xiangmu['itemlist'] as $key => $val) {
            //var_dump($val);
            $yyslist['record0'][$key]['buytime'] = microt($val['time']);
            $yyslist['record0'][$key]['buyName'] = $val[username];
            $yyslist['record0'][$key]['userweb'] = $val[uid];
            $yyslist['record1'][$key]['buytime'] = microt($val['time']);
            $yyslist['record1'][$key]['buyName'] = $val[username];
            $yyslist['record1'][$key]['userweb'] = $val[uid];
            $h = date("H", $val['time']);
            $i = date("i", $val['time']);
            $s = date("s", $val['time']);
            list($timesss, $msss) = explode(".", $val['time']);
            $yyslist['record1'][$key]['timeCodeVal'] = $h . $i . $s . $msss;
            $yyslist['record2'][$key]['userweb'] = $val[uid];

            $yyslist['record2'][$key]['buyName'] = $val[username];
            $yyslist['record2'][$key]['buytime'] = microt($val['time']);
        }


        echo json_encode($yyslist);
    }

    //未完成
    //商品详情
    public function goodsdesc() {
        $webname = $this->_yys['web_name'];
        $key = "图文详情";
        $xiangmuid = I('shopid');
        $desc = $this->db->YOne("select content from `@#_shangpin` where `id`='$xiangmuid'");
        if (!$desc) {
            $message['status'] = -1;
            $message['content'] = '无此图文详情';
            echo json_encode($message);
            exit;
        }
        $message['status'] = 0;
        $message['content'] = $desc['content'];
        echo json_encode($message);
    }

    public function calResult() {
        $xiangmuid = intval($this->segment(4));
        $xiangmu = $this->db->YOne("select id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,q_end_qishu,fahuo from `@#_shangpin` where `id`='$xiangmuid' and `q_end_time` is not null LIMIT 1");


        $xiangmuzx = $this->db->YOne("select id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,q_end_qishu,fahuo from `@#_shangpin` where `sid`='$xiangmu[sid]' and `qishu`>'$xiangmu[qishu]' and `q_end_time` is null order by `qishu` DESC LIMIT 1");



        $h = abs(date("H", $xiangmu['q_end_time']));
        $i = date("i", $xiangmu['q_end_time']);
        $s = date("s", $xiangmu['q_end_time']);
        $w = substr($xiangmu['q_end_time'], 11, 3);
        $weer_shop_time_add = $h . $i . $s . $w;

        $tt2 = $xiangmu['q_end_cp'];
        if (_yys('ssc')) {

            function calc($m, $n, $x) {

                switch ($x) {

                    case 'mod':
                        if ($n != 0) {
                            $t = bcmod($m, $n);
                        } else {
                            return $errors[0];
                        }
                        break;
                }
                $t = preg_replace("/\..*0+$/", '', $t);
                return $t;
            }

            $weer_shop_fmod = calc(($tt2 + $xiangmu['q_counttime']), $xiangmu['canyurenshu'], 'mod');
        } else {
            $weer_shop_fmod = fmod($xiangmu['q_counttime'], $xiangmu['canyurenshu']);
        }

        //var_dump($weer_shop_fmod);
        //$weer_shop_fmod = fmod($xiangmu['q_counttime'],$xiangmu['canyurenshu']);

        if ($xiangmu['q_content']) {
            $xiangmu['q_content'] = unserialize($xiangmu['q_content']);
        }
        $xiangmu['picarr'] = unserialize($xiangmu['picarr']);

        include templates("mobile/index", "calResultapp");
    }

    //用户信息查询
    public function app_user_info() {
        $userid = I('userid');
        $pass = I('password');
        $member = D("yonghu")->where(array("uid" => $userid, "password" => $pass))->find();

        $fufen_yys = C("user_fufen", '', 'member');

        if (!empty($member)) {
            //查询成功
            $user['ststus'] = 1;
            $user['userid'] = $member['uid'];
            $user['headimg'] = __PUBLIC__ . "/uploads/" . $member['img'];
            $user['username'] = $member['username'];
            $user['password'] = $member['password'];
            $user['money'] = $member['money'];
            $user['score'] = $member['score'];
            $user['money1'] = $member['money1'];
            if ($fufen_yys['fufen_yuan']) {

                $user['fufen_yuan'] = $fufen_yys['fufen_yuan'];
            }
        }
        echo json_encode($user);
    }

//未完成
    //找回密码
    public function app_forget_pass() {
        $username = safe_replace($this->segment(4)); //账号
        $password = md5(safe_replace($this->segment(5))); //密码
        $lock = safe_replace($this->segment(6)); //验证码是否通过
        if ($lock == "Y") {
            $member = $this->db->YOne("select * from `@#_yonghu` where `mobile`='$username'");
            if (!$member) {
                //帐号不存在错误
                $user['state'] = 1;
                $user['content'] = "手机号不存在";
            } else {
                //验证成功 
                $user['state'] = 0;
                $this->db->Query("update `@#_yonghu` SET `password` = '$password' where `uid` = '" . $member['uid'] . "'");
            }
        } else {
            $user['state'] = 2;
            $user['content'] = "验证不通过";
        }

        echo json_encode($user);
    }

//未完成
    //手机号注册
    public function app_reg() {
        $time = time(); //注册时间
        $mobile = safe_replace($this->segment(4)); //注册账号
        $pass = safe_replace($this->segment(5));
        $password = md5($pass); //注册密码
        $lock = safe_replace($this->segment(6)); //验证码是否通过
        if ($lock == "Y") {
            //验证码通过
            $member = $this->db->YOne("SELECT * FROM `@#_yonghu` WHERE `mobile` = '$mobile' LIMIT 1");
            if (!empty($member)) {
                $user['state'] = 2;
                $user['content'] = "手机号存在";
            } else if (empty($mobile)) {
                $user['state'] = 3;
                $user['content'] = "手机号不能为空";
            } else if (empty($pass)) {
                $user['state'] = 4;
                $user['content'] = "密码不能为空";
            } else {
                $user['state'] = 0; //注册成功
                $sql = "INSERT INTO `@#_yonghu`(username,mobile,password,img,qianming,emailcode,mobilecode,reg_key,yaoqing,time)VALUES('无名小卒','$mobile','$password','photo/member.jpg','让别人看到不一样的你！','-1','1','','','$time')";
                $this->db->Query($sql);
            }
        } else {
            $user['state'] = 1;
            $user['content'] = "验证不通过";
        }
        echo json_encode($user);
    }

//未完成
    //微信登录
    public function app_wx_login() {
        $nickname = safe_replace(I("nickname"));
        $unionid = safe_replace(I("unionid"));
        if (!empty($unionid)) {
            $go_user_info = D("yonghu_band")->where(array("b_code" => $unionid, "b_type" => "weixin"))->find();

            $bb_code = $go_user_info['b_code'];
            if (empty($go_user_info)) {//未注册过
                $userpass = md5("123456"); //初始密码
                $go_user_time = time(); //注册时间
                $q1 = D("yonghu")->add(array("username" => $nickname, "password" => $userpass, "img" => "photo/member.jpg", "band" => "weixin", "time" => $go_user_time, "money" => 0));
                $uid = $this->db->insert_id(); //获取注册id
                D("yonghu")->add(array("b_uid" => $uid, "b_type" => "weixin", "b_code" => $unionid, "b_time" => $go_user_time)); //注册微信
                $member = D("yonghu")->where(array("uid" => $uid))->find(); //用户数据
                $user['status'] = 0;
                $user['userid'] = $member['uid'];
                $user['headimg'] = __PUBLIC__ . "/uploads/" . $member['img'];
                $user['username'] = $member['username'];
                $user['password'] = $member['password'];
                $user['money'] = $member['money'];
                $user['img'] = $member['img'];
            } else {//已经注册过
                $uid = $go_user_info["b_uid"];
                $member = D("yonghu")->where(array("uid" => $uid))->find(); //用户数据
                $user['status'] = 0;
                $user['userid'] = $member['uid'];
                $user['headimg'] = __PUBLIC__ . "/uploads/" . $member['img'];
                $user['username'] = $member['username'];
                $user['password'] = $member['password'];
                $user['money'] = $member['money'];
                $user['img'] = $member['img'];
            }
        } else {
            $user['status'] = 1;
        }
        echo json_encode($user);
    }

    //微信头像下载
    public function down_wx_img() {
        $headimgurl = safe_replace(I('headimgurl'));
        $uid = safe_replace(I('userid'));
        $IMG = file_get_contents($headimgurl);
        $mulu = date("Ymd", time());
        $path = __PUBLIC__ . '/uploads/touimg/' . $mulu;
        if (!file_exists($path)) {
            mkdir($path);
        }
        $time = time();
        file_put_contents($path . "/" . $time . '.jpg', $IMG);
        $go_user_img = 'touimg/' . $mulu . '/' . $time . '.jpg';
        $size = size($go_user_img);
        D("yonghu")->where(array("uid" => $uid))->save(array("img" => $go_user_img));
    }

    public function my_info() {
        $userid = safe_replace(I('userid')); //用户id
        $pass = safe_replace(I('pass')); //用户密码
        $member = D("yonghu")->where(array("uid" => $userid, "password" => $pass))->find();
        if (!empty($member)) {
            $user['userid'] = $member['uid'];
            $user['headimg'] = __PUBLIC__ . "/uploads/" . $member['img'];
            $user['username'] = $member['username'];
            if (!empty($member['mobile'])) {
                $user['user'] = $member['mobile'];
            } else if (!empty($member['email'])) {
                $user['user'] = $member['email'];
            } else {
                $user['user'] = $member['band'];
            }
        } else {
            $user['status'] = 1; //用户出错
        }
        echo json_encode($user);
    }

    //充值回调
    public function add_money_success() {
        //获取Webhooks
        $input_data = json_decode(file_get_contents('php://input'), true);
        if ($input_data['type'] == 'charge.succeeded' && $input_data['data']['object']['paid'] == true) {
            $order_no = $input_data['data']['object']['order_no']; //订单号
            $paytype = $input_data['data']['object']['app']; //核对APP号
            //支付成功
            if ($paytype == 'app_mrrzLGe1mjz5rb14') {
                $dindan = D("yonghu_addmoney_record")->where(array("code" => $order_no))->find(); //查询订单
                $member = D("yonghu")->where(array("uid" => $dingdan['uid']))->find(); //查询订单
                $new_money = $member['money'] + $dindan['money'] . '.00';
                if ($dindan['status'] != '已付款') {
                    D("yonghu")->where(array("uid" => $dubgdan['uid']))->save(array("money" => $new_money)); //充值金额
                    D("yonghu_addmoney_record")->where(array("code" => $order_no))->save(array("status" => "已付款")); //更新订单
                }
            }
        }
    }

    public function up_my_headimg() {
        $imgbase64 = safe_replace(I("pic"));
        $userid = safe_replace(I('userid')); //用户id
        $pass = I('pass'); //用户密码
        $member = D("yonghu")->where(array("uid" => $userid, "password" => $pass))->find();
        if (!empty($imgbase64) && !empty($member)) {
            /* 图片处理 */
            $userimg_url = upheadimg($imgbase64);
            $user['status'] = 0; //上传成功
            D("yonghu")->where(array("uid" => $userid))->save(array("img" => $userimg_url));
            $user['headerimg'] = __PUBLIC__ . "/uploads/" . $userimg_url;
        } else {
            $user['status'] = 1; //上传出错
        }
        echo json_encode($user);
    }

    //设置新手机号
    public function new_mobile() {
        $userid = safe_replace(I('userid')); //用户id
        $pass = safe_replace(I('pass')); //用户密码
        $mobile = safe_replace(I('newmobile')); //新手机号
        $lock = safe_replace(I('lock')); //短信验证是否通过
        $newmember = $this->db->YOne("select * from `@#_yonghu` where `mobile` = '$mobile' LIMIT 1");
        if (!empty($newmember)) {
            $info['status'] = 2;
        } else if ($lock == 'Y') {
            $member = $this->db->YOne("select * from `@#_yonghu` where `uid` = '$userid' and `password` = '$pass' LIMIT 1");
            if (!empty($member)) {
                if ($this->db->Query("update `@#_yonghu` SET `mobile` = '$mobile' where `uid` = '$userid'")) {
                    $info['status'] = 0;
                } else {
                    $info['status'] = 1;
                }
            } else {
                $info['status'] = 1;
            }
        } else {
            $info['status'] = 1;
        }
        echo json_encode($info);
    }

    //修改昵称
    public function new_username() {
        $userid = safe_replace(I('userid')); //用户id
        $pass = safe_replace(I('pass')); //用户密码
        $newname = safe_replace(I('newname')); //新昵称
        $lock = safe_replace(I('lock')); //短信验证是否通过
        $newnickname = D("yonghu")->where(array("uid" => $userid, "password" => $pass))->find();
        if (!empty($newnickname)) {
            if ($lock == "Y" && !empty($newname)) {
                if (D("yonghu")->where(array("uid" => $userid))->save(array("username" => $newname))) {
                    $info['status'] = 0;
                } else {
                    $info['status'] = 1;
                }
            } else {
                $info['status'] = 1;
            }
        } else {
            $info['status'] = 1;
        }
        echo json_encode($info);
    }

    //收货地址数据
    public function app_address() {
        $userid = safe_replace(I('userid')); //用户id
        $pass = safe_replace(I('pass')); //用户密码
        $ressid = safe_replace(I('ressid')); //收货id
        $member = D("yonghu")->where(array("uid" => $userid, "password" => $pass))->find();

        if (empty($ressid)) {
            if (!empty($member)) {
                D("yonghu_dizhi")->where(array("uid" => $userid))->select();

                if (!empty($address)) {
                    $info['status'] = 0; //用户有添加过收货信息
                    foreach ($address as $key => $val) {
                        $info['ress'][$key]['id'] = $val['id']; //收货地址id
                        $info['ress'][$key]['uid'] = $val['uid']; //收货用户id
                        $info['ress'][$key]['sheng'] = $val['sheng']; //省
                        $info['ress'][$key]['shi'] = $val['shi']; //市
                        $info['ress'][$key]['xian'] = $val['xian']; //县
                        $info['ress'][$key]['jiedao'] = $val['jiedao']; //详细地址
                        $info['ress'][$key]['shouhuoren'] = $val['shouhuoren']; //收货人
                        $info['ress'][$key]['default'] = $val['default']; //收货人
                        $info['ress'][$key]['mobile'] = $val['mobile']; //手机号
                        $info['ress'][$key]['zfb'] = $val['zfb']; //支付宝（增加字段）
                        $info['ress'][$key]['zfbname'] = $val['zfbname']; //支付宝名字（增加字段）
                    }
                } else {
                    $info['status'] = 2; //用户没有添加过收货信息
                }
            } else {
                $info['status'] = 1;
            }
        } else {
            if (!empty($member)) {
                $address = D("yonghu_dizhi")->where(array("uid" => $userid, "id" => $ressid))->find();
                if (!empty($address)) {
                    $info['status'] = 0; //用户有添加过收货信息
                    $info['ress']['id'] = $address['id']; //收货地址id
                    $info['ress']['uid'] = $address['uid']; //收货用户id
                    $info['ress']['sheng'] = $address['sheng']; //省
                    $info['ress']['shi'] = $address['shi']; //市
                    $info['ress']['xian'] = $address['xian']; //县
                    $info['ress']['jiedao'] = $address['jiedao']; //详细地址
                    $info['ress']['shouhuoren'] = $address['shouhuoren']; //收货人
                    $info['ress']['default'] = $address['default']; //收货人
                    $info['ress']['mobile'] = $address['mobile']; //手机号
                    $info['ress']['zfb'] = $address['zfb']; //支付宝（增加字段）
                    $info['ress']['zfbname'] = $address['zfbname']; //支付宝名字（增加字段）
                }
            } else {
                $info['status'] = 1;
            }
        }
        echo json_encode($info);
    }

    //编辑收货地址
    public function edit_address() {
        $userid = safe_replace(I('userid')); //用户id
        $pass = safe_replace(I('pass')); //用户密码
        $ressid = safe_replace(I('ressid')); //收货id
        $shouhuoren = safe_replace(I('shouhuoren')); //收货人
        $sheng = safe_replace(I('sheng')); //省
        $shi = safe_replace(I('shi')); //市
        $xian = safe_replace(I('xian')); //市
        $jiedao = safe_replace(I('jiedao')); //详细地址
        $mobile = safe_replace(I('mobile')); //手机号
        $default = safe_replace(I('default')); //是否设置默认
        $zfb = safe_replace(I('zfb')); //支付宝账号（增加字段）
        $zfbName = safe_replace(I('zfbname')); //支付宝名字（增加字段）
        $time = time(); //修改时间
        $member = D("yonghu")->where(array("uid" => $userid, "password" => $pass)) -> find();
        if (!empty($member)) {
            $up = D("yonghu_dizhi")->where(array("id" => $ressid))->save(array("sheng" => $sheng, "shi" => $shi, "xian" => $xian, "jiedao" => $jiedao, "shouhuoren" => $shouhuoren, "mobile" => $mobile, "zfb" => $zfb, "zfbname" => $zfbName, "default" => $default, "time" => $time));
            if ($up) {
                $info['status'] = 0;
            }
        } else {
            $info['status'] = 1;
        }
        echo json_encode($info);
    }

    //删除收货地址
    public function del_address() {
        $userid = safe_replace(I('userid')); //用户id
        $pass = safe_replace(I('pass')); //用户密码
        $ressid = safe_replace(I('ressid')); //收货id
        $member = D("yonghu")->where(array("uid" => $userid, "password" => $pass))->find();
        if (!empty($member)) {
            $address = D("yonghu_dizhi")->where(array("uid" => $userid, "id" => $ressid))->find();
            if (!empty($address)) {
                if (D("yonghu_dizhi")->where(array("id" => $ressid))->delete()) {
                    $info['status'] = 0;
                }
            } else {
                $info['status'] = 1;
            }
        } else {
            $info['status'] = 1;
        }
        echo json_encode($info);
    }

    //中奖商品收货
    public function my_winner_shop() {
        $userid = safe_replace(I('userid')); //用户id
        $pass = safe_replace(I('pass')); //用户密码
        $id = safe_replace(I('id')); //收货id

        $zhenid = D("shangpin")->where(array("id" => $id))->find();

        $recordmmm = D("yonghu_yys_record")->where("shopid='$id' and uid='$userid' and goucode LIKE  '%$zhenid[q_user_code]%'")->find();
        $id = $recordmmm[id];
        $member = D("yonghu")->where(array("uid" => $userid, "password" => $pass))->find();

        if (!empty($member)) {
            //获取默认收货地址
            $address = D("yonghu_dizhi")->where(array("uid" => $userid, "default" => "Y"))->find();
            if (!empty($address)) {
                $info['addres'] = $address;
            } else {
                $info['addres'] = 'fail';
            }
            //奖品状态
            $info['shop'] = D("yonghu_yys_record")->where(array("uid" => $userid, "id" => $id))->find();
        }
        echo json_encode($info);
    }

    //确认收货地址
    public function sure_address() {
        $userid = safe_replace(I('userid')); //用户id
        $pass = safe_replace(I('pass')); //用户密码
        $id = safe_replace(I('id')); //收货id
        $member = D("yonghu")->where(array("uid" => $userid, "password" => $pass))->find();
        $zhenid = D("shangpin")->field("q_user_code from")->find();
        $recordmmm = D("yonghu_yys_record")->where("shopid='$id' and uid='$userid' and goucode LIKE  '%$zhenid[q_user_code]%'")->find();
        $id = $recordmmm[id];
        if (!empty($member)) {
            //获取默认收货地址
            $address = D("yonghu_dizhi")->where(array("uid" => $userid, "default" => "Y"))->find();
            if (!empty($address)) {
                $ress = $address['sheng'] . ',' . $address['shi'] . ',' . $address['xian'] . ',' . $address['jiedao'];
                $time = time();
                $ress_user_info = $address['shouhuoren'] . '|' . $address['mobile'];


                if ($recordmmm['leixing'] == 0) {

                    $status = '已付款,未发货,未完成,已提交地址';
                } else {
                    $status = '已付款,已发货,已完成';
                }
                $up = D("yonghu_yys_record")->where(array("id" => $id))->save(array("shouhuo" => "1", "status" => $status, "qq" => $qq, "youbian" => $youbian, "shipRemark" => $shipRemark, "shipTime" => $shipTime, "email" => $email, "tell" => $tell, "shouhuoren" => $address['shouhuoren'], "mobile" => $address['mobile'], "sheng" => $address['sheng'], "xian" => $address['xian'], "jiedao" => $address['jiedao'], "fhtime" => $time, "wei" => "0", "confrim_addr" => $ress, "confrim_addr_time" => $time, "confrim_addr_uinfo" => $ress_user_info));
                if ($up) {
                    $info['status'] = 0;
                } else {
                    $info['status'] = 111;
                }
            }
        }
        echo json_encode($info);
    }

    //确认收货
    public function confirmReceipt_shop() {
        $userid = safe_replace(I('userid')); //用户id
        $pass = safe_replace(I('pass')); //用户密码
        $id = safe_replace(I('id')); //收货id
        $zhenid = D("shangpin")->field("q_user_code")->where(array("id" => $id))->find();
        $recordmmm = D("yonghu_yys_record")->where("shopid='$id' and uid='$userid' and goucode LIKE  '%$zhenid[q_user_code]%'")->find();
        $id = $recordmmm[id];
        $member = D("yonghu")->where(array("uid" => $userid, "password" => $pass))->find();
        if (!empty($member)) {
            $up = D("yonghu_yys_record")->where(array("id" => $id))->save(array("status" => '已付款,已发货,已完成'));
            if ($up) {
                $info['status'] = 0;
            }
        }

        echo json_encode($info);
    }

    //商品转积分显示
    public function goodsToMoney1() {
        $id = safe_replace(I('id')); //收货id
        $sshop = D("shangpin")->where(array("id" => $id))->find();
        if ($sshop['leixing'] == 0) {
            $jia = $sshop['yuanjia'] * $bili["fufen_yongjinqd0"];
        } else if ($sshop['leixing'] == 1) {
            $jia = $sshop['yuanjia'] * $bili["fufen_yongjinqd1"];
        } else {
            $jia = $sshop['yuanjia'] * $bili["fufen_yongjinqd2"];
        }
        $message['status'] = 0;
        $message['money1'] = $jia;

        echo json_encode($message);
    }

    //积分转换
    public function excorderdetailsb() {
        $uid = $_POST['userid'];
        $id = $_POST['id'];
        $zhenid = D("shangpin")->field("q_user_code")->where(array("id" => $id))->find();
        $recordmmm = D("yonghu_yys_record")->where("shopid='$id' and uid='$uid' and goucode LIKE  '%$zhenid[q_user_code]%'")->find();
        $time = time();

        $shopinfoss = $this->db->YOne("select * from `@#_shangpin` where `id`='$recordmmm[shopid]' LIMIT 1");
        if (empty($shopinfoss['yuanjia'])) {
            $message['status'] = -1;
            $message['content'] = '未设置原价,不能兑换为积分';
            echo json_encode($message);
            exit;
        }
        $bili = C("user_fufen", '', 'member');
        if ($recordmmm['leixing'] == 0) {
            $jia = $shopinfoss['yuanjia'] * $bili["fufen_yongjinqd0"];
        } else if ($recordmmm['leixing'] == 1) {
            $jia = $shopinfoss['yuanjia'] * $bili["fufen_yongjinqd1"];
        } else {
            $jia = $shopinfoss['yuanjia'] * $bili["fufen_yongjinqd2"];
        }



        $pay_zhifu_name = '积分';
        if ($recordmmm[wei] == 1) {
            $message['status'] = -1;
            $message['content'] = '已经兑换为积分';
            echo json_encode($message);
            exit;
        }
        D("yonghu")->where(array("uid" => $uid))->setInc("money1", $jia);
        D("yonghu_yys_record")->where(array("id" => $recordmmm['id'], "uid" => $uid)) - save(array("status" => '已付款,已发货,已完成', "wei" => "1"));
        D("yonghu_zhanghao1")->adda(array("uid" => $uid, "type" => "-1", "pay" => $pay_zhifu_name, "content" => '商品id(' . $shopinfoss['id'] . ')转换获得积分', "money" => $jia, "time" => $time));
        $message['status'] = 0;
        echo json_encode($message);
    }

    //积分转余额
    public function jifenChangeMoney() {
        $uid = I('userid');
        $pass = I('pass');
        $time = time();
        $member = D("yonghu")->where(array("uid" => $uid, "password" => $pass))->find();
        $amount = isset($_POST['amount']) ? $_POST['amount'] : "";
        $pay_zhifu_name = '积分';
        if (isset($_POST['amount'])) {
            if (floor($amount) != $amount) {
                $message['status'] = -1;
                $message['content'] = "不能是小数";
                echo json_encode($message);
                exit;
            }
            if ($amount > $member['money1'] || $member['money1'] <= 0) {
                $message['status'] = -1;
                $message['content'] = "积分不足";
                echo json_encode($message);
                exit;
            }
            $amountsss = $amount * 0.01;
            if ($amountsss < 10) {
                $shouxufei = $amount * 0.01;
            } else {
                $shouxufei = '10';
            }
            $shouxufei = 0;
            D("yonghu")->where(array("uid" => $uid))->setDec("money1", $amount);
            D("yonghu")->where(array("uid" => $uid))->setInc("money", $amount);
            D("yonghu_zhanghao1")->add(array("uid" => $uid, "type" => "-1", "pay" => $pay_zhifu_name, "content" => "积分转账到余额", "money" => $amount, "time" => $time));
            $message['status'] = 0;
            $message['content'] = "充值到余额成功";
            echo json_encode($message);
            exit;
        }
    }

    //积分提现到支付宝
    public function jifenChangeAlipay() {
        $uid = I('userid');
        $pass = I('pass');
        $member = D("yonghu")->where(array("uid" => $uid, "password" => $pass))->find();
        $amount = I('amount') ? I('amount') : "";
        $alipayname = I('alipayname') ? I('alipayname') : "";
        $alipayusername = I('alipayusername') ? I('alipayusername') : "";
        $time = time();

        if ($amount < 10) {
            $message['status'] = -1;
            $message['content'] = "积分提现不能小于10";
            echo json_encode($message);
            exit;
        }
        if ($amount > $member['money1'] || !is_numeric($amount)) {
            $message['status'] = -1;
            $message['content'] = "积分不足,不能提现";
            echo json_encode($message);
            exit;
        }
        $amountsss = $amount * 0.01;
        if ($amountsss < 10) {
            $shouxufei = $amount * 0.01;
        } else {
            $shouxufei = '10';
        }
        D("yonghu")->where(array("uid" => $uid))->setDec("money1", $amount);
        D("yonghu_cashout1")->add(array("uid" => $uid, "money" => $amount, "username" => $alipayname, "bankname" => $alipayusername, "branch" => $branch, "banknumber" => $banknumber, "linkphone" => $linkphone, "time" => $time, "shenhe" => "0"));
        $message['status'] = 0;
        $message['content'] = "提现申请成功,请等待审核";
        echo json_encode($message);
    }

	//参与记录
	public function my_record(){
		$userid=safe_replace(I('userid'));//用户id
		$pass=safe_replace(I('pass'));//用户密码
		$pagenum=safe_replace(I('page'));//分页查询
		$type=safe_replace(I('type'));//查询类型
		$member=D("yonghu")->where("uid=$userid and password!=$pass")->find();
		if(!empty($member)){
			if($type==1){//进行中参与记录
				$member_record=D("yonghu_yys_record a")->join("shangpin b on a.shopid=b.id")->where("b.q_end_time is null AND a.uid = $userid");//查询是否有进行中商品
				if(!empty($member_record)){
					$num=10;//每页数量
					$total=D("yonghu_yys_record a")->field("DISTINCT,shopid")->join("yys_shangpin b on a.shopid=b.id")->where("b.q_end_time is null AND a.uid = $userid");;//进行中总数 
					$page = new \Claduipi\Tools\page;//分页方法
					$page->config($total,$num,$pagenum,"0");//分页
					$record_jinxing=D("yonghu_yys_record a")->join("yys_shangpin b on a.shopid=b.id")->field("count(distinct a.shopid)")->where(" b.q_end_time is null AND a.uid = $userid")->group("a.shopid")->order("a.time DESC")->limit(($pagenum - 1) * $num, $num)->select();//查询进行中数据

					if(!empty($record_jinxing)){
						$info['status']=0;//查询成功
						foreach($record_jinxing as $key=>$val){
							$info['list'][$key]['is_shi']=$val['is_shi'];//ten
							$info['list'][$key]['title']=$val['title'];//商品标题
							$info['list'][$key]['shopid']=$val['id'];//商品id
							$info['list'][$key]['shop_img']=YYS_LOCAL_PATH."/love/uploads/".$val['thumb'];//商品图片
							$info['list'][$key]['qishu']=$val['qishu'];//商品期数
							$info['list'][$key]['jindu']=$val['canyurenshu']/$val['zongrenshu']*100;//开奖进度
							$info['list'][$key]['zongrenshu']=$val['zongrenshu'];//商品期数
							$info['list'][$key]['shenyurenshu']=$val['shenyurenshu'];//商品期数
						}						
					}else{
						$info['status']=3;//分页数据结束
					}
				}else{
					$info['status']=2;//用户没有进行中商品
				}
			}else if($type==2){//已揭晓参与记录
				$num=10;//每页数量
				$page = new \Claduipi\Tools\page;
				$page->config($total,$num,$pagenum,"0");		
				$shop=D("shangpin")->where("q_end_time !='' and q_showtime !='Y' and q_uid =$userid")->order("q_end_time DESC")->limit(($pagenum - 1) * $num, $num)->select();
				$recordinfo=D("shangpin")->where("q_end_time !='' and q_showtime !='Y' and q_uid =$userid")->order("q_end_time DESC")->find();

				if(!empty($recordinfo)){
					if(!empty($shop)){
						$info['status']=0;//查询成功
						foreach($shop as $key=>$val){
							$info['list'][$key]['is_shi']=$val['is_shi'];//ten
							$info['list'][$key]['title']=$val['title'];//商品标题
							$info['list'][$key]['id']=$val['id'];//商品id
							$info['list'][$key]['shop_img']=__ROOT__."/public/uploads/".$val['thumb'];//商品图片
							$info['list'][$key]['qishu']=$val['qishu'];//商品期数
							$info['list'][$key]['q_end_time']=date("Y-m-d h:i",$val['q_end_time']);//商品揭晓时间date("Y-m-d",$val['q_end_time']);
							$info['list'][$key]['q_user_code']=$val['q_user_code'];//中奖号码
							$info['list'][$key]['username']=$this->huode_user_name($val['q_uid'],"username");//中奖用户

							$user_shop_number =D("yonghu_yys_record")->field("sum(gonumber) as gonumber")->where(array("uid"=>$val['q_uid'],"shopid"=>$val['id']))->find();//用户购买数量查询

							$info['list'][$key]['gonumber'] = $user_shop_number['gonumber'];//用户购买数量
						}
					}else{
						$info['status']=3;//分页数据结束
					}
				}else{
					$info['status']=2;//用户没有参与过
				}
			}else if($type==3){//全部参与记录
				$allbuy=D("yonghu_yys_record")->where(array("uid"=>$userid))->find();
				if(!empty($allbuy)){
					$num=10;//每页数量
					$total=D("yonghu_yys_record")->where(array("uid"=>$userid))->count();
					$page = new \Claduipi\Tools\page;	
					$page->config($total,$num,$pagenum,"0");
					$userbuy=D("yonghu_yys_record")->where(array("uid"=>$userid))->order("time DESC")->limit(($pagenum - 1) * $num, $num)->select();
					if(!empty($userbuy)){	
						$info['status']=0;//查询成功
						foreach($userbuy as $key=>$val){					
							$shop = $this->db->YOne("select * from `@#_shangpin` where `id`='".$val['shopid']."'");//查询shoplist
							$info['list'][$key]['qishu']=$shop['qishu'];//期数
							$info['list'][$key]['shenyurenshu']=$shop['shenyurenshu'];//剩余人数
							$info['list'][$key]['zongrenshu']=$shop['zongrenshu'];//总人数
							$info['list'][$key]['gonumber'] = $val['gonumber'];//本次参与
							$info['list'][$key]['shop_img']=__ROOT__."/public/uploads/".$shop['thumb'];//商品图片
							$info['list'][$key]['jindu']=$shop['canyurenshu']/$shop['zongrenshu']*100;//商品进度
							$info['list'][$key]['q_showtime']=$shop['q_showtime'];//中奖号码
							$info['list'][$key]['title']=$val['shopname'];//商品title
							$info['list'][$key]['shopid']=$val['shopid'];//商品id
							$info['list'][$key]['is_shi']=$shop['is_shi'];//ten
							/*已揭晓商品*/
							$info['list'][$key]['q_end_time']=date("Y-m-d H:i",$shop['q_end_time']);//商品揭晓时间
							$info['list'][$key]['q_user_code']=$shop['q_user_code'];//中奖号码
							$info['list'][$key]['username']=$this->huode_user_name($shop['q_uid'],"username");//中奖用户
							$user_jiexiaoshop_number = D("yonghu_yys_record")->field("sum(gonumber) as gonumber")->where(array("uid"=>$shop['q_uid'],"shopid"=>$shop['id']))->find();//用户购买数量查询
							$info['list'][$key]['jiexiao_gonumber'] = $user_jiexiaoshop_number['gonumber'];//用户购买数量
						}
					}else{
						$info['status']=3;//分页数据结束
					}
				}else{
					$info['status']=2;//用户没有购买记录
				}
			}else{
				$info['status']=1;
			}
		}else{
			$info['status']=1;
		}
		echo json_encode($info);
	}

	//晒单记录
	public function my_share_record(){
		$userid=safe_replace(I('userid'));//用户id
		$pass=safe_replace(I('pass'));//用户密码
		$pagenum=safe_replace(I('page'));//分页查询
		$member=D("yonghu")->where("uid=$userid and password!=$pass")->find();
		if(!empty($member)){
			$cord=D("yonghu_yys_record")->where("uid=$userid and huode > '10000000'")->select();
			$shaidan=D("shai")->where(array("sd_userid"=>$userid))->order("sd_id DESC")->select();
			$sd_id = $r_id = array();
			foreach($shaidan as $sd){
				$sd_id[]=$sd['sd_shopid'];			
			}		
			foreach($cord as $rd){
				if(!in_array($rd['shopid'],$sd_id)){
					$r_id[]=$rd['shopid'];
				}					
			}
			if(!empty($r_id)){
				$rd_id=implode(",",$r_id);
				$rd_id = trim($rd_id,',');
			}else{
				$rd_id="0";
			}
					
			$total=D("yonghu_yys_record")->where("shopid in ($rd_id) and uid=$$userid and huode>'10000000'")->count();
			$page = new \Claduipi\Tools\page;	
			$page->config($total,10,$pagenum,"0");
			$record = D("yonghu_yys_record")->field("shopid,id")->where("shopid in ($rd_id) and uid=$userid and huode>'10000000'")->limit(($pagenum - 1) * $num, $num)->select();
				
			if(!empty($record)){
				foreach($record as $key=>$val){
				  $data['status']=2;
				  $data['list'][$key]=D("shangpin")->where(array("id"=>$val['shopid']))->order("q_end_time DESC")->find();
				}
				foreach($data['list'] as $key=>$val){
					$info['status']=0;//数据查询成功
					$info['list'][$key]['title']=$val['title'];//商品标题
					$info['list'][$key]['shopid']=$val['id'];//商品id
					$info['list'][$key]['shop_img']=__ROOT__."/public/uploads/".$val['thumb'];//商品图片
					$info['list'][$key]['qishu']=$val['qishu'];//商品期数
					$info['list'][$key]['q_end_time']=date("Y-m-d h:i",$val['q_end_time']);//商品揭晓时间date("Y-m-d",$val['q_end_time']);
					$info['list'][$key]['q_user_code']=$val['q_user_code'];//中奖号码
					$info['list'][$key]['q_showtime']=$val['q_showtime'];//中奖号码
					$info['list'][$key]['username']=$this->huode_user_name($val['q_uid'],"username");//中奖用户

					$user_shop_number = D("yonghu_yys_record")->field("sum(gonumber) as gonumber")->where(array("uid"=>$val['q_uid'],"shopid"=>$val['id']))->find();//用户购买数量查询

					$info['list'][$key]['gonumber'] = $user_shop_number['gonumber'];//用户购买数量
				}
			}
		}else{
			$info['status']=1;//用户信息错误
		}
		echo json_encode($info);
	}

	//添加晒单
	public function add_my_share(){
		$userid=safe_replace(I('userid'));//用户id
		$pass=safe_replace(I('pass'));//用户密码
		$sd_content=safe_replace($_POST['content']);//晒单内容
		$sd_id=safe_replace(I('sd_shopid'));//晒单id
		$sd_photo=$_POST['thumbs'];//晒单主图
		$sd_photos=$_POST['photos'];//晒单图片组
		$time=time();//晒单时间
		$member=D("yonghu")->where(array("uid"=>$userid,"password"=>$pass))->find();
		if(!empty($member)){
			$add_sd=D("shai")->add(array("sd_userid"=>$userid,"sd_shopid"=>$sd_id,"sd_title"=>'中奖就是开心',"sd_thumbs"=>$sd_photo,"sd_content"=>$sd_content,"sd_photolist"=>$sd_photos,"sd_zhan"=>"0","sd_ping"=>"0","sd_time"=>$time));
			if($add_sd){
				$info['status']=0;
			}
		}
		echo json_encode($info);
	}

	//晒单图片上传
	public function up_share_photo(){
		$headimgurl=safe_replace(I('headimgurl'));
		$userid=safe_replace(I('userid'));//用户id
		$pass=safe_replace(I('pass'));//用户密码
		$base64=I('base64img');//图片base64编码数据
		$member=D("yonghu")->where(array("uid"=>$userid,"password"=>$pass))->find();
		if(!empty($member)){
			$IMG = base64_decode($base64);
			$mulu = date("Ymd",time());
			$path = 'public/uploads/shaidan/'.$mulu;
			if (!file_exists($path)){ 
				mkdir ($path);
			}
			$time=time();
			file_put_contents($path ."/". $time . '.jpg',$IMG);	
			$go_shaidan_img  = 'shaidan/'.$mulu .'/'.$time.'.jpg';
			$upload = new \Claduipi\Tools\upload;
			$upload->thumbs(200,200,false,__PUBLIC__.$go_shaidan_img);
			$info['url']=$go_shaidan_img;
		}
		echo json_encode($info);
	}

		//充值记录
	public function record_money(){
		$userid=safe_replace(I('userid'));//用户id
		$pass=safe_replace(I('pass'));//用户密码
		$pagenum=safe_replace(I('page'));//分页查询
		$member=D("yonghu")->where(array("uid"=>$userid,"password"=>$pass))->find();
		$num=10;//每页数量
		if(!empty($member)){
			$total=D("yonghu_addmoney_record")->where(array("uid"=>$userid))->count();
			$page = new \Claduipi\Tools\page;		
			$page->config($total,$num,$pagenum,"0");		
			$shop=D("yonghu_addmoney_record")->where(array("uid"=>$userid))->order("time DESC")->limit(($pagenum - 1) * $num, $num)->select();
			if(!empty($shop)){
				$info=$shop;
			}else{
				$info['status']=1;//分页数据结束
			}
		}
		echo json_encode($info);
	}

		//邀请好友管理
	public function yaoqing_manage(){
		$userid=safe_replace(I('userid'));//用户id
		$pass=safe_replace(I('pass'));//用户密码
		$member=D("yonghu")->where(array("uid"=>$userid,"password"=>$pass))->find();
		if(!empty($member)){
			//查询邀请好友信息		
			$onef=D("yonghu")->where(array("yaoqing"=>$member['uid']))->order("time DESC")->select();
			$twof=D("yonghu")->where(array("yaoqing2"=>$member['uid']))->order("time DESC")->select();
			$threef=D("yonghu")->where(array("yaoqing3"=>$member['uid']))->order("time DESC")->select();
			$onecounm=count($onef);
			$twocounm=count($twof);
			$threecounm=count($threef);
			$involvedtotal=$onecounm+$threecounm+$twocounm;
			//好友总数
			$info['count']=$involvedtotal;
			$info['yongjin']=$member['yongjin'];
		}
		echo json_encode($info);
	}

		//好友明细
	public function haoyou_mingxi(){
		$userid=safe_replace(I('userid'));//用户id
		$pass=safe_replace(I('pass'));//用户密码
		$member=D("yonghu")->where(array("uid"=>$userid,"password"=>$pass))->find();
		if(!empty($member)){
			//查询邀请好友信息		
			$onef=D("yonghu")->where(array("yaoqing"=>$member['uid']))->order("time DESC")->select();
			$twof=D("yonghu")->where(array("yaoqing2"=>$member['uid']))->order("time DESC")->select();
			$threef=D("yonghu")->where(array("yaoqing3"=>$member['uid']))->order("time DESC")->select();
			foreach($onef as $key=>$val){
				$info['one'][$key]['username']=$this->huode_user_name($val['uid']);
				$info['one'][$key]['uid']=100000000+$val['uid'];
				$info['one'][$key]['jibie']='一级好友';
			}
			foreach($twof as $key=>$val){
				$info['two'][$key]['username']=$this->huode_user_name($val['uid']);
				$info['two'][$key]['uid']=100000000+$val['uid'];
				$info['two'][$key]['jibie']='二级好友';
			}
			foreach($threef as $key=>$val){
				$info['three'][$key]['username']=$his->huode_user_name($val['uid']);
				$info['three'][$key]['uid']=100000000+$val['uid'];
				$info['three'][$key]['jibie']='三级好友';
			}
		}
		echo json_encode($info);
	}

	    //提现记录
    public function yaoqing_tixian_record() {
        $userid = safe_replace(I("userid")); //用户id
        $pass = safe_replace(I("pass")); //用户密码

        $member = D("yonghu")->where(array("uid" => $userid, "password" => $pass))->find();
        if (!empty($member)) {
            $info = D("yonghu_cashout")->where(array("uid" => $member["uid"]))->order("time DESC")->find();
        }
        echo json_encode($info);
    }

	//充值到账户
	public function yaoqing_tixian_money(){
		$userid=safe_replace(I('userid'));//用户id
		$pass=safe_replace(I('pass'));//用户密码
		$tx_money=safe_replace(I('money'));//充值到账户金额
		$member=D("yonghu")->where(array("uid"=>$userid,"password"=>$pass))->select();
		if(!empty($member)){
			if($member['yongjin']>0){
				//有佣金
				$new_money=$member['money']+$tx_money;
				$new_yongjin=$member['yongjin']-$tx_money;
				$up=D("yonghu")->where(array("uid"=>$userid))->save(array("money"=>$new_money));
				$yongjin=D("yonghu")->where(array("uid"=>$userid))->save(array("yongjin"=>$new_yongjin));
				if($up&&$yongjin){
					$info['status']=0;
					D("yonghu_cashout")->add(array("uid"=>$userid,"money"=>$tx_money,"auditstatus"=>"1"));
				}
			}			
		}
		echo json_encode($info);	
	}
    //体现银行卡   断点
    public function yaoqing_tixian_bank() {
        $userid = safe_replace(I("userid")); //用户id
        $pass = safe_replace(I("pass")); //用户密码
        $tx_money = safe_replace(I("tx_money"));
        $tx_kaihuren = safe_replace(I("tx_kaihuren"));
        $tx_bank = safe_replace(I("tx_bank"));
        $tx_zhihang = safe_replace(I("tx_zhihang"));
        $tx_kahao = safe_replace(I("tx_kahao"));
        $tx_mobile = safe_replace(I("tx_mobile"));
        $member = D("yonghu")->where(array("uid" => $userid, "password" => $pass))->find();
        $time = time();
        if (!empty($member)) {
            if ($member['yongjin'] >= 100) {
                //有佣金,待审核
                $info['status'] = 0;
                $data = array("uid" => $userid, "username" => $member['username'], "bankname" => $tx_bank, "branch" => $tx_zhihang, "money" => $tx_money, "time" => $time, "banknumber" => $tx_kahao, "linkphone" => $tx_mobile, "auditstatus" => "0", "procefees" => "0", "reviewtime" => "");
                D("yonghu_cashout")->add($data);
            }
        }
        echo json_encode($info);
    }

    //幻灯
    public function slides() {
        $SlideList = D("shouji")->select();
        foreach ($SlideList as $key => $val) {
            $slides['info'][$key]['shopid'] = $val['slide_val']; //连接商品id
            $slides['info'][$key]['type'] = $val['slide_type']; //type
            $slides['info'][$key]['is_shop'] = $val['is_apple']; //shop
            $slides['info'][$key]['link'] = $val['link']; //link
            $slides['info'][$key]['title'] = $val['title']; //title
            $slides['paths'][$key] = __ROOT__ . "/public/uploads/" . $val['img']; //图片数据
            $arrayPic = explode("/", $val['link']);
            $slides['info'][$key]['shopid'] = end($arrayPic);
        }
        echo json_encode($slides);
    }

    //揭晓商品
    public function show_newjxshop() {
        //最新揭晓
        $shop = D("shangpin")->where("q_end_time !='' and q_showtime !='Y'")->order("q_end_time DESC")->limit(10)->select();
        foreach ($shop as $key => $val) {
            $new_shop[$key]['shopid'] = $val['id']; //中奖商品id
            $new_shop[$key]['username'] = $this->huode_user_name($val['q_uid']); //中奖用户
            $new_shop[$key]['title'] = $val['title'];
        }
        echo json_encode($new_shop);
    }

    //分类获取商品数据
    //没改
    public function get_allshop() {
        /*
         * 以下声明分类变量
         * 	1 => 人气商品
         * 	2 => 最新商品
         * 	3 => 进度降序
         * 	4 => 总需人数升序
         * 	5 => 总需人数降序
         */
        $num = 6; //每页数量
        $type = safe_replace($this->segment(4)); //获取商品数据类型
        $pagenum = safe_replace($this->segment(5)); //获取页数
        if ($type == 1) {
            $total = D("shangpin")->where("renqi='1' and q_end_time is null")->count();
            $page = new \Claduipi\Tools\page;
            $page->config($total, $num, $pagenum, "0");
            $shop = D("shangpin")->where("renqi='1' and q_end_time is null")->order("id DESC")->limit(($pagenum - 1) * $num, $num)->select();

            foreach ($shop as $key => $val) {
                $new_shop[$key]['shopid'] = $val['id']; //中奖商品id
                $new_shop[$key]['qishu'] = $val['qishu']; //商品期数
                $new_shop[$key]['title'] = $val['title']; //商品标题
                $new_shop[$key]['shopimg'] = __ROOT__ . "/public/uploads/" . $val['thumb']; //商品图标
                $new_shop[$key]['money'] = $val['money']; //商品价格
                $new_shop[$key]['zongrenshu'] = $val['zongrenshu']; //总人数
                $new_shop[$key]['canyurenshu'] = $val['canyurenshu']; //参与人数
                $new_shop[$key]['is_shi'] = $val['is_shi']; //ten
                $new_shop[$key]['shenyurenshu'] = $val['shenyurenshu']; //剩余人数
                $new_shop[$key]['jindu'] = $val['canyurenshu'] / $val['zongrenshu'] * 100; //开奖进度
            }
            echo json_encode($new_shop);
        } else if ($type == 2) {
            $total = D("shangpin")->where("pos='1' and q_end_time is null")->count();
            $page = new \Claduipi\Tools\page;
            $page->config($total, $num, $pagenum, "0");
            $shop = D("shangpin")->where("q_end_time is null")->order("qishu DESC")->limit(($pagenum - 1) * $num, $num)->select();

            foreach ($shop as $key => $val) {
                $new_shop[$key]['shopid'] = $val['id']; //中奖商品id
                $new_shop[$key]['qishu'] = $val['qishu']; //商品期数
                $new_shop[$key]['title'] = $val['title']; //商品标题
                $new_shop[$key]['shopimg'] = __ROOT__ . "/public/uploads/" . $val['thumb']; //商品图标
                $new_shop[$key]['money'] = $val['money']; //商品价格
                $new_shop[$key]['zongrenshu'] = $val['zongrenshu']; //总人数
                $new_shop[$key]['canyurenshu'] = $val['canyurenshu']; //参与人数
                $new_shop[$key]['is_shi'] = $val['is_shi']; //ten
                $new_shop[$key]['shenyurenshu'] = $val['shenyurenshu']; //剩余人数
                $new_shop[$key]['jindu'] = $val['canyurenshu'] / $val['zongrenshu'] * 100; //开奖进度
            }
            echo json_encode($new_shop);
        } else if ($type == 3) {
            $total = D("shangpin")->where("q_end_time is null")->count();
            $page = new \Claduipi\Tools\page;
            $page->config($total, $num, $pagenum, "0");
            $shop = D("shangpin")->where("q_end_time is null")->order("canyurenshu DESC")->limit(($pagenum - 1) * $num, $num)->select();

            foreach ($shop as $key => $val) {
                $new_shop[$key]['shopid'] = $val['id']; //中奖商品id
                $new_shop[$key]['qishu'] = $val['qishu']; //商品期数
                $new_shop[$key]['title'] = $val['title']; //商品标题
                $new_shop[$key]['shopimg'] = __ROOT__ . "/public/uploads/" . $val['thumb']; //商品图标
                $new_shop[$key]['money'] = $val['money']; //商品价格
                $new_shop[$key]['zongrenshu'] = $val['zongrenshu']; //总人数
                $new_shop[$key]['canyurenshu'] = $val['canyurenshu']; //参与人数
                $new_shop[$key]['is_shi'] = $val['is_shi']; //ten
                $new_shop[$key]['shenyurenshu'] = $val['shenyurenshu']; //剩余人数
                $new_shop[$key]['jindu'] = $val['canyurenshu'] / $val['zongrenshu'] * 100; //开奖进度
            }
            echo json_encode($new_shop);
        } else if ($type == 4) {
            $total = D("shangpin")->where("pos = '1' and q_end_time is null")->count();
            $page = new \Claduipi\Tools\page;
            $page->config($total, $num, $pagenum, "0");
            $shop = D("shangpin")->where("q_end_time is null")->order("money asc")->limit(($pagenum - 1) * $num, $num)->select();

            foreach ($shop as $key => $val) {
                $new_shop[$key]['shopid'] = $val['id']; //中奖商品id
                $new_shop[$key]['qishu'] = $val['qishu']; //商品期数
                $new_shop[$key]['title'] = $val['title']; //商品标题
                $new_shop[$key]['shopimg'] = __ROOT__ . "/public/uploads/" . $val['thumb']; //商品图标
                $new_shop[$key]['money'] = $val['money']; //商品价格
                $new_shop[$key]['zongrenshu'] = $val['zongrenshu']; //总人数
                $new_shop[$key]['is_shi'] = $val['is_shi']; //ten
                $new_shop[$key]['canyurenshu'] = $val['canyurenshu']; //参与人数
                $new_shop[$key]['shenyurenshu'] = $val['shenyurenshu']; //剩余人数
                $new_shop[$key]['jindu'] = $val['canyurenshu'] / $val['zongrenshu'] * 100; //开奖进度
            }
            echo json_encode($new_shop);
        } else if ($type == 5) {
            $total = D("shangpin")->where("q_end_time is null")->count();
            $total = $this->db->YCount("SELECT COUNT(*) FROM `@#_shangpin` where `q_end_time` is null");
            $page = new \Claduipi\Tools\page;
            $page->config($total, $num, $pagenum, "0");
            $shop = D("shangpin")->where("q_end_time is null")->order("money desc")->limit(($pagenum - 1) * $num, $num)->select();

            foreach ($shop as $key => $val) {
                $new_shop[$key]['shopid'] = $val['id']; //中奖商品id
                $new_shop[$key]['qishu'] = $val['qishu']; //商品期数
                $new_shop[$key]['title'] = $val['title']; //商品标题
                $new_shop[$key]['shopimg'] = __ROOT__ . "/public/uploads/" . $val['thumb']; //商品图标
                $new_shop[$key]['money'] = $val['money']; //商品价格
                $new_shop[$key]['zongrenshu'] = $val['zongrenshu']; //总人数
                $new_shop[$key]['is_shi'] = $val['is_shi']; //ten
                $new_shop[$key]['canyurenshu'] = $val['canyurenshu']; //参与人数
                $new_shop[$key]['shenyurenshu'] = $val['shenyurenshu']; //剩余人数
                $new_shop[$key]['jindu'] = $val['canyurenshu'] / $val['zongrenshu'] * 100; //开奖进度
            }
            echo json_encode($new_shop);
        } else if ($type == 9) {
            $shop = D("shangpin")->where("pos='1' and q_end_time is null")->order("id desc")->limit(6)->select();
            foreach ($shop as $key => $val) {
                $new_shop[$key]['shopid'] = $val['id']; //中奖商品id
                $new_shop[$key]['qishu'] = $val['qishu']; //商品期数
                $new_shop[$key]['title'] = $val['title']; //商品标题
                $new_shop[$key]['shopimg'] = __ROOT__ . "/public/uploads/" . $val['thumb']; //商品图标
                $new_shop[$key]['money'] = $val['money']; //商品价格
                $new_shop[$key]['zongrenshu'] = $val['zongrenshu']; //总人数
                $new_shop[$key]['is_shi'] = $val['is_shi']; //ten
                $new_shop[$key]['canyurenshu'] = $val['canyurenshu']; //参与人数
                $new_shop[$key]['shenyurenshu'] = $val['shenyurenshu']; //剩余人数
                $new_shop[$key]['jindu'] = $val['canyurenshu'] / $val['zongrenshu'] * 100; //开奖进度
            }
            echo json_encode($new_shop);
        }
    }

    //获取商品组图
    //没改
    public function get_shopslide() {
        $shopid = safe_replace($this->segment(4)); //获取商品id
        $item = D("shangpin")->where(array("id" => $shopid))->find();
        $shop = unserialize($item['picarr']);
        foreach ($shop as $key => $val) {
            $new_shop[$key] = __ROOT__ . "/public/uploads/" . $val;
        }
        echo json_encode($new_shop);
    }

    //获取商品详情数据
    //没改
    public function get_shopinfo() {
        $shopid = safe_replace($this->segment(4)); //获取商品id
        $userid = safe_replace($this->segment(5)); //获取当前用户id
        $shop_data = D("shangpin")->where(array("id" => $shopid))->find();
        /*
          判断商品状态:
          1.进行中  "on" => "jxz";
          2.已揭晓  "on" => "yjx"
          3.开奖中  "on" => "kjz"
         */
        $user_code = D("yonghu_yys_record")->where(array("shopid" => $shopid, "uid" => $userid))->order("gonumber DESC")->select();
        if (!empty($user_code)) {
            foreach ($user_code as $key => $val) {
                $codeid = @explode(',', $val['goucode']);
                $shop_info['my_code'][$key] = $val['goucode']; //用户购买的号码
            }
            $user_shop_number = D("yonghu_yys_record")->where(array("shopid" => $shopid, "uid" => $userid))->field("sum(gonumber) as gonumber")->order("gonumber DESC")->find(); //用户购买数量查询
            $shop_info['my_code_num'] = $user_shop_number['gonumber']; //用户购买数量
        } else {
            $shop_info['my_code'] = "no"; //用户没有购买号码
        }

        if (empty($shop_data['q_end_time'])) {
            $shop_info['on'] = "jxz"; //进行中
            $shop_info['title'] = $shop_data['title']; //商品名称
            $shop_info['qishu'] = $shop_data['qishu']; //期数
            $shop_info['shopid'] = $shop_data['id']; //商品id
            $shop_info['sid'] = $shop_data['sid']; //商品sid
            $shop_info['is_shi'] = $shop_data['is_shi']; //ten
            $shop_info['zongrenshu'] = $shop_data['zongrenshu']; //总需人数
            $shop_info['shenyurenshu'] = $shop_data['shenyurenshu']; //剩余人数
            $shop_info['jindu'] = $shop_data['canyurenshu'] / $shop_data['zongrenshu'] * 100; //开奖进度
        } else if (!empty($shop_data['q_end_time']) && $shop_data['q_showtime'] == "N") {
            $shop_info['on'] = "yjx"; //已揭晓
            $shop_info['sid'] = $shop_data['sid']; //商品sid
            $shop_info['title'] = $shop_data['title']; //商品名称
            $shop_info['qishu'] = $shop_data['qishu']; //期数
            $shop_info['uid'] = $shop_data['q_uid']; //中奖用户id
            $shop_info['shopid'] = $shop_data['id']; //商品id
            $shop_info['is_shi'] = $shop_data['is_shi']; //ten
            $shop_info['q_user_code'] = $shop_data['q_user_code']; //中奖号码
            $shop_info['user_img'] = __ROOT__ . "/public/uploads/" . $this->huode_user_key($shop_data['q_uid'], "img"); //中奖用户头像
            $shop_info['username'] = $this->huode_user_name($shop_data['q_uid'], "username"); //中奖用户昵称
            $zj_shop_number = D("yonghu_yys_record")->where(array("shopid" => $shopid, "uid" => $shop_info['uid']))->field("sum(gonumber) as gonumber")->find(); //用户购买数量查询
            $shop_info['buy_number'] = $zj_shop_number['gonumber']; //本期购买人次
            $shop_info['jx_time'] = $this->microt($shop_data['q_end_time']); //揭晓时间
        } else if ($shop_data['q_showtime'] == "Y") {
            $shop_info['on'] = "kjz"; //开奖中
            $shop_info['sid'] = $shop_data['sid']; //商品sid
            $shop_info['is_shi'] = $shop_data['is_shi']; //ten
            $shop_info['shopid'] = $shop_data['id']; //商品id
        }
        echo json_encode($shop_info);
    }

    //获取最新期商品
    //没改
    public function get_new_shop() {
        $shopid = safe_replace($this->segment(4)); //当前商品id
        $shop_data = D("shangpin")->where(array("id" => $shopid))->find();
        $new_shop_data = D("shangpin")->where(array("sid" => $shop_data['sid']))->order("qishu DESC")->find();
        $new_shop_id['id'] = $new_shop_data['id'];
        echo json_encode($new_shop_id);
    }

    //商品全部购买记录
    //没改
    public function get_allbuy() {
        /* 商品所有购买记录 */
        $num = 10; //每页数量
        $shopid = safe_replace($this->segment(4)); //获取商品id
        $pagenum = safe_replace($this->segment(5)); //获取页数
        $total = D("yonghu_yys_record")->where(array("shopid" => $shopid))->count();
        $page = new \Claduipi\Tools\page;
        $page->config($total, $num, $pagenum, "0");
        $shop_allbuy = D("yonghu_yys_record")->where(array("shopid" => $shopid))->order("id DESC")->limit(($pagenum - 1) * $num, $num)->select();
        if (!empty($shop_allbuy)) {
            foreach ($shop_allbuy as $key => $val) {
                $shop_info[$key]['time'] = $this->microt($val['time']); //购买时间
                $shop_info[$key]['username'] = $this->strcut($val['username']); //购买用户名
                $shop_info[$key]['number'] = $val['gonumber']; //购买人次
                $shop_info[$key]['uid'] = $val['uid']; //购买用户id
                $shop_info[$key]['user_img'] = __ROOT__ . "/public/uploads/" . $this->huode_user_key($val['uid'], "img"); //购买用户头像
                $shop_info[$key]['ip'] = $this->huode_ip($val['id'], 'ipmac'); //购买用户IP
            }
        }
        echo json_encode($shop_info);
    }

    //商品详情倒计时
    //没改
    public function get_shopdjs() {
        $shopid = safe_replace($this->segment(4)); //获取倒计时商品id
        if (!$shopid) {
            echo json_encode(array("error" => '1'));
            return;
            exit;
        }
        $gid = $shopid;
        $times = (int) C('goods_end_time');
        if (!$times) {
            $times = 1;
        }
        $gid = $this->safe_replace($gid);
        if ($gid) {
            $info = D("shangpin")->where(array("id" => $gid, "q_showtime" => "Y"))->field("xsjx_time,id,thumb,title,q_uid,q_user,q_end_time,qishu,q_user_code")->order("id DESC")->find();
        }
        if (!$info) {
            echo json_encode(array("error" => '1'));
            return;
            exit;
        }
        if ($info['xsjx_time']) {
            $info['q_end_time'] = $info['q_end_time'] + $times;
        }
        /* 中奖用户信息 */
        $user = unserialize($info['q_user']); //安全转义
        $user = $this->huode_user_name($info['q_uid'], "username"); //用户昵称
        $user_img = __ROOT__ . "/public/uploads/" . $this->huode_user_key($info['q_uid'], "img"); //用户头像
        $uid = $info['q_uid']; //用户id
        $user_code = $info['q_user_code']; //中奖号码
        $qishu = $info['qishu']; //中奖商品期数
        $jx_time = microt($info['q_end_time']); //揭晓时间
        $title = $info['title']; //商品标题
        $gorecode = D("yonghu_yys_record")->where(array("uid" => $info['q_uid'], "shopid" => $shopid, "shopqishu" => $qishu))->field("sum(gonumber) as gonumber")->order("id DESC")->limit(6)->find();
        $gonumber = $gorecode['gonumber'];
        $q_time = substr($info['q_end_time'], 0, 10);
        if ($q_time <= time()) {
            D("shangpin")->where("id = '" . $info["id"] . "' and q_showtime = 'Y' and q_uid is not null")->save(array("q_showtime" => "N"));
            echo json_encode(array("error" => '-1'));
            return;
            exit;
        }
        $times = $q_time - time();
        echo json_encode(array("error" => "0", "uid" => "$uid", "user" => $user, "times" => $times, "qishu" => $info['qishu'], "jx_time" => $jx_time, "user_code" => $user_code, "user_canyu" => $gonumber, "user_img" => $user_img, "title" => $title));
        exit;
    }

    //搜索商品
    public function get_search_shop() {
        $search = I("search"); //搜索内容
        $pagenum = I("page"); //获取页数
        $search = safe_replace($search);
        if (!$this->is_utf8($search)) {
            $search = iconv("GBK", "UTF-8", $search);
        }
        $search = str_ireplace("union", '', $search);
        $search = str_ireplace("select", '', $search);
        $search = str_ireplace("delete", '', $search);
        $search = str_ireplace("update", '', $search);
        $search = str_ireplace("/**/", '', $search);
        /* 商品搜索分页 */
        $num = 10; //每页数量
        if (!$pagenum) {
            $pagenum = 1;
        }
        $total = D("shangpin")->where("shenyurenshu != '0' AND title LIKE '%" . $search . "%'")->count();
        $page = new \Claduipi\Tools\page;
        $page->config($total, $num, $pagenum, "0");
        $search_shop = D("shangpin")->where("shenyurenshu != '0' AND title LIKE '%" . $search . "%'")->limit(($pagenum - 1) * $num, $num)->select();
        if (!empty($search_shop)) {
            foreach ($search_shop as $key => $val) {
                $shop_info['shop_info'][$key]['title'] = $val['title']; //商品标题
                $shop_info['shop_info'][$key]['id'] = $val['id']; //商品id
                $shop_info['shop_info'][$key]['is_shi'] = $val['is_shi']; //商品id
                $shop_info['shop_info'][$key]['jindu'] = $val['canyurenshu'] / $val['zongrenshu'] * 100; //商品进度
                $shop_info['shop_info'][$key]['zongrenshu'] = $val['zongrenshu']; //商品总需人数
                $shop_info['shop_info'][$key]['shenyurenshu'] = $val['shenyurenshu']; //商品剩余人数
                $shop_info['shop_info'][$key]['shop_img'] = __ROOT__ . "/public/uploads/" . $val['thumb']; //商品图片
            }
        }
        echo json_encode($shop_info);
    }

    //获取最新揭晓商品，倒计时
    //没改
    public function get_jiexiao_shop() {
        /*
          type => 1 ;已揭晓数据
          type => 2 ;倒计时数据
         */
        $type = safe_replace($this->segment(4)); //查询揭晓类型
        $pagenum = safe_replace($this->segment(5)); //获取页数
        if ($type == 1) {//已揭晓数据
            $num = 10; //每页数量
            $total = D("shangpin")->where("q_end_time !='' and q_showtime !='Y'")->count(); //商品数据
            $page = new \Claduipi\Tools\page;
            $page->config($total, $num, $pagenum, "0");
            $shop = D("shangpin")->where("q_end_time !='' and q_showtime !='Y'")->order("q_end_time DESC")->limit(($pagenum - 1) * $num, $num)->select(); //商品数据
            foreach ($shop as $key => $val) {
                $shop_info[$key]['title'] = $val['title']; //商品标题
                $shop_info[$key]['id'] = $val['id']; //商品id
                $shop_info[$key]['shop_img'] = __ROOT__ . "/public/uploads/" . $val['thumb']; //商品图片
                $shop_info[$key]['qishu'] = $val['qishu']; //商品期数
                $shop_info[$key]['is_shi'] = $val['is_shi']; //ten
                $shop_info[$key]['q_end_time'] = date("Y-m-d", $val['q_end_time']); //商品揭晓时间date("Y-m-d",$val['q_end_time']);
                $shop_info[$key]['q_user_code'] = $val['q_user_code']; //中奖号码
                $shop_info[$key]['username'] = $this->huode_user_name($val['q_uid'], "username"); //中奖用户
                $user_shop_number = D("yonghu_yys_record")->where(array("uid" => $val['q_uid'], "shopid" => $val['id']))->field("sum(gonumber) as gonumber")->find(); //用户购买数量查询
                $shop_info[$key]['gonumber'] = $user_shop_number['gonumber']; //用户购买数量
            }
            echo json_encode($shop_info);
        } else if ($type == 2) {
            $times = (int) C('goods_end_time');
            if (!$times) {
                $times = 1;
            }
            $num = 10; //每页数量
            $total = D("shangpin")->where("q_showtime ='Y'")->count();
            $page = new \Claduipi\Tools\page;
            $page->config($total, $num, $pagenum, "0");
            $info = D("shangpin")->where("q_showtime ='Y'")->field("xsjx_time,id,thumb,title,q_uid,q_user,q_end_time,qishu,q_user_code")->order("q_end_time DESC")->limit(($pagenum - 1) * $num, $num)->select();
            //没有倒计时商品
            if (!$info) {
                echo json_encode(array("error" => '1'));
                return;
                exit;
            }
            //end
            foreach ($info as $key => $val) {
                $jx_infO['list'][$key]['shopid'] = $val['id']; //商品id
                $jx_infO['list'][$key]['q_user'] = $this->huode_user_name($val['q_uid'], "username"); //用户昵称
                $jx_infO['list'][$key]['shop_img'] = __ROOT__ . "/public/uploads/" . $val['thumb']; //商品图片
                $jx_infO['list'][$key]['q_uid'] = $val['q_uid']; //用户id
                $jx_infO['list'][$key]['is_shi'] = $val['is_shi']; //ten
                $jx_infO['list'][$key]['q_user_code'] = $val['q_user_code']; //中奖号码
                $jx_infO['list'][$key]['qishu'] = $val['qishu']; //中奖商品期数
                $jx_infO['list'][$key]['q_end_time'] = date("Y-m-d", $val['q_end_time']); //揭晓时间
                $jx_infO['list'][$key]['title'] = $val['title']; //商品标题
                //购买总人次
                $gorecode = D("yonghu_yys_record")->where(array("uid" => $val['q_uid'], "shopid" => $val['id'], "shopqishu" => $val['qishu']))->field("sum(gonumber) as gonumber")->order("id DESC")->limit(6)->find();
                $jx_infO['list'][$key]['user_canyu'] = $gorecode['gonumber'];
                //倒计时时间
                $q_time = substr($val['q_end_time'], 0, 10);
                $jx_infO['list'][$key]['times'] = $q_time - time();
                if ($jx_infO['list'][$key]['times'] <= 0) {
                    D("shangpin")->where("id = '" . $jx_infO['list'][$key]['shopid'] . "' and q_showtime = 'Y' and q_uid is not null")->save(array("q_showtime" => "N"));
                }
            }
            $jx_infO['ststua'] = 0;
            echo json_encode($jx_infO);
        }
    }

    //获取最新揭晓商品，倒计时
    //没改
    public function get_jiexiao_shop_by_sid() {
        /*
          type => 1 ;已揭晓数据
          type => 2 ;倒计时数据
         */
        $type = safe_replace($this->segment(4)); //查询揭晓类型
        $pagenum = safe_replace($this->segment(5)); //获取页数
        $sid = safe_replace($this->segment(6)); //获取页数
        if ($type == 1) {//已揭晓数据
            $num = 10; //每页数量
            $total = D("shangpin")->where("q_end_time !='' and q_showtime !='Y' and sid = '$sid'")->count(); //商品数据
            $page = new \Claduipi\Tools\page;
            $page->config($total, $num, $pagenum, "0");
            $shop = D("shangpin")->where("q_end_time !='' and q_showtime !='Y' and sid = '$sid'")->order("q_end_time DESC")->limit(($pagenum - 1) * $num, $num)->select(); //商品数据
            foreach ($shop as $key => $val) {
                $shop_info[$key]['title'] = $val['title']; //商品标题
                $shop_info[$key]['id'] = $val['id']; //商品id
                $shop_info[$key]['shop_img'] = YYS_LOCAL_PATH . "/love/uploads/" . $val['thumb']; //商品图片
                $shop_info[$key]['qishu'] = $val['qishu']; //商品期数
                $shop_info[$key]['is_shi'] = $val['is_shi']; //ten
                $shop_info[$key]['q_end_time'] = date("Y-m-d", $val['q_end_time']); //商品揭晓时间date("Y-m-d",$val['q_end_time']);
                $shop_info[$key]['q_user_code'] = $val['q_user_code']; //中奖号码
                $shop_info[$key]['username'] = huode_user_name($val['q_uid'], "username"); //中奖用户
                $user_shop_number = D("yonghu_yys_record")->where(array("uid" => $val['q_uid'], "shopid" => $val['id']))->field("sum(gonumber) as gonumber")->find(); //用户购买数量查询
                $shop_info[$key]['gonumber'] = $user_shop_number['gonumber']; //用户购买数量
            }
            echo json_encode($shop_info);
        }
    }

    //最新揭晓倒机时
    public function get_jiexiao_new_djs() {
        $shop = D("shangpin")->where(array("q_showtime" => "Y"))->order("q_end_time")->select(); //商品数据
        //倒计时时间
        if (!empty($shop)) {
            foreach ($shop as $key => $val) {
                $q_time = substr($val['q_end_time'], 0, 10);
                $data_djs = $q_time - time();
                $djs[$key]['id'] = $val[id];
                $djs[$key]['times'] = $data_djs * 1000;
            }
        } else {
            $djs['content'] = "目前没有揭晓商品";
        }
        echo json_encode($djs);
    }

    //倒计时结束获取中奖信息
    //没改
    public function get_jiexixao_shop_info() {
        $shopid = safe_replace($this->segment(4)); //商品id
        $shop_info = D("shangpin")->where(array("id" => $shopid))->find(); //商品数据
        $info['title'] = $shop_info['title'];
        $info['qishu'] = $shop_info['qishu'];
        $info['user'] = $this->huode_user_name($shop_info['q_uid'], "username"); //用户昵称
        $gorecode = D("yonghu_yys_record")->where(array("shopid" => $shop_info['id'], "shopqishu" => $shop_info['qishu'], "uid" => $shop_info['q_uid']))->field("sum(gonumber) as gonumber")->order("id DESC")->limit(6)->find();
        $info['user_canyu'] = $gorecode['gonumber'];
        $info['user_code'] = $shop_info['q_user_code'];
        $info['jx_time'] = date("Y-m-d", $shop_info['q_end_time']); //揭晓时间
        echo json_encode($info);
    }

    //晒单评论
    //没改
    public function goodspost() {
        $webname = $this->C('web_name');
        $key = "晒单评论";
        $xiangmuid = intval($this->segment(4));
        $yyslist = D("shangpin")->where(array("sid" => $xiangmuid))->field("id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,fahuo")->select();
        if (!$yyslist) {
            $this->notemobile('页面错误!');
        }
        $shop = '';
        foreach ($yyslist as $list) {
            $shop.=$list['id'] . ',';
        }
        $id = trim($shop, ',');
        if ($id) {
            $shaidingdan = D("shai")->where("'sd_shopid' IN ($id)")->order("`sd_id` DESC")->select();
            $sum = 0;
            foreach ($shaidingdan as $sd) {
                $shaidingdan_hueifu = D("shai_hueifu")->where(array("sdhf_id" => $sd["sd_id"]))->select();
                $sum = $sum + count($shaidingdan_hueifu);
            }
        } else {
            $shaidingdan = 0;
            $sum = 0;
        }
        $this->assign("key", $key);
        $this->assign("xiangmuid", $xiangmuid);
        $this->assign("shaidingdan_count", count($shaidingdan));
        $this->assign("sum", $sum);
        $this->display("mobile/index.goodspostapp");
        //include templates("mobile/index", "goodspostapp");
    }

    //晒单数据
    //没改
    public function get_shaidan() {
        /*
          查询全部数据 => type = 1;
          查询详细数据 =>type = 2;
         */
        $type = safe_replace($this->segment(4)); //查询类型
        if ($type == 1) {
            $pagenum = safe_replace($this->segment(5)); //分页查询
            $num = 10; //每页数量
            //$total = $this->db->YCount("SELECT COUNT(*) FROM `@#_shai`");
            $total = D("shai")->count();
            $page = new \Claduipi\Tools\page;
            $page->config($total, $num, $pagenum, "0");
            $shaidan = D("shai")->order("sd_id DESC")->limit(($pagenum - 1) * $num, $num)->select();
            foreach ($shaidan as $key => $val) {
                $shaidan_info[$key]['sd_id'] = $val['sd_id']; //晒单id
                $shop = D("shangpin")->where(array("id" => $val['sd_shopid']))->find();
                $shaidan_info[$key]['sd_shop_title'] = $shop['title'];
                $shaidan_info[$key]['sd_userid'] = $val['sd_userid']; //晒单用户id
                $shaidan_info[$key]['sd_username'] = $this->huode_user_name($val['sd_userid'], "username"); //晒单用户
                $shaidan_info[$key]['sd_user_img'] = __ROOT__ . "/public/uploads/" . $this->huode_user_key($val['sd_userid'], "img"); //晒单用户头像
                $shaidan_info[$key]['url'] = __ROOT__ . "/public/uploads/"; //图片地址
                $shaidan_info[$key]['sd_title'] = $val['sd_title']; //晒单标题
                $shaidan_info[$key]['sd_time'] = date("Y-m-d H:i", $val['sd_time']); //晒单时间
                $shaidan_info[$key]['sd_qishu'] = $shop['qishu']; //晒单期数
                $shaidan_info[$key]['sd_content'] = $val['sd_content']; //晒单内容
                $shaidan_info[$key]['sd_photolist'] = $val['sd_photolist']; //晒单组图
            }
            echo json_encode($shaidan_info);
        } else if ($type == 2) {
            $sd_id = safe_replace($this->segment(5)); //晒单ID
            $val = $this->db->YOne("select * from `@#_shai` where `sd_id`='$sd_id'");
            $shaidan_info['sd_id'] = $val['sd_id']; //晒单id
            $shop = $this->db->YOne("select * from `@#_shangpin` where `id`='" . $val['sd_shopid'] . "'");
            $shaidan_info['sd_shop_title'] = $shop['title'];
            $shaidan_info['sd_shopid'] = $val['sd_shopid']; //晒单商品id
            $shaidan_info['sd_shop_jiexiao_time'] = date("Y-m-d H:i", $shop['q_end_time']); //晒单商品揭晓时间
            $shaidan_info['sd_shop_code'] = $shop['q_user_code'];
            $shaidan_info['sd_userid'] = $val['sd_userid']; //晒单用户id
            $shaidan_info['sd_username'] = $this->huode_user_name($val['sd_userid'], "username"); //晒单用户
            $shaidan_info['sd_user_img'] = __ROOT__ . "/public/uploads/" . huode_user_key($val['sd_userid'], "img"); //晒单用户头像
            $user_shop_number = D("yonghu_yys_record")->where(array("uid" => $val['sd_userid'], "shopid" => $val['sd_shopid']))->field("sum(gonumber) as gonumber")->find(); //用户购买数量查询
            $shaidan_info['gonumber'] = $user_shop_number['gonumber']; //用户购买数量
            $shaidan_info['url'] = __ROOT__ . "/public/uploads/"; //图片地址
            $shaidan_info['sd_title'] = $val['sd_title']; //晒单标题
            $shaidan_info['sd_time'] = date("Y-m-d H:i", $val['sd_time']); //晒单时间
            $shaidan_info['sd_qishu'] = $shop['qishu']; //晒单期数
            $shaidan_info['sd_content'] = $val['sd_content']; //晒单内容
            $shaidan_info['sd_photolist'] = $val['sd_photolist']; //晒单组图
            echo json_encode($shaidan_info);
        }
    }

    //获取用户邀请链接
    public function getshorturl() {
        $userid = $_POST['userid'];
        $lianjie = D("yongjin")->order("id")->limit(1)->select();
        //$lianjie = $this->db->Ylist("select * from `@#_yongjin` order by id limit 1");
        $huiyuan = D("yonghu")->where(array("uid" => $userid))->find(); //用户数据
        foreach ($lianjie as $key => $val) {
            $yys['urls'][$key]['object_type'] = "";
            $yys['urls'][$key]['result'] = 'true';
            $yys['urls'][$key]['title'] = $val[title];
            if ($huiyuan[yaoqing] && empty($huiyuan[yaoqing2]) && empty($huiyuan[yaoqing3])) {
                $yys['urls'][$key]['url_short'] = $val[link] . '?yaoqing2=' . $huiyuan[yaoqing] . '&yaoqing=' . $huiyuan[uid];
            } else if ($huiyuan[yaoqing] && $huiyuan[yaoqing2] && empty($huiyuan[yaoqing3])) {
                $yys['urls'][$key]['url_short'] = $val[link] . '?yaoqing=' . $huiyuan[uid] . '&yaoqing2=' . $huiyuan[yaoqing] . '&yaoqing3=' . $huiyuan[yaoqing2];
            } else if ($huiyuan[yaoqing] && $huiyuan[yaoqing2] && $huiyuan[yaoqing3]) {
                $yys['urls'][$key]['url_short'] = $val[link] . '?yaoqing=' . $huiyuan[uid] . '&yaoqing2=' . $huiyuan[yaoqing] . '&yaoqing3=' . $huiyuan[yaoqing2];
            } else {
                $yys['urls'][$key]['url_short'] = $val[link] . '?yaoqing=' . $huiyuan[uid];
            }
            $yys['urls'][$key]['object_id'] = "";
            $yys['urls'][$key]['url_long'] = $val[link];
            $yys['urls'][$key]['type'] = "0";
        }
        echo json_encode($yys);
    }

    //邀请记录
    public function getinvitelist() {
        $kaishi = htmlspecialchars(I("FIdx")) - 1;
        $jieshu = htmlspecialchars(I("EIdx"));
        $huiyuan = $this->userinfo;

        $mingxi = D("yonghu")->where("yaoqing='$huiyuan[uid]' or yaoqing2='$huiyuan[uid]' or yaoqing3='$huiyuan[uid]'")->order("time asc")->limit("$kaishi,$jieshu")->select();

        if (!$mingxi) {
            $yys['code'] = -1;
            $yys['tips'] = "暂无记录";
        } else {
            $yys['code'] = 0;
            $yys['str']['totalCount'] = count($mingxi);
            foreach ($mingxi as $key => $val) {
                $jilu = D("yonghu_yys_record")->where(array("uid" => $val['uid']))->order("time desc")->find();
                $yys['str']['listItems'][$key]['userName'] = $this->huode_user_name($val['uid']);
                $yys['str']['listItems'][$key]['regTime'] = date('Y.m.d H:i:s', $val['time']);
                if ($jilu) {
                    $yys['str']['listItems'][$key]['state'] = 1;
                }
                $yys['str']['listItems'][$key]['userWeb'] = $val['uid'];
                $yys['str']['listItems'][$key]['userPhoto'] = 5;
                $yys['str']['listItems'][$key]['userCode'] = $val['uid'] + 1000000000;
            }
        }
        echo json_encode($yys);
    }

    //根据商品SID获取晒单信息
    //没改   断点
    public function get_shaidan_by_sid() {
        /*
          查询全部数据 => type = 1;
          查询详细数据 =>type = 2;
         */
        $type = safe_replace($this->segment(4)); //查询类型
        if ($type == 1) {
            $pagenum = safe_replace($this->segment(5)); //分页查询
            $sid = safe_replace($this->segment(6)); //分页查询
            $num = 10; //每页数量
            $total = D("shai")->where(array("sd_shopsid" => $sid))->count();
            $page = new \Claduipi\Tools\page;
            $page->config($total, $num, $pagenum, "0");
            $shaidan = D("shai")->where(array("sd_shopsid" => $sid))->order("sd_id DESC")->limit(($pagenum - 1) * $num, $num)->select();
            foreach ($shaidan as $key => $val) {
                $shaidan_info[$key]['sd_id'] = $val['sd_id']; //晒单id
                $total = D("shangpin")->where(array("id" => $val['sd_shopid']))->find();
                $shaidan_info[$key]['sd_shop_title'] = $shop['title'];
                $shaidan_info[$key]['sd_userid'] = $val['sd_userid']; //晒单用户id
                $shaidan_info[$key]['sd_username'] = $this->huode_user_name($val['sd_userid'], "username"); //晒单用户
                $shaidan_info[$key]['sd_user_img'] = __ROOT__ . "/public/uploads/" . huode_user_key($val['sd_userid'], "img"); //晒单用户头像
                $shaidan_info[$key]['url'] = __ROOT__ . "/public/uploads/"; //图片地址
                $shaidan_info[$key]['sd_title'] = $val['sd_title']; //晒单标题
                $shaidan_info[$key]['sd_time'] = date("Y-m-d H:i", $val['sd_time']); //晒单时间
                $shaidan_info[$key]['sd_qishu'] = $shop['qishu']; //晒单期数
                $shaidan_info[$key]['sd_content'] = $val['sd_content']; //晒单内容
                $shaidan_info[$key]['sd_photolist'] = $val['sd_photolist']; //晒单组图
            }
            echo json_encode($shaidan_info);
        } else if ($type == 2) {
            $sd_id = safe_replace($this->segment(5)); //晒单ID
            $val = D("shai")->where(array("sd_id" => $sd_id))->find();
            $shaidan_info['sd_id'] = $val['sd_id']; //晒单id
            $shop = D("shangpin")->where(array("id" => $val['sd_shopid']))->find();
            $shaidan_info['sd_shop_title'] = $shop['title'];
            $shaidan_info['sd_shopid'] = $val['sd_shopid']; //晒单商品id
            $shaidan_info['sd_shop_jiexiao_time'] = date("Y-m-d H:i", $shop['q_end_time']); //晒单商品揭晓时间
            $shaidan_info['sd_shop_code'] = $shop['q_user_code'];
            $shaidan_info['sd_userid'] = $val['sd_userid']; //晒单用户id
            $shaidan_info['sd_username'] = $this->huode_user_name($val['sd_userid'], "username"); //晒单用户
            $shaidan_info['sd_user_img'] = __ROOT__ . "/public/uploads/" . $this->huode_user_key($val['sd_userid'], "img"); //晒单用户头像
            $user_shop_number = D("yonghu_yys_record")->where(array("uid" => $val['sd_userid'], "shopid" => $val['sd_shopid']))->field("sum(gonumber) as gonumber")->find(); //用户购买数量查询
            $shaidan_info['gonumber'] = $user_shop_number['gonumber']; //用户购买数量
            $shaidan_info['url'] = __ROOT__ . "/public/uploads/"; //图片地址
            $shaidan_info['sd_title'] = $val['sd_title']; //晒单标题
            $shaidan_info['sd_time'] = date("Y-m-d H:i", $val['sd_time']); //晒单时间
            $shaidan_info['sd_qishu'] = $shop['qishu']; //晒单期数
            $shaidan_info['sd_content'] = $val['sd_content']; //晒单内容
            $shaidan_info['sd_photolist'] = $val['sd_photolist']; //晒单组图
            echo json_encode($shaidan_info);
        }
    }

    //获取个人中心购买数据
    //没改
    public function get_user_info() {
        $userid = safe_replace($this->segment(4)); //用户ID
        $pagenum = safe_replace($this->segment(5)); //分页查询
        if (!empty($pagenum)) {
            $num = 10; //每页数量
            $total = D("yonghu_yys_record")->where(array("uid" => $userid))->count();
            $page = new \Claduipi\Tools\page;
            $page->config($total, $num, $pagenum, "0");
            $userbuy = D("yonghu_yys_record")->where(array("uid" => $userid))->order("time DESC")->limit(($pagenum - 1) * $num, $num)->select();
            foreach ($userbuy as $key => $val) {
                $shop = D("shangpin")->where(array("id" => $val['shopid']))->find(); //查询shoplist
                $userbuy_info[$key]['qishu'] = $shop['qishu']; //期数
                $userbuy_info[$key]['shenyurenshu'] = $shop['shenyurenshu']; //剩余人数
                $userbuy_info[$key]['zongrenshu'] = $shop['zongrenshu']; //总人数
                $userbuy_info[$key]['gonumber'] = $val['gonumber']; //本次参与
                $userbuy_info[$key]['shop_img'] = __ROOT__ . "/public/uploads/" . $shop['thumb']; //商品图片
                $userbuy_info[$key]['jindu'] = $shop['canyurenshu'] / $shop['zongrenshu'] * 100; //商品进度
                $userbuy_info[$key]['q_showtime'] = $shop['q_showtime']; //中奖号码
                $userbuy_info[$key]['title'] = $val['shopname']; //商品title
                $userbuy_info[$key]['shopid'] = $val['shopid']; //商品id
                $userbuy_info[$key]['is_shi'] = $shop['is_shi']; //ten
                /* 已揭晓商品 */
                $userbuy_info[$key]['q_end_time'] = date("Y-m-d H:i", $shop['q_end_time']); //商品揭晓时间
                $userbuy_info[$key]['q_user_code'] = $shop['q_user_code']; //中奖号码
                $userbuy_info[$key]['username'] = $this->huode_user_name($shop['q_uid'], "username"); //中奖用户
                $user_jiexiaoshop_number = D("yonghu_yys_record")->where(array("uid" => $shop['q_uid'], "shopid" => $shop['id']))->field("sum(gonumber) as gonumber")->find(); //用户购买数量查询
                $userbuy_info[$key]['jiexiao_gonumber'] = $user_jiexiaoshop_number['gonumber']; //用户购买数量
            }
        } else {
            $user_info = $this->db->YOne("select * from `@#_yonghu` where `uid`= '" . $userid . "'"); //用户数据
            $userbuy_info['userimg'] = YYS_LOCAL_PATH . "/love/uploads/" . huode_user_key($user_info['uid'], "img"); //用户头像
            $userbuy_info['username'] = huode_user_name($user_info['uid'], "username"); //用户昵称
            $userbuy_info['userid'] = $user_info['uid'] + 1000000; //用户昵称
        }
        echo json_encode($userbuy_info);
    }

    //个人中心中奖记录
    //没改
    public function get_uesr_winner() {
        $userid = safe_replace($this->segment(4)); //用户ID
        $pagenum = safe_replace($this->segment(5)); //分页查询
        $num = 10; //每页数量
        $total = D("shangpin")->where("q_end_time !='' and q_showtime !='Y'")->count();
        $page = new \Claduipi\Tools\page;
        $page->config($total, $num, $pagenum, "0");
        $shop = D("shangpin")->where("q_end_time !='' and q_showtime !='Y' and `q_uid` =  $userid")->order("q_end_time DESC")->limit(($pagenum - 1) * $num, $num)->select();
        foreach ($shop as $key => $val) {
            $shop_info[$key]['title'] = $val['title']; //商品标题
            $shop_info[$key]['id'] = $val['id']; //商品id
            $shop_info[$key]['shop_img'] = __ROOT__ . "/public/uploads/" . $val['thumb']; //商品图片
            $shop_info[$key]['qishu'] = $val['qishu']; //商品期数
            $shop_info[$key]['q_end_time'] = date("Y-m-d h:i", $val['q_end_time']); //商品揭晓时间date("Y-m-d",$val['q_end_time']);
            $shop_info[$key]['q_user_code'] = $val['q_user_code']; //中奖号码
            $shop_info[$key]['username'] = $this->huode_user_name($val['q_uid'], "username"); //中奖用户
            $user_shop_number = D("yonghu_yys_record")->where(array("uid" => $val['q_uid'], "shopid" => $val['id']))->field("sum(gonumber) as gonumber")->find(); //用户购买数量查询
            $shop_info[$key]['gonumber'] = $user_shop_number['gonumber']; //用户购买数量
        }
        echo json_encode($shop_info);
    }

    //个人中心晒单
    //没改
    public function get_user_share() {
        $userid = safe_replace($this->segment(4)); //用户id
        $pagenum = safe_replace($this->segment(5)); //分页查询
        $num = 10; //每页数量
        $total = D("shai")->where(array("sd_userid" => $userid))->count();

        $page = new \Claduipi\Tools\page;
        $page->config($total, $num, $pagenum, "0");
        $shaidan = D("shai")->where(array("sd_shopsid" => "{$userid}"))->order("sd_time DESC")->limit(($pagenum - 1) * $num, $num)->select();
        foreach ($shaidan as $key => $val) {
            $shaidan_info[$key]['sd_id'] = $val['sd_id']; //晒单id
            $shop = D("shangpin")->where(array("id" => $val['sd_shopid']))->find();
            $shaidan_info[$key]['sd_shop_title'] = $shop['title'];
            $shaidan_info[$key]['sd_userid'] = $val['sd_userid']; //晒单用户id
            $shaidan_info[$key]['sd_username'] = $this->huode_user_name($val['sd_userid'], "username"); //晒单用户
            $shaidan_info[$key]['sd_user_img'] = __ROOT__ . "/public/uploads/" . huode_user_key($val['sd_userid'], "img"); //晒单用户头像
            $shaidan_info[$key]['url'] = __ROOT__ . "/public/uploads/"; //图片地址
            $shaidan_info[$key]['sd_title'] = $val['sd_title']; //晒单标题
            $shaidan_info[$key]['sd_time'] = date("Y-m-d H:i", $val['sd_time']); //晒单时间
            $shaidan_info[$key]['sd_qishu'] = $shop['qishu']; //晒单期数
            $shaidan_info[$key]['sd_content'] = $val['sd_content']; //晒单内容
            $shaidan_info[$key]['sd_photolist'] = $val['sd_photolist']; //晒单组图
        }
        echo json_encode($shaidan_info);
    }

    //添加商品到购物车
    //没改
    public function addShopCart() {
        $ShopId = safe_replace($this->segment(4)); //添加商品ID
        $ShopNum = safe_replace($this->segment(5)); //添加数量
        $Mcartlist = $this->Mcartlist;
        if ($ShopId == 0 || $ShopNum == 0) {
            $cart['status'] = 1; //添加失败
        } else {
            $Mcartlist[$ShopId]['num'] = $ShopNum; //添加数据
            cookie('Cartlist', json_encode($Mcartlist), '');
            $cart['status'] = 0;   //表示添加成功	
        }
        echo json_encode($cart);
    }

    //购物车数量
    public function cartnum() {
        $Mcartlist = $this->Mcartlist;
        if (is_array($Mcartlist)) {
            $cartnum['status'] = 0;
            $cartnum['num'] = count($Mcartlist);
            $zj = 0;
            foreach ($Mcartlist as $key => $val) {
                $shoplist = D("shangpin")->where("id='$key' and shenyurenshu!='0'")->find();
                $zj+=$shoplist['yunjiage'] * $val['num'];
            }
            $cartnum['zongji'] = substr(sprintf("%.3f", $zj), 0, -1);
        } else {
            $cartnum['status'] = 1;
            $cartnum['num'] = 0;
        }
        if (C('fufen_yuan')) {
            $cartnum['fufen_yuan'] = C('fufen_yuan');
        }
        echo json_encode($cartnum);
    }

    //购物车查询
    public function app_shoplist() {
        $Cartlist = $this->Mcartlist;
        if (is_array($Cartlist)) {
            $zj = 0;
            foreach ($Cartlist as $key => $val) {
                $shoplist = D("shangpin")->where("id='$key' and shenyurenshu!='0'")->find();
                $zj+=$shoplist['yunjiage'] * $val['num'];
            }
            //购物车数量
            $cartinfo['status'] = 0;
            $cartinfo['zongji'] = substr(sprintf("%.3f", $zj), 0, -1);
            $cartnum['num'] = count($Cartlist);
            foreach ($Cartlist as $key => $val) {
                $cartinfo['cart'][$key] = D("shangpin")->where(array("id" => $key))->find();
                $cartinfo['cart'][$key]['thumb'] = __ROOT__ . "/public/uploads/" . $cartinfo['cart'][$key]['thumb'];
                $cartinfo['cart'][$key]['num'] = $val['num'];
            }
        } else {
            $cartinfo['status'] = 1;
        }

        echo json_encode($cartinfo);
    }

    //购物车删除
    public function del_cartshop_local() {
        $cartlist = $this->Mcartlist;
        foreach ($cartlist as $key => $val) {
            $shop[$key] = D("shangpin")->where(array("id" => $key, "shenyurenshu" => "0"))->find();
        }
        foreach ($shop as $key => $val) {
            if ($val['id'] == null)
                continue;
            unset($cartlist[$val['id']]);
        }
        $Mcartlist = $cartlist;
        cookie('Cartlist', json_encode($cartlist), '');
    }

    //购物车删除
    //没改
    public function del_cartshop() {
        $ShopId = safe_replace($this->segment(4));
        $cartlist = $this->Mcartlist;
        if ($ShopId == 0) {
            $cart['status'] = 1;   //删除失败		
        } else {
            if (is_array($cartlist)) {
                if (count($cartlist) == 1) {
                    foreach ($cartlist as $key => $val) {
                        if ($key == $ShopId) {
                            $cart['status'] = 0;
                            cookie('Cartlist', '', '');
                        } else {
                            $cart['status'] = 1;
                        }
                    }
                } else {
                    foreach ($cartlist as $key => $val) {
                        if ($key == $ShopId) {
                            $cart['status'] = 0;
                        } else {
                            $Mcartlist[$key]['num'] = $val['num'];
                        }
                    }
                    cookie('Cartlist', json_encode($Mcartlist), '');
                }
            } else {
                $cart['status'] = 1;   //删除失败
            }
        }
        echo json_encode($cart);
    }

    //购物车支付
    //没改  里面不会改  程序设计 不符合这个
    function pay_sub() {
        $userid = safe_replace(I("userid")); //用户id
        $pass = safe_replace(I("pass")); //用户密码
        $pay_type = safe_replace(I("type")); //支付类型
        //支付信息
        header("Cache-control: private");
        parent::__construct();
        session_start();
        $_SESSION['submitcode'] = $submitcode = uniqid(); //获取SESSION
        $checkpay = $pay_type; //获取支付方式 
        $banktype = "nobank"; //获取选择的银行
        $money = safe_replace(I("money")); //获取需支付金额
        $pay_checkbox = true;
        /*         * ***********
          支付start
         * *********** */
        if (I("score")) {
            if (C('fufen_yuan')) {
                $fufen = $fufen * C('fufen_yuan');
            }
        } else {
            $fufen = 0;
        }
        $uid = $userid; //支付用户id
        $pay_checkbox = false;
        $pay_type_bank = false;
        $pay_type_id = false;
        $member = D("yonghu")->where(array("uid" => $userid, "password" => $pass))->find();
        if (!empty($member)) {
            if ($pay_type == "money") {//余额支付
                //判断余额是否可支付
                if ($member['money'] >= $money) {//余额足够
                    $pay_checkbox = true;
                    /*                     * ***********
                      支付start
                     * *********** */
                    $ok = R("Pay/createOrder", array($uid, '', 'go_record')); //云购商品
                    if ($ok == "ok") {
                        $pay_checkbox = true;
                        $check = R("Pay/start_pay", array($pay_checkbox, $pay_type));
                        if ($check) {
                            cookie('Cartlist', NULL);
                            $info['status'] = 0; //购买成功								
                        } else {
                            //失败	
                            cookie('Cartlist', NULL);
                            $info['status'] = "购买失败";
                        }
                    } else {
                        $info['status'] = "购物车没有商品";
                    }
                }
            } else if ($pay_type == "fufen") {//余额支付
                //判断余额是否可支付
                if ($member['score'] >= $fufen) {//余额足够
                    $pay = System::DOWN_App_class('pay', 'pay');
                    $pay->fufen = $fufen;
                    $ok = $pay->init($uid, '', 'go_record'); //云购商品
                    if ($ok == "ok") {
                        $pay_checkbox = true;
                        $check = $pay->start_pay($pay_checkbox, $pay_type);
                        if ($check) {
                            cookie('Cartlist', NULL);
                            $info['status'] = 0; //购买成功								
                        } else {
                            //失败	
                            cookie('Cartlist', NULL);
                            $info['status'] = "购买失败";
                        }
                    } else {
                        $info['status'] = "购物车没有商品";
                    }
                }
            }
            echo json_encode($info);
        }
    }

    /*     * *********************************************APP接口结束2016.2.23 10:30***************************************************** */

    //即将揭晓商品
    public function show_msjxshop() {
        //暂时没做
        //即将揭晓商品
        $shoplist['listItems'][0]['codeID'] = 14;  //商品id
        $shoplist['listItems'][0]['period'] = 3;  //商品期数
        $shoplist['listItems'][0]['goodsSName'] = '苹果';  //商品名称
        $shoplist['listItems'][0]['seconds'] = 10;  //商品名称
        $shoplist['errorCode'] = 0;
    }

    //没改
    public function delCartItem() {
        $ShopId = safe_replace($this->segment(4));
        $cartlist = $this->Mcartlist;
        if ($ShopId == 0) {
            $cart['code'] = 1;   //删除失败
        } else {
            if (is_array($cartlist)) {
                if (count($cartlist) == 1) {
                    foreach ($cartlist as $key => $val) {
                        if ($key == $ShopId) {
                            $cart['code'] = 0;
                            cookie('Cartlist', '', '');
                        } else {
                            $cart['code'] = 1;
                        }
                    }
                } else {
                    foreach ($cartlist as $key => $val) {
                        if ($key == $ShopId) {
                            $cart['code'] = 0;
                        } else {
                            $Mcartlist[$key]['num'] = $val['num'];
                        }
                    }
                    cookie('Cartlist', json_encode($Mcartlist), '');
                }
            } else {
                $cart['code'] = 1;   //删除失败
            }
        }
        echo json_encode($cart);
    }

    public function getCodeState() {
        $itemid = safe_replace($this->segment(4));
        $item = D("shangpin")->where(array("id" => $itemid))->find();
        $a['Code'] = 1;
        if (!$item) {
            $a['Code'] = 0;
        }
        echo json_encode($a);
    }

    //login
    //没改
    public function userlogin() {
        $username = $this->segment(4);
        $password = md5($this->segment(5));
        $logintype = '';
        if (strpos($username, '@') == false) {
            //手机				
            $logintype = 'mobile';
        } else {
            //邮箱
            $logintype = 'email';
        }
        $member = D("yonghu")->where(array("$logintype" => "$username", "password" => "$password"))->find();
        if (!$member) {
            //帐号不存在错误
            $user['state'] = 1;
            $user['num'] = -2;
        }
        if ($member[$check] != 1) {
            $user['state'] = 2; //未验证
        }
        if (!is_array($member)) {
            //帐号或密码错误
            $user['state'] = 1;
            $user['num'] = -1;
        } else {
            //登录成功 
            cookie("uid", $this->encrypt($member['uid']), 60 * 60 * 24 * 7);
            cookie("ushell", $this->encrypt(md5($member['uid'] . $member['password'] . $member['mobile'] . $member['email'])), 60 * 60 * 24 * 7);

            $user['state'] = 0;
        }
        echo json_encode($user);
    }

    //登录成功后
    public function loginok() {
        $user['Code'] = 0;
        echo json_encode($user);
    }

    /*     * *********************************注册******************************** */

    //检测用户是否已注册
    //没改
    public function checkname() {
        $config_email = System::DOWN_sys_config("email"); //不知道是什么
        $config_mobile = C("mobile");
        $name = $this->segment(4);
        $regtype = null;
        if ($this->checkmobile($name)) {
            $regtype = 'mobile';
            if (empty($config_mobile['mid']) && empty($config_email['mpass'])) {
                $user['state'] = 2; //_note("系统短息配置不正确!");
                echo json_encode($user);
                exit;
            }
        }
        $member = D("yonghu")->where(array("mobile" => $name))->find();
        if (is_array($member)) {
            if ($member['mobilecode'] == 1 || $member['emailcode'] == 1) {
                $user['state'] = 1; //_note("该账号已被注册");
            } else {
                D("yonghu")->where(array("mobile" => $name))->delete();
                $user['state'] = 0;
            }
        } else {
            $user['state'] = 0; //表示数据库里没有该帐号
        }
        echo json_encode($user);
    }

    //将数据注册到数据库
    //没改
    public function userMobile() {
        $name = $this->segment(4);
        $pass = md5($this->segment(5));
        $time = time();
        $decode = 0;
        //邮箱验证 -1 代表未验证， 1 验证成功 都不等代表等待验证
        $data = array("mobile" => $name, "password" => $pass, "img" => "photo/member.jpg", "emailcode" => "-1", "mobilecode" => "-1", "yaoqing" => $decode, "time" => $time);
        if (D("yonghu")->add($data)) {
            $userMobile['state'] = 0;
        } else {
            $userMobile['state'] = 1;
        }
        echo json_encode($userMobile);
    }

    //验证输入的手机验证码
    public function mobileregsn() {
        $mobile = $this->segment(4);
        $checkcodes = $this->segment(5);
        $member = D("yonghu")->where(array("mobile" => $mobile))->find();
        if (strlen($checkcodes) != 6) {
            $mobileregsn['state'] = 1;
            echo json_encode($mobileregsn);
            exit;
        }
        $usercode = explode("|", $member['mobilecode']);
        if ($checkcodes != $usercode[0]) {
            $mobileregsn['state'] = 1;
            echo json_encode($mobileregsn);
            exit;
        }
        D("yonghu")->where(array("uid" => $member['uid']))->save(array("mobilecode" => "1"));
        cookie("uid", $this->encrypt($member['uid']), 60 * 60 * 24 * 7);
        cookie("ushell", $this->encrypt(md5($member['uid'] . $member['password'] . $member['mobile'] . $member['email'])), 60 * 60 * 24 * 7);
        $mobileregsn['state'] = 0;
        $mobileregsn['str'] = 1;
        echo json_encode($mobileregsn);
    }

    //重新发送验证码
    //没改
    public function sendmobile() {
        $name = $this->segment(4);
        $member = D("yonghu")->where(array("mobile" => $name))->find();
        if (!$member) {
            $sendmobile['state'] = 1;
            echo json_encode($sendmobile);
            exit;
        }
        $checkcode = explode("|", $member['mobilecode']);
        $times = time() - $checkcode[1];
        if ($times > 120) {
            $sendok = R("Tools/send_mobile_reg_code", array($name, $member['uid']));
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

    //最新揭晓
    //没改
    public function getLotteryList() {
        $FIdx = $this->segment(4);
        $EIdx = 10; //$this->segment(5);
        $isCount = $this->segment(6);
        $db_goods = D("shangpin");
        $shopsum = $db_goods->where("q_end_time !=''")->field("id")->count();
        //最新揭晓
        $shoplist['listItems'] = $db_goods->where("q_end_time !='' and q_showtime!='Y'")->order("q_end_time DESC")->limit($FIdx, $EIdx)->select();
        if (empty($shoplist['listItems'])) {
            $shoplist['code'] = 1;
        } else {
            foreach ($shoplist['listItems'] as $key => $val) {
                //查询出购买次数
                $recodeinfo = D("yonghu_yys_record")->where(array("uid" => "{$val['q_uid']}", "shopid" => "{$val['id']}"))->field("gonumber")->find();
                $shoplist['listItems'][$key]['q_user'] = $this->huode_user_name($val['q_uid']);
                $shoplist['listItems'][$key]['userphoto'] = $this->huode_user_key($val['q_uid'], 'img');
                $shoplist['listItems'][$key]['q_end_time'] = $this->microt($val['q_end_time']);
                $shoplist['listItems'][$key]['gonumber'] = $recodeinfo['gonumber'];
            }
            $shoplist['code'] = 0;
            $shoplist['count'] = count($shopsum);
        }
        echo json_encode($shoplist);
    }

    //访问他人购买记录
    //没改
    public function getUserBuyList() {
        $type = $this->segment(4);
        $uid = $this->segment(5);
        $FIdx = $this->segment(6);
        $EIdx = 10; //$this->segment(7);
        $isCount = $this->segment(8);
        $db = new \Think\Model;
        if ($type == 0) {
            //参与云购的商品 全部...
            $filed = "*,sum(gonumber) as gonumber";
            $where = array("a.uid" => "{$uid}");
            $shoplist = $db->table("yys_yonghu_yys_record a")->join("yys_shangpin b on a.shopid=b.id")->where($where)->field($filed)->group("shopid")->select();
            $shop['listItems'] = $db->table("yys_yonghu_yys_record a")->join("yys_shangpin b on a.shopid=b.id")->where($where)->field($filed)->group("shopid")->order("a.time desc")->limit($FIdx, $EIdx)->select();
        } elseif ($type == 1) {
            //获得奖品		
            $shoplist = $db->table("yys_shangpin")->where(array("q_uid" => "$uid"))->select();
            $shop['listItems'] = $db->table("yys_shangpin")->where(array("q_uid" => "$uid"))->order("q_end_time desc")->limit($FIdx, $EIdx)->select();
        } elseif ($type == 2) {
            //晒单记录
            $shoplist = $db->table("yys_shai a")->join("yys_shangpin b on a.sd_shopid=b.id")->where(array("a.sd_userid" => "$uid"))->select();
            $shop['listItems'] = $db->table("yys_shai a")->join("yys_shangpin b on a.sd_shopid=b.id")->where(array("a.sd_userid" => "$uid"))->order("a.sd_time desc")->limit($FIdx, $EIdx)->select();
        }
        if (empty($shop['listItems'])) {
            $shop['code'] = 4;
        } else {
            foreach ($shop['listItems'] as $key => $val) {
                if ($val['q_end_time'] != '') {
                    $shop['listItems'][$key]['codeState'] = 3;
                    $shop['listItems'][$key]['q_user'] = $this->huode_user_name($val['q_uid']);
                    $shop['listItems'][$key]['q_end_time'] = $this->microt($val['q_end_time']);
                }
                if (isset($val['sd_time'])) {
                    $shop['listItems'][$key]['sd_time'] = date('m月d日 H:i', $val['sd_time']);
                }
            }
            $shop['code'] = 0;
            $shop['count'] = count($shoplist);
        }
        echo json_encode($shop);
    }

    //查看计算结果
    //没改  参数没接收
    public function getCalResult() {
        $itemid = $this->segment(4);
        $curtime = time();
        $item = D("shangpin")->where("id='$itemid' and q_end_time is not null")->find();
        if ($item['q_content']) {
            $item['contcode'] = 0;
            $item['itemlist'] = unserialize($item['q_content']);
            foreach ($item['itemlist'] as $key => $val) {
                $item['itemlist'][$key]['time'] = $this->microt($val['time']);
                $h = date("H", $val['time']);
                $i = date("i", $val['time']);
                $s = date("s", $val['time']);
                list($timesss, $msss) = explode(".", $val['time']);
                $item['itemlist'][$key]['timecode'] = $h . $i . $s . $msss;
            }
        } else {
            $item['contcode'] = 1;
        }
        if (!empty($item)) {
            $item['code'] = 0;
        } else {
            $item['code'] = 1;
        }
        echo json_encode($item);
    }

    //付款
    public function UserPay() {
        
    }

//显示两分钟内 马上揭晓的商品
    public function GetStartRaffleAllList() {

        //暂时没有该功能。。。。。
    }

//  生成充值记录
    function addmoneyRecord() {
        $uid = I("userid");
        $money = I("totalMoney");
        $dingdancode = 'C' . time() . substr(microtime(), 2, 6) . rand(0, 9); //订单号	
        $channel = I("channel");
        if ($channel == "ALI_APP") {
            $pay_type = "支付宝APP支付";
        } else if ($channel == "WX_APP") {
            $pay_type = "微信APP支付";
        } else if ($channel == "UN_APP") {
            $pay_type = "银联APP支付";
        } else if ($channel == "BD_APP") {
            $pay_type = "百度钱包支付";
        } else {
            $message['type'] = 0;
            $message['contente'] = "请选择支付方式";
            echo json_encode($message);
            exit;
        }
        $time = time();
        $record_data = array("uid" => "$uid", "code" => "$dingdancode", "money" => "$money", "pay_type" => "$pay_type", "status" => "未付款", "time" => "$time", "scookies" => "$scookies");
        $query = $this->model->table("yys_yonghu_addmoney_record")->add($record_data);
        if ($query) {
            $db->commit();
        } else {
            $db->rollback();
            return false;
        }
        $message['type'] = 1;
        $message['code'] = $dingdancode;
        echo json_encode($message);
    }

    public function qiantai() {
        $out_trade_no = I("orderID");
        $channel = I("channel");
        $totalfee = I("totalMoney");
        // 校验签名，然后进行业务处理
        $result = 1;
        if ($result == 1) {//验证成功
            //商户订单号
            //开始处理及时到账和担保交易订单
            $db = new \Think\Model;
            $db->startTrans();
            $dingdaninfo = $db->table("yys_yonghu_addmoney_record")->lock(true)->where(array("code" => $out_trade_no))->find();
            if (!$dingdaninfo) {
                $message['state'] = -1;
                $message['content'] = "无此订单";
                echo json_encode($message);
                exit;
            } //没有该订单,失败
            if ($dingdaninfo['status'] == '已付款') {
                $message['state'] = 0;
                $message['content'] = "充值成功";
                echo json_encode($message);
                exit;
            }
            $c_money = intval($dingdaninfo['money']);
            $uid = $dingdaninfo['uid'];
            $time = time();
            if ($totalfee != $c_money) {
                $message['state'] = -1;
                $message['content'] = "订单金额与付款金额不符";
                echo json_encode($message);
                exit;
            }
            if ($channel == "ALI_APP") {
                $pay_type = "支付宝APP支付";
            } else if ($channel == "WX_APP") {
                $pay_type = "微信APP支付";
            } else if ($channel == "UN_APP") {
                $pay_type = "银联APP支付";
            } else if ($channel == "BD_APP") {
                $pay_type = "百度钱包支付";
            }
            $up_q1 = $db->table("yys_yonghu_addmoney_record")->where(array("id" => $dingdaninfo['id'], "code" => $dingdaninfo['code']))->save(array("pay_type" => $pay_type, "status" => "已付款"));
            $up_q2 = $db->table("yys_yonghu")->where(array("uid" => $uid))->setInc('money', $c_money);
            $up_q3 = $db->table("yys_yonghu_zhanghao")->add(array("uid" => "$uid", "type" => "1", "pay" => "账户", "content" => "充值", "money" => "$c_money", "time" => "$time"));
            if ($up_q1 && $up_q2 && $up_q3) {
                $this->db->tijiao_commit();
                $message['state'] = 0;
                $message['content'] = "充值成功";
                echo json_encode($message);
                exit;
            } else {
                $this->db->tijiao_rollback();
                $message['state'] = -1;
                $message['content'] = "充值失败";
                echo json_encode($message);
                exit;
            }
        }//开始处理订单结束
    }

    public function houtai() {
        $appId = "d348f028-a2dc-4a1b-9fbd-6bb0c07b6e0b";
        $appSecret = "7c56c547-feea-4d2f-9d70-03512c56711e";
        $jsonStr = file_get_contents("php://input");
        $msg = json_decode($jsonStr);
        //第一步:验证签名
        $sign = md5($appId . $appSecret . $msg->timestamp);
        if ($sign != $msg->sign) {
            // 签名不正确
            exit();
        } else {
            $result = 1;
        }
        $out_trade_no = $msg->transaction_id;
        $db = new \Think\Model;
        $db->startTrans();
        $dingdaninfo = $db->table("yys_yonghu_addmoney_record")->lock(true)->where(array("code" => $out_trade_no))->find();
        if (!$dingdaninfo) {
            echo "fail10";
            exit;
        } //没有该订单,失败
        if ($dingdaninfo['status'] == '已付款') {
            echo "success";
            exit;
        }
        $c_money = intval($dingdaninfo['money']);
        if (($c_money * 100) != $msg->transaction_fee) {
            echo "交易金额与订单金额不符";
            exit;
        }
        $uid = $dingdaninfo['uid'];
        if ($result == 1) {//验证成功
            //商户订单号
            switch ($msg->channel_type) {
                case "WX":
                    /**
                     * 处理业务
                     */
                    break;
                case "ALI":
                    $pay_type = "支付宝APP支付";
                    break;
                case "UN":
                    $pay_type = "银联APP支付";
                    break;
                case "WX":
                    $pay_type = "微信APP支付";
                    break;
                case "BD":
                    $pay_type = "百度钱包支付";
                    break;
            }
            //开始处理及时到账和担保交易订单

            $time = time();
            $up_q1 = $db->table("yys_yonghu_addmoney_record")->where(array("id" => $dingdaninfo['id'], "code" => $dingdaninfo['code']))->save(array("pay_type" => $pay_type, "status" => "已付款"));
            $up_q2 = $db->table("yys_yonghu")->where(array("uid" => $uid))->setInc('money', $c_money);
            $up_q3 = $db->table("yys_yonghu_zhanghao")->add(array("uid" => "$uid", "type" => "1", "pay" => "账户", "content" => "充值", "money" => "$c_money", "time" => "$time"));

            if ($up_q1 && $up_q2 && $up_q3) {
                $db->commit();
                echo 'success';
                exit;
            } else {
                $db->rollback();
                echo "fail8";
                exit;
            }
        }
    }

}

/* * ***************APP封装方法******************* */

//头像上传异步方法
function upheadimg($base64 = '') {
    $IMG = base64_decode($base64);
    $mulu = date("Ymd", time());
    $path = __ROOT__ . '/public/uploads/touimg/' . $mulu;
    if (!file_exists($path)) {
        mkdir($path);
    }
    $time = time();
    file_put_contents($path . "/" . $time . '.jpg', $IMG);
    $go_user_img = 'touimg/' . $mulu . '/' . $time . '.jpg';
    $size = size($go_user_img);
    return $go_user_img;
}

//没改   G_UPLOAD不知道是什么
function size($imgurl = '') {
    $upload = new \Claduipi\Tools\upload;
    $upload->thumbs(160, 160, false, G_UPLOAD . $imgurl);
    $upload->thumbs(80, 80, false, G_UPLOAD . $imgurl);
    $upload->thumbs(30, 30, false, G_UPLOAD . $imgurl);
}
