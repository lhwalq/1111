<?php

/**
 * 菜单
 * addtime 2016/03/23
 */

namespace Duipi\Controller;

use Think\Controller;

class sendController extends BaseController {
    /*
      @type  1 邮件
      @type  2 手机
      @type  3 邮件,手机
     */

    public function send_shop_code() {
        ignore_user_abort(TRUE);
        set_time_limit(0);
        if (!isset($_POST['send']) && !isset($_POST['uid']) && !isset($_POST['gid'])) {
            exit(0);
        }

        $uid = abs($_POST['uid']);
        $yonghuid = abs($_POST['gid']);
        $openids = D("yonghu_band")->where(array("b_uid" => $uid))->find();
        $sendinfo = D("fasong")->where(array("gid" => $yonghuid, "uid" => $uid))->field("id,send_type")->find();
        if ($sendinfo)
        exit(0);
            $huiyuan = D("yonghu")->where(array("uid" => $uid))->find();
        if (!$huiyuan)
            exit(0);
        sleep(5);

        // $info = D("yonghu")->where(array("id" => $yonghuid))->field("id,q_user_code,q_end_time,title,q_user")->find();
        $info = D("shangpin")->field("id,sid,cateid,brandid,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,q_end_qishu,fahuo")->where(array("id" => $yonghuid))->find();
        if (!$info)
            exit(0);
        $weername = $this->huode_user_name($huiyuan, 'username', 'all');
        $this->send_insert($uid, $yonghuid, $weername, $info['title'], '-1');
        $type = C("send_type");
        if (!$type)
            exit(0);

        $q_time = abs(substr($info['q_end_time'], 0, 10));
        while (time() < $q_time) {
            sleep(5);
        }
        $ret_send = false;

        $dengluid = C("tongzhiid");

        $weixin = new \Claduipi\Wechat\weixin1;
        $iipp = $_SERVER["REMOTE_ADDR"];
        $tit = C("web_name_two");
        $titit = str_replace('&nbsp;', ' ', $info['title']);
        $template = array('touser' => $openids[b_data],
            'template_id' => $dengluid,
            'url' => C("URL_DOMAIN") . "user/orderlist",
            'topcolor' => "#7B68EE",
            'data' => array('first' => array('value' => "恭喜你，您在" . $tit . "购买的商品已获奖",
                    'color' => "#743A3A",
                ),
                'keyword1' => array('value' => $titit,
                    'color' => "#FF0000",
                ),
                'keyword2' => array('value' => "幸运号码" . $info['q_user_code'],
                    'color' => "#0000FF",
                ),
                'remark' => array('value' => "\\n请登陆网站查看详情！请尽快联系管理员发货！",
                    'color' => "#008000",
                ),
            )
        );

        $templatenew = array('touser' => $openids['b_data'],
            'template_id' => $dengluid,
            'url' => C("URL_DOMAIN") . "user/orderlist",
            'topcolor' => "#7B68EE",
            'data' => array('first' => array('value' => "恭喜你，您在" . $tit . "购买的商品已获奖",
                    'color' => "#743A3A",
                ),
                'keyword1' => array('value' => $titit,
                    'color' => "#FF0000",
                ),
                'keyword2' => array('value' => "幸运号码" . $info['q_user_code'],
                    'color' => "#0000FF",
                ),
                'remark' => array('value' => "\\n请登陆网站查看详情！请尽快联系管理员发货！",
                    'color' => "#008000",
                ),
            )
        );
        $weixin->send_template_message($template);
        $weixin->send_template_message($templatenew);
        if ($huiyuan['huiyuan'] != 1) {

            if ($type == '1') {
                if (!empty($huiyuan['email'])) {
                    $this->send_youjian_code($huiyuan['email'], $weername, $uid, $info['q_user_code'], $info['title']);
                    $ret_send = true;
                }
            }
            if ($type == '2') {
                if (!empty($huiyuan['mobile'])) {
                    $this->send_mobile_shop_code($huiyuan['mobile'], $uid, $info['q_user_code']);
                    $ret_send = true;
                }
            }

            if ($type == '3') {
                if (!empty($huiyuan['email'])) {
                    $this->send_youjian_code($huiyuan['email'], $weername, $uid, $info['q_user_code'], $info['title']);
                    $ret_send = true;
                }

                if (!empty($huiyuan['mobile'])) {

                    $this->send_mobile_shop_code($huiyuan['mobile'], $uid, $info['q_user_code']);

                    $ret_send = true;
                }
            }
        }
        if ($ret_send) {
            $this->send_insert($uid, $yonghuid, $weername, $info['title'], $type);
        }
        exit(0);
    }

    function send_youjian_code($youjian = null, $weername = null, $uid = null, $code = null, $shoptitle = null) {
        $moban = D("linshi")->where(array("key" => "template_email_shop"))->find();
        if (!$moban) {
            $moban = array();
            $moban['value'] = "恭喜您：{$weername},你在" . C("web_name") . "够买的商品{$shoptitle}已中奖,中奖码是:" . $code;
        } else {
            $moban['value'] = str_ireplace("{用户名}", $weername, $moban['value']);
            $moban['value'] = str_ireplace("{商品名称}", $shoptitle, $moban['value']);
            $moban['value'] = str_ireplace("{中奖码}", $code, $moban['value']);
        }
        $biaoti = "恭喜您!!! 您在" . C("web_name") . "够买的商品中奖了!!!";
        return $this->sendemail($youjian, '', $biaoti, $moban['value']);
    }

    function send_mobile_shop_code($mobile = null, $uid = null, $code = null) {

        if (!$uid)
            $this->note("发送用户手机获奖短信,用户ID不能为空！");
        if (!$mobile)
            $this->note("发送用户手机获奖短信,手机号码不能为空!");
        if (!$code)
            $this->note("发送用户手机获奖短信,中奖码不能为空!");
        $moban = D("linshi")->where(array("key" => "template_mobile_shop"))->find();

        if (!$moban) {
            $moban = array();
            $content = "你在" . C("web_name") . "够买的商品已中奖,中奖码是:" . $code;
        }
        if (empty($moban['value'])) {
            $content = "你在" . C("web_name") . "够买的商品已中奖,中奖码是:" . $code;
        } else {
            if (strpos($moban['value'], "00000000") == true) {
                $content = str_ireplace("00000000", $code, $moban['value']);
            } else {
                $content = $moban['value'] . $code;
            }
        }
        //$sendobj = new \Claduipi\Tools\sendmobile;

        return $this->sendmobile($mobile, $content);
    }

    private function send_insert($uid, $yonghuid, $weername, $shoptitle, $send_type) {
        $time = time();
        if ($send_type == '-1') {
            $data = array("uid" => $uid, "gid" => $yonghuid, "username" => $weername, "shoptitle" => $shoptitle, "send_type" => $send_type, "send_time" => $time);
            D("fasong")->add($data);
        } else {
            D("fasong")->where(array("gid" => $yonghuid, "uid" => $uid))->save(array("send_type" => $send_type));
        }
    }

}

?>