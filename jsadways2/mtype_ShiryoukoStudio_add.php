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

			<div class="row-fluid sortable">
				<div class="box span12">
					<div class="box-header well" data-original-title>
						<h2><i class="icon-edit"></i> <?php echo $row1['name']; ?>-新增媒體-【實況主】詩涼子SHIRYOUKO STUDIO</h2>

					</div>
					<div class="box-content">
						<form class="form-horizontal" action="mtype_ShiryoukoStudio_add2.php?id=<?php echo $_GET['id']; ?>&cue=<?php echo $_GET['cue']; ?>&media2=<?php echo $_GET['media2']; ?>&media=<?php echo $_GET['media']; ?>&mediaid=<?php echo $_GET['mediaid']; ?>" method="post">
						  <?php require "mtype_ShiryoukoStudio_add_edit.php"; ?>
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
