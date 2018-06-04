<?php

require_once __DIR__ .'/Adapter.php';

class ClientContact extends Adapter
{
    const DEFAULT_FIELDS = [
        'contact_id' => null,
        'contact_client' => 0,
        'contact_title' => '',
        'contact_name' => '',
        'contact_tel' => '',
        'contact_email' => ''
    ];

    public function __construct($id=0)
    {
        parent::__construct($id);
    }
    
    public function getAllList()
    {
        $rows = [];

        foreach ($this->searchAll() as $item) {
            $rows[$item['contact_client']][$item['contact_id']] = $item;
        }

        return $rows;
    }
}
