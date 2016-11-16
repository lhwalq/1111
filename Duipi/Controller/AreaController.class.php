<?php

/**
 * 组团专区
 * addtime 2016/08/19
 */

namespace Duipi\Controller;

use Think\Controller;

class AreaController extends BaseController {

    public $db;

    public function areaajax() {
        $parm = I("order");
        $p = I("p", 1);
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

        $count = D("shangpin")->order($sel . " DESC")->select();
        $shaidingdan = D("shangpin")->order($sel . " DESC")->limit($star, $end)->select();
        foreach ($shaidingdan as $sd) {
            $weer[] = $this->huode_user_name($sd['sd_userid']);
            $time[] = date("Y-m-d H:i", $sd['sd_time']);
            $huiyuan = D("yonghu")->where(array("uid" => $sd['sd_userid']))->find();
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

    //创建房间
    public function areacreat() {
        $u_id = $this->getUserInfo();
        $uid = $u_id['uid'];
        $area_id = I("area_id");

        if (empty($area_id)) {
            $this->notemobile("请输入房间编号！");
            exit;
        } else if (ereg("^[0-9]*[1-9][0-9]*$", $area_id) != 1 || strlen($area_id) > 5) {
            $this->notemobile("房间编号为不能超过5位数的正整数！");
            exit;
        }

        $selectarid = D("shangpin")->where("arid='$area_id' and auid<>'0'")->find();
        if ($selectarid['id'] != '') {
            if ($selectarid['q_end_time'] == '') {
                $this->notemobile("该编号正在使用，请输入其他编号！");
                exit;
            }
        }
        $password = I("password");
        if (empty($password)) {
            $this->notemobile("请输入房间密码！");
            exit;
        } else if (strlen($password) != 4 || !is_numeric($password)) {
            $this->notemobile("房间密码必须为4位数字！");
            exit;
        }
        $time = time();
		if(empty($uid)){
		$this->notemobile("请登陆", C("URL_DOMAIN") . "User/login");
		exit;
		}
        $res = D("shangpin")->add(array("arid" => $area_id, "apid" => $password, "auid" => $uid, "time" => $time));
        if ($res) {
            $this->notemobile("请选择房间商品", C("URL_DOMAIN") . "mobile/arealist");
        } else {
            $this->notemobile("房间创建失败");
        }
    }

    //创建房间
    public function add() {
        $id = htmlspecialchars(I("id")); //id
        $shangpin = D("shangpin")->where(array("id" => $id))->order("id")->find();
        $sid = $shangpin['sid']; //商品id
        $cateid = $shangpin['cateid']; //所属栏目ID
        $brandid = $shangpin['brandid']; //所属品牌ID
        $title = $shangpin['title']; //商品标题
        $title_style = $shangpin['title_style']; //商品标题
        $title2 = $shangpin['title2']; //副标题
        $keywords = $shangpin['keywords']; //关键词
        $description = $shangpin['description']; //描述
        $money = $shangpin['money']; //商品金额，小数点后两位
        $zongrenshu = $shangpin['zongrenshu']; //总需人数，剩余人数，原价，不带小数点
        $thumb = $shangpin['thumb']; //商品缩略图
        $picarr = $shangpin['picarr']; //商品图片
        $content = $shangpin['content']; //商品内容详情
        $seller_id = $shangpin['seller_id']; //商户
        $status = $shangpin['status']; //商户
        $u_id = $this->getUserInfo();
        $auid = $u_id['uid'];
        $sql = D("shangpin")->where(array("auid" => $auid, "sid" => 0))->order("id desc")->find();
        $iid = $sql["id"];
        $db = new \Think\Model();
        $db->startTrans();
        $data = array(
            "status" => $status, "seller_id" => $seller_id, "sid" => $sid, "cateid" => $cateid, "brandid" => $brandid, "codes_table" => 'shopcodes_1', "pos" => '0', "renqi" => '0', "title" => $title, "title_style" => $title_style, "title2" => $title2, "keywords" => $keywords, "description" => $description, "yuanjia" => $zongrenshu, "thumb" => $thumb, "picarr" => $picarr, "content" => $content, "qishu" => '1', "maxqishu" => '1', "xsjx_time" => '0', "q_counttime" => '', "canyurenshu" => '0', "zongrenshu" => $zongrenshu, "money" => $money, "yunjiage" => '1', "shenyurenshu" => $zongrenshu
        );
        $query_1 = D("shangpin")->where(array("id" => $iid))->save($data);
        $query_table = R("goods/content_get_codes_table");
        if (!$query_table) {
            $db->rollback();
            $this->notemobile("云购码仓库不正确!");
        }

        $query_2 = R("Goods/content_huode_go_codes", array($zongrenshu, 3000, $iid));
        $query_3 = D("shangpin")->where(array("id" => $iid))->save(array("codes_table" => $query_table, "sid" => $sid, "def_renshu" => '0'));
        //dump($query_1);dump($query_2);dump(D("shangpin")->getLastSql());exit;
        if ($query_1 && $query_2) {
            $db->commit();
            $this->notemobile("房间创建成功", C("URL_DOMAIN") . "goods/items/goodsId/" . $iid);
        } else {
            $db->rollback();
            $this->notemobile("房间创建失败");
        }

        header("Cache-control: private");
    }

    //快速加入
    public function fast() {
        $u_id = $this->getUserInfo();
        $uid = $u_id['uid'];
        $area_id = $_POST['area_id'];
        if (empty($area_id)) {
            $this->notemobile("请输入房间编号！");
            exit;
        } else if (ereg("^[0-9]*[1-9][0-9]*$", $area_id) != 1 || strlen($area_id) > 5) {
            $this->notemobile("房间编号为不能超过5位数的正整数！");
            exit;
        }
        $password = $_POST['password'];
        if (empty($password)) {
            $this->notemobile("请输入房间密码！");
            exit;
        } else if (strlen($password) != 4 || !is_numeric($password)) {
            $this->notemobile("房间密码必须为4位数字！");
            exit;
        }
        $sql = D("shangpin")->where(array("arid" => $area_id, "apid" => $password))->find();
        if ($sql['id'] != '') {
            cookie("room" . $sql['id'], "room" . $password, 1800);
            $this->notemobile("正在进入...", C("URL_DOMAIN") . "goods/items/goodsId/" . $sql['id']);
        } else {
            $this->notemobile("编号或密码错误！");
        }
    }

    //点击商品，输入密码
    public function sid() {
        $arid = $_POST['arid']; //id
        $apid = $_POST['password']; //password
        $sql = D("shangpin")->where(array("arid" => $arid, "apid" => $apid))->find();
        if ($sql['id'] != '') {
            cookie("room" . $sql['id'], "room" . $apid, 1800);
            $this->notemobile("正在进入...", C("URL_DOMAIN") . "goods/items/goodsId/" . $sql['id']);
        } else {
            $this->notemobile("编号或密码错误！");
        }
    }

    //获得专区商品列表
    public function getAreaPageList() {
        $_GET = array_change_key_case($_GET, CASE_LOWER);
        $cate_band = htmlspecialchars($_GET[sortid]);
        //var_dump($cate_band);
        $select = htmlspecialchars($_GET[orderflag]);
        $order = htmlspecialchars($_GET[orderflag]);
        $kaishi = htmlspecialchars($_GET[fidx]);
        $jieshu = htmlspecialchars($_GET[eidx]);
        $p = $kaishi;
        $sun_cate = D("fenlei")->where(array("parentid" => $cate_band))->field("cateid")->select();
        foreach ($sun_cate as $v) {
            $sun_id_str.= "'" . $v['cateid'] . "'" . ",";
        }
        $newstr = substr($sun_id_str, 0, strlen($sun_id_str) - 1);
        if ($newstr) {
            if (!$select) {
                $select = '10';
            }
            if ($cate_band) {
                $fen1 = intval($cate_band);
                $cate_band = 'list';
            }
            if (empty($fen1)) {
                $pinpai = D("pinpai")->order("`order` DESC")->select();
                $daohangs = '所有分类';
            } else {
                $pinpai = D("pinpai")->where(array("cateid" => $fen1))->order("`order` DESC")->select();
                $daohangs = D("fenlei")->where(array("cateid" => $fen1))->order("`order` DESC")->find();
                $daohangs = $daohangs['name'];
            }

            $fenlei = D("fenlei")->where(array("model" => '1'))->select();
            //
            //分页
            $end = $jieshu;
            //var_dump($end);
            $star = ($p - 1);
            //var_dump($star);
            $select_w = '';
            $select_w = 'arid ASC';
            if ($fen1) {
                $count = D("shangpin")->where("arid!=0 and sid!=0 and q_uid is null and cateid in ($newstr)")->order($select_w)->select();
            } else {
                $count = D("shangpin")->where("arid!=0 and sid!=0 and q_uid is null")->order($select_w)->select();
            }
            if ($fen1) {
                $yyslist = D("shangpin")->where("arid!=0 and sid!=0 and q_uid is null and cateid in ($newstr)")->order($select_w)->limit($star, $end)->select();
            } else {
                $yyslist = D("shangpin")->where("arid!=0 and sid!=0 and q_uid is null")->order($select_w)->limit($star, $end)->select();
            }
        }
        //var_dump(count($yyslist));
        //var_dump($yyslist);
        if ($yyslist) {
            $yyslist1['code'] = 0;
            $yyslist1['count'] = count($count);
            foreach ($yyslist as $key => $val) {
                $yyslist1['listItems'][$key]['rowid'] = 0;
                $yyslist1['listItems'][$key]['goodsid'] = $val['id'];
                $yyslist1['listItems'][$key]['goodssnme'] = $val['title'];
                $yyslist1['listItems'][$key]['goodspic'] = __ROOT__ . "/public/uploads/" . $val['thumb'];
                $yyslist1['listItems'][$key]['codeid'] = $val['id'];
                $yyslist1['listItems'][$key]['codeprice'] = $val['money'];
                $yyslist1['listItems'][$key]['codequantity'] = $val['zongrenshu'];
                $yyslist1['listItems'][$key]['codesales'] = $val['canyurenshu'];
                $yyslist1['listItems'][$key]['codeperiod'] = $val['qishu'];
                $yyslist1['listItems'][$key]['codetype'] = 0;
                $yyslist1['listItems'][$key]['goodstag'] = 0;
                $yyslist1['listItems'][$key]['codelimitbuy'] = 0;
                $yyslist1['listItems'][$key]['arid'] = $val['arid'];
                $uid = $val['shopid'];

                $name = D("yonghu")->where(array("uid" => $uid))->find();
                $yyslist1['listItems'][$key]['shopname'] = $name['username'];
            }
        } else {
            $yyslist1['code'] = 1;
        }
        echo json_encode($yyslist1);
    }

    //获得专区商品
    public function getAreaListPageList() {
        $_GET = array_change_key_case($_GET, CASE_LOWER);
        $webname = $this->_yys['web_name'];
        $cate_band = htmlspecialchars($_GET[sortid]);
        //var_dump($cate_band);
        $select = htmlspecialchars($_GET[orderflag]);
        $order = htmlspecialchars($_GET[orderflag]);
        $kaishi = htmlspecialchars($_GET[fidx]);
        $jieshu = htmlspecialchars($_GET[eidx]);
        $p = $kaishi;

        $sun_cate = D("fenlei")->where(array("parentid" => $cate_band))->select();
        foreach ($sun_cate as $v) {
            $sun_id_str.= "'" . $v['cateid'] . "'" . ",";
            //var_dump($sun_id_str);
        }
        $newstr = substr($sun_id_str, 0, strlen($sun_id_str) - 1);
        if ($newstr) {
            if (!$select) {
                $select = '10';
            }
            if ($cate_band) {
                $fen1 = intval($cate_band);
                $cate_band = 'list';
            }
            //var_dump($fen1);
            if (empty($fen1)) {
                $pinpai = D("pinpai")->order("`order` DESC")->select();
                $daohangs = '所有分类';
            } else {

                $pinpai = D("pinpai")->where(array("cateid" => $fen1))->order("`order` DESC")->select();
                $daohangs = D("fenlei")->where(array("cateid" => $fen1))->order("`order` DESC")->find();
                $daohangs = $daohangs['name'];
            }
            $fenlei = D("fenlei")->where(array("model" => '1'))->select();
            //分页
            $end = $jieshu;
            //var_dump($end);
            $star = ($p - 1);
            $select_w = '';
            if ($select == 10) {
                $select_w = 'money DESC';
            }
            if ($select == 20) {
                $select_w = 'money ASC';
            }
            if ($fen1) {
                $count = D("shangpin")->where("arid=0 and q_uid is null and cateid in ($newstr)")->order($select_w)->select();
            } else {
                $count = D("shangpin")->where("arid=0 and q_uid is null")->order($select_w)->select();
            }
            if ($fen1) {
                $yyslist = D("shangpin")->where("arid=0 and q_uid is null and cateid in ($newstr)")->order($select_w)->limit($star, $end)->select();
            } else {
                $yyslist = D("shangpin")->where("arid=0 and q_uid is null")->order($select_w)->limit($star, $end)->select();
            }
        }
        //var_dump(count($yyslist));
        //var_dump($yyslist);
        if ($yyslist) {
            $yyslist1['code'] = 0;
            $yyslist1['count'] = count($count);
            foreach ($yyslist as $key => $val) {
                $yyslist1['listItems'][$key]['rowid'] = 0;
                $yyslist1['listItems'][$key]['goodsid'] = $val['id'];
                $yyslist1['listItems'][$key]['goodssnme'] = $val['title'];
                $yyslist1['listItems'][$key]['goodspic'] = __ROOT__ . "/public/uploads/" . $val['thumb'];
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

}

?>