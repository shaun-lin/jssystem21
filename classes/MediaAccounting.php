<?php

require_once __DIR__ .'/Adapter.php';

class MediaAccounting extends Adapter
{
    const DEFAULT_FIELDS = [
        'accounting_id' => null,
        'accounting_media_ordinal' => 0,
        'accounting_media_item' => 0,
        'accounting_campaign' => 0,
        'accounting_month' => 0,
        'accounting_revenue' => 0,
        'accounting_cost' => 0,
        'accounting_profit' => 0,
        'accounting_gross_margin' => 0,
        'accounting_comment' => '',
        'accounting_modifier' => 0,
        'currency_id' => 'TWD',
        'curr_cost' => 0,
        'invoice_number' => '',
        'invoice_date' => '',
        'input_invoice_month' => ''
    ];

    public function __construct($id=0)
    {
        parent::__construct($id);
    }

    public function getList($id=0, $mediaOrdinal=0, $accountingMonth=0)
    {
        $campaignId = is_numeric($id) && $id > 0 ? $id : null;
        $mediaOrdinal = is_numeric($mediaOrdinal) && $mediaOrdinal > 0 ? $mediaOrdinal : null;

        // echo($campaignId);
        // echo($mediaOrdinal);
        if ($campaignId === null) {
            return [];
        } else {
            $conditions = [sprintf("`accounting_campaign` = %d", $campaignId)];
            if (is_numeric($mediaOrdinal) && $mediaOrdinal > 0) {
                $conditions[] = sprintf("`accounting_media_ordinal` = %d", $mediaOrdinal);
            }

            if (is_numeric($accountingMonth) && $accountingMonth > 0) {
                $conditions[] = sprintf("`accounting_month` = %d", $accountingMonth);
            }
            $result = [];

            foreach ($this->searchAll($conditions, 'accounting_month', 'ASC') as $itemAccounting) {
                $result[$itemAccounting['accounting_media_ordinal']][$itemAccounting['accounting_media_item']][$itemAccounting['accounting_month']] = $itemAccounting;
            }
            // print_r($conditions);
            // print_r(count($result));
            return $result;

        }
    }
}
