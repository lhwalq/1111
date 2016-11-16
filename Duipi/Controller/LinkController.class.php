<?php

/**
 * 友情连接
 * addtime 2016/03/28
 */

namespace Duipi\Controller;

use Think\Controller;

class LinkController extends BaseController {

    public function _initialize() {
        $this->getAdminRight(CONTROLLER_NAME, ACTION_NAME);
        $ment = array(
            array("lists", "友情链接", C("URL_DOMAIN") . "link/lists"),
            array("addcate", "添加链接", C("URL_DOMAIN") . "link/edit"),
        );
        $this->assign("ment", $ment);
    }

    //友情链接列表
    public function lists() {
        $linkres = D("link")->select();
        $this->assign("linkres", $linkres);
        $this->display("admin/link.list");
    }

    //添加
    public function save() {
        $cid = I("id", 0);
        $name = htmlspecialchars($_POST['name']);
        $url = htmlspecialchars($_POST['url']);
        if (empty($name) || empty($url)) {
            $this->note("操作失败", C("URL_DOMAIN") . "link/lists");
        }

        $logo = "";
        $type = 1;
        if (isset($_FILES['image']) && $_FILES['image']['name']) {
            $upload = new \Claduipi\Tools\upload;
            $upload->upload_config(array('png', 'jpg', 'jpeg', 'gif'), 500000, 'linkimg');
            $upload->go_upload($_FILES['image']);
            if (!$upload->ok) {
                $this->note($upload->error, C("URL_DOMAIN") . "link/lists");
            }
            $logo = $upload->filedir . "/" . $upload->filename;
            $type = 2;
        }
        $data = array("name" => "$name", "type" => "$type", "url" => "$url", "logo" => "$logo");

        if ($cid) {
            D("link")->where(array("id" => "$cid"))->save($data);
        } else {
            D("link")->add($data);
        }

        $this->note("操作成功", C("URL_DOMAIN") . 'link/lists');
    }

    //修改
    public function edit() {
        $linkid = I("id", 0);
        if (intval($linkid) > 0) {
            $info = D("link")->where(array("id" => "$linkid"))->find();
            if (!$info)
                $this->note("参数错误");
            $this->assign("info", $info);
        }
        $this->display("admin/link.addimg");
    }

    //添加图片链接
    public function addimg() {
        if (isset($_POST['submit'])) {
            $name = htmlspecialchars($_POST['name']);
            $url = htmlspecialchars($_POST['url']);
            $logo = '';
            if (empty($name) || empty($url)) {
                $this->note("插入失败", C("URL_DOMAIN") . "link/lists");
            }
            if (isset($_FILES['image'])) {
                System::DOWN_sys_class('upload', 'sys', 'no');
                upload::upload_config(array('png', 'jpg', 'jpeg', 'gif'), 500000, 'linkimg');
                upload::go_upload($_FILES['image']);
                if (!upload::$ok) {
                    $this->note(upload::$error, C("URL_DOMAIN") . "link/lists");
                }
                $logo = upload::$filedir . "/" . upload::$filename;
            }

            $this->db->Query("INSERT INTO `@#_link`(type,name,logo,url)VALUES('2','$name','$logo','$url')");
            if ($this->db->affected_rows()) {
                $this->note("插入成功", C("URL_DOMAIN") . "link/lists");
            } else {
                $this->note("插入失败");
            }
        }
        include $this->dwt(DOWN_M, 'link.addimg');
    }

    //添加文字链接
    public function addtext() {
        if (isset($_POST['submit'])) {
            $name = $_POST['name'];
            $url = $_POST['url'];
            if (empty($name) && empty($url)) {
                $this->note("插入失败", C("URL_DOMAIN") . "link/lists");
            }
            $this->db->Query("INSERT INTO `@#_link`(type,name,url)VALUES('1','$name','$url')");

            if ($this->db->affected_rows()) {
                $this->note("插入成功", C("URL_DOMAIN") . "link/lists");
            } else {
                $this->note("插入失败", C("URL_DOMAIN") . "link/lists");
            }
        }
        include $this->dwt(DOWN_M, 'link.addtext');
    }

    //执行修改链接
    public function modifiylink() {
        $linkid = intval($this->segment(4));
        $linkinfo = $this->db->YOne("SELECT * FROM `@#_link` where `id`='$linkid'");
        if (!$linkinfo)
            $this->note("参数不正确");

        if (isset($_POST['submit'])) {
            $name = htmlspecialchars($_POST['name']);
            $url = htmlspecialchars($_POST['url']);
            if ($linkinfo['type'] == 1) {
                $this->db->Query("UPDATE `@#_link` SET `name`='$name',`url`='$url' WHERE `id`=$linkid");
                if ($this->db->affected_rows()) {
                    $this->note("修改成功", C("URL_DOMAIN") . "link/lists");
                } else {
                    $this->note("修改失败");
                }
            }
            if ($linkinfo['type'] == 2) {

                $logo = $linkinfo['logo'];
                if (isset($_FILES['image'])) {
                    System::DOWN_sys_class('upload', 'sys', 'no');
                    upload::upload_config(array('png', 'jpg', 'jpeg', 'gif'), 500000, 'linkimg');
                    upload::go_upload($_FILES['image']);
                    if (!upload::$ok) {
                        $this->note(upload::$error, C("URL_DOMAIN") . "link/lists");
                    }
                    $logo = upload::$filedir . "/" . upload::$filename;
                }

                $this->db->Query("UPDATE `@#_link` SET `name`='$name',`url`='$url',`logo`='$logo' WHERE `id`='$linkid'");
                if ($this->db->affected_rows()) {
                    $this->note("修改成功", C("URL_DOMAIN") . "link/lists");
                } else {
                    $this->note("修改失败");
                }
            }
        }
        include $this->dwt(DOWN_M, 'link.editlink');
    }

    //删除链接
    public function delete() {
        $dellink = I("id", 0);
        if ($dellink) {
            $res = D("link")->where(array("id" => $dellink))->delete();
            if ($res) {
                $this->note("删除成功", C("URL_DOMAIN") . "link/lists");
            } else {
                $this->note("删除失败", C("URL_DOMAIN") . "link/lists");
            }
        }
    }

}

?>