<?php
	
	require_once dirname(__DIR__) .'/autoload.php';
	
	include('include/db.inc.php');
	

	$TypeItem=null;
	$TypeItem = $_POST['SelectType'];

	$total_days = 0;
	$days = array(0,0,0,0,0);
	for($i=1;$i<=5;$i++){
		if($_POST['date'.$i]!=NULL){
			$date[$i]=mktime (0,0,0,substr($_POST['date'.$i],0,2),substr($_POST['date'.$i],3,2),substr($_POST['date'.$i],6,4));
			$total_days++;
			$days[$i-1] = 1;
		}else{
			$date[$i]=0;
		}
	}
	

	if($_GET['cue']==2){
		$a0=$_GET['mediaid'];
		
	}
	$sql1 = "SELECT * FROM campaign WHERE id= ".$_GET['campaign'];
	$result1 = mysql_query($sql1);
	$row1 = mysql_fetch_array($result1);
	if($row1['status']==5){
		/*$sql2='UPDATE campaign SET status=1 WHERE id ='.$_GET['campaign'];
		mysql_query($sql2);
		$sql2='INSERT INTO campaignstatus(name,data,times,campaignid) VALUES("'.$_SESSION['name'].'","案件狀態由暫停轉成尚未送審",'.time().','.$_GET['campaign'].')';
		mysql_query($sql2);*/
		$sql3 = "SELECT * FROM media164 WHERE id= ".$_GET['id'];
		$result3 = mysql_query($sql3);
		$row3 = mysql_fetch_array($result3);
		if($row3['totalprice']!=$_POST['totalprice']){
			$data='【實況主】詩涼子SHIRYOUKO STUDIO媒體總金額由'.$row3['totalprice'].'改成'.$_POST['totalprice'].'<br />';
		}
		if($row3['date1']!=$date[1]){
			$data=$data.'【實況主】詩涼子SHIRYOUKO STUDIO媒體走期1由'.date('Ymd',$row3['date1']).'改成'.date('Ymd',$date[1]).'<br />';
		}
		if($row3['date3']!=$date[2]){
			$data=$data.'【實況主】詩涼子SHIRYOUKO STUDIO媒體走期1由'.date('Ymd',$row3['date1']).'改成'.date('Ymd',$date[2]).'<br />';
		}
		if($row3['date5']!=$date[3]){
			$data=$data.'【實況主】詩涼子SHIRYOUKO STUDIO媒體走期1由'.date('Ymd',$row3['date1']).'改成'.date('Ymd',$date[3]).'<br />';
		}
		if($row3['date7']!=$date[4]){
			$data=$data.'【實況主】詩涼子SHIRYOUKO STUDIO媒體走期1由'.date('Ymd',$row3['date1']).'改成'.date('Ymd',$date[4]).'<br />';
		}
		if($row3['date9']!=$date[5]){
			$data=$data.'【實況主】詩涼子SHIRYOUKO STUDIO媒體走期1由'.date('Ymd',$row3['date1']).'改成'.date('Ymd',$date[5]).'<br />';
		}
		if($data==NULL){
			$data='【實況主】詩涼子SHIRYOUKO STUDIO媒體我也不知道改了什麼，有可能是版位喔';
		}
		$sql2='INSERT INTO campaignstatus2(name,data,times,campaignid) VALUES("'.$_SESSION['username'].'","'.$data.'",'.time().','.$_GET['campaign'].')';
		mysql_query($sql2);
	}
	

	if($_GET['cue']==1){

			$sql2='UPDATE media164 SET 
			date1='.$date[1].' ,
			date2='.$date[1].',
			date3='.$date[2].' ,
			date4='.$date[2].',
			date5='.$date[3].' ,
			date6='.$date[3].',
			date7='.$date[4].' ,
			date8='.$date[4].',
			date9='.$date[5].' ,
			date10='.$date[5].',
			days='.$total_days.',
			days1='.$days[0].',
			days2='.$days[1].',
			days3='.$days[2].',
			days4='.$days[3].',
			days5='.$days[4].' ,
			others="' . $_POST['others'] . '",
			items2="' . $_POST['SelectType'] . '",
			items3="' . $_POST['SelectSystem'] . '",
			totalprice="'.$_POST['totalprice'].'",
			 a4="'.$_POST['totalprice'].'",
			 others="'.$_POST['others'].'" WHERE cue=1 and  campaign_id = '.$_GET['campaign']; 
			mysql_query($sql2);
	}
	else{
	$sql2='UPDATE media164 SET 
	date1='.$date[1].' ,
	date2='.$date[1].',
	date3='.$date[2].' ,
	date4='.$date[2].',
	date5='.$date[3].' ,
	date6='.$date[3].',
	date7='.$date[4].' ,
	date8='.$date[4].',
	date9='.$date[5].' ,
	date10='.$date[5].',
	days='.$total_days.',
	days1='.$days[0].',
	days2='.$days[1].',
	days3='.$days[2].',
	days4='.$days[3].',
	days5='.$days[4].' ,
	others="' . $_POST['others'] . '",
	items2="' . $_POST['SelectType'] . '",
	items3="' . $_POST['SelectSystem'] . '",
	totalprice="'.$_POST['totalprice'].'",
	 a4="'.$_POST['totalprice'].'",
	 others="'.$_POST['others'].'" WHERE cue=2 and campaign_id = '.$_GET['campaign']; 
	 mysql_query($sql2);
	}


	
	ShowMessageAndRedirect('修改媒體成功', 'campaign_view.php?id='. $_GET['campaign'], false);
	