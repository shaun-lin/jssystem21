<?php 
	
	require_once dirname(__DIR__) .'/autoload.php';

	$objMrbsUsers = CreateObject('MrbsUsers');
	$objBlogger = CreateObject('Blogger');
	$objBloggerChargeoff = CreateObject('BloggerChargeoff');

	$campaignSerial = GetVar('campaign_serial');
	$startTime = GetVar('start_time');
	$endTime = GetVar('end_time');
	$selectWomm = GetVar('SelectWomm');
	$bloggerId = GetVar('blogger_id');

	$searchCondition = '';
	$searchFields = "`blogger_chargeoff`.*, CA.`name` AS `campaignName`, BL.`ac_id`, BL.`display_name`, BL.`blog_name`, BL.`fb_name`, BL.`ig_name`, BL.`youtube_name`";
	$searchJoin = "LEFT JOIN `campaign` CA ON `blogger_chargeoff`.`campaign_id` = CA.`id` LEFT JOIN `blogger` BL ON `blogger_chargeoff`.`blogger_id` = BL.`id`";

	if ($campaignSerial) {
		$searchCondition = "`blogger_chargeoff`.`campaign_id` = ( SELECT `id` FROM `campaign` WHERE `idnumber` = ". SqlQuote($campaignSerial) ." )";
	} else if ($startTime && $endTime) {
		$searchCondition = "(`blogger_chargeoff`.`chargeoff_date` BETWEEN ". SqlQuote(date('Y-m-01', strtotime($startTime))) ." AND ". SqlQuote(date('Y-m-t', strtotime($endTime))) .")";
	}

	if (IsId($bloggerId)) {
		$searchCondition .= (empty($searchCondition) ? '' : ' AND ') ."`blogger_chargeoff`.`blogger_id` = $bloggerId";
	}

	if (IsId($selectWomm)) {
		$searchCondition .= (empty($searchCondition) ? '' : ' AND ') ."CA.`wommId` = $selectWomm";
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>【<?= $GLOBALS['env']['flag']['name']; ?>】案件列表</title>
		<?php include("public/head.php"); ?>
		<?php include("public/js.php"); ?>
	</head>
	<body>
		<?php include("public/topbar.php"); ?>
		
		<div class="container-fluid">
			<div class="row-fluid">
				<?php include("public/left.php"); ?>
				
				<div id="content" class="span10">
					<div class="row-fluid">
						<div class="box span12">
							<div class="box-header well" data-original-title>
								<h2><i class="icon-edit"></i> 查詢條件</h2>
							</div>

							<div class="box-content">
								<form class="form-horizontal" action="campaign_bloger.php" method="post" id="chargeoff_form">
									<fieldset>
										<div class="control-group">
											<label class="control-label">搜尋：委刊編號</label>
											<div class="controls">
											<input class="input-xlarge" id="campaign_serial" name="campaign_serial" type="text" value="<?= isset($_POST['campaign_serial']) ? $_POST['campaign_serial']:''; ?>">
											</div>
										</div>
										
										<div class="control-group">
											<label class="control-label">或</label>
											<div class="controls">
											
											</div>
										</div>
										
										<div class="control-group">
											<label class="control-label">搜尋：出帳時間</label>
											<div class="controls">
												<input id="start_time" style="width:100px" value="<?= isset($_POST['start_time']) ? $_POST['start_time']:''; ?>" name="start_time" type="text"> ~ <input id="end_time" style="width:100px" value="<?= isset($_POST['end_time']) ? $_POST['end_time']:''; ?>" name="end_time" type="text">
											</div>
										</div>
							
										<div class="control-group">
											<label class="control-label">搜尋：寫手</label>
											<div class="controls">
												<select id="blogger_id" name="blogger_id" data-rel="chosen" style="width:400px">
													<option value="">全部</option>
													<? foreach ($objBlogger->searchAll('', '', '', '', '', '`id`, `ac_id`, `display_name`, `blog_name`, `fb_name`, `ig_name`, `youtube_name`') AS $itemBlogger) : ?>
														<option value="<?= $itemBlogger['id']; ?>" <?= $bloggerId == $itemBlogger['id'] ? 'selected' : ''; ?>>
															<?= $itemBlogger['ac_id'] .'&nbsp;&nbsp;'. htmlspecialchars($itemBlogger['blog_name'], ENT_QUOTES); ?>
															<? if ($itemBlogger['blog_name']) : ?>
																&nbsp;-Blog:&nbsp;<?= htmlspecialchars($itemBlogger['blog_name'], ENT_QUOTES); ?>
															<? endif; ?>
															<? if ($itemBlogger['fb_name']) : ?>
																&nbsp;-FB:&nbsp;<?= htmlspecialchars($itemBlogger['fb_name'], ENT_QUOTES); ?>
															<? endif; ?>
															<? if ($itemBlogger['ig_name']) : ?>
																&nbsp;-IG:&nbsp;<?= htmlspecialchars($itemBlogger['ig_name'], ENT_QUOTES); ?>
															<? endif; ?>
															<? if ($itemBlogger['youtube_name']) : ?>
																&nbsp;-YouTube:&nbsp;<?= htmlspecialchars($itemBlogger['youtube_name'], ENT_QUOTES); ?>
															<? endif; ?>
														</option>
													<? endforeach; ?>
												</select>
											</div>
										</div>

										<div class="control-group">
											<label class="control-label" for="SelectWomm">搜尋：口碑PM</label>
											<div class="controls">
												<select id="SelectWomm" data-rel="chosen" name="SelectWomm">
													<option value=""></option>
													<? foreach ($objMrbsUsers->searchAll("(`id` = 18 OR `departmentid` IN (19, 20)) AND `id` NOT IN (142, 145)", '', '', '', '', '`id`, `name`, `username`') AS $itemUser) : ?>
														<option value="<?= $itemUser['id']; ?>" <?= $selectWomm == $itemUser['id'] ? 'selected' : ''; ?>><?= ucfirst($itemUser['name']) .'&nbsp;'. $itemUser['username']; ?></option>
													<? endforeach; ?>
												</select>
											</div>
										</div>

										<div class="control-group">
											<div class="controls">
												<button type="submit" class="btn btn-primary">查詢</button>&nbsp;&nbsp;&nbsp;&nbsp;
												<button type="button" class="btn btn-primary" id="print_blog_chargeoff">寫手清單Excel</button>&nbsp;&nbsp;&nbsp;&nbsp;
												<button type="button" class="btn btn-primary" id="print_blog_chargeoff_total">匯款加總Excel</button>
											</div>
										</div>
									</fieldset>
								</form>
							</div>
						</div>
					</div>
					
					<div class="row-fluid">
						<div class="box span12">
							<div class="box-header well" data-original-title>
								<h2><i class="icon-edit"></i> 寫手出帳列表</h2>
							</div>

							<div class="box-content">
								<table class="table table-striped table-bordered bootstrap-datatable datatable">
									<thead>
										<tr>
											<th>案件名稱</th>
											<th>帳戶ID</th>
											<th>姓名</th>
											<th>
												Blog名稱<br/>
												FB名稱<br/>
												IG名稱<br/>
												Youtube頻道名稱
											</th>
											<th>出帳時間</th>
											<th>金額</th>
											<th>備註</th>
											<th>建立時間</th>
											<th>填寫人</th>
										</tr>
									</thead>
									<tbody>
										<? foreach ($objBloggerChargeoff->search($searchCondition, '`chargeoff_date`', 'DESC', '', 0, empty($searchCondition) ? 30 : -1, $searchJoin, $searchFields) as $itemCharge) : ?>
											<tr>
												<td><?= $itemCharge["campaignName"]; ?></td>
												<td nowrap><?= $itemCharge["ac_id"]; ?></td>
												<td><?= $itemCharge["display_name"]; ?></td>
												<td>
													<? if ($itemCharge['blog_name']) : ?>
														Blog:&nbsp;&nbsp;<?= htmlspecialchars($itemCharge['blog_name'], ENT_QUOTES); ?><br/>
													<? endif; ?>
													<? if ($itemCharge['fb_name']) : ?>
														FB:&nbsp;&nbsp;<?= htmlspecialchars($itemCharge['fb_name'], ENT_QUOTES); ?><br/>
													<? endif; ?>
													<? if ($itemCharge['ig_name']) : ?>
														IG:&nbsp;&nbsp;<?= htmlspecialchars($itemCharge['ig_name'], ENT_QUOTES); ?><br/>
													<? endif; ?>
													<? if ($itemCharge['youtube_name']) : ?>
														YouTube:&nbsp;&nbsp;<?= htmlspecialchars($itemCharge['youtube_name'], ENT_QUOTES); ?><br/>
													<? endif; ?>
												</td>
												<td nowrap><?= $itemCharge["chargeoff_date"]; ?></td>
												<td><?= $itemCharge["price"]; ?></td>
												<td><?= $itemCharge["remark"]; ?></td>
												<td><?= $itemCharge["create_time"]; ?></td>
												<td><?= $itemCharge["name"]; ?></td>
											</tr>
										<? endforeach; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>	
			<hr/>

			<?php include("public/footer.php"); ?>
		</div>

		<script>
			$(document).ready(function() {
				$("#start_time").datepicker({ 
					dateFormat: 'yy-mm-dd'
				});
				$("#end_time").datepicker({ 
					dateFormat: 'yy-mm-dd'
				});

				$('#print_blog_chargeoff').click(function(event) {
					var w = window.open('excel/print_blog_chargeoff.php?'+ $('form#chargeoff_form').serialize(), '_blank');
				});

				$('#print_blog_chargeoff_total').click(function(event) {
					var w = window.open('excel/print_blog_chargeoff_total.php?'+ $('form#chargeoff_form').serialize(), '_blank');
				});
			});
		</script>
	</body>
</html>