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
		$sqlmedia = "SELECT * FROM media WHERE id=169";
		$resultmedia = mysql_query($sqlmedia);
		$rowmedia = mysql_fetch_array($resultmedia);
		$profit=($_POST['totalprice']*$rowmedia['profit'])/100;
		if($a3<$profit){
			$a5='1';
		}
	}

	$phonesystem = "";
	if($_POST['SelectCategory']==1){
		$phonesystem='iOS';
	}elseif($_POST['SelectCategory']==2){
		$phonesystem='Android';
	}elseif($_POST['SelectCategory']==3){
		$phonesystem='iOS/Android全投放';
	}elseif($_POST['SelectCategory']==4){
		$phonesystem='單下iPad';
	}

	$position = $_POST['position'];
	switch ($_POST['position']) {
		case '1':
			# code...
			$position = "小豬啦啦隊-粉絲團按讚CPF";
			break;
		case '2':
			# code...
			$position = "口碑培養皿-文章分享CPS";
			break;
		case '3':
			# code...
			$position = "小豬特派員-心得撰寫CPA";
			break;
	}

	$sql1 = "SELECT * FROM campaign WHERE id= ".$_GET['campaign'];
	$result1 = mysql_query($sql1);
	$row1 = mysql_fetch_array($result1);
	if($row1['status']==5){
		/*$sql2='UPDATE campaign SET status=1 WHERE id ='.$_GET['campaign'];
		mysql_query($sql2);
		$sql2='INSERT INTO campaignstatus(name,data,times,campaignid) VALUES("'.$_SESSION['name'].'","案件狀態由暫停轉成尚未送審",'.time().','.$_GET['campaign'].')';
		mysql_query($sql2);*/
		$sql3 = "SELECT * FROM media169 WHERE id= ".$_GET['id'];
		$result3 = mysql_query($sql3);
		$row3 = mysql_fetch_array($result3);
		if($row3['totalprice']!=$_POST['totalprice']){
			$data='錢包小豬(任務型)媒體總金額由'.$row3['totalprice'].'改成'.$_POST['totalprice'].'<br />';
		}
		if($row3['date1']!=$date[1]){
			$data=$data.'錢包小豬(任務型)媒體走期1由'.date('Ymd',$row3['date1']).'改成'.date('Ymd',$date[1]).'<br />';
		}
		if($row3['date2']!=$date[2]){
			$data=$data.'錢包小豬(任務型)媒體走期1由'.date('Ymd',$row3['date2']).'改成'.date('Ymd',$date[2]).'<br />';
		}
		if($row3['date3']!=$date[3]){
			$data=$data.'錢包小豬(任務型)媒體走期2由'.date('Ymd',$row3['date3']).'改成'.date('Ymd',$date[3]).'<br />';
		}
		if($row3['date4']!=$date[4]){
			$data=$data.'錢包小豬(任務型)媒體走期2由'.date('Ymd',$row3['date4']).'改成'.date('Ymd',$date[4]).'<br />';
		}
		if($row3['date5']!=$date[5]){
			$data=$data.'錢包小豬(任務型)媒體走期3由'.date('Ymd',$row3['date5']).'改成'.date('Ymd',$date[5]).'<br />';
		}
		if($row3['date6']!=$date[6]){
			$data=$data.'錢包小豬(任務型)媒體走期3由'.date('Ymd',$row3['date6']).'改成'.date('Ymd',$date[6]).'<br />';
		}
		if($row3['date7']!=$date[7]){
			$data=$data.'錢包小豬(任務型)媒體走期4由'.date('Ymd',$row3['date7']).'改成'.date('Ymd',$date[7]).'<br />';
		}
		if($row3['date8']!=$date[8]){
			$data=$data.'錢包小豬(任務型)媒體走期4由'.date('Ymd',$row3['date8']).'改成'.date('Ymd',$date[8]).'<br />';
		}
		if($row3['date9']!=$date[9]){
			$data=$data.'錢包小豬(任務型)媒體走期5由'.date('Ymd',$row3['date9']).'改成'.date('Ymd',$date[9]).'<br />';
		}
		if($row3['date10']!=$date[10]){
			$data=$data.'錢包小豬(任務型)媒體走期5由'.date('Ymd',$row3['date10']).'改成'.date('Ymd',$date[10]).'<br />';
		}
		if($data==NULL){
			$data='錢包小豬(任務型)媒體我也不知道改了什麼，有可能是版位喔';
		}
		$sql2='INSERT INTO campaignstatus2(name,data,times,campaignid) VALUES("'.$_SESSION['username'].'","'.$data.'",'.time().','.$_GET['campaign'].')';
		mysql_query($sql2);
	}
	$sql2='UPDATE media169 SET website="'.$_POST['website'].'" , actions="'.$_POST['actions'].'", phonesystem="'.$_POST['SelectCategory'].'" , position="'.$position.'" , format1="'.$_POST['format1'].'" , format2="'.$_POST['format2'].'" , wheel="'.$_POST['wheel'].'" , date1='.$date[1].' , date2='.$date[2].' , date3='.$date[3].' , date4='.$date[4].' , date5='.$date[5].' , date6='.$date[6].' , date7='.$date[7].' , date8='.$date[8].'  , date9='.$date[9].' , date10='.$date[10].' , days="'.$_POST['days'].'" , due="'.$_POST['due'].'" , quantity="'.$_POST['quantity'].'" , price="'.$_POST['price'].'" , totalprice="'.$_POST['totalprice'].'" , times='.time().' , items="'.$_POST['items'].'" , others="'.$_POST['others'].'"  , days1="'.$_POST['days1'].'", days2="'.$_POST['days2'].'", days3="'.$_POST['days3'].'", days4="'.$_POST['days4'].'", days5="'.$_POST['days5'].'", price1="'.$_POST['price1'].'", price2="'.$_POST['price2'].'", price3="'.$_POST['price3'].'", price4="'.$_POST['price4'].'", price5="'.$_POST['price5'].'", totalprice1="'.$_POST['totalprice1'].'", totalprice2="'.$_POST['totalprice2'].'", totalprice3="'.$_POST['totalprice3'].'", totalprice4="'.$_POST['totalprice4'].'", totalprice5="'.$_POST['totalprice5'].'", click1="'.$_POST['click1'].'", click2="'.$_POST['click2'].'", click3="'.$_POST['click3'].'", click4="'.$_POST['click4'].'", click5="'.$_POST['click5'].'",a1="'.$_POST['a1'].'",a2="'.$_POST['a2'].'",a3="'.$_POST['a3'].'",a4="'.$_POST['a4'].'",a5="'.$a5.'",items2="'.$TypeItem.'",items3="'.$_POST['SelectSystem'].'"  WHERE id ='.$_GET['id'];
	mysql_query($sql2);
	
	ShowMessageAndRedirect('修改媒體成功', 'campaign_view.php?id='. $_GET['campaign'], false);
	