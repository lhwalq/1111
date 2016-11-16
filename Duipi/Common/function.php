<?php

/**
 * 获取支付方式名称
 */
function getPayName($pay_id) {
    if ($pay_id) {
        $pay = D("payment")->where(array("pay_id" => $pay_id))->find();
    }
    return $pay ? $pay['pay_name'] : "余额支付";
}

/**
 * 获取订单状态
 */
function getOrderStatus($status) {
    $str = "";
    switch ($status) {
        case 0:
            $str = "未付款，未发货";
            break;
        case 1:
            $str = "已付款，未发货";
            break;
        case 2:
            $str = "已付款，未发货";
            break;
        case 3:
            $str = "已付款，已发货";
            break;
        case 4:
            $str = "已付款，已收货";
            break;
        case 5:
            $str = "已付款，已完成";
            break;
        case 6:
            $str = "已取消";
            break;
    }
    return $str;
}

