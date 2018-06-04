<?php

    set_time_limit(-1);
    ini_set('display_errors', 1);
    require_once dirname(__DIR__) .'/autoload.php';

    $endStamp = strtotime(date('Y-m-01', strtotime('-2 months')));
    $endDate = date('Y-m-t', $endStamp);
    $endStamp = strtotime("$endDate 23:59:59");

    $startStamp = AddMonthToDate(-1, date('Y-m-01', $endStamp), true);
    $startDate = date('Y-m-01', $startStamp);

    $endMonth = date('Ym', $endStamp);
    $startMonth = date('Ym', $startStamp);
    
    echo "$startDate $endDate<br/>";

    $sqlCampaign = "SELECT `id`, `name`, `date11`, `date22`, `memberid`, 
                    `status`, `action1`, `action2`, `action3` FROM `campaign` 
                    WHERE (
                        `id` IN ( 
                            SELECT `accounting_campaign` FROM `media_accounting` 
                            WHERE `accounting_campaign` IN (
                                SELECT `id` FROM `campaign` 
                                WHERE `status` IN (2, 3, 4, 5, 6, 7) 
                                AND `is_receipt` = 0
                            ) AND `accounting_month` >= $startMonth 
                            AND `accounting_month` <= $endMonth 
                            AND ( `accounting_revenue` != 0 OR `accounting_cost` != 0 ) 
                            GROUP BY `accounting_campaign` 
                            UNION 
                            SELECT `campaign_id` FROM `media_change` 
                            WHERE `campaign_id` IN (
                                SELECT `id` FROM `campaign` 
                                WHERE `status` IN (2, 3, 4, 5, 6, 7) 
                                AND `is_receipt` = 0
                            ) AND ( `change_income` != 0 OR `change_cost` != 0 ) 
                            AND `change_date` >= '$startDate' 
                            AND `change_date` <= '$endDate' 
                        )
                    ) AND `is_receipt` = 0 
                    AND `date11` >= $startStamp 
                    AND `date22` <= $endStamp
                    AND `date22` >= 1514736000;";

    $dbCampaign = clone($GLOBALS['app']->db);
    $dbReceipt = clone($GLOBALS['app']->db);
    $dbMediaOrdinal = clone($GLOBALS['app']->db);

    $campaigns = [];

    $dbCampaign->query($sqlCampaign);
    while ($itemCampaign = $dbCampaign->next_record()) {
        $campaigns[$itemCampaign['id']] = $itemCampaign + [
            'ext_cue_price' => 0, 
            'total_price' => 0, 
            'receipt' => []
        ];

        foreach (GetUsedMediaOrdinal($itemCampaign['id']) as $idxOrdinal) {
            $sqlMediaOrdinal = "SELECT SUM(`totalprice`) as `total` FROM `media{$idxOrdinal}` WHERE `cue` = 1 AND `campaign_id` = {$itemCampaign['id']};";
            $dbMediaOrdinal->query($sqlMediaOrdinal);
            if ($itemMediaOrdinal = $dbMediaOrdinal->next_record()) {
                $campaigns[$itemCampaign['id']]['ext_cue_price'] += $itemMediaOrdinal['total'];
            }
        }

        $sqlReceipt = "SELECT * FROM `receipt` WHERE `status` = 1 AND `campaign_id` = {$itemCampaign['id']}";
        $dbReceipt->query($sqlReceipt);
        while ($itemReceipt = $dbReceipt->next_record()) {
            $campaigns[$itemCampaign['id']]['total_price'] += $itemReceipt['totalprice1'];
            $campaigns[$itemCampaign['id']]['receipt'][$itemReceipt['id']] = $itemReceipt['id'];
        }
    }

    $sales = [];
    foreach ($campaigns as $itemCampaign) {
        $isPriceEqual = (round($itemCampaign['total_price']) == round($itemCampaign['ext_cue_price']));
        $diffPrice = abs(round($itemCampaign['total_price']) - round($itemCampaign['ext_cue_price']));

        if (!$isPriceEqual && $diffPrice > 20) {
            if (round($itemCampaign['total_price']) == 0 && count($itemCampaign['receipt'])) {

            } else {
                $sales[$itemCampaign['memberid']][$itemCampaign['id']] = $itemCampaign;
            }
        }
    }
    
    foreach ($sales as $saleId => $saleCampaign) {
        $assignForNonReceipt = [];
        $assignForLostReceipt = [];

        foreach ($saleCampaign as $campaignId => $campaignData) {
            if ($campaignData['total_price']) {
                $assignForLostReceipt[] = [
                    'campaignId' => $campaignData['id'],
                    'campaignName' => $campaignData['name'],
                    'campaignStart' => date('Y-m-d', $campaignData['date11']),
                    'campaignEnd' => date('Y-m-d', $campaignData['date22']),
                    'campaignPrice' => $campaignData['ext_cue_price'],
                ];
            } else {
                $assignForNonReceipt[] = [
                    'campaignId' => $campaignData['id'],
                    'campaignName' => $campaignData['name'],
                    'campaignStart' => date('Y-m-d', $campaignData['date11']),
                    'campaignEnd' => date('Y-m-d', $campaignData['date22']),
                    'campaignPrice' => $campaignData['ext_cue_price'],
                ];
            }
        }

        $twig = CreateObject('Twig', __DIR__ .'/templates', 'mail_for_check_campaign_receipt.html', [
            'flag' => $GLOBALS['env']['flag'],
            'campaignNonReceipt' => $assignForNonReceipt, 
            'campaignLostReceipt' => $assignForLostReceipt
        ]);
        
        echo $twig->getContent();
        echo '<br/><br/>';
    }
