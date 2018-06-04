<?php
	
	// 2018-02-22 (Jimmy): 傑思jsadways2/media_add2.php, 香港jsadways2hk/media_add2.php, 豐富媒體jsadways2ff/media_add2.php 共用此檔案
	require_once dirname(__DIR__) .'/autoload.php';
	$group=GetVar('group');
    //取得項目資料
	if($group=="media"){

		$objItems=CreateObject('Items');

		$arrItems=array();
		$id =GetVar('id');
		$sql=sprintf(" `id`  IN (SELECT `item_id` FROM `rel_media_item` WHERE `media_id` ='%d') and display = '1'",$id[0]);
			foreach ($objItems->searchAll($sql,'name','ASC') as $itemItem) {
				$item_id=$itemItem['id'];
				$item_name=$itemItem['name'];

				$arrItems[]=array("key"=>$item_id,"name"=>$item_name);
			}
		echo json_encode($arrItems);
	}
	//取得賣法資料
	else if ($group=="items"){

		$objItems=CreateObject('Mtype');

		$arrItems=array();
		$id =GetVar('id');
		$sql=sprintf(" `id`  IN (SELECT `type_id` FROM `rel_items_type` WHERE `items_id` ='%d') and display = '1'",$id[0]);
			foreach ($objItems->searchAll($sql,'name','ASC') as $itemItem) {
				$item_id=$itemItem['id'];
				$item_name=$itemItem['name'];

				$arrItems[]=array("key"=>$item_id,"name"=>$item_name);
			}
		// echo count($arrItems);
		echo json_encode($arrItems);
	}
	// $media = GetVar('media');

	// if (empty($GLOBALS['env']['flag']['pos']) && $_POST['media'] == 19) {
	// 	$uri = 'media19_edit.php?campaign_id='. $_GET['id'] .'&cue='. $_GET['cue'] .'&media='. $_POST['media'] .'&media2='. $_GET['media2'] .'&mediaid='. $_GET['mediaid'];
	// } else {
	// 	$uri = 'media'. $_POST['media'] .'_add.php?id='. $_GET['id'] .'&cue='. $_GET['cue'] .'&media='. $_POST['media'] .'&media2='. $_GET['media2'] .'&mediaid='. $_GET['mediaid'];
	// }

	// ShowMessageAndRedirect('Loading', $uri, false);
