<?php
	
	require_once dirname(__DIR__) .'/autoload.php';
	
	include('include/db.inc.php');
	$db = clone($GLOBALS['app']->db);
	$TypeItem=null;
	$TypeItem = $_POST['SelectType'];
	$autoSerialNumberA=autoSerialNumber();
 	$autoSerialNumberB=autoSerialNumber();
	if($_POST['pr']==1){
		$_POST['website']=$_POST['website'].'(PR)';
		$totalprice=0;
	}else{
		$totalprice=$_POST['totalprice'];
	}
	for($i=1;$i<=10;$i++){
		if($_POST['date'.$i]!=NULL){
			$date[$i]=mktime (0,0,0,substr($_POST['date'.$i],0,2),substr($_POST['date'.$i],3,2),substr($_POST['date'.$i],6,4));
		}else{
			$date[$i]=0;
		}
	}
	$sqlmedia = "SELECT * FROM media WHERE id=167";
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
	$sql2='INSERT INTO media167(campaign_id,
item_seq,
cue,
website,
actions,
phonesystem,
position,
format1,
format2,
wheel,
date1,
date2,
date3,
date4,
date5,
date6,
date7,
date8,
date9,
date10,
days,
due,
quantity,
price,
totalprice,
times,
others,
a,
a0,
a1,
a2,
a3,
a4,
a5,
items2,
items3) VALUES('.$_GET['id'].',
"'.$autoSerialNumberA.'",
'.$_GET['cue'].',
"'.$_POST['website'].'",
"'.$_POST['actions'].'",
"'.$_POST['phonesystem'].'",
"'.$_POST['position'].'",
"'.$_POST['format1'].'",
"'.$_POST['format2'].'",
"'.$_POST['wheel'].'",
'.$date[1].',
'.$date[2].',
'.$date[3].',
'.$date[4].',
'.$date[5].',
'.$date[6].',
'.$date[7].',
'.$date[8].',
'.$date[9].',
'.$date[10].',
"'.$_POST['days'].'",
"'.$_POST['due'].'",
"'.$_POST['quantity'].'",
"'.$_POST['price'].'",
"'.$totalprice.'",
'.time().',
"'.$_POST['others'].'",
"'.$a.'",
"'.$a0.'",
"'.$a1.'",
"'.$a2.'",
"'.$a3.'",
"'.$a4.'",
"'.$a5.'",
"'.$TypeItem.'",
"'.$_POST['SelectSystem'].'")';
	mysql_query($sql2);
	AddMediaMapping("media167", $_GET['id'], mysql_insert_id());
	if($_POST['samecue']==1){
		$sql1 = "SELECT * FROM campaign WHERE id= ".$_GET['id'];
		$result1 = mysql_query($sql1);
		$row1 = mysql_fetch_array($result1);
		$sqlnew = "SELECT * FROM media167 ORDER BY id DESC LIMIT 1;";
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
		$sql2='INSERT INTO media167(campaign_id,
item_seq,
cue,
website,
actions,
phonesystem,
position,
format1,
format2,
wheel,
date1,
date2,
date3,
date4,
date5,
date6,
date7,
date8,
date9,
date10,
days,
due,
quantity,
price,
totalprice,
times,
others,
a,
a0,
a1,
a2,
a3,
a4,
items2,
items3) VALUES('.$_GET['id'].',
"'.$autoSerialNumberB.'",
2,
"'.$_POST['website'].'",
"'.$_POST['actions'].'",
"'.$_POST['phonesystem'].'",
"'.$_POST['position'].'",
"'.$_POST['format1'].'",
"'.$_POST['format2'].'",
"'.$_POST['wheel'].'",
'.$date[1].',
'.$date[2].',
'.$date[3].',
'.$date[4].',
'.$date[5].',
'.$date[6].',
'.$date[7].',
'.$date[8].',
'.$date[9].',
'.$date[10].',
"'.$_POST['days'].'",
"'.$_POST['due'].'",
"'.$_POST['quantity'].'",
"'.$_POST['price'].'",
"'.$_POST['totalprice'].'",
'.time().',
"'.$_POST['others'].'",
"'.$a.'",
"'.$a0.'",
"'.$a1.'",
"'.$a2.'",
"'.$a3.'",
"'.$a4.'",
"'.$TypeItem.'",
"'.$_POST['SelectSystem'].'")';
		mysql_query($sql2);
		AddMediaMapping('media167', $_GET['id'], mysql_insert_id());
	}

	//jackie 2018/06/01 抓media***_id 填到cp_detail mtype_id
	$item_id1=mysql_insert_id();
	$item_id2=$item_id1-1;

	$sql1 = "SELECT * FROM campaign WHERE id= ".$_GET['id'];
	$result1 = mysql_query($sql1);
	$row1 = mysql_fetch_array($result1);
	if($row1['status']==5){
		$sql2='INSERT INTO campaignstatus2(name,data,times,campaignid) VALUES("'.$_SESSION['username'].'","新增LINE(3DM)媒體",'.time().','.$_GET['id'].')';
		mysql_query($sql2);
	}
	$cp_id = $_GET['id'];
	$media_id = $_GET['mediaid'];
	$item_id = $_GET['itemid'];
	$mtype_name = $_GET['mtypename'];
	$mtype_number = $_GET['mtypenumber'];
	$mtype_id = $_GET['mtypeid'];

	$goon=GetVar('goon');
	
	$sql3 = "INSERT INTO `cp_detail`( `cp_id`, `media_id`, `comp_id`, `item_id`, `mtype_name`, `mtype_number`, `mtype_id`,`item_seq`,`cue`) 
		VALUES ('".$cp_id."','".$media_id."','0','".$item_id."','".$mtype_name."','".$mtype_number."','".$item_id2."','".$autoSerialNumberA."','1')";
	mysql_query($sql3);

	$sql4 = "INSERT INTO `cp_detail`( `cp_id`, `media_id`, `comp_id`, `item_id`, `mtype_name`, `mtype_number`, `mtype_id`,`item_seq`,`cue`) 
		VALUES ('".$cp_id."','".$media_id."','0','".$item_id."','".$mtype_name."','".$mtype_number."','".$item_id1."','".$autoSerialNumberB."','2')";
	mysql_query($sql4);
		if ($goon=="Y") {

		$arrItems=array();
				$arrItems[]=array("key"=>"result","name"=>"OK");
		
		echo json_encode($arrItems);
	}else{
	ShowMessageAndRedirect('新增媒體成功', 'campaign_view.php?id='. $_GET['id'], false);
	}