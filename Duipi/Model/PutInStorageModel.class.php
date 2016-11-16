<?php
namespace Duipi\Model;
use Think\Model;   
class PutInStorageModel extends Model{
    
    protected $trueTableName = 'buy';
    
    protected $_validate = array(
//        array('ActionDate', 'require', '时间必须'), //默认情况下用正则进行验证
     
    );
}