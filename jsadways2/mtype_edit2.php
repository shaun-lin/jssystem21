<?php

	// 2018-03-14 (Jimmy): 傑思jsadways2/media_edit2.php, 香港jsadways2hk/media_edit2.php, 豐富媒體jsadways2ff/media_edit2.php 共用此檔案
	require_once dirname(__DIR__) .'/autoload.php';
	
	$objSizeformat = CreateObject('Mtype');
	if ( (!IsPermitted('superuser') && !in_array($_SESSION['departmentid'], [21, 22]))) {
		RedirectLink('mtype_list.php');
	}

	$db = clone($GLOBALS['app']->db);

	$name = str_replace(["\n", "\r"], ["\\n", ""], GetVar('name'));
	$display = str_replace(["\n", "\r"], ["\\n", ""], GetVar('display'));
	$dashboard= str_replace(["\n","\r"],["\\n",""],GetVar('dashboard'));
    $objSizeformat->setVar('id',getVar('id'));
	$objSizeformat->setVar('name', $name);
	$objSizeformat->setVar('display', $display);
	$objSizeformat->setVar('creator', $_SESSION['name']);
	$objSizeformat -> setVar('dashboard',$dashboard);
	$objSizeformat->setVar('time', time());
	$objSizeformat->store();
	
	
	// if ($GLOBALS['env']['flag']['pos'] == 'ff') {
	// 	$mailTo = [
	// 		'bluebee@js-adways.com.tw' => '豐富媒體',
	// 		'media@js-adways.com.tw' => '媒體部',
	// 	];
	// } else {
	// 	$mailTo = ['media@js-adways.com.tw' => '媒體部'];
		
	// 	$conditions = sprintf(" `usergroup` = %d AND (`user_resign_date` IS NULL OR `user_resign_date` = '0000-00-00') ", MrbsUsers::ROLE['SALES']);
	// 	$objMrbsUsers = CreateObject('MrbsUsers');
	// 	foreach ($objMrbsUsers->searchAll($conditions, '', '', '', '', 'name, username, email') as $itemUser) {
	// 		$mailTo[$itemUser['email']] = ucfirst($itemUser['name']) .' '. $itemUser['username'];
	// 	}
		
	// 	unset($objMrbsUsers);
	// }
	
	// $mailSender = sprintf('%s廣告後台', $GLOBALS['env']['flag']['name']);
	// $mailSubject = '【媒體規格更新】'. $objSizeformat->getVar('name') .' - '. date('Y/m/d');
	// $mailContent = '<div style="display: block; max-width: 250px; border: 1px solid #e5e5e5 !important; padding: 16px; margin-bottom: 10px; background-color: #ffffe5;">
	// 					<small><b>媒體 ('. $objSizeformat->getVar('name') .') 規格更新</b></small>
	// 				</div>
	// 				<div style="display: block; max-width: 250px; border: 1px solid #e5e5e5 !important; padding: 16px;">
	// 					<div style="font-size: 140%;">更新後規格如下</div>
	// 					<div><h3>媒體名稱</h3></div>
	// 					<div>'. str_replace('\\n', "<br/>", ($objSizeformat->getVar('name'))) .'</div>
	// 					<br><br><br>
	// 					<div><h3>是否顯示</h3></div>
	// 					<div>'. str_replace('\\n', "<br/>", ($objSizeformat->getVar('display'))=="1"?"顯示":"不顯示") .'</div>
	// 					<br><br>

	// 					由 '. $_SESSION['name'] .' 於 '. date('Y-m-d H:i', $objSizeformat->getVar('times')) .' 更新
	// 				</div>';

	// AddMailToQueue($mailTo, null, $mailContent, $mailSubject, $mailSender);
	
	ShowMessageAndRedirect('更新媒體格規成功', 'mtype_list.php?mtype='. GetVar('id', '') , false);
