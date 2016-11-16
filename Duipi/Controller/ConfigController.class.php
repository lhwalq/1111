<?php

/**
 * 系统配置
 * addtime 2016/03/22
 */

namespace Duipi\Controller;

use Think\Controller;

class ConfigController extends BaseController {

    public function _initialize() {
        $this->getAdminRight(CONTROLLER_NAME, ACTION_NAME);
        $ment = array(
            array("webcfg", "SEO设置", C("URL_DOMAIN") . "config/webcfg"),
            array("config", "基本配置", C("URL_DOMAIN") . "config/config"),
            array("upload", "上传配置", C("URL_DOMAIN") . "config/upload"),
            array("watermark", "水印配置", C("URL_DOMAIN") . "config/watermark"),
            array("email", "邮箱配置", C("URL_DOMAIN") . "config/email"),
            array("mobile", "短信配置", C("URL_DOMAIN") . "config/mobile"),
            array("payset", "支付方式", C("URL_DOMAIN") . "config/pay_list"),
            array("yuming", "模块域名绑定", C("URL_DOMAIN") . "config/yuming"),
            array("send", "中奖通知设置", C("URL_DOMAIN") . "config/sendconfig")
        );
        $this->assign("ment", $ment);
    }

    //会员配置
    public function userconfig() {
        $db_linshi = D("linshi");
        $nickname = $db_linshi->where(array("key" => "member_name_key"))->find();
        if (isset($_POST['submit'])) {
            $nicknames = htmlspecialchars($_POST['nickname']);
            $nicknames = trim($nicknames, ",");
            $nicknames = str_ireplace(" ", '', $nicknames);
            $config = require(APP_PATH . "Duipi/Conf/config.php");
            $config["reg_email"] = isset($_POST['reg_email']) ? 1 : 0;
            $config["reg_mobile"] = isset($_POST['reg_mobile']) ? 1 : 0;
            $config["reg_num"] = isset($_POST['reg_num']) ? $_POST['reg_num'] : 0;
            $this->setConfig($config);

            if ($nickname) {
                $db_linshi->where(array("key" => "member_name_key"))->save(array("value" => "$nicknames"));
            } else {
                $db_linshi->add(array("key" => "member_name_key", "value" => "$nicknames"));
            }
            $this->note("操作成功");
        }

        $nickname = $nickname['value'];
        $this->assign("ment", R('member/returnMent', array()));
        $this->assign("nickname", $nickname);
        $this->display("admin/member_config");
    }

    //福分配置
    public function member_fufen() {
        if (isset($_POST['submit'])) {
            $path = APP_PATH . "Duipi/Conf/config.php";
            $config = require($path);
            if (!is_writable($path)) {
                $this->note('没有写入权限!');
            }
            $config["f_overziliao"] = intval(trim($_POST['f_overziliao']));
            $config["f_shoppay"] = intval(trim($_POST['f_shoppay']));
            $config["f_phonecode"] = intval(trim($_POST['f_phonecode']));
            $config["f_visituser"] = intval(trim($_POST['f_visituser']));
            //以上是福分，一下是经验值
            $config["z_overziliao"] = intval(trim($_POST['z_overziliao']));
            $config["z_shoppay"] = intval(trim($_POST['z_shoppay']));
            $config["z_phonecode"] = intval(trim($_POST['z_phonecode']));
            $config["z_visituser"] = intval(trim($_POST['z_visituser']));
            $config["fufen_yuan"] = intval(trim($_POST['fufen_yuan']));
            $config["fufen_yuansong"] = trim($_POST['fufen_yuansong']);
            $config["fufen_yuansongzg"] = trim($_POST['fufen_yuansongzg']);

            $config["fufen_yuansongzg"] = trim($_POST['fufen_yuansongzg']);
            $config["fufen_yongjin"] = floatval(trim($_POST['fufen_yongjin']));
            $config["fufen_yongjin2"] = floatval(trim($_POST['fufen_yongjin2']));
            $config["fufen_yongjin3"] = floatval(trim($_POST['fufen_yongjin3']));
            $config["fufen_yongjinqd"] = floatval(trim($_POST['fufen_yongjinqd']));
            $config["fufen_yongjinqd0"] = floatval(trim($_POST['fufen_yongjinqd0']));
            $config["fufen_yongjinqd1"] = floatval(trim($_POST['fufen_yongjinqd1']));
            $config["fufen_yongjinqd2"] = floatval(trim($_POST['fufen_yongjinqd2']));
            $config["fufen_yongjintx"] = floatval(trim($_POST['fufen_yongjintx']));
            $config["xiangou"] = floatval(trim($_POST['xiangou']));
            $config["dengluid"] = trim($_POST['dengluid']);
            $config["tongzhiid"] = trim($_POST['tongzhiid']);
            $config["yaoqingid"] = trim($_POST['yaoqingid']);
            $config["goumaiid"] = trim($_POST['goumaiid']);
            $config["shaim"] = trim($_POST['shaim']);

            $config["appid"] = trim($_POST['appid']);
            $config["secret"] = trim($_POST['secret']);
            $config["appid1"] = trim($_POST['appid1']);
            $config["secret1"] = trim($_POST['secret1']);
            $config["fanhui"] = trim($_POST['fanhui']);
            $config["zhuanpank"] = trim($_POST['zhuanpank']);
            $config["zhuanpan7"] = trim($_POST['zhuanpan7']);
            $config["zhuanpan6"] = trim($_POST['zhuanpan6']);
            $config["zhuanpan5"] = trim($_POST['zhuanpan5']);
            $config["zhuanpan4"] = trim($_POST['zhuanpan4']);
            $config["zhuanpan3"] = trim($_POST['zhuanpan3']);
            $config["zhuanpan2"] = trim($_POST['zhuanpan2']);
            $config["zhuanpan1"] = trim($_POST['zhuanpan1']);
            $config["zhuanpan7b"] = trim($_POST['zhuanpan7b']);
            $config["zhuanpan6b"] = trim($_POST['zhuanpan6b']);
            $config["zhuanpan5b"] = trim($_POST['zhuanpan5b']);
            $config["zhuanpan4b"] = trim($_POST['zhuanpan4b']);
            $config["zhuanpan3b"] = trim($_POST['zhuanpan3b']);
            $config["zhuanpan2b"] = trim($_POST['zhuanpan2b']);
            $config["zhuanpan1b"] = trim($_POST['zhuanpan1b']);
            if ($config["fufen_yuansongzg"] > 1) {
                $this->note('冲值送余额需要设置为1以下');
            }
            if ($config["fufen_yuan"] <= 0) {
                $this->note('福分输入有错误');
            }
            if ($config["fufen_yuansong"] < 1) {
                $this->note('冲值送余额需要设置为1以上');
            }
            if ($config["fufen_yuansongzg"] > 1) {
                $this->note('冲值送余额需要设置为1以下');
            }
            $jieguo = $config["fufen_yuan"] % 10;
            if ($jieguo != 0) {
                $this->note('福分输入有错误');
            }
            $this->setConfig($config);
        }
        $this->assign("ment", R('member/returnMent', array()));
        $this->display("admin/member_insertfufen");
    }

    public function qq_set_config() {
        if (isset($_POST['dosubmit'])) {
            $qq_off = intval($_POST['type']);
            $qq_id = $_POST['id'];
            $qq_key = $_POST['key'];
            $config = require(APP_PATH . "Duipi/Conf/config.php");
            $config['qq_config'] = array("off" => $qq_off, "id" => $qq_id, "key" => $qq_key);
            $this->setConfig($config);
        }
        $this->display("admin/qq_set_config");
    }

    /**
     * 	中奖通知设置
     */
    public function sendconfig() {
        if (isset($_POST['s_type'])) {
            $s_type = abs($_POST['s_type']);
            if (($s_type == C("send_type")) || $s_type > 3) {
                $this->note("更新完成!");
            }
            $config = require(APP_PATH . "Duipi/Conf/config.php");
            $config['send_type'] = $s_type;
            $this->setConfig($config);
            $this->note("更新完成!");
        }
        $this->display("admin/config.send");
    }

    public function yuming() {
        $yuming_yys = C("yuming");
        if (empty($yuming_yys)) {
            $yuming_yys = array();
        }
        if (isset($_POST['dosubmit']) && $_POST['dosubmit'] != 'del') {
            //插入或者修改
            $yuming = isset($_POST['yuming']) ? trim(htmlspecialchars($_POST['yuming'])) : null;
            $module = isset($_POST['module']) ? trim(htmlspecialchars($_POST['module'])) : null;
            $action = isset($_POST['action']) ? trim(htmlspecialchars($_POST['action'])) : null;
            $func = isset($_POST['func']) ? trim(htmlspecialchars($_POST['func'])) : null;
            if (!$yuming || !$module) {
                exit("请正确填写绑定参数!");
            }
            if ($_POST['dosubmit'] == 'install') {
                if (array_key_exists($yuming, $yuming_yys)) {
                    exit("绑定的域名已经被使用!"); //array_keys
                }
            }
            $yuming = str_ireplace("http://", '', trim($yuming, '/'));
            $yuming_yys[$yuming] = array("m" => $module, "c" => $action, "a" => $func);
            $config = require(APP_PATH . "Duipi/Conf/config.php");
            $config['yuming'] = $yuming_yys;
            $this->setConfig($config, FALSE);
            exit("ok");
        }
        if (isset($_POST['dosubmit']) && $_POST['dosubmit'] == 'del') {
            $yuming = isset($_POST['yuming']) ? trim(htmlspecialchars($_POST['yuming'])) : null;
            if (!$yuming) {
                exit("操作失败1!");
            }
            if (isset($yuming_yys[$yuming])) {
                unset($yuming_yys[$yuming]);
                $config = require(APP_PATH . "Duipi/Conf/config.php");
                $config['yuming'] = $yuming_yys;
                $this->setConfig($config, FALSE);
                exit("ok");
            } else {
                exit("操作失败2!");
            }
        }
        $this->assign("yuming_yys", $yuming_yys);
        $this->display("admin/config.yuming");
    }

    //基本设置
    public function config() {
        if (isset($_POST['dosubmit'])) {
            $configs['charset'] = htmlspecialchars($_POST['charset']);
            $configs['timezone'] = htmlspecialchars($_POST['timezone']);
            $configs['error'] = htmlspecialchars($_POST['error']);
            $configs['gzip'] = htmlspecialchars($_POST['gzip']);
            $configs['gonggonghao'] = htmlspecialchars($_POST['gonggonghao']);
            $configs['zhideng'] = htmlspecialchars($_POST['zhideng']);
            $configs['wxb'] = htmlspecialchars($_POST['wxb']);
//            $configs['ssc'] = htmlspecialchars($_POST['ssc']);
            $configs['index_weijingtai'] = htmlspecialchars($_POST['index_weijingtai']);
            $configs['expstr'] = htmlspecialchars($_POST['expstr']);
            $configs['admindir'] = htmlspecialchars($_POST['admindir']);
            $configs['website_off'] = htmlspecialchars($_POST['website_off']);
            $configs['website_off_text'] = $_POST['website_off_text'];
            $configs['qq'] = htmlspecialchars($_POST['qq']);
            $configs['qq_qun'] = htmlspecialchars($_POST['qq_qun']);
            $configs['cell'] = htmlspecialchars($_POST['cell']);
            $configs['goods_end_time'] = intval($_POST['goods_end_time']); //shangpinss_end_time
            if ($configs['goods_end_time'] < 30 && $configs['goods_end_time'] != 0) {
                $configs['goods_end_time'] = 180;
            }
            if ($configs['goods_end_time'] >= 300) {
                $configs['goods_end_time'] = 180;
            }
            $db_config = D("configs");
            foreach ($configs as $key => $value) {
                $db_config->where(array("name" => $key))->save(array("value" => $value));
            }
            $config = require(APP_PATH . "Duipi/Conf/config.php");
            $config = array_merge($config, $configs);
            $this->setConfig($config);
            $this->note("修改成功");
        }
        $this->display("admin/config.system");
    }

    public function webcfg() {
        if (isset($_POST['dosubmit'])) {
            $configs['web_name'] = htmlspecialchars($_POST['web_name']);
            $configs['web_name_two'] = htmlspecialchars($_POST['web_name_two']);
            $configs['LOCAL_PATH'] = htmlspecialchars($_POST['LOCAL_PATH']);
            $configs['web_key'] = htmlspecialchars($_POST['web_key']);
            $configs['web_des'] = htmlspecialchars($_POST['web_des']);
            $configs['web_logo'] = htmlspecialchars($_POST['web_logo']);
            $configs['web_logo1'] = htmlspecialchars($_POST['web_logo1']);
            $configs['web_logo2'] = htmlspecialchars($_POST['web_logo2']);
            $configs['web_copyright'] = $_POST['web_copyright'];

            $db_config = D("configs");
            foreach ($configs as $key => $value) {
                $db_config->where(array("name" => $key))->save(array("value" => $value));
            }
            $config = require(APP_PATH . "Duipi/Conf/config.php");
            $config = array_merge($config, $configs);
            $this->setConfig($config);
        }
        $this->display("admin/config.webcfg");
    }

    public function email() {
        $cesi = I("type", "");
        if ($cesi == 'cesi') {
            $youxiang = I("emails", "");
//            $youxiang = str_replace("|", ".", $youxiang);
//            dump($youxiang);exit;
            $ok = $this->sendemail($youxiang, '', '后台邮箱配置测试成功', '<b>恭喜你邮箱测试成功</b>', "1", "0");
            if ($ok == '1') {
                echo "邮件测试成功";
            } else {
                echo "邮件测试失败";
            }
            exit;
        }
        if (isset($_POST['dosubmit'])) {

            $config = require(APP_PATH . "Duipi/Conf/config.php");
            $config['stmp_host'] = htmlspecialchars($_POST['server']);
            $config['user'] = htmlspecialchars($_POST['user']);
            $config['pass'] = htmlspecialchars($_POST['pass']);
            $config['big'] = htmlspecialchars($_POST['big']);
            $config['from'] = htmlspecialchars($_POST['email']);
            $config['fromName'] = htmlspecialchars($_POST['name']);

            $this->setConfig($config);
            $this->note("操作成功");
        }

        $this->display("admin/config.email");
    }

    /* 短信配置与测试 */

    public function mobile() {

        $mobiles = C("mobile");
        $sendobj = new \Claduipi\Tools\sendmobile;

        /* 修改和启用短信接口 */
        if (isset($_POST['dosubmit'])) {
            $cfg_id = trim($_POST['mid']);
            $cfg_pass = trim($_POST['mpass']);
            $cfg_qianming = trim(isset($_POST['mqianming']) ? $_POST['mqianming'] : '');
            $cfg_type = abs(intval($_POST['interface']));

            if ($cfg_pass == '******') {
                $this->note("保存需要在输入一次短信密码!!!");
            }

            $config = require(APP_PATH . "Duipi/Conf/config.php");
            $key = "cfg_mobile_" . $cfg_type;
            $config['mobile']['cfg_mobile_on'] = $cfg_type;
            $config['mobile'][$key]['mid'] = $cfg_id;
            $config['mobile'][$key]['mpass'] = $cfg_pass;
            $config['mobile'][$key]['mqianming'] = $cfg_qianming;
            $this->setConfig($config);
            $this->note("短信配置更新成功!");
        }
        /* 短信测试 */
        if (isset($_POST['ceshi_submit'])) {
            $_POST['ceshi_haoma'] = trim($_POST['ceshi_haoma']);
            $_POST['ceshi_con'] = trim($_POST['ceshi_con']);

            if (empty($_POST['ceshi_con']) || empty($_POST['ceshi_haoma'])) {
                echo json_encode(array("-1", "内容或者手机号不能为空!"));
                return;
            }

            if (!is_numeric($_POST['ceshi_haoma'])) {
                echo json_encode(array("-1", "手机号不正确!"));
                return;
            }
            $sendok = $this->sendmobile($_POST['ceshi_haoma'], $_POST['ceshi_con']);
            echo json_encode($sendok);
            return;
        }
        /* 短信条数 */
        foreach ($mobiles as $k => $v) {
            if (is_array($v)) {
                $k_t = explode("_", $k);
                $k_t = array_pop($k_t);
                $k_t_fun = "cfg_getdata_" . $k_t;
                $sendobj->$k_t_fun();
                if ($sendobj->v) {
                    $mobiles[$k]['mobile_text'] = "<b style='color:#0c0'>短信功能正常</b>,短信还剩余 " . $sendobj->v . " 条";
                } else {
                    $mobiles[$k]['mobile_text'] = "<b style='color:#ff0000'>短信测试失败</b>,失败原因:" . $sendobj->error;
                }
            }
        }
        $mobiles['cfg_mobile_' . count($mobiles)] = array("mid" => "", "mpass" => "", "mobile_text" => "");
        $this->assign("mobiles", $mobiles);
        $this->display("admin/config.mobile");
    }

    public function mobiles() {
        $mobile = array('mid' => '', 'mpass' => '');
        $mobile = System::DOWN_sys_config("mobile");

        $cesi = $this->segment(4);

        if (isset($_POST['dosubmit_ceshi'])) {
            $sendobj = System::DOWN_sys_class("sendmobile");

            $_POST['ceshi_haoma'] = trim($_POST['ceshi_haoma']);
            $_POST['ceshi_con'] = trim($_POST['ceshi_con']);

            if (empty($_POST['ceshi_con']) || empty($_POST['ceshi_haoma'])) {
                echo json_encode(array("-1", "内容或者手机号不能为空!"));
                return;
            }
            $ret = $sendobj->mobile_con_check($_POST['ceshi_con']);

            //内容检测不合法返回
            if ($ret[0] == -1) {
                echo json_encode($ret);
                return;
            }
            if (!is_numeric($_POST['ceshi_haoma'])) {
                echo json_encode(array("-1", "手机号不正确!"));
                return;
            }
            $sendok = _sendmobile($_POST['ceshi_haoma'], $_POST['ceshi_con']);
            echo json_encode($sendok);
            return;
        }/* if end */

        if (isset($_POST['dosubmit'])) {

            $cfg_id = trim($_POST['mid']);
            $cfg_pass = trim($_POST['mpass']);
            $cfg_qianming = trim(isset($_POST['mqianming']) ? $_POST['mqianming'] : '');
            $cfg_type = abs(intval($_POST['interface']));


            if ($cfg_type == 1) {
                $mobile['cfg_mobile_1']['mid'] = $cfg_id;
                $mobile['cfg_mobile_1']['mpass'] = $cfg_pass;
                $mobile['cfg_mobile_1']['mqianming'] = $cfg_qianming;
                $mobile['cfg_mobile_2']['mid'] = $mobile['cfg_mobile_2']['mid'];
                $mobile['cfg_mobile_2']['mpass'] = $mobile['cfg_mobile_2']['mpass'];
                $mobile['cfg_mobile_2']['mqianming'] = $mobile['cfg_mobile_2']['mqianming'];
            }

            if ($cfg_type == 2) {
                $mobile['cfg_mobile_1']['mid'] = $mobile['cfg_mobile_1']['mid'];
                $mobile['cfg_mobile_1']['mpass'] = $mobile['cfg_mobile_1']['mpass'];
                $mobile['cfg_mobile_1']['mqianming'] = $mobile['cfg_mobile_1']['mqianming'];
                $mobile['cfg_mobile_2']['mid'] = $cfg_id;
                $mobile['cfg_mobile_2']['mpass'] = $cfg_pass;
                $mobile['cfg_mobile_2']['mqianming'] = $cfg_qianming;
            }

            $mobile['cfg_mobile_on'] = $cfg_type;

            if (!is_writable(YYS_CONFIGS . 'mobile.inc.php'))
                $this->note('Please chmod  mobile.ini.php  to 0777 !');

            $html = var_export($mobile, true);
            $html = "<?php \n return " . $html . "; \n?>";
            $ok = file_put_contents(YYS_CONFIGS . 'mobile.inc.php', $html);
            if ($ok) {
                $this->note("短信配置更新成功!");
            }
        }

        $sendmobile = System::DOWN_sys_class("sendmobile");

        $sendmobile->GetBalance();
        if ($sendmobile->error == 1) {
            $text2 = "<b style='color:#0c0'>短信功能正常</b>,短信还剩余 " . $sendmobile->v . " 条";
        } else {
            $text2 = "<b style='color:#ff0000'>短信测试失败</b>,失败原因:" . $sendmobile->v;
        }

        $new_mbl = $sendmobile->GetBalance_new();
        if ($new_mbl['id']) {
            $text1 = "<b style='color:#0c0'>短信功能正常</b>,短信还剩余 " . $new_mbl['id'] . " 条";
        } else {
            $text1 = "<b style='color:#ff0000'>短信测试失败</b>,失败原因:" . $new_mbl['err'];
        }


        if (!isset($mobile['cfg_mobile_2'])) {
            $mobile['cfg_mobile_1'] = $mobile['cfg_mobile_2'] = array();
            $mobile['cfg_mobile_2']['mid'] = $mobile['mid'];
            $mobile['cfg_mobile_2']['mpass'] = $mobile['mpass'];
            $mobile['cfg_mobile_2']['mqianming'] = $mobile['mqianming'];
            $mobile['cfg_mobile_1'] = array();
            $mobile['cfg_mobile_1']['mid'] = '';
            $mobile['cfg_mobile_1']['mpass'] = '';
            $mobile['cfg_mobile_1']['mqianming'] = '';
        }


        include $this->dwt(DOWN_M, 'config.mobile');
    }

    //上传配置
    public function upload() {
        if (isset($_POST['dosubmit'])) {
            $config = require(APP_PATH . "Duipi/Conf/config.php");
            $config['up_image_type'] = trim(htmlspecialchars($_POST['up_image_type']), ',');
            $config['up_soft_type'] = trim(htmlspecialchars($_POST['up_soft_type']), ',');
            $config['up_media_type'] = trim(htmlspecialchars($_POST['up_media_type']), ',');
            $config['upsize'] = intval($_POST['upsize']);
            $this->setConfig($config);
            $this->note("修改成功");
        }
        $this->display("admin/config.upload");
    }

    //水印配置
    public function watermark() {

        if (isset($_POST['dosubmit'])) {
            $watermark_off = $_POST['watermark_off'];
            $watermark_type = $_POST['watermark_type'];
            $text = htmlspecialchars($_POST['text']);
            $color = htmlspecialchars($_POST['color']);
            $size = intval($_POST['size']);
            $width = intval($_POST['width']);
            $height = intval($_POST['height']);
            $image = htmlspecialchars($_POST['image']);
            $apache = intval($_POST['apache']);
            $shangpins = intval($_POST['good']);
            $sel = htmlspecialchars($_POST['sel']);
            $config = require(APP_PATH . "Duipi/Conf/config.php");

            $config['watermark_off'] = $watermark_off;
            $config['watermark_condition'] = array('width' => $width, 'height' => "$height");
            $config['watermark_type'] = $watermark_type;
            $config['watermark_text'] = array('text' => "$text", 'color' => "$color", 'size' => "$size", 'font' => 'C:\WINDOWS\Fonts\simhei.ttf');
            $config['watermark_image'] = $image;
            $config['watermark_position'] = $sel;
            $config['watermark_apache'] = $apache;
            $config['watermark_good'] = $shangpins;
            $this->setConfig($config);
            $this->note("修改成功");
        }
        $this->display("admin/config.watermark");
    }

    //授权
    //验证码配置
    public function checkcode() {
        if (isset($_POST['type'])) {
            $info = array();
            $info['width'] = $_POST['width'];
            $info['height'] = $_POST['height'];
            $info['color'] = $_POST['color'];
            $info['bgcolor'] = $_POST['bgcolor'];
            $info['lenght'] = $_POST['lenght'];
            $info['type'] = $_POST['type'];

            $html_a = var_export($info, true);
            $html = "
				<?php 
					return {$html_a};
				?>
			";
            $path = YYS_CONFIGS . '/' . 'checkcode.inc.php';
            file_put_contents($path, $html);
        }
        include $this->dwt(DOWN_M, 'config.checkcode');
    }

    public function returnQQConfig() {
        $ment = array(
            array("navigation", "qq群列表", C("URL_DOMAIN") . 'config/qq_admin'),
            array("navigation", "qq群添加", C("URL_DOMAIN") . 'config/qq_edit'),
        );
        return $ment;
    }

    //QQ群设置
    public function qq_admin() {
        $lists = D("qqshezhi")->select();
        if (!empty($lists)) {
            foreach ($lists as $key => $val) {
                $lists[$key]['address'] = $val['province'] . '&nbsp;' . $val['city'] . '&nbsp;' . $val['county'];
            }
        }
        $this->assign("ment", $this->returnQQConfig());
        $this->assign("lists", $lists);
        $this->display("admin/qq_list");
    }

    public function qq_edit() {
        $id = I("id", 0);
        if ($id) {
            $recomone = D("qqshezhi")->where(array("id" => "$id"))->find();
            $this->assign("recomone", $recomone);
        }
        $this->display("admin/qq_update");
    }

    public function qq_delete() {
        $id = I("id", 0);
        $res = D("qqshezhi")->where(array("id" => "$id"))->delete();
        if ($res) {
            $this->note("删除成功", C("URL_DOMAIN") . "config/qq_admin");
        } else {
            $this->note("删除失败");
        }
    }

    public function qq_save() {
        $id = I("id", 0);
        $qq = htmlspecialchars(trim($_POST['qq']));
        $name = htmlspecialchars(trim($_POST['name']));
        $type = htmlspecialchars(trim($_POST['qqtype']));
        $qqurl = trim($_POST['qqurl']);
        $full = htmlspecialchars(trim($_POST['full']));
        $province = htmlspecialchars(trim($_POST['s_province']));
        $city = htmlspecialchars(trim($_POST['s_city']));
        $county = htmlspecialchars(trim($_POST['s_county']));
        $subtime = time();

        $data = array("qq" => "$qq", 'name' => "$name", 'type' => "$type", 'qqurl' => "$qqurl", 'full' => "$full", 'province' => "$province", 'city' => "$city", 'county' => "$county", 'subtime' => "$subtime");
        if ($id) {
            $res = D("qqshezhi")->where(array("id" => $id))->save($data);
            if ($res) {
                $this->note("修改成功", C("URL_DOMAIN") . "config/qq_admin");
            }
        } else {
            $res = D("qqshezhi")->add($data);
            if ($res) {
                $this->note("添加成功", C("URL_DOMAIN") . "config/qq_admin");
            }
        }
        $this->note("添加失败");
    }

    public function fundset() {
        $config = D("jijin")->find();
        if (isset($_POST['dosubmit'])) {
            $off = intval($_POST['fund_off']);
            $money = floatval(substr(sprintf("%.3f", $_POST['fund_money']), 0, -1));
            if (isset($_POST['fund_count_money'])) {
                $count_money = floatval(substr(sprintf("%.3f", $_POST['fund_count_money']), 0, -1));
            } else {
                $count_money = $config['fund_count_money'];
            }
            if ($money <= 0) {
                $this->note("基金出资金额不正确");
            }
            $data = array("fund_off" => "$off", "fund_money" => "$money", "fund_count_money" => "$count_money");
            D("jijin")->where(array("id" => 1))->save($data);
            $this->note("修改成功");
        }
        $this->assign("config", $config);
        $this->display("admin/fundset");
    }

    public function temp() {
        $temps = D("linshi")->where("`key` LIKE 'template%'")->select();
        $temp = $this->key2key($temps, "key");
        $this->assign("temp", $temp);
        if (isset($_POST['dosubmit'])) {
            $db = D("linshi");
            $q_1 = $db->where(array("key" => "template_email_reg"))->save(array("value" => I("e_reg_temp", "")));
            $q_2 = $db->where(array("key" => "template_email_shop"))->save(array("value" => I("e_shop_temp", "")));
            $q_3 = $db->where(array("key" => "template_email_pwd"))->save(array("value" => I("e_pwd_temp", "")));
            $q_4 = $db->where(array("key" => "template_mobile_shop"))->save(array("value" => I("m_shop_temp", "")));
            $q_5 = $db->where(array("key" => "template_mobile_reg"))->save(array("value" => I("m_reg_temp", "")));
            $q_6 = $db->where(array("key" => "template_mobile_pwd"))->save(array("value" => I("m_pwd_temp", "")));

            if ($q_1 || $q_2 || $q_3 || $q_4 || $q_5 || $q_6) {
                $this->note("模板更新成功！");
            } else {
                $this->note("模板更新失败！");
            }
        }
        $this->display('admin/template.temp');
    }

    //支付列表
    public function pay_list() {
        $paylist = D("payment")->select();
//        $path = YYS_MANAGE . "class" . DIRECTORY_SEPARATOR . "pay" . DIRECTORY_SEPARATOR . "control" . DIRECTORY_SEPARATOR;
//        $dir = opendir($path);
//        $new_pay = array();
//        while (($file = readdir($dir)) !== false) {
//            if ($file != '.' && $file != '..' && !is_dir($path . $file)) {
//                if (preg_match("/(.*)\.install.php/", $file, $row)) {
//                    $new_pay[] = $file;
//                }
//            }
//        }
//        closedir($dir);
        $this->assign("paylist", $paylist);
        $this->display("admin/paylist");
    }

    public function pay_bank() {
        if (isset($_POST['dosubmit'])) {
            $bank_type = htmlspecialchars($_POST['bank_type']);
            $q_ok = D("linshi")->where(array("key" => "pay_bank_type"))->save(array("value" => $bank_type));
            if ($q_ok) {
                $this->note("操作成功!");
            } else {
                $this->note("操作失败!");
            }
        }

        $bank = D("linshi")->where(array("key" => "pay_bank_type"))->find();
        if (!$bank)
            $this->note("查询失败");
        $this->assign("bank", $bank);
        $this->display("admin/paybank");
    }

    public function pay_set() {

        $payid = I("id", 0);

        $pay = D("payment")->where(array("pay_id" => "$payid"))->find();
        if (!$pay)
            $this->note("参数错误");
        //if ($pay['pay_class'] == 'yeepay') {
          //  if (!file_exists(THINK_PATH . 'Library/Cla/Payment/yeepay.class.php')) {
            //    $this->note("开通易宝支付请联系官网!<a href='http://www.yiyuansha.com/'>http://www.yiyuansha.com/</a>", 'http://www.yiyuansha.com/', 10);
          //  }
     //   }

        $pay['pay_key'] = unserialize($pay['pay_key']);

        if (!is_array($pay['pay_key'])) {
            $pay['pay_key'] = array("id" => array("name" => "商户号", "val" => ""), "key" => array("name" => "密匙", "val" => ""));
        }

        if (isset($_POST['dosubmit'])) {
            $user = $this->getAdminInfo(FALSE);
            if ($user['xianzhi']) {

                echo "<script>
				alert('权限不足,请联系总管理员');
				exit;			
				</script>";

                exit;
            }
            $name = htmlspecialchars($_POST['pay_name']);
            $thumb = htmlspecialchars($_POST['pay_thumb']);
            $type = intval($_POST['pay_type']);
            $des = htmlspecialchars($_POST['pay_des']);
            $start = intval($_POST['pay_start']);

            $pay_key = $_POST['pay_key'];
            foreach ($pay_key as $key => $val) {
                $pay_key[$key] = array("name" => $pay['pay_key'][$key]['name'], "val" => $pay_key[$key]);
            }
            $pay_key = serialize($pay_key);
            D("payment")->where(array("pay_id" => $payid))->save(array("pay_name" => "$name", "pay_thumb" => "$thumb", "pay_type" => "$type", "pay_des" => "$des", "pay_start" => "$start", "pay_key" => "$pay_key"));
            $this->note("操作成功", C("URL_DOMAIN") . 'config/pay_list');
        }

        $arr = array(
            "id" => array("name" => "支付宝商户号:", "val" => "12322313"),
            "key" => array("name" => "支付宝密钥:", "val" => "8934e7d15453e97507ef794cf7b0519d1"),
            "user" => array("name" => "支付宝账号:", "val" => "xx@qq.ccc"),
        );

        $this->assign("pay", $pay);
        $this->display("admin/payset");
    }

}
