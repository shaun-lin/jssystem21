
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
					//ChangeSelectType();
				});
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
							  <label class="control-label" for="date01">日期</label>
							  <div class="controls">
								<input type="text" class="datepicker" id="date1" name="date1" value="" style="width:100px">
							  </div>
							</div>
                            <div class="control-group">
							  <label class="control-label" for="date01">日期</label>
							  <div class="controls">
								<input type="text" class="datepicker" id="date2" name="date2" value="" style="width:100px">
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label" for="date01">日期</label>
							  <div class="controls">
								<input type="text" class="datepicker" id="date3" name="date3" value="" style="width:100px">
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label" for="date01">日期</label>
							  <div class="controls">
								<input type="text" class="datepicker" id="date4" name="date4" value="" style="width:100px">
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label" for="date01">日期</label>
							  <div class="controls">
								<input type="text" class="datepicker" id="date5" name="date5" value="" style="width:100px">
							  </div>
							</div>
                            <div class="control-group">
							  <label class="control-label" for="totalprice">總價</label>
							  <div class="controls">
								$<input class="input-xlarge" id="totalprice" name="totalprice" type="text" value="" style="width:100px" required>元
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