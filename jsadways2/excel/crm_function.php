<?php
//給媒體id 案件 id cue 1or2 回傳 ary([0]=總價,[1]=收入,[2]=成本) 
//如果是 對外cue的話 收入跟成本會不準 因為有套到 調整數   只能使用 回傳陣列的 0 索引值
function costFromMedia($media_id,$campaign_id,$cue){

	//初始化 array
	$costAry = array(0,0,0,0);

	//合併撈 條整數
	$sql = "SELECT me.totalprice,(ifnull(me.text1,0) + ifnull(me.text5,0) + ifnull(me.text9,0) + ifnull(mc.change_income,0)) as incost,(ifnull(me.text2,0) + ifnull(me.text6,0) + ifnull(me.text10,0) + ifnull(mc.change_cost,0)) as cost,a
			
			FROM media".$media_id." me 

			LEFT JOIN media_change mc

			on me.campaign_id = mc.campaign_id

			WHERE me.campaign_id = $campaign_id AND me.cue = $cue AND mc.media_id = $media_id 
			 ";
	$result=mysql_query($sql);
	if (mysql_num_rows($result)>0){
		while($row=mysql_fetch_array($result)){
			$costAry[0] += $row["totalprice"];
			$costAry[1] += $row["incost"];
			$costAry[2] += $row["cost"];
			$costAry[3] = $row["a"];
		}
	}else{
		//沒有調整數的話 單撈媒體
		$sql = "SELECT me.totalprice,(ifnull(me.text1,0) + ifnull(me.text5,0) + ifnull(me.text9,0)) as incost,(ifnull(me.text2,0) + ifnull(me.text6,0) + ifnull(me.text10,0)) as cost,a
			
			FROM media".$media_id." me WHERE me.campaign_id = $campaign_id AND me.cue = $cue
			";
		$result=mysql_query($sql);
		if (mysql_num_rows($result)>0){
			while($row=mysql_fetch_array($result)){
				$costAry[0] += $row["totalprice"];
				$costAry[1] += $row["incost"];
				$costAry[2] += $row["cost"];
				$costAry[3] = $row["a"];
			}
		}
	}

	return $costAry;
}

//給 search_rule ex: AND agency_id= 代理商id  ,起始時間,結束時間 142200230 ,狀態 '3,4,5' ; 回傳 { [0]=> array(2) { ["id"]=> string(4) "1059" ["name"]=> string(19) "La new-1月SP活動" }  [1]=> array(2) { ["id"]=> string(4) "1457" ["name"]=> string(21) "...}}
function searchCampaign($search_rule,$start_time,$end_time,$status,$caseTime){
	if($caseTime == 1){
		$castDate = 'date11';
	}else{
		$castDate = 'date22';
	}

	//初始化 array
	$campaignAry = array();

	$sql = "SELECT id,name,exchang_math FROM campaign WHERE  version = 2 and ($castDate BETWEEN  $start_time AND $end_time) AND status in (".$status.") ".$search_rule;
	//echo $sql;
	$result=mysql_query($sql);
	if (mysql_num_rows($result)>0){
		while($row=mysql_fetch_array($result)){
			//echo $row["id"].'<br>';
			// $campaignAry['id'][] = $row["id"];
			// $campaignAry['name'][] = $row["name"];

			$campaignAry[] = array('id' => $row["id"],'name' =>$row["name"],'exchang_math'=>$row["exchang_math"]);
		}
	}
	//var_dump($campaignAry);
	return $campaignAry;
}

//回傳 array 
function searchMedia(){
	//初始化 array
	$mediaAry = array();
	// 0 CPM , 1 CPC , 2 CPI , 3 網站廣告 , 4 其他 , 5 廣告素材 , 6 寫手費 , 7 差價 , 8 CPA , 9 CPV , 10 CPT,20 海外CPM,21 海外CPC , 22 海外CPI, 29 海外CPV
	$media_list = "SELECT id,name,costper FROM  `media` WHERE id >0 ORDER BY  `type` , costper,id";
	$media_result=mysql_query($media_list);
	if (mysql_num_rows($media_result)>0){
		while($media_row=mysql_fetch_array($media_result)){
			$mediaAry[] = array('id' => $media_row["id"],'name' => $media_row["name"],'costper' => $media_row["costper"]);
		}
	}
	return $mediaAry; 
}

//傳入 id ex: 2,3,4,5,6 回傳 array 
function searchMediaById($id){
	//初始化 array
	$mediaAry = array();
	// 0 CPM , 1 CPC , 2 CPI , 3 網站廣告 , 4 其他 , 5 廣告素材 , 6 寫手費 , 7 差價 , 8 CPA , 9 CPV , 10 CPT,20 海外CPM,21 海外CPC , 22 海外CPI, 29 海外CPV
	$media_list = "SELECT id,name,costper,type2 FROM  `media` WHERE id >0 AND id in (".$id.") ORDER BY  `type` , costper,id";
	$media_result=mysql_query($media_list);
	if (mysql_num_rows($media_result)>0){
		while($media_row=mysql_fetch_array($media_result)){
			$mediaAry[] = array('id' => $media_row["id"],'name' => $media_row["name"],'costper' => $media_row["costper"],'type2' => $media_row["type2"]);
		}
	}
	return $mediaAry; 
}
//傳入 'PC広告売上','SP APP売上','SP広告売上','その他売上' 回傳 array 
function searchMediaByType($value){
	//初始化 array
	$mediaAry = array();
	// 0 CPM , 1 CPC , 2 CPI , 3 網站廣告 , 4 其他 , 5 廣告素材 , 6 寫手費 , 7 差價 , 8 CPA , 9 CPV , 10 CPT,20 海外CPM,21 海外CPC , 22 海外CPI, 29 海外CPV
	$media_list = "SELECT id,name,costper,type2 FROM  `media` WHERE id >0 AND type2 in (".$value.") ORDER BY  `type2` , costper,id";
	$media_result=mysql_query($media_list);
	if (mysql_num_rows($media_result)>0){
		while($media_row=mysql_fetch_array($media_result)){
			$mediaAry[] = array('id' => $media_row["id"],'name' => $media_row["name"],'costper' => $media_row["costper"],'type2' => $media_row["type2"]);
		}
	}
	return $mediaAry; 
}

//傳入 id ex: 2,3,4,5,6 回傳 array 
function campaignOfReceipt($id,$start_time,$end_time){
	$start = date("Ym",$start_time);
	$end = date("Ym",$end_time);
	//AND (datemonth BETWEEN $start AND $end)
	$receipt_value = 0;
	$receipt = "SELECT name ,sum(totalprice1) as totalprice1 FROM `receipt` WHERE `campaign_id` = ".$id." AND status = 1 AND (datemonth BETWEEN $start AND $end)  group by campaign_id";
	$receipt_result=mysql_query($receipt);
	if (mysql_num_rows($receipt_result)>0){
		$receipt_row=mysql_fetch_array($receipt_result);
		$receipt_value = $receipt_row["totalprice1"];
	}

	return $receipt_value;
}

//從發票去撈案件
//傳入 usertype => agency or client    userid => 代理商或直客id   .搜尋的起始時間 案件狀態
function receiptToCampaign($usertype,$userid,$start_time,$end_time,$status){
	$start = strtotime(date("Y-m-d",$start_time)." 00:00:00");
	$end = strtotime(date("Y-m-d",$end_time)." 00:00:00".'+1 DAY')-1;

	//初始化 array
	$campaignAry = array();

	// $sql = "SELECT distinct ca.id,ca.name,ca.exchang_math  FROM `receipt` re
		// LEFT JOIN campaign ca
		// on re.`campaign_id` = ca.id
		// WHERE re.`usertype` = '".$usertype."' and re.`userid` = $userid  AND re.status = 1 AND ca.status in (".$status.") AND (re.datemonth BETWEEN  $start AND $end ) order by ca.id";

		$sql = "SELECT distinct ca.id,ca.name,ca.exchang_math  FROM `receipt` re
		LEFT JOIN campaign ca
		on re.`campaign_id` = ca.id
		WHERE re.`usertype` = '".$usertype."' and re.`userid` = $userid  AND re.status = 1 AND ca.status in (".$status.") AND (re.times2 BETWEEN  $start AND $end ) order by ca.id";

	//echo $sql;
	$result=mysql_query($sql);
	if (mysql_num_rows($result)>0){
		while($row=mysql_fetch_array($result)){
			//echo $row["id"].'<br>';
			// $campaignAry['id'][] = $row["id"];
			// $campaignAry['name'][] = $row["name"];

			$campaignAry[] = array('id' => $row["id"],'name' =>$row["name"],'exchang_math'=>$row["exchang_math"]);
		}
	}
	//var_dump($campaignAry);
	return $campaignAry;
}

?>