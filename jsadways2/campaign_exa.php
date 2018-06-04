<?php
	
	require_once dirname(__DIR__) .'/autoload.php';
	
	$message = '';

	$db = clone($GLOBALS['app']->db);
	$dbMedia = clone($GLOBALS['app']->db);

	$status = GetVar('status');
	$campaignId = GetVar('id');
	$sqlCampaign = sprintf("SELECT * FROM `campaign` WHERE `id` = %d;", $campaignId);
	$db->query($sqlCampaign);
	$itemCampaign = $db->next_record();

	$objMrbsUsers = CreateObject('MrbsUsers', $itemCampaign['memberid']);
	if ($itemCampaign && IsId($itemCampaign['id'])) {
		$totalPriceForExternal = 0;
		$totalPriceForInternal = 0;
		$textForMessage = '';
		
		$medianame1='==========對外投放媒體==========';
		$medianame2='==========對內投放媒體==========';
	
		$rowsOrdinal = GetUsedMediaOrdinal($campaignId);
		foreach ($rowsOrdinal as $i) {
			$sqlMedia = sprintf('SELECT * FROM `media%d` WHERE `campaign_id` = %d;', $i, $campaignId);
			$dbMedia->query($sqlMedia);

			while ($itemMediaData = $dbMedia->next_record()) {
				$itemMediaInfo = GetMedia($i);
	
				if ($itemMediaData['cue'] == 1) {
					$totalPriceForExternal += $itemMediaData['totalprice'];
					$medianame1 = $medianame1 .'<br />'. $itemMediaInfo['name'] .'：'. number_format($itemMediaData['totalprice']);
				} else if ($itemMediaData['cue'] == 2) {
					$totalPriceForInternal += $itemMediaData['totalprice'];
	
					if ($itemMediaData['a5'] == 1) {
						$a = 1;
						$textForMessage .= $itemMediaInfo['name'] .'媒體利潤少於公司規定下限<br/>';
					}
	
					$medianame2 = $medianame2 .'<br />'. $itemMediaInfo['name'] .'：'. number_format($itemMediaData['totalprice']);
				}
			}
		}
	
		// 判斷是否有委刊編號，若無則給予委刊編號
		if (empty($itemCampaign['idnumber'])) {
			$ym = date('ym');
			$sqlIdNumber = sprintf("SELECT * FROM campaign ORDER BY idnumber DESC LIMIT 1;");
			$db->query($sqlIdNumber);
			$itemIdNumber = $db->next_record();
	
			if (substr($itemIdNumber['idnumber'], 0, 4) == $ym) {
				$idnumber = substr($itemIdNumber['idnumber'], 0, 4) . str_pad((substr($itemIdNumber['idnumber'], 4, 3) + 1), 3, '0', STR_PAD_LEFT);
			} else {
				$idnumber = $ym . '001';
			}
	
			$sqlUpdateCampaign = GenSqlFromArray(['idnumber' => $idnumber], 'campaign', 'update', ['id' => $campaignId]);
			$db->query($sqlUpdateCampaign);
		}
	
		//判斷是否異常
		
		$totalPriceForExternal = round($totalPriceForExternal);
		$totalPriceForInternal = round($totalPriceForInternal);
	
		if ($totalPriceForExternal != $totalPriceForInternal) {
			$textForMessage .= '異常原因為對內外cue表總金額不符<br/>';
		}
	
		foreach ($rowsOrdinal as $i) {
			$sqlPR = sprintf("SELECT * FROM `media%d` WHERE `campaign_id` = %d AND `totalprice` = 0;", $i, $campaignId);
			$db->query($sqlPR);
			if ($itemPR = $db->next_record()) {
				$pr = 1;
				$textForMessage .= '異常原因為此案件有PR或媒體金額為0<br/>';
				break;
			}
		}
		
		if ($status == 2 && $itemCampaign['status'] != 2) {
			if ($totalPriceForExternal != $totalPriceForInternal || $pr == 1 || $a == 1) {
				if ($itemCampaign['status'] != 7) {
					$message = '案件異常';
		
					$sqlUpdateCampaign = GenSqlFromArray([
						'status' => 7
					] + (isset($_REQUEST['exception_comment']) ? ['others' => $_REQUEST['exception_comment']] : []), 'campaign', 'update', ['id' => $campaignId]);
					$db->query($sqlUpdateCampaign);
					
					$sqlInserLog = GenSqlFromArray([
						'name' => $_SESSION['username'],
						'data' => '進行送審，發生異常'. $textForMessage,
						'times' => time(),
						'campaignid' => $campaignId
					], 'campaignstatus', 'insert');
					$db->query($sqlInserLog);
					
					$addressee = [];
					foreach (GetUsersInfo('backend_campaign_exception_approval') as $itemUser) {
						if (IsPermitted('backend_campaign_exception_approval', null, 'notify', true, $itemUser['id'])) {
							$addressee[$itemUser['email']] = ucfirst($itemUser['name']) . $itemUser['username'];
						}
					}

					$twig = CreateObject('Twig', __DIR__ .'/templates', 'mail_for_campaign.html', [
						'title' => '案件異常',
						'id' => $itemCampaign['id'],
						'name' => $itemCampaign['name'],
						'username' => ucfirst($objMrbsUsers->getVar('name')) . $objMrbsUsers->getVar('username'),
						'agency' => $itemCampaign['agency'],
						'client' => $itemCampaign['client'],
						'womm' => $itemCampaign['womm'],
						'flag' => $GLOBALS['env']['flag'],
						'actionDate' => date('Y-m-d'),
						'priceException' => $textForMessage,
						'exception' => isset($_REQUEST['exception_comment']) ? $_REQUEST['exception_comment'] : $itemCampaign['others']
					] + (empty($itemCampaign['other']) ? [] : ['other' => $itemCampaign['other']]));
					
					if (count($addressee)) {
						$mailSubject = '【'. $GLOBALS['env']['flag']['name'] .'廣告後台】【異常案件】'. $itemCampaign['name'] .' - '. $itemCampaign['member'] .' ('. date('Y/m/d') .')';
						AddMailToQueue($addressee, [], $twig->getContent(), $mailSubject, $GLOBALS['env']['flag']['name'] .'廣告後台');
					}
				}
			} else {
				$message = '案件送審中';
	
				$sqlUpdateCampaign = GenSqlFromArray(['status' => 2], 'campaign', 'update', ['id' => $campaignId]);
				$db->query($sqlUpdateCampaign);
	
				$sqlInserLog = GenSqlFromArray([
					'name' => $_SESSION['username'],
					'data' => '進行送審',
					'times' => time(),
					'campaignid' => $campaignId
				], 'campaignstatus', 'insert');
				$db->query($sqlInserLog);
			}
		} else if ($status == 5 && $itemCampaign['status'] != 5) {
			$message = '案件暫停';
	
			$sqlUpdateCampaign = GenSqlFromArray(['status' => 5], 'campaign', 'update', ['id' => $campaignId]);
			$db->query($sqlUpdateCampaign);
			
			if ($itemCampaign['action1']) {
				$sqlUpdateCampaign = GenSqlFromArray(['action1' => 2], 'campaign', 'update', ['id' => $campaignId]);
				$db->query($sqlUpdateCampaign);
			}
	
			$sqlInserLog = GenSqlFromArray([
				'name' => $_SESSION['username'],
				'data' => '案件暫停',
				'times' => time(),
				'campaignid' => $campaignId
			], 'campaignstatus', 'insert');
			$db->query($sqlInserLog);
		} else if ($status == 6 && $itemCampaign['status'] != 6) {
			$message = '案件中止';
	
			$sqlUpdateCampaign = GenSqlFromArray(['status' => 6], 'campaign', 'update', ['id' => $campaignId]);
			$db->query($sqlUpdateCampaign);
			
			$sqlInserLog = GenSqlFromArray([
				'name' => $_SESSION['username'],
				'data' => '案件中止',
				'times' => time(),
				'campaignid' => $campaignId
			], 'campaignstatus', 'insert');
			$db->query($sqlInserLog);
		}
	}

	if (isset($_REQUEST['refer'])) {
		$redirectPath = sprintf('%s.php?id=%d', $_REQUEST['refer'], $campaignId);
	} else if ($_SESSION['usergroup'] >= 3) {
		$redirectPath = 'campaign_listall.php';
	} else{
		$redirectPath = 'campaign_list.php';
	}

	$redirectPath = sprintf('campaign_view.php?id=%d', $campaignId);
	ShowMessageAndRedirect($message, $redirectPath, false);
