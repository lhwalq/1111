<?php

//中英文切换需要的文件,似乎只要第一行app_begin就可以了
return array(
    // 添加下面一行定义即可
    'app_begin'        => array('Behavior\CheckLangBehavior'),   //注意这里，官方的文档解释感觉有误（大家自行分辨），TP3.2.3用Behavior\CheckLang会出错，提示：Class 'Behavior\CheckLang' not found
    
);