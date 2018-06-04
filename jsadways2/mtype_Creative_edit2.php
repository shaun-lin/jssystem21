<?php
	
	require_once dirname(__DIR__) .'/autoload.php';
	
	include('include/db.inc.php');
	$TypeItem=$_POST['SelectType'];
	$TypeItem = $_POST['SelectType'];
	$totalprice=$_POST['quantity']*$_POST['price'];
	$sql1 = "SELECT * FROM campaign WHERE id= ".$_GET['campaign'];
	$result1 = mysql_query($sql1);
	$row1 = mysql_fetch_array($result1);
	if($row1['status']==5){
		/*$sql2='UPDATE campaign SET status=1 WHERE id ='.$_GET['campaign'];
		mysql_query($sql2);
		$sql2='INSERT INTO campaignstatus(name,data,times,campaignid) VALUES("'.$_SESSION['name'].'","案件狀態由暫停轉成尚未送審",'.time().','.$_GET['campaign'].')';
		mysql_query($sql2);*/
		$sql3 = "SELECT * FROM media161 WHERE id= ".$_GET['id'];
		$result3 = mysql_query($sql3);
		$row3 = mysql_fetch_array($result3);
		if($row3['totalprice']!=$totalprice){
			$data='廣告素材媒體總金額由'.$row3['totalprice'].'改成'.$_POST['totalprice'].'<br />';
		}

		if($data==NULL){
			$data='廣告素材媒體我也不知道改了什麼';
		}
		$sql2='INSERT INTO campaignstatus2(name,data,times,campaignid) VALUES("'.$_SESSION['username'].'","'.$data.'",'.time().','.$_GET['campaign'].')';
		mysql_query($sql2);
	}

	if($_POST['gearing']==1){
		$sql_campaign = "SELECT * FROM media161 where campaign_id	= ".$_GET['campaign']; 
		$campaign = mysql_query($sql_campaign);
		$campaign_row = mysql_num_rows($campaign);
		if($campaign_row == 2){
			$sql2='UPDATE media18 SET itemname="'.$_POST['itemname'].'" , price="'.$_POST['price'].'" , quantity="'.$_POST['quantity'].'" , totalprice="'.$totalprice.'" ,a4="'.$totalprice.'", other="'.$_POST['other'].'", times='.time().',items2="'.$TypeItem.'",items3="'.$_POST['SelectSystem'].'" WHERE campaign_id	= '.$_GET['campaign']; 
			mysql_query($sql2);
		}

	}

	$sql2='UPDATE media161 SET itemname="'.$_POST['itemname'].'" , price="'.$_POST['price'].'" , quantity="'.$_POST['quantity'].'" , totalprice="'.$totalprice.'" ,a4="'.$totalprice.'", other="'.$_POST['other'].'", times='.time().',items2="'.$TypeItem.'",items3="'.$_POST['SelectSystem'].'"  WHERE id ='.$_GET['id'];
	mysql_query($sql2);
	
	ShowMessageAndRedirect('修改媒體成功', 'campaign_view.php?id='. $_GET['campaign'], false);
	