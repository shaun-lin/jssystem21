<?php
	
	require_once dirname(__DIR__) .'/autoload.php';

	require_once __DIR__ .'/include/function.inc.php';
	
	$db = clone($GLOBALS['app']->db);
	require_once 'mtype_CPA_definition.php';

	$TypeItem = $_POST['SelectType'];

	if (array_key_exists($_POST['SelectCategory'], $mediaCategoryDefinition)) {
		$channel = $mediaCategoryDefinition[$_POST['SelectCategory']]['categoryName'];
	} else {
		$channel = '';
	}

	if (array_key_exists($_POST['SelectSubCategory'], $mediaSubCategoryDefinition)) {
		$phonesystem = $mediaSubCategoryDefinition[$_POST['SelectSubCategory']]['subCategoryName'];
	}

	if (array_key_exists($_POST['SelectThreeCategory'], $mediaThreeCategoryDefinition)) {
		$position = $mediaThreeCategoryDefinition[$_POST['SelectThreeCategory']]['threeCategoryName'];
	} else {
		$position = '';
	}
	
	for ($i=1; $i<=10; $i++) {
		if ($_POST['date'. $i] != NULL) {
			$date[$i] = mktime (0, 0, 0, substr($_POST['date'.$i], 0, 2), substr($_POST['date'. $i], 3, 2), substr($_POST['date'. $i], 6, 4));
		} else {
			$date[$i] = 0;
		}
	}

	$number2 = '';
	if ($_GET['cue'] == 2) {
		$a0 = $_GET['mediaid'];
		$a1 = $_POST['a1'];
		$a2 = $_POST['a2'];
		$a3 = $_POST['a3'];
		$a4 = $_POST['a4'];

		$sqlMedia = sprintf("SELECT * FROM `media` WHERE `id` = %d;", $mediaOrdinal);
		$db->query($sqlMedia);
		$rowMedia = $db->next_record();

		$profit = ($_POST['totalprice'] * $rowMedia['profit']) / 100;
		
		if ($a3 < $profit) {
			$a5 = '1';
		} else {
			$a5 = '0';
		}
	} else {
		$number2 = ",  quantity2 = '". $_POST['number2'];
	}
	
	$sql1 = sprintf("SELECT * FROM `campaign` WHERE `id` = %d;", $_GET['campaign']);
	$db->query($sql1);
	$row1 = $db->next_record();

	if ($row1['status'] == 5) {
		$sql3 = sprintf("SELECT * FROM `media%d` WHERE `id` = %d;", $mediaOrdinal, $_GET['id']);
		$db->query($sql3);
		$row3 = $db->next_record();

		$data = '';
		if ($row3['totalprice'] != $_POST['totalprice']) {
			$data = $mediaName .'媒體總金額由'. $row3['totalprice'] .'改成'. $_POST['totalprice'] .'<br />';
		}

		for ($i=1; $i<=10; $i++) {
			if ($row3['date'. $i] != $date[$i]) {
				$data .= $mediaName .'媒體走期'. ((int)(($i + 1) / 2)) .'由'. date('Ymd', $row3['date'. $i]) .'改成'. date('Ymd', $date[$i]) .'<br />';
			}
		}

		if ($data == NULL) {
			$data = $mediaName .'媒體我也不知道改了什麼，有可能是版位喔';
		}

		$sql2 = "INSERT INTO `campaignstatus2` (`name`, `data`, `times`, `campaignid`) 
				VALUES (". $db->quote($_SESSION['username']) .", ". $db->quote($data) .", ". time() .", ". ((int)$_GET['campaign']) .");";
		$db->query($sql2);
	}

	$updateData = [
		'channel' => $channel,
		'phonesystem' => $phonesystem,
		'position' => $position,
		'format1' => GetVar("format1"),
		'format2' => GetVar("format2"),
		'wheel' => GetVar("wheel"),
		'ctr' => GetVar("ctr"),
		'date1' => $date[1],
		'date2' => $date[2],
		'date3' => $date[3],
		'date4' => $date[4],
		'date5' => $date[5],
		'date6' => $date[6],
		'date7' => $date[7],
		'date8' => $date[8],
		'date9' => $date[9],
		'date10' => $date[10],
		'days' => GetVar("days"),
		'due' => GetVar("due"),
		'quantity' => GetVar("number1"),
		'quantity2' => GetVar("number2"),
		'totalprice' => GetVar('totalprice'),
		'days1' => GetVar("days1"),
		'days2' => GetVar("days2"),
		'days3' => GetVar("days3"),
		'days4' => GetVar("days4"),
		'days5' => GetVar("days5"),
		'price1' => GetVar("price1"),
		'price2' => GetVar("price2"),
		'price3' => GetVar("price3"),
		'price4' => GetVar("price4"),
		'price5' => GetVar("price5"),
		'totalprice1' => GetVar("totalprice1"),
		'totalprice2' => GetVar("totalprice2"),
		'totalprice3' => GetVar("totalprice3"),
		'totalprice4' => GetVar("totalprice4"),
		'totalprice5' => GetVar("totalprice5"),
		'click1' => GetVar("click1"),
		'click2' => GetVar("click2"),
		'click3' => GetVar("click3"),
		'click4' => GetVar("click4"),
		'click5' => GetVar("click5"),
		'impression1' => GetVar("impression1"),
		'impression2' => GetVar("impression2"),
		'impression3' => GetVar("impression3"),
		'impression4' => GetVar("impression4"),
		'impression5' => GetVar("impression5"),
		'times' => time(),
		'others' => GetVar("others"),
		'a1' => GetVar('a1'),
		'a2' => GetVar('a2'),
		'a3' => GetVar('a3'),
		'a4' => GetVar('a4'),
		'a5' => $a5,
		'items2' => $TypeItem,
		'items3' => GetVar("SelectSystem"),
	];

	if ($_POST['gearing'] == 1) {
		$sql_campaign = sprintf("SELECT * FROM `media%d` WHERE `campaign_id` = %d;", $mediaOrdinal, $_GET['campaign']);
		
		$db->query($sql_campaign);
		$campaign_row = $db->get_num_rows();

		if ($campaign_row == 2) {
			$fieldsStatement = [];

			foreach ($updateData as $dataName => $dataVar) {
				if (is_numeric($dataVar)) {
					$fieldsStatement[] = "`{$dataName}` = ". $dataVar;
				} else {
					$fieldsStatement[] = "`{$dataName}` = ". $db->quote($dataVar);
				}
			}

			$sql2 = "UPDATE `media". $mediaOrdinal ."` SET ". implode(', ', $fieldsStatement) ." WHERE `campaign_id`	= ". ((int)$_GET['campaign']) .";";
			$db->query($sql2);
		}
	}
	
	if ($_GET['cue'] == 2) {
		unset($updateData['quantity2']);
	}
	
	$fieldsStatement = [];

	foreach ($updateData as $dataName => $dataVar) {
		if (is_numeric($dataVar)) {
			$fieldsStatement[] = "`{$dataName}` = ". $dataVar;
		} else {
			$fieldsStatement[] = "`{$dataName}` = ". $db->quote($dataVar);
		}
	}

	$sql2 = "UPDATE `media". $mediaOrdinal ."` SET ". implode(', ', $fieldsStatement) ." WHERE `id`	= ". ((int)$_GET['id']) .";";
	$db->query($sql2);

	ShowMessageAndRedirect('修改媒體成功', 'campaign_view.php?id='. $_GET['campaign'], false);
	