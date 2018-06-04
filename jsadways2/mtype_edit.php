<?php
	
	// 2018-03-14 (Jimmy): 傑思jsadways2/media_edit.php, 香港jsadways2hk/media_edit.php, 豐富媒體jsadways2ff/media_edit.php 共用此檔案
	require_once dirname(__DIR__) .'/autoload.php';

	$objSizeformat = CreateObject('Mtype', GetVar('id'));
	if ((!IsPermitted('superuser') && !in_array($_SESSION['departmentid'], [21, 22]))) {
		RedirectLink('mtype_list.php');
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>【<?= $GLOBALS['env']['flag']['name']; ?>】模板規格</title>
		<?php include("public/head.php"); ?>
	</head>
	<body>

		<?php include("public/topbar.php"); ?>
		
		<div class="container-fluid">
			<div class="row-fluid">
				<?php include("public/left.php"); ?>
				
				<div id="content" class="span10">
					<form class="form-horizontal" action="mtype_edit2.php" method="post">
						<div class="row-fluid">	
							<div class="box span7 autogrow">
								<div class="box-header well" data-original-title>
									<h2><i class="fa fa-cube"></i>&nbsp;模板 - <?= $objSizeformat->getVar('name'); ?></h2>
								</div>
								<!--編輯-->
								<div class="box-content autogrow">
									<? if (IsId($objSizeformat->getId())){?>
									<table class="table table-bordered table-striped">
										<tr>
											<td><h4>最近更新</h4></td>
											<td>於 <b>
											<?php
											if(!empty($objSizeformat->getVar('time'))==1)
											{echo date('Y-m-d H:i',$objSizeformat->getVar('time')); 
											?></b> 由 <b>
											<?= $objSizeformat->getVar('creator'); }
											?></b> 更新</td>
										</tr>
										<tr>
										
											<td><h4>模板名稱
										</h4></td>
											<td style="text-align:left;"><input type="text" name="name" style="font-size:24px;height:30px;width:40%;margin-left: 10%;" value=<?echo str_replace("\\n","\n",$objSizeformat->getVar('name')); ?>></td>
										</tr>
										<tr>
											<td><h4>對應模板</h4></td>
											<td style="text-align:left;"><input type="text" name="dashboard" style="font-size:24px;height:30px;width:40%;margin-left: 10%;"  value=<?echo str_replace("\\n", "\n", $objSizeformat->getVar('dashboard')); ?>></td>
										</tr>
										<tr>
											<td><h4>是否顯示</h4></td>
											<td>
												<label class="radio-inline control-label">
												<input class="radio" type="radio" name="display" value="1" <?php if (str_replace("\\n", "\n", $objSizeformat->getVar('display'))=="1"):?>checked="checked"<?endif;?>> 顯示
											</label>
											<label class="radio-inline control-label">
  												<input class="radio" type="radio" name="display" value="0" <?php if (str_replace("\\n", "\n", $objSizeformat->getVar('display'))=="0"):?>checked="checked"<?endif;?>> 不顯示
  												</label>
											</td>
										</tr>
										
									</table>
									<!--  新增  -->
									<?}else if (!IsId($objSizeformat->getId())){?>
										<table class="table table-bordered table-striped">
										<tr>
											<td><h4>模板名稱</h4></td>
											<td style="text-align:left;"><input type="text" name="name" style="font-size:24px;height:30px;width:40%;margin-left: 10%;" ></td>
										</tr>
										<tr>
											<td><h4>對應模板</h4></td>
											<td style="text-align:left;"><input type="text" name="dashboard" style="font-size:24px;height:30px;width:40%;margin-left: 10%;" ></td>
										</tr>
										<tr>
											<td><h4>是否顯示</h4></td>
											<td style="text-align: left;">
											<label class="radio-inline control-label"> 
												<input type="radio" name="display" value="1" checked="checked"> 顯示<br />
											</label>
											<label class="radio-inline control-label">
  												<input type="radio" name="display" value="0"> 不顯示<br />
  											</label>
  											</td>
										
									</table>
										<?}?>

									<div class="form-actions">
										<input name="id" type="hidden" value="<?= $objSizeformat->getId(); ?>" />
										<input name="mediaid" type="hidden" value="<?= $objSizeformat->getVar('mediaid'); ?>" />
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