<?php

require_once __DIR__ .'/Adapter.php';

class BloggerBank extends Adapter
{
    const DEFAULT_FIELDS = [
        'id' => null,
        'blogger_id' => '',
        'bankName' => '',
        'bankIdNum' => '',
        'bankCheckCode' => '',
        'bankCode' => '',
        'bankAC' => '',
        'bankUserName' => '',
        'invoice' => '',
        'health' => '',
        'rt' => '',
        'create_time' => '',
        'update_time' => '',
        'by' => '',
        'shared' => '',
        'states' => '1',
        'account_payment_method' => 0,
        'history' => '',
    ];

    public function __construct($id=0, $key='id')
    {
        parent::__construct($id, $key);
    }
}
