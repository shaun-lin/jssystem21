<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2015 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

// date_default_timezone_set('Europe/London');

/** Include PHPExcel_IOFactory */
require_once dirname(__FILE__) . '/Classes/PHPExcel/IOFactory.php';
// require_once dirname(__FILE__) . '/../Classes/PHPExcel/IOFactory.php';

$popinFileName = 'popin.xls';
$applauseFileName = 'applause.xls';

$popinFile = basename($_FILES["popinFile"]["name"]);
$applauseFile = basename($_FILES["applauseFile"]["name"]);

if (move_uploaded_file($_FILES["popinFile"]["tmp_name"], $popinFileName)) {
    // echo "The file ". basename( $_FILES["popinFile"]["name"]). " has been uploaded.";
} else {
	exit("Sorry, there was an error uploading your file." . EOL);
}

if (move_uploaded_file($_FILES["applauseFile"]["tmp_name"], $applauseFileName)) {
    // echo "The file ". basename( $_FILES["applauseFile"]["name"]). " has been uploaded.";
} else {
    exit("Sorry, there was an error uploading your file." . EOL);
}


if (!file_exists($popinFileName)) {
	exit("Please import ".$popinFileName." first." . EOL);
}

if (!file_exists($applauseFileName)) {
	exit("Please import ".$applauseFileName." first." . EOL);
}

// echo date('H:i:s') , " Load from Excel2007 file" , EOL;
$callStartTime = microtime(true);

$objPHPExcelPopin = PHPExcel_IOFactory::load($popinFileName);
$objPHPExcelApplause = PHPExcel_IOFactory::load($applauseFileName);


$popinRowCount = $objPHPExcelPopin->getActiveSheet()->getHighestRow();
$applauseRowCount = $objPHPExcelApplause->getActiveSheet()->getHighestRow();


$report = array();
$rowIndex = 0;

$objPHPExcel = new PHPExcel();

$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A1', 'Date')
            ->setCellValue('B1', 'IMP')
            ->setCellValue('C1', 'CLICK')
            ->setCellValue('D1', 'CTR(%)')
            ->setCellValue('E1', '觀看25%')
            ->setCellValue('F1', '觀看50%')
            ->setCellValue('G1', '觀看75%')
            ->setCellValue('H1', '觀看100%')
            ->setCellValue('I1', 'Vew(5秒)')
            ->setCellValue('J1', '預算消耗(NT)');

// echo 'POPIN<br>';
/*
Popin 格式：
A:日期
B:impression
C:25%
D:50%
E:75%
F:100%(View)
G:click
H:CTR
I:金額
*/
$aryPopin = array();
$materialRate = $_POST['materialRate'];

for ($i=1; $i <= $popinRowCount; $i++) { 
	$date_cellIndex = 'A'.$i;
	$imp_cellIndex = 'B'.$i;
	$view_cellIndex = 'F'.$i;
	$click_cellIndex = 'G'.$i;
	$quarter_cellIndex = 'C'.$i;
	$half_cellIndex = 'D'.$i;
	$ThreeQuarters_cellIndex = 'E'.$i;
	$complete_cellIndex = 'F'.$i;
	$revenue_cellIndex = 'I'.$i;

	$date_cell = $objPHPExcelPopin->getActiveSheet()->getCell($date_cellIndex)->getValue();
	$imp_cell = $objPHPExcelPopin->getActiveSheet()->getCell($imp_cellIndex)->getValue();
	$view_cell = $objPHPExcelPopin->getActiveSheet()->getCell($view_cellIndex)->getValue();
	$click_cell = $objPHPExcelPopin->getActiveSheet()->getCell($click_cellIndex)->getValue();
	$quarter_cell = $objPHPExcelPopin->getActiveSheet()->getCell($quarter_cellIndex)->getValue();
	$half_cell = $objPHPExcelPopin->getActiveSheet()->getCell($half_cellIndex)->getValue();
	$ThreeQuarters_cell = $objPHPExcelPopin->getActiveSheet()->getCell($ThreeQuarters_cellIndex)->getValue();
	$complete_cell = $objPHPExcelPopin->getActiveSheet()->getCell($complete_cellIndex)->getValue();
	$revenue_cell = $objPHPExcelPopin->getActiveSheet()->getCell($revenue_cellIndex)->getValue();

	$vtrValue = 0;
	$vtrString = '0.00%';
	$popinFiveSecView = 0; //Popin 的 5 秒觀看數

	if ($date_cell > 0) {
		$InvDate = date("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($date_cell)); //將 int 轉換成 日期
		
		if ($imp_cell > 0) {

			if ($materialRate == 10) {
				//若素材為 10 秒，則要用 50% 的 view 數去算 VTR
				$vtrValue = round(($half_cell / $imp_cell), 4);
				$popinFiveSecView = $half_cell;
			} else if ($materialRate == 15) {
				//若素材為 15 秒，則要用 25% ~ 50% 的 view 數去算 VTR
				$tmpPopinFiveSecView = rand(($quarter_cell*0.75),($half_cell*1.25));
				$popinFiveSecView = $tmpPopinFiveSecView;
				$vtrValue = round(($popinFiveSecView / $imp_cell), 4);
			} else if ($materialRate == 20) {
				//若素材為 20 秒，則要用 25% 的 view 數去算 VTR
				$vtrValue = round(($quarter_cell / $imp_cell), 4);
				$popinFiveSecView = $quarter_cell;
			} else {
				//若素材為其他秒數，則從 30% 正負 2% 小數點後兩位隨機算出 VTR
				for ($intPopinIndex=0; $intPopinIndex < 3; $intPopinIndex++) { 
					$vtrValue = round(random_float(0.2800,0.3200), 4);
					$popinFiveSecView = intval($imp_cell * $vtrValue);

					if ($popinFiveSecView > $quarter_cell) break;
				}				
			}
			
			$vtrString = ($vtrValue * 100).'%';
			// $popinFiveSecView = intval($imp_cell * $vtrValue);
		}
		
		echo $date_cellIndex.'__'.$InvDate.'__imp='.$imp_cell.'__click='.$click_cell.'__VTR='.$vtrString.'__25%='.$quarter_cell.'__50%='.$half_cell.'__75%='.$ThreeQuarters_cell.'__100%='.$complete_cell.'__5sec Views='.$popinFiveSecView.'__revenue='.$revenue_cell.'<br>';				

		$aryPopin[] = array($InvDate, $imp_cell, $click_cell, $vtrString, $quarter_cell, $half_cell, $ThreeQuarters_cell, $complete_cell, $popinFiveSecView, $revenue_cell);
	}

	// echo $cell.'<br>';
}

// print_r($aryPopin);

// echo '<br><br>Applause<br>';
/*
Applause 格式：
A:日期
D:impression
F:Click
G:Times(View的次數，格式為：0~25%|26~50%|51~75%|76~99%|100%)
J:Revenue
*/
$aryApplause = array();

for ($j=1; $j <= $applauseRowCount; $j++) { 
	$applauseDateCellIndex = 'A'.$j;
	$applauseImpCellIndex = 'D'.$j;
	$applauseTimesCellIndex = 'G'.$j;
	$applauseClickCellIndex = 'F'.$j;
	$applauseRevenueCellIndex = 'J'.$j;

	$applauseDateCell = $objPHPExcelApplause->getActiveSheet()->getCell($applauseDateCellIndex)->getValue();
	$applauseImpCell = $objPHPExcelApplause->getActiveSheet()->getCell($applauseImpCellIndex)->getValue();
	$applauseTimesCell = $objPHPExcelApplause->getActiveSheet()->getCell($applauseTimesCellIndex)->getValue();
	$applauseClickCell = $objPHPExcelApplause->getActiveSheet()->getCell($applauseClickCellIndex)->getValue();
	$applauseRevenueCell = $objPHPExcelApplause->getActiveSheet()->getCell($applauseRevenueCellIndex)->getValue();
	
	$quarterViews = 0;
	$halfViews = 0;
	$ThreeQuartersViews = 0;
	$completeViews = 0;
	$applauseTimesValues = array();

	$applauseVtrValue = 0;
	$applauseVtrString = '0.00%';
	$applauseFiveSecView = 0; //Applause 的 5 秒觀看數

	if (intval($applauseDateCell) > 0) {
		$applauseInvDate = date('Y-m-d', strtotime($applauseDateCell)); //將 int 轉換成 日期

		$applauseImpValue = substr($applauseImpCell,0,(stripos($applauseImpCell, '(')));		

		$applauseTimesValues = explode("|",$applauseTimesCell);

		for ($k=0; $k < count($applauseTimesValues); $k++) { 
			if ($k > 0) {
				$quarterViews = $quarterViews + $applauseTimesValues[$k];
			}

			if ($k > 1) {
				$halfViews = $halfViews + $applauseTimesValues[$k];
			}

			if ($k > 2) {
				$ThreeQuartersViews = $ThreeQuartersViews + $applauseTimesValues[$k];
			}

			if ($k > 3) {
				$completeViews = $completeViews + $applauseTimesValues[$k];
			}
		}

		if ($materialRate == 10) {
			//若素材為 10 秒，則要用 50% 以上的 view 總和數去算 VTR
			$applauseVtrValue = round(($halfViews / $applauseImpValue), 4);
			$applauseFiveSecView = $halfViews;
		} else if ($materialRate == 15) {
			//若素材為 15 秒，則 VTR = 40% 正負 2% 小數點後兩位隨機算出 VTR，然後 view 數要介於 25% ~ 50% 的 view 數 
			$applauseVtrValue = round(random_float(0.3800,0.4200), 4);
			$applauseFiveSecView = intval($applauseImpValue * $applauseVtrValue);

			if (($applauseFiveSecView < $halfViews) || ($applauseFiveSecView > $quarterViews)) {
				echo '5秒 view('.$applauseFiveSecView.') 沒有介於 25%'.$quarterViews.' ~ 50%'.$halfViews.' 的 view 數之間';
				die();
			}
		} else if ($materialRate == 20) {
			//若素材為 20 秒，則要用 25% 以上的 view 總和數去算 VTR
			$applauseVtrValue = round(($quarterViews / $applauseImpValue), 4);
			$applauseFiveSecView = $quarterViews;
		} else if ($materialRate == 25) {
			//若素材為 25 秒，則 VTR = 50% 正負 2% 小數點後兩位隨機算出 VTR，然後 view 數要大於 25% 的 view 數 
			$applauseVtrValue = round(random_float(0.4800,0.5200), 4);
			$applauseFiveSecView = intval($applauseImpValue * $applauseVtrValue);

			if ($applauseFiveSecView < $quarterViews) {
				echo '5秒 view('.$applauseFiveSecView.') 沒有大於 25%'.$quarterViews.' 的 view 數';
				die();
			}
		} else {
			//若素材為其他秒數，則從 55% 正負 2% 小數點後兩位隨機算出 VTR
			$applauseVtrValue = round(random_float(0.5300,0.5700), 4);
			$applauseFiveSecView = intval($applauseImpValue * $applauseVtrValue);
		}

		$applauseVtrString = ($applauseVtrValue * 100).'%';

		echo $applauseDateCellIndex.'__'.$applauseInvDate.'__imp='.$applauseImpValue.'__click='.$applauseClickCell.'__VTR='.$applauseVtrString.'__25%='.$quarterViews.'__50%='.$halfViews.'__75%='.$ThreeQuartersViews.'__100%='.$completeViews.'__5sec Views='.$applauseFiveSecView.'__revenue='.$applauseRevenueCell.'<br>';

		$aryApplause[] = array($applauseInvDate, $applauseImpValue, $applauseClickCell, $applauseVtrString, $quarterViews, $halfViews, $ThreeQuartersViews, $completeViews, $applauseFiveSecView, $applauseRevenueCell);
	}
}

// die();
array_multisort($aryApplause,SORT_ASC);
// print_r($aryApplause);

// echo $objPHPExcelPopin->getActiveSheet()->getCell('A1', false);
// echo $objPHPExcelApplause->getActiveSheet()->getCell('A1', false);

$exportCount = 0;

if ($popinRowCount >= $applauseRowCount) {
	$exportCount = $popinRowCount;
} else {
	$exportCount = $applauseRowCount;
}

$workingIndex = 0;
$tmpIndex = 10;
for ($exportIndex=0; $exportIndex < $exportCount; $exportIndex++) { 
	if (isset($aryPopin[$exportIndex][0]) && isset($aryApplause[$exportIndex][0])) {
		$workingIndex = $exportIndex + 2;
		$tmpIndex = $exportIndex + 12;

		if (($aryPopin[$exportIndex][1] + $aryApplause[$exportIndex][1]) > 0) {
			$ctr = round( ( ( ($aryPopin[$exportIndex][2] + $aryApplause[$exportIndex][2]) / ($aryPopin[$exportIndex][1] + $aryApplause[$exportIndex][1]) ) * 100), 2).'%';	
		} else {
			$ctr = '0.00%';
		}

		$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$workingIndex, $aryApplause[$exportIndex][0])
            ->setCellValue('B'.$workingIndex, $aryPopin[$exportIndex][1] + $aryApplause[$exportIndex][1]) 
            ->setCellValue('C'.$workingIndex, $aryPopin[$exportIndex][2] + $aryApplause[$exportIndex][2])
            ->setCellValue('D'.$workingIndex, $ctr)
            ->setCellValue('E'.$workingIndex, $aryPopin[$exportIndex][4] + $aryApplause[$exportIndex][4])
            ->setCellValue('F'.$workingIndex, $aryPopin[$exportIndex][5] + $aryApplause[$exportIndex][5])
            ->setCellValue('G'.$workingIndex, $aryPopin[$exportIndex][6] + $aryApplause[$exportIndex][6])
            ->setCellValue('H'.$workingIndex, $aryPopin[$exportIndex][7] + $aryApplause[$exportIndex][7])
            ->setCellValue('I'.$workingIndex, $aryPopin[$exportIndex][8] + $aryApplause[$exportIndex][8])
            ->setCellValue('J'.$workingIndex, $aryPopin[$exportIndex][9] + $aryApplause[$exportIndex][9]);

        $objPHPExcel->setActiveSheetIndex(0)
        	->setCellValue('A'.$tmpIndex, $aryPopin[$exportIndex][0])
            ->setCellValue('B'.$tmpIndex, $aryPopin[$exportIndex][1].'__'.$aryApplause[$exportIndex][1]) 
            ->setCellValue('C'.$tmpIndex, $aryPopin[$exportIndex][2].'__'.$aryApplause[$exportIndex][2])
            ->setCellValue('D'.$tmpIndex, $aryPopin[$exportIndex][3].'__'.$aryApplause[$exportIndex][3])
            ->setCellValue('E'.$tmpIndex, $aryPopin[$exportIndex][4].'__'.$aryApplause[$exportIndex][4])
            ->setCellValue('F'.$tmpIndex, $aryPopin[$exportIndex][5].'__'.$aryApplause[$exportIndex][5])
            ->setCellValue('G'.$tmpIndex, $aryPopin[$exportIndex][6].'__'.$aryApplause[$exportIndex][6])
            ->setCellValue('H'.$tmpIndex, $aryPopin[$exportIndex][7].'__'.$aryApplause[$exportIndex][7])
            ->setCellValue('I'.$tmpIndex, $aryPopin[$exportIndex][8].'__'.$aryApplause[$exportIndex][8])
            ->setCellValue('J'.$tmpIndex, $aryPopin[$exportIndex][9].'__'.$aryApplause[$exportIndex][9]);
	}
}

function random_float ($min,$max) {
   return ($min+lcg_value()*(abs($max-$min)));
}

// header('Content-Disposition: attachment;filename="report_'.date("Y-m-d",time()).'.xlsx"');
// header('Cache-Control: max-age=0');
// // If you're serving to IE 9, then the following may be needed
// header('Cache-Control: max-age=1');
// // If you're serving to IE over SSL, then the following may be needed
// header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
// header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
// header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
// header ('Pragma: public'); // HTTP/1.0
// $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
// $objWriter->save('php://output');
// exit;
