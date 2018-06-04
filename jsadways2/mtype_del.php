<?php

	require_once dirname(__DIR__) .'/autoload.php';

	require_once __DIR__ .'/include/function.inc.php';

	$db = clone($GLOBALS['app']->db);

	$message = '';

	// if (isset($_REQUEST['another_cue'])) {
	// 	if ($_REQUEST['another_cue'] == 1) {
	// 		$sqlmedias = sprintf("SELECT `a0` as `id` FROM `medias%d` 
	// 					WHERE `status` = 0 AND `id` = %d;", 
	// 						$_GET['medias'], $_GET['id']);
	// 	} else if ($_REQUEST['another_cue'] == 2) {
	// 		$sqlmedias = sprintf("SELECT `id` FROM `medias%d` 
	// 							WHERE `status` = 0 AND `campaign_id` = %d 
	// 							AND `cue` = %d AND `a0` = %d;", 
	// 								$_GET['medias'], $_GET['campaign'], $_REQUEST['another_cue'], $_GET['id']);
	// 	} else {
	// 		$sqlmedias = null;
	// 	}
		
	// 	if ($sqlmedias) {
	// 		$db->query($sqlmedias);
			
	// 		if ($itemmedias = $db->next_record()) {
	// 			$db->query(sprintf("DELETE FROM `medias%d` WHERE `id` = %d;", $_GET['medis'], $itemmedias['id'])); 
	// 			RemovemediasMapping($_GET['medias'], $_GET['campaign'], $itemmedias['id']);
	// 			$message = '刪除媒體'
	// 		}
	// 	}
	// }

	// $objmedias = CreateObject('medias', $_GET['medias']);

	// $sqlmediasCue = sprintf("SELECT `cue` FROM `medias%d` WHERE `id` = %d", $_GET['medias'], $_GET['id']);
	// $db->query($sqlmediasCue);
	// $itemmediasCue = $db->next_record();
	
	// if (empty($message)) {
	// 	$message = "刪除". ($itemmediasCue['cue'] == 2 ? '對內' : '對外') ."媒體";
	// }
	// $message .= " (". $objmedias->getVar('name') .")";

	$sql2 = sprintf("DELETE FROM `mtype` WHERE `id` = %d", $_GET['id']);
	$sql3 =sprintf("DELETE FROM `rel_items_type` WHERE `type_id` = %d",$_GET['id']);
	$db->query($sql2);
	$db->query($sql3);
	//RemovemediasMapping($_GET['id']);

	// $sqlInserLog = GenSqlFromArray([
	// 	'name' => $_SESSION['username'],
	// 	'data' => $message,
	// 	'times' => time(),
	// 	'campaignid' => $_GET['id']
	// ], 'campaignstatus', 'insert');
	// $db->query($sqlInserLog);

	ShowMessageAndRedirect('刪除媒體成功', 'mtype_list.php', false);
