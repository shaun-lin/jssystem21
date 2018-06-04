<?php

	// 2017-10-18 (Jimmy): 傑思jsadways2/campaign_execute.php, 香港jsadways2hk/campaign_execute.php, 豐富媒體jsadways2ff/campaign_execute.php 共用此檔案
	require_once dirname(__DIR__) .'/autoload.php';

	$db = clone($GLOBALS['app']->db);

	$title = '';
	$refer = '';

	$isAjax = isset($_REQUEST['ajax']);
	$type = GetVar('type');
	$action = GetVar('action');
	$campaignId = GetVar('id') ? GetVar('id') : GetVar('campaign_id');

	$sql = sprintf("SELECT * FROM campaign WHERE id = %d;", $campaignId);
	$db->query($sql);
	$itemCampaign = $db->next_record();

	if ($action == 'close_entry') {
		$isGrantedCloseEntry = IsPermitted('finacial', null, Permission::ACL['finacial_close_entry']);

		if ($isGrantedCloseEntry) {
			IncludeFunctions('jsadways');
			closeEntry(GetVar('close_entry_flag'));
		}

		PrintJsonData([
			'success' => (int)$isGrantedCloseEntry,
			'failure' => (int)(!$isGrantedCloseEntry),
			'data' => $isGrantedCloseEntry ? [
				'flag' => $GLOBALS['app']->preference->get('close_entry_flag'),
				'datetime' => date('Y-m-d H:i:s', $GLOBALS['app']->preference->get('close_entry_stamp')),
				'username' => $GLOBALS['app']->preference->get('close_entry_user'),
			] : [],
			'message' => $isGrantedCloseEntry ? '關帳成功' : '沒有操作的權限'
		], true);
	} else if ($itemCampaign && IsId($itemCampaign['id'])) {
		$objMrbsUsers = CreateObject('MrbsUsers', $itemCampaign['memberid']);

		if ($type == 'exception') {
			$assign = [
				'id' => $itemCampaign['id'],
				'name' => $itemCampaign['name'],
				'username' => ucfirst($objMrbsUsers->getVar('name')) . $objMrbsUsers->getVar('username'),
				'agency' => $itemCampaign['agency'],
				'client' => $itemCampaign['client'],
				'womm' => $itemCampaign['womm'],
				'flag' => $GLOBALS['env']['flag'],
				'actionDate' => date('Y-m-d'),
			];

			switch ($action) {
				case 'approve': // status = 2
					if ($itemCampaign['status'] != 2) {
						$title = '案件異常核准';
						$confirmComment = GetVar('confirm_comment');

						$sqlUpdateCampaign = GenSqlFromArray([
							'status' => 2,
							'others' => $itemCampaign['others'],
							'campaign_exception_comment' => "＊異常核准, 原因: {$confirmComment}\n＊審核人員: ". $_SESSION['username'] .' '. date('Y-m-d H:i:s'),
						], 'campaign', 'update', ['id' => $campaignId]);
						$db->query($sqlUpdateCampaign);

						$sqlInsertLog = GenSqlFromArray([
							'name' => $_SESSION['username'],
							'data' => "異常核准, 原因: {$confirmComment}",
							'times' => time(),
							'campaignid' => $campaignId
						], 'campaignstatus', 'insert');
						$db->query($sqlInsertLog);

						// 寄送異常核准通知給案件業務相關主管
						$addressee = [$objMrbsUsers->getVar('email') => ucfirst($objMrbsUsers->getVar('name')) . $objMrbsUsers->getVar('username')];
						foreach (GetUsersInfo('backend_campaign_exception_approval') as $itemUser) {
							$addressee[$itemUser['email']] = ucfirst($itemUser['name']) . $itemUser['username'];
						}

						$mailSubject = '【'. $GLOBALS['env']['flag']['name'] .'廣告後台】【異常核准】'. $itemCampaign['name'] .' ('. date('Y-m-d H:i:s') .')';
						$twig = CreateObject('Twig', __DIR__ .'/templates', 'mail_for_campaign.html', [
							'title' => $title,
							'exception' => $itemCampaign['others'],
							'approver' => $_SESSION['username'],
							'approval_comment' => $confirmComment
						] + $assign);

						AddMailToQueue($addressee, [], $twig->getContent(), $mailSubject, $GLOBALS['env']['flag']['name'] .'廣告後台');
						

						// 寄送待審核信件至媒體部
						$mailSubject = '【'. $GLOBALS['env']['flag']['name'] .'廣告後台】【待審核案件】'. $itemCampaign['name'] .' ('. date('Y-m-d') .')';
						$twig = CreateObject('Twig', __DIR__ .'/templates', 'mail_for_campaign.html', [
							'title' => '案件待審核'	
						] + $assign);
						AddMailToQueue('media@js-adways.com.tw', '媒體部', $twig->getContent(), $mailSubject, $GLOBALS['env']['flag']['name'] .'廣告後台');
					}
					break;
				case 'reject': // status = 5
					if ($itemCampaign['status'] != 5) {
						$title = '案件異常不核准';
						$rejectComment = GetVar('reject_comment');

						$sqlUpdateCampaign = GenSqlFromArray([
							'status' => 5,
							'others' => $itemCampaign['others'],
							'campaign_exception_comment' => "＊異常不核准, 原因: {$rejectComment}\n＊審核人員: ". $_SESSION['username'] .' '. date('Y-m-d'),
						], 'campaign', 'update', ['id' => $campaignId]);
						$db->query($sqlUpdateCampaign);

						$sqlInsertLog = GenSqlFromArray([
							'name' => $_SESSION['username'],
							'data' => "異常不核准, 原因: {$rejectComment}",
							'times' => time(),
							'campaignid' => $campaignId
						], 'campaignstatus', 'insert');
						$db->query($sqlInsertLog);

						$mailSubject = '【'. $GLOBALS['env']['flag']['name'] .'廣告後台】【異常不核准】'. $itemCampaign['name'] .' ('. date('Y-m-d') .')';

						$twig = CreateObject('Twig', __DIR__ .'/templates', 'mail_for_campaign.html', [
							'title' => $title,
							'reject' => $rejectComment
						] + $assign);

						AddMailToQueue($objMrbsUsers->getVar('email'), ucfirst($objMrbsUsers->getVar('name')) . $objMrbsUsers->getVar('username'), $twig->getContent(), $mailSubject, $GLOBALS['env']['flag']['name'] .'廣告後台');
					}
					break;
			}
		} else if ($type == 'cancel') {
			$title = ($action == 'confirm') ? '案件已作廢' : ($action == 'reject' ? '不核准作廢' : '提出作廢送審中');
			$abortComment = trim(GetVar('abort_comment', ''));

			if ($action == 'request') {
				if ($itemCampaign['status'] != 9) {
					// 媒體 19 (寫手費)
					$bloggerItemId = 19;
					$hasBloggerItem = false;
					$hasCommonItem = false;
				
					$mediaOrdinal = GetUsedMediaOrdinal($campaignId);
					foreach ($mediaOrdinal as $i) {
						if ($i == $bloggerItemId) {
							$hasBloggerItem = true;
						} else {
							$hasCommonItem = true;
						}
						
						// 2017-10-25 (Jimmy): 作廢送審案件暫不刪除媒體資料
						// $deleteSQL = sprintf("DELETE FROM `media%d` WHERE `campaign_id` = %d;", $i, $campaignId);
						// $db->query($deleteSQL);
					}

					if (empty($mediaOrdinal)) {
						$hasCommonItem = true;
					}

					if ($itemCampaign['status'] == 1) {
						// 2017-08-18 (Jimmy): 尚未送審的案件可以跳過審核直接作廢
						$action = 'confirm';
					} else if ($itemCampaign['status'] != 9) {
						$sqlUpdateCampaign = GenSqlFromArray([
							'status' => 9
						], 'campaign', 'update', ['id' => $campaignId]);
						$db->query($sqlUpdateCampaign);
		
						$sqlInsertLog = GenSqlFromArray([
							'name' => $_SESSION['name'],
							'data' => $title . (empty($abortComment) ? '' : "  ( 原因: {$abortComment} )"),
							'times' => time(),
							'campaignid' => $campaignId
						], 'campaignstatus', 'insert');
						$db->query($sqlInsertLog);
						
						// 2017-08-18 (Jimmy): 產生通知信件內容
						$twig = CreateObject('Twig', __DIR__ .'/templates', 'campaign_abort_content.html', [
							'id' => $itemCampaign['id'],
							'name' => $itemCampaign['name'],
							'idnumber' => $itemCampaign['idnumber'],
							'member' => $itemCampaign['member'],
							'abortComment' => $abortComment,
							'flag' => $GLOBALS['env']['flag']
						]);
		
						$queueData = [
							'type' => 'send-mail',
							'recipient' => null,
							'subject' => sprintf('【%s廣告後台】%s 作廢案件送審清單', $GLOBALS['env']['flag']['name'], date('Y/m/d')),
							'content' => $twig->getContent(),
							'sender' => sprintf('%s廣告後台', $GLOBALS['env']['flag']['name'])
						];
		
						// 2017-08-18 (Jimmy): 信件寄送時間設定為隔天早上的 09:30
						$queueExecuteDateTime = date('Y-m-d 09:30:00', strtotime('+1 days', time()));
						$queueExecuteTime = strtotime($queueExecuteDateTime);
		
						if ($hasCommonItem) {
							// 2017-08-25 (Jimmy): 寄送作廢通知信給 媒體部 (media@js-adways.com.tw)
							$queueDataForMedia = $queueData + ['to' => 'media@js-adways.com.tw'];
							$queueName = date('Ymd') ."-abort-campaign{$GLOBALS['env']['flag']['pos']}-for-media";
							AppendContentToQueue($queueName, $queueDataForMedia, 'content', $queueExecuteTime);
						}
		
						if ($hasBloggerItem) {
							// 2017-08-25 (Jimmy): 寄送作廢通知信給 口碑部 (wom@js-adways.com.tw)
							$queueDataForWom = $queueData + ['to' => 'wom@js-adways.com.tw'];
							$queueName = date('Ymd') ."-abort-campaign{$GLOBALS['env']['flag']['pos']}-for-wom";
							AppendContentToQueue($queueName, $queueDataForWom, 'content', $queueExecuteTime);
						}
					}
				}
			}

			if ($action == 'confirm') {
				$refer = isset($_REQUEST['refer']) ? $_REQUEST['refer'] : 'campaign_list.php';

				if ($itemCampaign['status'] != 8) {
					$rowsOrdinal = GetUsedMediaOrdinal($campaignId);
					foreach ($rowsOrdinal as $i) {
						$sqlDeleteMedia = sprintf("DELETE FROM `media%d` WHERE `campaign_id` = %d;", $i, $campaignId); 
						$db->query($sqlDeleteMedia);
					}

					$sqlUpdateCampaign = GenSqlFromArray([
						'status' => 8
					], 'campaign', 'update', ['id' => $campaignId]);
					$db->query($sqlUpdateCampaign);

					$sqlInsertLog = GenSqlFromArray([
						'name' => $_SESSION['name'],
						'data' => '案件作廢' . (empty($abortComment) ? '' : "  ( 原因: {$abortComment} )"),
						'times' => time(),
						'campaignid' => $campaignId
					], 'campaignstatus', 'insert');
					$db->query($sqlInsertLog);

					// 判斷是否有已開發票
					$receiptsData = '';
					$sqlReceipt = sprintf("SELECT * FROM `receipt` WHERE `campaign_id` = %d AND status in (0, 1);", $campaignId);
					$db->query($sqlReceipt);
					while ($itemReceipt = $db->next_record()) {
						$receiptsData += "【已開發票：{$itemReceipt['receipt_number']}({$itemReceipt['totalprice2']})-{$itemReceipt['datemonth']}-[". ($itemReceipt['status'] == 0 ? '未收款' : '已收款') ."]】<br/>";
					}
					$hasReceiptRecord = empty($receiptsData) ? false : true ;

					$mailFromName = sprintf('%s廣告後台', $GLOBALS['env']['flag']['name']);
					$mailSubject = sprintf('【%s廣告後台】', $GLOBALS['env']['flag']['name']) .'【案件已作廢】【'. $itemCampaign['name'] .'】-'. date('Y:m:d', time());
					$mailContent = '【'. $itemCampaign['name'] .'】案件已作廢<br/>委刊編號【'. $itemCampaign['idnumber'] .'】<br/>負責AE【'. $itemCampaign['member'] .'】<br/>'. ((empty($abortComment) ? '' : "作廢原因: {$abortComment}<br/>")) . $receiptsData;
					$mailTo = [];
				
					if (IsId($itemCampaign['memberid']) && $itemCampaign['memberid'] != $GLOBALS['app']->userid) {
						$mailTo[$objMrbsUsers->getVar('email')] = ucfirst($objMrbsUsers->getVar('name')) . $objMrbsUsers->getVar('username');
					}

					if ($hasReceiptRecord) {
						foreach ($objMrbsUsers->searchAll("`usergroup` = 4 AND `status` = 1 AND (`user_resign_date` IS NULL OR `user_resign_date` = '0000-00-00')") as $itemUser) {
							$mailTo[$itemUser['email']] = ucfirst($itemUser['name']) . $itemUser['username'];
						}
					}
				
					if (count($mailTo)) {
						AddMailToQueue($mailTo, null, $mailContent, $mailSubject, $mailFromName);
					}
				}
			}

			if ($action == 'reject') {
				$refer = 'campaign_list.php';

				if ($itemCampaign['status'] != 5) {
					$rowsOrdinal = GetUsedMediaOrdinal($campaignId);
					foreach ($rowsOrdinal as $i) {
						// $sqlDeleteMedia = sprintf("DELETE FROM `media%d` WHERE `campaign_id` = %d;", $i, $campaignId); 
						// $db->query($sqlDeleteMedia);
					}

					$sqlUpdateCampaign = GenSqlFromArray([
						'status' => 5
					], 'campaign', 'update', ['id' => $campaignId]);
					$db->query($sqlUpdateCampaign);

					$sqlInsertLog = GenSqlFromArray([
						'name' => $_SESSION['name'],
						'data' => '案件不核准作廢',
						'times' => time(),
						'campaignid' => $campaignId
					], 'campaignstatus', 'insert');
					$db->query($sqlInsertLog);
				}
			}
		} else if ($action == 'pass') {
			$title = '案件審核通過';
			$refer = 'campaign_listall.php';

			//ken,審核通過需針對每個品項去選擇公司,更新cp_detail的comp_id欄位
			foreach($_POST['cp'] as $cp)
			{
				$cp_id = $cp['id'];
				$cp_media_id = $cp['media_id'];
				$cp_item_id = $cp['item_id'];
				$cp_comp_id = $cp['selCompany'];
				
				//echo $cp_id."...".$cp_media_id."...".$cp_item_id."...".$cp_comp_id."###";

				$sqlUpdateCpdetail = GenSqlFromArray([
					'media_id' => $cp_media_id,
					'item_id' => $cp_item_id,
					'comp_id' => $cp_comp_id
				], 'cp_detail', 'update', ['id' => $cp_id]);
				$db->query($sqlUpdateCpdetail);
			}

			if ($itemCampaign['status'] != 3) {
				$tagtext = '';
				if (isset($_POST['tagtext']) && is_array($_POST['tagtext'])) {
					$tagtext = implode('、', $_POST['tagtext']);
				}

				$sqlUpdateCampaign = GenSqlFromArray([
					'status' => 3,
					'tagtext' => $tagtext,
					'times' => time()
				], 'campaign', 'update', ['id' => $campaignId]);
				$db->query($sqlUpdateCampaign);

				$sqlInsertLog = GenSqlFromArray([
					'name' => $_SESSION['username'],
					'data' => '審核成功',
					'times' => time(),
					'campaignid' => $campaignId
				], 'campaignstatus', 'insert');
				$db->query($sqlInsertLog);

				$twig = CreateObject('Twig', __DIR__ .'/templates', 'mail_for_campaign_status_change.html', [
					'flag' => $GLOBALS['env']['flag'],
					'id' => $itemCampaign['id'],
					'name' => $itemCampaign['name'],
					'username' => ucfirst($objMrbsUsers->getVar('name')) . $objMrbsUsers->getVar('username'),
					'status' => '執行中'
				]);

				$mailSubject = '【案件審核通過】'. $itemCampaign['name'] .' ('. date('Y-m-d') .')';
				$mailContent = $twig->getContent();

				AddMailToQueue($objMrbsUsers->getVar('email'), ucfirst($objMrbsUsers->getVar('name')) . $objMrbsUsers->getVar('username'), $mailContent, $mailSubject, "{$GLOBALS['env']['flag']['name']}廣告後台");
			}
		} else if ($action == 'close') {
			$title = '案件已結案';
			$refer = 'campaign_listall.php';

			if ($itemCampaign['status'] != 4) {
				if (IsId($itemCampaign['agency_id'])) {
					$objAgencyClient = CreateObject('Agency', $itemCampaign['agency_id']);
				} else {
					$objAgencyClient = CreateObject('Client', $itemCampaign['client_id']);
				}

				if (IsId($objAgencyClient->getId()) && empty($objAgencyClient->getVar('is_old'))) {
					$objAgencyClient->setVar('is_old', 1);
					$objAgencyClient->store();
				}

				$sqlUpdateCampaign = GenSqlFromArray([
					'status' => 4
				], 'campaign', 'update', ['id' => $campaignId]);
				$db->query($sqlUpdateCampaign);

				$sqlInsertLog = GenSqlFromArray([
					'name' => $_SESSION['username'],
					'data' => '結案',
					'times' => time(),
					'campaignid' => $campaignId
				], 'campaignstatus', 'insert');
				$db->query($sqlInsertLog);

				$mailSubject = '【'. $GLOBALS['env']['flag']['name'] .'廣告後台】【案件已結案】'. $itemCampaign['name'] .' ('. date('Y-m-d') .')';
				$mailContent = "<br/>【{$itemCampaign['name']}】案件已結案，可登入廣告後台查看您所有已結案的案件<br/>";

				AddMailToQueue($objMrbsUsers->getVar('email'), ucfirst($objMrbsUsers->getVar('name')) . $objMrbsUsers->getVar('username'), $mailContent, $mailSubject, $GLOBALS['env']['flag']['name'] .'廣告後台');
			}
		} else if ($action == 'reject') {
			$title = '案件審核不通過';
			$refer = 'campaign_listall.php';

			if ($itemCampaign['status'] != 5) {
				$sqlUpdateCampaign = GenSqlFromArray([
					'status' => 5,
					'others2' => GetVar('text', '')
				], 'campaign', 'update', ['id' => $campaignId]);
				$db->query($sqlUpdateCampaign);

				$sqlInsertLog = GenSqlFromArray([
					'name' => $_SESSION['username'],
					'data' => '案件審核不通過',
					'times' => time(),
					'campaignid' => $campaignId
				], 'campaignstatus', 'insert');
				$db->query($sqlInsertLog);

				$mailSubject = "【{$GLOBALS['env']['flag']['name']}廣告後台】【案件審核不通過】". $itemCampaign['name'] .' ('. date('Y/m/d') .')';
				$mailContent = "【{$itemCampaign['name']}】此案件審核不通過，若有任何疑慮請洽行政部<br/>不通過理由: ". GetVar('text', '');
				AddMailToQueue($objMrbsUsers->getVar('email'), ucfirst($objMrbsUsers->getVar('name')) . $objMrbsUsers->getVar('username'), $mailContent, $mailSubject, $GLOBALS['env']['flag']['name'] .'廣告後台');
			}
		} else if ($action == 'update_categories') {
			$title = '修改案件分類';

			$tagtext = '';
			if (isset($_POST['tagtext2']) && is_array($_POST['tagtext2'])) {
				$tagtext = implode('、', $_POST['tagtext2']);
			}

			$sqlUpdateCampaign = GenSqlFromArray([
				'tagtext' => $tagtext
			], 'campaign', 'update', ['id' => $campaignId]);
			$db->query($sqlUpdateCampaign);

			$sqlInsertLog = GenSqlFromArray([
				'name' => $_SESSION['username'],
				'data' => '修改案件分類',
				'times' => time(),
				'campaignid' => $campaignId
			], 'campaignstatus', 'insert');
			$db->query($sqlInsertLog);
		} else if ($action == 'update_exchange') {
			$title = '修改外匯調整數';

			$exchangMath = GetVar('exchang_math');
			$exchangTime = GetVar('exchang_time');
			$writeTime = GetVar('write_time');

			$sqlUpdateCampaign = GenSqlFromArray([
				'exchang_math' => $exchangMath,
				'exchang_time' => $exchangTime,
				'write_time' => $writeTime,
			], 'campaign', 'update', ['id' => $campaignId]);
			$db->query($sqlUpdateCampaign);

			$sqlInsertLog = GenSqlFromArray([
				'name' => $_SESSION['username'],
				'data' => "修改外匯調整數/日期，從{$itemCampaign['exchang_math']} => $exchangMath  及 {$itemCampaign['exchang_time']} => $exchangTime <br/> 外匯調整數 填寫日期，從{$itemCampaign['write_time']} => $writeTime",
				'times' => time(),
				'campaignid' => $campaignId
			], 'campaignstatus', 'insert');
			$db->query($sqlInsertLog);
		} else if ($action == 'confirm_receipted') {
			$title = '確認已開發票';

			if ($itemCampaign['is_receipt'] != 1) {
				IncludeFunctions('jsadways');

				if (IsId($itemCampaign['agency_id'])) {
					$objAgencyClient = CreateObject('Agency', $itemCampaign['agency_id']);
				} else {
					$objAgencyClient = CreateObject('Client', $itemCampaign['client_id']);
				}

				if (IsId($objAgencyClient->getId()) && empty($objAgencyClient->getVar('is_old'))) {
					$objAgencyClient->setVar('is_old', 1);
					$objAgencyClient->store();
				}

				$sqlUpdateCampaign = GenSqlFromArray([
					'is_receipt' => 1
				], 'campaign', 'update', ['id' => $campaignId]);
				$db->query($sqlUpdateCampaign);

				$sqlInsertLog = GenSqlFromArray([
					'name' => $_SESSION['username'],
					'data' => '按下確認已開發票',
					'times' => time(),
					'campaignid' => $campaignId
				], 'campaignstatus', 'insert');
				$db->query($sqlInsertLog);

				$itemCampaign['is_receipt'] = 1;
				checkCampaignAndClose($campaignId, $itemCampaign);
			}
		} else if ($action == 'add_media_cost_income') {
			$title = '媒體調整 收入/成本/日期 設定中';

			$media_chang_name = GetVar('media_chang_name', '');
			$media_chang_income = GetVar('media_chang_income', '');
			$media_chang_cost = GetVar('media_chang_cost', '');
			$media_chang_time = GetVar('media_chang_time', '');
			$media_chang_note = GetVar('media_chang_note', '');

			$media_chang_time = explode('/', str_replace('-', '/', $media_chang_time));
			if (strlen($media_chang_time[0]) == 2) {
				$media_chang_time = "$media_chang_time[2]-$media_chang_time[0]-$media_chang_time[1]";
			} else {
				$media_chang_time = implode('-', $media_chang_time);
			}

			$str = explode(",", $media_chang_name);
			
			$str2 = explode("-", $str[0]);
			$media_id = $str2[0];	//媒體id
			$media_sn = $str2[1];	//該媒體內編號
			$media_name = $str[1];  //媒體名稱

			$sqlInsertChange = GenSqlFromArray([
				'campaign_id' => $campaignId,
				'change_income' => $media_chang_income,
				'change_cost' => $media_chang_cost,
				'change_date' => $media_chang_time,
				'media_id' => $media_id,
				'media_sn' => $media_sn,
				'media_name' => $media_name,
				'note' => $media_chang_note,
				'save_date' => date('Y-m-d H:i:s')
			], 'media_change', 'insert');
			$db->query($sqlInsertChange);

			$sqlInsertLog = GenSqlFromArray([
				'name' => $_SESSION['username'],
				'data' => "增加媒體 {$str[0]} {$media_name} ，調整收入 => {$media_chang_income}，調整成本 => {$media_chang_cost}，日期 => {$media_chang_time}",
				'times' => time(),
				'campaignid' => $campaignId
			], 'campaignstatus', 'insert');
			$db->query($sqlInsertLog);
		} else if ($action == 'load_history') {
			$feedback = [
				'success' => true,
				'failure' => null,
				'data' => []
			];
			
			$sqlHistory = sprintf("SELECT * FROM `campaignstatus` WHERE `campaignid` = %d ORDER BY `times` DESC, `id` DESC;", $campaignId);
			$db->query($sqlHistory);
			while ($itemHistory = $db->next_record()) {
				$feedback['data'][] = [
					'data' => $itemHistory['data'],
					'name' => $itemHistory['name'],
					'times' => date('Y-m-d H:i:s', $itemHistory['times']),
				];
			}

			PrintJsonData($feedback, true);
		} else if ($action == 'load_history2') {
			$feedback = [
				'success' => true,
				'failure' => null,
				'data' => []
			];
			
			$sqlHistory = sprintf("SELECT * FROM `campaignstatus2` WHERE `campaignid` = %d ORDER BY `times` DESC, `id` DESC;", $campaignId);
			$db->query($sqlHistory);
			while ($itemHistory = $db->next_record()) {
				$feedback['data'][] = [
					'data' => $itemHistory['data'],
					'name' => $itemHistory['name'],
					'times' => date('Y-m-d H:i:s', $itemHistory['times']),
				];
			}

			PrintJsonData($feedback, true);
		} else if ($action == 'save_media_accounting') {
			IncludeFunctions('jsadways');

			$isGrantedForCampaignEntry = IsPermitted('finacial', null, Permission::ACL['finacial_ignore_campaign_closed_entry']);
			$closeEntryFlag = $GLOBALS['app']->preference->get('close_entry_flag');
			$closeEntryFlag = $closeEntryFlag ? $closeEntryFlag : date('Ym', strtotime('-1 month'));

			//$thisMonth = date('Ym');

			if ($isGrantedForCampaignEntry 
				|| GetVar('accounting_month') > $closeEntryFlag 
				|| getCampaignClosedEntryStatus($campaignId)) {

				$result = updateMediaItemAccounting([
					'campaign' => $campaignId,
					'ordinal' => GetVar('accounting_media_ordinal'),
					'item' => GetVar('accounting_media_item'),
					'month' => GetVar('accounting_month'),
					'revenue' => GetVar('accounting_revenue'),
					'curr_cost' => GetVar('curr_cost'),
					'currency_id' => GetVar('currency_id'),
					'cost' => GetVar('accounting_cost'),
					'invoice_number' => GetVar('invoice_number'),
					'invoice_date' => GetVar('invoice_date'),
					'comment' => GetVar('accounting_comment')
				]);

				if($result === true){
					$feedback = [
						'success' => 1, 
						'failure' => null,
						'data' => [
							'accounting_revenue' => GetVar('accounting_revenue'), 
							'accounting_cost' => GetVar('accounting_cost')
						]
					];
				}else{
					$feedback = [
						'success' => null, 
				   		'failure' => 1,
						'message' => '更新資料發生錯誤'
					];
				}
				
			} else {
				$feedback = [
					'success' => null, 
					'failure' => 1,
					'message' => '此月份財報已關帳'
				];
			}
						
			if ($isAjax) {
				PrintJsonData($feedback, true);
			}

		} else if ($action == 'save_blogger_chargeoff') {
			$objBloggerBank = CreateObject('BloggerBank');
			IncludeFunctions('jsadways');
			
			$accountingMonth = GetVar('accounting_month');

			$title = '輸入寫手成本';

			$costDate = date('Y-m-05', strtotime($accountingMonth .'01'));
			$mediaId = GetVar('mediaid') ? GetVar('mediaid') : GetVar('media_id');
			$bloggerId = GetVar('blogger_id');
			$bloggerDetailId = GetVar('blogger_detail_id');
			
			if (IsId($bloggerId) && IsId($bloggerDetailId)) {
				$sqlCheck = sprintf("SELECT * FROM blogger_chargeoff 
									WHERE campaign_id = %d 
									AND blogger_id = %d 
									AND blogger_detail_id = %d
									AND cost_date = '%s';", 
										$campaignId, $bloggerId, $bloggerDetailId, $costDate
								);
				$db->query($sqlCheck);
		
				if ($itemChargeOff = $db->next_record()) {
					$logData = '';

					if ($itemChargeOff['price'] != GetVar('cost')) {
						$logData .= ', 成本: '. $_POST['cost'];
					}

					if ($itemChargeOff['chargeoff_date'] != GetVar('chargeoff_date')) {
						$logData .= ', 付款日期: '. GetVar('chargeoff_date');
					}

					$objBloggerBank->load(GetVar('bloggerBankId'));
					if ($itemChargeOff['bankId'] != $objBloggerBank->getId()) {
						$logData .= ', 帳戶: '. (IsId($objBloggerBank->getId()) ? ($objBloggerBank->getVar('bankUserName') .'-'. $objBloggerBank->getVar('bankName')) : ' -- ');
					}

					if ($itemChargeOff['remark'] != GetVar('remark')) {
						$logData .= ', 備註: '. GetVar('remark');
					}

					$logData = '更新寫手成本資訊('. $_POST['blogger_name'] .'), 月份: '. (int)date('m', strtotime(GetVar('accounting_month') .'01')) .'月('. GetVar('accounting_month') .')'. (empty($logData) ? ' ***僅按儲存鍵而已, 無修改任何資料' : $logData);

					$sqlUpdateChargeOff = GenSqlFromArray([
						'price' => GetVar('cost'),
						'remark' => GetVar('remark'),
						'update_time' => date('Y-m-d H:i:s'),
						'name' => $_SESSION['username'],
						'chargeoff_date' => GetVar('chargeoff_date', ''),
						'bankId' => $objBloggerBank->getId()
					], 'blogger_chargeoff', 'update', [
						'campaign_id' => $campaignId,
						'blogger_id' => $bloggerId,
						'blogger_detail_id' => $bloggerDetailId,
						'cost_date' => $costDate
					]);
					$db->query($sqlUpdateChargeOff);
				} else {
					$logData = '輸入寫手成本資訊('. $_POST['blogger_name'] .'), 月份: '. (int)date('m', strtotime(GetVar('accounting_month') .'01')) .'月('. GetVar('accounting_month') .'), 成本: '. $_POST['cost'] .', 付款日期: '. date("Y-m",strtotime($_POST['chargeoff_date']));
				
					if (IsId(GetVar('bloggerBankId')) && $objBloggerBank->load(GetVar('bloggerBankId'))) {
						$logData .= ', 帳戶: '. $objBloggerBank->getVar('bankUserName') .'-'. $objBloggerBank->getVar('bankName');
					}

					if (GetVar('remark')) {
						$logData .= ', 備註: '. GetVar('remark');
					}

					$sqlInsertChargeOff = GenSqlFromArray([
						'campaign_id' => $campaignId,
						'blogger_id' => $bloggerId,
						'blogger_detail_id' => $bloggerDetailId,
						'chargeoff_date' => GetVar('chargeoff_date', ''),
						'price' => GetVar('cost'),
						'remark' => GetVar('remark'),
						'create_time' => date('Y-m-d H:i:s'),
						'name' => $_SESSION['username'],
						'cost_date' => $costDate,
						'bankId' => $objBloggerBank->getId(),
					], 'blogger_chargeoff', 'insert');
					$db->query($sqlInsertChargeOff);
				}
		
				$sqlTotalChargeOff = sprintf("SELECT SUM(price) as TotalCost FROM blogger_chargeoff 
											WHERE cost_date = '%s' 
											AND campaign_id = %d;", 
												$costDate, $campaignId
										);
				$db->query($sqlTotalChargeOff);
				$item = $db->next_record();
				$totalCost = $item['TotalCost'];
				
				$sqlInsertLog = GenSqlFromArray([
					'name' => $_SESSION['username'],
					'data' => $logData,
					'times' => time(),
					'campaignid' => $campaignId
				], 'campaignstatus', 'insert');
				$db->query($sqlInsertLog);

				$closeEntryFlag = $GLOBALS['app']->preference->get('close_entry_flag');
				$closeEntryFlag = $closeEntryFlag ? $closeEntryFlag : date('Ym', strtotime('-1 month'));
				if (GetVar('accounting_month') <= $closeEntryFlag) {
					// 2018-01-23 (Jimmy): 如已關帳的話, 需檢查成本是否有異動, 如有異動則通知財務組
					$objMediaAccounting = CreateObject('MediaAccounting');
					$conditions = [
						sprintf("`accounting_campaign` = %d", $campaignId),
						sprintf("`accounting_media_ordinal` = %d", GetVar('media')),
						sprintf("`accounting_media_item` = %d", $mediaId),
						sprintf("`accounting_month` = %d", GetVar('accounting_month')),
					];
					$rowsAccounting = $objMediaAccounting->search($conditions);
					
					$queueExecuteTime = strtotime(date('Y-m-d 09:30:00', strtotime('+1 days')));
					$queueContent = [
						'campaign_id' => $campaignId,
						'accounting_month' => GetVar('accounting_month'),
						'media_item_id' => $mediaId,
						'origin_media_cost' => $rowsAccounting[0]['accounting_cost'] ? $rowsAccounting[0]['accounting_cost'] : 0,
						'current_media_cost' => $totalCost,
						'detail_id' => $bloggerDetailId,
						'blogger_id' => $bloggerId,
						'origin_blogger_cost' => isset($itemChargeOff['price']) ? $itemChargeOff['price'] : 0,
						'current_blogger_cost' => $_POST['cost'],
					];
					$queueData = [
						'type' => 'blogger-exception', 
						'subject' => sprintf('寫手成本異動通知 (%s)',date('Y-m-d')),
						'content' => json_encode($queueContent) ."\n"
					];
					$queueName = "blogger{$GLOBALS['env']['flag']['pos']}-exception-". date('Y-m-d');
					AppendContentToQueue($queueName, $queueData, 'content', $queueExecuteTime);
				} else {
					$result = updateMediaItemAccounting([
						'campaign' => $campaignId,
						'ordinal' => GetVar('media'),
						'item' => $mediaId,
						'month' => GetVar('accounting_month'),
						'cost' => $totalCost
					], false, ['cost']);
	
					$logData = "更新成本(寫手費)=>成本: {$totalCost}。";
					$sqlInsertLog = GenSqlFromArray([
						'name' => $_SESSION['username'],
						'data' => $logData,
						'times' => time(),
						'campaignid' => $campaignId
					], 'campaignstatus', 'insert');
					$db->query($sqlInsertLog);
				}
			}

			if ($isAjax) {
				PrintJsonData([
					'success' => 1,
					'data' => $totalCost
				], true);
			}
		} else if ($action == 'change_media_status') {
			$mediaStatus = GetVar('status', null);
			$mediaOrdinal = GetVar('media', null);
			$mediaItemId = GetVar('mid', null);

			$sqlMediaDetal = sprintf("SELECT * FROM `media%d` WHERE `id` = %d;", $mediaOrdinal, $mediaItemId);
			$db->query($sqlMediaDetal);
			$itemMediaDetail = $db->next_record();

			$objMedia = CreateObject('Media', $mediaOrdinal);
			
			$statusData = '';
			if ($mediaStatus == Media::STATUS['PROCESSING']) {
				$statusData = $objMedia->getVar('name') .' - 媒體執行';
			} else if ($mediaStatus == Media::STATUS['PAUSE']) {
				$statusData = $objMedia->getVar('name') .' - 媒體暫停';
			} else if ($mediaStatus == Media::STATUS['CLOSED']) {
				$statusData = $objMedia->getVar('name') .' - 媒體結案';
			} else {
				$statusData = '未知的操作';
			}

			$sqlInsertLog = GenSqlFromArray([
				'name' => $_SESSION['username'],
				'data' => $statusData,
				'times' => time(),
				'campaignid' => $campaignId
			], 'campaignstatus', 'insert');
			$db->query($sqlInsertLog);

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

			$isClosed = false;
			IncludeFunctions('jsadways');
			if (checkCampaignAndClose($campaignId, $itemCampaign)) {
				$isClosed = true;
			}

			if ($isAjax) {
				PrintJsonData([
					'success' => 1,
					'data' => [
						'ordinal' => $mediaOrdinal,
						'item' => $mediaItemId,
						'status' => $mediaStatus,
						'closed' => $isClosed ? 1: 0
					]
				], true);
			}
		} else if ($action == 'unlock_receipt_effective') {
			if (IsPermitted('finacial', null, Permission::ACL['finacial_unlock_receipt_effective'])) {
				$title = '此案件已解除封印';

				$receiptList = [];
				$objReceipt = CreateObject('Receipt');
				foreach ($objReceipt->searchAll("`status` = 1 AND `campaign_id` = $campaignId", '', '', '', '', '`receipt_number`') as $itemReceipt) {
					array_push($receiptList, $itemReceipt['receipt_number']);
				}

				if (count($receiptList)) {
					$sqlInsertLog = GenSqlFromArray([
						'name' => $_SESSION['username'],
						'data' => '發票狀態異動 ('. implode('、', $receiptList) .')',
						'times' => time(),
						'campaignid' => $campaignId
					], 'campaignstatus', 'insert');
					$db->query($sqlInsertLog);
				}

				$sqlUpdatedReceipt = GenSqlFromArray([
					'status' => 9
				], 'receipt', 'update', [
					'status' => 1,
					'campaign_id' => $campaignId
				]);
				$db->query($sqlUpdatedReceipt);

				$twig = CreateObject('Twig', __DIR__ .'/templates', 'mail_for_unlock_receipt_effective.html', [
					'id' => $itemCampaign['id'],
					'name' => $itemCampaign['name'],
					'flag' => $GLOBALS['env']['flag'],
				]);
				$mailSubject = sprintf('【案件封印解除通知】%s (%s)', $itemCampaign['name'], date('Y-m-d'));
				AddMailToQueue($objMrbsUsers->getVar('email'), ucfirst($objMrbsUsers->getVar('name')) . $objMrbsUsers->getVar('username'), $twig->getContent(), $mailSubject, "{$GLOBALS['env']['flag']['name']}廣告後台");

				$queueExecuteTime = strtotime(date('Y-m-d 09:30:00', strtotime('+1 days', time())));
				$queueData = ['type' => 'lock-receipt-effective', 'campaign_id' => $campaignId];
				$queueName = "campaign{$GLOBALS['env']['flag']['pos']}-{$campaignId}-lock-receipt-effective";
				AppendContentToQueue($queueName, $queueData, null, $queueExecuteTime);
			} else {
				$title = '沒有操作的權限';
			}
		} else if ($action == 'lock_receipt_effective') {
			if (IsPermitted('finacial', null, Permission::ACL['finacial_unlock_receipt_effective'])) {
				$title = '此案件已封印';

				$receiptList = [];
				$objReceipt = CreateObject('Receipt');
				foreach ($objReceipt->searchAll("`status` = 9 AND `campaign_id` = $campaignId", '', '', '', '', '`receipt_number`') as $itemReceipt) {
					array_push($receiptList, $itemReceipt['receipt_number']);
				}

				if (count($receiptList)) {
					$sqlInsertLog = GenSqlFromArray([
						'name' => $_SESSION['username'],
						'data' => '發票異動還原 ('. implode('、', $receiptList) .')',
						'times' => time(),
						'campaignid' => $campaignId
					], 'campaignstatus', 'insert');
					$db->query($sqlInsertLog);
				}

				$sqlUpdatedReceipt = GenSqlFromArray([
					'status' => 1
				], 'receipt', 'update', [
					'status' => 9,
					'campaign_id' => $campaignId
				]);
				$db->query($sqlUpdatedReceipt);
			} else {
				$title = '沒有操作的權限';
			}
		} else if ($action == 'unlock_campaign_finacial_fields') {
			if (IsPermitted('finacial', null, Permission::ACL['finacial_unlock_campaign_closed_entry'])) {
				$title = '已解除關帳月份的收入成本欄位';

				IncludeFunctions('jsadways');
				unlockCampaignClosedEntry($campaignId);

				$sqlInsertLog = GenSqlFromArray([
					'name' => $_SESSION['username'],
					'data' => '解除已關帳月份的收入成本欄位限制',
					'times' => time(),
					'campaignid' => $campaignId
				], 'campaignstatus', 'insert');
				$db->query($sqlInsertLog);
			} else {
				$title = '沒有操作的權限';
			}
		} else if ($action == 'lock_campaign_finacial_fields') {
			if (IsPermitted('finacial', null, Permission::ACL['finacial_unlock_campaign_closed_entry'])) {
				$title = '已鎖定關帳月份的收入成本欄位';

				IncludeFunctions('jsadways');
				lockCampaignClosedEntry($campaignId);

				$sqlInsertLog = GenSqlFromArray([
					'name' => $_SESSION['username'],
					'data' => '鎖定已關帳月份的收入成本欄位',
					'times' => time(),
					'campaignid' => $campaignId
				], 'campaignstatus', 'insert');
				$db->query($sqlInsertLog);
			} else {
				$title = '沒有操作的權限';
			}
		}
	}

	$redirectPath = empty($refer) ? sprintf('campaign_view.php?id=%d', $campaignId) : $refer;
	ShowMessageAndRedirect($title, $redirectPath, false);
