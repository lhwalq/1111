<?php
/*
 * 出库Model
 */
namespace Duipi\Model;
use Think\Model;
class StockoutModel extends Model {

    protected $trueTableName = 'jxc_b_Stock_StockOut';
    protected $fields = array('StockOutID', 'BillID', 'WareHouseID', 'sDate', 'Operator', 'GoodsTotal', 'OtherFee', 'FlagID', 'Remark', 'CHKTime', 'CHKOperator', 'CurStatus', 'OperationID', 'theCause', 'Summary', 'WriteOffType', 'WriteOffID', 'OperationType', 'bGoodsWriteOff');
    protected $_validate = array(
        array('WareHouseID', 'number', '必须为数字！'), //默认情况下用正则进行验证
    );

    /**
     * 写入出库信息
     * @param object $stockOut
     * @return boolean 
     */
    function addStockOut($stockOut) {

        $stockOut->sDate = date("Y-m-d H:i:s");
        $sesion=  session('user');
        $stockOut->Operator=  $sesion['username'];
        $per = new \Org\Util\Permissions();
        $stockOut->BillID=$per->GetBianHaoEx("CT");
        //自动验证
        if (!$this->create($stockOut)) {
            return false;
        }
        
        $this->startTrans(); //开启事务
        $stockOutRes = $this->add();
        if ($stockOutRes) {
            
            $stockOutDetails = array();
            $StockoutDetail = D('StockOutDetail');
            foreach ($stockOut->product as $key => $value) {
                $value['BillID'] = $stockOutRes;
                $value['Price']=0;
                $value['SpecID']=$value['SpecID']!=""?$value['SpecID']:"0";
                if (!$StockoutDetail->create($value)) {
                    $this->rollback(); //回滚
                    return FALSE;
                }
                $stockOutDetails[] = $value;
              
            }
            $stockOutDetailRes = $StockoutDetail->addAll($stockOutDetails);
            if ($stockOutDetailRes) {
                $this->commit(); //如果都成功 提交事务
                return $stockOutRes;
            } else {
                $this->rollback(); //回滚
                return FALSE;
            }
        } else {
            $this->rollback(); //回滚
            return FALSE;
        }
    }
    
   

}
