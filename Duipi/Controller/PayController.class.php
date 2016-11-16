<?php

/**
 * 支付
 * addtime 2016/02/22
 */

namespace Duipi\Controller;

use Think\Controller;

class PayController extends BaseController {

    private $MoenyCount;  //商品总金额 
    private $pay_type;  //支付类型
    private $fukuan_type; //付款类型 买商品 充值
    private $dingdan_query = true; //订单的	mysql_qurey 结果
    public $pay_type_bank = false;
    public $scookie = null;
    public $fufen = 0;
    public $fufen_to_money = 0;
    public $model;
    private $shoplist;
    public $chooselist = array(); //用户选购的幸运号码

    public function _initialize() {
        $this->userinfo = $this->getUserInfo();
        if ($this->userinfo) {
            $this->assign("huiyuan", $this->userinfo);
        } else if (ACTION_NAME != "goods_one_ok" && ACTION_NAME != "server_callback" && "pay_user_go_shop" != ACTION_NAME && "xcaction" != ACTION_NAME) {
            $this->autoNote("请先登录", C("URL_DOMAIN") . "user/login");
        }
    }

    //支付界面
    public function pay() {
        $fufen = C('fufen_yuan');
        $huiyuan = $this->userinfo;
        $Mcartlist = $this->getShopCart();
        $shopids = '';
        if (is_array($Mcartlist)) {
            foreach ($Mcartlist as $key => $val) {
                //限制负数
                if ($val["num"] <= 0 && isset($val["num"])) {
                    cookie('Cartlist', NULL);
                    $shopnum = 1;
                }
                //限制负数
                $shopids.=intval($key) . ',';
            }
            $shopids = str_replace(',0', '', $shopids);
            $shopids = trim($shopids, ',');
        }
        $yyslist = array();
        if ($shopids != NULL) {
            $db_goods = D("shangpin");
            $goods = $db_goods->where("id in (" . $shopids . ")")->select();
            $yyslist = array();
            foreach ($goods as $key => $value) {//键为ID
                $yyslist[$value['id']] = $value;
            }
        }
        $MoenyCount = 0;
        if (count($yyslist) >= 1) {
            foreach ($Mcartlist as $key => $val) {
                $key = intval($key);
                if (isset($yyslist[$key])) {
                    $yyslist[$key]['cart_gorenci'] = $val['num'] ? $val['num'] : 1;
                    $MoenyCount+=$yyslist[$key]['yunjiage'] * $yyslist[$key]['cart_gorenci'];
                    $yyslist[$key]['cart_xiaoji'] = substr(sprintf("%.3f", $yyslist[$key]['yunjiage'] * $val['num']), 0, -1);
                    $yyslist[$key]['cart_shenyu'] = $yyslist[$key]['zongrenshu'] - $yyslist[$key]['canyurenshu'];
                }
            }
            $shopnum = 0;  //表示有商品
        } else {
            cookie('Cartlist', NULL);
            $shopnum = 1; //表示没有商品
        }
        //总支付价格
        $MoenyCount = substr(sprintf("%.3f", $MoenyCount), 0, -1);
        //会员余额
        $Money = $huiyuan['money'];
        //商品数量
        $shoplen = count($yyslist);
        if (C("fufen_yuan")) {
            $fufen_dikou = intval($huiyuan['score'] / C("fufen_yuan"));
        } else {
            $fufen_dikou = 0;
        }
        $db_payment = D("payment");

        $paylist_yun = $db_payment->where(array("pay_start" => 1, "pay_class" => "yunpay"))->field("pay_class,pay_name")->select();

        if (ismobile()) {
            $paylist = $db_payment->where(array("pay_start" => 1, "mobile" => 1))->field("pay_class,pay_name")->select();
            $submitcode = uniqid();
        } else {
            $paylist = $db_payment->where(array("pay_start" => 1, "mobile" => 0))->select();
            $submitcode = md5(time());
        }
        session('submitcode', $submitcode);
        $this->assign("paylist", $paylist);
        $this->assign("submitcode", $submitcode);
        $this->assign("paylist_yun", $paylist_yun);
        $this->assign("shopnum", $shopnum);
        $this->assign("MoenyCount", $MoenyCount);
        $this->assign("fufen_dikou", $fufen_dikou);
        $this->assign("huiyuan", $huiyuan);
        $this->assign("yyslist", $yyslist);
        $this->assign("Money", $Money);
        $this->assign("fufen_yuan", $fufen);
        $this->autoShow("payment");
    }

    //开始支付
    public function paysubmit() {
        //选购商品
        $chooselist = array();
        $chooseid = I("chooseid");
        $code = I("code");
        if ($chooseid) {
            foreach ($chooseid as $key => $value) {
                $chooselist[$value] = $code[$key];
            }
        }
        $huiyuan = $this->userinfo;
        ini_set("display_errors", "On");
        error_reporting(E_ALL | E_STRICT);
        header("Cache-control: private");
        $uid = $huiyuan['uid'];
        if (ismobile()) {
            $checkpay = I("type", ""); //获取支付方式  fufen  money  bank
            $banktype = I("bank", ""); //获取选择的银行 CMBCHINA  ICBC CCB
            $money = I("sum", -1);   //获取需支付金额
            $fufen = I("point", -1);   //获取福分
            if (!$checkpay || !$banktype || $money == -1 || $fufen == -1) {
                $this->notemobile("参数错误");
            }
            $pay_class = "";
            $db_payment = D("payment");
            if ($banktype == "chinabankwap") {
                $pay_class = "chinabankwap";
            } else if ($banktype == "ICBC-WAP") {
                $pay_class = "wxpay";
            } else if ($banktype == "alipay1") {
                $pay_class = "alipay1";
            } else if ($banktype == "tenpay1") {
                $pay_class = "tenpay1";
            } else if ($banktype == "baowap") {
                $pay_class = "baowap";
            } else if ($banktype == "yunpay") {
                $pay_class = "yunpay";
            } else {
                $pay_class = "malipay";
            }
            if ($pay_class == "") {
                $this->notemobile("支付方式出错");
            }
            $zhifutype = $db_payment->where(array("pay_class" => $pay_class, "pay_start" => 1))->find();
            $pay_checkbox = false;
            $pay_type_bank = false;
            $pay_type_id = false;

            if ($checkpay == 'money') {
                $pay_checkbox = true;
            }
            if ($banktype != 'nobank') {
                $pay_type_id = $banktype;
            }
            if (!empty($zhifutype)) {
                $pay_type_bank = $zhifutype['pay_class'];
            }
            if (!$pay_type_id) {
                if ($checkpay != 'fufen' && $checkpay != 'money') {
                    $this->notemobile("选择支付方式");
                }
            }
//重复补丁开始
            if ($checkpay == 'money' && $money <= 0) {
                $this->notemobile("请正常提交数据");
            }
            if ($checkpay == 'fufen' && $fufen <= 0) {
                $this->notemobile("请正常提交数据");
            }
            $cf = I("cf", "");
            session_start();
            //         dump($cf == $_SESSION['cf']);exit;
            if ($cf != '' && $cf == $_SESSION['cf']) {
                $_SESSION['cf'] = null;
            } else {
                $this->notemobile("请勿重复提交");
                exit;
                //完全不能用  并没有
            }
//重复补丁结束
        } else {
            if (!isset($_POST["submit"])) {
                $this->note("正在返回购物车...", C("URL_DOMAIN") . 'goods/cartlist');
                exit;
            }
            if (I("submitcode", false)) {
                if (session("submitcode")) {
                    $submitcode = session("submitcode");
                } else {
                    $submitcode = null;
                }
                if (I("submitcode", 0) == $submitcode) {
                    session("submitcode", null);
                } else {
                    // $this->note("请不要重复提交...", C("URL_DOMAIN") . 'goods/cartlist');
                }
            } else {
                $this->note("正在返回购物车...", C("URL_DOMAIN") . 'goods/cartlist');
            }
            $pay_checkbox = I("moneycheckbox", false) ? true : false;
            $pay_type_bank = I("pay_bank", false);
            $pay_type_id = I("account", false);
            if (I("shop_score", false)) {
                $fufen = intval(I("shop_score_num"));
                if (C('fufen_yuan')) {
                    $fufen = intval($fufen / C('fufen_yuan'));
                    $fufen = $fufen * C('fufen_yuan');
                }
            } else {
                $fufen = 0;
            }
        }
        /*         * ***********
          start
         * *********** */
        $this->fufen = $fufen;
        $this->pay_type_bank = $pay_type_bank;
        $ok = $this->createOrder($uid, $pay_type_id, 'go_record', '', $chooselist); //抢购商品
        if ($ok != 'ok') {
            cookie('Cartlist', NULL);
            $this->autoNote("购物车没有商品请返回购物车查看", C("URL_DOMAIN") . "goods/cartlist");
            exit();
        }
        //修复福分和余额判断
        $check = $this->start_pay($pay_checkbox, $checkpay);
        if ($check === 'not_pay') {
            $this->autoNote('未选择支付平台!', C("URL_DOMAIN") . "goods/cartlist");
        }
        if (!$check) {
            $this->autoNote("订单添加失败,请返回购物车查看", C("URL_DOMAIN") . "goods/cartlist");
        }
        cookie('Cartlist', NULL);
        $this->paysuccess();
        exit;
    }

    //成功页面
    public function paysuccess() {
        if (ismobile()) {
            $this->display("public/paysuccess");
        } else {
            $this->display("public/paysuccessforpc");
        }
    }

    //初始化类数据
    //$addmoney 充值金额
    public function createOrder($uid = null, $pay_type = null, $fukuan_type = '', $addmoney = '', $chooselist = array()) {
        //幸运号码
        $this->chooselist = $chooselist;

        $this->model = new \Think\Model;
        $this->model->startTrans();
        $this->members = $this->model->table("yys_yonghu")->lock(true)->where(array("uid" => $uid))->find();
        if ($this->pay_type_bank) {
            $pay_class = $this->pay_type_bank;
            $this->pay_type = $this->model->table("yys_payment")->where(array("pay_class" => $pay_class, "pay_start" => 1))->find();
            $this->pay_type['pay_bank'] = $pay_type;
        }
        if (is_numeric($pay_type)) {
            $this->pay_type = $this->model->table("yys_payment")->where(array("pay_id" => $pay_type, "pay_start" => 1))->find();
            $this->pay_type['pay_bank'] = 'DEFAULT';
        }
        $this->fukuan_type = $fukuan_type;
        if ($fukuan_type == 'go_record') {
            return $this->go_record();
        }
        if ($fukuan_type == 'addmoney_record') {
            return $this->addmoney_record($addmoney);
        }
        return false;
    }

    //买商品
    private function go_record() {
        if (is_array($this->scookie)) {
            $gouwuchelist = $this->scookie;
        } else {
            $gouwuchelist = $this->getShopCart();
        }

        $shopids = '';   //商品ID
        if (is_array($gouwuchelist)) {
            foreach ($gouwuchelist as $key => $val) {
                $shopids.=intval($key) . ',';
            }
            $shopids = str_replace(',0', '', $shopids);
            $shopids = trim($shopids, ',');
        }
        $yyslist = array();  //商品信息	
        if ($shopids != NULL) {
            $yyslist_copy = $this->model->table("yys_shangpin")->lock(true)->where("id in (" . $shopids . ") and q_uid is null")->select();
            foreach ($yyslist_copy as $key => $value) {
                $yyslist[$value["id"]] = $value;
            }
        } else {
            $this->model->rollback();
            return '购物车内没有商品!';
        }
        $MoenyCount = 0;
        $shopguoqi = 0;
        if (count($yyslist) >= 1) {
            $scookies_arr = array();
            $scookies_arr['MoenyCount'] = 0;
            foreach ($gouwuchelist as $key => $val) {
                $key = intval($key);
                if (isset($yyslist[$key]) && $yyslist[$key]['shenyurenshu'] != 0) {
                    if (($yyslist[$key]['xsjx_time'] != '0') && $yyslist[$key]['xsjx_time'] < time()) {
                        unset($yyslist[$key]);
                        $shopguoqi = 1;
                        continue;
                    }
                    $yyslist[$key]['cart_gorenci'] = $val['num'] ? $val['num'] : 1;
                    if ($yyslist[$key]['cart_gorenci'] >= $yyslist[$key]['shenyurenshu']) {
                        $yyslist[$key]['cart_gorenci'] = $yyslist[$key]['shenyurenshu'];
                    }
                    $MoenyCount+=$yyslist[$key]['yunjiage'] * $yyslist[$key]['cart_gorenci'];
                    $yyslist[$key]['cart_xiaoji'] = substr(sprintf("%.3f", $yyslist[$key]['yunjiage'] * $yyslist[$key]['cart_gorenci']), 0, -1);
                    $yyslist[$key]['cart_shenyu'] = $yyslist[$key]['zongrenshu'] - $yyslist[$key]['canyurenshu'];
                    $scookies_arr[$key]['shenyu'] = $yyslist[$key]['cart_shenyu'];
                    $scookies_arr[$key]['num'] = $yyslist[$key]['cart_gorenci'];
                    $scookies_arr[$key]['money'] = intval($yyslist[$key]['yunjiage']);
                    $scookies_arr['MoenyCount'] += intval($yyslist[$key]['cart_xiaoji']);
                } else {
                    unset($yyslist[$key]);
                }
            }
            if (count($yyslist) < 1) {
                $scookies_arr = '0';
                $this->model->rollback();
                if ($shopguoqi) {
                    return '限时揭晓过期商品不能购买!';
                } else {
                    return '购物车里没有商品!';
                }
            }
        } else {
            $scookies_arr = '0';
            $this->model->rollback();
            return '购物车里商品已经卖完或已下架!';
        }
        $this->MoenyCount = substr(sprintf("%.3f", $MoenyCount), 0, -1);
        /**
         * 	最多能抵扣多少钱
         * */
        if ($this->fufen) {
            if ($this->fufen >= $this->members['score']) {
                $this->fufen = $this->members['score'];
            }
            if (C("fufen_yuan")) {
                $this->fufen_to_money = intval($this->fufen / C("fufen_yuan"));
                if ($this->fufen_to_money >= $this->MoenyCount) {
                    $this->fufen_to_money = $this->MoenyCount;
                    $this->fufen = $this->fufen_to_money * C("fufen_yuan");
                }
            } else {
                $this->fufen_to_money = 0;
                $this->fufen = 0;
            }
        } else {
            $this->fufen_to_money = 0;
            $this->fufen = 0;
        }
        //总支付价格		
        $this->MoenyCount = $this->MoenyCount - $this->fufen_to_money;
        $this->shoplist = $yyslist;
        $this->scookies_arr = $scookies_arr;
        return 'ok';
    }

    /**
     * 	开始支付
     * */
    public function start_pay($pay_checkbox, $checkpay) {
        if ($this->members['money'] >= $this->MoenyCount) {
            $uid = $this->members['uid'];
            $uid2 = $this->members['yaoqing2'];
            $uid3 = $this->members['yaoqing3'];
            //修复福分和余额判断
            $pay_1 = $this->pay_bag($checkpay);

            if (!$pay_1) {
                return $pay_1;
            }
            $dingdancode = $this->dingdancode;
            $pay_2 = $this->pay_go_jijin($this->goods_count_num);
            $pay_3 = $this->pay_go_yongjin($uid, $dingdancode, $uid2, $uid3);
            return $pay_1;
        }

        if (!is_array($this->pay_type)) {
            return 'not_pay';
        }
        if (is_array($this->scookies_arr)) {
            $scookie = serialize($this->scookies_arr);
        } else {
            $scookie = '0';
        }

        if ($pay_checkbox) {
            $money = $this->MoenyCount - $this->members['money'];
            return $this->addmoney_record($money, $scookie);
        } else {
            //全额支付
            $this->MoenyCount;
            return $this->addmoney_record($this->MoenyCount, $scookie);
        }
        exit;
    }

    /* 充值 data 其他数据 */

    public function addmoney_record($money = null, $data = null) {
        $uid = $this->members['uid'];
        $dingdancode = $this->pay_huode_dingdan('C');  //订单号	

        if (!is_array($this->pay_type)) {
            return 'not_pay';
        }
        $pay_type = $this->pay_type['pay_name'];

        $time = time();
        if (!empty($data)) {
            $scookies = $data;
        } else {
            $scookies = '0';
        }
        $score = $this->fufen;
        $record_data = array("uid" => "$uid", "code" => "$dingdancode", "money" => "$money", "pay_type" => "$pay_type", "status" => "未付款", "time" => "$time", "score" => "$score", "scookies" => "$scookies");
        $query = $this->model->table("yys_yonghu_addmoney_record")->add($record_data);

        if ($query) {
            $this->model->commit();
        } else {
            $this->model->rollback();
            return false;
        }
        $pay_type = $this->pay_type;

        $type = "\Claduipi\Payment\\" . $pay_type['pay_class'];
        $paydb = new $type;
//        $paydb = new \Claduipi\Payment\wxpay;
        if (!is_object($paydb)) {
            exit();
        }

        $pay_type['pay_key'] = unserialize($pay_type['pay_key']);
        $config = array();
        $config['id'] = $pay_type['pay_key']['id']['val'];   //支付合作ID
        $config['key'] = $pay_type['pay_key']['key']['val'];  //支付KEY
        $config['shouname'] = C('web_name');      //收款方
        $config['title'] = C('web_name');      //付款项目
        $config['money'] = $money;         //付款金额$money
        $config['type'] = $pay_type['pay_type'];     //支付方式：	即时到帐1   中介担保2		
        $config['ReturnUrl'] = C("URL_DOMAIN") . 'pay/qiantai/'; //前台回调	
        $config['NotifyUrl'] = C("URL_DOMAIN") . 'pay/server_callback/payment/' . $pay_type['pay_class'];  //后台回调
        $config['pay_bank'] = $this->pay_type['pay_bank'];
        $config['code'] = $dingdancode;
        $config['pay_type_data'] = $pay_type['pay_key'];
        $paydb->config($config);
        $paydb->send_pay();
        return true;
    }

    /*
      欢乐购基金
      go_number @欢乐购人次
     */

    function pay_go_jijin($go_number = null) {
        if (!$go_number)
            return true;

        $db_jijin = D("jijin");
        $fund = $db_jijin->find();
        if ($fund && $fund['fund_off']) {
            $money = $fund['fund_money'] * $go_number + $fund['fund_count_money'];
            $rs = $db_jijin->where(array("id" => $fund['id']))->save(array("fund_count_money" => "$money"));
            return $rs;
        } else {
            return true;
        }
    }

    /*
      用户佣金
      uid 		用户id
      dingdancode	@订单号
     */

    function pay_go_yongjin($uid = null, $dingdancode = null, $uid2 = null, $uid3 = null) {
        if (!$uid || !$dingdancode) {
            return true;
        }
        $time = time();
        $db_user = D("yonghu");
        $db_record = D("yonghu_yys_record");
        $db_recodes = D("yonghu_recodes");
        $yesyaoqing = $db_user->where(array("uid" => "$uid"))->find();
        if ($yesyaoqing['isfee'] == 0) {
            $db_user->where(array("uid" => "$uid"))->save(array("isfee" => "1"));
        }
        if ($yesyaoqing['yaoqing']) {
            $yongjin = C("fufen_yongjin"); //每一元返回的佣金
            $yongjin2 = C("fufen_yongjin2"); //每一元返回的佣金
            $yongjin3 = C("fufen_yongjin3"); //每一元返回的佣金
        } else {
            return true;
        }
        $yongjin = floatval(substr(sprintf("%.3f", $yongjin), 0, -1));
        $yongjin2 = floatval(substr(sprintf("%.3f", $yongjin2), 0, -1));
        $yongjin3 = floatval(substr(sprintf("%.3f", $yongjin3), 0, -1));
        $gorecode = $db_record->where(array("code" => "$dingdancode"))->select();
        foreach ($gorecode as $val) {
            $y_money = $val['moneycount'] * $yongjin;
            $y_money2 = $val['moneycount'] * $yongjin2;
            $y_money3 = $val['moneycount'] * $yongjin3;
            $content = "(第" . $val['shopqishu'] . "期)" . $val['shopname'];
            $uid1 = $yesyaoqing['yaoqing'];
            $recodes_data = array("uid" => "$uid1", "type" => "1", "content" => "$content", "shopid" => "{$val['shopid']}", "money" => "$y_money", "ygmoney" => "{$val['moneycount']}", "time" => $time);
            if ($yesyaoqing['yaoqing']) {
                //1级邀请
                $db_recodes->add($recodes_data);
                $db_user->where(array("uid" => $yesyaoqing['yaoqing']))->setInc('yongjin', $y_money);
            }
            if ($yesyaoqing['yaoqing2'] && $yesyaoqing['yaoqing']) {
                $recodes_data['uid'] = "$uid2";
                $recodes_data['money'] = "$y_money2";
                $db_recodes->add($recodes_data);
                $db_user->where(array("uid" => $yesyaoqing['yaoqing2']))->setInc('yongjin', $y_money2);
            }
            if ($yesyaoqing['yaoqing3'] && $yesyaoqing['yaoqing2'] && $yesyaoqing['yaoqing']) {
                $recodes_data['uid'] = "$uid3";
                $recodes_data['money'] = "$y_money3";
                $db_recodes->add($recodes_data);
                $db_user->where(array("uid" => $yesyaoqing['yaoqing3']))->setInc('yongjin', $y_money3);
            }
        }
    }

    //账户里支付
    private function pay_bag($checkpay) {
        $time = time();
        $uid = $this->members['uid'];
        $query_1 = $this->set_dingdan('账户', 'A');
        /* 会员购买过账户剩余金额 */
        $Money = $this->members['money'] - $this->MoenyCount + $this->fufen_to_money;
        $Moneys = $this->members['money'] - $this->MoenyCount;
        $query_fufen = true;
        $pay_zhifu_name = '账户';
        //****整合更新****//
        $myfufen = $this->members['score'] - $this->fufen;
        $MoneyCopy = $Money;
        if ($checkpay == 'money') {
            $myfufen = $this->members['score'];
            $MoneyCopy = $Moneys;
        }
        if ($this->fufen_to_money) {
            $query_fufen = $this->model->table("yys_yonghu")->where(array("uid" => "$uid"))->save(array("score" => $myfufen));
            $pay_zhifu_name = '福分';
            $this->MoenyCount = $this->fufen;
        }
        //更新用户账户金额

        $query_2 = $this->model->table("yys_yonghu")->where(array("uid" => "$uid"))->save(array("money" => $MoneyCopy));   //金额
        if ($query_fufen) {
            $query_2 = true;
        }
        $query_3 = $info = $this->model->table("yys_yonghu")->where(array("uid" => "$uid"))->find();
        $query_4 = $this->model->table("yys_yonghu_zhanghao")->data(array("uid" => "$uid", "type" => "-1", "pay" => "$pay_zhifu_name", "content" => '云购了商品', "money" => "{$this->MoenyCount}", "time" => $time))->add();
        $query_5 = true;
        $query_insert = true;
        $shangpinss_count_num = 0;

        foreach ($this->shoplist as $shop):
            if ($shop['canyurenshu'] >= $shop['zongrenshu'] && $shop['maxqishu'] >= $shop['qishu']) {
                $this->model->table("yys_shangpin")->where(array("id" => "$shop[id]"))->save(array("canyurenshu" => $shop['zongrenshu'], "shenyurenshu" => "0"));
            } else {
                $sellnum = $this->model->table("yys_yonghu_yys_record")->where(array("shopid" => "$shop[id]"))->field(array('sum(gonumber)' => 'sellnum'))->find();
                $sellnum = $sellnum['sellnum'];
                $shenyurenshu = $shop['zongrenshu'] - $sellnum;
                $query = $this->model->table("yys_shangpin")->where(array("id" => "$shop[id]"))->save(array("canyurenshu" => "$sellnum", "shenyurenshu" => $shenyurenshu));
                if (!$query) {
                    $query_5 = false;
                }
            }
            $shangpinss_count_num += $shop['goods_count_num'];
            $weixin = new \Claduipi\Wechat\weixin1;

            $iipp = $_SERVER["REMOTE_ADDR"];
            $dengluid2 = C('goumaiid');
            $titit = str_replace('&nbsp;', ' ', $shop['title']);
            $dingdancode = $this->dingdancode;
            $openids = D("yonghu_band")->field("b_data")->where(array("b_uid" => $uid))->find();

            $template = array('touser' => $openids[b_data],
                'template_id' => $dengluid2,
                'url' => C("URL_DOMAIN") . "/user/buyDetail/goodsid/" . $shop[id],
                'topcolor' => "#7B68EE",
                'data' => array('first' => array('value' => "恭喜您成功下单",
                        'color' => "#743A3A",
                    ),
                    'keyword1' => array('value' => $titit,
                        'color' => "#FF0000",
                    ),
                    'keyword2' => array('value' => "" . $dingdancode,
                        'color' => "#0000FF",
                    ),
                    'keyword3' => array('value' => $shop['goods_count_num'] . '元',
                        'color' => "#0000FF",
                    ),
                    'keyword4' => array('value' => date('Y-m-d h:i:s', time()),
                        'color' => "#0000FF",
                    ),
                    'remark' => array('value' => "\\n查看幸运码！",
                        'color' => "#008000",
                    ),
                )
            );

            $weixin->send_template_message($template);

            //微信登陆通知end
        endforeach;
        //添加用户经验
        $jingyan = $this->members['jingyan'] + (C("z_shoppay") * $shangpinss_count_num);
        $query_jingyan = $this->model->table("yys_yonghu")->where(array("uid" => "$uid"))->save(array("jingyan" => "$jingyan")); //经验值
        //添加福分
        if (!$this->fufen_to_money) {
            $mygoscore = C("f_shoppay") * $shangpinss_count_num;
            $mygoscore_text = "云购了{$shangpinss_count_num}人次商品";
            $myscore = $this->members['score'] + $mygoscore;
            $this->model->table("yys_yonghu")->where(array("uid" => "$uid"))->save(array("score" => "$myscore"));
            $this->model->table("yys_yonghu_zhanghao")->add(array("uid" => "$uid", "type" => "1", "pay" => '福分', "content" => "$mygoscore_text", "money" => "$mygoscore", "time" => "$time"));
        }
        $dingdancode = $this->dingdancode;
        //fix by dabin
        $mmmmm = $this->model->table("yys_yonghu_yys_record")->where(array("code" => "$dingdancode", "uid" => "$uid"))->find();
        if ($mmmmm['leixing'] == 2) {
            $query_6 = $this->model->table("yys_yonghu_yys_record")->where(array("code" => "$dingdancode", "uid" => "$uid"))->save(array("status" => "已付款,已发货,未完成"));
        } else {
            $query_6 = $this->model->table("yys_yonghu_yys_record")->where(array("code" => "$dingdancode", "uid" => "$uid"))->save(array("status" => "已付款,未发货,未完成,未提交地址"));
        }
        $query_7 = $this->dingdan_query;
        $query_8 = $this->model->table("yys_linshi")->where(array("key" => "goods_count_num", "uid" => "$uid"))->setInc("value", $shangpinss_count_num);
        $this->goods_count_num = $shangpinss_count_num;
//        dump($query_jingyan);
//        dump($query_1);
//        dump($query_2);
//        dump($query_3);
//        dump($query_4);
//        dump($query_5);
//        dump($query_6);
//        dump($query_7);
//        dump($query_8);
//        dump($query_insert);
//        exit;
        if (!$query_1 || !$query_2 || !$query_3 || !$query_4 || !$query_5 || !$query_6 || !$query_7 || !$query_insert || !$query_8) {
            $this->model->rollback();
            return false;
        }
        if ((empty($checkpay) && $info['money'] == $Money) || ($checkpay == 'money' && $info['money'] == $Moneys) || ($checkpay == 'fufen' && $info['money'] == $Money)) {
            $this->model->commit();
            foreach ($this->shoplist as $shop):
                if ($shop['canyurenshu'] >= $shop['zongrenshu'] && $shop['maxqishu'] >= $shop['qishu']) {
                    $this->model->startTrans();
                    $query_insert = $this->pay_insert_shop($shop, 'add');
                    if (!$query_insert) {
                        $this->model->rollback();
                    } else {
                        $this->model->commit();
                    }
                    $this->model->table("yys_shangpin")->where(array("id" => "{$shop['id']}"))->save(array("canyurenshu" => $shop['zongrenshu'], "shenyurenshu" => '0'));
                }
            endforeach;
            return true;
        } else {
            $this->model->rollback();
            return false;
        }
    }

    /*
      揭晓与插入商品
      @shop   商品数据
     */

    function pay_insert_shop($shop = '', $type = '') {
        $time = sprintf("%.3f", microtime(true) + (int) C("goods_end_time"));
        $model = new \Think\Model;
        if ($shop['xsjx_time'] != 0) {//////////////////原本是'0'
            return $model->table("yys_shangpin")->where(array("id" => "{$shop['id']}"))->save(array("canyurenshu" => $shop['zongrenshu'], "shenyurenshu" => '0'));
        }
        $tocode = new \Claduipi\Pay\tocode;
        $tocode->shop = $shop;
        $tocode->yunxing_shop($time, 100, $shop['canyurenshu'], $shop);
        $code = $tocode->go_code;
        $content = ($tocode->go_content); //addslashes($tocode->go_content)老系统加了 但是好像没起作用  给“前面加反斜杠
        $counttime = $tocode->count_time;
       

        $u_go_info = $model->table("yys_yonghu_yys_record")->where("shopid = '{$shop["id"]}' and shopqishu = '{$shop["qishu"]}' and goucode LIKE  '%$code%'")->find();
        $u_info = $model->table("yys_yonghu")->field("uid,username,email,mobile,img")->where(array("uid" => "{$u_go_info['uid']}"))->find();
        //更新商品
        $query = true;
        if ($u_info) {
            $u_info['username'] = $this->htmtguolv($u_info['username']);
            $q_yonghu = serialize($u_info);
            $gtimes = (int) C("goods_end_time");
            if ($gtimes == 0 || $gtimes == 1) {
                $q_showtime = 'N';
            } else {
                $q_showtime = 'Y';
            }
            $goods_data = array(
                "canyurenshu" => $shop['zongrenshu'],
                "shenyurenshu" => '0',
                "q_uid" => "{$u_info['uid']}",
                "q_user" => "$q_yonghu",
                "q_user_code" => "$code",
                "q_content" => "$content",
                "q_counttime" => "$counttime",
                "q_end_time" => "$time",
                "q_showtime" => "$q_showtime"
            );
            $q = $model->table("yys_shangpin")->where(array("id" => "{$shop['id']}"))->save($goods_data);
            if (!$q) {
                $query = false;
            }

            if ($q) {
                $q = $model->table("yys_yonghu_yys_record")->where(array("id" => "{$u_go_info['id']}", "code" => "{$u_go_info['code']}", "uid" => "{$u_go_info['uid']}", "shopid" => "{$shop['id']}", "shopqishu" => "{$shop['qishu']}"))->save(array("huode" => "$code"));
                if (!$q) {
                    $query = false;
                } else {
                    $post_arr = array("uid" => $u_info['uid'], "gid" => $shop['id'], "send" => 1);
                    $this->g_YYSabcde(C("URL_DOMAIN") . "send/send_shop_code", false, $post_arr);
                }
            } else {
                $query = false;
            }
        } else {
            $query = false;
        }
        /* 新建 */
        if ($query) {
            if ($shop['qishu'] < $shop['maxqishu']) {
                $maxinfo = $model->table("yys_shangpin")->where(array("sid" => "{$shop['sid']}"))->order("qishu DESC")->find();

                if (!$maxinfo) {
                    $maxinfo = array("qishu" => $shop['qishu']);
                }
                $intall = R('Goods/content_add_shop_install', array(0 => $maxinfo, 1 => false));
                if (!$intall)
                    return $query;
            }
        }
        return $query;
    }

    public function test() {
        $post_arr = array("uid" => 1, "gid" => 159, "send" => 1);
        $res = $this->http_request(C("URL_DOMAIN") . "send/send_shop_code", $post_arr);
        dump($res);
    }

    //生成订单号
    function pay_huode_dingdan($dingdanzhui = '') {
        return $dingdanzhui . time() . substr(microtime(), 2, 6) . rand(0, 9);
    }

    //生成订单
    private function set_dingdan($pay_type = '', $dingdanzhui = '') {
        $uid = $this->members['uid'];
        $uphoto = $this->members['img'];
        $weername = $this->huode_user_name($this->members);
        $this->dingdancode = $dingdancode = $this->pay_huode_dingdan($dingdanzhui);  //订单号			
        if (count($this->shoplist) > 1) {
            $dingdancode_tmp = 1; //多个商品相同订单
        } else {
            $dingdancode_tmp = 0; //单独商品订单
        }
        $ip = $this->huode_ip_dizhi();
        $arr = array();
        //订单时间
        $time = sprintf("%.3f", microtime(true));
        $this->MoenyCount = 0;
        $chooselist = $this->chooselist;
        foreach ($this->shoplist as $key => $shop) {
            $ret_data = array();
            //增加选购号码
            if ($shop['is_choose'] && $chooselist[$shop['id']]) {
                $this->getChooseCodes($chooselist[$shop['id']], $shop, $ret_data);
            } else {
                $this->pay_huode_codes($shop['cart_gorenci'], $shop, $ret_data);
            }
            $this->dingdan_query = $ret_data['query'];
            if (!$ret_data['query']) {
                $this->dingdan_query = false;
            }
            $codes = $ret_data['user_code'];         //得到的夺宝码					
            $codes_len = intval($ret_data['user_code_len']);      //得到夺宝码个数					
            $money = $codes_len * $shop['yunjiage'];        //单条商品的总价格
            $this->MoenyCount += $money;          //总价格
            $status = '未付款,未发货,未完成';
            $shop['canyurenshu'] = intval($shop['canyurenshu']) + $codes_len;
            $shop['goods_count_num'] = $codes_len;
            $shop['title'] = addslashes($shop['title']);
            if ($shop['leixing'] == 0) {
                $jia = $shop['yuanjia'] * C("fufen_yongjinqd0");
            } else if ($shop['leixing'] == 1) {
                $jia = $shop['yuanjia'] * C("fufen_yongjinqd1");
            } else {
                $jia = $shop['yuanjia'] * C("fufen_yongjinqd2");
            }
            $this->shoplist[$key] = $shop;
            $ymoney = $this->MoenyCount * C("fufen_yongjin"); //每一元返回的佣金
            $ymoney2 = $this->MoenyCount * C("fufen_yongjin2"); //每一元返回的佣金
            $ymoney3 = $this->MoenyCount * C("fufen_yongjin3"); //每一元返回的佣金

            if ($codes_len) {
                $array = array(
                    'code' => "$dingdancode",
                    'code_tmp' => "$dingdancode_tmp",
                    'uid' => "$uid",
                    'username' => "$weername",
                    'uphoto' => "$uphoto",
                    'shopid' => "$shop[id]",
                    'shopname' => "$shop[title]",
                    'shopqishu' => "$shop[qishu]",
                    'gonumber' => "$codes_len",
                    'moneycount' => "$money",
                    'goucode' => "$codes",
                    'pay_type' => "$pay_type",
                    'ip' => "$ip",
                    'status' => "$status",
                    'time' => "$time",
                    'cardId' => "$shop[cardId]",
                    'cardPwd' => "$shop[cardPwd]",
                    'leixing' => "$shop[leixing]",
                    'yuanjia' => "$shop[yuanjia]",
                    'money' => "$shop[money]",
                    'zhuanhuan' => "$jia",
                    'ymoney' => "$ymoney",
                    'ymoney2' => "$ymoney2",
                    'ymoney3' => "$ymoney3"
                );
                $arr[] = $array;
            }
        }
        $ret = $this->model->table("yys_yonghu_yys_record")->addall($arr);
        return $ret;
    }

    function pay_huode_codes($weer_num = 1, $shopinfo = null, &$ret_data = null) {
        $ret_data['query'] = true;
        $table = $shopinfo['codes_table'];
        $db_table = $this->model->table("yys_" . $table);
        $codes_arr = array();
        $codes_one = $db_table->lock(true)->where(array("s_id" => $shopinfo['id']))->field("id,s_id,s_cid,s_len,s_codes,s_codes_tmp")->order("s_cid desc")->find();
        $codes_arr[$codes_one['s_cid']] = $codes_one;
        $codes_count_len = $codes_arr[$codes_one['s_cid']]['s_len'];
        if ($codes_count_len < $weer_num && $codes_one['s_cid'] > 1) {
            for ($i = $codes_one['s_cid'] - 1; $i >= 1; $i--):
                $codes_arr[$i] = D($table)->lock(true)->where(array("s_id" => $shopinfo['id'], "s_cid" => $i))->field("id,s_id,s_cid,s_len,s_codes")->find();
                $codes_count_len += $codes_arr[$i]['s_len'];
                if ($codes_count_len > $weer_num)
                    break;
            endfor;
        }
        if ($codes_count_len < $weer_num) {
            $weer_num = $codes_count_len;
        }
        $ret_data['user_code'] = '';
        $ret_data['user_code_len'] = 0;
        foreach ($codes_arr as $icodes) {
            $u_num = $weer_num;
            $icodes['s_codes'] = unserialize($icodes['s_codes']);
            $code_tmp_arr = array_slice($icodes['s_codes'], 0, $u_num);
            $ret_data['user_code'] .= implode(',', $code_tmp_arr);
            $code_tmp_arr_len = count($code_tmp_arr);
            if ($code_tmp_arr_len < $u_num) {
                $ret_data['user_code'] .= ',';
            }

            $icodes['s_codes'] = array_slice($icodes['s_codes'], $u_num, count($icodes['s_codes']));

            $icode_sub = count($icodes['s_codes']);
            $icodes['s_codes'] = serialize($icodes['s_codes']);

            //修复机器人
            $jiqiren = $this->model->table("yys_shangpin")->lock(true)->where(array("id" => $icodes[s_id]))->find();
            $icodessss['s_codes_tmp'] = unserialize($icodes['s_codes_tmp']);
            $ssyy = $jiqiren['shenyurenshu'] - $u_num;
            if ($ssyy < 0) {
                exit;
            }
            $srci = $jiqiren['zongrenshu'] - $jiqiren['canyurenshu'] - $u_num;
            $srci2 = fmod(($jiqiren['zongrenshu'] - $srci), 3000);
            $icodesnew = array_slice($icodessss['s_codes_tmp'], $srci2, count($icodessss['s_codes_tmp']));
            $icodesnewss = serialize($icodesnew);
            $counts1 = $this->model->table("yys_" . $table)->lock(true)->where(array("s_id" => $shopinfo['id']))->field("id,s_id,s_cid,s_len,s_codes")->order("s_cid desc")->select();
            foreach ($counts1 as $value) {
                $kkkkk +=$value[s_len];
            }
            if (!$icode_sub) {
                $data = array('s_cid' => 0, "s_codes" => $icodesnewss, "s_len" => $icode_sub);
                $query = $this->model->table("yys_" . $table)->where(array("id" => $icodes['id']))->save($data);
                if (!$query) {
                    $ret_data['query'] = false;
                }
            } else {
                $data = array("s_codes" => $icodes['s_codes'], "s_len" => $icode_sub);
                $query = $this->model->table("yys_" . $table)->where(array("id" => $icodes['id']))->save($data);
//                $this->model->rollback();
                if (!$query) {
                    $ret_data['query'] = false;
                }
            }
            $ret_data['user_code_len'] += $code_tmp_arr_len;
            $weer_num = $weer_num - $code_tmp_arr_len;
            //修复机器人结束
        }
    }

    public function pay_user_go_shop($uid = null, $yonghuid = null, &$num = null) {
        //$uid=1;$yonghuid=227;$num=2;  //测试数据
        if (empty($uid) || empty($yonghuid) || empty($num)) {
            return false;
        }
        $uid = intval($uid);
        $yonghuid = intval($yonghuid);
        $num = intval($num);
        $db = new \Think\Model;
        $this->model = &$db;
        $db->startTrans();
        $huiyuan = $db->table("yys_yonghu")->where(array("uid" => "$uid"))->lock(true)->find();
        $shangpinsinfo = $db->table("yys_shangpin")->where("id = '$yonghuid' and shenyurenshu != '0'")->lock(true)->find();
        if (!$shangpinsinfo['shenyurenshu']) {
            $db->rollback();
            return false;
        }
        if ($shangpinsinfo['shenyurenshu'] < $num) {
            $num = $shangpinsinfo['shenyurenshu'];
        }
        $if_money = $shangpinsinfo['yunjiage'] * $num;
        $this->members = $huiyuan;
        $this->MoenyCount = $if_money;
        $shangpinsinfo['goods_count_num'] = $num;
        $shangpinsinfo['cart_gorenci'] = $num;
        $this->shoplist = array();
        $this->shoplist[0] = $shangpinsinfo;
        if ($huiyuan && $shangpinsinfo && $huiyuan['money'] >= $if_money) {
            $uid = $this->members['uid'];
            $uid2 = $this->members['yaoqing2'];
            $uid3 = $this->members['yaoqing3'];
            $pay_1 = $this->pay_bag($checkpay);
            if (!$pay_1) {
                return $pay_1;
            }
            $dingdancode = $this->dingdancode;
            $pay_2 = $this->pay_go_jijin($this->goods_count_num);
            $pay_3 = $this->pay_go_yongjin($uid, $dingdancode, $uid2, $uid3);
            return $pay_1;
        } else {
            $db->rollback();
            return false;
        }
    }

    /**
     * 选购号码
     */
    function getChooseCodes($choose, $shopinfo = null, &$ret_data = null) {
        $code = explode(",", $choose);
        $ret_data['query'] = TRUE;
        if (count($code) != $shopinfo['cart_gorenci']) {
            $ret_data['query'] = FALSE;
            return 0;
        }
        $table = $shopinfo['codes_table'];
        $codes_arr = D($table)->lock(true)->where("s_id = '$shopinfo[id]' and s_cid <>0")->field("id,s_id,s_cid,s_len,s_codes")->select();
        $ret_data['user_code'] = $choose;
        $ret_data['user_code_len'] = $shopinfo['cart_gorenci'];
        $removeLenght = 0;
        foreach ($codes_arr as $icodes) {
            $icodes['s_codes'] = unserialize($icodes['s_codes']);
            foreach ($code as $val) {
                $key = array_search($val, $icodes['s_codes']);
                if ($key !== false) {
                    //扣减幸运号码
                    array_splice($icodes['s_codes'], $key, 1);
                    $removeLenght++;
                }
            }
            $icode_sub = count($icodes['s_codes']);
            $icodes['s_codes'] = serialize($icodes['s_codes']);
            //扣除完毕判断
            if (!$icode_sub) {
                $query = D($table)->where(array("id" => $icodes['id']))->save(array("s_cid" => 0, "s_codes" => $icodes['s_codes'], "s_len" => $icode_sub));
            } else {
                $query = D($table)->where(array("id" => $icodes['id']))->save(array("s_codes" => $icodes['s_codes'], "s_len" => $icode_sub));
            }
            if (!$query) {
                $ret_data['query'] = false;
                return 0;
            }
        }
        //判断合法
        if ($removeLenght != $shopinfo['cart_gorenci']) {
            $ret_data['query'] = FALSE;
            return 0;
        }
    }

    //充值
    public function addmoney() {
        if (!ismobile()) {
            $this->Cartlist = cookie('Cartlist');
            $this->Cartlistzg = cookie('Cartlistzg');
            if (!isset($_POST['submit'])) {
                $this->note("正在返回充值页面...", C("URL_DOMAIN") . 'user/userrecharge');
                exit;
            }
            $this->userinfo = $this->getUserInfo();
            if (!$this->userinfo) {
                $this->HeaderLogin();
            }
            $pay_type_bank = isset($_POST['pay_bank']) ? $_POST['pay_bank'] : false;
            $pay_type_id = isset($_POST['account']) ? $_POST['account'] : false;
            $money = intval($_POST['money']);
            $uid = $this->userinfo['uid'];
            $this->pay_type_bank = $pay_type_bank;
            $ok = $this->createOrder($uid, $pay_type_id, 'addmoney_record', $money);
            if ($ok === 'not_pay') {
                $this->note("未选择支付平台");
            }
        }

        $money = I("money", 0); //获取充值金额
        $banktype = I("data", 0);
        $pay_class = "";
        $db_payment = D("payment");
        if ($banktype == "chinabankwap") {
            $pay_class = "chinabankwap";
        } else if ($banktype == "wxpay") {
            $pay_class = "wxpay";
        } else if ($banktype == "alipay1") {
            $pay_class = "alipay1";
        } else if ($banktype == "tenpay1") {
            $pay_class = "tenpay1";
        } else if ($banktype == "baowap") {
            $pay_class = "baowap";
        } else if ($banktype == "yunpay") {
            $pay_class = "yunpay";
        } else {
            $pay_class = "malipay";
        }
        if ($pay_class == "") {
            $this->notemobile("支付方式出错");
        }
        $zhifutype = $db_payment->where(array("pay_class" => $pay_class, "pay_start" => 1))->find();

        if (!empty($zhifutype)) {
            $pay_type_bank = $zhifutype['pay_class'];
        }

        $pay_type_id = $banktype;

        $userinfo = $this->getUserInfo();
        $uid = $userinfo['uid'];

        $this->pay_type_bank = $pay_type_bank;

        $this->createOrder($uid, $pay_type_id, 'addmoney_record', $money);
    }

    public function server_callback() {
        //创建工厂
        $payment_class = I('payment', "wxpay");
        $payment_db = D("payment")->where(array("pay_class" => $payment_class))->find();
        if ($payment_db) {
            $type = "\Claduipi\Payment\\" . $payment_class;
            $payment = new $type;
        }
        if (!is_object($payment)) {
            exit();
        }
        $callbackData = $GLOBALS["HTTP_RAW_POST_DATA"];
        $money = 0;
        $message = "";
        $orderNo = "";
        //加工
        $is_true = $payment->serverCallback($callbackData, &$money, &$message, &$orderNo);
        if ($is_true != 1 || !$orderNo || !$money) {
            \Think\Log::record($message . "\n" . $callbackData);
            exit;
        }
        //返回后处理分充值和购买
//        $dingdancode = substr($orderNo, 0, 1);
//        if ($dingdancode == "C") {
//            $this->callRechargeOrder($orderNo, $money);
//        } else if ($dingdancode == "A") {
//            $this->callOrder($orderNo, $money);
//        }
//        exit();
        $db = new \Think\Model;
        $db->startTrans();
        $dingdaninfo = $db->table("yys_yonghu_addmoney_record")->lock(true)->where(array("code" => $orderNo))->find();

        if (!$dingdaninfo) {
            return false;
        }
        if ($dingdaninfo['status'] == '已付款') {
            echo "success";
            exit;
        }
        $c_money = $dingdaninfo['money'];
        if ($c_money * 100 != $money) {
            //echo "success";
            //exit;
        }

        $uid = $dingdaninfo['uid'];
        $time = time();


        $up_q1 = $db->table("yys_yonghu_addmoney_record")->where(array("id" => $dingdaninfo['id'], "code" => $dingdaninfo['code']))->save(array("pay_type" => $payment_db['pay_name'], "status" => "已付款"));
        $c_money = $c_money * C("fufen_yuansong"); //佣金
        $db->table("yys_yonghu")->where(array("uid" => $uid))->setInc('money', $c_money);
        $up_q3 = $db->table("yys_yonghu_zhanghao")->add(array("uid" => "$uid", "type" => "1", "pay" => "账户", "content" => "充值", "money" => "$c_money", "time" => "$time"));

        if ($up_q1 && $up_q3) {
            $db->commit();
        } else {
            $db->rollback();
            echo "fail";
            exit;
        }

        if (empty($dingdaninfo['scookies'])) {
            echo "success";
            exit; //充值完成			
        }
        $scookies = unserialize($dingdaninfo['scookies']);
        $this->scookie = $scookies;

        $ok = $this->createOrder($uid, $payment_db['pay_id'], 'go_record'); //云购商品	
        if ($ok != 'ok') {
            cookie('Cartlist', NULL);
            echo "fail";
            exit; //商品购买失败			
        }
        $check = $this->start_pay(1);
        if ($check) {
            $db->table("yys_yonghu_addmoney_record")->where(array("status" => "已付款", "code" => $orderNo))->save(array("scookies" => "1", "status" => "已付款"));
            cookie('Cartlist', NULL);
            echo "success";
            exit;
        } else {
            echo "fail";
            exit;
        }
    }

    //获取code
    public function wxGetCode() {
        if (isset($_GET['code']) && isset($_GET['states'])) {
            $paydb = new \Claduipi\Payment\wxpay;
            $paydb->next_pay($_GET['code'], $_GET['states']);
        }
    }

    public function hui() {
        $out_trade_no = I("out_trade_no", 0);
        $dingdaninfo = D("yonghu_addmoney_record")->where(array('code' => $out_trade_no))->find();
        if ($dingdaninfo['status'] == '已付款') {
            $gg[code] = '4';
            //exit;
        } else {
            $gg[code] = '1';
            //exit;
        }
        echo json_encode($gg);
    }

}
