<?php

namespace Claduipi\Payment;

//全局引入微信支付类
Vendor('Payment.Wxpay.WxPayPubHelper.WxPayPubHelper');

/**
 * 类
 */
class weixin {

    private $config;

    public function config($config = null) {
        $this->config = $config;
    }

    /**
     * 生成支付代码
     * @param   array   $order      订单信息
     * @param   array   $payment    支付方式信息
     */
    //支付页面

    public function send_pay() {
        //使用jsapi接口
        $unifiedOrder = new \UnifiedOrder_pub();
        $amount = trim($this->config['money']) * 100;
        //$amount = 1;

        $notify_url = $this->config['NotifyUrl'];   //通知URL
        //设置统一支付接口参数
        //设置必填参数
        //appid已填,商户无需重复填写
        //mch_id已填,商户无需重复填写
        //noncestr已填,商户无需重复填写
        //spbill_create_ip已填,商户无需重复填写
        //sign已填,商户无需重复填写
        //iconv("gb2312","utf-8//IGNORE",
        $unifiedOrder->setParameter("body", $this->config['title']); //商品描述
        //自定义订单号，此处仅作举例
        $out_trade_no = $this->config['code'];
        $unifiedOrder->setParameter("out_trade_no", $out_trade_no); //商户订单号 
        $unifiedOrder->setParameter("total_fee", $amount); //总金额
        $unifiedOrder->setParameter("notify_url", $notify_url); //通知地址 
        $unifiedOrder->setParameter("trade_type", "NATIVE"); //交易类型
        //非必填参数，商户可根据实际情况选填
        //$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号  
        //$unifiedOrder->setParameter("device_info","XXXX");//设备号 
        $unifiedOrder->setParameter("attach", "111"); //附加数据 
        //$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
        //$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间 
        //$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记 
        //$unifiedOrder->setParameter("openid","XXXX");//用户标识
        //$unifiedOrder->setParameter("product_id","XXXX");//商品ID
        //获取统一支付接口结果
        //dump($unifiedOrder);exit;
        $unifiedOrderResult = $unifiedOrder->getResult();
        //商户根据实际情况设置相应的处理流程
        if ($unifiedOrderResult["return_code"] == "FAIL") {
            //商户自行增加处理流程
            echo "通信出错：" . $unifiedOrderResult['return_msg'] . "<br>";
            exit;
        } elseif ($unifiedOrderResult["result_code"] == "FAIL") {
            //商户自行增加处理流程
            echo iconv("utf-8", "gb2312//IGNORE", "错误代码：" . $unifiedOrderResult['err_code'] . "<br>");
            echo iconv("utf-8", "gb2312//IGNORE", "错误代码描述：" . $unifiedOrderResult['err_code_des'] . "<br>");
            exit;
        } elseif ($unifiedOrderResult["code_url"] != NULL) {
            include('diannao.php');
        }
    }

    public function serverCallback($xml, &$money, &$message, &$orderNo) {
        //使用通用通知接口
        $notify = new \Notify_pub();
        //存储微信的回调
        $notify->saveData($xml);

        if ($notify->checkSign() == FALSE) {
            $notify->setReturnParameter("return_code", "FAIL"); //返回状态码
            $notify->setReturnParameter("return_msg", "签名失败"); //返回信息
        } else {
            $notify->setReturnParameter("return_code", "SUCCESS"); //设置返回码
        }
        $returnXml = $notify->returnXml();
        echo $returnXml;

        if ($notify->checkSign() == TRUE) {
            if ($notify->data["return_code"] == "FAIL") {
                $message = "return_code error通信出错";
                //\Think\Log::record("【通信出错】:\n" . $xml . "\n")
                return 0;
            } elseif ($notify->data["result_code"] == "FAIL") {
                $message = "result_code error业务出错";
                return -1;
            } else {
                $money = $notify->data["total_fee"];
                $orderNo = $notify->data["out_trade_no"];
                return 1;
            }
        }
    }

}

?>