<?php
ob_start();
session_start();
ini_set( "memory_limit", "256M");
//header("Content-Type:text/html; charset=utf-8");
header('Content-type:application/vnd.ms-excel');  //宣告網頁格式
$xlsFilename = 'mycrm.xls';
header("Content-disposition: attachment; filename=\"".addslashes($xlsFilename)."\";");
include('../include/db.inc.php');
include('crm_function.php');
// $search_list = $_POST['search_list'];
if(isset($_POST['Aname'])){
	$Aname = $_POST['Aname'];
}else{
	$Aname = array();
}
if(isset($_POST['Cname'])){
	$Cname = $_POST['Cname'];
}else{
	$Cname = array();
}
$search_list = $Aname + $Cname;

sort($search_list); //排序 代理商優先 （因代理商是 agency 英文排序較前
//var_dump($search_list);
$start_time = strtotime($_POST['start_time']);
$end_time = strtotime($_POST['end_time']);//.'+1 DAY'

//echo $end_time;
//取得全媒體 用於 1個對外媒體  有多對內媒體 ， 撈 多對內媒體 對應的 外媒體名稱
$sql = "SELECT id,name FROM media order by id";
$result=mysql_query($sql);
if (mysql_num_rows($result)>0){
	while($row=mysql_fetch_array($result)){
		$media_name[] = $row["name"];
	}
}

//媒體選擇
if($_POST['search_item'] == 1){
	//取得 媒體列表 //return ary ex: array([0] => array('id' => 2,'name' => 'Facbook','costper' =>'CPC');
	if(isset($_POST['media_chaose'])){ //如果有勾選全部媒體 ALL
		$media_list = searchMedia();
	}else{
		if(isset($_POST['search_media'])){
			$si_ary = $_POST['search_media'];
			$si_value = implode (',', $si_ary);
			//var_dump($myallsport);// => string(36) "28,32,87,64,58,76,72,78,86,90,40,102" 
			$media_list = searchMediaById($si_value);
		}else{
			$media_list = searchMedia();
		}
	}
}else if($_POST['search_item'] == 2){
	if(isset($_POST['media_type1'])){ //如果有勾選全部媒體 ALL
		$media_list = searchMediaByType("'PC広告売上','SP APP売上','SP広告売上','その他売上'");
	}else{
		if(isset($_POST['media_type'])){
			$si_ary = $_POST['media_type'];
			$si_value = implode ("','", $si_ary);
			//var_dump($si_value);// => string(36) "28,32,87,64,58,76,72,78,86,90,40,102" 
			//exit();
			$si_value = "'".$si_value."'";
			$media_list = searchMediaByType($si_value);
		}else{
			$media_list = searchMediaByType("'PC広告売上','SP APP売上','SP広告売上','その他売上'");
		}
	}
}

//媒體選擇 結束

//營業額選擇
if (isset($_POST["search_turnover"])) {
	switch ($_POST["search_turnover"]) {
		case 1:
			# code...
			$search_turnover = 1;
			$turnover = '預估營業額 (對外CUE，包含未審核、審核中)';
			break;
		case 2:
			# code...
			$search_turnover = 2;	
			$turnover = '對外CUE (執行中、結案的案件)';
			break;
		case 3:
			# code...
			$search_turnover = 3;
			$turnover = '媒體營業額（撈對內CUE）';
			break;
		default:
			# code...
			$search_turnover = 1;
			$turnover = '預估營業額 (對外CUE，包含未審核、審核中)';
			break;
	}
}

//顯示欄位
$show_case_num = 0; //案件總數
$show_case_name = 0; //	案件列表 

$show_case_incost = 0; //收入 成本
$show_case_profit = 0; //	毛利
$show_case_per = 0; //毛利率
if(isset($_POST['media_out4'])){
	$show_case_num = 1;
}
if(isset($_POST['media_out5'])){
	$show_case_name = 1;
}
if(isset($_POST['media_out6'])){
	$show_case_incost = 1;
}
if(isset($_POST['media_out7'])){
	$show_case_profit = 1;
}
if(isset($_POST['media_out8'])){
	$show_case_per = 1;
}
if(isset($_POST['media_out1'])){
	$show_case_num = 1; //案件總數
	$show_case_name = 1; //	案件列表 
	$show_case_incost = 1; //收入 成本
	$show_case_profit = 1; //毛利
	$show_case_per = 1; //毛利率
}
?>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
<style type="text/css">
	td {
    padding: 0px;
}
</style>
<table border="0">
	<tr>
		<td>撈選區間</td><td><?php echo $_POST['start_time'];?></td><td>～</td><td><?php echo $_POST['end_time'];?></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
	</tr>
	<tr>
		<td></td>
	</tr>
	<tr>
		<td>撈選客戶(廣告主)</td><td>撈選客戶(代理商)</td><td>媒體類型</td><td>媒體選擇</td><td><?php echo $turnover?></td><td><?php echo $show_case_num ? '案件總數':'' ;?></td><td><?php echo $show_case_name ? '案件列表（撈案件名稱）':'' ;?></td>
		<?php
		if($search_turnover == 2){
			echo '<td>發票營業額（實際開發票）</td>';
		}
		?>
		<td><?php echo $show_case_name ? '對外媒體名稱':'' ;?></td><td><?php echo $show_case_incost ? '後台收入值':'' ;?></td><td><?php echo $show_case_incost ? '後台給媒體的支出成本':'' ;?></td><td><?php echo $show_case_profit ? '毛利':'' ;?></td><td><?php echo $show_case_per ? '毛利率（實際）':'' ;?></td><td>案件ID</td>
	</tr>
<?php
//發票使用
$receipt_ary = array();
$caseTime = isset($_POST['castTime']) ? $_POST['castTime']:'2';

for($i=0;$i<count($search_list);$i++){

	//判斷是否為代理商
	$AorC = explode(",",$search_list[$i]);
	if($AorC[0] == 'agency')//代理商
	{
		echo '<tr>
				<td></td><td>'.$AorC[2].'</td><td></td><td></td><td></td><td></td><td></td>';
		if($search_turnover == 2){
			echo '<td></td>';
		}
		echo '<td></td><td></td><td></td><td></td><td></td><td></td>
			</tr>';
		
		//searchCampaign 回傳 回傳 { [0]=> array(2) { ["id"]=> string(4) "1059" ["name"]=> string(19) "La new-1月SP活動" }  
		if($search_turnover == 3){
			if(!$caseTime == 3){
				$campaignID = searchCampaign('AND agency_id='.$AorC[1],$start_time,$end_time,'2,3,4,5,8',$caseTime);
			}else if($caseTime == 3){
				$campaignID = receiptToCampaign($AorC[0],$AorC[1],$start_time,$end_time,'2,3,4,5,8');
			}
		}else if($search_turnover == 1){
			if(!$caseTime == 3){
				$campaignID = searchCampaign('AND agency_id='.$AorC[1],$start_time,$end_time,'1,2,3,4,5',$caseTime);
			}else if($caseTime == 3){
				$campaignID = receiptToCampaign($AorC[0],$AorC[1],$start_time,$end_time,'1,2,3,4,5');
			}
		}else if($search_turnover == 2){
			if(!$caseTime == 3){
				$campaignID = searchCampaign('AND agency_id='.$AorC[1],$start_time,$end_time,'3,4',$caseTime);
			}else if($caseTime == 3){
				$campaignID = receiptToCampaign($AorC[0],$AorC[1],$start_time,$end_time,'3,4');
			}
		}
		//var_dump($campaignID);


		//外匯調整數 $campaign_exchang 
		$campaign_exchang = 0;
		//初始化 $campaign_id_list
		$campaign_id_list = '';
		for($campaign_i = 0;$campaign_i < count($campaignID);$campaign_i++){
			//echo $campaignID[$campaign_i]['id'].':'.$campaignID[$campaign_i]['name'].'<br>';
			$campaign_id_list .= $campaignID[$campaign_i]['id'].',';
			//累加 各案件 外匯調整數
			$campaign_exchang += $campaignID[$campaign_i]['exchang_math'];
		}
		$campaign_id_list .= '0';


		
		//初始化 媒體類型 costper 媒體類型 總和 發票金額總和
		$media_inovice_total = '';
		$media_costper = '';
		$media_costper_total = 0;
		//案件總數
		$media_count = 0;

		for($media_i=0;$media_i < count($media_list);$media_i++){
			//判斷媒體選擇類別
			if($_POST['search_item'] == 1){
				//如果媒體類型不同時
				if($media_costper != $media_list[$media_i]['costper'] || !isset($media_list[$media_i+1]['costper'])){
					//重設 媒體類型 總和
					if(($media_costper != $media_list[$media_i]['costper'] || !isset($media_list[$media_i+1]['costper'])) && $media_costper_total > 0){

						?>
						<tr bgcolor="#fce699">
								<td></td><td><?php echo $AorC[2]; ?></td><td><?php echo $media_costper;?></td><td>加總</td><td><?php echo $media_costper_total;?></td><td><?php echo $show_case_num ? $media_count:'' ;?></td><td></td>
								<?php 
								if($search_turnover == 2){
									echo '<td>'.$media_inovice_total.'</td>';
								}
								?>
								<td></td><td></td><td></td><td></td><td></td><td></td>
							</tr>
						<?php
						$media_costper_total = 0;
						$media_count = 0;
						$media_inovice_total = 0;
					}
					if(isset($media_list[$media_i+1]['costper'])){
						echo '<tr bgcolor="#fce699">
								<td></td><td></td><td>'.$media_list[$media_i]['costper'].'</td><td></td><td></td><td></td><td></td>';
					}
					if($search_turnover == 2){
						echo '<td></td>';
					}	
					echo '<td></td><td></td><td></td><td></td><td></td><td></td><td></td>
						</tr>';

				}
			}else if($_POST['search_item'] == 2){
				//如果媒體類型不同時
				if(($media_costper != $media_list[$media_i]['type2']) || !isset($media_list[$media_i+1]['type2'])){
					//重設 媒體類型 總和
					if((($media_costper != $media_list[$media_i]['type2']) || !isset($media_list[$media_i+1]['type2'])) && $media_costper_total > 0){
						?>
						<tr bgcolor="#fce699">
								<td></td><td><?php echo $AorC[2]; ?></td><td><?php echo $media_costper;?></td><td>加總</td><td><?php echo $media_costper_total;?></td><td><?php echo $show_case_num ? $media_count:'' ;?></td>
								<?php 
								if($search_turnover == 2){
									echo '<td>'.$media_inovice_total.'</td>';
								}
								?>
								<td></td><td></td><td></td><td></td><td></td><td></td>
							</tr>
						<?php
						$media_costper_total = 0;
						$media_count = 0;
					}
					if(isset($media_list[$media_i+1]['type2'])){
						echo '<tr bgcolor="#fce699">
								<td></td><td></td><td>'.$media_list[$media_i]['type2'].'</td><td></td><td></td><td></td><td></td>';
					}
					if($search_turnover == 2){
						echo '<td></td>';
					}	
					echo '<td></td><td></td><td></td><td></td><td></td><td></td>
						</tr>';
				}
			}




			//在各媒體中跑案件列表 找出此媒體中是否有此案件的 總價 收入  成本 a=>對應的對外媒體id
			for($campaign_i = 0;$campaign_i < count($campaignID);$campaign_i++){
				//針對媒體cue表 撈 總價 收入  成本
				if($search_turnover == 3){	// 媒體營業額（撈對內CUE）
					$cost = costFromMedia( $media_list[$media_i]['id'] , $campaignID[$campaign_i]['id'],2);
					if($cost[0] != 0 || $cost[1] != 0||$cost[2] != 0){
						//echo $media_list[$media_i]['id'].$media_list[$media_i]['name'].$media_list[$media_i]['costper'].'=> 總價：'.$cost[0].'收入：'.$cost[1].'成本：'.$cost[2].'<br>';
						if($cost[1] != 0||$cost[2] != 0){
							$pro_avg = ($cost[1] - $cost[2]) / $cost[1];
							$pro_ans = round($pro_avg, 2); 
							$pro_ans = $pro_ans*100;
						}else{
							$pro_ans = 0;
						}

						//初始化 對外媒體
						$cue1_media_name = '';
						//檢查costFromMediac回傳  a 對應的對外媒體是否存在 （對外cue 沒有此值） a = $cost[3]
						if($cost[3] > 0){
							if($media_list[$media_i]['name'] != $media_name[$cost[3]]){
								$cue1_media_name = $media_name[$cost[3]];
							}
						}
						?>
							<tr>
								<td></td><td></td><td></td><td><?php echo $media_list[$media_i]['name'];?></td><td><?php echo $cost[0];?></td><td><?php echo $show_case_num ? '1':'' ;?></td><td><?php echo $show_case_name ? $campaignID[$campaign_i]['name']:'' ;?></td><td><?php echo $show_case_name ? $cue1_media_name:'' ;?></td><td><?php echo $show_case_incost ? $cost[1]:'' ;?></td><td><?php echo $show_case_incost ? $cost[2]:'' ;?></td><td><?php echo $show_case_profit ? ($cost[1] - $cost[2]):'';?></td><td><?php echo $show_case_per ? $pro_ans.'%':'';?></td><td><?php echo  $campaignID[$campaign_i]['id']; ?></td>
							</tr>
						<?php
						$media_costper_total += $cost[0]; //累加媒體營業額
						$media_count++;//案件總數
					}
				}else if($search_turnover == 1){	//預估營業額 (對外CUE，包含未審核、審核中)
					$cost = costFromMedia( $media_list[$media_i]['id'] , $campaignID[$campaign_i]['id'],1);
					if($cost[0] != 0 || $cost[1] != 0||$cost[2] != 0){
						//echo $media_list[$media_i]['id'].$media_list[$media_i]['name'].$media_list[$media_i]['costper'].'=> 總價：'.$cost[0].'收入：'.$cost[1].'成本：'.$cost[2].'<br>';
						if($cost[1] != 0||$cost[2] != 0){
							$pro_avg = ($cost[1] - $cost[2]) / $cost[1];
							$pro_ans = round($pro_avg, 2); 
							$pro_ans = $pro_ans*100;
						}else{
							$pro_ans = 0;
						}

						//初始化 對外媒體
						$cue1_media_name = '';
						//檢查costFromMediac回傳  a 對應的對外媒體是否存在 （對外cue 沒有此值） a = $cost[3]
						if($cost[3] > 0){
							if($media_list[$media_i]['name'] != $media_name[$cost[3]]){
								$cue1_media_name = $media_name[$cost[3]];
							}
						}
						?>
							<tr>
								<td></td><td></td><td></td><td><?php echo $media_list[$media_i]['name'];?></td><td><?php echo $cost[0];?></td><td><?php echo $show_case_num ? '1':'' ;?></td><td><?php echo $show_case_name ? $campaignID[$campaign_i]['name']:'' ;?></td><td><?php echo $show_case_name ? $cue1_media_name:'' ;?></td><td><?php echo $show_case_incost ? '':'' ;?></td><td><?php echo $show_case_incost ? '':'' ;?></td><td><?php echo $show_case_profit ? '':'';?></td><td><?php echo $show_case_per ? $pro_ans.'%':'';?></td><td><?php echo  $campaignID[$campaign_i]['id']; ?></td>
							</tr>
						<?php
						$media_costper_total += $cost[0]; //累加媒體營業額
						$media_count++;//案件總數
					}
				}else if($search_turnover == 2){	//發票營業額（結案&對外CUE，實際開發票）
					$cost = costFromMedia( $media_list[$media_i]['id'] , $campaignID[$campaign_i]['id'],1);
					if($cost[0] != 0 || $cost[1] != 0||$cost[2] != 0){
						//echo $media_list[$media_i]['id'].$media_list[$media_i]['name'].$media_list[$media_i]['costper'].'=> 總價：'.$cost[0].'收入：'.$cost[1].'成本：'.$cost[2].'<br>';
						$pro_ans = 0;


						//初始化 對外媒體
						$cue1_media_name = '';

						?>
							<tr>
								<td></td><td></td><td></td><td><?php echo $media_list[$media_i]['name'];?></td><td><?php echo $cost[0];?></td><td><?php echo $show_case_num ? '1':'' ;?></td><td><?php echo $show_case_name ? $campaignID[$campaign_i]['name']:'' ;?></td>
						<?php
							//發票營業額（實際開發票） 
							if(isset($receipt_ary[$campaignID[$campaign_i]['id']])){
								$receipt = '因無法判斷分媒體發票，總額只顯示在最前方媒體';
							}else{
								$receipt_ary[$campaignID[$campaign_i]['id']] = campaignOfReceipt($campaignID[$campaign_i]['id'],$start_time,$end_time);
								$receipt = $receipt_ary[$campaignID[$campaign_i]['id']];
								$media_inovice_total += $receipt_ary[$campaignID[$campaign_i]['id']];
							}
							echo '<td>'.$receipt.'</td>';
						?>

								<td><?php echo $show_case_name ? $cue1_media_name:'' ;?></td><td><?php echo $show_case_incost ? '':'' ;?></td><td><?php echo $show_case_incost ? '':'' ;?></td><td><?php echo $show_case_profit ? '':'';?></td><td><?php echo $show_case_per ? $pro_ans.'%':'';?></td><td><?php echo  $campaignID[$campaign_i]['id']; ?></td>
							</tr>
						<?php
						$media_costper_total += $cost[0]; //累加媒體營業額
						$media_count++;//案件總數
					}
				}
			}
			
			if($_POST['search_item'] == 1){
				//媒體類型不同時 設定 媒體類型  
				if($media_costper != $media_list[$media_i]['costper']){
					$media_costper = $media_list[$media_i]['costper'];
				}
			}else if($_POST['search_item'] == 2){
				//媒體類型不同時 設定 媒體類型  
				if($media_costper != $media_list[$media_i]['type2']){
					$media_costper = $media_list[$media_i]['type2'];
				}
			}
		}
		//最後加上外匯調整數
		echo '<tr>
				<td></td><td></td><td>外匯調整數</td><td></td><td>'.$campaign_exchang.'</td><td></td><td></td>';
		if($search_turnover == 2){
			echo '<td></td>';
		}
		echo '<td></td><td></td><td></td><td></td><td></td><td></td>
			</tr>';

	}
	else if($AorC[0] == 'client') //廣告主
	{
		echo '<tr>
				<td>'.$AorC[2].'</td><td></td><td></td><td></td><td></td><td></td><td></td>';
		if($search_turnover == 2){
			echo '<td></td>';
		}
		echo '<td></td><td></td><td></td><td></td><td></td><td></td>
			</tr>';
		
		//searchCampaign 回傳 回傳 { [0]=> array(2) { ["id"]=> string(4) "1059" ["name"]=> string(19) "La new-1月SP活動" }  
		if($search_turnover == 3){
			if(!$caseTime == 3){
				$campaignID = searchCampaign('AND client_id='.$AorC[1],$start_time,$end_time,'2,3,4,5,8',$caseTime);
			}else if($caseTime == 3){
				$campaignID = receiptToCampaign($AorC[0],$AorC[1],$start_time,$end_time,'2,3,4,5,8');
			}
		}else if($search_turnover == 1){
			if(!$caseTime == 3){
				$campaignID = searchCampaign('AND client_id='.$AorC[1],$start_time,$end_time,'1,2,3,4,5',$caseTime);
			}else if($caseTime == 3){
				$campaignID = receiptToCampaign($AorC[0],$AorC[1],$start_time,$end_time,'1,2,3,4,5');
			}
		}else if($search_turnover == 2){
			if(!$caseTime == 3){
				$campaignID = searchCampaign('AND client_id='.$AorC[1],$start_time,$end_time,'3,4',$caseTime);
			}else if($caseTime == 3){
				$campaignID = receiptToCampaign($AorC[0],$AorC[1],$start_time,$end_time,'3,4');
			}
		}
		//var_dump($campaignID);


		//外匯調整數 $campaign_exchang 
		$campaign_exchang = 0;
		//初始化 $campaign_id_list
		$campaign_id_list = '';
		for($campaign_i = 0;$campaign_i < count($campaignID);$campaign_i++){
			//echo $campaignID[$campaign_i]['id'].':'.$campaignID[$campaign_i]['name'].'<br>';
			$campaign_id_list .= $campaignID[$campaign_i]['id'].',';
			//累加 各案件 外匯調整數
			$campaign_exchang += $campaignID[$campaign_i]['exchang_math'];
		}
		$campaign_id_list .= '0';


		
		//初始化 媒體類型 costper 媒體類型 總和 發票金額總和
		$media_inovice_total = '';
		$media_costper = '';
		$media_costper_total = 0;
		//案件總數
		$media_count = 0;

		for($media_i=0;$media_i < count($media_list);$media_i++){
			//判斷媒體選擇類別
			if($_POST['search_item'] == 1){
				//如果媒體類型不同時
				if($media_costper != $media_list[$media_i]['costper'] || !isset($media_list[$media_i+1]['costper'])){
					//重設 媒體類型 總和
					if(($media_costper != $media_list[$media_i]['costper'] || !isset($media_list[$media_i+1]['costper'])) && $media_costper_total > 0){

						?>
						<tr bgcolor="#fce699">
								<td><?php echo $AorC[2]; ?></td><td></td><td><?php echo $media_costper;?></td><td>加總</td><td><?php echo $media_costper_total;?></td><td><?php echo $show_case_num ? $media_count:'' ;?></td><td></td>
								<?php 
								if($search_turnover == 2){
									echo '<td>'.$media_inovice_total.'</td>';
								}
								?>
								<td></td><td></td><td></td><td></td><td></td><td></td>
							</tr>
						<?php
						$media_costper_total = 0;
						$media_count = 0;
						$media_inovice_total = 0;
					}
					if(isset($media_list[$media_i+1]['costper'])){
						echo '<tr bgcolor="#fce699">
								<td></td><td></td><td>'.$media_list[$media_i]['costper'].'</td><td></td><td></td><td></td><td></td>';
					}
					if($search_turnover == 2){
						echo '<td></td>';
					}	
					echo '<td></td><td></td><td></td><td></td><td></td><td></td><td></td>
						</tr>';

				}
			}else if($_POST['search_item'] == 2){
				//如果媒體類型不同時
				if(($media_costper != $media_list[$media_i]['type2']) || !isset($media_list[$media_i+1]['type2'])){
					//重設 媒體類型 總和
					if((($media_costper != $media_list[$media_i]['type2']) || !isset($media_list[$media_i+1]['type2'])) && $media_costper_total > 0){
						?>
						<tr bgcolor="#fce699">
								<td><?php echo $AorC[2]; ?></td><td></td><td><?php echo $media_costper;?></td><td>加總</td><td><?php echo $media_costper_total;?></td><td><?php echo $show_case_num ? $media_count:'' ;?></td>
								<?php 
								if($search_turnover == 2){
									echo '<td>'.$media_inovice_total.'</td>';
								}
								?>
								<td></td><td></td><td></td><td></td><td></td><td></td>
							</tr>
						<?php
						$media_costper_total = 0;
						$media_count = 0;
					}
					if(isset($media_list[$media_i+1]['type2'])){
						echo '<tr bgcolor="#fce699">
								<td></td><td></td><td>'.$media_list[$media_i]['type2'].'</td><td></td><td></td><td></td><td></td>';
					}
					if($search_turnover == 2){
						echo '<td></td>';
					}	
					echo '<td></td><td></td><td></td><td></td><td></td><td></td>
						</tr>';
				}
			}




			//在各媒體中跑案件列表 找出此媒體中是否有此案件的 總價 收入  成本 a=>對應的對外媒體id
			for($campaign_i = 0;$campaign_i < count($campaignID);$campaign_i++){
				//針對媒體cue表 撈 總價 收入  成本
				if($search_turnover == 3){	// 媒體營業額（撈對內CUE）
					$cost = costFromMedia( $media_list[$media_i]['id'] , $campaignID[$campaign_i]['id'],2);
					if($cost[0] != 0 || $cost[1] != 0||$cost[2] != 0){
						//echo $media_list[$media_i]['id'].$media_list[$media_i]['name'].$media_list[$media_i]['costper'].'=> 總價：'.$cost[0].'收入：'.$cost[1].'成本：'.$cost[2].'<br>';
						if($cost[1] != 0||$cost[2] != 0){
							$pro_avg = ($cost[1] - $cost[2]) / $cost[1];
							$pro_ans = round($pro_avg, 2); 
							$pro_ans = $pro_ans*100;
						}else{
							$pro_ans = 0;
						}

						//初始化 對外媒體
						$cue1_media_name = '';
						//檢查costFromMediac回傳  a 對應的對外媒體是否存在 （對外cue 沒有此值） a = $cost[3]
						if($cost[3] > 0){
							if($media_list[$media_i]['name'] != $media_name[$cost[3]]){
								$cue1_media_name = $media_name[$cost[3]];
							}
						}
						?>
							<tr>
								<td></td><td></td><td></td><td><?php echo $media_list[$media_i]['name'];?></td><td><?php echo $cost[0];?></td><td><?php echo $show_case_num ? '1':'' ;?></td><td><?php echo $show_case_name ? $campaignID[$campaign_i]['name']:'' ;?></td><td><?php echo $show_case_name ? $cue1_media_name:'' ;?></td><td><?php echo $show_case_incost ? $cost[1]:'' ;?></td><td><?php echo $show_case_incost ? $cost[2]:'' ;?></td><td><?php echo $show_case_profit ? ($cost[1] - $cost[2]):'';?></td><td><?php echo $show_case_per ? $pro_ans.'%':'';?></td><td><?php echo  $campaignID[$campaign_i]['id']; ?></td>
							</tr>
						<?php
						$media_costper_total += $cost[0]; //累加媒體營業額
						$media_count++;//案件總數
					}
				}else if($search_turnover == 1){	//預估營業額 (對外CUE，包含未審核、審核中)
					$cost = costFromMedia( $media_list[$media_i]['id'] , $campaignID[$campaign_i]['id'],1);
					if($cost[0] != 0 || $cost[1] != 0||$cost[2] != 0){
						//echo $media_list[$media_i]['id'].$media_list[$media_i]['name'].$media_list[$media_i]['costper'].'=> 總價：'.$cost[0].'收入：'.$cost[1].'成本：'.$cost[2].'<br>';
						if($cost[1] != 0||$cost[2] != 0){
							$pro_avg = ($cost[1] - $cost[2]) / $cost[1];
							$pro_ans = round($pro_avg, 2); 
							$pro_ans = $pro_ans*100;
						}else{
							$pro_ans = 0;
						}

						//初始化 對外媒體
						$cue1_media_name = '';
						//檢查costFromMediac回傳  a 對應的對外媒體是否存在 （對外cue 沒有此值） a = $cost[3]
						if($cost[3] > 0){
							if($media_list[$media_i]['name'] != $media_name[$cost[3]]){
								$cue1_media_name = $media_name[$cost[3]];
							}
						}
						?>
							<tr>
								<td></td><td></td><td></td><td><?php echo $media_list[$media_i]['name'];?></td><td><?php echo $cost[0];?></td><td><?php echo $show_case_num ? '1':'' ;?></td><td><?php echo $show_case_name ? $campaignID[$campaign_i]['name']:'' ;?></td><td><?php echo $show_case_name ? $cue1_media_name:'' ;?></td><td><?php echo $show_case_incost ? '':'' ;?></td><td><?php echo $show_case_incost ? '':'' ;?></td><td><?php echo $show_case_profit ? '':'';?></td><td><?php echo $show_case_per ? $pro_ans.'%':'';?></td><td><?php echo  $campaignID[$campaign_i]['id']; ?></td>
							</tr>
						<?php
						$media_costper_total += $cost[0]; //累加媒體營業額
						$media_count++;//案件總數
					}
				}else if($search_turnover == 2){	//發票營業額（結案&對外CUE，實際開發票）
					$cost = costFromMedia( $media_list[$media_i]['id'] , $campaignID[$campaign_i]['id'],1);
					if($cost[0] != 0 || $cost[1] != 0||$cost[2] != 0){
						//echo $media_list[$media_i]['id'].$media_list[$media_i]['name'].$media_list[$media_i]['costper'].'=> 總價：'.$cost[0].'收入：'.$cost[1].'成本：'.$cost[2].'<br>';
						$pro_ans = 0;


						//初始化 對外媒體
						$cue1_media_name = '';

						?>
							<tr>
								<td></td><td></td><td></td><td><?php echo $media_list[$media_i]['name'];?></td><td><?php echo $cost[0];?></td><td><?php echo $show_case_num ? '1':'' ;?></td><td><?php echo $show_case_name ? $campaignID[$campaign_i]['name']:'' ;?></td>
						<?php
							//發票營業額（實際開發票） 
							if(isset($receipt_ary[$campaignID[$campaign_i]['id']])){
								$receipt = '因無法判斷分媒體發票，總額只顯示在最前方媒體';
							}else{
								$receipt_ary[$campaignID[$campaign_i]['id']] = campaignOfReceipt($campaignID[$campaign_i]['id'],$start_time,$end_time);
								$receipt = $receipt_ary[$campaignID[$campaign_i]['id']];
								$media_inovice_total += $receipt_ary[$campaignID[$campaign_i]['id']];
							}
							echo '<td>'.$receipt.'</td>';
						?>

								<td><?php echo $show_case_name ? $cue1_media_name:'' ;?></td><td><?php echo $show_case_incost ? '':'' ;?></td><td><?php echo $show_case_incost ? '':'' ;?></td><td><?php echo $show_case_profit ? '':'';?></td><td><?php echo $show_case_per ? $pro_ans.'%':'';?></td><td><?php echo  $campaignID[$campaign_i]['id']; ?></td>
							</tr>
						<?php
						$media_costper_total += $cost[0]; //累加媒體營業額
						$media_count++;//案件總數
					}
				}
			}
			
			if($_POST['search_item'] == 1){
				//媒體類型不同時 設定 媒體類型  
				if($media_costper != $media_list[$media_i]['costper']){
					$media_costper = $media_list[$media_i]['costper'];
				}
			}else if($_POST['search_item'] == 2){
				//媒體類型不同時 設定 媒體類型  
				if($media_costper != $media_list[$media_i]['type2']){
					$media_costper = $media_list[$media_i]['type2'];
				}
			}
		}
		//最後加上外匯調整數
		echo '<tr>
				<td></td><td></td><td>外匯調整數</td><td></td><td>'.$campaign_exchang.'</td><td></td><td></td>';
		if($search_turnover == 2){
			echo '<td></td>';
		}
		echo '<td></td><td></td><td></td><td></td><td></td><td></td>
			</tr>';
	}
}
?>
<!--
<table>
	<tr>
		<td>撈選區間</td><td><?php echo $_POST['start_time'];?></td><td>～</td><td><?php echo $_POST['end_time'];?></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
	</tr>
	<tr>
		<td></td>
	</tr>
	<tr>
		<td>撈選客戶</td><td>撈選客戶</td><td>媒體類型</td><td>媒體選擇</td><td>媒體營業額（撈對內CUE）</td><td>案件總數</td><td>案件列表（撈案件名稱）</td><td>對外媒體</td><td>成本</td><td>毛利</td><td>毛利率（實際）</td>
	</tr>
	-->
</table>
</body>
</html>