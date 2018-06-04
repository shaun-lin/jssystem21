<?php

function checkCampaignAndClose($campaignId=0, $itemCampaign=[], $notify=true)
{
    if (IsId($campaignId)) {
        if (!property_exists($GLOBALS['app'], 'mrbsUsers') || !is_object($GLOBALS['app']->mrbsUsers)) {
            $GLOBALS['app']->mrbsUsers = CreateObject('MrbsUsers');
        }

        if (!property_exists($GLOBALS['app'], 'media') || !is_object($GLOBALS['app']->media)) {
            $GLOBALS['app']->media = CreateObject('Media');
        }

        $db = clone($GLOBALS['app']->db);

        if (empty($itemCampaign)) {
            $sqlCampaign = sprintf("SELECT * FROM `campaign` WHERE `id` = %d;", $campaignId);
            $db->query($sqlCampaign);
            $itemCampaign = $db->next_record();
        }

        $isReceipted = ($itemCampaign["is_receipt"] == 1);
        $isAllMediaClosed = true;

        $rowsOrdinal = GetUsedMediaOrdinal($campaignId);
        foreach ($rowsOrdinal as $itemOrdinalId) {
			$sqlMediaData = sprintf("SELECT * FROM `media%d` WHERE `cue` = 2 AND `campaign_id` = %d;", $itemOrdinalId, $campaignId);
			$db->query($sqlMediaData);
			while ($itemMediaData = $db->next_record()) {
				if ($itemMediaData['status'] != Media::STATUS['CLOSED']) {
					$isAllMediaClosed = false;
					break;
				}
			}

			if (!$isAllMediaClosed) {
				break;
			}
        }
        
        if ($isReceipted && $isAllMediaClosed) {
			$updateFields = [
				'status' => 4
			] + (isset($_POST['text9']) ? [
				'text9' => $_POST['text9'],
				'text10' => $_POST['text10'],
				'text11' => $_POST['text11'],
				'text12' => $_POST['text12'],
			] : []);
			$sqlUpdateCampaign = GenSqlFromArray($updateFields, 'campaign', 'update', ['id' => $campaignId]);
			$db->query($sqlUpdateCampaign);

			$sqlInsertCampaignStatus = GenSqlFromArray([
				'name' => $_SESSION['username'],
				'data' => '結案',
				'times' => time(),
				'campaignid' => $campaignId
			], 'campaignstatus', 'insert');
			$db->query($sqlInsertCampaignStatus);

			if ($notify === true && $GLOBALS['app']->mrbsUsers->load($itemCampaign['memberid'])) {
                $twig = CreateObject('Twig', dirname(__DIR__) .'/jsadways2/templates', 'mail_for_close_campaign.html', [
                    'id' => $itemCampaign['id'],
                    'name' => $itemCampaign['name'],
                    'flag' => $GLOBALS['env']['flag'],
                    'closedDate' => date('Y-m-d')
                ]);
                $mailSubject = sprintf('【傑思廣告後台】【案件已結案】%s (%s)', $itemCampaign['name'], date('Y-m-d'));
                
				AddMailToQueue($GLOBALS['app']->mrbsUsers->getVar('email'), ucfirst($GLOBALS['app']->mrbsUsers->getVar('name')) . $GLOBALS['app']->mrbsUsers->getVar('username'), $twig->getContent(), $mailSubject, '傑思廣告後台');
            }
            
            return true;
        }
        
        return false;
    }

    return null;
}

function updateMediaItemAccounting($data=[], $logging=true, $specificField=[])
{
    $campaignId = isset($data['campaign']) && IsId($data['campaign']) ? $data['campaign'] : 0;
    $ordinalId = isset($data['ordinal']) && IsId($data['ordinal']) ? $data['ordinal'] : 0;
    $itemId = isset($data['item']) && IsId($data['item']) ? $data['item'] : 0;
    $accountingMonth = isset($data['month']) && date('Ym', strtotime($data['month'] ."01")) == $data['month'] ? $data['month'] : 0;
    $revenue = isset($data['revenue']) && is_numeric($data['revenue']) ? $data['revenue'] : 0;
    $cost = isset($data['cost']) && is_numeric($data['cost']) ? $data['cost'] : 0;

    //ken,2018/5/22,新增三個欄位
    //ken,2018/5/31,新增兩個欄位
    $currency_id = isset($data['currency_id']) ? $data['currency_id'] : 'TWD';
    //$curr_cost = isset($data['curr_cost']) && is_float($data['curr_cost']) ? $data['curr_cost'] : 0;
    $curr_cost = $data['curr_cost'];

    $invoice_number = isset($data['invoice_number']) ? $data['invoice_number'] : 0;
    //$invoice_date = isset($data['invoice_date']) ? date('m/d/Y', strtotime($data['invoice_date'])) : 0;
    $invoice_date = isset($data['invoice_date']) ? $data['invoice_date'] : 0;
    //ken,如果有輸入發票,則記錄輸入時候的日期
    $input_invoice_month = isset($data['invoice_number']) ? date('Ym') : 0;

    $comment = isset($data['comment']) ? $data['comment'] : false;

    if (!IsId($ordinalId) || !IsId($itemId)) {
        return null;
    }

    $objMediaAccounting = CreateObject('MediaAccounting');

    $profit = $revenue - $cost;
    $marginGross = round(($profit / $revenue) * 10000) / 100;

    $conditions = [
        sprintf("`accounting_campaign` = %d", $campaignId),
        sprintf("`accounting_media_ordinal` = %d", $ordinalId),
        sprintf("`accounting_media_item` = %d", $itemId),
        sprintf("`accounting_month` = %d", $accountingMonth)
    ];
    $rowsAccounting = $objMediaAccounting->search($conditions);

    $isAmountChanged = true;
    
    if (count($rowsAccounting)) {
        $objMediaAccounting->load($rowsAccounting[0]['accounting_id']);

        if ($specificField && is_array($specificField)) {
            if (in_array('revenue', $specificField)) {
                $objMediaAccounting->setVar('accounting_revenue', $revenue);
            } else if (in_array('cost', $specificField)) {
                $objMediaAccounting->setVar('accounting_cost', $cost);
            }
        } else {
            $objMediaAccounting->setVar('accounting_revenue', $revenue);
            $objMediaAccounting->setVar('accounting_cost', $cost);
            $objMediaAccounting->setVar('accounting_profit', $profit);
            $objMediaAccounting->setVar('accounting_margin_gross', $marginGross);
        }
        //ken,2018/5/22,新增三個欄位
        //ken,2018/5/31,新增兩個欄位
        $objMediaAccounting->setVar('currency_id', $currency_id);
        $objMediaAccounting->setVar('curr_cost', $curr_cost);
        $objMediaAccounting->setVar('invoice_number', $invoice_number);
        $objMediaAccounting->setVar('invoice_date', $invoice_date);
        $objMediaAccounting->setVar('input_invoice_month', $input_invoice_month);

        $objMediaAccounting->store();

        $isAmountChanged = $rowsAccounting[0]['accounting_revenue'] != $revenue || $rowsAccounting[0]['accounting_cost'] != $cost;
    } else {
        $objMediaAccounting->bind([
            'accounting_campaign' => $campaignId,
            'accounting_media_ordinal' => $ordinalId,
            'accounting_media_item' => $itemId,
            'accounting_month' => $accountingMonth
        ]);

        if ($specificField && is_array($specificField)) {
            if (in_array('revenue', $specificField)) {
                $objMediaAccounting->setVar('accounting_revenue', $revenue);
            } else if (in_array('cost', $specificField)) {
                $objMediaAccounting->setVar('accounting_cost', $cost);
            }
        } else {
            $objMediaAccounting->setVar('accounting_revenue', $revenue);
            $objMediaAccounting->setVar('accounting_cost', $cost);
            $objMediaAccounting->setVar('accounting_profit', $profit);
            $objMediaAccounting->setVar('accounting_margin_gross', $marginGross);
        }
        //ken,2018/5/22,新增三個欄位
        //ken,2018/5/31,新增兩個欄位
        $objMediaAccounting->setVar('currency_id', $currency_id);
        $objMediaAccounting->setVar('curr_cost', $curr_cost);
        $objMediaAccounting->setVar('invoice_number', $invoice_number);
        $objMediaAccounting->setVar('invoice_date', $invoice_date);
        $objMediaAccounting->setVar('input_invoice_month', $input_invoice_month);

        $objMediaAccounting->store();
    }

    $db = clone($GLOBALS['app']->db);

    $itemMedia = GetMedia($ordinalId);

    if ($comment !== false) {
        $sqlUpdateMediaComment = GenSqlFromArray([
            'text13' => $comment
        ], sprintf('media%d', $ordinalId), 'update', ['id' => $itemId]);
        $db->query($sqlUpdateMediaComment);
    }

    //ken,調整,增加三個欄位
    //if ($logging === true && $isAmountChanged === true) {
    if ($logging === true ) {
        $logData = "輸入收入成本({$itemMedia['name']})=>收入: {$revenue}, 成本: {$cost}, 幣別: {$currency_id}, 發票編號: {$invoice_number}, 發票日期: {$invoice_date}。";
        $sqlInsertLog = GenSqlFromArray([
            'name' => $_SESSION['username'],
            'data' => $logData,
            'times' => time(),
            'campaignid' => $campaignId
        ], 'campaignstatus', 'insert');
        $db->query($sqlInsertLog);
    }

    unset($db);
    return true;
}

function getCampaignStatusLabelColor($status=0)
{
    switch ($status) {
        case 1:
            return 'warning';
        case 2:
            return 'info';
        case 3:
            return 'success';
        case 4:
            return 'important';
        case 6:
        case 7:
            return 'inverse';
    }
    
    return '';
}

function getCampaignStatusText($status=0)
{
    switch ($status) {
        case 1:
            return '尚未送審';
        case 2:
            return '送審中';
        case 3:
            return '執行中';
        case 4:
            return '已結案';
        case 5:
            return '暫停';
        case 6:
            return '中止';
        case 7:
            return '異常';
        case 8:
            return '作廢';
        case 9:
            return '作廢審核中';
    }

    return '';
}

function getMediaStatusText($status=0)
{
    switch ($status) {
        case 0:
            return '未執行';
        case 1:
            return '執行中';
        case 2:
            return '暫停';
        case 3:
            return '結案';
    }

    return '';
}

function genBloggerACCode($id=0)
{
    $db = clone($GLOBALS['app']->db);

    if (IsId($id)) {
        $sqlACCode = sprintf("SELECT `ac_id` FROM `blogger` WHERE `id` = %d;", $id);
        $db->query($sqlACCode);
        $itemACCode = $db->next_record();
    }

    if (isset($itemACCode['ac_id']) && $itemACCode['ac_id']) {
        return $itemACCode['ac_id'];
    } else {
        $sqlACCode = "SELECT MAX(`ac_id`) as `last_ac_id` FROM `blogger`";
        $db->query($sqlACCode);
        $itemACCode = $db->next_record();
    
        $lastACCode = $itemACCode['last_ac_id'];
        list($mainId, $miniId) = explode('-', $lastACCode);
    
        $miniId++;
        if ($miniId >= 1000) {
            $mainId++;
            $miniId = 1;
        }
    
        return sprintf('%02d-%03d', $mainId, $miniId);
    }
}

function displayModified($idx='', $data=[], $perm=null)
{
    if (isset($data[$idx]['modified'])) {
        return "<div class=\"modified-detail\">(上次更新日期 {$data[$idx]['modified']} <br/>By {$data[$idx]['modifier']})</div>";
    }
    
    return '';
}

function getInfluencerPriceText($price=0, $priceRateOption=0, $printUnit='')
{
    if ($price == -1) {
        return 'Free';
    } else if ($price) {
        return ($price * getInfuencerPriceRate('inner_tax_included', $priceRateOption)) . ($printUnit ? "/{$printUnit}" : '') ." (含稅)\n". ($price * getInfuencerPriceRate('outer_tax_included', $priceRateOption)) . ($printUnit ? "/{$printUnit}" : '') .' (含利潤)';
    }

    return $price;
}

function getInfluencerCostPriceText($price=0, $method='', $printUnit='')
{
    if ($price == -1) {
        return 'Free';
    } else if ($price) {
        return '$'. number_format(calcInfluenceCostPrice($price, $method)) . ($printUnit ? "/{$printUnit}" : '');
    }

    return $price;
}

function calcInfluenceCostPrice($price=0, $method='')
{
    $objInfluncer = CreateObject('Blogger');

    $rate = 1;

    switch ($method) {
        case Blogger::PAYMENT_METHOD['tax_included']:
        case Blogger::PAYMENT_METHOD['real_amount']:
        case Blogger::PAYMENT_METHOD['real_amount_without_2nhi']:
            $rate = getInfuencerPriceRate('inner_tax_included', $method);
            break;
        case Blogger::PAYMENT_METHOD['tax_excluded_with_invoice']:
            $rate = 1;
            break;
    }

    unset($objInfluncer);

    return $price * $rate;
}

function getInfuencerPriceRate($type='', $method='')
{
    $objInfluncer = CreateObject('Blogger');

    $result = 1;

    switch ($type) {
        case 'outer_tax_included':
            $result *= 1.25;
        case 'inner_tax_included':
            if ($method == Blogger::PAYMENT_METHOD['tax_included']) {
                $result *= 1;
            } else if ($method == Blogger::PAYMENT_METHOD['real_amount']) {
                $result /= 0.8809;
            } else if ($method == Blogger::PAYMENT_METHOD['tax_excluded_with_invoice']) {
                $result *= 1.05;
            } else if ($method == Blogger::PAYMENT_METHOD['real_amount_without_2nhi']) {
                $result /= 0.9;
            }
            break;
        default:
        case 'inner_tax_excluded':
        case 'invite_tax_excluded':
            $result *= 1;
            break;
    }
    
    unset($objInfluncer);

    return $result;
}

function getInfluencerPriceUnit($type='option-3-with-basic-unit')
{
    switch ($type) {
        case 'option-3-with-ultimate-unit':
            return [
                'option' => [
                    'N' => '不可',
                    'Y' => '可，稿費已含',
                    'Y+' => '可，$ _______ (平台/次/日/週/雙週/月/季/半年/年/不下架)'
                ],
                'unit' => [
                    '平台', '次', '日', '週', '雙週', '月', '季', '半年', '年', '不下架'
                ]
            ];
        case 'option-3-with-expert-unit':
            return [
                'option' => [
                    'N' => '不可',
                    'Y' => '可，稿費已含',
                    'Y+' => '可，$ _______ (平台/張/週/雙週/月/季/半年/年/不下架)'
                ],
                'unit' => [
                    '平台', '張', '週', '雙週', '月', '季', '半年', '年', '不下架'
                ]
            ];
        case 'option-3-with-advance-unit':
            return [
                'option' => [
                    'N' => '不可',
                    'Y' => '可，稿費已含',
                    'Y+' => '可，$ _______ (張/次/平台/日/週/雙週/月)'
                ],
                'unit' => [
                    '張', '次', '平台', '日', '週', '雙週', '月'
                ]
            ];
        case 'option-3-with-professional-unit':
            return [
                'option' => [
                    'N' => '不可',
                    'Y' => '可，稿費已含',
                    'Y+' => '可，$ _______ (次/日/週/雙週/月/季)'
                ],
                'unit' => [
                    '次', '日', '週', '雙週', '月', '季'
                ]
            ];
        case 'option-3-with-novice-unit':
            return [
                'option' => [
                    'N' => '不可',
                    'Y' => '可，稿費已含',
                    'Y+' => '可，$ _______ (週/雙週/月/季/半年)'
                ],
                'unit' => [
                    '週', '雙週', '月', '季', '半年'
                ]
            ];
        case 'option-3-with-basic-unit':
            return [
                'option' => [
                    'N' => '不可',
                    'Y' => '可，稿費已含',
                    'Y+' => '可，$ _______ (次/日/週/雙週/月)'
                ],
                'unit' => [
                    '次', '日', '週', '雙週', '月'
                ]
            ];
        case 'option-3-with-empty-unit':
            return [
                'option' => [
                    'N' => '不可',
                    'Y' => '可，稿費已含',
                    'Y+' => '可，$ _______'
                ],
                'unit' => []
            ];
        case 'option-2-with-basic-unit':
            return [
                'option' => [
                    'N' => '不可',
                    'Y+' => '可，$ _______ (次/日/週/雙週/月)'
                ],
                'unit' => [
                    '次', '日', '週', '雙週', '月'
                ]
            ];
        case 'option-2-with-single-unit':
            return [
                'option' => [
                    'N' => '不可',
                    'Y+' => '可，$ _______ /次'
                ],
                'unit' => [
                    '次'
                ]
            ];
        default:
            return [];
    }
}

function getInfluencerPriceType()
{
    return [
        1 => [
            'name' => 'blog_article_price',
            'text' => 'BLOG文章',
            'old' => 'BLOG文章價格',
        ], 
        2 => [
            'name' => 'fb_post_price',
            'text' => 'FB靜態圖文',
            'old' => 'FB文章連結轉po費用'
        ], 
        3 => [
            'name' => 'fb_video_price',
            'text' => 'FB影片',
            'old' => '#',
        ], 
        4 => [
            'name' => 'fb_live_price',
            'text' => 'FB直撥',
            'old' => '#',
        ], 
        5 => [
            'name' => 'ig_sidecar_price',
            'text' => 'Instagram靜態多圖文',
            'old' => '#',
        ], 
        6 => [
            'name' => 'youtube_video_price',
            'text' => 'YouTube影片',
            'old' => 'Youtube影片',
        ], 
        7 => [
            'name' => 'fbads_client_with_js_price',
            'text' => '加傑思為廣告主',
            'old' => '#',
        ], 
        8 => [
            'name' => 'auth_quote_to_website_with_feedback_price',
            'text' => '引用至官網/campaign(連回)',
            'old' => '#',
        ], 
        9 => [
            'name' => 'other_attend_without_interview_price',
            'text' => '靠櫃/活動出席',
            'old' => '出席費',
        ],
        10 => [
            'text' => '其他',
            'old' => [
                '轉分享至寫手粉絲團',
                '使用個人識別圖',
                '轉分享至客戶粉絲團費用',
                '平面廣編引用費',
                '網路全平台引用費',
                '棚拍費'
            ],
        ]
    ];
}

function closeEntry($closeDate='')
{
    if ($closeDate) {
        $GLOBALS['app']->preference->set('close_entry_flag', $closeDate);
        $GLOBALS['app']->preference->set('close_entry_stamp', time());
        $GLOBALS['app']->preference->set('close_entry_user', $_SESSION['username']);
        $GLOBALS['app']->preference->store();
    }
}

function getCampaignClosedEntryStatus($campaignId=0)
{
    if (IsId($campaignId)) {
        return $GLOBALS['app']->preference->get("unlock_campaign{$GLOBALS['env']['flag']['pos']}_closed_entry", $campaignId);
    }

    return null;
}

function unlockCampaignClosedEntry($campaignId=0)
{
    if (IsId($campaignId)) {
        $GLOBALS['app']->preference->set("unlock_campaign{$GLOBALS['env']['flag']['pos']}_closed_entry", $campaignId, time());
        $GLOBALS['app']->preference->store();
    }
}

function lockCampaignClosedEntry($campaignId=0)
{
    if (IsId($campaignId)) {
        $GLOBALS['app']->preference->drop("unlock_campaign{$GLOBALS['env']['flag']['pos']}_closed_entry", $campaignId);
        $GLOBALS['app']->preference->store();
    }
}
