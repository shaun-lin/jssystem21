<?php
ob_start();
ini_set( "memory_limit", "256M");
include('../include/db.inc.php');
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');
if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');
/** Include PHPExcel */
require_once 'Classes/PHPExcel.php';


$media_list = isset($_POST['media_list']) ? $_POST['media_list'] : NULL;
//var_dump($media_list);
$select_tag = isset($_POST["select_tag"]) ? $_POST["select_tag"] : NULL;

$ALL = isset($_POST["all"]) ? $_POST["all"] : NULL;

$start_time = $_POST['start_time'];
$end_time = $_POST['end_time'];
$str_date = '';
if($start_time != '' && $end_time != ''){
	$str_date = " AND date1 >= '".$start_time."' AND date2 < '".$end_time."' ";
}


//var_dump($select_tag);
$ACname = $_POST['ACname'];
$ACname = explode(",", $ACname);
//TAG ary
$str_tag = '';
if($select_tag != NULL){
	$str_tag = 'AND ';
	for($i=0;$i<count($select_tag);$i++){
		$str_tag += "tagtext LIKE '%".$select_tag[$i]."%'";
		if(isset($select_tag[$i+1])){
			$str_tag += " or ";
		}
	}
}
//media ary
$str_media = '';
$media_id = '';
$media_name = '';
if($media_list != NULL){
	$str_media = ' AND ';
	for($i=0;$i<count($media_list);$i++){
		$media_str = explode(",", $media_list[$i]);
		$media_id[] = $media_str[0];
		$media_name[] = $media_str[1];
	}
}



$number = 1;
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

// Set document properties
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");
//設定預設樣式
$objPHPExcel->getDefaultStyle()->getFont()->setName('新細明體');
$objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
$objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(18);//設定欄位寬度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(26);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(26);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(26);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(34);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(11);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(11);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(11);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(11);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(11);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(11);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(11);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(11);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(11);
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(11);



$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A'.$number, '代理商')
			->setCellValue('B'.$number, '廣告主')
			->setCellValue('C'.$number, 'CAMPAIGN')
			->setCellValue('D'.$number, '負責業務')
			->setCellValue('E'.$number, '媒體')
			->setCellValue('F'.$number, 'cue表類型')
			->setCellValue('G'.$number, '期間')
			->setCellValue('H'.$number, '期間')
			->setCellValue('I'.$number, '外匯調整')
			->setCellValue('J'.$number, '總價')
			->setCellValue('K'.$number, '收入1')
			->setCellValue('L'.$number, '收入2')
			->setCellValue('M'.$number, '收入3')
			->setCellValue('N'.$number, '成本1')
			->setCellValue('O'.$number, '成本2')
			->setCellValue('P'.$number, '成本3')
			->setCellValue('Q'.$number, '毛利');



$objPHPExcel->getActiveSheet()->getComment('E11')->setAuthor('PHPExcel');//どこに影響しているのかわかりません
//次がコメントのタイトルになります
$objCommentRichText = $objPHPExcel->getActiveSheet()->getComment('E11')->getText()->createTextRun('PHPExcel:');
$objCommentRichText->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getComment('E11')->getText()->createTextRun("\r\n");
//次が本文になりますが日本語にするときはUTF8にしてください
$objPHPExcel->getActiveSheet()->getComment('E11')->getText()->createTextRun('Total amount on the current invoice, excluding VAT.');

$sql = "SELECT TB.name AS TBName, CA.* FROM campaign CA LEFT JOIN $ACname[0] TB ON TB.id = CA.$ACname[0]_id WHERE TB.id =$ACname[1] AND CA.status in (3,4,5) ".$str_date.$str_tag;
//echo $sql; //SELECT TB.name AS AGName, CA. * FROM campaign CA LEFT JOIN agency TB ON TB.id = CA.agency_id WHERE TB.id =3
//var_dump($media_id);
//var_dump($media_name);
//exit();

$result=mysql_query($sql); 
if (mysql_num_rows($result)>0){
	while($row=mysql_fetch_array($result)){
		for($i=0;$i<count($media_id);$i++){

			$sql2 = "SELECT `campaign_id`,`cue`,`totalprice`,`text1` as income1,`text5` as income2,`text9` as income3,`text2` as cost1,`text6` as cost2,`text10` as cost3  FROM media".$media_id[$i]." where campaign_id = ".$row['id']." AND totalprice <> 0";
			//echo $sql2;
			//exit();
			
			$result2=mysql_query($sql2); 
			if (mysql_num_rows($result2)>0){
				while($row2=mysql_fetch_array($result2)){
					/*
					echo "代理商：".$row["TBName"];
					echo "廣告主：".$row["client"];
					echo "CAMPAIGN：".$row["name"];
					echo "AE：".$row["member"];
					echo "外匯調整：".$row["exchang_math"];
					echo "媒體：".$media_name[$i];

					if($row2["cue"] == '1'){
						echo "對外cue";	
					}else{
						echo "對內cue";	
					}
					echo "總價：".$row2["totalprice"];
					echo "收入1：".$row2["income1"];
					echo "收入2：".$row2["income2"];
					echo "收入3：".$row2["income3"];
					echo "成本1：".$row2["cost1"];
					echo "成本2：".$row2["cost2"];
					echo "成本3：".$row2["cost3"];
					echo "毛利：";
					echo ($row2["income1"]+$row2["income2"]+$row2["income3"])-($row2["cost1"]+$row2["cost2"]+$row2["cost3"]);
					echo "<br>";
					*/
					if($row2["cue"] == '1'){
						$str_cue = "對外cue";	
					}else{
						$str_cue = "對內cue";	
					}
					$price = ($row2["income1"]+$row2["income2"]+$row2["income3"])-($row2["cost1"]+$row2["cost2"]+$row2["cost3"]);
					$number++;

					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A'.$number, $row["TBName"])
					->setCellValue('B'.$number, $row["client"])
					->setCellValue('C'.$number, $row["name"])
					->setCellValue('D'.$number, $row["member"])
					->setCellValue('E'.$number, $media_name[$i])
					->setCellValue('F'.$number, $str_cue)
					->setCellValue('G'.$number, $start_time)
					->setCellValue('H'.$number, $end_time)
					->setCellValue('I'.$number, $row["exchang_math"])
					->setCellValue('J'.$number, $row2["totalprice"])
					->setCellValue('K'.$number, $row2["income1"])
					->setCellValue('L'.$number, $row2["income2"])
					->setCellValue('M'.$number, $row2["income3"])
					->setCellValue('N'.$number, $row2["cost1"])
					->setCellValue('O'.$number, $row2["cost2"])
					->setCellValue('P'.$number, $row2["cost3"])
					->setCellValue('Q'.$number, $price);
					/*
					$objPHPExcel->getActiveSheet()->getComment('E11')->setAuthor('PHPExcel');
$objCommentRichText = $objPHPExcel->getActiveSheet()->getComment('E11')->getText()->createTextRun('PHPExcel:');
$objCommentRichText->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getComment('E11')->getText()->createTextRun("\r\n");
$objPHPExcel->getActiveSheet()->getComment('E11')->getText()->createTextRun('Total amount on the current invoice, excluding VAT.');

$objPHPExcel->getActiveSheet()->getComment('E12')->setAuthor('PHPExcel');
$objCommentRichText = $objPHPExcel->getActiveSheet()->getComment('E12')->getText()->createTextRun('PHPExcel:');
$objCommentRichText->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getComment('E12')->getText()->createTextRun("\r\n");
$objPHPExcel->getActiveSheet()->getComment('E12')->getText()->createTextRun('Total amount of VAT on the current invoice.');

$objPHPExcel->getActiveSheet()->getComment('E13')->setAuthor('PHPExcel');
$objCommentRichText = $objPHPExcel->getActiveSheet()->getComment('E13')->getText()->createTextRun('PHPExcel:');
$objCommentRichText->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getComment('E13')->getText()->createTextRun("\r\n");
$objPHPExcel->getActiveSheet()->getComment('E13')->getText()->createTextRun('Total amount on the current invoice, including VAT.');
$objPHPExcel->getActiveSheet()->getComment('E13')->setWidth('100pt');
$objPHPExcel->getActiveSheet()->getComment('E13')->setHeight('100pt');
$objPHPExcel->getActiveSheet()->getComment('E13')->setMarginLeft('150pt');
$objPHPExcel->getActiveSheet()->getComment('E13')->getFillColor()->setRGB('EEEEEE');
*/
				}
					
			}
			
		}
		
	}
}


// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
$xlsFilename = $ACname[1].'媒體毛利報表_'.'.xls';
header("Content-disposition: attachment; filename=\"".addslashes($xlsFilename)."\";");
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
//var_dump($media_id);
//var_dump($media_name);
exit();


?>