<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<link rel="stylesheet" href="/Public/plugin/style/global/css/global.css" type="text/css">
<link rel="stylesheet" href="/Public/plugin/style/global/css/style.css" type="text/css">
<style>
tbody tr{ line-height:30px; height:30px;} 
</style>
</head>
<body>
<div class="header lr10">
	<?php echo R('admin/headerment',array($ment));?>
</div>
<div class="bk10"></div>
<div class="table-list lr10">
<!--start-->
  <table width="100%" cellspacing="0">
    <thead>
		<tr>
		<th width="80px">id</th>
		<th width="" align="center">图片</th>
		<th width="30%" align="center">操作</th>
		</tr>
    </thead>
    <tbody>
	<?php if(is_array($lists)): foreach($lists as $key=>$v): ?><tr>
			<td align="center"><?php echo $v['id']; ?></td>
			<td align="center"><img height="50px" src="/Public/uploads/<?php echo $v['img']; ?>"/></td>
			<td align="center">
				<a href="/Invite/update/id/<?php echo $v['id'];?>">修改</a>
				<a href="/Invite/delete/id/<?php echo $v['id'];?>">删除</a>
			</td>	
		</tr><?php endforeach; endif; ?>
  	</tbody>
</table>
	<div class="btn_paixu"></div>
</div><!--table-list end-->

<script>
</script>
</body>
</html>