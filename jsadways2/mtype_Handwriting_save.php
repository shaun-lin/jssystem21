<?php
	
	require_once dirname(__DIR__) .'/autoload.php';
	require_once __DIR__ .'/include/function.inc.php';
	
	IncludeFunctions('jsadways');

	$db = clone($GLOBALS['app']->db);

	$cue = GetVar('cue');
	$campaignId = GetVar('campaign');
	$mediaId = GetVar('media_id');
	$items2 = GetVar('SelectType');
	$items3 = GetVar('SelectSystem');
	$totalprice = GetVar('totalprice');
	$totalprice2 = GetVar('totalprice2');
	$totalprice3 = GetVar('totalprice3');
	$others = GetVar('others');
	$time = time();

	$autoSerialNumberA=autoSerialNumber();
	$autoSerialNumberB=autoSerialNumber();
	
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
		'item_seq' => $autoSerialNumberA,
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
		'item_seq' => $autoSerialNumberB,
	];

	
	if (IsId($mediaId)) {
		$sqlMedia = sprintf("SELECT * FROM `media162` WHERE `id` = %d;", $mediaId);
		$db->query($sqlMedia);
		if ($itemMedia = $db->next_record()) {
			if ($itemMedia['cue'] == 2) {
				$sqlUpdateForIntCue = GenSqlFromArray($bindDataForIntCue + [
					'a5' => GetVar('totalprice') < (GetVar('totalprice2') * getInfuencerPriceRate('outer_tax_included')) ? 1 : 0,
				], 'media162', 'update', ['id' => $mediaId, 'campaign_id' => $campaignId]);
				$db->query($sqlUpdateForIntCue);

				if (GetVar('sync')) {
					$sqlUpdateForExtCue = GenSqlFromArray($bindDataForExtCue, 'media162', 'update', ['id' => $itemMedia['a0'], 'campaign_id' => $campaignId]);
					$db->query($sqlUpdateForExtCue);
				}
			} else if ($itemMedia['cue'] == 1) {
				$sqlUpdateForExtCue = GenSqlFromArray($bindDataForExtCue, 'media162', 'update', ['id' => $mediaId, 'campaign_id' => $campaignId]);
				$db->query($sqlUpdateForExtCue);
			}
		}
	} else {
		

		$cp_id = GetVar('id');
		$item_id = GetVar('itemid');
		$mtype_name = GetVar('mtypename');
		$mtype_number = GetVar('mtypenumber');
		$mtype_id = GetVar('mtypeid');
		$media_Id = GetVar('mediaid');

		$sqlInsertForExtCue = GenSqlFromArray($bindDataForExtCue + [
			'cue' => 1,
			'status' => 0,
		], 'media162', 'insert');

		$db->query($sqlInsertForExtCue);

		$lastInsertId = $db->get_last_insert_id();
		AddMediaMapping("media162", $campaignId, $lastInsertId);

		$sql3 = "INSERT INTO `cp_detail`( `cp_id`, `media_id`, `comp_id`, `item_id`, `mtype_name`, `mtype_number`, `mtype_id`,`item_seq`,`cue`) 
		VALUES ('".$cp_id."','".$media_Id."','0','".$item_id."','".$mtype_name."','".$mtype_number."','".$lastInsertId."','".$autoSerialNumberA."','1')";
		$db->query($sql3);
		
		$sqlInsertForIntCue = GenSqlFromArray($bindDataForIntCue + [
			'cue' => 2,
			'status' => 0,
			'a0' => $lastInsertId,
			'a' => 162,
			'a5' => GetVar('totalprice') < (GetVar('totalprice2') * getInfuencerPriceRate('outer_tax_included')) ? 1 : 0,
		], 'media162', 'insert');
		$db->query($sqlInsertForIntCue);

		$lastInsertId = $db->get_last_insert_id();
		AddMediaMapping("media162", $campaignId, $lastInsertId);

		$sql4 = "INSERT INTO `cp_detail`( `cp_id`, `media_id`, `comp_id`, `item_id`, `mtype_name`, `mtype_number`, `mtype_id`,`item_seq`,`cue`) 
		VALUES ('".$cp_id."','".$media_Id."','0','".$item_id."','".$mtype_name."','".$mtype_number."','".$lastInsertId."','".$autoSerialNumberB."','2')";
		$db->query($sql4);
	}
	$goon=GetVar('goon');

		if ($goon=="Y") {

		$arrItems=array();
				$arrItems[]=array("key"=>"result","name"=>"OK");
		
		echo json_encode($arrItems);
	}else{
	ShowMessageAndRedirect((IsId($mediaId) ? '編輯' : '新增') .'媒體成功', 'campaign_view.php?id='. $campaignId, false);
	}
	
	