<?php

require_once __DIR__ .'/Adapter.php';

class Receipt extends Adapter
{
    const DEFAULT_FIELDS = [
        'id' => null,
        'campaign_id' => 0,
        'receipt_number' => 0,
        'name' => '',
        'user1' => null,
        'user2' => null,
        'usertype' => null,
        'userid' => null,
        'taxid' => '',
        'numberid' => '',
        'class' => '',
        'totalprice1' => '',
        'totalprice2' => '',
        'others' => '',
        'others2' => null,
        'datemonth' => '',
        'times1' => 0,
        'times2' => 0,
        'status' => 0,
        'receipt3id' => 0,
        'ae_datemonth' => null,
        'finance_updatetime' => null,
    ];

    public function __construct($id=0, $key='id')
    {
        parent::__construct($id, $key);
    }
}
