<?php

/**
 * 微商城商品
 * addtime 2016/07/13
 */

namespace Duipi\Controller;

use Duipi\DuipiCla\Goods;
use Think\Controller;

class SgoodsController extends BaseController {

	//权限设置需要的开始
 public function _initialize() {
        $this->getAdminRight(CONTROLLER_NAME, ACTION_NAME);

     
      
    }
	//权限设置需要的结束
    /**
     * admin 商品添加
     */
    public function goods_edit() {
        $db = new \Think\Model;
        $id = I("id", 0);
        if ($id) {
            $shopinfo = D("s_goods")->where(array("id" => $id))->find();
        }
        if (IS_AJAX || IS_POST) {
            $cid = intval(I("cateid", 0));
            $bid = intval(I("brand", 0));
            $name = I("title");
            $money = I("money");
            $mar_money = I("mar_money");
            $stock = intval(I("stock", 0));
            $sales = intval(I("sales", 0));
            $thumb = htmlspecialchars(I("thumb"));
            if (I("uppicarr", "")) {
                $picarr = serialize(I("uppicarr", ""));
            } else {
                $picarr = serialize(array());
            }
            $key = I("goods_key");
            $popularity = $key['renqi'];
            if ($popularity == null) {
                $popularity = 0;
            }
            $recommend = $key['pos'];
            $content = $this->editor_safe_replace(stripslashes(I("content", "")));
            $sort = I("sort", 0);
            $is_del = I("is_del");
            $type = I("type");
            if (!$cid)
                $this->note("请选择栏目");
            if (!$bid)
                $this->note("请选择品牌");
            if (!$name)
                $this->note("标题不能为空");
            if (!$thumb)
                $this->note("缩略图不能为空");
            if (!$stock)
                $this->note("库存不能为空");
            if (!$money)
                $this->note("价格不能为空");
            $db->startTrans();
            if ($shopinfo) {
                $query = $db->table("yys_s_goods")->where(array("id" => $id))->save(array("name" => $name, "cid" => $cid, "bid" => $bid, "mar_money" => $mar_money, "money" => $money, "stock" => $stock, "sales" => $sales, "popularity" => $popularity, "recommend" => $recommend, "content" => $content, "thumb" => $thumb, "prcarr" => $picarr, "type" => $type, "sort" => $sort, "is_del" => $is_del));
                if ($query) {
                    $db->commit();
                    $this->note("商品修改成功!");
                } else {
                    $db->rollback();
                    $this->note("商品修改失败!");
                }
            } else {
                $query = $db->table("yys_s_goods")->add(array("name" => $name, "cid" => $cid, "bid" => $bid, "mar_money" => $mar_money, "money" => $money, "stock" => $stock, "sales" => $sales, "popularity" => $popularity, "recommend" => $recommend, "content" => $content, "thumb" => $thumb, "prcarr" => $picarr, "type" => $type, "sort" => $sort, "is_del" => $is_del));
                if ($query) {
                    $db->commit();
                    $this->note("商品添加成功!");
                } else {
                    $db->rollback();
                    $this->note("商品添加失败!");
                }
            }
            header("Cache-control: private");
        }
        $cateid = $shopinfo['cid']; //intval($this->segment(4));
        $fenlei = D("s_fenlei")->where(array("model" => "1"))->order("parentid ASC,cateid")->select();
        $fenleis = $this->key2key($fenlei, "cateid");
        $pinpai = $db->table("yys_s_pinpai")->select();
        $pinpaiList = $this->key2key($pinpai, "id");
        $tree = new \Claduipi\Tools\tree;
        $tree->icon = array('│ ', '├─ ', '└─ ');
        $tree->nbsp = '&nbsp;';
        $fenleishtml = "<option value='\$cateid'>\$spacer\$name</option>";
        $tree->init($fenleis);
        $fenleishtml = $tree->get_tree(0, $fenleishtml);
        $fenleishtml = '<option value="0">≡ 请选择栏目 ≡</option>' . $fenleishtml;
        if ($cateid) {
            $cateinfo = D("s_fenlei")->where(array("cateid" => "$cateid"))->find();
            if (!$cateinfo)
                $this->note("参数不正确,没有这个栏目", G_ADMIN_PATH . '/' . DOWN_C . '/addarticle');
            $fenleishtml.='<option value="' . $cateinfo['cateid'] . '" selected="true">' . $cateinfo['name'] . '</option>';
            $pinpai = D("s_pinpai")->where(array("cateid" => intval($cateid)))->select();
            $pinpaiList = $this->key2key($pinpai, "id");
        }else {
            $pinpai = D("s_pinpai")->select();
            $pinpaiList = $this->key2key($pinpai, "id");
        }
        $ment = array(
            array("lists", "商品管理", C("URL_DOMAIN") . "goods/goods_list"),
            array("insert", "添加商品", C("URL_DOMAIN") . "goods/goods_add"),
        );
        $shopinfo['prcarr'] = unserialize($shopinfo['prcarr']);
        $shopinfo['content'] = htmlspecialchars_decode($shopinfo['content']);
        $goodsTypeList = M("s_goods_type")->select();
        $this->assign('goodsTypeList', ($goodsTypeList));
        $this->assign("pinpaiList", $pinpaiList);
        $this->assign("shopinfo", $shopinfo);
        $this->assign("fenleishtml", $fenleishtml);
        $this->assign("ment", $ment);
        if ($shopinfo) {
            $this->display("sadmin/shop.edit");
        } else {
            $this->display("sadmin/shop.insert");
        }
    }

    public function json_brand() {
        $cateid = intval(I("cid", 0));
        if ($cateid) {
            $pinpaiList = D("s_pinpai")->where("cateid LIKE '%$cateid%'")->select();
            echo json_encode($pinpaiList);
        }
    }

    //商品列表	
    public function goods_list() {
        $del = I("del");
        if ($del == 1) {
            $list_where = "is_del=1";
            $ment = array(
                array("lists", "商品管理", C("URL_DOMAIN") . "sgoods/goods_list"),
                array("add", "添加商品", C("URL_DOMAIN") . "sgoods/goods_edit"),
                array("renqi", "人气商品", C("URL_DOMAIN") . "sgoods/goods_list/del/1/order/renqi"),
                array("money", "商品价格倒序", C("URL_DOMAIN") . "sgoods/goods_list/del/1/order/money"),
                array("money", "时间倒序", C("URL_DOMAIN") . "sgoods/goods_list/del/1/order/time"),
                array("money", "已上架商品", C("URL_DOMAIN") . "sgoods/goods_list/del/0"),
            );
        } else if ($del == 0) {
            $list_where = "is_del=0";
            $ment = array(
                array("lists", "商品管理", C("URL_DOMAIN") . "sgoods/goods_list"),
                array("add", "添加商品", C("URL_DOMAIN") . "sgoods/goods_edit"),
                array("renqi", "人气商品", C("URL_DOMAIN") . "sgoods/goods_list/del/0/orderrenqi"),
                array("money", "商品价格倒序", C("URL_DOMAIN") . "sgoods/goods_list/del/0/order/money"),
                array("money", "时间倒序", C("URL_DOMAIN") . "sgoods/goods_list/del/0/order/time"),
                array("money", "已删除商品", C("URL_DOMAIN") . "sgoods/goods_list/del/1"),
            );
        }
        $cateid = I("order", "");
        if ($cateid) {
            if ($cateid == 'renqi') {
                $list_order = "popularity DESC ";
            }
            if ($cateid == 'money') {
                $list_order = " money ASC";
                $ment[6][1] = "商品价格正序";
                $ment[6][2] = C("URL_DOMAIN") . "sgoods/goods_list/order/moneyasc";
            }
            if ($cateid == 'moneyasc') {
                $list_order = " money desc";
                $ment[6][1] = "商品价格倒序";
                $ment[6][2] = C("URL_DOMAIN") . "sgoods/goods_list/or3324der/money";
            }
        } else {
            $list_order = " id DESC";
        }
        if ($del) {
            if ($cateid == 'renqi') {
                $list_where = "popularity DESC ";
            }
            if ($cateid == 'money') {
                $list_order = " money ASC";
                $ment[6][1] = "商品价格正序";
                $ment[6][2] = C("URL_DOMAIN") . "sgoods/goods_list/del/1/order/moneyasc";
            }
            if ($cateid == 'moneyasc') {
                $list_order = " money desc";
                $ment[6][1] = "商品价格倒序";
                $ment[6][2] = C("URL_DOMAIN") . "sgoods/goods_list/del/1/order/money";
            }
        }
        if (isset($_POST['sososubmit'])) {
            $posttime1 = !empty($_POST['posttime1']) ? strtotime($_POST['posttime1']) : NULL;
            $posttime2 = !empty($_POST['posttime2']) ? strtotime($_POST['posttime2']) : NULL;
            $sotype = $_POST['sotype'];
            $sosotext = $_POST['sosotext'];

            if ($posttime1 && $posttime2) {
                if ($posttime2 < $posttime1)
                    $this->note("结束时间不能小于开始时间");
                $list_where = $list_where . " and time > '$posttime1' AND time < '$posttime2'";
            }
            if ($posttime1 && empty($posttime2)) {
                $list_where = $list_where . " and time > '$posttime1'";
            }
            if ($posttime2 && empty($posttime1)) {
                $list_where = $list_where . " and time < '$posttime2'";
            }
            if (empty($posttime1) && empty($posttime2)) {
                $list_where = false;
            }
            if (!empty($sosotext)) {
                if ($sotype == 'cateid') {
                    $sosotext = intval($sosotext);
                    if ($list_where)
                        $list_where = $list_where . " AND cid = '$sosotext'";
                    else
                        $list_where = $list_where . " and cid = '$sosotext'";
                }
                if ($sotype == 'brandid') {
                    $sosotext = intval($sosotext);
                    if ($list_where)
                        $list_where = $list_where . " AND bid = '$sosotext'";
                    else
                        $list_where = $list_where . " and bid = '$sosotext'";
                }
                if ($sotype == 'brandname') {
                    $sosotext = htmlspecialchars($sosotext);

                    $info = D("pinpai")->where("name LIKE '%$sosotext%'")->find();

                    if ($list_where && $info)
                        $list_where.= " AND bid = '{$info['id']}'";
                    else if ($info)
                        $list_where.= " AND bid = '{$info['id']}'";
                    else
                        $list_where.= " AND 1";
                }
                if ($sotype == 'catename') {
                    $sosotext = htmlspecialchars($sosotext);
                    $info = D("fenlei")->where("name LIKE '%$sosotext%'")->find();
                    if ($list_where && $info)
                        $list_where.= " AND cateid = '{$info['cateid']}'";
                    elseif ($info)
                        $list_where.= " and cateid = '{$info['cateid']}'";
                    else
                        $list_where.= " and 1";
                }
                if ($sotype == 'title') {
                    $sosotext = htmlspecialchars($sosotext);
                    $list_where.= " and title = '$sosotext'";
                }
                if ($sotype == 'id') {
                    $sosotext = intval($sosotext);
                    $list_where.= "and id = '$sosotext'";
                }
            } else {
                if (!$list_where)
                    $list_where.= 'and 1';
            }
        }
        $num = 20;
        $zongji = D("s_goods")->where($list_where)->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");
        $yyslist = D("s_goods")->where($list_where)->order($list_order)->limit(($fenyenum - 1) * $num, $num)->select();
        $this->assign("ment", $ment);
        $this->assign("del", $del);
        $this->assign("zongji", $zongji);
        $this->assign("fenye", $fenye);
        $this->assign("yyslist", $yyslist);
        $this->assign("cateid", $cateid);
        $this->display("sadmin/shop.lists");
    }

    //ajax 删除商品
    public function goods_type() {
        $type = I("type");
        $info = R('admin/getAdminInfo', array());
        $shopid = intval(I("id", 0));
        $db = new \Think\Model;
        if ($type == 0) {
            $infoinfo = $db->table("yys_s_goods")->where(array("id" => $shopid))->find();
            if ($infoinfo[type] == 1) {
                $this->note("商品已上架，请勿重复上架", C("URL_DOMAIN") . "sgoods/goods_list");
            }
            $info = $db->table("yys_s_goods")->where(array("id" => "$shopid"))->save(array("type" => 1));
        } else if ($type == 1) {
            $info = $db->table("yys_s_goods")->where(array("id" => "$shopid"))->save(array("is_del" => 1));
        } else if ($type == 2) {
            $info = $db->table("yys_s_goods")->where(array("id" => "$shopid"))->save(array("is_del" => 0));
        } else if ($type == 3) {
            $info = $db->table("yys_s_goods")->where(array("id" => "$shopid"))->delete();
            ;
        } else if ($type == 4) {
            $infoinfo = $db->table("yys_s_goods")->where(array("id" => $shopid))->find();
            if ($infoinfo[type] == 0) {
                $this->note("商品已下架，请勿重复下架", C("URL_DOMAIN") . "sgoods/goods_list");
            }
            $info = $db->table("yys_s_goods")->where(array("id" => "$shopid"))->save(array("type" => 0));
        }

        if ($info) {

            $this->note("操作成功", C("URL_DOMAIN") . "sgoods/goods_list");
        } else {

            $this->note("操作失败", C("URL_DOMAIN") . "sgoods/goods_list");
        }
        exit;
    }

    /**
     * 商品类型  用于设置商品的属性
     */
    public function goodsTypeList() {
        $ment = array(
            array("lists", "商品类型", C("URL_DOMAIN") . "sgoods/goodsTypeList"),
            array("add", "添加类型", C("URL_DOMAIN") . "sgoods/goodsTypeEdit"),
        );
        $model = M("s_goods_type");
        $count = $model->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($count, 20, $fenyenum, "0");
        $goodsTypeList = $model->order("id desc")->limit(($fenyenum - 1) * 20, 20)->select();
        $this->assign('goodsTypeList', $goodsTypeList);
        $this->assign('fenye', $fenye);
        $this->assign('zongji', $count);
        $this->assign('ment', $ment);
        $this->display('sadmin/shop.typeList');
    }

    /**
     * 商品类型修改
     */
    public function goodsTypeEdit() {
        $ment = array(
            array("lists", "商品类型", C("URL_DOMAIN") . "sgoods/goodsTypeList"),
            array("add", "添加类型", C("URL_DOMAIN") . "sgoods/goodsTypeEdit"),
        );
        $id = I("id");
        if ($id) {
            $res = M("s_goods_type")->where(array("id" => $id))->find();
            $this->assign("type", $res);
        }
        $this->assign('ment', $ment);
        $this->display('sadmin/shop.typeAdd');
    }

    /**
     * 类型删除
     */
    public function goodsTypeDel() {
        $id = I("id");
        $res = 0;
        if ($id) {
            $res = M("s_goods_type")->where(array("id" => $id))->delete();
            if ($res) {
                $this->note("删除成功", C("URL_DOMAIN") . "sgoods/goodsTypeList");
            }
        }
        if (!$res) {
            $this->note("删除失败", C("URL_DOMAIN") . "sgoods/goodsTypeList");
        }
    }

    /**
     * 类型修改
     */
    public function goodsTypeSave() {
        $id = I("id");
        $data['name'] = I("name");
        if (IS_POST) {
            if ($id)
                M("s_goods_type")->where(array("id" => $id))->save($data);
            else
                M("s_goods_type")->add($data);

            $this->note("操作成功!!!", C("URL_DOMAIN") . "sgoods/goodsTypeList");
            exit;
        }
    }

    /**
     * 商品规格列表    
     */
    public function specList() {
        $ment = array(
            array("lists", "规格列表", C("URL_DOMAIN") . "sgoods/specList"),
            array("add", "添加规格", C("URL_DOMAIN") . "sgoods/specEdit"),
        );
        $type = I('type_id');
        $where = ""; // 搜索条件    
        if ($type) {
            $where = array("type_id" => $type);
        }
        // 关键词搜索               
        $model = D('s_spec');
        $goodscla = new Goods;
        $count = $model->where($where)->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($count, 20, $fenyenum, "0");
        $goodsTypeList = M("s_goods_type")->select();
        $specList = $model->where($where)->order('type_id desc')->limit(($fenyenum - 1) * 20, 20)->select();
        foreach ($specList as $k => $v) {       // 获取规格项     
            $arr = $goodscla->getSpecItem($v['id']);
            $specList[$k]['spec_item'] = implode(' , ', $arr);
        }
        //dump($specList);exit;
        $this->assign('fenye', $fenye);
        $this->assign('zongji', $count);
        $this->assign('ment', $ment);
        $this->assign('type', $type);
        $this->assign('goodsTypeList', $this->key2key($goodsTypeList, "id"));
        $this->assign('specList', $specList);
        $this->display('sadmin/spec.list');
    }

    /**
     * 规格修改
     */
    public function specEdit() {
        $ment = array(
            array("lists", "规格列表", C("URL_DOMAIN") . "sgoods/specList"),
            array("add", "添加规格", C("URL_DOMAIN") . "sgoods/specEdit"),
        );
        $id = I("id");
        if ($id) {
            $res = M("s_spec")->where(array("id" => $id))->find();
            $goodscla = new Goods;
            $arr = $goodscla->getSpecItem($id);
            $res['spec_item'] = implode('', $arr);
            $this->assign("res", $res);
        }
        $goodsTypeList = M("s_goods_type")->select();
        $this->assign('goodsTypeList', ($goodsTypeList));
        $this->assign('ment', $ment);
        $this->display('sadmin/spec.add');
    }

    /**
     * 规格删除
     */
    public function specDel() {
        $id = I("id");
        $res = 0;
        if ($id) {
            $res = M("s_spec_item")->where(array("spec_id" => $id))->delete();
            $res = M("s_spec")->where(array("id" => $id))->delete();
            if ($res) {
                $this->note("删除成功", C("URL_DOMAIN") . "sgoods/specList");
            }
        }
        if (!$res) {
            $this->note("删除失败", C("URL_DOMAIN") . "sgoods/specList");
        }
    }

    /**
     * 规格修改
     */
    public function specSave() {
        $id = I("id");
        $data['name'] = I("name");
        $data['type_id'] = I("type_id");
        $items = I("items");
        $items = explode("\n", $items);
        if (IS_POST) {
            if ($id) {
                M("s_spec")->where(array("id" => $id))->save($data);
                M("s_spec_item")->where(array("spec_id" => $id))->delete();
            } else {
                $id = M("s_spec")->add($data);
            }
            $arr = array("spec_id" => $id);
            foreach ($items as $key => $value) {
                $arr['item'] = trimall($value);
                $res = M("s_spec_item")->add($arr);
            }
            if (!$res) {
                $this->note("操作失败!!!", C("URL_DOMAIN") . "sgoods/specList");
            }
            $this->note("操作成功!!!", C("URL_DOMAIN") . "sgoods/specList");
            exit;
        }
    }

    /**
     * 获取规格ajax
     */
    public function ajaxGetSpecSelect() {
        $id = I("id");
        $data['code'] = 1;
        $res = M("s_spec")->where(array("type_id" => $id))->select();
        // dump(M("s_spec")->getLastSql());
        if (!$res) {
            $data['code'] = 0;
        }
        $goodscla = new Goods;
        foreach ($res as $key => $value) {
            $res[$key]['spec_item'] = $goodscla->getSpecItem($value['id']);
        }
        $data['item'] = $res;
        echo json_encode($data);
    }

    /**
     * 生成规格
     */
    public function ajaxGetSpecInput() {
        $id = I("id");
        $data = array("code" => 0, "item" => "");
        if (!$id) {
            echo json_encode($data);
            exit;
        }
        $items = M("s_spec_item")->where("id in ($id)")->select();
        if (!$items) {
            echo json_encode($data);
            exit;
        }
        $arr = array();
        $arr2 = array();
        $arr3 = array();
        $retarr = array();
        foreach ($items as $key => $value) {
            if (!isset($arr[$value['spec_id']])) {
                $arr[$value['spec_id']] = array();
            }
            if (!isset($arr2[$value['spec_id']])) {
                $arr2[$value['spec_id']] = array();
            }
            array_push($arr[$value['spec_id']], trimall($value['item']));
            array_push($arr2[$value['spec_id']], $value['id']);
        }
        $arr2 = combineDika(array_merge($arr2), count($arr2));
        $arr = combineDika(array_merge($arr), count($arr));
        foreach ($arr2 as $key => $value) {
            $retarr[implode("_", $arr2[$key])] = implode("_", $arr[$key]);
        }
        $data['item'] = $retarr;
        $data['code'] = 1;
        echo json_encode($data);
    }

    /**
     * 保存商品规格
     */
    public function goodsSpecSave() {
        $id = I("id");
        $item = I("item");
        if (!$id) {
            $this->note("获取商品信息失败", C("URL_DOMAIN") . "sgoods/goods_list/id/$id");
        }if (!$item) {
            $this->note("获取规格信息失败", C("URL_DOMAIN") . "sgoods/goods_edit/id/$id");
        }
        $model = new \Think\Model;
        $model->startTrans();
        M("s_goods")->where(array("id" => $id))->save(array("goods_type" => 1));
        $data = array("goods_id" => $id);
        foreach ($item as $key => $value) {
            $data['key'] = $key;
            $data['key_name'] = $value['name'];
            $data['money'] = $value['money'];
            $data['stock'] = $value['store'];
            $res = M("s_spec_goods_price")->add($data);
            if (!$res) {
                $model->rollback();
                $this->note("添加出错", C("URL_DOMAIN") . "sgoods/goods_edit/id/$id");
            }
        }
        $model->commit();
        $this->note("添加成功", C("URL_DOMAIN") . "sgoods/goods_list");
    }

    //*******************************************************************前台********************************************************//
    /**
     * 商品详情
     */
    public function items() {
        $id = I("id");
        $info = D("s_goods")->where(array("id" => $id, "is_del" => 0, "type" => 1))->find();
        if (!$info) {
            $this->errmsg("亲，该商品失踪了～！");
        }
        unset($info['content']);
        //获取规格
        if ($info["goods_type"] == 1) {
            //$guige = D("s_spec_goods_price")->join("yys_s_spec")->where(array("goods_id" => $id))->select();
            $gc = new Goods;
            $guige = $gc->get_spec($id);
            //dump($guige);
            $this->assign("guige", $guige);
        }
        //爆款新品
        $bgoods = D("s_goods")->where("is_del=0 and type=1 and stock>0")->order("sales desc,popularity desc,time desc")->field("id,name,money,thumb")->limit(12)->select();
        //历史记录
        $user = $this->getUserInfo();
        $lishi = cookie("lishi_" . $user['uid']);
        $arr = array();
        if ($lishi) {
            $arr = unserialize($lishi);
        }
        $linshiarr = array(
            "id" => $info['id'],
            "thumb" => $info['thumb'],
            "name" => $info['name'],
            "money" => $info['money'],
        );
        foreach ($arr as $key => $value) {
            if ($value['id'] == $id) {
                unset($arr[$key]);
            }
        }
        array_unshift($arr, $linshiarr);
        if (count($arr) > 10) {
            unset($arr[10]);
        }
        cookie("lishi_" . $user['uid'], serialize($arr));
        $this->assign("bgoods", $bgoods);
        $this->assign("info", $info);
        $this->assign("shopnumber", $this->getShopCartNumber());
        $this->assign("prcarr", unserialize($info['prcarr']));
        $this->assign("biaoti", "商品详情");
        $this->display("smobile/index.item");
    }

    /**
     * 商品详情内容页面
     */
    public function info() {
        $id = I("id");
        $info = D("s_goods")->where(array("id" => $id, "is_del" => 0, "type" => 1))->find();
        if (!$info) {
            $this->errmsg("亲，该商品失踪了～！");
        }
        $this->assign("content", htmlspecialchars_decode($info['content']));
        $this->display("smobile/index.item_info");
    }

    /**
     * 购物车
     */
    public function cart() {
        $user = $this->getUserInfo();
        $userCart = D("s_cart")->where(array("uid" => $user['uid']))->find();
        $goods = array();
        $cart = unserialize($userCart['content']);
        $cartShop = $cart['goods'];
        $goodsids = implode(",", array_keys($cartShop));
        if ($goodsids) {
            $goods = D("s_goods")->where("id in ($goodsids)")->select();
        }
        //规格数据
        $cartShop1 = $cart['product'];
        $goodsids = implode(",", array_keys($cartShop1));
        if ($goodsids) {
            $info = D("s_goods a")->join("yys_s_spec_goods_price b on a.id=b.goods_id")->field("a.thumb,a.name,b.key_name,b.id,b.goods_id,a.mar_money,b.money,b.stock")->where("b.id in ($goodsids)")->select();
        }
        $this->assign("biaoti", "购物车");
        $this->assign("cart", $cart);
        $this->assign("shopnum", count($cartShop) + count($cartShop1));
        $this->assign("number", $this->getShopCartNumber($user['uid']));
        $this->assign("goods", $goods);
        $this->assign("info", $info);
        $this->display("smobile/cart.index");
    }

    /**
     * 商品列表
     */
    public function glist() {
        $key = I("keyword");
        if (!$this->is_utf8($key)) {
            $key = iconv("GBK", "UTF-8", $key);
        }
        $type = I("type_select"); //搜索方式 后期做
        //$goods = D("s_goods")->where("name like '%$key%'")->select();
        $user = $this->getUserInfo();
        //记录搜索信息
        if ($key && $user) {
            //$user['uid'] = $user['uid'] ? $user['uid'] : 0;
            $userso = D("s_soso")->where(array("content" => $key, "uid" => $user['uid']))->find();
            $sum = $userso['sum'] ? $userso['sum'] + 1 : 1;
            $data = array("content" => $key, "uid" => $user['uid'], "sum" => $sum, "time" => time());

            if ($userso) {
                $res = D("s_soso")->where(array("id" => $userso['id']))->save($data);
            } else {
                $res = D("s_soso")->add($data);
            }
        }
        $this->assign("keyword", $key);
        $this->display("smobile/index.goods_list");
    }

}
