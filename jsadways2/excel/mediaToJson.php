<?php
header("Content-Type:text/html; charset=utf-8");
include('../include/db.inc.php');

$type = $_POST['type'];
$json_ary = array();
$sql_media='SELECT * FROM media where id > 0 and type in ('.$type.') and display = 1 ';
$result_media=mysql_query($sql_media); 
if (mysql_num_rows($result_media)>0){
	while($row_media=mysql_fetch_array($result_media)){
		$json_ary[] = array('id' => $row_media["id"],'name' => urlencode($row_media['name'].'【'.$row_media['costper'].'】'));
	}
}
echo urldecode(json_encode($json_ary));

?>