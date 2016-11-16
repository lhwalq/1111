<?php
   
    header("Content-Type: text/html; charset=utf-8");
    error_reporting( E_ERROR | E_WARNING );
	exit;
    include "Uploader.class.php";
  
    $config = array(
        "savePath" => "upload/" , 
        "allowFiles" => array( ".rar" , ".doc" , ".docx" , ".zip" , ".pdf" , ".txt" , ".swf" , ".wmv" ) , //文件允许格式
        "maxSize" => 100000 //文件大小限制，单位KB
    );
    
    $up = new Uploader( "upfile" , $config );

    
    $info = $up->getFileInfo();

    
    echo '{"url":"' .$info[ "url" ] . '","fileType":"' . $info[ "type" ] . '","original":"' . $info[ "originalName" ] . '","state":"' . $info["state"] . '"}';

