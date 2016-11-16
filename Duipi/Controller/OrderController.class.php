<?php

/**
 * 订单
 * addtime 2016/03/23
 */

namespace Duipi\Controller;

use Think\Controller;

class OrderController extends BaseController {

    public function _initialize() {
        $this->getAdminRight(CONTROLLER_NAME, ACTION_NAME);
        $ment = array(
            array("lists", "订单列表", C("URL_DOMAIN") . "order/lists"),
            array("lists", "中奖订单", C("URL_DOMAIN") . "order/lists/type/zj"),
            array("lists", "已发货", C("URL_DOMAIN") . "order/lists/type/sendok"),
            array("lists", "未发货", C("URL_DOMAIN") . "order/lists/type/notsend"),
            array("insert", "已完成", C("URL_DOMAIN") . "order/lists/type/ok"),
            array("insert", "已作废", C("URL_DOMAIN") . "order/lists/type/del"),
            array("insert", "待收货", C("URL_DOMAIN") . "order/lists/type/shouhuo"),
            array("genzhong", "快递跟踪", C("URL_DOMAIN") . "order/genzhong"),
        );
        $this->assign("ment", $ment);
    }

//商品购买记录
    public function buyrecords() {
        $key = "所有一元云购记录";
        $xiangmuid = I("id", 0);
        $cords = D("yonghu_yys_record")->where(array("shopid" => $xiangmuid))->order("time desc")->select();
        $this->assign("xiangmuid", $xiangmuid);
        $this->assign("cords", $cords);
        $this->display("mobile/index.buyrecords");
    }

    /**
     * 购买记录
     */
    public function getBuyRecord() {
        $xiangmuid = I("codeid", 0);
        $fidx = I("fIdx", 0) - 1;
        $eidx = I("eIdx", 0) - 1;
        if (!$xiangmuid || $fidx == -1 || $eidx == -1) {
            $this->myReturn(1, "参数错误", 0);
        }
        $db_user_record = D("yonghu_yys_record");
        $goumaijilu = $db_user_record->where(array("shopid" => $xiangmuid))->order("time desc")->limit($fidx, $eidx)->select();
        $count = $db_user_record->where(array("shopid" => $xiangmuid))->select();
        if (!empty($count)) {
            $yyslist['Code'] = 0;
            $yyslist['Count'] = count($count) - 1;
            foreach ($goumaijilu as $key => $val) {
                $yyslist['Data']['Tables']['BuyList']['Rows'][$key]['username'] = $val['username'];
                $yyslist['Data']['Tables']['BuyList']['Rows'][$key]['userphoto'] = $val['uphoto'];
                $yyslist['Data']['Tables']['BuyList']['Rows'][$key]['userweb'] = $val['uid'];
                $yyslist['Data']['Tables']['BuyList']['Rows'][$key]['buynum'] = $val['gonumber'];
                $yyslist['Data']['Tables']['BuyList']['Rows'][$key]['buyip'] = $val['ip'];
                $yyslist['Data']['Tables']['BuyList']['Rows'][$key]['buyips'] = $val['ip'];
                $yyslist['Data']['Tables']['BuyList']['Rows'][$key]['buyipaddr'] = 5;
                $yyslist['Data']['Tables']['BuyList']['Rows'][$key]['buytime'] = $this->microt($val['time']);
                $yyslist['Data']['Tables']['BuyList']['Rows'][$key]['buydevice'] = 6;
                $yyslist['Data']['Tables']['BuyList']['Rows'][$key]['buyid'] = $val['shopid'];
            }
        } else {
            $yyslist['Code'] = 1;
        }
        echo json_encode($yyslist);
    }

//admin 充值记录
    public function recharge() {
        $wheres = "(content='充值' or content='使用佣金充值到全民夺彩账户' or content='使用余额宝充值到全民夺彩账户') AND a.type='1'";
        if (isset($_POST['sososubmit'])) {
            $posttime1 = isset($_POST['posttime1']) ? $_POST['posttime1'] : '';
            $posttime2 = isset($_POST['posttime2']) ? $_POST['posttime2'] : '';
            if (empty($posttime1) || empty($posttime2)) {
                $this->note("2个时间都不为能空！");
            }
            if (!empty($posttime1) && !empty($posttime2)) { //如果2个时间都不为空
                $posttime1 = strtotime($posttime1);
                $posttime2 = strtotime($posttime2);
                if ($posttime1 > $posttime2) {
                    $this->note("前一个时间不能大于后一个时间");
                }
                $times = "a.time>='$posttime1' AND a.time<='$posttime2'";
            }
            $chongzhi = isset($_POST['chongzhi']) ? $_POST['chongzhi'] : '';
            $content = !empty($chongzhi) && $chongzhi != '请选择充值来源' ? " AND content='$chongzhi'" : " AND (content='充值' or content='管理员修改金额' or content='使用佣金充值到全民夺彩账户' or content='使用余额宝充值到全民夺彩账户')";

            $yonghu = isset($_POST['yonghu']) ? $_POST['yonghu'] : '';
            if (empty($yonghu) || $yonghu == '请选择用户类型') {
                $uid = ' AND 1';
            }
            $yonghuzhi = isset($_POST['yonghuzhi']) ? $_POST['yonghuzhi'] : '';
            if ($yonghu == '用户id') {
                if ($yonghuzhi) {
                    $uid = " AND a.uid='$yonghuzhi'";
                } else {
                    $uid = ' AND 1';
                }
            }

            $key = $yonghu == '用户名称' ? "username" : null;
            $key = $yonghu == '用户邮箱' ? "email" : null;
            $key = $yonghu == '用户手机' ? "mobile" : null;
            $uid = ' AND 1';
            if ($yonghuzhi) {
                $weer_uid = D("yonghu")->where(array("$key" => "$yonghuzhi"))->field("uid")->find();
                $uid = $weer_uid ? " AND a.uid='{$weer_uid['uid']}'" : null;
                if (!$uid) {
                    $this->note($yonghuzhi . "用户不存在！");
                }
            }
            $wheres = $times . $content . $uid;
            $this->assign("posttime1", $posttime1);
            $this->assign("posttime2", $posttime2);
            $this->assign("chongzhi", $chongzhi);
            $this->assign("yonghu", $yonghu);
            $this->assign("yonghuzhi", $yonghuzhi);
        }
        $num = 20;

        $zongji = D("yonghu_zhanghao a")->where($wheres)->count();
        $summoeny = D("yonghu_zhanghao a")->where($wheres)->field("sum(money) sum_money")->find();

        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");
        $recharge = D("yonghu_zhanghao a")->join("yys_yonghu b on a.uid = b.uid")->where($wheres)->field("a.*,b.username,b.email,b.mobile")->order("a.time desc")->limit(($fenyenum - 1) * $num, $num)->select();
        $this->assign("recharge", $recharge);
        $this->assign("fenye", $fenye);
        $this->assign("zongji", $zongji);
        $this->assign("ment", R('member/returnMent', array()));
        $this->assign("summoeny", $summoeny);
        $this->display("admin/member.recharge");
    }

    public function returnShaidan() {
        $ment = array(
            array("lists", "晒单管理", C("URL_DOMAIN") . "order/shaidan_admin"),
            array("addcate", "晒单回复管理", C("URL_DOMAIN") . "order/sd_hueifu"),
            array("addcate", "未晒单管理", C("URL_DOMAIN") . "order/not_shaidan"),
        );
        return $ment;
    }

    public function shaidan_admin() {
        $num = 20;
        $db_shai = D("shai");
        $zongji = $db_shai->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "1");
        if ($fenyenum > $fenye->page) {
            $fenyenum = $fenye->page;
        }
		//分页fix by dabin start 
		if($fenyenum<=0){
		$fenyenum=1;
		}
		//fix by dabin end 
        $shaidingdan = $db_shai->limit(($fenyenum - 1) * $num, $num)->select();
        $this->assign("ment", $this->returnShaidan());
        $this->assign("fenye", $fenye);
        $this->assign("zongji", $zongji);
        $this->assign("shaidingdan", $shaidingdan);
        $this->display("admin/shaidan.list");
    }

    public function not_shaidan() {
        $db_record = D("yonghu_yys_record");
        $cord = $db_record->field("shopid,shopname")->where("huode > '10000000'")->select();
//已晒单		
        $shaidan = D("shai")->order("sd_time")->select();
        $sd_id = $r_id = array();
        foreach ($shaidan as $sd) {
            $sd_id[] = $sd['sd_shopid'];
        }
        foreach ($cord as $rd) {
            if (!in_array($rd['shopid'], $sd_id)) {
                $r_id[] = $rd['shopid'];
            }
        }

        if (!empty($r_id)) {
            $rd_id = implode(",", $r_id);
            $rd_id = trim($rd_id, ',');
        } else {
            $rd_id = "0";
        }
        $zongji = $db_record->field("id")->where("shopid in ($rd_id) and `huode`>'10000000'")->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, 10, $fenyenum, "0");
        $shaidingdan = $db_record->where("shopid in ($rd_id) and `huode`>'10000000'")->order("id desc")->limit(($fenyenum - 1) * 10, 10)->select();

        $this->assign("ment", $this->returnShaidan());
        $this->assign("fenye", $fenye);
        $this->assign("zongji", $zongji);
        $this->assign("shaidingdan", $shaidingdan);
        $this->display("admin/shaidan.list1");
    }

    public function editShaidan() {
        $shangpinid = I("id", 0);

        $shangpin = D("shangpin")->where(array("id" => "$shangpinid"))->find();
        $sd_shopsid = $shangpin['id'];
        if (isset($_POST['dosubmit'])) {
            $sd_title = htmlspecialchars($_POST['title']);
            $content1 = $this->editor_safe_replace(stripslashes($_POST['content']));
            $tags = array(
                "'<iframe[^>]*?>.*?</iframe>'is",
                "'<frame[^>]*?>.*?</frame>'is",
                "'<script[^>]*?>.*?</script>'is",
                "'<head[^>]*?>.*?</head>'is",
                "'<title[^>]*?>.*?</title>'is",
                "'<meta[^>]*?>'is",
                "'<link[^>]*?>'is",
                "'<p[^>]*?>'is",
                "'</p[^>]*?>'is",
            );
            $sd_content1 = stripslashes($content1);
            $sd_content = preg_replace($tags, "", $sd_content1);
            if (isset($_POST['uppicarr'])) {
                $picarr = serialize($_POST['uppicarr']);
            } else {
                $picarr = serialize(array());
            }
            $sd_photolist = unserialize($picarr);
            foreach ($sd_photolist as $value) {
                $hhhhh.= $value . ";";
            }
            $sd_time = time();
            $sd_ip = $this->huode_ip_dizhi();
            $data = array("shenhe" => "1", "sd_shopid" => "{$shangpin['id']}", "sd_userid" => "{$shangpin['q_uid']}", "sd_qishu" => "{$shangpin['qishu']}", "sd_ip" => "$sd_ip", "sd_shopsid" => "{$shangpin['sid']}", "sd_title" => "$sd_title", "sd_thumbs" => "$sd_photolist[0]", "sd_content" => "$sd_content", "sd_photolist" => "$hhhhh", "sd_time" => "$sd_time");
            D("shai")->add($data);
            $this->note("晒单分享成功", C("URL_DOMAIN") . "/order/shaidan_admin");
        }
        $this->assign("shangpin", $shangpin);
        $this->display("admin/shaidan.list2");
    }

    public function sd_del() {
        $id = I("id", 0);
        $db_shai = D("shai");
        $shaidingdanx = $db_shai->where(array("sd_id" => "$id"))->find();
        if ($shaidingdanx) {
            $db_shai->where(array("sd_id" => "$id"))->delete();
            $this->note("删除成功");
        } else {
            $this->note("参数错误");
        }
    }

    public function hf_del() {
        $id = I("id", 0);
        $db_shai = D("shai_hueifu");
        $shaidingdanx = $db_shai->where(array("id" => "$id"))->find();
        if ($shaidingdanx) {
            $db_shai->where(array("id" => "$id"))->delete();
            $this->note("删除成功");
        } else {
            $this->note("参数错误");
        }
    }

    public function sd_hueifu() {
//$huiyuan = $this->db->Ylist("select * from `@#_yonghu`");
        $num = 20;
        $db_hueifu = D("shai_hueifu a");
        $zongji = $db_hueifu->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p']) && $_GET['p'] > 0) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");
        $shaidingdan = $db_hueifu->field("a.*,b.username,b.email,b.mobile")->join("yys_yonghu b on a.sdhf_userid = b.uid")->limit(($fenyenum - 1) * 10, 10)->select();
        $this->assign("ment", $this->returnShaidan());
        $this->assign("fenye", $fenye);
        $this->assign("zongji", $zongji);
        $this->assign("shaidingdan", $shaidingdan);
        $this->display("admin/shaidan.liuyan");
    }

    public function lists() {
        /*
          已付款,未发货,已完成
          未付款,已发货,已作废
          已付款,未发货,待收货
         */
        $where = I("type", "");
        if (!$where) {
            $list_where = "a.status LIKE '%已付款%'";
        } elseif ($where == 'zj') {
//中奖		
            $list_where = "a.huode > '0'";
        } elseif ($where == 'sendok') {
//已发货订单
            $list_where = "a.huode > '0' and  a.status LIKE  '%已发货%'";
        } elseif ($where == 'notsend') {
//未发货订单
            $list_where = "a.huode > '0' and a.status LIKE  '%未发货%'";
        } elseif ($where == 'ok') {
//已完成
            $list_where = "a.huode > '0' and  a.status LIKE  '%已完成%'";
        } elseif ($where == 'del') {
//已作废		
            $list_where = "a.status LIKE  '%已作废%'";
        } elseif ($where == 'gaisend') {
//该发货			
            $list_where = "a.huode > '0' and a.status LIKE  '%未发货%'";
        } elseif ($where == 'shouhuo') {
//该发货			
            $list_where = " a.status LIKE  '%待收货%'";
        }

        if (isset($_POST['paixu_submit'])) {
            $order = "";
            $paixu = $_POST['paixu'];
            if ($paixu == 'time1') {
                $order = "a.time DESC";
            }
            if ($paixu == 'time2') {
                $order = "a.time ASC";
            }
            if ($paixu == 'num1') {
                $order = "a.gonumber DESC";
            }
            if ($paixu == 'num2') {
                $order = "a.gonumber ASC";
            }
            if ($paixu == 'money1') {
                $order = "a.moneycount DESC";
            }
            if ($paixu == 'money2') {
                $order = "a.moneycount ASC";
            }
        } else {
            $order = "a.time DESC";
            $paixu = 'time1';
        }

        $num = 20;
        $zongji = D("yonghu_yys_record a")->join("yys_yonghu b on b.uid = a.uid")->where($list_where)->count();

        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");
        if ($where == 'feijiqi') {
            $zongji = D("yonghu a")->join("yys_yonghu_yys_record b on b.uid = a.uid")->where("a.huiyuan = 0 and b.huode >1")->count();
            $recordlist = D("yonghu b")->join("yys_yonghu_yys_record a on b.uid = a.uid")->where("b.huiyuan = 0 and a.huode >1")->limit(($fenyenum - 1) * $num, $num)->select();
        } else {
            $recordlist = D("yonghu_yys_record a")->join("yys_yonghu b on b.uid = a.uid")->field("a.*,b.huiyuan")->where($list_where)->order($order)->limit(($fenyenum - 1) * $num, $num)->select();
        }

        $this->assign("zongji", $zongji);
        $this->assign("recordlist", $recordlist);
        $this->assign("fenye", $fenye);
        $this->display("admin/dingdan.list");
    }

//订单详细
    public function get_dingdan() {
        $code = abs(I("id", 0));
        $record = D("yonghu_yys_record")->where(array("id" => $code))->find();
        if (!$record) {
            $this->note("参数不正确!");
        }
        if (isset($_POST['submit'])) {
            $record_code = explode(",", $record['status']);
            $status = $_POST['status'];
            $company = $_POST['company'];
            $company_code = $_POST['company_code'];
            $company_money = floatval($_POST['company_money']);
            $code = abs(intval($_POST['id']));
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
            if ($status == '已完成') {
                $status = '已付款,已发货,已完成';
            }
            if ($status == '已作废') {
                $status = $record_code[0] . ',' . $record_code[1] . ',' . '已作废';
            }
            $ret = D("yonghu_yys_record")->where(array("id" => $code))->save(array("status" => "$status", "company" => "$company", "company_code" => "$company_code", "company_money" => "$company_money"));

            if ($ret) {
                $this->note("更新成功");
            } else {
                $this->note("更新失败");
            }
        }
        $uid = $record['uid'];
        $weer = D("yonghu")->where(array("uid" => $uid))->find();
        $weer_dizhi = D("yonghu_yys_record")->where(array("uid" => $uid, "id" => $code))->find();
        $go_time = $record['time'];

        $shopid = $record['shopid'];
        $shop = D("shangpin")->where(array("id" => "$shopid"))->find();

        $this->assign("weer", $weer);
        $this->assign("record", $record);
        $this->assign("shop", $shop);
        $this->assign("weer_dizhi", $weer_dizhi);
        $this->assign("go_time", $go_time);
        $this->display("admin/dingdan.code");
    }

//订单搜索
    public function select() {
        $record = '';
        if (isset($_POST['codesubmit'])) {
            $code = htmlspecialchars($_POST['text']);
            $record = D("yonghu_yys_record")->where(array("code" => "$code"))->select();
        }

        if (isset($_POST['usersubmit'])) {
            if ($_POST['user'] == 'uid') {
                $uid = intval($_POST['text']);
                $record = D("yonghu_yys_record")->where(array("uid" => "$uid"))->select();
            }
        }

        if (isset($_POST['nichengsubmit'])) {
            if ($_POST['user'] == 'uid') {
                $uid = $_POST['text'];
                $record = D("yonghu_yys_record")->where(array("username" => "$uid"))->select();
            }
        }
        if (isset($_POST['shopsubmit'])) {
            if ($_POST['shop'] == 'sid') {
                $sid = intval($_POST['text']);
                $record = D("yonghu_yys_record")->where(array("shopid" => "$sid"))->select();
            }
            if ($_POST['shop'] == 'sname') {
                $sname = htmlspecialchars($_POST['text']);
                $record = D("yonghu_yys_record")->where(array("shopname" => "$sname"))->select();
            }
        }
        if (isset($_POST['timesubmit'])) {
            $start_time = strtotime($_POST['posttime1']) ? strtotime($_POST['posttime1']) : time();
            $end_time = strtotime($_POST['posttime2']) ? strtotime($_POST['posttime2']) : time();
            $record = D("yonghu_yys_record")->where("time > '$start_time' and time < '$end_time'")->select();
        }
        $this->assign("record", $record);
        $this->display("admin/dingdan.soso");
    }

    public function genzhong() {
        $this->display("admin/dingdan.genzhong");
    }

//消费记录
    public function pay_list() {
        $wheres = "1=1";
        if (isset($_POST['sososubmit'])) {
            $posttime1 = isset($_POST['posttime1']) ? $_POST['posttime1'] : '';
            $posttime2 = isset($_POST['posttime2']) ? $_POST['posttime2'] : '';
            if (empty($posttime1) || empty($posttime2)) {
                $this->note("2个时间都不为能空！");
            }
            if (!empty($posttime1) && !empty($posttime2)) { //如果2个时间都不为空
                $posttime1 = strtotime($posttime1);
                $posttime2 = strtotime($posttime2);
                if ($posttime1 > $posttime2) {
                    $this->note("前一个时间不能大于后一个时间");
                }
                $times = "a.time>='$posttime1' AND a.time<='$posttime2'";
            }

            $yonghu = isset($_POST['yonghu']) ? $_POST['yonghu'] : '';
            if (empty($yonghu) || $yonghu == '请选择用户类型') {
                $uid = ' AND 1';
            }
            $yonghuzhi = isset($_POST['yonghuzhi']) ? $_POST['yonghuzhi'] : '';
            if ($yonghu == '用户id') {
                if ($yonghuzhi) {
                    $uid = " AND a.uid='$yonghuzhi'";
                } else {
                    $uid = ' AND 1';
                }
            }
            if ($yonghu == '用户名称') {
                if ($yonghuzhi) {
                    $weer_uid = D("yonghu")->field("uid")->where(array("username" => "$yonghuzhi"))->find();
                    if ($weer_uid) {
                        $uid = " AND a.uid='{$weer_uid['uid']}'";
                    } else {
                        $this->note($yonghuzhi . "用户不存在！");
                        $uid = ' AND 1';
                    }
                } else {
                    $uid = ' AND 1';
                }
            }
            if ($yonghu == '用户邮箱') {
                if ($yonghuzhi) {
                    $weer_uid = D("yonghu")->field("uid")->where(array("email" => "$yonghuzhi"))->find();
                    if ($weer_uid) {
                        $uid = " AND a.uid='{$weer_uid['uid']}'";
                    } else {
                        $this->note($yonghuzhi . "用户不存在！");
                        $uid = ' AND 1';
                    }
                } else {
                    $uid = ' AND 1';
                }
            }
            if ($yonghu == '用户手机') {
                if ($yonghuzhi) {
                    $weer_uid = D("yonghu")->field("uid")->where(array("mobile" => "$yonghuzhi"))->find();
                    if ($weer_uid) {
                        $uid = " AND uid='{$weer_uid['uid']}'";
                    } else {
                        $this->note($yonghuzhi . "用户不存在！");
                        $uid = ' AND 1';
                    }
                } else {
                    $uid = ' AND 1';
                }
            }
            $wheres = $times . $uid;
        }
        $num = 20;
        $zongji = D("yonghu_yys_record a")->join("yys_yonghu b on a.uid = b.uid")->where($wheres)->count();

        $summoeny = D("yonghu_yys_record a")->where($wheres)->field("sum(moneycount) sum_money")->find();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");
        $pay_list = D("yonghu_yys_record a")->join("yys_yonghu b on a.uid = b.uid")->where($wheres)->field("a.*,b.username,b.email,b.mobile")->order("time desc")->limit(($fenyenum - 1) * $num, $num)->select();

        $this->assign("pay_list", $pay_list);
        $this->assign("summoeny", $summoeny);
        $this->assign("fenye", $fenye);
        $this->assign("zongji", $zongji);
        $this->display("admin/member.pay_list");
    }

//佣金提现申请管理  后台提现记录
    public function WithdrawCommissions() {
        $num = 20;
        $zongji = D("yonghu_cashout")->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");
        $commissions = D("yonghu_cashout")->limit(($fenyenum - 1) * $num, $num)->select();
//查询用户名
        if (!empty($commissions)) {
            foreach ($commissions as $key => $val) {
                $uid = $val['uid'];
                $weer[$key] = D("yonghu")->where(array("uid" => "$uid"))->field("username")->find();
            }
        }
        $this->assign("ment", R("member/returnMent"));
        $this->assign("zongji", $zongji);
        $this->assign("weer", $weer);
        $this->assign("commissions", $commissions);
        $this->assign("fenye", $fenye);
        $this->display("admin/member.commissions");
    }

    public function commreview1() {
        $id = intval(I("id", 0));
        $audsta = D("yonghu_cashout1")->where(array("id" => $id))->find();
        $is = D("yonghu_cashout1")->where(array("id" => $id))->save(array("shenhe" => "1"));
        if ($is == 1) {
//审核通过后将该数据插入到佣金记录表中
            $type = -3;
            $content = "积分提现";
            $time = time();
            $data = array("uid" => $audsta['uid'], "type" => $type, "pay" => "积分", "content" => $content, "money" => $audsta['money'], "time" => $audsta['time']);
            D("yonghu_zhanghao1")->add($data);
            $this->note("审核成功！");
        } else {
            $this->note("审核失败！");
        }
    }

//佣金提现申请审核
    public function commreview() {
        $id = I("id", 0);
        $audsta = D("yonghu_cashout")->where(array("id" => $id))->find();
        $is = D("yonghu_cashout")->where(array("id" => $id))->save(array("auditstatus" => "1"));
        if ($is) {
//审核通过后将该数据插入到佣金记录表中
            $data = array("uid" => $audsta['uid'], "type" => -3, "content" => "提现", "money" => $audsta['money'], "time" => $audsta['time'], "cashoutid" => $audsta['id']);
            D("yonghu_recodes")->add($data);
            $this->note("审核成功！");
        } else {
            $this->note("审核失败！");
        }
    }

    public function commissions1() {
        $p_key = I("type", 0);
        $num = 20;
        $zongji = D("yonghu_cashout1")->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");

        $commissions = '';
        switch ($p_key) {
            case 'no':
                $commissions = D("yonghu_cashout1")->where(array("shenhe" => 0))->limit(($fenyenum - 1) * $num, $num)->select();
                break;
            default:
                $commissions = D("yonghu_cashout1")->order("time desc")->limit(($fenyenum - 1) * $num, $num)->select();
        }
//查询用户名
        if (!empty($commissions)) {
            foreach ($commissions as $key => $val) {
                $uid = $val['uid'];
                $user[$key] = D("yonghu")->where(array("uid" => "$uid"))->field("username")->find();
            }
        }
        $this->assign("ment", R("member/returnMent"));
        $this->assign("zongji", $zongji);
        $this->assign("weer", $weer);
        $this->assign("commissions", $commissions);
        $this->assign("fenye", $fenye);
        $this->display("admin/member.commissions1");
    }

    public function commissionsDetail() {
        $p_key = I("type", 0);
        $num = 20;
        $zongji = D("yonghu_cashout1 a")->join("yys_yonghu b on b.uid=a.uid")->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");
        $commissions = D("yonghu_cashout1 a")->join("yys_yonghu b on b.uid=a.uid")->order("a.time desc")->limit(($fenyenum - 1) * $num, $num)->select();
//查询用户名
        $this->assign("ment", R("member/returnMent"));
        $this->assign("zongji", $zongji);
        $this->assign("commissions", $commissions);
        $this->assign("fenye", $fenye);
        $this->display("admin/member.commissions2");
    }

//转账
    public function oplist() {
        $wheres = "1=1";
        if (isset($_POST['sososubmit'])) {
            $posttime1 = isset($_POST['posttime1']) ? $_POST['posttime1'] : '';
            $posttime2 = isset($_POST['posttime2']) ? $_POST['posttime2'] : '';
            if (!empty($posttime1) && !empty($posttime2)) { //如果2个时间都不为空
                $posttime1 = strtotime($posttime1);
                $posttime2 = strtotime($posttime2);
                if ($posttime1 > $posttime2) {
                    $this->note("前一个时间不能大于后一个时间");
                }
                $times = "a.time>='$posttime1' AND a.time<='$posttime2'";
            } else {
                $times = "1";
            }

            $yonghu = isset($_POST['yonghu']) ? $_POST['yonghu'] : '';
            if (empty($yonghu) || $yonghu == '请选择用户类型') {
                $uid = ' AND 1';
            }
            $yonghuzhi = isset($_POST['yonghuzhi']) ? $_POST['yonghuzhi'] : '';
            if ($yonghu == '用户id') {
                if ($yonghuzhi) {
                    $uid = " AND a.uid='$yonghuzhi'";
                } else {
                    $uid = ' AND 1';
                }
            }

            $key = $yonghu == '用户名称' ? "username" : null;
            $key = $yonghu == '用户邮箱' ? "email" : null;
            $key = $yonghu == '用户手机' ? "mobile" : null;
            $uid = ' AND 1';
            if ($yonghuzhi) {
                $user_uid = D("yonghu")->where(array("$key" => "$yonghuzhi"))->field("uid")->find();
                $uid = $user_uid ? " AND a.uid='{$weer_uid['uid']}'" : null;
                if (!$uid) {
                    $this->note($yonghuzhi . "用户不存在！");
                }
            }
            $wheres = $times . $uid;
            $this->assign("posttime1", $posttime1);
            $this->assign("posttime2", $posttime2);
            $this->assign("yonghu", $yonghu);
            $this->assign("yonghuzhi", $yonghuzhi);
        }
        $num = 20;
        $summoeny = D("yonghu")->field("sum(money) sum_money")->find();
        $total = D("yonghu_op_record a")->where($wheres)->count();
        $page = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $pagenum = $_GET['p'];
        } else {
            $pagenum = 1;
        }
        $page->config($total, $num, $pagenum, "0");
        $recharge = D("yonghu_op_record a")->join("yys_yonghu b on b.uid=a.uid")->field("a.*,b.username,b.email,b.mobile")->order("a.time desc")->limit(($pagenum - 1) * $num, $num)->select();
        $this->assign("ment", R("member/returnMent"));
        $this->assign("summoeny", $summoeny);
        $this->assign("total", $total);
        $this->assign("recharge", $recharge);
        $this->assign("page", $page);
        $this->display("admin/member.oplist");
    }

    public function set() {
        if (!isset($_POST['uid']) || !isset($_POST['oid'])) {
            exit;
        }
        
        $uid = abs(intval($_POST['uid']));
        $oid = abs(intval($_POST['oid']));
        
        if (!$oid || !$uid) {
            echo "0";
            exit;
        }

        $info = D("yonghu_yys_record")->where(array("id" => $oid, "uid" => $uid))->find();
        if (!$info)
            $this->note("参数错误");
        $status = @explode(",", $info['status']);
        if (is_array($status) && $status[1] == '已发货') {
            $status = '已付款,已发货,已完成';
            $q = D("yonghu_yys_record")->where(array("id" => $oid))->save(array("status" => $status));
            echo $q ? '1' : '0';
        } else {
            echo "0";
        }
    }

    public function setzg() {
        if (!isset($_POST['uid']) || !isset($_POST['oid'])) {
            exit;
        }
        $uid = abs(intval($_POST['uid']));
        $oid = abs(intval($_POST['oid']));
        if (!$oid || !$uid) {
            echo "0";
            exit;
        }

        $info = D("yonghu_yys_recordzg")->where(array("id" => $oid, "uid" => $uid))->find();
        if (!$info)
            $this->note("参数错误");
        $status = @explode(",", $info['status']);
        if (is_array($status) && $status[1] == '已发货') {
            $status = '已付款,已发货,已完成';
            $q = D("yonghu_yys_recordzg")->where(array("id" => $oid))->save(array("status" => $status));
            echo $q ? '1' : '0';
        } else {
            echo "0";
        }
    }

//直购订单列表
    public function listszg() {
        /*
          已付款,未发货,已完成
          未付款,已发货,已作废
          已付款,未发货,待收货
         */
        $where = I("type", "");
        if (!$where) {
            $list_where = "a.status LIKE '%已付款%'";
        } elseif ($where == 'zj') {
//中奖		
            $list_where = "a.huode > '0'";
        } elseif ($where == 'sendok') {
//已发货订单
            $list_where = "a.huode > '0' and  a.status LIKE  '%已发货%'";
        } elseif ($where == 'notsend') {
//未发货订单
            $list_where = "a.huode > '0' and a.status LIKE  '%未发货%'";
        } elseif ($where == 'ok') {
//已完成
            $list_where = "a.huode > '0' and  a.status LIKE  '%已完成%'";
        } elseif ($where == 'del') {
//已作废		
            $list_where = "a.status LIKE  '%已作废%'";
        } elseif ($where == 'gaisend') {
//该发货			
            $list_where = "a.huode > '0' and a.status LIKE  '%未发货%'";
        } elseif ($where == 'shouhuo') {
//该发货			
            $list_where = " a.status LIKE  '%待收货%'";
        }

        if (isset($_POST['paixu_submit'])) {
            $order = "";
            $paixu = $_POST['paixu'];
            if ($paixu == 'time1') {
                $order = "a.time DESC";
            }
            if ($paixu == 'time2') {
                $order = "a.time ASC";
            }
            if ($paixu == 'num1') {
                $order = "a.gonumber DESC";
            }
            if ($paixu == 'num2') {
                $order = "a.gonumber ASC";
            }
            if ($paixu == 'money1') {
                $order = "a.moneycount DESC";
            }
            if ($paixu == 'money2') {
                $order = "a.moneycount ASC";
            }
        } else {
            $order = "a.time DESC";
            $paixu = 'time1';
        }

        $num = 20;
        $zongji = D("yonghu_yys_recordzg a")->join("yys_yonghu b on b.uid = a.uid")->where($list_where)->count();

        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");
        if ($where == 'feijiqi') {
            $zongji = D("yonghu a")->join("yys_yonghu_yys_recordzg b on b.uid = a.uid")->where("a.huiyuan = 0 and b.huode >1")->count();
            $recordlist = D("yonghu b")->join("yys_yonghu_yys_recordzg a on b.uid = a.uid")->where("b.huiyuan = 0 and a.huode >1")->limit(($fenyenum - 1) * $num, $num)->select();
        } else {
            $recordlist = D("yonghu_yys_recordzg a")->join("yys_yonghu b on b.uid = a.uid")->field("a.*,b.huiyuan")->where($list_where)->order($order)->limit(($fenyenum - 1) * $num, $num)->select();
        }

        $this->assign("zongji", $zongji);
        $this->assign("recordlist", $recordlist);
        $this->assign("fenye", $fenye);
        $this->display("admin/dingdan.listzg");
    }

	 public function setqq() {

        if (!I('uid') || !I('oid')) {
            exit;
        }
        $uid = abs(intval(I('uid')));
        $oid = abs(intval(I('oid')));
		
        if (!$oid || !$uid) {
            echo "0";
            exit;
        }
        $info = D("yonghu_yys_record")->field('uid,status,cardId,shopid')->where(array("id"=>$oid,"uid"=>$uid))->find();
			//$this->db->YOne("SELECT uid,status,cardId,shopid FROM `@#_yonghu_yys_record` WHERE `id` = '$oid' and `uid` = '$uid' limit 1");		
        if (!$info)
            _note("参数错误");
		
        $status = @explode(",", $info['status']);
         if (is_array($status) && ($status[1] == '已发货' && $info['cardId'] != '')) {
            $status = '已付款,已发货,已完成';
			
            $q =D("yonghu_yys_record")->where(array("id"=>$oid))->save(array("status"=>$status));
        echo $q ? '1' : '0';
        } else {
            echo "0";
        }
    }

}
