<?php
	
	require_once dirname(__DIR__) .'/autoload.php';
	require_once __DIR__ .'/include/function.inc.php';
	
	IncludeFunctions('jsadways');

	$db = clone($GLOBALS['app']->db);

	$cue = GetVar('cue');
	$campaignId = GetVar('campaign_id');
	$mediaId = GetVar('media_id');
	$items2 = GetVar('SelectType');
	$items3 = GetVar('SelectSystem');
	$totalprice = GetVar('totalprice');
	$totalprice2 = GetVar('totalprice2');
	$totalprice3 = GetVar('totalprice3');
	$others = GetVar('others');
	$time = time();

	$bindDataForExtCue = [
		'campaign_id' => $campaignId,
		'website' => '寫手費',
		'totalprice' => $totalprice,
		'totalprice2' => $totalprice2,
		'totalprice3' => $totalprice3,
		'times' => $time,
		'others' => $others,
		'items2' => $items2,
		'items3' => $items3,
	];

	$bindDataForIntCue = [
		'campaign_id' => $campaignId,
		'website' => '寫手費',
		'totalprice' => $totalprice,
		'a4' => $totalprice2,
		'a3' => $totalprice3,
		'times' => $time,
		'others' => $others,
		'items2' => $items2,
		'items3' => $items3,
	];

	
	if (IsId($mediaId)) {
		$sqlMedia = sprintf("SELECT * FROM `media19` WHERE `id` = %d;", $mediaId);
		$db->query($sqlMedia);
		if ($itemMedia = $db->next_record()) {
			if ($itemMedia['cue'] == 2) {
				$sqlUpdateForIntCue = GenSqlFromArray($bindDataForIntCue + [
					'a5' => GetVar('totalprice') < (GetVar('totalprice2') * getInfuencerPriceRate('outer_tax_included')) ? 1 : 0,
				], 'media19', 'update', ['id' => $mediaId, 'campaign_id' => $campaignId]);
				$db->query($sqlUpdateForIntCue);

				if (GetVar('sync')) {
					$sqlUpdateForExtCue = GenSqlFromArray($bindDataForExtCue, 'media19', 'update', ['id' => $itemMedia['a0'], 'campaign_id' => $campaignId]);
					$db->query($sqlUpdateForExtCue);
				}
			} else if ($itemMedia['cue'] == 1) {
				$sqlUpdateForExtCue = GenSqlFromArray($bindDataForExtCue, 'media19', 'update', ['id' => $mediaId, 'campaign_id' => $campaignId]);
				$db->query($sqlUpdateForExtCue);
			}
		}
	} else {
		$sqlInsertForExtCue = GenSqlFromArray($bindDataForExtCue + [
			'cue' => 1,
			'status' => 0,
		], 'media19', 'insert');

		$db->query($sqlInsertForExtCue);

		$lastInsertId = $db->get_last_insert_id();
		AddMediaMapping(19, $campaignId, $lastInsertId);

		$sqlInsertForIntCue = GenSqlFromArray($bindDataForIntCue + [
			'cue' => 2,
			'status' => 0,
			'a0' => $lastInsertId,
			'a' => 19,
			'a5' => GetVar('totalprice') < (GetVar('totalprice2') * getInfuencerPriceRate('outer_tax_included')) ? 1 : 0,
		], 'media19', 'insert');
		$db->query($sqlInsertForIntCue);

		$lastInsertId = $db->get_last_insert_id();
		AddMediaMapping(19, $campaignId, $lastInsertId);
	}
	
	ShowMessageAndRedirect((IsId($mediaId) ? '編輯' : '新增') .'媒體成功', 'campaign_view.php?id='. $campaignId, false);
	