<?php

/**
 * 手机端页面
 * addtime 2016/02/18
 */

namespace Duipi\Controller;

use Think\Controller;

class MobileController extends BaseController {

    public function _initialize() {
        if (!ismobile()) {
            $url = $_SERVER["REQUEST_SCHEME"] . '://' . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
            $url = str_ireplace("/mobile/", "/index/", $url);
            header("location: " . $url);
        }
    }

    public function show() {
        $wenzhangid = I("d");
        if ($wenzhangid) {
            $wenzhang = D("wenzhang")->where(array('id' => $wenzhangid))->find();
            if ($wenzhang) {
                $cateinfo = D("wenzhang")->where(array('cateid' => $wenzhang[cateid]))->find();
            } else {
                $cateinfo = array("info" => null);
            }
            $info = unserialize($cateinfo['info']);
            $biaoti = $wenzhang['title'] . "_" . C("web_name");
            $guanjianzi = $wenzhang['keywords'];
            $miaoshu = $wenzhang['description'];
            $info['template_show'] = "article_show.help.html";
            $moban = explode('.', $info['template_show']);
            $v = $moban[0] . "." . $moban[1];
            $this->assign('wenzhang', $wenzhang);
        } else {

            $ss = D("wenzhang")->where(array('name' => '网站公告'))->find();
            $wenzhang = D("wenzhang")->where(array('cateid' => $ss[cateid]))->select();
            //var_dump($wenzhang);
            if ($wenzhang) {

                $cateinfo = D("wenzhang")->where(array('cateid' => $wenzhang[cateid]))->find();
            } else {
                $cateinfo = array("info" => null);
            }
            $info = unserialize($cateinfo['info']);
            $biaoti = $wenzhang['title'] . "_" . C("web_name");
            $guanjianzi = $wenzhang['keywords'];
            $miaoshu = $wenzhang['description'];
            $info['template_show'] = "article_show1.help.html";
            $moban = explode('.', $info['template_show']);
            $v = $moban[0] . "." . $moban[1];
            $this->assign('wenzhang', $wenzhang);
        }
        $this->display("$v");
    }

    public function index() {
        if (C('zhideng') && !$this->userinfo && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            header("location: " . C("URL_DOMAIN") . "user/wxlogin/");
            exit;
        }
        if ($_GET['yaoqing2']) {
            session("yaoqing2", $_GET['yaoqing2']);
        }
        if ($_GET['yaoqing3']) {
            session("yaoqing3", $_GET['yaoqing3']);
        }
        //佣金部分结束
        //最新揭晓
        $yysqishu = D("shangpin")->where("q_end_time is not null and q_showtime ='N'")->field("id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,fahuo")->order("q_end_time DESC")->LIMIT(4)->select();
        $this->assign("yysqishu", $yysqishu);
        $this->assign('keys', "首页");
        $this->assign('category', $this->getCategory());
        $this->display("index.index");
    }

    /**
     * 首页专区
     */
    public function glist() {
        $this->assign('category', $this->getCategory());
        $this->display("index.glist");
    }

    /**
     * 最新揭晓
     */
    public function lottery() {
        $this->assign('keys', "最新揭晓");
        $this->assign('category', $this->getCategory());
        $this->display("index.lottery");
    }

    /**
     * 获取分类
     */
    public function getCategory() {
        $db_category = D("fenlei");
        $category = $db_category->where(array("model" => '1', 'parentid' => '0'))->select();
        return $category;
    }

    //访问个人主页
    public function userindex() {
        $uid = I("id", 0);
        //获取个人资料
        $huiyuan = D("yonghu")->where(array("uid" => "$uid"))->find();
        //获取一元云购等级  一元云购新手  一元云购小将==
        $huiyuandj = D("yonghu_group")->select();
        $jingyan = $huiyuan['jingyan'];
        if (!empty($huiyuandj)) {
            foreach ($huiyuandj as $key => $val) {
                if ($jingyan >= $val['jingyan_start'] && $jingyan <= $val['jingyan_end']) {
                    $huiyuan['yungoudj'] = $val['name'];
                }
            }
        }
        $this->assign("huiyuan", $huiyuan);
        $this->display("mobile/index.userindex");
    }

    //新手指南
    public function about() {
        $db_category = D("fenlei");
        $fenlei = $db_category->where(array("name" => '新手指南', 'parentid' => '1'))->find();
        $db_wenzhang = D("wenzhang");
        $wenzhang = $db_wenzhang->where(array("cateid" => "{$fenlei['cateid']}"))->select();
        $this->assign("fenlei", $fenlei);
        $this->assign("wenzhang", $wenzhang);
        $this->display("index.about");
    }

//晒单评论 单个的find 
    public function goodspost() {
        $key = "晒单评论";
        $xiangmuid = I("id", 0);
        $yyslist = D("shangpin")->where(array("sid" => $xiangmuid))->select();
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
                $shaidingdan_hueifu = D("shai_hueifu")->where(array("sdhf_id" => $sd[sd_id]))->select();
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
        $this->display("mobile/index.goodspost");
    }

//图文详细
    public function goodsdesc() {
        $key = "图文详情";
        $xiangmuid = I("id", 0);
        $desc = D("shangpin")->where(array("id" => $xiangmuid))->find();
        if (!$desc) {
            $this->notemobile('页面错误!');
        }
        $desc['content'] = htmlspecialchars_decode($desc['content']);
        $this->assign("key", $key);
        $this->assign("desc", $desc);

        $this->display("mobile/index.goodsdesc");
    }

    //关注
    public function guanzhu() {
        $this->display("index.guanzhu");
    }

    //用户服务协议
    public function terms() {
        $fenlei = D("fenlei")->where(array("name" => '新手指南', 'parentid' => '1'))->find();
        $wenzhang = D("wenzhang")->where(array("cateid" => "{$fenlei['cateid']}", "title" => "服务协议"))->find();
        $this->assign("wenzhang", $wenzhang);
        $this->display("mobile/system.terms");
    }

    //晒单分享
    public function shaidan() {
        $this->assign("keys", "晒单");
        $this->display("mobile/index.shaidan");
    }

    /**
     * 晒单ajax
     */
    public function shaidanajax() {
        $parm = I("parm", "");
        $p = I("page", 1);
        //分页		
        $end = 10;
        $star = ($p - 1) * $end;
        if ($parm == 'new') {
            $sel = 'sd_time';
        } else if ($parm == 'renqi') {
            $sel = 'sd_zhan';
        } else if ($parm == 'pinglun') {
            $sel = 'sd_ping';
        }
        $db_shai = D("shai");
        $count = $db_shai->where(array("shenhe" => 1))->order("$sel DESC")->select();
        $shaidingdan = $db_shai->where(array("shenhe" => 1))->order("$sel DESC")->limit($star, $end)->select();
        foreach ($shaidingdan as $sd) {
            $weer[] = $this->huode_user_name($sd['sd_userid']);
            $time[] = date("Y-m-d H:i", $sd['sd_time']);
            $huiyuan = D("yonghu")->where(array("uid" => "{$sd['sd_userid']}"))->find();
            $pic[] = $huiyuan['img'];
        }
        for ($i = 0; $i < count($shaidingdan); $i++) {
            $shaidingdan[$i]['user'] = $weer[$i];
            $shaidingdan[$i]['time'] = $time[$i];
            $shaidingdan[$i]['pic'] = $pic[$i];
        }
        $fenyex = ceil(count($count) / $end);
        if ($p <= $fenyex) {
            $shaidingdan[0]['page'] = $p + 1;
        }
        if ($fenyex > 0) {
            $shaidingdan[0]['sum'] = $fenyex;
        } else if ($fenyex == 0) {
            $shaidingdan[0]['sum'] = $fenyex;
        }
        echo json_encode($shaidingdan);
    }

    public function shaidan2() {
        $fidx = (I("fIdx")) - 1;
        $eidx = (I("eIdx"));
        $id = (I("goodsid"));
        $yyslist = D("shangpin")->where(array("sid" => $id))->select();
        $shop = '';
        foreach ($yyslist as $list) {
            $shop.=$list['id'] . ',';
        }
        $id = trim($shop, ',');
        if ($id) {
            $shaidingdan = D("shai")->where("sd_shopid IN ($id)")->order("sd_id DESC")->limit("$fidx,$eidx")->select();
            $count = D("shai")->where("sd_shopid IN ($id)")->order("sd_id DESC")->select();
            foreach ($shaidingdan as $sd) {
                $shaidingdan_hueifu = D("shai_hueifu")->where(array("sdhf_id" => $sd["sd_id"]))->select();
                $sum = $sum + count($shaidingdan_hueifu);
            }
            if (!count($shaidingdan)) {
                $yyslist['code'] = 1;
            } else {
                $yyslist['code'] = 0;
                $yyslist['CountEx'] = $sum;
                $yyslist['Count'] = count($shaidingdan);
                foreach ($shaidingdan as $key => $val) {
                    //var_dump($val['sd_thumbs']);
                    $yyslist['Data'][$key]['username'] = $this->huode_user_name($val['sd_userid']);
                    $yyslist['Data'][$key]['userphoto'] = $val['sd_thumbs'];
                    $yyslist['Data'][$key]['userweb'] = $val['sd_userid'];
                    $yyslist['Data'][$key]['codeperiod'] = $val['sd_qishu'];
                    $yyslist['Data'][$key]['postid'] = $val['sd_id'];
                    $yyslist['Data'][$key]['posttitle'] = $val['sd_title'];
                    $yyslist['Data'][$key]['postContent'] = $val['sd_content'];
                    $yyslist['Data'][$key]['postAllPic'] = $val['sd_thumbs'];
                    $yyslist['Data'][$key]['postHits'] = $val['shopid'];
                    $yyslist['Data'][$key]['postReplyCount'] = $val['sd_ping'];
                    $yyslist['Data'][$key]['grade'] = $val['shopid'];
                    $yyslist['Data'][$key]['gradeName'] = $val['shopid'];
                    $yyslist['Data'][$key]['postTimeEx'] = date('Y.m.d H:i:s', $val['sd_time']);
                }
            }
        }
        echo json_encode($yyslist);
    }

    public function detail() {
        $key = "晒单分享";
        $huiyuan = $this->getUserInfo();
        $sd_id = I("id", 0);
        $shaidingdan = D("shai")->where(array("sd_id" => $sd_id))->find();
        $shangpinss = D("shangpin")->where(array("sid" => $shaidingdan['sd_shopid']))->order("qishu DESC")->find();
        $shaidingdannew = D("shai")->order("sd_id DESC")->limit("5")->select();
        $shaidingdan_hueifu = D("shai_hueifu")->where(array("sdhf_id" => $sd_id))->select();

        if (!$shaidingdan) {
            echo "页面错误";
        }
        $substr = substr($shaidingdan['sd_photolist'], 0, -1);
        $sd_photolist = explode(";", $substr);
        $jikxiao = $this->huode_shop_if_jiexiao($shaidingdan['sd_shopid']);
        $q_end_time = date("Y-m-d H:i:s", $jikxiao['q_end_time']);
        $pic_list = explode(";", $shaidingdan['sd_photolist']);
        array_pop($pic_list);
        $this->assign("keys", $key);
        $this->assign("pic_list", $pic_list);
        $this->assign("jikxiao", $jikxiao);
        $this->assign("q_end_time", $q_end_time);
        $this->assign("shaidingdan", $shaidingdan);
        $this->assign("sd_photolist", $sd_photolist);
        $this->assign("shaidingdan_hueifu", $shaidingdan_hueifu);
        $this->assign("shaidingdannew", $shaidingdannew);
        $this->assign("shangpinss", $shangpinss);
        $this->assign("huiyuan", $huiyuan);
        $this->display("mobile/index.detail");
    }

    public function plajax() {
        $huiyuan = $this->getUserInfo();
        if (!is_array($huiyuan)) {
            echo "页面错误";
            exit;
        }
        $sdhf_id = $_POST['sd_id'];
        $sdhf_userid = $huiyuan['uid'];
        $sdhf_content = $_POST['count'];
        $sdhf_time = time();
        if ($sdhf_content == null) {
            echo "页面错误";
            exit;
        }
        $shaidingdan = D("shai")->where(array("sd_id" => $sdhf_id))->find();
        $data = array(
            "sdhf_id" => $sdhf_id,
            "sdhf_userid" => $sdhf_userid,
            "sdhf_content" => $sdhf_content,
            "sdhf_time" => $sdhf_time
        );

        D("shai_hueifu")->add($data);
        $sd_ping = $shaidingdan['sd_ping'] + 1;
        $data = array(
            "sd_ping" => $sd_ping
        );
        D("shai")->where(array("sd_id" => $shaidingdan['sd_id']))->save($data);
        echo "1";
    }

    //羡慕嫉妒恨
    public function xianmu() {
        $sd_id = I("id", 0);
        D("shai")->where(array("sd_id" => $sd_id))->setInc('sd_zhan', 1);
        $shaidingdan = D("shai")->where(array("sd_id" => $sd_id))->find();
        echo $shaidingdan['sd_zhan'];
    }

    //访问他人购买记录
    public function getUserBuyList() {
        $type = intval(I("Type", 0));
        $uid = intval(I("UserID", 0));
        $fidx = intval(I("FIdx", 0));
        $eidx = 10; //$this->segment(7);
        $isCount = I("IsCount", 0);
        $db = new \Think\Model;
        if ($type == 0) {
            //参与一元云购的商品 全部...
            $filed = "*,sum(gonumber) as gonumber";
            $where = array("a.uid" => "{$uid}");
            $yyslist = $db->table("yys_yonghu_yys_record a")->join("yys_shangpin b on a.shopid=b.id")->where($where)->field($filed)->group("shopid")->select();
            $shop['listItems'] = $db->table("yys_yonghu_yys_record a")->join("yys_shangpin b on a.shopid=b.id")->where($where)->field($filed)->group("shopid")->order("a.time desc")->limit($fidx, $eidx)->select();
        } elseif ($type == 1) {
            //获得奖品	
            $yyslist = $db->table("yys_shangpin")->where(array("q_uid" => "$uid"))->select();
            if (C('ssc')) {
                $shop['listItems'] = $db->table("yys_shangpin")->where("q_uid=$uid and q_end_cp is not null and q_showtime='N'")->field("id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,fahuo")->order("q_end_time desc")->limit($fidx, $eidx)->select();
            } else {
                $shop['listItems'] = $db->table("yys_shangpin")->where(array("q_uid" => "$uid"))->field("id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,fahuo")->order("q_end_time desc")->limit($fidx, $eidx)->select();
            }
        } elseif ($type == 2) {
            //晒单记录
            $yyslist = $db->table("yys_shai a")->join("yys_shangpin b on a.sd_shopid=b.id")->where(array("a.sd_userid" => "$uid"))->select();
            $shop['listItems'] = $db->table("yys_shai a")->join("yys_shangpin b on a.sd_shopid=b.id")->where(array("a.sd_userid" => "$uid"))->order("a.sd_time desc")->limit($fidx, $eidx)->select();
        }
        if (empty($shop['listItems'])) {
            $shop['code'] = 4;
        } else {
            foreach ($shop['listItems'] as $key => $val) {
                if ($val['q_end_time'] != '') {
                    $shop['listItems'][$key]['codeState'] = 3;
                    $shop['listItems'][$key]['q_end_time'] = $this->microt($val['q_end_time']);
                    if (C('ssc')) {
                        $shop['listItems'][$key]['q_user_code'] = $val['q_user_code'];
                    }
                    if (time() < $val['q_end_time']) {
                        $shop['listItems'][$key]['q_user_code'] = "正在揭晓";
                        $shop['listItems'][$key]['q_user'] = "既将公布";
                    } else {
                        $shop['listItems'][$key]['q_user'] = $this->huode_user_name($val['q_uid']);
                        $shop['listItems'][$key]['q_end_time'] = $this->microt($val['q_end_time']);
                    }
                }
                if (isset($val['sd_time'])) {
                    $shop['listItems'][$key]['sd_time'] = date('m月d日 H:i', $val['sd_time']);
                }
            }
            $shop['code'] = 0;
            //$shop['count'] = count($yyslist);
        }
        echo json_encode($shop);
    }

    public function chongzhi() {
        $uid = $this->huode_user_uid();
        $this->assign("uid", $uid);
        $this->display("mobile/index.chongzhi");
    }

    /**
     * 推广二维码
     */
    public function erweimas() {
        $huiyuan = $this->getUserInfo();
        if (!$huiyuan && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            header("location: " . C("URL_DOMAIN") . "user/wxloginer/");
            exit;
        }
        $this->display("mobile/invite.erweima");
    }

    public function calResult() {
        $xiangmuid = I("id", 0);
        $xiangmu = D("shangpin")->where("id='$xiangmuid' and q_end_time is not null")->find();
        $xiangmuzx = D("shangpin")->where("sid='{$xiangmu['sid']}' and qishu>'{$xiangmu['qishu']}' and q_end_time is null")->order("qishu DESC")->find();
        $h = abs(date("H", $xiangmu['q_end_time']));
        $i = date("i", $xiangmu['q_end_time']);
        $s = date("s", $xiangmu['q_end_time']);
        $w = substr($xiangmu['q_end_time'], 11, 3);
        $weer_shop_time_add = $h . $i . $s . $w;
        $tt2 = $xiangmu['q_end_cp'];
        if (C('ssc')) {
            $weer_shop_fmod = calc(($tt2 + $xiangmu['q_counttime']), $xiangmu['canyurenshu'], 'mod');
        } else {
            $weer_shop_fmod = fmod($xiangmu['q_counttime'], $xiangmu['canyurenshu']);
        }
        if ($xiangmu['q_content']) {
            $xiangmu['q_content'] = unserialize($xiangmu['q_content']);
        }
        $xiangmu['picarr'] = unserialize($xiangmu['picarr']);
        $this->assign("xiangmu", $xiangmu);
        $this->assign("xiangmuzx", $xiangmuzx);
        $this->assign("weer_shop_fmod", $weer_shop_fmod);
        $this->display("mobile/index.calResult");
    }

    public function getCalResult() {
        $xiangmuid = I("codeid", 0);
        $xiangmu = D("shangpin")->where("id='$xiangmuid' and q_end_time is not null")->field("id,sid,cateid,brandid,q_content")->find();
        if ($xiangmu['q_content']) {
            $xiangmu['contcode'] = 0;
            $xiangmu['itemlist'] = unserialize($xiangmu['q_content']);
            foreach ($xiangmu['itemlist'] as $key => $val) {
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
        $yyslist['code'] = 0;
        foreach ($xiangmu['itemlist'] as $key => $val) {
            $yyslist['record0'][$key]['buytime'] = $this->microt($val['time']);
            $yyslist['record0'][$key]['buyName'] = $val[username];
            $yyslist['record0'][$key]['userweb'] = $val[uid];
            $yyslist['record1'][$key]['buytime'] = $this->microt($val['time']);
            $yyslist['record1'][$key]['buyName'] = $val[username];
            $yyslist['record1'][$key]['userweb'] = $val[uid];
            $h = date("H", $val['time']);
            $i = date("i", $val['time']);
            $s = date("s", $val['time']);
            list($timesss, $msss) = explode(".", $val['time']);
            $yyslist['record1'][$key]['timeCodeVal'] = $h . $i . $s . $msss;
            $yyslist['record2'][$key]['userweb'] = $val[uid];
            $yyslist['record2'][$key]['buyName'] = $val[username];
            $yyslist['record2'][$key]['buytime'] = $this->microt($val['time']);
        }
        echo json_encode($yyslist);
    }

    public function getshorturl() {
        $huiyuan = $this->getUserInfo();
        $lianjie = D("yongjin")->order('id DESC')->limit(1)->select();
        $friednsURL = "";
        $yys = array();
        foreach ($lianjie as $key => $val) {
            $yys['urls'][$key]['object_type'] = "";
            $yys['urls'][$key]['result'] = 'true';
            $yys['urls'][$key]['title'] = $val["title"];

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

    /**
     * 选购
     */
    public function choose() {
        $xiangmuid = I("id");
        $xiangmulist = D("shangpin")->where(array("id" => $xiangmuid))->select();
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
        $this->assign("huiyuan", $this->getUserInfo());
        $this->assign("bl", $bl);
        $this->assign("xiangmulist", $xiangmulist);
        $this->display("mobile/index.itemChoose");
    }

    //选购获取幸运号码
    public function getChoose() {
        $shop = D("shangpin")->where(array("id" => I("gid")))->find();
        $ggs = D("{$shop['codes_table']}")->where(array("s_id" => $shop['id']))->field("s_codes")->select();
        $code = array();
        foreach ($ggs as $key => $value) {
            $code = array_merge($code, unserialize($value['s_codes']));
        }
        sort($code);
        $list['status'] = true;
        $list['codes'] = $code;
        echo json_encode($list);
    }

    //**************************************团***************************************/
    //专区
    public function area() {
        $this->display("mobile/index.area");
    }

    //专区商品列表
    public function arealist() {
        $this->display("mobile/index.arealist");
    }

    //商品密码
    public function areacheck() {
        $xiangmuid = I("id");
        $xiangmu = D("shangpin")->where(array("id" => $xiangmuid))->find();
        $this->assign("xiangmu", $xiangmu);
        $this->display("mobile/index.areacheck");
    }

    //**************************************团***************************************/
}
