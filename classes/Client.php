<?php

require_once __DIR__ .'/Adapter.php';

class Client extends Adapter
{
    const DEFAULT_FIELDS = [
        'id' => null,
        'name' => '',
        'name2' => '',
        'name3' => '',
        'taxid' => '',
        'tel' => '',
        'fax' => '',
        'contact' => '',
        'email' => '',
        'address' => '',
        'other' => '',
        'time' => '',
        'changename' => '',
        'is_old' => '',
        'zipcode' => '',
        'division' => '',
    ];

    public function __construct($id=0, $key='id')
    {
        parent::__construct($id, $key);
    }
}
