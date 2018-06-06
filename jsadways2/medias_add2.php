<?php
	
	// 2018-02-22 (Jimmy): 傑思jsadways2/media_add2.php, 香港jsadways2hk/media_add2.php, 豐富媒體jsadways2ff/media_add2.php 共用此檔案
	require_once dirname(__DIR__) .'/autoload.php';
	
	$media = GetVar('media');

	if (empty($GLOBALS['env']['flag']['pos']) && $_POST['media'] == 162) {
		$uri = 'mtype_Handwriting_edit.php?campaign_id='. $_GET['id'] .'&cue='. $_GET['cue'] .'&media='. $_POST['media'] .'&media2='. $_GET['media2'] .'&mediaid='. $_GET['mediaid'];
	} else {
		$uri = 'mtype_Handwriting_add.php?id='. $_GET['id'] .'&cue='. $_GET['cue'] .'&media='. $_POST['media'] .'&media2='. $_GET['media2'] .'&mediaid='. $_GET['mediaid'];
	}

	ShowMessageAndRedirect('Loading', $uri, false);
