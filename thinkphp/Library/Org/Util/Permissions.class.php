<?php

namespace Org\Util;

use Think\Controller;

class Permissions {

    public static function Check($root, $note, $info = '你没有权限!') {
        $res = array();
        $bool = self::checkPermissions($root, $note);
        $res['status'] = 0;
        if ($bool == 0) {
            $res['info'] = $info;
            echo json_encode($res);
            exit();
        }
        if ($bool == -1) {
            $res['info'] = '请先登录!';
            echo json_encode($res);
            exit();
        }
    }

    //检查权限
    function checkPermissions($controll, $node = '') {

        $session = session('user');
        $rightSet = $session['rightSet'];

        if (!$session) {
            //没有登录
            return -1;
        }
        //如果为超级管理员返回true
        if (isset($rightSet['all'])) {
            return 1;
        }
        //模块权限有了  返回true
        if ($rightSet[$controll]) {
            return 1;
        }
        //判断节点权限
        $node = $controll . '.' . $node;
        if ($rightSet[$node]) {
            return 1;
        }
        return 0;
    }
    /*
     * 返回编号
     * CT 出库，RK 入库，KY 移仓，PD盘点
     */
    function GetBianHaoEx($m) {
        $model = new \Think\Model();
        //echo mktime(0, 0, 0, date('m'), date('d'), date('Y'));

         //取得前面两位
        $res = $m . '-';
        //时间六位
        $res = $res . date('y') . date('m') . date('d') . '-';
        //随机5位
        $temp = rand(1, 99999);
        $res = $res . substr(strval($temp + 100000), 1, 5) . '-';
        switch ($m) {
            case "CT":
                $data = $model->table("jxc_b_Stock_StockOut")->order("sDate DESC")->field('BillID,sDate')->where(" sDate>'" . date('Y-m-d') . "'")->find();
                break;
            case "RK":
                $data=$model->table("buy")->order("selldate DESC")->where(" selldate>'" . date('Y-m-d') . "'")->find();
                break;
            case "KY":
                $data=$model->table("jxc_b_Stock_StockNO")->order("sDate DESC")->field('BillID,sDate')->where(" sDate>'" . date('Y-m-d') . "'")->find();
                print_r($data);
                break;
            case "PD":
                $data=$model->table('jxc_check_stock')->order('createdate DESC')->field('bianhao,createdate')->where("createdate>'".date('Y-m-d') . "'")->find();
                break;
            default:
                break;
            
        }
       
        $temp="";
        if ($data != NULL) {
            switch ($m) {
                case "CT":
                    $temp = substr($data['BillID'], -5) + 1;
                    break;
                case "RK":
                    $temp = substr($data['bianhao'], -5) + 1;
                    break;
                case "PD":
                    $temp = substr($data['bianhao'], -5) + 1;
                    break;;
                case "KY":
                     $temp = substr($data['BillID'], -5) + 1;
                default:
                    $temp=1;
                    break;
            }
           
            $res = $res . substr(strval($temp + 100000), 1, 5);
        } else {
            $res = $res . '00001';
        }
       
        return $res;
    }

}
