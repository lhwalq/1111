<?php

namespace Duipi\Controller;

use Think\Controller;

header("Access-Control-Allow-Origin:*");
if (EXEC_RET != 'ZHVpcGkuY29tbGVxaW5nc2hpZHVpcGl3YW5nbHVva2VqaXlvdXhpYW5nb25nc2kxMjM') {
    header('HTTP/1.1 404 Not Found');
    header('status: 404 Not Found'); //404状态码  
};
/*
 * controller的父类
 */

class BaseController extends Controller {

    public function __construct111() {//暂时不用
        $uid = intval($this->encrypt(cookie("uid"), 'DECODE'));
        $wehell = $this->encrypt(cookie("ushell"), 'DECODE');
        if (!$uid)
            $this->userinfo = false;
        $this->userinfo = D("yonghu")->where(array("uid" => $uid))->find();

        if (!$this->userinfo)
            $this->userinfo = false;

        $shell = md5($this->userinfo['uid'] . $this->userinfo['password'] . $this->userinfo['mobile'] . $this->userinfo['email']);

        if ($this->userinfo['dongjie']) {
            cookie("uid", "", time() - 3600);
            cookie("ushell", "", time() - 3600);
            $this->autoNote("帐号被冻结，请联系管理员", C("URL_DOMAIN") . "index/index");
        }
        //限制登陆补丁
        if ($wehell != $shell)
            $this->userinfo = false;
        global $_yys;
        $_yys['userinfo'] = $this->userinfo;
    }

    function idjia($id) {
        return $id + 1000000000;
    }

    function quanzid($qzid) {
        $quanzi = D("quan")->where(array("id" => $qzid))->find();
        return $quanzi['title'];
    }

    function huati($lex) {
        $uid = $this->encrypt(cookie('uid'), 'DECODE');
        if ($lex == 'tiezi') {
            $dongtai = D("quan_tiezi")->where(array("hueiyuan" => $uid))->select();
            return count($dongtai);
        }if ($lex == 'hueifu') {
            $hueifu = D("quan_hueifu")->where(array("hueiyuan" => $uid))->select();
            return count($hueifu);
        }
    }

    function qznum() {
        $uid = $this->encrypt(cookie('uid'), 'DECODE');
        $huiyuan = D("yonghu")->where(array("uid" => $uid))->find();
        $addgroup = rtrim($huiyuan['addgroup'], ",");
        if ($addgroup) {
            $group = D("quan")->where("id in ($addgroup)")->select();
            return count($group);
        } else {
            $group = null;
            return false;
        }
    }

    function huifu($tzid) {
        $quanzi = D("quan_hueifu")->where(array("tzid" => $tzid))->select();
        return count($quanzi);
    }

    function width($p, $t, $w) {
        if ($p <= 0) {
            return 0;
        }
        return $p / $t * $w;
    }

    function Getlogo() {
        $web_logo = D("configs")->where(array("name" => "web_logo"))->find();
        return $web_logo['value'];
    }

//总一元云购人次
    function go_count_renci() {
        $recordx = D("linshi")->where(array("key" => "goods_count_num"))->find();
        return $recordx['value'];
    }

    function Getheader($type = 'index') {
        $navigation = D("daohang")->where(array("status" => "Y", "type" => $type))->order("`order` desc")->select();
        $url = "";
        if ($type == 'foot') {
            foreach ($navigation as $v) {
                $url.='<a  href="' . C("URL_DOMAIN") . $v['url'] . '">' . $v['name'] . '</a><b></b>';
            }
            return $url;
        }
        if ($type == 'faxian') {
            foreach ($navigation as $v) {
                $url.='<li class="sort-all" ><a  href="' . C("URL_DOMAIN") . $v['url'] . '">' . $v['name'] . '</a><em></em></li>';
            }
            return $url;
        }
        foreach ($navigation as $v) {
            $url.='<li class="sort-all" ><a  href="' . C("URL_DOMAIN") . $v['url'] . '">' . $v['name'] . '</a><em>/</em></li>';
        }
        return $url;
    }

    /**
     * 显示页面的时候验证
     * @param type $root 模块名
     * @param type $note 节点
     */
    function showCheck($root, $note) {
        $per = new \Org\Util\Permissions();
        $bool = $per->checkPermissions($root, $note);
        if ($bool == 0) {
            $this->display("/daohang");
            exit();
        }
        if ($bool == -1) {
            $User = A('Users');
            $User->newAuth();
            exit();
        }
    }

    function strlen($str = '') {
        if (empty($str)) {
            return 0;
        }
        if (!$this->is_utf8($str)) {
            $str = iconv("GBK", "UTF-8", $str);
        }
        return ceil((strlen($str) + mb_strlen($str, 'utf-8')) / 2);
    }

    function checkemail($youjian = '') {
        if (mb_strlen($youjian) < 5) {
            return false;
        }
        $res = "/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/";
        if (preg_match($res, $youjian)) {
            return true;
        } else {
            return false;
        }
    }

    function checkmobile($mobilephone = '') {
        if (strlen($mobilephone) != 11) {
            return false;
        }
        if (preg_match("/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|17[0-9]{1}[0-9]{8}$|14[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/", $mobilephone)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取用户信息  测试版本
     */
    public function getUserInfo1() {
        $db_user = D("yonghu");
        $huiyuan = $db_user->where(array("uid" => 6790))->find();
        return $huiyuan;
    }

    /* 栏目类型 */

    function cattype($n = 0) {
        if ($n > 0) {
            return '<font>内部栏目</font>';
        }
        if ($n == -1) {
            return '<font color="#ff0000">单网页</font>';
        }
        if ($n == -2) {
            return '<font color="#09f">链接</font>';
        }
    }

    function genRandomString($changdu) {
        $chars = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "m", "n", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "2", "3", "4", "5", "6", "7", "8", "9");
        $charsLen = count($chars) - 1;
        shuffle($chars);
        $output = "";
        for ($i = 0; $i < $changdu; $i++) {
            $output .= $chars[mt_rand(0, $charsLen)];
        }return $output;
    }

    function genRandomString2($changdu) {
        $chars = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
        $charsLen = count($chars) - 1;
        shuffle($chars);
        $output = "";
        for ($i = 0; $i < $changdu; $i++) {
            $output .= $chars[mt_rand(0, $charsLen)];
        }return $output;
    }

    function huode_lanmu($cid) {
        if (empty($cid)) {
            return '';
        }
        $info = D("fenlei")->where(array("cateid" => $cid))->find();
        if ($info) {
            return $info['name'];
        } else {
            return '';
        }
    }

    /**
     * 	获取当前登陆用户头像
     * 	
     */
    function huode_user_img($size = '') {
        $_yys = $this->getUserInfo();
        if ($_yys) {
            $fk = explode('.', $_yys['img']);
            $h = array_pop($fk);
            if ($size) {
                return $_yys['img'] . '_' . $size . '.' . $h;
            } else {
                return $_yys['img'];
            }
        } else {
            return 'photo/member.jpg';
        }
    }

    /*
     * 	获取当前登录用户数组
     */

    function huode_user_arr($key = '', $where = '') {
        global $_yys;
        if (isset($_yys['userinfo'])) {
            return $_yys['userinfo'];
        }

        if (empty($where)) {
            $where = 'uid,username,password,email,mobile,img';
        } else {
            $where = 'uid,username,password,email,mobile,img,' . $where;
        }

        $db = D("yonghu");
        $uid = abs(intval($this->encrypt(cookie("uid"), 'DECODE')));
        $wehell = $this->encrypt(cookie("ushell"), 'DECODE');
        if (!$uid) {
            return false;
        }
        $_yys['userinfo'] = $db->where(array("uid" => "$uid"))->field($where)->find();
        if (!$_yys['userinfo']) {
            return false;
        }
        $shell = md5($_yys['userinfo']['uid'] . $_yys['userinfo']['password'] . $_yys['userinfo']['mobile'] . $_yys['userinfo']['email']);
        if ($wehell != $shell) {
            return false;
        }
        if (empty($key)) {
            return $_yys['userinfo'];
        } elseif (isset($_yys['userinfo']['key'])) {
            return $_yys['userinfo']['key'];
        } else {
            return false;
        }
    }

    function uidcookie($get_name = null) {
        $huiyuan = $this->getUserInfo();
        if (!$huiyuan)
            return false;
        if (isset($huiyuan[$get_name])) {
            return $huiyuan[$get_name];
        } else {
            return null;
        }
    }

    public function getUserInfo() {
        $uid = intval($this->encrypt(cookie("uid"), 'DECODE'));
        $wehell = $this->encrypt(cookie("ushell"), 'DECODE');
        if (!$uid)
            return false;
        if ($wehell === NULL)
            return false;
        $userinfo = D("yonghu")->where(array("uid" => "$uid"))->find();
        if (!$userinfo) {
            return false;
        }
        $shell = md5($userinfo['uid'] . $userinfo['password'] . $userinfo['mobile'] . $userinfo['email']);
        if ($wehell != $shell) {
            return false;
        } else {
            return $userinfo;
        }
    }

    /**
     * 跳转登录
     */
    public function HeaderLogin() {
        $this->autoNote("你还未登录，无权限访问该页！", C("URL_DOMAIN") . "/user/login");
    }

    /**
     * admin信息
     * @return type
     */
    public function getAdminInfo($is_login = true) {
        $id = $this->encrypt(cookie("AID"), 'DECODE');
        if (!$id && $is_login) {
            $this->note("请登录后在查看页面", C("URL_DOMAIN") . 'admin/login');
            exit;
        } else if (!$id) {
            return FALSE;
        }
        return D("manage")->where(array("uid" => $id))->find();
    }

    /**
     * 获取购物车信息
     */
    public function getShopCart($type = null) {
        //查询购物车的信息
        if (!$type) {
            $Mcartlist = cookie("Cartlist");
        } else {
            $Mcartlist = cookie($type);
        }

        return json_decode(stripslashes($Mcartlist), true);
    }

    /**
     * 清理购物车
     */
    public function clearShopCart() {
        cookie("Cartlist", NULL);
    }

    /**
     * 验证
     * @param type $root 模块
     * @param type $note 节点
     * @param type $type 验证类型 1 显示页面的时候验证，  2  ajax的时候验证 ,3 审核的时候验证
     * @param type $info 要提示的信息
     */
    function Check($root, $note, $type = 1, $info = "你没有权限！！！") {
        return true;
        $per = new \Org\Util\Permissions();
        $bool = $per->checkPermissions($root, $note);
        switch ($type) {
            case 1:
                if ($bool == 0) {
                    $this->assign("msg", $info);
                    $this->display("/Transition");
                    exit();
                }
                if ($bool == -1) {
                    $User = A('Users');
                    $User->newAuth();
                    exit();
                }
                break;
            case 2:
                $res = array();
                $res['status'] = 0;
                if ($bool == 0) {
                    $res['info'] = $info;
                    self::ajaxReturn($res, JSON);
                    exit();
                }
                if ($bool == -1) {
                    $res['info'] = '请先登录!';
                    self::ajaxReturn($res, JSON);
                    exit();
                }
                break;
            case 3:
                if ($bool == 0) {
                    $res['status'] = 1;
                    $res['info'] = $info . "没有审核权限!";
                    self::ajaxReturn($res, JSON);
                    exit();
                }
                if ($bool == -1) {
                    $res['status'] = 1;
                    $res['info'] = $info . '请先登录!';
                    self::ajaxReturn($res, JSON);
                    exit();
                }
                break;
        }
    }

    function myReturn($code, $info, $count = 0, $data = "") {
        $return['code'] = $code;
        $return['count'] = $count;
        $return['listItems'] = $data;
        $return['info'] = $info;
        self::ajaxReturn($return, "JSON");
    }

    /**
     * 获得用户IP
     * @param type $id 用户ID
     * @param type $ipmac
     * @return int
     */
    function getIp($id, $ipmac = null) {
        return 10;
        $db_user_record = D("yonghu_yys_record");
        $record = $db_user_record->where(array("id" => $id))->find();
        $ip = explode(',', $record['ip']);
        if ($ipmac == 'ipmac') {
            return $ip[1];
        } elseif ($ipmac == 'ipcity') {
            return $ip[0];
        }
    }

    /**
     * 转换数组 指定key 作为下标
     */
    function key2key($fenlei, $keys) {
        $fenleis = array();
        foreach ($fenlei as $key => $value) {
            $fenleis[$value[$keys]] = $value;
        }
        return $fenleis;
    }

    function huode_ip_dizhi($ip = null) {
        $opts = array(
            'http' => array(
                'method' => "GET",
                'timeout' => 5,)
        );
        $context = stream_context_create($opts);


        if ($ip) {
            $ipmac = $ip;
        } else {
            $ipmac = $this->huode_ip();
            if (strpos($ipmac, "127.0.0.") === true)
                return '';
        }

        $url_ip = 'http://ip.taobao.com/service/getIpInfo.php?ip=' . $ipmac;
        $str = @file_get_contents($url_ip, false, $context);
        if (!$str)
            return "";
        $json = json_decode($str, true);
        if ($json['code'] == 0) {

            $json['data']['region'] = addslashes($this->htmtguolv($json['data']['region']));
            $json['data']['city'] = addslashes($this->htmtguolv($json['data']['city']));

            $ipcity = $json['data']['region'] . $json['data']['city'];
            $ip = $ipcity . ',' . $ipmac;
        } else {
            $ip = "";
        }
        return $ip;
    }

    //异步登陆
    function put_file_from_url_content($url, $saveName, $path) {
        // 设置运行时间为无限制
        set_time_limit(10);

        $url = trim($url);
        $curl = curl_init();
        // 设置你需要抓取的URL
        curl_setopt($curl, CURLOPT_URL, $url);
        // 设置header
        curl_setopt($curl, CURLOPT_HEADER, 0);
        // 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // 运行cURL，请求网页
        $file = curl_exec($curl);
        // 关闭URL请求
        curl_close($curl);
        // 将文件写入获得的数据
        $filename = $path . $saveName;
        $write = @fopen($filename, "w");
        if ($write == false) {
            return false;
        }
        if (fwrite($write, $file) == false) {
            return false;
        }
        if (fclose($write) == false) {
            return false;
        }
    }

    function htmtguolv($content) {
        $content = str_replace('%', '%&lrm;', $content);
        $content = str_replace("<", "&lt;", $content);
        $content = str_replace(">", "&gt;", $content);
        $content = str_replace("\n", "<br/>", $content);
        $content = str_replace(" ", "&nbsp;", $content);
        $content = str_replace('"', "&quot;", $content);
        $content = str_replace("'", "&#039;", $content);
        $content = str_replace("$", "&#36;", $content);
        $content = str_replace('}', '&rlm;}', $content);
        return $content;
    }

    /**
     * 时间戳转时间
     * @param type $time
     * @param type $x
     * @return type
     */
    function microt($time, $x = null) {
        $changdu = strlen($time);
        if ($changdu < 13) {
            $time = $time . "0";
        }
        $list = explode(".", $time);
        if ($x == "L") {
            return date("His", $list[0]) . substr($list[1], 0, 3);
        } else if ($x == "Y") {
            return date("Y-m-d", $list[0]);
        } else if ($x == "H") {
            return date("H:i:s", $list[0]) . "." . substr($list[1], 0, 3);
        } else if ($x == "r") {
            return date("Y年m月d日 H:i", $list[0]);
        } else {
            return date("Y-m-d H:i:s", $list[0]) . "." . substr($list[1], 0, 3);
        }
    }

    /*
     * 获取用户昵称
     * uid 用户id，或者 用户数组
     * type 获取的类型, username,email,mobile
     * key  获取完整用户名, sub 截取,all 完整
     */

    function huode_user_name($uid = '', $type = 'username', $key = 'sub') {
        if (is_array($uid)) {
            if (isset($uid['username']) && !empty($uid['username'])) {
                return $uid['username'];
            }
            if (isset($uid['email']) && !empty($uid['email'])) {
                if ($key == 'sub') {
                    $youjian = explode('@', $uid['email']);
                    return $uid['email'] = substr($uid['email'], 0, 2) . '*' . $youjian[1];
                } else {
                    return $uid['email'];
                }
            }
            if (isset($uid['mobile']) && !empty($uid['mobile'])) {
                if ($key == 'sub') {
                    return $uid['mobile'] = substr($uid['mobile'], 0, 3) . '****' . substr($uid['mobile'], 7, 4);
                } else {
                    return $uid['mobile'];
                }
            }
            return '';
        } else {
            $uid = intval($uid);
            $db_user = D("yonghu");
            $info = $db_user->where(array("uid" => $uid))->field("username,email,mobile")->find();
            if (isset($info['username']) && !empty($info['username'])) {
                return $info['username'];
            }

            if (isset($info['email']) && !empty($info['email'])) {
                if ($key == 'sub') {
                    $youjian = explode('@', $info['email']);
                    return $info['email'] = substr($info['email'], 0, 2) . '*' . $youjian[1];
                } else {
                    return $info['email'];
                }
            }
            if (isset($info['mobile']) && !empty($info['mobile'])) {
                if ($key == 'sub') {
                    return $info['mobile'] = substr($info['mobile'], 0, 3) . '****' . substr($info['mobile'], 7, 4);
                } else {
                    return $info['mobile'];
                }
            }
            if (isset($info[$type]) && !empty($info[$type])) {
                return $info[$type];
            }
            return '';
        }
    }

    function g_YYSabcde($url, $io = false, $post_data = array(), $cookie = array()) {
        $method = empty($post_data) ? 'GET' : 'POST';

        $url_array = parse_url($url);
        $port = isset($url_array['port']) ? $url_array['port'] : 80;

        if (function_exists('fsockopen')) {
            $fp = @fsockopen($url_array['host'], $port, $errno, $errstr, 30);
        } elseif (function_exists('pfsockopen')) {
            $fp = @pfsockopen($url_array['host'], $port, $errno, $errstr, 30);
        } elseif (function_exists('stream_socket_client')) {
            $fp = @stream_socket_client($url_array['host'] . ':' . $port, $errno, $errstr, 30);
        } else {
            $fp = false;
        }

        if (!$fp) {
            return false;
        }



        $url_array['query'] = isset($url_array['query']) ? $url_array['query'] : '';
        $getPath = $url_array['path'] . "?" . $url_array['query'];

        $header = $method . " " . $getPath . " ";
        $header .= "HTTP/1.1\r\n";
        $header .= "Host: " . $url_array['host'] . "\r\n"; //HTTP 1.1 Host域不能省略
        $header .= "Pragma: no-cache\r\n";




        if (!empty($cookie)) {
            $_cookie_s = strval(NULL);
            foreach ($cookie as $k => $v) {
                $_cookie_s .= $k . "=" . $v . "; ";
            }
            $_cookie_s = rtrim($_cookie_s, "; ");
            $cookie_str = "Cookie: " . base64_encode($_cookie_s) . " \r\n";    //传递Cookie
            $header .= $cookie_str;
        }
        $post_str = '';
        if (!empty($post_data)) {
            $_post = strval(NULL);
            foreach ($post_data as $k => $v) {
                $_post .= $k . "=" . urlencode($v) . "&";
            }
            $_post = rtrim($_post, "&");
            $header .= "Content-Type: application/x-www-form-urlencoded\r\n"; //POST数据
            $header .= "Content-Length: " . strlen($_post) . " \r\n"; //POST数据的长度	

            $post_str = $_post . "\r\n"; //传递POST数据
        }
        $header .= "Connection: Close\r\n\r\n";
        $header .= $post_str;

        fwrite($fp, $header);
        if ($io) {
            while (!feof($fp)) {
                echo fgets($fp, 1024);
            }
        }
        fclose($fp);
        //echo $header;
        return true;
    }

    /*
     * 获取用户信息
     */

    function huode_user_key($uid = '', $type = 'img', $size = '') {
        if (is_array($uid)) {
            if (isset($uid[$type])) {
                if ($type == 'img') {
                    $fk = explode('.', $uid[$type]);
                    $h = array_pop($fk);
                    if ($size) {
                        return $uid[$type] . '_' . $size . '.' . $h;
                    } else {
                        return $uid[$type];
                    }
                }
                return $uid[$type];
            }
            return 'null';
        } else {
            $db = D("yonghu");
            $uid = intval($uid);
            $info = $db->where(array("uid" => "$uid"))->find();
            if ($type == 'img') {
                $fk = explode('.', $info[$type]);
                $h = array_pop($fk);
                if ($size) {
                    return $info[$type] . '_' . $size . '.' . $h;
                } else {
                    return $info[$type];
                }
            }
            if (isset($info[$type])) {
                return $info[$type];
            }
            return 'null';
        }
    }

    /**
     * 消息页面
     * @param type $string
     * @param type $defurl
     * @param type $time
     * @param type $config
     */
    function notemobile($string = null, $defurl = null, $time = 2, $config = null) {
        if (empty($defurl)) {
            $defurl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
            if (empty($defurl))
                $defurl = dirname(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $_SERVER['SCRIPT_NAME']);
        }
        if (empty($config)) {
            if (DOWN_M == C("admindir")) {
                $config = array();
                $config['titlebg'] = '#549bd9';
                $config['title'] = '#fff';
            }
        }
        $time = intval($time);
        if ($time < 2) {
            $time = 2;
        }
        $this->assign("time", $time);
        $this->assign("defurl", $defurl);
        $this->assign("string", $string);
        $this->display("public/moblie.message");
        exit;
    }

    function note($string = null, $defurl = null, $time = 2, $config = null) {
        $info = $this->getAdminInfo(FALSE);
        if (empty($defurl)) {
            $defurl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        }
        if ($info) {
            if (empty($config)) {
                $config = array("titlebg" => "#549bd9", "title" => "#fff");
            }
            $str_url_two = array("url" => C("URL_DOMAIN") . "admin/Tdefault", "text" => "返回后台首页");
        } else {
            $str_url_two = array("url" => C("URL_DOMAIN") . "index/index", "text" => "返回首页");
        }
        $time = intval($time);
        if ($time < 2) {
            $time = 2;
        }
        $this->assign("time", $time);
        $this->assign("str_url_two", $str_url_two);
        $this->assign("defurl", $defurl);
        $this->assign("string", $string);
        $this->display("public/system.message");
        exit;
    }

    function autoNote($string = null, $defurl = null, $time = 2, $config = null) {

        if (ismobile()) {
            $defurl = str_replace("/index/", "/mobile/", $defurl);
            self::notemobile($string, $defurl, $time, $config);
        } else {
            $defurl = str_replace("/mobile/", "/index/", $defurl);
            self::note($string, $defurl, $time, $config);
        }
    }

    /*
     * 	取得用户的收货地址
     * 	@uid  用户ID
     * 	@key  返回类型, bool 真假值,array 返回地址数组
     */

    function member_huode_dizhi($uid = '', $key = 'bool') {
        $uid = abs(intval($uid));
        if (!$uid)
            return false;
        $info = D("yonghu_dizhi")->where(array("uid" => $uid, "default" => "Y"))->find();
        if ($info) {
            return $info;
        } else {
            return false;
        }
    }

    //判断商品是否揭晓
    function huode_shop_if_jiexiao($shopid = null) {
        $db = D("shangpin");
        $record = $db->where(array("id" => "$shopid"))->find();
        if (!$record)
            return false;
        if ($record['q_user']) {
            $record['q_user'] = unserialize($record['q_user']);
            return $record;
        } else {
            return $record;
        }
    }

    function yunjl($id, $n = 1) {
        $shop = D("shangpin")->where(array("id" => $id))->find();
        return $shop['thumb'];
    }

    function yunma1($ma, $html = "span") {
        $list = explode(",", $ma);
        $st = "";
        $i = 1;
        foreach ($list as $list2) {
            if ($i < 28) {
                $st.="<" . $html . ">" . $list2 . "</" . $html . ">";
                $i++;
            }
        }
        return $st;
    }

    function yunmashouji($ma, $html = "span") {
        $list = explode(",", $ma);
        $st = "";
        $count = 0;
        $row = "";
        foreach ($list as $list2) {
            $count++;
            $row.="&nbsp&nbsp&nbsp&nbsp" . $list2 . "&nbsp&nbsp&nbsp&nbsp";
            if ($count == 3) {
                $st.="<" . $html . ">" . $row . "</" . $html . ">";
                $count = 0;
                $row = "";
            }
        }
        if ($count < 3) {
            $st.="<" . $html . ">" . $row . "</" . $html . ">";
        }

        return $st;
    }

    function strcut($string, $changdugth, $dot = '...') {
        $string = trim($string);
        if ($changdugth && strlen($string) > $changdugth) {
            //截断字符   
            $wordscut = '';
            if (strtolower(C("charset")) == 'utf-8') {
                //utf8编码   
                $n = 0;
                $tn = 0;
                $noc = 0;
                while ($n < strlen($string)) {
                    $t = ord($string[$n]);
                    if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                        $tn = 1;
                        $n++;
                        $noc++;
                    } elseif (194 <= $t && $t <= 223) {
                        $tn = 2;
                        $n += 2;
                        $noc += 2;
                    } elseif (224 <= $t && $t < 239) {
                        $tn = 3;
                        $n += 3;
                        $noc += 2;
                    } elseif (240 <= $t && $t <= 247) {
                        $tn = 4;
                        $n += 4;
                        $noc += 2;
                    } elseif (248 <= $t && $t <= 251) {
                        $tn = 5;
                        $n += 5;
                        $noc += 2;
                    } elseif ($t == 252 || $t == 253) {
                        $tn = 6;
                        $n += 6;
                        $noc += 2;
                    } else {
                        $n++;
                    }
                    if ($noc >= $changdugth) {
                        break;
                    }
                }
                if ($noc > $changdugth) {
                    $n -= $tn;
                }
                $wordscut = substr($string, 0, $n);
            } else {
                for ($i = 0; $i < $changdugth - 1; $i++) {
                    if (ord($string[$i]) > 127) {
                        $wordscut .= $string[$i] . $string[$i + 1];
                        $i++;
                    } else {
                        $wordscut .= $string[$i];
                    }
                }
            }
            $string = $wordscut . $dot;
        }
        return trim($string);
    }

    function userid($uid, $zhi) {

        $huiyuan = D("yonghu")->where(array("uid" => "$uid"))->find();

        if ($zhi == 'username') {
            if ($huiyuan['username'] != null) {
                return $this->strcut($huiyuan['username'], 8, "");
            } else if ($huiyuan['mobile'] != null) {
                return $this->strcut($huiyuan['mobile'], 7, "");
            } else {
                return $this->strcut($huiyuan['email'], 7, "");
            }
        } else {
            return $huiyuan[$zhi];
        }
    }

    /**
     * 	获取登陆用户UID	
     * 	
     */
    function huode_user_uid($type = 'bool') {
//        global $_yys;
//        if (isset($_yys['userinfo']) && is_array($_yys['userinfo'])) {
//            return $_yys['userinfo']['uid'];
//        } else {
//            return false;
//        }
        $user = $this->getUserInfo();
        if ($user) {
            return $user['uid'];
        } else {
            return false;
        }
    }

    /* 	获取系统信息  */

    function GetSysInfo() {
        $sys_info['os'] = PHP_OS;
        $sys_info['zlib'] = function_exists('gzclose'); //zlib
        $sys_info['safe_mode'] = (boolean) ini_get('safe_mode'); //safe_mode = Off
        $sys_info['safe_mode_gid'] = (boolean) ini_get('safe_mode_gid'); //safe_mode_gid = Off
        $sys_info['timezone'] = function_exists("date_default_timezone_get") ? date_default_timezone_get() : "no_timezone";
        $sys_info['socket'] = function_exists('fsockopen');

        $web = explode(' ', $_SERVER['SERVER_SOFTWARE']);
        $sys_info['web_server'] = $web[0];
        $sys_info['phpv'] = phpversion();
        $sys_info['fileupload'] = @ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'unknown';
        $sys_info['set_time_limit'] = function_exists("set_time_limit") ? true : false;
        $sys_info['fsockopen'] = function_exists("fsockopen") ? true : false;

        return $sys_info;
    }

    /* 函数支持 */

    function showResult($str = '') {
        if (function_exists($str)) {
            return '<font color="#1194be">支持</font>';
        } else {
            return '<font color="#f17564">不支持</font>';
        }
    }

    function get_LOCAL_url() {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? $this->safe_replace($_SERVER['PHP_SELF']) : $this->safe_replace($_SERVER['SCRIPT_NAME']);
        $path_info = isset($_SERVER['PATH_INFO']) ? $this->safe_replace($_SERVER['PATH_INFO']) : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? $this->safe_replace($_SERVER['REQUEST_URI']) : $php_self . (isset($_SERVER['QUERY_STRING']) ? '?' . $this->safe_replace($_SERVER['QUERY_STRING']) : $path_info);
        return $sys_protocal . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $relate_url;
    }

    function safe_replace($string) {
        $string = str_replace('%20', '', $string);
        $string = str_replace('%27', '', $string);
        $string = str_replace('%2527', '', $string);
        $string = str_replace('*', '', $string);
        $string = str_replace('"', '&quot;', $string);
        $string = str_replace("'", '', $string);
        $string = str_replace('"', '', $string);
        $string = str_replace(';', '', $string);
        $string = str_replace('<', '&lt;', $string);
        $string = str_replace('>', '&gt;', $string);
        $string = str_replace("{", '', $string);
        $string = str_replace('}', '', $string);
        $string = str_replace('\\', '', $string);
        return $string;
    }

    function encrypt($string, $operation = 'ENCODE', $key = '', $expiry = 0) {
        if ($operation == 'DECODE') {
            $string = str_replace('_', '/', $string);
        }
        $key_length = 4;
        if (C("YYS_BANBEN_NUMBER")) {
            $key = md5($key != '' ? $key : C("code"));
        } else {
            $key = md5($key != '' ? $key : dirname(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $_SERVER['SCRIPT_NAME']));
        }
        $fixedkey = md5($key);
        $egiskeys = md5(substr($fixedkey, 16, 16));
        $runtokey = $key_length ? ($operation == 'ENCODE' ? substr(md5(microtime(true)), -$key_length) : substr($string, 0, $key_length)) : '';
        $keys = md5(substr($runtokey, 0, 16) . substr($fixedkey, 0, 16) . substr($runtokey, 16) . substr($fixedkey, 16));
        $string = $operation == 'ENCODE' ? sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $egiskeys), 0, 16) . $string : base64_decode(substr($string, $key_length));

        $i = 0;
        $result = '';
        $string_length = strlen($string);
        for ($i = 0; $i < $string_length; $i++) {
            $result .= chr(ord($string{$i}) ^ ord($keys{$i % 32}));
        }
        if ($operation == 'ENCODE') {
            $retstrs = str_replace('=', '', base64_encode($result));
            $retstrs = str_replace('/', '_', $retstrs);
            return $runtokey . $retstrs;
        } else {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $egiskeys), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        }
    }

    function huode_ip() {
        if (isset($_SERVER['HTTP_CLIENT_IP']) && strcasecmp($_SERVER['HTTP_CLIENT_IP'], "unknown"))
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], "unknown"))
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
            $ip = $_SERVER['REMOTE_ADDR'];
        else if (isset($_SERVER['REMOTE_ADDR']) && isset($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
            $ip = $_SERVER['REMOTE_ADDR'];
        else
            $ip = "";

        return ($ip);
    }

    /* 百度编辑器过滤 */

    function editor_safe_replace($content) {
        $tags = array(
            "'<iframe[^>]*?>.*?</iframe>'is",
            "'<frame[^>]*?>.*?</frame>'is",
            "'<script[^>]*?>.*?</script>'is",
            "'<head[^>]*?>.*?</head>'is",
            "'<title[^>]*?>.*?</title>'is",
            "'<meta[^>]*?>'is",
            "'<link[^>]*?>'is",
        );
        return preg_replace($tags, "", $content);
    }

    function is_utf8($string) {
        return preg_match('%^(?:
					[\x09\x0A\x0D\x20-\x7E] # ASCII
					| [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
					| \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
					| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
					| \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
					| \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
					| [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
					| \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
					)*$%xs', $string);
    }

    function buy_time($time = 0, $test = '') {
        if (empty($time)) {
            return $test;
        }
        $time = substr($time, 0, 10);
        $ttime = time() - $time;
        if ($ttime <= 0 || $ttime < 60) {
            return '几秒前';
        }
        if ($ttime > 60 && $ttime < 120) {
            return '1分钟前';
        }

        $i = floor($ttime / 60);       //分
        $h = floor($ttime / 60 / 60);      //时
        $d = floor($ttime / 86400);       //天
        $m = floor($ttime / 2592000);      //月
        $y = floor($ttime / 60 / 60 / 24 / 365);   //年
        if ($i < 30) {
            return $i . '分钟前';
        }
        if ($i > 30 && $i < 60) {
            return '一小时内';
        }
        if ($h >= 1 && $h < 24) {
            return $h . '小时前';
        }
        if ($d >= 1 && $d < 30) {
            return $d . '天前';
        }
        if ($m >= 1 && $m < 12) {
            return $m . '个月前';
        }
        if ($y) {
            return $y . '年前';
        }
        return "";
    }

    /* 此方法为公共方法用来删除某个文件夹下的所有文件
     * $path为文件的路径
     * $fileName文件夹名称
     * */

    public function rmFile($path, $fileName) {
        //去除空格
        $path = preg_replace('/(\/){2,}|{\\\}{1,}/', '/', $path);
        //得到完整目录    
        $path.= $fileName . "/Duipi";
        //判断此文件是否为一个文件目录
        if (is_dir($path)) {
            $file = scandir($path);
            foreach ($file as $key => $value) {
                unlink($path . '/' . $value);
            }
        }
    }

    //HTTP请求（支持HTTP/HTTPS，支持GET/POST）
    public function http_request($url, $data = null) {
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

    function sendemail($youjian, $weername = null, $biaoti = '', $content = '', $yes = '', $no = '') {
        if (!$weername)
            $weername = "";
        if (!$yes)
            $yes = "发送成功,如果没有收到，请到垃圾箱查看,\n请把" . C('fromName') . "设置为信任,方便以后接收邮件";
        if (!$no)
            $no = "发送失败，请重新点击发送";
        if (!$this->checkemail($youjian)) {
            return false;
        }
        import('ORG.Util.Mail');
//        if (is_array($youjian)) {
//            email::adduser($youjian);
//        } else {
//            email::adduser($youjian, $weername);
//        }
//        $if = SendMail('673902663@qq.com', 'tasswra', "tasswra",'tasswra');
        $if = SendMail($youjian, $biaoti, $content);
        if ($if) {
            return $yes;
        } else {
            return $no;
        }
    }

    function sendmobile($mobiles = '', $content = '') {
        $mobiles = str_replace("，", ',', $mobiles);
        $mobiles = str_replace(" ", '', $mobiles);
        $mobiles = trim($mobiles, " ");
        $mobiles = trim($mobiles, ",");
        $sends = new \Claduipi\Tools\sendmobile;
        $config = array();
        $config['mobile'] = $mobiles;
        $config['content'] = $content;
        $config['ext'] = '';
        $config['stime'] = '';
        $config['rrid'] = '';
        $cok = $sends->init($config);
        if (!$cok) {
            return array('-1', '配置不正确!');
        }
        $sends->send();
        $sendarr = array($sends->error, $sends->v);
        return $sendarr;
    }

    /*
      获取用户单个商品的总一元云购次数
     */

    function huode_user_goods_num($uid = null, $sid = null) {
        if (empty($uid) || empty($sid)) {
            return false;
        }
        $list = D("yonghu_yys_record")->where("uid = '$uid' and shopid = '$sid' and status LIKE '%已付款%'")->select();
        $num = 0;
        foreach ($list as $v) {
            $num+=$v['gonumber'];
        }
        return $num;
    }

    function autoShow($templateFile = '', $charset = '', $contentType = '', $content = '', $prefix = '') {
        if (ismobile()) {
            $this->display("mobile/" . $templateFile, $charset, $contentType, $content, $prefix);
        } else {
            $this->display("index/" . $templateFile, $charset, $contentType, $content, $prefix);
        }
    }

    function checkuser($uid, $wehell) {
        $uid = intval($this->encrypt($uid, 'DECODE'));
        $wehell = $this->encrypt($wehell, 'DECODE');
        if (!$uid)
            return false;
        if ($wehell === NULL)
            return false;
        $this->userinfo = D("yonghu")->where(array("uid" => $uid))->find();
        if (!$this->userinfo) {
            $this->userinfo = false;
            return false;
        }
        $shell = md5($this->userinfo['uid'] . $this->userinfo['password'] . $this->userinfo['mobile'] . $this->userinfo['email']);
        if ($wehell != $shell) {
            $this->userinfo = false;
            return false;
        } else {
            return true;
        }
    }

    function help($cateid) {
        $bangzhu = D("wenzhang")->where(array('cateid' => $cateid))->select();
        $li = "";
        foreach ($bangzhu as $bangzhutu) {
            $li.='<li><a href="' . C('URL_DOMAIN') . 'index/show/d/' . $bangzhutu['id'] . '" class="cur' . $bangzhutu['id'] . '"><b></b>' . $bangzhutu['title'] . '</a></li>';
        }
        return $li;
    }

    function shoplisext($id, $zd) {

        $shop = D("shangpin")->where(array('id' => $id))->find();
        return $shop[$zd];
    }

    function yunma($ma) {
        $list = explode(",", $ma);
        $st = "";
        foreach ($list as $list2) {
            $st.="<span>" . $list2 . "</span>";
        }
        return $st;
    }

    function huodecode($n = 10) {
        $num = intval($n) ? intval($n) : 10;
        if ($num > 44)
            $codestr = base64_encode(md5(time()) . md5(time()));
        else
            $codestr = base64_encode(md5(time()));
        $temp = array();
        $temp['code'] = substr($codestr, 0, $num);
        $temp['time'] = time();
        return $temp;
    }

    function tubimg($src, $width, $height) {
        $url = __PUBLIC__ . "/" . $src;
        $size = getimagesize($url);
        $name = rand(10, 99) . substr(microtime(), 2, 6) . substr(time(), 4, 6);
        $filetype = explode("/", $src);
        $img = imagecreatefromjpeg($url);
        $dst = ImageCreateTrueColor($width, $height);
        imagecopyresampled($dst, $img, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
        imagejpeg($dst, __PUBLIC__ . $filetype[0] . "/" . $filetype[1] . "/" . $name . ".jpg");
        return $filetype[1] . "/" . $name . ".jpg";
    }

//获取图像

    function shopimg($shopid) {
        $bangzhu = D("shangpin")->field("thumb")->where(array('id' => $shopid))->find();
        return $bangzhu['thumb'];
    }

    //获取管理员权限
    public function getAdminRight($c, $a) {
        $user = $this->getAdminInfo(FALSE);
        if ($user['xianzhi']) {
            $url = $c . "/" . $a;
            if (is_numeric(stripos($user['qx'], $url))) {
                if ($user['xianzhi']) {

                    echo "<script>
				alert('您不是超级管理员，没有权限访问');
				exit;			
				</script>";

                    exit;
                }
            }
        }
    }

    //空方法
    function _empty() {
        header('HTTP/1.1 404 Not Found');
        header('status: 404 Not Found'); //404状态码  
        $this->display("public/404");
    }

    public function setConfig($config, $callback = true) {
        $user = $this->getAdminInfo(FALSE);
        if ($user['xianzhi']) {

            echo "<script>
				alert('权限不足,请联系总管理员');
				exit;			
				</script>";

            exit;
        }

        $html = "<?php return " . var_export($config, true) . "; ?>";
        if (!is_writable(APP_PATH . "Duipi/Conf/config.php")) {
            if ($callback) {
                $this->note('没有写入权限!');
            } else {
                return FALSE;
            }
        }
        file_put_contents("Duipi/Conf/config.php", $html);
        if ($callback) {
            $this->note('配置更新成功!');
        } else {
            return TRUE;
        }
    }

    //**********************************************************************SHOP*************************************************************//
    /**
     * 出错页面
     */
    public function errmsg($string = null, $defurl = null, $type = 0, $time = 2) {
        if (empty($defurl)) {
            $defurl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
            if (empty($defurl))
                $defurl = dirname(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $_SERVER['SCRIPT_NAME']);
        }
        $time = intval($time);
        if ($time < 2) {
            $time = 2;
        }
        $this->assign("time", $time);
        $this->assign("defurl", $defurl);
        $this->assign("string", $string);
        if (is_array($string)) {
            $this->display("public/smoblie.message1");
        } else {
            $this->display("public/smoblie.message");
        }
        exit;
    }

    /**
     * 成功页面
     */
    public function success($string = null, $defurl = null, $type = 0, $time = 2, $config = null) {
        if (empty($defurl)) {
            $defurl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
            if (empty($defurl))
                $defurl = dirname(isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $_SERVER['SCRIPT_NAME']);
        }
        $time = intval($time);
        if ($time < 2) {
            $time = 2;
        }
        $this->assign("time", $time);
        $this->assign("defurl", $defurl);
        $this->assign("string", $string);
        $this->display("public/smobile.tishi");
        exit;
    }

    /**
     * 获取购物车商品总数
     */
    public function getShopCartNumber($uid = 0) {
        if (!$uid) {
            $user = $this->getUserInfo();
            $uid = $user['uid'];
        }
        $userCart = D("s_cart")->where(array("uid" => $uid))->find();
        $cartShop = unserialize($userCart['content']);
        $cartSum = 0;
        foreach ($cartShop['goods'] as $key => $value) {
            $cartSum+=$value;
        }
        foreach ($cartShop['product'] as $key => $value) {
            $cartSum+=$value;
        }
        return $cartSum;
    }

    /**
     * 获取省信息
     */
    public function getProvince() {
        $db = new \Think\Model();
        $arr = $db->table("city")->where(array("pid" => "0001"))->select();
        return $arr;
    }

    /**
     * 计算当前购物车价格
     * $goodsId 排除的商品id
     * $type 排除类型
     */
    public function getCartMoney($uid = 0, $goodsId = 0, $type = null) {
        $sum = 0;
        $goodswhere = "";
        $productwhere = "";
        if (!$uid) {
            $user = $this->getUserInfo();
            $uid = $user['uid'];
        }
        if (!$cartShop) {
            $userCart = D("s_cart")->where(array("uid" =>  $uid))->find();
            $cartShop = unserialize($userCart['content']);
        }

        if ($type == "goods" && $goodsId) {
            $goodswhere = " and id<>$goodsId";
        } else if ($type == "product" && $goodsId) {
            $productwhere = " and id<>$goodsId";
        }
        $goodsids = implode(",", array_keys($cartShop['goods']));
        if ($goodsids) {
            $goods = D("s_goods")->where("id in ($goodsids) $goodswhere")->select();
            foreach ($goods as $value) {
                $sum+=$cartShop['goods'][$value['id']] * $value['money'];
            }
        }
        $goodsids = implode(",", array_keys($cartShop['product']));
        if ($goodsids) {
            $goods = D("s_spec_goods_price")->where("id in ($goodsids) $productwhere")->select();
            foreach ($goods as $value) {
                $sum+=$cartShop['product'][$value['id']] * $value['money'];
            }
        }
        return $sum;
    }

}
