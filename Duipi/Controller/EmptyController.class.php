<?php

/**
 * 空控制器 
 * addtime 20160627
 */

namespace Duipi\Controller;

use Think\Controller;

class EmptyController extends Controller {

    function index() {
        header('HTTP/1.1 404 Not Found'); 
header('status: 404 Not Found'); //404状态码  
        $this->display("public/404");
    }

}
