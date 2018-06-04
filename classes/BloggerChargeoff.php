<?php

require_once __DIR__ .'/Adapter.php';

class BloggerChargeoff extends Adapter
{
    const DEFAULT_FIELDS = [
        'sno' => null,
        'campaign_id' => '',
        'blogger_id' => '',
        'blogger_detail_id' => '',
        'chargeoff_date' => '',
        'cost_date' => '',
        'price' => '',
        'remark' => '',
        'create_time' => '',
        'update_time' => '',
        'name' => '',
        'bankId' => '',
    ];

    public function __construct($id=0, $key='id')
    {
        parent::__construct($id, $key);
    }
}
