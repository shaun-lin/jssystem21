<?php

    require_once dirname(__DIR__) .'/autoload.php';

    $feedback = [
        'success' => null,
        'failure' => true,
        'data' => [],
        'message' => '沒有操作的權限',
    ];

    $isGrantDelete = IsPermitted('backend_blogger', null, Permission::ACL['backend_blogger_delete']);

    $method = GetVar('method');

    if ($method == 'delete') {
        if ($isGrantDelete) {
            $objBlogger = CreateObject('Blogger', GetVar('blogger_id'));

            if ($objBlogger->getId()) {
                $objBlogger->delete();

                $feedback = [
                    'success' => true,
                    'reload' => true
                ];
            }
        }
    } else if ($method == 'load') {
        $objBlogger = CreateObject('Blogger', GetVar('blogger_id'));
        
        if ($objBlogger->getId()) {
            $feedback = [
                'success' => true,
                'data' => $objBlogger->fields
            ];
        } else {
            $feedback['message'] = '此寫手不存在';
        }
    } else if ($method == 'load_shared_bank') {
        $objBloggerBank = CreateObject('BloggerBank', GetVar('bank_id'));

        if ($objBloggerBank->getId() && $objBloggerBank->getVar('states') && $objBloggerBank->getVar('shared')) {
            $feedback = [
                'success' => true,
                'data' => $objBloggerBank->fields
            ];
        } else {
            $feedback['message'] = '此共用帳戶不存在';
        }
    } else if ($method == 'save_shared_bank') {
        if (IsPermitted('backend_blogger', null, 'sub-supervise')) {
            $bankFields = GetVar('bankFields');
            $objBloggerBank = CreateObject('BloggerBank');

            if (isset($bankFields['id']) && IsId($bankFields['id'])) {
                if ($objBloggerBank->load($bankFields['id']) && $objBloggerBank->getVar('states') && $objBloggerBank->getVar('shared')) {
                    $objBloggerBank->bind($bankFields + [
                        'by' => $_SESSION['username'],
                        'update_time' => date('Y-m-d H:i:s'),
                    ]);
                    $objBloggerBank->store();

                    $feedback['success'] = true;
                    unset($feedback['failure']);
                } else {
                    $feedback['message'] = '此共用帳戶不存在';
                }
            } else {
                $bankFields['id'] = null;
                $bankFields['blogger_id'] = 0;

                $objBloggerBank->bind($bankFields + [
                    'states' => 1,
                    'shared' => 1,
                    'by' => $_SESSION['username'],
                    'create_time' => date('Y-m-d H:i:s'),
                    'update_time' => date('Y-m-d H:i:s'),
                ]);
                $objBloggerBank->store();

                $feedback['success'] = true;
                unset($feedback['failure']);
            }
        }
    } else if ($method == 'delete_shared_bank') {
        if (IsPermitted('backend_blogger', null, 'sub-supervise')) {
            $bankId = GetVar('bank_id');
            $objBloggerBank = CreateObject('BloggerBank');

            if (is_array($bankId)) {
                foreach ($bankId as $id) {
                    if ($objBloggerBank->load($id) && $objBloggerBank->getVar('states') && $objBloggerBank->getVar('shared')) {
                        $objBloggerBank->delete();
        
                        $feedback['success'] = true;
                        unset($feedback['failure']);
                        unset($feedback['message']);
                    }
                }
            } else {
                if ($objBloggerBank->load($bankId) && $objBloggerBank->getVar('states') && $objBloggerBank->getVar('shared')) {
                    $objBloggerBank->delete();
    
                    $feedback['success'] = true;
                    unset($feedback['failure']);
                } else {
                    $feedback['message'] = '此共用帳戶不存在';
                }
            }
        }
    } else if ($method == 'toggle_blogger_selected') {
        $bloggerId = GetVar('blogger_id');

        $objBlogger = CreateObject('Blogger', $bloggerId);

        if (IsId($objBlogger->getId())) {
            $bloggerName = $objBlogger->getName();

            if (isset($_SESSION['blogger'])) {
                if (in_array($bloggerName, $_SESSION['blogger'])) {
                    unset($_SESSION['blogger'][$objBlogger->getId()]);
                } else {
                    $_SESSION['blogger'][$objBlogger->getId()] = $bloggerName;
                }
            } else {
                $_SESSION['blogger'][$objBlogger->getId()] = $bloggerName;
            }
        }

        $total = isset($_SESSION['blogger']) ? count($_SESSION['blogger']) : 0;
        $feedback = [
            'total' => $total,
		    'blogger' => $total ? implode(',&nbsp;&nbsp;&nbsp;', $_SESSION['blogger']) : '',
        ];
    } else if ($method == 'clean_cache') {
        if (isset($_SESSION['blogger'])) {
            unset($_SESSION['blogger']);
        }
    }

    PrintJsonData($feedback, true);
