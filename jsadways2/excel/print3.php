<?php

	ini_set('memory_limit', '256M');
	require_once dirname(dirname(__DIR__)) .'/autoload.php';

	$db = clone($GLOBALS['app']->db);

	IncludeFunctions('jsadways');

	$objMediaAccounting = CreateObject('MediaAccounting');
	$dateYearMonth = sprintf('%04d%02d', $_REQUEST['search3'], $_REQUEST['search4']);

	IncludeFunctions('excel');
	$objPHPExcel = CreateExcelFile();
	$excelActiveSheet = &$objPHPExcel->getActiveSheet( );

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

$search4 = $_GET['search4'];
if($search4 < 10) {
	$search4 = "0".$search4;
}

$number=1;
$excelActiveSheet->getStyle('A'.$number.':AK'.$number)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$excelActiveSheet->getStyle('A'.$number.':AK'.$number)->getFill()->getStartColor()->setRGB('DDDDDD'); 
$array = array(0 => "B", 1 => "C", 2 => "D", 3 => "E", 4 => "F", 5 => "G", 6 => "H", 7 => "I", 8 => "J", 9 => "K", 10 => "L", 11 => "M", 12 => "N", 13 => "O", 14 => "P", 15 => "Q", 16 => "R", 17 => "S", 18 => "T", 19 => "U", 20 => "V", 21 => "W", 22 => "X", 23 => "Y", 24 => "Z", 25 => "AA", 26 => "AB", 27 => "AC", 28 => "AD", 29 => "AE", 30 => "AF", 31 => "AG", 32 => "AH", 33 => "AI", 34 => "AJ", 35 => "AK");
for($j = 0; $j < count($array); $j++) {
	for($i=$number;$i<=$number;$i++) {
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
	'J'. $number => '對外媒體【1.0】',
	'K'. $number => '對外總金額【1.0】',
	'L'. $number => '佣金+現折【1.0】',
	'M'. $number => '對外媒體【2.0】',
	'N'. $number => '對外總金額【2.0】',
	'O'. $number => '賣價【2.0】',
	'P'. $number => '總數量【2.0】',
	'Q'. $number => '媒體',
	'R'. $number => '分類',
	'S'. $number => '計價方式',
	'T'. $number => '買價',
	'U'. $number => '總數量',
	'V'. $number => '總價',
	'W'. $number => '佣金',
	'X'. $number => '現折',
	'Y'. $number => '利潤',
	'Z'. $number => '操作預算',
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

if (in_array($_GET['search4'], [1, 3, 5, 7, 8,10, 12])) {
	$endday = 31;
} else if (in_array($_GET['search4'], [4, 6, 9, 11])) {
	$endday = 30;
} else if ($_GET['search4'] == 2) {
	$endday = 28;
}

if (isset($_REQUEST['search2']) && $_REQUEST['search2']) {
	$search2 = " AND memberid IN ({$_REQUEST['search2']})";
} else {
	$search2=' AND memberid='.$_SESSION['userid'];
}

$duration = ' AND (
	( date11 >= '. mktime(0, 0, 0, $_GET['search4'], 1, $_GET['search3']) .' AND date11 <= '. mktime(0, 0, 0, $_GET['search4'], $endday, $_GET['search3']) .') 
	OR ( date22 >= '. mktime(0, 0, 0, $_GET['search4'], 1, $_GET['search3']) .' AND date22 <= '. mktime(0, 0, 0, $_GET['search4'], $endday, $_GET['search3']) .') 
	OR ( date11 <= '. mktime(0, 0, 0, $_GET['search4'], 1, $_GET['search3']) .' AND date22 >= '. mktime(0, 0, 0, $_GET['search4'], $endday, $_GET['search3']) .') 
)';

$sql2 = 'SELECT * FROM campaign 
		WHERE `status` <> 8 AND `status` >= 2 AND `status` <= 5 '. $duration . $search2;

$result2 = mysql_query($sql2); 
if (mysql_num_rows($result2) > 0) {
	while ($row2 = mysql_fetch_array($result2)) {
		$rowsAccounting = $objMediaAccounting->getList($row2['id']);

		$i++;
		$media1='';
		$media2='';
		$totalprice1=0;
		$totalprice2=0;
		$rowsMediaOrdinal = GetUsedMediaOrdinal($row2['id']);
		foreach ($rowsMediaOrdinal as $j) {
			$sql3 = sprintf("SELECT * FROM `media%d` 
							WHERE `campaign_id` = %d ORDER BY `id`;", 
								$j, $row2['id']
							);
			$db->query($sql3);
			while ($itemMediaItem = $db->next_record()) {
				if ($itemMediaItem['cue'] == 1) {
					$totalprice1 = $totalprice1 + $itemMediaItem['totalprice'];
					$media1 = $media1 . $itemMediaItem['website'] .'=>'. $itemMediaItem['totalprice'] .'、';
				} else if ($itemMediaItem['cue'] == 2) {
					$totalprice2 = $totalprice2 + $itemMediaItem['totalprice'];
				}
			}
		}
		
		$totalprice3 = $totalprice1 - $totalprice2;
		
		foreach ($rowsMediaOrdinal as $j) {
			$mediaclass='';
			$sql3='SELECT * FROM media'.$j.' WHERE campaign_id = '.$row2['id'].' AND cue=2 ORDER BY id';
			$result3=mysql_query($sql3); 
			if (mysql_num_rows($result3) > 0) {
				while ($row3=mysql_fetch_array($result3)) {
					$number=$number+1;
					$sql4 = "SELECT * FROM media WHERE id= ".$j;
					$result4 = mysql_query($sql4);
					$row4 = mysql_fetch_array($result4);
					if($row3['a']!=NULL) {
						$sql5 = "SELECT * FROM media WHERE id= ".$row3['a'];
						$result5 = mysql_query($sql5);
						$row5 = mysql_fetch_array($result5);
						$media11=$row5['name'];
						$sql6='SELECT * FROM media'.$row3['a'].' WHERE id = '.$row3['a0'];
						$result6 = mysql_query($sql6);
						$row6 = mysql_fetch_array($result6);
						$totalprice11=$row6['totalprice'];
						if(($row6['quantity']==NULL)||($row6['quantity']==0)) {
							$quantity11=1;
						}else{
							$quantity11=$row6['quantity'];
						}
						if(($row3['quantity']==NULL)||($row3['quantity']==0)) {
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
					if($row2['agency']==NULL) {
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
					
					//判斷是否跨月
					if (isset($rowsAccounting[$j][$row3['id']]) && count($rowsAccounting[$j][$row3['id']])) {
						foreach ($rowsAccounting[$j][$row3['id']] as $accountingMonth => $accountingDetal) {
							$a5 += $accountingDetal['accounting_revenue'];
							$a6 += $accountingDetal['accounting_cost'];
						}

						if ($a5 && $a6) {
							$a7 = $a5 - $a6;
							$a8 = round($a7 / $a5, 2);
						}
					}

					if($row2['version']==2) {
						$version=2;
						$aa=$row3['totalprice']-$row3['a1']-$row3['a2'];
					}else{
						$version=1;
						$aa='';
					}
					$search2=$row2['member'];

					//Abow
					if (isset($rowsAccounting[$j][$row3['id']][$dateYearMonth])) {
						$a1 += $rowsAccounting[$j][$row3['id']][$dateYearMonth]['accounting_revenue'];
						$a2 += $rowsAccounting[$j][$row3['id']][$dateYearMonth]['accounting_cost'];

						if ($a1 && $a2) {
							$a3 = $a1 - $a2;
							if($a3 != 0 && $a1 != 0) {
								$a4 = round($a3 / $a1, 2);
							}
						}
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
						'J'. $number => $media1,
						'K'. $number => $totalprice1,
						'L'. $number => round($totalprice3),
						'M'. $number => $media11,
						'N'. $number => $totalprice11,
						'O'. $number => $price11,
						'P'. $number => $quantity11,
						'Q'. $number => $row3['website'],
						'R'. $number => $row4['type2'],
						'S'. $number => $row4['costper'],
						'T'. $number => $price22,
						'U'. $number => round($row3['quantity']),
						'V'. $number => $row3['totalprice'],
						'W'. $number => $row3['a1'],
						'X'. $number => $row3['a2'],
						'Y'. $number => $row3['a3'],
						'Z'. $number => $row3['a4'],
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
					SetExcelCellCenter($excelActiveSheet, ['B'. $number, 'C'. $number, 'D'. $number, 'E'. $number, 'F'. $number, 'G'. $number, 'H'. $number, 'I'. $number, 'J'. $number, 'M'. $number, 'Q'. $number, 'R'. $number, 'S'. $number]);
				}
			}
		}	
	}

$query_ary[] = array();

	//by abow 媒體調整收入 成本
		if (isset($_REQUEST['search2']) && $_REQUEST['search2']) {
			$join_search = " AND ca.memberid IN ({$_REQUEST['search2']})";
		} else {
			$join_search =' AND ca.memberid = '. $_SESSION['userid'];
		}

		$now_month = $_GET['search3'].'-'.$_GET['search4'].'-01';
		$now_month_math = strtotime($now_month);
		$next_month = date("Y-m",strtotime("+1 month", $now_month_math))."-01";
		$sql = "SELECT distinct ca.*,me.campaign_id,  me.media_id ,  me.media_sn  FROM  media_change me LEFT JOIN campaign ca ON ca.id = me.campaign_id WHERE me.change_date  >= '$now_month' and me.change_date < '$next_month' ".$join_search;
		// echo $sql.'<br><br>';
		// exit();
		$c_id = array();
		$c_aa = array();
		$result=mysql_query($sql); 
		if (mysql_num_rows($result) > 0) {
			while ($row=mysql_fetch_array($result)) {

				if(in_array($row['campaign_id'].'-'.$row['media_id'].'-'.$row['media_sn'], $c_id)) {								
					//echo 'abow = '.$row['campaign_id'].' AA='.$aa.'<br>';
					
					continue;
				}else{
					$c_id[] = $row['campaign_id'].'-'.$row['media_id'].'-'.$row['media_sn'];
				}

				$media1='';
				$media2='';
				$totalprice1=0;
				$totalprice2=0;
				//取得調整媒體總數 by campaign id
				$media_sql = "SELECT * FROM media_change WHERE campaign_id=".$row["campaign_id"]." AND media_id = ".$row["media_id"]." AND media_sn = ".$row["media_sn"]."  order by save_date DESC ";
				//echo $media_sql."<br><br>";
				//exit();

				$media_result=mysql_query($media_sql); 
				if (mysql_num_rows($media_result) > 0) {
					$media_row=mysql_fetch_array($media_result);
						$sql3='SELECT * FROM media'.$row['media_id'].' WHERE campaign_id = '.$row['campaign_id'].' AND cue=1 ORDER BY id';
						$result3=mysql_query($sql3); 
						if (mysql_num_rows($result3) > 0) {
							while ($row3=mysql_fetch_array($result3)) {
								$totalprice1=$totalprice1+$row3['totalprice'];
								$media1=$media1.$row3['website'].'=>'.$row3['totalprice'].'、';
							}
						}

						$sql3='SELECT * FROM media'.$row['media_id'].' WHERE campaign_id = '.$row['campaign_id'].' AND cue=2 ORDER BY id';
						$result3=mysql_query($sql3); 
						if (mysql_num_rows($result3) > 0) {
							while ($row3=mysql_fetch_array($result3)) {
								$totalprice2=$totalprice2+$row3['totalprice'];
							}
						}

						$totalprice3=$totalprice1-$totalprice2;
						//$row2 改 $row
						$mediaclass='';
						$sql3='SELECT * FROM media'.$row['media_id'].' WHERE campaign_id = '.$row['campaign_id'].' AND cue=2 ORDER BY id';
						//echo $sql3."<br>".$media_row["media_sn"]."<br>";
						//exit();
						
						$result3=mysql_query($sql3); 
						if (mysql_num_rows($result3) > 0) {
							//$row3=mysql_fetch_array($result3);
							while ($row3=mysql_fetch_array($result3)) {

								

								$number=$number+1;
								$sql4 = "SELECT * FROM media WHERE id= ".$row['media_id'];
								$result4 = mysql_query($sql4);
								$row4 = mysql_fetch_array($result4);

								if($row3['a']!=NULL) {
									$sql5 = "SELECT * FROM media WHERE id= ".$row3['a'];
									$result5 = mysql_query($sql5);
									$row5 = mysql_fetch_array($result5);

									$media11=$row5['name'];
									$sql6='SELECT * FROM media'.$row3['a'].' WHERE id = '.$row3['a0'];
									$result6 = mysql_query($sql6);
									$row6 = mysql_fetch_array($result6);

									$totalprice11=$row6['totalprice'];
									if(($row6['quantity']==NULL)||($row6['quantity']==0)) {
										$quantity11=1;
									}else{
										$quantity11=$row6['quantity'];
									}
									if(($row3['quantity']==NULL)||($row3['quantity']==0)) {
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
								if($row['agency']==NULL) {
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
								$sn = $row3["id"] -1;
								//$sql_media = "SELECT * FROM media_change WHERE campaign_id = ".$row3["campaign_id"]." AND media_sn = ".$sn." order by save_date DESC LIMIT 0 , 1"; 
								$sql_media = "SELECT * FROM media_change WHERE campaign_id = ".$row3["campaign_id"]." AND media_sn = ".$sn." order by save_date DESC"; 
								
								$result_media = mysql_query($sql_media);
								//echo $sql_media."<br>";

								if(in_array($sql_media, $query_ary)) {
									$number--;
									break;
								}

								$query_ary[] = $sql_media;
								//$get_row = mysql_fetch_array($result_media);
								//Abow 20150610 增加while 對應 同一對外媒體  多對內媒體  媒體編號相同的 調整數
								$total_mediachang = mysql_num_rows($result_media);
								while ($get_row=mysql_fetch_array($result_media)) {



									$a1=$get_row['change_income'];
									$a2=$get_row['change_cost'];
									if(($a1!=NULL)&&($a2!=NULL)) {
										$a3=$a1-$a2;
										if(($a3!=0)&&($a1!=0)) {
											$a4=round($a3/$a1,2);
										}
									}
									$a5=$get_row['change_income'];
									$a6=$get_row['change_cost'];
									if(($a5!=NULL)&&($a6!=NULL)) {
										$a7=$a5-$a6;
										if($a5 > 0) {
											$a8=round($a7/$a5,2);
										}else{
											$a8 = 0;
										}
									}
									$note = $get_row["note"];
									
									if($row['version']==2) {
										$version=2;
										$aa=$row3['totalprice']-$row3['a1']-$row3['a2'];
									}else{
										$version=1;
										$aa='';
									}
									$search2=$row['member'];
									
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
										'J'. $number => $media1,
										'K'. $number => $totalprice1,
										'L'. $number => round($totalprice3),
										'M'. $number => $media11,
										'N'. $number => $totalprice11,
										'O'. $number => $price11,
										'P'. $number => $quantity11,
										'Q'. $number => $row3['website']."-媒體調整數",
										'R'. $number => $row4['type2'],
										'S'. $number => $row4['costper'],
										'T'. $number => $price22,
										'U'. $number => round($row3['quantity']),
										'V'. $number => $row3['totalprice'],
										'W'. $number => $row3['a1'],
										'X'. $number => $row3['a2'],
										'Y'. $number => $row3['a3'],
										'Z'. $number => $row3['a4'],
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
									SetExcelCellCenter($excelActiveSheet, ['B'. $number, 'C'. $number, 'D'. $number, 'E'. $number, 'F'. $number, 'G'. $number, 'H'. $number, 'I'. $number, 'J'. $number, 'M'. $number, 'Q'. $number, 'R'. $number, 'S'. $number]);

									if($total_mediachang > 1) {
										$number=$number+1;	
									}
								}
							}
						}
				}
				
			}
			
		}
}

$excelActiveSheet->setTitle('月報表');
$objPHPExcel->setActiveSheetIndex(0);

$xlsFilename = $_GET['search3'].'年'.$_GET['search4'].'月報表_'.(isset($_REQUEST['search2']) && $_REQUEST['search2'] && !is_numeric($_REQUEST['search2']) ? '全部組員' : $search2);

SendExcellFile($objPHPExcel, $xlsFilename);
