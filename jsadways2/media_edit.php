<?php
	
	// 2018-03-14 (Jimmy): 傑思jsadways2/media_edit.php, 香港jsadways2hk/media_edit.php, 豐富媒體jsadways2ff/media_edit.php 共用此檔案
	require_once dirname(__DIR__) .'/autoload.php';

	$objSizeformat = CreateObject('Sizeformat', GetVar('id'));

	if (!IsId($objSizeformat->getId()) || (!IsPermitted('superuser') && !in_array($_SESSION['departmentid'], [21, 22]))) {
		RedirectLink('media_list.php');
	}
	
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>【<?= $GLOBALS['env']['flag']['name']; ?>】媒體格規</title>
		<?php include("public/head.php"); ?>
	</head>
	<body>
		<?php include("public/topbar.php"); ?>
		
		<div class="container-fluid">
			<div class="row-fluid">
				<?php include("public/left.php"); ?>
				
				<div id="content" class="span10">
					<form class="form-horizontal" action="media_edit2.php" method="post">
						<div class="row-fluid">	
							<div class="box span7">
								<div class="box-header well" data-original-title>
									<h2><i class="fa fa-cube"></i>&nbsp;媒體 - <?= $objSizeformat->getVar('medianame'); ?></h2>
								</div>

								<div class="box-content">
									<table class="table table-bordered table-striped">
										<tr>
											<td><h4>adtype</h4></td>
											<td><?= str_replace("\\n", "<br/>", $objSizeformat->getVar('adtype')); ?></td>
										</tr>
										<tr>
											<td><h4>phonesystem</h4></td>
											<td><?= str_replace("\\n", "<br/>", $objSizeformat->getVar('phonesystem')); ?></td>
										</tr>
										<tr>
											<td><h4>position</h4></td>
											<td><?= str_replace("\\n", "<br/>", $objSizeformat->getVar('position')); ?></td>
										</tr>
										<tr>
											<td><h4>最近更新</h4></td>
											<td>於 <b><?= date('Y-m-d H:i',$objSizeformat->getVar('times')); ?></b> 由 <b><?= $objSizeformat->getVar('user'); ?></b> 更新</td>
										</tr>
										<tr>
											<td><h4>size</h4></td>
											<td><textarea class="autogrow" name="format1"><?= str_replace("\\n", "\n", $objSizeformat->getVar('format1')); ?></textarea></td>
										</tr>
										<tr>
											<td><h4>format</h4></td>
											<td><textarea class="autogrow" name="format2"><?= str_replace("\\n", "\n", $objSizeformat->getVar('format2')); ?></textarea></td>
										</tr>
									</table>

									<div class="form-actions">
										<input name="id" type="hidden" value="<?= $objSizeformat->getId(); ?>" />
										<input name="mediaid" type="hidden" value="<?= $objSizeformat->getVar('mediaid'); ?>" />
										<button type="submit" class="btn btn-primary">確定修改</button>
										<button type="submit" class="btn btn-primary">確定修改</button>
									</div>
								</div>	
							</div>
						</div>
					</form>
				</div>
			</div>		
			<hr/>

			<?php include("public/footer.php"); ?>
		</div>

		<?php include("public/js.php"); ?>
	</body>
</html>