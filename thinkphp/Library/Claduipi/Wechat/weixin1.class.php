<?php

namespace Claduipi\Wechat;

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
/*
  方倍工作室 http://www.fangbei.org/
  CopyRight 2014 All Rights Reserved
 */

define('APPID', C(appid));
define('APPSECRET', C(secret));

class weixin1 {

    var $appid = APPID;
    var $appsecret = APPSECRET;

    //构造函数，获取Access Token
    public function __construct($appid = NULL, $appsecret = NULL) {

        if ($appid && $appsecret) {
            $this->appid = $appid;
            $this->appsecret = $appsecret;
        }
        //require_once($_SERVER['DOCUMENT_ROOT'] . "/yiyuansha/class/mobile/jssdk.php");

        $jssdk = new \Claduipi\Wechat\JSSDK($appId, $secret);
        //dump(2323);exit;
        $access_token = $jssdk->getAccessToken();//echo 1111;exit;
        //$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret;
		
        $res = $this->http_request($url);
        $result = json_decode($res, true);
        $this->access_token = $access_token;
        $data = json_decode(file_get_contents("access_token.json"));
        $this->expires_time = $data->expire_time;

    }

    /*
     *  PART3 模板消息
     */

    //发送模版消息



    public function send_template_message($template) {
        foreach ($template['data'] as $k => &$item) {
            $item['value'] = urlencode($item['value']);
        }
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $this->access_token;
        $res = $this->http_request($url, urldecode(json_encode($template)));
        return json_decode($res, true);
    }

    //HTTP请求（支持HTTP/HTTPS，支持GET/POST）
    protected function http_request($url, $data = null) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

    //日志记录
    private function logger($log_content) {
        if (isset($_SERVER['HTTP_APPNAME'])) {   //SAE
            sae_set_display_errors(false);
            sae_debug($log_content);
            sae_set_display_errors(true);
        } else if ($_SERVER['REMOTE_ADDR'] != "127.0.0.1") { //LOCAL
            $max_size = 500000;
            $log_filename = "log.xml";
            if (file_exists($log_filename) and ( abs(filesize($log_filename)) > $max_size)) {
                unlink($log_filename);
            }
            file_put_contents($log_filename, date('Y-m-d H:i:s') . $log_content . "\r\n", FILE_APPEND);
        }
    }

}
