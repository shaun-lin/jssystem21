<?php

	// 2018-03-14 (Jimmy): 傑思jsadways2/media_edit2.php, 香港jsadways2hk/media_edit2.php, 豐富媒體jsadways2ff/media_edit2.php 共用此檔案
	require_once dirname(__DIR__) .'/autoload.php';
	include('include/db.inc.php');
	//建立狀態，如果狀態為1代表原本已有資料，如果為2代表為新增資料
	//狀態數值由medias_edit取得(POST)
	$status=$_POST['status'];
	if($status==1){
		savetoDB();
		saveRel();
		ShowMessageAndRedirect('更新模板成功', 'medias_list.php' , false);
	}
	else if($status==2){
		savetoDB();	
		$sqlselete="select id from medias where name='".$_POST['name']."';";
		$seleteresult=mysql_query($sqlselete);
		$sqlID=mysql_fetch_array($seleteresult);
		$sql1=$sqlID['id'];
		header('location:medias_edit.php?id='.$sql1);
	}



	//jackie 2018/05/21避免重複名稱輸入
	function savetoDB(){

	$objSizeformat = CreateObject('Medias');
	$objSizeformat2 = CreateObject('RelMediaCompanies');
	$objRelMediaCompanies=CreateObject('RelMediaCompanies');

	if ( (!IsPermitted('superuser') && !in_array($_SESSION['departmentid'], [21, 22]))) {
		RedirectLink('medias_list.php');
	}

	$db = clone($GLOBALS['app']->db);

	$name = str_replace(["\n", "\r"], ["\\n", ""], GetVar('name'));
	$display = str_replace(["\n", "\r"], ["\\n", ""], GetVar('display'));
	$crop = str_replace(["\n","\r"],["\\n",""],getVar('crop'));
    $companies=GetVar('companies');
		

	$sqlcheck="select * from `medias` where id='".getVar('id')."'";
	$sqlcheck3="select * from `medias` where name ='".getVar('name')."'";
	$result=mysql_query($sqlcheck);
	$check=mysql_fetch_array($result);
	$check3=mysql_query($sqlcheck3);

	if(getVar('id')==NULL){ //if null
		if(mysql_num_rows($check3)==0){
		$objSizeformat->setVar('id',getVar('id'));
		$objSizeformat->setVar('name', $name);
		$objSizeformat->setVar('crop',$crop);
		$objSizeformat->setVar('display', $display);
		$objSizeformat->setVar('creator', $_SESSION['name']);
		$objSizeformat->setVar('time', time());
		$objSizeformat->store();
		}
		else{
			ECHO "<script>alert('警告：名稱重複，確認後返回編輯頁'); history.back();</script>";
		}
	} //if null
	else if($check['name']==getVar('name')){
		$objSizeformat->setVar('id',getVar('id'));
		$objSizeformat->setVar('name', $name);
		$objSizeformat->setVar('crop',$crop);
		$objSizeformat->setVar('display', $display);
		$objSizeformat->setVar('creator', $_SESSION['name']);
		$objSizeformat->setVar('time', time());
		$objSizeformat->store();
	}
	else{
			if(mysql_num_rows($check3)==0){
				$objSizeformat->setVar('id',getVar('id'));
				$objSizeformat->setVar('name', $name);
				$objSizeformat->setVar('crop',$crop);
				$objSizeformat->setVar('display', $display);
				$objSizeformat->setVar('creator', $_SESSION['name']);
				$objSizeformat->setVar('time', time());
				$objSizeformat->store();
			}
			else{
				ECHO "<script>alert('警告：名稱重複，確認後返回編輯頁'); history.back();</script>";
			}
		}	
	}

	//jackie 2018/05/17 修改rel_item_type table
	function saveRel(){
	$item=$_POST['tagtext2'];
	$sqldelete="delete from `rel_media_item` where media_id ="."'".getVar('id')."'";
	mysql_query($sqldelete);
	if(!empty($item)){
	foreach($item as $im){
		$sqlinsert="insert into `rel_media_item` (media_id,item_id) values ("."'".getVar('id')."','".$im."');";
		mysql_query($sqlinsert);		
			}
		}
	}
	// foreach ($companies as $itemcompanies) {	
	// 	$objRelMediaCompanies->setVar('medias_id',getVar('id'));
	// 	$objRelMediaCompanies->setVar('companies_id',$itemcompanies);
	// 	$objRelMediaCompanies->store();
	// }

	
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
	
	
