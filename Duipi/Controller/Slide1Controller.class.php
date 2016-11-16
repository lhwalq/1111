<?php

/**
 * 视频
 * addtime 2016/03/28
 */

namespace Duipi\Controller;

use Think\Controller;

class Slide1Controller extends BaseController {

    public function _initialize() {
        $this->getAdminRight(CONTROLLER_NAME, ACTION_NAME);
        $ment = array(
            array("navigation", "幻灯管理", C("URL_DOMAIN") . "slide1/index"),
            array("navigation", "添加视频", C("URL_DOMAIN") . "slide1/edit"),
        );
        $this->assign("ment", $ment);
    }

    public function index() {
        $isMobile = I("mobile", 0);
        $table = $isMobile ? "shouji" : "flash";
        $lists = D('video')->select();
        $this->assign("lists", $lists);
        if ($isMobile) {
            $this->assign("is_mobile", TRUE);
        }
        $this->display("admin/slide_list1");
    }

    public function edit() {
        $id = I("id", 0);
        $mobie = I("mobile", 0);
        $table = $mobie ? "shouji" : "flash";
        if ($id) {
            $slideone = D('video')->where(array("id" => "$id"))->find();
            $this->assign("slideone", $slideone);
        }
        if ($mobie) {
            $this->assign("is_mobile", TRUE);
        }
        $this->display("admin/slide_update1");
    }

    public function save() {
        $id = I("id", 0);
        $mobile = I("mobile", 0);

        $title = htmlspecialchars(trim($_POST['title']));
        $link = htmlspecialchars(trim($_POST['link']));
        $img = $_POST['image'];
       
            $bac = htmlspecialchars(trim($_POST['title2']));
            $data = array("img" => "$img", "title" => "$title", "link" => "$link", "color" => "$bac");
            $table = "video";
       


        if ($id) {
            $res = D('video')->where(array("id" => $id))->save($data);
        } else {
            $res = D('video')->add($data);
        }
        if ($res) {
            $this->note("修改成功", C("URL_DOMAIN") . "slide1/index");
        } else {
            $this->note("修改失败");
        }
    }

    public function delete() {
        $id = I("id", 0);
        $mobie = I("mobile", 0);
        $table = $mobie ? "shouji" : "flash";
        $res = D('video')->where(array("id" => $id))->delete();
        if ($res) {
            $this->note("删除成功", C("URL_DOMAIN") . "slide1/index");
        } else {
            $this->note("删除失败");
        }
    }

    /**
     * 焦点图
     */
    public function getSlides() {
        $db_slides = D('video');
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