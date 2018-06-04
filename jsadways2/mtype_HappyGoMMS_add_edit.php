<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
                        <script type="text/javascript" src="js/jquery.xml2json.js"></script>
						<script>
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
										//ChangeSelectType();
									});
									//Abow end

									//GetSceneryJsonData();
									//GetHotelJsonData();
								}
						</script>
						<fieldset>
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
								<label class="control-label">網站(Website)</label>
								<div class="controls">
								  <input class="input-xlarge" id="website" name="website" type="text" value="HappyGo MMS" readonly>
								</div>
							  </div>
                          	<div class="control-group">
								<label class="control-label">頻道(Channel)</label>
								<div class="controls">
								  <input class="input-xlarge" id="channel" name="channel" type="text" value="MMS" readonly>
								</div>
							  </div>
                            <script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
                            <script type="text/javascript" src="js/jquery.xml2json.js"></script>
							<script type="text/javascript" src="js/JSLINQ.js"></script>

							<div class="control-group">
							  <label class="control-label" for="position">版位(Position)</label>
							  <div class="controls">
								 <input class="input-xlarge" id="position" name="position" type="text" value="最低購買量10,000封" readonly>
							  </div>
							</div>
							  <div class="control-group">
								<label class="control-label">規格(Size)</label>
								<div class="controls">
                                  <textarea id="format1" name="format1" readonly>JPG/TEXT</textarea>
								</div>
							  </div>

							  <div class="control-group">
								<label class="control-label">格式(Format)</label>
								<div class="controls">
                                <textarea id="format2" name="format2" readonly>圖片尺寸 : 480*580   480*480  480*380  (選擇一種)
標題 : 14字內,
圖檔 : jpg檔
文字內容100字內
以上圖+文字檔案大小 加總起來是50K以內</textarea>
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
								<input class="input-xlarge" id="days" name="days" type="text" value="" style="width:100px">天
							  </div>
							</div>

							<div class="control-group">
								<label class="control-label">素材提供期限(Material Due)</label>
								<div class="controls">
								  <input class="datepicker" id="due" name="due" type="text" value="上線日前3天" style="width:100px">
	  							</div>
							</div>
                            <div class="control-group">
							  <label class="control-label" for="quantity">發送封數</label>
							  <div class="controls">
								<input class="input-xlarge" id="quantity" name="quantity" type="text" value="" style="width:100px"  required>最低購買量 10,000封
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
								$<input class="input-xlarge" id="price" name="price" type="text" value=""  onChange="number()" style="width:100px" >元(單封價格為 2.3元台幣)
							  </div>
							</div>
                            <div class="control-group">
							  <label class="control-label" for="totalprice">售價(Totalprice)</label>
							  <div class="controls">
								$<input class="input-xlarge" id="totalprice" name="totalprice" type="text" value="" style="width:100px" >元
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
							<?php if($_GET['cue']==1){ ?>
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