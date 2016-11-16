<?php

/**
 * 支付(部分方法调用 普通商品支付 后续提取方法)
 * addtime 2016/06/15
 */

namespace Duipi\Controller;

use Think\Controller;

class PayzgController extends BaseController {

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

    public function _initialize() {
        $this->userinfo = $this->getUserInfo();
        if ($this->userinfo) {
            $this->assign("huiyuan", $this->userinfo);
        } else if (ACTION_NAME != "server_callback") {
            $this->autoNote("请先登录", C("URL_DOMAIN") . "user/login");
        }
    }

    //支付界面
    public function payzg() {
		$fufen= C('fufen_yuan');
        $huiyuan = $this->userinfo;
        $Mcartlist = $this->getShopCart("Cartlistzg");
        $shopids = '';
        if (is_array($Mcartlist)) {
            foreach ($Mcartlist as $key => $val) {
                //限制负数
                if ($val[num] <= 0 && isset($val["num"])) {
                    cookie('Cartlistzg', NULL);
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
            $db_goods = D("shangpinzg");
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
            cookie('Cartlistzg', NULL);
            $this->autoNote("购物车没有商品!", C("URL_DOMAIN") . 'goodszg/cartlistzg');
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
        //$cookies = base64_encode($this->Cartlist);
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
        $this->autoShow("paymentzg");
    }

    //开始支付
    public function paysubmitzg() {
        $uid = $this->userinfo['uid'];
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
        } else {
            if (!isset($_POST['submit'])) {
                $this->autoNote("正在返回购物车...", C("URL_DOMAIN") . 'goodszg/cartlistzg');
                exit;
            }
            session_start();
            if (isset($_POST['submitcode'])) {
                if (isset($_SESSION['submitcode'])) {
                    $submitcode = $_SESSION['submitcode'];
                } else {
                    $submitcode = null;
                }
                if ($_POST['submitcode'] == $submitcode) {
                    unset($_SESSION["submitcode"]);
                } else {
                    //$this->note("请不要重复提交...", C("URL_DOMAIN") . 'goodszg/cartlistzg');
                }
            } else {
                $this->note("正在返回购物车...", C("URL_DOMAIN") . 'goodszg/cartlistzg');
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
        //$pay->scookie = json_decode(base64_decode($_POST['cookies']));
        $this->fufen = $fufen;
        $this->pay_type_bank = $pay_type_bank;
        $ok = $this->createOrder($uid, $pay_type_id, 'go_recordzg');

        if ($ok !== 'ok') {
            cookie("Cartlistzg", null);
            $this->autoNote($ok, C("URL_DOMAIN") . "index/index");
        }

        $check = $this->start_payzg($pay_checkbox, $checkpay);
        if ($check === 'not_pay') {
            $this->autoNote('未选择支付平台!', C("URL_DOMAIN") . 'goodszg/cartlistzg');
        }
        if (!$check) {
            $this->autoNote("商品支付失败!", C("URL_DOMAIN") . 'goodszg/cartlistzg');
        }
        if ($check) {
            //成功
            header("location: " . C("URL_DOMAIN") . "payzg/paysuccesszg");
        } else {
            //失败	
            cookie("Cartlistzg", null);
            header("location: " . C("URL_DOMAIN") . "index/index");
        }
        cookie('Cartlist', NULL);
        exit;
    }

    //成功页面
    public function paysuccesszg() {
        if (ismobile()) {
            $this->display("public/paysuccesszg");
        } else {
            $this->display("public/paysuccessforpczg");
        }
    }

    //初始化类数据
    //$addmoney 充值金额
    public function createOrder($uid = null, $pay_type = null, $fukuan_type = '', $addmoney = '') {
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

        if ($fukuan_type == 'go_recordzg') {
            return $this->go_recordzg();
        }
        if ($fukuan_type == 'addmoney_record') {
            return $this->addmoney_record($addmoney);
        }
        return false;
    }

    //直购
    private function go_recordzg() {

        if (is_array($this->scookie)) {
            $gouwuchelist = $this->scookie;
        } else {
            $gouwuchelist = $this->getShopCart("Cartlistzg");
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
            $yyslist = $this->model->table("yys_shangpinzg")->lock(true)->where("id in (" . $shopids . ") and q_uid is null")->select(); //
            $yyslist = $this->key2key($yyslist, "id");
            //$this->db->Ylist("SELECT * FROM `@#_shangpinzg` where `id` in($shopids) and `q_uid` is null for update",array("key"=>"id"));
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
    public function start_payzg($pay_checkbox, $checkpay) {
        if ($this->members['money'] >= $this->MoenyCount) {
            $uid = $this->members['uid'];
            $uid2 = $this->members['yaoqing2'];
            $uid3 = $this->members['yaoqing3'];
            //修复福分和余额判断
            $pay_1 = $this->pay_bagzg($checkpay);
            if (!$pay_1) {
                return $pay_1;
            }
            $dingdancode = $this->dingdancode;
            $pay_2 = R("Pay/pay_go_jijin", array($this->goods_count_num));
            $pay_3 = R("Pay/pay_go_yongjin", array($uid, $dingdancode, $uid2, $uid3));
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

    private function pay_bagzg($checkpay) {

        $time = time();
        $uid = $this->members['uid'];

        $query_1 = $this->set_dingdanzg('账户', 'A');
        /* 会员购买过账户剩余金额 */
        $Money = $this->members['money'] - $this->MoenyCount + $this->fufen_to_money;
        $Moneys = $this->members['money'] - $this->MoenyCount;
        $zhang = $this->MoenyCount;
//        var_dump($Moneys);
//        dump($this->members['money'] . "   " . $this->MoenyCount . "   " . $this->fufen_to_money);
//        exit;
        $query_fufen = true;
        $pay_zhifu_name = '账户';
        $uid1 = $this->members['yaoqing'];
        $uid2 = $this->members['yaoqing2'];
        $uid3 = $this->members['yaoqing3'];
        if ($uid3 && $uid2 && $uid1) {
            $mes = $uid3;
        } else if (!$uid3 && $uid2 && $uid1) {
            $mes = $uid2;
        } else if (!$uid3 && !$uid2 && $uid1) {
            $mes = $uid1;
        }
        $dailizh = D("yonghu_yys_recordzg")->field("sum('gonumber') as xiang")->where(array('uid' => $uid))->find();
        $dailizh1 = D("yonghu_yys_recordzg")->field("sum('gonumber') as xiang")->where(array('uid' => $uid1))->find();
        $dailizh2 = D("yonghu_yys_recordzg")->field("sum('gonumber') as xiang")->where(array('uid' => $uid2))->find();
        $dailizh3 = D("yonghu_yys_recordzg")->field("sum('gonumber') as xiang")->where(array('uid' => $uid3))->find();
        //本身升级
        if ($dailizh[xiang] >= C("xiang1") && $dailizh[xiang] < C("xiang2") && $dailizh[xiang] < C("xiang3")) {
            if ($this->members['daili'] != 3) {
                D("yonghu")->where(array('uid' => $uid))->seve(array('daili' => '3'));
                D("yonghu")->where(array('uid' => $mes))->setInc('money', C("xiang3a"));
            }
        } else if ($dailizh[xiang] >= C("xiang1") && $dailizh[xiang] >= C("xiang2") && $dailizh[xiang] < C("xiang3")) {
            if ($this->members['daili'] != 2) {
                D("yonghu")->where(array('uid' => $uid))->seve(array('daili' => '2'));
                D("yonghu")->where(array('uid' => $mes))->setInc('money', C("xiang2a"));
            }
        } else if ($dailizh[xiang] >= C("xiang1") && $dailizh[xiang] >= C("xiang2") && $dailizh[xiang] >= C("xiang3")) {
            $rr = D("yonghu")->where(array('uid' => $mes))->setInc('money', C("xiang1a"));
            if ($this->members['daili'] != 1) {
                D("yonghu")->where(array('uid' => $uid))->seve(array('daili' => '1'));
                D("yonghu")->where(array('uid' => $mes))->setInc('money', C("xiang1a"));
            }
        }

        //1级上级代理升级

        if (($dailizh[xiang] + $dailizh1[xiang]) >= $xiangp1[xiang1] && ($dailizh[xiang] + $dailizh1[xiang]) < C("xiang2") && ($dailizh[xiang] + $dailizh1[xiang]) < C("xiang3")) {
            D("yonghu")->where(array("uid" => $uid1))->save(array("daili" => 3));
        } else if (($dailizh[xiang] + $dailizh1[xiang]) >= C("xiang1") && ($dailizh[xiang] + $dailizh1[xiang]) >= C("xiang2") && ($dailizh[xiang] + $dailizh1[xiang]) < C("xiang3")) {
            D("yonghu")->where(array("uid" => $uid1))->save(array("daili" => 2));
        } else if (($dailizh[xiang] + $dailizh1[xiang]) >= C("xiang1") && ($dailizh[xiang] + $dailizh1[xiang]) >= C("xiang2") && ($dailizh[xiang] + $dailizh1[xiang]) >= C("xiang3")) {
            D("yonghu")->where(array("uid" => $uid1))->save(array("daili" => 1));
        }


        //2级上级代理升级
        if (($dailizh[xiang] + $dailizh2[xiang]) >= $xiangp1[xiang1] && ($dailizh[xiang] + $dailizh2[xiang]) < C("xiang2") && ($dailizh[xiang] + $dailizh2[xiang]) < C("xiang3")) {
            D("yonghu")->where(array("uid" => $uid2))->save(array("daili" => 3));
        } else if (($dailizh[xiang] + $dailizh2[xiang]) >= C("xiang1") && ($dailizh[xiang] + $dailizh2[xiang]) >= C("xiang2") && ($dailizh[xiang] + $dailizh2[xiang]) < C("xiang3")) {
            D("yonghu")->where(array("uid" => $uid2))->save(array("daili" => 2));
        } else if (($dailizh[xiang] + $dailizh2[xiang]) >= C("xiang1") && ($dailizh[xiang] + $dailizh2[xiang]) >= C("xiang2") && ($dailizh[xiang] + $dailizh2[xiang]) >= C("xiang3")) {
            D("yonghu")->where(array("uid" => $uid2))->save(array("daili" => 1));
        }

        //3级上级代理升级
        if (($dailizh[xiang] + $dailizh3[xiang]) >= $xiangp1[xiang1] && ($dailizh[xiang] + $dailizh3[xiang]) < C("xiang2") && ($dailizh[xiang] + $dailizh3[xiang]) < C("xiang3")) {
            D("yonghu")->where(array("uid" => $uid3))->save(array("daili" => 3));
        } else if (($dailizh[xiang] + $dailizh3[xiang]) >= C("xiang1") && ($dailizh[xiang] + $dailizh3[xiang]) >= C("xiang2") && ($dailizh[xiang] + $dailizh3[xiang]) < C("xiang3")) {
            D("yonghu")->where(array("uid" => $uid3))->save(array("daili" => 2));
        } else if (($dailizh[xiang] + $dailizh3[xiang]) >= C("xiang1") && ($dailizh[xiang] + $dailizh3[xiang]) >= C("xiang2") && ($dailizh[xiang] + $dailizh3[xiang]) >= C("xiang3")) {
            D("yonghu")->where(array("uid" => $uid3))->save(array("daili" => 1));
        }

        if ($checkpay == 'fufen') {
            if ($this->fufen_to_money) {
                $myfufen = $this->members['score'] - $this->fufen;
                $query_fufen = D("yonghu")->where(array("uid" => $uid))->save(array("score" => $myfufen));
                $pay_zhifu_name = '福分';
                $this->MoenyCount = $this->fufen;
            }
            $query_2 = D("yonghu")->where(array("uid" => $uid))->save(array("money" => $Money));   //金额
            $query_3 = $info = D("yonghu")->where(array("uid" => $uid))->find();
            $query_4 = $this->model->table("yys_yonghu_zhanghao")->data(array("uid" => "$uid", "type" => "-1", "pay" => "$pay_zhifu_name", "content" => '直购了商品', "money" => "{$this->MoenyCount}", "time" => $time))->add();
        } else if ($checkpay == 'money') {
            if ($this->fufen_to_money) {
                $myfufen = $this->members['score'] - $this->fufen;
                $tt = $this->members['score'];
                $query_fufen = D("yonghu")->where(array("uid" => $uid))->save(array("score" => $tt));
                $pay_zhifu_name = '账户';
                $this->MoenyCount = $this->fufen;
            }
            //更新用户账户金额
            $query_2 = D("yonghu")->where(array("uid" => $uid))->save(array("money" => $Moneys));   //金额
            $query_3 = $info = D("yonghu")->where(array("uid" => $uid))->find();
            $query_4 = $this->model->table("yys_yonghu_zhanghao")->data(array("uid" => "$uid", "type" => "-1", "pay" => "$pay_zhifu_name", "content" => '直购了商品', "money" => $zhang, "time" => $time))->add();
        } else {
            if ($this->fufen_to_money) {
                $myfufen = $this->members['score'] - $this->fufen;
                $query_fufen = D("yonghu")->where(array("uid" => $uid))->save(array("score" => $myfufen));
                $pay_zhifu_name = '福分';
                $this->MoenyCount = $this->fufen;
            }

            //更新用户账户金额
            $query_2 = D("yonghu")->where(array("uid" => $uid))->save(array("money" => $Money));   //金额
            $query_3 = $info = $info = D("yonghu")->where(array("uid" => $uid))->find();
            $query_4 = $this->model->table("yys_yonghu_zhanghao")->data(array("uid" => "$uid", "type" => "-1", "pay" => "$pay_zhifu_name", "content" => '直购了商品', "money" => "{$this->MoenyCount}", "time" => $time))->add();
        }
        $query_5 = true;
        $query_insert = true;


        $shangpinss_count_num = 0;
        //fix dabin start

        foreach ($this->shoplist as $shop):
            if ($shop['canyurenshu'] >= $shop['zongrenshu'] && $shop['maxqishu'] >= $shop['qishu']) {
                $this->model->table("yys_shangpinzg")->where(array("id" => "$shop[id]"))->save(array("canyurenshu" => $shop['zongrenshu'], "shenyurenshu" => "0"));
            } else {
                $sellnum = $this->model->table("yys_yonghu_yys_recordzg")->where(array("shopid" => "$shop[id]"))->field(array('sum(gonumber)' => 'sellnum'))->find();
                $sellnum = $sellnum['sellnum'];
                $shenyurenshu = $shop['zongrenshu'] - $sellnum;
                $query = $this->model->table("yys_shangpinzg")->where(array("id" => "$shop[id]"))->save(array("canyurenshu" => "$sellnum", "shenyurenshu" => $shenyurenshu));
                if (!$query) {
                    $query_5 = false;
                }
            }
            $shangpinss_count_num += $shop['goods_count_num'];
            //微信登陆通知start
            /*
              include_once "weixin1.class.php";
              $weixin = new class_weixin();
              $dingdancode = $this->dingdancode;
              $iipp = $_SERVER["REMOTE_ADDR"];
              $gggg = System::DOWN_App_config("user_fufen", '', 'member'); //福分/经验/佣金
              $dengluid2 = $gggg[goumaiid];
              $titit = str_replace('&nbsp;', ' ', $shop['title']);
              $openids = $this->db->YOne("SELECT b_data FROM `@#_yonghu_band` WHERE `b_uid` = '$uid'");
              $template = array('touser' => $openids[b_data],
              'template_id' => $dengluid2,
              'url' => C("URL_DOMAIN") . "/mobile/user/buyDetail/" . $shop[id],
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
              // var_dump($shop[title]);
              // exit;
              $weixin->send_template_message($template);

              //微信登陆通知end

             */
        endforeach;
        //fix dabin end
        //添加用户经验
        $jingyan = $this->members['jingyan'] + (C("z_shoppay") * $shangpinss_count_num);
        $query_jingyan = $this->model->table("yys_yonghu")->where(array("uid" => "$uid"))->save(array("jingyan" => "$jingyan")); //经验值
        //添加福分
        if ($checkpay == 'money') {
            $mygoscore = C("f_shoppay") * $shangpinss_count_num;
            $mygoscore_text = "云购了{$shangpinss_count_num}人次商品";
            $myscore = $this->members['score'] + $mygoscore;
            $query_add_fufen_1 = $this->model->table("yys_yonghu")->where(array("uid" => "$uid"))->save(array("score" => "$myscore"));
            $query_add_fufen_2 = $this->model->table("yys_yonghu_zhanghao")->add(array("uid" => "$uid", "type" => "1", "pay" => '福分', "content" => "$mygoscore_text", "money" => "$mygoscore", "time" => "$time"));
            $query_fufen = ($query_add_fufen_1 && $query_add_fufen_2);
        }

        $dingdancode = $this->dingdancode;


        $mmmmm = $this->model->table("yys_yonghu_yys_recordzg")->where(array("code" => "$dingdancode", "uid" => "$uid"))->find();
        if ($mmmmm['leixing'] == 2) {
            $query_6 = $this->model->table("yys_yonghu_yys_recordzg")->where(array("code" => "$dingdancode", "uid" => "$uid"))->save(array("status" => "已付款,已发货,未完成"));
        } else {
            $query_6 = $this->model->table("yys_yonghu_yys_recordzg")->where(array("code" => "$dingdancode", "uid" => "$uid"))->save(array("status" => "已付款,未发货,未完成,未提交地址"));
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
        if ($query_jingyan && $query_1 && $query_2 && $query_3 && $query_4 && $query_5 && $query_6 && $query_7 && $query_insert && $query_8) {
            if (empty($checkpay)) {
                if ($info['money'] == $Money) {
                    $this->model->commit();
                    foreach ($this->shoplist as $shop):
                        if ($shop['canyurenshu'] >= $shop['zongrenshu'] && $shop['maxqishu'] >= $shop['qishu']) {
                            $this->model->startTrans();
                            $query_insert = $this->pay_insert_shopzg($shop, 'add');
                            if (!$query_insert) {
                                $this->model->rollback();
                            } else {
                                $this->model->commit();
                            }
                            $this->model->table("yys_shangpinzg")->where(array("id" => "{$shop['id']}"))->save(array("canyurenshu" => $shop['zongrenshu'], "shenyurenshu" => '0'));
                        }
                    endforeach;
                    return true;
                } else {
                    $this->model->rollback();
                    return false;
                }
            } else {
                if ($checkpay == 'money') {
                    if ($info['money'] == $Moneys) {
                        $this->model->commit();
                        foreach ($this->shoplist as $shop):
                            if ($shop['canyurenshu'] >= $shop['zongrenshu'] && $shop['maxqishu'] >= $shop['qishu']) {
                                $this->model->table("yys_shangpinzg")->where(array("id" => "{$shop['id']}"))->save(array("canyurenshu" => $shop['zongrenshu'], "shenyurenshu" => '0'));
                            }
                        endforeach;
                        return true;
                    }
                } elseif ($checkpay == 'fufen') {
                    if ($info['money'] == $Money) {
                        $this->model->commit();
                        foreach ($this->shoplist as $shop):
                            if ($shop['canyurenshu'] >= $shop['zongrenshu'] && $shop['maxqishu'] >= $shop['qishu']) {
                                $this->model->table("yys_shangpinzg")->where(array("id" => "{$shop['id']}"))->save(array("canyurenshu" => $shop['zongrenshu'], "shenyurenshu" => '0'));
                            }
                        endforeach;
                        return true;
                    }
                } else {
                    $this->model->rollback();
                    return false;
                }
            }
        } else {
            $this->model->rollback();
            return false;
        }
    }

    /*
      揭晓与插入商品
      @shop   商品数据
     */

    function pay_insert_shopzg($shop = '', $type = '') {
        $time = sprintf("%.3f", microtime(true) + (int) C("goods_end_time"));
        $model = new \Think\Model;
        if ($shop['xsjx_time'] != 0) {//////////////////原本是'0'
            return $model->table("yys_shangpinzg")->where(array("id" => "{$shop['id']}"))->save(array("canyurenshu" => $shop['zongrenshu'], "shenyurenshu" => '0'));
        }
        $tocode = new \Claduipi\Pay\tocode;
        $tocode->shop = $shop;
        $tocode->yunxing_shop($time, 100, $shop['canyurenshu'], $shop);
        $code = $tocode->go_code;
        $content = addslashes($tocode->go_content);
        $counttime = $tocode->count_time;
       
        $u_go_info = $model->table("yys_yonghu_yys_recordzg")->where("shopid = '{$shop["id"]}' and shopqishu = '{$shop["qishu"]}' and goucode LIKE  '%$code%'")->find();
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
            $q = $model->table("yys_shangpinzg")->where(array("id" => "{$shop['id']}"))->save($goods_data);
            if (!$q) {
                $query = false;
            }
            if ($q) {
                $q = $model->table("yys_yonghu_yys_recordzg")->where(array("id" => "{$u_go_info['id']}", "code" => "{$u_go_info['code']}", "uid" => "{$u_go_info['uid']}", "shopid" => "{$shop['id']}", "shopqishu" => "{$shop['qishu']}"))->save(array("huode" => "$code"));
                if (!$q) {
                    $query = false;
                } else {
                    $post_arr = array("uid" => $u_info['uid'], "gid" => $shop['id'], "send" => 1);
                    $this->http_request(C("URL_DOMAIN") . "send/send_shop_code", $post_arr);
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
                $maxinfo = $model->table("yys_shangpinzg")->where(array("sid" => "{$shop['sid']}"))->order("qishu DESC")->find();

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

    //生成订单
    private function set_dingdanzg($pay_type = '', $dingdanzhui = '') {
        $uid = $this->members['uid'];
        $uphoto = $this->members['img'];
        $weername = $this->huode_user_name($this->members);
        $this->dingdancode = $dingdancode = R("Pay/pay_huode_dingdan", array($dingdanzhui)); //订单号	
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
        foreach ($this->shoplist as $key => $shop) {
            $this->dingdan_query = true;

            $codes_len = $shop['cart_gorenci'];      //得到夺宝码个数	
            if ($this->members['daili'] == '1') {
                $money = $codes_len * $shop['yunjiage1'];
            } else if ($this->members['daili'] == '2') {
                $money = $codes_len * $shop['yunjiage2'];
            } else if ($this->members['daili'] == '3') {
                $money = $codes_len * $shop['yunjiage3'];
            } else {
                $money = $codes_len * $shop['yunjiage']; //单条商品的总价格
            }
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
            $this->shoplist["$key"] = $shop;
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
        $ret = $this->model->table("yys_yonghu_yys_recordzg")->addall($arr);
        return $ret;
    }

    public function pay_user_go_shopzg($uid = null, $yonghuid = null, &$num = null) {
//        $uid=1;$yonghuid=125;$num=2;  //测试数据
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
        $shangpinsinfo = $db->table("yys_shangpinzg")->where("id = '$yonghuid' and shenyurenshu != '0'")->lock(true)->find();
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
            $pay_1 = $this->pay_bagzg();
            if (!$pay_1) {
                return $pay_1;
            }
            $dingdancode = $this->dingdancode;
            $pay_2 = R("Pay/pay_go_jijin", array($this->goods_count_num));
            $pay_3 = R("Pay/pay_go_yongjin", array($uid, $dingdancode, $uid2, $uid3));
            return $pay_1;
        } else {
            $db->rollback();
            return false;
        }
    }

    //充值
    public function addmoney() {
        if (!ismobile()) {
            $this->Cartlist = cookie('Cartlist');
            $this->Cartlistzg = cookie('Cartlistzg');
            if (!isset($_POST['submit'])) {
                $this->autoShow("正在返回充值页面...", C("URL_DOMAIN") . 'user/userrecharge');
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
                $this->autoShow("未选择支付平台");
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


        $up_q1 = $db->table("yys_yonghu_addmoney_record")->where(array("id" => $dingdaninfo['id'], "code" => $dingdaninfo['code']))->save(array("pay_type" => "微信手机支付", "status" => "已付款"));
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
