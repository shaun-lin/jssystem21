<?php
	
	// 2018-02-22 (Jimmy): 傑思jsadways2/campaign_edit2.php, 香港jsadways2hk/campaign_edit2.php, 豐富媒體jsadways2ff/campaign_edit2.php 共用此檔案
	require_once dirname(__DIR__) .'/autoload.php';

	$db = clone($GLOBALS['app']->db);

	$campaignId = GetVar('id');

	$sql = sprintf("SELECT * FROM campaign WHERE id = %d;", $campaignId);
	$db->query($sql);
	$itemCampaign = $db->next_record();

	if ($itemCampaign && IsId($itemCampaign['id'])) {
		$date1 = GetVar('date1');
		$date2 = GetVar('date2');
		$date11 = mktime(0, 0, 0, substr($date1, 0, 2), substr($date1, 3, 2), substr($date1, 6, 4));
		$date22 = mktime(0, 0, 0, substr($date2, 0, 2), substr($date2, 3, 2), substr($date2, 6, 4));

		$toUpdateCampaignDate = true;
		$searchCondition = sprintf("`accounting_campaign` = %d AND `accounting_revenue` != '' AND `accounting_revenue` != 0 AND `accounting_cost` != '' AND `accounting_cost` != 0", $campaignId);
		$objMediaAccounting = CreateObject('MediaAccounting');
		if ($numEntry = $objMediaAccounting->searchCount($searchCondition)) {
			$minEntryDate = $objMediaAccounting->searchAll($searchCondition, '', '', '', '', 'MIN(`accounting_month`) as `date_month`')[0]['date_month'];
			$maxEntryDate = $objMediaAccounting->searchAll($searchCondition, '', '', '', '', 'MAX(`accounting_month`) as `date_month`')[0]['date_month'];

			if (date('Ym', $date11) > $maxEntryDate || date('Ym', $date22) < $minEntryDate) {
				$toUpdateCampaignDate = false;
			}
		}

		$agencyId = GetVar('agency');
		$objAgency = CreateObject('Agency', $agencyId);

		$clientId = GetVar('client');
		$objClient = CreateObject('Client', $clientId);

		$SelectWomm = GetVar('SelectWomm');
		$wommAry = empty($SelectWomm) ? [0, ''] : explode(',', $SelectWomm);

		$updatedData = [
			'name' => GetVar('name'),
			'agency' => $objAgency->getVar('name'),
			'agency_id' => $objAgency->getId(),
			'client' => $objClient->getVar('name'),
			'client_id' => $objClient->getId(),
			'pay1' => GetVar('pay1'),
			'pay2' => GetVar('pay2'),
			'receipt1' => GetVar('receipt1'),
			'receipt2' => GetVar('receipt2'),
			'title' => GetVar('title'),
			'contact1' => GetVar('contact1'),
			'contact2' => GetVar('contact2'),
			'contact3' => GetVar('contact3'),
			'rate' => GetVar('rate'),
			'ratetime' => GetVar('ratetime'),
			'time' => time(),
			'draw' => GetVar('draw'),
			'others' => GetVar('others'),
			'wommId' => $wommAry[0],
			'womm' => $wommAry[1]
		];

		$mediaLeaderId = GetVar('media_leader');
		if (IsId($mediaLeaderId)) {
			$objMrbsUsers = CreateObject('MrbsUsers', $mediaLeaderId);
			if ($objMrbsUsers->getVar('departmentid') != 21 && $objMrbsUsers->getVar('departmentid') != 22) {
				$mediaLeaderId = 0;
			}

			unset($objMrbsUsers);
		}

		$updatedData += [
			'media_leader' => $mediaLeaderId
		];

		if ($toUpdateCampaignDate) {
			$updatedData += [
				'date1' => $date1,
				'date2' => $date2,
				'date11' => $date11,
				'date22' => $date22,
			];

			if ($itemCampaign['date11'] != $date11 || $itemCampaign['date22'] != $date22) {
				$logdata = date('Y/m/d', $itemCampaign['date11']) .' ~ '. date('Y/m/d', $itemCampaign['date22']) .' => '. date('Y/m/d', $date11) .' ~ '. date('Y/m/d', $date22);
				$sqlInsertLog = GenSqlFromArray([
					'name' => $_SESSION['username'],
					'data' => '案件修改走期 '. $logdata,
					'times' => time(),
					'campaignid' => $campaignId
				], 'campaignstatus', 'insert');
				$db->query($sqlInsertLog);
			}
		}

		$sqlUpdateCampaign = GenSqlFromArray($updatedData, 'campaign', 'update', ['id' => $campaignId]);
		$db->query($sqlUpdateCampaign);
	}
	
	if (isset($toUpdateCampaignDate) && $toUpdateCampaignDate === false) {
		ShowMessageAndRedirect('無法更新案件走期, 此案件已有填收入成本', "campaign_view.php?id=$campaignId");
	} else {
		ShowMessageAndRedirect('更新案件成功', "campaign_view.php?id=$campaignId", false);
	}
	