<?php
    
    require_once dirname(__DIR__) .'/autoload.php';
    
    $mediaId = GetVar('media_id');
    $campaignId = GetVar('campaign');
    $blogId = GetVar('blogid');

    $sqlDelete = sprintf("DELETE FROM media162_detail WHERE id = %d AND campaign_id = %d;", $blogId, $campaignId);
    $GLOBALS['app']->db->query($sqlDelete);

    // ShowMessageAndRedirect('刪除寫手成功', "mtype_Handwriting_edit.php?campaign_id={$campaignId}&media_id={$mediaId}", false);
	$arrItems = array();
    $arrItems[] = array("key" => "result", "name" => "OK");

    echo json_encode($arrItems);