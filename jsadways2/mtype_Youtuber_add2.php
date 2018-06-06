<?php
	
	require_once dirname(__DIR__) .'/autoload.php';
	
	include('include/db.inc.php');
	
	$db = clone($GLOBALS['app']->db);

	$TypeItem = $_POST['SelectType'];
	$cp_id = $_GET['id'];
	$media_id = $_GET['mediaid'];
	$item_id = $_GET['itemid'];
	$mtype_name = $_GET['mtypename'];
	$mtype_number = $_GET['mtypenumber'];
	$mtype_id = $_GET['mtypeid'];

	$sql2='INSERT INTO media166(campaign_id,cue,website,totalprice,totalprice2,totalprice3,times,others,status,items2,items3) VALUES('.$_GET['id'].',1,"Youtuber費","'.$_POST['totalprice'].'","'.$_POST['totalprice2'].'","'.$_POST['totalprice3'].'",'.time().',"'.$_POST['others'].'",0,"'.$TypeItem.'","'.$_POST['SelectSystem'].'")';
	mysql_query($sql2);
	AddMediaMapping('media166', $_GET['id'], mysql_insert_id());
	$sqlnew = "SELECT * FROM media166 ORDER BY id DESC LIMIT 1;";
	$resultnew = mysql_query($sqlnew);
	$rownew = mysql_fetch_array($resultnew);
	$a0=$rownew['id'];
	if($_POST['totalprice']<($_POST['totalprice2']*1.25)){
		$a5=1;
	}else{
		$a5='';
	}
	$sql2='INSERT INTO media166(campaign_id,cue,website,totalprice,a4,a3,a0,a,a5,times,others,status,items2,items3) VALUES('.$_GET['id'].',2,"Youtuber費","'.$_POST['totalprice'].'","'.$_POST['totalprice2'].'","'.$_POST['totalprice3'].'","'.$a0.'","166","'.$a5.'",'.time().',"'.$_POST['others'].'",0,"'.$TypeItem.'","'.$_POST['SelectSystem'].'")';
	mysql_query($sql2);
	AddMediaMapping('media166', $_GET['id'], mysql_insert_id());
	


	$goon=GetVar('goon');
	

	$sql3 = "INSERT INTO `cp_detail`( `cp_id`, `media_id`, `comp_id`, `item_id`, `mtype_name`, `mtype_number`, `mtype_id`) VALUES ('".$cp_id."','".$media_id."','0','".$item_id."','".$mtype_name."','".$mtype_number."','".$mtype_id."')";
	$db->query($sql3);
		if ($goon=="Y") {

		$arrItems=array();
				$arrItems[]=array("key"=>"result","name"=>"OK");
		
		echo json_encode($arrItems);
	}else{
	ShowMessageAndRedirect('新增媒體成功', 'campaign_view.php?id='. $_GET['id'], false);
	}
	