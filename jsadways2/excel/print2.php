<?php
	
	if (!isset($parameter)) {
		$parameter = [
			'extra_fee_media' => [133]
		];
	}

	ini_set('memory_limit', '256M');
	require_once dirname(dirname(__DIR__)) .'/autoload.php';
	
	$db = clone($GLOBALS['app']->db);

	IncludeFunctions('jsadways');

	$objMediaAccounting = CreateObject('MediaAccounting');
	$dateYearMonth = sprintf('%04d%02d', $_REQUEST['search3'], $_REQUEST['search4']);

	IncludeFunctions('excel');
	$objPHPExcel = CreateExcelFile();
	$excelActiveSheet = &$objPHPExcel->getActiveSheet();

	CreateNativeDBConnector();

$excelActiveSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
$objPHPExcel->getDefaultStyle()->getFont()->setName('新細明體');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
$excelActiveSheet->getDefaultColumnDimension()->setWidth(18);//設定欄位寬度
$excelActiveSheet->getColumnDimension('A')->setWidth(5);
$excelActiveSheet->getColumnDimension('B')->setWidth(10);
$excelActiveSheet->getColumnDimension('C')->setWidth(10);
$excelActiveSheet->getColumnDimension('D')->setWidth(26);
$excelActiveSheet->getColumnDimension('E')->setWidth(26);
$excelActiveSheet->getColumnDimension('F')->setWidth(34);
$excelActiveSheet->getColumnDimension('G')->setWidth(14);
$excelActiveSheet->getColumnDimension('H')->setWidth(14);
$excelActiveSheet->getColumnDimension('I')->setWidth(10);
$excelActiveSheet->getColumnDimension('O')->setWidth(11);
$excelActiveSheet->getColumnDimension('P')->setWidth(11);
$excelActiveSheet->getColumnDimension('U')->setWidth(10);
$excelActiveSheet->getColumnDimension('V')->setWidth(10);
$excelActiveSheet->getColumnDimension('S')->setWidth(10);
$excelActiveSheet->getColumnDimension('T')->setWidth(10);
$excelActiveSheet->getColumnDimension('W')->setWidth(10);
$excelActiveSheet->getColumnDimension('X')->setWidth(10);
$excelActiveSheet->getColumnDimension('Y')->setWidth(10);
$excelActiveSheet->getColumnDimension('Z')->setWidth(10);
$excelActiveSheet->getColumnDimension('AB')->setWidth(10);
$excelActiveSheet->getColumnDimension('AC')->setWidth(10);
$excelActiveSheet->getColumnDimension('AD')->setWidth(10);
$excelActiveSheet->getColumnDimension('AE')->setWidth(10);
$excelActiveSheet->getColumnDimension('AF')->setWidth(10);
$excelActiveSheet->getColumnDimension('AG')->setWidth(10);
$excelActiveSheet->getColumnDimension('AH')->setWidth(10);
$excelActiveSheet->getColumnDimension('AI')->setWidth(10);

$sqlmedia = "SELECT * FROM media WHERE id=0";
$resultmedia = mysql_query($sqlmedia);
$rowmedia = mysql_fetch_array($resultmedia);
$medianumber=$rowmedia['name']; //媒體數量


$search4 = $_GET['search4'];
if($search4 < 10){
	$search4 = "0".$search4;
}

$number=1;
$excelActiveSheet->getStyle('A'.$number.':AK'.$number)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$excelActiveSheet->getStyle('A'.$number.':AK'.$number)->getFill()->getStartColor()->setRGB('DDDDDD'); 
$array = array(0 => "B", 1 => "C", 2 => "D", 3 => "E", 4 => "F", 5 => "G", 6 => "H", 7 => "I", 8 => "J", 9 => "K", 10 => "L", 11 => "M", 12 => "N", 13 => "O", 14 => "P", 15 => "Q", 16 => "R", 17 => "S", 18 => "T", 19 => "U", 20 => "V", 21 => "W", 22 => "X", 23 => "Y", 24 => "Z", 25 => "AA", 26 => "AB", 27 => "AC", 28 => "AD", 29 => "AE", 30 => "AF", 31 => "AG", 32 => "AH", 33 => "AI", 34 => "AJ", 35 => "AK");
for($j = 0; $j < count($array); $j++){
	for($i=$number;$i<=$number;$i++){
		SetExcellCellBorder($excelActiveSheet, [$array[$j] . $i => 'all']);
		SetExcelCellCenter($excelActiveSheet, [$array[$j] . $i]);
	}
}

SetExcelCellValue($objPHPExcel, [
	'A'. $number => '版本',
	'B'. $number => '委刊號碼',
	'C'. $number => '負責業務',
	'D'. $number => '代理商',
	'E'. $number => '廣告主',
	'F'. $number => '項目',
	'G'. $number => '期間',
	'H'. $number => '期間',
	'I'. $number => '狀態',
	'J'. $number => '發票月份',
	'K'. $number => '回簽日期',
	'L'. $number => '對外媒體【2.0】',
	'M'. $number => '對外總金額【2.0】',
	'N'. $number => '賣價【2.0】',
	'O'. $number => '總數量【2.0】',
	'P'. $number => '媒體',
	'Q'. $number => '分類',
	'R'. $number => '計價方式',
	'S'. $number => '買價',
	'T'. $number => '總數量',
	'U'. $number => '總價',
	'V'. $number => '佣金',
	'W'. $number => '現折',
	'X'. $number => '利潤',
	'Y'. $number => '操作預算',
	'AA'. $number => '總收入(V-W-X)',
	'AB'. $number => $_GET['search4'].'月收入',
	'AC'. $number => $_GET['search4'].'月成本',
	'AD'. $number => $_GET['search4'].'月毛利',
	'AE'. $number => $_GET['search4'].'月毛利率',
	'AF'. $number => '總收入',
	'AG'. $number => '總成本',
	'AH'. $number => '總毛利',
	'AI'. $number => '總毛利率',
	'AJ'. $number => '備註',
	'AK'. $number => '公式備註'
]);

if(($_GET['search4']==1)||($_GET['search4']==3)||($_GET['search4']==5)||($_GET['search4']==7)||($_GET['search4']==8)||($_GET['search4']==10)||($_GET['search4']==12)){
	$endday=31;
}elseif(($_GET['search4']==4)||($_GET['search4']==6)||($_GET['search4']==9)||($_GET['search4']==11)){
	$endday=30;
}elseif($_GET['search4']==2){
	$endday=28;
}

$search2 = '';
if (isset($_REQUEST['search5']) && $_REQUEST['search5'] == 'pm') {
	$pmId = $_REQUEST['search2'];
					
	if (StartsWith($pmId, 'wom-')) {
		$pmId = str_replace('wom-', '', $pmId);
		if (IsId($pmId)) {
			$search2 = sprintf(" AND wommId = %d ", $pmId);
		}
	} else if (StartsWith($pmId, 'media-')) {
		$pmId = str_replace('media-', '', $pmId);
		if (IsId($pmId)) {
			$search2 = sprintf(" AND media_leader = %d ", $pmId);
		}
	}
} else if ($_GET['search2'] != NULL) {
	$search2 = sprintf(' AND memberid = %d ', $_GET['search2']);
}

// $sql2='SELECT * FROM campaign WHERE status <> 8 AND status>=2 AND status <=5  AND ((date11>='.mktime(0,0,0,$_GET['search4'],1,$_GET['search3']).' AND date11<='.mktime(0,0,0,$_GET['search4'],$endday,$_GET['search3']).')OR(date22>='.mktime(0,0,0,$_GET['search4'],1,$_GET['search3']).' AND date22<='.mktime(0,0,0,$_GET['search4'],$endday,$_GET['search3']).')OR(date11<='.mktime(0,0,0,$_GET['search4'],1,$_GET['search3']).' AND date22>='.mktime(0,0,0,$_GET['search4'],$endday,$_GET['search3']).')) '.$search2;
$sql2 = "SELECT campaign.*, MAX(`campaignstatus2`.`times`) as `last_sign` FROM campaign 
		LEFT JOIN `campaignstatus2` ON `campaign`.`id` = `campaignstatus2`.`campaignid` AND `campaignstatus2`.`data` = '按下回簽' 
		WHERE `status` <> 8 AND `status` >= 2 AND `status` <= 5 
		AND (
			( `date11` >= ". mktime(0, 0, 0, $_GET['search4'], 1, $_GET['search3']) .' 
				AND `date11` <= '. mktime(0, 0, 0, $_GET['search4'], $endday, $_GET['search3']) .' 
			) OR ( `date22` >= '. mktime(0, 0, 0, $_GET['search4'], 1, $_GET['search3']) .' 
				AND `date22` <= '. mktime(0, 0, 0, $_GET['search4'], $endday, $_GET['search3']) .' 
			) OR ( `date11` <= '. mktime(0, 0, 0, $_GET['search4'], 1, $_GET['search3']) .' 
				AND `date22` >= '. mktime(0, 0, 0, $_GET['search4'], $endday, $_GET['search3']) ." 
			)
		) $search2 GROUP BY `campaign`.`id`;";

$result2=mysql_query($sql2); 
if (mysql_num_rows($result2)>0){
	while($row2=mysql_fetch_array($result2)){
		$rowsAccounting = $objMediaAccounting->getList($row2['id']);

		$i=$i+1;
		$dateInvoice = sprintf('%d年%d月', $row2['receipt1'], $row2['receipt2']);
		$dateSign = $row2['last_sign'] === null ? '' : date('Y-m-d ', $row2['last_sign']);
		$media1='';
		$media2='';
		$totalprice1=0;
		$totalprice2=0;
		
		$rowsMediaOrdinal = GetUsedMediaOrdinal($row2['id']);
		foreach ($rowsMediaOrdinal as $j) {
			$sql3='SELECT * FROM media'.$j.' WHERE campaign_id = '.$row2['id'].' AND cue=1 ORDER BY id';
			$result3=mysql_query($sql3); 
			if (mysql_num_rows($result3)>0){
				while($row3=mysql_fetch_array($result3)){
					$totalprice1 += $row3['totalprice'];
					$website = $row3['website'];

					if (in_array($j, $parameter['extra_fee_media'])) {
						$website .= empty($row3['channel']) ? '' : " ({$row3['channel']})";
					}

					$media1 .= $website .'=>'. $row3['totalprice'] .'、';
				}
			}
		}
		
		foreach ($rowsMediaOrdinal as $j) {
			$sql3='SELECT * FROM media'.$j.' WHERE campaign_id = '.$row2['id'].' AND cue=2 ORDER BY id';
			$result3=mysql_query($sql3); 
			if (mysql_num_rows($result3)>0){
				while($row3=mysql_fetch_array($result3)){
					$totalprice2=$totalprice2+$row3['totalprice'];
				}
			}
		}
		$totalprice3=$totalprice1-$totalprice2;
		
		foreach ($rowsMediaOrdinal as $j) {
			$mediaclass='';
			$sql3='SELECT * FROM media'.$j.' WHERE campaign_id = '.$row2['id'].' AND cue=2 ORDER BY id';
			$result3=mysql_query($sql3); 
			if (mysql_num_rows($result3)>0){
				while($row3=mysql_fetch_array($result3)){
					$number=$number+1;
					$sql4 = "SELECT * FROM media WHERE id= ".$j;
					$result4 = mysql_query($sql4);
					$row4 = mysql_fetch_array($result4);
					if($row3['a']!=NULL){
						$sql5 = "SELECT * FROM media WHERE id= ".$row3['a'];
						$result5 = mysql_query($sql5);
						$row5 = mysql_fetch_array($result5);
						$media11=$row5['name'];
						$sql6='SELECT * FROM media'.$row3['a'].' WHERE id = '.$row3['a0'];
						$result6 = mysql_query($sql6);
						$row6 = mysql_fetch_array($result6);
						$totalprice11=$row6['totalprice'];
						if(($row6['quantity']==NULL)||($row6['quantity']==0)){
							$quantity11=1;
						}else{
							$quantity11=$row6['quantity'];
						}
						if(($row3['quantity']==NULL)||($row3['quantity']==0)){
							$quantity22=1;
						}else{
							$quantity22=$row3['quantity'];
						}
						$price11=round($totalprice11/$quantity11,2);
						$price22=round($row3['a4']/$quantity22,2);
					}else{
						$media11='';
						$totalprice11='';
						$quantity11='';
						$price11='';
						$price22='';
					}
					if($row2['agency']==NULL){
						$agency='直客';
					}else{
						$agency=$row2['agency'];
					}
					
					$status = getCampaignStatusText($row2['status']);
					
					$a1=0;
					$a2=0;
					$a3=0;
					$a4=0;
					$a5=0;
					$a6=0;
					$a7=0;
					$a8=0;
					
					if (isset($rowsAccounting[$j][$row3['id']]) && count($rowsAccounting[$j][$row3['id']])) {
						foreach ($rowsAccounting[$j][$row3['id']] as $accountingMonth => $accountingDetal) {
							$a5 += $accountingDetal['accounting_revenue'];
							$a6 += $accountingDetal['accounting_cost'];
						}

						if ($a5 || $a6) {
							$a7 = $a5 - $a6;
							$a8 = round($a7 / $a5, 2);
						}
					}
					
					if($row2['version']==2){
						$version=2;
						$aa=$row3['totalprice']-$row3['a1']-$row3['a2'];
					}else{
						$version=1;
						$aa='';
					}

					$search2=$row2['member'];


					if (isset($rowsAccounting[$j][$row3['id']][$dateYearMonth])) {
						$a1 = $rowsAccounting[$j][$row3['id']][$dateYearMonth]['accounting_revenue'];
						$a2 = $rowsAccounting[$j][$row3['id']][$dateYearMonth]['accounting_cost'];

						if ($a1 || $a2) {
							$a3 = $a1 - $a2;
							if ($a3 != 0 && $a1 != 0) {
								$a4 = round($a3 / $a1, 2);
							}
						}
					}

					$website = $row3['website'];
					if (in_array($j, $parameter['extra_fee_media'])) {
						$website .= empty($row3['channel']) ? '' : " ({$row3['channel']})";
					}
					
					SetExcelCellValue($objPHPExcel, [
						'A'. $number => $version,
						'B'. $number => $row2['idnumber'],
						'C'. $number => $row2['member'],
						'D'. $number => $agency,
						'E'. $number => $row2['client'],
						'F'. $number => $row2['name'],
						'G'. $number => $row2['date1'],
						'H'. $number => $row2['date2'],
						'I'. $number => $status,
						'J'. $number => $dateInvoice,
						'K'. $number => $dateSign,
						'L'. $number => $media11,
						'M'. $number => $totalprice11,
						'N'. $number => $price11,
						'O'. $number => $quantity11,
						'P'. $number => $website,
						'Q'. $number => $row4['type2'],
						'R'. $number => $row4['costper'],
						'S'. $number => $price22,
						'T'. $number => round($row3['quantity']),
						'U'. $number => $row3['totalprice'],
						'V'. $number => $row3['a1'],
						'W'. $number => $row3['a2'],
						'X'. $number => $row3['a3'],
						'Y'. $number => $row3['a4'],
						'Z'. $number => in_array($j, $parameter['extra_fee_media']) && isset($row3['action']) && $row3['action'] ? "{$row3['action']}%" : '',
						'AA'. $number => $aa,
						'AB'. $number => $a1,
						'AC'. $number => $a2,
						'AD'. $number => $a3,
						'AE'. $number => $a4,
						'AF'. $number => $a5,
						'AG'. $number => $a6,
						'AH'. $number => $a7,
						'AI'. $number => $a8,
						'AJ'. $number => $row2['others'],
						'AK'. $number => strpos($row3['text13'], '=') === 0 ? "'{$row3['text13']}" : $row3['text13']
					]);
					SetExcelCellCenter($excelActiveSheet, ['B'. $number, 'C'. $number, 'D'. $number, 'E'. $number, 'F'. $number, 'G'. $number, 'H'. $number, 'I'. $number, 'J'. $number, 'K'. $number, 'L'. $number, 'P'. $number, 'Q'. $number, 'R'. $number]);
				}
			}
		}	
	}

$query_ary[] = array();
$set_num = 0;
	//by abow 媒體調整收入 成本
		if($_GET['search2']!=NULL){
			$join_search=' AND ca.memberid='.$_GET['search2'];
		}else{
			$join_search='';
		}
		$now_month = $_GET['search3'].'-'.$_GET['search4'].'-01';
		$now_month_math = strtotime($now_month);
		$next_month = date("Y-m",strtotime("+1 month", $now_month_math))."-01";
		// $sql = "SELECT distinct ca.*,me.campaign_id,  me.media_id ,  me.media_sn  FROM  media_change me LEFT JOIN campaign ca ON ca.id = me.campaign_id WHERE me.change_date  >= '$now_month' and me.change_date < '$next_month' ".$join_search;
		$sql = "SELECT distinct ca.*,me.campaign_id,  me.media_id ,  me.media_sn, `tbl`.`last_sign`  FROM  media_change me 
				LEFT JOIN campaign ca ON ca.id = me.campaign_id 
				LEFT JOIN (SELECT `campaignid`, MAX(`times`) as `last_sign` FROM `campaignstatus2` WHERE `data` = '按下回簽' GROUP BY `campaignid`) tbl ON me.campaign_id = tbl.campaignid
				WHERE me.change_date  >= '$now_month' and me.change_date < '$next_month' ".$join_search;
		// echo 'SQL 1:'.$sql.'<br>';
		// exit();
		$c_id = array();
		$c_aa = array();
		$result=mysql_query($sql); 
		if (mysql_num_rows($result)>0){
			while($row=mysql_fetch_array($result)){

				if(in_array($row['campaign_id'].'-'.$row['media_id'].'-'.$row['media_sn'], $c_id)){								
					continue;
				}else{
					$c_id[] = $row['campaign_id'].'-'.$row['media_id'].'-'.$row['media_sn'];
				}
				$dateInvoice = sprintf('%d年%d月', $row['receipt1'], $row['receipt2']);
				$dateSign = $row['last_sign'] === null ? '' : date('Y-m-d ', $row['last_sign']);
				$media1='';
				$media2='';
				$totalprice1=0;
				$totalprice2=0;
				//取得調整媒體總數 by campaign id
				$media_sql = "SELECT * FROM media_change 
							WHERE campaign_id=".$row["campaign_id"]." 
							AND media_id = ".$row["media_id"]." 
							AND media_sn = ".$row["media_sn"]." 
							AND change_date LIKE '%". sprintf('%04d-%02d-', $_REQUEST['search3'], $_REQUEST['search4']) ."%'
							order by save_date DESC ";
				$media_result=mysql_query($media_sql); 
				if (mysql_num_rows($media_result)>0){
					$media_row=mysql_fetch_array($media_result);

						$sql3='SELECT * FROM media'.$row['media_id'].' WHERE campaign_id = '.$row['campaign_id'].' AND cue=1 ORDER BY id';
						$result3=mysql_query($sql3); 
						if (mysql_num_rows($result3)>0){
							while($row3=mysql_fetch_array($result3)){
								$totalprice1 += $row3['totalprice'];
								$website = $row3['website'];
								
								if (in_array($row['media_id'], $parameter['extra_fee_media'])) {
									$website .= empty($row3['channel']) ? '' : " ({$row3['channel']})";
								}
								
								$media1 .= $website .'=>'. $row3['totalprice'] .'、';
							}
						}

						$sql3='SELECT * FROM media'.$row['media_id'].' WHERE campaign_id = '.$row['campaign_id'].' AND cue=2 ORDER BY id';
						$result3=mysql_query($sql3); 
						if (mysql_num_rows($result3)>0){
							while($row3=mysql_fetch_array($result3)){
								$totalprice2=$totalprice2+$row3['totalprice'];
							}
						}

						$totalprice3=$totalprice1-$totalprice2;

						$mediaclass='';
						$sql3='SELECT * FROM media'.$row['media_id'].' WHERE campaign_id = '.$row['campaign_id'].' AND cue=2 ORDER BY id';
						
						$result3=mysql_query($sql3); 
						$qwi = 0;
						if (mysql_num_rows($result3)>0){
							
							while($row3=mysql_fetch_array($result3)){
								$qwi++;
								$number=$number+1;
								$sql4 = "SELECT * FROM media WHERE id= ".$row['media_id'];
								$result4 = mysql_query($sql4);
								$row4 = mysql_fetch_array($result4);

								if($row3['a']!=NULL){
									$sql5 = "SELECT * FROM media WHERE id= ".$row3['a'];
									$result5 = mysql_query($sql5);
									$row5 = mysql_fetch_array($result5);

									$media11=$row5['name'];
									$sql6='SELECT * FROM media'.$row3['a'].' WHERE id = '.$row3['a0'];
									$result6 = mysql_query($sql6);
									$row6 = mysql_fetch_array($result6);

									$totalprice11=$row6['totalprice'];
									if(($row6['quantity']==NULL)||($row6['quantity']==0)){
										$quantity11=1;
									}else{
										$quantity11=$row6['quantity'];
									}
									if(($row3['quantity']==NULL)||($row3['quantity']==0)){
										$quantity22=1;
									}else{
										$quantity22=$row3['quantity'];
									}
									$price11=round($totalprice11/$quantity11,2);
									$price22=round($row3['a4']/$quantity22,2);
								}else{
									$media11='';
									$totalprice11='';
									$quantity11='';
									$price11='';
									$price22='';
								}
								if($row['agency']==NULL){
									$agency='直客';
								}else{
									$agency=$row['agency'];
								}
								$status = getCampaignStatusText($row['status']);
								
								$a1=0;
								$a2=0;
								$a3=0;
								$a4=0;
								$a5=0;
								$a6=0;
								$a7=0;
								$a8=0;
								//當月調整收入
								$sn = $row3["a0"];
								//$sql_media = "SELECT * FROM media_change WHERE campaign_id = ".$row3["campaign_id"]." AND media_sn = ".$sn." order by save_date DESC LIMIT 0 , 1"; 
								$sql_media = "SELECT * FROM media_change 
											WHERE campaign_id = ".$row3["campaign_id"]." 
											AND media_sn = ".$sn." 
											AND change_date LIKE '%". sprintf('%04d-%02d-', $_REQUEST['search3'], $_REQUEST['search4']) ."%' 
											order by save_date DESC"; 
								
								$result_media = mysql_query($sql_media);
								// echo 'SQL 6:'.$sql_media."<br>";
								
								if(in_array($sql_media, $query_ary)){
									$number--;
									break;
								}
								
								//echo "NO-RE <br>";
								$query_ary[] = $sql_media;
								//$get_row = mysql_fetch_array($result_media);
								//Abow 20150610 增加while 對應 同一對外媒體  多對內媒體  媒體編號相同的 調整數
								$total_mediachang = mysql_num_rows($result_media);
								while($get_row=mysql_fetch_array($result_media)){

									// echo 'SQL 4:'.$sql_media."<br>";


									$a1=$get_row['change_income'];
									$a2=$get_row['change_cost'];
									if(($a1!=NULL)&&($a2!=NULL)){
										$a3=$a1-$a2;
										if(($a3!=0)&&($a1!=0)){
											$a4=round($a3/$a1,2);
										}
									}
									$a5=$get_row['change_income'];
									$a6=$get_row['change_cost'];
									if(($a5!=NULL)&&($a6!=NULL)){
										$a7=$a5-$a6;
										if($a5 > 0){
											$a8=round($a7/$a5,2);
										}else{
											$a8 = 0;
										}
									}
									$note = $get_row["note"];
									
									if($row['version']==2){
										$version=2;
										$aa=$row3['totalprice']-$row3['a1']-$row3['a2'];
									}else{
										$version=1;
										$aa='';
									}
									$search2=$row['member'];

									$website = $row3['website'];
									if (in_array($row['media_id'], $parameter['extra_fee_media'])) {
										$website .= empty($row3['channel']) ? '' : " ({$row3['channel']})";
									}

									SetExcelCellValue($objPHPExcel, [
										'A'. $number => $version,
										'B'. $number => $row['idnumber'],
										'C'. $number => $row['member'],
										'D'. $number => $agency,
										'E'. $number => $row['client'],
										'F'. $number => $row['name'],
										'G'. $number => $row['date1'],
										'H'. $number => $row['date2'],
										'I'. $number => $status,
										'J'. $number => $dateInvoice,
										'K'. $number => $dateSign,
										'L'. $number => $media11,
										'M'. $number => $totalprice11,
										'N'. $number => $price11,
										'O'. $number => $quantity11,
										'P'. $number => $website ."-媒體調整數",
										'Q'. $number => $row4['type2'],
										'R'. $number => $row4['costper'],
										'S'. $number => $price22,
										'T'. $number => round($row3['quantity']),
										'U'. $number => $row3['totalprice'],
										'V'. $number => $row3['a1'],
										'W'. $number => $row3['a2'],
										'X'. $number => $row3['a3'],
										'Y'. $number => $row3['a4'],
										'Z'. $number => '',
										'AA'. $number => $aa,
										'AB'. $number => $a1,
										'AC'. $number => $a2,
										'AD'. $number => $a3,
										'AE'. $number => $a4,
										'AF'. $number => $a5,
										'AG'. $number => $a6,
										'AH'. $number => $a7,
										'AI'. $number => $a8,
										'AJ'. $number => $note,
										'AK'. $number => strpos($row3['text13'], '=') === 0 ? "'{$row3['text13']}" : $row3['text13']
									]);
									SetExcelCellCenter($excelActiveSheet, ['B'. $number, 'C'. $number, 'D'. $number, 'E'. $number, 'F'. $number, 'G'. $number, 'H'. $number, 'I'. $number, 'J'. $number, 'K'. $number, 'L'. $number, 'P'. $number, 'Q'. $number, 'R'. $number]);
									
									if($total_mediachang > 1){
										$number=$number+1;	
									}
									
								}

							
							}
						}
				}
				
			}
			
		}
		//by abow end

	
}
//exit();
//財務部用
if (isset($_GET['Finance'])) {
	$now_month = $_GET['search3'].'-'.$_GET['search4'].'-01';
	$now_month_math = strtotime($now_month);
	$next_month = date("Y-m",strtotime("+1 month", $now_month_math))."-01";
	$sql5="SELECT * FROM campaign WHERE status <> 8 AND status>=2 AND status <=5  AND exchang_math <> 0 AND write_time >= '$now_month' and write_time < '$next_month' ";
	
	$result5=mysql_query($sql5); 
	$result5_num = mysql_num_rows($result5);
	if($result5_num>0){
		while($row5=mysql_fetch_array($result5)){
			if($row5['version']==2){
				$version=2;
			}else{
				$version=1;
			}
			if($row5['agency']==NULL){
				$agency='直客';
			}else{
				$agency=$row5['agency'];
			}

			$status = getCampaignStatusText($row5['status']);

			SetExcelCellValue($objPHPExcel, [
				'A'. $number => $version,
				'B'. $number => $row5['idnumber'],
				'C'. $number => $row5['member'],
				'D'. $number => $agency,
				'E'. $number => $row5['client'],
				'F'. $number => $row5['name'].' - 外匯調整數',
				'G'. $number => $row5['date1'],
				'H'. $number => $row5['date2'],
				'I'. $number => $status,
				'J'. $number => '',
				'K'. $number => '',
				'L'. $number => '',
				'M'. $number => '',
				'N'. $number => '',
				'O'. $number => '',
				'P'. $number => '',
				'Q'. $number => '',
				'R'. $number => '',
				'S'. $number => '',
				'T'. $number => '',
				'U'. $number => '',
				'V'. $number => '',
				'W'. $number => '',
				'X'. $number => '',
				'Y'. $number => '',
				'AA'. $number => '',
				'AB'. $number => $row5['exchang_math'],
				'AC'. $number => '',
				'AD'. $number => '',
				'AE'. $number => '',
				'AF'. $number => '',
				'AG'. $number => '',
				'AH'. $number => '',
				'AI'. $number => '',
				'AJ'. $number => '',
				'AK'. $number => ''
			]);
			SetExcelCellCenter($excelActiveSheet, ['B'. $number, 'C'. $number, 'D'. $number, 'E'. $number, 'F'. $number, 'G'. $number, 'H'. $number, 'I'. $number, 'J'. $number, 'M'. $number, 'Q'. $number, 'R'. $number, 'S'. $number]);
		}
	}
}

if (isset($_REQUEST['search5']) && $_REQUEST['search5'] == 'pm') {
	$search2 = 'All';
	$pmId = $_REQUEST['search2'];
	
					
	if (StartsWith($pmId, 'wom-')) {
		$search2 = "口碑部";
		$pmId = str_replace('wom-', '', $pmId);
	} else if (StartsWith($pmId, 'media-')) {
		$search2 = "媒體部";
		$pmId = str_replace('media-', '', $pmId);
	}

	if (IsId($pmId)) {
		$objMrbsUsers = CreateObject('MrbsUsers', $pmId);

		if (IsId($objMrbsUsers->getId())) {
			$search2 .= $objMrbsUsers->getVar('username');
		}

		unset($objMrbsUsers);
	}
} else if ($_GET['search2'] == NULL) {
	$search2 = 'All';
}

$excelActiveSheet->setTitle('月報表');
$objPHPExcel->setActiveSheetIndex(0);

$xlsFilename = $_GET['search3'] .'年'. $_GET['search4'] .'月報表_'. $search2;

SendExcellFile($objPHPExcel, $xlsFilename);
