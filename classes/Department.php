<?php

require_once __DIR__ .'/Adapter.php';

class Department extends Adapter
{
    const DEFAULT_FIELDS = [
        'id' => null,
        'parent' => '',
        'name' => '',
        'leader' => '',
        'leaderid' => '',
        'others' => '',
        'enname' => '',
        'color' => ''
    ];

    public function __construct($id=0, $key='id')
    {
        parent::__construct($id, $key);
    }
}
