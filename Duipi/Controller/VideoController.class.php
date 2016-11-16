<?php

/**
 * ajax1
 * addtime 2016/07/18
 */

namespace Duipi\Controller;

use Think\Controller;

class VideoController extends BaseController {

    public $userinfo;
    private $Mcartlist;
    private $Mcartlistzg;

    public function _initialize() {
        //查询购物车的信息
        $Mcartlist = cookie("Cartlist");
        $Mcartlistzg = cookie("Cartlistzg");
        $this->Mcartlist = json_decode(stripslashes($Mcartlist), true);
        $this->Mcartlistzg = json_decode(stripslashes($Mcartlistzg), true);
        $this->userinfo = $this->getUserInfo();
    }

    //订单详情
  public function videoid() {
	  $id=I('id');
$wenzhanglist = D("video")->where("id=$id")->order("`id` DESC")->find();
$video = D("video")->order("id desc")->limit("5")->select();

$this->assign('link',$wenzhanglist['link']);
$this->assign('title',$wenzhanglist[title]);

$this->assign('video',$video);
        $this->display("index.videoid");
 
  }
  public function mvideo() {
	  $id=I('id');
//$wenzhanglist = D("video")->where("id=$id")->order("`id` DESC")->find();
$video = D("video")->order("id desc")->limit("5")->select();

$this->assign('link',$wenzhanglist['link']);
$this->assign('title',$wenzhanglist[title]);

$this->assign('video',$video);
        $this->display("index.mvideo");
 
  }

   public function mvideos() {
	 $cate_band = I("sortid", 0);
        $select = I("orderFlag", 0);
        $kaishi = I("fIdx", 0);
        $jieshu = I("eIdx", 0);
        $glist = I("glist", 0);
        $p = $kaishi;
		$end = $jieshu;
            $star = ($p - 1);
$yyslist = D("video")->order("id desc")->limit($star, $end)->select();



if ($yyslist) {
            $yyslist1['code'] = 0;
            $yyslist1['count'] = count($yyslist);
            foreach ($yyslist as $key => $val) {
				
                $yyslist1['listItems'][$key]['link'] = $val['link'];
                $yyslist1['listItems'][$key]['goodsid'] = $val['id'];
                $yyslist1['listItems'][$key]['goodssnme'] = $val['title'];
                $yyslist1['listItems'][$key]['goodspic'] = $val['img'];
                $yyslist1['listItems'][$key]['codeid'] = $val['id'];
                $yyslist1['listItems'][$key]['codeprice'] = $val['money'];
                $yyslist1['listItems'][$key]['codequantity'] = $val['zongrenshu'];
                $yyslist1['listItems'][$key]['codesales'] = $val['canyurenshu'];
                $yyslist1['listItems'][$key]['codeperiod'] = $val['qishu'];
                $yyslist1['listItems'][$key]['codetype'] = 0;
                $yyslist1['listItems'][$key]['goodstag'] = 0;
                $yyslist1['listItems'][$key]['codelimitbuy'] = 0;
            }
        } else {
            $yyslist1['code'] = 1;
        }

echo json_encode($yyslist1);
 
  }


    public function video() {
        
       $video = D("video")->order("`id` DESC")->select();
	 

	   $yys[msg][total]=count($video);
		   $yys[msg][name]='全部视频';
		   $yys[msg][perPage]=9;
  foreach ($video as $key=>$val){
	 // var_dump($val[link]);
		   $yys[tjv][0][v_link]=$val[link];
		   $yys[tjv][0][v_id]=$val[id];
		   	 $yys[tjv][0][v_title]=$val[title];
	$yyss[tjv][0][v_content]= $val[link];
           $yys[tjv][0][v_hits]= "780";
            $yys[tjv][0][v_img]= null;
             $yys[tjv][0][v_time]= time();
             $yys[tjv][0][v_back]= "0";
             $yys[tjv][0][v_open]= "1";
             $yys[tjv][0][sort_id]= null;
             $yys[tjv][0][zan]= "29";
             $yys[tjv][0][v_note]= "4567";
             $yys[tjv][0][v_hot]= "1";
             $yys[tjv][0][cai]= "0";
             $yys[tjv][0][v_author]= null;
             $yys[tjv][0][v_gz]= "0";
             $yys[tjv][0][v_bs]= "0";
             $yys[tjv][0][v_kx]= "0";
             $yys[tjv][0][v_fn]= "0";
             $yys[tjv][0][v_kl]= "0";
             $yys[tjv][0][v_cover]= 'public/uploads/'.$val[img];
             $yys[tjv][0][v_type]= "3";
if($key>0){
			$yys[vhits][$key][v_link]=$val[link];
		   $yys[vhits][$key][v_id]=$val[id];
		   	 $yys[vhits][$key][v_title]=$val[title];
				$yys[vhits][$key][v_content]= $val[link];
				$yys[vhits][$key][v_hits]= "780";
            $yys[vhits][$key][v_img]= null;
             $yys[vhits][$key][v_time]= "1468475616";
             $yys[vhits][$key][v_back]= "0";
             $yys[vhits][$key][v_open]= "1";
             $yys[vhits][$key][sort_id]= null;
             $yys[vhits][$key][zan]= "29";
             $yys[vhits][$key][v_note]= "7777";
             $yys[vhits][$key][v_hot]= "1";
             $yys[vhits][$key][cai]= "0";
             $yys[vhits][$key][v_author]= null;
             $yys[vhits][$key][v_gz]= "0";
             $yys[vhits][$key][v_bs]= "0";
             $yys[vhits][$key][v_kx]= "0";
             $yys[vhits][$key][v_fn]= "0";
             $yys[vhits][$key][v_kl]= "0";
             $yys[vhits][$key][v_cover]= 'public/uploads/'.$val[img];
             $yys[vhits][$key][v_type]= "3";

			  $yys[data][$key][v_link]=$val[link];
		   $yys[data][$key][v_id]=$val[id];
		   	 $yys[data][$key][v_title]=$val[title];
	$yys[data][$key][v_content]= $val[link];
           $yys[data][$key][v_hits]= "780";
            $yys[data][$key][v_img]= null;
             $yys[data][$key][v_time]= "1468475616";
             $yys[data][$key][v_back]= "0";
             $yys[data][$key][v_open]= "1";
             $yys[data][$key][sort_id]= null;
             $yys[data][$key][zan]= "29";
             $yys[data][$key][v_note]= "234";
             $yys[data][$key][v_hot]= "1";
             $yys[data][$key][cai]= "0";
             $yys[data][$key][v_author]= null;
             $yys[data][$key][v_gz]= "0";
             $yys[data][$key][v_bs]= "0";
             $yys[data][$key][v_kx]= "0";
             $yys[data][$key][v_fn]= "0";
             $yys[data][$key][v_kl]= "0";
             $yys[data][$key][v_cover]= 'public/uploads/'.$val[img];
             $yys[data][$key][v_type]= "3";
			 $yys[data][$key][type_name]="cccc";
}
			 
	   }
        echo json_encode($yys);
    }

  
		
	}


