<?php

require_once __DIR__ .'/Adapter.php';

class Item extends Adapter
{
    const DEFAULT_FIELDS = [
        'id' => null,
        'name' => '',
        'creator' => '',
        'display' => '',
        'time' => '',


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
