<?php
	
	require_once dirname(__DIR__) .'/autoload.php';
	
	include('include/db.inc.php');
	$newMediaId = GetNewMediaId("area");//ken,取新的media id , 順便+1
	$TypeItem=null;
	$TypeItem = $_POST['SelectType'];
	if($_POST['SelectCategory']==1){
		$channel='Line Today 原生廣告';
	}elseif($_POST['SelectCategory']==2){
			$channel='Line Today 富邦悍將直播贊助案';
	}else{
		$channel='';
	}
	if(($_POST['SelectSubCategory']==1)){
		$phonesystem = 'iOS';
	}else if($_POST['SelectSubCategory']==2){
		$phonesystem='Android';
	}else if($_POST['SelectSubCategory']==3){
		$phonesystem='iOS/Android/全投放';
	}

	if(($_POST['SelectThreeCategory']==1)){
		$position='Line Today 文章列表';
	}elseif(($_POST['SelectThreeCategory']==2)){
		$position='Line Today 富邦悍將直播';
	}else{
		$position='';
	}
	if($_POST['pr']==1){
		$website='Line Today(PR)';
		$totalprice=0;
	}else{
		$website='Line Today';
		$totalprice=$_POST['totalprice'];
	}
	for($i=1;$i<=10;$i++){
		if($_POST['date'.$i]!=NULL){
			$date[$i]=mktime (0,0,0,substr($_POST['date'.$i],0,2),substr($_POST['date'.$i],3,2),substr($_POST['date'.$i],6,4));
		}else{
			$date[$i]=0;
		}
	}
	$sqlmedia = "SELECT * FROM media WHERE id=128";
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
	$sql2='INSERT INTO media'.$newMediaId.'(campaign_id,cue,website,channel,actions,phonesystem,position,format1,format2,wheel,ctr,date1,date2,date3,date4,date5,date6,date7,date8,date9,date10,days,due,quantity,quantity2,totalprice,days1,days2,days3,days4,days5,price1,price2,price3,price4,price5,totalprice1,totalprice2,totalprice3,totalprice4,totalprice5,click1,click2,click3,click4,click5,impression1,impression2,impression3,impression4,impression5,times,items,others,a,a0,a1,a2,a3,a4,a5,items2,items3,taget) VALUES('.$_GET['id'].','.$_GET['cue'].',"'.$website.'","'.$channel.'","'.$_POST['actions'].'","'.$phonesystem.'","'.$position.'","'.$_POST['format1'].'","'.$_POST['format2'].'","'.$_POST['wheel'].'","'.$_POST['ctr'].'",'.$date[1].','.$date[2].','.$date[3].','.$date[4].','.$date[5].','.$date[6].','.$date[7].','.$date[8].','.$date[9].','.$date[10].',"'.$_POST['days'].'","'.$_POST['due'].'","'.$_POST['number1'].'","'.$_POST['number2'].'","'.$totalprice.'","'.$_POST['days1'].'","'.$_POST['days2'].'","'.$_POST['days3'].'","'.$_POST['days4'].'","'.$_POST['days5'].'","'.$_POST['price1'].'","'.$_POST['price2'].'","'.$_POST['price3'].'","'.$_POST['price4'].'","'.$_POST['price5'].'","'.$_POST['totalprice1'].'","'.$_POST['totalprice2'].'","'.$_POST['totalprice3'].'","'.$_POST['totalprice4'].'","'.$_POST['totalprice5'].'","'.$_POST['click1'].'","'.$_POST['click2'].'","'.$_POST['click3'].'","'.$_POST['click4'].'","'.$_POST['click5'].'","'.$_POST['impression1'].'","'.$_POST['impression2'].'","'.$_POST['impression3'].'","'.$_POST['impression4'].'","'.$_POST['impression5'].'",'.time().',"","'.$_POST['others'].'","'.$a.'","'.$a0.'","'.$a1.'","'.$a2.'","'.$a3.'","'.$a4.'","'.$a5.'","'.$TypeItem.'","'.$_POST['SelectSystem'].'","'.$_POST['taget'].'")';
	mysql_query($sql2);
	AddMediaMapping(__FILE__, $_GET['id'], mysql_insert_id());
	if($_POST['samecue']==1){
		$sql1 = "SELECT * FROM campaign WHERE id= ".$_GET['id'];
		$result1 = mysql_query($sql1);
		$row1 = mysql_fetch_array($result1);
		$sqlnew = "SELECT * FROM media128 ORDER BY id DESC LIMIT 1;";
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
		$sql2='INSERT INTO media'.$newMediaId.'(campaign_id,cue,website,channel,actions,phonesystem,position,format1,format2,wheel,ctr,date1,date2,date3,date4,date5,date6,date7,date8,date9,date10,days,due,quantity,quantity2,totalprice,days1,days2,days3,days4,days5,price1,price2,price3,price4,price5,totalprice1,totalprice2,totalprice3,totalprice4,totalprice5,click1,click2,click3,click4,click5,impression1,impression2,impression3,impression4,impression5,times,items,others,a,a0,a1,a2,a3,a4,items2,items3,taget) VALUES('.$_GET['id'].',2,"'.$website.'","'.$channel.'","'.$_POST['actions'].'","'.$phonesystem.'","'.$position.'","'.$_POST['format1'].'","'.$_POST['format2'].'","'.$_POST['wheel'].'","'.$_POST['ctr'].'",'.$date[1].','.$date[2].','.$date[3].','.$date[4].','.$date[5].','.$date[6].','.$date[7].','.$date[8].','.$date[9].','.$date[10].',"'.$_POST['days'].'","'.$_POST['due'].'","'.$_POST['number1'].'","'.$_POST['number2'].'","'.$_POST['totalprice'].'","'.$_POST['days1'].'","'.$_POST['days2'].'","'.$_POST['days3'].'","'.$_POST['days4'].'","'.$_POST['days5'].'","'.$_POST['price1'].'","'.$_POST['price2'].'","'.$_POST['price3'].'","'.$_POST['price4'].'","'.$_POST['price5'].'","'.$_POST['totalprice1'].'","'.$_POST['totalprice2'].'","'.$_POST['totalprice3'].'","'.$_POST['totalprice4'].'","'.$_POST['totalprice5'].'","'.$_POST['click1'].'","'.$_POST['click2'].'","'.$_POST['click3'].'","'.$_POST['click4'].'","'.$_POST['click5'].'","'.$_POST['impression1'].'","'.$_POST['impression2'].'","'.$_POST['impression3'].'","'.$_POST['impression4'].'","'.$_POST['impression5'].'",'.time().',"","'.$_POST['others'].'","'.$a.'","'.$a0.'","'.$a1.'","'.$a2.'","'.$a3.'","'.$a4.'","'.$TypeItem.'","'.$_POST['SelectSystem'].'","'.$_POST['taget'].'")';
		mysql_query($sql2);
		AddMediaMapping(__FILE__, $_GET['id'], mysql_insert_id());
	}
	//echo $sql2;
	$sql1 = "SELECT * FROM campaign WHERE id= ".$_GET['id'];
	$result1 = mysql_query($sql1);
	$row1 = mysql_fetch_array($result1);
	if($row1['status']==5){
		$sql2='INSERT INTO campaignstatus2(name,data,times,campaignid) VALUES("'.$_SESSION['username'].'","新增Line Today媒體",'.time().','.$_GET['id'].')';
		mysql_query($sql2);
	}
	ShowMessageAndRedirect('新增媒體成功', 'campaign_view.php?id='. $_GET['id'], false);
	