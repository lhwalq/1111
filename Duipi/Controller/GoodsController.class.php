<?php

/**
 * 商品
 * addtime 2016/02/18
 */

namespace Duipi\Controller;

use Think\Controller;

class GoodsController extends BaseController {

    public function _initialize() {
        $this->getAdminRight(CONTROLLER_NAME, ACTION_NAME);
        $this->userinfo = $this->getUserInfo();
        if ($this->userinfo) {
            $this->assign("huiyuan", $this->userinfo);
        }
        if (!ismobile()) {
            $daodao = D("fenlei")->where(array("model" => 1, "parentid" => 0))->order("`order` DESC")->LIMIT("15")->select();
            $guanggao = D("guanggao_shuju")->where(array("title" => "首页头", "parentid" => 0))->find();
            $lou = '-22';
            $louname = '0';
            $user_arr = $this->huode_user_arr();
            $user_name = $this->huode_user_name($user_arr, 'username');
            $pinpin = D("pinpai")->where(array("status" => "Y"))->order("`order` DESC")->select();
            $this->assign("huiyuan", $user_arr);
            $this->assign("user_name", $user_name);
            $this->assign("pinpin", $pinpin);
            $this->assign("louname", $louname);
            $this->assign("lou", $lou);
            $this->assign("guanggao", $guanggao);
            $this->assign("daodao", $daodao);
        }
    }

//选购
//商品列表
    public function glistxg() {
        if (ismobile()) {
            $biaoti = "商品列表_" . C("web_name");
            $key = "所有商品";
            $this->assign('category', R("Mobile/getCategory"));
            $this->assign("keys", $key);
            $this->assign("biaoti", $biaoti);
            $this->display("mobile/glistxg");
            exit;
        }
        $glist = I("glist", 0);
        $select = I("type", 0);
        $select = explode("_", $select);
        $select[] = '0';
        $select[] = '0';
        $cid = abs(intval($select[0]));
        $bid = abs(intval($select[1]));
        $order = abs(intval($select[2]));
        $where = '1=1';
        $orders = '';
        switch ($order) {
            case '1':
                $orders = 'shenyurenshu ASC';
                break;
            case '2':
                $where = "renqi = '1'";
                break;
            case '3':
                $orders = 'shenyurenshu ASC';
                break;
            case '4':
                $orders = 'time DESC';
                break;
            case '5':
                $orders = 'money DESC';
                break;
            case '6':
                $orders = 'money ASC';
                break;
            default:
                $orders = 'shenyurenshu ASC';
        }
        /* 设置了查询分类ID 和品牌ID */
        if (!$cid) {
            $pinpai = D("pinpai")->field("id,cateid,name")->order("`order` DESC")->select();
            $daohangs_title = '所有分类';
        } else {
            $pinpai = D("pinpai")->field("id,cateid,name")->where("cateid LIKE '%$cid%'")->order("`order` DESC")->select();
            $daohangs = D("fenlei")->field("cateid,name,parentid,info")->where(array("cateid" => $cid))->order("`order` DESC")->find();
            $daohangs['info'] = unserialize($daohangs['info']);
            $daohangs_title = empty($daohangs['info']['meta_title']) ? $daohangs['name'] : $daohangs['info']['meta_title'];
            $guanjianzi = $daohangs['info']['meta_keywords'];
            $miaoshu = $daohangs['info']['meta_description'];
        }
        $biaoti = $daohangs_title . "_商品列表_" . C("web_name");
        //分页
        $num = 20;
        //整合10圆区等
        if ($glist && ($glist == 10 || $glist == 100)) {
            $where.=" and yunjiage=" . $glist;
        } else if ($glist && $glist == 'xg') {
            $where.=" and fahuo=1";
        }
        /* 设置了查询分类ID 和品牌ID */
        if ($cid && $bid) {
            $sun_id_str = "'" . $cid . "'";
            $sun_cate = D("fenlei")->field("cateid")->where(array("parentid" => $daohangs['cateid']))->select();
            foreach ($sun_cate as $v) {
                $sun_id_str .= "," . "'" . $v['cateid'] . "'";
            }
            $zongji = D("shangpin")->field("cateid")->where("q_uid is null and brandid='$bid'  and cateid in ($sun_id_str) and `is_choose`='1' and $where")->count();
        } else {
            if ($bid) {
                $zongji = D("shangpin")->field("id")->where("q_uid is null and brandid='$bid' and `is_choose`='1' and $where")->count();
            } elseif ($cid) {
                $sun_id_str = "'" . $cid . "'";
                $sun_cate = D("fenlei")->where(array("parentid" => $daohangs['cateid']))->select();
                foreach ($sun_cate as $v) {
                    $sun_id_str .= "," . "'" . $v['cateid'] . "'";
                }
                $zongji = D("shangpin")->field("id")->where("q_uid is null and cateid in ($sun_id_str) and `is_choose`='1' and $where")->count();
            } else {
                $zongji = D("shangpin")->field("ect id")->where("q_uid is null and `is_choose`='1' and $where")->count();
            }
        }
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");

        if ($cid && $bid) {
            $yyslist = D("shangpin")->field("id,sid,cateid,brandid,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,q_end_qishu,fahuo")->where("q_uid is null and brandid='$bid' and cateid in ($sun_id_str) and `is_choose`='1' and $where")->order($orders)->limit(($fenyenum - 1) * $num, $num)->select();
        } else {
            if ($bid) {
                $yyslist = D("shangpin")->field("id,sid,cateid,brandid,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,q_end_qishu,fahuo")->where("q_uid is null and brandid='$bid' and `is_choose`='1' and $where")->order($orders)->limit(($fenyenum - 1) * $num, $num)->select();
            } elseif ($cid) {
                $yyslist = D("shangpin")->field("id,sid,cateid,brandid,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,q_end_qishu,fahuo")->where("q_uid is null and cateid in ($sun_id_str) and `is_choose`='1' and $where")->order($orders)->limit(($fenyenum - 1) * $num, $num)->select();
            } else {
                $yyslist = D("shangpin")->field("id,sid,cateid,brandid,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,q_end_qishu,fahuo")->where("q_uid is null and `is_choose`='1' and $where")->order($orders)->limit(($fenyenum - 1) * $num, $num)->select();
            }
        }
        $this->assign("daohangs", $daohangs);
        $this->assign("zongji", $zongji);
        $this->assign("fenye", $fenye);
        $this->assign("yyslist", $yyslist);
        $this->assign("cid", $cid);
        $this->assign("bid", $bid);
        $this->display("index/index.glistxg");
    }

    /**
     * 首页商品数据
     */
    public function getGoodsList_ajax() {
        $order = I("orderFlag", 0);
        $kaishi = I("fIdx", 0) - 1;
        $jieshu = I("eIdx", 0);
        if (!$order || $kaishi == -1 || !$jieshu) {
            $this->myReturn(1, "参数错误", 0);
        }
        $where = 'q_end_time is null';
        switch ($order) {
            case '50':
                $orders = "id DESC";
                break;
            case '20':
                $where.=" and renqi=1";
                $orders = "shenyurenshu DESC";
                break;
            case '31':
                $orders = "money DESC";
                break;
            case '30':
                $orders = "money ASC";
                break;
            default:
                $orders = "MOD(canyurenshu,zongrenshu) DESC";
        }
        $db_goods = M('shangpin');
        $goods = $db_goods->where($where)->field('0 codelimitbuy,0 goodstag,id goodsid,title goodssnme,thumb goodspic,qishu codeperiod,money codeprice,zongrenshu codequantity,canyurenshu codesales,yunjiage,fahuo,0 rowid,id codeid,0 codetype')->order($orders)->limit($kaishi, $jieshu)->select();
        if (!$goods) {
            $this->myReturn(1, "没有商品", 0);
        }

        $count = count($db_goods->select()) - 1;
        $this->myReturn(0, "成功", $count, $goods);
    }

//商品详细
    public function itemsForPc() {
        $ddaa = "product";
        $xiangmushaiid = I("goodsId", 0);
        $xiangmushai = D("shangpin")->field("id,sid,cateid,brandid,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,q_end_qishu,fahuo,content,is_choose")->where(array("id" => $xiangmushaiid))->find();
        if (!$xiangmushai) {
            $error = 1;
        } else {
            $error = 0;
            $fenye = new \Claduipi\Tools\page;
            $zongji = D("shai")->where(array("sd_shopsid" => $xiangmushai['sid']))->field("id")->count();
            if (!$zongji) {
                $error = 1;
            }
            if (isset($_GET['p'])) {
                $fenyenum = $_GET['p'];
            } else {
                $fenyenum = 1;
            }
            $num = 10;
            $fenye->config($zongji, $num, $fenyenum, "0");

            $shaidingdan = D("shai")->where(array("sd_shopsid" => "{$xiangmushai['sid']}"))->order("sd_id DESC")->limit(($fenyenum - 1) * $num, $num)->select();
            foreach ($shaidingdan as $key => $val) {
                $huiyuan_info = D("yonghu")->where(array("uid" => $val['sd_userid']))->find();
                $huiyuan_img[$val['sd_id']] = $huiyuan_info['img'];
                $huiyuan_id[$val['sd_id']] = $huiyuan_info['uid'];
                $huiyuan_username[$val['sd_id']] = $huiyuan_info['username'];
            }
        }
        $xiangmuid = $xiangmushaiid;
        $xiangmu = $xiangmushai;


        $weer_shop_codes_arr = D("yonghu_yys_record")->where(array("uid" => $xiangmu['q_uid'], "shopid" => $xiangmuid, "shopqishu" => $xiangmu['qishu']))->select();
        $weer_shop_codes = '';
        foreach ($weer_shop_codes_arr as $v) {
            $weer_shop_codes .= $v['goucode'] . ',';
        }

        $weer_shop_codes = rtrim($weer_shop_codes, ',');

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
        //var_dump($xiangmu['q_counttime']);
        if ($xiangmu['q_content']) {
            $xiangmu_q_content = unserialize($xiangmu['q_content']);
            $keysvalue = $new_array = array();

            foreach ($xiangmu_q_content as $k => $v) {
                $keysvalue[$k] = $v['time'];
                $h = date("H", $v['time']);
                $i = date("i", $v['time']);
                $s = date("s", $v['time']);
                list($timesss, $msss) = explode(".", $v['time']);
                $xiangmu_q_content[$k]['timeadd'] = $h . $i . $s . $msss;
            }
            arsort($keysvalue); //asort($keysvalue);正序
            reset($keysvalue);
            foreach ($keysvalue as $k => $v) {
                $new_array[$k] = $xiangmu_q_content[$k];
            }
            $xiangmu['q_content'] = $new_array;
        }



        if (!$xiangmu) {
            $this->note("没有这个商品！", C("URL_DOMAIN") . "index/index", 3);
        }
        $q_showtime = (isset($xiangmu['q_showtime']) && $xiangmu['q_showtime'] == 'N') ? 'N' : 'Y';
        if (C('ssc')) {
            if ($xiangmu['q_end_cp'] && $xiangmu['q_user_code'] && $xiangmu['q_uid'] && $xiangmu['q_user']) {
                header("location: " . C("URL_DOMAIN") . "goods/dataserverForPC/goodsId/" . $xiangmu['id']);
                exit;
            }
        } else {
            if ($xiangmu['q_end_time'] && $q_showtime == 'N') {
                header("location: " . C("URL_DOMAIN") . "goods/dataserverForPC/goodsId/" . $xiangmu['id']);
                exit;
            }
        }



        $sid = $xiangmu['sid'];
        $sid_code = D("shangpin")->where(array("sid" => $sid))->order("`id` DESC")->LIMIT(1, 1)->select();
        $sid_code = $sid_code[0];

        if ($xiangmu['id'] == $sid_code['id']) {
            $sid_code = null;
        }

        $sid_go_record = D("yonghu_yys_record")->where(array("shopid" => $sid_code['id'], "uid" => $sid_code['q_uid']))->order("id DESC")->find();
        $fenlei = D("fenlei")->where(array("cateid" => $xiangmu['cateid']))->find();
        $pinpai = D("pinpai")->where(array("id" => $xiangmu['brandid']))->find();

        $biaoti = $xiangmu['title'] . ' (' . $xiangmu['title2'] . ')';

        $guanjianzi = $xiangmu['keywords'];
        $miaoshu = $xiangmu['description'];

        $nomal = $xiangmu['zongrenshu'] - $xiangmu['canyurenshu'];
        $xiangmu['picarr'] = unserialize($xiangmu['picarr']);

        $we = D("yonghu_yys_record")->where(array("shopid" => $xiangmuid, "shopqishu" => $xiangmu['qishu']))->order("id DESC")->LIMIT("6")->select();
        $we2 = D("yonghu_yys_record")->where(array("shopid" => $xiangmuid, "shopqishu" => $xiangmu['qishu']))->order("id DESC")->LIMIT("50")->select();

        $yyslistrenqib = D("shangpin")->field("id,sid,cateid,brandid,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,q_end_qishu,fahuo")->where("q_uid is null")->order("q_counttime DESC")->LIMIT("15")->select();
        $yyslistrenqibb = D("shangpin")->field("id,sid,cateid,brandid,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,q_end_qishu,fahuo")->where("renqi='1' and q_uid is null")->order("q_counttime DESC")->LIMIT("15")->select();

        //期数显示
        $num = 10;

        $zongji = D("shangpin")->where(array("sid" => $xiangmu['sid']))->count();
        $xx = $zongji / $num;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");
        $pp = ($fenyenum - 1) * 10;

        $xiangmulist = D("shangpin")->where(array("sid" => $xiangmu['sid']))->field("id,qishu,q_uid")->order("qishu DESC")->LIMIT("$pp,10")->select();
        $xiangmulistse = D("shangpin")->where(array("sid" => $xiangmu['sid']))->order("qishu DESC")->select();

        if ($fenyenum > $fenye->page) {
            $fenyenum = $fenye->page;
        }
        $wangqiqishu = '<ul class="Period_list">';


        if (!$xiangmulist[0]['q_uid']) {
            if ($xiangmulist[0]['id'] == $xiangmu['id'])
                $wangqiqishu.='<li><a class="w_the" href="' . C("URL_DOMAIN") . 'goods/goodsId/' . $xiangmulist[0]['id'] . '"><b class="period_Ongoing period_ArrowCur" style="padding-left:0px;">' . "第" . $xiangmulist[0]['qishu'] . "期<i></i></b></a></li>";
            else
                $wangqiqishu.='<li><a href="' . C("URL_DOMAIN") . 'goods/goodsId/' . $xiangmulist[0]['id'] . '"><b class="period_Ongoing">' . "第" . $xiangmulist[0]['qishu'] . "期<i></i></b></a></li>";
        }else {
            if ($xiangmulist[0]['id'] == $xiangmu['id'])
                $wangqiqishu.='<li><a href="' . C("URL_DOMAIN") . 'goods/goodsId/' . $xiangmulist[0]['id'] . '"><b class="period_ArrowCur">' . "第" . $xiangmulist[0]['qishu'] . "期<i></i></b></a></li>";
            else
                $wangqiqishu.='<li><a href="' . C("URL_DOMAIN") . 'goods/dataserverForPc/goodsId/' . $xiangmulist[0]['id'] . '" class="gray02">第' . $xiangmulist[0]['qishu'] . '期</a></li>';
        }
        unset($xiangmulist[0]);
        foreach ($xiangmulist as $key => $qitem) {
            if ($key % 9 == 0) {
                $wangqiqishu.='</ul><ul class="Period_list">';
            }
            if ($qitem['id'] == $xiangmu['id'])
                $wangqiqishu.='<li><b class="period_ArrowCur">第' . $qitem['qishu'] . '期</b></li>';
            else
                $wangqiqishu.='<li><a href="' . C("URL_DOMAIN") . 'goods/dataserverForPc/goodsId/' . $qitem['id'] . '" class="gray02">第' . $qitem['qishu'] . '期</a></li>';
        }
        $wangqiqishu.='</ul>';

        $xiangmu['content'] = htmlspecialchars_decode($xiangmu['content']);
        $this->assign("error", $error);
        $this->assign("q_showtime", $q_showtime);
        $this->assign("xiangmulistse", $xiangmulistse);
        $this->assign("nomal", $nomal);
        $this->assign("xiangmu", $xiangmu);
        $this->assign("q_content", $xiangmu['q_content']);
        $this->assign("xiangmuid", $xiangmuid);
        $this->assign("guanjianzi", $guanjianzi);
        $this->assign("miaoshu", $miaoshu);
        $this->assign("biaoti", $biaoti);
        $this->assign("wangqiqishu", $wangqiqishu);
        $this->assign("num", $num);
        $this->assign("zongji", $zongji);
        $this->assign("fenye", $fenye);
        $this->assign("pinpai", $pinpai);
        $this->assign("fenlei", $fenlei);
        $this->assign("xiangmu", $xiangmu);
        $this->assign("sid_code", $sid_code);
        $this->assign("sid_go_record", $sid_go_record);
        $this->assign("xx", $xx);
        $this->display("index/index.item");
        exit;
    }

    /**
     * 商品详情 
     */
    public function items() {
        if (!ismobile()) {
            $this->itemsForPc();
        }
        if (C('zhideng') && !$this->userinfo && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            header("location: " . C("URL_DOMAIN") . "user/wxlogin/");
            exit;
        }
        $xiangmuid = I("goodsId", 0);
        if (!$xiangmuid) {
            $this->notemobile("找不到商品");
        }
        $model = new \Think\Model();
        $deng = $this->userinfo;
        session_start();
        if ($_GET['yaoqing']) {
            session('uu', $_GET['yaoqing']);
        }
        //	echo session('uu');

        if ($_GET['yaoqing2']) {
            session('yaoqing2', $_GET['yaoqing2']);
        }
        if ($_GET['yaoqing3']) {
            session('yaoqing3', $_GET['yaoqing3']);
        }
        //佣金部分结束
        $key = "商品详情";
        $xiangmu = $model->table("yys_shangpin")->where(array("id" => $xiangmuid))->field('id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,fahuo,is_choose,arid')->find();
        //组团密码判断
        if ($xiangmu['arid'] != 0) {
            $checks = cookie("room" . $xiangmu['id']);
            if (!$checks) {
                $this->notemobile("请输入房间密码", C("URL_DOMAIN") . "mobile/areacheck/id/" . $xiangmu['id']);
            } else {
                // _setcookie("room" . $xiangmu['id'], NULL);是否立即过期  默认1800秒
            }
        }

//限购
        if (!empty($xiangmu['id']) && $this->userinfo) {
            $huiyuan = $this->userinfo;
            $counts1 = $model->table("yys_yonghu_yys_record")->field("gonumber")->where(array("uid" => $huiyuan['uid'], "shopid" => $xiangmu['id']))->select();
            for ($xs = 0; $xs < C("xiangou"); $xs++) {
                $sums +=$counts1[$xs]['gonumber'];
            }
            $counts = intval($sums);
        }
        //限购结束
        if (!$xiangmu) {
            echo "商品不存在！";
            exit(); //////////////////////////////报错
        }
        $q_showtime = (isset($xiangmu['q_showtime']) && $xiangmu['q_showtime'] == 'N') ? 'N' : 'Y';
        if ($xiangmu['q_end_time'] && $q_showtime == 'N') {
            header("location: " . C("URL_DOMAIN") . "goods/dataserver/goodsId/" . $xiangmu['id']);
            exit;
        }
        $sid = $xiangmu['sid'];
        $sid_code = $model->table("yys_shangpin")->field("id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,fahuo")->where(array("sid" => $sid, "shopid" => $xiangmu['id']))->order('id DESC')->find();
        $sid_go_record = $model->table("yys_yonghu_yys_record")->where(array("shopid" => $sid_code['sid'], "uid" => $sid_code['q_uid']))->order('id DESC')->find();
        $fenlei = $model->table("yys_fenlei")->where("cateid ='" . $xiangmu['cateid'] . "'")->find();
        $pinpai = $model->table("yys_pinpai")->where("id = '" . $xiangmu['brandid'] . "'")->find();
        $biaoti = $xiangmu['title'];
        $nomal = $xiangmu['zongrenshu'] - $xiangmu['canyurenshu'];
        $xiangmu['picarr'] = unserialize($xiangmu['picarr']);
        $we = $model->table("yys_yonghu_yys_record")->where(array("shopqishu" => $xiangmu['qishu'], "shopid" => $xiangmuid))->order("id DESC")->limit(6)->select();
        $xiangmulist = $model->table("yys_shangpin")->field("id,sid,cateid,brandid,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,q_end_qishu,fahuo")->where("sid='" . $xiangmu['sid'] . "' and q_end_time is not null")->order("qishu DESC")->select();
        //期数显示
        $xiangmuzx = $model->table("yys_shangpin")->field("id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,fahuo")->where("sid='" . $xiangmu['sid'] . "' and qishu>'" . $xiangmu['qishu'] . "' and q_end_time is null")->order("qishu DESC")->find();
        $wangqiqishu = '';
        if (empty($xiangmu['q_end_time']) && empty($xiangmu['q_uid'])) {
            $wangqiqishu.='<li class="cur"><a href="' . C("URL_DOMAIN") . '/goods/items/goodsId/' . $xiangmu['id'] . '">' . "第" . $xiangmu['qishu'] . "期</a><b></b></li>";
        } else {
            $wangqiqishu.='<li class="cur"><a href="' . C("URL_DOMAIN") . '/goods/items/goodsId/' . $xiangmuzx['id'] . '">' . "第" . $xiangmuzx['qishu'] . "期</a><b></b></li>";
        }
        if (empty($xiangmulist)) {
            foreach ($xiangmulist as $qitem) {
                $wangqiqishu.='<li><a href="' . C("URL_DOMAIN") . '/goods/items/goodsId/' . $qitem['id'] . '" class="">第' . $qitem['qishu'] . '期</a></li>';
            }
        }
        foreach ($xiangmulist as $qitem) {
            if ($qitem['id'] == $xiangmuid) {

                $wangqiqishu.='<li class="cur"><a href="javascript:;">' . "第" . $xiangmulist[0]['qishu'] . "期</a><b></b></li>";
            } else {
                $wangqiqishu.='<li><a href="' . C("URL_DOMAIN") . '/goods/dataserver/goodsId/' . $qitem['id'] . '" >第' . $qitem['qishu'] . '期</a></li>';
            }
        }
        $gorecode = array();
        if (!empty($xiangmulist)) {
            //查询上期的获奖者信息
            $gorecode = $model->table("yys_yonghu_yys_record")->where(array("shopid" => $xiangmulist[0]['id'], "shopqishu" => $xiangmulist[0]['qishu']))->order("id DESC")->find();
        }
        $curtime = time();
        $shopitem = 'itemfun';
        //晒单数
        $shopid = $model->table("yys_shangpin")->field("id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,fahuo")->where(array("id" => $xiangmuid))->find();
        $yyslist = $model->table("yys_shangpin")->field("id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,fahuo")->where(array("sid" => $shopid['sid']))->select();
        $shop = '';
        foreach ($yyslist as $list) {
            $shop.=$list['id'] . ',';
        }
        $id = trim($shop, ',');
        if ($id) {
            $shaidingdan = $model->table("yys_shai")->where("sd_shopid IN (" . $id . ")")->select();
            $sum = 0;
            foreach ($shaidingdan as $sd) {
                $shaidingdan_hueifu = $model->table("yys_shai_hueifu")->where(array("sdhf_id" => $sd['sd_id']))->select();
                $sum = $sum + count($shaidingdan_hueifu);
            }
        } else {
            $shaidingdan = 0;
            $sum = 0;
        }
        $appid = C("appid");
        $secret = C("secret");
        $jssdk = new \Claduipi\Wechat\JSSDK($appid, $secret);
        $signPackage = $jssdk->GetSignPackage();
        $lianjie = $model->table("yys_yongjin")->order("id desc")->limit(1)->select();
        $yys = array();
        foreach ($lianjie as $key => $val) {
            $yys['urls'][$key]['object_type'] = "";
            $yys['urls'][$key]['result'] = 'true';
            $yys['urls'][$key]['title'] = $val['title'];
            $yys['urls'][$key]['img'] = '/' . $val['img'];
            if ($huiyuan['yaoqing'] && empty($huiyuan['yaoqing2']) && empty($huiyuan['yaoqing3'])) {
                $yys['urls'][$key]['url_short'] = C("URL_DOMAIN") . '/goods/items/goodsId/' . $xiangmu['id'] . '?yaoqing2=' . $huiyuan['yaoqing'] . '&yaoqing=' . $huiyuan['uid'];
            } else if ($huiyuan['yaoqing'] && $huiyuan['yaoqing2'] && empty($huiyuan['yaoqing3'])) {
                $yys['urls'][$key]['url_short'] = C("URL_DOMAIN") . '/goods/items/goodsId/' . $xiangmu['id'] . '?yaoqing=' . $huiyuan['uid'] . '&yaoqing2=' . $huiyuan['yaoqing'] . '&yaoqing3=' . $huiyuan['yaoqing2'];
            } else if ($huiyuan['yaoqing'] && $huiyuan['yaoqing2'] && $huiyuan['yaoqing3']) {
                $yys['urls'][$key]['url_short'] = C("URL_DOMAIN") . '/goods/items/goodsId/' . $xiangmu['id'] . '?yaoqing=' . $huiyuan['uid'] . '&yaoqing2=' . $huiyuan['yaoqing'] . '&yaoqing3=' . $huiyuan['yaoqing2'];
            } else {
                $yys['urls'][$key]['url_short'] = C("URL_DOMAIN") . '/goods/items/goodsId/' . $xiangmu['id'] . '?yaoqing=' . $huiyuan['uid'];
            }
            $yys['urls'][$key]['object_id'] = "";
            $yys['urls'][$key]['url_long'] = C("URL_DOMAIN") . '/goods/items/goodsId/' . $xiangmu['id'];
            $yys['urls'][$key]['type'] = "0";
        }
        //团
        $fangjianhao = '<a href="' .  C("URL_DOMAIN") . 'goods/items/goodsId/' . $xiangmu['arid'] . '" style="color:#f60;background:#fff;">' . "房间号：" . $xiangmu['arid'] . "</a><b></b>";
        $xiangmu['content'] = htmlspecialchars_decode($xiangmu['content']);
        $this->assign('xiangmuzx', $xiangmuzx);
        $this->assign('xiangmu', $xiangmu);
        $this->assign('fangjianhao', $fangjianhao);
        $this->assign("signPackage", $signPackage);
        $this->assign("yys", $yys);
        $this->assign("wangqiqishu", $wangqiqishu);
        $this->display("mobile/item");
        exit();
    }

    /**
     * 购物车列表
     */
    public function cartlist() {
        $key = "购物车";
        $Mcartlist = $this->getShopCart();
        $shopids = '';
        if (is_array($Mcartlist)) {
            foreach ($Mcartlist as $key => $val) {
                $shopids.=intval($key) . ',';
            }
            $shopids = str_replace(',0', '', $shopids);
            $shopids = trim($shopids, ',');
        }

        $yyslist = array();
        if ($shopids != NULL) {
            $shoparrCopy = D("shangpin")->where("id in (" . $shopids . ")")->select();
            if (ismobile()) {
                $shoparr = $this->key2key($shoparrCopy, "id");
            } else {
                $yyslist = $this->key2key($shoparrCopy, "id");
            }
        }

        if (ismobile()) {
            if (!empty($shoparr)) {
                foreach ($shoparr as $key => $val) {
                    if ($val['q_end_time'] == '' || $val['q_end_time'] == NULL) {
                        $yyslist[$key] = $val;
                        $Mcartlist[$val['id']]['num'] = $Mcartlist[$val['id']]['num'];
                        $Mcartlist[$val['id']]['shenyu'] = $val['shenyurenshu'];
                        $Mcartlist[$val['id']]['money'] = $val['yunjiage'];
                    }
                }
                cookie('Cartlist', json_encode($Mcartlist));
            }
            $shop = 0;
            if (!empty($yyslist)) {
                $shop = 1;
            }
            $this->assign("shop", $shop);
        }
        $MoenyCount = 0;
        $gouwucheshopinfo = '{';
        if (count($yyslist) >= 1) {
            foreach ($Mcartlist as $key => $val) {
                $key = intval($key);
                if (isset($yyslist[$key])) {
                    $yyslist[$key]['cart_gorenci'] = $val['num'] ? $val['num'] : 1;
                    $MoenyCount+=$yyslist[$key]['yunjiage'] * $yyslist[$key]['cart_gorenci'];
                    $yyslist[$key]['cart_xiaoji'] = substr(sprintf("%.3f", $yyslist[$key]['yunjiage'] * $val['num']), 0, -1);
                    $yyslist[$key]['cart_shenyu'] = $yyslist[$key]['zongrenshu'] - $yyslist[$key]['canyurenshu'];
                    $gouwucheshopinfo.="'$key':{'shenyu':" . $yyslist[$key]['cart_shenyu'] . ",'num':" . $val['num'] . ",'money':" . $yyslist[$key]['yunjiage'] . "},";
                }
            }
        }

        $MoenyCount = substr(sprintf("%.3f", $MoenyCount), 0, -1);
        $gouwucheshopinfo.="'MoenyCount':$MoenyCount}";
        $num = count($yyslist);

        $this->assign("gouwucheshopinfo", $gouwucheshopinfo);
        $this->assign("keys", "购物车");
        $this->assign("Mcartlist", $Mcartlist);
        $this->assign("yyslist", $yyslist);
        $this->assign("num", $num);
        $this->assign("MoenyCount", $MoenyCount);
        $this->autoShow("cartlist");
    }

    //添加购物车
    public function addShopCart() {
        $ShopId = I("codeid", 0);
        $shopnum = I("shopNum", 0);
        $gouwuchebs = I("from", null);
        $shopis = 0;          //0表示不存在  1表示存在
        $Mcartlist = $this->getShopCart();
        $model = new \Think\Model;
        if ($ShopId == 0 || $shopnum <= 0) {
            $gouwuche['code'] = 1;   //表示添加失败
        } else {
            //限购
            if (!empty($ShopId) && $this->userinfo) {
                $huiyuan = $this->userinfo;
                $counts1 = $model->table("yys_yonghu_yys_record")->where(array("uid" => $huiyuan['uid'], "shopid" => $ShopId))->select();
                for ($xs = 0; $xs < C("xiangou"); $xs++) {
                    $sums +=$counts1[$xs]['gonumber'];
                }
                $counts = intval($sums);
            }
            $xgs = $model->table("yys_shangpin")->field("fahuo")->where(array('id' => $ShopId))->find();
            if (($shopnum + $counts > C("xiangou")) && $xgs['fahuo']) {
                $gouwuche['code'] = 4;
            } else {
                //限购结束	
                if (is_array($Mcartlist)) {
                    foreach ($Mcartlist as $key => $val) {
                        if ($key == $ShopId) {
                            if (isset($gouwuchebs) && $gouwuchebs == 'cart') {
                                $Mcartlist[$ShopId]['num'] = $shopnum;
                            } else {
                                $Mcartlist[$ShopId]['num'] = $val['num'] + $shopnum;
                            }
                            $shopis = 1;
                        } else {
                            $Mcartlist[$key]['num'] = $val['num'];
                        }
                    }
                } else {
                    $Mcartlist = array();
                    $Mcartlist[$ShopId]['num'] = $shopnum;
                }
                if ($shopis == 0) {
                    $Mcartlist[$ShopId]['num'] = $shopnum;
                }
                cookie('Cartlist', json_encode($Mcartlist));
                $gouwuche['code'] = 0;   //表示添加成功	
            }
        }
        $gouwuche['num'] = count($Mcartlist);    //表示现在购物车有多少条记录
        echo json_encode($gouwuche);
    }

    public function test($cc, $dd) {
        echo $cc . $dd;
    }

    /*
      计算云购码表
     */

    function content_get_codes_table() {
        $db = new \Think\Model;
        $num = $db->table("yys_linshi")->where(array("key" => 'shopcodes_table'))->find();
        $table = 'yys_shopcodes_' . $num['value'];
        $tables = $db->query("SHOW TABLE STATUS"); //获取所有TABLE
        $shopcodes_table = array();
        foreach ($tables as $value) {
            if ($value['Name'] == $table) {
                $shopcodes_table = $value;
            }
        }
        if (!$shopcodes_table || !$num) {
            return false;
        }
        if ($shopcodes_table['Auto_increment'] >= 99999) {
            $num = intval($num['value']) + 1;
            $shopcodes_table = 'yys_shopcodes_' . $num;
            $q1 = $db->query("		
				CREATE TABLE yys_$shopcodes_table (
				  id int(10) unsigned NOT NULL AUTO_INCREMENT,
				  s_id int(10) unsigned NOT NULL,
				  s_cid smallint(5) unsigned NOT NULL,
				  s_len smallint(5) DEFAULT NULL,
				  s_codes text,
				  s_codes_tmp text,
				  PRIMARY KEY (id),
				  KEY s_id (s_id),
				  KEY s_cid (s_cid),
				  KEY s_len (s_len)
				) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
		");
            $q2 = $db->query("UPDATE yys_linshi SET value = '$num' where key = 'shopcodes_table'");
            if (!$q1 || !$q2)
                return false;
        }else {
            $num = intval($num['value']);
            $shopcodes_table = 'shopcodes_' . $num;
        }
        return $shopcodes_table;
    }

    /*
      生成云购码
      CountNum @ 生成个数
      len 	    @ 生成长度
      sid	    @ 商品ID
     */

    public function content_huode_go_codes($CountNum = null, $changdu = null, $sid = null) {
        $db = new \Think\Model;
        $table = $db->table("yys_linshi")->where(array("key" => 'shopcodes_table'))->find();
        $table = 'yys_shopcodes_' . $table['value'];
        $num = ceil($CountNum / $changdu);
        $code_i = $CountNum;
        if ($num == 1) {
            $codes = array();
            for ($i = 1; $i <= $CountNum; $i++) {
                $codes[$i] = 10000000 + $i;
            }shuffle($codes);
            $codes = serialize($codes);
            $insert_data = array(
                "s_id" => "$sid",
                "s_cid" => '1',
                "s_len" => "$CountNum",
                "s_codes" => "$codes",
                "s_codes_tmp" => "$codes"
            );
            $query = $db->table($table)->add($insert_data);
            unset($codes);
            return $query;
        }
        $query_1 = true;
        for ($k = 1; $k < $num; $k++) {
            $codes = array();
            for ($i = 1; $i <= $changdu; $i++) {
                $codes[$i] = 10000000 + $code_i;
                $code_i--;
            }shuffle($codes);
            $codes = serialize($codes);
            $insert_data = array(
                "s_id" => "$sid",
                "s_cid" => "$k",
                "s_len" => "$changdu",
                "s_codes" => "$codes",
                "s_codes_tmp" => "$codes"
            );
            $query_1 = $db->table($table)->add($insert_data);
            unset($codes);
        }
        $CountNum = $CountNum - (($num - 1) * $changdu);
        $codes = array();
        for ($i = 1; $i <= $CountNum; $i++) {
            $codes[$i] = 10000000 + $code_i;
            $code_i--;
        }shuffle($codes);
        $codes = serialize($codes);
        $insert_data = array(
            "s_id" => "$sid",
            "s_cid" => "$num",
            "s_len" => "$CountNum",
            "s_codes" => "$codes",
            "s_codes_tmp" => "$codes"
        );
        $query_2 = $db->table($table)->add($insert_data);
        unset($codes);
        return $query_1 && $query_2;
    }

    /*
      添加推荐位
     */

    function content_add_position() {
        
    }

    /*
      新建一期商品
      info 	 商品的ID 或者 商品的数组
      使用此函数注意传进来的的商品期数不等于最大期数
      autocommit @是否开启事物
     */

    function content_add_shop_install($info = null, $autocommit = true) {
        $db = new \Think\Model;
        if ($autocommit) {
            $db->startTrans();
        }
        unset($info['id']);
        unset($info['q_uid']);
        unset($info['q_user']);
        unset($info['q_user_code']);
        unset($info['q_content']);
        unset($info['q_counttime']);
        unset($info['q_end_time']);

        $info['xsjx_time'] = 0;
        $info['time'] = time();
        $info['qishu'] = intval($info['qishu']);
        $info['qishu'] ++;
        $info['canyurenshu'] = '0';
        $info['shenyurenshu'] = $info['zongrenshu'];
        $info['codes_table'] = $this->content_get_codes_table();
        $info['q_showtime'] = 'N';
        $info['title'] = $this->htmtguolv($info['title']);
        $info['title2'] = $this->htmtguolv($info['title2']);
        //fix by dabin

        $baby = explode(";", $info['cardId1']);
        $babys = explode("-", $baby[0]);
        $l = strlen($babys[1]);

        $weishu = strpos($info['cardId1'], ";") + 1;
        $weishus = strpos($baby[0], "-") + 1;

        $str1 = substr($info['cardId1'], $weishu);
        $str2 = substr($baby[0], $weishus, $l);


        if (strstr($info['cardId1'], ";")) {
            $info['cardId1'] = $str1;
            $info['cardId'] = $babys[0];
            $info['cardPwd'] = $str2;
        } else {
            $info['cardId1'] = "";
            $info['cardId'] = "";
            $info['cardPwd'] = "";
        }

        $id = $db->table("yys_shangpin")->add($info);
        $q2 = $this->content_huode_go_codes($info['zongrenshu'], 3000, $id);
        if ($autocommit) {
            if ($id && $q2) {
                $db->commit();
                return true;
            } else {
                $db->rollback();
                return false;
            }
        } else {
            if ($id && $q2) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function test1() {
        $str1 = "222-bbb\r\n333-ccc\r\n444-ddd";
        $baby = explode("\n", $str1);
        var_dump($str1);
    }

    //往期商品查看
    public function dataserverForPc() {
        $ddaa = "dataserver";
        $xiangmuid = I("goodsId", 0);
        $xiangmushaiid = I("shai", 0);
        $xiangmushai = D("shangpin")->field("sid")->where(array("id" => $xiangmushaiid))->find();
        if (!$xiangmushai) {
            $error = 1;
        } else {
            $error = 0;
            $fenye = new \Claduipi\Tools\page;
            $zongji = D("shai")->where(array("sd_shopsid" => $xiangmushai['sid']))->field("id")->count();
            if (!$zongji) {
                $error = 1;
            }
            if (isset($_GET['p'])) {
                $fenyenum = $_GET['p'];
            } else {
                $fenyenum = 1;
            }
            $num = 10;
            $fenye->config($zongji, $num, $fenyenum, "0");
            $shaidingdan = D("shai")->where(array("sd_shopsid" => "{$xiangmushai['sid']}"))->order("sd_id DESC")->limit(($fenyenum - 1) * $num, $num)->select();
            $this->assign("shaidingdan", $shaidingdan);
            $this->assign("fenye", $fenye);
            $this->assign("zongji", $zongji);
            $this->assign("num", $num);
        }
        $xiangmu = D("shangpin")->field("id,sid,cateid,brandid,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,leixing,yuanjia,q_end_cp,q_end_qishu,fahuo")->where(array("id" => $xiangmuid))->find();
        if (!$xiangmu) {
            $this->note("没有这个商品!");
        }
        if (empty($xiangmu['q_end_time']) && $xiangmu['q_showtime'] == 'Y') {

            header("location: " . C("URL_DOMAIN") . "goods/items/goodsId/" . $xiangmu['id']);
            exit;
        }
        if (C('ssc')) {
            if (!$xiangmu['q_end_cp'] || !$xiangmu['q_user_code'] || !$xiangmu['q_uid'] || !$xiangmu['q_user']) {
                header("location: " . C("URL_DOMAIN") . "goods/items/goodsId/" . $xiangmu['id']);
                exit;
            }
        } else {
            if (empty($xiangmu['q_user_code'])) {
                header("location: " . C("URL_DOMAIN") . "goods/items/goodsId/" . $xiangmu['id']);
                exit;
            }
        }
        if (isset($xiangmu['q_showtime']) && $xiangmu['q_showtime'] == 'Y') {
            header("location: " . C("URL_DOMAIN") . "goods/items/goodsId/" . $xiangmu['id']);
            exit;
        }
        $fenlei = D("fenlei")->where("cateid ='" . $xiangmu['cateid'] . "'")->find();
        $pinpai = D("pinpai")->where("id = '" . $xiangmu['brandid'] . "'")->find();
        //云购中奖码
        $q_yonghu = unserialize($xiangmu['q_user']);
        $q_yonghu_code_len = strlen($xiangmu['q_user_code']);
        $q_yonghu_code_arr = array();
        for ($q_i = 0; $q_i < $q_yonghu_code_len; $q_i++) {
            $q_yonghu_code_arr[$q_i] = substr($xiangmu['q_user_code'], $q_i, 1);
        }
        //总云购次数
        $weer_shop_number = D("yonghu_yys_record")->where("uid= '{$xiangmu["q_uid"]}' and shopid = '$xiangmuid' and shopqishu = '{$xiangmu["qishu"]}'")->field("sum(gonumber) as gonumber")->find();
        $weer_shop_number = $weer_shop_number['gonumber'];
        //用户云购时间
        $weer_shop_time = D("yonghu_yys_record")->where("uid= '{$xiangmu["q_uid"]}' and shopid = '$xiangmuid' and shopqishu = '{$xiangmu["qishu"]}'  and huode = '{$xiangmu['q_user_code']}'")->field("time")->find();
        $weer_shop_time = $weer_shop_time['time'];
        //得到云购码
        $weer_shop_codes_arr = D("yonghu_yys_record")->where("uid= '{$xiangmu["q_uid"]}' and shopid = '$xiangmuid' and shopqishu = '{$xiangmu["qishu"]}'")->field("goucode")->select();
        $weer_shop_codes = '';
        foreach ($weer_shop_codes_arr as $v) {
            $weer_shop_codes .= $v['goucode'] . ',';
        }
        $weer_shop_codes = rtrim($weer_shop_codes, ',');
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
            $xiangmu_q_content = unserialize($xiangmu['q_content']);
            $keysvalue = $new_array = array();
            foreach ($xiangmu_q_content as $k => $v) {
                $keysvalue[$k] = $v['time'];
                $h = date("H", $v['time']);
                $i = date("i", $v['time']);
                $s = date("s", $v['time']);
                list($timesss, $msss) = explode(".", $v['time']);
                $xiangmu_q_content[$k]['timeadd'] = $h . $i . $s . $msss;
            }
            arsort($keysvalue); //asort($keysvalue);正序
            reset($keysvalue);
            foreach ($keysvalue as $k => $v) {
                $new_array[$k] = $xiangmu_q_content[$k];
            }
            $xiangmu['q_content'] = $new_array;
        }
        $biaoti = $xiangmu['title'] . ' (' . $xiangmu['title2'] . ')';
        $guanjianzi = $xiangmu['keywords'];
        $miaoshu = $xiangmu['description'];
        //新加补丁
        $yys_record_list = D("yonghu_yys_record")->where(" shopid = '$xiangmuid' and shopqishu = '{$xiangmu["qishu"]}'")->order("id DESC")->LIMIT("50")->select();
        $xiangmuzx = D("shangpin")->field("id,sid,cateid,brandid,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,q_end_qishu,fahuo")->where("sid='$xiangmu[sid]' and qishu>'$xiangmu[qishu]'")->order("qishu DESC")->find();
        //期数显示
        $xiangmulist = D("shangpin")->field("id,sid,cateid,brandid,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,q_end_qishu,fahuo")->where(array("sid" => $xiangmu['sid']))->order("qishu DESC")->select();
        $xiangmulists = $xiangmulist;
        $wangqiqishu = '';
        //dabin
        if (empty($xiangmulist[0]['q_uid'])) {
            $wangqiqishu.='<a class="color01" href="' . C("URL_DOMAIN") . 'goods/items/goodsId/' . $xiangmulist[0]['id'] . '">' . "第" . $xiangmulist[0]['qishu'] . "期进行中</a>";
            unset($xiangmulist[0]);
        } else {
            $wangqiqishu.='<a href="' . C("URL_DOMAIN") . 'goods/dataserverForPc/goodsId/' . $xiangmulist[0]['id'] . '">' . "第" . $xiangmulist[0]['qishu'] . "期</a>";
            unset($xiangmulist[0]);
        }
        if (empty($xiangmulist)) {
            $wangqiqishu.='';
        }
        foreach ($xiangmulist as $key => $qitem) {
            if ($key < 15) {
                if ($key % 9 == 0) {
                    $wangqiqishu.='</ul><ul>';
                }
                if ($qitem['id'] == $xiangmuid) {
                    $wangqiqishu.='<a class="w_nper_color">第' . $qitem['qishu'] . '期</a>';
                } else {
                    $wangqiqishu.='<a href="' . C("URL_DOMAIN") . 'goods/dataserverForPc/goodsId/' . $qitem['id'] . '" >第' . $qitem['qishu'] . '期</a>';
                }
            }
        }
        $wangqiqishull = '';
        //dabin
        if (empty($xiangmulist[0]['q_uid'])) {
            $wangqiqishull.='<dd><a  href="' . C("URL_DOMAIN") . 'goods/items/goodsId/' . $xiangmulists[0]['id'] . '">' . "第" . $xiangmulists[0]['qishu'] . "期进行中</a></dd>";
            unset($xiangmulist[0]);
        } else {
            $wangqiqishull.='<dd><a href="' . C("URL_DOMAIN") . 'goods/dataserverForPc/goodsId/' . $xiangmulist[0]['id'] . '">' . "第" . $xiangmulist[0]['qishu'] . "期</a></dd>";
            unset($xiangmulist[0]);
        }
        if (empty($xiangmulist)) {
            $wangqiqishull.='';
        }

        foreach ($xiangmulist as $key => $qitem) {
            if ($key % 9 == 0) {
                $wangqiqishull.='</ul><ul>';
            }
            if ($qitem['id'] == $xiangmuid) {
                $wangqiqishull.='<dd class="w_nper_color"><a >第' . $qitem['qishu'] . '期</a></dd>';
            } else {
                $wangqiqishull.='<dd><a href="' . C("URL_DOMAIN") . 'goods/dataserverForPc/goodsId/' . $qitem['id'] . '" >第' . $qitem['qishu'] . '期</a></dd>';
            }
        }
        $wangqiqishuss = '';
        if (!$xiangmulists[0]['q_uid']) {
            if ($xiangmulists[0]['id'] == $xiangmu['id'])
                $wangqiqishuss.='<option  value="' . C("URL_DOMAIN") . 'goods/items/goodsId/' . $xiangmulists[0]['id'] . '">' . "第" . $xiangmulists[0]['qishu'] . "期</option>";
            else
                $wangqiqishuss.='<option value="' . C("URL_DOMAIN") . 'goods/items/goodsId/' . $xiangmulists[0]['id'] . ' ">' . "第" . $xiangmulists[0]['qishu'] . "期</option>";
        }else {
            if ($xiangmulist[0]['id'] == $xiangmu['id'])
                $wangqiqishuss.='<li><a href="' . C("URL_DOMAIN") . 'goods/items/goodsId/' . $xiangmulist[0]['id'] . '"><b class="period_ArrowCur">' . "第" . $xiangmulist[0]['qishu'] . "期<i></i></b></a></li>";
            else
                $wangqiqishuss.='<li><a href="' . C("URL_DOMAIN") . 'goods/dataserverForPc/goodsId/' . $xiangmulists[0]['id'] . '" class="gray02">第' . $xiangmulists[0]['qishu'] . '期</a></li>';
        }
        unset($xiangmulist[0]);
        foreach ($xiangmulist as $key => $qitem) {
            if ($key % 9 == 0) {
                $wangqiqishuss.='</ul><ul class="Period_list">';
            }
            if ($qitem['id'] == $xiangmu['id'])
                $wangqiqishuss.='<option selected="" value="' . C("URL_DOMAIN") . 'goods/dataserverForPc/goodsId/' . $qitem['id'] . '">第' . $qitem['qishu'] . '期</option>';
            else
                $wangqiqishuss.='<option value="' . C("URL_DOMAIN") . 'goods/dataserverForPc/goodsId/' . $qitem['id'] . '">第' . $qitem['qishu'] . '期</option>';
        }
        $wangqiqishuss.='</li>';
        $wangqiqishussee = '';
        if (!$xiangmulists[0]['q_uid']) {
            if ($xiangmulists[0]['id'] == $xiangmu['id'])
                $wangqiqishussee.='<li class="curr"><a href="javascript:void(0)">' . "第" . $xiangmulists[0]['qishu'] . "期</a></li>";
            else
                $wangqiqishussee.='<li> <a href="' . C("URL_DOMAIN") . 'goods/items/goodsId/' . $xiangmulists[0]['id'] . ' ">' . "第" . $xiangmulists[0]['qishu'] . "期</a></li>";
        }else {
            if ($xiangmulist[0]['id'] == $xiangmu['id'])
                $wangqiqishussee.='<li><a href="' . C("URL_DOMAIN") . 'goods/items/goodsId/' . $xiangmulists[0]['id'] . '">' . "第" . $xiangmulists[0]['qishu'] . "期</a></li>";
            else
                $wangqiqishussee.='<li><a href="' . C("URL_DOMAIN") . 'goods/dataserverForPc/goodsId/' . $xiangmulists[0]['id'] . '" class="gray02">第' . $xiangmulists[0]['qishu'] . '期</a></li>';
        }
        unset($xiangmulist[0]);
        foreach ($xiangmulist as $key => $qitem) {
            if ($key % 9 == 0) {
                $wangqiqishussee.='';
            }
            if ($qitem['id'] == $xiangmu['id'])
                $wangqiqishussee.='<li class="curr"><a href="javascript:void(0)">第' . $qitem['qishu'] . '期</a></li>';
            else
                $wangqiqishussee.='<li><a href="' . C("URL_DOMAIN") . 'goods/dataserverForPc/goodsId/' . $xiangmulists[0]['id'] . '">第' . $qitem['qishu'] . '期</a></li>';
        }
        $wangqiqishussee.='</li>';
        $this->assign("huiyuan", $this->userinfo);
        $this->assign("error", $error);
        $this->assign("weer_shop_fmod", $weer_shop_fmod);
        $this->assign("weer_shop_number", $weer_shop_number);
        $this->assign("weer_shop_time", $weer_shop_time);
        $this->assign("fenlei", $fenlei);
        $this->assign("pinpai", $pinpai);
        $this->assign("q_content", $xiangmu['q_content']);
        $this->assign("wangqiqishull", $wangqiqishull);
        $this->assign("xiangmulists", $xiangmulists);
        $this->assign("q_yonghu", $q_yonghu);
        $this->assign("wangqiqishu", $wangqiqishu);
        $this->assign("xiangmuzx", $xiangmuzx);
        $this->assign('xiangmuid', $xiangmuid);
        $this->assign('xiangmu', $xiangmu);
        $this->display("index/index.lotterys");
    }

    //往期商品查看
    public function dataserver() {
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
        $appid = C("appid");
        $secret = C("secret");
        $jssdk = new \Claduipi\Wechat\JSSDK($appid, $secret);
        $signPackage = $jssdk->GetSignPackage();
        $key = "揭晓结果";
        $xiangmuid = I("goodsId", 0);
        $model = new \Think\Model;
        $xiangmu = $model->table("yys_shangpin")->where("id='$xiangmuid' and q_end_time is not null")->field('id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,fahuo')->find();
        if (!$xiangmu) {
            echo ("商品不存在！");
        }
        if ($xiangmu['q_showtime'] == 'Y') {
            header("location: " . C("URL_DOMAIN") . "goods/items/goodsId/" . $xiangmu['id']);
            exit;
        }
        $xiangmuzx = $model->table("yys_shangpin")->where("sid='{$xiangmu["sid"]}' and qishu>'{$xiangmu["qishu"]}' and q_end_time is null")->field("id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,fahuo")->order("qishu DESC")->find();
        $xiangmulist = $model->table("yys_shangpin")->where("sid='{$xiangmu["sid"]}'")->field("id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,fahuo")->order("qishu DESC")->select();
        $fenlei = $model->table("yys_fenlei")->where("cateid ='" . $xiangmu['cateid'] . "'")->find();
        $pinpai = $model->table("yys_pinpai")->where("id = '" . $xiangmu['brandid'] . "'")->find();
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
            $wangqiqishu.='<li><a href="' . C("URL_DOMAIN") . 'goods/items/goodsId/' . $xiangmulist[0]['id'] . '">' . "第" . $xiangmulist[0]['qishu'] . "期</a><b></b></li>";
            array_shift($xiangmulist);
        }
        foreach ($xiangmulist as $qitem) {
            if ($qitem['id'] == $xiangmuid) {
                $wangqiqishu.='<li><a class="hover" href="javascript:;"><s class="fl"></s>' . "第" . $qitem['qishu'] . "期</a><b></b></li>";
            } else {
                $wangqiqishu.='<li><a href="' . C("URL_DOMAIN") . 'goods/dataserver/goodsId/' . $qitem['id'] . '" ><s class="fl"></s>第' . $qitem['qishu'] . '期</a></li>';
            }
        }
        //总一元云购次数
        $weer_shop_number = 0;
        //用户一元云购时间
        $weer_shop_time = 0;
        //得到一元云购码
        $weer_shop_codes = '';
        $weer_shop_list = $model->table("yys_yonghu_yys_record")->where("uid= '{$xiangmu["q_uid"]}' and shopid = '$xiangmuid' and shopqishu = '{$xiangmu["qishu"]}'")->select();
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
        $weer_shop_fmod = fmod($weer_shop_time_add * 100, $xiangmu['canyurenshu']);
        if ($xiangmu['q_content']) {
            $xiangmu['q_content'] = unserialize($xiangmu['q_content']);
        }
        $xiangmu['picarr'] = unserialize($xiangmu['picarr']);
        //记录	 
        $gorecode = $model->table("yys_yonghu_yys_record")->where(array("shopid" => "$xiangmuid", "shopqishu" => "{$xiangmu['qishu']}", "uid" => "{$xiangmu['q_uid']}"))->field("*,sum(gonumber) as gonumber")->order("id DESC")->find();
        $me = $this->getUserInfo();
        $gorecodeme = $model->table("yys_yonghu_yys_record")->field("sum(gonumber) as gonumber")->where(array("shopid" => "$xiangmuid", "shopqishu" => "{$xiangmu['qishu']}", "uid" => "{$me['uid']}"))->order("id DESC")->select();
        $shopitem = 'dataserverfun';
        $curtime = time();
        //晒单数
        $shopid = $model->table("yys_shangpin")->where(array("id" => "$xiangmuid"))->field("id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,fahuo")->find();
        $yyslist = $model->table("yys_shangpin")->where(array("sid" => "{$shopid['sid']}"))->field("id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,fahuo")->select();
        $shop = '';
        foreach ($yyslist as $list) {
            $shop.=$list['id'] . ',';
        }
        $id = trim($shop, ',');
        if ($id) {
            $shaidingdan = $model->table("yys_shai")->where("sd_shopid IN (" . $id . ")")->select();
            $sum = 0;
            foreach ($shaidingdan as $sd) {
                $shaidingdan_hueifu = $model->table("yys_shai_hueifu")->where(array("sdhf_id" => "{$sd['sd_id']}"))->select();
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
        $this->assign("gorecodeme", $gorecodeme);
        $this->assign("weer_shop_number", $weer_shop_number);
        $this->assign("gorecode", $gorecode);
        $this->assign("me", $me);
        $this->assign("wangqiqishu", $wangqiqishu);
        $this->assign("xiangmuzx", $xiangmuzx);
        $this->assign('xiangmu', $xiangmu);
        $this->display("mobile/lotterys");
    }

    //购物车数量
    public function cartnum() {
        $Mcartlist = $this->getShopCart();
        if (is_array($Mcartlist)) {
            $gouwuchenum['code'] = 0;
            $gouwuchenum['num'] = count($Mcartlist);
        } else {
            $gouwuchenum['code'] = 1;
            $gouwuchenum['num'] = 0;
        }
        echo json_encode($gouwuchenum);
    }

    public function delCartItem() {
        $ShopId = I("codeid", 0);
        $gouwuchelist = $this->getShopCart();
        if ($ShopId == 0) {
            $gouwuche['code'] = 1;   //删除失败
        } else {
            if (is_array($gouwuchelist)) {
                if (count($gouwuchelist) == 1) {
                    foreach ($gouwuchelist as $key => $val) {
                        if ($key == $ShopId) {
                            $gouwuche['code'] = 0;
                            cookie('Cartlist', null);
                        } else {
                            $gouwuche['code'] = 1;
                        }
                    }
                } else {
                    foreach ($gouwuchelist as $key => $val) {
                        if ($key == $ShopId) {
                            $gouwuche['code'] = 0;
                        } else {
                            $Mcartlist[$key]['num'] = $val['num'];
                        }
                    }
                    cookie('Cartlist', json_encode($Mcartlist), '');
                }
            } else {
                $gouwuche['code'] = 1;   //删除失败
            }
        }
        echo json_encode($gouwuche);
    }

    //最新揭晓
    public function getLotteryList() {
        $fidx = I("fIdx", 0) - 1;
        $eidx = 10;
        $isCount = I("isCount", 0);
        $db_goods = D("shangpin");
        $shopsum = $db_goods->where("q_end_time !=''")->field("id")->count();
        //最新揭晓
        $yyslist['listItems'] = $db_goods->where("q_end_time !='' and q_showtime!='Y'")->field("id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,fahuo")->order("q_end_time DESC")->limit($fidx, $eidx)->select();
        if (empty($yyslist['listItems'])) {
            $yyslist['code'] = 1;
        } else {
            $gtimes = (int) C("goods_end_time");
            $db_record = D("yonghu_yys_record");
            foreach ($yyslist['listItems'] as $key => $val) {
                //查询出购买次数
                $recodeinfo = $db_record->where(array("uid" => "{$val['q_uid']}", "shopid" => "{$val['id']}"))->field("sum(gonumber) as gonumber")->find();
                $yyslist['listItems'][$key]['codeid'] = $val['id'];
                $yyslist['listItems'][$key]['codegoodspic'] = $val['thumb'];
                $yyslist['listItems'][$key]['userphoto'] = $this->huode_user_key($val['q_uid'], 'img');
                $yyslist['listItems'][$key]['username'] = $this->huode_user_name($val['q_uid']);
                $yyslist['listItems'][$key]['codeRIpAddr'] = $recodeinfo['gonumber'];
                $yyslist['listItems'][$key]['codeRUserBuyCount'] = $recodeinfo['gonumber'];
                $yyslist['listItems'][$key]['codeRNO'] = $recodeinfo['huode'];
                $yyslist['listItems'][$key]['codeRTime'] = $this->microt($val['q_end_time']);
                $yyslist['listItems'][$key]['userweb'] = $val['q_uid'];
                $yyslist['listItems'][$key]['codetype'] = $recodeinfo['gonumber'];
                $yyslist['listItems'][$key]['goodssnme'] = $recodeinfo['shopname'];
                $yyslist['listItems'][$key]['codeperiod'] = $val['qishu'];
                $yyslist['listItems'][$key]['codeprice'] = $val['money'];
            }
            $yyslist['code'] = 0;
            $yyslist['count'] = $shopsum;
        }
        echo json_encode($yyslist);
    }

    //商品列表
    public function glist() {
        if (ismobile()) {
            $biaoti = "商品列表_" . C("web_name");
            $key = "所有商品";
            $this->assign('category', R("Mobile/getCategory"));
            $this->assign("keys", $key);
            $this->assign("biaoti", $biaoti);
            $this->display("mobile/glist");
            exit;
        }
        $glist = I("glist", 0);
        $select = I("type", 0);
        $select = explode("_", $select);
        $select[] = '0';
        $select[] = '0';
        $cid = abs(intval($select[0]));
        $bid = abs(intval($select[1]));
        $order = abs(intval($select[2]));
        $where = '1=1';
        $orders = '';
        switch ($order) {
            case '1':
                $orders = 'shenyurenshu ASC';
                break;
            case '2':
                $where = "renqi = '1'";
                break;
            case '3':
                $orders = 'shenyurenshu ASC';
                break;
            case '4':
                $orders = 'time DESC';
                break;
            case '5':
                $orders = 'money DESC';
                break;
            case '6':
                $orders = 'money ASC';
                break;
            default:
                $orders = 'shenyurenshu ASC';
        }
        /* 设置了查询分类ID 和品牌ID */
        if (!$cid) {
            $pinpai = D("pinpai")->field("id,cateid,name")->order("`order` DESC")->select();
            $daohangs_title = '所有分类';
        } else {
            $pinpai = D("pinpai")->field("id,cateid,name")->where("cateid LIKE '%$cid%'")->order("`order` DESC")->select();
            $daohangs = D("fenlei")->field("cateid,name,parentid,info")->where(array("cateid" => $cid))->order("`order` DESC")->find();
            $daohangs['info'] = unserialize($daohangs['info']);
            $daohangs_title = empty($daohangs['info']['meta_title']) ? $daohangs['name'] : $daohangs['info']['meta_title'];
            $guanjianzi = $daohangs['info']['meta_keywords'];
            $miaoshu = $daohangs['info']['meta_description'];
        }
        $biaoti = $daohangs_title . "_商品列表_" . C("web_name");
        //分页
        $num = 20;
        //整合10圆区等
        if ($glist && ($glist == 10 || $glist == 100)) {
            $where.=" and yunjiage=" . $glist;
        } else if ($glist && $glist == 'xg') {
            $where.=" and fahuo=1";
        }
        /* 设置了查询分类ID 和品牌ID */
        if ($cid && $bid) {
            $sun_id_str = "'" . $cid . "'";
            $sun_cate = D("fenlei")->field("cateid")->where(array("parentid" => $daohangs['cateid']))->select();
            foreach ($sun_cate as $v) {
                $sun_id_str .= "," . "'" . $v['cateid'] . "'";
            }
            $zongji = D("shangpin")->field("cateid")->where("q_uid is null and brandid='$bid'  and cateid in ($sun_id_str) and $where")->count();
        } else {
            if ($bid) {
                $zongji = D("shangpin")->field("id")->where("q_uid is null and brandid='$bid' and $where")->count();
            } elseif ($cid) {
                $sun_id_str = "'" . $cid . "'";
                $sun_cate = D("fenlei")->where(array("parentid" => $daohangs['cateid']))->select();
                foreach ($sun_cate as $v) {
                    $sun_id_str .= "," . "'" . $v['cateid'] . "'";
                }
                $zongji = D("shangpin")->field("id")->where("q_uid is null and cateid in ($sun_id_str) and $where")->count();
            } else {
                $zongji = D("shangpin")->field("ect id")->where("q_uid is null and $where")->count();
            }
        }
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");

        if ($cid && $bid) {
            $yyslist = D("shangpin")->field("id,sid,cateid,brandid,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,q_end_qishu,fahuo")->where("q_uid is null and brandid='$bid' and cateid in ($sun_id_str) and $where")->order($orders)->limit(($fenyenum - 1) * $num, $num)->select();
        } else {
            if ($bid) {
                $yyslist = D("shangpin")->field("id,sid,cateid,brandid,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,q_end_qishu,fahuo")->where("q_uid is null and brandid='$bid' and $where")->order($orders)->limit(($fenyenum - 1) * $num, $num)->select();
            } elseif ($cid) {
                $yyslist = D("shangpin")->field("id,sid,cateid,brandid,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,q_end_qishu,fahuo")->where("q_uid is null and cateid in ($sun_id_str) and $where")->order($orders)->limit(($fenyenum - 1) * $num, $num)->select();
            } else {
                $yyslist = D("shangpin")->field("id,sid,cateid,brandid,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,q_end_qishu,fahuo")->where("q_uid is null and $where")->order($orders)->limit(($fenyenum - 1) * $num, $num)->select();
            }
        }
        $this->assign("daohangs", $daohangs);
        $this->assign("zongji", $zongji);
        $this->assign("fenye", $fenye);
        $this->assign("yyslist", $yyslist);
        $this->assign("cid", $cid);
        $this->assign("bid", $bid);
        $this->display("index/index.glist");
    }

    public function getGoodsPageList() {
        $cate_band = I("sortid", 0);
        $select = I("orderFlag", 0);
        $kaishi = I("fIdx", 0);
        $jieshu = I("eIdx", 0);
        $glist = I("glist", 0);
        $p = $kaishi;
        $db = new \Think\Model;
        $sun_cate = $db->table("yys_fenlei")->where(array("parentid" => "$cate_band"))->field("cateid")->select();
        foreach ($sun_cate as $v) {
            $sun_id_str .= "'" . $v['cateid'] . "'" . ",";
        }
        $newstr = substr($sun_id_str, 0, strlen($sun_id_str) - 1);
        if ($newstr) {
            $select = !$select ? '10' : $select;
            if ($cate_band) {
                $fen1 = intval($cate_band);
                $cate_band = 'list';
            }
            if (empty($fen1)) {
                $pinpai = $db->table("yys_pinpai")->order("`order` DESC")->select();
                $daohangs = '所有分类';
            } else {

                $pinpai = $db->table("yys_pinpai")->where(array("cateid" => "$fen1"))->order(" `order` DESC")->select();
                $daohangs = $db->table("yys_fenlei")->where(array("cateid" => "$fen1"))->order(" `order` DESC")->find();
                $daohangs = $daohangs['name'];
            }
            $fenlei = $db->table("yys_fenlei")->where(array("model" => "1"))->select();
            //分页
            $end = $jieshu;
            $star = ($p - 1);
            $where = " and 1=1";
            $order = "id";
            if ($select == 10 || $select == 40) {
                $order = " shenyurenshu";
            }
            if ($select == 20) {
                $where = " and renqi='1'";
            }
            if ($select == 30) {
                $order = " money DESC";
            }
            if ($select == 31 || $select == 60) {
                $order = " money";
            }
            if ($select == 50) {
                $order = " id DESC";
            }
            $where2 = $fen1 ? " and cateid in ($newstr)" : "";
            $where = "q_uid is null" . $where2 . $where;
            //整合10圆区等
            if ($glist && ($glist == 10 || $glist == 100)) {
                $where.=" and yunjiage=" . $glist;
            } else if ($glist && $glist == 'xg') {
                $where.=" and fahuo=1";
            } else if ($glist && $glist == 'xuangou') {
                $where.=" and is_choose=1";
            }
            $count = $db->table("yys_shangpin")->where($where)->count();
            $yyslist = $db->table("yys_shangpin")->where($where)->field("id,title,thumb,qishu,money,zongrenshu,canyurenshu,qishu")->order($order)->limit($star, $end)->select();
        }
        if ($yyslist) {
            $yyslist1['code'] = 0;
            $yyslist1['count'] = $count;
            foreach ($yyslist as $key => $val) {
                $yyslist1['listItems'][$key]['rowid'] = 0;
                $yyslist1['listItems'][$key]['goodsid'] = $val['id'];
                $yyslist1['listItems'][$key]['goodssnme'] = $val['title'];
                $yyslist1['listItems'][$key]['goodspic'] = $val['thumb'];
                $yyslist1['listItems'][$key]['codeid'] = $val['id'];
                $yyslist1['listItems'][$key]['codeprice'] = $val['money'];
                $yyslist1['listItems'][$key]['codequantity'] = $val['zongrenshu'];
                $yyslist1['listItems'][$key]['codesales'] = $val['canyurenshu'];
                $yyslist1['listItems'][$key]['codeperiod'] = $val['qishu'];
                $yyslist1['listItems'][$key]['codetype'] = 0;
                $yyslist1['listItems'][$key]['goodstag'] = 0;
                $yyslist1['listItems'][$key]['codelimitbuy'] = 0;
            }
        } else {
            $yyslist1['code'] = 1;
        }
        echo json_encode($yyslist1);
    }

    /**
     * admin 商品添加
     */
    public function goods_add() {
        $db = new \Think\Model;
        if (IS_AJAX || IS_POST) {
            $cateid = intval(I("cateid", 0));
            $pinpaiid = intval(I("brand", 0));
            $biaoti = $this->htmtguolv(I("title", ""));
            $biaoti_color = htmlspecialchars(I("title_style_color", ""));
            $biaoti_bold = htmlspecialchars(I("title_style_bold", ""));
            $biaoti2 = $this->htmtguolv(I("title2", ""));
            $guanjianzi = htmlspecialchars(I("keywords", ""));
            $miaoshu = htmlspecialchars(I("description", ""));
            $content = $this->editor_safe_replace(stripslashes(I("content", "")));
            $money = intval(I("money", 0));
            $yunjiage = intval(I("yunjiage", 0));
            $thumb = htmlspecialchars(I("thumb", ""));
            $maxqishu = intval(I("maxqishu", 0));
            $canyurenshu = 0;
            $shangpinss_key_pos = isset($_POST['goods_key']['pos']) ? 1 : 0;
            $shangpinss_key_renqi = isset($_POST['goods_key']['renqi']) ? 1 : 0;
            $shangpinss_key_renqi1 = isset($_POST['goods_key']['renqi1']) ? 1 : 0;
            //fix by dabin
            $goods_key_leixing = $_POST['goods_key']['leixing'];
            //roce:获取卡密卡号
            $cardId1 = str_replace(array("\r\n", "\r", "\n"), ";", I("cardId1", ""));
            $cardId2 = mysql_escape_string(I("cardId2", ""));
            $cardId = mysql_escape_string(I("cardId", ""));
            $cardPwd = mysql_escape_string(I("cardPwd", ""));
          
            if (!$cateid)
                $this->note("请选择栏目");
            if (!$pinpaiid)
                $this->note("请选择品牌");
            if (!$biaoti)
                $this->note("标题不能为空");
            if (!$thumb)
                $this->note("缩略图不能为空");
            $biaoti_style = '';
            if ($biaoti_color) {
                $biaoti_style.='color:' . $biaoti_color . ';';
            }
            if ($biaoti_bold) {
                $biaoti_style.='font-weight:' . $biaoti_bold . ';';
            }
            if (I("uppicarr", "")) {
                $picarr = serialize(I("uppicarr", ""));
            } else {
                $picarr = serialize(array());
            }
            if (I("xsjx_time", "") != '') {
                $xsjx_time = strtotime(I("xsjx_time", "")) ? strtotime(I("xsjx_time", "")) : time();
                $xsjx_time_h = intval(I("xsjx_time_h", "")) ? I("xsjx_time_h", "") : 36000;
                $xsjx_time += $xsjx_time_h;
            } else {
                $xsjx_time = '0';
            }
            if ($maxqishu > 65535) {
                $this->note("最大期数不能超过65535期");
            }
            if ($money < $yunjiage)
                $this->note("商品价格不能小于购买价格");
            $zongrenshu = ceil($money / $yunjiage);
            $codes_len = ceil($zongrenshu / 3000);
            $shenyurenshu = $zongrenshu - $canyurenshu;
            if ($zongrenshu == 0 || ($zongrenshu - $canyurenshu) == 0) {
                $this->note("云购价格不正确");
            }
            $time = time(); //商品添加时间		
            $db->startTrans();
            $arr = array("ka" => htmlspecialchars(I('kahao')), "mi" => htmlspecialchars($_POST['mima']), "cateid" => "$cateid", "brandid" => "$pinpaiid", "title" => "$biaoti", "title_style" => "$biaoti_style", "title2" => "$biaoti2", "keywords" => "$guanjianzi", "description" => "$miaoshu", "money" => "$money", "yunjiage" => "$yunjiage", "zongrenshu" => "$zongrenshu", "canyurenshu" => "$canyurenshu", "shenyurenshu" => "$shenyurenshu", "qishu" => "1", "maxqishu" => "$maxqishu", "thumb" => "$thumb", "picarr" => "$picarr", "content" => "$content", "xsjx_time" => "$xsjx_time", "renqi" => "$shangpinss_key_renqi", "renqi1" => "$shangpinss_key_renqi1", "pos" => "$shangpinss_key_pos", "time" => "$time", "cardId" => "$cardId", "cardId1" => "$cardId1", "cardPwd" => "$cardPwd", "leixing" => "$goods_key_leixing", "yuanjia" => "$cardId2");
            $query_1 = $db->table("yys_shangpin")->add($arr);
            $shopid = $query_1;
            $query_table = $this->content_get_codes_table();
            if (!$query_table) {
                $db->rollback();
                $this->note("云购码仓库不正确!");
            }
            $query_2 = $this->content_huode_go_codes($zongrenshu, 3000, $shopid);
            $query_3 = $db->table("yys_shangpin")->where(array("id" => $shopid))->save(array("codes_table" => "$query_table", "sid" => "$shopid", "def_renshu" => "$canyurenshu"));
            if ($query_1 && $query_2 && $query_3) {
                $db->commit();
                $this->note("商品添加成功!");
            } else {
                $db->rollback();
                $this->note("商品添加失败!");
            }
            header("Cache-control: private");
        }
        $cateid = intval(I("cateid", 0)); //intval($this->segment(4));
        $fenlei = $db->table("yys_fenlei")->where(array("model" => "1"))->order("parentid ASC,cateid")->select();
        $fenleis = $this->key2key($fenlei, "cateid");
        $tree = new \Claduipi\Tools\tree;
        $tree->icon = array('│ ', '├─ ', '└─ ');
        $tree->nbsp = '&nbsp;';
        $fenleishtml = "<option value='\$cateid'>\$spacer\$name</option>";
        $tree->init($fenleis);
        $fenleishtml = $tree->get_tree(0, $fenleishtml);
        $fenleishtml = '<option value="0">≡ 请选择栏目 ≡</option>' . $fenleishtml;
        if ($cateid) {
            $cateinfo = $db->table("yys_fenlei")->where(array("cateid" => "$cateid"))->find();
            if (!$cateinfo)
                $this->note("参数不正确,没有这个栏目", G_ADMIN_PATH . '/' . DOWN_C . '/addarticle');
            $fenleishtml.='<option value="' . $cateinfo['cateid'] . '" selected="true">' . $cateinfo['name'] . '</option>';
            $pinpai = D("pinpai")->where(array("cateid" => "$cateid"))->select();
            $pinpaiList = $this->key2key($pinpai, "id");
        }else {
            $pinpai = D("pinpai")->select();
            $pinpaiList = $this->key2key($pinpai, "id");
        }
        $ment = array(
            array("lists", "商品管理", C("URL_DOMAIN") . "goods/goods_list"),
            array("insert", "添加商品", C("URL_DOMAIN") . "goods/goods_add"),
        );
        $this->assign("fenleishtml", $fenleishtml);
        $this->assign("ment", $ment);
        $this->display("admin/shop.insert");
    }

    /**
     * 获取分类下的品牌ajax
     */
    public function json_brand() {
        $cateid = intval(I("cid", 0));
        if ($cateid) {
            $pinpaiList = D("pinpai")->where("cateid LIKE '%$cateid%'")->select();
            echo json_encode($pinpaiList);
        }
    }

//ajax 删除商品
    public function goods_del() {
        $info = R('admin/getAdminInfo', array());
        $shopid = intval(I("id", 0));
        $db = new \Think\Model;
        $info = $db->table("yys_shangpin")->where(array("id" => "$shopid"))->find();
        $table = $info['codes_table'];
        $db->startTrans();
        $q4 = $db->table("yys_shangpin_del")->add($info);
        $q1 = $db->table("yys_{$table}")->where(array("s_id" => "$shopid"))->delete();
        $q2 = $db->table("yys_shangpin")->where(array("id" => "$shopid"))->delete();
        $q3 = $db->table("yys_yonghu_yys_record")->where(array("shopid" => "$shopid"))->delete();
        if ($q1 && $q2 && $q4) {
            $db->commit();
            $this->note("商品删除成功", C("URL_DOMAIN") . "goods/goods_list");
        } else {
            $db->rollback();
            $this->note("商品删除失败", C("URL_DOMAIN") . "goods/goods_list");
        }
        exit;
    }

    // 撤销删除
    public function goods_del_key() {
        $db = new \Think\Model;
        $shopid = I("id", 0);
        $key = I("type", "");
        //撤销	
        if ($key == 'yes') {
            $db->startTrans();
            $info = $db->table("yys_shangpin_del")->where(array("id" => "$shopid"))->find();
            $q1 = $db->table("yys_shangpin")->add($info);
            $q2 = $db->table("yys_shangpin_del")->where(array("id" => "$shopid"))->delete();
            if (!$q1 || !$q2) {
                $db->rollback();
                $this->note("操作失败");
            } else {
                $db->commit();
                $this->note("操作成功");
            }
        }
        //从数据库删除
        if ($key == 'no') {
            $db->table("yys_shangpin_del")->where(array("id" => "$shopid"))->delete();
            $this->note("操作成功");
        }
    }

    //清空回收站
    public function goods_del_all() {
        D("shangpin_del")->where("1=1")->delete();
        $this->note("清空成功");
    }

    //商品列表	
    public function goods_list() {
        $ment = array(
            array("lists", "商品管理", C("URL_DOMAIN") . "goods/goods_list"),
            array("add", "添加商品", C("URL_DOMAIN") . "goods/goods_add"),
            array("renqi", "人气商品", C("URL_DOMAIN") . "goods/goods_list/order/renqi"),
            array("xsjx", "限时揭晓商品", C("URL_DOMAIN") . "goods/goods_list/order/xianshi"),
            array("qishu", "期数倒序", C("URL_DOMAIN") . "goods/goods_list/order/qishu"),
            array("danjia", "单价倒序", C("URL_DOMAIN") . "goods/goods_list/order/danjia"),
            array("money", "商品价格倒序", C("URL_DOMAIN") . "goods/goods_list/order/money"),
            array("money", "已揭晓", C("URL_DOMAIN") . "goods/goods_list/order/jiexiaook"),
            array("money", "<font color='#f00'>期数已满商品</font>", C("URL_DOMAIN") . "goods/goods_list/order/maxqishu"),
        );
        $cateid = I("order", "");
        $list_where = '';
        $list_order = '';
        if ($cateid) {
            if ($cateid == 'jiexiaook') {
                $list_where = "q_uid is not null";
            }
            if ($cateid == 'maxqishu') {
                $list_where = "qishu = maxqishu and q_end_time is not null";
            }
            if ($cateid == 'renqi') {
                $list_where = "renqi = '1'";
            }
            if ($cateid == 'xianshi') {
                $list_where = "xsjx_time != '0'";
            }
            if ($cateid == 'qishu') {
                $list_order = " qishu DESC";
                $ment[4][1] = "期数正序";
                $ment[4][2] = C("URL_DOMAIN") . "goods/goods_list/order/qishuasc";
            }
            if ($cateid == 'qishuasc') {
                $list_order = " qishu ASC";
                $ment[4][1] = "期数倒序";
                $ment[4][2] = C("URL_DOMAIN") . "goods/goods_list/order/qishu";
            }
            if ($cateid == 'danjia') {
                $list_order = " yunjiage DESC";
                $ment[5][1] = "单价正序";
                $ment[5][2] = C("URL_DOMAIN") . "goods/goods_list/order/danjiaasc";
            }
            if ($cateid == 'danjiaasc') {
                $list_order = " yunjiage ASC";
                $ment[5][1] = "单价倒序";
                $ment[5][2] = C("URL_DOMAIN") . "goods/goods_list/order/danjia";
            }
            if ($cateid == 'money') {
                $list_order = " money DESC";
                $ment[6][1] = "商品价格正序";
                $ment[6][2] = C("URL_DOMAIN") . "goods/goods_list/order/moneyasc";
            }
            if ($cateid == 'moneyasc') {
                $list_order = " money ASC";
                $ment[6][1] = "商品价格倒序";
                $ment[6][2] = C("URL_DOMAIN") . "goods/goods_list/order/money";
            }
            if ($cateid == '') {
                $list_where = "q_uid is null  order by id DESC";
            }
            if (intval($cateid)) {
                $list_where = "cateid = '$cateid'";
            }
        } else {
            $list_where = "q_uid is null";
            $list_order = " id DESC";
        }
        if (isset($_POST['sososubmit'])) {
            $posttime1 = !empty($_POST['posttime1']) ? strtotime($_POST['posttime1']) : NULL;
            $posttime2 = !empty($_POST['posttime2']) ? strtotime($_POST['posttime2']) : NULL;
            $sotype = $_POST['sotype'];
            $sosotext = $_POST['sosotext'];
            if ($posttime1 && $posttime2) {
                if ($posttime2 < $posttime1)
                    $this->note("结束时间不能小于开始时间");
                $list_where = "time > '$posttime1' AND time < '$posttime2'";
            }
            if ($posttime1 && empty($posttime2)) {
                $list_where = "time > '$posttime1'";
            }
            if ($posttime2 && empty($posttime1)) {
                $list_where = "time < '$posttime2'";
            }
            if (empty($posttime1) && empty($posttime2)) {
                $list_where = false;
            }
            if (!empty($sosotext)) {
                if ($sotype == 'cateid') {
                    $sosotext = intval($sosotext);
                    if ($list_where)
                        $list_where .= " AND cateid = '$sosotext'";
                    else
                        $list_where = "cateid = '$sosotext'";
                }
                if ($sotype == 'brandid') {
                    $sosotext = intval($sosotext);
                    if ($list_where)
                        $list_where .= " AND brandid = '$sosotext'";
                    else
                        $list_where = "brandid = '$sosotext'";
                }
                if ($sotype == 'brandname') {
                    $sosotext = htmlspecialchars($sosotext);

                    $info = D("pinpai")->where("name LIKE '%$sosotext%'")->find();

                    if ($list_where && $info)
                        $list_where .= " AND brandid = '{$info['id']}'";
                    elseif ($info)
                        $list_where = "brandid = '{$info['id']}'";
                    else
                        $list_where = "1";
                }
                if ($sotype == 'catename') {
                    $sosotext = htmlspecialchars($sosotext);
                    $info = D("fenlei")->where("name LIKE '%$sosotext%'")->find();
                    if ($list_where && $info)
                        $list_where .= " AND cateid = '{$info['cateid']}'";
                    elseif ($info)
                        $list_where = "cateid = '{$info['cateid']}'";
                    else
                        $list_where = "1";
                }
                if ($sotype == 'title') {
                    $sosotext = htmlspecialchars($sosotext);
                    $list_where = "title = '$sosotext'";
                }
                if ($sotype == 'id') {
                    $sosotext = intval($sosotext);
                    $list_where = "id = '$sosotext'";
                }
            } else {
                if (!$list_where)
                    $list_where = '1';
            }
        }
        $num = 20;
        $zongji = D("shangpin")->where($list_where)->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");
        $yyslist = D("shangpin")->where($list_where)->order($list_order)->limit(($fenyenum - 1) * $num, $num)->select();
        $this->assign("ment", $ment);
        $this->assign("zongji", $zongji);
        $this->assign("fenye", $fenye);
        $this->assign("yyslist", $yyslist);
        $this->assign("cateid", $cateid);
        $this->display("admin/shop.lists");
    }

    /* 所有参与记录 */

    public function go_record_ifram() {
        $yonghuid = I("id", 0);
        $changdu = I("len", 0);
        if ($changdu < 10) {
            $changdu = 10;
        }
        $fenye = new \Claduipi\Tools\page;
        $zongji = D("yonghu_yys_record")->where(array("shopid" => $yonghuid))->count();
        if (isset($_GET['p'])) {
            $fenyenum = (int) $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $num = $changdu;
        $fenye->config($zongji, $num, $fenyenum, "0");
        $yys_record_list = D("yonghu_yys_record")->where(array("shopid" => $yonghuid))->order("id DESC")->limit(($fenyenum - 1) * $num, $num)->select();
        $this->assign("zongji", $zongji);
        $this->assign("fenye", $fenye);
        $this->assign("num", $num);
        $this->assign("yys_record_list", $yys_record_list);
        $this->display("index/index.go_record_ifram");
    }

    /*
     * 	商品排序
     */

    public function goods_listorder() {
        foreach ($_POST['listorders'] as $id => $listorder) {
            $id = intval($id);
            $listorder = intval($listorder);
            D("shangpin")->where(array("id" => "$id"))->save(array("order" => "$listorder"));
        }
        $this->note("排序更新成功");
    }

    //商品设置
    public function goods_set() {
        $p_key = I("type");
        $p_val = I("value");
        if (empty($p_key) || empty($p_val)) {
            $this->note("设置失败");
        }
        $ss = D("shai")->where(array("sd_shopid" => "$p_val"))->find();
        $query = true;
        switch ($p_key) {
            case 'renqi':
                $query = D("shangpin")->where(array("id" => "$p_val"))->save(array("renqi" => "1"));
                break;
            case 'renqi1':
                $query = D("shangpin")->where(array("id" => "$p_val"))->save(array("renqi1" => "1"));
                break;
            case 'shenhe':
                $query = D("shai")->where(array("sd_shopid" => "$p_val"))->save(array("shenhe" => "1"));
                D("yonghu")->where("uid={$ss['sd_userid']}")->setInc('score', C('shaim'));
                break;
            case 'shenhe1':
                $query = D("shai")->where(array("sd_shopid" => "$p_val"))->save(array("shenhe" => "0"));
                break;
            case 'fahuo':
                $query = D("shangpin")->where(array("id" => "$p_val"))->save(array("fahuo" => "0"));
                break;
            case 'fahuo1':
                $query = D("shangpin")->where(array("id" => "$p_val"))->save(array("fahuo" => "1"));
                break;
            case 'huiyuan':
                $query = D("yonghu")->where(array("uid" => "$p_val"))->save(array("huiyuan" => "1"));
                break;
            case 'huiyuan1':
                $query = D("yonghu")->where(array("uid" => "$p_val"))->save(array("huiyuan" => "0"));
                break;
            case 'xuangou':
                $query = D("shangpin")->where(array("id" => "$p_val"))->save(array("is_choose" => "0"));
                break;
            case 'xuangou1':
                $query = D("shangpin")->where(array("id" => "$p_val"))->save(array("is_choose" => "1"));
                break;
            case 'audit_seller':
                $query = D("yonghu")->where(array('uid' => $p_val))->save(array('type' => 1));
                break;
            case 'audit_sellerok':
                $query = D("yonghu")->where(array('uid' => $p_val))->save(array('type' => 0));
                break;
        }
        if ($query) {
            $this->note("设置成功");
        } else {
            $this->note("设置失败");
        }
    }

    /**
     * 	重置商品价格
     * */
    public function goods_set_money() {
        $ment = array(
            array("lists", "商品管理", C("URL_DOMAIN") . "goods/goods_list"),
            array("add", "添加商品", C("URL_DOMAIN") . "goods/goods_add"),
            array("renqi", "人气商品", C("URL_DOMAIN") . "goods/goods_list/order/renqi"),
            array("xsjx", "限时揭晓商品", C("URL_DOMAIN") . "goods/goods_list/order/xianshi"),
            array("qishu", "期数倒序", C("URL_DOMAIN") . "goods/goods_list/order/qishu"),
            array("danjia", "单价倒序", C("URL_DOMAIN") . "goods/goods_list/order/danjia"),
            array("money", "商品价格倒序", C("URL_DOMAIN") . "goods/goods_list/order/money"),
            array("money", "已揭晓", C("URL_DOMAIN") . "goods/goods_list/order/jiexiaook"),
            array("money", "<font color='#f00'>期数已满商品</font>", C("URL_DOMAIN") . "goods/goods_list/order/maxqishu"),
        );
        $db = new \Think\Model;
        $db->startTrans();
        $yonghuid = abs(I("id", 0));
        $shopinfo = $db->table("yys_shangpin")->where(array("id" => "$yonghuid"))->lock(true)->find();
        if (!$shopinfo || !empty($shopinfo['q_uid'])) {
            $this->note("参数不正确!");
            exit;
        }
        $g1o = $db->table("yys_yonghu_yys_record")->where(array("shopid" => "$yonghuid"))->select();
        if (isset($_POST['money']) || isset($_POST['yunjiage'])) {
            $new_money = abs(intval($_POST['money']));
            $new_one_m = abs(intval($_POST['yunjiage']));
            if ($new_one_m > $new_money) {
                $this->note("单人次购买价格不能大于商品总价格!");
            }
            if (!$new_one_m || !$new_money) {
                $this->note("价格填写错误!");
            }
            if (($new_one_m == $shopinfo['yunjiage']) && ($new_money == $shopinfo['money'])) {
                $this->note("价格没有改变!");
            }
            $table = $shopinfo['codes_table'];
            $db->table("yys_yonghu_yys_record")->where(array("shopid" => "$yonghuid"))->delete();
            $q2 = $db->table("yys_{$table}")->where(array("s_id" => "$yonghuid"))->delete();
            $zongrenshu = ceil($new_money / $new_one_m);
            $q3 = $this->content_huode_go_codes($zongrenshu, 3000, $yonghuid);
            $q4 = $db->table("yys_shangpin")->where(array("id" => "$yonghuid"))->save(array("canyurenshu" => '0', "zongrenshu" => "$zongrenshu", "money" => "$new_money", "yunjiage" => "$new_one_m", "shenyurenshu" => "{$shopinfo['zongrenshu']}"));
            foreach ($g1o as $v) {
                $q5 = $db->table("yys_yonghu")->where(array("uid" => "{$v['uid']}"))->setInc('money', $v['moneycount']);
                $q6 = $db->table("yys_yonghu_zhanghao")->add(array("uid" => "{$v['uid']}", "type" => '1', "pay" => '账户', "content" => "商品iD号.$yonghuid.重置价格返回", "money" => "{$v['moneycount']}", time => time()));
            }
            if ($q2 && $q3 && $q4 && $q5 && $q6) {
                $db->commit();
                $this->note("更新成功并返回余额到用户帐号!");
            } else if ($q2 && $q3 && $q4) {
                $db->commit();
                $this->note("更新成功");
            } else {
                $db->rollback();
                $this->note("更新失败!");
            }
        }
        $this->assign("shopinfo", $shopinfo);
        $this->assign("ment", $ment);
        $this->display("admin/shop.set_money");
    }

    //编辑商品
    public function goods_edit() {
        $db = new \Think\Model;
        $db->startTrans();
        $shopid = intval(I("id", 0));
        $shopinfo = $db->table("yys_shangpin")->lock(true)->where("id = '$shopid' and qishu")->order("qishu DESC")->find();
        if ($shopinfo['q_end_time'])
            $this->note("该商品已经揭晓,不能修改!", C("URL_DOMAIN") . '/goods/goods_list');
        if (!$shopinfo)
            $this->note("参数不正确");
        if (isset($_POST['dosubmit'])) {
            $cateid = intval($_POST['cateid']);
            $pinpaiid = intval($_POST['brand']);
            $biaoti = htmlspecialchars($_POST['title']);
            $biaoti_color = htmlspecialchars($_POST['title_style_color']);
            $biaoti_bold = htmlspecialchars($_POST['title_style_bold']);
            $biaoti2 = htmlspecialchars($_POST['title2']);
            $guanjianzi = htmlspecialchars($_POST['keywords']);
            $miaoshu = htmlspecialchars($_POST['description']);
            $content = $this->editor_safe_replace(stripslashes($_POST['content']));
            $thumb = trim(htmlspecialchars($_POST['thumb']));
            $maxqishu = intval($_POST['maxqishu']) ? intval($_POST['maxqishu']) : 1;
            $shangpinss_key_pos = isset($_POST['goods_key']['pos']) ? 1 : 0;
            $shangpinss_key_renqi = isset($_POST['goods_key']['renqi']) ? 1 : 0;
            $shangpinss_key_renqi1 = isset($_POST['goods_key']['renqi1']) ? 1 : 0;
            $goods_key_leixing = $_POST['goods_key']['leixing'];
            $cardId1 = mysql_escape_string($_POST['cardId1']);
            $cardId2 = mysql_escape_string($_POST['cardId2']);
            $cardId = mysql_escape_string($_POST['cardId']);
            $cardPwd = mysql_escape_string($_POST['cardPwd']);
          
          
            if (!$cateid)
                $this->note("请选择栏目");
            if (!$pinpaiid)
                $this->note("请选择品牌");
            if (!$biaoti)
                $this->note("标题不能为空");
            if (!$thumb)
                $this->note("缩略图不能为空");
            $biaoti_style = '';
            if ($biaoti_color) {
                $biaoti_style.='color:' . $biaoti_color . ';';
            }
            if ($biaoti_bold) {
                $biaoti_style.='font-weight:' . $biaoti_bold . ';';
            }
            if (isset($_POST['uppicarr'])) {
                $picarr = serialize($_POST['uppicarr']);
            } else {
                $picarr = serialize(array());
            }
            if ($_POST['xsjx_time'] != '') {
                $xsjx_time = strtotime($_POST['xsjx_time']) ? strtotime($_POST['xsjx_time']) : time();
                $xsjx_time_h = intval($_POST['xsjx_time_h']) ? $_POST['xsjx_time_h'] : 36000;
                $xsjx_time += $xsjx_time_h;
            } else {
                $xsjx_time = '0';
            }
            if ($maxqishu > 65535) {
                $this->note("最大期数不能超过65535期");
            }
            if ($maxqishu < $shopinfo['qishu']) {
                $this->note("最期数不能小于当前期数！");
            }
            $up_data = array(
                "cateid" => $cateid,
                "brandid" => "$pinpaiid",
                "title" => "$biaoti",
                "title_style" => "$biaoti_style",
                "title2" => "$biaoti2",
                "keywords" => "$guanjianzi",
                "description" => "$miaoshu",
                "thumb" => "$thumb",
                "picarr" => "$picarr",
                "content" => "$content",
                "maxqishu" => "$maxqishu",
                "renqi" => "$shangpinss_key_renqi",
                "renqi1" => "$shangpinss_key_renqi1",
                "leixing" => "$goods_key_leixing",
                "xsjx_time" => "$xsjx_time",
               
                "ka" => htmlspecialchars($_POST['kahao']),
                "mi" => htmlspecialchars($_POST["mima"]),
                "pos" => "$shangpinss_key_pos",
                "cardId1" => "$cardId1",
                "yuanjia" => "$cardId2",
                "cardId" => "$cardId",
                "cardPwd" => "$cardPwd"
            );
            $s_sid = $shopinfo['sid'];
            $db->table("yys_shangpin")->where(array("sid" => "$s_sid"))->save(array("maxqishu" => "$maxqishu"));
            if ($db->table("yys_shangpin")->where(array("id" => "$shopid"))->save($up_data)) {
                $db->commit();
                $this->note("修改成功!");
            } else {
                $db->rollback();
                $this->note("修改失败!");
            }
        }
        $ment = array(
            array("lists", "商品管理", C("URL_DOMAIN") . "goods/goods_list"),
            array("insert", "添加商品", C("URL_DOMAIN") . "goods/goods_add"),
        );
        $cateinfo = $db->table("yys_fenlei")->where("cateid = '{$shopinfo['cateid']}'")->find();
        $pinpai = $db->table("yys_pinpai")->where("cateid = '{$shopinfo['cateid']}'")->select();
        $pinpaiList = $this->key2key($pinpai, "id");
        $fenlei = $db->table("yys_fenlei")->where("model = '1'")->order(" parentid ASC,cateid")->select();
        $fenleis = $this->key2key($fenlei, "cateid");
        $tree = new \Claduipi\Tools\tree;
        $tree->icon = array('│ ', '├─ ', '└─ ');
        $tree->nbsp = '&nbsp;';
        $fenleishtml = "<option value='\$cateid'>\$spacer\$name</option>";
        $tree->init($fenleis);
        $fenleishtml = $tree->get_tree(0, $fenleishtml);
        $fenleishtml.='<option value="' . $cateinfo['cateid'] . '" selected="true">' . $cateinfo['name'] . '</option>';
        if ($shopinfo['title_style']) {
            if (stripos($shopinfo['title_style'], "font-weight:") !== false) {
                $biaoti_bold = 'bold';
            } else {
                $biaoti_bold = '';
            }
            if (stripos($shopinfo['title_style'], "color:") !== false) {
                $biaoti_color = explode(';', $shopinfo['title_style']);
                $biaoti_color = explode(':', $biaoti_color[0]);
                $biaoti_color = $biaoti_color[1];
            } else {
                $biaoti_color = '';
            }
        } else {
            $biaoti_color = '';
            $biaoti_bold = '';
        }
        $shopinfo['picarr'] = unserialize($shopinfo['picarr']);
        if ($shopinfo['xsjx_time']) {
            $shopinfo['xsjx_time_1'] = date("Y-m-d", $shopinfo['xsjx_time']);
            $shopinfo['xsjx_time_h'] = $shopinfo['xsjx_time'] - strtotime($shopinfo['xsjx_time_1']);
            $shopinfo['xsjx_time'] = $shopinfo['xsjx_time_1'];
        } else {
            $shopinfo['xsjx_time'] = '';
            $shopinfo['xsjx_time_h'] = 79200;
        }
        $shopinfo['content'] = htmlspecialchars_decode($shopinfo['content']);
        $this->assign("pinpaiList", $pinpaiList);
        $this->assign("fenleishtml", $fenleishtml);
        $this->assign("shopinfo", $shopinfo);
        $this->assign("ment", $ment);
        $this->display("admin/shop.edit");
    }

    //期数列表
    public function qishu_list() {
        $ment = array(
            array("lists", "商品列表", C("URL_DOMAIN") . "/goods/goods_list"),
        );
        $shopid = intval(I("id", 0));
        $db_good = D("shangpin");
        $info = $db_good->where(array("id" => "$shopid"))->find();
        $num = 20;
        $zongji = $db_good->where(array("sid" => "{$info['sid']}"))->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");
        if ($fenyenum > $fenye->page) {
            $fenyenum = $fenye->page;
        }
        $qishu = $db_good->where(array("sid" => "{$info['sid']}"))->order("qishu DESC")->limit(($fenyenum - 1) * $num, $num)->select();
        $cateid = $qishu[0]['cateid'];
        $cate_name = D("fenlei")->where(array("cateid" => "$cateid"))->find();
        $cate_name = $cate_name['name'];
        $this->assign("zongji", $zongji);
        $this->assign("fenye", $fenye);
        $this->assign("qishu", $qishu);
        $this->assign("ment", $ment);
        $this->display("admin/qishu.list");
    }

    /* 单个商品的购买详细 */

    public function goods_go_one() {
        $yonghuid = intval(I("id", 0));
        $key = I("key", 0);
        $db = new \Think\Model;
        $ginfo = $db->table("yys_shangpin")->where(array("id" => "$yonghuid"))->find();
        if (!$ginfo)
            $this->note("没有找到这个商品");
        $zongji = $db->table("yys_yonghu_yys_record")->where(array("shopid" => "$yonghuid"))->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, 20, $fenyenum, "0");
        if (!$key) {
            $order = "b.id DESC";
        } else {
            $order = "b.gonumber DESC";
        }
        $go_list = $db->table("yys_yonghu a")->join("yys_yonghu_yys_record b on b.uid = a.uid")->where(array("b.shopid" => "$yonghuid"))->order($order)->limit(($fenyenum - 1) * 20, 20)->select();
        $this->assign("ginfo", $ginfo);
        $this->assign("fenye", $fenye);
        $this->assign("zongji", $zongji);
        $this->assign("go_list", $go_list);
        $this->display("admin/shop.go_list");
    }

    /* 手动揭晓	 */

    public function goods_one_ok() {
        $yonghuid = intval(I("id", 0));
        $db = new \Think\Model;
        $ginfo = $db->table("yys_shangpin")->where(array("id" => "$yonghuid"))->find();
        if (!$ginfo)
            $this->note("没有找到这个商品");
        $jinri_time = time();
        if ($ginfo['xsjx_time'] != '0')
            $this->note("限时揭晓商品不能手动揭晓");
        if ($ginfo['shenyurenshu'] != '0')
            $this->note("该商品还有剩余人数,不能手动揭晓！");
        if ($ginfo['shenyurenshu'] == '0' && (empty($ginfo['q_uid']) || $ginfo['q_uid'] == '')) {
            $db->startTrans();
            $ok = R('pay/pay_insert_shop', array($ginfo));
            if (!$ok) {
                $db->rollback();
                $this->note("揭晓失败!");
            } else {
                $db->commit();
                $this->note("揭晓成功!");
            }
        }
    }

    //商品回收站
    public function goods_del_list() {
        $ment = array(
            array("lists", "返回商品列表", C("URL_DOMAIN") . "goods/goods_list"),
            array("add", "添加商品", C("URL_DOMAIN") . "goods/goods_add"),
        );
        $num = 20;
        $zongji = D("shangpin_del")->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");
        $yyslist = D("shangpin_del")->limit(($fenyenum - 1) * $num, $num)->select();

        $this->assign("fenye", $fenye);
        $this->assign("yyslist", $yyslist);
        $this->assign("zongji", $zongji);
        $this->assign("ment", $ment);
        $this->display("admin/shop.del");
    }

    //揭晓商品ajax
    public function lottery_shop_json() {
        if (C('ssc')) {
            if (!isset($_GET['gid'])) {
                echo json_encode(array("error" => '1'));
                return;
                exit;
            }
            $yonghuid = trim($_GET['gid']);
            $times = (int) System::DOWN_sys_config('system', 'goods_end_time');
            if (!$times) {
                $times = 1;
            }

            $db = System::DOWN_sys_class('model');
            $yonghuid = $this->safe_replace($yonghuid);
            $yonghuid = $this->sstr_ireplace("select", "", $yonghuid);
            $yonghuid = $this->sstr_ireplace("union", "", $yonghuid);
            $yonghuid = $this->sstr_ireplace("'", "", $yonghuid);
            $yonghuid = $this->sstr_ireplace("%27", "", $yonghuid);
            $yonghuid = trim($yonghuid, ',');

            $infos = $db->Ylist("select  qishu,xsjx_time,id,zongrenshu,thumb,title,q_uid,q_user,q_user_code,q_end_time,q_end_cp,q_counttime,canyurenshu from `@#_shangpin` where `q_showtime` = 'Y' order by `q_end_time` ASC limit 0,10");
            $yid1 = str_replace('_', ',', $yonghuid);
            $yid2 = ltrim($yid1, ",");

            $yonghuid = @explode('_', $yonghuid);
            $info = false;
            foreach ($infos as $infov) {
                if (!in_array($infov['id'], $yonghuid)) {
                    $info = $infov;
                    break;
                } else {
                    if (empty($infov[q_end_cp])) {
                        $info = $infov;
                    }
                }
            }
            if ($info['xsjx_time']) {
                $info['q_end_time'] = $info['q_end_time'] + $times;
            }

            System::DOWN_sys_fun("user");
            $weer = unserialize($info['q_user']);
            $weer = huode_user_name($info['q_uid'], "username");
            $uid = $info['q_uid'];
            $upload = YYS_UPLOADS_PATH;

            $q_time = substr($info['q_end_time'], 0, 10);
            $times1 = (int) System::DOWN_sys_config('system', 'goods_end_time');

            if (time() >= ($q_time - $times1)) {
                if ($q_time < time()) {
                    if ($info['q_showtime'] == 'N' && !empty($info['q_end_cp']) && !empty($info['q_user_code'])) {

                        echo json_encode(array("error" => '-1'));
                        exit;
                    }
                    $db->Query("update `@#_shangpin` SET `q_showtime` = 'N' where `id` = '$info[id]' and `q_showtime` = 'Y' and `q_uid` is not null");
                }
                if (empty($info[q_end_cp])) {
                    $url = 'http://b1.yyygcms.cn/s1.php?stime=' . date('Y-m-d%20H:i:s', $q_time - $times1);
                    $s = file_get_contents($url);
                    $tt3 = @explode(",", $s);
                    if ($tt3['0'] && $tt3['1'] && $info['q_counttime'] && $info['canyurenshu']) {
                        $q_user_code = calc(($tt3['0'] + $info['q_counttime']), $info['canyurenshu'], 'mod') + 10000001;
                        $u_go_info = $db->YOne("select uid,id from `@#_yonghu_yys_record` where `shopid` = '$info[id]' and `shopqishu` = '$info[qishu]' and `goucode` LIKE  '%$q_user_code%'");
                        $huiyuan = $db->YOne("select * from `@#_yonghu` where `uid` = '$u_go_info[uid]'");
                        $q_user = serialize($huiyuan);
                        $db->tijiao_commit();

                        $g1 = $db->Query("UPDATE `@#_shangpin` SET `q_end_cp`='$tt3[0]',`q_uid`='$u_go_info[uid]',`q_end_qishu`='$tt3[1]',`q_user_code`='$q_user_code',`q_user`='$q_user' WHERE `id`='$info[id]'");
                        $g2 = $db->Query("UPDATE `@#_yonghu_yys_record` SET `huode` = '$q_user_code' where `id` = '$u_go_info[id]'");

                        if ($g1 && $$g2 && $tt3[0] && $u_go_info[uid] && $tt3[1] && $q_user_code && $q_user) {
                            $db->tijiao_commit();
                        } else {
                            $db->tijiao_rollback();
                        }
                    }
                }
            }
            $weer_shop_number = $db->YOne("select sum(gonumber) as gonumber from `@#_yonghu_yys_record` where `uid`= '$uid' and `shopid` = '$info[id]' and `shopqishu` = '$info[qishu]'");
            $weer_shop_number = $weer_shop_number['gonumber'];
            $times = $q_time - time();
            if ($info) {
                echo json_encode(array("error" => "0", "user_shop_number" => "$weer_shop_number", "user" => $weer, "zongrenshu" => $info['zongrenshu'], "q_user_code" => $info['q_user_code'], "qishu" => $info['qishu'], "upload" => $upload, "thumb" => $info['thumb'], "id" => $info['id'], "uid" => "$uid", "title" => $info['title'], "times" => $times));
                exit;
            } else {
                echo json_encode(array("error" => "1"));
                return;
                exit;
            }
        } else {
            if (!isset($_GET['gid'])) {
                echo json_encode(array("error" => '1'));
                return;
                exit;
            }
            $yonghuid = trim($_GET['gid']);
            $times = (int) C("goods_end_time");
            if (!$times) {
                $times = 1;
            }
            $db = new \Think\Model;
            $yonghuid = $this->safe_replace($yonghuid);
            $yonghuid = str_ireplace("select", "", $yonghuid);
            $yonghuid = str_ireplace("union", "", $yonghuid);
            $yonghuid = str_ireplace("'", "", $yonghuid);
            $yonghuid = str_ireplace("%27", "", $yonghuid);
            $yonghuid = trim($yonghuid, ',');
            if (!$yonghuid) {
                $info = $db->table("yys_shangpin")->where(array("q_showtime" => "Y"))->field("qishu,xsjx_time,id,zongrenshu,thumb,title,q_uid,q_user,q_user_code,q_end_time")->order("q_end_time")->find();
            } else {
                $infos = $db->table("yys_shangpin")->where(array("q_showtime" => "Y"))->field("qishu,xsjx_time,id,zongrenshu,thumb,title,q_uid,q_user,q_user_code,q_end_time")->order("q_end_time")->limit("0,5")->select();
                $yonghuid = @explode('_', $yonghuid);
                $info = false;
                foreach ($infos as $infov) {
                    if (!in_array($infov['id'], $yonghuid)) {
                        $info = $infov;
                        break;
                    }
                }
            }

            if (!$info) {
                echo json_encode(array("error" => '1'));
                return;
                exit;
            }

            if ($info['xsjx_time']) {
                $info['q_end_time'] = $info['q_end_time'] + $times;
            }
            $weer = unserialize($info['q_user']);
            $weer = $this->huode_user_name($info['q_uid'], "username");
            $uid = $info['q_uid'];
            $upload = __PUBLIC__ . "/uploads";

            $q_time = substr($info['q_end_time'], 0, 10);

            if ($q_time <= time()) {
                $db->table("yys_shangpin")->where("id = '{$info['id']}' and q_showtime = 'Y' and q_uid is not null")->save(array("q_showtime" => "N"));


                echo json_encode(array("error" => '-1'));
                return;
                exit;
            }
            $weer_shop_number = $db->table("yys_yonghu_yys_record")->where(array("uid" => $uid, "shopid" => $info['id'], "shopqishu" => $info['qishu']))->field("sum(gonumber) as gonumber")->find();
            $weer_shop_number = $weer_shop_number['gonumber'];
            $times = $q_time - time();
            echo json_encode(array("error" => "0", "user_shop_number" => "$weer_shop_number", "user" => "$weer", "zongrenshu" => $info['zongrenshu'], "q_user_code" => $info['q_user_code'], "qishu" => $info['qishu'], "upload" => $upload, "thumb" => $info['thumb'], "id" => $info['id'], "uid" => "$uid", "title" => $info['title'], "user" => $weer, "times" => $times));
            exit;
        }
    }

    //ajax
    public function lottery_shop_set() {
        if (C('ssc')) {
            if (isset($_POST['lottery_sub'])) {
                $db = System::DOWN_sys_class('model');
                $times = (int) System::DOWN_sys_config('system', 'goods_end_time');
                $yonghuid = isset($_POST['gid']) ? abs(intval($_POST['gid'])) : exit();
                $info = $db->YOne("select id,xsjx_time,thumb,title,q_uid,q_user,q_end_cp,q_end_time from `@#_shangpin` where `id` ='$yonghuid'");

                if (!$info) {
                    echo '0';
                    exit;
                }

                if (empty($info['q_end_time'])) {
                    echo '0';
                    exit;
                }

                if (empty($info['q_end_cp'])) {
                    echo '0';
                    exit;
                }
                if ($info['xsjx_time']) {
                    $info['q_end_time'] = $info['q_end_time'] + $times;
                }
                $times = str_ireplace(".", "", $info['q_end_time']);
                $q_time = substr($info['q_end_time'], 0, 10);
                $q = false;
                echo '0';
            }
        } else {
            if (isset($_POST['lottery_sub'])) {
                $times = (int) C("goods_end_time");
                $yonghuid = isset($_POST['gid']) ? abs(intval($_POST['gid'])) : exit();
                $info = D("shangpin")->where(array("id" => $yonghuid))->field("id,xsjx_time,thumb,title,q_uid,q_user,q_end_time")->find();
                if (!$info || empty($info['q_end_time'])) {
                    echo '0';
                    exit;
                }
                if ($info['xsjx_time']) {
                    $info['q_end_time'] = $info['q_end_time'] + $times;
                }
                $times = str_ireplace(".", "", $info['q_end_time']);
                $q_time = substr($info['q_end_time'], 0, 10);
                $q = false;
                if (time() >= $q_time) {
                    // 彩票不发送  $post_arr = array("uid" => $info['q_uid'], "gid" => $info['id'], "send" => 1);
                    // 彩票不发送 $this->g_YYSabcde(C("URL_DOMAIN") . "send/send_shop_code", false, $post_arr);
                    $q = D("shangpin")->where("id = '$yonghuid' and q_showtime = 'Y' and q_uid is not null")->save(array("q_showtime" => "N"));
                }
                if ($q)
                    echo '1';
                else
                    echo '0';
            }
        }
    }

    //ajax	
    public function lottery_shop_get() {

        if (C('ssc')) {
            if (isset($_POST['lottery_shop_get'])) {
                $db = System::DOWN_sys_class('model');
                $times = (int) System::DOWN_sys_config('system', 'goods_end_time');
                $yonghuid = isset($_POST['gid']) ? abs(intval($_POST['gid'])) : exit();
                $info = $db->YOne("select id,xsjx_time,q_end_time,q_end_cp from `@#_shangpin` where `id` ='$yonghuid' and `q_showtime` = 'Y'");
                if (!$info) {
                    echo "no";
                    exit;
                }
                if ($info['xsjx_time']) {
                    $info['q_end_time'] = $info['q_end_time'] + $times;
                }
                $q_time = intval(substr($info['q_end_time'], 0, 10));
                if (time() >= $q_time && !empty($info['q_end_cp'])) {
                    $db->Query("update `@#_shangpin` SET `q_showtime` = 'N' where `id` = '$info[id]' and `q_showtime` = 'Y' and `q_uid` is not null");
                }
                echo $q_time - time();
                exit;
            }
        } else {
            if (isset($_POST['lottery_shop_get'])) {


                $times = (int) C("goods_end_time");
                $yonghuid = isset($_POST['gid']) ? abs(intval($_POST['gid'])) : exit();
                $info = D("shangpin")->where(array("id" => $yonghuid))->field("id,xsjx_time,thumb,title,q_uid,q_user,q_end_time")->find();
                if (!$info || empty($info['q_end_time'])) {
                    echo '0';
                    exit;
                }
                if ($info['xsjx_time']) {
                    $info['q_end_time'] = $info['q_end_time'] + $times;
                }
                $times = str_ireplace(".", "", $info['q_end_time']);
                $q_time = substr($info['q_end_time'], 0, 10);
                $q = false;
                if (time() >= $q_time) {
                    $q = D("shangpin")->where("id = '$yonghuid' and q_showtime = 'Y' and q_uid is not null")->save(array("q_showtime" => "N"));
                }
                if ($q)
                    echo '1';
                else
                    echo '0';

                $times = (int) C("goods_end_time");
                $yonghuid = isset($_POST['gid']) ? abs(intval($_POST['gid'])) : exit();
                $info = D("shangpin")->where(array("id" => $yonghuid, "q_showtime" => "Y"))->field("id,xsjx_time,q_end_time")->find();
                if (!$info) {
                    echo "no";
                    exit;
                }
                if ($info['xsjx_time']) {
                    $info['q_end_time'] = $info['q_end_time'] + $times;
                }
                $q_time = intval(substr($info['q_end_time'], 0, 10));
                if ($q_time <= time()) {
                    $q = D("shangpin")->where("id = '$info[id]' and q_showtime = 'Y' and q_uid is not null")->save(array("q_showtime" => "N"));
                }
                echo $q_time - time();
                exit;
            }
        }
    }

    //即将揭晓商品
    public function show_msjxshop() {
        //暂时没做
        //即将揭晓商品
        $yyslist['listItems'][0]['codeid'] = 14;  //商品id
        $yyslist['listItems'][0]['period'] = 3;  //商品期数
        $yyslist['listItems'][0]['goodssnme'] = '苹果';  //商品名称
        $yyslist['listItems'][0]['seconds'] = 10;  //商品名称
        $yyslist['errorCode'] = 0;
    }

    //显示两分钟内 马上揭晓的商品
    public function GetStartRaffleAllList() {
        $fidx = I("fIdx", 0);
        $eidx = 10;
        $isCount = I("isCount", 0);
        //最新揭晓
        $tongtong = D("shangpin")->where("q_end_time is not null and q_showtime = 'Y'")->field("id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,fahuo")->order("q_end_time DESC")->select();
        if (empty($tongtong)) {
            $yyslist['code'] = 1;
        } else {
            $yyslist['code'] = 0;
            $yyslist['errorCode'] = 0;
            $yyslist['maxSeconds'] = time();
            $gtimes = (int) C("goods_end_time");
            $tt = microtime(true);
            $db_record = D("yonghu_yys_record");
            foreach ($tongtong as $key => $val) {
                //查询出购买次数
                $recodeinfo = $db_record->field("gonumber")->where(array("uid" => $val['q_uid'], "shopid" => $val['id']))->find();
                $yyslist['listItems'][$key]['codeid'] = $val['id'];
                $yyslist['listItems'][$key]['goodspic'] = $val['thumb'];
                $yyslist['listItems'][$key]['codeRTime'] = $this->microt($val['q_end_time'] - $gtimes);
                $yyslist['listItems'][$key]['codequantity'] = $val['zongrenshu'];
                $yyslist['listItems'][$key]['codesales'] = $val['canyurenshu'];
                $yyslist['listItems'][$key]['seconds'] = $val['q_end_time'] - $tt;
                $yyslist['listItems'][$key]['codetype'] = $recodeinfo['gonumber'];
                $yyslist['listItems'][$key]['goodssnme'] = $val['title'];
                $yyslist['listItems'][$key]['period'] = $val['qishu'];
                $yyslist['listItems'][$key]['price'] = $val['money'];
            }
        }
        echo json_encode($yyslist);
    }

    public function lottery_shop_set111() {
        if (isset($_POST['lottery_sub'])) {
            $times = (int) C("goods_end_time");
            $yonghuid = isset($_POST['gid']) ? abs(intval($_POST['gid'])) : exit();
            $ginfo = D("shangpin")->where(array("id" => $yonghuid))->find();
            if ($ginfo['q_showtime'] == 'Y') {
                exit;
            }
            $db->tijiao_start();
            $ok = R("pay/pay_insert_shop", array($ginfo));
            if (!$ok) {
                $db->tijiao_rollback();
                echo '0';
            } else {
                $db->tijiao_commit();
                echo '1';
            }
        }
    }

    public function GetBarcodernoInfo() {
        $_GET = array_change_key_case($_GET, CASE_LOWER);
        $id = htmlspecialchars($_GET[codeid]);
        $tongtong = D("shangpin")->where(array("id" => $id, "q_showtime" => 'Y'))->field("id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,fahuo")->find();
        if (empty($tongtong)) {
            $yyslist['code'] = 1;
        } else {
            $yyslist['code'] = 0;
            $yyslist['errorCode'] = 0;
            $yyslist['maxSeconds'] = time();
            $gtimes = (int) C("goods_end_time");
            $tt = microtime(true);
            //查询出购买次数
            $recodeinfo = D("yonghu_yys_record")->where(array("uid" => $tongtong['q_uid'], "shopid" => $tongtong['id']))->find();
            $yyslist['codePeriod'] = $tongtong['id'];
            $yyslist['codeRNO'] = $tongtong['q_user_code'];
            $yyslist['seconds'] = $tongtong['q_end_time'] - $tt;
            $yyslist['username'] = $recodeinfo['username'];
            $yyslist['codeRTime'] = $this->microt($tongtong['q_end_time'] - $gtimes);
            $yyslist['buyCount'] = $recodeinfo['gonumber'];
            $yyslist['buyip'] = $recodeinfo['ip'];
            $yyslist['buyipaddr'] = 5;
            $yyslist['buydevice'] = 6;
            $yyslist['buytime'] = $this->microt($recodeinfo['time']);
            $yyslist['price'] = $tongtong['money'];
            $yyslist['goodsName'] = $tongtong['title'];
            $yyslist['goodspic'] = $tongtong['thumb'];
            $yyslist['userphoto'] = $recodeinfo['uphoto'];
            $yyslist['userweb'] = $recodeinfo['uid'];
            $yyslist['codeType'] = $recodeinfo['gonumber'];
            $times123 = str_ireplace(".", "", $tongtong['q_end_time']);
            $q_time123 = substr($tongtong['q_end_time'], 0, 10);
            $q = false;
            if (time() >= $q_time123) {
                if (C('ssc')) {
                    if ($xiangmu['q_end_cp'] && $xiangmu['q_user_code'] && $xiangmu['q_user'] && $xiangmu['q_uid'] && $xiangmu['q_end_qishu']) {
                        $q = D("shangpin")->where("id = '$id' and q_showtime = 'Y' and q_uid is not null")->save(array("q_showtime" => 'N'));
                    }
                } else {
                    $q = D("shangpin")->where("id = '$id' and q_showtime = 'Y' and q_uid is not null")->save(array("q_showtime" => 'N'));
                }
            }
        }
        echo json_encode($yyslist);
    }

    public function more() {
        $key = "揭晓结果";
        $xiangmuid = I("goodsId");
        $xiangmu = D("shangpin")->where("id='$xiangmuid' and `q_end_time` is not null")->find();
        $xiangmulist = D("shangpin")->where(array("sid" => $xiangmu['sid']))->order("qishu DESC")->select();
        $fenlei = D("fenlei")->where(array("cateid" => $xiangmu['cateid']))->find();
        $pinpai = D("fenlei")->where(array("id" => $xiangmu['brandid']))->find();
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
            $wangqiqishu.='<li><a href="' . C("URL_DOMAIN") . '/goods/items/goodsId/' . $xiangmulist[0]['id'] . '">' . "第" . $xiangmulist[0]['qishu'] . "期</a><b></b></li>";
            array_shift($xiangmulist);
        }
        foreach ($xiangmulist as $qitem) {
            if ($qitem['id'] == $xiangmuid) {

                $wangqiqishu.='<li><a class="hover" href="javascript:;"><s class="fl"></s>' . "第" . $qitem['qishu'] . "期</a><b></b></li>";
            } else {
                $wangqiqishu.='<li><a href="' . C("URL_DOMAIN") . '/goods/dataserver/goodsId/' . $qitem['id'] . '" ><s class="fl"></s>第' . $qitem['qishu'] . '期</a></li>';
            }
        }
        //总一元云购次数
        $weer_shop_number = 0;
        //用户一元云购时间
        $weer_shop_time = 0;
        //得到一元云购码
        $weer_shop_codes = '';
        $weer_shop_list = D("yonghu_yys_record")->where(array("uid" => $xiangmu['q_uid'], "shopid" => $xiangmuid, "shopqishu" => $xiangmu['qishu']))->select();
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
        if (C('ssc')) {
            $weer_shop_fmod = calc(($tt2 + $xiangmu['q_counttime']), $xiangmu['canyurenshu'], 'mod');
        } else {
            $weer_shop_fmod = fmod($xiangmu['q_counttime'], $xiangmu['canyurenshu']);
        }
        if ($xiangmu['q_content']) {
            $xiangmu['q_content'] = unserialize($xiangmu['q_content']);
        }
        $xiangmu['picarr'] = unserialize($xiangmu['picarr']);

        //记录	 
        $gorecode = D("yonghu_yys_record")->where(array("uid" => $xiangmu['q_uid'], "shopid" => $xiangmuid, "shopqishu" => $xiangmu['qishu']))->order("id DESC")->LIMIT("6")->select();
        $shopitem = 'dataserverfun';
        $curtime = time();
        //晒单数
        $shopid = D("shangpin")->where(array("id" => $xiangmuid))->find();
        $yyslist = D("shangpin")->where(array("sid" => $shopid['sid']))->select();
        $shop = '';
        foreach ($yyslist as $list) {
            $shop.=$list['id'] . ',';
        }
        $id = trim($shop, ',');
        if ($id) {
            $shaidingdan = D("shai")->where("sd_shopid IN ($id)")->select();
            $sum = 0;
            foreach ($shaidingdan as $sd) {
                $shaidingdan_hueifu = D("shai_hueifu")->where(array("sdhf_id" => $sd['sd_id']))->select();
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
//        $this->assign("fenye", $fenye);
//        $this->assign("yyslist", $yyslist);
        $this->assign("xiangmuid", $xiangmuid);
        $this->display("mobile/index.more");
    }

    public function moreAjax() {
        $fidx = htmlspecialchars($_GET['FIdx']) - 1;
        $eidx = htmlspecialchars($_GET['EIdx']);
        $xiangmuid = htmlspecialchars($_GET['goodsID']);
//新加补丁

        $xiangmu = D("shangpin")->where(array("id" => $xiangmuid))->find();
        $xiangmuzx = D("shangpin")->where(array("sid" => $xiangmu['sid'], "qishu" => $xiangmu['qishu']))->order("qishu DESC")->find();
        //期数显示
        $xiangmulist = D("shangpin")->where(array("sid" => $xiangmu['sid']))->order("qishu DESC")->limit("$fidx,$eidx")->select();
        $count = D("shangpin")->where(array("sid" => $xiangmu['sid']))->count();
        if (empty($xiangmulist)) {
            $yyslist['Code'] = 1;
        } else {
            $yyslist['code'] = 0;
            $yyslist['count'] = count($count) - 1;
            $yyslist['minPeriod'] = $xiangmulist['0']['qishu'];
            $yyslist['newPeriod'] = $xiangmuzx['qishu'];
            $yyslist['newCodeID'] = $xiangmuzx['id'];
            foreach ($xiangmulist as $key => $val) {
                if (!empty($val['q_uid'])) {
                    $goods = D("yonghu_yys_record")->where(array("shopid" => $val['id'], "uid" => $val['q_uid']))->field("uid,gonumber,uphoto")->find();
                }
                if ($val['q_end_time'] != '' && $val['q_showtime'] != 'Y') {
                    //商品已揭晓
                    $yyslist['listItems'][$key]['codeState'] = 3;
                } elseif ($val['shenyurenshu'] == 0) {
                    //商品购买次数已满
                    $yyslist['listItems'][$key]['codeState'] = 2;
                } else {
                    //进行中
                    $yyslist['listItems'][$key]['codeState'] = 1;
                }
                $yyslist['listItems'][$key]['codeID'] = $val['id'];
                $yyslist['listItems'][$key]['userPhoto'] = $goods['uphoto'];
                $yyslist['listItems'][$key]['userName'] = $this->huode_user_name($val['q_uid']);
                $yyslist['listItems'][$key]['buyNum'] = $goods['gonumber'];
                $yyslist['listItems'][$key]['codeRNO'] = $val['q_user_code'];
                $yyslist['listItems'][$key]['codeRTime'] = $this->microt($val['q_end_time']);
                $yyslist['listItems'][$key]['userWeb'] = $goods['uid'];
                $yyslist['listItems'][$key]['codePeriod'] = $val['qishu'];
                $yyslist['listItems'][$key]['codeQuantity'] = $val['zongrenshu'];
                $yyslist['listItems'][$key]['codeSales'] = $val['canyurenshu'];
                $yyslist['listItems'][$key]['goodsPic'] = $val['thumb'];
            }
        }

        echo json_encode($yyslist);
    }

    //查看计算结果
    public function getGoodsPeriodInfo() {
        $xiangmuid = I("goodsID", 0);
        $qishu = I("period", 0);
        $xiangmu = D("shangpin")->where(array("id" => $xiangmuid))->field("id,sid,cateid,brandid,q_content")->find();
        $xiangmuzx = D("shangpin")->where(array("sid" => "{$xiangmu['sid']}", "qishu" => $qishu))->order("qishu DESC")->find();
        $yys['code'] = 0;
        $yys['codeID'] = $xiangmuzx['id'];
        echo json_encode($yys);
    }

    public function getCodeState() {
        $xiangmuid = I("codeID", 0);
        $xiangmu = D("shangpin")->where(array("id" => $xiangmuid, "q_showtime" => "Y"))->find();
        if (!$xiangmu) {
            $a['Code'] = 0;
        } else {
            $a['Code'] = 0;
            $times123 = str_ireplace(".", "", $xiangmu['q_end_time']);
            $q_time123 = substr($xiangmu['q_end_time'], 0, 10);
            $q = false;
            if (time() >= $q_time123) {
                if (C('ssc')) {
                    if ($xiangmu['q_end_cp'] && $xiangmu['q_user_code'] && $xiangmu['q_user'] && $xiangmu['q_uid'] && $xiangmu['q_end_qishu']) {
                        $q = D("shangpin")->where("id = '$xiangmuid' and q_showtime = 'Y' and q_uid is not null")->save(array("q_showtime" => "N"));
                    }
                } else {
                    $q = D("shangpin")->where("id = '$xiangmuid' and q_showtime = 'Y' and q_uid is not null")->save(array("q_showtime" => "N"));
                }
            }
            if ($q) {
                $a['State'] = 3;
            }
        }

        echo json_encode($a);
    }

    public function che() {
        $Mcartlist = $this->getShopCart();
        $shopids = '';
        if (is_array($Mcartlist)) {
            foreach ($Mcartlist as $key => $val) {
                $shopids.=intval($key) . ',';
            }
            $shopids = str_replace(',0', '', $shopids);
            $shopids = trim($shopids, ',');
        }
        $yyslist = array();
        if ($shopids != NULL) {
            $shops = D("shangpin")->where("id in($shopids)")->select();

            $shoparr = $this->key2key($shops, "id");
        }
        if (!empty($shoparr)) {
            foreach ($shoparr as $key => $val) {
                if ($val['q_end_time'] == '' || $val['q_end_time'] == NULL) {
                    $yyslist[$key] = $val;
                    $Mcartlist[$val['id']]['num'] = $Mcartlist[$val['id']]['num'];
                    $Mcartlist[$val['id']]['shenyu'] = $val['shenyurenshu'];
                    $Mcartlist[$val['id']]['money'] = $val['yunjiage'];
                }
            }
            cookie('Cartlist', json_encode($Mcartlist), '');
        }
        $MoenyCount = 0;
        $gouwucheshopinfo = '';
        if (count($yyslist) >= 1) {
            foreach ($Mcartlist as $key => $val) {
                $key = intval($key);
                if (isset($yyslist[$key])) {
                    $yyslist[$key]['cart_gorenci'] = $val['num'] ? $val['num'] : 1;
                    $MoenyCount+=$yyslist[$key]['yunjiage'] * $yyslist[$key]['cart_gorenci'];
                    $yyslist[$key]['cart_xiaoji'] = substr(sprintf("%.3f", $yyslist[$key]['yunjiage'] * $val['num']), 0, -1);
                    $yyslist[$key]['cart_shenyu'] = $yyslist[$key]['zongrenshu'] - $yyslist[$key]['canyurenshu'];
                    $gouwucheshopinfo = (array(
                        "gid" => $yyslist[$key][id],
                        "type" => "1",
                        "buyPeriod" => "1",
                        "times" => "1",
                        "online" => array("periodCurrent" => array($yyslist[$key]['qishu']),
                            "priceSell" => array("1"),
                            "priceTotal" => array("1"),
                            "showImages" => array("split" => array($yyslist[$key]['thumb']),
                    ))));

                    $uuuuu = json_encode($gouwucheshopinfo) . ',';
                    $ggggg.=$uuuuu;
                }
            }
        }
        $ggggg = rtrim($ggggg, ',');

        $sssss = '{"cartList": [' . json_encode($ggggg) . ']}';
        $tou = '{"cartList": [';
        $wei = ']}';
        $kkkkk = $tou . $ggggg . $wei;
        echo rtrim($kkkkk, ',');
    }

    public function che1() {//$id
        $Mcartlist = $this->getShopCart();
        $shopids = '';
        if (is_array($Mcartlist)) {
            foreach ($Mcartlist as $key => $val) {
                $shopids.=intval($key) . ',';
            }
            $shopids = str_replace(',0', '', $shopids);
            $shopids = trim($shopids, ',');
        }

        $yyslist = array();
        if ($shopids != NULL) {
            $shoparr = D("shangpin")->where("id in($shopids)")->select();
//            echo count($this->key2key($shoparr, "id"));
            echo count($shoparr);
        }
    }

    public function buy_count() {
        $fun = I("type");
        $info = D("shangpin")->find();
        $arr['state'] = success;
        $arr['count'] = $info['count'] ? $info['count'] + 100000000 : 100000000;
        $arr['fundTotal'] = $info['count'] ? $info['count'] / 100 : '1000010.00';
        $fun = explode('=', $fun);
        $fun = $fun[1];
        $fun = explode('&', $fun);
        $fun = $fun[0];
        echo $fun . '(' . json_encode($arr) . ')';
        die;
    }

    public function BuyDetail() {
        $this->display("mobile/BuyDetail");
    }

    public function getUserBuyGoodsCodeInfo() {
        $huiyuan = $this->userinfo;
        $xiangmuid = $_GET[hidCodeRno1];
        $uid = $_GET[codeID];
        // var_dump($_GET);
        $yy = D("yonghu_yys_record a")->join("yys_shangpin b on a.shopid=b.id")->field("*,a.time as timego")->where(array("a.uid" => $uid, "b.id" => $xiangmuid))->group("a.id")->order("a.time DESC")->select();

        if ($yy) {
            $yys[code] = 0;
        }
        foreach ($yy as $key => $val) {
            $yys[data][$key][buyTime] = $this->microt($val[timego]);
            $yys[data][$key][rnoNum] = $val[goucode];
        }
        //var_dump($yys);
        echo json_encode($yys);
    }

}
