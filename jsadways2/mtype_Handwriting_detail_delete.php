<?php
    
    require_once dirname(__DIR__) .'/autoload.php';
    
    $mediaId = GetVar('media_id');
    $campaignId = GetVar('campaign_id');
    $blogId = GetVar('blog_id');

    $sqlDelete = sprintf("DELETE FROM media19_detail WHERE id = %d AND campaign_id = %d;", $blogId, $campaignId);
    $GLOBALS['app']->db->query($sqlDelete);

    ShowMessageAndRedirect('刪除寫手成功', "media19_edit.php?campaign_id={$campaignId}&media_id={$mediaId}", false);
