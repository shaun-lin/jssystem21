<?php

require_once __DIR__ .'/Adapter.php';

class AgencyContact extends Adapter
{
    const DEFAULT_FIELDS = [
        'contact_id' => null,
        'contact_agency' => 0,
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
            $rows[$item['contact_agency']][$item['contact_id']] = $item;
        }

        return $rows;
    }
}
