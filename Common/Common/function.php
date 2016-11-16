<?php

function log_result($file, $word) {
    $fp = fopen($file, "a");
    flock($fp, LOCK_EX);
    fwrite($fp, "执行日期：" . strftime("%Y-%m-%d-%H：%M：%S", time()) . "\n" . $word . "\n\n");
    flock($fp, LOCK_UN);
    fclose($fp);
}

function img($img) {
    $img = explode(".", $img);
    return $img[1];
}

/**
 * 生成第三方平台验证码
 * @return type 验证码
 */
function getPlatformCode() {
    $number = "1234567890";
    $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $code = substr(str_shuffle($number), 0, 4) . "-" . substr(str_shuffle($str), 0, 4);
    return $code;
}

/**
 * 是否为手机端
 * @return boolean
 */
function ismobile() {
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset($_SERVER['HTTP_X_WAP_PROFILE']))
        return true;

    //此条摘自TPM智能切换模板引擎，适合TPM开发
    if (isset($_SERVER['HTTP_CLIENT']) && 'PhoneClient' == $_SERVER['HTTP_CLIENT'])
        return true;
    //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset($_SERVER['HTTP_VIA']))
    //找不到为flase,否则为true
        return stristr($_SERVER['HTTP_VIA'], 'wap') ? true : false;
    //判断手机发送的客户端标志,兼容性有待提高
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = array(
            'nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile'
        );
        //从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return true;
        }
    }
    //协议法，因为有可能不准确，放到最后判断
    if (isset($_SERVER['HTTP_ACCEPT'])) {
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return true;
        }
    }
    return false;
}

/**
 * 是否为一个合法的email 
 * @param sting $email 
 * @return boolean 
 */
function is_email($email) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 是否为一个合法的url 
 * @param string $url 
 * @return boolean 
 */
function is_url($url) {
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 是否为一个合法的ip地址 
 * @param string $ip 
 * @return boolean 
 */
function is_ip($ip) {
    if (ip2long($ip)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 验证字符串是否为数字,字母,中文和下划线构成 
 * @param string $username 
 * @return bool 
 */
function is_check_string($str) {
    if (preg_match('/^[\x{4e00}-\x{9fa5}\w_]+$/u', $str)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 是否是手机号码 
 * @param string $phone 手机号码 
 * @return boolean 
 */
function is_phone($phone) {
    if (strlen($phone) != 11 || !preg_match('/^1[3|4|5|8][0-9]\d{4,8}$/', $phone)) {
        return false;
    } else {
        return true;
    }
}

/**
 * 是否为合法的身份证(支持15位和18位) 
 * @param string $card 
 * @return boolean 
 */
function is_card($card) {
    if (preg_match('/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/', $card) || preg_match('/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{4}$/', $card))
        return true;
    else
        return false;
}

/**
 * 验证日期格式是否正确 
 * @param string $date 
 * @param string $format 
 * @return boolean 
 */
function is_date($date, $format = 'Y-m-d') {
    $t = date_parse_from_format($format, $date);
    if (empty($t['errors'])) {
        return true;
    } else {
        return false;
    }
}

/**
 * @cc 获取所有控制器名称
 * @param $module
 * @return array|null
 */
function getController($module) {
    if (empty($module))
        return null;
    $module_path = APP_PATH . '/' . $module . '/Controller/';  //控制器路径
    if (!is_dir($module_path))
        return null;
    $module_path .= '/*.class.php';
    $ary_files = glob($module_path);
    foreach ($ary_files as $file) {
        if (is_dir($file)) {
            continue;
        } else {
            $files[] = basename($file, C('DEFAULT_C_LAYER') . '.class.php');
        }
    }
    return $files;
}

/**
 * @cc 获取所有方法名称
 * @param $module
 * @param $controller
 * @return array|null
 */
function getAction($module, $controller) {
    if (empty($controller))
        return null;
    $content = file_get_contents(APP_PATH . '/' . $module . '/Controller/' . $controller . 'Controller.class.php');
    preg_match_all("/.*?public.*?function(.*?)\(.*?\)/i", $content, $matches);
    $functions = $matches[1];
    //排除部分方法
    $inherents_functions = array(); //array(<span style = "color: #6a8759;">'_before_index'<span style = "color: #cc7832;">, <span style = "color: #6a8759;">'_after_index'<span style = "color: #cc7832;">, </span></span></span></span>'_initialize', '__construct', 'getActionName', 'isAjax', 'display', 'show', 'fetch', 'buildHtml', 'assign', '__set', 'get', '__get', '__isset', '__call', 'error', 'success', 'ajaxReturn', 'redirect', '__destruct', '_empty');
    foreach ($functions as $func) {
        $func = trim($func);
        if (!in_array($func, $inherents_functions)) {
            if (strlen($func) > 0)
                $customer_functions[] = $func;
        }
    }
    return $customer_functions;
}

/**
 * @cc 获取函数的注释
 * @param $module Home
 * @param $controller Auth
 * @param $action index
 * @return string 注释
 *
 */
function get_cc_desc($module, $controller, $action) {
    $desc = $module . '\Controller\\' . $controller . 'Controller';
    $func = new \ReflectionMethod(new $desc(), $action);
    $tmp = $func->getDocComment();
    $flag = preg_match_all('/@cc(.*?)\n/', $tmp, $tmp);
    $tmp = trim($tmp[1][0]);
    $tmp = $tmp != '' ? $tmp : '无';
    return $tmp;
}

function mergerImg($imgs) {
    list($max_width, $max_height) = getimagesize($imgs['dst']);
    $dests = imagecreatetruecolor($max_width, $max_height);
    $dst_im = imagecreatefrompng($imgs['dst']);
    imagecopy($dests, $dst_im, 0, 0, 0, 0, $max_width, $max_height);
    imagedestroy($dst_im);
    $src_im = imagecreatefrompng($imgs['src']);
    $src_info = getimagesize($imgs['src']);
    imagecopy($dests, $src_im, 15, $max_height / 3, 0, 0, $src_info[0], $src_info[1]);
    imagedestroy($src_im);
    header("Content-type: image/jpeg");
    imagejpeg($dests);
}

function calc($m, $n, $x) {
    switch ($x) {
        case 'mod':
            if ($n != 0) {
                $t = bcmod($m, $n);
            } else {
                return $errors[0];
            }
            break;
    }
    $t = preg_replace("/\..*0+$/", '', $t);
    return $t;
}

/**
 * @param $arr
 * @param $key_name
 * @param $key_name2
 * @return array
 * 将数据库中查出的列表以指定的 id 作为数组的键名 数组指定列为元素 的一个数组
 */
function get_id_val($arr, $key_name, $key_name2) {
    $arr2 = array();
    foreach ($arr as $key => $val) {
        $arr2[$val[$key_name]] = $val[$key_name2];
    }
    return $arr2;
}

//递归求笛卡尔积函数
function combineDika($dikad, $dalen) {
    $data = $dikad;
    $cnt = $dalen;

    $result = array();
    foreach ($data[0] as $item) {
        $result[] = array($item);
    }
    for ($i = 1; $i < $cnt; $i++) {
        $result = combineArray($result, $data[$i]);
    }
    return $result;
}

//求两个数组的笛卡尔积
function combineArray($arr1, $arr2) {
    $result = array();
    foreach ($arr1 as $item1) {
        foreach ($arr2 as $item2) {
            $temp = $item1;
            $temp[] = $item2;
            $result[] = $temp;
        }
    }
    return $result;
}

function trimall($str) {
    $qian = array(" ", "　", "\t", "\n", "\r");
    $hou = array("", "", "", "", "");
    return str_replace($qian, $hou, $str);
}
