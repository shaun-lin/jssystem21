<?php

require_once __DIR__ .'/Adapter.php';

class Cpdetail extends Adapter
{
    const DEFAULT_FIELDS = [
        'id' => null,
        'cp_id' => null,
        'media_id' => null,
        'comp_id' => null,
        'item_id' => null,
        'mtype_name' => null,
        'mtype_number' => null,
        'mtype_id' => null
    ];

    const STATUS = [
        'PENDING' => 1,
        'REVIEW' => 2,
        'PROCESSING' => 3,
        'CLOSED' => 4,
        'PAUSE' => 5,
        'TERMINATION' => 6,
        'EXCEPTION' => 7,
        'CANCELLED' => 8,
        'CANCELLING' => 9,
    ];

    public function __construct($id=0, $key='id')
    {
        parent::__construct($id, $key);
    }
}
