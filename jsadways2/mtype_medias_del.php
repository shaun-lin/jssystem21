<?php

	require_once dirname(__DIR__) .'/autoload.php';

	require_once __DIR__ .'/include/function.inc.php';

	$db = clone($GLOBALS['app']->db);

	$message = '';

	if (isset($_REQUEST['another_cue'])) {
		if ($_REQUEST['another_cue'] == 1) {
			$sqlMedia = sprintf("SELECT `a0` as `id` FROM `media%d` 
						WHERE `status` = 0 AND `id` = %d;", 
							$_GET['media'], $_GET['id']);
		} else if ($_REQUEST['another_cue'] == 2) {
			$sqlMedia = sprintf("SELECT `id` FROM `media%d` 
								WHERE `status` = 0 AND `campaign_id` = %d 
								AND `cue` = %d AND `a0` = %d;", 
									$_GET['media'], $_GET['campaign'], $_REQUEST['another_cue'], $_GET['id']);
		} else {
			$sqlMedia = null;
		}
		
		if ($sqlMedia) {
			$db->query($sqlMedia);
			
			if ($itemMedia = $db->next_record()) {
				$db->query(sprintf("DELETE FROM `media%d` WHERE `id` = %d;", $_GET['media'], $itemMedia['id'])); 
				RemoveMediaMapping($_GET['media'], $_GET['campaign'], $itemMedia['id']);
				$message = '刪除媒體';
			}
		}
	}

	$objMedia = CreateObject('Media', $_GET['media']);

	$sqlMediaCue = sprintf("SELECT `cue` FROM `media%d` WHERE `id` = %d", $_GET['media'], $_GET['id']);
	$db->query($sqlMediaCue);
	$itemMediaCue = $db->next_record();
	
	if (empty($message)) {
		$message = "刪除". ($itemMediaCue['cue'] == 2 ? '對內' : '對外') ."媒體";
	}
	$message .= " (". $objMedia->getVar('name') .")";

	$sql2 = sprintf("DELETE FROM `media%d` WHERE `id` = %d", $_GET['media'], $_GET['id']);
    $db->query($sql2);
	RemoveMediaMapping($_GET['media'], $_GET['campaign'], $_GET['id']);

	$sqlInserLog = GenSqlFromArray([
		'name' => $_SESSION['username'],
		'data' => $message,
		'times' => time(),
		'campaignid' => $_GET['campaign']
	], 'campaignstatus', 'insert');
	$db->query($sqlInserLog);
	$sqlDelSQL = "DELETE FROM `cp_detail` where `cp_id` = '".$_GET['campaign']." and `mtype_number` = '".$_GET['media']."' and `cue` = '".$_GET['cue']."' and `media_id` = '".$_GET['id']."'";

	ShowMessageAndRedirect('刪除媒體成功', 'campaign_view.php?id='. $_GET['campaign'], false);
