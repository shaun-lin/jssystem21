<?php

	session_start();
	include('include/db.inc.php');
	include 'mtype_FBFee_definition.php';

	$sql1 = sprintf("SELECT * FROM `campaign` WHERE `id` = %d;", $_GET['id']);
    $result1 = mysql_query($sql1);
	$row1 = mysql_fetch_array($result1);
	
?>
<!DOCTYPE html>
<html>
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

				<div id="content" class="span10">
					<div class="row-fluid sortable">
						<div class="box span12">
							<div class="box-header well" data-original-title>
								<h2><i class="icon-edit"></i> <?php echo $row1['name']; ?>-新增媒體-<?= $mediaName; ?></h2>
							</div>
							
							<div class="box-content">
							<form class="form-horizontal" action="mtype_FBFee_add2.php?id=<?php echo $_GET['id']; ?>&cue=<?php echo $_GET['cue']; ?>&media2=<?php echo $_GET['media2']; ?>&media=<?php echo $_GET['media']; ?>&mediaid=<?php echo $_GET['mediaid']; ?>" method="post">
									<?php require 'mtype_FBFee_add_edit.php'; ?>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			<hr/>

			<?php include("public/footer.php"); ?>
		</div>

		<?php include("public/js.php"); ?>
	</body>
</html>
