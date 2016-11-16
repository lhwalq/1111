<?php

/**
 * 分类
 * addtime 2016/03/24
 */

namespace Duipi\Controller;

use Think\Controller;

class CategoryController extends BaseController {

    public function _initialize() {
        $this->getAdminRight(CONTROLLER_NAME, ACTION_NAME);
        $ment = array(
            array("lists", "栏目管理", C("URL_DOMAIN") . "category/lists"),
            array("addcate", "添加栏目", C("URL_DOMAIN") . "category/addcate/type/def"),
            array("addcate", "添加单网页", C("URL_DOMAIN") . "category/addcate/type/danweb"),
            array("addcate", "添加外部链接", C("URL_DOMAIN") . "category/addcate/type/link"),
        );
        $this->assign("ment", $ment);
    }

    //栏目列表
    public function lists() {
        $cate_type = I("type", "");
        if (!$cate_type) {
            $cate_where = '1';
        }
        if ($cate_type == 'article') {
            $cate_where = "model = '2'";
        }
        if ($cate_type == 'goods') {
            $cate_where = "model = '1'";
        }
        if ($cate_type == 'single') {
            $cate_where = "model = '-1'";
        }
        $fenlei = D("fenlei")->where($cate_where)->order("parentid")->select();
        $fenleis = $this->key2key($fenlei, "cateid");
        $model = D("moxing")->select();
        $models = $this->key2key($model, "modelid");

        $tree = new \Claduipi\Tools\tree;
        $tree->icon = array('│ ', '├─ ', '└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        foreach ($fenleis as $v) {
            $v['typename'] = $this->cattype($v['model']);
            if ($v['model'] == -1) {
                $v['addsun'] = C("URL_DOMAIN") . 'category/addcate/type/danweb/id/';
            }
            if ($v['model'] == -2) {
                $v['addsun'] = C("URL_DOMAIN") . 'category/addcate/type/link/id/';
            }
            if ($v['model'] > 0) {
                $v['addsun'] = C("URL_DOMAIN") . 'category/addcate/type/def/id/';
                $v['model'] = $models[$v['model']]['name'];
            } else {
                $v['model'] = '';
            }
            $v['editcate'] = C("URL_DOMAIN") . 'category/editcate/id/';
            $v['delcate'] = C("URL_DOMAIN") . 'category/delcate/id/';
            $fenleis[$v['cateid']] = $v;
        }
        $html = <<<HTML
			<tr>
            <td align='center'><input name='listorders[\$cateid]' type='text' size='3' value='\$order' class='input-text-c'></td>
			<td align='center'>\$cateid</td>
            <td align='left'>\$spacer\$name</th>
            <td align='center'>\$typename</td>
            <td align='center'>\$model</td>
            <td align='center'></td>
			<td align='center'>
                <a href='\$addsun\$cateid'>添加子栏目</a><span class='span_fenge lr5'>|</span>   
				<a href='\$editcate\$cateid'>修改</a><span class='span_fenge lr5'>|</span>
				<a href='\$delcate\$cateid' onclick='return confirm(\"\确定删除吗\")'>删除</a>
          </tr>
HTML;
        $tree->init($fenleis);
        $html = $tree->get_tree(0, $html);
        $this->assign("html", $html);
        $this->display("admin/category.list");
    }

    //添加栏目
    public function addcate() {
        $catetype = I("type", "");   //类型
        $fenlei = D("fenlei")->order("parentid,cateid")->select();
        $fenleis = $this->key2key($fenlei, "cateid");
        $model = D("moxing")->select();
        $models = $this->key2key($model, "modelid");
        $tree = new \Claduipi\Tools\tree;
        $tree->icon = array('│ ', '├─ ', '└─ ');
        $tree->nbsp = '&nbsp;';
        $fenleishtml = "<option value='\$cateid'>\$spacer\$name</option>";
        $tree->init($fenleis);
        $fenleishtml = $tree->get_tree(0, $fenleishtml);
        $topmodel = '';
        if ($topcat = I("id", 0)) {
            $topcat = D("fenlei")->where("cateid = '$topcat'")->find();
            if (!$topcat)
                $this->note("参数错误");
            $fenleishtml.="<option value='$topcat[cateid]' selected>≡ $topcat[name] ≡</option>";
            if ($topcat['model'] > 0) {
                $modelname = $models[$topcat['model']]['name'];
                $topmodel = "<option value=\"$topcat[model]\" selected>≡ $modelname ≡</option>";
            }
        }

        $info = array();
        $mesage = NULL;

        if (isset($_POST['info'])) {
	
            switch ($catetype) {
                case 'def':
                    $info['modelid'] = ($topcat != 0) ? $topcat['model'] : intval($_POST['info']['modelid']);
                    $info['parentid'] = intval($_POST['info']['parentid']);
                    $info['name'] = htmlspecialchars($_POST['info']['name']);
                    $info['catdir'] = htmlspecialchars($_POST['info']['catdir']);
                    if ($info['modelid'] == 0) {
                        $mesage = '请选择数据模型';
                    }
                    if (empty($info['name'])) {
                        $mesage = '栏目名不能为空';
                    }
                    if (empty($info['catdir'])) {
                        $mesage = '英文名不能为空';
                    }
                    if (!empty($mesage))
                        $this->note($mesage, null, 3);
                    $setting = array(
                        'thumb' => htmlspecialchars($_POST['thumb']),
                        'des' => '',
                        'template' => '',
                        'content' => '',
                        'meta_title' => htmlspecialchars($_POST['setting']['meta_title']),
                        'meta_keywords' => htmlspecialchars($_POST['setting']['meta_keywords']),
                        'meta_description' => htmlspecialchars($_POST['setting']['meta_description']),
                    );

                    $setting['template_list'] = $_POST['info']['template_list'];
                    $setting['template_show'] = $_POST['info']['template_show'];
                    $setting['des'] = htmlspecialchars($_POST['info']['description']);
                    $setting = serialize($setting);
                    $data = array("parentid" => $info['parentid'], "channel" => "0", "model" => $info['modelid'], "name" => $info['name'], "catdir" => $info['catdir'], "url" => "", "info" => $setting, "order" => "1");
                    $res = D("fenlei")->add($data);
                    if ($res) {
						
                        $this->note("栏目添加成功!", C("URL_DOMAIN") . '/category/lists');
                    } else {
                        $this->note("栏目添加失败!");
                    }
                    break;
                case 'danweb':
                    $info['modelid'] = -1;
                    $info['parentid'] = intval($_POST['info']['parentid']);
                    $info['name'] = htmlspecialchars($_POST['info']['name']);
                    $info['catdir'] = htmlspecialchars($_POST['info']['catdir']);
                    if (empty($info['name'])) {
                        $mesage = '栏目名不能为空';
                    }
                    if (empty($info['catdir'])) {
                        $mesage = '英文名不能为空';
                    }
                    if (!empty($mesage))
                        $this->note($mesage, null, 3);
                    $setting = array(
                        'thumb' => htmlspecialchars($_POST['thumb']),
                        'des' => '',
                        'template' => '',
                        'content' => '',
                        'meta_title' => $_POST['setting']['meta_title'],
                        'meta_keywords' => $_POST['setting']['meta_keywords'],
                        'meta_description' => $_POST['setting']['meta_description'],
                    );
                    $setting['des'] = htmlspecialchars($_POST['info']['description']);
                    $setting['template'] = $_POST['info']['template'];
                    $setting['content'] = base64_encode($this->editor_safe_replace(stripslashes($_POST['setting']['content'])));
                    $setting = serialize($setting);
                    $data = array("parentid" => $info['parentid'], "channel" => "0", "model" => $info['modelid'], "name" => $info['name'], "catdir" => $info['catdir'], "url" => "", "info" => $setting, "order" => "1");
                    $res = D("fenlei")->add($data);

                    if ($res) {
                        $this->note("栏目添加成功!", C("URL_DOMAIN") . '/category/lists');
                    } else {
                        $this->note("栏目添加失败!");
                    }
                    break;
                case 'link':
                    $info['modelid'] = -2;
                    $info['parentid'] = intval($_POST['info']['parentid']);
                    $info['name'] = htmlspecialchars($_POST['info']['name']);
                    $info['url'] = htmlspecialchars($_POST['info']['url']);
                    if (empty($info['name'])) {
                        $this->note('栏目名不能为空');
                    }
                    if (empty($info['url'])) {
                        $this->note("地址不能为空");
                    }
                    $data = array("parentid" => $info['parentid'], "channel" => "0", "model" => $info['modelid'], "name" => $info['name'], "url" => "", "order" => "1");
                    $res = D("fenlei")->add($data);

                    if ($res) {
                        $this->note("栏目添加成功!", C("URL_DOMAIN") . '/category/lists');
                    } else {
                        $this->note("栏目添加失败!");
                    }
                    break;
            }//switch END			
        }//$_POST END
		$this->assign("fenleishtml",$fenleishtml);
		$this->assign("fenleis",$fenleis);
        $this->assign("models", $models);
        $this->assign("catetype", $catetype);
        $this->assign("topmodel", $topmodel);
        $this->display("admin/category.add");
    }

    //编辑
    public function editcate() {
        $cateid = I("id", 0);
        if (!intval($cateid)) {
            $this->note("参数错误");
            exit;
        }
        $cateinfo = D("fenlei")->where(array("cateid" => $cateid))->find();
        if (!$cateinfo) {
            $this->note("没有这个栏目");
        }
        $cateinfo['info'] = unserialize($cateinfo['info']);
        $fenlei = D("fenlei")->order("parentid,cateid")->select();
        $fenleis = $this->key2key($fenlei, "cateid");
        $model = D("moxing")->select();
        $models = $this->key2key($model, "modelid");

        $tree = new \Claduipi\Tools\tree;
        $tree->icon = array('│ ', '├─ ', '└─ ');
        $tree->nbsp = '&nbsp;';
        $fenleishtml = "<option value='\$cateid'>\$spacer\$name</option>";
        $tree->init($fenleis);
        $fenleishtml = $tree->get_tree(0, $fenleishtml);
        $catetype = 'def';      //类型
        if ($cateinfo['model'] > 0)
            $catetype = 'def';
        if ($cateinfo['model'] == -1)
            $catetype = 'danweb';
        if ($cateinfo['model'] == -2)
            $catetype = 'link';
        $topinfo = D("fenlei")->where(array("cateid" => $cateinfo['parentid']))->find();

        if ($topinfo) {
            $fenleishtml.="<option value='{$topinfo['cateid']}' selected>≡ {$topinfo['name']} ≡</option>";
        } else {
            $fenleishtml.="<option value='0' selected>≡ 作为一级栏目 ≡</option>";
        }
        $info = array();

        if (isset($_POST['info'])) {
            switch ($catetype) {
                case 'def':
                    $info['parentid'] = intval($_POST['info']['parentid']);
                    $info['name'] = htmlspecialchars($_POST['info']['name']);
                    $info['catdir'] = htmlspecialchars($_POST['info']['catdir']);
                    if (empty($info['name'])) {
                        $this->note('栏目名不能为空');
                    }
                    if (empty($info['catdir'])) {
                        $this->note("地址不能为空");
                    }
                    $setting = array(
                        'thumb' => htmlspecialchars($_POST['thumb']),
                        'des' => htmlspecialchars($_POST['info']['description']),
                        'template' => '',
                        'content' => '',
                        'meta_title' => htmlspecialchars($_POST['setting']['meta_title']),
                        'meta_keywords' => htmlspecialchars($_POST['setting']['meta_keywords']),
                        'meta_description' => htmlspecialchars($_POST['setting']['meta_description']),
                    );
                    $setting['template_list'] = $_POST['info']['template_list'];
                    $setting['template_show'] = $_POST['info']['template_show'];
                    $setting = serialize($setting);
                    $data = array("parentid" => $info['parentid'], "name" => $info['name'], "catdir" => $info['catdir'], "info" => $setting);
                    $res = D("fenlei")->where(array("cateid" => $cateid))->save($data);
                    if ($res) {
                        $this->note("操作成功!", C("URL_DOMAIN") . '/category/lists/');
                    } else {
                        $this->note("操作失败!");
                    }
                    break;
                case 'danweb':
                    $info['parentid'] = intval($_POST['info']['parentid']);
                    $info['name'] = $_POST['info']['name'];
                    $info['catdir'] = $_POST['info']['catdir'];
                    if (empty($info['name'])) {
                        $this->note('栏目名不能为空');
                    }
                    if (empty($info['catdir'])) {
                        $this->note("地址不能为空");
                    }
                    $setting = array(
                        'thumb' => htmlspecialchars($_POST['thumb']),
                        'des' => htmlspecialchars($_POST['info']['description']),
                        'template' => $_POST['info']['template'],
                        'content' => base64_encode($this->editor_safe_replace(stripslashes($_POST['setting']['content']))),
                        'meta_title' => htmlspecialchars($_POST['setting']['meta_title']),
                        'meta_keywords' => htmlspecialchars($_POST['setting']['meta_keywords']),
                        'meta_description' => htmlspecialchars($_POST['setting']['meta_description']),
                    );
                    $setting = serialize($setting);
                    $data = array("parentid" => $info['parentid'], "name" => $info['name'], "catdir" => $info['catdir'], "info" => $setting);
                    $res = D("fenlei")->where(array("cateid" => $cateid))->save($data);
                    if ($res) {
                        $this->note("操作成功!", C("URL_DOMAIN") . '/category/lists/');
                    } else {
                        $this->note("操作失败!");
                    }
                    break;

                case 'link':
                    $info['parentid'] = intval($_POST['info']['parentid']);
                    $info['name'] = htmlspecialchars($_POST['info']['name']);
                    $info['url'] = htmlspecialchars($_POST['info']['url']);
                    if (empty($info['name'])) {
                        $this->note('栏目名不能为空');
                    }
                    if (empty($info['url'])) {
                        $this->note("地址不能为空");
                    }

                    $data = array("parentid" => $info['parentid'], "name" => $info['name'], "url" => $info['url']);
                    $res = D("fenlei")->where(array("cateid" => $cateid))->save($data);
                    if ($res) {
                        $this->note("操作成功!", C("URL_DOMAIN") . '/category/lists/');
                    } else {
                        $this->note("操作失败!");
                    }

                    break;
            }//SWITCH END				
        }//IF POST END
		$this->assign("fenleishtml", $fenleishtml);
		 $this->assign("cateid", $cateid);
        $this->assign("models", $models);
        $this->assign("cateinfo", $cateinfo);
        $this->display("admin/category.edit");
    }

    /*
     * 	栏目排序
     */

    public function listorder() {
        if (IS_AJAX || IS_POST) {
            foreach ($_POST['listorders'] as $id => $listorder) {
                D("fenlei")->where(array("cateid" => "$id"))->save(array("order" => "$listorder"));
            }
            $this->note("排序更新成功");
        } else {
            $this->note("请排序");
        }
    }

    //ajax删除栏目
    public function delcate() {
        $cateid = I("id", 0);
        if (!intval($cateid)) {
            echo "no";
            exit;
        }
        $res = D("fenlei")->where(array("cateid" => $cateid))->delete();
        if ($res) {
            $this->note("操作成功", C("URL_DOMAIN") . "/category/lists/type/goods");
        } else {
            echo "no";
        }
    }

}

?>
