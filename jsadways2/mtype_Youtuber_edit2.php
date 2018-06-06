<?php
	
	require_once dirname(__DIR__) .'/autoload.php';
	
	include('include/db.inc.php');
	$TypeItem=$_POST['SelectType'];
	$TypeItem = $_POST['SelectType'];

	if($_POST['gearing']==1){
		$sql_campaign = "SELECT * FROM media166 where campaign_id = ".$_GET['campaign']; 
		$campaign = mysql_query($sql_campaign);
		$campaign_row = mysql_num_rows($campaign);
		if($campaign_row == 2){
			$sql2='UPDATE media166 SET totalprice="'.$_POST['totalprice'].'" ,totalprice2="'.$_POST['totalprice2'].'" ,totalprice3="'.$_POST['totalprice3'].'" , others="'.$_POST['others'].'", times='.time().',items2="'.$TypeItem.'",items3="'.$_POST['SelectSystem'].'"  WHERE campaign_id = '.$_GET['campaign']; 
			mysql_query($sql2);
		}
	}

	if($_GET['cue']==1){
		$sql2='UPDATE media166 SET totalprice="'.$_POST['totalprice'].'" ,totalprice2="'.$_POST['totalprice2'].'" ,totalprice3="'.$_POST['totalprice3'].'" , others="'.$_POST['others'].'", times='.time().',items2="'.$TypeItem.'",items3="'.$_POST['SelectSystem'].'"  WHERE campaign_id ='.$_GET['campaign'].' AND cue=1';
		mysql_query($sql2);
	}
	if($_POST['totalprice']<($_POST['totalprice2']*1.25)){
		$a5=1;
	}else{
		$a5='';
	}
	if($_GET['cue']==2){
		$sql2='UPDATE media166 SET totalprice="'.$_POST['totalprice'].'" ,a4="'.$_POST['totalprice2'].'" ,a3="'.$_POST['totalprice3'].'" ,a5="'.$a5.'", others="'.$_POST['others'].'", times='.time().',items2="'.$TypeItem.'",items3="'.$_POST['SelectSystem'].'"  WHERE campaign_id ='.$_GET['campaign'].' AND cue=2';
		mysql_query($sql2);
	}
	$sql1 = "SELECT * FROM campaign WHERE id= ".$_GET['campaign'];
	$result1 = mysql_query($sql1);
	$row1 = mysql_fetch_array($result1);
	
	ShowMessageAndRedirect('修改媒體成功', 'campaign_view.php?id='. $_GET['campaign'], false);
	