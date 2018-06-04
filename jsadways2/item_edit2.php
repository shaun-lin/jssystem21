<?php
	// 2018-03-14 (Jimmy): 傑思jsadways2/media_edit2.php, 香港jsadways2hk/media_edit2.php, 豐富媒體jsadways2ff/media_edit2.php 共用此檔案
	require_once dirname(__DIR__) .'/autoload.php';
	include('include/db.inc.php');
	$status=$_POST['status'];
	if($status==1){	
		savetoDB();
		saveRel();
		ShowMessageAndRedirect('更新品項成功', 'item_list.php' , false);
	}
	else if($status==2){
		savetoDB();	
		$sqlselete="select id from items where name='".$_POST['name']."';";
		$seleteresult=mysql_query($sqlselete);
		$sqlID=mysql_fetch_array($seleteresult);
		$sql1=$sqlID['id'];
		header('location:item_edit.php?id='.$sql1);
	}






	function savetoDB(){
	$objSizeformat = CreateObject('Items');
	$objRelItemsType=CreateObject('RelItemsType');
	$objMtype=CreateObject('Mtype');

	if ( (!IsPermitted('superuser') && !in_array($_SESSION['departmentid'], [21, 22]))) {
		RedirectLink('item_list.php');
	}

	$db = clone($GLOBALS['app']->db);

	$name = str_replace(["\n", "\r"], ["\\n", ""], GetVar('name'));
	$display = str_replace(["\n", "\r"], ["\\n", ""], GetVar('display'));
	$mtype=GetVar('mtype');
	$mtype1=$_POST['mype1'];
	$dashboard=$_POST['dashboard'];


	//jackie 2018/05/21避免重複名稱輸入
	$sqlcheck="select * from `items` where id='".getVar('id')."'";
	$sqlcheck3="select * from `items` where name ='".getVar('name')."'";
	$result=mysql_query($sqlcheck);
	$check=mysql_fetch_array($result);
	$check3=mysql_query($sqlcheck3);
	if(getVar('id')==NULL){ //if null
		if(mysql_num_rows($check3)==0){
		$objSizeformat->setVar('id',getVar('id'));
		$objSizeformat->setVar('name', $name);
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
		$objSizeformat->setVar('display', $display);
		$objSizeformat->setVar('creator', $_SESSION['name']);
		$objSizeformat->setVar('time', time());
		$objSizeformat->store();
	}
	else{
			if(mysql_num_rows($check3)==0){
				$objSizeformat->setVar('id',getVar('id'));
				$objSizeformat->setVar('name', $name);
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

	function saveRel(){
	$mtype=GetVar('mtype');
	$dashboard=$_POST['dashboard'];
	if(!empty($mtype)){//如果前台沒有任何模板被checked
	mysql_query("delete from `rel_items_type` where items_id ="."'".getVar('id')."'");
	//一開始先刪掉所有對應值，之後照有checked的值新增
	$qwe=$_POST['mtype'];//only take checked number   mtype_id._.auto Increment number
	for($i=0;$i<=count($qwe)-1;$i++){
		//因為checked的值跟text的值無法mapping，所以前台value+流水號，進來再做分割
		for($ds=0;$ds<=count($dashboard)-1;$ds++){
		$myarrary=mb_split("_",$qwe[$i]);
		//字串分割
		$arraymatch[]="insert into `rel_items_type`(items_id,type_id,dashboard) values('".getVar('id')."','".$myarrary[0]."','".$dashboard[$myarrary[1]]."');";
		//分割後直接存入sql陣列，之後一次insert into
		}
	}
	$uniqueArray=array_unique($arraymatch);
	//前面做兩層for迴圈有很多重複值，做剃除
	$resortArray=array_values($uniqueArray);
	//array重新排序
	for($test=0;$test<=count(array_unique($arraymatch))-1;$test++){
		//一次新增
		mysql_query($resortArray[$test]);
			}
		}	
	}


	//下面都寫失敗的
	// $checked=$_POST['mtype'];
	// print_r($checked);
	// print_r("<br>");
	// print_r($dashboard);
	// exit();
	// if(!empty($mtype1)){
	// 	$sqldelete="delete from `rel_items_type` where items_id ="."'".getVar('id')."'";
	// 	mysql_query($sqldelete);
	// 	$sqlarray[]=null;
	// 	$sqlarrayds[]=null;
	// 	foreach($mtype1 as $itemtype){
	// 		$sqlarray[]=$itemtype;
	// 	}
	// 	foreach($dashboard as $itemdas){
	// 		$sqlarrayds[]=$itemdas;
	// 	}
	// 	for($i=0;$i<=count($sqlarray)-2;$i++){
	// 		$sqlinsert="insert into `rel_items_type`(items_id,type_id,dashboard) values('".getVar('id')."','".$sqlarray[$i+1]."','".$sqlarrayds[$i+1]."');";
	// 		//mysql_query($sqlinsert);
	// 	}
	// 	// foreach($sqlarray as $arrary){
	// 	// 	foreach($checked as $ck){
	// 	// 		if($array!=$ck){
	// 	// 			//mysql_query("DELETE FROM `rel_items_type` where items_id='".getVar('id')."' and type_id='".$ck."'");
	// 	// 			print_r("DELETE  if exit FROM `rel_items_type` where items_id='".getVar('id')."' and type_id='".$ck."'");
	// 	// 			print_r("<br>");
	// 	// 		}
	// 	// 	}
	// 	// }
	// }
	// exit();

	//jackie 2018/05/17 修改rel_item_type table
	// $sqldelete="delete from `rel_items_type` where items_id ="."'".getVar('id')."'";
	// $sqlinsert="";
	// mysql_query($sqldelete);
	// if(!empty($mtype1)){
	// foreach($mtype1 as $itemtype){
	// $sqlinsert="insert into `rel_items_type` (items_id,type_id) values ("."'".getVar('id')."','".$itemtype."')";
	// print_r($sqlinsert);
	// print_r("<br>"."測試用");
	// 	//mysql_query($sqlinsert);
	// 	$sqlupdete="update `rel_items_type` set dashboard='".$dashboard[$itemtype-1]."' where type_id='".$itemtype."'";
	// print_r($sqlupdete);
	// print_r("<br>");
	// 	//mysql_query($sqlupdete);
	// 	// echo $itemtype;
	// 	// print_r($dashboard);
	// 		}
	// 	}
	// exit();

			
	//foreach ($mtype as $itemtype) {
		
		// $objRelItemsType->setVar('id',getVar('id'));
		// $objRelItemsType->setVar('type_id',$itemtype);
		// $objRelItemsType->setVar('items_id',getVar('id'));
		// $objRelItemsType->store();
	//}

	//foreach ($dashboard as $type) {
	//	$objRelItemsType->setVar('items_id',getVar('items_id'));
	//	$objRelItemsType->setVar('type_id',$itemtype);
//
	//	$objRelItemsType->store();
	//}

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
	
	ShowMessageAndRedirect('更新品項規格成功', 'item_list.php?item='. GetVar('id', '') , false);


