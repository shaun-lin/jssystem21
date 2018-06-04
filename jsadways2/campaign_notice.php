<?php 
	session_start();
	ini_set( "memory_limit", "256M");
	header("Content-Type:text/html; charset=utf-8");
	 // header('Content-type:application/vnd.ms-excel');  //宣告網頁格式
	 // header('Content-Disposition: attachment; filename=mycrm.xls');  //設定檔案名稱
	include('include/db.inc.php');
	include("mail/class.phpmailer.php"); //匯入PHPMailer類別
	$first_day = strtotime(date("Y",time()).'01-01 -1 year');
	$last_day = strtotime(date("Y-m",time()).'-01 +1 month -1 days');


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

			// 沒發票  有收入成本
			if($receipt == 0 && $cost == 1 && ($row["status"] == 3 || $row["status"] == 5 ||$row["status"] == 7)){

				$campaign_list_NoReceipt[]= array('name' => $row["name"],'id' =>$row["id"], 'member' => $row["member"],'idnumber' => $row["idnumber"]);

			}


			// 有開發票  沒填收入成本
			if($receipt == 1 && $cost == 0 && ($row["status"] == 3 || $row["status"] == 5 ||$row["status"] == 7)){
				$campaign_list_NoCost[]= array('name' => $row["name"],'id' =>$row["id"], 'member' => $row["member"],'idnumber' => $row["idnumber"]);
			}

			// 沒開發票  沒填收入成本
			if($receipt == 1 && $cost == 0){
				$campaign_list_AllNo[] = array('name' => $row["name"],'id' =>$row["id"], 'member' => $row["member"],'idnumber' => $row["idnumber"]);

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
	while($row=mysql_fetch_array($result)){
		$sql_media = "SELECT text1,text2,text5,text6,text9,text10 from media".$row["id"]." WHERE campaign_id = $campaign_id and cue = 2";
		// echo $sql_media.'<br>';
		$result2=mysql_query($sql_media);
		if (mysql_num_rows($result2)>0){
			while($row2=mysql_fetch_array($result2)){
				if($row2["text1"] != null || $row2["text2"] != null || $row2["text5"] != null || $row2["text6"] != null || $row2["text9"] != null || $row2["text10"] != null){

				}else{
					$cost++;
				}
			}
		}
	}

	if($cost > 0){
		return false;	//有媒體沒填收入成本
	}else{
		return true;
	}
}
?>