<?php

	require_once dirname(__DIR__) .'/autoload.php';

	$isGrantEdit = IsPermitted('backend_blogger', null, Permission::ACL['backend_blogger_edit']);

	IncludeFunctions('jsadways');

	$objBlogger = CreateObject('Blogger', GetVar('id'));

	$objMrbsUsers = CreateObject('MrbsUsers', GetCurrentUserId());
	$modified = date('Y-m-d H:i:s');
	$modifier = ucfirst($objMrbsUsers->getVar('name')) . $objMrbsUsers->getVar('username');
	unset($objMrbsUsers);

	$originBloggerData = [];
	if ($objBlogger->getId()) {
		$originBloggerData = $objBlogger->fields;
		$objBloggerHistory = json_decode($originBloggerData['history'], true);
		$objBloggerHistory = $objBloggerHistory ? $objBloggerHistory : [];
		unset($originBloggerData['history']);
	}

	foreach ($_POST as $idxBloggerField => $varBloggerField) {
		if (EndsWith($idxBloggerField, 'unit_check')) {
			if ($varBloggerField == 'N') {
				$_POST[str_replace('_unit_check', '_unit', $idxBloggerField)] = 'N';
				$_POST[str_replace('_unit_check', '_price', $idxBloggerField)] = 0;
			} else if ($varBloggerField == 'Y') {
				$_POST[str_replace('_unit_check', '_unit', $idxBloggerField)] = 'Y';
				$_POST[str_replace('_unit_check', '_price', $idxBloggerField)] = 0;
			} else if ($varBloggerField == 'Y+') {
				$varPrice = $_POST[str_replace('_unit_check', '_price', $idxBloggerField)];
				
				if (!is_numeric($varPrice)) {
					if (strtolower(trim($varPrice)) == 'free') {
						$_POST[str_replace('_unit_check', '_price', $idxBloggerField)] = -1;
					} else {
						$_POST[str_replace('_unit_check', '_price', $idxBloggerField)] = 0;
					}
				}
			} else {
				$_POST[str_replace('_unit_check', '_unit', $idxBloggerField)] = '';
				$_POST[str_replace('_unit_check', '_price', $idxBloggerField)] = 0;
			}

			unset($_POST[$idxBloggerField]);
		} else if (EndsWith($idxBloggerField, '_price') && strpos($idxBloggerField, '_other_') === false) {
			if (!is_numeric($varBloggerField)) {
				if (strtolower(trim($varBloggerField)) == 'free') {
					$_POST[str_replace('_unit_check', '_price', $idxBloggerField)] = -1;
				} else {
					$_POST[str_replace('_unit_check', '_price', $idxBloggerField)] = 0;
				}
			}
		}
	}

	if (isset($_FILES['attachment'])) {
		$influencerAttachmentPath = '';
		
		if (empty($_FILES['attachment']['error']) && $_FILES['attachment']['name'] && $_FILES['attachment']['size']) {
			$newAttachmentName = microtime(true) .'_'. rand(0, 1000) .'.'. pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
			$influencerAttachmentPath = 'uploads/'. $newAttachmentName;
			$influencerAttachmentNewPath = __DIR__ .'/'. $influencerAttachmentPath;

			if (!move_uploaded_file($_FILES['attachment']['tmp_name'], $influencerAttachmentNewPath)) {
				$influencerAttachmentPath = '';
			}
		}

		if ($influencerAttachmentPath) {
			$objBlogger->setVar('photo', $influencerAttachmentPath);
		}
	}

	$objBlogger->bind($_POST);

	if (empty($objBlogger->getVar('ac_id'))) {
		$objBlogger->setVar('ac_id', genBloggerACCode());
	} else if ($objBlogger->getVar('ac_id') && !preg_match('/^[0-9]{2}\-[0-9]{3}$/', $objBlogger->getVar('ac_id'))) {
		$objBlogger->setVar('ac_id', genBloggerACCode());
	}

	$class = '';
	if (isset($_POST['class']) && is_array($_POST['class']) && count($_POST['class'])) {
		$class = [];

		foreach ($_POST['class'] as $itemClass) {
			if ($itemClass) {
				$class[] = $itemClass;
			}
		}

		$class = implode('、', $class);
		unset($_POST['class']);
	}
	$objBlogger->setVar('class', $class);
	
	$objBlogger->store();

	if ($objBlogger->getId()) {
		$objBloggerBank = CreateObject('BloggerBank');
		$bankFields = GetVar('bankFields');

		if (isset($bankFields['id']) && is_array($bankFields['id']) && count($bankFields['id'])) {
			foreach ($bankFields['id'] as $idxBankAccount => $itemBankId) {
				$objBloggerBank->reset();
				$originBloggerBankData = [];

				if (IsId($itemBankId)) {
					if (!$objBloggerBank->load($itemBankId)) {
						continue;
					}

					if ($objBloggerBank->getVar('blogger_id') == $objBlogger->getId()) {
						$originBloggerBankData = $objBloggerBank->fields;
						$objBankHistory = json_decode($originBloggerBankData['history'], true);
						unset($originBloggerBankData['create_time']);
						unset($originBloggerBankData['update_time']);
						unset($originBloggerBankData['by']);
						unset($originBloggerBankData['history']);
					} else {
						$sharedBankId[] = $itemBankId;
						
						if (isset($bankFields['main'][$idxBankAccount]) && $bankFields['main'][$idxBankAccount]) {
							$mainBankId = $itemBankId;
						}

						$objBloggerBank->bind([
							'account_payment_method' => isset($bankFields['account_payment_method'][$idxBankAccount]) ? $bankFields['account_payment_method'][$idxBankAccount] : 0,
							'update_time' => date('Y-m-d H:i:s'),
							'by' => $_SESSION['username'],
						]);
						$objBloggerBank->store();
						$objBloggerBank->reset();
						continue;
					}
				} else {
					if (empty($bankFields['bankName'][$idxBankAccount]) && empty($bankFields['bankCode'][$idxBankAccount]) && empty($bankFields['bankAC'][$idxBankAccount]) && empty($bankFields['bankUserName'][$idxBankAccount])) {
						continue;
					}

					$objBloggerBank->setVar('blogger_id', $objBlogger->getId());
					$objBloggerBank->setVar('create_time', date('Y-m-d H:i:s'));
				}

				$objBloggerBank->bind([
					'bankName' => isset($bankFields['bankName'][$idxBankAccount]) ? $bankFields['bankName'][$idxBankAccount] : '',
					'bankCode' => isset($bankFields['bankCode'][$idxBankAccount]) ? $bankFields['bankCode'][$idxBankAccount] : '',
					'bankAC' => isset($bankFields['bankAC'][$idxBankAccount]) ? $bankFields['bankAC'][$idxBankAccount] : '',
					'bankCheckCode' => isset($bankFields['bankCheckCode'][$idxBankAccount]) ? $bankFields['bankCheckCode'][$idxBankAccount] : '',
					'bankUserName' => isset($bankFields['bankUserName'][$idxBankAccount]) ? $bankFields['bankUserName'][$idxBankAccount] : '',
					'bankIdNum' => isset($bankFields['bankIdNum'][$idxBankAccount]) ? $bankFields['bankIdNum'][$idxBankAccount] : '',
					'account_payment_method' => isset($bankFields['account_payment_method'][$idxBankAccount]) ? $bankFields['account_payment_method'][$idxBankAccount] : 0,
					'update_time' => date('Y-m-d H:i:s'),
					'by' => $_SESSION['username'],
				]);

				if ($originBloggerBankData) {
					$updatedFields = [];
					$isBankModified = false;
					foreach ($originBloggerBankData as $fieldName => $fieldVar) {
						if ($fieldVar != $objBloggerBank->getVar($fieldName)) {
							$isBankModified = true;

							$updatedFields[$fieldName] = [
								'origin' => $fieldVar,
								'modified' => $modified,
								'modifier' => $modifier
							];
						}
					}

					if ($isBankModified) {
						foreach ($updatedFields as $idxUpdated => $varUpdated) {
							$objBankHistory[$idxUpdated] = $varUpdated;
						}

						$objBloggerBank->setVar('history', json_encode($objBankHistory));
					}
				}

				$objBloggerBank->store();
				
				if (isset($bankFields['main'][$idxBankAccount]) && $bankFields['main'][$idxBankAccount]) {
					$mainBankId = $objBloggerBank->getId();
				}
			}
		}

		if (isset($_POST['disableBankAccount']) && is_array($_POST['disableBankAccount']) && count($_POST['disableBankAccount'])) {
			foreach ($_POST['disableBankAccount'] as $itemBankId) {
				$objBloggerBank->reset();

				if (IsId($itemBankId) && $objBloggerBank->load($itemBankId) && empty($objBloggerBank->getVar('shared')) && $objBloggerBank->getVar('blogger_id') == $objBlogger->getId()) {
					$objBloggerBank->setVar('states', 0);
					$objBloggerBank->store();
				}
			}
		}
	}

	$objBlogger->setVar('main_bank_id', isset($mainBankId) && IsId($mainBankId) ? $mainBankId : 0);
	$objBlogger->setVar('shared_bank_id', isset($sharedBankId) && count($sharedBankId) ? implode(',', $sharedBankId) : '0');

	if ($originBloggerData) {
		$updatedFields = [];
		$isBloggerModified = false;
		foreach ($originBloggerData as $fieldName => $fieldVar) {
			if ($fieldVar != $objBlogger->getVar($fieldName)) {
				$isBloggerModified = true;

				$updatedFields[$fieldName] = [
					'origin' => $fieldVar,
					'modified' => $modified,
					'modifier' => $modifier
				];
			}
		}
		
		if ($isBloggerModified) {
			foreach ($updatedFields as $idxUpdated => $varUpdated) {
				$objBloggerHistory[$idxUpdated] = $varUpdated;
			}

			$objBlogger->setVar('history', json_encode($objBloggerHistory));
		}
	}
	
	$objBlogger->store();
	

	// 更新寫手標籤
	$objTagMap = CreateObject('TagMap');
	$objTagMap->deleteMapping($objBlogger->getId(), 'blogger');
	if (($tagId = GetVar('tag_id')) && is_array($tagId)) {
		$objTag = CreateObject('Tag', null, 'blogger');
		
		foreach ($tagId as $itemId) {
			if ($itemTag = $objTag->getDetail($itemId, 'blogger')) {
				$objTagMap->reset();
				$objTagMap->bind([
					'map_tag' => $itemTag['tag_id'],
					'map_relation' => $itemTag['tag_relation'],
					'map_item_id' => $objBlogger->getId(),
				]);
				$objTagMap->store();
			}
		}
	}

	ShowMessageAndRedirect('儲存成功', 'blogger_view.php?id='. $objBlogger->getId(), false);
