<?php

require_once __DIR__ .'/Adapter.php';

class MrbsUsers extends Adapter
{
    const DEFAULT_FIELDS = [
        'id' => null,
        'account_lid' => '',
        'name' => '',
        'level' => 1,
        'password' => '',
        'username' => '',
        'nickname' => '',
        'sex' => '',
        'email' => '',
        'usergroup' => '',
        'usergroup2' => '',
        'avator' => '',
        'usergroup4' => '',
        'usergroup5' => '',
        'usergroup6' => '',
        'usergroup7' => '',
        'usergroup8' => '',
        'listall3' => '',
        'hometown' => '',
        'marriage' => '',
        'idnumber' => '',
        'blood' => '',
        'height' => '',
        'weight' => '',
        'tel1' => '',
        'tel2' => '',
        'address1' => '',
        'address2' => '',
        'birthday' => '',
        'firstday' => '',
        'contact1' => '',
        'contact2' => '',
        'contact3' => '',
        'contact4' => '',
        'others' => '',
        'position' => '',
        'position_en' => '',
        'department' => '',
        'departmentid' => '',
        'executive' => '',
        'executiveid' => '',
        'monthday' => '',
        'email2' => '',
        'status' => 1,
        'punch_notification' => 1,
        'purview_people' => '',
        'NumEmp' => '',
        'Top_manage' => '',
        'Sec_manage' => '',
        'MemberType' => '',
        'blackholiday' => '',
        'CompLeave' => '',
        'SPBLIO' => '',
        'early' => '',
        'ceoReview' => '',
        'is_part_time' => 0,
        'user_referee' => '',
        'user_resign_date' => '0000-00-00',
        'user_bank_id' => '822',
        'user_bank_account' => ''
    ];

    const ROLE = [
        'PRODUCT' => 1,
        'SALES' => 2,
        'PM' => 3,
        'ACCOUNTANT' => 4,
        'MANAGER' => 5,
        'ADMIN' => 6,
    ];

    public function __construct($id=0, $key='id', $database='jsadways')
    {
        parent::__construct($id, $key, $database);
    }
}
