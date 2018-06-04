<?php

require_once __DIR__ .'/Adapter.php';

class Campaign extends Adapter
{
    const DEFAULT_FIELDS = [
        'id' => null,
        'name' => '',
        'agency' => '',
        'agency_id' => null,
        'client' => '',
        'client_id' => null,
        'date1' => '',
        'date2' => '',
        'date11' => 0,
        'date22' => 0,
        'member' => '',
        'memberid' => 0,
        'status' => 1,
        'sex' => '',
        'pay1' => '',
        'pay2' => '',
        'receipt1' => '',
        'receipt2' => '',
        'title' => '',
        'contact1' => '',
        'contact2' => '',
        'contact3' => '',
        'time' => 0,
        'times' => '',
        'others' => '',
        'others2' => '',
        'campaign_exception_comment' => '',
        'check1' => '',
        'time1' => '',
        'check2' => '',
        'time2' => '',
        'idnumber' => '',
        'action1' => '',
        'action2' => '',
        'action3' => '',
        'tagtext' => '',
        'rate' => '',
        'ratetime' => '',
        'version' => 0,
        'putfile' => 0,
        'exchang_math' => 0,
        'exchang_time' => '',
        'write_time' => '',
        'is_jp' => 0,
        'is_receipt' => 0,
        'draw' => '',
        'wommId' => null,
        'womm' => '',
        'media_leader' => 0,
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
