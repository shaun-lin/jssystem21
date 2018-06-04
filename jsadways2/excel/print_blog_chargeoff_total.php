<?php
ob_start();
ini_set( "memory_limit", "256M");
include('../include/db.inc.php');
error_reporting(E_ALL);
ini_set('display_errors', false);
ini_set('display_startup_errors', false);

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
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(11);
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(25);
$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(15);



$number=1;
$array = array(0 => "B", 1 => "C", 2 => "D", 3 => "E", 4 => "F", 5 => "G", 6 => "H", 7 => "I", 8 => "J", 9 => "K", 10 => "L", 11 => "M", 12 => "N", 13 => "O", 14 => "P", 15 => "Q", 16 => "R", 17 => "S", 18 => "T", 19 => "U", 20 => "V", 21 => "W", 22 => "X", 23 => "Y", 24 => "Z", 25 => "AA", 26 => "AB", 27 => "AC", 28 => "AD", 29 => "AE", 30 => "AF", 31 => "AG", 32 => "AH", 33 => "AI", 34 => "AJ", 35 => "AK");
for($j = 0; $j < count($array); $j++){
	for($i=$number;$i<=$number;$i++){
		$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$objPHPExcel->getActiveSheet()->getStyle($array[$j].$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	}
}
$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$number, '委刊號碼')
			->setCellValue('B'.$number, '代理商')
			->setCellValue('C'.$number, '客戶')
			->setCellValue('D'.$number, '走期')
			->setCellValue('E'.$number, '發票總額')
			->setCellValue('F'.$number, '發票月份')
			->setCellValue('G'.$number, '媒體別')
			->setCellValue('H'.$number, '媒體報價')
			->setCellValue('I'.$number, '寄送費用')
			->setCellValue('J'.$number, '寫手總報價')
			->setCellValue('K'.$number, '總稿酬')
			->setCellValue('L'.$number, '含稅價')
			->setCellValue('M'.$number, '寫手編號')
			->setCellValue('N'.$number, '寫手姓名')
			->setCellValue('O'.$number, '匯款金額')
			->setCellValue('P'.$number, '匯款日期')
			->setCellValue('Q'.$number, '銀行戶名')
			->setCellValue('R'.$number, '銀行')
			->setCellValue('S'.$number, '存款帳號')
			->setCellValue('T'.$number, '部落格名字')
			->setCellValue('U'.$number, '扣費身分查詢')
			->setCellValue('V'.$number, '免扣身分')
			->setCellValue('W'.$number, '扣繳金額')
			->setCellValue('X'.$number, '二代健保金額');


//委刊編號
if($_GET['campaign_serial'] != ''){
	$bytime = false;
	$sql = 'SELECT SUM(BC.price)as totalprice  , sum(MD.price2)as MDtotalprice ,
	BC.*,
			CA.name as campaignName,
			CA.idnumber as campaignIdnumber,
			CA.agency ,
			CA.client ,
			CA.date1 as campaignStart ,
			CA.date2 as campaignEnd ,
			BL.ac_id ,
			BL.truename as BLname ,
			BL.name2 as BLname2 ,
			BL.name3 as BLname3 ,
			BL.name4 as BLname4 ,
			BL.bank3 as BLbankName1 ,
			BL.bank1 as BLbank1 ,
			BL.bank2 as BLbankNum1 ,
			BL.idnumber as BLIdnumber1,
			BL.idnumber2 as BLIdnumber2,
			BL.bank6 as BLbankName2 ,
			BL.bank4 as BLbank2 ,
			BL.bank5 as BLbankNum2 ,
			MD.price2 as MDprice,
			BL.health as BLhealth,
			BB.*

  FROM blogger_chargeoff  BC LEFT JOIN campaign CA ON BC.`campaign_id` = CA.id LEFT JOIN blogger BL ON BC.`blogger_id` = BL.id 
  LEFT JOIN media19_detail MD ON BC.`blogger_detail_id` = MD.id
  LEFT JOIN blogger_bank BB ON BB.id = BC.bankId

	WHERE BC.campaign_id = ( SELECT id FROM campaign WHERE idnumber = '.$_GET['campaign_serial'].') group by BB. bankAC';
		

}else {

	if($_GET['start_time'] != '' && $_GET['end_time'] != ''){
		$bytime = true;
		//時間區間
		$start_time = date("Y-m",strtotime($_GET['start_time'])).'-01';
		$end_time = date("Y-m",strtotime(date("Y-m",strtotime($_GET['end_time'])).'-01'.' + 1 months')).'-01';
		$blog_id = '';
		if($_GET['blogger_id'] != ''){
			$blog_id = ' AND BC.blogger_id = '.$_GET['blogger_id'];
		}


		$sql= "SELECT SUM(BC.price)as totalprice  , sum(MD.price2)as MDtotalprice ,
		BC.*,
		CA.name as campaignName, 
		CA.idnumber as campaignIdnumber, 
		CA.agency ,
		CA.client ,
		CA.date1 as campaignStart ,
		CA.date2 as campaignEnd ,
		BL.ac_id ,
		BL.truename as BLname ,
		BL.name2 as BLname2 ,
		BL.name3 as BLname3 ,
		BL.name4 as BLname4 ,
		BL.bank3 as BLbankName1 ,
		BL.bank1 as BLbank1 ,
		BL.bank2 as BLbankNum1 ,
		BL.idnumber as BLIdnumber1,
		BL.idnumber2 as BLIdnumber2,
		BL.bank6 as BLbankName2 ,
		BL.bank4 as BLbank2 ,
		BL.bank5 as BLbankNum2 ,
		MD.price2 as MDprice ,
		BL.health as BLhealth,
		BB.*

		FROM blogger_chargeoff BC 
		LEFT JOIN campaign CA ON BC.`campaign_id` = CA.id 
		LEFT JOIN blogger BL ON BC.`blogger_id` = BL.id 
		LEFT JOIN media19_detail MD ON BC.`blogger_detail_id` = MD.id 
		LEFT JOIN blogger_bank BB ON BB.id = BC.bankId

		WHERE (BC.chargeoff_date BETWEEN '$start_time' AND '$end_time') $blog_id group by BB. bankAC";

	}else{
		echo '請輸入搜尋條件';
		exit();
	}
}


$result=mysql_query($sql); 
if (mysql_num_rows($result)>0){
	while($row=mysql_fetch_array($result)){
		$number=$number+1;
		
		// echo $BLIdnumber;
		// exit();
		//部落格名稱 部落格→粉絲團→youtube
		$blogName = '';
		if($row['BLname2']!=''){
			$blogName = $row['BLname2'];
		}else if($row['BLname3']!=''){
			$blogName =  $row['BLname3'];
		}else if($row['BLname4']!=''){
			$blogName =  $row['BLname4'];
		}
		//走期
		$campaignSE =  date("m",strtotime($row["campaignStart"])).'-'.date("m",strtotime($row["campaignEnd"])).'月';

		//付款月份
		$chargoffdate = date("Y-m",strtotime($row["chargeoff_date"]));

		//匯款日期
		$chargoffdate_ful = date("Y-m",strtotime($row["chargeoff_date"].' + 1 months')).'-01';
		$chargoffdate_ful = date("Y-m-d",strtotime($chargoffdate_ful.' -1 days'));


		//如果撈取到 非當月出帳的資料跳過此迴圈
		$offMon = date("Ym",strtotime($chargoffdate_ful));
		if($bytime){
			if((date("Ym",strtotime($start_time)) >= $offMon) && (date("Ym",strtotime($_GET['end_time'])) <= $offMon)){
				
			}else{
				$number--;
				continue;
			}
		}


		// $BankName = explode("", $row["BLbankName1"]);
		// echo $row["BLbankName1"].'=>'.mb_strlen( $row["BLbankName1"], "utf-8");
		// echo '<br>';

		$taxes = 0;
		$health_price = 0;
		$invoicePrice = 0;

		//判定是否為空的  空的用之前的欄位
		if($row["bankUserName"] == null){

			//判斷戶名是否超過3個字 判為 公司行號 開發票
			if(mb_strlen( $row["BLbankName1"], "utf-8") > 3){
				//公司 含稅價
				$invoicePrice = round($row["totalprice"] * 1.05);
				$transfer = $invoicePrice;
			}else{
				//不含稅金額
				$invoicePrice = $row["totalprice"];
				//個人戶
				//計算是否要二代健保
				$health_price = 0;
				if($row["BLhealth"] ==1) {
					$health_price = round($row["totalprice"]*0.0191);
				}

				$taxes = 0;
				//計算是否超過兩萬
				if($row["totalprice"] > 20000){
					$taxes = round($row["totalprice"]*0.1);
				}

				$transfer = $row["totalprice"] - $taxes - $health_price;
			}
			

			$bankAC = $row['BLbankNum1'];
			$bankName = $row['BLbank1'];
			$bankUserName = $row['BLbankName1'];
			//身分證字號
			// if(checkTwID($row["BLIdnumber1"])){
				$BLIdnumber = $row["BLIdnumber1"];
			// }else{
				// $BLIdnumber = '';
			// }

		}else{
			//新銀行資訊
			//判斷戶名是否超過3個字 判為 公司行號 開發票
			if(mb_strlen( $row["bankUserName"], "utf-8") > 3 || $row["invoice"] == 1){
				//公司 含稅價
				$invoicePrice = round($row["totalprice"] * 1.05);
				$transfer = $invoicePrice;
			}else{
				//不含稅金額
				$invoicePrice = $row["totalprice"];
				//個人戶
				//計算是否要二代健保
				$health_price = 0;
				if($row["health"] ==1) {
					$health_price = round($row["totalprice"]*0.0191);
				}

				$taxes = 0;
				//計算是否超過兩萬
				if($row["totalprice"] > 20000){
					$taxes = round($row["totalprice"]*0.1);
				}

				$transfer = $row["totalprice"] - $taxes - $health_price;
			}

			$bankAC = $row['bankAC'];
			$bankName = $row['bankName'];
			$bankUserName = $row["bankUserName"];
			//身分證字號
			// if(checkTwID($row["bankIdNum"])){
				$BLIdnumber = $row["bankIdNum"];
			// }else{
				// $BLIdnumber = '';
			// }

		}


		$campaignIdnumber = $row["campaignIdnumber"];
		$agency = $row['agency'];
		$client = $row['client'];
		$campaignSE = $campaignSE;
		//判斷是否有加總過 有的話都放空
		if($row['totalprice'] != $row['price']){
			$campaignIdnumber .= ','+$row["campaignIdnumber"];
			$agency = '';
			$client = '';
			$campaignSE = '';
		}


		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValueExplicit('A'.$number, (string)$campaignIdnumber,PHPExcel_Cell_DataType::TYPE_STRING)
			// ->setCellValue('A'.$number, $campaignIdnumber)
			->setCellValue('B'.$number, $agency)
			->setCellValue('C'.$number, $client)
			->setCellValue('D'.$number, $campaignSE)
			->setCellValue('E'.$number, '')
			->setCellValue('F'.$number, '')
			->setCellValue('G'.$number, 'JS-寫手口碑操作')
			->setCellValue('H'.$number, '')
			->setCellValue('I'.$number, '')
			->setCellValue('J'.$number, $row['MDtotalprice'])
			->setCellValue('K'.$number, $row['totalprice'])
			->setCellValue('L'.$number, $invoicePrice)
			->setCellValue('M'.$number, $row["ac_id"])
			->setCellValue('N'.$number, htmlspecialchars_decode($row["BLname"]))
			->setCellValue('O'.$number, $transfer)
			->setCellValue('P'.$number, $chargoffdate_ful)
			->setCellValue('Q'.$number, $bankUserName)
			->setCellValue('R'.$number, $bankName)
			// ->setCellValue('S'.$number)->setValueExplicit($row['BLbankNum1'], PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValueExplicit('S'.$number, (string)$bankAC,PHPExcel_Cell_DataType::TYPE_STRING)
			->setCellValue('T'.$number, htmlspecialchars_decode($blogName))
			->setCellValue('U'.$number, $BLIdnumber)
			->setCellValue('V'.$number, '')
			->setCellValue('W'.$number, $taxes)
			->setCellValue('X'.$number, $health_price);
			
			$objPHPExcel->getActiveSheet()->getStyle('B'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('C'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('D'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('E'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('F'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('G'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('H'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('I'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('J'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('M'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('Q'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('R'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('S'.$number)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
	}	
}


$fileName = date("Y-m",strtotime($_GET['start_time'])).' - '.date("Y-m",strtotime($_GET['end_time']));

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
$xlsFilename = $fileName.'寫手匯款加總表.xls';
header("Content-disposition: attachment; filename=\"".addslashes($xlsFilename)."\";");
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;


function checkTwID($id){
	$id = strtoupper($id);
	//建立字母分數陣列
	$headPoint = array(
		'A'=>1,'I'=>39,'O'=>48,'B'=>10,'C'=>19,'D'=>28,
		'E'=>37,'F'=>46,'G'=>55,'H'=>64,'J'=>73,'K'=>82,
		'L'=>2,'M'=>11,'N'=>20,'P'=>29,'Q'=>38,'R'=>47,
		'S'=>56,'T'=>65,'U'=>74,'V'=>83,'W'=>21,'X'=>3,
		'Y'=>12,'Z'=>30
	);
	//建立加權基數陣列
	$multiply = array(8,7,6,5,4,3,2,1);
	//檢查身份字格式是否正確
	if (ereg("^[a-zA-Z][1-2][0-9]+$",$id) && strlen($id) == 10){
		//切開字串
		$stringArray = str_split($id);
		//取得字母分數(取頭)
		$total = $headPoint[array_shift($stringArray)];
		//取得比對碼(取尾)
		$point = array_pop($stringArray);
		//取得數字部分分數
		$len = count($stringArray);
		for($j=0; $j<$len; $j++){
			$total += $stringArray[$j]*$multiply[$j];
		}
		//計算餘數碼並比對
		$last = (($total%10) == 0 )? 0: (10 - ( $total % 10 ));
		if ($last != $point) {
			return false;
		} else {
			return true;
		}
	}  else {
	   return false;
	}
}

?>