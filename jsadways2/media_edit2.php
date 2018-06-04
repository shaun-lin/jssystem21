<?php

	// 2018-03-14 (Jimmy): 傑思jsadways2/media_edit2.php, 香港jsadways2hk/media_edit2.php, 豐富媒體jsadways2ff/media_edit2.php 共用此檔案
	require_once dirname(__DIR__) .'/autoload.php';
	
	$objSizeformat = CreateObject('Sizeformat', GetVar('id'));

	if (!IsId($objSizeformat->getId()) || (!IsPermitted('superuser') && !in_array($_SESSION['departmentid'], [21, 22]))) {
		RedirectLink('media_list.php');
	}

	$db = clone($GLOBALS['app']->db);

	$format1 = str_replace(["\n", "\r"], ["\\n", ""], GetVar('format1'));
	$format2 = str_replace(["\n", "\r"], ["\\n", ""], GetVar('format2'));

	$objSizeformat->setVar('format1', $format1);
	$objSizeformat->setVar('format2', $format2);
	$objSizeformat->setVar('user', $_SESSION['name']);
	$objSizeformat->setVar('times', time());
	$objSizeformat->store();
	
	if ($GLOBALS['env']['flag']['pos'] == 'ff') {
		$mailTo = [
			'bluebee@js-adways.com.tw' => '豐富媒體',
			'media@js-adways.com.tw' => '媒體部',
		];
	} else {
		$mailTo = ['media@js-adways.com.tw' => '媒體部'];
		
		$conditions = sprintf(" `usergroup` = %d AND (`user_resign_date` IS NULL OR `user_resign_date` = '0000-00-00') ", MrbsUsers::ROLE['SALES']);
		$objMrbsUsers = CreateObject('MrbsUsers');
		foreach ($objMrbsUsers->searchAll($conditions, '', '', '', '', 'name, username, email') as $itemUser) {
			$mailTo[$itemUser['email']] = ucfirst($itemUser['name']) .' '. $itemUser['username'];
		}
		
		unset($objMrbsUsers);
	}
	
	$mailSender = sprintf('%s廣告後台', $GLOBALS['env']['flag']['name']);
	$mailSubject = '【媒體規格更新】'. $objSizeformat->getVar('medianame') .' - '. date('Y/m/d');
	$mailContent = '<div style="display: block; max-width: 250px; border: 1px solid #e5e5e5 !important; padding: 16px; margin-bottom: 10px; background-color: #ffffe5;">
						<small><b>媒體 ('. $objSizeformat->getVar('medianame') .') 規格更新</b></small>
					</div>
					<div style="display: block; max-width: 250px; border: 1px solid #e5e5e5 !important; padding: 16px;">
						<div style="font-size: 140%;">更新後規格如下</div>
						<div><h3>Size</h3></div>
						<div>'. str_replace('\\n', "<br/>", ($objSizeformat->getVar('format1'))) .'</div>
						<br><br><br>
						<div><h3>Format</h3></div>
						<div>'. str_replace('\\n', "<br/>", ($objSizeformat->getVar('format2'))) .'</div>
						<br><br>
						由 '. $_SESSION['name'] .' 於 '. date('Y-m-d H:i', $objSizeformat->getVar('times')) .' 更新
					</div>';

	AddMailToQueue($mailTo, null, $mailContent, $mailSubject, $mailSender);
	
	ShowMessageAndRedirect('更新媒體格規成功', 'media_list.php?media='. GetVar('mediaid', '') , false);
