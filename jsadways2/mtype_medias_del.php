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
	// echo $_REQUEST['another_cue'];
	// print_r($_REQUEST);
	// 	exit();

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

	//jackie 2018/06/07 刪除對內/對外表
	//從compaign_view以$_GET['item_seq']接值，如果是1就是單純刪對內/對外，如果是2就是一次刪對內外
	if($itemMediaCue['cue']==1){
	$sqlDelSQL = "DELETE FROM `cp_detail` where `item_seq` = '".$_GET['item_seq']."'";
	$db->query($sqlDelSQL);
	}
	else{
	//因為對內的流水編號是對外的流水編號+1，所以新變數接$_GET['item_seq']然後+1
	//執行兩段sql結束
	$item_seq=$_GET['item_seq']+1;
	$sqlDelSQL = "DELETE FROM `cp_detail` where `item_seq` = '".$_GET['item_seq']."'";
	$sqlDelSQL2 = "DELETE FROM `cp_detail` where `item_seq` = '".$item_seq."'";
	$db->query($sqlDelSQL);
	$db->query($sqlDelSQL2);
	}

	ShowMessageAndRedirect('刪除媒體成功', 'campaign_view.php?id='. $_GET['campaign'], false);
