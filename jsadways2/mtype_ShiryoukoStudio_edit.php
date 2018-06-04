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
				$sql2 = "SELECT * FROM media164 WHERE id= ".$_GET['id'];
				$result2 = mysql_query($sql2);
				$row2 = mysql_fetch_array($result2);
			?>

            
			<div class="row-fluid sortable">
				<div class="box span12">
					<div class="box-header well" data-original-title>
						<h2><i class="icon-edit"></i> <?php echo $row1['name']; ?>-編輯媒體-【實況主】詩涼子SHIRYOUKO STUDIO</h2>

					</div>
					<div class="box-content">
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
					include('campaign_required_select_edit.php');
					echo $Select_str;
				?>

				$('#SelectType').change(function(){
					//ChangeSelectType();
				});

			}
			</script>	
						<form class="form-horizontal" action="mtype_ShiryoukoStudio_edit2.php?campaign=<?php echo $_GET['campaign']; ?>&id=<?php echo $_GET['id']; ?>&cue=<?php echo $_GET['cue']; ?>&media=<?php echo $_GET['media']; ?>" method="post">
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
								<input type="text" class="datepicker" id="date1" name="date1" value="<?php if($row2['date1']!=0){echo date('m',$row2['date1']).'/'.date('d',$row2['date1']).'/'.date('Y',$row2['date1']);} ?>" style="width:100px">
							  </div>
							</div>
                            <div class="control-group">
							  <label class="control-label" for="date01">日期</label>
							  <div class="controls">
								<input type="text" class="datepicker" id="date2" name="date2" value="<?php if($row2['date3']!=0){echo date('m',$row2['date3']).'/'.date('d',$row2['date3']).'/'.date('Y',$row2['date3']);} ?>" style="width:100px">
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label" for="date01">日期</label>
							  <div class="controls">
								<input type="text" class="datepicker" id="date3" name="date3" value="<?php if($row2['date5']!=0){echo date('m',$row2['date5']).'/'.date('d',$row2['date5']).'/'.date('Y',$row2['date5']);} ?>" style="width:100px">
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label" for="date01">日期</label>
							  <div class="controls">
								<input type="text" class="datepicker" id="date4" name="date4" value="<?php if($row2['date7']!=0){echo date('m',$row2['date7']).'/'.date('d',$row2['date7']).'/'.date('Y',$row2['date7']);} ?>" style="width:100px">
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label" for="date01">日期</label>
							  <div class="controls">
								<input type="text" class="datepicker" id="date5" name="date5" value="<?php if($row2['date9']!=0){echo date('m',$row2['date9']).'/'.date('d',$row2['date9']).'/'.date('Y',$row2['date9']);} ?>" style="width:100px">
							  </div>
							</div>
                            <div class="control-group">
							  <label class="control-label" for="totalprice">總價</label>
							  <div class="controls">
								$<input class="input-xlarge" id="totalprice" name="totalprice" type="text" value="<?php echo $row2['totalprice']; ?>" style="width:100px"   required>元
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
