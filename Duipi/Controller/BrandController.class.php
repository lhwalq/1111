<?php

/**
 * 品牌
 * addtime 2016/03/24
 */

namespace Duipi\Controller;

use Think\Controller;

class BrandController extends BaseController {

    public function _initialize() {
        $this->getAdminRight(CONTROLLER_NAME, ACTION_NAME);
        $ment = array(
            array("lists", "品牌管理", C("URL_DOMAIN") . "brand/lists"),
            array("insert", "添加品牌", C("URL_DOMAIN") . "brand/edit"),
        );
        $this->assign("ment", $ment);
    }

    //品牌管理
    public function lists() {
        $fenye = new \Claduipi\Tools\page;
        $num = 20;
        $zongji = D("pinpai")->count();
        if (isset($_GET['p'])) {
            $fenyenum = intval($_GET['p']);
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum);
        $pinpai = D("pinpai")->order("'order' desc")->limit(($fenyenum - 1) * $num, $num)->select();
        $pinpais = $this->key2key($pinpai, "id");
        $fenlei = D("fenlei")->order("parentid,cateid")->select();
        $fenleis = $this->key2key($fenlei, "cateid");

        $this->assign("fenleis", $fenleis);
        $this->assign("fenye", $fenye);
        $this->assign("pinpais", $pinpais);
        $this->assign("zongji", $zongji);
        $this->display("admin/brand.list");
    }

    //品牌管理入库
    public function save() {
        $pinpaiid = I("id", 0);
        if (!isset($_POST['cateid'])) {
            $this->note("请选择所属栏目");
        }
        if (!isset($_POST['name']) && !$pinpaiid) {
            $this->note("请填写品牌名称");
        }
        $cateidsty = '';
        foreach ($_POST['cateid'] as $cateid) {
            $cateidsty .= intval($cateid) . ",";
        }
        $cateidsty = trim($cateidsty, ",");
        $name = htmlspecialchars($_POST['name']);
        $order = intval($_POST['order']) ? intval($_POST['order']) : 1;
        if ($pinpaiid) {
            $res = D("pinpai")->where(array("id" => "$pinpaiid"))->save(array("cateid" => "$cateidsty", "name" => "$name", "order" => "$order"));
        } else {
            $res = D("pinpai")->add(array("cateid" => "$cateidsty", "name" => "$name", "order" => "$order"));
        }
        if ($res) {
            $this->note("操作成功!", C("URL_DOMAIN") . '/brand/lists');
        } else {
            $this->note("操作失败!");
        }
    }

    //修改品牌
    public function edit() {
        $pinpaiid = I("id", 0);
        $fenlei = D("fenlei")->where(array("model" => "1"))->order("parentid,cateid")->select();
        $fenleis = $this->key2key($fenlei, "cateid");
        $tree = new \Claduipi\Tools\tree;
        $tree->icon = array('│ ', '├─ ', '└─ ');
        $tree->nbsp = '&nbsp;';
        $fenleishtml = "<option value='\$cateid'>\$spacer\$name</option>";
        $tree->init($fenleis);
        $fenleishtml = $tree->get_tree(0, $fenleishtml);

        if ($pinpaiid) {
            $pinpais = D("pinpai")->where(array("id" => "$pinpaiid"))->find();
            $cateid_arr = explode(",", $pinpais['cateid']);
            $this->assign("cateid_arr", $cateid_arr);
            $this->assign("pinpais", $pinpais);
        }
        $this->assign("fenleishtml", $fenleishtml);
        $this->display("admin/brand.edit");
    }

    //删除品牌管理
    public function del() {
        $pinpaiid = I("id", 0);
        if (!$pinpaiid) {
            $this->note("参数错误!");
        }
        $res = D("pinpai")->where(array("id" => "$pinpaiid"))->delete();
        if ($res) {
            $this->note("操作成功!", C("URL_DOMAIN") . '/brand/lists');
        } else {
            $this->note("操作失败!");
        }
    }

    /*
     * 	品牌排序
     */

    public function listorder() {
        if (IS_POST) {
            foreach ($_POST['listorders'] as $id => $listorder) {
                D("pinpai")->where(array("id" => "$id"))->save(array("order" => "$listorder"));
            }
            $this->note("排序更新成功");
        } else {
            $this->note("请排序");
        }
    }

}

?>