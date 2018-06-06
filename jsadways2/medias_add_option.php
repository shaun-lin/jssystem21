<?php
	
	// 2018-06-04 (Austin): 傑思jsadways2/media_add_option.php
	require_once dirname(__DIR__) .'/autoload.php';
	$group = GetVar('group');
	
    //取得項目資料
	if($group == "media"){

		$objItems = CreateObject('Items');

		$arrItems = array();
		$id = GetVar('id');
		$sql = sprintf(" `id`  IN (SELECT `item_id` FROM `rel_media_item` WHERE `media_id` ='%d') and display = '1'",$id);
			foreach ($objItems->searchAll($sql,'name','ASC') as $itemItem) {
				$item_id=$itemItem['id'];
				$item_name=$itemItem['name'];

				$arrItems[]=array("key"=>$item_id,"name"=>$item_name);
			}
		echo json_encode($arrItems);
	}
	//取得賣法資料
	else if ($group=="items"){
		$id =GetVar('id');
		$media_id = GetVar('media_id');
		$campign_id = GetVar('campign_id');

		// print_r($media_id);
		// print_r($campign_id);
		$objCpDetail = CreateObject('Cp_detail');
		$condition = sprintf(" `cp_id` = %d and `media_id` = %d and `item_id` = %d", $campign_id ,$media_id, $id );
		 // print_r($condition);
		$resCpDetail = $objCpDetail->searchAll($condition);
		$arrCpdetail = array();
		foreach ($objCpDetail->searchAll($condition) as $itemCpDetail) {
			$arrCpdetail[] = $itemCpDetail['mtype_id'];
		}
		 // print_r($arrCpdetail);
		$objItems=CreateObject('Mtype');
		$arrItems=array();
		
		$sql=sprintf(" `id`  IN (SELECT `type_id` FROM `rel_items_type` WHERE `items_id` ='%d') and display = '1'",$id);
		foreach($objItems->searchAll($sql,'name','ASC') as $itemItem){
				$item_id=$itemItem['id'];
				if(in_array($item_id, $arrCpdetail)){
					$item_name = "(已設定)".$itemItem['name'];
				}else{
					$item_name = $itemItem['name'];
				}
				$arrItems[] = array("key"=>$item_id,"name"=>$item_name);
		}
		// echo count($arrItems);
		echo json_encode($arrItems);
	}

