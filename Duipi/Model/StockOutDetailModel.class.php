<?php
/*
 * 出口Item的model
 */
namespace Duipi\Model;
use Think\Model;
class StockoutDetailModel extends Model {

    protected $trueTableName = 'jxc_b_Stock_StockOutDetail';
    protected $_validate = array(
        array('GoodsID', 'number', '必须为数字！'), //默认情况下用正则进行验证
        array('BillID', 'number', '必须为数字！'),
        array('GoodsCount', 'number', '必须为数字！'),
        array('SpecID', 'number', '必须为数字！'),
        array('sRecID', 'number', '必须为数字！'),
    );

}
