<?php
	session_start();
	include('include/db.inc.php');
	// print_r($_POST);
	// exit;
	if($_POST['price1']!=null){
		$type=$type.'BLOG文章價格、';
	}
	if($_POST['price2']!=null){
		$type=$type.'fb文章連結轉po費用、';
	}
	if($_POST['price3']!=null){
		$type=$type.'官網識別圖引用費、';
	}
	if($_POST['price4']!=null){
		$type=$type.'出席費、';
	}
	if($_POST['price5']!=null){
		$type=$type.'fb操作費、';
	}
	if($_POST['price6']!=null){
		$type=$type.'平面廣編引用費、';
	}
	if($_POST['price7']!=null){
		$type=$type.'網路全平台引用費、';
	}
	if($_POST['price8']!=null){
		$type=$type.'棚拍費、';
	}
	if($_POST['price9']!=null){
		$type=$type.'Youtuber費、';
	}
	$id = $_POST['id'];
	if($_POST['isEdit']=='Y')
	{
		$id = $_POST['campaign'];
	}
	$sql2='INSERT INTO media166_detail(campaign_id,blogid,blog,blog1,blog2,blog3,type,price,price2,price3,times,others,status) VALUES("'.$id.'","'.$_POST['blogid'].'","'.$_POST['blog'].'","'.$_POST['blog1'].'","'.$_POST['blog2'].'","'.$_POST['blog3'].'","'.$type.'",'.$_POST['totalprice'].','.$_POST['totalprice2'].','.$_POST['totalprice3'].','.time().',"'.$_POST['others'].'",0)';

	$result = mysql_query($sql2) or die(mysql_error());
	AddMediaMapping("media166", $_GET['id'], mysql_insert_id());

    $arrItems = array();
    $arrItems[] = array("key" => "result", "name" => "OK");

    echo json_encode($arrItems);
?>
