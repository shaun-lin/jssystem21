<?php
	
	require_once dirname(__DIR__) .'/autoload.php';
	
	include('include/db.inc.php');
	$TypeItem=null;
	$TypeItem = $_POST['SelectType'];
	if($_POST['pr']==1){
		$website='媽媽經(PR)';
		$totalprice=0;
	}else{
		$website='媽媽經';
		$totalprice=$_POST['totalprice'];
	}
	for($i=1;$i<=10;$i++){
		if($_POST['date'.$i]!=NULL){
			$date[$i]=mktime (0,0,0,substr($_POST['date'.$i],0,2),substr($_POST['date'.$i],3,2),substr($_POST['date'.$i],6,4));
		}else{
			$date[$i]=0;
		}
	}
	$sqlmedia = "SELECT * FROM media WHERE id=89";
	$resultmedia = mysql_query($sqlmedia);
	$rowmedia = mysql_fetch_array($resultmedia);
	$profit=($_POST['totalprice']*$rowmedia['profit'])/100;
	if($_GET['cue']==2){
		$a=$_GET['media2'];
		$a0=$_GET['mediaid'];
		$a1=$_POST['a1'];
		$a2=$_POST['a2'];
		$a3=$_POST['a3'];
		$a4=$_POST['a4'];
		if($a3<$profit){
			$a5='1';
		}
	}
	$sql2='INSERT INTO media89(campaign_id,cue,website,channel,position,format1,format2,wheel,ctr,date1,date2,date3,date4,date5,date6,date7,date8,date9,date10,days,due,price,quantity,quantity2,totalprice,times,others,a,a0,a1,a2,a3,a4,a5,days1,days2,days3,days4,days5,items2,items3) VALUES('.$_GET['id'].','.$_GET['cue'].',"'.$website.'","'.$_POST['channel'].'","'.$_POST['position'].'","'.$_POST['format1'].'","'.$_POST['format2'].'","'.$_POST['wheel'].'","'.$_POST['ctr'].'",'.$date[1].','.$date[2].','.$date[3].','.$date[4].','.$date[5].','.$date[6].','.$date[7].','.$date[8].','.$date[9].','.$date[10].',"'.$_POST['days'].'","'.$_POST['due'].'","'.$totalprice.'","'.$_POST['number1'].'","'.$_POST['number2'].'","'.$totalprice.'",'.time().',"'.$_POST['others'].'","'.$a.'","'.$a0.'","'.$a1.'","'.$a2.'","'.$a3.'","'.$a4.'","'.$a5.'","'.$_POST['days1'].'","'.$_POST['days2'].'","'.$_POST['days3'].'","'.$_POST['days4'].'","'.$_POST['days5'].'","'.$TypeItem.'","'.$_POST['SelectSystem'].'")';
	mysql_query($sql2);
	AddMediaMapping("media89", $_GET['id'], mysql_insert_id());
	if($_POST['samecue']==1){
		$sql1 = "SELECT * FROM campaign WHERE id= ".$_GET['id'];
		$result1 = mysql_query($sql1);
		$row1 = mysql_fetch_array($result1);
		$sqlnew = "SELECT * FROM media89 ORDER BY id DESC LIMIT 1;";
		$resultnew = mysql_query($sqlnew);
		$rownew = mysql_fetch_array($resultnew);
		if($row1['agency_id']!=0){
			$sql4 = "SELECT * FROM commission WHERE agency= ".$row1['agency_id']." AND media=".$_GET['media'];
			$result4 = mysql_query($sql4);
			$row4 = mysql_fetch_array($result4);
			if($row4['commission5']!=0){
				$commission1=($_POST['totalprice']*$row4['commission1'])/100;
				$commission4=($_POST['totalprice']*$row4['commission4'])/100;
			}else{
				$commission1=0;
				$commission4=0;
			}
		}else{
			$commission1=0;
			$commission4=0;
		}
		$a=$_GET['media'];
		$a0=$rownew['id'];
		$a1=$commission1;
		$a2=$commission4;
		$a3=$profit;
		$a4=$_POST['totalprice']-$commission1-$commission4-$profit;
		$sql2='INSERT INTO media89(campaign_id,cue,website,channel,position,format1,format2,wheel,ctr,date1,date2,date3,date4,date5,date6,date7,date8,date9,date10,days,due,price,quantity,quantity2,totalprice,times,others,a,a0,a1,a2,a3,a4,days1,days2,days3,days4,days5,items2,items3) VALUES('.$_GET['id'].',2,"'.$website.'","'.$_POST['channel'].'","'.$_POST['position'].'","'.$_POST['format1'].'","'.$_POST['format2'].'","'.$_POST['wheel'].'","'.$_POST['ctr'].'",'.$date[1].','.$date[2].','.$date[3].','.$date[4].','.$date[5].','.$date[6].','.$date[7].','.$date[8].','.$date[9].','.$date[10].',"'.$_POST['days'].'","'.$_POST['due'].'","'.$_POST['totalprice'].'","'.$_POST['number1'].'","'.$_POST['number2'].'","'.$_POST['totalprice'].'",'.time().',"'.$_POST['others'].'","'.$a.'","'.$a0.'","'.$a1.'","'.$a2.'","'.$a3.'","'.$a4.'","'.$_POST['days1'].'","'.$_POST['days2'].'","'.$_POST['days3'].'","'.$_POST['days4'].'","'.$_POST['days5'].'","'.$TypeItem.'","'.$_POST['SelectSystem'].'")';
		mysql_query($sql2);
		AddMediaMapping("media89", $_GET['id'], mysql_insert_id());
	}
	$sql1 = "SELECT * FROM campaign WHERE id= ".$_GET['id'];
	$result1 = mysql_query($sql1);
	$row1 = mysql_fetch_array($result1);
	if($row1['status']==5){
		$sql2='INSERT INTO campaignstatus2(name,data,times,campaignid) VALUES("'.$_SESSION['username'].'","新增媽媽經媒體",'.time().','.$_GET['id'].')';
		mysql_query($sql2);
	}
	
	ShowMessageAndRedirect('新增媒體成功', 'campaign_view.php?id='. $_GET['id'], false);
	