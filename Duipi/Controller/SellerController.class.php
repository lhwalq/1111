<?php

/**
 * 用户
 * addtime 2016/02/18
 */

namespace Duipi\Controller;

use Think\Controller;

class SellerController extends BaseController {

    public function _initialize() {
        $this->getAdminRight(CONTROLLER_NAME, ACTION_NAME);
        $this->userinfo = $this->getUserInfo();
        if ($this->userinfo) {
            $this->assign("huiyuan", $this->userinfo);
        }
    }

    //手机商户入住
    public function mseller_register() {
        if ($this->userinfo['type'] == 1) {
            $this->notemobile("恭喜您,您已成功成为商家!", C('URL_DOMAIN') . "user/home/");
        }
        $user = $this->userinfo;
        if (isset($_POST['submit'])) {
            if (isset($_FILES['image'])) {
                upload::upload_config(array('png', 'jpg', 'jpeg', 'gif'), 500000, 'sellerimg');
                upload::go_upload($_FILES['image']);
                if (!upload::$ok) {
                    $this->notemobile(upload::$error);
                }
                $logo = "sellerimg/" . upload::$filedir . "/" . upload::$filename;
            }
            if (empty($logo)) {
                $this->notemobile("图片不能为空");
                exit;
            }
            $res = D("yonghu")->where(array('uid' => $this->userinfo['uid']))->save(array('seller_img' => $logo));
            if ($res) {
                $this->notemobile("上传成功,请等待管理员审核", C('URL_DOMAIN') . "user/home/");
            }
            $this->notemobile("上传失败,请稍后重试", C('URL_DOMAIN') . "user/home/");
        }
        $this->assign("user", $user);
        $this->display("mobile/seller.add");
        // include templates("mobile/seller", "add");
    }

    //商户入驻申请
    public function seller_register() {
        if (!$this->userinfo) {
            $this->autoNote("请先登录", C("URL_DOMAIN") . "user/login");
        }
        if ($this->userinfo['type'] == 1) {
            $this->note("恭喜您,您已成功成为商家!", C('URL_DOMAIN') . "user/home/");
        }
        $user = $this->userinfo;
        if (isset($_POST['dosubmit'])) {
            if (!ismobile()) {
                $thumb = trim(htmlspecialchars($_POST['thumb']));
                $res = D("yonghu")->where(array('uid' => $this->userinfo['uid']))->save(array('seller_img' => $thumb));
            } else {
                if (isset($_FILES['image'])) {
                    $upload = new \Claduipi\Tools\upload;
                    $upload->upload_config(array('png', 'jpg', 'jpeg', 'gif'), 500000, 'sellerimg');
                    $upload->go_upload($_FILES['image']);
                    if (!$upload->ok) {
                        $this->notemobile($upload->error);
                    }
                    $logo = "sellerimg/" . $upload->filedir . "/" . $upload->filename;
                }
                if (empty($logo)) {
                    $this->notemobile("图片不能为空");
                    exit;
                }
                $res = D("yonghu")->where(array('uid' => $this->userinfo['uid']))->save(array('seller_img' => $logo));
            }
            if ($res) {
                $this->autoNote("上传成功,请等待管理员审核", C('URL_DOMAIN') . "user/home/");
            }
            $this->autoNote("上传失败,请稍后重试", C('URL_DOMAIN') . "user/home/");
        }
        $this->assign("user", $user);
        $this->autoShow("seller.add");
    }

    //商户审核
    public function seller_audit() {
        $ment = array(
            array("lists", "会员列表", C("URL_DOMAIN") . "member/lists"),
            //       array("lists", "查找会员", C("URL_DOMAIN") . "user/select"),
            array("insert", "添加会员", C("URL_DOMAIN") . "member/insert"),
            array("insert", "会员配置", C("URL_DOMAIN") . "config/userconfig"),
            array("insert", "会员福利配置", C("URL_DOMAIN") . "config/member_fufen"),
            array("insert", "充值记录", C("URL_DOMAIN") . "order/recharge"),
            array("insert", "机器人头像设置", C("URL_DOMAIN") . "member/touxiang"),
        );
        $fenye = new \Claduipi\Tools\page;
        $table = "@#_yonghu";
        $sql_where = "seller_img is not NULL and type=0";
        $num = 20;
        $zongji = D("yonghu")->where($sql_where)->count();
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");
        $huiyuans = D("yonghu")->where($sql_where)->limit(($fenyenum - 1) * 20, 10)->select();
        $this->assign("zongji", $zongji);
        $this->assign("huiyuans", $huiyuans);
        $this->assign("fenye", $fenye);
        $this->assign("ment", $ment);
        $this->display("admin/member.audit_lists");
    }

//已经审核商户
    public function seller_auditok() {
        $ment = array(
            array("lists", "会员列表", C("URL_DOMAIN") . "member/lists"),
            //       array("lists", "查找会员", C("URL_DOMAIN") . "user/select"),
            array("insert", "添加会员", C("URL_DOMAIN") . "member/insert"),
            array("insert", "会员配置", C("URL_DOMAIN") . "config/userconfig"),
            array("insert", "会员福利配置", C("URL_DOMAIN") . "config/member_fufen"),
            array("insert", "充值记录", C("URL_DOMAIN") . "order/recharge"),
            array("insert", "机器人头像设置", C("URL_DOMAIN") . "member/touxiang"),
        );
        $table = "@#_yonghu";
        $sql_where = "seller_img is not NULL and type=1";
        $num = 20;
        $zongji = D("yonghu")->where($sql_where)->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");

        $huiyuans = D("yonghu")->where($sql_where)->limit(($fenyenum - 1) * 20, 10)->select();
        $this->assign("zongji", $zongji);
        $this->assign("huiyuans", $huiyuans);
        $this->assign("fenye", $fenye);
        $this->assign("ment", $ment);

        $this->display("admin/member.audit_listsok");
    }

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

    /**
     * 商户添加商品
     */
    public function goods_add() {
        $huiyuan = $this->getUserInfo();
        if ($huiyuan['type'] != 1) {
            $this->note("没有权限发布", "__ROOT__/user/home");
        }
        $db = new \Think\Model;
        if (IS_POST) {
            $cateid = intval(I("cateid", 0));
            $pinpaiid = intval(I("brand", 0));
            $biaoti = $this->htmtguolv(I("title", ""));
            $biaoti_color = htmlspecialchars(I("title_style_color", ""));
            $biaoti_bold = htmlspecialchars(I("title_style_bold", ""));
            $biaoti2 = $this->htmtguolv(I("title2", ""));
            $guanjianzi = htmlspecialchars(I("keywords", ""));
            $miaoshu = I("description", "");
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
            $cardId1 = mysql_escape_string(I("cardId1", ""));
            $cardId2 = mysql_escape_string(I("cardId2", ""));
            $cardId = mysql_escape_string(I("cardId", ""));
            $cardPwd = mysql_escape_string(I("cardPwd", ""));
           
            if (I("uppicarr", "")) {
                $picarr = serialize(I("uppicarr", ""));
            } else {
                $picarr = serialize(array());
            }

            if (ismobile()) {
                //手机端传输方式不同
                if ($_FILES['thumb']['name']) {
                    $upload = new \Claduipi\Tools\upload;
                    $upload->upload_config(array('png', 'jpg', 'jpeg', 'gif'), 500000, 'shopimg');
                    $upload->go_upload($_FILES['thumb']);
                    if (!$upload->ok) {
                        $this->notemobile($upload->error);
                    }
                    $thumb = "shopimg/" . $upload->filedir . "/" . $upload->filename;
                }

                $uppicarr = array();
                if ($_FILES['uppicarr1']['name']) {
                    $upload = new \Claduipi\Tools\upload;
                    $upload->upload_config(array('png', 'jpg', 'jpeg', 'gif'), 500000, 'shopimg');
                    $upload->go_upload($_FILES['uppicarr1']);
                    if (!$upload->ok) {
                        $this->notemobile($upload->error);
                    }
                    $thumb = "shopimg/" . $upload->filedir . "/" . $upload->filename;
                    array_push($uppicarr, $thumb);
                }
                if ($_FILES['uppicarr2']['name']) {
                    $upload = new \Claduipi\Tools\upload;
                    $upload->upload_config(array('png', 'jpg', 'jpeg', 'gif'), 500000, 'shopimg');
                    $upload->go_upload($_FILES['uppicarr2']);
                    if (!$upload->ok) {
                        $this->notemobile($upload->error);
                    }
                    $thumb = "shopimg/" . $upload->filedir . "/" . $upload->filename;
                    array_push($uppicarr, $thumb);
                }
                if ($_FILES['uppicarr3']['name']) {
                    $upload = new \Claduipi\Tools\upload;
                    $upload->upload_config(array('png', 'jpg', 'jpeg', 'gif'), 500000, 'shopimg');
                    $upload->go_upload($_FILES['uppicarr3']);
                    if (!$upload->ok) {
                        $this->notemobile($upload->error);
                    }
                    $thumb = "shopimg/" . $upload->filedir . "/" . $upload->filename;
                    array_push($uppicarr, $thumb);
                }
                //dump($uppicarr);exit;
                if ($uppicarr) {
                    $picarr = serialize($uppicarr);
                } else {
                    $picarr = serialize(array());
                }
            }


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
            $arr = array("ka" => htmlspecialchars(I('kahao')), "mi" => htmlspecialchars($_POST['mima']), "cateid" => "$cateid", "brandid" => "$pinpaiid", "title" => "$biaoti", "title_style" => "$biaoti_style", "title2" => "$biaoti2", "keywords" => "$guanjianzi", "description" => "$miaoshu", "money" => "$money", "yunjiage" => "$yunjiage", "zongrenshu" => "$zongrenshu", "canyurenshu" => "$canyurenshu", "shenyurenshu" => "$shenyurenshu", "qishu" => "1", "maxqishu" => "$maxqishu", "thumb" => "$thumb", "picarr" => "$picarr", "content" => "$content", "xsjx_time" => "$xsjx_time", "renqi" => "$shangpinss_key_renqi", "renqi1" => "$shangpinss_key_renqi1", "pos" => "$shangpinss_key_pos", "time" => "$time", "cardId" => "$cardId", "cardId1" => "$cardId1", "cardPwd" => "$cardPwd", "leixing" => "$goods_key_leixing", "yuanjia" => "$cardId2", "seller_id" => "{$huiyuan['uid']}", "status" => "1");



            $query_1 = D("shangpin_audit")->add($arr);
            $shopid = $query_1;
            $query_table = $this->content_get_codes_table();
            if (!$query_table) {
                $db->rollback();
                $this->note("云购码仓库不正确!");
            }
            $query_2 = R("Goods/content_huode_go_codes", array($zongrenshu, 3000, $shopid));
            $query_3 = D("shangpin_audit")->where(array("id" => $shopid))->save(array("codes_table" => "$query_table", "sid" => "$shopid", "def_renshu" => "$canyurenshu"));

            if ($query_1 && $query_2 && $query_3) {
                $db->commit();
                $this->autoNote("商品添加成功!");
            } else {
                $db->rollback();
                $this->autoNote("商品添加失败!");
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
        $this->autoShow("sellershop.insert");
    }

    //手机AJAX
    public function getSellerShopList() {
        if ($this->userinfo['type'] != 1) {
            exit;
        }
        $FIdx = I("FIdx") - 1;
        $EIdx = 10;
        $type = I("Type");
        $table = "shangpin a";
        $list_where = array("a.seller_id" => $this->userinfo['uid'], "a.status" => 0);
        if ($type) {
            $table = "shangpin_audit a";
            $list_where = array("a.seller_id" => $this->userinfo['uid'], "a.status" => 1);
        }
        $yyslist['code'] = 0;
        $yyslist["listItems"] = D($table)->join("left join yys_fenlei b on b.cateid=a.cateid")->where($list_where)->field("a.*,b.name as catename")->order("id DESC")->limit($FIdx, $EIdx)->select();
        if ($yyslist['listItems']) {
            $yyslist['code'] = 1;
        }
        $yyslist['count'] = count($yyslist['listItems']);
        echo json_encode($yyslist);
        exit;
    }

    //会员商品列表
    public function goods_list() {
        $fenye = new \Claduipi\Tools\page;
        $type = abs(I("type", 0));
        $this->assign("type", $type);
        if (ismobile()) {
            $this->display("mobile/sellershop.lists");
            exit;
        }
        $categorys = D("fenlei")->where(array('key' => 'cateid'))->order('parentid ASC')->select();
        $categorys = $this->key2key($categorys, 'cateid');
        if ($this->userinfo['type'] != 1) {
            $this->note("无权限操作!", C("URL_DOMAIN") . "user/home");
            exit;
        }

        $table = "shangpin";
        $list_where = array('seller_id' => $this->userinfo['uid'], 'status' => 0);
        if ($type) {
            $table = "shangpin_audit";
            $list_where = array('seller_id' => $this->userinfo['uid'], 'status' => 1);
        }
        $num = 20;
        $zongji = D($table)->where($list_where)->count();

        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");
        $yyslist = D($table)->where($list_where)->order('id DESC')->limit(($fenyenum - 1) * 20, 10)->select();
        $this->assign("categorys", $categorys);
        $this->assign("yyslist", $yyslist);
        $this->assign("zongji", $zongji);
        $this->assign("fenye", $fenye);
        $this->display("index/sellershop.lists");
    }

//待审核商品
    public function notAudit() {
        $this->ment = array(
            array("lists", "商品管理", DOWN_M . '/' . DOWN_C . "/goods_list"),
            array("add", "添加商品", DOWN_M . '/' . DOWN_C . "/goods_add"),
            array("renqi", "人气商品", DOWN_M . '/' . DOWN_C . "/goods_list/renqi"),
            array("xsjx", "限时揭晓商品", DOWN_M . '/' . DOWN_C . "/goods_list/xianshi"),
            array("qishu", "期数倒序", DOWN_M . '/' . DOWN_C . "/goods_list/qishu"),
            array("danjia", "单价倒序", DOWN_M . '/' . DOWN_C . "/goods_list/danjia"),
            array("money", "商品价格倒序", DOWN_M . '/' . DOWN_C . "/goods_list/money"),
            array("money", "已揭晓", DOWN_M . '/' . DOWN_C . "/goods_list/jiexiaook"),
            array("lists", "未审核商品", DOWN_M . '/' . DOWN_C . "/notAudit"),
            array("money", "<font color='#f00'>期数已满商品</font>", DOWN_M . '/' . DOWN_C . "/goods_list/maxqishu"),
        );
        $table = 'shangpin_audit';
        $list_where = "q_uid is null and status=1";
        $num = 20;
        $zongji = D($table)->where($list_where)->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");
        $yyslist = D($table)->where($list_where)->order('id DESC')->limit(($fenyenum - 1) * 20, 10)->select();
        $this->assign("yyslist", $yyslist);
        $this->assign("zongji", $zongji);
        $this->assign("fenye", $fenye);
        $this->display("admin/notAudit.lists");
    }

    //编辑商品
    public function goods_audit_edit() {
        $db = new \Think\Model;
        $db->startTrans();
        $shopid = I("id", 0);
        $shopinfo = D("shangpin_audit")->where(array('id' => $shopid))->where('qishu')->lock(true)->order('qishu DESC')->limit(1)->find();
        if ($shopinfo['q_end_time'])
            $this->note("该商品已经揭晓,不能修改!", C("URL_DOMAIN") . "seller/goods_list");
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
            $seller_id = htmlspecialchars($_POST['seller_id']);
            $content = stripslashes($_POST['content']);
            $thumb = trim(htmlspecialchars($_POST['thumb']));
            $maxqishu = intval($_POST['maxqishu']) ? intval($_POST['maxqishu']) : 1;
            $shangpinss_key_pos = isset($_POST['goods_key']['pos']) ? 1 : 0;
            $shangpinss_key_renqi = isset($_POST['goods_key']['renqi']) ? 1 : 0;
            $shangpinss_key_renqi1 = isset($_POST['goods_key']['renqi1']) ? 1 : 0;
            //fxi by dabin
            $goods_key_leixing = $_POST['goods_key']['leixing'];

            //roce
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

            $sql = $db->table("yys_shangpin_audit")->where(array('id' => $shopid))->save(array("ka" => htmlspecialchars(I('kahao')), "mi" => htmlspecialchars($_POST['mima']), "cateid" => "$cateid", "brandid" => "$pinpaiid", "title" => "$biaoti", "title_style" => "$biaoti_style", "title2" => "$biaoti2", "keywords" => "$guanjianzi", "description" => "$miaoshu", "maxqishu" => "$maxqishu", "thumb" => "$thumb", "picarr" => "$picarr", "content" => "$content", "xsjx_time" => "$xsjx_time", "renqi" => "$shangpinss_key_renqi", "renqi1" => "$shangpinss_key_renqi1", "pos" => "$shangpinss_key_pos", "time" => "$time", "cardId" => "$cardId", "cardId1" => "$cardId1", "cardPwd" => "$cardPwd", "leixing" => "$goods_key_leixing", "yuanjia" => "$cardId2", "seller_id" => "$seller_id", "status" => "1"));

            $s_sid = $shopinfo['sid'];

            $db->table("yys_shangpin_audit")->where(array('sid' => $s_sid))->save(array('maxqishu' => $maxqishu));

            if ($sql) {
                $db->commit();
                $this->note("修改成功!");
            } else {
                $db->rollback();
                $this->note("修改失败!");
            }
        }
        $cateinfo = D("fenlei")->where(array('cateid' => $shopinfo[cateid]))->find();
        $pinpaiList = D("pinpai")->where(array("cateid" => intval($shopinfo['cateid'])))->select();
        //dump($pinpaiList);
        $pinpaiList = $this->key2key($pinpaiList, 'id');


        $fenleis = D("fenlei")->where(array('model' => 1))->order('parentid ASC, cateid ASC')->select();
        $fenleis = $this->key2key($fenleis, 'cateid');
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
        
        $this->assign("pinpaiList",$pinpaiList);
        $this->assign("fenleishtml", $fenleishtml);
        $this->assign("shopinfo", $shopinfo);
        $this->display("admin/notAudit.edit");

        //   include $this->dwt(DOWN_M, 'notAudit.edit');
    }

    //商品审核通过
    public function goods_audit() {
        $db = new \Think\Model;
        $id = I('id', 0);
        $ginfo = D("shangpin_audit")->where(array('id' => $id))->find();
        //$this->db->YOne("select * from `@#_shangpin_audit` where `id` = '$id' limit 1");
        if (!$ginfo) {
            $this->note("没有找到这个商品");
        }
        $db->startTrans();
        $info = D("shangpin_audit")->where(array('id' => $id))->find();
        $q1 = $db->table("yys_shangpin")->where(array('id' => $id))->add(array('ka' => $info[ka], 'mi' => $info[mi], 'cateid' => $info[cateid], 'brandid' => $info[brandid], 'title' => $info[title], 'title_style' => $info[title_style], 'title2' => $info[title2], 'keywords' => $info[keywords], 'description' => $info[description], 'money' => $info[money], 'yunjiage' => $info[yunjiage], 'zongrenshu' => $info[zongrenshu], 'canyurenshu' => $info[canyurenshu], 'shenyurenshu' => $info[shenyurenshu], 'qishu' => $info[qishu], 'maxqishu' => $info[maxqishu], 'thumb' => $info[thumb], 'picarr' => $info[picarr], 'content' => $info[content], 'xsjx_time' => $info[xsjx_time], 'renqi' => $info[renqi], 'renqi1' => $info[renqi1], 'pos' => $info['pos'], 'time' => $info['time'], 'cardId' => $info[cardId], 'cardId1' => $info[cardId1], 'cardPwd' => $info[cardPwd], 'leixing' => $info[leixing], 'yuanjia' => $info[yuanjia], 'seller_id' => $info[seller_id], 'status' => $info[status]));
       
        $q2 = $db->table("yys_shangpin_audit")->where(array('id' => $id))->delete();

        $zongrenshu = $ginfo['zongrenshu'];
        $canyurenshu = $ginfo['canyurenshu'];
        $query_table = $this->content_get_codes_table();
        if (!$query_table) {
            $db->rollback();
            $this->note("云购码仓库不正确!");
        }
        $q3 = R("Goods/content_huode_go_codes", array($zongrenshu, 3000, $shopid));
        $q4 = $db->table("yys_shangpin")->where(array('id' => $shopid))->save(array('status' => 0, 'codes_table' => $query_table, 'sid' => $shopid, 'def_renshu' => $canyurenshu));
        //	$this->db->Query("UPDATE `@#_shangpin` SET `status`=0,`codes_table` = '$query_table',`sid` = '$shopid',`def_renshu` = '$canyurenshu' where `id` = '$shopid'");



        if ($q1 && $q2 && $q3 && $q4) {
            $db->commit();
            $this->note("商品审核成功");
        } else {
            $db->rollback();
            $this->note("商品审核失败");
        }
    }

//我的一元速购记录
    public function getSellerOrderList() {
        if ($this->userinfo['type'] != 1) {
            exit;
        }
        $FIdx = I("FIdx") - 1;
        $EIdx = I("EIdx");


        $yyslistall['listItems'] = D("shangpin a")->join("yys_yonghu_yys_record b on b.shopid= a.id")->where(array('a.seller_id' => $this->userinfo['uid']))->where('b.gonumber>0')->order('b.id DESC')->limit($FIdx, $EIdx)->select();
        $yyslistall['count'] = count($yyslistall['listItems']);
        if (!empty($yyslistall['listItems'])) {
            if ($yyslistall['count'] < $FIdx) {
                $yyslistall['code'] = 1;
            } else {
                $yyslistall['code'] = 0;
            }
            foreach ($yyslistall['listItems'] as $key => $val) {
                $yyslistall['listItems'][$key]['time'] = $this->microt($val['time']);
                $yyslistall['listItems'][$key]['username'] = R("Base/huode_user_name", array($val['uid']));
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
        echo json_encode($yyslistall);
    }

    //商户订单列表
    public function seller_order_list() {
        if ($this->userinfo['type'] != 1) {
            $this->autoNote("无权限操作!", C("URL_DOMAIN") . "user/home");
            exit;
        }
        if (ismobile()) {
            $this->display("mobile/sellerdingdan.list");
            EXIT;
        }
        $num = 20;
        $info = D("shangpin a")->join("yys_yonghu_yys_record b on b.shopid= a.id")->where(array('a.seller_id' => $this->userinfo['uid']))->where('b.gonumber>0')->select();
        $zongji = count($info);
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");
        $recordlist = D("shangpin a")->join("yys_yonghu_yys_record b on b.shopid= a.id")->where(array('a.seller_id' => $this->userinfo['uid']))->where('b.gonumber>0')->order('b.id DESC')->limit(($fenyenum - 1) * 20, 10)->select();
        $this->assign("recordlist", $recordlist);
        $this->assign("zongji", $zongji);
        $this->assign("fenye", $fenye);
        $this->display("index/sellerdingdan.list");
    }

    /**
     * 	重置商品价格
     * */
    public function goods_set_money() {
        $user = $this->userinfo;
        $db = new \Think\Model;
        $yonghuid = abs(intval(I("id")));
        $shopinfo = $db->table("yys_shangpin")->where(array("id" => $yonghuid))->lock(true)->find();
        if (!$shopinfo || !empty($shopinfo['q_uid'])) {
            $this->autoNote("商品不存在或已开奖!");
            exit;
        }
        if ($user['uid'] != $shopinfo['seller_id'] || $user['type'] != 1) {
            $this->autoNote("无权限操作!");
            exit;
        }

        $g1o = $db->table("yys_yonghu_yys_record")->where(array("shopid" => $yonghuid))->select();

        if (isset($_POST['money']) || isset($_POST['yunjiage'])) {
            $new_money = abs(intval(I("money")));
            $new_one_m = abs(intval(I("yunjiage")));
            if ($new_one_m > $new_money) {
                $this->autoNote("单人次购买价格不能大于商品总价格!");
            }
            if (!$new_one_m || !$new_money) {
                $this->autoNote("价格填写错误!");
            }
            if (($new_one_m == $shopinfo['yunjiage']) && ($new_money == $shopinfo['money'])) {
                $this->autoNote("价格没有改变!");
            }
            $table = $shopinfo['codes_table'];
            $db->startTrans();
            $q1 = $db->table("yys_yonghu_yys_record")->where(array("shopid" => $yonghuid))->delete();
            $q2 = $db->table("yys_{$table}")->where(array("s_id" => $yonghuid))->delete();
            $zongrenshu = ceil($new_money / $new_one_m);

            $q3 = R("Goods/content_huode_go_codes", array($zongrenshu, 3000, $yonghuid));
            $q4 = $db->table("yys_shangpin")->where(array("id" => $yonghuid))->save(array("canyurenshu" => '0', "zongrenshu" => $zongrenshu, "money" => $new_money, "yunjiage" => $new_one_m, "shenyurenshu" => $zongrenshu));

            $q5 = $q6 = true;
            foreach ($g1o as $v) {
                $q5 = $db->table("yys_yonghu")->where(array("uid" => $v["uid"]))->setInc('money', $v["moneycount"]);
                $q6 = $db->table("yys_yonghu_zhanghao")->add(array("uid" => $v["uid"], "type" => "1", "pay" => "账户", "content" => "商品iD号{$yonghuid}重置价格返回", "money" => $v["moneycount"], "time" => time()));
            }


            // dump($q1);dump($q2);dump($q3);dump($q4);dump($q5);dump($q6);exit;
            if ($q2 && $q3 && $q4 && $q5 && $q6) {
                $db->commit();
                $this->autoNote("更新成功并返回余额到用户帐号!");
            } else if ($q2 && $q3 && $q4) {
                $db->rollback();
                $this->autoNote("更新成功");
            } else {
                $db->rollback();
                $this->autoNote("更新失败!");
            }
        }
        $this->assign("yonghuid", $yonghuid);
        $this->assign("shopinfo", $shopinfo);
        $this->autoShow("sellershop.set_money");
    }

    //编辑商品
    public function goods_edit() {
        $db = new \Think\Model;
        $db->startTrans();
        $shopid = I("id", 0);
        $this->assign("id", $shopid);
        $type = intval(I("type", 0));
        $this->assign("type", $type);
        $table = "yys_shangpin";
        if ($type) {
            $table = "yys_shangpin_audit";
        }
        $shopinfo = $db->table($table)->where(array("id" => $shopid))->where('qishu')->lock(true)->order('qishu DESC')->find();
        if ($this->userinfo['uid'] != $shopinfo['seller_id'] || $this->userinfo['type'] != 1) {
            $this->autoNote("无权限操作!");
            exit;
        }
        if ($shopinfo['q_end_time']) {
            $this->autoNote("该商品已经揭晓,不能修改!", C("URL_DOMAIN") . "seller/goods_list");
        }
        if (!$shopinfo) {
            $this->autoNote("参数不正确");
        }

        if (isset($_POST['dosubmit'])) {
            $cateid = intval($_POST['cateid']);
            $pinpaiid = intval($_POST['brand']);
            $biaoti = htmlspecialchars($_POST['title']);
            $biaoti_color = htmlspecialchars($_POST['title_style_color']);
            $biaoti_bold = htmlspecialchars($_POST['title_style_bold']);
            $biaoti2 = htmlspecialchars($_POST['title2']);
            $guanjianzi = htmlspecialchars($_POST['keywords']);
            $miaoshu = htmlspecialchars($_POST['description']);
            $content = (stripslashes($_POST['content']));
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
           
            if (isset($_POST['uppicarr'])) {
                $picarr = serialize($_POST['uppicarr']);
            } else {
                $picarr = serialize(array());
            }
            if (ismobile()) {
                //手机端传输方式不同
                $thumb = $shopinfo['thumb'];
                if ($_FILES['thumb']['name']) {
                    $upload = new \Claduipi\Tools\upload;
                    $upload->upload_config(array('png', 'jpg', 'jpeg', 'gif'), 500000, 'shopimg');
                    $upload->go_upload($_FILES['thumb']);
                    if (!$upload->ok) {
                        $this->notemobile($upload->error);
                    }
                    $thumb = "shopimg/" . $upload->filedir . "/" . $upload->filename;
                }

                $uppicarr = array();
                if ($_FILES['uppicarr1']['name']) {
                    $upload = new \Claduipi\Tools\upload;
                    $upload->upload_config(array('png', 'jpg', 'jpeg', 'gif'), 500000, 'shopimg');
                    $upload->go_upload($_FILES['uppicarr1']);
                    if (!$upload->ok) {
                        $this->notemobile($upload->error);
                    }
                    $thumbs = "shopimg/" . $upload->filedir . "/" . $upload->filename;
                    array_push($uppicarr, $thumbs);
                }
                if ($_FILES['uppicarr2']['name']) {
                    $upload = new \Claduipi\Tools\upload;
                    $upload->upload_config(array('png', 'jpg', 'jpeg', 'gif'), 500000, 'shopimg');
                    $upload->go_upload($_FILES['uppicarr2']);
                    if (!$upload->ok) {
                        $this->notemobile($upload->error);
                    }
                    $thumbs = "shopimg/" . $upload->filedir . "/" . $upload->filename;
                    array_push($uppicarr, $thumbs);
                }
                if ($_FILES['uppicarr3']['name']) {
                    $upload = new \Claduipi\Tools\upload;
                    $upload->upload_config(array('png', 'jpg', 'jpeg', 'gif'), 500000, 'shopimg');
                    $upload->go_upload($_FILES['uppicarr3']);
                    if (!$upload->ok) {
                        $this->notemobile($upload->error);
                    }
                    $thumbs = "shopimg/" . $upload->filedir . "/" . $upload->filename;
                    array_push($uppicarr, $thumbs);
                }
                if ($uppicarr) {
                    $picarr = serialize($uppicarr);
                } else {
                    $picarr = $shopinfo['picarr'];
                }
            }

           
            if (!$cateid)
                $this->autoNote("请选择栏目");
            if (!$pinpaiid)
                $this->autoNote("请选择品牌");
            if (!$biaoti)
                $this->autoNote("标题不能为空");
            if (!$thumb)
                $this->autoNote("缩略图不能为空");

            $biaoti_style = '';
            if ($biaoti_color) {
                $biaoti_style.='color:' . $biaoti_color . ';';
            }
            if ($biaoti_bold) {
                $biaoti_style.='font-weight:' . $biaoti_bold . ';';
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

            $data = array(
                "cateid" => $cateid,
                "brandid" => $pinpaiid,
                "title" => $biaoti,
                "title_style" => $biaoti_style,
                "title2" => $biaoti2,
                "keywords" => $guanjianzi,
                "description" => $miaoshu,
                "thumb" => $thumb,
                "picarr" => $picarr,
                "content" => $content,
                "maxqishu" => $maxqishu,
                "renqi" => $shangpinss_key_renqi,
                "renqi1" => $shangpinss_key_renqi1,
                "leixing" => $goods_key_leixing,
                "xsjx_time" => $xsjx_time,
               
                "ka" => htmlspecialchars($_POST["kahao"]),
                "mi" => htmlspecialchars($_POST["mima"]),
                "pos" => $shangpinss_key_pos,
                "cardId1" => $cardId1,
                "yuanjia" => $cardId2,
                "cardId" => $cardId,
                "cardPwd" => $cardPwd
            );
            $q10 = $db->table($table)->where(array('id' => $shopid))->save($data);
            $s_sid = $shopinfo['sid'];
            $db->table($table)->where(array('sid' => $s_sid))->save(array('maxqishu' => $maxqishu));
            if (!$type && $q10) {
                $g1o = D("yonghu_yys_record")->where(array("shopid" => $shopid))->select();
                $q5 = true;
                $q6 = TRUE;
                foreach ($g1o as $v) {
                    $q5 = $db->table("yys_yonghu")->where(array("uid" => $v["uid"]))->setInc('money', $v["moneycount"]);
                    $q6 = $db->table("yys_yonghu_zhanghao")->add(array("uid" => $v["uid"], "type" => "1", "pay" => "账户", "content" => "商品iD号{$shopid}重置价格返回", "money" => $v["moneycount"], "time" => time()));
                }
                $q1 = true;
                if ($g1o) {
                    $q1 = $db->table("yys_yonghu_yys_record")->where(array("shopid" => $shopid))->delete();
                }
                $codes = "yys_{$shopinfo['codes_table']}";
                $q2 = $db->table($codes)->where(array("s_id" => $shopid))->delete();
                $data = array(
                    
                    "ka" => htmlspecialchars($_POST["kahao"]),
                    "mi" => htmlspecialchars($_POST["mima"]),
                    "cateid" => $cateid,
                    "brandid" => $pinpaiid,
                    "title" => $biaoti,
                    "title_style" => $biaoti_style,
                    "title2" => $biaoti2,
                    "keywords" => $guanjianzi,
                    "description" => $miaoshu,
                    "money" => $shopinfo['money'],
                    "yunjiage" => $shopinfo['yunjiage'],
                    "zongrenshu" => $shopinfo['zongrenshu'],
                    "canyurenshu" => 0,
                    "shenyurenshu" => $shopinfo['shenyurenshu'],
                    "qishu" => $shopinfo['qishu'],
                    "maxqishu" => $maxqishu,
                    "thumb" => $thumb,
                    "picarr" => $picarr,
                    "content" => $content,
                    "xsjx_time" => $xsjx_time,
                    "renqi" => $shangpinss_key_renqi,
                    "renqi1" => $shangpinss_key_renqi1,
                    "pos" => $shangpinss_key_pos,
                    "time" => $shopinfo['time'],
                    "cardId" => $cardId,
                    "cardId1" => $cardId1,
                    "cardPwd" => cardPwd,
                    "leixing" => $goods_key_leixing,
                    "yuanjia" => $cardId2,
                    "seller_id" => $shopinfo['seller_id'],
                    "status" => "1"
                );
                $q3 = $db->table("yys_shangpin_audit")->add($data);
               
                $q4 = $db->table("yys_shangpin")->where(array("id" => $shopid))->delete();

                //dump($q1);dump($q2);dump($q3);dump($q4);dump($q5);dump($q6);exit;
                if ($q1 && $q2 && $q3 && $q4 && $q5 && $q6) {
                    $db->commit();
                    $this->autoNote("修改成功!", C("URL_DOMAIN") . "seller/goods_list");
                } else {
                    $db->rollback();
                    $this->autoNote("修改失败!");
                }
            } else if ($q10) {
                $db->commit();
                $this->autoNote("修改成功!", C("URL_DOMAIN") . "seller/goods_list/type/1");
            } else {
                $db->rollback();
                $this->autoNote("修改失败!");
            }
        }

        $cateinfo = D("fenlei")->where(array('cateid' => $shopinfo[cateid]))->find();
        $pinpaiList = D("pinpai")->where(array("cateid" => intval($shopinfo['cateid'])))->select();
        $pinpaiList = $this->key2key($pinpaiList, 'id');


        $fenleis = D("fenlei")->where(array('model' => 1))->order('parentid ASC, cateid ASC')->select();
        $fenleis = $this->key2key($fenleis, 'cateid');
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
        $this->assign("pinpaiList", $pinpaiList);
        $this->assign("fenleishtml", $fenleishtml);
        $this->assign("shopinfo", $shopinfo);
        $this->autoShow("sellershop.edit");
    }

    //期数  手机端
    public function getQishuList() {
        if ($this->userinfo['type'] != 1) {
            exit;
        }
        $FIdx = I("FIdx", 0) - 1;
        $EIdx = 10;
        $id = I("Shopid", 0);
        if ($id < 0 || $FIdx < 0) {
            exit;
        }
        $yyslist['code'] = 0;
        $yyslist["listItems"] = D("shangpin")->where(array("sid" => $id))->order("qishu DESC")->limit($FIdx, $EIdx)->select();
        //dump(D("shangpin")->getLastSql());exit;
        if ($yyslist['listItems']) {
            $yyslist['code'] = 1;
        }
        $yyslist['count'] = count($yyslist['listItems']);
        echo json_encode($yyslist);
        exit;
    }

    //期数列表
    public function qishu_list() {
        $shopid = intval(I("id"));

        $info = D("shangpin")->where(array("id" => $shopid))->find();
        $huiyuan = $this->userinfo;
        if ($huiyuan['uid'] != $info['seller_id'] || $huiyuan['type'] != 1) {
            $this->autoNote("无权限操作!");
            exit;
        }
        if (ismobile()) {
            $this->assign("shopid", $shopid);
            $this->autoShow("sellerqishu.list");
            exit;
        }

        $num = 20;

        $zongji = D("shangpin")->where(array("sid" => $info["sid"]))->count();
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

        $qishu = D("shangpin")->where(array("sid" => $info["sid"]))->order("qishu DESC")->limit(($fenyenum - 1) * $num, $num)->select();
        $cateid = $qishu[0]['cateid'];
        $cate_name = D("fenlei")->where(array("cateid" => $cateid))->find();
        $cate_name = $cate_name['name'];
        $this->assign("cate_name", $cate_name);
        $this->assign("cateid", $cateid);
        $this->assign("qishu", $qishu);
        $this->assign("fenye", $fenye);
        $this->assign("zongji", $zongji);
        $this->autoShow("sellerqishu.list");
    }

    //删除商品
    public function goods_del() {
        $shopid = intval(I("id"));
        $type = intval(I("type", 0));
        $table = "shangpin";
        if ($type) {
            $table = "shangpin_audit";
        }
        $info = D($table)->where(array("id" => "$shopid"))->field("codes_table,seller_id")->find();
        $user = $this->userinfo;
        if ($user['uid'] != $info['seller_id'] || $user['type'] != 1) {
            $this->note("无权限操作!");
            exit;
        }
        $db = new \Think\Model;
        $db->startTrans();
        if (!$type) {
            $table = $info['codes_table'];
            $info = $db->table("yys_shangpin")->where(array("id" => "$shopid"))->find();
            $q4 = $db->table("yys_shangpin_del")->add($info);
            $g1o = $db->table("yys_yonghu_yys_record")->where(array("shopid" => $shopid))->select();
            $q5 = true;
            $q6 = TRUE;
            foreach ($g1o as $v) {
                $q5 = $db->table("yys_yonghu")->where(array("uid" => $v["uid"]))->setInc('money', $v["moneycount"]);
                $q6 = $db->table("yys_yonghu_zhanghao")->add(array("uid" => $v["uid"], "type" => "1", "pay" => "账户", "content" => "商品iD号{$shopid}商户删除商品返回", "money" => $v["moneycount"], "time" => time()));
            }
            $q1 = $db->table("yys_{$table}")->where(array("s_id" => "$shopid"))->delete();
            $q2 = $db->table("yys_shangpin")->where(array("id" => "$shopid"))->delete();
            $q3 = true;
            if ($g1o) {
                $q3 = $db->table("yys_yonghu_yys_record")->where(array("shopid" => "$shopid"))->delete();
            }
            //dump($q1);dump($q2);dump($q3);dump($q4);dump($q5);dump($q6);exit;
            $q1 = $q1 && $q2 && $q3 && $q6 && $q5 && $q4;
        } else {
            $q1 = $db->table("yys_shangpin_audit")->where(array("id" => "$shopid"))->delete();
        }
        if ($q1) {
            $db->commit();
            $this->autoNote("商品删除成功");
        } else {
            $db->rollback();
            $this->autoNote("商品删除失败");
        }
        exit;
    }

    /* 单个商品的购买详细 */

    public function goods_go_one() {
        $yonghuid = intval(I("id"));
        $key = I("key");
        $ginfo = D("shangpin")->where(array("id" => "$yonghuid"))->find();
        if (!$ginfo)
            $this->note("没有找到这个商品");
        $user = $this->userinfo;

        if ($user['uid'] != $ginfo['seller_id'] || $user['type'] != 1) {
            $this->note("无权限操作!");
            exit;
        }
        if (ismobile()) {
            $bl = ($ginfo['canyurenshu'] / $ginfo['zongrenshu']) * 100;
            $this->assign("shopinfo", $ginfo);
            $this->assign("bl", $bl);
            $this->display("mobile/sellershop.go_list");
            exit;
        }


        $zongji = D("yonghu_yys_record")->where(array("shopid" => $yonghuid))->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, 20, $fenyenum, "0");
        if (!$key) {
            $go_list = D("yonghu a")->join("left join yys_yonghu_yys_record b on b.uid = a.uid")->where(array("b.shopid" => $yonghuid))->order("b.id DESC")->limit(($fenyenum - 1) * 20, 10)->select();
            //$go_list = $this->db->YPage("select * from yys_yonghu left join @#_yonghu_yys_record on @#_yonghu_yys_record.uid = @#_yonghu.uid where @#_yonghu_yys_record.shopid=$yonghuid order by @#_yonghu_yys_record.id DESC", array("num" => 20, "page" => $fenyenum, "type" => 1, "cache" => 0));
        } else {
            $go_list = D("yonghu a")->join("left join yys_yonghu_yys_record b on b.uid = a.uid")->where(array("b.shopid" => $yonghuid))->order("b.gonumber DESC")->limit(($fenyenum - 1) * 20, 10)->select();
            //$go_list = $this->db->YPage("select * from yys_yonghu left join @#_yonghu_yys_record on @#_yonghu_yys_record.uid = @#_yonghu.uid where @#_yonghu_yys_record.shopid=$yonghuid order by @#_yonghu_yys_record.gonumber DESC", array("num" => 20, "page" => $fenyenum, "type" => 1, "cache" => 0));
        }
        $this->assign("ginfo", $ginfo);
        $this->assign("go_list", $go_list);
        $this->assign("fenye", $fenye);
        $this->assign("zongji", $zongji);
        $this->display("index/sellershop.go_list");
    }

    //订单详细
    public function get_dingdan() {
        $code = abs(I("id"));
        $record = D("yonghu_yys_record")->where(array("id" => "$code"))->find();
        if (!$record)
            $this->autoNote("参数不正确!");
        $user = $this->userinfo;
        $shopinfo = D("shangpin")->where(array("id" => $record['shopid']))->find();
        if ($user['uid'] != $shopinfo['seller_id'] || $user['type'] != 1) {
            $this->autoNote("无权限操作!");
            exit;
        }
        if (isset($_POST['submit'])) {
            $record_code = explode(",", $record['status']);
            $status = $_POST['status'];
            $company = $_POST['company'];
            $company_code = $_POST['company_code'];
            $company_money = floatval($_POST['company_money']);
            if (!$company_money) {
                $company_money = '0.01';
            } else {
                $company_money = sprintf("%.2f", $company_money);
            }
            if ($status == '未完成') {
                $status = $record_code[0] . ',' . $record_code[1] . ',' . '未完成';
            }
            if ($status == '已发货') {
                $status = '已付款,已发货,待收货';
            }
            if ($status == '未发货') {
                $status = '已付款,未发货,未完成';
            }
            $ret = D("yonghu_yys_record")->where(array("id" => $code))->save(array("status" => $status, "company" => $company, "company_code" => $company_code, "company_money" => $company_money));
            if ($ret) {
                $this->autoNote("更新成功");
            } else {
                $this->autoNote("更新失败");
            }
        }

        if (ismobile()) {
            $bl = ($shopinfo['canyurenshu'] / $shopinfo['zongrenshu']) * 100;
            $this->assign("bl", $bl);
        }
        $uid = $record['uid'];
        $weer = D("yonghu")->where(array("uid" => "$uid"))->find();
        $weer_dizhi = $record;
        $go_time = $record['time'];
        $this->assign("code", $code);
        $this->assign("record", $record);
        $this->assign("shopinfo", $shopinfo);
        $this->assign("weer", $weer);
        $this->assign("weer_dizhi", $weer_dizhi);
        $this->assign("go_time", $go_time);
        $this->autoShow("sellerdingdan.code");
    }

}
