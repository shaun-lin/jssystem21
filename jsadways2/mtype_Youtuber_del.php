<?php
	session_start();
	include('include/db.inc.php');
	$sql2='DELETE FROM media166_detail WHERE id='.$_GET['id'];
    $result2=mysql_query($sql2); 
	// if($_GET['edit']==1){
	// 	echo '<meta http-equiv=REFRESH CONTENT=1;url=mtype_Youtuber_edit.php?campaign='.$_GET['campaign'].'&id='.$_GET['editid'].'>';
	// }else{
	// 	echo '<meta http-equiv=REFRESH CONTENT=2;url=mtype_Youtuber_add.php?id='.$_GET['campaign'].'>';
	// }
	$arrItems = array();
    $arrItems[] = array("key" => "result", "name" => "OK");

    echo json_encode($arrItems);
?>
