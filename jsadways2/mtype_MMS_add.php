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
				<div class="box span7">
					<div class="box-header well" data-original-title>
						<h2><i class="icon-edit"></i> <?php echo $row1['name']; ?>-新增媒體-手機簡訊</h2>

					</div>
					<div class="box-content">
						<form name="myForm" class="form-horizontal" action="mtype_MMS_add2.php?id=<?php echo $_GET['id']; ?>&cue=<?php echo $_GET['cue']; ?>&media=<?php echo $_GET['media']; ?>&media2=<?php echo $_GET['media2']; ?>&mediaid=<?php echo $_GET['mediaid']; ?>" method="post">
							<?php require "mtype_MMS_add_edit.php"; ?>
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
