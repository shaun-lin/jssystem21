<?php

	require_once dirname(__DIR__) .'/autoload.php';
	
	IncludeFunctions('jsadways');

	$db = clone($GLOBALS['app']->db);

	$campaignId = GetVar('id', null);
	$mediaStatus = GetVar('status', null);
	$mediaOrdinal = GetVar('media', null);
	$mediaItemId = GetVar('mid', null);
	
	$sqlCampaign = sprintf("SELECT * FROM `campaign` WHERE `id` = %d;", $_GET['id']);
	$db->query($sqlCampaign);
	$itemCampaign = $db->next_record();

	$sqlMediaOrdinal = sprintf("SELECT * FROM `media%d` WHERE `id` = %d;", $mediaOrdinal, $_GET['mid']);
	$db->query($sqlMediaOrdinal);
	$itemMediaOrdinal = $db->next_record();
	
	$objMedia = CreateObject('Media', $mediaOrdinal);
	$objMrbsUsers = CreateObject('MrbsUsers', isset($itemCampaign['memberid']) ? $itemCampaign['memberid'] : 0);
	
	$statusData = '';
	if ($mediaStatus == Media::STATUS['PROCESSING']) {
		$statusData = $itemCampaign['name'] .' '. $objMedia->getVar('name') .' - 媒體執行';
	} else if ($mediaStatus == Media::STATUS['PAUSE']) {
		$statusData = $itemCampaign['name'] .' '. $objMedia->getVar('name') .' - 媒體暫停';
	} else if ($mediaStatus == Media::STATUS['CLOSED']) {
		$statusData = $itemCampaign['name'] .' '. $objMedia->getVar('name') .' - 媒體結案';
	} else {
		$statusData = '未知的操作';
	}

	$sqlInsertCampaignStatus = GenSqlFromArray([
		'name' => $_SESSION['username'],
		'data' => $statusData,
		'times' => time(),
		'campaignid' => $campaignId
	], 'campaignstatus', 'insert');
	$db->query($sqlInsertCampaignStatus);

	$sqlUpdateMediaItem = GenSqlFromArray([
		'status' => $mediaStatus,
	], sprintf('media%d', $mediaOrdinal), 'update', ['id' => $mediaItemId]);
	$db->query($sqlUpdateMediaItem);	

	if ($mediaStatus == Media::STATUS['CLOSED']) {
		$twig = CreateObject('Twig', __DIR__ .'/templates', 'mail_for_close_media.html', [
			'id' => $itemCampaign['id'],
			'name' => $itemCampaign['name'],
			'mediaName' => $itemMediaOrdinal['website'],
			'flag' => $GLOBALS['env']['flag'],
			'closedDate' => date('Y-m-d')
		]);
		$mailSubject = sprintf('【%s廣告後台】【媒體結案】%s - %s (%s)', $GLOBALS['env']['flag']['name'], $itemMediaOrdinal['website'], $itemCampaign['name'], date('Y-m-d'));

		AddMailToQueue($objMrbsUsers->getVar('email'), ucfirst($objMrbsUsers->getVar('name')) . $objMrbsUsers->getVar('username'), $twig->getContent(), $mailSubject, '傑思廣告後台');
	}
	
	checkCampaignAndClose($campaignId, $itemCampaign);
	
	$redirectPath = sprintf('campaign_view.php?id=%d', $_GET['id']);
	ShowMessageAndRedirect($statusData, $redirectPath, false);
