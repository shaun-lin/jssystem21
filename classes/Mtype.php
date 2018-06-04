<?php

require_once __DIR__ .'/Adapter.php';

class Mtype extends Adapter
{
    const DEFAULT_FIELDS = [
        'id' => null,
        'name' => '',
        'dashboard'=>'',
        'creator' => '',
        'time' => '',
        'display' => '',
        'creator' => ''        
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
