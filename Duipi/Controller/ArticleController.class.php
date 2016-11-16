<?php

/**
 * 文章管理
 * addtime 2016/03/25
 */

namespace Duipi\Controller;

use Think\Controller;

class ArticleController extends BaseController {

    public function _initialize() {
        $this->getAdminRight(CONTROLLER_NAME, ACTION_NAME);
    }

    //添加新文章
    public function save() {
        $id = I("id", 0);
        $cateid = intval($_POST['cateid']);
        $biaoti = htmlspecialchars($_POST['title']);
        if (empty($biaoti)) {
            $this->note("标题不能为空");
        }
        if (!$cateid) {
            $this->note("栏目不能为空");
        }
        $biaoti_color = htmlspecialchars($_POST['title_style_color']);
        $biaoti_bold = htmlspecialchars($_POST['title_style_bold']);
        $biaoti_style = '';
        if ($biaoti_color) {
            $biaoti_style.='color:' . $biaoti_color . ';';
        }
        if ($biaoti_bold) {
            $biaoti_style.='font-weight:' . $biaoti_bold . ';';
        }
        $data = array(
            "cateid" => $cateid,
            "author" => isset($_POST['zuoze']) ? htmlspecialchars($_POST['zuoze']) : '',
            "title" => $biaoti,
            "title_style" => $biaoti_style,
            "keywords" => htmlspecialchars($_POST['keywords']),
            "description" => htmlspecialchars($_POST['description']),
            "thum" => isset($_POST['thumb']) ? htmlspecialchars($_POST['thumb']) : '',
            "picarr" => isset($_POST['uppicarr']) ? serialize($_POST['uppicarr']) : serialize(array()),
            "content" => $this->editor_safe_replace(stripslashes($_POST['content'])),
            "posttime" => strtotime($_POST['posttime']) ? strtotime($_POST['posttime']) : time(),
            "hit" => intval($_POST['hit'])
        );
        if ($id) {
			$user = $this->getAdminInfo(FALSE);
        if ($user['xianzhi']) {
            
                echo "<script>
				alert('权限不足,请联系总管理员');
				exit;			
				</script>";

exit;
            }
            $res = D("wenzhang")->where(array("id" => "$id"))->save($data);
        } else {
            $data['order'] = "1";
            $res = D("wenzhang")->add($data);
        }
        if ($res) {
            $this->note("操作成功");
        } else {
            $this->note("操作失败");
        }
        header("Cache-control: private");
    }

    //文章编辑
    public function article_edit() {
        $cateid = I("cateid", 0);
        $id = I("id", 0);
        if ($id) {
            $info = D("wenzhang")->where(array("id" => "$id"))->find();
            if (!$info) {
                $this->note("参数错误");
            }
            $cateinfo = D("fenlei")->where(array("cateid" => "{$info['cateid']}"))->order("parentid,cateid")->find();
            $this->assign("info", $info);
        }
        $fenleis = $this->key2key(D("fenlei")->order("parentid,cateid")->select(), "cateid");
        $tree = new \Claduipi\Tools\tree;
        $tree->icon = array('│ ', '├─ ', '└─ ');
        $tree->nbsp = '&nbsp;';
        $fenleishtml = "<option value='\$cateid'>\$spacer\$name</option>";
        $tree->init($fenleis);
        $fenleishtml = $tree->get_tree(0, $fenleishtml);
        if ($id) {
            $fenleishtml.='<option value="' . $cateinfo['cateid'] . '" selected="true">' . $cateinfo['name'] . '</option>';
            $info['picarr'] = unserialize($info['picarr']);
            $info['posttime'] = date("Y-m-d H:i:s", $info['posttime']);
            if ($info['title_style']) {
                if (stripos($info['title_style'], "font-weight:") !== false) {
                    $biaoti_bold = 'bold';
                } else {
                    $biaoti_bold = '';
                }
                if (stripos($info['title_style'], "color:") !== false) {
                    $biaoti_color = explode(';', $info['title_style']);
                    $biaoti_color = explode(':', $biaoti_color[0]);
                    $biaoti_color = $biaoti_color[1];
                } else {
                    $biaoti_color = '';
                }
            } else {
                $biaoti_color = '';
                $biaoti_bold = '';
            }
        } else {
            $fenleishtml = '<option value="0">≡ 请选择栏目 ≡</option>' . $fenleishtml;
        }
        if ($cateid) {
            $cateinfo = D("fenlei")->where(array("cateid" => "$cateid"))->find();
            if (!$cateinfo)
                $this->note("参数不正确,没有这个栏目", C("URL_DOMAIN") . '/addarticle');
            $fenleishtml.='<option value="' . $cateinfo['cateid'] . '" selected="true">' . $cateinfo['name'] . '</option>';
        }

        $ment = array(
            array("lists", "内容管理", C("URL_DOMAIN") . "article/article_list"),
            array("insert", "添加文章", C("URL_DOMAIN") . "article/article_edit"),
        );
		
        $this->assign("ment", $ment);
        $this->assign("fenleishtml", $fenleishtml);
        $this->display("admin/article.edit");
    }

    //文章列表
    public function article_list() {
        $ment = array(
            array("lists", "文章管理", C("URL_DOMAIN") . "article/article_list"),
            array("insert", "添加文章", C("URL_DOMAIN") . "article/article_edit"),
        );

        $cateid = I("cateid", 0);
        $list_where = '';
        if (!$cateid) {
            $list_where = "1";
        } else {
            $list_where = "cateid = '$cateid'";
        }
        if (isset($_POST['sososubmit'])) {
            $posttime1 = !empty($_POST['posttime1']) ? strtotime($_POST['posttime1']) : NULL;
            $posttime2 = !empty($_POST['posttime2']) ? strtotime($_POST['posttime2']) : NULL;
            $sotype = $_POST['sotype'];
            $sosotext = $_POST['sosotext'];
            if ($posttime1 && $posttime2) {
                if ($posttime2 < $posttime1)
                    $this->note("结束时间不能小于开始时间");
                $list_where = "posttime > '$posttime1' AND posttime < '$posttime2'";
            }
            if ($posttime1 && empty($posttime2)) {
                $list_where = "posttime > '$posttime1'";
            }
            if ($posttime2 && empty($posttime1)) {
                $list_where = "posttime < '$posttime2'";
            }
            if (empty($posttime1) && empty($posttime2)) {
                $list_where = false;
            }

            if (!empty($sosotext)) {
                if ($sotype == 'cateid') {
                    $sosotext = intval($sosotext);
                    if ($list_where)
                        $list_where .= "AND cateid = '$sosotext'";
                    else
                        $list_where = "cateid = '$sosotext'";
                }
                if ($sotype == 'catename') {
                    $sosotext = htmlspecialchars($sosotext);
                    $info = D("fenlei")->where(array("name" => "$sosotext"))->find();

                    if ($list_where && $info)
                        $list_where .= "AND cateid = '$info[cateid]'";
                    elseif ($info)
                        $list_where = "cateid = '$info[cateid]'";
                    else
                        $list_where = "1";
                }
                if ($sotype == 'title') {
                    $sosotext = htmlspecialchars($sosotext);
                    $list_where = "title = '$sosotext'";
                }
                if ($sotype == 'id') {
                    $sosotext = intval($sosotext);
                    $list_where = "id = '$sosotext'";
                }
            } else {
                if (!$list_where)
                    $list_where = '1';
            }
        }
        $num = 20;
        $zongji = D("wenzhang")->where($list_where)->count();
        $fenye = new \Claduipi\Tools\page;
        if (isset($_GET['p'])) {
            $fenyenum = $_GET['p'];
        } else {
            $fenyenum = 1;
        }
        $fenye->config($zongji, $num, $fenyenum, "0");

        $wenzhanglist = D("wenzhang")->where($list_where)->order("`order` DESC")->limit(($fenyenum - 1) * $num, $num)->select();
        $categorys = $this->key2key(D("fenlei")->order("parentid,cateid")->select(), "cateid");

        $this->assign("categorys", $categorys);
        $this->assign("ment", $ment);
        $this->assign("zongji", $zongji);
        $this->assign("fenye", $fenye);
        $this->assign("wenzhanglist", $wenzhanglist);
        $this->display("admin/article.lists");
    }

    //ajax 删除文章
    public function article_del() {
		$user = $this->getAdminInfo(FALSE);
        if ($user['xianzhi']) {
            
                echo "<script>
				alert('权限不足,请联系总管理员');
				exit;			
				</script>";

exit;
            }
        $id = I("id", 0);
        $res = D("wenzhang")->where(array("id" => "$id"))->delete();
        if ($res) {
            $this->note("文章删除成功", C("URL_DOMAIN") . "/article/article_list");
        } else {
            $this->note("文章删除失败", C("URL_DOMAIN") . "/article/article_list");
        }
    }

    /*
     * 	文章排序
     */

    public function article_listorder() {

        foreach ($_POST['listorders'] as $id => $listorder) {
            $id = intval($id);
            $listorder = intval($listorder);
            D("wenzhang")->where(array("id" => "$id"))->save(array("order" => "$listorder"));
        }
        $this->note("排序更新成功");
    }

    //模型
    public function model() {
        $models = $this->key2key(D("moxing")->select(), "modelid");
        $this->assign("models", $models);
        $this->display("admin/content.model");
    }

}
