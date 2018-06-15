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
			
			<div class="row-fluid sortable">
				<div class="box span12">
					<div class="box-header well" data-original-title>
						<h2><i class="icon-edit"></i> <?php echo $row1['name']; ?>-編輯媒體-廣告素材製作</h2>

					</div>
					<div class="box-content">
						<?php
							$sql1 = "SELECT * FROM campaign WHERE id= ".$_GET['campaign'];
							$result1 = mysql_query($sql1);
							$row1 = mysql_fetch_array($result1);
							$sql2 = "SELECT * FROM media161 WHERE id= ".$_GET['id'];
							$result2 = mysql_query($sql2);
							$row2 = mysql_fetch_array($result2);
						?>
						<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
			            <script type="text/javascript" src="js/jquery.xml2json.js"></script>
						<script type="text/javascript">
						//Abow Start
								$(document).ready(function()
								{
									Page_Init();
								});

								function Page_Init()
								{
									<?php 
										include('campaign_required_select_edit.php');
										echo $Select_str;
									?>

									$('#SelectType').change(function(){
										//ChangeSelectType();
									});
									//Abow end
								}
						</script>
						<form class="form-horizontal" action="mtype_Creative_edit2.php?campaign=<?php echo $_GET['campaign']; ?>&id=<?php echo $_GET['id']; ?>" method="post">
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

                          	<div class="control-group">
								<label class="control-label">項目</label>
								<div class="controls">
								<select name="itemname" id="itemname">
                                	<option value="網路活動" <?php if($row2['itemname']=='網路活動'){echo 'selected';} ?>>網路活動</option>
                                    <option value="廣告素材(flash)" <?php if($row2['itemname']=='廣告素材(flash)'){echo 'selected';} ?>>廣告素材(flash)</option>
                                    <option value="廣告素材(jpg)" <?php if($row2['itemname']=='廣告素材(jpg)'){echo 'selected';} ?>>廣告素材(jpg)</option>
                                    <option value="廣告素材RESIZE(flash)" <?php if($row2['itemname']=='廣告素材RESIZE(flash)'){echo 'selected';} ?>>廣告素材RESIZE(flash)</option>
                                    <option value="廣告素材RESIZE(jpg)" <?php if($row2['itemname']=='廣告素材RESIZE(jpg)'){echo 'selected';} ?>>廣告素材RESIZE(jpg)</option>
                                    <option value="廣告文案" <?php if($row2['itemname']=='廣告文案'){echo 'selected';} ?>>廣告文案</option>
                                    <option value="Party使用費" <?php if($row2['itemname']=='Party使用費'){echo 'selected';} ?>>Party使用費</option>
                                    <option value="FG市調大隊" <?php if($row2['itemname']=='FG市調大隊'){echo 'selected';} ?>>FG市調大隊</option>
                                    <option value="記者會" <?php if($row2['itemname']=='記者會'){echo 'selected';} ?>>記者會</option>
                                </select>
                                </div>
							  </div>
							<div class="control-group">
							  <label class="control-label" for="SelectCategory">unit cost</label>
							  <div class="controls">
								$<input class="input-xlarge" id="price" name="price" type="text" value="<?php echo $row2['price']; ?>" style="width:100px">元
							  </div>
							</div>

							<div class="control-group">
							  <label class="control-label" for="SelectSubCategory">unit</label>
							  <div class="controls">
								<input class="input-xlarge" id="quantity" name="quantity" type="text" value="<?php echo $row2['quantity']; ?>" style="width:100px">
							  </div>
							</div>
							  <div class="control-group">
								<label class="control-label">total</label>
								<div class="controls">
								$<?php echo number_format($row2['totalprice']); ?>元
								</div>
							  </div>
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
								<label class="control-label">備註</label>
								<div class="controls">
                                <textarea id="other" name="other"><?php echo $row2['other']; ?></textarea>
								</div>
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
	</div><!--/.fluid-container-->

	<?php include("public/js.php"); ?>

</body>
</html>
