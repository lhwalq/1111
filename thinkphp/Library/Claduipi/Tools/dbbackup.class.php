<?php

namespace Claduipi\Tools;

class dbbackup {

    private $pdo = "";
    private $host = "";
    private $dbname = "";
    private $user = "";
    private $password = "";

//构造函数
    function __construct() {
        $this->host = C('DB_HOST');
        $this->dbname = C('DB_NAME');
        $this->user = C('DB_USER');
        $this->password = C('DB_PWD');
        try {
            $this->pdo = new \PDO("mysql:host = $this->host;dbname = $this->dbname", $this->user, $this->password); //连接数据库
            $this->pdo->query("use {$this->dbname};");
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION); //设置调优参数，遇到错误抛出异常
        } catch (PDOException $e) {
            echo $e->getMessage(); //如果连接异常则抛出错误信息
            exit;
        }
    }

    /**
     * 获去数据库表
     */
    public function getList() {
        $arr = array();
        $statments = $this->pdo->query("show tables"); //返回结果集，show tables检视所有表名
        $this->pdo->query("SET NAMES UTF8");
        foreach ($statments as $value) {//遍历此结果集，导出每个表名对应的信息
            $table_name = $value[0]; //获取该表名
            $table_query = $this->pdo->query("SHOW TABLE STATUS LIKE '$table_name'");
            $create_sql = $table_query->fetch();
            $array = array(
                "rows" => $create_sql['Rows'],
                "collation" => $create_sql['Collation'],
                "comment" => $create_sql['Comment'],
                "name" => $create_sql['Name'],
                "size" => ceil($create_sql['Data_length'] / 1024),
            );
            array_push($arr, $array);
        }
        return $arr;
    }

    /*
     * 数据库导出函数outputSql
     * 用PDO方式导出数据库数据
     * $host 主机名 如localhost
     * $dbname 数据库名
     * $user 用户名
     * $password 密码
     * $flag 标志位0或1 0为仅导出数据库结构 1为导出数据库结构和数据 默认为1
     */

    public function outputSql($name, $flag = 1) {
        $mysql = "";
        //$mysql = "DROP DATABASE IF EXISTS {$this->dbname};\n"; //$mysql装载sql语句，这里如果存在数据库则drop该数据库
        $creat_db = $this->pdo->query("show create database {$this->dbname}")->fetch(); //用show create database查看sql语句
        preg_match('/DEFAULT CHARACTER SET(.*)\*/', $creat_db['Create Database'], $matches); //正则取出DEFAULT CHARACTER SET 后面的字符集
        //$mysql.="CREATE DATABASE {$this->dbname} DEFAULT CHARACTER SET $matches[1]"; //该语句如CREATE DATABASE `test_db` DEFAULT CHARACTER SET utf8
        /* 查找该数据库的字符整序如COLLATE utf8_general_ci */
        $db_collate = $this->pdo->query("SELECT DEFAULT_COLLATION_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME ='{$this->dbname}' LIMIT 1")->fetch();
        // $mysql.="COLLATE " . $db_collate['DEFAULT_COLLATION_NAME'] . ";\nUSE {$this->dbname};\n\n";
        //$mysql.="USE {$this->dbname};\n\n";
        $statments = $this->pdo->query("show tables"); //返回结果集，show tables检视所有表名
        //dump($statments);exit;
        foreach ($statments as $value) {//遍历此结果集，导出每个表名对应的信息
            $table_name = $value[0]; //获取该表名
            if (!in_array($table_name, $name)) {
                continue;
            }
            $mysql.="DROP TABLE IF EXISTS $table_name;\n"; //每个表前都准备Drop语句
            $table_query = $this->pdo->query("show create table $table_name"); //取出该表建表信息的结果集
            $create_sql = $table_query->fetch(); //利用fetch方法取出该结果集对应的数组
            $mysql.=$create_sql['Create Table'] . ";\r\n\r\n"; //写入建表信息
            if ($flag != 0) {//如果标志位不是0则继续取出该表内容生成insert语句
                $iteams_query = $this->pdo->query("select * from $table_name"); //取出该表所有字段结果集
                $values = ""; //准备空字符串装载insert value值
                $items = ""; //准备空字符串装载该表字段名
                while ($item_query = $iteams_query->fetch(\PDO::FETCH_ASSOC)) { //用关联查询方式返回表中字段名和值的数组
                    $item_names = array_keys($item_query); //取出该数组键值 即字段名
                    $item_names = array_map("addslashes", $item_names); //将特殊字符转译加\
                    $items = join('`,`', $item_names); //联合字段名 如：items1`,`item2 `符号为反引号 键盘1旁边 字段名用反引号括起
                    $item_values = array_values($item_query); //取出该数组值 即字段对应的值
                    $item_values = array_map("addslashes", $item_values); //将特殊字符转译加\
                    $value_string = join("','", $item_values); //联合值 如：value1′,'value2 值用单引号括起
                    $value_string = "('" . $value_string . "'),"; //值两边加括号
                    $values = "INSERT INTO `$table_name` (`$items`) VALUES" . rtrim($value_string, ",") . ";\n\r";
                    $mysql.=$values;
                    //$values.="\n" . $value_string; //最后返回给$value
                }
//                if ($values != "") {
//                    //如果$values不为空，即该表有内容
//                    //写入insert语句
//                    $insert_sql = $values;
////                    $insert_sql = "INSERT INTO `$table_name` (`$items`) VALUES" . rtrim($values, ",") . ";\n\r";
//                    //将该语句写入$mysql
//                    $mysql.=$insert_sql;
//                }
            }
        }
        return $mysql;
    }

}
