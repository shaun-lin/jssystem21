<?php
	
	require_once dirname(__DIR__) .'/autoload.php';
	
	include('include/db.inc.php');
	$TypeItem=$_POST['SelectType'];
	$TypeItem = $_POST['SelectType'];
	for($i=1;$i<=10;$i++){
		if($_POST['date'.$i]!=NULL){
			$date[$i]=mktime (0,0,0,substr($_POST['date'.$i],0,2),substr($_POST['date'.$i],3,2),substr($_POST['date'.$i],6,4));
		}else{
			$date[$i]=0;
		}
	}
	if($_GET['cue']==2){
		$a0=$_GET['mediaid'];
		$a1=$_POST['a1'];
		$a2=$_POST['a2'];
		$a3=$_POST['a3'];
		$a4=$_POST['a4'];
		$sqlmedia = "SELECT * FROM media WHERE id=165";
		$resultmedia = mysql_query($sqlmedia);
		$rowmedia = mysql_fetch_array($resultmedia);
		$profit=($_POST['totalprice']*$rowmedia['profit'])/100;
		if($a3<$profit){
			$a5='1';
		}
	}
	$sql1 = "SELECT * FROM campaign WHERE id= ".$_GET['campaign'];
	$result1 = mysql_query($sql1);
	$row1 = mysql_fetch_array($result1);
	if($row1['status']==5){
		/*$sql2='UPDATE campaign SET status=1 WHERE id ='.$_GET['campaign'];
		mysql_query($sql2);
		$sql2='INSERT INTO campaignstatus(name,data,times,campaignid) VALUES("'.$_SESSION['name'].'","案件狀態由暫停轉成尚未送審",'.time().','.$_GET['campaign'].')';
		mysql_query($sql2);*/
		$sql3 = "SELECT * FROM media165 WHERE id= ".$_GET['id'];
		$result3 = mysql_query($sql3);
		$row3 = mysql_fetch_array($result3);
		if($row3['totalprice']!=$_POST['totalprice']){
			$data='HappyGo MMS媒體總金額由'.$row3['totalprice'].'改成'.$_POST['totalprice'].'<br />';
		}
		if($row3['date1']!=$date[1]){
			$data=$data.'HappyGo MMS媒體走期1由'.date('Ymd',$row3['date1']).'改成'.date('Ymd',$date[1]).'<br />';
		}
		if($row3['date2']!=$date[2]){
			$data=$data.'HappyGo MMS媒體走期1由'.date('Ymd',$row3['date2']).'改成'.date('Ymd',$date[2]).'<br />';
		}
		if($row3['date3']!=$date[3]){
			$data=$data.'HappyGo MMS媒體走期2由'.date('Ymd',$row3['date3']).'改成'.date('Ymd',$date[3]).'<br />';
		}
		if($row3['date4']!=$date[4]){
			$data=$data.'HappyGo MMS媒體走期2由'.date('Ymd',$row3['date4']).'改成'.date('Ymd',$date[4]).'<br />';
		}
		if($row3['date5']!=$date[5]){
			$data=$data.'HappyGo MMS媒體走期3由'.date('Ymd',$row3['date5']).'改成'.date('Ymd',$date[5]).'<br />';
		}
		if($row3['date6']!=$date[6]){
			$data=$data.'HappyGo MMS媒體走期3由'.date('Ymd',$row3['date6']).'改成'.date('Ymd',$date[6]).'<br />';
		}
		if($row3['date7']!=$date[7]){
			$data=$data.'HappyGo MMS媒體走期4由'.date('Ymd',$row3['date7']).'改成'.date('Ymd',$date[7]).'<br />';
		}
		if($row3['date8']!=$date[8]){
			$data=$data.'HappyGo MMS媒體走期4由'.date('Ymd',$row3['date8']).'改成'.date('Ymd',$date[8]).'<br />';
		}
		if($row3['date9']!=$date[9]){
			$data=$data.'HappyGo MMS媒體走期5由'.date('Ymd',$row3['date9']).'改成'.date('Ymd',$date[9]).'<br />';
		}
		if($row3['date10']!=$date[10]){
			$data=$data.'HappyGo MMS媒體走期5由'.date('Ymd',$row3['date10']).'改成'.date('Ymd',$date[10]).'<br />';
		}
		if($data==NULL){
			$data='HappyGo MMS媒體我也不知道改了什麼，有可能是版位喔';
		}
		$sql2='INSERT INTO campaignstatus2(name,data,times,campaignid) VALUES("'.$_SESSION['username'].'","'.$data.'",'.time().','.$_GET['campaign'].')';
		mysql_query($sql2);
	}

	if($_POST['gearing']==1){
		$sql_campaign = "SELECT * FROM media165 where campaign_id	= ".$_GET['campaign']; 
		$campaign = mysql_query($sql_campaign);
		$campaign_row = mysql_num_rows($campaign);
		if($campaign_row == 2){
			$sql2='UPDATE media49 SET website="'.$_POST['website'].'" , channel="'.$_POST['channel'].'" , position="'.$_POST['position'].'" , format1="'.$_POST['format1'].'" , format2="'.$_POST['format2'].'" , wheel="'.$_POST['wheel'].'" , date1='.$date[1].' , date2='.$date[2].' , date3='.$date[3].' , date4='.$date[4].' , date5='.$date[5].' , date6='.$date[6].' , date7='.$date[7].' , date8='.$date[8].'  , date9='.$date[9].' , date10='.$date[10].' ,  days="'.$_POST['days'].'" , due="'.$_POST['due'].'" , quantity="'.$_POST['quantity'].'" , price="'.$_POST['price'].'" , totalprice="'.$_POST['totalprice'].'" , times='.time().' , others="'.$_POST['others'].'" ,a1="'.$_POST['a1'].'",a2="'.$_POST['a2'].'",a3="'.$_POST['a3'].'",a4="'.$_POST['a4'].'",a5="'.$a5.'", days1="'.$_POST['days1'].'", days2="'.$_POST['days2'].'", days3="'.$_POST['days3'].'", days4="'.$_POST['days4'].'", days5="'.$_POST['days5'].'",items2="'.$TypeItem.'",items3="'.$_POST['SelectSystem'].'"  WHERE campaign_id	= '.$_GET['campaign']; 
			mysql_query($sql2);
		}
	}
	
	$sql2='UPDATE media165 SET website="'.$_POST['website'].'" , channel="'.$_POST['channel'].'" , position="'.$_POST['position'].'" , format1="'.$_POST['format1'].'" , format2="'.$_POST['format2'].'" , wheel="'.$_POST['wheel'].'" , date1='.$date[1].' , date2='.$date[2].' , date3='.$date[3].' , date4='.$date[4].' , date5='.$date[5].' , date6='.$date[6].' , date7='.$date[7].' , date8='.$date[8].'  , date9='.$date[9].' , date10='.$date[10].' ,  days="'.$_POST['days'].'" , due="'.$_POST['due'].'" , quantity="'.$_POST['quantity'].'" , price="'.$_POST['price'].'" , totalprice="'.$_POST['totalprice'].'" , times='.time().' , others="'.$_POST['others'].'" ,a1="'.$_POST['a1'].'",a2="'.$_POST['a2'].'",a3="'.$_POST['a3'].'",a4="'.$_POST['a4'].'",a5="'.$a5.'", days1="'.$_POST['days1'].'", days2="'.$_POST['days2'].'", days3="'.$_POST['days3'].'", days4="'.$_POST['days4'].'", days5="'.$_POST['days5'].'",items2="'.$TypeItem.'",items3="'.$_POST['SelectSystem'].'"  WHERE id ='.$_GET['id'];
	mysql_query($sql2);
	//echo $sql2;
	$sql1 = "SELECT * FROM campaign WHERE id= ".$_GET['campaign'];
	$result1 = mysql_query($sql1);
	$row1 = mysql_fetch_array($result1);
	
	ShowMessageAndRedirect('修改媒體成功', 'campaign_view.php?id='. $_GET['campaign'], false);
	