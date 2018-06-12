<?php
	session_start();
	include('include/db.inc.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>編輯媒體</title>
	<?php include("public/head.php"); ?>

</head>

<body>
	<?php include("public/topbar.php"); ?>
		<div class="container-fluid">
		<div class="row-fluid">
			<?php include("public/left.php"); ?>

			<noscript>
				<div class="alert alert-block span10">
					<h4 class="alert-heading">Warning!</h4>
					<p>You need to have <a href="http://en.wikipedia.org/wiki/JavaScript" target="_blank">JavaScript</a> enabled to use this site.</p>
				</div>
			</noscript>

			<div id="content" class="span10">
			<!-- content starts -->
			<?php
				$sql1 = "SELECT * FROM campaign WHERE id= ".$_GET['campaign'];
				$result1 = mysql_query($sql1);
				$row1 = mysql_fetch_array($result1);
				$sql2 = "SELECT * FROM media157 WHERE id= ".$_GET['id'];
				$result2 = mysql_query($sql2);
				$row2 = mysql_fetch_array($result2);
			?>
			<div class="row-fluid sortable">
				<div class="box span12">
					<div class="box-header well" data-original-title>
						<h2><i class="icon-edit"></i> <?php echo $row1['name']; ?>-編輯媒體-Line Today</h2>

					</div>
					<div class="box-content">
						<form class="form-horizontal" action="mtype_Schedule_edit2.php?campaign=<?php echo $_GET['campaign']; ?>&id=<?php echo $_GET['id']; ?>&cue=<?php echo $_GET['cue']; ?>&media=<?php echo $_GET['media']; ?>" method="post">
						  <fieldset>
                            <script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
                            <script type="text/javascript" src="js/jquery.xml2json.js"></script>
							<script type="text/javascript" src="js/JSLINQ.js"></script>
                            <script type="text/javascript">
							<!--
								var jsonScenery = [];
								var jsonHotel = [];
								$(document).ready(function()
								{
									Page_Init();
								});
								function Page_Init()
								{
									var jsonData =
									[
										{
											"categoryId": "1",
											"categoryName": "Line Today 原生廣告"
										},
										{
											"categoryId": "2",
											"categoryName": "Line Today 富邦悍將直播贊助案"
										}
									];


									$('#SelectCategory').empty().append($('<option></option>').val('').text('------'));

									$.each(jsonData, function (i, item)
									{
										$('#SelectCategory').append($('<option></option>').val(item.categoryId).text(item.categoryName));
									});

									$('#SelectSubCategory').empty().append($('<option></option>').val('').text('------'));

									$('#SelectCategory').change(function(){
										ChangeCategory();
									});
									$('#SelectThreeCategory').empty().append($('<option></option>').val('').text('------'));
					     			$('#SelectSubCategory').change(function(){
										ChangeSubCategory();
									});
									$('#SelectThreeCategory').change(function(){
										ChangeThreeCategory();
									});

									//Abow Start
									<?php 
										include('campaign_required_select_edit.php');
										echo $Select_str;
									?>

									$('#SelectType').change(function(){
										//ChangeSelectType();
									});
									//Abow end
								}

								function ChangeCategory()
								{
									//變動第一個下拉選單
									$('#SelectSubCategory').empty().append($('<option></option>').val('').text('------'));
									$('#SelectThreeCategory').empty().append($('<option></option>').val('').text('------'));

									var categoryId = $.trim($('#SelectCategory option:selected').val());

									var jsonData = [];

									if(categoryId == '1' || categoryId == '2')
									{
										jsonData =
										[
											{
												"subCategoryId": "1",
												"subCategoryName": "iOS"
											},
											{
												"subCategoryId": "2",
												"subCategoryName": "Android"
											},
											{
												"subCategoryId": "3",
												"subCategoryName": "iOS/Android/全投放"
											}
										];
									}
									// else if(categoryId == '2')
									// {
									// 	jsonData =
									// 	[
									// 		{
									// 			"subCategoryId": "2",
									// 			"subCategoryName": "PC"
									// 		},
									// 		{
									// 			"subCategoryId": "3",
									// 			"subCategoryName": "PC+iOS"
									// 		},
									// 		{
									// 			"subCategoryId": "32",
									// 			"subCategoryName": "PC+Android"
									// 		},
									// 		{
									// 			"subCategoryId": "33",
									// 			"subCategoryName": "PC+Mobile"
									// 		}
									// 	];
									
									// }

									if(categoryId.length != 0)
									{
										$.each(jsonData , function(i, item){
											$('#SelectSubCategory').append($('<option></option>').val(item.subCategoryId).text(item.subCategoryName));
										});

									}
								}

								function ChangeSubCategory()
								{
									//變動第二個下拉選單
									$('#SelectThreeCategory').empty().append($('<option></option>').val('').text('------'));

									var subCategoryId = $.trim($('#SelectCategory option:selected').val());

									var jsonData = [];

									if(subCategoryId == '1')
									{
										jsonData =
										[
											{
												"threeCategoryId": "1",
												"threeCategoryName": "LINE 動態消息廣告"
											}
										];
									}else if(subCategoryId == '2')
									{
										jsonData =
										[
											{
												"threeCategoryId": "2",
												"threeCategoryName": "Line Today 富邦悍將直播"
											}
										];
									}

									// else if(subCategoryId == '2')
									// {
									// 	jsonData =
									// 	[
									// 		{
									// 			"threeCategoryId": "1",
									// 			"threeCategoryName": "Facebook側邊欄(Marketplace)"
									// 		},
									// 		{
									// 			"threeCategoryId": "22",
									// 			"threeCategoryName": "Facebook動態消息(News Feed)"
									// 		},
									// 		{
									// 			"threeCategoryId": "23",
									// 			"threeCategoryName": "Facebook側邊欄(Marketplace)+Facebook動態消息(News Feed)"
									// 		}
									// 	];
									// }

									if(subCategoryId.length != 0)
									{
										$.each(jsonData , function(i, item){
											$('#SelectThreeCategory').append($('<option></option>').val(item.threeCategoryId).text(item.threeCategoryName));
										});

									}
								}


								function ChangeThreeCategory()
								{
									//變動第二個下拉選單

									var categoryId = $.trim($('#SelectCategory option:selected').val());
									var categoryName = $.trim($('#SelectCategory option:selected').text());
									var subCategoryId = $.trim($('#SelectThreeCategory option:selected').val());
									var subCategoryName = $.trim($('#SelectThreeCategory option:selected').text());

									if((subCategoryId == '1') || (subCategoryId == '2'))
									{
										<?php
											$sqlsize = "SELECT * FROM sizeformat WHERE id =193";
											$resultsize = mysql_query($sqlsize);
											$rowsize = mysql_fetch_array($resultsize);
										?>
										 document.getElementById('format1').value = "<?php echo $rowsize['format1']; ?>";
										 document.getElementById('format2').value = "<?php echo $rowsize['format2']; ?>";
									}
									// else if((subCategoryId == '22')||(subCategoryId == '23')||(subCategoryId == '6')||(subCategoryId == '7')||(subCategoryId == '8'))
									// {
									// 	<?php
									// 		$sqlsize = "SELECT * FROM sizeformat WHERE id =172";
									// 		$resultsize = mysql_query($sqlsize);
									// 		$rowsize = mysql_fetch_array($resultsize);
									// 	?>
									// 	 document.getElementById('format1').value = "<?php echo $rowsize['format1']; ?>";
									// 	 document.getElementById('format2').value = "<?php echo $rowsize['format2']; ?>";
									// }
									
								};
							-->
							function compare(){
							 var f = document.getElementById('date1').value,
								e = document.getElementById('date2').value;
							 if(Date.parse(f.valueOf()) > Date.parse(e.valueOf()))
							 {
							  	alert('警告！到期日期不能小於起始日期');
								document.getElementById('date2').value=document.getElementById('date1').value;
								document.getElementById('days1').value=1;
								document.getElementById('days').value=Number(document.getElementById('days1').value)+Number(document.getElementById('days2').value)+Number(document.getElementById('days3').value)+Number(document.getElementById('days4').value)+Number(document.getElementById('days5').value);
							 }
							 else
							 {
						     	var ff=new Date(f);
							 	var ee=new Date(e);
							 	var d=((ee-ff)/86400000)+1;
							 	document.getElementById('days1').value=d;
								document.getElementById('days').value=Number(document.getElementById('days1').value)+Number(document.getElementById('days2').value)+Number(document.getElementById('days3').value)+Number(document.getElementById('days4').value)+Number(document.getElementById('days5').value);
							 }
							}

							function compare2(){
							 var f = document.getElementById('date3').value,
								e = document.getElementById('date4').value;
							 if(Date.parse(f.valueOf()) > Date.parse(e.valueOf()))
							 {
							  	alert('警告！到期日期不能小於起始日期');
								document.getElementById('date4').value=document.getElementById('date3').value;
								document.getElementById('days2').value=1;
								document.getElementById('days').value=Number(document.getElementById('days1').value)+Number(document.getElementById('days2').value)+Number(document.getElementById('days3').value)+Number(document.getElementById('days4').value)+Number(document.getElementById('days5').value);
							 }
							 else
							 {
						     	var ff=new Date(f);
							 	var ee=new Date(e);
							 	var d=((ee-ff)/86400000)+1;
							 	document.getElementById('days2').value=d;
								document.getElementById('days').value=Number(document.getElementById('days1').value)+Number(document.getElementById('days2').value)+Number(document.getElementById('days3').value)+Number(document.getElementById('days4').value)+Number(document.getElementById('days5').value);
							 }
							}

							function compare3(){
							 var f = document.getElementById('date5').value,
								e = document.getElementById('date6').value;
							 if(Date.parse(f.valueOf()) > Date.parse(e.valueOf()))
							 {
							  	alert('警告！到期日期不能小於起始日期');
								document.getElementById('date6').value=document.getElementById('date5').value;
								document.getElementById('days3').value=1;
								document.getElementById('days').value=Number(document.getElementById('days1').value)+Number(document.getElementById('days2').value)+Number(document.getElementById('days3').value)+Number(document.getElementById('days4').value)+Number(document.getElementById('days5').value);
							 }
							 else
							 {
						     	var ff=new Date(f);
							 	var ee=new Date(e);
							 	var d=((ee-ff)/86400000)+1;
							 	document.getElementById('days3').value=d;
								document.getElementById('days').value=Number(document.getElementById('days1').value)+Number(document.getElementById('days2').value)+Number(document.getElementById('days3').value)+Number(document.getElementById('days4').value)+Number(document.getElementById('days5').value);
							 }
							}

							function compare4(){
							 var f = document.getElementById('date7').value,
								e = document.getElementById('date8').value;
							 if(Date.parse(f.valueOf()) > Date.parse(e.valueOf()))
							 {
							  	alert('警告！到期日期不能小於起始日期');
								document.getElementById('date8').value=document.getElementById('date7').value;
								document.getElementById('days4').value=1;
								document.getElementById('days').value=Number(document.getElementById('days1').value)+Number(document.getElementById('days2').value)+Number(document.getElementById('days3').value)+Number(document.getElementById('days4').value)+Number(document.getElementById('days5').value);
							 }
							 else
							 {
						     	var ff=new Date(f);
							 	var ee=new Date(e);
							 	var d=((ee-ff)/86400000)+1;
							 	document.getElementById('days4').value=d;
								document.getElementById('days').value=Number(document.getElementById('days1').value)+Number(document.getElementById('days2').value)+Number(document.getElementById('days3').value)+Number(document.getElementById('days4').value)+Number(document.getElementById('days5').value);
							 }
							}

							function compare5(){
							 var f = document.getElementById('date9').value,
								e = document.getElementById('date10').value;
							 if(Date.parse(f.valueOf()) > Date.parse(e.valueOf()))
							 {
							  	alert('警告！到期日期不能小於起始日期');
								document.getElementById('date10').value=document.getElementById('date9').value;
								document.getElementById('days5').value=1;
								document.getElementById('days').value=Number(document.getElementById('days1').value)+Number(document.getElementById('days2').value)+Number(document.getElementById('days3').value)+Number(document.getElementById('days4').value)+Number(document.getElementById('days5').value);
							 }
							 else
							 {
						     	var ff=new Date(f);
							 	var ee=new Date(e);
							 	var d=((ee-ff)/86400000)+1;
							 	document.getElementById('days5').value=d;
								document.getElementById('days').value=Number(document.getElementById('days1').value)+Number(document.getElementById('days2').value)+Number(document.getElementById('days3').value)+Number(document.getElementById('days4').value)+Number(document.getElementById('days5').value);
							 }
							}
							function change11(){
							 document.getElementById('price1').value=document.getElementById('totalprice1').value/document.getElementById('click1').value;
							 change();
							}
							function change22(){
							 document.getElementById('price2').value=document.getElementById('totalprice2').value/document.getElementById('click2').value;
							 change();
							}
							function change33(){
							 document.getElementById('price3').value=document.getElementById('totalprice3').value/document.getElementById('click3').value;
							 change();
							}
							function change44(){
							 document.getElementById('price4').value=document.getElementById('totalprice4').value/document.getElementById('click4').value;
							 change();
							}
							function change55(){
							 document.getElementById('price5').value=document.getElementById('totalprice5').value/document.getElementById('click5').value;
							 change();
							}
							function change1(){
							<?php if($_GET['cue']==1){ ?>
							 document.getElementById('impression1').value=Math.round((document.getElementById('totalprice1').value*1000)/document.getElementById('price1').value);
							 document.getElementById('click1').value=Math.round((document.getElementById('impression1').value*document.getElementById('ctr').value)/100);
							 <?php } ?>
							 <?php if($_GET['cue']==2){ ?>
							 document.getElementById('price1').value=(document.getElementById('totalprice1').value*1000)/document.getElementById('impression1').value;
							 <?php } ?>
							 change();
							}
							function change2(){
							<?php if($_GET['cue']==1){ ?>
							 document.getElementById('impression2').value=Math.round((document.getElementById('totalprice2').value*1000)/document.getElementById('price2').value);
							 document.getElementById('click2').value=Math.round((document.getElementById('impression2').value*document.getElementById('ctr').value)/100);
							 <?php } ?>
							 <?php if($_GET['cue']==2){ ?>
							 document.getElementById('price2').value=(document.getElementById('totalprice2').value*1000)/document.getElementById('impression2').value;
							 <?php } ?>
							 change();
							}
							function change3(){
							<?php if($_GET['cue']==1){ ?>
							 document.getElementById('impression3').value=Math.round((document.getElementById('totalprice3').value*1000)/document.getElementById('price3').value);
							 document.getElementById('click3').value=Math.round((document.getElementById('impression3').value*document.getElementById('ctr').value)/100);
							 <?php } ?>
							 <?php if($_GET['cue']==2){ ?>
							 document.getElementById('price3').value=(document.getElementById('totalprice3').value*1000)/document.getElementById('impression3').value;
							 <?php } ?>
							 change();
							}
							function change4(){
							<?php if($_GET['cue']==1){ ?>
							 document.getElementById('impression4').value=Math.round((document.getElementById('totalprice4').value*1000)/document.getElementById('price4').value);
							 document.getElementById('click4').value=Math.round((document.getElementById('impression4').value*document.getElementById('ctr').value)/100);
							 <?php } ?>
							 <?php if($_GET['cue']==2){ ?>
							 document.getElementById('price4').value=(document.getElementById('totalprice4').value*1000)/document.getElementById('impression4').value;
							 <?php } ?>
							 change();
							}
							function change5(){
							<?php if($_GET['cue']==1){ ?>
							 document.getElementById('impression5').value=Math.round((document.getElementById('totalprice5').value*1000)/document.getElementById('price5').value);
							 document.getElementById('click5').value=Math.round((document.getElementById('impression5').value*document.getElementById('ctr').value)/100);
							 <?php } ?>
							 <?php if($_GET['cue']==2){ ?>
							 document.getElementById('price5').value=(document.getElementById('totalprice5').value*1000)/document.getElementById('impression5').value;
							 <?php } ?>
							 change();
							}
							<?php
								if($row1['agency_id']!=0){
									$sql4 = "SELECT * FROM commission WHERE agency= ".$row1['agency_id']." AND media=".$_GET['media'];
									$result4 = mysql_query($sql4);
									$row4 = mysql_fetch_array($result4);
									if($row4['commission5']!=0){
										$commission1=$row4['commission1'];
										$commission4=$row4['commission4'];
									}else{
										$commission1=0;
										$commission4=0;
									}
								}else{
									$commission1=0;
									$commission4=0;
								}
								$sqlmedia = "SELECT * FROM media WHERE id=".$_GET['media'];
								$resultmedia = mysql_query($sqlmedia);
								$rowmedia = mysql_fetch_array($resultmedia);
								$profit=$rowmedia['profit'];
							?>
							function change(){
							 document.getElementById('totalprice').value=Number(document.getElementById('totalprice1').value)+Number(document.getElementById('totalprice2').value)+Number(document.getElementById('totalprice3').value)+Number(document.getElementById('totalprice4').value)+Number(document.getElementById('totalprice5').value);
							 document.getElementById('number1').value=Number(document.getElementById('click1').value)+Number(document.getElementById('click2').value)+Number(document.getElementById('click3').value)+Number(document.getElementById('click4').value)+Number(document.getElementById('click5').value);
							 <?php if($_GET['cue']==1){ ?>
							 document.getElementById('number2').value=Number(document.getElementById('impression1').value)+Number(document.getElementById('impression2').value)+Number(document.getElementById('impression3').value)+Number(document.getElementById('impression4').value)+Number(document.getElementById('impression5').value);
							 <?php } ?>
							 <?php if($_GET['cue']==2){ ?>
							 document.getElementById('a1').value=Math.round((Number(document.getElementById('totalprice').value)*<?php echo $commission1; ?>)/100);
							 document.getElementById('a2').value=Math.round((Number(document.getElementById('totalprice').value)*<?php echo $commission4; ?>)/100);
							 document.getElementById('a3').value=Math.round((Number(document.getElementById('totalprice').value)*Number(document.getElementById('profit').value))/100);
							 document.getElementById('a4').value=Math.round(document.getElementById('totalprice').value-document.getElementById('a1').value-document.getElementById('a2').value-document.getElementById('a3').value);
							 <?php } ?>
							}
							</script>
                       		<div class="control-group">
							  <label class="control-label" for="SelectType">類別(Type)</label>
							  <div class="controls">
								 <select id="SelectType" name="SelectType">
							    </select>
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label" for="SelectSystem">系統(System)</label>
							  <div class="controls">
								 <select id="SelectSystem" name="SelectSystem">
							    </select>
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label" for="SelectCategory">廣告類型(Ad type)</label>
							  <div class="controls">
								 <select id="SelectCategory" name="SelectCategory" style="width:400px">
							    </select>
							  </div>
							</div>

							<div class="control-group">
							  <label class="control-label" for="SelectSubCategory">系統(System)</label>
							  <div class="controls">
								 <select id="SelectSubCategory" name="SelectSubCategory" style="width:400px">
							    </select>
							  </div>
							</div>
                            <div class="control-group">
							  <label class="control-label" for="SelectThreeCategory">版位(Position)</label>
							  <div class="controls">
								 <select id="SelectThreeCategory" name="SelectThreeCategory" style="width:500px">
							    </select>
							  </div>
							</div>
                            <!-- <div class="control-group">
							  <label class="control-label" for="taget">目標受眾(Taget)</label>
							  <div class="controls">
								 <select id="taget" name="taget" style="width:400px">
                                 	<option value="男" <?php if($row2['taget']=='男'){echo 'selected';} ?>>男</option>
                                    <option value="女" <?php if($row2['taget']=='女'){echo 'selected';} ?>>女</option>
                                    <option value="男＋女" <?php if($row2['taget']=='男＋女'){echo 'selected';} ?>>男＋女</option>
							    </select>
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label" for="actions">動作鈕(Action)</label>
							  <div class="controls">
								 <select id="actions" name="actions" style="width:400px">
								 <option value="立刻購買" <?php if($row2['actions']=='立刻購買'){echo 'selected';} ?>>立刻購買</option>
								 <option value="搶先預定" <?php if($row2['actions']=='搶先預定'){echo 'selected';} ?>>搶先預定</option>
								 <option value="瞭解詳情" <?php if($row2['actions']=='瞭解詳情'){echo 'selected';} ?>>瞭解詳情</option>
								 <option value="註冊" <?php if($row2['actions']=='註冊'){echo 'selected';} ?>>註冊</option>
								 <option value="下載" <?php if($row2['actions']=='下載'){echo 'selected';} ?>>下載</option>
								 <option value="觀看更多" <?php if($row2['actions']=='觀看更多'){echo 'selected';} ?>>觀看更多</option>
								 <option value="聯絡我們" <?php if($row2['actions']=='聯絡我們'){echo 'selected';} ?>>聯絡我們</option>
							    </select>
							  </div>
							</div> -->
							  <div class="control-group">
								<label class="control-label">規格(Size)</label>
								<div class="controls">
                                  <textarea id="format1" name="format1" readonly><?php echo $row2['format1']; ?></textarea>
								</div>
							  </div>

							  <div class="control-group">
								<label class="control-label">格式(Format)</label>
								<div class="controls">
                                <textarea id="format2" name="format2" readonly><?php echo $row2['format2']; ?></textarea>
								</div>
							  </div>

							  <div class="control-group">
							  <label class="control-label" for="wheel">輪替/固定(R/F)</label>
							  <div class="controls">
								 <select id="wheel" name="wheel">
									<option value="R">R</option>
							    </select>
							  </div>
							</div>

							<div class="control-group">
							  <label class="control-label" for="typeahead">預估點擊率(Est. CTR)</label>
							  <div class="controls">
								<input class="input-xlarge" id="ctr" name="ctr" type="text" value="<?php echo $row2['ctr']; ?>" onChange="number()" style="width:100px">%
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label" for="date01">刊登期間1(Period)</label>
							  <div class="controls">
								<input type="text" class="datepicker" id="date1" name="date1" value="<?php if($row2['date1']!=0){echo date('m',$row2['date1']).'/'.date('d',$row2['date1']).'/'.date('Y',$row2['date1']);} ?>" style="width:100px">~<input type="text" name="date2" class="input-xlarge datepicker" id="date2" value="<?php if($row2['date2']!=0){echo date('m',$row2['date2']).'/'.date('d',$row2['date2']).'/'.date('Y',$row2['date2']);} ?>" style="width:100px" onChange="compare()"> 共<input class="input-xlarge" id="days1" name="days1" type="text" value="<?php echo $row2['days1']; ?>" style="width:30px" readonly>天 定價<input class="input-xlarge" id="price1" name="price1" type="text" value="<?php echo $row2['price1']; ?>" style="width:30px" <?php if($_GET['cue']==2){echo 'readonly';} ?>>總價<input class="input-xlarge" id="totalprice1" name="totalprice1" type="text" value="<?php echo $row2['totalprice1']; ?>" onChange="change1()" style="width:70px">預估點擊<input class="input-xlarge" id="click1" name="click1" type="text" onChange="change11()" value="<?php echo $row2['click1']; ?>" style="width:70px"><?php if($_GET['cue']==1){ ?>保証曝光數<input class="input-xlarge" id="impression1" name="impression1" onChange="change()" type="text" value="<?php echo $row2['impression1']; ?>"  style="width:70px"><?php } ?><?php if($_GET['cue']==2){?><font color="#FF0000">*總價及預估點擊欄位非系統自動換算，請自行確認數字是否正確</font><?php } ?>
							  </div>
							</div>
                            <div class="control-group">
							  <label class="control-label" for="date02">刊登期間2(Period)</label>
							  <div class="controls">
								<input type="text" class="datepicker" id="date3" name="date3" value="<?php if($row2['date3']!=0){echo date('m',$row2['date3']).'/'.date('d',$row2['date3']).'/'.date('Y',$row2['date3']);} ?>" style="width:100px">~<input type="text" name="date4" class="input-xlarge datepicker" id="date4" value="<?php if($row2['date4']!=0){echo date('m',$row2['date4']).'/'.date('d',$row2['date4']).'/'.date('Y',$row2['date4']);} ?>" style="width:100px" onChange="compare2()">共<input class="input-xlarge" id="days2" name="days2" type="text" value="<?php echo $row2['days2']; ?>" style="width:30px" readonly>天 定價<input class="input-xlarge" id="price2" name="price2" type="text" value="<?php echo $row2['price2']; ?>" style="width:30px" <?php if($_GET['cue']==2){echo 'readonly';} ?>>總價<input class="input-xlarge" id="totalprice2" name="totalprice2" type="text" value="<?php echo $row2['totalprice2']; ?>" onChange="change2()" style="width:70px">預估點擊<input class="input-xlarge" id="click2" name="click2" type="text" onChange="change22()" value="<?php echo $row2['click2']; ?>" style="width:70px"><?php if($_GET['cue']==1){ ?>保証曝光數<input class="input-xlarge" id="impression2" name="impression2" onChange="change()" type="text" value="<?php echo $row2['impression2']; ?>"  style="width:70px"><?php } ?>
							  </div>
							</div>
                             <div class="control-group">
							  <label class="control-label" for="date03">刊登期間3(Period)</label>
							  <div class="controls">
								<input type="text" class="datepicker" id="date5" name="date5" value="<?php if($row2['date5']!=0){echo date('m',$row2['date5']).'/'.date('d',$row2['date5']).'/'.date('Y',$row2['date5']);} ?>" style="width:100px">~<input type="text" name="date6" class="input-xlarge datepicker" id="date6" value="<?php if($row2['date6']!=0){echo date('m',$row2['date6']).'/'.date('d',$row2['date6']).'/'.date('Y',$row2['date6']);} ?>" style="width:100px" onChange="compare3()">共<input class="input-xlarge" id="days3" name="days3" type="text" value="<?php echo $row2['days3']; ?>" style="width:30px" readonly>天 定價<input class="input-xlarge" id="price3" name="price3" type="text" value="<?php echo $row2['price3']; ?>" style="width:30px" <?php if($_GET['cue']==2){echo 'readonly';} ?>>總價<input class="input-xlarge" id="totalprice3" name="totalprice3" type="text" value="<?php echo $row2['totalprice3']; ?>" onChange="change3()" style="width:70px">預估點擊<input class="input-xlarge" id="click3" name="click3" type="text" onChange="change33()" value="<?php echo $row2['click3']; ?>" style="width:70px"><?php if($_GET['cue']==1){ ?>保証曝光數<input class="input-xlarge" id="impression3" name="impression3" onChange="change()" type="text" value="<?php echo $row2['impression3']; ?>"  style="width:70px"><?php } ?>
							  </div>
							</div>
                             <div class="control-group">
							  <label class="control-label" for="date04">刊登期間4(Period)</label>
							  <div class="controls">
								<input type="text" class="datepicker" id="date7" name="date7" value="<?php if($row2['date7']!=0){echo date('m',$row2['date7']).'/'.date('d',$row2['date7']).'/'.date('Y',$row2['date7']);} ?>" style="width:100px">~<input type="text" name="date8" class="input-xlarge datepicker" id="date8" value="<?php if($row2['date8']!=0){echo date('m',$row2['date8']).'/'.date('d',$row2['date8']).'/'.date('Y',$row2['date8']);} ?>" style="width:100px" onChange="compare4()">共<input class="input-xlarge" id="days4" name="days4" type="text" value="<?php echo $row2['days4']; ?>" style="width:30px" readonly>天 定價<input class="input-xlarge" id="price4" name="price4" type="text" value="<?php echo $row2['price4']; ?>" style="width:30px" <?php if($_GET['cue']==2){echo 'readonly';} ?>>總價<input class="input-xlarge" id="totalprice4" name="totalprice4" type="text" value="<?php echo $row2['totalprice4']; ?>" onChange="change4()" style="width:70px">預估點擊<input class="input-xlarge" id="click4" name="click4" type="text" onChange="change44()" value="<?php echo $row2['click4']; ?>" style="width:70px"><?php if($_GET['cue']==1){ ?>保証曝光數<input class="input-xlarge" id="impression4" name="impression4" onChange="change()" type="text" value="<?php echo $row2['impression4']; ?>"  style="width:70px"><?php } ?>
							  </div>
							</div>
                             <div class="control-group">
							  <label class="control-label" for="date05">刊登期間5(Period)</label>
							  <div class="controls">
								<input type="text" class="datepicker" id="date9" name="date9" value="<?php if($row2['date9']!=0){echo date('m',$row2['date9']).'/'.date('d',$row2['date9']).'/'.date('Y',$row2['date9']);} ?>" style="width:100px">~<input type="text" name="date10" class="input-xlarge datepicker" id="date10" value="<?php if($row2['date10']!=0){echo date('m',$row2['date10']).'/'.date('d',$row2['date10']).'/'.date('Y',$row2['date10']);} ?>" style="width:100px" onChange="compare5()">共<input class="input-xlarge" id="days5" name="days5" type="text" value="<?php echo $row2['days5']; ?>" style="width:30px" readonly>天 定價<input class="input-xlarge" id="price5" name="price5" type="text" value="<?php echo $row2['price5']; ?>" style="width:30px" <?php if($_GET['cue']==2){echo 'readonly';} ?>>總價<input class="input-xlarge" id="totalprice5" name="totalprice5" type="text" value="<?php echo $row2['totalprice5']; ?>" onChange="change5()" style="width:70px">預估點擊<input class="input-xlarge" id="click5" name="click5" type="text" onChange="change55()" value="<?php echo $row2['click5']; ?>" style="width:70px"><?php if($_GET['cue']==1){ ?>保証曝光數<input class="input-xlarge" id="impression5" name="impression5" onChange="change()" type="text" value="<?php echo $row2['impression5']; ?>"  style="width:70px"><?php } ?>
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label" for="days">天數(Days)</label>
							  <div class="controls">
								<input class="input-xlarge" id="days" name="days" type="text" value="<?php echo $row2['days']; ?>" style="width:100px">天
							  </div>
							</div>

							<div class="control-group">
								<label class="control-label">素材提供期限(Material Due)</label>
								<div class="controls">
								  <input id="due" name="due" type="text" value="<?php echo $row2['due']; ?>" style="width:200px">
								</div>
							  </div>
                            <?php if($_GET['cue']==2){ ?>
                           <div class="control-group">
							  <label class="control-label" for="profit">修改利潤%</label>
							  <div class="controls">
								<input class="input-xlarge" id="profit" name="profit" type="text" value="<?php echo $profit; ?>" style="width:100px" onChange="change()">
							  </div>
							</div>
                            <?php } ?>
                            <div class="control-group">
							  <label class="control-label" for="totalprice">總投放金額(Total)</label>
							  <div class="controls">
								$<input class="input-xlarge" id="totalprice" name="totalprice" type="text" value="<?php echo $row2['totalprice']; ?>" style="width:100px"   required>元
							  </div>
							</div>
                            <?php if($_GET['cue']==2){ ?>
                            <div class="control-group">
							  <label class="control-label" for="a1">佣金<?php echo $commission1; ?>%</label>
							  <div class="controls">
								<input class="input-xlarge" id="a1" name="a1" type="text" value="<?php echo $row2['a1']; ?>" style="width:100px" readonly>
							  </div>
							</div>
                            <div class="control-group">
							  <label class="control-label" for="a2">現折<?php echo $commission4; ?>%</label>
							  <div class="controls">
								<input class="input-xlarge" id="a2" name="a2" type="text" value="<?php echo $row2['a2']; ?>" style="width:100px" readonly>
							  </div>
							</div>
                            <div class="control-group">
							  <label class="control-label" for="a3">利潤</label>
							  <div class="controls">
								<input class="input-xlarge" id="a3" name="a3" type="text" value="<?php echo $row2['a3']; ?>" style="width:100px" readonly>
							  </div>
							</div>
                            <div class="control-group">
							  <label class="control-label" for="a4">行政發媒體金額</label>
							  <div class="controls">
								<input class="input-xlarge" id="a4" name="a4" type="text" value="<?php echo $row2['a4']; ?>" style="width:100px" readonly>
							  </div>
							</div>
                            <?php } ?>
                            <div class="control-group">
							  <label class="control-label" for="number1">預估點擊數(Est. Clicks)</label>
							  <div class="controls">
								<input class="input-xlarge" id="number1" name="number1" type="text" value="<?php echo $row2['quantity']; ?>" style="width:100px" readonly>
							  </div>
							</div>
                             <?php if($_GET['cue']==1){ ?>
                            <div class="control-group">
							  <label class="control-label" for="number2">保証曝光數(Est. Impressions)</label>
							  <div class="controls">
								<input class="input-xlarge" id="number2" name="number2" type="text" value="<?php echo $row2['quantity2']; ?>" style="width:100px" readonly>
							  </div>
							</div>
							<!--
							<div class="control-group">
							  <label class="control-label" for="gearing">連動修改對內CUE</label>
							  <div class="controls">
								<input type="checkbox" name="gearing" value="1" ><p style="color:red">注意。若為對外一個媒體，對內多個媒體的情況，請逐個修改對內CUE</p>
							  </div>
							</div>
							-->
                            <?php } ?>
                            <div class="control-group">
							  <label class="control-label" for="others">備註</label>
							  <div class="controls">
								<textarea id="others" name="others" ><?php echo $row2['others']; ?></textarea>
							  </div>
							</div>
							<div class="form-actions">
							  <button type="submit" class="btn btn-primary">確定修改</button>
							</div>
						  </fieldset>
						</form>

					</div>
				</div><!--/span-->

			</div><!--/row-->



					<!-- content ends -->
			</div><!--/#content.span10-->
				</div><!--/fluid-row-->

		<hr>

		<?php include("public/footer.php"); ?>

	</div><!--/.fluid-container-->

	<?php include("public/js.php"); ?>

</body>
</html>
