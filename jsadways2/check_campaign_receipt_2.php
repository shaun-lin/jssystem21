<?php

    set_time_limit(-1);
    require_once __DIR__ .'/autoload.php';

    IncludeFunctions('jsadways');

    $endStamp = strtotime(date('Y-m-01'));
    $endMonth = date('Ym', $endStamp);
    $startStamp = AddMonthToDate(-3, date('Y-m-01'), true);
    $startMonth = date('Ym', $startStamp);

    $campaigns = [];
    $mediaChangePrice = [];
    $campaignIncome = [];
    $receipt = [];

    $dbCampaign = clone($GLOBALS['app']->db);
    $dbMedia = clone($GLOBALS['app']->db);
    $dbMediaChange = clone($GLOBALS['app']->db);
    $dbReceipt = clone($GLOBALS['app']->db);

    $sqlReceipt = "SELECT campaign_id, SUM(totalprice1) as totalprice FROM receipt 
                    WHERE status = 1 AND campaign_id IN (
                        SELECT accounting_campaign FROM media_accounting 
                        LEFT JOIN campaign ON campaign.id = accounting_campaign
                        WHERE accounting_campaign IN ( SELECT id FROM `campaign` WHERE `status` IN (2, 3, 4, 5, 6, 7) AND `is_receipt` = 0) 
                        AND accounting_month >= $startMonth AND accounting_month <= $endStamp 
                        AND date11 >= $startStamp AND date22 <= $endStamp
                        AND (accounting_revenue > 0 OR accounting_cost > 0)
                        GROUP by accounting_campaign
                    ) GROUP BY campaign_id;";
    $dbReceipt->query($sqlReceipt);
    while ($itemReceipt = $dbReceipt->next_record()) {
        $receipt[$itemReceipt['campaign_id']] = $itemReceipt['totalprice'];
    }



    $sqlMediaChange = "SELECT campaign_id, SUM(change_income) AS change_income, SUM(change_cost) AS change_cost FROM `media_change` WHERE campaign_id IN (
                            SELECT accounting_campaign FROM media_accounting 
                            LEFT JOIN campaign ON campaign.id = accounting_campaign
                            WHERE accounting_campaign IN ( SELECT id FROM `campaign` WHERE status IN (3, 4, 5, 6, 7)) 
                            AND accounting_month >= 201601 AND accounting_month <= 201712 
                            AND date11 >= ". strtotime('2016-01-01 00:00:00') ." AND date22 <= ". strtotime('2017-12-31 23:59:59') ."
                            AND (accounting_revenue > 0 OR accounting_cost > 0)
                            GROUP by accounting_campaign
                        ) GROUP BY campaign_id;";

                        
    $dbMediaChange->query($sqlMediaChange);
    while ($itemMediaChange = $dbMediaChange->next_record()) {
        $mediaChangePrice[$itemMediaChange['campaign_id']] = $itemMediaChange['change_income'];
    }



    $sqlMediaIncome = "SELECT name, idnumber, date11, date22, member, status, action2,
                    accounting_campaign, SUM(accounting_revenue) as accounting_revenue, SUM(accounting_cost) as accounting_cost
                    FROM media_accounting 
                    LEFT JOIN campaign ON campaign.id = accounting_campaign
                    WHERE accounting_campaign IN ( SELECT id FROM `campaign` WHERE status IN (3, 4, 5, 6, 7)) 
                    AND accounting_month >= 201601 AND accounting_month <= 201712
                    AND date11 >= ". strtotime('2016-01-01 00:00:00') ." AND date22 <= ". strtotime('2017-12-31 23:59:59') ."
                    AND (accounting_revenue > 0 OR accounting_cost > 0)
                    GROUP by accounting_campaign  
                    ORDER BY campaign.date11 ASC , `media_accounting`.`accounting_campaign` ASC";
    $dbCampaign->query($sqlMediaIncome);
    while ($itemCampaign = $dbCampaign->next_record()) {
        $campaigns[$itemCampaign['accounting_campaign']] = [
            'id' => $itemCampaign['accounting_campaign'],
            'idnumber' => $itemCampaign['idnumber'],
            'name' => $itemCampaign['name'],
            'startdate' => date('Y-m-d', $itemCampaign['date11']),
            'enddate' => date('Y-m-d', $itemCampaign['date22']),
            'member' => $itemCampaign['member'],
            'return' => $itemCampaign['action2'],
            'status' => getCampaignStatusText($itemCampaign['status']),
            'price' => 0,
            'income' => number_format($itemCampaign['accounting_revenue'], 0, '', ''),
            'income_change' => number_format($mediaChangePrice[$itemCampaign['accounting_campaign']], 0, '', ''),
            // 'cost' => number_format($itemCampaign['accounting_cost'], 0, '', ''),
            // 'cost_change' => number_format($itemCampaign['media_change_cost'], 0, '', ''),
            'remainder' => 0,
            '',
            'tax_included_price' => 0,
            'receipt' => $receipt[$itemCampaign['accounting_campaign']],
            'is_equal' => '',
            'diff' => '',
        ];

        $rowsOrdinal = GetUsedMediaOrdinal($itemCampaign['accounting_campaign']);

        foreach ($rowsOrdinal as $idxOrdinal) {
            $sqlMedia = "SELECT SUM(totalprice) AS totalprice FROM `media{$idxOrdinal}` WHERE cue = 1 AND campaign_id = {$itemCampaign['accounting_campaign']};";
            $dbMedia->query($sqlMedia);

            while ($itemMedia = $dbMedia->next_record()) {
                $campaigns[$itemCampaign['accounting_campaign']]['price'] += $itemMedia['totalprice'];
            }
        }
    }
// echo '<pre>';
//     print_r($campaigns);
// die();
    foreach ($campaigns as $campiagnId => $itemCampaign) {
        $campaigns[$campiagnId]['price'] = number_format($itemCampaign['price'], 0, '', '');
        $campaigns[$campiagnId]['tax_included_price'] = number_format($itemCampaign['price'], 0, '', '');

        if (($campaigns[$campiagnId]['income'] + $mediaChangePrice[$campiagnId]) == $campaigns[$campiagnId]['price']) {
            unset($campaigns[$campiagnId]);
            // $campaigns[$campiagnId]['is_equal'] = ($campaigns[$campiagnId]['tax_included_price'] == $campaigns[$campiagnId]['receipt'] ? '' : 'X');
            // $campaigns[$campiagnId]['remainder'] = 0;
            // $campaigns[$campiagnId]['diff'] = $campaigns[$campiagnId]['tax_included_price'] - $campaigns[$campiagnId]['receipt'];
        } else {
            // unset($campaigns[$campiagnId]);
            $campaigns[$campiagnId]['remainder'] = $campaigns[$campiagnId]['price'] - ($campaigns[$campiagnId]['income'] + $mediaChangePrice[$campiagnId]);
            $campaigns[$campiagnId]['is_equal'] = ($campaigns[$campiagnId]['tax_included_price'] == $campaigns[$campiagnId]['receipt'] ? '' : 'X');
            $campaigns[$campiagnId]['diff'] = $campaigns[$campiagnId]['tax_included_price'] - $campaigns[$campiagnId]['receipt'];
        }
    }

    $data = [];
    $data[] = [
        'ID',
        '委刊單號', 
        '案件名稱', 
        '開始日期', 
        '結束日期', 
        '負責AE', 
        '回簽日期', 
        '狀態',
        '總價', 
        '總收入', 
        '總調整收入', 
        '剩餘',
        '',
        '金額 (未稅)',
        '已開發票金額 (未稅)',
        'X',
        ' ',
    ];

    $data += $campaigns;

    IncludeFunctions('excel');
    $objPHPExcel = CreateExcelFile();
    $excelActiveSheet = &$objPHPExcel->getActiveSheet();
    $objPHPExcel->getDefaultStyle()->getFont()->setSize(16);
    $excelActiveSheet->getDefaultColumnDimension()->setWidth(18);

    SetExcellCellFromArray($objPHPExcel, $data);
    SendExcellFile($objPHPExcel, '發票-');