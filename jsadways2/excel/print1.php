<?php

$chatBotMediaId = 132;

ini_set( "memory_limit", "256M");
include('../include/db.inc.php');
$sql2 = "SELECT * FROM campaign WHERE id= ".$_GET['id'];
$result2 = mysql_query($sql2);
$row2 = mysql_fetch_array($result2);
/** Error reporting */
if($row2['rate']!=NULL){
	if($row2['rate']>1){
		$usdjpy='USD ';
	}else{
		$usdjpy='JPY ';
	}
	$rate=$row2['rate'];
}else{
	$usdjpy='';
	$rate=1;
}
error_reporting(E_ALL);
ini_set('display_errors', false); 
ini_set('display_startup_errors', false);
date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once 'Classes/PHPExcel.php';


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

// Set document properties
$objPHPExcel->getProperties()->setCreator("JS Adways Media Inc.")
							 ->setLastModifiedBy("JS Adways Media Inc.")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");
							 
//設定預設樣式
$objPHPExcel->getDefaultStyle()->getFont()->setName('新細明體');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(18);//設定欄位寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(2);
//標題 JS-Adways Media Schedule
$objPHPExcel->getActiveSheet()->mergeCells('B1:E1');
$objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setSize(28);
$objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->getColor()->setRGB('0300DC');
//案件內容
$objPHPExcel->getActiveSheet()->mergeCells('B2:E2');
$objPHPExcel->getActiveSheet()->mergeCells('B3:E3');
$objPHPExcel->getActiveSheet()->mergeCells('B4:E4');
$objPHPExcel->getActiveSheet()->mergeCells('B5:E5');
$objPHPExcel->getActiveSheet()->getStyle('B2:B5')->getFont()->setSize(14);
$objPHPExcel->getActiveSheet()->getStyle('B2:B5')->getFont()->setBold(true);
//Pre-Buy Analysis
$objPHPExcel->getActiveSheet()->getStyle('F2:G2')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('F2:G2')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('F2:G2')->getFont()->getColor()->setRGB('FFFFFF');
$objPHPExcel->getActiveSheet()->getStyle('F2:G2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('F2:G2')->getFill()->getStartColor()->setRGB('000000'); 
$objPHPExcel->getActiveSheet()->getStyle('F3:G6')->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getStyle('F3:F6')->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
$objPHPExcel->getActiveSheet()->getStyle('F3:G6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$objPHPExcel->getActiveSheet()->getStyle('F3:G6')->getFill()->getStartColor()->setRGB('FFCC66'); 
//插入AppDriver及Jsadways圖片
//$objPHPExcel->getActiveSheet()->mergeCells('K2:M5');
$objPHPExcel->getActiveSheet()->mergeCells('M2:O5');
$objDrawing = new PHPExcel_Worksheet_Drawing();
$objDrawing->setName('Logo');
$objDrawing->setDescription('Logo');
$objDrawing->setPath('jsadways.png');
$objDrawing->setHeight(70);
$objDrawing->setWidth(280);
$objDrawing->setCoordinates('M2');
$objDrawing->setOffsetX(110);
$objDrawing->setRotation(25);
$objDrawing->getShadow()->setVisible(true);
$objDrawing->getShadow()->setDirection(45);
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
// 標頭內容
$a1=0;
$a2=0;
$a3=0;
$s1=0;
$s2=0;

//是否有海外媒體
$abroad_num = 0;
$sql_abroad='SELECT * FROM media WHERE type>=20 ORDER BY id';
$result_abroad=mysql_query($sql_abroad); 
if (mysql_num_rows($result_abroad)>0){
	while($row_abroad=mysql_fetch_array($result_abroad)){
		$sql_abroad_media ='SELECT * FROM media'.$row_abroad['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
		$result_abroad_media=mysql_query($sql_abroad_media); 
		if (mysql_num_rows($result_abroad_media)>0){
			$abroad_num++;
		}
	}
}

if($abroad_num == 0){
//如果沒有海外媒體才執行
	$sql33='SELECT * FROM media WHERE type=1 ORDER BY id';
	$result33=mysql_query($sql33); 
	if (mysql_num_rows($result33)>0){
		while($row33=mysql_fetch_array($result33)){	
			$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
			$result3=mysql_query($sql3); 
			if (mysql_num_rows($result3)>0){
				while($row3=mysql_fetch_array($result3)){
					$a1=$a1+$row3['quantity2'];
					$s1=$s1+$row3['totalprice'];
					$s2=$s2+$row3['quantity'];
				}
			}
		}
	}
	$sql33='SELECT * FROM media WHERE type=3 ORDER BY id';
	$result33=mysql_query($sql33); 
	if (mysql_num_rows($result33)>0){
		while($row33=mysql_fetch_array($result33)){
			$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
			$result3=mysql_query($sql3); 
			if (mysql_num_rows($result3)>0){
				while($row3=mysql_fetch_array($result3)){
					$a1=$a1+$row3['quantity2'];
					$s1=$s1+$row3['totalprice'];
					$s2=$s2+$row3['quantity'];
				}
			}
		}
	}

	if($a1!=0 && $s2 !=0){
		$a2=(($s1*1000)/$a1);
		$a3=$s1/$s2;
	}
}


$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('B1', 'JS-Adways Media  Schedule')
            ->setCellValue('B2', '廣告代理商(Agency)：'.$row2['agency'])
            ->setCellValue('B3', '廣告主(Client)：'.$row2['client'])
            ->setCellValue('B4', '活動(Campaign)：'.$row2['name'])
            ->setCellValue('B5', '期間(Period)：'.substr($row2['date1'],0,5).'-'.substr($row2['date2'],0,5))
            ->setCellValue('F2', 'Pre-Buy Analysis')
            ->setCellValue('F3', 'Total Impression :')
			->setCellValue('F4', 'Ave. CPM :')
			->setCellValue('F5', 'Ave. CPC :')
			->setCellValue('F6', 'Discount :')
			->setCellValue('G3', number_format($a1))
			->setCellValue('G4', number_format($a2))
			->setCellValue('G5', number_format($a3))
			->setCellValue('G6', 'N/A');
$objPHPExcel->getActiveSheet()->getStyle('G3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('G4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('G5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('G6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);




$totalprice=0;
$number=6;
$cpc=0;
//cpc媒體
$sql33='SELECT * FROM media WHERE type=1 ORDER BY id';
$result33=mysql_query($sql33); 
if (mysql_num_rows($result33)>0){
	while($row33=mysql_fetch_array($result33)){
		$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
		$result3=mysql_query($sql3); 
		if (mysql_num_rows($result3)>0){
			while($row3=mysql_fetch_array($result3)){
				$cpc=1;
			}
		}
	}
}
if($cpc==1){
	// 標頭內容
	$number=$number+1;
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':Q'.($number+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':Q'.($number+1))->getFill()->getStartColor()->setRGB('DDDDDD'); 
	$array = array(0 => "B", 1 => "C", 2 => "D", 3 => "E", 4 => "F", 5 => "G", 6 => "H", 7 => "I", 8 => "J", 9 => "K", 10 => "L", 11 => "M", 12 => "N", 13 => "O", 14 => "P", 15 => "Q");
	for($j = 0; $j < count($array); $j++){
		for($i=$number;$i<=($number+1);$i++){
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
		}
	}
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, 'Website')
				->setCellValue('C'.$number, 'Channel')
				->setCellValue('D'.$number, 'System')
				->setCellValue('E'.$number, 'Position')
				->setCellValue('F'.$number, 'Size')
				->setCellValue('G'.$number, 'Format')
				->setCellValue('H'.$number, 'R/F')
				->setCellValue('I'.$number, 'Est.Impressions')
				->setCellValue('J'.$number, 'Est.CTR(%)')
				->setCellValue('K'.$number, 'Est.Clicks')
				->setCellValue('L'.$number, 'Period')
				->setCellValue('M'.$number, 'Days')
				->setCellValue('N'.$number, 'CPC計價')
				->setCellValue('O'.$number, 'Material Due')
				->setCellValue('P'.$number, 'Net Cost(NTD)')
				->setCellValue('Q'.$number, 'Other');
	$number=$number+1;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, '網站')
				->setCellValue('C'.$number, '頻道')
				->setCellValue('D'.$number, '系統')
				->setCellValue('E'.$number, '版位')
				->setCellValue('F'.$number, '規格')
				->setCellValue('G'.$number, '格式')
				->setCellValue('H'.$number, '輪替/固定')
				->setCellValue('I'.$number, '預估曝光數')
				->setCellValue('J'.$number, '預估點擊率')
				->setCellValue('K'.$number, '預估點擊數')
				->setCellValue('L'.$number, '刊登日期')
				->setCellValue('M'.$number, '天數')
				->setCellValue('N'.$number, '台幣')
				->setCellValue('O'.$number, '素材提供期限')
				->setCellValue('P'.$number, '售價')
				->setCellValue('Q'.$number, '備註');
	
	$sql33='SELECT * FROM media WHERE type=1 ORDER BY id';
	$result33=mysql_query($sql33); 
	if (mysql_num_rows($result33)>0){
		while($row33=mysql_fetch_array($result33)){
			$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
			$result3=mysql_query($sql3); 
			if (mysql_num_rows($result3)>0){
				while($row3=mysql_fetch_array($result3)){
					$other = '';
					//whoscall 若有篩選 增加在備註
					if($row33['id'] == 88){
						if($row3['beinstall_total'] != ''){
							$other .= "預估安裝數：".$row3['beinstall_total']."。\n";
						}
					}
					$other .= $row3['others'];
					$number=$number+1;
					$datedate='';
					if($row3['price']!=NULL){
						$price1=$usdjpy.'$'.($row3['price']/$rate);
					}else{
						$price1=$usdjpy.'$'.($row3['price1']/$rate);
						if($row3['price2']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price2']/$rate);}
						if($row3['price3']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price3']/$rate);}
						if($row3['price4']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price4']/$rate);}
						if($row3['price5']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price5']/$rate);}
					}
					if($row3['date1']!=0){$datedate=date('m/d',$row3['date1']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date2']+ (8 * 60 * 60));}
					if($row3['date3']!=0){$datedate=$datedate.','.date('m/d',$row3['date3']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date4']+ (8 * 60 * 60));}
					if($row3['date5']!=0){$datedate=$datedate.','.date('m/d',$row3['date5']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date6']+ (8 * 60 * 60));}
					if($row3['date7']!=0){$datedate=$datedate.','.date('m/d',$row3['date7']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date8']+ (8 * 60 * 60));}
					if($row3['date9']!=0){$datedate=$datedate.','.date('m/d',$row3['date9']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date10']+ (8 * 60 * 60));}
					$website = '';
					$position = '';
					if($row33['id'] == 111){
						$website = 'JS NATIVE SOLUTION';
						$position = 'Popin Recommend Article ADS';
						$other = '連結標的：必須是部落客介紹文 / 廣編與置入文 / 新聞報導等具內容的頁面
									圖片中不得放置文字 及Logo，但產品圖上之Logo圖則不在此限
									此版位不可使用的連結標的:
									．不可直接連到app下載頁面
									．不可連至第一屏即填寫名單且沒有其他內容的頁面
									．不可連至商品購物頁面，若是商品評論或報導頁中
									    帶有購買功能則不在此限
									．若有特殊需求，請洽您服務窗口';
					} else if(in_array($row33['id'], [134, 135, 136])){
						$website = str_replace('代操', '', $row3['website']);
					}else{
						$website = $row3['website'];
						$position = $row3['position'];
					}

					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('B'.$number, $website)
					->setCellValue('C'.$number, $row3['channel'])
					->setCellValue('D'.$number, $row3['phonesystem'])
					->setCellValue('E'.$number, $position)
					->setCellValue('F'.$number, str_replace("\\n", "\n", $row3['format1']))
					->setCellValue('G'.$number, str_replace("\\n", "\n", $row3['format2']))
					->setCellValue('H'.$number, $row3['wheel'])
					->setCellValue('I'.$number, $row3['quantity2'])
					->setCellValue('J'.$number, $row3['ctr'].'%')
					->setCellValue('K'.$number, $row3['quantity'])
					->setCellValue('L'.$number, $datedate)
					->setCellValue('M'.$number, $row3['days'])
					->setCellValue('N'.$number, $price1)
					->setCellValue('O'.$number, $row3['due'])
					->setCellValue('P'.$number, $usdjpy.'$'.number_format($row3['totalprice']/$rate))
					->setCellValue('Q'.$number, $other);
					$objPHPExcel->getActiveSheet()->getStyle('Q'.$number)->getAlignment()->setWrapText(true);
					$totalprice=$totalprice+$row3['totalprice'];
					$objPHPExcel->getActiveSheet()->getStyle('B'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('H'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('I'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('J'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('K'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('L'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('M'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('N'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('O'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('P'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					for($j = 0; $j < count($array); $j++){
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					}
				}
			}
		}
	}
}


$cpi=0;
//cpi媒體
$sql33='SELECT * FROM media WHERE type=2 ORDER BY id';
$result33=mysql_query($sql33); 
if (mysql_num_rows($result33)>0){
	while($row33=mysql_fetch_array($result33)){
		$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
		$result3=mysql_query($sql3); 
		if (mysql_num_rows($result3)>0){
			while($row3=mysql_fetch_array($result3)){
				$cpi=1;
			}
		}
	}
}
if($cpi==1){
	// 標頭內容
	$number=$number+2;
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':Q'.($number+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':Q'.($number+1))->getFill()->getStartColor()->setRGB('DDDDDD');
	$array = array(0 => "B", 1 => "C", 2 => "D", 3 => "E", 4 => "F", 5 => "G", 6 => "H", 7 => "I", 8 => "J", 9 => "K", 10 => "L", 11 => "M", 12 => "N", 13 => "O", 14 => "P", 15 => "Q");
	for($j = 0; $j < count($array); $j++){
		for($i=$number;$i<=($number+1);$i++){
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
		}
	}
	$objPHPExcel->getActiveSheet()->mergeCells('F'.$number.':G'.$number);
	$objPHPExcel->getActiveSheet()->mergeCells('H'.$number.':I'.$number);

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, 'Website')
				->setCellValue('C'.$number, 'Action')
				->setCellValue('D'.$number, 'system')
				->setCellValue('E'.$number, 'Position')
				->setCellValue('F'.$number, 'Size')
				->setCellValue('H'.$number, 'Format')
				->setCellValue('J'.$number, 'R/F')
				->setCellValue('K'.$number, 'Est.Actions')
				->setCellValue('L'.$number, 'Period')
				->setCellValue('M'.$number, 'Days')
				->setCellValue('N'.$number, 'Material Due')
				->setCellValue('O'.$number, 'Cost Per')
				->setCellValue('P'.$number, 'Net Cost(NTD)')
				->setCellValue('Q'.$number, 'Other');
	$number=$number+1;
	$objPHPExcel->getActiveSheet()->mergeCells('F'.$number.':G'.$number);
	$objPHPExcel->getActiveSheet()->mergeCells('H'.$number.':I'.$number);

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, '項目')
				->setCellValue('C'.$number, '執行內容')
				->setCellValue('D'.$number, 'OS')
				->setCellValue('E'.$number, '版位')
				->setCellValue('F'.$number, '規格')
				->setCellValue('H'.$number, '格式')
				->setCellValue('J'.$number, '輪替/固定')
				->setCellValue('K'.$number, '數量')
				->setCellValue('L'.$number, '刊登日期')
				->setCellValue('M'.$number, '天數')
				->setCellValue('N'.$number, '素材提供期限')
				->setCellValue('O'.$number, 'CPI定價')
				->setCellValue('P'.$number, '售價')
				->setCellValue('Q'.$number, '備註');
	
	$sql33='SELECT * FROM media WHERE type=2 ORDER BY id';
	$result33=mysql_query($sql33); 
	if (mysql_num_rows($result33)>0){
		while($row33=mysql_fetch_array($result33)){
			$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
			$result3=mysql_query($sql3); 
			if (mysql_num_rows($result3)>0){
				while($row3=mysql_fetch_array($result3)){
					$number=$number+1;
					$datedate='';
					if($row3['price']!=NULL){
						$price1=$usdjpy.'$'.($row3['price']/$rate);
					}else{
						$price1=$usdjpy.'$'.($row3['price1']/$rate);
						if($row3['price2']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price2']/$rate);}
						if($row3['price3']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price3']/$rate);}
						if($row3['price4']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price4']/$rate);}
						if($row3['price5']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price5']/$rate);}
					}
					if($row3['date1']!=0){$datedate=date('m/d',$row3['date1']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date2']+ (8 * 60 * 60));}
					if($row3['date3']!=0){$datedate=$datedate.','.date('m/d',$row3['date3']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date4']+ (8 * 60 * 60));}
					if($row3['date5']!=0){$datedate=$datedate.','.date('m/d',$row3['date5']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date6']+ (8 * 60 * 60));}
					if($row3['date7']!=0){$datedate=$datedate.','.date('m/d',$row3['date7']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date8']+ (8 * 60 * 60));}
					if($row3['date9']!=0){$datedate=$datedate.','.date('m/d',$row3['date9']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date10']+ (8 * 60 * 60));}

					if(in_array($row33['id'], [134, 135, 136])){
						$website = str_replace('代操', '', $row3['website']);
					} else {
						$website = $row3['website'];
					}

					$objPHPExcel->getActiveSheet()->mergeCells('F'.$number.':G'.$number);
					$objPHPExcel->getActiveSheet()->mergeCells('H'.$number.':I'.$number);
					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('B'.$number, $website)
					->setCellValue('C'.$number, $row3['actions'])
					->setCellValue('D'.$number, $row3['phonesystem'])
					->setCellValue('E'.$number, $row3['position'])
					->setCellValue('F'.$number, str_replace("\\n", "\n", $row3['format1']))
					->setCellValue('H'.$number, str_replace("\\n", "\n", $row3['format2']))
					->setCellValue('J'.$number, $row3['wheel'])
					->setCellValue('K'.$number, number_format($row3['quantity']))
					->setCellValue('L'.$number, $datedate)
					->setCellValue('M'.$number, $row3['days'])
					->setCellValue('N'.$number, $row3['due'])
					->setCellValue('O'.$number, $price1)
					->setCellValue('P'.$number, $usdjpy.'$'.number_format($row3['totalprice']/$rate))
					->setCellValue('Q'.$number, $row3['others']);
					$totalprice=$totalprice+$row3['totalprice'];
					$objPHPExcel->getActiveSheet()->getStyle('B'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('H'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('J'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('K'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('L'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('M'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('N'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('O'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('P'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					for($j = 0; $j < count($array); $j++){
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					}
				}
			}
		}
	}
}

$cpt=0;
//cpt媒體
$sql33='SELECT * FROM media WHERE type=10 ORDER BY id';
$result33=mysql_query($sql33); 
if (mysql_num_rows($result33)>0){
	while($row33=mysql_fetch_array($result33)){
		$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
		$result3=mysql_query($sql3); 
		if (mysql_num_rows($result3)>0){
			while($row3=mysql_fetch_array($result3)){
				$cpt=1;
			}
		}
	}
}
if($cpt==1){
	// 標頭內容
	$number=$number+2;
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':Q'.($number+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':Q'.($number+1))->getFill()->getStartColor()->setRGB('DDDDDD');
	$array = array(0 => "B", 1 => "C", 2 => "D", 3 => "E", 4 => "F", 5 => "G", 6 => "H", 7 => "I", 8 => "J", 9 => "K", 10 => "L", 11 => "M", 12 => "N", 13 => "O", 14 => "P", 15 => "Q");
	for($j = 0; $j < count($array); $j++){
		for($i=$number;$i<=($number+1);$i++){
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
		}
	}
	$objPHPExcel->getActiveSheet()->mergeCells('F'.$number.':G'.$number);
	$objPHPExcel->getActiveSheet()->mergeCells('H'.$number.':I'.$number);

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, 'Website')
				->setCellValue('C'.$number, 'Action')
				->setCellValue('D'.$number, 'system')
				->setCellValue('E'.$number, 'Position')
				->setCellValue('F'.$number, 'Size')
				->setCellValue('H'.$number, 'Format')
				->setCellValue('J'.$number, 'R/F')
				->setCellValue('K'.$number, 'Est.Actions')
				->setCellValue('L'.$number, 'Period')
				->setCellValue('M'.$number, 'Days')
				->setCellValue('N'.$number, 'Material Due')
				->setCellValue('O'.$number, 'Cost Per')
				->setCellValue('P'.$number, 'Net Cost(NTD)')
				->setCellValue('Q'.$number, 'Other');
	$number=$number+1;
	$objPHPExcel->getActiveSheet()->mergeCells('F'.$number.':G'.$number);
	$objPHPExcel->getActiveSheet()->mergeCells('H'.$number.':I'.$number);

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, '項目')
				->setCellValue('C'.$number, '執行內容')
				->setCellValue('D'.$number, 'OS')
				->setCellValue('E'.$number, '版位')
				->setCellValue('F'.$number, '規格')
				->setCellValue('H'.$number, '格式')
				->setCellValue('J'.$number, '輪替/固定')
				->setCellValue('K'.$number, '數量')
				->setCellValue('L'.$number, '刊登日期')
				->setCellValue('M'.$number, '天數')
				->setCellValue('N'.$number, '素材提供期限')
				->setCellValue('O'.$number, 'CPT定價')
				->setCellValue('P'.$number, '售價')
				->setCellValue('Q'.$number, '備註');
	
	$sql33='SELECT * FROM media WHERE type=10 ORDER BY id';
	$result33=mysql_query($sql33); 
	if (mysql_num_rows($result33)>0){
		while($row33=mysql_fetch_array($result33)){
			$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
			$result3=mysql_query($sql3); 
			if (mysql_num_rows($result3)>0){
				while($row3=mysql_fetch_array($result3)){
					$number=$number+1;
					$datedate='';
					if($row3['price']!=NULL){
						$price1=$usdjpy.'$'.($row3['price']/$rate);
					}else{
						$price1=$usdjpy.'$'.($row3['price1']/$rate);
						if($row3['price2']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price2']/$rate);}
						if($row3['price3']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price3']/$rate);}
						if($row3['price4']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price4']/$rate);}
						if($row3['price5']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price5']/$rate);}
					}
					if($row3['date1']!=0){$datedate=date('m/d',$row3['date1']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date2']+ (8 * 60 * 60));}
					if($row3['date3']!=0){$datedate=$datedate.','.date('m/d',$row3['date3']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date4']+ (8 * 60 * 60));}
					if($row3['date5']!=0){$datedate=$datedate.','.date('m/d',$row3['date5']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date6']+ (8 * 60 * 60));}
					if($row3['date7']!=0){$datedate=$datedate.','.date('m/d',$row3['date7']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date8']+ (8 * 60 * 60));}
					if($row3['date9']!=0){$datedate=$datedate.','.date('m/d',$row3['date9']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date10']+ (8 * 60 * 60));}
					$objPHPExcel->getActiveSheet()->mergeCells('F'.$number.':G'.$number);
					$objPHPExcel->getActiveSheet()->mergeCells('H'.$number.':I'.$number);
					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('B'.$number, $row3['website'])
					->setCellValue('C'.$number, $row3['actions'])
					->setCellValue('D'.$number, $row3['phonesystem'])
					->setCellValue('E'.$number, $row3['position'])
					->setCellValue('F'.$number, str_replace("\\n", "\n", $row3['format1']))
					->setCellValue('H'.$number, str_replace("\\n", "\n", $row3['format2']))
					->setCellValue('J'.$number, $row3['wheel'])
					->setCellValue('K'.$number, number_format($row3['quantity']))
					->setCellValue('L'.$number, $datedate)
					->setCellValue('M'.$number, $row3['days'])
					->setCellValue('N'.$number, $row3['due'])
					->setCellValue('O'.$number, $price1)
					->setCellValue('P'.$number, $usdjpy.'$'.number_format($row3['totalprice']/$rate))
					->setCellValue('Q'.$number, $row3['others']);
					$totalprice=$totalprice+$row3['totalprice'];
					$objPHPExcel->getActiveSheet()->getStyle('B'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('H'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('J'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('K'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('L'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('M'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('N'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('O'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('P'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					for($j = 0; $j < count($array); $j++){
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					}
				}
			}
		}
	}
}

$cpm=0;
//cpm媒體
$sql33='SELECT * FROM media WHERE type=0 ORDER BY id';
$result33=mysql_query($sql33); 
if (mysql_num_rows($result33)>0){
	while($row33=mysql_fetch_array($result33)){
		$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
		$result3=mysql_query($sql3); 
		if (mysql_num_rows($result3)>0){
			while($row3=mysql_fetch_array($result3)){
				$cpm=1;
			}
		}
	}
}
if($cpm==1){
	// 標頭內容
	$number=$number+2;
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':Q'.($number+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':Q'.($number+1))->getFill()->getStartColor()->setRGB('DDDDDD'); 
	$array = array(0 => "B", 1 => "C", 2 => "D", 3 => "E", 4 => "F", 5 => "G", 6 => "H", 7 => "I", 8 => "J", 9 => "K", 10 => "L", 11 => "M", 12 => "N", 13 => "O", 14 => "P", 15 => "Q");
	for($j = 0; $j < count($array); $j++){
		for($i=$number;$i<=($number+1);$i++){
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
		}
	}
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, 'Website')
				->setCellValue('C'.$number, 'Channel')
				->setCellValue('D'.$number, 'System')
				->setCellValue('E'.$number, 'Position')
				->setCellValue('F'.$number, 'Size')
				->setCellValue('G'.$number, 'Format')
				->setCellValue('H'.$number, 'R/F')
				->setCellValue('I'.$number, 'Est.Impressions')
				->setCellValue('J'.$number, 'Est.CTR(%)')
				->setCellValue('K'.$number, 'Est.Clicks')
				->setCellValue('L'.$number, 'Period')
				->setCellValue('M'.$number, 'Days')
				->setCellValue('N'.$number, 'CPM計價')
				->setCellValue('O'.$number, 'Material Due')
				->setCellValue('P'.$number, 'Net Cost(NTD)')
				->setCellValue('Q'.$number, 'Other');
	$number=$number+1;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, '網站')
				->setCellValue('C'.$number, '頻道')
				->setCellValue('D'.$number, '系統')
				->setCellValue('E'.$number, '版位')
				->setCellValue('F'.$number, '規格')
				->setCellValue('G'.$number, '格式')
				->setCellValue('H'.$number, '輪替/固定')
				->setCellValue('I'.$number, '預估曝光數')
				->setCellValue('J'.$number, '預估點擊率')
				->setCellValue('K'.$number, '預估點擊數')
				->setCellValue('L'.$number, '刊登日期')
				->setCellValue('M'.$number, '天數')
				->setCellValue('N'.$number, '台幣')
				->setCellValue('O'.$number, '素材提供期限')
				->setCellValue('P'.$number, '售價')
				->setCellValue('Q'.$number, '備註');
	
	$sql33='SELECT * FROM media WHERE type=0 ORDER BY id';
	$result33=mysql_query($sql33); 
	if (mysql_num_rows($result33)>0){
			$cpm225_phonesystem = '';
			$cpm225_format1 = '';
			$cpm225_format2 = '';
			$cpm225_quantity2 = '';
			$cpm225_ctr = '';
			$cpm225_quantity = '';
			$cpm225_datedate = '';
			$cpm225_days = '';
			$cpm225_totalprice = 0;
			$cpm225_other = '';
		while($row33=mysql_fetch_array($result33)){
			$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
			$result3=mysql_query($sql3); 
			if (mysql_num_rows($result3)>0){
				while($row3=mysql_fetch_array($result3)){
					$other = '';
					//whoscall 若有篩選 增加在備註
					if($row33['id'] == 86){
						if($row3['filter1'] != ''){
							$other .= "篩選條件－關鍵字設定：".$row3['filter1']."。\n";
						}
						if($row3['filter2'] != ''){
							$other .= "篩選條件－關聯號碼搜尋：".$row3['filter2']."。\n";
						}
					}
					$other .= $row3['others'];
					$number=$number+1;
					$datedate='';
					if($row3['price']!=NULL){
						$price1=$usdjpy.'$'.($row3['price']/$rate);
					}else{
						$price1=$usdjpy.'$'.($row3['price1']/$rate);
						if($row3['price2']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price2']/$rate);}
						if($row3['price3']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price3']/$rate);}
						if($row3['price4']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price4']/$rate);}
						if($row3['price5']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price5']/$rate);}
					}
					if($row3['date1']!=0){$datedate=date('m/d',$row3['date1']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date2']+ (8 * 60 * 60));}
					if($row3['date3']!=0){$datedate=$datedate.','.date('m/d',$row3['date3']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date4']+ (8 * 60 * 60));}
					if($row3['date5']!=0){$datedate=$datedate.','.date('m/d',$row3['date5']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date6']+ (8 * 60 * 60));}
					if($row3['date7']!=0){$datedate=$datedate.','.date('m/d',$row3['date7']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date8']+ (8 * 60 * 60));}
					if($row3['date9']!=0){$datedate=$datedate.','.date('m/d',$row3['date9']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date10']+ (8 * 60 * 60));}

					$website = '';
					$position = '';
					$format1 = '';
					$format2 = '';
					$quantity2 = '';
					$ctr = '';
					$quantity = '';
					$days = '';
					// $totalprice = '';

					if($row33['id'] == 112||$row33['id'] == 114||$row33['id'] == 115){
						$number=$number-1;
						$cpm225_phonesystem = $row3['phonesystem'];
						$cpm225_format1 .= str_replace("\\n", "\n", $row3['format1'])."\n";
						$cpm225_format2 .= str_replace("\\n", "\n", $row3['format2'])."\n";
						$cpm225_quantity2 += $row3['quantity2']; //曝光數
						$cpm225_ctr += $row3['ctr'];
						$cpm225_quantity += $row3['quantity']; //預估點擊數
						// echo $cpm225_datedate.' & '.$datedate.'=';
						if (false !== ($rst = strpos($cpm225_datedate, $datedate))) {
						    // echo 'find : '.$rst; // 印出 find : 6
						} else {
							$cpm225_datedate .= $datedate.',';
							$cpm225_days += $row3['days'];
						    // echo 'not find'; // 若不存在, 則印出 not find
						}

						// echo '<br>';
						$cpm225_totalprice += $row3['totalprice'];
						$cpm225_other .= $other."\n";
						// $totalprice=$totalprice+$row3['totalprice']; //總金額加總

						continue;
					}else if ($row33['id'] == 113) {
						$website = 'JS NATIVE SOLUTION';
						$position = 'Applause Interstitial Video Ads';
						$format1 = str_replace("\\n", "\n", $row3['format1']);
						$format2 = str_replace("\\n", "\n", $row3['format2']);
						$quantity2 = $row3['quantity2'];
						$ctr = $row3['ctr'];
						$quantity = $row3['quantity'];
						$days = $row3['days'];
					} else if(in_array($row33['id'], [134, 135, 136])){
						$website = str_replace('代操', '', $row3['website']);
					}else{
						$website = $row3['website'];
						$position = $row3['position'];
						$format1 = str_replace("\\n", "\n", $row3['format1']);
						$format2 = str_replace("\\n", "\n", $row3['format2']);
						$quantity2 = $row3['quantity2'];
						$ctr = $row3['ctr'];
						$quantity = $row3['quantity'];
						$days = $row3['days'];
						
						// $totalprice = $row3['totalprice'];
						
					}

					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('B'.$number, $website)
					->setCellValue('C'.$number, $row3['channel'])
					->setCellValue('D'.$number, $row3['phonesystem'])
					->setCellValue('E'.$number, $position)
					->setCellValue('F'.$number, $format1)
					->setCellValue('G'.$number, $format2)
					->setCellValue('H'.$number, $row3['wheel'])
					->setCellValue('I'.$number, $quantity2)
					->setCellValue('J'.$number, $ctr.'%')
					->setCellValue('K'.$number, $quantity)
					->setCellValue('L'.$number, $datedate)
					->setCellValue('M'.$number, $days)
					->setCellValue('N'.$number, $price1)
					->setCellValue('O'.$number, $row3['due'])
					->setCellValue('P'.$number, $usdjpy.'$'.number_format($row3['totalprice']/$rate))
					->setCellValue('Q'.$number, $other);
					// echo $totalprice;
					$totalprice=$totalprice+$row3['totalprice'];
					 // echo 'bbb'.$totalprice.'ccc';

					$objPHPExcel->getActiveSheet()->getStyle('Q'.$number)->getAlignment()->setWrapText(true);
					$objPHPExcel->getActiveSheet()->getStyle('B'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('H'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('I'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('J'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('K'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('L'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('M'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('N'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('O'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('P'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					for($j = 0; $j < count($array); $j++){
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					}
				}
			}
		}
		//while 結束

		if($cpm225_quantity > 0){
			// exit();
			$number=$number+1;
			$website = 'JS NATIVE SOLUTION';
			$position = 'Applause Native Video Ads';
			$ctr = $cpm225_quantity / $cpm225_quantity2; //點擊數 除 曝光

			$cpm225_ctr = round($ctr, 4) * 100;
			if($cpm225_days > 0 ){
				$cpm225_datedate = substr($cpm225_datedate,0,-1);
			}

			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, $website)
				->setCellValue('C'.$number, '投放台灣地區為主')
				->setCellValue('D'.$number, $cpm225_phonesystem)
				->setCellValue('E'.$number, $position)
				->setCellValue('F'.$number, $cpm225_format1)
				->setCellValue('G'.$number, $cpm225_format2)
				->setCellValue('H'.$number, 'R')
				->setCellValue('I'.$number, $cpm225_quantity2)
				->setCellValue('J'.$number, $cpm225_ctr.'%')
				->setCellValue('K'.$number, $cpm225_quantity)
				->setCellValue('L'.$number, $cpm225_datedate)
				->setCellValue('M'.$number, $cpm225_days)
				->setCellValue('N'.$number, '$225')
				->setCellValue('O'.$number, '上線日前3-5天')
				->setCellValue('P'.$number, $usdjpy.'$'.number_format($cpm225_totalprice/$rate))
				->setCellValue('Q'.$number, $cpm225_other);
				$objPHPExcel->getActiveSheet()->getStyle('Q'.$number)->getAlignment()->setWrapText(true);
				$totalprice=$totalprice+$cpm225_totalprice;
				$objPHPExcel->getActiveSheet()->getStyle('B'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('H'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('I'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('J'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle('K'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle('L'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('M'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('N'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('O'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('P'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				for($j = 0; $j < count($array); $j++){
					$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				}
		}
	}
}




$cpv=0;
//cpm媒體
$sql33='SELECT * FROM media WHERE type=9 ORDER BY id';
$result33=mysql_query($sql33); 
if (mysql_num_rows($result33)>0){
	while($row33=mysql_fetch_array($result33)){
		$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
		$result3=mysql_query($sql3); 
		if (mysql_num_rows($result3)>0){
			while($row3=mysql_fetch_array($result3)){
				$cpv=1;
			}
		}
	}
}
if($cpv==1){
	// 標頭內容
	$number=$number+2;
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':Q'.($number+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':Q'.($number+1))->getFill()->getStartColor()->setRGB('DDDDDD'); 
	$array = array(0 => "B", 1 => "C", 2 => "D", 3 => "E", 4 => "F", 5 => "G", 6 => "H", 7 => "I", 8 => "J", 9 => "K", 10 => "L", 11 => "M", 12 => "N", 13 => "O", 14 => "P", 15 => "Q");
	for($j = 0; $j < count($array); $j++){
		for($i=$number;$i<=($number+1);$i++){
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
		}
	}
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, 'Website')
				->setCellValue('C'.$number, 'Channel')
				->setCellValue('D'.$number, 'System')
				->setCellValue('E'.$number, 'Position')
				->setCellValue('F'.$number, 'Size')
				->setCellValue('G'.$number, 'Format')
				->setCellValue('H'.$number, 'R/F')
				->setCellValue('I'.$number, 'Est.Impressions')
				->setCellValue('J'.$number, 'Est.CTR(%)')
				->setCellValue('K'.$number, 'Est.Clicks')
				->setCellValue('L'.$number, 'Period')
				->setCellValue('M'.$number, 'Days')
				->setCellValue('N'.$number, 'CPV計價')
				->setCellValue('O'.$number, 'Material Due')
				->setCellValue('P'.$number, 'Net Cost(NTD)')
				->setCellValue('Q'.$number, 'Other');
	$number=$number+1;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, '網站')
				->setCellValue('C'.$number, '頻道')
				->setCellValue('D'.$number, '系統')
				->setCellValue('E'.$number, '版位')
				->setCellValue('F'.$number, '規格')
				->setCellValue('G'.$number, '格式')
				->setCellValue('H'.$number, '輪替/固定')
				->setCellValue('I'.$number, '預估曝光數')
				->setCellValue('J'.$number, '預估點擊率')
				->setCellValue('K'.$number, '預估點擊數')
				->setCellValue('L'.$number, '刊登日期')
				->setCellValue('M'.$number, '天數')
				->setCellValue('N'.$number, '台幣')
				->setCellValue('O'.$number, '素材提供期限')
				->setCellValue('P'.$number, '售價')
				->setCellValue('Q'.$number, '備註');
	
	$sql33='SELECT * FROM media WHERE type=9 ORDER BY id';
	$result33=mysql_query($sql33); 
	if (mysql_num_rows($result33)>0){
		while($row33=mysql_fetch_array($result33)){
			$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
			$result3=mysql_query($sql3); 
			if (mysql_num_rows($result3)>0){
				while($row3=mysql_fetch_array($result3)){
					$number=$number+1;
					$datedate='';
					if($row3['price']!=NULL){
						$price1=$row3['price'];
					}else{
						$price1=$row3['price1'];
						if($row3['price2']!=NULL){$price1=$price1.','.$row3['price2'];}
						if($row3['price3']!=NULL){$price1=$price1.','.$row3['price3'];}
						if($row3['price4']!=NULL){$price1=$price1.','.$row3['price4'];}
						if($row3['price5']!=NULL){$price1=$price1.','.$row3['price5'];}
					}
					if($row3['date1']!=0){$datedate=date('m/d',$row3['date1']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date2']+ (8 * 60 * 60));}
					if($row3['date3']!=0){$datedate=$datedate.','.date('m/d',$row3['date3']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date4']+ (8 * 60 * 60));}
					if($row3['date5']!=0){$datedate=$datedate.','.date('m/d',$row3['date5']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date6']+ (8 * 60 * 60));}
					if($row3['date7']!=0){$datedate=$datedate.','.date('m/d',$row3['date7']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date8']+ (8 * 60 * 60));}
					if($row3['date9']!=0){$datedate=$datedate.','.date('m/d',$row3['date9']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date10']+ (8 * 60 * 60));}


					$website = '';
					$position = '';
					$format1 = '';
					$format2 = '';
					$quantity2 = '';
					$ctr = '';
					$quantity = '';
					$days = '';
					// $totalprice = '';

					if($row33['id'] == 119 || $row33['id'] == 121){
						$website = 'JS NATIVE SOLUTION';
						// $position = 'Applause Interstitial Video Ads';


					}else{
						$website = $row3['website'];
						// $position = $row3['position'];						
						
					}


					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('B'.$number, $website)
					->setCellValue('C'.$number, $row3['channel'])
					->setCellValue('D'.$number, $row3['phonesystem'])
					->setCellValue('E'.$number, $row3['position'])
					->setCellValue('F'.$number, str_replace("\\n", "\n", $row3['format1']))
					->setCellValue('G'.$number, str_replace("\\n", "\n", $row3['format2']))
					->setCellValue('H'.$number, $row3['wheel'])
					->setCellValue('I'.$number, $row3['quantity2'])
					->setCellValue('J'.$number, $row3['ctr'].'%')
					->setCellValue('K'.$number, $row3['quantity'])
					->setCellValue('L'.$number, $datedate)
					->setCellValue('M'.$number, $row3['days'])
					->setCellValue('N'.$number, $usdjpy.'$'.($price1/$rate))
					->setCellValue('O'.$number, $row3['due'])
					->setCellValue('P'.$number, $usdjpy.'$'.number_format($row3['totalprice']/$rate))
					->setCellValue('Q'.$number, $row3['others']);
					$totalprice=$totalprice+$row3['totalprice'];
					$objPHPExcel->getActiveSheet()->getStyle('B'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('H'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('I'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('J'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('K'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('L'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('M'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('N'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('O'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('P'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					for($j = 0; $j < count($array); $j++){
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					}
				}
			}
		}
	}
}




$webad=0;
//網站廣告媒體
$sql33='SELECT * FROM media WHERE type=3 ORDER BY id';
$result33=mysql_query($sql33); 
if (mysql_num_rows($result33)>0){
	while($row33=mysql_fetch_array($result33)){
		$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
		$result3=mysql_query($sql3); 
		if (mysql_num_rows($result3)>0){
			while($row3=mysql_fetch_array($result3)){
				$webad=1;
			}
		}
	}
}
if($webad==1){
	// 標頭內容
	$number=$number+2;
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':N'.($number+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':N'.($number+1))->getFill()->getStartColor()->setRGB('DDDDDD');
	$array = array(0 => "B", 1 => "C", 2 => "D", 3 => "E", 4 => "F", 5 => "G", 6 => "H", 7 => "I", 8 => "J", 9 => "K", 10 => "L", 11 => "M", 12 => "N");
	for($j = 0; $j < count($array); $j++){
		for($i=$number;$i<=($number+1);$i++){
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
		}
	}

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, 'Website')
				->setCellValue('C'.$number, 'Channel')
				->setCellValue('D'.$number, 'Position')
				->setCellValue('E'.$number, 'Size')
				->setCellValue('F'.$number, 'Format')
				->setCellValue('G'.$number, 'R/F')
				->setCellValue('H'.$number, 'Est.IMP')
				->setCellValue('I'.$number, 'Est.CTR(%)')
				->setCellValue('J'.$number, 'Est.Clicks')
				->setCellValue('K'.$number, 'Period')
				->setCellValue('L'.$number, 'Days')
				->setCellValue('M'.$number, 'Material Due')
				->setCellValue('N'.$number, 'Net Cost(NTD)');
	$number=$number+1;

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, '網站')
				->setCellValue('C'.$number, '頻道')
				->setCellValue('D'.$number, '版位')
				->setCellValue('E'.$number, '規格')
				->setCellValue('F'.$number, '格式')
				->setCellValue('G'.$number, '輪替/固定')
				->setCellValue('H'.$number, '預估曝光數') //發送封數 for happy go
				->setCellValue('I'.$number, '預估點擊率')
				->setCellValue('J'.$number, '預估點擊數')
				->setCellValue('K'.$number, '刊登日期')
				->setCellValue('L'.$number, '天數')
				->setCellValue('M'.$number, '素材提供期限')
				->setCellValue('N'.$number, '售價');
	
	$sql33='SELECT * FROM media WHERE type=3 ORDER BY id';
	$result33=mysql_query($sql33); 
	if (mysql_num_rows($result33)>0){
		while($row33=mysql_fetch_array($result33)){
			$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
			$result3=mysql_query($sql3); 
			if (mysql_num_rows($result3)>0){
				while($row3=mysql_fetch_array($result3)){
					$number=$number+1;
					$datedate='';
					
					if($row3['date1']!=0){$datedate=date('m/d',$row3['date1']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date2']+ (8 * 60 * 60));}
					if($row3['date3']!=0){$datedate=$datedate.','.date('m/d',$row3['date3']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date4']+ (8 * 60 * 60));}
					if($row3['date5']!=0){$datedate=$datedate.','.date('m/d',$row3['date5']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date6']+ (8 * 60 * 60));}
					if($row3['date7']!=0){$datedate=$datedate.','.date('m/d',$row3['date7']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date8']+ (8 * 60 * 60));}
					if($row3['date9']!=0){$datedate=$datedate.','.date('m/d',$row3['date9']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date10']+ (8 * 60 * 60));}
					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('B'.$number, $row3['website'])
					->setCellValue('C'.$number, $row3['channel'])
					->setCellValue('D'.$number, $row3['position'])
					->setCellValue('E'.$number, str_replace("\\n", "\n", $row3['format1']))
					->setCellValue('F'.$number, str_replace("\\n", "\n", $row3['format2']))
					->setCellValue('G'.$number, $row3['wheel'])
					->setCellValue('H'.$number, number_format($row3['quantity2'])) //number_format($row3['quantity']))
					->setCellValue('I'.$number, $row3['ctr'])
					->setCellValue('J'.$number, number_format($row3['quantity']))//number_format(($row3['quantity']*$row3['ctr'])/100)) //預估點擊數
					->setCellValue('K'.$number, $datedate)
					->setCellValue('L'.$number, $row3['days'])
					->setCellValue('M'.$number, $row3['due'])
					->setCellValue('N'.$number, $usdjpy.'$'.number_format($row3['totalprice']/$rate));
					$totalprice=$totalprice+$row3['totalprice'];
					$objPHPExcel->getActiveSheet()->getStyle('B'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('H'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('J'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('K'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('L'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('M'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('N'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					for($j = 0; $j < count($array); $j++){
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					}
				}
			}
		}
	}
}




$cpa=0;
//網站廣告媒體
$sql33='SELECT * FROM media WHERE type=4 ORDER BY id';
$result33=mysql_query($sql33); 
if (mysql_num_rows($result33)>0){
	while($row33=mysql_fetch_array($result33)){
		$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
		$result3=mysql_query($sql3); 
		if (mysql_num_rows($result3)>0){
			while($row3=mysql_fetch_array($result3)){
				$cpa=1;
			}
		}
	}
}
if($cpa==1){
	$rowsOrdinal = GetUsedMediaOrdinal($_GET['id']);
	
	if ((!in_array($chatBotMediaId, $rowsOrdinal) && count($rowsOrdinal)) || (in_array($chatBotMediaId, $rowsOrdinal) && count($rowsOrdinal) > 1)) {
		// 標頭內容
		$number=$number+2;
		$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':M'.($number+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':M'.($number+1))->getFill()->getStartColor()->setRGB('DDDDDD');
		$array = array(0 => "B", 1 => "C", 2 => "D", 3 => "E", 4 => "F", 5 => "G", 6 => "H", 7 => "I", 8 => "J", 9 => "K", 10 => "L", 11 => "M");
		for($j = 0; $j < count($array); $j++){
			for($i=$number;$i<=($number+1);$i++){
				$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
			}
		}

		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('B'.$number, 'Website')
					->setCellValue('C'.$number, 'Action')
					->setCellValue('D'.$number, 'Position')
					->setCellValue('E'.$number, 'Size')
					->setCellValue('F'.$number, 'Format')
					->setCellValue('G'.$number, 'R/F')
					->setCellValue('H'.$number, 'Est.Actions')
					->setCellValue('I'.$number, 'Period')
					->setCellValue('K'.$number, 'Days')
					->setCellValue('J'.$number, 'Material Due')
					->setCellValue('L'.$number, 'Cost Per')
					->setCellValue('M'.$number, 'Net Cost(NTD)');
		$number=$number+1;

		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('B'.$number, '網站')
					->setCellValue('C'.$number, 'CPA內容')
					->setCellValue('D'.$number, '版位')
					->setCellValue('E'.$number, '規格')
					->setCellValue('F'.$number, '格式')
					->setCellValue('G'.$number, '輪替/固定')
					->setCellValue('H'.$number, '會員人數')
					->setCellValue('I'.$number, '刊登日期')
					->setCellValue('J'.$number, '天數')
					->setCellValue('K'.$number, '素材提供期限')
					->setCellValue('L'.$number, 'CPA定價')
					->setCellValue('M'.$number, '售價');

		$sql33 = "SELECT * FROM media WHERE type=4 AND id NOT IN ($chatBotMediaId) ORDER BY id";
		$result33=mysql_query($sql33);

		while($row33=mysql_fetch_array($result33)){
			$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
			$result3=mysql_query($sql3); 
			if (mysql_num_rows($result3)>0){
				while($row3=mysql_fetch_array($result3)){
					$number=$number+1;
					$datedate='';
					if($row3['price']!=NULL){
						$price1=$usdjpy.'$'.($row3['price']/$rate);
					}else{
						$price1=$usdjpy.'$'.($row3['price1']/$rate);
						if($row3['price2']!=NULL){$price1=$price1.','. $usdjpy.'$'.($row3['price2']/$rate);}
						if($row3['price3']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price3']/$rate);}
						if($row3['price4']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price4']/$rate);}
						if($row3['price5']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price5']/$rate);}
					}
					if($row3['date1']!=0){$datedate=date('m/d',$row3['date1']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date2']+ (8 * 60 * 60));}
					if($row3['date3']!=0){$datedate=$datedate.','.date('m/d',$row3['date3']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date4']+ (8 * 60 * 60));}
					if($row3['date5']!=0){$datedate=$datedate.','.date('m/d',$row3['date5']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date6']+ (8 * 60 * 60));}
					if($row3['date7']!=0){$datedate=$datedate.','.date('m/d',$row3['date7']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date8']+ (8 * 60 * 60));}
					if($row3['date9']!=0){$datedate=$datedate.','.date('m/d',$row3['date9']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date10']+ (8 * 60 * 60));}
					if($row3['actions']!=NULL){$actions=$row3['actions'].'('.$row3['actions2'].')';}else{$actions='-';}
					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('B'.$number, $row3['website'])
					->setCellValue('C'.$number, $actions)
					->setCellValue('D'.$number, $row3['position'])
					->setCellValue('E'.$number, str_replace("\\n", "\n", $row3['format1']))
					->setCellValue('F'.$number, str_replace("\\n", "\n", $row3['format2']))
					->setCellValue('G'.$number, $row3['wheel'])
					->setCellValue('H'.$number, number_format($row3['quantity']))
					->setCellValue('I'.$number, $datedate)
					->setCellValue('J'.$number, $row3['days'])
					->setCellValue('K'.$number, $row3['due'])
					->setCellValue('L'.$number, $price1)
					->setCellValue('M'.$number, $usdjpy.'$'.number_format($row3['totalprice']/$rate));
					$totalprice=$totalprice+$row3['totalprice'];
					$objPHPExcel->getActiveSheet()->getStyle('B'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('H'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('J'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('K'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('L'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('M'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					for($j = 0; $j < count($array); $j++){
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					}
				}
			}
		}
	}











	
	
	
	if (in_array($chatBotMediaId, $rowsOrdinal)) {
		$number=$number+2;
		$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':N'.($number+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':N'.($number+1))->getFill()->getStartColor()->setRGB('DDDDDD');
		$array = array(0 => "B", 1 => "C", 2 => "D", 3 => "E", 4 => "F", 5 => "G", 6 => "H", 7 => "I", 8 => "J", 9 => "K", 10 => "L", 11 => "M", 12 => 'N');
		for($j = 0; $j < count($array); $j++){
			for($i=$number;$i<=($number+1);$i++){
				$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
			}
		}
	
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('B'.$number, 'Website')
					->setCellValue('C'.$number, 'Type')
					->setCellValue('D'.$number, 'Position')
					->setCellValue('E'.$number, 'Size')
					->setCellValue('F'.$number, 'Format')
					->setCellValue('G'.$number, 'R/F')
					->setCellValue('H'.$number, 'Est.Actions')
					->setCellValue('I'.$number, 'Period')
					->setCellValue('K'.$number, 'Days')
					->setCellValue('J'.$number, 'Material Due')
					->setCellValue('L'.$number, 'Cost Per')
					->setCellValue('M'.$number, 'Net Cost(NTD)')
					->setCellValue('N'.$number, 'Other');
		$number=$number+1;
	
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('B'.$number, '網站')
					->setCellValue('C'.$number, '類別')
					->setCellValue('D'.$number, '版位')
					->setCellValue('E'.$number, '規格')
					->setCellValue('F'.$number, '格式')
					->setCellValue('G'.$number, '輪替/固定')
					->setCellValue('H'.$number, '會員人數')
					->setCellValue('I'.$number, '刊登日期')
					->setCellValue('J'.$number, '天數')
					->setCellValue('K'.$number, '素材提供期限')
					->setCellValue('L'.$number, 'CPA定價')
					->setCellValue('M'.$number, '售價')
					->setCellValue('N'.$number, '備註');

		$sql33 = "SELECT * FROM media WHERE type=4 AND id IN ($chatBotMediaId) ORDER BY id";
		$result33=mysql_query($sql33);

		while($row33=mysql_fetch_array($result33)){
			$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
			$result3=mysql_query($sql3); 
			if (mysql_num_rows($result3)>0){
				while($row3=mysql_fetch_array($result3)){
					$number=$number+1;
					$datedate='';
					if($row3['price']!=NULL){
						$price1=$usdjpy.'$'.($row3['price']/$rate);
					}else{
						$price1=$usdjpy.'$'.($row3['price1']/$rate);
						if($row3['price2']!=NULL){$price1=$price1.','. $usdjpy.'$'.($row3['price2']/$rate);}
						if($row3['price3']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price3']/$rate);}
						if($row3['price4']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price4']/$rate);}
						if($row3['price5']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price5']/$rate);}
					}
					if($row3['date1']!=0){$datedate=date('m/d',$row3['date1']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date2']+ (8 * 60 * 60));}
					if($row3['date3']!=0){$datedate=$datedate.','.date('m/d',$row3['date3']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date4']+ (8 * 60 * 60));}
					if($row3['date5']!=0){$datedate=$datedate.','.date('m/d',$row3['date5']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date6']+ (8 * 60 * 60));}
					if($row3['date7']!=0){$datedate=$datedate.','.date('m/d',$row3['date7']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date8']+ (8 * 60 * 60));}
					if($row3['date9']!=0){$datedate=$datedate.','.date('m/d',$row3['date9']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date10']+ (8 * 60 * 60));}
					if($row3['actions']!=NULL){$actions=$row3['actions'].'('.$row3['actions2'].')';}else{$actions='-';}
					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('B'.$number, $row3['website'])
					->setCellValue('C'.$number, $row3['taget'])
					->setCellValue('D'.$number, $row3['position'])
					->setCellValue('E'.$number, str_replace("\\n", "\n", $row3['format1']))
					->setCellValue('F'.$number, str_replace("\\n", "\n", $row3['format2']))
					->setCellValue('G'.$number, $row3['wheel'])
					->setCellValue('H'.$number, number_format($row3['quantity']))
					->setCellValue('I'.$number, $datedate)
					->setCellValue('J'.$number, $row3['days'])
					->setCellValue('K'.$number, $row3['due'])
					->setCellValue('L'.$number, $price1)
					->setCellValue('M'.$number, $usdjpy.'$'.number_format($row3['totalprice']/$rate))
					->setCellValue('N'.$number, $row3['others']);
					$totalprice=$totalprice+$row3['totalprice'];
					$objPHPExcel->getActiveSheet()->getStyle('B'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('H'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('J'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('K'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('L'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('M'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					for($j = 0; $j < count($array); $j++){
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					}
				}
			}
		}
	}
}


$monipla=0;
//monipla
$sql33='SELECT * FROM media WHERE type=8 ORDER BY id';
$result33=mysql_query($sql33); 
if (mysql_num_rows($result33)>0){
	while($row33=mysql_fetch_array($result33)){
		$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
		$result3=mysql_query($sql3); 
		if (mysql_num_rows($result3)>0){
			while($row3=mysql_fetch_array($result3)){
				$monipla=1;
			}
		}
	}
}
if($monipla==1){
	// 標頭內容
	$number=$number+2;
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':M'.($number))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':M'.($number))->getFill()->getStartColor()->setRGB('DDDDDD');
	$array = array(0 => "B", 1 => "C", 2 => "D", 3 => "E", 4 => "F", 5 => "G", 6 => "H", 7 => "I", 8 => "J", 9 => "K", 10 => "L", 11 => "M");
	for($j = 0; $j < count($array); $j++){
		for($i=$number;$i<=($number+1);$i++){
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
		}
	}

	

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, '網站')
				->setCellValue('C'.$number, '頻道')
				->setCellValue('D'.$number, '版位')
				->setCellValue('E'.$number, '基本規格')
				->setCellValue('F'.$number, '格式')
				->setCellValue('G'.$number, '輪替/固定')
				->setCellValue('H'.$number, '模組內容')
				->setCellValue('I'.$number, '模組素材')
				->setCellValue('J'.$number, '是否需要創意素材')
				->setCellValue('K'.$number, '創意素材規範')
				->setCellValue('L'.$number, '售價')
				->setCellValue('M'.$number, '日期');
	
	$sql33='SELECT * FROM media WHERE type=8 ORDER BY id';
	$result33=mysql_query($sql33); 
	if (mysql_num_rows($result33)>0){
		while($row33=mysql_fetch_array($result33)){
			$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
			$result3=mysql_query($sql3); 
			if (mysql_num_rows($result3)>0){
				while($row3=mysql_fetch_array($result3)){
					$number=$number+1;
					$datedate='';
					if($row3['module']==NULL){
						$module='否';
					}else{
						$module='是';
					}
					if($row3['date1']!=0){$datedate=date('m/d',$row3['date1']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date2']+ (8 * 60 * 60));}
					if($row3['date3']!=0){$datedate=$datedate.','.date('m/d',$row3['date3']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date4']+ (8 * 60 * 60));}
					if($row3['date5']!=0){$datedate=$datedate.','.date('m/d',$row3['date5']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date6']+ (8 * 60 * 60));}
					if($row3['date7']!=0){$datedate=$datedate.','.date('m/d',$row3['date7']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date8']+ (8 * 60 * 60));}
					if($row3['date9']!=0){$datedate=$datedate.','.date('m/d',$row3['date9']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date10']+ (8 * 60 * 60));}
					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('B'.$number, $row3['website'])
					->setCellValue('C'.$number, $row3['channel'].'('.$row3['actions2'].')')
					->setCellValue('D'.$number, $row3['position'])
					->setCellValue('E'.$number, str_replace("\\n", "\n", $row3['format1']))
					->setCellValue('F'.$number, str_replace("\\n", "\n", $row3['format2']))
					->setCellValue('G'.$number, $row3['wheel'])
					->setCellValue('H'.$number, $row3['modulecontent'])
					->setCellValue('I'.$number, $row3['modulesource'])
					->setCellValue('J'.$number, $module)
					->setCellValue('K'.$number, $row3['module'])
					->setCellValue('L'.$number, $usdjpy.'$'.number_format($row3['totalprice']/$rate))
					->setCellValue('M'.$number, $datedate);
					$totalprice=$totalprice+$row3['totalprice'];
					$objPHPExcel->getActiveSheet()->getStyle('B'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('H'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('J'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('K'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('L'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('M'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					for($j = 0; $j < count($array); $j++){
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					}
				}
			}
		}
	}
}




$ad=0;
//網站廣告媒體
$sql3='SELECT * FROM media18 WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
$result3=mysql_query($sql3); 
if (mysql_num_rows($result3)>0){
	while($row3=mysql_fetch_array($result3)){
		$ad=1;
	}
}
if($ad==1){
	// 標頭內容
	$number=$number+2;
	$objPHPExcel->getActiveSheet()->mergeCells('B'.$number.':F'.$number);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':F'.($number+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':F'.($number+1))->getFill()->getStartColor()->setRGB('DDDDDD');
	$array = array(0 => "B", 1 => "C", 2 => "D", 3 => "E", 4 => "F");
	for($j = 0; $j < count($array); $j++){
		for($i=$number;$i<=($number+1);$i++){
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
		}
	}

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, 'Banner 製作費');
	$number=$number+1;

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, '項目')
				->setCellValue('C'.$number, 'unit cost')
				->setCellValue('D'.$number, 'unit')
				->setCellValue('E'.$number, 'total')
				->setCellValue('F'.$number, 'Total');
	$totalprice2=0;
	$number2=0;
	$sql3='SELECT * FROM media18 WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
	$result3=mysql_query($sql3); 
	if (mysql_num_rows($result3)>0){
		while($row3=mysql_fetch_array($result3)){
			$number=$number+1;
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('B'.$number, $row3['itemname'])
			->setCellValue('C'.$number, $usdjpy.'$'.number_format($row3['price']/$rate))
			->setCellValue('D'.$number, $row3['quantity'])
			->setCellValue('E'.$number, $usdjpy.'$'.number_format($row3['totalprice']/$rate));
			$totalprice2=$totalprice2+$row3['totalprice'];
			$number2=$number2+1;
			$totalprice=$totalprice+$row3['totalprice'];
			$objPHPExcel->getActiveSheet()->getStyle('C'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('D'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('F'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			for($j = 0; $j < count($array); $j++){
				$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			}
		}
		$objPHPExcel->getActiveSheet()->mergeCells('F'.($number-$number2+1).':F'.($number));
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('F'.($number-$number2+1), '$'.number_format($totalprice2));
	}
}




$blog=0;
//寫手費
for($i=19;$i<=19;$i++){
	$sql3='SELECT * FROM media'.$i.' WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
	$result3=mysql_query($sql3); 
	if (mysql_num_rows($result3)>0){
		while($row3=mysql_fetch_array($result3)){
			$blog=1;
			
		}
	}
}
if($blog==1){
	// 標頭內容
	$number=$number+2;
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':M'.($number+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':M'.($number+1))->getFill()->getStartColor()->setRGB('DDDDDD');
	$array = array(0 => "B", 1 => "C", 2 => "D", 3 => "E", 4 => "F", 5 => "G", 6 => "H", 7 => "I", 8 => "J", 9 => "K", 10 => "L", 11 => "M");
	for($j = 0; $j < count($array); $j++){
		for($i=$number;$i<=($number+1);$i++){
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
		}
	}

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, 'Website')
				->setCellValue('C'.$number, 'Link')
				->setCellValue('D'.$number, 'Net Cost(NTD)');
	$number=$number+1;

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, '名稱')
				->setCellValue('C'.$number, '連結')
				->setCellValue('D'.$number, '金額');
	
	$sql3='SELECT * FROM media19_detail WHERE campaign_id = '.$_GET['id'].'  ORDER BY id';
	$result3=mysql_query($sql3); 
	if (mysql_num_rows($result3)>0){
		while($row3=mysql_fetch_array($result3)){
			$number=$number+1;
			
			$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('B'.$number, $row3['blog1'].'：'.$row3['blog2'])
			->setCellValue('C'.$number, $row3['blog3'])
			->setCellValue('D'.$number, $usdjpy.'$'.number_format($row3['price2']/$rate));
			$totalprice=$totalprice+$row3['price2'];
			$objPHPExcel->getActiveSheet()->getStyle('B'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			for($j = 0; $j < count($array); $j++){
				$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			}
		}
	}
}


//額外媒體
$cpm=0;
$cpc=0;
$cpv=0;
$cpi=0;
$cpt=0;
$cpa=0;//網站廣告
//cpm媒體
$sql33='SELECT * FROM media WHERE type=11 ORDER BY id';
$result33=mysql_query($sql33); 
if (mysql_num_rows($result33)>0){
	while($row33=mysql_fetch_array($result33)){
		$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
		$result3=mysql_query($sql3); 
		if (mysql_num_rows($result3)>0){
			$sql_sp = 'SELECT * FROM media94 where campaign_id = '.$_GET['id'].' AND cue=1 AND media_type = "CPM" ORDER BY id';
			$result_sp = mysql_query($sql_sp); 
			if (mysql_num_rows($result_sp)>0){
				$cpm=1;
			}

			$sql_sp = 'SELECT * FROM media94 where campaign_id = '.$_GET['id'].' AND cue=1 AND media_type = "CPC" ORDER BY id';
			$result_sp = mysql_query($sql_sp); 
			if (mysql_num_rows($result_sp)>0){
				$cpc=1;
			}

			$sql_sp = 'SELECT * FROM media94 where campaign_id = '.$_GET['id'].' AND cue=1 AND media_type = "CPV" ORDER BY id';
			$result_sp = mysql_query($sql_sp); 
			if (mysql_num_rows($result_sp)>0){
				$cpv=1;
			}

			$sql_sp = 'SELECT * FROM media94 where campaign_id = '.$_GET['id'].' AND cue=1 AND media_type = "CPI" ORDER BY id';
			$result_sp = mysql_query($sql_sp); 
			if (mysql_num_rows($result_sp)>0){
				$cpi=1;
			}

			$sql_sp = 'SELECT * FROM media94 where campaign_id = '.$_GET['id'].' AND cue=1 AND media_type = "CPT" ORDER BY id';
			$result_sp = mysql_query($sql_sp); 
			if (mysql_num_rows($result_sp)>0){
				$cpt=1;
			}

			$sql_sp = 'SELECT * FROM media94 where campaign_id = '.$_GET['id'].' AND cue=1 AND media_type = "網站廣告" ORDER BY id';
			$result_sp = mysql_query($sql_sp); 
			if (mysql_num_rows($result_sp)>0){
				$cpa=1;
			}
		}
	}
}
if($cpm==1){
	// 標頭內容
	$number=$number+2;
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':Q'.($number+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':Q'.($number+1))->getFill()->getStartColor()->setRGB('DDDDDD'); 
	$array = array(0 => "B", 1 => "C", 2 => "D", 3 => "E", 4 => "F", 5 => "G", 6 => "H", 7 => "I", 8 => "J", 9 => "K", 10 => "L", 11 => "M", 12 => "N", 13 => "O", 14 => "P", 15 => "Q");
	for($j = 0; $j < count($array); $j++){
		for($i=$number;$i<=($number+1);$i++){
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
		}
	}
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, 'Website')
				->setCellValue('C'.$number, 'Channel')
				->setCellValue('D'.$number, 'System')
				->setCellValue('E'.$number, 'Position')
				->setCellValue('F'.$number, 'Size')
				->setCellValue('G'.$number, 'Format')
				->setCellValue('H'.$number, 'R/F')
				->setCellValue('I'.$number, 'Est.Impressions')
				->setCellValue('J'.$number, 'Est.CTR(%)')
				->setCellValue('K'.$number, 'Est.Clicks')
				->setCellValue('L'.$number, 'Period')
				->setCellValue('M'.$number, 'Days')
				->setCellValue('N'.$number, 'CPM計價')
				->setCellValue('O'.$number, 'Material Due')
				->setCellValue('P'.$number, 'Net Cost(NTD)')
				->setCellValue('Q'.$number, 'Other');
	$number=$number+1;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, '網站')
				->setCellValue('C'.$number, '頻道')
				->setCellValue('D'.$number, '系統')
				->setCellValue('E'.$number, '版位')
				->setCellValue('F'.$number, '規格')
				->setCellValue('G'.$number, '格式')
				->setCellValue('H'.$number, '輪替/固定')
				->setCellValue('I'.$number, '預估曝光數')
				->setCellValue('J'.$number, '預估點擊率')
				->setCellValue('K'.$number, '預估點擊數')
				->setCellValue('L'.$number, '刊登日期')
				->setCellValue('M'.$number, '天數')
				->setCellValue('N'.$number, '台幣')
				->setCellValue('O'.$number, '素材提供期限')
				->setCellValue('P'.$number, '售價')
				->setCellValue('Q'.$number, '備註');
	
	$sql33='SELECT * FROM media WHERE type=11 ORDER BY id';
	$result33=mysql_query($sql33); 
	if (mysql_num_rows($result33)>0){
		while($row33=mysql_fetch_array($result33)){
			$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 AND media_type = "CPM" ORDER BY id';
			$result3=mysql_query($sql3); 
			if (mysql_num_rows($result3)>0){
				while($row3=mysql_fetch_array($result3)){
					$other = '';
					
					$other .= $row3['others'];
					$number=$number+1;
					$datedate='';
					if($row3['price']!=NULL){
						$price1=$usdjpy.'$'.($row3['price']/$rate);
					}else{
						$price1=$usdjpy.'$'.($row3['price1']/$rate);
						if($row3['price2']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price2']/$rate);}
						if($row3['price3']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price3']/$rate);}
						if($row3['price4']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price4']/$rate);}
						if($row3['price5']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price5']/$rate);}
					}
					if($row3['date1']!=0){$datedate=date('m/d',$row3['date1']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date2']+ (8 * 60 * 60));}
					if($row3['date3']!=0){$datedate=$datedate.','.date('m/d',$row3['date3']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date4']+ (8 * 60 * 60));}
					if($row3['date5']!=0){$datedate=$datedate.','.date('m/d',$row3['date5']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date6']+ (8 * 60 * 60));}
					if($row3['date7']!=0){$datedate=$datedate.','.date('m/d',$row3['date7']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date8']+ (8 * 60 * 60));}
					if($row3['date9']!=0){$datedate=$datedate.','.date('m/d',$row3['date9']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date10']+ (8 * 60 * 60));}
					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('B'.$number, $row3['website'])
					->setCellValue('C'.$number, $row3['channel'])
					->setCellValue('D'.$number, $row3['phonesystem'])
					->setCellValue('E'.$number, $row3['position'])
					->setCellValue('F'.$number, str_replace("\\n", "\n", $row3['format1']))
					->setCellValue('G'.$number, str_replace("\\n", "\n", $row3['format2']))
					->setCellValue('H'.$number, $row3['wheel'])
					->setCellValue('I'.$number, $row3['quantity2'])
					->setCellValue('J'.$number, $row3['ctr'].'%')
					->setCellValue('K'.$number, $row3['quantity'])
					->setCellValue('L'.$number, $datedate)
					->setCellValue('M'.$number, $row3['days'])
					->setCellValue('N'.$number, $price1)
					->setCellValue('O'.$number, $row3['due'])
					->setCellValue('P'.$number, $usdjpy.'$'.number_format($row3['totalprice']/$rate))
					->setCellValue('Q'.$number, $other);
					$objPHPExcel->getActiveSheet()->getStyle('Q'.$number)->getAlignment()->setWrapText(true);
					$totalprice=$totalprice+$row3['totalprice'];
					$objPHPExcel->getActiveSheet()->getStyle('B'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('H'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('I'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('J'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('K'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('L'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('M'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('N'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('O'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('P'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					for($j = 0; $j < count($array); $j++){
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					}
				}
			}
		}
	}
}
if($cpc==1){
	// 標頭內容
	$number=$number+1;
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':Q'.($number+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':Q'.($number+1))->getFill()->getStartColor()->setRGB('DDDDDD'); 
	$array = array(0 => "B", 1 => "C", 2 => "D", 3 => "E", 4 => "F", 5 => "G", 6 => "H", 7 => "I", 8 => "J", 9 => "K", 10 => "L", 11 => "M", 12 => "N", 13 => "O", 14 => "P", 15 => "Q");
	for($j = 0; $j < count($array); $j++){
		for($i=$number;$i<=($number+1);$i++){
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
		}
	}
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, 'Website')
				->setCellValue('C'.$number, 'Channel')
				->setCellValue('D'.$number, 'System')
				->setCellValue('E'.$number, 'Position')
				->setCellValue('F'.$number, 'Size')
				->setCellValue('G'.$number, 'Format')
				->setCellValue('H'.$number, 'R/F')
				->setCellValue('I'.$number, 'Est.Impressions')
				->setCellValue('J'.$number, 'Est.CTR(%)')
				->setCellValue('K'.$number, 'Est.Clicks')
				->setCellValue('L'.$number, 'Period')
				->setCellValue('M'.$number, 'Days')
				->setCellValue('N'.$number, 'CPC計價')
				->setCellValue('O'.$number, 'Material Due')
				->setCellValue('P'.$number, 'Net Cost(NTD)')
				->setCellValue('Q'.$number, 'Other');
	$number=$number+1;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, '網站')
				->setCellValue('C'.$number, '頻道')
				->setCellValue('D'.$number, '系統')
				->setCellValue('E'.$number, '版位')
				->setCellValue('F'.$number, '規格')
				->setCellValue('G'.$number, '格式')
				->setCellValue('H'.$number, '輪替/固定')
				->setCellValue('I'.$number, '預估曝光數')
				->setCellValue('J'.$number, '預估點擊率')
				->setCellValue('K'.$number, '預估點擊數')
				->setCellValue('L'.$number, '刊登日期')
				->setCellValue('M'.$number, '天數')
				->setCellValue('N'.$number, '台幣')
				->setCellValue('O'.$number, '素材提供期限')
				->setCellValue('P'.$number, '售價')
				->setCellValue('Q'.$number, '備註');
	
	$sql33='SELECT * FROM media WHERE type=11 ORDER BY id';
	$result33=mysql_query($sql33); 
	if (mysql_num_rows($result33)>0){
		while($row33=mysql_fetch_array($result33)){
			$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 AND media_type = "CPC" ORDER BY id';
			$result3=mysql_query($sql3); 
			if (mysql_num_rows($result3)>0){
				while($row3=mysql_fetch_array($result3)){
					$other = '';
					$other .= $row3['others'];
					$number=$number+1;
					$datedate='';
					if($row3['price']!=NULL){
						$price1=$usdjpy.'$'.($row3['price']/$rate);
					}else{
						$price1=$usdjpy.'$'.($row3['price1']/$rate);
						if($row3['price2']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price2']/$rate);}
						if($row3['price3']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price3']/$rate);}
						if($row3['price4']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price4']/$rate);}
						if($row3['price5']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price5']/$rate);}
					}
					if($row3['date1']!=0){$datedate=date('m/d',$row3['date1']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date2']+ (8 * 60 * 60));}
					if($row3['date3']!=0){$datedate=$datedate.','.date('m/d',$row3['date3']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date4']+ (8 * 60 * 60));}
					if($row3['date5']!=0){$datedate=$datedate.','.date('m/d',$row3['date5']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date6']+ (8 * 60 * 60));}
					if($row3['date7']!=0){$datedate=$datedate.','.date('m/d',$row3['date7']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date8']+ (8 * 60 * 60));}
					if($row3['date9']!=0){$datedate=$datedate.','.date('m/d',$row3['date9']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date10']+ (8 * 60 * 60));}
					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('B'.$number, $row3['website'])
					->setCellValue('C'.$number, $row3['channel'])
					->setCellValue('D'.$number, $row3['phonesystem'])
					->setCellValue('E'.$number, $row3['position'])
					->setCellValue('F'.$number, str_replace("\\n", "\n", $row3['format1']))
					->setCellValue('G'.$number, str_replace("\\n", "\n", $row3['format2']))
					->setCellValue('H'.$number, $row3['wheel'])
					->setCellValue('I'.$number, $row3['quantity2'])
					->setCellValue('J'.$number, $row3['ctr'].'%')
					->setCellValue('K'.$number, $row3['quantity'])
					->setCellValue('L'.$number, $datedate)
					->setCellValue('M'.$number, $row3['days'])
					->setCellValue('N'.$number, $price1)
					->setCellValue('O'.$number, $row3['due'])
					->setCellValue('P'.$number, $usdjpy.'$'.number_format($row3['totalprice']/$rate))
					->setCellValue('Q'.$number, $other);
					$objPHPExcel->getActiveSheet()->getStyle('Q'.$number)->getAlignment()->setWrapText(true);
					$totalprice=$totalprice+$row3['totalprice'];
					$objPHPExcel->getActiveSheet()->getStyle('B'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('H'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('I'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('J'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('K'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('L'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('M'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('N'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('O'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('P'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					for($j = 0; $j < count($array); $j++){
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					}
				}
			}
		}
	}
}
if($cpv==1){
	// 標頭內容
	$number=$number+2;
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':Q'.($number+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':Q'.($number+1))->getFill()->getStartColor()->setRGB('DDDDDD'); 
	$array = array(0 => "B", 1 => "C", 2 => "D", 3 => "E", 4 => "F", 5 => "G", 6 => "H", 7 => "I", 8 => "J", 9 => "K", 10 => "L", 11 => "M", 12 => "N", 13 => "O", 14 => "P", 15 => "Q");
	for($j = 0; $j < count($array); $j++){
		for($i=$number;$i<=($number+1);$i++){
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
		}
	}
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, 'Website')
				->setCellValue('C'.$number, 'Channel')
				->setCellValue('D'.$number, 'System')
				->setCellValue('E'.$number, 'Position')
				->setCellValue('F'.$number, 'Size')
				->setCellValue('G'.$number, 'Format')
				->setCellValue('H'.$number, 'R/F')
				->setCellValue('I'.$number, 'Est.Impressions')
				->setCellValue('J'.$number, 'Est.CTR(%)')
				->setCellValue('K'.$number, 'Est.Clicks')
				->setCellValue('L'.$number, 'Period')
				->setCellValue('M'.$number, 'Days')
				->setCellValue('N'.$number, 'CPV計價')
				->setCellValue('O'.$number, 'Material Due')
				->setCellValue('P'.$number, 'Net Cost(NTD)')
				->setCellValue('Q'.$number, 'Other');
	$number=$number+1;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, '網站')
				->setCellValue('C'.$number, '頻道')
				->setCellValue('D'.$number, '系統')
				->setCellValue('E'.$number, '版位')
				->setCellValue('F'.$number, '規格')
				->setCellValue('G'.$number, '格式')
				->setCellValue('H'.$number, '輪替/固定')
				->setCellValue('I'.$number, '預估曝光數')
				->setCellValue('J'.$number, '預估點擊率')
				->setCellValue('K'.$number, '預估點擊數')
				->setCellValue('L'.$number, '刊登日期')
				->setCellValue('M'.$number, '天數')
				->setCellValue('N'.$number, '台幣')
				->setCellValue('O'.$number, '素材提供期限')
				->setCellValue('P'.$number, '售價')
				->setCellValue('Q'.$number, '備註');
	
	$sql33='SELECT * FROM media WHERE type=11 ORDER BY id';
	$result33=mysql_query($sql33); 
	if (mysql_num_rows($result33)>0){
		while($row33=mysql_fetch_array($result33)){
			$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 AND media_type = "CPV" ORDER BY id';
			$result3=mysql_query($sql3); 
			if (mysql_num_rows($result3)>0){
				while($row3=mysql_fetch_array($result3)){
					$number=$number+1;
					$datedate='';
					if($row3['price']!=NULL){
						$price1=$row3['price'];
					}else{
						$price1=$row3['price1'];
						if($row3['price2']!=NULL){$price1=$price1.','.$row3['price2'];}
						if($row3['price3']!=NULL){$price1=$price1.','.$row3['price3'];}
						if($row3['price4']!=NULL){$price1=$price1.','.$row3['price4'];}
						if($row3['price5']!=NULL){$price1=$price1.','.$row3['price5'];}
					}
					if($row3['date1']!=0){$datedate=date('m/d',$row3['date1']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date2']+ (8 * 60 * 60));}
					if($row3['date3']!=0){$datedate=$datedate.','.date('m/d',$row3['date3']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date4']+ (8 * 60 * 60));}
					if($row3['date5']!=0){$datedate=$datedate.','.date('m/d',$row3['date5']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date6']+ (8 * 60 * 60));}
					if($row3['date7']!=0){$datedate=$datedate.','.date('m/d',$row3['date7']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date8']+ (8 * 60 * 60));}
					if($row3['date9']!=0){$datedate=$datedate.','.date('m/d',$row3['date9']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date10']+ (8 * 60 * 60));}
					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('B'.$number, $row3['website'])
					->setCellValue('C'.$number, $row3['channel'])
					->setCellValue('D'.$number, $row3['phonesystem'])
					->setCellValue('E'.$number, $row3['position'])
					->setCellValue('F'.$number, str_replace("\\n", "\n", $row3['format1']))
					->setCellValue('G'.$number, str_replace("\\n", "\n", $row3['format2']))
					->setCellValue('H'.$number, $row3['wheel'])
					->setCellValue('I'.$number, $row3['quantity2'])
					->setCellValue('J'.$number, $row3['ctr'].'%')
					->setCellValue('K'.$number, $row3['quantity'])
					->setCellValue('L'.$number, $datedate)
					->setCellValue('M'.$number, $row3['days'])
					->setCellValue('N'.$number, $usdjpy.'$'.($price1/$rate))
					->setCellValue('O'.$number, $row3['due'])
					->setCellValue('P'.$number, $usdjpy.'$'.number_format($row3['totalprice']/$rate))
					->setCellValue('Q'.$number, $row3['others']);
					$totalprice=$totalprice+$row3['totalprice'];
					$objPHPExcel->getActiveSheet()->getStyle('B'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('H'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('I'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('J'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('K'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('L'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('M'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('N'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('O'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('P'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					for($j = 0; $j < count($array); $j++){
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					}
				}
			}
		}
	}
}
if($cpi==1){
	// 標頭內容
	$number=$number+2;
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':Q'.($number+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':Q'.($number+1))->getFill()->getStartColor()->setRGB('DDDDDD');
	$array = array(0 => "B", 1 => "C", 2 => "D", 3 => "E", 4 => "F", 5 => "G", 6 => "H", 7 => "I", 8 => "J", 9 => "K", 10 => "L", 11 => "M", 12 => "N", 13 => "O", 14 => "P", 15 => "Q");
	for($j = 0; $j < count($array); $j++){
		for($i=$number;$i<=($number+1);$i++){
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
		}
	}
	$objPHPExcel->getActiveSheet()->mergeCells('F'.$number.':G'.$number);
	$objPHPExcel->getActiveSheet()->mergeCells('H'.$number.':I'.$number);

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, 'Website')
				->setCellValue('C'.$number, 'Action')
				->setCellValue('D'.$number, 'system')
				->setCellValue('E'.$number, 'Position')
				->setCellValue('F'.$number, 'Size')
				->setCellValue('H'.$number, 'Format')
				->setCellValue('J'.$number, 'R/F')
				->setCellValue('K'.$number, 'Est.Actions')
				->setCellValue('L'.$number, 'Period')
				->setCellValue('M'.$number, 'Days')
				->setCellValue('N'.$number, 'Material Due')
				->setCellValue('O'.$number, 'Cost Per')
				->setCellValue('P'.$number, 'Net Cost(NTD)')
				->setCellValue('Q'.$number, 'Other');
	$number=$number+1;
	$objPHPExcel->getActiveSheet()->mergeCells('F'.$number.':G'.$number);
	$objPHPExcel->getActiveSheet()->mergeCells('H'.$number.':I'.$number);

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, '項目')
				->setCellValue('C'.$number, '執行內容')
				->setCellValue('D'.$number, 'OS')
				->setCellValue('E'.$number, '版位')
				->setCellValue('F'.$number, '規格')
				->setCellValue('H'.$number, '格式')
				->setCellValue('J'.$number, '輪替/固定')
				->setCellValue('K'.$number, '數量')
				->setCellValue('L'.$number, '刊登日期')
				->setCellValue('M'.$number, '天數')
				->setCellValue('N'.$number, '素材提供期限')
				->setCellValue('O'.$number, 'CPI定價')
				->setCellValue('P'.$number, '售價')
				->setCellValue('Q'.$number, '備註');
	
	$sql33='SELECT * FROM media WHERE type=11 ORDER BY id';
	$result33=mysql_query($sql33); 
	if (mysql_num_rows($result33)>0){
		while($row33=mysql_fetch_array($result33)){
			$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 AND media_type = "CPI" ORDER BY id';
			$result3=mysql_query($sql3); 
			if (mysql_num_rows($result3)>0){
				while($row3=mysql_fetch_array($result3)){
					$number=$number+1;
					$datedate='';
					if($row3['price']!=NULL){
						$price1=$usdjpy.'$'.($row3['price']/$rate);
					}else{
						$price1=$usdjpy.'$'.($row3['price1']/$rate);
						if($row3['price2']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price2']/$rate);}
						if($row3['price3']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price3']/$rate);}
						if($row3['price4']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price4']/$rate);}
						if($row3['price5']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price5']/$rate);}
					}
					if($row3['date1']!=0){$datedate=date('m/d',$row3['date1']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date2']+ (8 * 60 * 60));}
					if($row3['date3']!=0){$datedate=$datedate.','.date('m/d',$row3['date3']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date4']+ (8 * 60 * 60));}
					if($row3['date5']!=0){$datedate=$datedate.','.date('m/d',$row3['date5']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date6']+ (8 * 60 * 60));}
					if($row3['date7']!=0){$datedate=$datedate.','.date('m/d',$row3['date7']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date8']+ (8 * 60 * 60));}
					if($row3['date9']!=0){$datedate=$datedate.','.date('m/d',$row3['date9']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date10']+ (8 * 60 * 60));}
					$objPHPExcel->getActiveSheet()->mergeCells('F'.$number.':G'.$number);
					$objPHPExcel->getActiveSheet()->mergeCells('H'.$number.':I'.$number);
					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('B'.$number, $row3['website'])
					->setCellValue('C'.$number, $row3['actions'])
					->setCellValue('D'.$number, $row3['phonesystem'])
					->setCellValue('E'.$number, $row3['position'])
					->setCellValue('F'.$number, str_replace("\\n", "\n", $row3['format1']))
					->setCellValue('H'.$number, str_replace("\\n", "\n", $row3['format2']))
					->setCellValue('J'.$number, $row3['wheel'])
					->setCellValue('K'.$number, number_format($row3['quantity']))
					->setCellValue('L'.$number, $datedate)
					->setCellValue('M'.$number, $row3['days'])
					->setCellValue('N'.$number, $row3['due'])
					->setCellValue('O'.$number, $price1)
					->setCellValue('P'.$number, $usdjpy.'$'.number_format($row3['totalprice']/$rate))
					->setCellValue('Q'.$number, $row3['others']);
					$totalprice=$totalprice+$row3['totalprice'];
					$objPHPExcel->getActiveSheet()->getStyle('B'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('H'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('J'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('K'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('L'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('M'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('N'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('O'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('P'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					for($j = 0; $j < count($array); $j++){
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					}
				}
			}
		}
	}
}
if($cpt==1){
	// 標頭內容
	$number=$number+2;
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':Q'.($number+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':Q'.($number+1))->getFill()->getStartColor()->setRGB('DDDDDD');
	$array = array(0 => "B", 1 => "C", 2 => "D", 3 => "E", 4 => "F", 5 => "G", 6 => "H", 7 => "I", 8 => "J", 9 => "K", 10 => "L", 11 => "M", 12 => "N", 13 => "O", 14 => "P", 15 => "Q");
	for($j = 0; $j < count($array); $j++){
		for($i=$number;$i<=($number+1);$i++){
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
		}
	}
	$objPHPExcel->getActiveSheet()->mergeCells('F'.$number.':G'.$number);
	$objPHPExcel->getActiveSheet()->mergeCells('H'.$number.':I'.$number);

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, 'Website')
				->setCellValue('C'.$number, 'Action')
				->setCellValue('D'.$number, 'system')
				->setCellValue('E'.$number, 'Position')
				->setCellValue('F'.$number, 'Size')
				->setCellValue('H'.$number, 'Format')
				->setCellValue('J'.$number, 'R/F')
				->setCellValue('K'.$number, 'Est.Actions')
				->setCellValue('L'.$number, 'Period')
				->setCellValue('M'.$number, 'Days')
				->setCellValue('N'.$number, 'Material Due')
				->setCellValue('O'.$number, 'Cost Per')
				->setCellValue('P'.$number, 'Net Cost(NTD)')
				->setCellValue('Q'.$number, 'Other');
	$number=$number+1;
	$objPHPExcel->getActiveSheet()->mergeCells('F'.$number.':G'.$number);
	$objPHPExcel->getActiveSheet()->mergeCells('H'.$number.':I'.$number);

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, '項目')
				->setCellValue('C'.$number, '執行內容')
				->setCellValue('D'.$number, 'OS')
				->setCellValue('E'.$number, '版位')
				->setCellValue('F'.$number, '規格')
				->setCellValue('H'.$number, '格式')
				->setCellValue('J'.$number, '輪替/固定')
				->setCellValue('K'.$number, '數量')
				->setCellValue('L'.$number, '刊登日期')
				->setCellValue('M'.$number, '天數')
				->setCellValue('N'.$number, '素材提供期限')
				->setCellValue('O'.$number, 'CPT定價')
				->setCellValue('P'.$number, '售價')
				->setCellValue('Q'.$number, '備註');
	
	$sql33='SELECT * FROM media WHERE type=11 ORDER BY id';
	$result33=mysql_query($sql33); 
	if (mysql_num_rows($result33)>0){
		while($row33=mysql_fetch_array($result33)){
			$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 AND media_type = "CPT" ORDER BY id';
			$result3=mysql_query($sql3); 
			if (mysql_num_rows($result3)>0){
				while($row3=mysql_fetch_array($result3)){
					$number=$number+1;
					$datedate='';
					if($row3['price']!=NULL){
						$price1=$usdjpy.'$'.($row3['price']/$rate);
					}else{
						$price1=$usdjpy.'$'.($row3['price1']/$rate);
						if($row3['price2']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price2']/$rate);}
						if($row3['price3']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price3']/$rate);}
						if($row3['price4']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price4']/$rate);}
						if($row3['price5']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price5']/$rate);}
					}
					if($row3['date1']!=0){$datedate=date('m/d',$row3['date1']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date2']+ (8 * 60 * 60));}
					if($row3['date3']!=0){$datedate=$datedate.','.date('m/d',$row3['date3']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date4']+ (8 * 60 * 60));}
					if($row3['date5']!=0){$datedate=$datedate.','.date('m/d',$row3['date5']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date6']+ (8 * 60 * 60));}
					if($row3['date7']!=0){$datedate=$datedate.','.date('m/d',$row3['date7']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date8']+ (8 * 60 * 60));}
					if($row3['date9']!=0){$datedate=$datedate.','.date('m/d',$row3['date9']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date10']+ (8 * 60 * 60));}
					$objPHPExcel->getActiveSheet()->mergeCells('F'.$number.':G'.$number);
					$objPHPExcel->getActiveSheet()->mergeCells('H'.$number.':I'.$number);
					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('B'.$number, $row3['website'])
					->setCellValue('C'.$number, $row3['actions'])
					->setCellValue('D'.$number, $row3['phonesystem'])
					->setCellValue('E'.$number, $row3['position'])
					->setCellValue('F'.$number, str_replace("\\n", "\n", $row3['format1']))
					->setCellValue('H'.$number, str_replace("\\n", "\n", $row3['format2']))
					->setCellValue('J'.$number, $row3['wheel'])
					->setCellValue('K'.$number, number_format($row3['quantity']))
					->setCellValue('L'.$number, $datedate)
					->setCellValue('M'.$number, $row3['days'])
					->setCellValue('N'.$number, $row3['due'])
					->setCellValue('O'.$number, $price1)
					->setCellValue('P'.$number, $usdjpy.'$'.number_format($row3['totalprice']/$rate))
					->setCellValue('Q'.$number, $row3['others']);
					$totalprice=$totalprice+$row3['totalprice'];
					$objPHPExcel->getActiveSheet()->getStyle('B'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('H'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('J'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('K'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('L'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('M'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('N'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('O'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('P'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					for($j = 0; $j < count($array); $j++){
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					}
				}
			}
		}
	}
}
if($cpa==1){
	// 標頭內容
	$number=$number+2;
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':M'.($number+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':M'.($number+1))->getFill()->getStartColor()->setRGB('DDDDDD');
	$array = array(0 => "B", 1 => "C", 2 => "D", 3 => "E", 4 => "F", 5 => "G", 6 => "H", 7 => "I", 8 => "J", 9 => "K", 10 => "L", 11 => "M");
	for($j = 0; $j < count($array); $j++){
		for($i=$number;$i<=($number+1);$i++){
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
		}
	}

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, 'Website')
				->setCellValue('C'.$number, 'Action')
				->setCellValue('D'.$number, 'Position')
				->setCellValue('E'.$number, 'Size')
				->setCellValue('F'.$number, 'Format')
				->setCellValue('G'.$number, 'R/F')
				->setCellValue('H'.$number, 'Est.Actions')
				->setCellValue('I'.$number, 'Period')
				->setCellValue('K'.$number, 'Days')
				->setCellValue('J'.$number, 'Material Due')
				->setCellValue('L'.$number, 'Cost Per')
				->setCellValue('M'.$number, 'Net Cost(NTD)');
	$number=$number+1;

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, '網站')
				->setCellValue('C'.$number, 'CPA內容')
				->setCellValue('D'.$number, '版位')
				->setCellValue('E'.$number, '規格')
				->setCellValue('F'.$number, '格式')
				->setCellValue('G'.$number, '輪替/固定')
				->setCellValue('H'.$number, '會員人數')
				->setCellValue('I'.$number, '刊登日期')
				->setCellValue('J'.$number, '天數')
				->setCellValue('K'.$number, '素材提供期限')
				->setCellValue('L'.$number, 'CPA定價')
				->setCellValue('M'.$number, '售價');
	
	$sql33='SELECT * FROM media WHERE type=11 ORDER BY id';
	$result33=mysql_query($sql33); 
	if (mysql_num_rows($result33)>0){
		while($row33=mysql_fetch_array($result33)){
			$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 AND media_type = "網站廣告"  ORDER BY id';
			$result3=mysql_query($sql3); 
			if (mysql_num_rows($result3)>0){
				while($row3=mysql_fetch_array($result3)){
					$number=$number+1;
					$datedate='';
					if($row3['price']!=NULL){
						$price1=$usdjpy.'$'.($row3['price']/$rate);
					}else{
						$price1=$usdjpy.'$'.($row3['price1']/$rate);
						if($row3['price2']!=NULL){$price1=$price1.','. $usdjpy.'$'.($row3['price2']/$rate);}
						if($row3['price3']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price3']/$rate);}
						if($row3['price4']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price4']/$rate);}
						if($row3['price5']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price5']/$rate);}
					}
					if($row3['date1']!=0){$datedate=date('m/d',$row3['date1']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date2']+ (8 * 60 * 60));}
					if($row3['date3']!=0){$datedate=$datedate.','.date('m/d',$row3['date3']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date4']+ (8 * 60 * 60));}
					if($row3['date5']!=0){$datedate=$datedate.','.date('m/d',$row3['date5']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date6']+ (8 * 60 * 60));}
					if($row3['date7']!=0){$datedate=$datedate.','.date('m/d',$row3['date7']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date8']+ (8 * 60 * 60));}
					if($row3['date9']!=0){$datedate=$datedate.','.date('m/d',$row3['date9']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date10']+ (8 * 60 * 60));}
					if($row3['actions']!=NULL){$actions=$row3['actions'].'('.$row3['actions2'].')';}else{$actions='-';}
					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('B'.$number, $row3['website'])
					->setCellValue('C'.$number, $actions)
					->setCellValue('D'.$number, $row3['position'])
					->setCellValue('E'.$number, str_replace("\\n", "\n", $row3['format1']))
					->setCellValue('F'.$number, str_replace("\\n", "\n", $row3['format2']))
					->setCellValue('G'.$number, $row3['wheel'])
					->setCellValue('H'.$number, number_format($row3['quantity']))
					->setCellValue('I'.$number, $datedate)
					->setCellValue('J'.$number, $row3['days'])
					->setCellValue('K'.$number, $row3['due'])
					->setCellValue('L'.$number, $price1)
					->setCellValue('M'.$number, $usdjpy.'$'.number_format($row3['totalprice']/$rate));
					$totalprice=$totalprice+$row3['totalprice'];
					$objPHPExcel->getActiveSheet()->getStyle('B'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('H'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('J'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('K'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('L'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('M'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					for($j = 0; $j < count($array); $j++){
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					}
				}
			}
		}
	}
}



// <== 海外媒體CUE 表 ==>
$cpm=0;
//cpm媒體
$sql33='SELECT * FROM media WHERE type=20 ORDER BY id';
$result33=mysql_query($sql33); 
if (mysql_num_rows($result33)>0){
	while($row33=mysql_fetch_array($result33)){
		$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
		$result3=mysql_query($sql3); 
		if (mysql_num_rows($result3)>0){
			while($row3=mysql_fetch_array($result3)){
				$cpm=1;
			}
		}
	}
}
if($cpm==1){
	// 標頭內容
	$number=$number+2;
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':Q'.($number+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':Q'.($number+1))->getFill()->getStartColor()->setRGB('DDDDDD'); 
	$array = array(0 => "B", 1 => "C", 2 => "D", 3 => "E", 4 => "F", 5 => "G", 6 => "H", 7 => "I", 8 => "J", 9 => "K", 10 => "L", 11 => "M", 12 => "N", 13 => "O", 14 => "P", 15 => "Q");
	for($j = 0; $j < count($array); $j++){
		for($i=$number;$i<=($number+1);$i++){
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
		}
	}
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, 'Website')
				->setCellValue('C'.$number, 'Channel')
				->setCellValue('D'.$number, 'System')
				->setCellValue('E'.$number, 'Position')
				->setCellValue('F'.$number, 'Size')
				->setCellValue('G'.$number, 'Format')
				->setCellValue('H'.$number, 'R/F')
				->setCellValue('I'.$number, 'Est.Impressions')
				->setCellValue('J'.$number, 'Est.CTR(%)')
				->setCellValue('K'.$number, 'Est.Clicks')
				->setCellValue('L'.$number, 'Period')
				->setCellValue('M'.$number, 'Days')
				->setCellValue('N'.$number, 'CPM計價')
				->setCellValue('O'.$number, 'Material Due')
				->setCellValue('P'.$number, 'Net Cost(NTD)')
				->setCellValue('Q'.$number, 'Other');
	$number=$number+1;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, '網站')
				->setCellValue('C'.$number, '頻道')
				->setCellValue('D'.$number, '系統')
				->setCellValue('E'.$number, '版位')
				->setCellValue('F'.$number, '規格')
				->setCellValue('G'.$number, '格式')
				->setCellValue('H'.$number, '輪替/固定')
				->setCellValue('I'.$number, '預估曝光數')
				->setCellValue('J'.$number, '預估點擊率')
				->setCellValue('K'.$number, '預估點擊數')
				->setCellValue('L'.$number, '刊登日期')
				->setCellValue('M'.$number, '天數')
				->setCellValue('N'.$number, '台幣')
				->setCellValue('O'.$number, '素材提供期限')
				->setCellValue('P'.$number, '售價')
				->setCellValue('Q'.$number, '備註');
	
	$sql33='SELECT * FROM media WHERE type=20 ORDER BY id';
	$result33=mysql_query($sql33); 
	if (mysql_num_rows($result33)>0){
		while($row33=mysql_fetch_array($result33)){
			$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
			$result3=mysql_query($sql3); 
			if (mysql_num_rows($result3)>0){
				while($row3=mysql_fetch_array($result3)){
					$other = '';
					//whoscall 若有篩選 增加在備註
					if($row33['id'] == 86){
						if($row3['filter1'] != ''){
							$other .= "篩選條件－關鍵字設定：".$row3['filter1']."。\n";
						}
						if($row3['filter2'] != ''){
							$other .= "篩選條件－關聯號碼搜尋：".$row3['filter2']."。\n";
						}
					}
					$other .= $row3['others'];
					$number=$number+1;
					$datedate='';
					if($row3['price']!=NULL){
						$price1=$usdjpy.'$'.($row3['price']/$rate);
					}else{
						$price1=$usdjpy.'$'.($row3['price1']/$rate);
						if($row3['price2']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price2']/$rate);}
						if($row3['price3']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price3']/$rate);}
						if($row3['price4']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price4']/$rate);}
						if($row3['price5']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price5']/$rate);}
					}
					if($row3['date1']!=0){$datedate=date('m/d',$row3['date1']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date2']+ (8 * 60 * 60));}
					if($row3['date3']!=0){$datedate=$datedate.','.date('m/d',$row3['date3']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date4']+ (8 * 60 * 60));}
					if($row3['date5']!=0){$datedate=$datedate.','.date('m/d',$row3['date5']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date6']+ (8 * 60 * 60));}
					if($row3['date7']!=0){$datedate=$datedate.','.date('m/d',$row3['date7']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date8']+ (8 * 60 * 60));}
					if($row3['date9']!=0){$datedate=$datedate.','.date('m/d',$row3['date9']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date10']+ (8 * 60 * 60));}
					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('B'.$number, $row3['website'])
					->setCellValue('C'.$number, $row3['channel'])
					->setCellValue('D'.$number, $row3['phonesystem'])
					->setCellValue('E'.$number, $row3['position'])
					->setCellValue('F'.$number, str_replace("\\n", "\n", $row3['format1']))
					->setCellValue('G'.$number, str_replace("\\n", "\n", $row3['format2']))
					->setCellValue('H'.$number, $row3['wheel'])
					->setCellValue('I'.$number, $row3['quantity2'])
					->setCellValue('J'.$number, $row3['ctr'].'%')
					->setCellValue('K'.$number, $row3['quantity'])
					->setCellValue('L'.$number, $datedate)
					->setCellValue('M'.$number, $row3['days'])
					->setCellValue('N'.$number, $price1)
					->setCellValue('O'.$number, $row3['due'])
					->setCellValue('P'.$number, $usdjpy.'$'.number_format($row3['totalprice']/$rate))
					->setCellValue('Q'.$number, '「海外媒體」'.$other);
					$objPHPExcel->getActiveSheet()->getStyle('Q'.$number)->getAlignment()->setWrapText(true);
					$totalprice=$totalprice+$row3['totalprice'];
					$objPHPExcel->getActiveSheet()->getStyle('B'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('H'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('I'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('J'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('K'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('L'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('M'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('N'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('O'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('P'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					for($j = 0; $j < count($array); $j++){
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					}
				}
			}
		}
	}
}


$cpi=0;
//cpi媒體
$sql33='SELECT * FROM media WHERE type=22 ORDER BY id';
$result33=mysql_query($sql33); 
if (mysql_num_rows($result33)>0){
	while($row33=mysql_fetch_array($result33)){
		$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
		$result3=mysql_query($sql3); 
		if (mysql_num_rows($result3)>0){
			while($row3=mysql_fetch_array($result3)){
				$cpi=1;
			}
		}
	}
}
if($cpi==1){
	// 標頭內容
	$number=$number+2;
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':Q'.($number+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':Q'.($number+1))->getFill()->getStartColor()->setRGB('DDDDDD');
	$array = array(0 => "B", 1 => "C", 2 => "D", 3 => "E", 4 => "F", 5 => "G", 6 => "H", 7 => "I", 8 => "J", 9 => "K", 10 => "L", 11 => "M", 12 => "N", 13 => "O", 14 => "P", 15 => "Q");
	for($j = 0; $j < count($array); $j++){
		for($i=$number;$i<=($number+1);$i++){
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
		}
	}
	$objPHPExcel->getActiveSheet()->mergeCells('F'.$number.':G'.$number);
	$objPHPExcel->getActiveSheet()->mergeCells('H'.$number.':I'.$number);

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, 'Website')
				->setCellValue('C'.$number, 'Action')
				->setCellValue('D'.$number, 'system')
				->setCellValue('E'.$number, 'Position')
				->setCellValue('F'.$number, 'Size')
				->setCellValue('H'.$number, 'Format')
				->setCellValue('J'.$number, 'R/F')
				->setCellValue('K'.$number, 'Est.Actions')
				->setCellValue('L'.$number, 'Period')
				->setCellValue('M'.$number, 'Days')
				->setCellValue('N'.$number, 'Material Due')
				->setCellValue('O'.$number, 'Cost Per')
				->setCellValue('P'.$number, 'Net Cost(NTD)')
				->setCellValue('Q'.$number, 'Other');
	$number=$number+1;
	$objPHPExcel->getActiveSheet()->mergeCells('F'.$number.':G'.$number);
	$objPHPExcel->getActiveSheet()->mergeCells('H'.$number.':I'.$number);

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, '項目')
				->setCellValue('C'.$number, '執行內容')
				->setCellValue('D'.$number, 'OS')
				->setCellValue('E'.$number, '版位')
				->setCellValue('F'.$number, '規格')
				->setCellValue('H'.$number, '格式')
				->setCellValue('J'.$number, '輪替/固定')
				->setCellValue('K'.$number, '數量')
				->setCellValue('L'.$number, '刊登日期')
				->setCellValue('M'.$number, '天數')
				->setCellValue('N'.$number, '素材提供期限')
				->setCellValue('O'.$number, 'CPI定價')
				->setCellValue('P'.$number, '售價')
				->setCellValue('Q'.$number, '備註');
	
	$sql33='SELECT * FROM media WHERE type=22 ORDER BY id';
	$result33=mysql_query($sql33); 
	if (mysql_num_rows($result33)>0){
		while($row33=mysql_fetch_array($result33)){
			$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
			$result3=mysql_query($sql3); 
			if (mysql_num_rows($result3)>0){
				while($row3=mysql_fetch_array($result3)){
					$number=$number+1;
					$datedate='';
					if($row3['price']!=NULL){
						$price1=$usdjpy.'$'.($row3['price']/$rate);
					}else{
						$price1=$usdjpy.'$'.($row3['price1']/$rate);
						if($row3['price2']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price2']/$rate);}
						if($row3['price3']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price3']/$rate);}
						if($row3['price4']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price4']/$rate);}
						if($row3['price5']!=NULL){$price1=$price1.','.$usdjpy.'$'.($row3['price5']/$rate);}
					}
					if($row3['date1']!=0){$datedate=date('m/d',$row3['date1']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date2']+ (8 * 60 * 60));}
					if($row3['date3']!=0){$datedate=$datedate.','.date('m/d',$row3['date3']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date4']+ (8 * 60 * 60));}
					if($row3['date5']!=0){$datedate=$datedate.','.date('m/d',$row3['date5']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date6']+ (8 * 60 * 60));}
					if($row3['date7']!=0){$datedate=$datedate.','.date('m/d',$row3['date7']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date8']+ (8 * 60 * 60));}
					if($row3['date9']!=0){$datedate=$datedate.','.date('m/d',$row3['date9']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date10']+ (8 * 60 * 60));}
					$objPHPExcel->getActiveSheet()->mergeCells('F'.$number.':G'.$number);
					$objPHPExcel->getActiveSheet()->mergeCells('H'.$number.':I'.$number);
					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('B'.$number, $row3['website'])
					->setCellValue('C'.$number, $row3['actions'])
					->setCellValue('D'.$number, $row3['phonesystem'])
					->setCellValue('E'.$number, $row3['position'])
					->setCellValue('F'.$number, str_replace("\\n", "\n", $row3['format1']))
					->setCellValue('H'.$number, str_replace("\\n", "\n", $row3['format2']))
					->setCellValue('J'.$number, $row3['wheel'])
					->setCellValue('K'.$number, number_format($row3['quantity']))
					->setCellValue('L'.$number, $datedate)
					->setCellValue('M'.$number, $row3['days'])
					->setCellValue('N'.$number, $row3['due'])
					->setCellValue('O'.$number, $price1)
					->setCellValue('P'.$number, $usdjpy.'$'.number_format($row3['totalprice']/$rate))
					->setCellValue('Q'.$number, '「海外媒體」'.$row3['others']);
					$totalprice=$totalprice+$row3['totalprice'];
					$objPHPExcel->getActiveSheet()->getStyle('B'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('H'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('J'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('K'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('L'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('M'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('N'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('O'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('P'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					for($j = 0; $j < count($array); $j++){
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					}
				}
			}
		}
	}
}


$cpv=0;
//cpv媒體
$sql33='SELECT * FROM media WHERE type=29 ORDER BY id';
$result33=mysql_query($sql33); 
if (mysql_num_rows($result33)>0){
	while($row33=mysql_fetch_array($result33)){
		$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
		$result3=mysql_query($sql3); 
		if (mysql_num_rows($result3)>0){
			while($row3=mysql_fetch_array($result3)){
				$cpv=1;
			}
		}
	}
}
if($cpv==1){
	// 標頭內容
	$number=$number+2;
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':Q'.($number+1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle('B'.$number.':Q'.($number+1))->getFill()->getStartColor()->setRGB('DDDDDD'); 
	$array = array(0 => "B", 1 => "C", 2 => "D", 3 => "E", 4 => "F", 5 => "G", 6 => "H", 7 => "I", 8 => "J", 9 => "K", 10 => "L", 11 => "M", 12 => "N", 13 => "O", 14 => "P", 15 => "Q");
	for($j = 0; $j < count($array); $j++){
		for($i=$number;$i<=($number+1);$i++){
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
		}
	}
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, 'Website')
				->setCellValue('C'.$number, 'Channel')
				->setCellValue('D'.$number, 'System')
				->setCellValue('E'.$number, 'Position')
				->setCellValue('F'.$number, 'Size')
				->setCellValue('G'.$number, 'Format')
				->setCellValue('H'.$number, 'R/F')
				->setCellValue('I'.$number, 'Est.Impressions')
				->setCellValue('J'.$number, 'Est.CTR(%)')
				->setCellValue('K'.$number, 'Est.Clicks')
				->setCellValue('L'.$number, 'Period')
				->setCellValue('M'.$number, 'Days')
				->setCellValue('N'.$number, 'CPV計價')
				->setCellValue('O'.$number, 'Material Due')
				->setCellValue('P'.$number, 'Net Cost(NTD)')
				->setCellValue('Q'.$number, 'Other');
	$number=$number+1;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B'.$number, '網站')
				->setCellValue('C'.$number, '頻道')
				->setCellValue('D'.$number, '系統')
				->setCellValue('E'.$number, '版位')
				->setCellValue('F'.$number, '規格')
				->setCellValue('G'.$number, '格式')
				->setCellValue('H'.$number, '輪替/固定')
				->setCellValue('I'.$number, '預估曝光數')
				->setCellValue('J'.$number, '預估點擊率')
				->setCellValue('K'.$number, '預估點擊數')
				->setCellValue('L'.$number, '刊登日期')
				->setCellValue('M'.$number, '天數')
				->setCellValue('N'.$number, '台幣')
				->setCellValue('O'.$number, '素材提供期限')
				->setCellValue('P'.$number, '售價')
				->setCellValue('Q'.$number, '備註');
	
	$sql33='SELECT * FROM media WHERE type=29 ORDER BY id';
	$result33=mysql_query($sql33); 
	if (mysql_num_rows($result33)>0){
		while($row33=mysql_fetch_array($result33)){
			$sql3='SELECT * FROM media'.$row33['id'].' WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
			$result3=mysql_query($sql3); 
			if (mysql_num_rows($result3)>0){
				while($row3=mysql_fetch_array($result3)){
					$number=$number+1;
					$datedate='';
					if($row3['price']!=NULL){
						$price1=$row3['price'];
					}else{
						$price1=$row3['price1'];
						if($row3['price2']!=NULL){$price1=$price1.','.$row3['price2'];}
						if($row3['price3']!=NULL){$price1=$price1.','.$row3['price3'];}
						if($row3['price4']!=NULL){$price1=$price1.','.$row3['price4'];}
						if($row3['price5']!=NULL){$price1=$price1.','.$row3['price5'];}
					}
					if($row3['date1']!=0){$datedate=date('m/d',$row3['date1']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date2']+ (8 * 60 * 60));}
					if($row3['date3']!=0){$datedate=$datedate.','.date('m/d',$row3['date3']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date4']+ (8 * 60 * 60));}
					if($row3['date5']!=0){$datedate=$datedate.','.date('m/d',$row3['date5']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date6']+ (8 * 60 * 60));}
					if($row3['date7']!=0){$datedate=$datedate.','.date('m/d',$row3['date7']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date8']+ (8 * 60 * 60));}
					if($row3['date9']!=0){$datedate=$datedate.','.date('m/d',$row3['date9']+ (8 * 60 * 60)).'~'.date('m/d',$row3['date10']+ (8 * 60 * 60));}
					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('B'.$number, $row3['website'])
					->setCellValue('C'.$number, $row3['channel'])
					->setCellValue('D'.$number, $row3['phonesystem'])
					->setCellValue('E'.$number, $row3['position'])
					->setCellValue('F'.$number, str_replace("\\n", "\n", $row3['format1']))
					->setCellValue('G'.$number, str_replace("\\n", "\n", $row3['format2']))
					->setCellValue('H'.$number, $row3['wheel'])
					->setCellValue('I'.$number, $row3['quantity2'])
					->setCellValue('J'.$number, $row3['ctr'].'%')
					->setCellValue('K'.$number, $row3['quantity'])
					->setCellValue('L'.$number, $datedate)
					->setCellValue('M'.$number, $row3['days'])
					->setCellValue('N'.$number, $usdjpy.'$'.($price1/$rate))
					->setCellValue('O'.$number, $row3['due'])
					->setCellValue('P'.$number, $usdjpy.'$'.number_format($row3['totalprice']/$rate))
					->setCellValue('Q'.$number, '「海外媒體」'.$row3['others']);
					$totalprice=$totalprice+$row3['totalprice'];
					$objPHPExcel->getActiveSheet()->getStyle('B'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('H'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('I'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('J'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('K'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					$objPHPExcel->getActiveSheet()->getStyle('L'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('M'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('N'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('O'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$objPHPExcel->getActiveSheet()->getStyle('P'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
					for($j = 0; $j < count($array); $j++){
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
						$objPHPExcel->getActiveSheet()->getStyle($array[$j].$number)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					}
				}
			}
		}
	}
}


//海外媒體 結束




$number=$number+2;
$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('B'.$number, '委刊客戶')
			->setCellValue('B'.($number+1), '負責人簽署')
			->setCellValue('B'.($number+2), '委刊客戶')
			->setCellValue('B'.($number+3), '經辦人簽署')
			->setCellValue('B'.($number+4), '委刊客戶')
			->setCellValue('B'.($number+5), '公司印鑑')
			->setCellValue('B'.($number+7), '傑思愛德威媒體')
			->setCellValue('B'.($number+8), '負責人簽署')
			->setCellValue('B'.($number+9), '業務行銷')
			->setCellValue('B'.($number+10), '總監簽署')
			->setCellValue('B'.($number+11), '傑思愛德威媒體')
			->setCellValue('B'.($number+12), '公司印鑑')
			->setCellValue('E'.($number+9), '業務行銷')
			->setCellValue('E'.($number+10), '簽署');
$array = array(0 => "B", 1 => "C", 2 => "D", 3 => "E", 4 => "F", 5 => "G");
for($i = $number; $i <= ($number+5); $i++){
	for($j = 0; $j < count($array); $j++){
		$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	}
}
$array = array(0 => "B", 1 => "C", 2 => "D", 3 => "E", 4 => "F", 5 => "G");
for($k = ($number+7); $k <= ($number+12); $k++){
	for($j = 0; $j < count($array); $j++){
		$objPHPExcel->getActiveSheet()->getStyle($array[$j].$k)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle($array[$j].$k)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle($array[$j].$k)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle($array[$j].$k)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle($array[$j].$k)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	}
}
$objPHPExcel->getActiveSheet()->mergeCells('C'.($number).':G'.($number+1));
$objPHPExcel->getActiveSheet()->mergeCells('C'.($number+2).':G'.($number+3));
$objPHPExcel->getActiveSheet()->mergeCells('C'.($number+4).':G'.($number+5));
$objPHPExcel->getActiveSheet()->mergeCells('C'.($number+7).':G'.($number+8));
$objPHPExcel->getActiveSheet()->mergeCells('C'.($number+9).':D'.($number+10));
$objPHPExcel->getActiveSheet()->mergeCells('F'.($number+9).':G'.($number+10));
$objPHPExcel->getActiveSheet()->mergeCells('C'.($number+11).':G'.($number+12));
//金額列印
$array = array(0 => "I", 1 => "J", 2 => "K", 3 => "L", 4 => "M", 5 => "N", 6 => "O");
for($i = $number; $i <= ($number+8); $i++){
	for($j = 0; $j < count($array); $j++){
		$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getAlignment()->setvertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

	}
}
if($row2['rate']==NULL){
	$objPHPExcel->getActiveSheet()->mergeCells('I'.$number.':O'.$number);
	$objPHPExcel->getActiveSheet()->mergeCells('I'.($number+1).':O'.($number+5));
	$objPHPExcel->getActiveSheet()->mergeCells('I'.($number+6).':I'.($number+8));
	$objPHPExcel->getActiveSheet()->mergeCells('J'.($number+6).':K'.($number+8));
	$objPHPExcel->getActiveSheet()->mergeCells('L'.($number+6).':M'.($number+8));
	$objPHPExcel->getActiveSheet()->mergeCells('N'.($number+6).':O'.($number+8));
	$objPHPExcel->getActiveSheet()->getStyle('I'.($number+1).':O'.($number+5))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('I'.($number+1).':O'.($number+5))->getFont()->getColor()->setRGB('FF0000');
	$objPHPExcel->getActiveSheet()->getStyle('I'.($number+1).':O'.($number+5))->getFont()->setSize(48);
	$objPHPExcel->getActiveSheet()->getStyle('J'.($number+6))->getFont()->setSize(24);
	$objPHPExcel->getActiveSheet()->getStyle('N'.($number+6))->getFont()->setSize(24);
	$objPHPExcel->getActiveSheet()->getStyle('I'.($number+6))->getFont()->setSize(14);
	$objPHPExcel->getActiveSheet()->getStyle('L'.($number+6))->getFont()->setSize(14);
}else{
	$objPHPExcel->getActiveSheet()->mergeCells('I'.$number.':O'.$number);
	$objPHPExcel->getActiveSheet()->mergeCells('I'.($number+1).':O'.($number+3));
	$objPHPExcel->getActiveSheet()->mergeCells('I'.($number+4).':O'.($number+5));
	$objPHPExcel->getActiveSheet()->mergeCells('I'.($number+6).':I'.($number+8));
	$objPHPExcel->getActiveSheet()->mergeCells('J'.($number+6).':K'.($number+7));
	$objPHPExcel->getActiveSheet()->mergeCells('J'.($number+8).':K'.($number+8));
	$objPHPExcel->getActiveSheet()->mergeCells('L'.($number+6).':M'.($number+8));
	$objPHPExcel->getActiveSheet()->mergeCells('N'.($number+6).':O'.($number+7));
	$objPHPExcel->getActiveSheet()->mergeCells('N'.($number+8).':O'.($number+8));
	$objPHPExcel->getActiveSheet()->getStyle('I'.($number+1).':O'.($number+5))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('I'.($number+1).':O'.($number+5))->getFont()->getColor()->setRGB('FF0000');
	$objPHPExcel->getActiveSheet()->getStyle('I'.($number+1).':O'.($number+3))->getFont()->setSize(48);
	$objPHPExcel->getActiveSheet()->getStyle('I'.($number+4).':O'.($number+5))->getFont()->setSize(24);
	$objPHPExcel->getActiveSheet()->getStyle('J'.($number+6))->getFont()->setSize(24);
	$objPHPExcel->getActiveSheet()->getStyle('N'.($number+6))->getFont()->setSize(24);
	$objPHPExcel->getActiveSheet()->getStyle('I'.($number+6))->getFont()->setSize(14);
	$objPHPExcel->getActiveSheet()->getStyle('L'.($number+6))->getFont()->setSize(14);	
}

if($row2['rate']==NULL){

$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('I'.$number, '總廣告預算金額')
			->setCellValue('I'.($number+1), 'NT$'.number_format($totalprice))
			->setCellValue('I'.($number+6), '稅金(5%)')
			->setCellValue('J'.($number+6), 'NT$'.number_format(($totalprice*0.05)))
			->setCellValue('L'.($number+6), 'Gross Cost
(含稅價)')
			->setCellValue('N'.($number+6), 'NT$'.number_format(($totalprice*1.05)));
$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('I'.($number+9), '《 Remark 》')
			->setCellValue('I'.($number+10), '1 )  請詳細檢查上表正確無誤後於托播前簽章回傳至 02-6636-0166')
			->setCellValue('I'.($number+11), '2 )  廣告素材如以Flash形式表現，製作規範可到
http://cony.nicecampaign.com/JS_formal_of_sozai/JS formal of sozai.htm 查詢，並須提供原始檔(.fla)')
			->setCellValue('I'.($number+12), '3 )  廣告素材格式請以Gif, Jpg, Flash為主，文字格式為text')
			->setCellValue('I'.($number+13), '4 )  以CPC模式採購之媒體，預估曝光數及CTR均為參考值，不保證一定達到。')
			->setCellValue('I'.($number+14), '5 )  以CPI或CPA模式採購之媒體，預估點擊數、曝光數及CTR均為參考值，不保證一定達到。')
			->setCellValue('I'.($number+15), '6 )  所有素材得至少見刊前一天16:00前提供確認無誤後之檔案，始能上稿，若超過時限，恕無法刊登')
			->setCellValue('I'.($number+16), '7 )  傑思愛德威媒體保留最後廣告刊登之權利。');
}else{
	if($row2['rate']>1){
		$usdjpy='USD ';
	}else{
		$usdjpy='JPY ';
	}

	$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('I'.$number, '總廣告預算金額')
			->setCellValue('I'.($number+1), 'NT$'.number_format($totalprice))
			->setCellValue('I'.($number+4),  $usdjpy.number_format($totalprice/$row2['rate']))
			->setCellValue('I'.($number+6), '稅金(5%)')
			->setCellValue('J'.($number+6), 'NT$'.number_format(($totalprice*0.05)))
			->setCellValue('J'.($number+8),  $usdjpy.number_format((($totalprice*0.05)/$row2['rate'])))
			->setCellValue('L'.($number+6), 'Gross Cost
(含稅價)')
			->setCellValue('N'.($number+6), 'NT$'.number_format($totalprice*1.05))
			->setCellValue('N'.($number+8),  $usdjpy.number_format(($totalprice*1.05)/$row2['rate']));
$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('I'.($number+9), '《 Remark 》')
			->setCellValue('I'.($number+10), '1 )  請詳細檢查上表正確無誤後於托播前簽章回傳至 02-6636-0166')
			->setCellValue('I'.($number+11), '2 )  廣告素材如以Flash形式表現，製作規範可到
http://cony.nicecampaign.com/JS_formal_of_sozai/JS formal of sozai.htm 查詢，並須提供原始檔(.fla)')
			->setCellValue('I'.($number+12), '3 )  廣告素材格式請以Gif, Jpg, Flash為主，文字格式為text')
			->setCellValue('I'.($number+13), '4 )  以CPC模式採購之媒體，預估曝光數及CTR均為參考值，不保證一定達到。')
			->setCellValue('I'.($number+14), '5 )  以CPI或CPA模式採購之媒體，預估點擊數、曝光數及CTR均為參考值，不保證一定達到。')
			->setCellValue('I'.($number+15), '6 )  所有素材得至少見刊前一天16:00前提供確認無誤後之檔案，始能上稿，若超過時限，恕無法刊登')
			->setCellValue('I'.($number+16), '7 )  傑思愛德威媒體保留最後廣告刊登之權利。');
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
if($row2['agency_id']==0){
	$sql3 = "SELECT * FROM client WHERE id= ".$row2['client_id'];
	$result3 = mysql_query($sql3);
	$row3 = mysql_fetch_array($result3);
	$filename1=$row3['name2'];
}else{
	$sql3 = "SELECT * FROM agency WHERE id= ".$row2['agency_id'];
	$result3 = mysql_query($sql3);
	$row3 = mysql_fetch_array($result3);
	$filename1=$row3['name2'];
}
if($row2['times']==NULL){
	$timetimes=date('ymd',$row2['time']);
}else{
	$timetimes=date('ymd',$row2['times']);
}

$userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
$isWindowsPlatform = strpos($userAgent, "windows ") !== false;

$xlsFilename = $filename1 .'_'. $row2['name'] .'_'. date('md', $row2['date11']) .'-'. date('md', $row2['date22']) .'_'. $timetimes . ($isWindowsPlatform ? '.xlsx' : '.xls');

header("Content-disposition: attachment; filename=\"".addslashes($xlsFilename)."\";");

header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $isWindowsPlatform ? 'Excel2007' : 'Excel5');
$objWriter->save('php://output');
exit;
?>