<?php
error_reporting(0);
isset($_POST['url'])?$url = $_POST['url']:$url=null;
$kuozhanming = explode(".",$url);
$file_ext = $kuozhanming['1'];//图片扩展名
$url = addslashes($url);
$url2 = str_replace(".","a.",$url);
    $filename = "../..".$url;//图片路径
    $filename2 = "../..".$url2;//新图片路径
    $width="100";//图片尺寸像素(宽)
    $height="100";//图片尺寸像素(高)
    $zoom="1";//是否按照原比例，1为原比例，2为以上$width and $height的固定尺寸
  /*获取原图的大小*/
  list($width_orig,$height_orig) = getimagesize($filename);

if($zoom=="1"){ 
/*根据参数$width和$height，换算出等比例的高度和宽度*/
if($width && ($width_orig < $height_orig)){
$width = ($height / $height_orig) * $width_orig;
}else{
$height = ($width / $width_orig) * $height_orig;
}}
  /*以新的大小创建画布*/
  $image_p = imagecreatetruecolor($width, $height);
  /*获取图像资源*/
  if($file_ext=="jpg"){
  $image = imagecreatefromjpeg($filename);
  }
  if($file_ext=="gif"){
  $image = imagecreatefromgif($filename);
  }
  if($file_ext=="png"){
  $image = imagecreatefrompng($filename);
  }
  /*进行缩放*/
  imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
  /*保存缩放后的图片和命名*/
  
  if($file_ext=="jpg"){
  return imagejpeg($image_p,$filename2);
  }
  if($file_ext=="gif"){
  return imagegif($image_p,$filename2);
  }
  if($file_ext=="png"){
  return imagepng($image_p,$filename2);
  }
  /*释放*/
  imagedestroy($image_p);
  imagedestroy($image);
?>