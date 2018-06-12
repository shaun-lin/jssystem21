<?php
	
	// 2018/5/12 ken chien,新增對內的SAP成本表
    require_once dirname(__DIR__) .'/autoload.php';
	$db = clone($GLOBALS['app']->db);
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>【<?= $GLOBALS['env']['flag']['name'];?>】_總公司編號匯入</title>
		<?php include("public/head.php"); ?>
	</head>
	<body>
		<?php include("public/topbar.php"); ?>

		<div class="container-fluid">
			<div class="row-fluid">
				<?php include("public/left.php"); ?>
				
				<div id="content" class="span10">
					<div class="span6">
						<div class="row-fluid">
							<div class="box">
								<div class="box-header well" data-original-title>
									<h2><i class="fa fa-search"></i>&nbsp;&nbsp;總公司編號匯入</h2>
								</div>

								<div class="box-content">
                                    <form action="excel/sap_excel2_add.php" class="form-horizontal" name="frmMain" method="post" enctype="multipart/form-data">
										<fieldset> 
											
											<div class="control-group">
												<div class="controls">
                                                <input type="file" name="file" id="file" class"btn btn-primary" /><br />
                                                <input type="submit" name="submit" value="上傳檔案" />										
												</div>
											</div>
											<div class="contol-group">
											<h3>Tip:</h5>
											<h4>1.檔案僅接受副檔名xls檔，Excel版本2003版。</h4>
											<h4>2.若無總公司編號，請勿上傳。</h4>
											<h4>3.Excel表單結構請勿擅自更改。</h4>
											<h4>(列BE為Jasdways內部編號；列BF為總公司編號)</h4>
											</div>
											
										</fieldset>
									</form>
								</div>
							</div>
						</div>

					</div>
										
				</div>
			</div>
			<hr/>		
	</body>
</html>
