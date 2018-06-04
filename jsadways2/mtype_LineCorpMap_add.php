<?php

	session_start();
	include('include/db.inc.php');
	include 'mtype_LineCorpMap_definition.php';

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
								<h2><i class="icon-edit"></i> <?= $row1['name']; ?>-新增媒體-<?= $mediaName; ?></h2>
							</div>
							
							<div class="box-content">
							<form class="form-horizontal" action="mtype_LineCorpMap_add2.php?id=<?= $_GET['id']; ?>&cue=<?= $_GET['cue']; ?>&media2=<?= $_GET['media2']; ?>&media=<?= $_GET['media']; ?>&mediaid=<?= $_GET['mediaid']; ?>" method="post">
									<?php require "mtype_LineCorpMap_add_edit.php"; ?>
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