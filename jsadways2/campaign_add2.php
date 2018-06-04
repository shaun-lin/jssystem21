<?php
	
	// 2018-02-22 (Jimmy): 傑思jsadways2/campaign_add2.php, 香港jsadways2hk/campaign_add2.php, 豐富媒體jsadways2ff/campaign_add2.php 共用此檔案
	require_once dirname(__DIR__) .'/autoload.php';

	$db = clone($GLOBALS['app']->db);

	if (isset($_POST['agency']) && IsId($_POST['agency'])) {
		$objAgency = CreateObject('Agency', $_POST['agency']);
		$agency1 = $objAgency->getVar('name');
		$agency2 = $_POST['agency'];
	} else {
		$agency1 = "";
		$agency2 = 0;
	}
	
	$objClient = CreateObject('Client', $_POST['client']);

	$date11 = mktime(0, 0, 0, substr($_POST['date1'], 0, 2), substr($_POST['date1'], 3, 2), substr($_POST['date1'], 6, 4));
	$date22 = mktime(0, 0, 0, substr($_POST['date2'], 0, 2), substr($_POST['date2'], 3, 2), substr($_POST['date2'], 6, 4));
	
	$is_jp = isset($_POST['is_jp']) && $_POST['is_jp'] ? "1" : "0";

	if (isset($_POST['SelectWomm']) && $_POST['SelectWomm']) {
		list($wommUserId, $wommUserName) = explode(',', $_POST['SelectWomm']);
	} else {
		$wommUserId = 0;
		$wommUserName = '';
	}

	$insertData = [
		'name' => $_POST['name'],
		'agency' => $agency1,
		'agency_id' => $agency2,
		'client' => $objClient->getVar('name'),
		'client_id' => $_POST['client'],
		'date1' => $_POST['date1'],
		'date2' => $_POST['date2'],
		'date11' => $date11,
		'date22' => $date22,
		'member' => $_SESSION['username'],
		'memberid' => $_SESSION['userid'],
		'status' => 1,
		'sex' => $_SESSION['sex'],
		'pay1' => $_POST['pay1'],
		'pay2' => $_POST['pay2'],
		'receipt1' => $_POST['receipt1'],
		'receipt2' => $_POST['receipt2'],
		'title' => $_POST['title'],
		'contact1' => $_POST['contact1'],
		'contact2' => $_POST['contact2'],
		'contact3' => $_POST['contact3'],
		'time' => time(),
		'others' => $_POST['others'],
		'version' => 2,
		'is_jp' => $is_jp,
		'wommId' => $wommUserId,
		'womm' => $wommUserName
	];

	$mediaLeaderId = GetVar('media_leader');
	if (IsId($mediaLeaderId)) {
		$objMrbsUsers = CreateObject('MrbsUsers', $mediaLeaderId);
		if ($objMrbsUsers->getVar('departmentid') != 21 && $objMrbsUsers->getVar('departmentid') != 22) {
			$mediaLeaderId = 0;
		}

		unset($objMrbsUsers);
	}

	$insertData += [
		'media_leader' => $mediaLeaderId
	];
	
	$db->query(GenSqlFromArray($insertData, 'campaign', 'insert'));
	
	$campaignId = $db->get_last_insert_id();

	if (IsId($campaignId)) {
		ShowMessageAndRedirect('新增案件成功', 'campaign_view.php?id='. $campaignId, false);
	} else {
		ShowMessageAndRedirect('新增案件成功', 'campaign_list.php', false);	
	}
