<?php
namespace Org\Util;
class SqlSrvDB {

    //保存类实例的静态成员变量
    private static $_instance;
    public static $_conn;
    private function __construct() {
        if(C('DB_PORT')==''){
            $serverName = C('DB_HOST');
        }else{
            $serverName = C('DB_HOST').','.C('DB_PORT');
       
        } 
        $connectionInfo = array("UID" => C('DB_USER'), "PWD" => C('DB_PWD'), "Database" => C('DB_NAME'));
        self::$_conn = sqlsrv_connect($serverName, $connectionInfo);
    }

    public static function getInstance() {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }
}
