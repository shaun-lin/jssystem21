<?php

require_once __DIR__ .'/Adapter.php';

class HrmDetail extends Adapter
{
    const DEFAULT_FIELDS = [
        'detail_id' => null,
        'detail_hrm' => 0,
        'detail_type' => '',
        'detail_name' => '',
        'detail_value' => '',
        'detail_comment_1' => '',
        'detail_comment_2' => '',
        'detail_comment_3' => '',
    ];

    public function __construct($id=0)
    {
        parent::__construct($id);
    }
}
