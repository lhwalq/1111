<?php

/**
 * 工具
 * addtime 2016/03/03
 */

namespace Duipi\Controller;

use Think\Controller;

class ToolsController extends BaseController {

    /**
     * 验证码
     */
    public function checkcode() {

        $style = I("style", "");
        $cun_type = I("type", "");

        if ($cun_type == 'cookie' || $cun_type == 'session') {
            $cun_type = I("type", "");
        } else {
            $cun_type = 'cookie';
        }
        $style = explode("_", $style);
        $width = isset($style[0]) ? intval($style[0]) : '';
        $height = isset($style[1]) ? intval($style[1]) : '';
        $color = isset($style[2]) ? $style[2] : '';
        $bgcolor = isset($style[3]) ? $style[3] : '';
        $changdught = isset($style[4]) ? intval($style[4]) : 6;
        $type = isset($style[5]) ? intval($style[5]) : 3;

        $checkcode = new \Claduipi\Tools\checkcodeimg;
        $checkcode->config($width, $height, $color, $bgcolor, $changdught, $type);

        if (isset($_GET['dian'])) {
            $checkcode->dian(50, $color);
        }

        if ($cun_type == 'cookie') {
            cookie("checkcode", md5($checkcode->code));
        }
        if ($cun_type == 'session') {
            session('checkcode', md5($checkcode->code));
        }

        $checkcode->image();
    }

    /*
     * 	@上传图片
     * 	@参数1 	$biaoti	标题
     * 	@参数2 	$type	上传类型
     * 	缩略图上传/image/image/1/500000/uploadify/picurl/undefined
     */

    public function upload() {
        $biaoti = htmlspecialchars(I("title", ''));  //标题
        $type = htmlspecialchars(I("type", ''));  //上传类型
        $path = htmlspecialchars(I("dir", '')); //上传的文件夹
        $num = htmlspecialchars(I("num", 0));     //上传个数
        $size = htmlspecialchars(I("size", 0));      //最大size大小
        $frame = htmlspecialchars(I("frame", ''));  //iframe的ID
        $input = htmlspecialchars(I("input", '')); //父框架保存图片地址的input的id
        $desc = $type;              //类型描述
        $biaoti = urldecode($biaoti);
        if (!$this->is_utf8($biaoti)) {
            $biaoti = iconv("GBK", "UTF-8", $biaoti);
        }
        $size_str = $this->getsize($size, false);
        $uptype = $this->getUPtype($type, false);
        $check = cookie("AID") . '&' . cookie("ASHELL");

        $this->assign("biaoti", $biaoti);
        $this->assign("type", $type);
        $this->assign("path", $path);
        $this->assign("num", $num);
        $this->assign("size", $size);
        $this->assign("frame", $frame);
        $this->assign("input", $input);
        $this->assign("desc", $desc);
        $this->assign("size_str", $size_str);
        $this->assign("uptype", $uptype);
        $this->assign("check", $check);
        $this->display("uploadify");
    }

    public function insert() {
        $msg = array();
        $path = isset($_POST['path']) ? $this->encrypt($_POST['path'], 'DECODE') : '';
        $size = isset($_POST['size']) ? $this->encrypt($_POST['size'], 'DECODE') : 0;
        $type = isset($_POST['type']) ? $this->encrypt($_POST['type'], 'DECODE') : 'image';
        $type = explode(',', $this->getUPtype($type, true));
        if (!is_dir(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . $path)) {
            $msg['ok'] = 'no';
            $msg['text'] = $path . "文件夹不存在";
            echo json_encode($msg);
            exit;
        }
        if (is_array($_FILES['Filedata'])) {
            $upload = new \Claduipi\Tools\upload;
            $upload->upload_config($type, $size, $path);
            $upload->go_upload($_FILES['Filedata']);
            if (!$upload->ok) {
                $msg['ok'] = 'no';
                $msg['text'] = $upload->error;
            } else {
                $msg['ok'] = 'yes';
                $msg['text'] = $path . '/' . $upload->filedir . "/" . $upload->filename;
            }
            print_r(json_encode($msg));
        }
    }

    /*
      删除上传的图片
     */

    public function delupload() {
        $action = isset($_GET['action']) ? $_GET['action'] : null;
        $filename = isset($_GET['filename']) ? $_GET['filename'] : null;
        $filename = str_replace('../', '', $filename);
        $filename = trim($filename, '.');
        $filename = trim($filename, '/');
        if ($action == 'del' && !empty($filename)) {
            $filename = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . $filename;
            $size = getimagesize($filename);
            $filetype = explode('/', $size['mime']);
            if ($filetype[0] != 'image') {
                return false;
                exit;
            }
            unlink($filename);
            exit;
        }
    }

    /*
      获取上传类型
      @type 类型	imgage ,soft , media
      @arr  是否返回数组
     */

    private function getUPtype($type, $arr = false) {
        $typearr = array('up_image_type', 'up_soft_type', 'up_media_type');
        if ($type == 'image')
            $uptype = C("up_image_type");
        if ($type == 'soft')
            $uptype = C("up_soft_type");
        if ($type == 'media')
            $uptype = C("up_media_type");
        if (!$uptype)
            $uptype = C("up_image_type");
        if ($arr) {
            return $uptype;
        }

        $uptype = explode(',', $uptype);
        $html = '';
        foreach ($uptype as $v) {
            $html.="*." . $v . ';';
        }
        return $html;
    }

    /*
      @计算上传大小
      @size 数据大小
      @xi	  是否返回详细
     */

    private function getsize($size = 0, $xi = false) {
        $maxsize = C("upsize");
        if ($size > $maxsize || $size < 1)
            $size = $maxsize;
        $units = array(3 => 'G', 2 => 'M', 1 => 'KB', 0 => 'B'); //单位字符,可类推添加更多字符.
        $str = '';
        foreach ($units as $i => $unit) {
            if ($i > 0) {
                $n = $size / pow(1024, $i) % pow(1024, $i);
            } else {
                $n = $size;
            }
            if ($n != 0) {
                $str.=" $n{$unit} ";
                if (!$xi)
                    return $str;
            }
        }
        return $str;
    }

    public function cache() {
        if (isset($_POST['dosubmit'])) {
            $c_ok = '';
            $arr = array();
            if (isset($_POST['cache']['Cache'])) {
                array_push($arr, "Cache");
            }
            if (isset($_POST['cache']['Data'])) {
                array_push($arr, "Data");
            }
            if (isset($_POST['cache']['Temp'])) {
                array_push($arr, "Temp");
            }
            if (isset($_POST['cache']['Logs'])) {
                array_push($arr, "Logs");
            }
//得到切割的条数，便于下面循环
            $count = count($arr);
//循环调用上面的方法
            for ($i = 0; $i < $count; $i++) {
//调用删除文件夹下所有文件的方法
                $this->rmFile(RUNTIME_PATH, $arr[$i]);
            }
            $this->note("清理成功");
        }
        $this->display("admin/cache");
    }

//二维码带头像

    public function erweimatou() {
        $db = new \Think\Model();
        $huiyuan = $this->getUserInfo();
        $lianjie = $db->table("yys_yongjin")->order("id desc")->limit(1)->select();
        if (!$huiyuan && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            header("location: " . C("URL_DOMAIN") . "user/wxloginer/");
            exit;
        }
        //新增商城二维码
        $urls = I("url");


        foreach ($lianjie as $key => $val) {
            $yys['urls'][$key]['object_type'] = "";
            $yys['urls'][$key]['result'] = 'true';
            $yys['urls'][$key]['title'] = $val[title];
            $val['link'] = $urls ? C("URL_DOMAIN") . $urls : $val['link'];
            if ($huiyuan['yaoqing'] && empty($huiyuan['yaoqing2']) && empty($huiyuan['yaoqing3'])) {
                $yys['urls'][$key]['url_short'] = $val['link'] . '?yaoqing2=' . $huiyuan['yaoqing'] . '&yaoqing=' . $huiyuan['uid'];
            } else if ($huiyuan['yaoqing'] && $huiyuan['yaoqing2'] && empty($huiyuan['yaoqing3'])) {
                $yys['urls'][$key]['url_short'] = $val['link'] . '?yaoqing=' . $huiyuan['uid'] . '&yaoqing2=' . $huiyuan['yaoqing'] . '&yaoqing3=' . $huiyuan['yaoqing2'];
            } else if ($huiyuan['yaoqing'] && $huiyuan['yaoqing2'] && $huiyuan['yaoqing3']) {
                $yys['urls'][$key]['url_short'] = $val['link'] . '?yaoqing=' . $huiyuan['uid'] . '&yaoqing2=' . $huiyuan['yaoqing'] . '&yaoqing3=' . $huiyuan['yaoqing2'];
            } else {
                $yys['urls'][$key]['url_short'] = $val['link'] . '?yaoqing=' . $huiyuan['uid'];
            }
            $yys['urls'][$key]['object_id'] = "";
            $yys['urls'][$key]['url_long'] = $val['link'];
            $yys['urls'][$key]['type'] = "0";
        }
        $url = str_replace('index.php/Duipi/', '', C("URL_DOMAIN"));
        vendor("phpqrcode.phpqrcode");
        $value = $yys['urls'][0]['url_short']; //二维码内容 
        $errorCorrectionLevel = 'L'; //容错级别 
        $matrixPointSize = 6; //生成图片大小 
//生成二维码图片 
        \QRcode::png($value, 'qrcode.png', $errorCorrectionLevel, $matrixPointSize, 2);
        $logo = __PUBLIC__ . $huiyuan['img']; //准备好的logo图片 
        $QR = 'qrcode.png'; //已经生成的原始二维码图 

        if ($logo !== FALSE) {
            $QR = imagecreatefromstring(file_get_contents($QR));
            $logo = imagecreatefromstring(file_get_contents($logo));
            $QR_width = imagesx($QR); //二维码图片宽度 
            $QR_height = imagesy($QR); //二维码图片高度 
            $logo_width = imagesx($logo); //logo图片宽度 
            $logo_height = imagesy($logo); //logo图片高度 
            $logo_qr_width = $QR_width / 5;
            $scale = $logo_width / $logo_qr_width;
            $logo_qr_height = $logo_height / $scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
//重新组合图片并调整大小 
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
        }
//输出图片 
        Header("Content-type: image/png");
        ImagePng($QR);
    }

    public function erweima() {
        $url = I("code", 0);
        vendor("phpqrcode.phpqrcode");
        $level = 'M';
//        // 点的大小：1到10,用于手机端4就可以了
        $size = 6;
        $ss = \QRcode::png($url, false, $level, $size);
        echo $ss;
    }

//发送验证码
    public function send_mobile_reg_code($mobile = null, $uid = null) {
        if (!$uid)
            $this->note("发送用户手机认证码,用户ID不能为空！");
        if (!$mobile)
            $this->note("发送用户手机认证码,手机号码不能为空!");

        $checkcodes = rand(100000, 999999) . '|' . time(); //验证码
        D("yonghu")->where(array("uid" => $uid))->save(array("mobilecode" => $checkcodes));
        $checkcodes = explode("|", $checkcodes);

        $moban = D("linshi")->where(array("key" => 'template_mobile_reg'))->find();
        if (!$moban) {
            $content = "你在" . C("web_name") . "的短信验证码是:" . strtolower($checkcodes[0]);
        }
        if (empty($moban['value'])) {
            $content = "你在" . C("web_name") . "的短信验证码是:" . strtolower($checkcodes[0]);
        } else {
            if (strpos($moban['value'], "000000") == true) {
                $content = str_ireplace("000000", strtolower($checkcodes[0]), $moban['value']);
            } else {
                $content = $moban['value'] . strtolower($checkcodes[0]);
            }
        }
        return $this->sendmobile($mobile, $content);
    }

    public function send_email_reg($youjian = null, $uid = null) {
        $checkcode = $this->huodecode(10);
        $checkcode_sql = $checkcode['code'] . '|' . $checkcode['time'];
        $check_code = serialize(array("email" => $youjian, "code" => $checkcode['code'], "time" => $checkcode['time']));
        $check_code_url = $this->encrypt($check_code, "ENCODE", '', 3600 * 24);

        $clickurl = C("URL_DOMAIN") . 'user/emailok/code/' . $check_code_url;
        D("yonghu")->where(array("uid" => $uid))->save(array("emailcode" => $checkcode_sql));


        $web_name = C("web_name");
        $biaoti = C("web_name") . "激活注册邮箱";
        $moban = D("linshi")->where(array("key" => 'template_email_reg'))->find();
        $url = '<a href="';
        $url.= $clickurl . '">';
        $url.= $clickurl . "</a>";
        $moban['value'] = str_ireplace("{地址}", $url, $moban['value']);
        return $this->sendemail($youjian, '', $biaoti, $moban['value']);
    }

    public function upimage() {
//上传图片框中的描述表单名称，
        if (!isset($_POST['pictitle']) && !isset($_FILES['upfile'])) {
            exit;
        }
        $biaoti = $_POST['pictitle'];
        $upload = new \Claduipi\Tools\upload;
        $upload->upload_config(array('png', 'jpg', 'jpeg', 'gif'), 500000, 'shopimg');
        $upload->go_upload($_FILES['upfile']);

        if (!$upload->ok) {
            $url = '';
            $biaoti = $biaoti;
            $originalName = '';
            $state = $upload->error;
        } else {
            $url = C("URL_DOMAIN") . 'public/uploads/shopimg/' . $upload->filedir . "/" . $upload->filename;
            $biaoti = $biaoti;
            $originalName = '';
            $state = 'SUCCESS';
        }
        echo "{'url':'" . $url . "','title':'" . $biaoti . "','original':'" . $originalName . "','state':'" . $state . "'}";
//{'url':'upload/20130728/13749880933714.jpg','title':'梨花.jpg','original':'梨花.jpg','state':'SUCCESS'}
    }

    function downfile() {
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
        $filename = C("DB_BACKUP") . I("id");
        $Http = new \Org\Net\Http();
        $Http::download($filename, $filename);
    }

    function dbdelete() {
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
        $name = I("id");
        if (!$name) {
            $this->note("参数错误");
        }
        $filename = "backup/database/" . I("id");
        unlink($filename);
        $this->note("删除成功");
    }

}
