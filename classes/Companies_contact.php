<?php

require_once __DIR__ .'/Adapter.php';

class Companies_contact extends Adapter
{
    const DEFAULT_FIELDS = [
        'contact_id' => null,
        'contact_agency' => '',
        'contact_title' => '',
        'contact_name' =>'',
        'contact_tel' => '',
        'contact_email' => '',
    ];

    const STATUS = [
        'PENDING' => 0,
        'PROCESSING' => 1,
        'PAUSE' => 2,
        'CLOSED' => 3
    ];

    public function __construct($id=0, $key='contact_id')
    {
        parent::__construct($id, $key);
    }
}