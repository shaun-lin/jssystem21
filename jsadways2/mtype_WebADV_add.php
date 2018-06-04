<?php
	session_start();
	include('include/db.inc.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>新增媒體</title>
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
				$sql1 = "SELECT * FROM campaign WHERE id= ".$_GET['id'];
				$result1 = mysql_query($sql1);
				$row1 = mysql_fetch_array($result1);
			?>
			<div class="row-fluid sortable">
				<div class="box span12">
					<div class="box-header well" data-original-title>
						<h2><i class="icon-edit"></i> <?php echo $row1['name']; ?>-新增媒體-媽媽經</h2>

					</div>
					<div class="box-content">
						<form class="form-horizontal" action="mtype_WebADV_add2.php?id=<?php echo $_GET['id']; ?>&cue=<?php echo $_GET['cue']; ?>&media=<?php echo $_GET['media']; ?>&media2=<?php echo $_GET['media2']; ?>&mediaid=<?php echo $_GET['mediaid']; ?>" method="post">
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
											"categoryName": "焦點大看板"
										},
										{
											"categoryId": "2",
											"categoryName": "華麗看板"
										}
									];

									$('#SelectCategory').empty().append($('<option></option>').val('').text('------'));

									$.each(jsonData, function (i, item)
									{
										$('#SelectCategory').append($('<option></option>').val(item.categoryId).text(item.categoryName));
									});


									$('#SelectCategory').change(function(){
										ChangeCategory();
									});

									//Abow Start
									<?php 
										include('campaign_required_select.php');
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


									var categoryId = $.trim($('#SelectCategory option:selected').val());

									var jsonData = [];

									if(categoryId == '1')
									{
										<?php
											$sqlsize = "SELECT * FROM sizeformat WHERE id =168";
											$resultsize = mysql_query($sqlsize);
											$rowsize = mysql_fetch_array($resultsize);
										?>
										 document.getElementById('channel').value ="文章內容中段";
										 document.getElementById('format1').value = "<?php echo $rowsize['format1']; ?>";
										 document.getElementById('format2').value = "<?php echo $rowsize['format2']; ?>";
										 document.getElementById('wheel').value ="3R";
										 document.getElementById('num2').value ="400000";
										 document.getElementById('ctr').value ="0";
										 document.getElementById('num1').value ="12000";
										 document.getElementById('num3').value ="1";
										 document.getElementById('position').value ="焦點大看板";
									}
									else if(categoryId == '2')
									{
										<?php
											$sqlsize = "SELECT * FROM sizeformat WHERE id =169";
											$resultsize = mysql_query($sqlsize);
											$rowsize = mysql_fetch_array($resultsize);
										?>
										 document.getElementById('channel').value ="文章分享下方";
										 document.getElementById('format1').value = "<?php echo $rowsize['format1']; ?>";
										 document.getElementById('format2').value = "<?php echo $rowsize['format2']; ?>";
										 document.getElementById('wheel').value ="3R";
										 document.getElementById('num2').value ="400000";
										 document.getElementById('ctr').value ="0";
										 document.getElementById('num1').value ="12000";
										 document.getElementById('num3').value ="1";
										 document.getElementById('position').value ="華麗看板";
									}

									if(categoryId.length != 0)
									{
										$.each(jsonData , function(i, item){
											$('#SelectSubCategory').append($('<option></option>').val(item.subCategoryId).text(item.subCategoryName));
										});

									}
								}
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
							 number();
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
							 number();
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
							 number();
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
							 number();
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
							 number();
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
							 var a = document.getElementById('ctr').value,
								b = document.getElementById('num1').value,
								c = document.getElementById('num2').value,
								d = document.getElementById('days').value,
								e = document.getElementById('num3').value;
							 var g = (a*c*d)/100;

							 //document.getElementById('number1').value=Math.round(g/e);
							 //document.getElementById('number2').value=Math.round(c*d/e);
							 //document.getElementById('totalprice').value=Math.round(b*d/e);
							 <?php if($_GET['cue']==2){ ?>
							 //document.getElementById('a1').value=Math.round((Number(document.getElementById('totalprice').value)*<?php echo $commission1; ?>)/100);
							 //document.getElementById('a2').value=Math.round((Number(document.getElementById('totalprice').value)*<?php echo $commission4; ?>)/100);
							 //document.getElementById('a3').value=Math.round((Number(document.getElementById('totalprice').value)*Number(document.getElementById('profit').value))/100);
							 //document.getElementById('a4').value=Math.round(document.getElementById('totalprice').value-document.getElementById('a1').value-document.getElementById('a2').value-document.getElementById('a3').value);
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
							  <label class="control-label" for="SelectCategory">版位(Position)</label>
							  <div class="controls">
								 <select id="SelectCategory" name="SelectCategory">
							    </select>
							  </div>
							</div>
							<div class="control-group">
								<label class="control-label">頻道(Channel)</label>
								<div class="controls">
								  <input class="input-xlarge" id="channel" name="channel" type="text" style="width:500px" readonly>
								</div>
							  </div>

							  <div class="control-group">
								<label class="control-label">規格(Size)</label>
								<div class="controls">
                                  <textarea id="format1" name="format1" readonly></textarea>
								</div>
							  </div>

							  <div class="control-group">
								<label class="control-label">格式(Format)</label>
								<div class="controls">
                                <textarea id="format2" name="format2" readonly></textarea>
								</div>
							  </div>

							  <div class="control-group">
							  <label class="control-label" for="wheel">輪替/固定(R/F)</label>
							  <div class="controls">
								 <input class="input-xlarge" id="wheel" name="wheel" type="text" readonly>
							  </div>
							</div>
                            <div class="control-group">
							  <label class="control-label" for="number2">預估曝光數(Est. Impressions)</label>
							  <div class="controls">
								<input class="input-xlarge" id="number2" name="number2" type="text" value="" style="width:100px" >
							  </div>
							</div>
                            <div class="control-group">
							  <label class="control-label" for="typeahead">預估點擊率(Est. CTR)</label>
							  <div class="controls">
								<input class="input-xlarge" id="ctr" name="ctr" type="text"  value="" style="width:100px" >%
							  </div>
							</div>
							 <div class="control-group">
							  <label class="control-label" for="number1">預估點擊數(Est. Clicks)</label>
							  <div class="controls">
								<input class="input-xlarge" id="number1" name="number1" type="text" value="" style="width:100px" >
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label" for="date01">刊登期間1(Period)</label>
							  <div class="controls">
								<input type="text" class="datepicker" id="date1" name="date1" value="" style="width:100px">~<input type="text" name="date2" class="input-xlarge datepicker" id="date2" value="" style="width:100px" onChange="compare()"> 共<input class="input-xlarge" id="days1" name="days1" type="text" value="0" style="width:30px" readonly>天
							  </div>
							</div>
                            <div class="control-group">
							  <label class="control-label" for="date02">刊登期間2(Period)</label>
							  <div class="controls">
								<input type="text" class="datepicker" id="date3" name="date3" value="" style="width:100px">~<input type="text" name="date4" class="input-xlarge datepicker" id="date4" value="" style="width:100px" onChange="compare2()">共<input class="input-xlarge" id="days2" name="days2" type="text" value="0" style="width:30px" readonly>天
							  </div>
							</div>
                             <div class="control-group">
							  <label class="control-label" for="date03">刊登期間3(Period)</label>
							  <div class="controls">
								<input type="text" class="datepicker" id="date5" name="date5" value="" style="width:100px">~<input type="text" name="date6" class="input-xlarge datepicker" id="date6" value="" style="width:100px" onChange="compare3()">共<input class="input-xlarge" id="days3" name="days3" type="text" value="0" style="width:30px" readonly>天
							  </div>
							</div>
                             <div class="control-group">
							  <label class="control-label" for="date04">刊登期間4(Period)</label>
							  <div class="controls">
								<input type="text" class="datepicker" id="date7" name="date7" value="" style="width:100px">~<input type="text" name="date8" class="input-xlarge datepicker" id="date8" value="" style="width:100px" onChange="compare4()">共<input class="input-xlarge" id="days4" name="days4" type="text" value="0" style="width:30px" readonly>天
							  </div>
							</div>
                             <div class="control-group">
							  <label class="control-label" for="date05">刊登期間5(Period)</label>
							  <div class="controls">
								<input type="text" class="datepicker" id="date9" name="date9" value="" style="width:100px">~<input type="text" name="date10" class="input-xlarge datepicker" id="date10s" value="" style="width:100px" onChange="compare5()">共<input class="input-xlarge" id="days5" name="days5" type="text" value="0" style="width:30px" readonly>天
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label" for="days">天數(Days)</label>
							  <div class="controls">
								<input class="input-xlarge" id="days" name="days" type="text" value="" style="width:100px"  onChange="number()" >天
							  </div>
							</div>

							<div class="control-group">
								<label class="control-label">素材提供期限(Material Due)</label>
								<div class="controls">
								  <input class="datepicker" id="due" name="due" type="text" value="上線日前5天" style="width:100px">
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
							  <label class="control-label" for="totalprice">定價(List Price NTD)</label>
							  <div class="controls">
								$<input class="input-xlarge" id="totalprice" name="totalprice" type="text" value=""  style="width:100px" >元
							  </div>
							</div>
                            <?php if($_GET['cue']==2){ ?>
                            <div class="control-group">
							  <label class="control-label" for="a1">佣金<?php echo $commission1; ?>%</label>
							  <div class="controls">
								<input class="input-xlarge" id="a1" name="a1" type="text" value="" style="width:100px" readonly>
							  </div>
							</div>
                            <div class="control-group">
							  <label class="control-label" for="a2">現折<?php echo $commission4; ?>%</label>
							  <div class="controls">
								<input class="input-xlarge" id="a2" name="a2" type="text" value="" style="width:100px" readonly>
							  </div>
							</div>
                            <div class="control-group">
							  <label class="control-label" for="a3">利潤</label>
							  <div class="controls">
								<input class="input-xlarge" id="a3" name="a3" type="text" value="" style="width:100px" readonly>
							  </div>
							</div>
                            <div class="control-group">
							  <label class="control-label" for="a4">行政發媒體金額</label>
							  <div class="controls">
								<input class="input-xlarge" id="a4" name="a4" type="text" value="" style="width:100px" readonly>
							  </div>
							</div>
                            <?php } ?>
                            <div class="control-group">
							  <label class="control-label" for="others">備註</label>
							  <div class="controls">
								<textarea id="others" name="others" ></textarea>
							  </div>
							</div>
                            <input name="position" id="position" type="hidden" value="">
                            <input name="num1" id="num1" type="hidden" value="">
                            <input name="num2" id="num2" type="hidden" value="">
                            <input name="num3" id="num3" type="hidden" value="">
							<?php if($_GET['cue']==1){ ?>
                            <div class="control-group">
							  <label class="control-label" for="pr">是否為PR</label>
							  <div class="controls">
								<input type="checkbox" name="pr" value="1">勾選後此筆記錄將會是PR案件，總金額為0
							  </div>
							</div>
                            <div class="control-group">
							  <label class="control-label" for="samecue">是否同時產生對內媒體</label>
							  <div class="controls">
								<input type="checkbox" name="samecue" value="1" checked>勾選後即會在對內cue表產生同筆媒體
							  </div>
							</div>
							<?php }?>
							<div class="form-actions">
							  <button type="submit" class="btn btn-primary">新增媒體</button>
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
