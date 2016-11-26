<?php

/**
 * PC
 * addtime 2016/05/03
 */

namespace Duipi\Controller;

use Think\Controller;

class IndexController extends BaseController {

    public function _initialize() {
        $this->userinfo = $this->getUserInfo();
        if (ismobile()) {
            $url = $_SERVER["REQUEST_SCHEME"] . '://' . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
            $url1 = str_replace("/index/", "/mobile/", $url);
            $str = explode("/", $_SERVER["REQUEST_URI"]);
            // $url = str_replace("//index/", "/mobile/", $url);
            if ($url1 != $url || count($str) >= 5) {
                header("location: " . $url1);
            } else {
                header("location: " . C("URL_DOMAIN") . "mobile/index");
            }
        } else {
            $this->assign("huiyuan", $this->userinfo);
        }
    }

    public function index() {
        //晒单分享
        $shaidingdan = D("shai")->where(array("shenhe" => 1))->order("`sd_id`")->limit("6")->select();
        $remen = D("shangpin")->where(array("q_uid is null and pos = '1'"))->field("id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,fahuo")->order("canyurenshu DESC")->LIMIT("8")->select();
        $slides = D("flash")->where(array("pid" => 0))->order("`sort`")->select();
        $this->assign("isindex", 'Y');
        $this->assign("slides", $slides);
        $this->assign("shaidingdan", $shaidingdan);
        $this->assign("remen", $remen);
        $this->display("index/index.index");
    }

    public function video() {
        $this->display("index/index.video");
    }

    public function about() {
        $this->display("single_web.newbie");
    }

    public function group_qq() {
        $lists = D("qqshezhi")->select();
        $lists1 = array();
        $lists2 = array();

        $biaoti = "官方QQ群" . C("web_name");
        if (!empty($lists)) {
            foreach ($lists as $key => $val) {
                if ($val['type'] == '地方群') {
                    $lists1[$key] = $val;
                } else {
                    $lists2[$key] = $val;
                }
            }
        }
        $this->assign("lists1", $lists1);
        $this->assign("lists2", $lists2);
        $this->assign("lists", $lists);
        $this->display("index.qq");
    }

    //ajax 获取地方代理的qq群信息 
    //获取市/县
    public function get_cityqq() {
        $prov = urldecode(trim(I("prov", 0)));
        $cityv = urldecode(trim(I("cityv", 0)));
        $couv = urldecode(trim(I("couv", 0)));
        $prov = $this->safe_replace($prov);
        $cityv = $this->safe_replace($cityv);
        $couv = $this->safe_replace($couv);

        if (!$this->is_utf8($prov)) {
            $prov = iconv("GBK", "UTF-8", $prov);
        }
        if (!$this->is_utf8($cityv)) {
            $cityv = iconv("GBK", "UTF-8", $cityv);
        }
        if (!$this->is_utf8($couv)) {
            $couv = iconv("GBK", "UTF-8", $couv);
        }

        $str = '----请选择----';
        if ($prov != $str && $cityv == $str && $couv == $str) {
            $res = D("qqshezhi")->where(array("province" => $prov))->select();
        }
        if ($prov != $str && $cityv != $str && $couv == $str) {
            $res = D("qqshezhi")->where(array("province" => $prov, "city" => $cityv))->select();
        }
        if ($prov != $str && $cityv != $str && $couv != $str) {
            $res = D("qqshezhi")->where(array("province" => $prov, "city" => $cityv, "county" => $couv))->select();
        }
        if (!empty($res)) {
            $str = '<ul>';
            foreach ($res as $v) {
                $str.='<li><dt><img border="0" alt="' . $v['name'] . '" src="' . YYS_TEMPLATES_IMAGE . '/images/logo.jpg"></dt><dt>' . $v['name'];
                if ($v['full'] == '已满') {
                    $str.='<img src="' . YYS_TEMPLATES_IMAGE . '/qqhot.gif"/>';
                }
                $str.='</dt><dd><a href="' . $v['qqurl'] . '">' . $v['qq'] . '</a></dd></li>';
            }
            $str.='</ul>';
        } else {
            $youjian = C("user");
            $str = "<div class='nothing'>该地区暂无QQ群加盟，" . C("web_name_two") . "诚邀您加盟，详情请咨询Email:<a href='mailto:" . $youjian . "' target='_blank' >" . $youjian . "</div>";
        }

        echo $str;
    }

    public function s_tag() {
        $search = I("val", "");
        // $search = implode('/', $search);
        if (!$search) {
            $this->note("输入搜索关键字");
        }
        $search = urldecode($search);
        $search = $this->safe_replace($search);
        if (!$this->is_utf8($search)) {
            $search = iconv("GBK", "UTF-8", $search);
        }
        $search = str_ireplace("union", '', $search);
        $search = str_ireplace("select", '', $search);
        $search = str_ireplace("delete", '', $search);
        $search = str_ireplace("update", '', $search);
        $search = str_ireplace("/**/", '', $search);

        $biaoti = $search . ' - ' . C('web_name');
        $yyslist = D("shangpin")->where("title LIKE '%" . $search . "%' and shenyurenshu>0")->field("title,thumb,id,sid,zongrenshu,canyurenshu,shenyurenshu,money")->select();
        $list = count($yyslist);

        $this->assign("yyslist", $yyslist);
        $this->assign("list", $list);
        $this->assign("search", $search);
        $this->display("search.search");
    }

    public function show() {
        $wenzhangid = I("d");
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
        $this->display("$v");
    }

//晒单分享
    public function shaidan() {
        $biaoti = "晒单分享";
        $num = 40;
        $zongji = D("shai")->where(array("shenhe" => "1"))->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");
        $shaidingdan = D("shai")->where(array("shenhe" => "1"))->order("sd_id DESC")->limit(($fenyenum - 1) * $num, $num)->select();

        $lie = 4;
        $sum = $num;
        $yushu = $zongji % $num;
        $yeshu = floor($zongji / $num) + 1;
        if ($yushu > 0 && $yeshu == $fenyenum) {
            $sum = $yushu;
        }
        $sa_one = array();
        $sa_two = array();
        $sa_tree = array();
        $sa_for = array();

        foreach ($shaidingdan as $sk => $sv) {
            $shaidingdan[$sk]['sd_title'] = $this->htmtguolv($shaidingdan[$sk]['sd_title']);
        }
        if ($shaidingdan) {
            for ($i = 0; $i < $lie; $i++) {
                $n = $i;
                while ($n < $sum) {
                    if ($i == 0) {
                        $sa_one[] = $shaidingdan[$n];
                    } else if ($i == 1) {
                        $sa_two[] = $shaidingdan[$n];
                    } else if ($i == 2) {
                        $sa_tree[] = $shaidingdan[$n];
                    } else if ($i == 3) {
                        $sa_for[] = $shaidingdan[$n];
                    }
                    $n+=$lie;
                }
            }
        }
        $this->assign("shaidingdan", $shaidingdan);
        $this->assign("zongji", $zongji);
        $this->assign("fenye", $fenye);
        $this->display("index.shaidan");
    }

    public function detail() {
        $huiyuan = $this->userinfo;
        $sd_id = I("id");
        $shaidingdan = D("shai")->where(array("sd_id" => $sd_id))->find();
        $shangpinss = D("shangpin")->where(array("id" => $shaidingdan['sd_shopid']))->find();
        $shangpinss = D("shangpin")->where(array("sid" => $shangpinss['sid']))->order("qishu DESC")->find();
        if (isset($_POST['submit'])) {
            $sdhf_syzm = cookie("checkcode");
            $sdhf_pyzm = isset($_POST['sdhf_code']) ? strtoupper($_POST['sdhf_code']) : '';
            $sdhf_id = $shaidingdan['sd_id'];
            $sdhf_userid = $huiyuan['uid'];
            $sdhf_content = $_POST['sdhf_content'];
            $sdhf_time = time();
            $sdhf_username = $this->htmtguolv($this->huode_user_name($huiyuan));
            $sdhf_img = $this->htmtguolv($huiyuan['img']);
            if (empty($sdhf_content)) {
                $this->note("页面错误");
            }
            if (empty($sdhf_pyzm)) {
                $this->note("请输入验证码");
            }
            if ($sdhf_syzm != md5($sdhf_pyzm)) {
                $this->note("验证码不正确");
            }
            $data = array("sdhf_id" => $sdhf_id, "sdhf_userid" => $sdhf_userid, "sdhf_content" => $sdhf_content, "sdhf_time" => $sdhf_time, "sdhf_username" => $sdhf_username, "sdhf_img" => $sdhf_img);
            D("shai_hueifu")->add($data);

            $sd_ping = $shaidingdan['sd_ping'] + 1;
            D("shai")->where(array("sd_id" => $shaidingdan["sd_id"]))->save(array("sd_ping" => $sd_ping));
            $this->note("评论成功", C("URL_DOMAIN") . "/index/detail/id/" . $sd_id);
        }
        $shaidingdannew = D("shai")->order("sd_id DESC")->limit("5")->select();
        $shaidingdan_hueifu = D("shai_hueifu")->where(array("sdhf_id" => $sd_id))->limit("10")->select();

        foreach ($shaidingdan_hueifu as $k => $v) {
            $shaidingdan_hueifu[$k]['sdhf_content'] = $this->htmtguolv($shaidingdan_hueifu[$k]['sdhf_content']);
        }
        if (!$shaidingdan) {
            $this->note("页面错误");
        }
        $substr = substr($shaidingdan['sd_photolist'], 0, -1);
        $sd_photolist = explode(";", $substr);

        $biaoti = $shaidingdan['sd_title'] . "_" . C("web_name");
        $guanjianzi = $shaidingdan['sd_title'];
        $miaoshu = $shaidingdan['sd_title'];

        $this->assign("shaidingdan", $shaidingdan);
        $this->assign("shangpinss", $shangpinss);
        $this->assign("sd_photolist", $sd_photolist);
        $this->assign("shaidingdan_hueifu", $shaidingdan_hueifu);
        $this->assign("shaidingdannew", $shaidingdannew);
        $this->assign("sd_id", $sd_id);
        $this->display("index.detail");
    }

    //羡慕嫉妒恨
    public function xianmu() {
        $sd_id = I("id", 0);
        D("shai")->where(array("sd_id" => $sd_id))->setInc('sd_zhan', 1);
        $shaidingdan = D("shai")->where(array("sd_id" => $sd_id))->find();
        echo $shaidingdan['sd_zhan'];
    }

    /* 商品晒单列表ifram 暂时没用 */

    public function itmeifram() {
        $xiangmuid = safe_replace($this->segment(4));
        $xiangmu = $this->db->YOne("select id,sid,qishu from `@#_shangpin` where `id`='$xiangmuid' LIMIT 1");
        if (!$xiangmu) {
            $error = 1;
        } else {
            $error = 0;
            $fenye = System::DOWN_sys_class('page');
            $zongji = $this->db->YCount("select id from `@#_shai` where `sd_shopsid`='$xiangmu[sid]'");
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
            $shaidingdan = $this->db->YPage("select * from `@#_shai` where `sd_shopsid` = '$xiangmu[sid]' order by sd_id  DESC", array("num" => $num, "page" => $fenyenum, "type" => 1, "cache" => 0));

            foreach ($shaidingdan as $key => $val) {
                $huiyuan_info = $this->db->YOne("select * from `@#_yonghu` where `uid`='$val[sd_userid]'");
                $huiyuan_img[$val['sd_id']] = $huiyuan_info['img'];
                $huiyuan_id[$val['sd_id']] = $huiyuan_info['uid'];
                $huiyuan_username[$val['sd_id']] = $huiyuan_info['username'];
            }
        }
        include templates("index", "itemifram");
    }

    public function lottery() {
        //最新揭晓
        $fenye = new \Claduipi\Tools\page;
        if (C('ssc')) {
            $zongji = D("shangpin")->where("q_uid is not null and q_end_cp<>'' and q_showtime = 'N'")->field("id")->count();
        } else {
            $zongji = D("shangpin")->where("q_uid is not null and q_showtime = 'N'")->field("id")->count();
        }
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $num = 24;
        $fenye->config($zongji, $num, $fenyenum, "0");
        if ($_GET[type] == 'y') {
            $yysqishu = D("shangpin")->where("q_uid is not null and q_showtime = 'N'")->order("q_end_time DESC")->limit(($fenyenum - 1) * $num, $num)->select();
        } else if ($_GET[type] == 'd') {
            //$yysqishu = D("shangpin")->where("q_uid is not null and q_showtime = 'Y'")->order("q_end_time DESC")->limit(($fenyenum - 1) * $num, $num)->select();
            //$yysqishu=$this->db->YPage("select * from `@#_shangpin` where `q_uid` is not null and `q_showtime` = 'Y' ORDER BY `q_end_time` DESC",array("num"=>$num,"page"=>$fenyenum,"type"=>1,"cache"=>0));
        } else {
            $yysqishu = D("shangpin")->where("q_uid is not null and q_showtime = 'N'")->order("q_end_time DESC")->limit(($fenyenum - 1) * $num, $num)->select();
        }
        $yyslistji = D("shangpin")->where("q_uid is null and shenyurenshu != '0'")->order("shenyurenshu ASC")->limit(9)->select();

        $huiyuan_record = D("yonghu_yys_record")->order("id DESC")->limit("6")->select();
        $this->assign("huiyuan_record", $huiyuan_record);
        $this->assign("yysqishu", $yysqishu);
        $this->assign("fenye", $fenye);

        $this->assign("yyslistji", $yyslistji);
        $this->assign("zongji", $zongji);

        $this->display("index.lottery");
    }

    public function pleasereg() {
        $this->display("pleasereg");
    }

    public function ers() {
        $imgs = array(
            'dst' => __PUBLIC__ . C('web_logo2'),
            'src' => C("URL_DOMAIN") . 'Tools/erweimatou'
        );
        mergerImg($imgs);
    }

    public function getBuyCode() {
        $gid = I("gid");
        $shop = D("shangpin")->where(array("id" => $gid))->find();
        $ggs = D("yonghu_yys_record")->where(array("uid" => $shop['q_uid'], "shopid" => $gid, "shopqishu" => $shop['qishu']))->select();
        foreach ($ggs as $key => $val) {
            $vvs.=$val['goucode'] . ',';
        }
        $newstrss = substr($vvs, 0, strlen($vvs) - 1);
        $list['codes']["buyCodes"] = $newstrss;
        $list['status'] = true;
        echo json_encode($list);
    }

    public function map() {
        $this->display("index.map");
    }

    //一元云购历史记录
    public function buyrecord() {
        $this_time_h = date("H");
        $this_time_i = date("i");
        if (isset($_POST['dosubmit'])) {
            $start_time = $_POST['start_time_data'] . ' ' . $_POST['start_time_h'] . ':' . $_POST['start_time_i'] . ':00';
            $end_time = $_POST['end_time_data'] . ' ' . $_POST['end_time_h'] . ':' . $_POST['end_time_i'] . ':00';

            $start_time = strtotime($start_time);
            $end_time = strtotime($end_time);
            if (strlen($start_time) != 10 && strlen($end_time) != 10) {
                $this->note(L('web_tip_parameter'));
            }
            if ($end_time < $start_time) {
                $this->note(L('web_tip_time'));
            }
            if (($end_time - 7200) > $start_time) {
                $this->note(L('web_exceed_time'));
            }
            $start_time.='.000';
            $end_time .='.000';
            $RecordList = D("yonghu_yys_record")->where("time > '$start_time' and time < '$end_time'")->field("username,uid,shopid,shopname,shopqishu,gonumber,time")->limit("0,20")->select();
        } else {
            $time = time();
            $start_time = ($time - 7200) . '.000';
            $end_time = $time . '.000';
            $RecordList = D("yonghu_yys_record")->where("time > '$start_time' and time < '$end_time'")->field("username,uid,shopid,shopname,shopqishu,gonumber,time")->limit("0,20")->select();
        }
        $this->assign("time", date("Y-m-d"));
        $this->assign("this_time_h", $this_time_h);
        $this->assign("this_time_i", $this_time_i);
        $this->assign("RecordList", $RecordList);
        $this->display("index.buyrecord");
    }

    public function buyrecordbai() {
        $res = D("yonghu_yys_record")->field("sum(gonumber) gonumber")->find();
        D("linshi")->where(array("key" => "goods_count_num"))->save(array("value" => $res['gonumber']));
        $RecordList = D("yonghu_yys_record")->field("username,uid,shopid,shopname,shopqishu,gonumber,time")->order("id desc")->limit("0,100")->select();
        $this->assign("RecordList", $RecordList);
        $this->assign("res", $res);
        $this->display("index.buyrecordbai");
    }

    public function business() {
        $this->display("single_web.business");
    }

    //选购获取幸运号码
    public function getChoose() {
        $shop = D("shangpin")->where(array("id"=>I("gid")))->find();
        $ggs = D("{$shop['codes_table']}")->where(array("s_id"=>$shop['id']))->field("s_codes")->select();
        $code = array();
        foreach ($ggs as $key => $value) {
            $code = array_merge($code, unserialize($value['s_codes']));
        }
        sort($code);
        $list['status'] = true;
        $list['codes'] = $code;
        echo json_encode($list);
    }

}

?>