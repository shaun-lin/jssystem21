<?php

require_once __DIR__ .'/Adapter.php';

class Commission extends Adapter
{
    const DEFAULT_FIELDS = [
        'id' => null,
        'agency' => '',
        'media' => '',
        'commission1' => '',
        'commission2' => '',
        'commission3' => '',
        'commission4' => '',
        'commission5' => '',
        'commission6' => '',
        'commission7' => '',
        'commission8' => '',
        'commission9' => '',
    ];

    public function __construct($id=0, $key='id')
    {
        parent::__construct($id, $key);
    }
}
