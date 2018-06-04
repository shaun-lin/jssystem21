<?php

require_once __DIR__ .'/Adapter.php';

class Agency extends Adapter
{
    const DEFAULT_FIELDS = [
        'id' => null,
        'name' => '',
        'name2' => '',
        'taxid' => '',
        'tel' => '',
        'fax' => '',
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
        'display' => 1
    ];

    public function __construct($id=0, $key='id')
    {
        parent::__construct($id, $key);
    }
}
