<?php

require_once __DIR__ .'/Adapter.php';

class Hrm extends Adapter
{
    const DEFAULT_FIELDS = [
        'hrm_id' => null,
        'hrm_status' => 1,
        'hrm_type' => 0,
        'hrm_boarded_year' => 0,
        'hrm_boarded_month' => 0,
        'hrm_boarded_day' => 0,
        'hrm_boarded_stamp' => 0,
        'hrm_resigned_year' => 0,
        'hrm_resigned_month' => 0,
        'hrm_resigned_day' => 0,
        'hrm_resigned_stamp' => 0
    ];

    const TYPE = [
        'full-time' => 0,
        'part-time' => 1,
    ];

    const STATUS = [
        'resigned' => 0,
        'on-job' => 1,
    ];

    public function __construct($id=0)
    {
        parent::__construct($id);
    }
}
