<?php

/**
 * 幻灯片
 * addtime 2016/03/28
 */

namespace Duipi\Controller;

use Think\Controller;

class SlideController extends BaseController {

    public function _initialize() {
        $this->getAdminRight(CONTROLLER_NAME, ACTION_NAME);
        $ment = array(
            array("navigation", "幻灯管理", C("URL_DOMAIN") . "slide/index"),
            array("navigation", "添加幻灯片", C("URL_DOMAIN") . "slide/edit"),

			array("navigation", "手机幻灯管理", C("URL_DOMAIN") . "slide/index/mobile/1"),
            array("navigation", "手机添加幻灯片", C("URL_DOMAIN") . "slide/edit/mobile/1"),
        );
        $this->assign("ment", $ment);
    }

    public function index() {
        $isMobile = I("mobile", 0);
        $table = $isMobile ? "shouji" : "flash";
        $lists = D($table)->select();
        $this->assign("lists", $lists);
        if ($isMobile) {
            $this->assign("is_mobile", TRUE);
        }
        $this->display("admin/slide_list");
    }

    public function edit() {
$user = $this->getAdminInfo(FALSE);
        if ($user['xianzhi']) {
            
                echo "<script>
				alert('权限不足,请联系总管理员');
				exit;			
				</script>";

exit;
            }
        $id = I("id", 0);
        $mobie = I("mobile", 0);
        $table = $mobie ? "shouji" : "flash";
        if ($id) {
            $slideone = D($table)->where(array("id" => "$id"))->find();
            $this->assign("slideone", $slideone);
        }
        if ($mobie) {
            $this->assign("is_mobile", TRUE);
        }
        $this->display("admin/slide_update");
    }

    public function save() {
        $id = I("id", 0);
        $mobile = I("mobile", 0);

        $title = htmlspecialchars(trim($_POST['title']));
        $link = htmlspecialchars(trim($_POST['link']));
        $img = $_POST['image'];
        if (!$mobile) {
            $bac = htmlspecialchars(trim($_POST['bac']));
            $sort = htmlspecialchars(trim($_POST['sort']));
            $pid = htmlspecialchars(trim($_POST['pid']));
            $data = array("img" => "$img", "title" => "$title", "link" => "$link", "pid" => "$pid", "bac" => "$bac", "sort" => "$sort");
            $table = "flash";
        } else {
            $bac = htmlspecialchars(trim($_POST['title2']));
            $data = array("img" => "$img", "title" => "$title", "link" => "$link", "color" => "$bac");
            $table = "shouji";
        }


        if ($id) {
            $res = D($table)->where(array("id" => $id))->save($data);
        } else {
            $res = D($table)->add($data);
        }
        if ($res) {
            $this->note("修改成功", C("URL_DOMAIN") . "slide/index");
        } else {
            $this->note("修改失败");
        }
    }

    public function delete() {
		$user = $this->getAdminInfo(FALSE);
        if ($user['xianzhi']) {
            
                echo "<script>
				alert('权限不足,请联系总管理员');
				exit;			
				</script>";

exit;
            }
        $id = I("id", 0);
        $mobie = I("mobile", 0);
        $table = $mobie ? "shouji" : "flash";
        $res = D($table)->where(array("id" => $id))->delete();
        if ($res) {
            $this->note("删除成功", C("URL_DOMAIN") . "slide/index");
        } else {
            $this->note("删除失败");
        }
    }

    /**
     * 焦点图
     */
    public function getSlides() {
        $db_slides = D('shouji');
        $SlideList = $db_slides->select();
        if (empty($SlideList)) {
            $slides['state'] = 1;
        } else {
            $slides['state'] = 0;
            foreach ($SlideList as $key => $val) {
                $shopid = ereg_replace('[^0-9]', '', $val['link']);
                $slides['listItems'][$key]['alt'] = $val['color'];
                $slides['listItems'][$key]['url'] = C("URL_DOMAIN") . "goods/items/goodsId/" . $shopid;
                $slides['listItems'][$key]['src'] = $val['img'];
                $slides['listItems'][$key]['width'] = '614px';
                $slides['listItems'][$key]['height'] = '110px';
            }
        }
        echo json_encode($slides);
    }

}

?>