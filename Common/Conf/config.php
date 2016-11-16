<?php

return array(
   // 'SHOW_PAGE_TRACE' => true, // 显示页面Trace信息   开启后上传图片会报错
    'SHOW_ERROR_MSG' => false, // 显示错误信息
    /* 数据库设置 */
    'DB_TYPE' => 'mysql', // 数据库类型
	 'DB_BACKUP' => "backup/database/",
    'DB_HOST'=>'localhost',//'配置项'=>'配置值'
	'DB_NAME'=>'yyygcms',
	'DB_USER'=>'root',
	'DB_PWD'=>'123456',
    'URL_DOMAIN' => "http://www.yyg.com/",
    'DB_PORT'=>'3306',
    'DB_PREFIX' => 'yys_', // 数据库表前缀
    'DB_PARAMS' => array(\PDO::ATTR_CASE => \PDO::CASE_NATURAL), //配置为数据字段默认
    'DEFAULT_CONTROLLER' => 'Index', // 默认控制器名称
    'DEFAULT_Action' => 'index', // 默认控制器名称
    'URL_HTML_SUFFIX' => 'html',
    'SESSION_AUTO_START' => false, //不自动开启session  网站没用到session  开启机器人时会卡  需要关闭
    'DEFAULT_MODULE' => 'Home', // 默认模块
);
