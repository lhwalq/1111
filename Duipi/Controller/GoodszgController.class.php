<?php

/**
 * 直购
 * addtime 2016/06/12
 */

namespace Duipi\Controller;

use Think\Controller;

class GoodszgController extends BaseController {

    public function _initialize() {
        $this->getAdminRight(CONTROLLER_NAME, ACTION_NAME);
    }

    public function goods_addzg() {
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
            $cardId1 = mysql_escape_string(I("cardId1", ""));
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

            $zongrenshu = ceil($money / $yunjiage);
            $codes_len = ceil($zongrenshu / 3000);
            $shenyurenshu = $zongrenshu - $canyurenshu;

            $time = time(); //商品添加时间		
            $db->startTrans();
            $arr = array("ka" => htmlspecialchars(I('kahao')), "mi" => htmlspecialchars($_POST['mima']), "cateid" => "$cateid", "brandid" => "$pinpaiid", "title" => "$biaoti", "title_style" => "$biaoti_style", "title2" => "$biaoti2", "keywords" => "$guanjianzi", "description" => "$miaoshu", "money" => "$money", "yunjiage" => "$yunjiage", "zongrenshu" => "$zongrenshu", "canyurenshu" => "$canyurenshu", "shenyurenshu" => "$shenyurenshu", "qishu" => "1", "maxqishu" => "$maxqishu", "thumb" => "$thumb", "picarr" => "$picarr", "content" => "$content", "xsjx_time" => "$xsjx_time", "renqi" => "$shangpinss_key_renqi", "renqi1" => "$shangpinss_key_renqi1", "pos" => "$shangpinss_key_pos", "time" => "$time", "cardId" => "$cardId", "cardId1" => "$cardId1", "cardPwd" => "$cardPwd", "leixing" => "$goods_key_leixing", "yuanjia" => "$cardId2");
            $query_1 = $db->table("yys_shangpinzg")->add($arr);
            $shopid = $query_1;


            $query_3 = $db->table("yys_shangpinzg")->where(array("id" => $shopid))->save(array("codes_table" => "$query_table", "sid" => "$shopid", "def_renshu" => "$canyurenshu"));
            if ($query_1 && $query_3) {
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
        $this->assign("pinpai", $pinpai);
        $this->assign("ment", $ment);
        $this->display("admin/shop.insertzg");
    }

    //商品列表	
    public function goods_listzg() {
        $ment = array(
            array("lists", "商品管理", C("URL_DOMAIN") . "goodszg/goods_listzg"),
            array("add", "添加商品", C("URL_DOMAIN") . "goodszg/goods_addzg"),
            array("renqi", "人气商品", C("URL_DOMAIN") . "goodszg/goods_listzg/order/renqi"),
            array("xsjx", "限时揭晓商品", C("URL_DOMAIN") . "goodszg/goods_listzg/order/xianshi"),
            array("qishu", "期数倒序", C("URL_DOMAIN") . "goodszg/goods_listzg/order/qishu"),
            array("danjia", "单价倒序", C("URL_DOMAIN") . "goodszg/goods_listzg/order/danjia"),
            array("money", "商品价格倒序", C("URL_DOMAIN") . "goodszg/goods_listzg/order/money"),
            array("money", "已揭晓", C("URL_DOMAIN") . "goodszg/goods_listzg/order/jiexiaook"),
            array("money", "<font color='#f00'>期数已满商品</font>", C("URL_DOMAIN") . "goodszg/goods_listzg/order/maxqishu"),
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
                $ment[4][2] = C("URL_DOMAIN") . "goodszg/goods_listzg/order/qishuasc";
            }
            if ($cateid == 'qishuasc') {
                $list_order = " qishu ASC";
                $ment[4][1] = "期数倒序";
                $ment[4][2] = C("URL_DOMAIN") . "goodszg/goods_listzg/order/qishu";
            }
            if ($cateid == 'danjia') {
                $list_order = " yunjiage DESC";
                $ment[5][1] = "单价正序";
                $ment[5][2] = C("URL_DOMAIN") . "goodszg/goods_listzg/order/danjiaasc";
            }
            if ($cateid == 'danjiaasc') {
                $list_order = " yunjiage ASC";
                $ment[5][1] = "单价倒序";
                $ment[5][2] = C("URL_DOMAIN") . "goodszg/goods_listzg/order/danjia";
            }
            if ($cateid == 'money') {
                $list_order = " money DESC";
                $ment[6][1] = "商品价格正序";
                $ment[6][2] = C("URL_DOMAIN") . "goodszg/goods_listzg/order/moneyasc";
            }
            if ($cateid == 'moneyasc') {
                $list_order = " money ASC";
                $ment[6][1] = "商品价格倒序";
                $ment[6][2] = C("URL_DOMAIN") . "goodszg/goods_listzg/order/money";
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
        $zongji = D("shangpinzg")->where($list_where)->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");
        $yyslist = D("shangpinzg")->where($list_where)->order($list_order)->limit(($fenyenum - 1) * $num, $num)->select();
        $this->assign("ment", $ment);
        $this->assign("zongji", $zongji);
        $this->assign("fenye", $fenye);
        $this->assign("yyslist", $yyslist);
        $this->assign("cateid", $cateid);
        $this->display("admin/shop.listszg");
    }

    //直购商品设置
    public function goods_setzg() {
        $p_key = I("type");

        $p_val = I("value");

        if (empty($p_key) || empty($p_val)) {
            $this->note("设置失败");
        }

        $ss = D("shai")->where(array("sd_shopid" => "$p_val"))->find();

        $query = true;
        switch ($p_key) {
            case 'renqi':
                $query = D("shangpinzg")->where(array("id" => "$p_val"))->save(array("renqi" => "1"));
                break;
            case 'renqi1':
                $query = D("shangpinzg")->where(array("id" => "$p_val"))->save(array("renqi1" => "1"));
                break;
            case 'shenhe':
                $query = D("shai")->where(array("sd_shopid" => "$p_val"))->save(array("shenhe" => "1"));
                D("yonghu")->where("uid={$ss['sd_userid']}")->setInc('score', C('shaim'));
                break;
            case 'shenhe1':
                $query = D("shai")->where(array("sd_shopid" => "$p_val"))->save(array("shenhe" => "0"));
                break;
            case 'fahuo':
                $query = D("shangpinzg")->where(array("id" => "$p_val"))->save(array("fahuo" => "0"));
                break;
            case 'fahuo1':
                $query = D("shangpinzg")->where(array("id" => "$p_val"))->save(array("fahuo" => "1"));
                break;
            case 'huiyuan':
                $query = D("yonghu")->where(array("uid" => "$p_val"))->save(array("huiyuan" => "1"));
                break;
            case 'huiyuan1':
                $query = D("yonghu")->where(array("uid" => "$p_val"))->save(array("huiyuan" => "0"));
                break;
            case 'xuangou':
                $query = D("shangpinzg")->where(array("id" => "$p_val"))->save(array("is_choose" => "0"));
                break;
            case 'xuangou1':
                $query = D("shangpinzg")->where(array("id" => "$p_val"))->save(array("is_choose" => "1"));
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
    public function goods_set_moneyzg() {
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
        $yonghuid = I("id", 0);

        $shopinfo = $db->table("yys_shangpinzg")->where(array("id" => "$yonghuid"))->lock(true)->find();

        if (!$shopinfo || !empty($shopinfo['q_uid'])) {
            $this->note("参数不正确!");
            exit;
        }
        $g1o = $db->table("yys_yonghu_yys_recordzg")->where(array("shopid" => "$yonghuid"))->select();
        $a = count($g1o);

        if (isset($_POST['money']) || isset($_POST['yunjiage'])) {
            $new_money = abs(intval($_POST['money']));
            $new_one_m = abs(intval($_POST['yunjiage']));

            if (!$new_one_m || !$new_money) {
                $this->note("价格填写错误!");
            }
            if (($new_one_m == $shopinfo['yunjiage']) && ($new_money == $shopinfo['money'])) {
                $this->note("价格没有改变!");
            }

            $db->table("yys_yonghu_yys_recordzg")->where(array("shopid" => "$yonghuid"))->delete();

            $zongrenshu = ceil($new_money / $new_one_m);

            if ($a != 0) {
                $q1 = D("yonghu_yys_recordzg")->where(array('shopis' => $yonghuid))->delete();
                if (!$q1) {

                    $db->rollback();
                    $this->note("更新失败!");
                }
            }
            //	$this->db->Query("DELETE FROM `@#_yonghu_yys_recordzg` WHERE `shopid` = '$yonghuid'");
            //   $q2 =D("shopcodes_1")->where(array('s_id')=>$yonghuid)->delete();
            //	$this->db->Query("DELETE FROM `@#_{$table}` WHERE `s_id` = '$yonghuid'");


            $q4 = D("shangpinzg")->where(array('id' => $yonghuid))->save(array("canyurenshu" => '0', "zongrenshu" => "$zongrenshu", "money" => "$new_money", "yunjiage" => "$new_one_m", "shenyurenshu" => "{$shopinfo['zongrenshu']}"));
            //	var_dump($q4);exit;

            $q5 = true;
            $q6 = true;
            foreach ($g1o as $v) {
                $q5 = $db->table("yys_yonghu")->where(array("uid" => "{$v['uid']}"))->setInc('money', $v['moneycount']);
                $q6 = $db->table("yys_yonghu_zhanghao")->add(array("uid" => "{$v['uid']}", "type" => '1', "pay" => '账户', "content" => "商品iD号.$yonghuid.重置价格返回", "money" => "{$v['moneycount']}", time => time()));
            }
            //	var_dump(D("yonghu_yys_recordzg")->where(array('shopis'=>$yonghuid))->delete());exit;

            if ($q4 && $q5 && $q6) {
                $db->commit();
                $this->note("更新成功并返回余额到用户帐号!");
            } else {

                $db->rollback();
                $this->note("更新失败!");
            }
        }
        $this->assign("shopinfo", $shopinfo);
        $this->assign("yonghuid", $yonghuid);
        $this->assign("ment", $ment);
        $this->display("admin/shop.set_moneyzg");
    }

    //编辑商品
    public function goods_editzg() {
        $db = new \Think\Model;
        $db->startTrans();
        $shopid = intval(I("id", 0));
        $shopinfo = $db->table("yys_shangpinzg")->lock(true)->where("id = '$shopid' and qishu")->order("qishu DESC")->find();
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
            $up_data = array(
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
            $db->table("yys_shangpinzg")->where(array("sid" => "$s_sid"))->save(array("maxqishu" => "$maxqishu"));
            if ($db->table("yys_shangpinzg")->where(array("id" => "$shopid"))->save($up_data)) {
                $db->commit();
                $this->note("修改成功!");
            } else {
                $db->rollback();
                $this->note("修改失败!");
            }
        }
        $ment = array(
            array("lists", "商品管理", C("URL_DOMAIN") . "goodszg/goods_listzg"),
            array("insert", "添加商品", C("URL_DOMAIN") . "goodszg/goods_addzg"),
        );
        $cateinfo = $db->table("yys_fenlei")->where("cateid = '{$shopinfo['cateid']}'")->find();
        $pinpai = $db->table("yys_pinpai")->select();
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
        $this->display("admin/shop.editzg");
    }

    //期数列表
    public function qishu_listzg() {
        $ment = array(
            array("lists", "商品列表", C("URL_DOMAIN") . "/goods/goods_list"),
        );
        $shopid = intval(I("id", 0));
        $db_good = D("shangpinzg");
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
        $this->display("admin/qishu.listzg");
    }

    /* 单个商品的购买详细 */

    public function goods_go_onezg() {
        $yonghuid = intval(I("id", 0));
        $key = I("key", 0);
        $db = new \Think\Model;
        $ginfo = $db->table("yys_shangpinzg")->where(array("id" => "$yonghuid"))->find();
        if (!$ginfo)
            $this->note("没有找到这个商品");
        $zongji = $db->table("yys_yonghu_yys_recordzg")->where(array("shopid" => "$yonghuid"))->count();
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
        $go_list = $db->table("yys_yonghu a")->join("yys_yonghu_yys_recordzg b on b.uid = a.uid")->where(array("b.shopid" => "$yonghuid"))->order($order)->limit(($fenyenum - 1) * 20, 20)->select();

        $this->assign("ginfo", $ginfo);
        $this->assign("fenye", $fenye);
        $this->assign("zongji", $zongji);
        $this->assign("go_list", $go_list);
        $this->display("admin/shop.go_listzg");
    }
//ajax 删除商品
    public function goods_delzg() {
        $info = R('admin/getAdminInfo', array());
        $shopid = intval(I("id", 0));
        $db = new \Think\Model;
        $info = $db->table("yys_shangpinzg")->where(array("id" => "$shopid"))->find();
        $table = $info['codes_table'];
        $db->startTrans();
       // $q4 = $db->table("yys_shangpin_del")->add($info);
        $q1 = $db->table("yys_shopcodes_1")->where(array("s_id" => "$shopid"))->delete();
        $q2 = $db->table("yys_shangpinzg")->where(array("id" => "$shopid"))->delete();
        $q3 = D("yonghu_yys_recordzg")->where(array("shopid" => "$shopid"))->delete();
		//dump(D("yonghu_yys_recordzg")->getLastSql());//exit;
        if ($q1 && $q2) {
            $db->commit();
            $this->note("商品删除成功", C("URL_DOMAIN") . "goods/goods_list");
        } else {
            $db->rollback();
            $this->note("商品删除失败", C("URL_DOMAIN") . "goods/goods_list");
        }
        exit;
    }
//直购列表
    public function glistzg($select = '0_0_0') {
        if (ismobile()) {
            $biaoti = "商品列表_" . C("web_name");
            $key = "所有商品";
            $this->assign('category', R("Mobile/getCategory"));
            $this->assign("keys", $key);
            $this->assign("biaoti", $biaoti);
            $this->display("mobile/glistzg");
            exit;
        }
        $select = explode("_", $select);
        $select[] = '0';
        $select[] = '0';

        $cid = abs(intval($select[0]));
        $bid = abs(intval($select[1]));
        $order = abs(intval($select[2]));

        $where = '';
        $orders = '';
        switch ($order) {
            case '1':
                $orders = 'shenyurenshu ASC';
                break;
            case '2':
                $where = " renqi = '1'";
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

        ///////////////////////////////////////////////////////////////////////////////////分割写
        //分页
        $num = 20;
        /* 设置了查询分类ID 和品牌ID */
        if ($cid && $bid) {
            $sun_id_str = "'" . $cid . "'";
            $sun_cate = D("fenlei")->field("cateid")->where(array("parentid" => $daohangs['cateid']))->select();
            foreach ($sun_cate as $v) {
                $sun_id_str .= "," . "'" . $v['cateid'] . "'";
            }
            $zongji = D("shangpinzg")->field("cateid")->where("q_uid is null and brandid='$bid'  and cateid in ($sun_id_str)")->count();
        } else {
            if ($bid) {
                $zongji = D("shangpinzg")->field("id")->where("q_uid is null and brandid='$bid'")->count();
            } elseif ($cid) {
                $sun_id_str = "'" . $cid . "'";
                $sun_cate = D("fenlei")->where(array("parentid" => $daohangs['cateid']))->select();
                foreach ($sun_cate as $v) {
                    $sun_id_str .= "," . "'" . $v['cateid'] . "'";
                }
                $zongji = D("shangpinzg")->field("id")->where("q_uid is null and cateid in ($sun_id_str)")->count();
            } else {
                $zongji = D("shangpinzg")->field("ect id")->where("q_uid is null")->count();
            }
        }


        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");

//        if ($fenyenum > $fenye->page) {
//            $fenyenum = $fenye->page;
//        }

        if ($cid && $bid) {
            $yyslist = D("shangpinzg")->where("q_uid is null and brandid='$bid' and cateid in ($sun_id_str)")->order($orders)->limit(($fenyenum - 1) * $num, $num)->select();
        } else {
            if ($bid) {
                $yyslist = D("shangpinzg")->where("q_uid is null and brandid='$bid'")->order($orders)->limit(($fenyenum - 1) * $num, $num)->select();
            } elseif ($cid) {
                $yyslist = D("shangpinzg")->where("q_uid is null and cateid in ($sun_id_str)")->order($orders)->limit(($fenyenum - 1) * $num, $num)->select();
            } else {
                $yyslist = D("shangpinzg")->where("q_uid is null")->order($orders)->limit(($fenyenum - 1) * $num, $num)->select();
            }
        }
        $this->assign("daohangs", $daohangs);
        $this->assign("zongji", $zongji);
        $this->assign("fenye", $fenye);
        $this->assign("yyslist", $yyslist);
        $this->assign("cid", $cid);
        $this->assign("bid", $bid);
        $this->display("index/index.glistzg");
        $this_time = time();
    }

    public function itemszg() {
        if (!ismobile()) {
            $this->itemsForPczg();
        }
        $xiangmuid = I("goodsId", 0);
        if (!$xiangmuid) {
            $this->notemobile("找不到商品");
        }
        $model = new \Think\Model();
        $deng = $this->userinfo;
        $this->assign("huiyuan", $deng);
        if ($_GET['yaoqing']) {
            session('uu', $_GET['yaoqing']);
        }
        if ($_GET['yaoqing2']) {
            session('yaoqing2', $_GET['yaoqing2']);
        }
        if ($_GET['yaoqing3']) {
            session('yaoqing3', $_GET['yaoqing3']);
        }
        //佣金部分结束
        $key = "商品详情";
        $xiangmu = $model->table("yys_shangpinzg")->where(array("id" => $xiangmuid))->field('id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,fahuo')->find();
        //限购
        if (!empty($xiangmu['id']) && $this->userinfo) {
            $huiyuan = $this->userinfo;
            $counts1 = $model->table("yys_yonghu_yys_recordzg")->field("gonumber")->where(array("uid" => $huiyuan['uid'], "shopid" => $xiangmu['id']))->select();
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
        $sid_code = $model->table("yys_shangpinzg")->field("id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,fahuo")->where(array("sid" => $sid, "shopid" => $xiangmu['id']))->order('id DESC')->find();
        $sid_go_record = $model->table("yys_yonghu_yys_recordzg")->where(array("shopid" => $sid_code['sid'], "uid" => $sid_code['q_uid']))->order('id DESC')->find();
        $fenlei = $model->table("yys_fenlei")->where("cateid ='" . $xiangmu['cateid'] . "'")->find();
        $pinpai = $model->table("yys_pinpai")->where("id = '" . $xiangmu['brandid'] . "'")->find();
        $biaoti = $xiangmu['title'];
        $nomal = $xiangmu['zongrenshu'] - $xiangmu['canyurenshu'];
        $xiangmu['picarr'] = unserialize($xiangmu['picarr']);
        $we = $model->table("yys_yonghu_yys_recordzg")->where(array("shopqishu" => $xiangmu['qishu'], "shopid" => $xiangmuid))->order("id DESC")->limit(6)->select();
        $xiangmulist = $model->table("yys_shangpinzg")->where("sid='" . $xiangmu['sid'] . "' and q_end_time is not null")->order("qishu DESC")->select();
        //期数显示
        $xiangmuzx = $model->table("yys_shangpinzg")->field("id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,fahuo")->where("sid='" . $xiangmu['sid'] . "' and qishu>'" . $xiangmu['qishu'] . "' and q_end_time is null")->order("qishu DESC")->find();
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
            $gorecode = $model->table("yys_yonghu_yys_recordzg")->where(array("shopid" => $xiangmulist[0]['id'], "shopqishu" => $xiangmulist[0]['qishu']))->order("id DESC")->find();
        }
        $curtime = time();
        $shopitem = 'itemfun';
        //晒单数
        $shopid = $model->table("yys_shangpinzg")->field("id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,fahuo")->where(array("id" => $xiangmuid))->find();
        $yyslist = $model->table("yys_shangpinzg")->field("id,sid,cateid,brandid,title,title,title2,keywords,description,money,yunjiage,zongrenshu,canyurenshu,shenyurenshu,def_renshu,qishu,maxqishu,thumb,picarr,codes_table,xsjx_time,pos,renqi,time,q_uid,q_user,q_user_code,q_content,q_counttime,q_end_time,q_showtime,renqi1,ka,mi,ex_yuedingzhongjiang,cardId,cardPwd,cardId1,leixing,yuanjia,q_end_cp,fahuo")->where(array("sid" => $shopid['sid']))->select();
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
                $yys['urls'][$key]['url_short'] = C("URL_DOMAIN") . '/goodszg/itemszg/goodsId/' . $xiangmu['id'] . '?yaoqing2=' . $huiyuan['yaoqing'] . '&yaoqing=' . $huiyuan['uid'];
            } else if ($huiyuan['yaoqing'] && $huiyuan['yaoqing2'] && empty($huiyuan['yaoqing3'])) {
                $yys['urls'][$key]['url_short'] = C("URL_DOMAIN") . '/goodszg/itemszg/goodsId/' . $xiangmu['id'] . '?yaoqing=' . $huiyuan['uid'] . '&yaoqing2=' . $huiyuan['yaoqing'] . '&yaoqing3=' . $huiyuan['yaoqing2'];
            } else if ($huiyuan['yaoqing'] && $huiyuan['yaoqing2'] && $huiyuan['yaoqing3']) {
                $yys['urls'][$key]['url_short'] = C("URL_DOMAIN") . '/goodszg/itemszg/goodsId/' . $xiangmu['id'] . '?yaoqing=' . $huiyuan['uid'] . '&yaoqing2=' . $huiyuan['yaoqing'] . '&yaoqing3=' . $huiyuan['yaoqing2'];
            } else {
                $yys['urls'][$key]['url_short'] = C("URL_DOMAIN") . '/goodszg/itemszg/goodsId/' . $xiangmu['id'] . '?yaoqing=' . $huiyuan['uid'];
            }
            $yys['urls'][$key]['object_id'] = "";
            $yys['urls'][$key]['url_long'] = C("URL_DOMAIN") . '/goodszg/itemszg/goodsId/' . $xiangmu['id'];
            $yys['urls'][$key]['type'] = "0";
        }
        $xiangmu['content'] = htmlspecialchars_decode($xiangmu['content']);
        $this->assign('xiangmu', $xiangmu);
        $this->assign("signPackage", $signPackage);
        $this->assign("yys", $yys);
        $this->assign("wangqiqishu", $wangqiqishu);
        $this->display("mobile/itemzg");
        exit();
    }

    public function itemsForPczg() {
        $ddaa = "product";
        $xiangmushaiid = I("goodsId", 0);
        $xiangmushai = D("shangpinzg")->where(array("id" => $xiangmushaiid))->find();
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


        $weer_shop_codes_arr = D("yonghu_yys_recordzg")->where(array("uid" => $xiangmu['q_uid'], "shopid" => $xiangmuid, "shopqishu" => $xiangmu['qishu']))->select();
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
        $sid_code = D("shangpinzg")->where(array("sid" => $sid))->order("`id` DESC")->LIMIT(1, 1)->select();
        $sid_code = $sid_code[0];

        if ($xiangmu['id'] == $sid_code['id']) {
            $sid_code = null;
        }

        $sid_go_record = D("yonghu_yys_recordzg")->where(array("shopid" => $sid_code['id'], "uid" => $sid_code['q_uid']))->order("id DESC")->find();
        $fenlei = D("fenlei")->where(array("cateid" => $xiangmu['cateid']))->find();
        $pinpai = D("pinpai")->where(array("id" => $xiangmu['brandid']))->find();

        $biaoti = $xiangmu['title'] . ' (' . $xiangmu['title2'] . ')';

        $guanjianzi = $xiangmu['keywords'];
        $miaoshu = $xiangmu['description'];

        $nomal = $xiangmu['zongrenshu'] - $xiangmu['canyurenshu'];
        $xiangmu['picarr'] = unserialize($xiangmu['picarr']);

        $we = D("yonghu_yys_recordzg")->where(array("shopid" => $xiangmuid, "shopqishu" => $xiangmu['qishu']))->order("id DESC")->LIMIT("6")->select();
        $we2 = D("yonghu_yys_recordzg")->where(array("shopid" => $xiangmuid, "shopqishu" => $xiangmu['qishu']))->order("id DESC")->LIMIT("50")->select();

        $yyslistrenqib = D("shangpinzg")->where("q_uid is null")->order("q_counttime DESC")->LIMIT("15")->select();
        $yyslistrenqibb = D("shangpinzg")->where("renqi='1' and q_uid is null")->order("q_counttime DESC")->LIMIT("15")->select();

        //期数显示
        $num = 10;

        $zongji = D("shangpinzg")->where(array("sid" => $xiangmu['sid']))->count();
        $xx = $zongji / $num;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");
        $pp = ($fenyenum - 1) * 10;

        $xiangmulist = D("shangpinzg")->where(array("sid" => $xiangmu['sid']))->field("id,qishu,q_uid")->order("qishu DESC")->LIMIT("$pp,10")->select();
        $xiangmulistse = D("shangpinzg")->where(array("sid" => $xiangmu['sid']))->order("qishu DESC")->select();

        if ($fenyenum > $fenye->page) {
            $fenyenum = $fenye->page;
        }
        $wangqiqishu = '<ul class="Period_list">';


        if (!$xiangmulist[0]['q_uid']) {
            if ($xiangmulist[0]['id'] == $xiangmu['id'])
                $wangqiqishu.='<li><a class="w_the" href="' . C("URL_DOMAIN") . 'goodszzg/goodsId/' . $xiangmulist[0]['id'] . '"><b class="period_Ongoing period_ArrowCur" style="padding-left:0px;">' . "第" . $xiangmulist[0]['qishu'] . "期<i></i></b></a></li>";
            else
                $wangqiqishu.='<li><a href="' . C("URL_DOMAIN") . 'goodszg/goodsId/' . $xiangmulist[0]['id'] . '"><b class="period_Ongoing">' . "第" . $xiangmulist[0]['qishu'] . "期<i></i></b></a></li>";
        }else {
            if ($xiangmulist[0]['id'] == $xiangmu['id'])
                $wangqiqishu.='<li><a href="' . C("URL_DOMAIN") . 'goodszg/goodsId/' . $xiangmulist[0]['id'] . '"><b class="period_ArrowCur">' . "第" . $xiangmulist[0]['qishu'] . "期<i></i></b></a></li>";
            else
                $wangqiqishu.='<li><a href="' . C("URL_DOMAIN") . 'goodszg/dataserverForPczg/goodsId/' . $xiangmulist[0]['id'] . '" class="gray02">第' . $xiangmulist[0]['qishu'] . '期</a></li>';
        }
        unset($xiangmulist[0]);
        foreach ($xiangmulist as $key => $qitem) {
            if ($key % 9 == 0) {
                $wangqiqishu.='</ul><ul class="Period_list">';
            }
            if ($qitem['id'] == $xiangmu['id'])
                $wangqiqishu.='<li><b class="period_ArrowCur">第' . $qitem['qishu'] . '期</b></li>';
            else
                $wangqiqishu.='<li><a href="' . C("URL_DOMAIN") . 'goodszg/dataserverForPczg/goodsId/' . $qitem['id'] . '" class="gray02">第' . $qitem['qishu'] . '期</a></li>';
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
        $this->display("index/index.itemzg");
        exit;
    }

    /**
     * 购物车列表
     */
    public function cartlistzg() {
        $key = "购物车";
        $this->Cartlistzg = cookie('Cartlistzg');
        $Mcartlist = json_decode(stripslashes($this->Cartlistzg), true);
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
            $shoparrCopy = D("shangpinzg")->where("id in (" . $shopids . ")")->select();
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
                cookie('Cartlistzg', json_encode($Mcartlist));
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
        $this->autoShow("cartlistzg");
    }

    public function getGoodsPageListzg() {
        $cate_band = I("sortid", 0);
        $select = I("orderFlag", 0);
        $kaishi = I("fIdx", 0);
        $jieshu = I("eIdx", 0);
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


            $count = $db->table("yys_shangpinzg")->where($where)->count();
            $yyslist = $db->table("yys_shangpinzg")->where($where)->field("id,title,thumb,qishu,money,zongrenshu,canyurenshu,qishu,yunjiage")->order($order)->limit($star, $end)->select();
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
                $yyslist1['listItems'][$key]['yunjiage'] = $val['yunjiage'];
                $yyslist1['listItems'][$key]['goodstag'] = 0;
                $yyslist1['listItems'][$key]['codelimitbuy'] = 0;
            }
        } else {
            $yyslist1['code'] = 1;
        }
        echo json_encode($yyslist1);
    }

    //添加购物车
    public function addShopCart() {
        $ShopId = I("codeid", 0);
        $shopnum = I("shopNum", 0);
        $gouwuchebs = I("from", null);
        $shopis = 0;          //0表示不存在  1表示存在
        $Mcartlist = $this->getShopCart("Cartlistzg");
        $model = new \Think\Model;
        if ($ShopId == 0 || $shopnum <= 0) {
            $gouwuche['code'] = 1;   //表示添加失败
        } else {
            //限购
            if (!empty($ShopId) && $this->userinfo) {
                $huiyuan = $this->userinfo;
                $counts1 = $model->table("yys_yonghu_yys_recordzg")->where(array("uid" => $huiyuan['uid'], "shopid" => $ShopId))->select();
                for ($xs = 0; $xs < C("xiangou"); $xs++) {
                    $sums +=$counts1[$xs]['gonumber'];
                }
                $counts = intval($sums);
            }
            $xgs = $model->table("yys_shangpinzg")->where(array('id' => $ShopId))->find();
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
                cookie('Cartlistzg', json_encode($Mcartlist));
                $gouwuche['code'] = 0;   //表示添加成功	
            }
        }
        $gouwuche['num'] = count($Mcartlist);    //表示现在购物车有多少条记录
        echo json_encode($gouwuche);
    }

    public function delCartItem() {
        $ShopId = I("codeid", 0);
        $gouwuchelist = $this->getShopCart("Cartlistzg");

        if ($ShopId == 0) {
            $gouwuche['code'] = 1;   //删除失败
        } else {
            if (is_array($gouwuchelist)) {
                if (count($gouwuchelist) == 1) {
                    foreach ($gouwuchelist as $key => $val) {
                        if ($key == $ShopId) {
                            $gouwuche['code'] = 0;
                            cookie('Cartlistzg', '', '');
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

                    cookie('Cartlistzg', json_encode($Mcartlist), '');
                }
            } else {
                $gouwuche['code'] = 1;   //删除失败
            }
        }
        echo json_encode($gouwuche);
    }

}
