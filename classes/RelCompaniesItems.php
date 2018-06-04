<?php

require_once __DIR__ .'/Adapter.php';

class RelMediaCompanies extends Adapter
{
    const DEFAULT_FIELDS = [
        'medias_id' => null,
        'companies_id' => null
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
