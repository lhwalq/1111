<?php

/**
 * 微信支付 基础类
 * 2015/12/17
 */

namespace Claduipi\Wechat;

class Wechat {

    var $appid = "";
    var $appsecret = "";
    var $access_token = "";

    //构造函数，获取Access Token
    public function __construct($appid = NULL, $appsecret = NULL) {
        if ($appid && $appsecret) {
            $this->appid = $appid;
            $this->appsecret = $appsecret;
        } else {
            $this->appid = C("appid");
            $this->appsecret = C("secret");
        }

        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $this->appid . "&secret=" . $this->appsecret;
        $res = $this->http_request($url);
        $result = json_decode($res, true);
        $this->access_token = $result["access_token"];
        $this->expires_time = time();
    }

    /**
     *  PART3 模板消息
     */
    public function send_template_message($template) {
        foreach ($template['data'] as $k => &$item) {
            $item['value'] = urlencode($item['value']);
        }
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $this->access_token;
        $res = $this->http_request($url, urldecode(json_encode($template)));
        return json_decode($res, true);
    }

    /**
     * 生成签名
     * @param $params生成签名所需要的数组
     * @return 签名
     */
    public function getOrderMd5($params, $key) {
        ksort($params);
        $params['key'] = $key;
        return strtoupper(md5(urldecode(http_build_query($params))));
    }

    /**
     * XML文档解析成数组，并将键值转成小写
     * @param  $xml要转换的XML
     * @param  $toBig 返回结果是否转为大写
     * @return array
     */
    public function extractXml($xml, $toBig) {
        $data = (array) simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($toBig) {
            return array_change_key_case($data, CASE_LOWER);
        }
        return $data;
    }

    /**
     * 把数据转换成XML格式
     * @param  $xml生成的XML
     * @param $data要转换的数据
     */
    public function data2xml($xml, $data, $item = 'item') {
        foreach ($data as $key => $value) {
            is_numeric($key) && $key = $item;
            if (is_array($value) || is_object($value)) {
                $child = $xml->addChild($key);
                $this->data2xml($child, $value, $item);
            } else {
                if (is_numeric($value)) {
                    $child = $xml->addChild($key, $value);
                } else {
                    $child = $xml->addChild($key);
                    $node = dom_import_simplexml($child);
                    $node->appendChild($node->ownerDocument->createCDATASection($value));
                }
            }
        }
    }

    /**
     * 把数组转换成XML
     * @param  $array需要转换的数组
     * @return xml
     */
    public function array2Xml($array) {
        $xml = new \SimpleXMLElement('<xml></xml>');
        $this->data2xml($xml, $array);
        return $xml->asXML();
    }

    /**
     * 生成随机nonce_str (微信参数，验证用)
     * @param  $lenght需要生成的长度默认16位
     * @return 生成后的随机串
     */
    public function getRandomStr($lenght = 16) {
        $str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        return substr(str_shuffle($str_pol), 0, $lenght);
    }

    /**
     * 发送HTTP请求
     * @param  $url 目标路径
     * @param  $params 目标路径POST时要发送的数据
     * @param  $method 请求方式
     * @param  $ssl证书 默认不需要
     * @return 返回微信API所返回的数据
     */
    public function http($url, $params = array(), $method = 'GET', $ssl = false) {
        $opts = array(CURLOPT_TIMEOUT => 30, CURLOPT_RETURNTRANSFER => 1, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => false);
        /* 根据请求类型设置特定参数 */
        switch (strtoupper($method)) {
            case 'GET' :
                $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
                break;
            case 'POST' :
                $opts[CURLOPT_URL] = $url;
                $opts[CURLOPT_POST] = 1;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
        }
        if ($ssl) {
            $pemPath = dirname(__FILE__) . '/Wechat/';
            $pemCret = $pemPath . $this->pem . '_cert.pem';
            $pemKey = $pemPath . $this->pem . '_key.pem';
            if (!file_exists($pemCret)) {
                $this->error = '证书不存在';
                return false;
            }
            if (!file_exists($pemKey)) {
                $this->error = '密钥不存在';
                return false;
            }
            $opts[CURLOPT_SSLCERTTYPE] = 'PEM';
            $opts[CURLOPT_SSLCERT] = $pemCret;
            $opts[CURLOPT_SSLKEYTYPE] = 'PEM';
            $opts[CURLOPT_SSLKEY] = $pemKey;
        }
        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        curl_close($ch);
        if ($err > 0) {
            $this->error = $errmsg;
            return false;
        } else {
            return $data;
        }
    }

    /**
     * 形成微信支付所需要的参数
     * @param  $prepay_id 统一下单后返回回来的预支付交易会话标识 在return_code 和result_code都为SUCCESS的时候有返回
     * @return 返回json数组
     */
    public function createPayParams($prepay_id, $key) {



        if (empty($prepay_id)) {
            echo 'prepay_id参数错误';
            return false;
        }
        $params['appId'] = C("appid");
        $timeStamp = time();
        $params['timeStamp'] = "$timeStamp";
        $params['nonceStr'] = $this->getRandomStr();
        $params['package'] = 'prepay_id=' . $prepay_id;
        $params['signType'] = 'MD5';
        $params['paySign'] = $this->getOrderMd5($params, $key);
        return json_encode($params);
    }

    /**
     * 通过页面传过来的CODE获取用户信息 网页授权接口调用凭证,注意：此access_token与基础支持的access_token不同($baseinfo)
     * @param $appid 微信appid
     * @param $secret 微信secret
     * @param $code 为了获取用户信息用
     * @return 返回用户信息
     */
    public function AuthNow($appid, $secret, $code) {
        $ret = file_get_contents('https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $secret . '&code=' . $code . '&grant_type=authorization_code');
        $baseinfo = json_decode($ret);
        //拉取用户信息
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $baseinfo->access_token . '&openid=' . $baseinfo->openid . "&lang=zh_CN";
        $html = file_get_contents($url);
        $obj = json_decode($html, true);
        if (empty($obj['nickname'])) {
            $obj['nickname'] = $obj['openid'];
        }
        return $obj;
    }

    /**
     * 支付(1.提交支付信息获取预支付交易会话标识2.成功则生成支付需要的参数并返回)
     * @param string $appid 微信分配的公众账号ID
     * @param string $openid 用户ID 从验证code后取得 PC端可不传此参数
     * @param string $mch_id 微信支付分配的商户号
     * @param string $body 商品或支付单简要描述
     * @param string $out_trade_no 商户系统内部的订单号,32个字符内、可包含字母
     * @param int $total_fee 订单总金额，只能为整数
     * @param string $trade_type 支付类型 分JSAPI支付和NATIVE  分别为微信支付 跟二维码支付
     * @param string $key 微信商户列表KEY
     * @param string $notify_url 回调URL
     * @param string $attach 附带参数
     * @return 返回支付所需要的参数供前端调用
     */
    public function pay($openid, $appid, $mch_id, $body, $out_trade_no, $total_fee, $trade_type, $key, $notify_url, $attach) {
        $params = array('openid' => $openid, 'appid' => $appid, 'mch_id' => $mch_id, 'nonce_str' => $this->getRandomStr(), 'body' => $body, 'out_trade_no' => $out_trade_no, 'total_fee' => $total_fee, 'spbill_create_ip' => $_SERVER['REMOTE_ADDR'], 'notify_url' => $notify_url, 'trade_type' => $trade_type, 'attach' => $attach);
        $params['sign'] = $this->getOrderMd5($params, $key);
        $data = $this->array2Xml($params, $key);
        $data = $this->http('https://api.mch.weixin.qq.com/pay/unifiedorder', $data, 'POST');
        $data = $this->extractXml($data, 1);

        if ($data['return_code'] == "SUCCESS") {
            if ($data['result_code'] == "SUCCESS") {
                if ($trade_type == "JSAPI") {
                    $r = $this->createPayParams($data['prepay_id'], $key);
                    return $r;
                } else if ($trade_type == "NATIVE") {
                    $r = $data['code_url'];
                    return $r;
                }
            } else {
                return ($data['err_code'] . " " . $data['err_code_des']);
            }
        } else {
            return ($data['return_msg']);
        }
    }

    /**
     * 退款操作
     * @param string $order_no 商户订单号
     * @param string $refund_no 商户退款单号，同一个退款单号多次请求只退款一次
     * @param int $total_fee 总金额（单位分）
     * @param int $refund_fee 退款金额（单位分）
     * @return 返回支付所需要的参数供前端调用
     */
    public function returnMoeny($order_no, $refund_no, $total_fee, $refund_fee) {
        $site_config = include '/config/site_config.php';
        $appid = $site_config['wechat_AppID'];
        $mch_id = $site_config['wechat_mchID'];
        $key = $site_config['wechat_Key'];
        $data = array(
            "appid" => $appid,
            "mch_id" => $mch_id,
            "nonce_str" => $this->getRandomStr(),
            "out_trade_no" => $order_no,
            "out_refund_no" => $refund_no,
            "total_fee" => $total_fee * 100,
            "refund_fee" => $refund_fee * 100,
//            "refund_fee_type" => "CNY",//货币类型
            "op_user_id" => $mch_id//操作员  默认为商户号
        );
        $data['sign'] = $this->getOrderMd5($data, $key);

        $post_data = $this->array2Xml($data, $key);
        $return_data = $this->http('https://api.mch.weixin.qq.com/secapi/pay/refund', $post_data, 'POST', true); //带证书的post请求
        $arr_data = $this->extractXml($return_data, l);
        return $arr_data;
    }

}
