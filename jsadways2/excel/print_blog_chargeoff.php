<?php

	set_time_limit(-1);
	ini_set( "memory_limit", "256M");	
	require_once dirname(dirname(__DIR__)) .'/autoload.php';

	IncludeFunctions('jsadways');
	IncludeFunctions('excel');

	$campaignSerial = GetVar('campaign_serial');
	$startTime = GetVar('start_time');
	$endTime = GetVar('end_time');
	$selectWomm = GetVar('SelectWomm');
	$bloggerId = GetVar('blogger_id');

	$objBlogger = CreateObject('Blogger');
	$objBloggerBank = CreateObject('BloggerBank');
	$objBloggerChargeoff = CreateObject('BloggerChargeoff');

	$objPHPExcel = CreateExcelFile();
	$objPHPExcel->getDefaultStyle()->getFont()->setName('新細明體');
	$objPHPExcel->getDefaultStyle()->getFont()->setSize(12);

	$excelActiveSheet = &$objPHPExcel->getActiveSheet();
	$excelActiveSheet->getDefaultColumnDimension()->setWidth(18);

	$number = 1;
	$fields = ['A' => '委刊號碼', 'B' => '代理商', 'C' => '客戶', 'D' => '案件名稱', 'E' => '走期', 'F' => '發票總額', 'G' => '發票月份', 'H' => '媒體別', 
				'I' => '口碑PM', 'J' => '媒體報價', 'K' => '寄送費用', 'L' => '寫手報價', 'M' => '稿酬', 'N' => '含稅價', 'O' => '備註欄位', 'P' => '寫手編號', 
				'Q' => '寫手姓名', 'R' => '匯款金額', 'S' => '匯款日期', 'T' => '銀行戶名', 'U' => '銀行', 'V' => '存款帳號', 'W' => '部落格名字', 'X' => '扣費身分查詢'];
	$size = ['A' => 10, 'B' => 25, 'C' => 25, 'D' => 30, 'E' => 15, 'F' => 10, 'G' => 20, 'H' => 15, 
			'I' => 15, 'J' => 15, 'K' => 15, 'L' => 15, 'M' => 15, 'N' => 25, 'O' => 15, 'P' => 11, 
			'Q' => 15, 'R' => 15, 'S' => 20, 'T' => 25, 'U' => 15, 'V' => 15, 'W' => 15, 'X' => 15];

	$poniterGetter = &$objPHPExcel->getActiveSheet();
	$poniterSetter = &$objPHPExcel->setActiveSheetIndex(0);
	foreach ($size as $column => $width) {
		$poniterGetter->getColumnDimension($column)->setWidth($width);
		SetExcellCellBorder($poniterGetter, [$column . $number => 'all']);
		SetExcelCellCenter($poniterGetter, [$column . $number]);
		$poniterSetter->setCellValue($column . $number, $fields[$column]);
	}

	$searchOrder = '';
	$searchCondition = '1=1';
	$searchJoin = " LEFT JOIN `campaign` ON `blogger_chargeoff`.`campaign_id` = `campaign`.`id` 
					LEFT JOIN `blogger` ON `blogger_chargeoff`.`blogger_id` = `blogger`.`id` 
					LEFT JOIN `media19_detail` ON `blogger_chargeoff`.`blogger_detail_id` = `media19_detail`.`id` ";
	$searchFields = "`blogger_chargeoff`.*,
					`campaign`.`name` as `campaign_name`, `campaign`.`idnumber` as `campaign_idnumber`,
					`campaign`.`agency`, `campaign`.`client`, `campaign`.`womm`,
					`campaign`.`date11` as `campaign_start`,
					`campaign`.`date22` as `campaign_end`,
					`blogger`.`ac_id`, `blogger`.`payment_method`, `blogger`.`idnumber` as `blogger_idnumber`, `blogger`.`main_bank_id`, 
					IF (`blogger`.`true_name` = '', IF (`blogger`.`display_name` = '', IF (`blogger`.`blog_name` = '', IF (`blogger`.`fb_name` = '', IF (`blogger`.`ig_name` = '', IF (`blogger`.`youtube_name` = '', '', `blogger`.`youtube_name`), `blogger`.`ig_name`), `blogger`.`fb_name`), `blogger`.`blog_name`), `blogger`.`display_name`), `blogger`.`true_name`) as `display_name`,
					`media19_detail`.`blog2` AS `platform_blogger_name`, `media19_detail`.`blog` AS `platform_blogger_name_bak`, `media19_detail`.`price2` as `chargeoff_price` ";

	if ($serialnumber) {
		$searchOrder = 'ORDER BY blogger_chargeoff.blogger_id ASC, campaign.wommId ASC';
		$searchOrderBy = ['`blogger_chargeoff`.`blogger_id`', '`campaign`.`wommId`'];
		$searchOrderDir = ['ASC', 'ASC'];
		$searchCondition .= " AND blogger_chargeoff.campaign_id IN ( SELECT `id` FROM `campaign` WHERE `idnumber` = ". SqlQuote($campaignSerial) ." ) ";
	} else if ($startTime && $endTime) {
		$searchOrder = 'ORDER BY campaign.wommId ASC';
		$searchOrderBy = ['`campaign`.`wommId`'];
		$searchOrderDir = ['ASC'];
		$searchCondition .= " AND blogger_chargeoff.`chargeoff_date` >= ". SqlQuote(date('Y-m-01', strtotime($startTime))) ." AND blogger_chargeoff.`chargeoff_date` <= ". SqlQuote(date('Y-m-t', strtotime($endTime)));
	} else {
		echo '請輸入搜尋條件';
		exit();
	}

	if (IsId($bloggerId)) {
		$searchCondition .= " AND blogger_chargeoff.`blogger_id` = $bloggerId ";
	}

	if (IsId($selectWomm)) {
		$searchCondition .= " AND campaign.`wommId` = $selectWomm ";
	}
	
	$data = [];
	foreach ($objBloggerChargeoff->searchAll($searchCondition, $searchOrderBy, $searchOrderDir, '', $searchJoin, $searchFields) as $idxCharge => $itemCharge) {
		$bankDetail = [];

		if (IsId($itemCharge['bankId'])) {
			$objBloggerBank->load($itemCharge['bankId']);
			$bankDetail = $objBloggerBank->fields;
		} else if (IsId($itemCharge['main_bank_id'])) {
			$objBloggerBank->load($itemCharge['main_bank_id']);
			$bankDetail = $objBloggerBank->fields;
		} else {
			if ($rowsBank = $objBloggerBank->search(sprintf("`states` = 1 AND `blogger_id` = %d", $itemCharge['blogger_id']), '', '', '', 0, 0, '', 'MIN(`id`) AS `id`')) {
				$objBloggerBank->load($rowsBank[0]['id']);
				$bankDetail = $objBloggerBank->fields;
			}
		}

		$campaignDuration = date("m", $itemCharge["campaign_start"]) .'-'. date("m", $itemCharge["campaign_end"]) .'月';
		$chargeOffDate = date("Y-m", strtotime($itemCharge["chargeoff_date"]));

		// 匯款日期
		$chargOffDateFul = date("Y-m-01", strtotime($itemCharge["chargeoff_date"] .' + 1 months'));
		$chargOffDateFul = date("Y-m-d", strtotime($chargOffDateFul .' -1 days'));

		$bloggerBankAccountName = $bankDetail["bankUserName"];
		$bloggerBankName = $bankDetail['bankName'];
		$bloggerBackAccountId = $bankDetail['bankAC'];
		$bloggerIdNumber = empty($bankDetail['bankIdNum']) ? $itemCharge['blogger_idnumber'] : $bankDetail['bankIdNum'];
		if (in_array($bankDetail['account_payment_method'], Blogger::PAYMENT_METHOD)) {
			if ($bankDetail['account_payment_method'] == Blogger::PAYMENT_METHOD['tax_excluded_with_invoice']) {
				$invoicePrice = round($itemCharge['price'] * getInfuencerPriceRate('inner_tax_included', $bankDetail['account_payment_method']));
			} else {
				$invoicePrice = round($itemCharge['price']);	
			}
		} else {
			if ($itemCharge['payment_method'] == Blogger::PAYMENT_METHOD['tax_excluded_with_invoice']) {
				$invoicePrice = round($itemCharge['price'] * getInfuencerPriceRate('inner_tax_included', $itemCharge['payment_method']));
			} else {
				$invoicePrice = round($itemCharge['price']);	
			}
		}

		$data[] = [
			$itemCharge["campaign_idnumber"],
			$itemCharge['agency'],
			$itemCharge['client'],
			$itemCharge['campaign_name'],
			$campaignDuration,
			'',
			'',
			'JS-寫手口碑操作',
			$itemCharge["womm"],
			'',
			'',
			'number://'. $itemCharge['chargeoff_price'],
			'number://'. $itemCharge['price'],
			'number://'. $invoicePrice,
			$itemCharge['remark'],
			$itemCharge["ac_id"],
			$itemCharge["display_name"],
			'',
			$chargOffDateFul,
			$bloggerBankAccountName,
			$bloggerBankName,
			$bloggerBackAccountId,
			empty($itemCharge['platform_blogger_name']) ? $itemCharge['platform_blogger_name_bak'] : $itemCharge['platform_blogger_name'],
			$bloggerIdNumber,
		];

		$number++;
		foreach (['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'N', 'R', 'S', 'T'] as $column) {
			SetExcelCellCenter($poniterGetter, [$column . $number]);
		}
	}

	SetExcellCellFromArray($objPHPExcel, $data, 0, 2);

	$sheetName = date("Y-m", strtotime($_GET['start_time'])) .' - '. date("Y-m", strtotime($_GET['end_time'])) .'寫手出帳清單';
	$objPHPExcel->getActiveSheet()->setTitle($sheetName);
	$objPHPExcel->setActiveSheetIndex(0);

	SendExcellFile($objPHPExcel, $sheetName);
	