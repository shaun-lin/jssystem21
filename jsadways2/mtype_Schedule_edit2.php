<?php
	
	require_once dirname(__DIR__) .'/autoload.php';
	
	include('include/db.inc.php');

	$TypeItem = $_POST['SelectType'];
	if($_POST['SelectCategory']!=null){
		if($_POST['SelectCategory']==1){
			$channel=', channel= "Line Today 原生廣告"';
		}elseif($_POST['SelectCategory']==2){
			$channel=' , channel="Line Today 富邦悍將直播贊助案"';
		}

		if(($_POST['SelectSubCategory']==1)){
			$phonesystem=' , phonesystem="iOS"';
		}elseif($_POST['SelectSubCategory']==2){
			$phonesystem=' , phonesystem="Android"';
		}elseif($_POST['SelectSubCategory']==3){
			$phonesystem=' , phonesystem="iOS/Android/全投放"';
		}

		if(($_POST['SelectThreeCategory']==1)){
			$position=' , position="Line Today 文章列表"';
		}else if (($_POST['SelectThreeCategory']==2)) {
			$position=' , position="Line Today 富邦悍將直播"';
		}

	}else{
		$channel="";
		$phonesystem="";
		$position="";
	}
	for($i=1;$i<=10;$i++){
		if($_POST['date'.$i]!=NULL){
			$date[$i]=mktime (0,0,0,substr($_POST['date'.$i],0,2),substr($_POST['date'.$i],3,2),substr($_POST['date'.$i],6,4));
		}else{
			$date[$i]=0;
		}
	}
	$number2 = '';
	if($_GET['cue']==2){
		$a0=$_GET['mediaid'];
		$a1=$_POST['a1'];
		$a2=$_POST['a2'];
		$a3=$_POST['a3'];
		$a4=$_POST['a4'];
		$sqlmedia = "SELECT * FROM media WHERE id=128";
		$resultmedia = mysql_query($sqlmedia);
		$rowmedia = mysql_fetch_array($resultmedia);
		$profit=($_POST['totalprice']*$rowmedia['profit'])/100;
		if($a3<$profit){
			$a5='1';
		}else{
			$a5='0';
		}
	}else{
		$number2 = ',  quantity2="'.$_POST['number2'].'" ';

	}
	
	$sql1 = "SELECT * FROM campaign WHERE id= ".$_GET['campaign'];
	$result1 = mysql_query($sql1);
	$row1 = mysql_fetch_array($result1);
	if($row1['status']==5){
		/*$sql2='UPDATE campaign SET status=1 WHERE id ='.$_GET['campaign'];
		mysql_query($sql2);
		$sql2='INSERT INTO campaignstatus(name,data,times,campaignid) VALUES("'.$_SESSION['name'].'","案件狀態由暫停轉成尚未送審",'.time().','.$_GET['campaign'].')';
		mysql_query($sql2);*/
		$sql3 = "SELECT * FROM media157 WHERE id= ".$_GET['id'];
		$result3 = mysql_query($sql3);
		$row3 = mysql_fetch_array($result3);
		if($row3['totalprice']!=$_POST['totalprice']){
			$data='Line Today媒體總金額由'.$row3['totalprice'].'改成'.$_POST['totalprice'].'<br />';
		}
		if($row3['date1']!=$date[1]){
			$data=$data.'Line Today媒體走期1由'.date('Ymd',$row3['date1']).'改成'.date('Ymd',$date[1]).'<br />';
		}
		if($row3['date2']!=$date[2]){
			$data=$data.'Line Today媒體走期1由'.date('Ymd',$row3['date2']).'改成'.date('Ymd',$date[2]).'<br />';
		}
		if($row3['date3']!=$date[3]){
			$data=$data.'Line Today媒體走期2由'.date('Ymd',$row3['date3']).'改成'.date('Ymd',$date[3]).'<br />';
		}
		if($row3['date4']!=$date[4]){
			$data=$data.'Line Today媒體走期2由'.date('Ymd',$row3['date4']).'改成'.date('Ymd',$date[4]).'<br />';
		}
		if($row3['date5']!=$date[5]){
			$data=$data.'Line Today媒體走期3由'.date('Ymd',$row3['date5']).'改成'.date('Ymd',$date[5]).'<br />';
		}
		if($row3['date6']!=$date[6]){
			$data=$data.'Line Today媒體走期3由'.date('Ymd',$row3['date6']).'改成'.date('Ymd',$date[6]).'<br />';
		}
		if($row3['date7']!=$date[7]){
			$data=$data.'Line Today媒體走期4由'.date('Ymd',$row3['date7']).'改成'.date('Ymd',$date[7]).'<br />';
		}
		if($row3['date8']!=$date[8]){
			$data=$data.'Line Today媒體走期4由'.date('Ymd',$row3['date8']).'改成'.date('Ymd',$date[8]).'<br />';
		}
		if($row3['date9']!=$date[9]){
			$data=$data.'Line Today媒體走期5由'.date('Ymd',$row3['date9']).'改成'.date('Ymd',$date[9]).'<br />';
		}
		if($row3['date10']!=$date[10]){
			$data=$data.'Line Today媒體走期5由'.date('Ymd',$row3['date10']).'改成'.date('Ymd',$date[10]).'<br />';
		}
		if($data==NULL){
			$data='Line Today媒體我也不知道改了什麼，有可能是版位喔';
		}
		$sql2='INSERT INTO campaignstatus2(name,data,times,campaignid) VALUES("'.$_SESSION['username'].'","'.$data.'",'.time().','.$_GET['campaign'].')';
		mysql_query($sql2);
	}

	if($_POST['gearing']==1){
		$sql_campaign = "SELECT * FROM media157 where campaign_id	= ".$_GET['campaign']; 
		$campaign = mysql_query($sql_campaign);
		$campaign_row = mysql_num_rows($campaign);
		if($campaign_row == 2){
			$sql2='UPDATE media128 SET  format1="'.$_POST['format1'].'"'.$channel.$phonesystem.$position.' , actions="'.$_POST['actions'].'" , format2="'.$_POST['format2'].'" , wheel="'.$_POST['wheel'].'" , ctr="'.$_POST['ctr'].'" , date1='.$date[1].' , date2='.$date[2].' , date3='.$date[3].' , date4='.$date[4].' , date5='.$date[5].' , date6='.$date[6].' , date7='.$date[7].' , date8='.$date[8].'  , date9='.$date[9].' , date10='.$date[10].' , days="'.$_POST['days'].'" , due="'.$_POST['due'].'" ,  quantity="'.$_POST['number1'].'",  quantity2="'.$_POST['number2'].'" , totalprice="'.$_POST['totalprice'].'", days1="'.$_POST['days1'].'", days2="'.$_POST['days2'].'", days3="'.$_POST['days3'].'", days4="'.$_POST['days4'].'", days5="'.$_POST['days5'].'", price1="'.$_POST['price1'].'", price2="'.$_POST['price2'].'", price3="'.$_POST['price3'].'", price4="'.$_POST['price4'].'", price5="'.$_POST['price5'].'", totalprice1="'.$_POST['totalprice1'].'", totalprice2="'.$_POST['totalprice2'].'", totalprice3="'.$_POST['totalprice3'].'", totalprice4="'.$_POST['totalprice4'].'", totalprice5="'.$_POST['totalprice5'].'", click1="'.$_POST['click1'].'", click2="'.$_POST['click2'].'", click3="'.$_POST['click3'].'", click4="'.$_POST['click4'].'", click5="'.$_POST['click5'].'", impression1="'.$_POST['impression1'].'", impression2="'.$_POST['impression2'].'", impression3="'.$_POST['impression3'].'", impression4="'.$_POST['impression4'].'", impression5="'.$_POST['impression5'].'" , times='.time().' , items="'.$_POST['items'].'" ,  others="'.$_POST['others'].'",a1="'.$_POST['a1'].'",a2="'.$_POST['a2'].'",a3="'.$_POST['a3'].'",a4="'.$_POST['a4'].'",a5="'.$a5.'",items2="'.$TypeItem.'",items3="'.$_POST['SelectSystem'].'",taget="'.$_POST['taget'].'"  '.$number2.'  WHERE campaign_id	= '.$_GET['campaign']; 
			mysql_query($sql2);
		}
	}
	
	$sql2='UPDATE media157 SET  format1="'.$_POST['format1'].'"'.$channel.$phonesystem.$position.' , actions="'.$_POST['actions'].'" , format2="'.$_POST['format2'].'" , wheel="'.$_POST['wheel'].'" , ctr="'.$_POST['ctr'].'" , date1='.$date[1].' , date2='.$date[2].' , date3='.$date[3].' , date4='.$date[4].' , date5='.$date[5].' , date6='.$date[6].' , date7='.$date[7].' , date8='.$date[8].'  , date9='.$date[9].' , date10='.$date[10].' , days="'.$_POST['days'].'" , due="'.$_POST['due'].'" ,  quantity="'.$_POST['number1'].'" , totalprice="'.$_POST['totalprice'].'", days1="'.$_POST['days1'].'", days2="'.$_POST['days2'].'", days3="'.$_POST['days3'].'", days4="'.$_POST['days4'].'", days5="'.$_POST['days5'].'", price1="'.$_POST['price1'].'", price2="'.$_POST['price2'].'", price3="'.$_POST['price3'].'", price4="'.$_POST['price4'].'", price5="'.$_POST['price5'].'", totalprice1="'.$_POST['totalprice1'].'", totalprice2="'.$_POST['totalprice2'].'", totalprice3="'.$_POST['totalprice3'].'", totalprice4="'.$_POST['totalprice4'].'", totalprice5="'.$_POST['totalprice5'].'", click1="'.$_POST['click1'].'", click2="'.$_POST['click2'].'", click3="'.$_POST['click3'].'", click4="'.$_POST['click4'].'", click5="'.$_POST['click5'].'", impression1="'.$_POST['impression1'].'", impression2="'.$_POST['impression2'].'", impression3="'.$_POST['impression3'].'", impression4="'.$_POST['impression4'].'", impression5="'.$_POST['impression5'].'" , times='.time().' , items="'.$_POST['items'].'" ,  others="'.$_POST['others'].'",a1="'.$_POST['a1'].'",a2="'.$_POST['a2'].'",a3="'.$_POST['a3'].'",a4="'.$_POST['a4'].'",a5="'.$a5.'",items2="'.$TypeItem.'",items3="'.$_POST['SelectSystem'].'",taget="'.$_POST['taget'].'"  '.$number2.'  WHERE id ='.$_GET['id'];
	mysql_query($sql2);
	ShowMessageAndRedirect('修改媒體成功', 'campaign_view.php?id='. $_GET['campaign'], false);
