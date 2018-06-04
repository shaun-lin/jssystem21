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
				$sql2 = "SELECT * FROM media160 WHERE id= ".$_GET['id'];
				$result2 = mysql_query($sql2);
				$row2 = mysql_fetch_array($result2);
			?>
			<div class="row-fluid sortable">
				<div class="box span12">
					<div class="box-header well" data-original-title>
						<h2><i class="icon-edit"></i> <?php echo $row1['name']; ?>-編輯媒體-手機簡訊</h2>

					</div>
					<div class="box-content">
						<form name="myForm" class="form-horizontal" action="mtype_MMS_edit2.php?campaign=<?php echo $_GET['campaign']; ?>&id=<?php echo $_GET['id']; ?>&cue=<?php echo $_GET['cue']; ?>&media=<?php echo $_GET['media']; ?>" method="post">
						  <fieldset>

                          	<div class="control-group">
								<label class="control-label">網站(Website)</label>
								<div class="controls">
								  <input class="input-xlarge" id="website" name="website" type="text" value="<?php echo $row2['website']; ?>" readonly>
								</div>
							  </div>
                          	<div class="control-group">
								<label class="control-label">頻道(Channel)</label>
								<div class="controls">
								  <input class="input-xlarge" id="channel" name="channel" type="text" value="<?php echo $row2['channel']; ?>" readonly>
								</div>
							  </div>
                            <script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
                            <script type="text/javascript" src="js/jquery.xml2json.js"></script>
							<script type="text/javascript" src="js/JSLINQ.js"></script>
                            <script type="text/javascript">

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
											"categoryName": "文字簡訊"
										},
										{
											"categoryId": "2",
											"categoryName": "簡訊廣告-基本篩選"
										},
										{
											"categoryId": "3",
											"categoryName": "簡訊廣告-特殊篩選"
										},
										{
											"categoryId": "4",
											"categoryName": "Happygo"
										},
										{
											"categoryId": "5",
											"categoryName": "台哥大LBS"
										},
										{
											"categoryId": "6",
											"categoryName": "中華、遠傳LBS"
										}
									];
									$('#position').empty().append($('<option></option>').val('').text('------'));

									$.each(jsonData, function (i, item)
									{
										$('#position').append($('<option></option>').val(item.categoryId).text(item.categoryName));
									});

									$('#position').change(function(){
										ChangeCategory();
									});
									 document.getElementById('position').value = "<?php echo $row2['position']; ?>";

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
									//變動第二個下拉選單


									var categoryId = $.trim($('#position option:selected').val());
									var categoryName = $.trim($('#position option:selected').text());

									if(categoryId == '1')
									{
										<?php
											$sqlsize = "SELECT * FROM sizeformat WHERE id =97";
											$resultsize = mysql_query($sqlsize);
											$rowsize = mysql_fetch_array($resultsize);
										?>
										 document.getElementById('format1').value = "<?php echo $rowsize['format1']; ?>";
										 document.getElementById('format2').value = "<?php echo $rowsize['format2']; ?>";
										 document.getElementById('others').value = "";
									}

									else if(categoryId == '2')
									{
										<?php
											$sqlsize = "SELECT * FROM sizeformat WHERE id =98";
											$resultsize = mysql_query($sqlsize);
											$rowsize = mysql_fetch_array($resultsize);
										?>
										 document.getElementById('format1').value = "<?php echo $rowsize['format1']; ?>";
										 document.getElementById('format2').value = "<?php echo $rowsize['format2']; ?>";
										 document.getElementById('others').value = "可發送電信：中華、遠傳、台哥大、亞太、威寶";
									}
									else if(categoryId == '3')
									{
										<?php
											$sqlsize = "SELECT * FROM sizeformat WHERE id =99";
											$resultsize = mysql_query($sqlsize);
											$rowsize = mysql_fetch_array($resultsize);
										?>
										 document.getElementById('format1').value = "<?php echo $rowsize['format1']; ?>";
										 document.getElementById('format2').value = "<?php echo $rowsize['format2']; ?>";
										 document.getElementById('others').value = "可發送電信：中華、遠傳、台哥大、亞太、威寶;台哥大特篩條件需加收特篩費2000元";
									}
									else if(categoryId == '4')
									{
										<?php
											$sqlsize = "SELECT * FROM sizeformat WHERE id =100";
											$resultsize = mysql_query($sqlsize);
											$rowsize = mysql_fetch_array($resultsize);
										?>
										 document.getElementById('format1').value = "<?php echo $rowsize['format1']; ?>";
										 document.getElementById('format2').value = "<?php echo $rowsize['format2']; ?>";
										 document.getElementById('others').value = "可篩選消費品類、年收、職業…等";
									}
									else if(categoryId == '5')
									{
										<?php
											$sqlsize = "SELECT * FROM sizeformat WHERE id =101";
											$resultsize = mysql_query($sqlsize);
											$rowsize = mysql_fetch_array($resultsize);
										?>
										 document.getElementById('format1').value = "<?php echo $rowsize['format1']; ?>";
										 document.getElementById('format2').value = "<?php echo $rowsize['format2']; ?>";
										 document.getElementById('others').value = "可篩選消費品類、年收、職業…等";
									}
									else if(categoryId == '6')
									{
										<?php
											$sqlsize = "SELECT * FROM sizeformat WHERE id =102";
											$resultsize = mysql_query($sqlsize);
											$rowsize = mysql_fetch_array($resultsize);
										?>
										 document.getElementById('format1').value = "<?php echo $rowsize['format1']; ?>";
										 document.getElementById('format2').value = "<?php echo $rowsize['format2']; ?>";
										 document.getElementById('others').value = "";
									}
								};

							function compare(){
							 var f = document.getElementById('date1').value,
								e = document.getElementById('date2').value;
							 if(Date.parse(f.valueOf()) > Date.parse(e.valueOf()))
							 {
							  	alert('警告！到期日期不能小於起始日期');
								document.getElementById('date2').value=document.getElementById('date1').value;
								document.getElementById('days').value=1;
							 }
							 else
							 {
						     	var ff=new Date(f);
							 	var ee=new Date(e);
							 	var d=((ee-ff)/86400000)+1;
							 	document.getElementById('days').value=d;
							 }
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
							function number(){
							  var a = document.getElementById('price').value,
							 	b = document.getElementById('quantity').value;
							 var g = a*b;

							 document.getElementById('totalprice').value=Math.round(g);
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
							  <label class="control-label" for="position">版位(Position)</label>
							  <div class="controls">
								 <select id="position" name="position"></select>
							  </div>
							</div>
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
									<option value="F">F</option>
							    </select>
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label" for="date01">刊登期間1(Period)</label>
							  <div class="controls">
								<input type="text" class="datepicker" id="date1" name="date1" value="<?php if($row2['date1']!=0){echo date('m',$row2['date1']).'/'.date('d',$row2['date1']).'/'.date('Y',$row2['date1']);} ?>" style="width:100px">~<input type="text" name="date2" class="input-xlarge datepicker" id="date2" value="<?php if($row2['date2']!=0){echo date('m',$row2['date2']).'/'.date('d',$row2['date2']).'/'.date('Y',$row2['date2']);} ?>" style="width:100px" onChange="compare()"> 共<input class="input-xlarge" id="days1" name="days1" type="text" value="<?php echo $row2['days1']; ?>" style="width:30px" readonly>天
							  </div>
							</div>
                            <div class="control-group">
							  <label class="control-label" for="date02">刊登期間2(Period)</label>
							  <div class="controls">
								<input type="text" class="datepicker" id="date3" name="date3" value="<?php if($row2['date3']!=0){echo date('m',$row2['date3']).'/'.date('d',$row2['date3']).'/'.date('Y',$row2['date3']);} ?>" style="width:100px">~<input type="text" name="date4" class="input-xlarge datepicker" id="date4" value="<?php if($row2['date4']!=0){echo date('m',$row2['date4']).'/'.date('d',$row2['date4']).'/'.date('Y',$row2['date4']);} ?>" style="width:100px" onChange="compare2()">共<input class="input-xlarge" id="days2" name="days2" type="text" value="<?php echo $row2['days2']; ?>" style="width:30px" readonly>天
							  </div>
							</div>
                             <div class="control-group">
							  <label class="control-label" for="date03">刊登期間3(Period)</label>
							  <div class="controls">
								<input type="text" class="datepicker" id="date5" name="date5" value="<?php if($row2['date5']!=0){echo date('m',$row2['date5']).'/'.date('d',$row2['date5']).'/'.date('Y',$row2['date5']);} ?>" style="width:100px">~<input type="text" name="date6" class="input-xlarge datepicker" id="date6" value="<?php if($row2['date6']!=0){echo date('m',$row2['date6']).'/'.date('d',$row2['date6']).'/'.date('Y',$row2['date6']);} ?>" style="width:100px" onChange="compare3()">共<input class="input-xlarge" id="days3" name="days3" type="text" value="<?php echo $row2['days3']; ?>" style="width:30px" readonly>天
							  </div>
							</div>
                             <div class="control-group">
							  <label class="control-label" for="date04">刊登期間4(Period)</label>
							  <div class="controls">
								<input type="text" class="datepicker" id="date7" name="date7" value="<?php if($row2['date7']!=0){echo date('m',$row2['date7']).'/'.date('d',$row2['date7']).'/'.date('Y',$row2['date7']);} ?>" style="width:100px">~<input type="text" name="date8" class="input-xlarge datepicker" id="date8" value="<?php if($row2['date8']!=0){echo date('m',$row2['date8']).'/'.date('d',$row2['date8']).'/'.date('Y',$row2['date8']);} ?>" style="width:100px" onChange="compare4()">共<input class="input-xlarge" id="days4" name="days4" type="text" value="<?php echo $row2['days4']; ?>" style="width:30px" readonly>天
							  </div>
							</div>
                             <div class="control-group">
							  <label class="control-label" for="date05">刊登期間5(Period)</label>
							  <div class="controls">
								<input type="text" class="datepicker" id="date9" name="date9" value="<?php if($row2['date9']!=0){echo date('m',$row2['date9']).'/'.date('d',$row2['date9']).'/'.date('Y',$row2['date9']);} ?>" style="width:100px">~<input type="text" name="date10" class="input-xlarge datepicker" id="date10s" value="<?php if($row2['date10']!=0){echo date('m',$row2['date10']).'/'.date('d',$row2['date10']).'/'.date('Y',$row2['date10']);} ?>" style="width:100px" onChange="compare5()">共<input class="input-xlarge" id="days5" name="days5" type="text" value="<?php echo $row2['days5']; ?>" style="width:30px" readonly>天
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
								  <input class="datepicker" id="due" name="due" type="text" value="<?php echo $row2['due']; ?>" style="width:100px">
      							</div>
							  </div>
                             <div class="control-group">
							  <label class="control-label" for="quantity">發送封數</label>
							  <div class="controls">
								<input class="input-xlarge" id="quantity" name="quantity" type="text" value="<?php echo $row2['quantity']; ?>" style="width:100px"  onChange="number()" required>
							  </div>
							</div>
                            <?php if($_GET['cue']==2){ ?>
                           <div class="control-group">
							  <label class="control-label" for="profit">修改利潤%</label>
							  <div class="controls">
								<input class="input-xlarge" id="profit" name="profit" type="text" value="<?php echo $profit; ?>" style="width:100px" onChange="number()">
							  </div>
							</div>
                            <?php } ?>
                            <div class="control-group">
							  <label class="control-label" for="price">單價(Net Cost NTD)</label>
							  <div class="controls">
								$<input class="input-xlarge" id="price" name="price" type="text" value="<?php echo $row2['price']; ?>" onChange="number()" style="width:100px" >元
							  </div>
							</div>
                            <div class="control-group">
							  <label class="control-label" for="totalprice">售價(Totalprice)</label>
							  <div class="controls">
								$<input class="input-xlarge" id="totalprice" name="totalprice" type="text" value="<?php echo $row2['totalprice']; ?>" style="width:100px" >元
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
                            <?php if($_GET['cue']==1){ ?>
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
