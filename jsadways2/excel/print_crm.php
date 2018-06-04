<?php
ob_start();
ini_set( "memory_limit", "256M");
include('../include/db.inc.php');
error_reporting(E_ALL);
ini_set('display_errors', false);
ini_set('display_startup_errors', false);
date_default_timezone_set('Europe/London');
if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');
/** Include PHPExcel */
require_once 'Classes/PHPExcel.php';
require_once('Classes/PHPExcel/Writer/Excel2007.php');



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

//媒體調整用
$str_start = explode("/",$_POST['start_time']);
$Date_start = $str_start[2]."-".$str_start[0]."-01";
//媒體調整用
$str_end = explode("/",$_POST['end_time']);
$Date_end = date("Y-m-d",strtotime($str_end[2]."-".$str_end[0]."-01"." +1 months"));

//var_dump($select_tag);
$ACname = $_POST['ACname'];
$ACname = explode(",", $ACname);
//TB.id =$ACname[1] 為ID
//$ACname[0] 為table name

$str_id = '';
if($ACname[1] != 0){
	$str_id = 'AND TB.id = '.$ACname[1]." ";
}


if($ACname[1] == 0){
	//全部代理商
	if($ACname[0] == 'agency'){
		$str_id = "AND agency  <> '' ";
	}

	//全部直客
	if($ACname[0] == 'client'){
		$str_id = "AND agency  = '' ";
	}
}

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

//cue 表選擇
$cue = '';
switch ($_POST['cue']) {
	case '1':
		$cue = ' and cue in (1) ';
		break;
	case '2':
		$cue = ' and cue in (2) ';
		break;
	
	case '3':
		$cue = ' and cue in (1,2) ';
		break;
}

//加總表格
$detail = $_POST['detail'];

//毛利率
$profit = $_POST['profit'];

$number = 1;



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
if($detail == 0){
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
	$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(11);
	$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(11);



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
				->setCellValue('N'.$number, '補填收入')
				->setCellValue('O'.$number, '成本1')
				->setCellValue('P'.$number, '成本2')
				->setCellValue('Q'.$number, '成本3')
				->setCellValue('R'.$number, '補填成本')
				->setCellValue('S'.$number, '毛利')
				->setCellValue('U'.$number, '分類');

	if($profit){
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('T'.$number, '毛利率');
	}


	//  *****************    插入註解  Start   *************************
	//$objPHPExcel->getActiveSheet()->getComment('E11')->setAuthor('PHPExcel');//どこに影響しているのかわかりません
	//次がコメントのタイトルになります
	//$objCommentRichText = $objPHPExcel->getActiveSheet()->getComment('E11')->getText()->createTextRun('PHPExcel:');
	//$objCommentRichText->getFont()->setBold(true);
	//$objPHPExcel->getActiveSheet()->getComment('E11')->getText()->createTextRun("\r\n");
	//次が本文になりますが日本語にするときはUTF8にしてください
	//$objPHPExcel->getActiveSheet()->getComment('E11')->getText()->createTextRun('Total amount on the current invoice, excluding VAT.');
	//  *****************    插入註解  End   *************************

	$sql = "SELECT TB.name AS TBName, CA.* FROM campaign CA LEFT JOIN $ACname[0] TB ON TB.id = CA.$ACname[0]_id WHERE CA.status in (3,4,5) ".$str_id.$str_date.$str_tag." ORDER BY  `CA`.`agency_id`,`CA`.`client_id` ASC";
	//echo $sql; //SELECT TB.name AS AGName, CA. * FROM campaign CA LEFT JOIN agency TB ON TB.id = CA.agency_id WHERE TB.id =3
	//var_dump($media_id);
	//var_dump($media_name);
	//exit();
	if($ACname[0] == 'all'){
		$sql = "SELECT TB.name AS TBName, CA.* FROM campaign CA LEFT JOIN client CL ON CL.id = CA.client_id LEFT JOIN agency TB ON TB.id = CA.agency_id WHERE CA.status in (3,4,5) ".$str_id.$str_date.$str_tag."  ORDER BY  `CA`.`agency_id`,`CA`.`client_id` ASC";
	}

	$sum_totalprice = 0; //總發稿加總
	$sum_price = 0; //總毛利加總

	$result=mysql_query($sql); 
	if (mysql_num_rows($result)>0){
		while($row=mysql_fetch_array($result)){
			for($i=0;$i<count($media_id);$i++){
				$ver = $row["version"];	//後台版本  1.0 = 0  2.0 =2
				$sql2 = "SELECT `campaign_id`,`cue`,`totalprice`,`text1` as income1,`text5` as income2,`text9` as income3,`text2` as cost1,`text6` as cost2,`text10` as cost3  FROM media".$media_id[$i]." where campaign_id = ".$row['id']." AND totalprice <> 0 ".$cue.' order by cue DESC';
				//echo $sql2;
				//exit();
				$ok_campaign = 0;
				$result2=mysql_query($sql2); 
				if (mysql_num_rows($result2)>0){
					while($row2=mysql_fetch_array($result2)){
						$income_bc = 0;	//補填收入
						$cost_bc = 0;	//補填成本
						$chang_str = "補填";

						$sql_mediaChang = "SELECT * FROM media_change WHERE media_id = ".$media_id[$i]." AND campaign_id = ".$row2["campaign_id"]." AND change_date	 >= '$Date_start' AND change_date <'$Date_end'";
						//echo $sql_mediaChang;
						//exit();
						$result_mediaChang=mysql_query($sql_mediaChang); 
						if (mysql_num_rows($result_mediaChang)>0){
							$row_mediaChang=mysql_fetch_array($result_mediaChang);
							$income_bc = $row_mediaChang["change_income"];
							$cost_bc = $row_mediaChang["change_cost"];	
						}

						if($media_id[$i] == 20 || $ver == 0){	//價差
							$income_bc = $row2["totalprice"];
							$chang_str = "價差";
						}

						$price = ($row2["income1"]+$row2["income2"]+$row2["income3"]+$income_bc)-($row2["cost1"]+$row2["cost2"]+$row2["cost3"]+$cost_bc);
						
						if($row2["cue"] == '2'){
							// 20150109
							if($row["version"] != 0){
								if($price == 0)
									continue;
							}
							$ok_campaign = 1;
							// 20150109
							$str_cue = "對內cue";
						}else{
							$str_cue = "對外cue";
							$income_bc = 0;
							$cost_bc = 0;
							$price = 0;
							// 20150109
							if($row["version"] != 0){
								if($ok_campaign == 0 && $_POST['cue'] != 1)
									continue;
							}
							// 20150109
						}
						
						
						
						$number++;

						$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A'.$number, $row["TBName"])		//代理商
						->setCellValue('B'.$number, $row["client"])		//廣告主
						->setCellValue('C'.$number, $row["name"])		//campaign
						->setCellValue('D'.$number, $row["member"])		//業務
						->setCellValue('E'.$number, $media_name[$i])
						->setCellValue('F'.$number, $str_cue)
						->setCellValue('G'.$number, $row["date1"])
						->setCellValue('H'.$number, $row["date2"])
						->setCellValue('I'.$number, $row["exchang_math"])
						->setCellValue('J'.$number, $row2["totalprice"])
						->setCellValue('K'.$number, $row2["income1"])		//收入1
						->setCellValue('L'.$number, $row2["income2"])		//收入2
						->setCellValue('M'.$number, $row2["income3"])		//收入3
						->setCellValue('N'.$number, $income_bc)				//補填收入
						->setCellValue('O'.$number, $row2["cost1"])			//成本1
						->setCellValue('P'.$number, $row2["cost2"])			//成本2
						->setCellValue('Q'.$number, $row2["cost3"])			//成本3
						->setCellValue('R'.$number, $cost_bc)				//補填成本
						->setCellValue('S'.$number, $price)
						->setCellValue('U'.$number, $row["tagtext"]);
						$sum_totalprice += $row2["totalprice"];
						$sum_price += $price;

						//收入1 成本1 註記
						if((substr($row['date2'],0,2)-substr($row['date1'],0,2))>=0){
							$objPHPExcel->getActiveSheet()->getComment('K'.$number)->setAuthor('PHPExcel');//どこに影響しているのかわかりません
							//次がコメントのタイトルになります
							$objCommentRichText = $objPHPExcel->getActiveSheet()->getComment('K'.$number)->getText()->createTextRun(substr($row['date1'],0,2).'月收入');
							$objPHPExcel->getActiveSheet()->getComment('O'.$number)->setAuthor('PHPExcel');//どこに影響しているのかわかりません
							//次がコメントのタイトルになります
							$objCommentRichText = $objPHPExcel->getActiveSheet()->getComment('O'.$number)->getText()->createTextRun(substr($row['date1'],0,2).'月成本');
						}
						//收入2 成本2 註記
						if((substr($row['date2'],0,2)-substr($row['date1'],0,2))>=1){
							$objPHPExcel->getActiveSheet()->getComment('L'.$number)->setAuthor('PHPExcel');//どこに影響しているのかわかりません
							//次がコメントのタイトルになります
							$objCommentRichText = $objPHPExcel->getActiveSheet()->getComment('L'.$number)->getText()->createTextRun(sprintf("%02d",substr($row['date1'],0,2)+1).'月收入');
							$objPHPExcel->getActiveSheet()->getComment('P'.$number)->setAuthor('PHPExcel');//どこに影響しているのかわかりません
							//次がコメントのタイトルになります
							$objCommentRichText = $objPHPExcel->getActiveSheet()->getComment('P'.$number)->getText()->createTextRun(sprintf("%02d",substr($row['date1'],0,2)+1).'月成本');
						}
						//收入3 成本3 註記
						if((substr($row['date2'],0,2)-substr($row['date1'],0,2))>=2){
							$objPHPExcel->getActiveSheet()->getComment('M'.$number)->setAuthor('PHPExcel');//どこに影響しているのかわかりません
							//次がコメントのタイトルになります
							$objCommentRichText = $objPHPExcel->getActiveSheet()->getComment('M'.$number)->getText()->createTextRun(substr($row['date2'],0,2).'月收入');
							$objPHPExcel->getActiveSheet()->getComment('Q'.$number)->setAuthor('PHPExcel');//どこに影響しているのかわかりません
							//次がコメントのタイトルになります
							$objCommentRichText = $objPHPExcel->getActiveSheet()->getComment('Q'.$number)->getText()->createTextRun(substr($row['date2'],0,2).'月成本');
						}

						//補填收入 補填成本 註記
						if((substr($income_bc,0,2)-substr($cost_bc,0,2))>=2){
							$objPHPExcel->getActiveSheet()->getComment('N'.$number)->setAuthor('PHPExcel');//どこに影響しているのかわかりません
							//次がコメントのタイトルになります
							$objCommentRichText = $objPHPExcel->getActiveSheet()->getComment('N'.$number)->getText()->createTextRun($chang_str);
							$objPHPExcel->getActiveSheet()->getComment('R'.$number)->setAuthor('PHPExcel');//どこに影響しているのかわかりません
							//次がコメントのタイトルになります
							$objCommentRichText = $objPHPExcel->getActiveSheet()->getComment('R'.$number)->getText()->createTextRun($chang_str);
						}

						//毛利率 計算
						if($profit){
							$pro_avg = $price / $row2["totalprice"];
							$pro_ans = round($pro_avg, 2); 
							$pro_ans = $pro_ans*100;
							$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue('T'.$number, $pro_ans.'%');
						}
					}
						
				}
				
			}
			
		}
		$number++;
		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('J'.$number, $sum_totalprice)
			->setCellValue('S'.$number, $sum_price);
	}
}else{


	$objPHPExcel->getDefaultStyle()->getFont()->setName('新細明體');
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(12);
	$objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(18);//設定欄位寬度
	//index ary
	$index_ary = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
	/*
	$str_s2 = "代理商";
	if($ACname[0] ==  'client'){
		$str_s2 = "廣告主";
	}
	*/
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$number, '序')
				->setCellValue('B'.$number, '代理商')
				->setCellValue('C'.$number, '廣告主');

	$media_detial = 2;		//前面已設3欄 0  1 2
	$media = $media_detial;	//設定欄位變數

	$sql_media = "SELECT * FROM  `media` WHERE  `id` >0 ORDER BY  `id` ASC ";
	$result=mysql_query($sql_media); 
	while($row_mlist=mysql_fetch_array($result)){
		$media++;
		$media_ary[] = $row_mlist["id"];	//媒體id陣列
		if($media <= 25){
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($index_ary[$media].$number, $row_mlist["name"]);
		}
		else{
			$j = floor($media / 26) - 1;
			$i = $media % 26 ;
			//echo $index_ary[$j].$index_ary[$i]."=".'j='.$j.' i='.$i.'<br>';
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($index_ary[$j].$index_ary[$i].$number, $row_mlist["name"]);
		}
		$media++;
		if($media <= 25){
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($index_ary[$media].$number, $row_mlist["name"].'毛利');
		}
		else{
			$j = floor($media / 26) - 1;
			$i = $media % 26;
			//echo $index_ary[$j].$index_ary[$i]."=".'j='.$j.' i='.$i.'<br>';
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue($index_ary[$j].$index_ary[$i].$number, $row_mlist["name"].'毛利');
		}
	}

	$media++;
	$j = floor($media / 26) - 1;
	$i = $media % 26;
	$end1 = $index_ary[$j].$index_ary[$i];

	$media++;
	$j = floor($media / 26) - 1;
	$i = $media % 26;
	$end2 = $index_ary[$j].$index_ary[$i];

	$media++;
	$j = floor($media / 26) - 1;
	$i = $media % 26;
	$end3 = $index_ary[$j].$index_ary[$i];

	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue($end1.$number, '年度總發稿量(總計)')
		->setCellValue($end2.$number, '年度發稿毛利(總計)')
		->setCellValue($end3.$number, '年度毛利率(總計)');
		//->setCellValue($end3.$number, '年度案件偏好類別');


	

	$sql = "SELECT TB.name AS TBName, CA.* FROM campaign CA LEFT JOIN $ACname[0] TB ON TB.id = CA.$ACname[0]_id WHERE CA.status in (3,4,5) ".$str_id.$str_date.$str_tag." ORDER BY  `CA`.`agency_id`,`CA`.`client_id` ASC";
	//$sql = "SELECT TB.name AS TBName, CA.* FROM campaign CA LEFT JOIN agency TB ON TB.id = CA.agency_id WHERE CA.status in (3,4,5) AND TB.id in (3,5,8) ".$str_id.$str_date.$str_tag." ORDER BY  `CA`.`agency_id`,`CA`.`client_id` ASC";
	
	if($ACname[0] == 'all'){
		$sql = "SELECT TB.name AS TBName, CA.* FROM campaign CA LEFT JOIN client CL ON CL.id = CA.client_id LEFT JOIN agency TB ON TB.id = CA.agency_id WHERE CA.status in (3,4,5) ".$str_id.$str_date.$str_tag."  ORDER BY  `CA`.`agency_id`,`CA`.`client_id` ASC";
	}

	$sum_totalprice = 0; //總發稿加總
	$sum_price = 0; //總毛利加總
	//加總表格log
	$file = 'SQL_total_log.txt';
	$person = $sql."\r\n".mysql_error()."\r\n".date("Y-m-d H:i:s",time())."\r\n\r\n";
	file_put_contents($file, $person, FILE_APPEND | LOCK_EX);
	//$objPHPExcel->setActiveSheetIndex(0)
	//			->setCellValue('A1', $sql);
	
	//echo $sql;
	//exit();
	//預設 代理商id廣告主id
	$Dispatch = array();
	$agency = 0; $client = 0;$if_next = 0;$end_toal = 0;
	$result=mysql_query($sql);	//找代理商 或 廣告主的capaign id
	if (mysql_num_rows($result)>0){
		while($row=mysql_fetch_array($result)){	//有多個campaign

			/*
			$str_name = $row["agency"];
			if($row["agency_id"] == 0){
				$str_name = $row["client"];
			}
			*/
			//echo $agency."&".$row["agency_id"].'='.$client."&".$row["client_id"].'<br>';
			if($ACname[0] == 'client'){		//廣告主為主
				if($agency != $row["agency_id"] || $client != $row["client_id"]){
					$number++;
					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A'.$number, $number-1)
					->setCellValue('B'.$number, $row["agency"])
					->setCellValue('C'.$number, $row["client"]);		//廠商名
					
					$sum_totalprice = 0; //總發稿加總 歸 0
					$sum_price = 0; //總毛利加總 歸 0
					
					//unset($Dispatch);
					//ReSetAry(count($media_ary));
					for($i=0;$i<count($media_ary);$i++){
						$Dispatch[$media_ary[$i]][1] = 0;
						$Dispatch[$media_ary[$i]][2] = 0;
					}
				}
			}else{							//代理商為主
				if($agency != $row["agency_id"]){
					$number++;
					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A'.$number, $number-1)
					->setCellValue('B'.$number, $row["agency"])
					->setCellValue('C'.$number,'');		//廠商名
					
					$sum_totalprice = 0; //總發稿加總 歸 0
					$sum_price = 0; //總毛利加總 歸 0
					
					//unset($Dispatch);
					//ReSetAry(count($media_ary));
					for($i=0;$i<count($media_ary);$i++){
						$Dispatch[$media_ary[$i]][1] = 0;
						$Dispatch[$media_ary[$i]][2] = 0;
					}
				}
			}
			
			if($ACname[0] == 'all'){
				if($agency != $row["agency_id"] || $client != $row["client_id"]){
					$number++;
					$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A'.$number, $number-1)
					->setCellValue('B'.$number, $row["agency"])
					->setCellValue('C'.$number, $row["client"]);		//廠商名
					
					$sum_totalprice = 0; //總發稿加總 歸 0
					$sum_price = 0; //總毛利加總 歸 0
					
					//unset($Dispatch);
					//ReSetAry(count($media_ary));
					for($i=0;$i<count($media_ary);$i++){
						$Dispatch[$media_ary[$i]][1] = 0;
						$Dispatch[$media_ary[$i]][2] = 0;
					}
				}
			}

			$ver = $row["version"];	//後台版本  1.0 = 0  2.0 =2
			for($ary=0;$ary<count($media_ary);$ary++){	//媒體ary  找各媒體
				//找各媒體的 SQL
				//echo 'campagin id = '.$row['id'].'media = '.$media_ary[$ary].'<br>';;
				//$sql_m = "SELECT id,campaign_id,cue,totalprice,IFNULL(text1,0) AS text1,IFNULL(text5,0) AS text5,IFNULL(text9,0) AS text9,IFNULL(text2,0) AS text2,IFNULL(text6,0) AS text6,IFNULL(text10,0) AS text10 FROM media".$media_ary[$ary]." where campaign_id = ".$row['id']." AND totalprice <> 0 ";
				$sql_m = "SELECT `campaign_id`,`cue`,`totalprice`,`text1` as text1,`text5` as text5,`text9` as text9,`text2` as text2,`text6` as text6,`text10` as text10  FROM media".$media_ary[$ary]." where campaign_id = ".$row['id']." AND totalprice <> 0 order by id DESC ,cue DESC ";
				$ok_campaign = 0;
				$result2=mysql_query($sql_m); 
				if (mysql_num_rows($result2)>0){
					while($row2=mysql_fetch_array($result2)){
						$u = $media_ary[$ary];
						//echo $sql_m."<br>";
						//20150119
						$income_bc = 0;	//補填收入
						$cost_bc = 0;	//補填成本

						$sql_mediaChang = "SELECT * FROM media_change WHERE media_id = ".$media_ary[$ary]." AND campaign_id = ".$row["id"]." AND change_date	 >= '$Date_start' AND change_date <'$Date_end'";
						//echo $sql_mediaChang;
						//exit();
						$result_mediaChang=mysql_query($sql_mediaChang); 
						if (mysql_num_rows($result_mediaChang)>0){
							$row_mediaChang=mysql_fetch_array($result_mediaChang);
							$income_bc = $row_mediaChang["change_income"];
							$cost_bc = $row_mediaChang["change_cost"];	
						}


						// 20150109
						$price = ($row2["text1"]+$row2["text5"]+$row2["text9"]+$income_bc)-($row2["text2"]+$row2["text6"]+$row2["text10"]+$cost_bc);
						if($row2["cue"] == '2'){
							// 20150109
							if($row["version"] != 0){
								if($price == 0 && $u != 20)
									continue;
							}
							$ok_campaign = 1;
							// 20150109
							$str_cue = "對內cue";
						}else{
							$str_cue = "對外cue";
							$income_bc = 0;	//補填收入
							$cost_bc = 0;	//補填成本
							// 20150109
							if($row["version"] != 0){
								if($ok_campaign == 0)
									continue;
							}
							// 20150109
							$ok_campaign = 0;
						}
						// 20150109
						//echo 'campagin id = '.$row['id'].'media = '.$media_ary[$ary].' priec='.$row2["totalprice"].'<br>';
						
						//echo $u.'='.$row2["totalprice"].'<br>';
						if($row2["cue"] == '1'){
							
								$Dispatch[$media_ary[$ary]][1] += $row2["totalprice"];	//發稿量
								//echo "ary=$ary totalprice=".$row2["totalprice"]." ALL = ".$Dispatch[$media_ary[$ary]][1]."<br>";
									//if($row2["totalprice"] > 0)
									//echo "媒體編號=".$media_ary[$ary]." 發稿量".$Dispatch[$media_ary[$ary]][1]."<br>";

							$sum_totalprice += $row2["totalprice"];
							
						}else{
							//$str_cue = "對內cue";
							if($u == 20 || $ver == 0){	//價差
								
									$Dispatch[$media_ary[$ary]][2] += $row2["totalprice"];	//毛利
							
							}else{
								$price = ($row2["text1"]+$row2["text5"]+$row2["text9"]+$income_bc)-($row2["text2"]+$row2["text6"]+$row2["text10"]+$cost_bc);
							
									$Dispatch[$media_ary[$ary]][2] += $price;	//毛利
							
								$sum_price += $price;
				
							}
							
						}
					}
				}
			}
			
			//exit();
			//填入媒體發稿＆毛利
			
			if($ACname[0] == 'client'){		//廣告主為主
				if($agency != $row["agency_id"] || $client != $row["client_id"]){
					$if_next = 1;
				}
			}else{							//代理商為主
				if($agency != $row["agency_id"]){
					$if_next = 1;
				}
			}

			if($agency == 5){
				//var_dump($Dispatch);
				//exit();
			}
			//echo 'if next='.$if_next.' ACname='.$ACname[0].'<br>' ;
			//echo 'if next='.$if_next.' agency='.$agency.' agency_id='.$row["agency_id"].'<br>' ;
			//if($if_next){

				$agency = $row["agency_id"];
				$client = $row["client_id"];

				$media = $media_detial;	//設定欄位變數
				$ary_math = 0;
				
				$total_media = $media+(count($media_ary)*2);
				
					
				for($for_i = $media;$for_i < $total_media;){
					
					$for_i++;
					//echo $for_i;	
					if($for_i <= 25){
						if(isset($Dispatch[$media_ary[$ary_math]][1])){
							$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue($index_ary[$for_i].$number, $Dispatch[$media_ary[$ary_math]][1]);
							
								//echo $index_ary[$for_i].$number.'='.$Dispatch[$media_ary[$ary_math]][1].'<br>';
						}else{
							$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue($index_ary[$for_i].$number, '0');
							//	echo $index_ary[$for_i].$number.'=0<br>';
						}
					}
					else{
						$j = floor($for_i / 26) - 1;
						$i = $for_i % 26 ;

						if(isset($Dispatch[$media_ary[$ary_math]][1])){
							$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue($index_ary[$j].$index_ary[$i].$number, $Dispatch[$media_ary[$ary_math]][1]);
							//	echo $index_ary[$j].$index_ary[$i].$number.'='.$Dispatch[$media_ary[$ary_math]][1].'<br>';
						}else{
							$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue($index_ary[$j].$index_ary[$i].$number, '0');
							//	echo $index_ary[$j].$index_ary[$i].$number.'=0<br>';

						}
					}
			
					$set_media = $for_i+1;

					$for_i++;
					if($set_media <= 25){
						
						if(isset($Dispatch[$media_ary[$ary_math]][2])){
							//$var = $Dispatch[$media_ary[$ary_math]][2];
							$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue($index_ary[$set_media].$number, $Dispatch[$media_ary[$ary_math]][2]);
								//echo $index_ary[$set_media].$number.'='.$Dispatch[$media_ary[$ary_math]][2].'<br>';
							

						}else{
							$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue($index_ary[$set_media].$number, '0');
							//	echo $index_ary[$set_media].$number.'=0<br>';
						}
					}
					else{
						$j = floor($set_media / 26) - 1;
						$i = $set_media % 26;
						
						if(isset($Dispatch[$media_ary[$ary_math]][2])){
							$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue($index_ary[$j].$index_ary[$i].$number, ''.$Dispatch[$media_ary[$ary_math]][2].'');
							//	echo $index_ary[$j].$index_ary[$i].$number.'='.$Dispatch[$media_ary[$ary_math]][2].'<br>';
						}else{
							$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue($index_ary[$j].$index_ary[$i].$number, '0');
							//	echo $index_ary[$j].$index_ary[$i].$number.'=0<br>';
						}
					}

					$ary_math++;	//媒體索引累加
					
				}
				//exit();

				$year_pers = 0;
				if($sum_price > 0 && $sum_totalprice > 0){
					$total = $sum_price / $sum_totalprice;
					$year_pers = round($total,2);
					$year_pers = $year_pers * 100;
				}
				$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue($end1.$number, $sum_totalprice)
					->setCellValue($end2.$number, $sum_price)
					->setCellValue($end3.$number, $year_pers.'%');
					

				//echo 'sum_totalprice='.$sum_totalprice."<br>sum_price =".$sum_price;
				$end_toal++;
				if($agency == 8){
					//echo '<br>end_toal='.$end_toal.'count='.count($media_ary);
				}
				if($end_toal >= count($media_ary)){
				$if_next = 0;
				}
			//}
		}
	}

}
//exit();
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
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

//var_dump($media_id);
//var_dump($media_name);
function ReSetAry($total){
	for($i=0;$i<$total;$i++){
		$Dispatch[$i][1] = 0;
		$Dispatch[$i][2] = 0;
	}
}

exit();


?>