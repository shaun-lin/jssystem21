<?php 
	session_start();
	ini_set( "memory_limit", "256M");
	header("Content-Type:text/html; charset=utf-8");
	 // header('Content-type:application/vnd.ms-excel');  //宣告網頁格式
	 // header('Content-Disposition: attachment; filename=mycrm.xls');  //設定檔案名稱
	include('include/db.inc.php');
	// include("mail/class.phpmailer.php"); //匯入PHPMailer類別
	include('../php/auto_mail.php');
	$first_day = strtotime(date("Y",time()).'01-01 -1 year');
	$last_day = strtotime(date("Y-m",time()).'-01 +1 month -1 days');

	$first_day = strtotime('2013-01-01');
	$last_day = strtotime('2015-12-31');


	$sql = "SELECT * FROM campaign WHERE status in (2,3,4,5,6,7) and (date11 BETWEEN $first_day and $last_day) or (date22 BETWEEN $first_day and $last_day )";


	$campaign_list_NoReceipt = array();

	$campaign_list_NoCost = array();

	$campaign_list_AllNo = array();



	$result=mysql_query($sql);
	if (mysql_num_rows($result)>0){
		while($row=mysql_fetch_array($result)){
			$receipt = 0;
			$cost = 0;

			if(checkReceipt($row["id"])){
				//有發票
				$receipt = 1;
			}else{
				//無發票
				$receipt = 0;
			}

			if(mediaSearch($row["id"])){
				//全有填收入
				$cost = 1;
			}else{
				//有媒體沒填收入
				$cost = 0;
			}

			// 有收入成本 沒發票 
			if($receipt == 0 && $cost == 1 && ($row["status"] == 3 || $row["status"] == 5 ||$row["status"] == 7)){

				$campaign_list_NoReceipt[]= array('team'=>PacketByName($row["member"]),'name' => $row["name"],'id' =>$row["id"], 'member' => $row["member"],'idnumber' => $row["idnumber"]);

			}


			// 有開發票  沒填收入成本
			if($receipt == 1 && $cost == 0 && ($row["status"] == 3 || $row["status"] == 5 ||$row["status"] == 7)){
				$campaign_list_NoCost[]= array('team'=>PacketByName($row["member"]),'name' => $row["name"],'id' =>$row["id"], 'member' => $row["member"],'idnumber' => $row["idnumber"]);
			}

			// 沒開發票  沒填收入成本
			if($receipt == 0 && $cost == 0 && $row["idnumber"] != ''){
				$campaign_list_AllNo[] = array('team'=>PacketByName($row["member"]),'name' => $row["name"],'id' =>$row["id"], 'member' => $row["member"],'idnumber' => $row["idnumber"]);

			}
		}
	}


	if(date("d",time()) == 20){
		//每月20號
	}
?>
	有收入成本 沒發票<br>
	<table>
	<tr><td>委刊編號</td><td>案件名稱</td><td>負責業務</td><td>案件ID</td></tr>
	<?php

	for($i=0;$i<count($campaign_list_NoReceipt);$i++){
		echo '<tr>
		<td>'.$campaign_list_NoReceipt[$i]["idnumber"].'</td>
		<td>'.$campaign_list_NoReceipt[$i]["name"].'</td>
		
		<td>'.$campaign_list_NoReceipt[$i]["member"].'</td>
		<td><a href="http://172.16.1.16/jsadways2/campaign_view.php?id='.$campaign_list_NoReceipt[$i]["id"].'" target="_blank">'.
			$campaign_list_NoReceipt[$i]["id"].'</a></td>
	</tr>';
		// echo "<tr><td>".$campaign_list_NoReceipt[$i]["name"]."</td><td>".$campaign_list_NoReceipt[$i]["id"]."</td><td>".$campaign_list_NoReceipt[$i]["member"]."</td></tr>";
	}

	$teamA_count = 0;
	$teamA = '有收入成本 沒發票<br>
	<table>
	<tr><td>委刊編號</td><td>案件名稱</td><td>負責業務</td><td>案件ID</td></tr>';

	$teamB_count = 0;
	$teamB = '有收入成本 沒發票<br>
	<table>
	<tr><td>委刊編號</td><td>案件名稱</td><td>負責業務</td><td>案件ID</td></tr>';

	$teamC_count = 0;
	$teamC = '有收入成本 沒發票<br>
	<table>
	<tr><td>委刊編號</td><td>案件名稱</td><td>負責業務</td><td>案件ID</td></tr>';

	$teamD_count = 0;
	$teamD = '有收入成本 沒發票<br>
	<table>
	<tr><td>委刊編號</td><td>案件名稱</td><td>負責業務</td><td>案件ID</td></tr>';

	for($i=0;$i<count($campaign_list_NoReceipt);$i++){
		if($campaign_list_NoReceipt[$i]["team"] == 'A'){
			$teamA_count++;
			echo $teamA_count;
			$teamA .= '<tr>
			<td>'.$campaign_list_NoReceipt[$i]["idnumber"].'</td>
			<td>'.$campaign_list_NoReceipt[$i]["name"].'</td>
			
			<td>'.$campaign_list_NoReceipt[$i]["member"].'</td>
			<td><a href="http://172.16.1.16/jsadways2/campaign_view.php?id='.$campaign_list_NoReceipt[$i]["id"].'" target="_blank">'.
				$campaign_list_NoReceipt[$i]["id"].'</a></td>
			</tr>';
		}else if($campaign_list_NoReceipt[$i]["team"] == 'B'){
			$teamB_count++;
			echo $teamB_count;
			$teamB .= '<tr>
			<td>'.$campaign_list_NoReceipt[$i]["idnumber"].'</td>
			<td>'.$campaign_list_NoReceipt[$i]["name"].'</td>
			
			<td>'.$campaign_list_NoReceipt[$i]["member"].'</td>
			<td><a href="http://172.16.1.16/jsadways2/campaign_view.php?id='.$campaign_list_NoReceipt[$i]["id"].'" target="_blank">'.
				$campaign_list_NoReceipt[$i]["id"].'</a></td>
			</tr>';
		}else if($campaign_list_NoReceipt[$i]["team"] == 'C'){
			$teamC_count++;
			echo $teamC_count;

			$teamC .= '<tr>
			<td>'.$campaign_list_NoReceipt[$i]["idnumber"].'</td>
			<td>'.$campaign_list_NoReceipt[$i]["name"].'</td>
			
			<td>'.$campaign_list_NoReceipt[$i]["member"].'</td>
			<td><a href="http://172.16.1.16/jsadways2/campaign_view.php?id='.$campaign_list_NoReceipt[$i]["id"].'" target="_blank">'.
				$campaign_list_NoReceipt[$i]["id"].'</a></td>
			</tr>';
		}else if($campaign_list_NoReceipt[$i]["team"] == 'D'){
			$teamD_count++;
			echo $teamD_count;

			$teamD .= '<tr>
			<td>'.$campaign_list_NoReceipt[$i]["idnumber"].'</td>
			<td>'.$campaign_list_NoReceipt[$i]["name"].'</td>
			
			<td>'.$campaign_list_NoReceipt[$i]["member"].'</td>
			<td><a href="http://172.16.1.16/jsadways2/campaign_view.php?id='.$campaign_list_NoReceipt[$i]["id"].'" target="_blank">'.
				$campaign_list_NoReceipt[$i]["id"].'</a></td>
			</tr>';
		}
	}



	$teamA .= "</table>";


	$teamB .= "</table>";


	$teamC .= "</table>";

	$teamD .= "</table>";

	if($teamA_count >0){
		// send_mail("Abow@js-adways.com.tw","Cony",$teamA,'至 2015-12-31止'."A Team 有收入成本，未開發票！案件","廣告系統");
		send_mail("Abow@js-adways.com.tw","Cony",$teamA,'至 2015-12-31止'."品牌 1 Team 有收入成本，未開發票！案件","廣告系統");
		send_mail("finance@js-adways.com.tw","Cony",$teamA,'至 2015-12-31止'."品牌 1 Team 有收入成本，未開發票！案件","廣告系統");
	}
	if($teamB_count > 0){
		send_mail("Abow@js-adways.com.tw","Cony",$teamB,'至 2015-12-31止'."品牌 2 Team 有收入成本，未開發票！案件","廣告系統");
		send_mail("finance@js-adways.com.tw","Cony",$teamB,'至 2015-12-31止'."品牌 2 Team 有收入成本，未開發票！案件","廣告系統");
	}
	if($teamC_count > 0){
		send_mail("Abow@js-adways.com.tw","Cony",$teamC,'至 2015-12-31止'."遊戲Team 有收入成本，未開發票！案件","廣告系統");
		send_mail("finance@js-adways.com.tw","Cony",$teamC,'至 2015-12-31止'."遊戲Team 有收入成本，未開發票！案件","廣告系統");
	}
	if($teamD_count > 0){
		send_mail("Abow@js-adways.com.tw","Cony",$teamD,'至 2015-12-31止'."海外Team 有收入成本，未開發票！案件","廣告系統");
		send_mail("finance@js-adways.com.tw","Cony",$teamD,'至 2015-12-31止'."海外Team 有收入成本，未開發票！案件","廣告系統");
	}
	?>
	

	<?php
	

	echo "</table>";

	echo '<br><br><br>有發票 沒收入成本';
	echo "<table>";
	echo "<tr><td>委刊編號</td><td>案件名稱</td><td>負責業務</td><td>案件ID</td></tr>";
		
	for($i=0;$i<count($campaign_list_NoCost);$i++){
		echo '<tr>
		<td>'.$campaign_list_NoCost[$i]["idnumber"].'</td>
		<td>'.$campaign_list_NoCost[$i]["name"].'</td>
		
		<td>'.$campaign_list_NoCost[$i]["member"].'</td>
		<td><a href="http://172.16.1.16/jsadways2/campaign_view.php?id='.$campaign_list_NoCost[$i]["id"].'" target="_blank">'.
			$campaign_list_NoCost[$i]["id"].'</a></td>
	</tr>';
	}

	echo "</table>";

	echo '<br><br><br>沒收入成本 沒發票';
	echo "<table>";
	echo "<tr><td>委刊編號</td><td>案件名稱</td><td>負責業務</td><td>案件ID</td></tr>";
		
	for($i=0;$i<count($campaign_list_NoReceipt);$i++){
		echo '<tr>
		<td>'.$campaign_list_AllNo[$i]["idnumber"].'</td>
		<td>'.$campaign_list_AllNo[$i]["name"].'</td>
		<td>'.$campaign_list_AllNo[$i]["member"].'</td>
		<td><a href="http://172.16.1.16/jsadways2/campaign_view.php?id='.$campaign_list_AllNo[$i]["id"].'" target="_blank">'.
			$campaign_list_AllNo[$i]["id"].'</a></td>
	</tr>';
	}

	echo "</table>";

function checkReceipt($campaign_id){
	$sql = "SELECT id from receipt where campaign_id = $campaign_id";
	$result=mysql_query($sql);
	if (mysql_num_rows($result)>0){
		return true;	//有發票
	}else{
		return false;	//無發票
	}
}

function mediaSearch($campaign_id){	//已找沒有填收入成本 為主
	$sql = "SELECT id from media where id > 0 order by id ASC";
	$result=mysql_query($sql);
	$cost = 0; //如果有沒填收入成本 累加
	$have_media= 0;
	while($row=mysql_fetch_array($result)){
		$sql_media = "SELECT text1,text2,text5,text6,text9,text10 from media".$row["id"]." WHERE campaign_id = $campaign_id and cue = 2";
		// echo $sql_media.'<br>';
		
		$result2=mysql_query($sql_media);
		if (mysql_num_rows($result2)>0){
			while($row2=mysql_fetch_array($result2)){
				$have_media++;
				if($row2["text1"] != null || $row2["text2"] != null || $row2["text5"] != null || $row2["text6"] != null || $row2["text9"] != null || $row2["text10"] != null){

				}else{
					$cost++;
				}
			}
		}
	}
	if($have_media>0){
		if($cost > 0){
			return false;	//有媒體沒填收入成本
		}else{
			return true;
		}
	}else{
		return false;  // 沒有媒體
	}
}

function PacketByName($name){
	$team = '';
	switch ($name) {
		case '王登順':
			# code...
			$team = 'A';
			break;
		case '朱家葦':
			# code...
			$team = 'A';
			break;

		case '林建廷':
			# code...
			$team = 'A';
			break;
		case '鍾雅妮':
			# code...
			$team = 'A';
			break;
		case '張家豪':
			# code...
			$team = 'A';
			break;
		case '梁瑋真':
			# code...
			$team = 'A';
			break;
		case '王宛柔':
			# code...
			$team = 'B';
			break;
		case '鄭曦':
			# code...
			$team = 'B';
			break;
		case '黃婉菁':
			# code...
			$team = 'B';
			break;
		case '王詩惠':
			# code...
			$team = 'B';
			break;
		case '曾敏睿':
			# code...
			$team = 'B';
			break;
		case '小池舞衣':
			# code...
			$team = 'D';
			break;
		case '楊凱淇':
			# code...
			$team = 'D';
			break;
		case '林雨欣':
			# code...
			$team = 'C';
			break;
		case '曾世強':
			# code...
			$team = 'C';
			break;
		case '廖家葦':
			# code...
			$team = 'C';
			break;
		
		default:
			$team = 'O';
			# code...
			break;
	}

	return $team;
}
?>