<?php

require_once __DIR__ .'/Adapter.php';

class Sizeformat extends Adapter
{
    const DEFAULT_FIELDS = [
        'id' => null,
        'mediaid' => 0,
        'medianame' => '',
        'adtype' => '',
        'phonesystem' => '',
        'position' => '',
        'format1' => '',
        'format2' => '',
        'times' => 0,
        'user' => '',
    ];

    public function __construct($id=0, $key='id')
    {
        parent::__construct($id, $key);
    }
}
