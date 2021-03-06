<?php
	
	require_once dirname(__DIR__) .'/autoload.php';

	require_once __DIR__ .'/include/function.inc.php';
	
	$db = clone($GLOBALS['app']->db);
	// require_once __DIR__ .'/media'. str_replace(['media', '_add2.php'], ['', ''], basename(__FILE__)) .'_definition.php';
	require_once __DIR__ .'/media150_definition.php';
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

	if (isset($_POST['pr']) && $_POST['pr'] == 1) {
		$website = "{$mediaName}(PR)";
		$totalprice = 0;
	} else {
		$website = $mediaName;
		$totalprice = $_POST['totalprice'];
	}

	for ($i=1; $i<=10; $i++) {
		if (isset($_POST['date'. $i]) && $_POST['date'. $i]) {
			$date[$i] = mktime (0, 0, 0, substr($_POST['date'. $i], 0, 2), substr($_POST['date'. $i], 3, 2), substr($_POST['date'. $i], 6, 4));
		} else {
			$date[$i] = 0;
		}
	}

	$sqlMedia = sprintf("SELECT * FROM `media` WHERE `id` = %d;", $mediaOrdinal);
	echo ($mediaOrdinal);
	$db->query($sqlMedia);
	$rowMedia = $db->next_record();

	$profit = ($_POST['totalprice'] * $rowMedia['profit']) / 100;
	if ($_GET['cue'] == 2) {
		$a = $_GET['media2'];
		$a0 = $_GET['mediaid'];
		$a1 = $_POST['a1'];
		$a2 = $_POST['a2'];
		$a3 = $_POST['a3'];
		$a4 = $_POST['a4'];
		if ($a3 < $profit) {
			$a5 = '1';
		}
	}

	$fieldsNameForCue1 = [];
	$fieldsVarForCue1 = [];

	$addDataForCue1 = [
		'campaign_id' => GetVar('id'),
		'cue' => GetVar('cue'),
		'website' => $website,
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
		'totalprice' => $totalprice,
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
		'a' => $a,
		'a0' => $a0,
		'a1' => $a1,
		'a2' => $a2,
		'a3' => $a3,
		'a4' => $a4,
		'a5' => $a5,
		'items2' => $TypeItem,
		'items3' => GetVar("SelectSystem"),
	];
	print_r($addDataForCue1);

	$sql2 = GenSqlFromArray($addDataForCue1, "media$mediaOrdinal", 'insert');

	$db->query($sql2);
	// AddMediaMapping(__FILE__, $_GET['id'], $db->get_last_insert_id());
	AddMediaMapping('media150', $_GET['id'], $db->get_last_insert_id());

	if ($_POST['samecue'] == 1) {
		$sql1 = sprintf("SELECT * FROM `campaign` WHERE `id` = %d;", $_GET['id']);
		$db->query($sql1);
		$row1 = $db->next_record();

		$sqlnew = sprintf("SELECT * FROM `media%d` ORDER BY `id` DESC LIMIT 1;", $mediaOrdinal);
		$db->query($sqlnew);
		$rownew = $db->next_record();

		if ($row1['agency_id'] != 0) {
			$sql4 = sprintf("SELECT * FROM `commission` WHERE `agency` = %d AND `media` = %d;", $row1['agency_id'], $_GET['media']);
			$db->query($sql4);
			$row4 = $db->next_record();

			if ($row4['commission5'] != 0) {
				$commission1 = ($_POST['totalprice'] * $row4['commission1']) / 100;
				$commission4 = ($_POST['totalprice'] * $row4['commission4']) / 100;
			} else {
				$commission1 = 0;
				$commission4 = 0;
			}
		} else {
			$commission1 = 0;
			$commission4 = 0;
		}
		
		$fieldsNameForCue2 = [];
		$fieldsVarForCue2 = [];
		$addDataForCue2 = $addDataForCue1;

		$addDataForCue2['cue'] = 2;
		$addDataForCue2['a'] = $_GET['media'];
		$addDataForCue2['a0'] = $rownew['id'];
		$addDataForCue2['a1'] = $commission1;
		$addDataForCue2['a2'] = $commission4;
		$addDataForCue2['a3'] = $profit;
		$addDataForCue2['a4'] = $_POST['totalprice'] - $commission1 - $commission4 - $profit;

		$sql2 = GenSqlFromArray($addDataForCue2, "media$mediaOrdinal", 'insert');
		$db->query($sql2);
		AddMediaMapping(__FILE__, $_GET['id'], $db->get_last_insert_id());
	}
	
	$sql1 = sprintf("SELECT * FROM `campaign` WHERE `id` = %d;", $_GET['id']);
	$db->query($sql1);
	$row1 = $db->next_record();

	if ($row1['status'] == 5) {
		$sql2 = "INSERT INTO `campaignstatus2` (`name`, `data`, `times`, `campaignid`) 
				VALUES (". $db->quote($_SESSION['username']) .", ". $db->quote('新增'. $mediaName .'媒體') .", ".time() .", ". ((int)$_GET['id']) .");";
		$db->query($sql2);
	}
	$cp_id = $_GET['id'];
	$media_id = $_GET['mediaid'];
	$item_id = $_GET['itemid'];
	$mtype_name = $_GET['mtypename'];
	$mtype_number = $_GET['mtypenumber'];
	$mtype_id = $_GET['mtypeid'];

	$goon=GetVar('goon');
	

	$sql3 = "INSERT INTO `cp_detail`( `cp_id`, `media_id`, `comp_id`, `item_id`, `mtype_name`, `mtype_number`, `mtype_id`) VALUES ('".$cp_id."','".$media_id."','0','".$item_id."','".$mtype_name."','".$mtype_number."','".$mtype_id."')";
	$db->query($sql3);
		if ($goon=="Y") {

		$arrItems=array();
				$arrItems[]=array("key"=>"result","name"=>"OK");
		
		echo json_encode($arrItems);
	}else{
	ShowMessageAndRedirect('新增媒體成功', 'campaign_view.php?id='. $_GET['id'], false);
	}