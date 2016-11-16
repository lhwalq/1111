<?php

/**
 * 邀请 （佣金相关）
 * addtime 2016/03/04
 */

namespace Duipi\Controller;

use Think\Controller;

class InviteController extends BaseController {

    public function _initialize() {
        $filter = array("init","delete","update","add");
		
//        if (DOWN_A != 'userphotoup' and DOWN_A != 'singphotoup') {
        $this->userinfo = $this->getUserInfo();
        if ($this->userinfo) {
            $this->assign("huiyuan", $this->userinfo);
        } else if (!in_array(ACTION_NAME, $filter)) {
            $this->autoNote("请先登录", C("URL_DOMAIN") . "user/login");
        }
    }

    public function init() {
        $ment = array(
            array("lists", "佣金链接管理", C("URL_DOMAIN") . "Invite/init"),
            array("reg", "添加佣金链接", C("URL_DOMAIN") . "Invite/add"),
            array("edit", "修改管理员", C("URL_DOMAIN") . "admin/reg", 'hide'),
        );
        $db = new \Think\Model;
        $lists = $db->table("yys_yongjin")->select();
        //$this->db->Ylist("SELECT * FROM `@#_yongjin` where 1");	
        $this->assign("ment", $ment);
        $this->assign("lists", $lists);
        $this->display("admin/invtie.list");
    }

    function friends() {
        $db = new \Think\Model;
        $huiyuan = $this->getUserInfo();
        $biaoti = "我的一元云购中心";
        $huiyuandj = $db->table("yys_yonghu_group")->select();
        $jingyan = $huiyuan['jingyan'];
        $lianjie = $db->table("yys_yongjin")->order("id desc")->limit(1)->select();
        foreach ($lianjie as $key => $val) {
            $yys['urls'][$key]['object_type'] = "";
            $yys['urls'][$key]['result'] = 'true';
            $yys['urls'][$key]['title'] = $val['title'];
            $yys['urls'][$key]['img'] = $val['img'];
            if ($huiyuan['yaoqing'] && empty($huiyuan['yaoqing2']) && empty($huiyuan['yaoqing3'])) {
                $yys['urls'][$key]['url_short'] = $val['link'] . '?yaoqing2=' . $huiyuan['yaoqing'] . '&yaoqing=' . $huiyuan['uid'];
            } else if ($huiyuan['yaoqing'] && $huiyuan['yaoqing2'] && empty($huiyuan['yaoqing3'])) {
                $yys['urls'][$key]['url_short'] = $val['link'] . '?yaoqing=' . $huiyuan['uid'] . '&yaoqing2=' . $huiyuan['yaoqing'] . '&yaoqing3=' . $huiyuan['yaoqing2'];
            } else if ($huiyuan['yaoqing'] && $huiyuan['yaoqing2'] && $huiyuan['yaoqing3']) {
                $yys['urls'][$key]['url_short'] = $val['link'] . '?yaoqing=' . $huiyuan['uid'] . '&yaoqing2=' . $huiyuan['yaoqing'] . '&yaoqing3=' . $huiyuan['yaoqing2'];
            } else {
                $yys['urls'][$key]['url_short'] = $val['link'] . '?yaoqing=' . $huiyuan['uid'];
            }
            $yys['urls'][$key]['object_id'] = "";
            $yys['urls'][$key]['url_long'] = $val['link'];
            $yys['urls'][$key]['type'] = "0";
        }
        if (!empty($huiyuandj)) {
            foreach ($huiyuandj as $key => $val) {
                if ($jingyan >= $val['jingyan_start'] && $jingyan <= $val['jingyan_end']) {
                    $huiyuan['yungoudj'] = $val['name'];
                }
            }
        }
        $uid = $huiyuan['uid'];
        $uid2 = $huiyuan['yaoqing'];
        $uid3 = $huiyuan['yaoqing2'];
        //$uid4=$huiyuan['yaoqing3'];
        $notinvolvednum = 0;  //未参加一元云购的人数
        $involvednum = 0;     //参加预购的人数
        $involvedtotal = 0;   //邀请人数
        //佣金部分
        $zongji = 0;
        $shourutotal = 0;
        $zhichutotal = 0;
        $cashoutdjtotal = 0;
        $cashouthdtotal = 0;
        $moneyData = $db->table("yys_yonghu a")->join("yys_yonghu_yys_record b on a.uid=b.uid")->where("yaoqing='{$huiyuan['uid']}' or yaoqing2='{$huiyuan['uid']}' or yaoqing3='{$huiyuan['uid']}'")->select();
        $money = 0;
        foreach ($moneyData as $key => $value) {
            $money += $value['money'];
        }
        //查询佣金消费(提现,充值)
        $zhichu = $db->table("yys_yonghu_recodes")->where("uid='$uid' and type !=1")->order("time DESC")->select();
        //查询被冻结金额
        $cashoutdj = $db->table("yys_yonghu_cashout")->where("uid='$uid' and auditstatus !=1")->field("SUM(money) as summoney")->order("time DESC")->find();
        if (!empty($zhichu)) {
            foreach ($zhichu as $key => $val3) {
                $zhichutotal+=$val3['money']; //总支出的佣金
            }
        }
        $zongji = $money - $zhichutotal;
        $cashoutdjtotal = $cashoutdj['summoney'];  //冻结佣金余额
        $ye = $db->table("yys_yonghu")->where("uid='{$huiyuan['uid']}'")->field("yongjin")->limit(1)->find();
        $cashouthdtotal = $ye['yongjin'];  //佣金余额
        //佣金部分
        //查询邀请好友信息
        $invifriends = $db->table("yys_yonghu")->where("yaoqing='{$huiyuan['uid']}'")->order("time DESC")->select();
        $involvedtotal = count($invifriends);
        $invifriends2 = $db->table("yys_yonghu")->where("yaoqing2='{$huiyuan['uid']}'")->order("time DESC")->select();
        $involvedtotal2 = count($invifriends2);
        $invifriends3 = $db->table("yys_yonghu")->where("yaoqing3='{$huiyuan['uid']}'")->order("time DESC")->select();
        $involvedtotal3 = count($invifriends3);

        for ($i = 0; $i < count($invifriends); $i++) {
            $sqluid = $invifriends[$i]['uid'];
            $sqname = $this->huode_user_name($invifriends[$i]);
            $invifriends[$i]['sqlname'] = $sqname;
            //查询邀请好友的消费明细
            $accounts[$sqluid] = $db->table("yys_yonghu_zhanghao")->where("uid='$sqluid'")->order("time DESC")->select();
            //判断哪个好友有消费
            if (empty($accounts[$sqluid])) {
                $notinvolvednum +=1;
                $records[$sqluid] = '未参与一元云购';
            } else {
                $involvednum +=1;
                $records[$sqluid] = '已参与一元云购';
            }
        }
        for ($i = 0; $i < count($invifriends2); $i++) {
            $sqluid = $invifriends2[$i]['uid'];
            $sqname = $this->huode_user_name($invifriends2[$i]);
            $invifriends2[$i]['sqlname'] = $sqname;
            //查询邀请好友的消费明细
            $accounts[$sqluid] = $db->table("yys_yonghu_zhanghao")->where("uid='$sqluid'")->order("time DESC")->select();
            //判断哪个好友有消费
            if (empty($accounts[$sqluid])) {
                $notinvolvednum +=1;
                $records[$sqluid] = '未参与一元云购';
            } else {
                $involvednum +=1;
                $records[$sqluid] = '已参与一元云购';
            }
        }
        for ($i = 0; $i < count($invifriends3); $i++) {
            $sqluid = $invifriends3[$i]['uid'];
            $sqname = $this->huode_user_name($invifriends3[$i]);
            $invifriends3[$i]['sqlname'] = $sqname;
            //查询邀请好友的消费明细
            $accounts[$sqluid] = $db->table("yys_yonghu_zhanghao")->where("uid='$sqluid'")->order("time DESC")->select();
            //判断哪个好友有消费
            if (empty($accounts[$sqluid])) {
                $notinvolvednum +=1;
                $records[$sqluid] = '未参与一元云购';
            } else {
                $involvednum +=1;
                $records[$sqluid] = '已参与一元云购';
            }
        }

        $appid = C("appid");
        $secret = C("secret");
        $jssdk = $jssdk = new \Claduipi\Wechat\JSSDK($appid, $secret);
        $signPackage = $jssdk->GetSignPackage();

        $this->assign("involvedtotal", $involvedtotal);
        $this->assign("involvedtotal2", $involvedtotal2);
        $this->assign("involvedtotal3", $involvedtotal3);
        $this->assign("huiyuan", $huiyuan);
        $this->assign("cashouthdtotal", $cashouthdtotal);
        $this->assign("signPackage", $signPackage);
        $this->assign("yys", $yys);
        $this->display("mobile/invite.friends");
    }

    //邀请记录 ajax
    public function getinvitelist() {
        $kaishi = htmlspecialchars(I("FIdx", 0)) - 1;
        $jieshu = htmlspecialchars(I("EIdx", 0));
        $status = I("status", 0);
        $huiyuan = $this->getUserInfo();
        $status = $status == 1 ? "" : $status;
        $mingxi = D("yonghu")->where(array("yaoqing" . $status => "{$huiyuan['uid']}"))->order("time ASC")->LIMIT($kaishi, $jieshu)->select();

        if (!$mingxi) {
            $yys['code'] = -1;
            $yys['tips'] = "暂无记录";
        } else {
            $yys['code'] = 0;
            $yys['str']['totalCount'] = count($mingxi);
            foreach ($mingxi as $key => $val) {
                $jilu = D("yonghu_yys_record")->where(array("uid" => "{$val['uid']}"))->order("time desc")->find();
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

    function cha() {
        $this->display("mobile/invite.cha");
    }

    function commissions() {
        $huiyuan = $this->getUserInfo();
        $biaoti = "我的一元云购中心";
        $huiyuandj = D("yonghu_group")->select();
        $db_user = D("yonghu");
        $ye = $db_user->where(array("uid" => "{$huiyuan['uid']}"))->field("yongjin")->find();
        $cashouthdtotal = $ye['yongjin'];  //佣金余额
        $this->assign("cashouthdtotal", $cashouthdtotal);
        $this->display("mobile/invite.commissions");
    }

    //1级佣金明细111
    public function getcommissionlist() {
        $kaishi = htmlspecialchars(I("FIdx", 0)) - 1;
        $jieshu = htmlspecialchars(I("EIdx", 0));
        $status = I("status", 0);
        $status = $status == 1 ? "" : $status;
        $huiyuan = $this->getUserInfo();
        $uid = $huiyuan['uid'];

        $yaoqing = D("yonghu a")->join("yys_yonghu_yys_record b on a.uid = b.uid")->where(array("a.yaoqing" . $status => "{$huiyuan['uid']}"))->order("id desc")->limit($kaishi, $jieshu)->select();
        $count = D("yonghu a")->join("yys_yonghu_yys_record b on a.uid = b.uid")->where(array("a.yaoqing" . $status => "{$huiyuan['uid']}"));

        if (empty($yaoqing[0][uid])) {
            $yys['code'] = -1;
            $yys['tips'] = "暂无记录";
        } else {
            $yys['code'] = 0;
            $yys['str']['totalCount'] = count($count);
            foreach ($yaoqing as $key => $val) {
                $yys['str']['listItems'][$key]['userName'] = $val['username'];
                $yys['str']['listItems'][$key]['buyTime'] = $this->microt($val['time']);
                $yys['str']['listItems'][$key]['codeID'] = $val['shopid'];
                $yys['str']['listItems'][$key]['period'] = "(第" . $val['shopqishu'] . "期)";
                $yys['str']['listItems'][$key]['descript'] = $val['shopname'];
                $yys['str']['listItems'][$key]['buyMoney'] = $val['moneycount'];
                $yys['str']['listItems'][$key]['brokerage'] = $val['ymoney' . $status];
                $yys['str']['listItems'][$key]['userWeb'] = $val['uid'];
                $yys['str']['listItems'][$key]['userPhoto'] = $this->huode_user_key($val['uid'], 'img');
                $yys['str']['listItems'][$key]['logType'] = "1";
                $yys['str']['listItems'][$key]['logType2'] = "1";
                $yys['str']['listItems'][$key]['applyState'] = "";
                $yys['str']['listItems'][$key]['applyID'] = "";
            }
        }
        echo json_encode($yys);
    }

    function cashout() {
        $huiyuan = $this->getUserInfo();
        $db = new \Think\Model;
        $huiyuandj = $db->table("yys_yonghu_group")->select();
        $ye = $db->table("yys_yonghu")->where(array("uid" => "{$huiyuan['uid']}"))->field("yongjin")->find();
        $cashouthdtotal = $ye['yongjin'];  //佣金余额
        $jingyan = $huiyuan['jingyan'];
        if (!empty($huiyuandj)) {
            foreach ($huiyuandj as $key => $val) {
                if ($jingyan >= $val['jingyan_start'] && $jingyan <= $val['jingyan_end']) {
                    $huiyuan['yungoudj'] = $val['name'];
                }
            }
        }
        $uid = $huiyuan['uid'];
        $zongji = 0;
        $shourutotal = 0;
        $zhichutotal = 0;
        $cashoutdjtotal = 0;
        //查询邀请好友id
        $invifriends = $db->table("yys_yonghu")->where("yaoqing='{$huiyuan['uid']}' or yaoqing2='{$huiyuan['uid']}' or yaoqing3='{$huiyuan['uid']}'")->order("time DESC")->select();
        //查询佣金收入
        for ($i = 0; $i < count($invifriends); $i++) {
            $sqluid = $invifriends[$i]['uid'];
            //查询邀请好友给我反馈的佣金
            $recodes[$sqluid] = $db->table("yys_yonghu_recodes")->where(array("uid" => "$sqluid", "type" => 1))->order("time DESC")->select();
        }
        //查询佣金消费(提现,充值)
        $zhichu = $db->table("yys_yonghu_recodes")->where("uid='$uid' and type !=1")->order("time DESC")->select();
        //查询被冻结金额
        $cashoutdj = $db->table("yys_yonghu_cashout")->where("uid='$uid' and auditstatus !='1'")->field("SUM(money) as summoney")->order("time DESC")->find();
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

        $zongji = $shourutotal - $zhichutotal;  //计算佣金余额
        $cashoutdjtotal = $cashoutdj['summoney'];  //冻结佣金余额

        if (isset($_POST['submit1'])) { //提现
            $money = abs(intval($_POST['money']));
            $weername = htmlspecialchars($_POST['txtusername']);
            $bankname = htmlspecialchars($_POST['txtBankName']);
            $branch = htmlspecialchars($_POST['txtSubBank']);
            $banknumber = htmlspecialchars($_POST['txtBankNo']);
            $linkphone = htmlspecialchars($_POST['txtPhone']);
            $time = time();
            $type = -3;  //收取1/消费-1/充值-2/提现-3
            if ($zongji < 100) {
                $this->notemobile("佣金金额大于100元才能提现！");
                exit;
            } elseif ($cashouthdtotal < $money) {
                $this->notemobile("输入额超出活动佣金金额！");
                exit;
            } elseif ($zongji < $money) {
                $this->notemobile("输入额超出总佣金金额！");
                exit;
            } else {
                //插入提现申请表  这里不用在佣金表中插入记录 等后台审核才插入
                $this->db->Query("INSERT INTO `@#_yonghu_cashout`(`uid`,`money`,`username`,`bankname`,`branch`,`banknumber`,`linkphone`,`time`)VALUES
			('$uid','$money','$weername','$bankname','$branch','$banknumber','$linkphone','$time')");
                $this->notemobile("申请成功！请等待审核！", C("URL_DOMAIN") . 'invite/cashout');
            }
        }

        if (isset($_POST['submit2'])) {//充值
            $money = abs(intval($_POST['txtCZMoney']));
            $type = 1;
            $pay = "佣金";
            $time = time();
            $content = "使用佣金充值到一元云购账户";

            if ($money <= 0 || $money > $zongji) {
                $this->notemobile("佣金金额输入不正确！");
                exit;
            }
            if ($cashouthdtotal < $money) {
                $this->notemobile("输入额超出活动佣金金额！");
                exit;
            }

            //插入记录
            $account = $this->db->Query("INSERT INTO `@#_yonghu_zhanghao`(`uid`,`type`,`pay`,`content`,`money`,`time`)VALUES
			('$uid','$type','$pay','$content','$money','$time')");

            // 查询是否有该记录
            if ($account) {
                //修改剩余金额
                $leavemoney = $huiyuan['money'] + $money;
                $mrecode = $this->db->Query("UPDATE `@#_yonghu` SET `money`='$leavemoney' WHERE `uid`='$uid' ");
                //在佣金表中插入记录
                $recode = $this->db->Query("INSERT INTO `@#_yonghu_recodes`(`uid`,`type`,`content`,`money`,`time`)VALUES
			('$uid','-2','$content','$money','$time')");
                $this->notemobile("充值成功！", C("URL_DOMAIN") . 'invite/cashout');
            } else {
                $this->notemobile("充值失败！");
            }
        }
        $this->assign("cashouthdtotal", $cashouthdtotal);
        $this->display("mobile/invite.cashout");
    }

    function record() {
        $this->display("mobile/invite.record");
        exit;
    }

    //提现记录
    public function getrecord() {
        $kaishi = htmlspecialchars(I("FIdx", 0)) - 1;
        $jieshu = htmlspecialchars(I("EIdx", 0));
        $huiyuan = $this->getUserInfo();
        $uid = $huiyuan['uid'];
        $db = D("yonghu_cashout");
        $count = $db->where(array("uid" => "{$uid}"))->order("time")->select();
        $recordarr = $db->where(array("uid" => "{$uid}"))->order("time DESC")->LIMIT($kaishi, $jieshu)->select();
        if (empty($recordarr)) {
            $yys['code'] = -1;
            $yys['tips'] = "暂无记录";
        } else {
            $yys['code'] = 0;
            $yys['str']['totalCount'] = count($recordarr);
            foreach ($recordarr as $key => $val) {
                $yys['str']['listItems'][$key]['userName'] = $val['username'];
                $yys['str']['listItems'][$key]['buyTime'] = date('Y.m.d H:i:s', $val['time']);
                $yys['str']['listItems'][$key]['codeID'] = $val['shopid'];
                $yys['str']['listItems'][$key]['period'] = "(第" . $val['shopqishu'] . "期)";
                $yys['str']['listItems'][$key]['descript'] = $val['shopname'];
                $yys['str']['listItems'][$key]['buyMoney'] = $val['moneycount'];
                $yys['str']['listItems'][$key]['brokerage'] = $val['money'];
                $yys['str']['listItems'][$key]['userWeb'] = $val['uid'];
                $yys['str']['listItems'][$key]['userPhoto'] = $this->huode_user_key($val['uid'], 'img');
                $yys['str']['listItems'][$key]['logType'] = "1";
                if ($val['auditstatus']) {
                    $yys['str']['listItems'][$key]['logType2'] = "4";
                } else {
                    $yys['str']['listItems'][$key]['logType2'] = "2";
                }
                if ($val['auditstatus']) {
                    $yys['str']['listItems'][$key]['applyState'] = "4";
                }
                $yys['str']['listItems'][$key]['applyID'] = $val['id'];
            }
        }
        echo json_encode($yys);
    }

    //佣金充值记录
    public function record1() {
        $this->display("mobile/invite.record1");
    }

    //佣金冲值记录
    public function getrecord1() {
        $kaishi = htmlspecialchars(I("FIdx", 0)) - 1;
        $jieshu = htmlspecialchars(I("EIdx", 0));
        $huiyuan = $this->getUserInfo();
        $uid = $huiyuan['uid'];
        $db = D("yonghu_zhanghao");
        $count1 = $db->where(array("uid" => "{$uid}"))->order("time")->select();
        $chongzhi = $db->where(array("uid" => "{$uid}", "type" => 1, "pay" => "佣金"))->order("time DESC")->LIMIT($kaishi, $jieshu)->select();
        if (empty($chongzhi)) {
            $yys['code'] = -1;
            $yys['tips'] = "暂无记录";
        } else {
            $yys['code'] = 0;
            $yys['str']['totalCount'] = count($chongzhi);
            foreach ($chongzhi as $key => $val) {
                $yys['str']['listItems'][$key]['userName'] = $val['username'];
                $yys['str']['listItems'][$key]['buyTime'] = date('Y.m.d H:i:s', $val['time']);
                $yys['str']['listItems'][$key]['codeID'] = $val['shopid'];
                $yys['str']['listItems'][$key]['period'] = "(第" . $val['shopqishu'] . "期)";
                $yys['str']['listItems'][$key]['descript'] = $val['shopname'];
                $yys['str']['listItems'][$key]['buyMoney'] = $val['moneycount'];
                $yys['str']['listItems'][$key]['brokerage'] = $val['money'];
                $yys['str']['listItems'][$key]['userWeb'] = $val['uid'];
                $yys['str']['listItems'][$key]['userPhoto'] = $this->huode_user_key($val['uid'], 'img');
                $yys['str']['listItems'][$key]['logType'] = "1";
                $yys['str']['listItems'][$key]['logType2'] = "2";
                $yys['str']['listItems'][$key]['applyState'] = "";
                $yys['str']['listItems'][$key]['applyID'] = $val['id'];
            }
        }
        echo json_encode($yys);
    }

    //商品佣金二维码
    public function sperweima() {

        vendor("phpqrcode.phpqrcode");
        $level = 'M';
//        // 点的大小：1到10,用于手机端4就可以了
        $size = 4;
//        echo \QRcode::png($data, false, $level, $size);
//        exit();

        $shangpinid = I("id", 0);
        $huiyuan = $this->getUserInfo();
        $uid = $huiyuan['uid'];
        $uid2 = $huiyuan['yaoqing'];
        $uid3 = $huiyuan['yaoqing2'];


        $newyao = $uid;
        if (!empty($uid2)) {
            $newyao = $uid . '/' . $uid2;
        }

        if (!empty($uid3)) {
            $newyao = $uid . '/' . $uid2 . '/' . $uid3;
        }

        $url = C("URL_DOMAIN") . "goods/items/goodsId/$shangpinid/$newyao";
        $ss = \QRcode::png($url, false, $level, $size);
        echo $ss;
    }

    public function update() {
        $ment = array(
            array("lists", "佣金链接管理", C("URL_DOMAIN") . "Invite/init"),
            array("reg", "添加佣金链接", C("URL_DOMAIN") . "Invite/add"),
            array("edit", "修改管理员", C("URL_DOMAIN") . "admin/reg", 'hide'),
        );
        $id = I("id");

        $wapone = D("yongjin")->where(array('id' => $id))->find();

        //$this->db->YOne("SELECT * FROM `@#_yongjin` where `id`='$id'");	

        if (isset($_POST['submit'])) {
			$user = $this->getAdminInfo(FALSE);
        if ($user['xianzhi']) {
            
                echo "<script>
				alert('权限不足,请联系总管理员');
				exit;			
				</script>";

exit;
            }
            $biaoti = htmlspecialchars(trim($_POST['title']));
            $link = htmlspecialchars(trim($_POST['link']));
            $biaoti2 = htmlspecialchars(trim($_POST['title2']));

            if (isset($_POST['image'])) {
                $img = $_POST['image'];
            } else {
                $img = $slideone['img'];
            }

            $sql = D("yongjin")->where(array('id' => $id))->data(array('img' => $img, 'title' => $biaoti, 'link' => $link, 'color' => $biaoti2))->save();
            ;

            //$this->db->Query("UPDATE `@#_yongjin` SET `img`='$img',`title`='$biaoti',`link`='$link',`color`='$biaoti2' WHERE `id`=$id");
            if ($sql) {
                $this->note("修改成功", C("URL_DOMAIN") . "invite/init");
            } else {
                $this->note("修改失败");
            }
        }
        $this->assign("id", $id);
        $this->assign("ment", $ment);
        $this->assign("wapone", $wapone);
        $this->display("admin/Invite.update");
    }

    public function add() {
        $ment = array(
            array("lists", "佣金链接管理", C("URL_DOMAIN") . "Invite/init"),
            array("reg", "添加佣金链接", C("URL_DOMAIN") . "Invite/add"),
            array("edit", "修改管理员", C("URL_DOMAIN") . "admin/reg", 'hide'),
        );
        if (isset($_POST['submit'])) {
            $biaoti = htmlspecialchars(trim($_POST['title']));
            $link = htmlspecialchars(trim($_POST['link']));
            $biaoti2 = htmlspecialchars(trim($_POST['title2']));
            if (isset($_POST['image'])) {
                $img = $_POST['image'];
            } else {
                $img = $slideone['img'];
            }

            $sql = D("yongjin")->add(array('title' => $biaoti, 'link' => $link, 'img' => $img, 'color' => $biaoyi2));
            //	$this->db->Query("insert into `@#_yongjin`(`title`,`link`,`img`,`color`) values('$biaoti','$link','$img','$biaoti2') ");	
            if ($sql) {
                $this->note("添加成功", C("URL_DOMAIN") . "invite/init");
            } else {
                $this->note("添加失败");
            }
        }

        $this->assign("ment", $ment);

        $this->display("admin/invite.add");
    }

    public function delete() {
		$user = $this->getAdminInfo(FALSE);
        if ($user['xianzhi']) {
            
                echo "<script>
				alert('权限不足,请联系总管理员');
				exit;			
				</script>";

exit;
            }
        $id = I('id');
        $sql = D("yongjin")->where(array('id' => $id))->delete();
        //$this->db->Query("DELETE FROM `@#_yongjin` WHERE (`id`='$id')");
        if ($sql) {

            $this->note("删除成功", C("URL_DOMAIN") . "invite/init");
        } else {
            $this->note("删除失败");
        }
    }

}

?>