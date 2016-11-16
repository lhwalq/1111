<?php
  namespace Duipi\Model;
    use Think\Model;   
class LogModel extends Model{
    protected $trueTableName = 'jxc_b_log';
    
    protected $_validate = array(
        array('ActionDate', 'require', '时间必须'), //默认情况下用正则进行验证
     
    );
}