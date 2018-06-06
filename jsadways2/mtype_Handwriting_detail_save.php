<?php
    
	require_once dirname(__DIR__) .'/autoload.php';

	IncludeFunctions('jsadways');

    $mediaId = GetVar('media_id');
	$campaignId = GetVar('campaign_id');
    $blogId = GetVar('blog_id');
    $detailId = GetVar('detail_id');

	$priceType = getInfluencerPriceType();
    $db = clone($GLOBALS['app']->db);
    
	$typeString = [];
	foreach ($priceType as $idxType => $itemType) {
		if (isset($_POST["price{$idxType}"])) {
			$typeString[] = $itemType['text'];
		}
	}
	$typeString = implode('、', $typeString);
    
    $saveData = [
        'blog' => GetVar('blog'),
        'blog1' => GetVar('blog1'),
        'blog2' => GetVar('blog2'),
        'blog3' => GetVar('blog3'),
        'price' => GetVar('totalprice'),
        'price2' => GetVar('totalprice2'),
        'price3' => GetVar('totalprice3'),
        'others' => GetVar('others'),
        'type' => $typeString,
        'times' => time(),
    ];

    if (IsId($detailId)) {
        $sqlUpdate = GenSqlFromArray($saveData, 'media162_detail', 'update', [
            'id' => $detailId,
            'campaign_id' => $campaignId,
            'blogid' => $blogId,
        ]);
        $db->query($sqlUpdate);
    } else {
        $saveData += [
            'campaign_id' => $campaignId,
            'blogid' => $blogId,
            'status' => 0
        ];

        $db->query(GenSqlFromArray($saveData, 'media162_detail', 'insert'));
    }

    ShowMessageAndRedirect(IsId($detailId) ? '修改媒體成功' : '新增媒體成功', "mtype_Handwriting_edit.php?campaign_id={$campaignId}&media_id={$mediaId}", false);
