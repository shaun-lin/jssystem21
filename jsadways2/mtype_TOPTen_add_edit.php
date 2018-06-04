<fieldset>

                          	<div class="control-group">
								<label class="control-label">項目(Website)</label>
								<div class="controls">
								  <input class="input-xlarge" id="website" name="website" type="text" value="預約TOP10" readonly>
								</div>
							  </div>
							  <div class="control-group">
								<label class="control-label">執行內容(Action)</label>
								<div class="controls">
								  <input class="input-xlarge" id="actions" name="actions" type="text" value="CPR(RESERVATION)" readonly>
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
                            		//Abow Start
									<?php 
										include('campaign_required_select.php');
										echo $Select_str;
									?>

									$('#SelectType').change(function(){
										ChangeSelectType();
									});
									//Abow end
							}

							//Abow function
								function ChangeSelectType(){
									//變動手機系統投放
									$('#phonesystem').empty().append($('<option></option>').val('').text('------'));
									//$('#SelectSubCategory').empty().append($('<option></option>').val('').text('------'));
									//$('#SelectViewpoint').empty().append($('<option></option>').val('').text('------'));
									var categoryId = $.trim($('#SelectType option:selected').val());


									if(categoryId == '1' || categoryId == '2')
									{

										var jsonData =
										[
											{
												"TypeItemId": "1",
												"TypeItemName": "iOS"
											},
											{
												"TypeItemId": "2",
												"TypeItemName": "Android"
											}/*,
											{
												"TypeItemId": "4",
												"TypeItemName": "單下iPad"
											}*/
										];
									}
									else if(categoryId == '3')
									{
										var jsonData  =
										[
											{
												"TypeItemId": "1",
												"TypeItemName": "iOS"
											},
											{
												"TypeItemId": "2",
												"TypeItemName": "Android"
											}/*,
											{
												"TypeItemId": "3",
												"TypeItemName": "iOS/Android全投放"
											},
											{
												"TypeItemId": "4",
												"TypeItemName": "單下iPad"
											}*/
										];
									}
									if(categoryId.length != 0)
									{
										$.each(jsonData , function (i, item){
											$('#phonesystem').append($('<option></option>').val(item.TypeItemId).text(item.TypeItemName));
										});

									}
								}
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
							 document.getElementById('click1').value=Math.round(document.getElementById('totalprice1').value/document.getElementById('price1').value);
							 <?php } ?>
							 <?php if($_GET['cue']==2){ ?>
							 document.getElementById('price1').value=document.getElementById('totalprice1').value/document.getElementById('click1').value;
							 <?php } ?>
							 change();
							}
							function change2(){
							<?php if($_GET['cue']==1){ ?>
							 document.getElementById('click2').value=Math.round(document.getElementById('totalprice2').value/document.getElementById('price2').value);
							 <?php } ?>
							 change();
							}
							function change3(){
							<?php if($_GET['cue']==1){ ?>
							 document.getElementById('click3').value=Math.round(document.getElementById('totalprice3').value/document.getElementById('price3').value);
							 <?php } ?>
							 change();
							}
							function change4(){
							<?php if($_GET['cue']==1){ ?>
							 document.getElementById('click4').value=Math.round(document.getElementById('totalprice4').value/document.getElementById('price4').value);
							 <?php } ?>
							 change();
							}
							function change5(){
							<?php if($_GET['cue']==1){ ?>
							 document.getElementById('click5').value=Math.round(document.getElementById('totalprice5').value/document.getElementById('price5').value);
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
							  <label class="control-label" for="phonesystem">手機系統(Mobile system)</label>
							  <div class="controls">
								 <select id="phonesystem" name="phonesystem">
								 	<!--
								 	<option value="iOS">iOS</option>
								 	<option value="Android">Android</option>
								 	-->
							    </select>
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label" for="position">版位(Position)</label>
							  <div class="controls">
								 <input class="input-xlarge" id="position" name="position" type="text" value="預約任務牆" style="width:100px" readonly>
							  </div>
							</div>
							  <div class="control-group">
								<label class="control-label">規格(Size)</label>
								<div class="controls">
                                  <textarea id="format1" name="format1" readonly>*請與負責業務窗口詢問上稿需知。
請下載以下EXCEL規格表後填寫推廣資訊：
https://docs.google.com/a/js-adways.com.tw/spreadsheets/d/11rjwqWHhJBiKdOwYMqP_Y6Xpmcysc8w1dCyI_4l77vs/edit#gid=0</textarea>
								</div>
							  </div>
							  <div class="control-group">
								<label class="control-label">格式(Format)</label>
								<div class="controls">
                                <textarea id="format2" name="format2" readonly>文字/JPG/GIF</textarea>
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
								<input type="text" class="datepicker" id="date1" name="date1" value="" style="width:100px">~<input type="text" name="date2" class="input-xlarge datepicker" id="date2" value="" style="width:100px" onChange="compare()"> 共<input class="input-xlarge" id="days1" name="days1" type="text" value="0" style="width:30px" readonly>天 定價<input class="input-xlarge" id="price1" name="price1" type="text" value="20" style="width:30px" <?php if($_GET['cue']==2){echo 'readonly';} ?> >總價<input class="input-xlarge" id="totalprice1" name="totalprice1" type="text" value="" onChange="change1()" style="width:70px">數量<input class="input-xlarge" id="click1" name="click1" type="text" onChange="change11()" value="" style="width:70px">
							  </div>
							</div>
                            <div class="control-group">
							  <label class="control-label" for="date02">刊登期間2(Period)</label>
							  <div class="controls">
								<input type="text" class="datepicker" id="date3" name="date3" value="" style="width:100px">~<input type="text" name="date4" class="input-xlarge datepicker" id="date4" value="" style="width:100px" onChange="compare2()">共<input class="input-xlarge" id="days2" name="days2" type="text" value="0" style="width:30px" readonly>天 定價<input class="input-xlarge" id="price2" name="price2" type="text" value="" style="width:30px" <?php if($_GET['cue']==2){echo 'readonly';} ?>>總價<input class="input-xlarge" id="totalprice2" name="totalprice2" type="text" value="" onChange="change2()" style="width:70px">數量<input class="input-xlarge" id="click2" name="click2" type="text" value="" style="width:70px" onChange="change22()">
							  </div>
							</div>
                             <div class="control-group">
							  <label class="control-label" for="date03">刊登期間3(Period)</label>
							  <div class="controls">
								<input type="text" class="datepicker" id="date5" name="date5" value="" style="width:100px">~<input type="text" name="date6" class="input-xlarge datepicker" id="date6" value="" style="width:100px" onChange="compare3()">共<input class="input-xlarge" id="days3" name="days3" type="text" value="0" style="width:30px" readonly>天 定價<input class="input-xlarge" id="price3" name="price3" type="text" value="" style="width:30px" <?php if($_GET['cue']==2){echo 'readonly';} ?>>總價<input class="input-xlarge" id="totalprice3" name="totalprice3" type="text" value="" onChange="change3()" style="width:70px">數量<input class="input-xlarge" id="click3" name="click3" type="text" onChange="change33()" value="" style="width:70px">
							  </div>
							</div>
                             <div class="control-group">
							  <label class="control-label" for="date04">刊登期間4(Period)</label>
							  <div class="controls">
								<input type="text" class="datepicker" id="date7" name="date7" value="" style="width:100px">~<input type="text" name="date8" class="input-xlarge datepicker" id="date8" value="" style="width:100px" onChange="compare4()">共<input class="input-xlarge" id="days4" name="days4" type="text" value="0" style="width:30px" readonly>天 定價<input class="input-xlarge" id="price4" name="price4" type="text" value="" style="width:30px" <?php if($_GET['cue']==2){echo 'readonly';} ?>>總價<input class="input-xlarge" id="totalprice4" name="totalprice4" type="text" value="" onChange="change4()" style="width:70px">數量<input class="input-xlarge" id="click4" name="click4" type="text" onChange="change44()" value="" style="width:70px">
							  </div>
							</div>
                             <div class="control-group">
							  <label class="control-label" for="date05">刊登期間5(Period)</label>
							  <div class="controls">
								<input type="text" class="datepicker" id="date9" name="date9" value="" style="width:100px">~<input type="text" name="date10" class="input-xlarge datepicker" id="date10" value="" style="width:100px" onChange="compare5()">共<input class="input-xlarge" id="days5" name="days5" type="text" value="0" style="width:30px" readonly>天 定價<input class="input-xlarge" id="price5" name="price5" type="text" value="" style="width:30px" <?php if($_GET['cue']==2){echo 'readonly';} ?>>總價<input class="input-xlarge" id="totalprice5" name="totalprice5" type="text" value="" onChange="change5()" style="width:70px">數量<input class="input-xlarge" id="click5" name="click5" type="text" onChange="change55()" value="" style="width:70px">
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label" for="days">天數(Days)</label>
							  <div class="controls">
								<input class="input-xlarge" id="days" name="days" type="text" value="" style="width:100px">天
							  </div>
							</div>

							<div class="control-group">
								<label class="control-label">素材提供期限(Material Due)</label>
								<div class="controls">
								  <input id="due"  class="datepicker"  name="due" type="text" value="上線日前3天"  style="width:100px">
      							</div>
							  </div>
                           <?php if($_GET['cue']==2){ ?>
                           <div class="control-group">
							  <label class="control-label" for="profit">修改預設最低利潤%</label>
							  <div class="controls">
								<input class="input-xlarge" id="profit" name="profit" type="text" value="<?php echo $profit; ?>" style="width:100px" onChange="change()">
							  </div>
							</div>
                            <?php } ?>
                            <div class="control-group">
							  <label class="control-label" for="totalprice">總投放金額(Total)</label>
							  <div class="controls">
								$<input class="input-xlarge" id="totalprice" name="totalprice" type="text" value="" style="width:100px"  onChange="changechange()" required>元
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
							  <label class="control-label" for="a3">該媒體最低利潤<?php echo $profit; ?>%</label>
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
							  <label class="control-label" for="number1">數量(Est. Clicks)</label>
							  <div class="controls">
								<input class="input-xlarge" id="number1" name="number1" type="text" value="" style="width:100px" readonly>
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label" for="others">備註</label>
							  <div class="controls">
                                <textarea id="others" name="others" ></textarea>
							  </div>
							</div>

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
