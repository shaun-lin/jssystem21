<?php

require_once __DIR__ .'/ServerStorage.php';

class Permission extends ServerStorage
{
    const ACL = [
        'allow' => 1,
        'read' => 1,
        'edit' => 2,
        'delete' => 4,
        'inspect' => 8,
        'approve' => 16,
        'confirm' => 32,
        'notify' => 64,
        'undefined5' => 128,
        'undefined6' => 256,
        'undefined7' => 512,
        'undefined8' => 1024,
        'undefined9' => 2048,
        'undefined10' => 4096,
        'undefined11' => 8192,
        'sub-supervise' => 16384,
        'undefined12' => 32768,
        'supervise' => 65536,
        // 'root' => 131072 (262143)
        'backend_campaign_exception_approval_mail_notify' => 'notify',
        'backend_blogger_exception_mail_notify' => 'notify',
        'backend_blogger_view_account' => 'undefined5',
        'backend_blogger_edit' => 'undefined6',
        'backend_blogger_delete' => 'undefined7',

        'finacial_unlock_receipt_effective' => 'undefined6',
        'finacial_ignore_campaign_closed_entry' => 'undefined8',
        'finacial_unlock_campaign_closed_entry' => 'undefined10',
        'finacial_close_entry' => 'undefined11',

        'guardian_configure' => 'edit',
    ];

    const MODULE = [
        'superuser' => [
            'name' => '系統管理員',
            'option' => [1]
        ],
        'guardian' => [
            'name' => '人員進出紀錄',
            'option' => [1, 2],
            'plaintext' => [
                1 => '查詢',
                2 => '設定',
            ]
        ],
        'hrm' => [
            'name' => '人員管理系統',
            'option' => [1, 2, 4, 16384],
            'plaintext' => [
                1 => '檢視'
            ]
        ],
        'timecard' => [
            'name' => '打卡記錄',
            'option' => [1, 2, 4, 16384],
            'plaintext' => [
                1 => '檢視',
                2 => '修改'
            ]
        ],
        'filemanager' => [
            'name' => '檔案管理',
            'option' => [1, 2, 4, 16384],
            'plaintext' => [
                1 => '檢視'
            ]
        ],
        'backend_js_portal_10' => [
            'name' => '傑思廣告後台 1.0',
            'option' => [1]
        ],
        'backend_js_portal_20' => [
            'name' => '傑思廣告後台 2.0',
            'option' => [1]
        ],
        'backend_hk_portal' => [
            'name' => '香港廣告後台',
            'option' => [1]
        ],
        'backend_ff_portal' => [
            'name' => '豐富媒體廣告後台',
            'option' => [1]
        ],
        'backend_campaign_exception_approval' => [
            'name' => '廣告後台案件異常審核',
            'option' => [16, 64],
            'plaintext' => [
                16 => '審核',
                64 => '信件通知'
            ]
        ],
        'backend_blogger_exception' => [
            'name' => '廣告後台寫手異常警訊',
            'option' => [64],
            'plaintext' => [
                64 => '信件通知',
            ]
        ],
        'backend_blogger' => [
            'name' => '廣告後台寫手',
            'option' => [128, 256, 512, 16384],
            'plaintext' => [
                128 => '檢視帳戶',
                256 => '新增/編輯',
                512 => '刪除'
            ]
        ],
        'official_website_backend_portal' => [
            'name' => '傑思官網後台系統',
            'option' => [1]
        ],
        'edm' => [
            'name' => 'EDM系統',
            'option' => [1]
        ],
        'worklist' => [
            'name' => '工單系統',
            'option' => [1]
        ],
        'express' => [
            'name' => '信件收發一覽表',
            'option' => [2]
        ],
        'finacial' => [
            'name' => '帳務',
            'option' => [256, 1024, 4096, 8192],
            'plaintext' => [
                256 => '暫時作廢發票',
                1024 => '成本更正',
                4096 => '解除案件財報限制',
                8192 => '關帳',
            ]
        ]
    ];

    const PERM_TEXT = [
        1 => '使用/讀取',
        2 => '新增/編輯',
        4 => '刪除',
        8 => '查閱',
        16 => '審核',
        32 => '二次審核',
        64 => '通知',
        128 => '',
        256 => '',
        512 => '調閱',
        1024 => '刪除',
        16384 => '模組管理員',
        65536 => 'Root'
    ];

    public function isPermitted($userid=0, $flag='', $acl='read', $ignoreSuperVisor=false)
    {
        if (is_string($acl)) {
            $acl = strtolower($acl);

            if (array_key_exists($acl, self::ACL)) {
                $level = self::ACL[$acl];

                $access = 0;

                $variable = '';
                $args = explode('.', $flag);
                
                foreach ($args as $n => $arg) {
                    if (strlen(trim($arg))) {
                        $variable .= '[$args[' . $n . ']]';
                    }
                }

                if ($ignoreSuperVisor === false && isset($this->data['superuser'][$userid])) {
                    $access = $this->data['superuser'][$userid] & self::ACL['allow'];
                }

                if (empty($access) && strlen($variable)) {
                    $aclDefinition = [];
                    $code = '$aclDefinition = isset($this->data'. $variable .') ? $this->data'. $variable ." : '[]';";
                    eval($code);

                    if (isset($aclDefinition[$userid])) {
                        $access = $aclDefinition[$userid] & self::ACL[$acl];
                    }
                }

                return $access;
            }
        }

        return null;
    }

    public function getData()
    {
        $script = '';

        $args = func_get_args();

        foreach ($args as $text) {
            $script .= is_numeric($text) ? "[{$text}]" : "['{$text}']";
        }

        $script = '$data = isset($this->data'. $script .') ? $this->data'. $script .' : null;';
        eval($script);

        return $data;
    }
}
