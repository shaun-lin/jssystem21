<?php

require_once __DIR__ .'/Adapter.php';

class Class2 extends Adapter
{
    const DEFAULT_FIELDS = [
        'id' => null,
        'name' => '',
    ];

    public function __construct($id=0, $key='id')
    {
        parent::__construct($id, $key);
    }
}
