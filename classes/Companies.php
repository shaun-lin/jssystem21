<?php

require_once __DIR__ .'/Adapter.php';

class Companies extends Adapter
{
    const DEFAULT_FIELDS = [
        'id' => null,
        'name' => '',
        'name2' => '',
        'taxid' => '',
        'tel' => '',
        'contact' => '',
        'email' => '',
        'address' => '',
        'other' => '',
        'time' => '',
        'days' => '',
        'changename' => '',
        'is_old' => '',
        'zipcode' => '',
        'division' => '',
        'dispplay' => '',
    ];

    const STATUS = [
        'PENDING' => 0,
        'PROCESSING' => 1,
        'PAUSE' => 2,
        'CLOSED' => 3
    ];

    public function __construct($id=0, $key='id')
    {
        parent::__construct($id, $key);
    }
}
