<?php
define("APP_DEBUG", TRUE);
// 默认绑定Home模块--注意这里
define('BIND_MODULE', 'Duipi');
//定义一个字符串，接受存储过程的返回值
define('__HTTP__', isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://');
define('__HOST__', (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''));
//define("__PUBLIC__", __HOST__ . $_SERVER['SCRIPT_NAME'] . '/public/uploads');
define("__PUBLIC__", dirname(__FILE__) . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR);
define('PAGE_SIZE', 20); //定义一页显示20条数据
//define("LOCAL_PATH ", __HOST__);
require 'ThinkPHP/ThinkPHP.php';
?>

