<?php

	$chatBotMediaId = 132;

	ini_set( "memory_limit", "256M");
	require_once dirname(dirname(__DIR__)) .'/autoload.php';

	IsPermitted();

	$id = GetVar('id');

	$objCampaign = CreateObject('Campaign', $id);

	if (IsId($objCampaign->getId())) {
		$totalPrice = 0;
		$asciiCodeA = 65;
		$idxExcelMediaDataStart = 8;
		$idxExcelRow = $idxExcelMediaDataStart;

		$db = clone($GLOBALS['app']->db);
		$dbMediaData = clone($GLOBALS['app']->db);

		$row2 = $objCampaign->fields;
		foreach ($row2 as $idxKey => $data) {
			$row2[$idxKey] = trim($data);
		}
		$a1 = 0;
		$a2 = 0;
		$a3 = 0;
		$s1 = 0;
		$s2 = 0;

		$sqlAboardMedia = sprintf("SELECT COUNT(*) AS `total` FROM `media_map` 
									WHERE `map_campaign` = %d 
									AND `map_media_ordinal` IN (
										SELECT `id` FROM `media` 
										WHERE `type` >= 20 AND `display` = 1
									);", 
										$objCampaign->getId()
								);
		$db->query($sqlAboardMedia);
		$result = $db->next_record();

		if (empty($result['total'])) {
			$dbClac = clone($GLOBALS['app']->db);

			$sqlMediaType = sprintf("SELECT `map_media_ordinal` FROM `media_map` 
									WHERE `map_campaign` = %d 
									AND `map_media_ordinal` IN (
										SELECT id  FROM `media` 
										WHERE `type` IN (1, 3) AND `display` = 1
									) GROUP BY `map_media_ordinal`", 
										$objCampaign->getId()
								);
			$db->query($sqlMediaType);
			while ($itemMediaType = $db->next_record()) {
				$sqlCalc = sprintf("SELECT SUM(`quantity`) AS `quantity`, 
									SUM(`quantity2`) AS `quantity2`, 
									SUM(`totalprice`) AS `totalprice` FROM `media%d` 
									WHERE `campaign_id` = %d AND `cue` = 1;", 
										$itemMediaType['map_media_ordinal'], $objCampaign->getId()
								);
				$dbClac->query($sqlCalc);

				$itemCalc = $dbClac->next_record();
				$a1 = $a1 + $itemCalc['quantity2'];
				$s1 = $s1 + $itemCalc['totalprice'];
				$s2 = $s2 + $itemCalc['quantity'];
			}

			if ($a1 != 0 && $s2 != 0) {
				$a2 = (($s1 * 1000) / $a1);
				$a3 = $s1 / $s2;
			}
		}

		if (empty($row2['rate'])) {
			$usdjpy = '';
			$rate = 1;
		} else {
			$usdjpy = $row2['rate'] > 1 ? 'USD' : 'JPY';
			$rate = $row2['rate'];
		}
		
		IncludeFunctions('jsadways');
		IncludeFunctions('excel');

		$columnWidth = [
			'A' => 8.6,
			'B' => 9.1,
			'C' => 9.6,
			'D' => 10.8,
			'E' => 13.8,
			'F' => 5.3,
			'G' => 8.5,
			'H' => 4.5,
			'I' => 5.8,
			'J' => 5.3,
			'K' => 5.5,
			'L' => 6.3,
			'M' => 5.1,
			'N' => 4.8,
			'O' => 9.5,
			'P' => 5.7,
			'Q' => 8.8,
		];

		$objPHPExcel = CreateExcelFile();
		$objPHPExcel->getDefaultStyle()->getFont()->setName('新細明體');
		
		$excelActiveSheet = &$objPHPExcel->getActiveSheet();
		$excelActiveSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$excelActiveSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
		$excelActiveSheet->getPageMargins()->setLeft(0.44);
		$excelActiveSheet->getPageMargins()->setRight(0.44);
		$excelActiveSheet->getSheetView()->setZoomScale(220);

		// $excelActiveSheet->getDefaultRowDimension()->setRowHeight(8.5);
		foreach ($columnWidth as $columnName => $columnWidth) {
			$excelActiveSheet->getColumnDimension($columnName)->setWidth($columnWidth);
		}

		$excelActiveSheet->getRowDimension(1)->setRowHeight(14);
		for ($idxRow=2; $idxRow<=7; $idxRow++) {
			$excelActiveSheet->getRowDimension($idxRow)->setRowHeight(8.5);
		}

		MergeExcellCell($excelActiveSheet, [
			'A1:D1', 'A2:D2', 'A3:D3', 'A4:D4', 'A5:D5',
			'F2:G2', 'F3:G3', 'F4:G4', 'F5:G5', 'F6:G6',
			'N1:Q4',
		]);
		SetExcelCellMiddle($excelActiveSheet, ['A1:F6']);
		SetExcelCellCenter($excelActiveSheet, ['F3:F6']);

		$excelActiveSheet->getStyle('A1')->getFont()->setSize(12);
		$excelActiveSheet->getStyle('A1')->getFont()->setBold(true);
		$excelActiveSheet->getStyle('A1')->getFont()->getColor()->setRGB('0300DC');
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'JS-Adways Media Schedule');
		
		$excelActiveSheet->getStyle('A2:A5')->getFont()->setSize(6);
		$excelActiveSheet->getStyle('A2:A5')->getFont()->setBold(true);
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A2', '廣告代理商(Agency)：'. $row2['agency'])
					->setCellValue('A3', '廣告主(Client)：'. $row2['client'])
					->setCellValue('A4', '活動(Campaign)：'. $row2['name'])
					->setCellValue('A5', '期間(Period)：'. date('m/d', $row2['date11']) .' - '. date('m/d', $row2['date22']));

		$excelActiveSheet->getStyle('E2:G2')->getFont()->setSize(7);
		$excelActiveSheet->getStyle('E2:G2')->getFont()->setBold(true);
		$excelActiveSheet->getStyle('E2:G2')->getFont()->getColor()->setRGB('FFFFFF');
		$excelActiveSheet->getStyle('E2:G2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$excelActiveSheet->getStyle('E2:G2')->getFill()->getStartColor()->setRGB('000000'); 
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('E2', 'Pre-Buy Analysis');

		$excelActiveSheet->getStyle('E3:G6')->getFont()->setSize(7);
		$excelActiveSheet->getStyle('E3:F6')->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
		$excelActiveSheet->getStyle('E3:G6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$excelActiveSheet->getStyle('E3:G6')->getFill()->getStartColor()->setRGB('FFCC66'); 
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('E3', 'Total Impression :')
					->setCellValue('E4', 'Ave. CPM :')
					->setCellValue('E5', 'Ave. CPC :')
					->setCellValue('E6', 'Discount :')
					->setCellValue('F3', number_format($a1))
					->setCellValue('F4', number_format($a2))
					->setCellValue('F5', number_format($a3))
					->setCellValue('F6', 'N/A');

		$objDrawing = new PHPExcel_Worksheet_Drawing();
		$objDrawing->setName('Logo');
		$objDrawing->setDescription('Logo');
		$objDrawing->setPath('jsadways.png');
		$objDrawing->setHeight(54);
		$objDrawing->setWidth(186);
		$objDrawing->setCoordinates('N1');
		$objDrawing->setOffsetX(110);
		$objDrawing->setRotation(25);
		$objDrawing->getShadow()->setVisible(true);
		$objDrawing->getShadow()->setDirection(45);
		$objDrawing->setWorksheet($excelActiveSheet);

		$mediaTypeList = [
			'CPC' => [
				'id' => 1,
				'column' => [
					'A' => ['Website', '網站'],
					'B' => ['Channel', '頻道'],
					'C' => ['System', '系統'],
					'D' => ['Position', '版位'],
					'E' => ['Size', '規格'],
					'F' => ['Format', '格式'],
					'H' => ['R/F', '輪替/固定'],
					'I' => ['Est.Impressions', '預估曝光數'],
					'J' => ['Est.CTR(%)', '預估點擊率'],
					'K' => ['Est.Clicks', '預估點擊數'],
					'L' => ['Period', '刊登日期'],
					'M' => ['Days', '天數'],
					'N' => ['CPC計價', '台幣'],
					'O' => ['Material Due', '素材提供期限'],
					'P' => ['Net Cost(NTD)', '售價'],
					'Q' => ['Other', '備註'],
				],
				'extend' => 0,
			],
			'CPI' => [
				'id' => 2,
				'column' => [
					'A' => ['Website', '項目'],
					'B' => ['Action', '執行內容'],
					'C' => ['system', 'OS'],
					'D' => ['Position', '版位'],
					'E' => ['Size', '規格'],
					'F' => ['Format', '格式'],
					'H' => ['R/F', '輪替/固定'],
					'I' => ['Est.Actions', '數量'],
					'J' => ['Period', '刊登日期'],
					'K' => ['Days', '天數'],
					'L' => ['Material Due', '素材提供期限'],
					'N' => ['Cost Per', 'CPI定價'],
					'O' => ['Net Cost(NTD)', '售價'],
					'Q' => ['Other', '備註'],
				],
				'extend' => 2,
			],
			'CPT' => [
				'id' => 10,
				'column' => [
					'A' => ['Website', '項目'],
					'B' => ['Action', '執行內容'],
					'C' => ['system', 'OS'],
					'D' => ['Position', '版位'],
					'E' => ['Size', '規格'],
					'F' => ['Format', '格式'],
					'H' => ['R/F', '輪替/固定'],
					'I' => ['Est.Actions', '數量'],
					'J' => ['Period', '刊登日期'],
					'K' => ['Days', '天數'],
					'L' => ['Material Due', '素材提供期限'],
					'N' => ['Cost Per', 'CPT定價'],
					'O' => ['Net Cost(NTD)', '售價'],
					'Q' => ['Other', '備註'],
				],
				'extend' => 2,
			],
			'CPM' => [
				'id' => 0,
				'column' => [
					'A' => ['Website', '網站'],
					'B' => ['Channel', '頻道'],
					'C' => ['System', '系統'],
					'D' => ['Position', '版位'],
					'E' => ['Size', '規格'],
					'F' => ['Format', '格式'],
					'H' => ['R/F', '輪替/固定'],
					'I' => ['Est.Impressions', '預估曝光數'],
					'J' => ['Est.CTR(%)', '預估點擊率'],
					'K' => ['Est.Clicks', '預估點擊數'],
					'L' => ['Period', '刊登日期'],
					'M' => ['Days', '天數'],
					'N' => ['CPM計價', '台幣'],
					'O' => ['Material Due', '素材提供期限'],
					'P' => ['Net Cost(NTD)', '售價'],
					'Q' => ['Other', '備註'],
				],
				'extend' => 0,
			],
			'CPV' => [
				'id' => 9,
				'column' => [
					'A' => ['Website', '網站'],
					'B' => ['Channel', '頻道'],
					'C' => ['System', '系統'],
					'D' => ['Position', '版位'],
					'E' => ['Size', '規格'],
					'F' => ['Format', '格式'],
					'H' => ['R/F', '輪替/固定'],
					'I' => ['Est.Impressions', '預估曝光數'],
					'J' => ['Est.CTR(%)', '預估點擊率'],
					'K' => ['Est.Clicks', '預估點擊數'],
					'L' => ['Period', '刊登日期'],
					'M' => ['Days', '天數'],
					'N' => ['CPV計價', '台幣'],
					'O' => ['Material Due', '素材提供期限'],
					'P' => ['Net Cost(NTD)', '售價'],
					'Q' => ['Other', '備註'],
				],
				'extend' => 0,
			],
			'WebAD' => [
				'id' => 3,
				'column' => [
					'A' => ['Website', '網站'],
					'B' => ['Channel', '頻道'],
					'C' => ['Position', '版位'],
					'D' => ['Size', '規格'],
					'E' => ['Format', '格式'],
					'F' => ['R/F', '輪替/固定'],
					'G' => ['Est.IMP', '預估曝光數'],
					'I' => ['Est.CTR(%)', '預估點擊率'],
					'K' => ['Est.Clicks', '預估點擊數'],
					'M' => ['Material Due', '素材提供期限'],
					'O' => ['Period', '刊登日期'],
					'P' => ['Days', '天數'],
					'Q' => ['Net Cost(NTD)', '售價'],
				],
				'extend' => 3,
			],
			'CPA' => [
				'id' => 4,
				'column' => [
					'A' => ['Website', '網站'],
					'B' => ['Action', 'CPA內容'],
					'C' => ['Position', '版位'],
					'D' => ['Size', '規格'],
					'E' => ['Format', '格式'],
					'F' => ['R/F', '輪替/固定'],
					'H' => ['Est.Actions', '會員人數'],
					'I' => ['Period', '刊登日期'],
					'J' => ['Days', '天數'],
					'K' => ['Material Due', '素材提供期限'],
					'L' => ['Cost Per', 'CPA定價'],
					'M' => ['Net Cost(NTD)', '售價'],
				],
				'extend' => 0,
			],
		];

		// 2018-03-22 (Jimmy): BEGIN - 搜尋對外CUE媒體詳細資料
			foreach ($mediaTypeList as $typeName => $typeInfo) {
				$sqlMediaTypeCount = sprintf("SELECT COUNT(*) AS `total` FROM `media_map`
												WHERE `map_campaign` = %d 
												AND `map_media_ordinal` IN (
													SELECT `id` FROM `media` 
													WHERE `type` = %d AND `display` = 1
												)",
													$objCampaign->getId(), $typeInfo['id']
											);
				$db->query($sqlMediaTypeCount);
				$resultMediaTypeCount = $db->next_record();
				if ($resultMediaTypeCount['total']) {
					$excelActiveSheet->getRowDimension($idxExcelRow)->setRowHeight(8.5);
					$excelActiveSheet->getRowDimension($idxExcelRow + 1)->setRowHeight(8.5);
					$excelActiveSheet->getStyle(chr($asciiCodeA). $idxExcelRow .':'. chr($asciiCodeA + count($typeInfo['column']) + $typeInfo['extend']) . ($idxExcelRow + 1))->getFill()->getStartColor()->setRGB('DDDDDD');
					$excelActiveSheet->getStyle(chr($asciiCodeA). $idxExcelRow .':'. chr($asciiCodeA + count($typeInfo['column']) + $typeInfo['extend']) . ($idxExcelRow + 1))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
					
					for ($idxAscii=$asciiCodeA; $idxAscii<=($asciiCodeA + count($typeInfo['column']) + $typeInfo['extend']); $idxAscii++) {
						SetExcellCellBorder($excelActiveSheet, [
							chr($idxAscii). $idxExcelRow => 'all',
							chr($idxAscii). ($idxExcelRow + 1) => 'all',
						]);
					}

					SetExcelCellCenter($excelActiveSheet, [chr($asciiCodeA) . $idxExcelRow .':'. chr($asciiCodeA + count($typeInfo['column']) + $typeInfo['extend']) . ($idxExcelRow + 1)]);
					SetExcelCellMiddle($excelActiveSheet, [chr($asciiCodeA) . $idxExcelRow .':'. chr($asciiCodeA + count($typeInfo['column']) + $typeInfo['extend']) . ($idxExcelRow + 1)]);

					if (in_array($typeName, ['WebAD'])) {
						MergeExcellCell($excelActiveSheet, [
							'G'. $idxExcelRow .':H'. $idxExcelRow,
							'G'. ($idxExcelRow + 1) .':H'. ($idxExcelRow + 1),
							'I'. $idxExcelRow .':J'. $idxExcelRow,
							'I'. ($idxExcelRow + 1) .':J'. ($idxExcelRow + 1),
							'K'. $idxExcelRow .':L'. $idxExcelRow,
							'K'. ($idxExcelRow + 1) .':L'. ($idxExcelRow + 1),
							'M'. $idxExcelRow .':N'. $idxExcelRow,
							'M'. ($idxExcelRow + 1) .':N'. ($idxExcelRow + 1),
						]);
					} else {
						MergeExcellCell($excelActiveSheet, [
							'F'. $idxExcelRow .':G'. $idxExcelRow,
							'F'. ($idxExcelRow + 1) .':G'. ($idxExcelRow + 1)
						]);
						
						if (in_array($typeName, ['CPI', 'CPT'])) {
							MergeExcellCell($excelActiveSheet, [
								'L'. $idxExcelRow .':M'. $idxExcelRow,
								'L'. ($idxExcelRow + 1) .':M'. ($idxExcelRow + 1),
								'O'. $idxExcelRow .':P'. $idxExcelRow,
								'O'. ($idxExcelRow + 1) .':P'. ($idxExcelRow + 1),
							]);
						}
					}

					foreach ($typeInfo['column'] as $idxColumn => $arrColumnText) {
						SetExcelCellValue($objPHPExcel, [
							$idxColumn . $idxExcelRow => $arrColumnText[0],
							$idxColumn . ($idxExcelRow + 1) => $arrColumnText[1],
						]);
					}
					$idxExcelRow += 2;

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
					
					$sqlMediaTypeList = sprintf("SELECT `map_media_ordinal` FROM `media_map`
													WHERE `map_campaign` = %d
													AND `map_media_ordinal` IN (
														SELECT `id` FROM `media` 
														WHERE `type` = %d AND `display` = 1
													) GROUP BY `map_media_ordinal`;",
														$objCampaign->getId(), $typeInfo['id']
												);
					$db->query($sqlMediaTypeList);
					while ($itemMediaTypeList = $db->next_record()) {
						$sqlTypeData = sprintf("SELECT * FROM `media%d` WHERE `cue` = 1 AND `campaign_id` = %d;",$itemMediaTypeList['map_media_ordinal'], $objCampaign->getId());
						$dbMediaData->query($sqlTypeData);
						while ($itemMediaData = $dbMediaData->next_record()) {
							for ($idxAscii=$asciiCodeA; $idxAscii<=($asciiCodeA + count($typeInfo['column']) + $typeInfo['extend']); $idxAscii++) {
								SetExcellCellBorder($excelActiveSheet, [chr($idxAscii). $idxExcelRow => 'all']);
							}

							SetExcelCellCenter($excelActiveSheet, [chr($asciiCodeA) . $idxExcelRow .':'. chr($asciiCodeA + count($typeInfo['column']) + $typeInfo['extend']) . $idxExcelRow]);
							if (in_array($typeName, ['WebAD'])) {
								
							} else {
								MergeExcellCell($excelActiveSheet, ["F$idxExcelRow:G$idxExcelRow"]);
							}
							
							$excelActiveSheet->getStyle("A$idxExcelRow:Q$idxExcelRow")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
							$excelActiveSheet->getStyle("A$idxExcelRow:Q$idxExcelRow")->getAlignment()->setWrapText(true);

							$website = '';
							$position = '';
							$other = '';

							$price = [];
							if (empty($itemMediaData['price'])) {
								for ($idxPrice=1; $idxPrice<=5; $idxPrice++) {
									if ($itemMediaData["price$idxPrice"]) {
										$price[] = $usdjpy .'$'. ($itemMediaData["price$idxPrice"] / $rate);
									}
								}
							} else {
								$price[] = $usdjpy .'$'. ($itemMediaData["price"] / $rate);
							}
							$price = implode("\n", $price);

							$duration = [];
							for ($idxDate=1; $idxDate<=9; $idxDate+=2) {
								if ($itemMediaData["date$idxDate"]) {
									$duration[] = date('m/d', $itemMediaData["date$idxDate"] + (8 * 60 * 60))  .' ~ '. date('m/d', $itemMediaData['date'. ($idxDate + 1)] + (8 * 60 * 60));
								}
							}
							$duration = implode("\n", $duration);

							if ($typeName == 'CPC') {
								SetExcelCellLeft($excelActiveSheet, [
									"E$idxExcelRow:F$idxExcelRow", 
									"Q$idxExcelRow",
								]);
								
								if ($itemMediaTypeList['map_media_ordinal'] == 88) {
									if($itemMediaData['beinstall_total'] != '') {
										$other .= "預估安裝數：{$itemMediaData['beinstall_total']}。\n";
									}
								}

								$other .= $itemMediaData['others'];

								if ($itemMediaTypeList['map_media_ordinal'] == 111) {
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
								} else if (in_array($itemMediaTypeList['map_media_ordinal'], [134, 135, 136])) {
									$website = str_replace('代操', '', $itemMediaData['website']);
								} else {
									$website = $itemMediaData['website'];
									$position = $itemMediaData['position'];
								}

								$totalPrice += $itemMediaData['totalprice'];
								SetExcelCellValue($objPHPExcel, [
									'A'. $idxExcelRow => $website,
									'B'. $idxExcelRow => $itemMediaData['channel'],
									'C'. $idxExcelRow => $itemMediaData['phonesystem'],
									'D'. $idxExcelRow => $position,
									'E'. $idxExcelRow => trim(str_replace("\\n", "\n", $itemMediaData['format1'])),
									'F'. $idxExcelRow => trim(str_replace("\\n", "\n", $itemMediaData['format2'])),
									'H'. $idxExcelRow => $itemMediaData['wheel'],
									'I'. $idxExcelRow => number_format($itemMediaData['quantity2']),
									'J'. $idxExcelRow => $itemMediaData['ctr'].'%',
									'K'. $idxExcelRow => number_format($itemMediaData['quantity']),
									'L'. $idxExcelRow => $duration,
									'M'. $idxExcelRow => $itemMediaData['days'],
									'N'. $idxExcelRow => number_format($price),
									'O'. $idxExcelRow => $itemMediaData['due'],
									'P'. $idxExcelRow => $usdjpy .'$'. number_format($itemMediaData['totalprice'] / $rate),
									'Q'. $idxExcelRow => $other,
								]);
							} else if (in_array($typeName, ['CPI', 'CPT'])) {
								SetExcelCellLeft($excelActiveSheet, [
									"E$idxExcelRow:F$idxExcelRow",
									"Q$idxExcelRow",
								]);
								MergeExcellCell($excelActiveSheet, [
									'L'. $idxExcelRow .':M'. $idxExcelRow, 
									'O'. $idxExcelRow .':P'. $idxExcelRow, 
								]);

								if ($typeName == 'CPI') {
									if (in_array($itemMediaTypeList['map_media_ordinal'], [134, 135, 136])){
										$website = str_replace('代操', '', $itemMediaData['website']);
									} else {
										$website = $itemMediaData['website'];
									}
								}

								$totalPrice += $itemMediaData['totalprice'];
								SetExcelCellValue($objPHPExcel, [
									'A'. $idxExcelRow => $typeName == 'CPI' ? $website : $itemMediaData['website'],
									'B'. $idxExcelRow => $itemMediaData['actions'],
									'C'. $idxExcelRow => $itemMediaData['phonesystem'],
									'D'. $idxExcelRow => $itemMediaData['position'],
									'E'. $idxExcelRow => trim(str_replace("\\n", "\n", $itemMediaData['format1'])),
									'F'. $idxExcelRow => trim(str_replace("\\n", "\n", $itemMediaData['format2'])),
									'H'. $idxExcelRow => $itemMediaData['wheel'],
									'I'. $idxExcelRow => number_format($itemMediaData['quantity']),
									'J'. $idxExcelRow => $duration,
									'K'. $idxExcelRow => $itemMediaData['days'],
									'L'. $idxExcelRow => $itemMediaData['due'],
									'N'. $idxExcelRow => number_format($price),
									'O'. $idxExcelRow => $usdjpy .'$'. number_format($itemMediaData['totalprice'] / $rate),
									'Q'. $idxExcelRow => trim($itemMediaData['others']),
								]);
							} else if ($typeName == 'CPM') {
								SetExcelCellLeft($excelActiveSheet, [
									"E$idxExcelRow:F$idxExcelRow",
									"Q$idxExcelRow",
								]);

								$website = '';
								$position = '';
								$format1 = '';
								$format2 = '';
								$quantity = '';
								$quantity2 = '';
								$days = '';
								$ctr = '';

								if ($itemMediaTypeList['map_media_ordinal'] == 86) {
									if ($itemMediaData['filter1'] != '') {
										$other .= "篩選條件－關鍵字設定：". $itemMediaData['filter1'] ."。\n";
									}
									
									if ($itemMediaData['filter2'] != '') {
										$other .= "篩選條件－關聯號碼搜尋：". $itemMediaData['filter2'] ."。\n";
									}

									$other .= $itemMediaData['others'];
								}

								if ($itemMediaTypeList['map_media_ordinal'] == 112 || $itemMediaTypeList['map_media_ordinal'] == 114 || $itemMediaTypeList['map_media_ordinal'] == 115) {
									$cpm225_phonesystem = $itemMediaData['phonesystem'];
									$cpm225_format1 .= str_replace("\\n", "\n", $itemMediaData['format1'])."\n";
									$cpm225_format2 .= str_replace("\\n", "\n", $itemMediaData['format2'])."\n";
									$cpm225_quantity2 += $itemMediaData['quantity2']; //曝光數
									$cpm225_ctr += $itemMediaData['ctr'];
									$cpm225_quantity += $itemMediaData['quantity']; //預估點擊數
									
									if (false !== ($rst = strpos($cpm225_datedate, $duration))) {
										
									} else {
										$cpm225_datedate .= $duration.',';
										$cpm225_days += $itemMediaData['days'];
										
									}
			
									$cpm225_totalprice += $itemMediaData['totalprice'];
									$cpm225_other .= $other."\n";
			
									continue;
								} else if ($itemMediaTypeList['map_media_ordinal'] == 113) {
									$website = 'JS NATIVE SOLUTION';
									$position = 'Applause Interstitial Video Ads';
									$format1 = str_replace("\\n", "\n", $itemMediaData['format1']);
									$format2 = str_replace("\\n", "\n", $itemMediaData['format2']);
									$quantity2 = $itemMediaData['quantity2'];
									$ctr = $itemMediaData['ctr'];
									$quantity = $itemMediaData['quantity'];
									$days = $itemMediaData['days'];
								} else if (in_array($itemMediaTypeList['map_media_ordinal'], [134, 135, 136])) {
									$website = str_replace('代操', '', $itemMediaData['website']);
								} else {
									$website = $itemMediaData['website'];
									$position = $itemMediaData['position'];
									$format1 = str_replace("\\n", "\n", $itemMediaData['format1']);
									$format2 = str_replace("\\n", "\n", $itemMediaData['format2']);
									$quantity2 = $itemMediaData['quantity2'];
									$ctr = $itemMediaData['ctr'];
									$quantity = $itemMediaData['quantity'];
									$days = $itemMediaData['days'];
								}

								$totalPrice += $itemMediaData['totalprice'];
								SetExcelCellValue($objPHPExcel, [
									'A'. $idxExcelRow => $website,
									'B'. $idxExcelRow => $itemMediaData['channel'],
									'C'. $idxExcelRow => $itemMediaData['phonesystem'],
									'D'. $idxExcelRow => $position,
									'E'. $idxExcelRow => trim($format1),
									'F'. $idxExcelRow => trim($format2),
									'H'. $idxExcelRow => $itemMediaData['wheel'],
									'I'. $idxExcelRow => number_format($quantity2),
									'J'. $idxExcelRow => $ctr .'%',
									'K'. $idxExcelRow => number_format($quantity),
									'L'. $idxExcelRow => $duration,
									'M'. $idxExcelRow => $days,
									'N'. $idxExcelRow => number_format($price),
									'O'. $idxExcelRow => $itemMediaData['due'],
									'P'. $idxExcelRow => $usdjpy .'$'. number_format($itemMediaData['totalprice'] / $rate),
									'Q'. $idxExcelRow => trim($other),
								]);
							} else if ($typeName == 'CPV') {
								SetExcelCellLeft($excelActiveSheet, [
									"E$idxExcelRow:F$idxExcelRow",
									"Q$idxExcelRow",
								]);

								$totalPrice += $itemMediaData['totalprice'];
								SetExcelCellValue($objPHPExcel, [
									'A'. $idxExcelRow => ($itemMediaTypeList['map_media_ordinal'] == 119 || $itemMediaTypeList['map_media_ordinal'] == 121) ? 'JS NATIVE SOLUTION' : $itemMediaData['website'],
									'B'. $idxExcelRow => $itemMediaData['channel'],
									'C'. $idxExcelRow => $itemMediaData['phonesystem'],
									'D'. $idxExcelRow => $itemMediaData['position'],
									'E'. $idxExcelRow => trim(str_replace("\\n", "\n", $itemMediaData['format1'])),
									'F'. $idxExcelRow => trim(str_replace("\\n", "\n", $itemMediaData['format2'])),
									'H'. $idxExcelRow => $itemMediaData['wheel'],
									'I'. $idxExcelRow => number_format($itemMediaData['quantity2']),
									'J'. $idxExcelRow => $itemMediaData['ctr'] .'%',
									'K'. $idxExcelRow => number_format($itemMediaData['quantity']),
									'L'. $idxExcelRow => $duration,
									'M'. $idxExcelRow => $itemMediaData['days'],
									'N'. $idxExcelRow => $usdjpy .'$'. number_format($price / $rate),
									'O'. $idxExcelRow => $itemMediaData['due'],
									'P'. $idxExcelRow => $usdjpy .'$'. number_format($itemMediaData['totalprice'] / $rate),
									'Q'. $idxExcelRow => trim($itemMediaData['others']),
								]);
							} else if ($typeName == 'WebAD') {
								SetExcelCellLeft($excelActiveSheet, [
									"D$idxExcelRow:E$idxExcelRow",
								]);
								MergeExcellCell($excelActiveSheet, [
									'G'. $idxExcelRow .':H'. $idxExcelRow,
									'I'. $idxExcelRow .':J'. $idxExcelRow,
									'K'. $idxExcelRow .':L'. $idxExcelRow,
									'M'. $idxExcelRow .':N'. $idxExcelRow,
								]);

								$totalPrice += $itemMediaData['totalprice'];
								SetExcelCellValue($objPHPExcel, [
									'A'. $idxExcelRow => $itemMediaData['website'],
									'B'. $idxExcelRow => $itemMediaData['channel'],
									'C'. $idxExcelRow => $itemMediaData['position'],
									'D'. $idxExcelRow => trim(str_replace("\\n", "\n", $itemMediaData['format1'])),
									'E'. $idxExcelRow => trim(str_replace("\\n", "\n", $itemMediaData['format2'])),
									'F'. $idxExcelRow => $itemMediaData['wheel'],
									'G'. $idxExcelRow => number_format($itemMediaData['quantity2']),
									'I'. $idxExcelRow => $itemMediaData['ctr'],
									'K'. $idxExcelRow => number_format($itemMediaData['quantity']),
									'M'. $idxExcelRow => $itemMediaData['due'],
									'O'. $idxExcelRow => $duration,
									'P'. $idxExcelRow => $itemMediaData['days'],
									'Q'. $idxExcelRow => $usdjpy .'$'. number_format($itemMediaData['totalprice'] / $rate),
								]);
							} else if ($typeName == 'CPA') {
								SetExcelCellLeft($excelActiveSheet, [
									"D$idxExcelRow:E$idxExcelRow"
								]);

								$totalPrice += $itemMediaData['totalprice'];
							}

							$idxExcelRow++;
						}
					}

					$idxExcelRow++;
				}
			}

			$excelActiveSheet->getStyle('A'. $idxExcelMediaDataStart.':Q'. ($idxExcelRow - 1))->getFont()->setSize(5.5);
		// 2018-03-22 (Jimmy): END - 搜尋對外CUE媒體詳細資料
		

		// 2018-03-22 (Jimmy): BEGIN - 簽名區塊
			$columnBorderStyle = [
				'A'. ($idxExcelRow + 0) .':'. 'A'. ($idxExcelRow + 1) => 'all',
				'B'. ($idxExcelRow + 0) .':'. 'F'. ($idxExcelRow + 1) => 'all',
				'A'. ($idxExcelRow + 2) .':'. 'A'. ($idxExcelRow + 3) => 'all',
				'B'. ($idxExcelRow + 2) .':'. 'F'. ($idxExcelRow + 3) => 'all',
				'A'. ($idxExcelRow + 4) .':'. 'A'. ($idxExcelRow + 5) => 'all',
				'B'. ($idxExcelRow + 4) .':'. 'F'. ($idxExcelRow + 5) => 'all',
				'A'. ($idxExcelRow + 7) .':'. 'A'. ($idxExcelRow + 8) => 'all',
				'B'. ($idxExcelRow + 7) .':'. 'F'. ($idxExcelRow + 8) => 'all',
				'A'. ($idxExcelRow + 9) .':'. 'A'. ($idxExcelRow + 10) => 'all',
				'B'. ($idxExcelRow + 9) .':'. 'C'. ($idxExcelRow + 10) => 'all',
				'D'. ($idxExcelRow + 9) .':'. 'D'. ($idxExcelRow + 10) => 'all',
				'E'. ($idxExcelRow + 9) .':'. 'F'. ($idxExcelRow + 10) => 'all',
				'A'. ($idxExcelRow + 11) .':'. 'A'. ($idxExcelRow + 12) => 'all',
				'B'. ($idxExcelRow + 11) .':'. 'F'. ($idxExcelRow + 12) => 'all',
			];
			
			MergeExcellCell($excelActiveSheet, array_keys($columnBorderStyle));
			SetExcellCellBorder($excelActiveSheet, $columnBorderStyle);
			SetExcelCellValue($objPHPExcel, [
				'A'. ($idxExcelRow + 0) => "委刊客戶\n負責人簽署",
				'A'. ($idxExcelRow + 2) => "委刊客戶\n經辦人簽署",
				'A'. ($idxExcelRow + 4) => "委刊客戶\n公司印鑑",
				'A'. ($idxExcelRow + 7) => "傑思愛德威媒體\n負責人簽署",
				'A'. ($idxExcelRow + 9) => "業務行銷\n總監簽署",
				'D'. ($idxExcelRow + 9) => "業務行銷\n簽署",
				'A'. ($idxExcelRow + 11) => "傑思愛德威媒體\n公司印鑑",
			]);
			
			$excelActiveSheet->getStyle('A'. $idxExcelRow.':F'. ($idxExcelRow + 13))->getFont()->setSize(6);
			$excelActiveSheet->getStyle('A'. $idxExcelRow.':F'. ($idxExcelRow + 13))->getAlignment()->setWrapText(true);
			$excelActiveSheet->getStyle('A'. $idxExcelRow.':F'. ($idxExcelRow + 13))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$excelActiveSheet->getStyle('A'. $idxExcelRow.':F'. ($idxExcelRow + 13))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		// 2018-03-22 (Jimmy): END - 簽名區塊

		// 2018-03-22 (Jimmy): BEGIN - 價錢與條款區塊
			$columnBorderStyle = [
				'H'. ($idxExcelRow + 0) .':'. 'P'. ($idxExcelRow + 0) => 'all',
				'H'. ($idxExcelRow + 1) .':'. 'P'. ($idxExcelRow + 2) => 'all',
				'H'. ($idxExcelRow + 3) .':'. 'I'. ($idxExcelRow + 3) => 'all',
				'J'. ($idxExcelRow + 3) .':'. 'K'. ($idxExcelRow + 3) => 'all',
				'L'. ($idxExcelRow + 3) .':'. 'N'. ($idxExcelRow + 3) => 'all',
				'O'. ($idxExcelRow + 3) .':'. 'P'. ($idxExcelRow + 3) => 'all',
			];

			MergeExcellCell($excelActiveSheet, array_keys($columnBorderStyle) + [count($columnBorderStyle) + $typeInfo['extend'] => 'H'. ($idxExcelRow + 5) .':'. 'P'. ($idxExcelRow + 13)]);
			SetExcellCellBorder($excelActiveSheet, $columnBorderStyle);

			SetExcelCellValue($objPHPExcel, [
				'H'. ($idxExcelRow + 0) => "總廣告預算金額",
				'H'. ($idxExcelRow + 1) => 'NT$'. number_format($totalPrice),
				'H'. ($idxExcelRow + 3) => "稅金(5%)",
				'J'. ($idxExcelRow + 3) => 'NT$'. number_format(($totalPrice * 0.05)),
				'L'. ($idxExcelRow + 3) => "Gross Cost (含稅價)",
				'O'. ($idxExcelRow + 3) => 'NT$'. number_format(($totalPrice * 1.05)),
				'H'. ($idxExcelRow + 5) => "《 Remark 》\n1 )  請詳細檢查上表正確無誤後於托播前簽章回傳至 02-6636-0166\n2 )  廣告素材如以Flash形式表現，製作規範可到http://cony.nicecampaign.com/JS_formal_of_sozai/JS formal of sozai.htm 查詢，並須提供原始檔(.fla)\n3 )  廣告素材格式請以Gif, Jpg, Flash為主，文字格式為text\n4 )  以CPC模式採購之媒體，預估曝光數及CTR均為參考值，不保證一定達到。\n5 )  以CPI或CPA模式採購之媒體，預估點擊數、曝光數及CTR均為參考值，不保證一定達到。\n6 )  所有素材得至少見刊前一天16:00前提供確認無誤後之檔案，始能上稿，若超過時限，恕無法刊登\n7 )  傑思愛德威媒體保留最後廣告刊登之權利。",
			]);
			
			$excelActiveSheet->getStyle('H'. ($idxExcelRow + 0))->getFont()->setSize(8);
			$excelActiveSheet->getStyle('H'. ($idxExcelRow + 1))->getFont()->setSize(14);
			$excelActiveSheet->getStyle('H'. ($idxExcelRow + 1))->getFont()->getColor()->setRGB('FF0000');
			$excelActiveSheet->getStyle('H'. ($idxExcelRow + 0) .':P'. ($idxExcelRow + 5))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$excelActiveSheet->getStyle('H'. ($idxExcelRow + 0) .':P'. ($idxExcelRow + 5))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			
			$excelActiveSheet->getStyle('H'. ($idxExcelRow + 3) .':P'. ($idxExcelRow + 3))->getFont()->setSize(6);
			$excelActiveSheet->getStyle('J'. ($idxExcelRow + 3))->getFont()->setSize(8);
			$excelActiveSheet->getStyle('O'. ($idxExcelRow + 3))->getFont()->setSize(8);
			
			$excelActiveSheet->getStyle('H'. ($idxExcelRow + 5))->getFont()->setSize(7.5);
			$excelActiveSheet->getStyle('H'. ($idxExcelRow + 5))->getAlignment()->setWrapText(true);
			$excelActiveSheet->getStyle('H'. ($idxExcelRow + 5))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$excelActiveSheet->getStyle('H'. ($idxExcelRow + 5))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
		// 2018-03-22 (Jimmy): END - 價錢與條款區塊

		$excelActiveSheet->setSelectedCells('A1');
		
		if (empty($row2['agency_id'])) {
			$objAgencyClient = Createobject('Client', $row2['client_id']);
		} else {
			$objAgencyClient = Createobject('Agency', $row2['agency_id']);
		}

		$xlsFilename = $objAgencyClient->getVar('name2') .'_'. $row2['name'] .'_'. date('md', $row2['date11']) .'-'. date('md', $row2['date22']) .'_'. (empty($row2['times']) ? date('ymd', $row2['time']) : date('ymd', $row2['times']));
		SendExcellFile($objPHPExcel, $xlsFilename, isWindowsPlatform() ? '.xlsx' : '.xls');
	}

	ShowMessageAndRedirect('無效的連結', '../');
