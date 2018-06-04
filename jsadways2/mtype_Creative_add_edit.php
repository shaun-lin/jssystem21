 <script src="js/jquery.js"></script>
					<script>
					var a=0;
                       $(document).ready(function(){

						  $("#add_button").click
						  (
							function()
							{
							  $("#add_file_button").append('<div><select name="member[]" id="member[]" onChange="renew();"><option >網路活動<option>廣告素材(flash)<option >廣告素材(jpg)<option >廣告素材RESIZE(flash)<option >廣告素材RESIZE(jpg)<option >廣告文案<option >Party使用費<option >FG市調大隊<option >記者會</select></select>&nbsp;unit cost：<input type="text" name="unitcost[]" id="unitcost[]" style="width:100px" onChange="renew();">&nbsp;unit：<input type="text" name="unit[]" value="1"  style="width:50px" onChange="renew();">&nbsp;total：<input type="text" name="total[]" value=""  style="width:50px" readonly>&nbsp;備註：<input type="text" name="other[]" value=""  style="width:200px"></div>');
							  a=a+1;
							}
						  );
						  $("a[id='del_file[]']").click(function(){
							  if (confirm('確定刪除檔案')) {
								return true;
							  }
							  return false;
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
						});
						function renew(){
							var obj1 = document.getElementsByName("member[]");
							var obj2 = document.getElementsByName("unitcost[]");
							var obj3 = document.getElementsByName("unit[]");
							var obj4 = document.getElementsByName("total[]");
							for (i=0; i<=a; i=i+1)
							{
								/*if(obj1[i].selectedIndex=='1')
								{
									obj2[i].value = "3375";
								}
								else if(obj1[i].selectedIndex=='2')
								{
									obj2[i].value = "1620";
								}
								else if(obj1[i].selectedIndex=='3')
								{
									obj2[i].value = "1350";
								}
								else if(obj1[i].selectedIndex=='4')
								{
									obj2[i].value = "1080";
								}*/
								obj4[i].value=obj2[i].value*obj3[i].value;

							}
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
						  <fieldset>
                        <input type="button" id="add_button" name="add_button" value="增加項目">
                             <div id="add_file_button"></div>
  							<?php if($_GET['cue']==1){ ?>
                            <div class="control-group">
							  <label class="control-label" for="samecue">是否同時產生對內媒體</label>
							  <div class="controls">
								<input type="checkbox" name="samecue" value="1" checked>勾選後即會在對內cue表產生同筆媒體
							  </div>
							</div>
							<?php }?>
                            <div class="control-group">
							  <div class="controls">
								廣告素材(flash)=>3375、廣告素材(jpg)=>1620、廣告素材RESIZE(flash)=>1350、廣告素材RESIZE(jpg)=>1080
							  </div>
							</div>
							<div class="form-actions">
							  <button type="submit" class="btn btn-primary">新增媒體</button>
							</div>
						  </fieldset>