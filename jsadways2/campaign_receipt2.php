<?php
	
	// 2017-10-30 (Jimmy): 傑思jsadways2/campaign_receipt2.php, 香港jsadways2hk/campaign_receipt2.php, 豐富媒體jsadways2ff/campaign_receipt2.php 共用此檔案
	require_once dirname(__DIR__) .'/autoload.php';

	IsPermitted();
	
	$db = clone($GLOBALS['app']->db);

	$objCampaign = CreateObject('Campaign', GetVar('id'));

	if (IsId($objCampaign->getId())) {
		$sqlInsertReceipt = genSqlFromArray([
			'campaign_id' => $objCampaign->getId(),
			'name' => GetVar('name'),
			'user1' => GetVar('user1'),
			'user2' => GetVar('user2'),
			'taxid' => GetVar('taxid'),
			'numberid' => GetVar('numberid'),
			'class' => GetVar('class'),
			'totalprice1' => GetVar('totalprice1'),
			'totalprice2' => GetVar('totalprice2'),
			'datemonth' => GetVar('datemonth'),
			'others' => GetVar('others'),
			'times1' => time(),
			'usertype' => GetVar('usertype'),
			'userid' => GetVar('userid2'),
			'ae_datemonth' => GetVar('datemonth'),
		], 'receipt', 'insert');
		$db->query($sqlInsertReceipt);

		$mailSubject = '【'. GetVar('user2') .' - 提出開發票需求】 ('. date('Y-m-d') .')';

		$twig = CreateObject('Twig', __DIR__ .'/templates', 'mail_for_campaign_receipt.html', [
			'flag' => $GLOBALS['env']['flag'],
			'id' => $objCampaign->getId(),
			'name' => $objCampaign->getVar('name'),
			'username' => $objCampaign->getVar('member'),
			'presenter' => GetVar('user2'),
		]);

		AddMailToQueue('invoice@js-adways.com.tw', '財務組', $twig->getContent(), $mailSubject, "{$GLOBALS['env']['flag']['name']}廣告後台");
	}

	ShowMessageAndRedirect('提出開發票需求成功', "campaign_view.php?id={$campaignId}", false);
