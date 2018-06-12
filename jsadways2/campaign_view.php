<?php
	
	if (!isset($parameter)) {
		$parameter = [
			'blogger_media_id' => [19],
			'extra_media_id' => [94],
			'cpm_media_list' => [32, 40, 41, 51, 58, 69, 72, 75, 76, 77, 78, 86, 87, 90, 95, 102, 110, 112, 113, 114, 115, 129, 136, 151, 152, 153, 154, 155, 156, 157],
			'detail_head_simple_media' => [133, 137, 144],
			'detail_head_reverve_type' => ['ChatBot'],
			'extra_fee_media' => [133, 142, 144],
			'direct_income' => [79, 82, 143, 148],
		];
	}
	// echo "parameter <br/>";

	// print_r($parameter);

	// echo "<br/>parameter <br/>";
	require_once dirname(__DIR__) .'/autoload.php';
	
	$db = clone($GLOBALS['app']->db);

	IncludeFunctions('jsadways');
	CreateNativeDBConnector();

	$campaignId = GetVar('id');
	$sql2 = sprintf("SELECT `campaign`.*, `mrbs_users`.`username` AS `media_leader_name` FROM `campaign` LEFT JOIN `{$GLOBALS['env']['db_master']}`.`mrbs_users` ON `media_leader` = `mrbs_users`.`id` WHERE `campaign`.`id` = %d;", $campaignId);
	$db->query($sql2);
	// echo "sql2:<br/>".$sql2."<br/>";
	$row2 = $db->next_record();

	if (!IsId($campaignId) || empty($row2['id'])) {
		RedirectLink('campaign_list.php');
	}

	$bgcolor = [
		'FFCEED', 'FFCDE5', 'FFCECD', 'FFE7CD', 'FEFFCD',
		'E5FFCD', 'CDFFCE', 'CDFFE7', 'CDFEFF', 'CDE5FF',
		'CECDFF', 'E7CDFF', 'FFCDFE', '7ABC7B', '9ABC7A',
		'BC9C7A', 'C68F8E', 'FFCCCC', 'CCCCFF', 'A2CFD0',
		'CDFFCE', 'E7CDFF', 'C68F8E', '7ABC7B', '9ABC7A',
		'BC9C7A', '9ABC7A', 'FFCCCC', 'CCCCFF', 'E5FFCD',
		'CDFFCE', 'CDE5FF', 'FFCCCC', 'FFCDE5'
	];

	$isRequireExceptionMessage = false;
	$isGrantedForCancel = $row2['status'] != 4;
	$isCampaignException = $row2['status'] == 7;
	$isGreaterPMLevel = $_SESSION['usergroup'] >= 3;
	$isGrantedForFinancial = in_array($_SESSION['usergroup'], [4, 6]);
	$isGrantedForExceptionApproval = ($row2['status'] == 7 && ($_SESSION['usergroup'] == 9 || IsPermitted('backend_campaign_exception_approval')));
	
	$objMediaAccounting = CreateObject('MediaAccounting');
	$rowsAccounting = $objMediaAccounting->getList($campaignId);

	// echo "<br/>rowsAccounting<br/>";
	// print_r($rowsAccounting);

	// echo count($rowsAccounting)."<br/>";
	// echo "<br/>rowsAccounting<br/>";
	$a5 = 0;
	$pr = 0;
	$cost = 0;
	$totaltotal = 0;
	$extCueQuantity = 0;
	$extCueTotalPrice = 0;
	$intCueQuantity = 0;
	$intCueTotalPrice = 0;
	$rowsClass2 = [];
	$listMediaChangeItem = [];
	$mediaItemStatus = [];

	$rowsReceipt = [0 => [], 1 => []];
	$sqlReceipt = "SELECT * FROM receipt WHERE campaign_id = $campaignId AND status IN (0, 1, 2, 9);";
	$db->query($sqlReceipt);
	while ($itemReceipt = $db->next_record()) {
		$rowsReceipt[$itemReceipt['status']][] = $itemReceipt;
	}
	$isReceipted = count($rowsReceipt[1]);

	if ($isGreaterPMLevel) {
		$sqlClass2 = "SELECT * FROM class2";
		$resultClass2 = mysql_query($sqlClass2);

		while ($itemClass2 = mysql_fetch_array($resultClass2)) {
			$rowsClass2[] = $itemClass2;
		}
	}

	$rowsOrdinal = GetUsedMediaOrdinal($campaignId);
	foreach ($rowsOrdinal as $idxOrdinal) {
		$row4 = GetMedia($idxOrdinal);

		$sqlMediaItem = "SELECT * FROM media$idxOrdinal WHERE campaign_id = $campaignId ORDER BY id ASC;";
		// echo($sqlMediaItem);
		$db->query($sqlMediaItem);
		// echo "<br/>row3<br/>";
		// print_r($db->next_record());
		// echo "<br/>row3<br/>";
		while ($row3 = $db->next_record()) {
			if ($row3['cue'] == 1) {
				$extCueTotalPrice += $row3['totalprice'];

				if (in_array($idxOrdinal, $parameter['extra_media_id'])) {
					// CPM顯示為 曝光數
					$extCueQuantity += $row3['media_type'] == 'CPM' ? $row3['quantity2'] : $row3['quantity'];
				} else {
					// CPM顯示為 曝光數
					$extCueQuantity += in_array($idxOrdinal, $parameter['cpm_media_list']) ? $row3['quantity2'] : $row3['quantity'];
				}
			}

			if ($row3['cue'] == 2) {
				if ($isGreaterPMLevel) {
					$key = $row3['a'] .'-'. $row3['a0'] .','. $row4["name"];
					$text = $row4["name"] .'('. $row3['a'] .'-'. $row3['a0'] .')';
					$listMediaChangeItem[$key] = $text;
				}

				if ($row3['a5'] == 1) {
					$a5 = 1;
				}
			}
			
			if (empty($row3['totalprice'])) {
				$pr = 1;
				$isRequireExceptionMessage = true;
			}
		}
	}


	$closeEntryFlag = $GLOBALS['app']->preference->get('close_entry_flag');
	$closeEntryFlag = $closeEntryFlag ? $closeEntryFlag : date('Ym', strtotime('-1 month'));
	$isNonGrantedForCampaignEntry = !IsPermitted('finacial', null, Permission::ACL['finacial_ignore_campaign_closed_entry']);
	$isIncludedClosedFiedls = date('Ym', $row2['date11']) <= $closeEntryFlag && date('Ym', $row2['date22']) >= $closeEntryFlag;

	$accountingMonth = [];
	$isRunning = true;
	$campaignStartStamp = $row2['date11'];
	$campaignEndStamp = $row2['date22'];
	$firstDate = date('Ym', $campaignStartStamp);
	while ($isRunning) {
		$accountingMonth[date('Ym', $campaignStartStamp)] = date('m', $campaignStartStamp);

		if (date('Ym', $campaignStartStamp) >= date('Ym', $campaignEndStamp)) {
			$isRunning = false;
		} else {
			$campaignStartStamp = AddMonthToDate(1, date('Y-m-d', $campaignStartStamp), true);
		}
	}


?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>【<?= $GLOBALS['env']['flag']['name']; ?>】案件列表<?= $_SESSION['version']; ?></title>
		<?php include("public/head.php"); ?>
		<?php include("public/js.php"); ?>
		<script>
			$(function() {
				totalTochang();
			});

			function totalTochang()
			{
				var sp2 = new Array();
				var sp4 = new Array();
				var splt2;
				var splt4;

				if ($('#receipt1 option:selected').text() != '') {
					var str = $('#receipt1 option:selected').text();
					sp = str.split("(");

					for(i=1;i<sp.length;i++) {
						splt2 = sp[i].split(")");
						sp2.push(splt2[0]);
					}

					var total_sp = 0;
					for(i= 0;i<sp2.length;i++) {
						total_sp += parseInt(sp2[i]);
					}

					//加總未稅
					var str2 = $('#receipt1 option:selected').text();
					AB = str.split("未稅[");
					
					for(i=1;i<AB.length;i++) {
						splt4 = AB[i].split("]");
						sp4.push(splt4[0]);
					}

					var total_sp2 = 0;
					for(i= 0;i<sp4.length;i++) {
						total_sp2 += parseInt(sp4[i]);
					}
					//加總未稅

					$('#test_view').val(total_sp);
					$('#test_view2').val(total_sp2);
				}
			}

			function chkMoney(str)
			{
				var SP = str.split("[");
				var SP2 = SP[1].split("]");
				alert(SP2[0]);
				if (SP2[0] == '已收款') {
					return true;
				} else {
					return false;
				}
			}

			function chkMoneyForEnd()
			{
				if (confirm('確定要結案？')) {
					return true;
				} else {
					return false;
				}
			}
		</script>
		<style>
			.dropdown {
				position: relative;
				display: inline-block;
			}

			.dropdown-content {
				display: none;
				position: absolute;
				background-color: rgba(212, 30, 36, 0.8);
				min-width: 140px;
				box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
				z-index: 1;
				border-radius: 4px;
			}

			.dropdown-content a {
				color: white;
				padding: 12px 4px;
				text-decoration: none;
				display: block;
			}

			.dropdown-content a:hover {
				background-color: rgba(200, 200, 200, 0.8);
				font-weight: bold;
				border-radius: 4px;
			}

			.dropdown:hover .dropdown-content {
				display: block;
			}

			.progress {
				margin-bottom: 9px;
				height: 26px;
			}

			.progress .bar {
				height: 26px; 
				font-size: 1.4em; 
				padding-top: 3px;
			}

			.awesome-icon {
				color: white;
				font-size: 1.3em;
			}

			.btn {
				margin-right: 1px;
				margin-bottom: 2px;
			}

			input.large_text_input {
				font-size: 1.5em;
				height: 1.2em;
				width: 6em;
			}

			.row-accounting-detail td, .row-accounting-detail td:hover {
				background-color: #ffffe5 !important;
			}

			.row-grey td, .row-grey td:hover,
			.row-grey th, .row-grey th:hover {
				background-color: #fcfcfc !important;
			}

			.row-dark-grey td, .row-dark-grey td:hover,
			.row-dark-grey th, .row-dark-grey th:hover {
				background-color: #f3f3f3 !important;
			}

			.row-white td, .row-white td:hover,
			.row-white th, .row-white th:hover {
				background-color: white !important;
			}

			.row-yellow td, .row-yellow td:hover,
			.row-yellow th, .row-yellow th:hover {
				background-color: #ffffe5 !important;
			}

			td.cell-warning, td.cell-warning:hover {
				background-color: rgba(255, 153, 153, .3) !important;
			}

			td.cell-warning input[type="text"] {
				border-color: red;
			}
		</style>
	</head>
	<body>
		<?php include("public/topbar.php"); ?>
	
		<div class="container-fluid">
			<div class="row-fluid">
				<?php include("public/left.php"); ?>

				<div id="content" class="span10">
					<div class="row-fluid">
						<div class="box span4">
							<div class="box-content">
								<div class="row-fluid">
									<h3><?= sprintf("%s【%s】【%s】", $row2['name'], $row2['version'] == 2 ? '2.0' : '1.0', $row2['idnumber']); ?></h3>

									<? if ($row2['status'] != 4) : ?>
										<a href="campaign_edit.php?id=<?= $campaignId; ?>" style="padding: 6px;">
											<i class="fa fa-pencil"></i>&nbsp;編輯
										</a>
									<? endif; ?>

									<div style="font-size: 1.1em; display: block; padding-top: 6px;">
										<p style="margin-bottom: 4px;">
											<span style="color: #777;">廣告代理商(Agency)：&nbsp;&nbsp;</span><b><?= $row2['agency']; ?></b>
										</p>
										<p style="margin-bottom: 4px;">
											<span style="color: #777;">廣告主(Client)：&nbsp;&nbsp;</span><b><?= $row2['client']; ?></b>
										</p>
										<p style="margin-bottom: 4px;">
											<span style="color: #777;">期間(Period)：&nbsp;&nbsp;</span><b><?= $row2['date1']; ?> ~ <?= $row2['date2']; ?></b>
										</p>
										<p style="margin-bottom: 4px;">
											<span style="color: #777;">負責AE：&nbsp;&nbsp;</span><b><?= $row2['member']; ?></b>
										</p>
										<? if (isset($row2['media_leader_name']) && $row2['media_leader_name']) : ?>
											<p style="margin-bottom: 4px;">
												<span style="color: #777;">媒體部PM：&nbsp;&nbsp;</span><b><?= $row2['media_leader_name']; ?></b>
											</p>
										<? endif; ?>
										<? if (isset($row2['womm']) && $row2['womm']) : ?>
											<p style="margin-bottom: 4px;">
												<span style="color: #777;">口碑部PM：&nbsp;&nbsp;</span><b><?= $row2['womm']; ?></b>
											</p>
										<? endif; ?>
										<p>
											<span style="color: #777;">備註：&nbsp;&nbsp;</span><?= nl2br($row2['others']); ?>
											<span style="width: 90%; display: inline-block; padding: 4px; display: none;" id="exception_tip">
												<span style="width: 90%; display: inline-block; border: 1px solid #ff9999; padding: 6px; border-radius: 4px; background-color: #ffe5e5;">	
													案件金額異常須於備註欄位填寫原因
												</span>
											</span>
										</p>

										<? if ($row2['campaign_exception_comment']) : ?>
											<p>
												<span style="width: 90%; display: inline-block; padding: 4px;">
													<span style="width: 100%; display: inline-block; border: 1px solid #cccc00; padding: 6px; border-radius: 4px; background-color: #ffffe5;">	
														<?= nl2br($row2['campaign_exception_comment']); ?>
													</span>
												</span>
											</p>
										<? endif; ?>
									</div>
								</div>
							</div>
					  
                  			<div>
								<div class="box-content">
									<div class="row-fluid">
										<h4><?= empty($row2['is_receipt']) ? '尚未確認發票' : '已確認發票'; ?></h4>
										<? if ($row2['is_receipt'] == 0 && in_array($_SESSION['usergroup'], [4, 6])) : ?>
											<a class="btn btn-info" href="campaign_execute.php?id=<?= $row2['id']; ?>&action=confirm_receipted" onclick="return confirm('確定已開完發票？');" title="確定已開完發票？" data-rel="tooltip">
												<i class="fa fa-vcard-o awesome-icon"></i>&nbsp;確認已開發票
											</a>
										<? endif; ?>
										<br/><br/>
										<h4><?= $row2['action1'] >= 1 ? '已回簽' : '未回簽'; ?></h4>
										<h4>發票需求</h4>
										<select multiple data-rel="chosen" style="width: 300px;">
											<? foreach ($rowsReceipt[0] as $row3) : ?>
												<option value="<?= $row3['id']; ?>" selected><?= $row3['receipt_number'] .'('. $row3['totalprice2'] .')-'. $row3['datemonth'] .'-['. ($row3['status'] == 0 ? '未收款' : '已收款') .'] 未稅['. $row3['totalprice1'] .']'; ?></option>
											<? endforeach; ?>
										</select>
										<h4>已開發票</h4>
										<select multiple data-rel="chosen"  style="width: 300px;">
											<? if (isset($rowsReceipt[1]) && is_array($rowsReceipt[1])) : ?>
												<? foreach ($rowsReceipt[1] as $row3) : ?>
													<? if (empty($row3['receipt3id'])) : ?>
														<option value="<?= $row3['id']; ?>" selected><?= $row3['receipt_number'].'('.$row3['totalprice2'].')-'.$row3['datemonth'].'-[未收款] 未稅['.$row3['totalprice1'].']'; ?></option>
													<? else : ?>
														<?php
															$sql44 = "SELECT * FROM receipt3 WHERE id = {$row3['receipt3id']};";
															$result44 = mysql_query($sql44);
															$row44 = mysql_fetch_array($result44);
														?>
														<? if (empty($row44['times1'])) : ?>
															<option value="<?= $row3['id']; ?>" selected><?= $row3['receipt_number'].'('.$row3['totalprice2'].')-'.$row3['datemonth'].'-['. (empty($row44['times2']) ? '未收款' : '已收到支票') .'] 未稅['.$row3['totalprice1'].']'; ?></option>
														<? else : ?>
															<option value="<?= $row3['id']; ?>" selected><?= $row3['receipt_number'].'('.$row3['totalprice2'].')-'.$row3['datemonth'].'-[已收款] 未稅['.$row3['totalprice1'].']'; ?></option>
														<? endif; ?>
													<? endif; ?>
												<? endforeach; ?>
											<? endif; ?>
										</select>
										<? if (IsPermitted('finacial', null, Permission::ACL['finacial_unlock_receipt_effective'])) : ?>
											<? if (isset($rowsReceipt[1]) && count($rowsReceipt[1])) : ?>
												<? if (isset($rowsReceipt[1]) && count($rowsReceipt[1])) : ?>
													<br/>
													<a class="btn btn-danger" href="campaign_execute.php?id=<?= $row2['id']; ?>&action=unlock_receipt_effective" data-rel="tooltip" onclick="return confirm('確定要解除封印？');" title="確定要解除封印">
														<i class="fa fa-unlock awesome-icon"></i>&nbsp;解除發票封印
													</a>
													<br/>
												<? endif; ?>
											<? endif; ?>
										<? endif; ?>
										<br/><br/>
										<h4>已開發票總額(含稅)</h4>
										<input type="text" id="test_view" readonly="true" style="width: 290px;">
										<h4>已開發票總額(未稅)</h4>
										<input type="text" id="test_view2" readonly="true" style="width: 290px;">
										<? if ($isIncludedClosedFiedls && IsPermitted('finacial', null, Permission::ACL['finacial_unlock_campaign_closed_entry'])) : ?>
											<? if (getCampaignClosedEntryStatus($campaignId)) : ?>
												<a class="btn btn-info" href="campaign_execute.php?id=<?= $row2['id']; ?>&action=lock_campaign_finacial_fields" data-rel="tooltip" onclick="return confirm('確定要鎖定已關帳財報欄位？');" title="確定要鎖定已關帳財報欄位">
													<i class="fa fa-lock awesome-icon"></i>&nbsp;鎖定已關帳財報欄位
												</a>
											<? else : ?>
												<a class="btn btn-inverse" href="campaign_execute.php?id=<?= $row2['id']; ?>&action=unlock_campaign_finacial_fields" data-rel="tooltip" onclick="return confirm('確定要解除已關帳財報欄位？');" title="確定要解除已關帳財報欄位？">
													<i class="fa fa-unlock awesome-icon"></i>&nbsp;解除已關帳財報欄位
												</a>
											<? endif; ?>
											<br/><br/>
										<? endif; ?>
									</div>
								</div>
							</div>
					
							<div>
								<div class="box-content">
									<div class="row-fluid">
									<h4>作廢發票</h4>
									<select multiple data-rel="chosen" style="width: 300px;">
										<? if (isset($rowsReceipt[2]) && is_array($rowsReceipt[2])) : ?>
											<? foreach ($rowsReceipt[2] as $row3) : ?>
												<option value="<?= $row3['id']; ?>" selected><?= $row3['receipt_number'] .'('. $row3['totalprice2'] .')-'. $row3['datemonth']; ?></option>
											<? endforeach; ?>
										<? endif; ?>
									</select>
									<? if (isset($rowsReceipt[9]) && is_array($rowsReceipt[9])) : ?>
										<style>
											div.jsadways-unlock-receipt-effective .chzn-container-multi .chzn-choices .search-choice {
												color: red;
											}
										</style>
										<div class="jsadways-unlock-receipt-effective">
											<select multiple data-rel="chosen" style="width: 300px;">
												<? foreach ($rowsReceipt[9] as $row3) : ?>
													<? if (empty($row3['receipt3id'])) : ?>
														<option value="<?= $row3['id']; ?>" selected style="color: red;"><?= $row3['receipt_number'].'('.$row3['totalprice2'].')-'.$row3['datemonth'].'-[未收款] 未稅['.$row3['totalprice1'].']'; ?></option>
													<? else : ?>
														<?php
															$sql44 = "SELECT * FROM receipt3 WHERE id = {$row3['receipt3id']};";
															$result44 = mysql_query($sql44);
															$row44 = mysql_fetch_array($result44);
														?>
														<? if (empty($row44['times1'])) : ?>
															<option class="jsadways-unlock-receipt-effective" value="<?= $row3['id']; ?>" selected style="color: red;"><?= $row3['receipt_number'].'('.$row3['totalprice2'].')-'.$row3['datemonth'].'-['. (empty($row44['times2']) ? '未收款' : '已收到支票') .'] 未稅['.$row3['totalprice1'].']'; ?></option>
														<? else : ?>
															<option class="jsadways-unlock-receipt-effective" value="<?= $row3['id']; ?>" selected style="color: red;"><?= $row3['receipt_number'].'('.$row3['totalprice2'].')-'.$row3['datemonth'].'-[已收款] 未稅['.$row3['totalprice1'].']'; ?></option>
														<? endif; ?>
													<? endif; ?>
												<? endforeach; ?>
											</select>
										</div>
									<? endif; ?>
										<? if (IsPermitted('finacial', null, Permission::ACL['finacial_unlock_receipt_effective'])) : ?>
											<? if (isset($rowsReceipt[9]) && count($rowsReceipt[9])) : ?>
												<? if (isset($rowsReceipt[9]) && count($rowsReceipt[9])) : ?>
													<a class="btn btn-warning" href="campaign_execute.php?id=<?= $row2['id']; ?>&action=lock_receipt_effective" data-rel="tooltip">
														<i class="fa fa-lock awesome-icon"></i>&nbsp;封印
													</a>
												<? endif; ?>
											<? endif; ?>
										<? endif; ?>
									</div>
								</div>
							</div>
						</div>

						<div class="box span8">
							<div class="box-header well" data-original-title>
								<h2><i class="icon-th"></i> 狀態：<?= getCampaignStatusText($row2['status']); ?></h2>
							</div>

							<div class="box-content">
								<div class="row-fluid" id="action-row">

									<? //echo "row2 status=".$row2['status']."<br/>";
								if (in_array($row2['status'], [1, 5])) : ?>
										<a class="btn btn-info" href="medias_add.php?id=<?= $campaignId; ?>&cue=1">
											<i class="icon-edit icon-white"></i>&nbsp;新增媒體 (對外)
										</a>
									<? endif; ?>
									
									<? if ($isGreaterPMLevel && $row2['idnumber'] && in_array($row2['status'], [3, 4])) : ?>
										<a class="btn btn-success" onclick="export_reports();">
											<i class="fa fa-download awesome-icon"></i>&nbsp;匯出內外CUE表&委刊單版型
										</a>
										<script>
											function export_reports(id)
											{
												window.open('excel/print1.php?id=<?= $campaignId; ?>', '_blank');
												window.open('excel/print1_2.php?id=<?= $campaignId; ?>', '_blank');
												window.open('pdf/print1.php?id=<?= $campaignId; ?>&attachment', '_blank');
											}
										</script>
									<? endif; ?>

									<a class="btn btn-success" href="excel/print1.php?id=<?= $campaignId; ?>" target="_blank">
										<i class="fa fa-download awesome-icon"></i>&nbsp;產出對外CUE表
									</a>

									<a class="btn btn-success" href="excel/print1_3.php?id=<?= $campaignId; ?>" target="_blank">
										<i class="fa fa-download awesome-icon"></i>&nbsp;產出對外CUE表(英)
									</a>

									<? if ($isGreaterPMLevel) : ?>
										<a class="btn btn-success" href="excel/print1_2.php?id=<?= $campaignId; ?>" target="_blank">
											<i class="fa fa-download awesome-icon"></i>&nbsp;產生對內CUE表
										</a>
									<? endif; ?>
									
									<? if (isset($_REQUEST['appoint']) || ($row2['idnumber'] && in_array($row2['status'], [3, 4]))) : ?>
										<a class="btn btn-success" href="campaign_print.php?id=<?= $campaignId; ?>">
											<i class="fa fa-download awesome-icon"></i>&nbsp;產出委刊單
										</a>
									<? endif; ?>

									<? if ($row2['idnumber'] && in_array($row2['status'], [3, 4])) : ?>
										<a class="btn btn-danger" href="campaign_receipt.php?id=<?=  $row2['id']; ?>">
											<i class="fa fa-vcard-o awesome-icon"></i>&nbsp;開發票
										</a>
									<? endif; ?>
									
									<? if (in_array($row2['status'], [1, 5])) : ?>
										<a class="btn btn-danger" href="javascript:ReviewItem();" title="將此活動送往行政部進行送審" data-rel="tooltip">
											<i class="fa fa-search awesome-icon"></i>&nbsp;進行送審
										</a>
										<script>
											var isRequireExceptionMessage = null;
											function ReviewItem()
											{
												if (confirm('確定要送審？')) {
													<? if (empty($row2['others'])) : ?>
														var exceptionComment = null;

														if (isRequireExceptionMessage) {
															exceptionComment = prompt('由於案件金額異常, 須於備註欄位填寫異常原因 (必填)');

															if (!exceptionComment || !exceptionComment.replace(/\s/g, '').length || !exceptionComment.length) {
																return;
															}
														}

														window.location.href = 'campaign_exa.php?id=<?= $row2['id']; ?>&status=2&refer=campaign_view'+ (exceptionComment === null ? '' : ('&exception_comment='+ encodeURIComponent(exceptionComment)));
													<? else : ?>
														window.location.href = 'campaign_exa.php?id=<?= $row2['id']; ?>&status=2&refer=campaign_view';
													<? endif; ?>
												}
											}
										</script>
									<? endif; ?>

									<? if (in_array($row2['status'], [2, 3, 6, 7])) : ?>
										<a class="btn btn-danger"  href="campaign_exa.php?id=<?= $row2['id']; ?>&status=5&refer=campaign_view" onclick="return confirm('確定要暫停？');" title="暫停此活動，之後還可以恢復活動" data-rel="tooltip">
											<i class="fa fa-pause awesome-icon"></i>&nbsp;暫停
										</a>
									<? endif; ?>

									<? if ($isGrantedForCancel) : ?>
										<a id="del_case" style="display:none;" class="btn btn-danger" href="#" onclick="AbortCampaign();" data-rel="tooltip">
											<i class="fa fa-trash-o awesome-icon"></i>&nbsp;案件作廢
										</a>
										<script>
											function AbortCampaign()
											{
												if (confirm('確定要作廢嗎？')) {
													var abortComment = prompt("請輸入原因 (必填)");
													
													if (!abortComment || !abortComment.replace(/\s/g, '').length || !abortComment.length) {
														return;
													}
													
													window.location.href = 'campaign_execute.php?id=<?= $row2['id']; ?>&type=cancel&action=request&abort_comment='+ encodeURIComponent(abortComment);
												}
											}
										</script>
									<? endif; ?>

									<? if ($row2['status'] == 4 && $_SESSION['usergroup'] >= 5) : ?>
										<a class="btn btn-danger" href="campaign_exa.php?id=<?= $row2['id']; ?>&status=5&refer=campaign_view" onclick="return confirm('確定要重跑？');" data-rel="tooltip">
											<i class="fa fa-repeat awesome-icon"></i>&nbsp;重跑案件
										</a>
									<? endif; ?>
										
									<? if ($isGrantedForExceptionApproval) : ?>
										<a class="btn btn-success" href="javascript:ConfirmExceptionItem();" data-rel="tooltip" id="btn-exception-approve" style="display: none;">
											<i class="fa awesome-icon fa-check-circle-o"></i>&nbsp;異常核准
										</a>
										<a class="btn btn-danger" href="javascript:RejectExceptionItem();" data-rel="tooltip" id="btn-exception-reject" style="display: none;">
											<i class="fa awesome-icon fa-times-circle-o"></i>&nbsp;異常不核准
										</a>
										<script>
											function ConfirmExceptionItem()
											{
												if (confirm('確定要核准此異常案件嗎？')) {
													var confirmComment = prompt("請輸入原因 (必填)");
													if (!confirmComment || !confirmComment.replace(/\s/g, '').length || !confirmComment.length) {
														return;
													}

													window.location.href = 'campaign_execute.php?id=<?=  $row2['id']; ?>&type=exception&action=approve&confirm_comment='+ encodeURIComponent(confirmComment);
												}
											}

											function RejectExceptionItem()
											{
												if (confirm('確定不核准此案件嗎？')) {
													var rejectComment = prompt("請輸入原因 (必填)");

													if (!rejectComment || !rejectComment.replace(/\s/g, '').length || !rejectComment.length) {
														return;
													}
													
													window.location.href = 'campaign_execute.php?id=<?=  $row2['id']; ?>&type=exception&action=reject&reject_comment='+ encodeURIComponent(rejectComment);
												}
											}
										</script>
									<? endif; ?>
									
									<? if ($row2['status'] == 3 && $isGreaterPMLevel && $row2['is_receipt'] == 1) : ?>
										<a class="btn btn-danger" href="campaign_execute.php?id=<?= $row2['id']; ?>&action=close" onclick="return chkMoneyForEnd();" title="此活動進行結案" data-rel="tooltip">
											<i class="fa fa-trash-o awesome-icon"></i>&nbsp;結案
										</a>
									<? endif; ?>

									<? if ($row2['status'] == 9 && $isGreaterPMLevel) : ?>
										<a class="btn btn-danger" href="campaign_execute.php?id=<?= $row2['id']; ?>&type=cancel&action=confirm" onclick="return confirm('確定要作廢？');" data-rel="tooltip">
											<i class="fa awesome-icon fa-check-circle-o"></i>&nbsp;核准作廢
										</a>
										<a class="btn btn-success" href="campaign_execute.php?id=<?= $row2['id']; ?>&type=cancel&action=reject" onclick="return confirm('確定不核准作廢？');" data-rel="tooltip">
											<i class="fa awesome-icon fa-times-circle-o"></i>&nbsp;不核准作廢
										</a>
									<? endif; ?>
								</div>
							</div>
						</div>

						<? if ($row2['status'] == 2 && $isGreaterPMLevel) : ?>
							<div class="box span6">
								<div class="box-content">
									<div class="row-fluid">
										<form action="campaign_execute.php?id=<?= $row2['id']; ?>&action=pass" method="post">
											<div>選擇類型(多選)
												<select id="selectError1" multiple data-rel="chosen" name="tagtext[]">
												<? foreach ($rowsClass2 as $row6) : ?>
													<option value="<?= $row6['name']; ?>" <?= strstr($row2['tagtext'], $row6['name']) === false ? '' : 'selected'; ?>><?= $row6['name']; ?></option>
												<? endforeach; ?>
												</select>
											</div>
											<br/>

											<div>
												<table class="table table-bordered bootstrap-datatable ">
													<tr><th>媒體</th><th>品項</th><th style="text-align:left;">對應公司</th></tr>
												<?php 
												//ken,新增審查通過的panel,加一個table可選擇每個品項的公司
												$sqlcp = "SELECT cp.id,cp.media_id, medias.name, cp.item_id, items.name AS item_name
															FROM cp_detail cp
															LEFT JOIN items ON items.id = cp.item_id
															LEFT JOIN medias ON medias.id = cp.media_id
															WHERE cp.cue = '2' and cp.cp_id = ".$row2['id'];
												$dsCp = mysql_query($sqlcp);
												$pos = 0;
												while($drCp = mysql_fetch_array($dsCp)){ ?>
													<tr>
														<td><? echo $drCp['name'] ?><input type='hidden' name='cp[<?= $pos ?>][id]' value='<? echo $drCp['id'] ?>' /><input type='hidden' name='cp[<?= $pos ?>][media_id]' value='<? echo $drCp['media_id'] ?>' /></td>
														<td><? echo $drCp['item_name'] ?><input type='hidden' name='cp[<?= $pos ?>][item_id]' value='<? echo $drCp['item_id'] ?>' /> </td>
														<td style="text-align:left;">
															<select name="cp[<?= $pos ?>][selCompany]">
																<option value=''>請選擇</option>
																<?php
																//ken,每個品項都有提供的公司,必須每個都跑一次迴圈
																$sqlCompany = sprintf("SELECT m.id,c.id as c_id,c.name as c_name 
																	FROM rel_media_companies rmc
																	LEFT JOIN medias m ON rmc.medias_id = m.id
																	left join companies c on c.id = rmc.companies_id 
																	WHERE rmc.medias_id = %d" , $drCp['media_id']);
																$dsCompany = mysql_query($sqlCompany);

																if (mysql_num_rows($dsCompany) > 0) {
																	while ($dr1 = mysql_fetch_array($dsCompany)) {
																		echo '<option value="'.$dr1['c_id'].'">'.$dr1['c_name'].'</option>';
																	}
																}
																?>
															</select>
														</td>
													</tr>
												<? 	$pos=$pos+1; ?>
												<? } ?>
												</table>
											</div>
												
											<input class="btn btn-primary" value="審核通過" type="submit">
										</form>
									</div>
								</div>
							</div>
						<? endif; ?>
						
						<? if ($row2['status'] == 2 && $isGreaterPMLevel) : ?>
							<div class="box span2">
								<div class="box-content">
									<div class="row-fluid">
										<form action="campaign_execute.php?id=<?= $row2['id']; ?>&type=reject" method="post">
											<input name="text" type="text">
											<input class="btn btn-primary" value="審核不過" type="submit">
										</form>
									</div>
								</div>
							</div>
						<? endif; ?>
				
						<? if ($isGreaterPMLevel) : ?>
							<div class="box span3">
								<div class="box-content">
									<div class="row-fluid">
										<form action="campaign_execute.php?id=<?= $row2['id']; ?>&action=update_categories" method="post">
											<select id="selectError2" multiple data-rel="chosen" name="tagtext2[]">
												<? foreach ($rowsClass2 as $row6) : ?>
													<option value="<?= $row6['name']; ?>" <?= strstr($row2['tagtext'], $row6['name']) === false ? '' : 'selected'; ?>><?= $row6['name']; ?></option>
												<? endforeach; ?>
											</select>
											<input class="btn btn-primary" value="修改分類" type="submit">
										</form>
									</div>
								</div>
							</div>
						<? endif; ?>

						<div class="box span5">
							<div class="box-content">
								<div class="row-fluid">
									<form class="form-horizontal" action="campaign_execute.php?id=<?= $row2['id']; ?>&action=update_exchange" method="post">
										外匯調整數：<input id="exchang_math" <?= $isGrantedForFinancial ? '' : 'readonly="readonly"'; ?> class="input-xlarge" type="text" style="width: 100px;" value="<?= $row2['exchang_math']; ?>" name="exchang_math"><br/>
										收款日期：<input id="exchang_time" <?= $isGrantedForFinancial ? '' : 'readonly="readonly"'; ?> class="datepicker" type="text" style="width: 100px;" value="<?= ChangeDateYmdTomdY($row2['exchang_time'], '/'); ?>" name="exchang_time"><br/>
										填寫日期：<input id="write_time" <?= $isGrantedForFinancial ? '' : 'readonly="readonly"'; ?> class="datepicker" type="text" style="width: 100px;" value="<?= ChangeDateYmdTomdY($row2['write_time'], '/'); ?>" name="write_time"><br/>

										<? if ($isGrantedForFinancial) : ?>
											<input class="btn btn-primary" value="修改" type="submit">
										<? endif; ?>
									</form>
								</div>
							</div>
						</div>

						<? if ($isGreaterPMLevel) : ?>
							<div class="box span3" >
								<div class="box-content">
									<div class="row-fluid">
										<form class="form-horizontal" action="campaign_execute.php?id=<?= $row2['id']; ?>&action=add_media_cost_income" method="post">
											<select id="media_chang_name" name="media_chang_name" style="list-style-type: none;">
												<? foreach ($listMediaChangeItem as $listKey => $listText) : ?>
													<option value="<?= $listKey; ?>"><?= $listText; ?></option>
												<? endforeach; ?>
											</select>
											
											<p style="margin: 3px 0px;">收入：<input id="media_chang_income" required="required" class="input-xlarge" type="text" style="width: 100px;" value="" name="media_chang_income"></p>
											<p style="margin: 3px 0px;">成本：<input id="media_chang_cost" required="required" class="input-xlarge" type="text" style="width: 100px;" value="" name="media_chang_cost"></p>
											<p style="margin: 3px 0px;">調整日期：<input id="media_chang_time" required="required" class="datepicker" type="text" style="width: 100px;" value="" name="media_chang_time"></p>
											<p style="margin: 3px 0px;">備註：<input id="media_chang_note" required="required" type="text" style="width: 100px;"value="" name="media_chang_note"></p>
											<p style="margin: 6px 0px;"><input class="btn btn-primary" value="送出" type="submit"></p>
										</form>
									</div>
								</div>
							</div>
							
							<?php
								$sqlMediaChange = "SELECT * FROM media_change WHERE campaign_id = $campaignId ORDER BY id ASC;";
								$db->query($sqlMediaChange);
								$displayMediaChange = $db->get_num_rows();
							?>
							<div class="box span5" style="<?= $displayMediaChange ? '' : 'display: none;'; ?>">
								<div class="box-content">
									<div class="row-fluid">
										<table class="table table-bordered bootstrap-datatable ">
											<thead>
												<tr>
													<th>媒體</th>
													<th>調整收入</th>
													<th>調整成本</th>
													<th>日期</th>
													<th>備註</th>
												</tr>
											</thead>
											<tbody>
												<? while ($row_change = $db->next_record()) : ?>
													<tr>
														<td><?= $row_change["media_id"]; ?>"-"<?= $row_change["media_sn"]; ?>" "<?= $row_change["media_name"]; ?></td>
														<td><?= $row_change["change_income"]; ?></td>
														<td><?= $row_change["change_cost"]; ?></td>
														<td><?= $row_change["change_date"]; ?></td>
														<td><?= $row_change["note"]; ?></td>
													</tr>
												<? endwhile; ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						<? endif; ?>
					</div>

					<?php require_once __DIR__ .'/campaign_view_published.php'; ?>

					<div class="row-fluid">
						<div class="box span4">
							<div class="box-header well" data-original-title>
								<h2><i class="icon-th"></i> 對外CUE表</h2>
							</div>

							<div class="box-content">
								<table class="table table-bordered bootstrap-datatable ">
									<thead>
										<tr>
											<th>媒體別</th>
											<th>賣給客戶的總價</th>
											<th>總數量</th>
											<th>編號</th>
										</tr>
									</thead>
									<tbody>
										<?php
											// echo "rowsOrdinal <br/>";
											// 	print_r($rowsOrdinal);
											// 	echo "<br/>rowsOrdinal <br/>";
											foreach ($rowsOrdinal as $idxOrdinal) {
												// echo "idxOrdinal <br/>";
												// print_r($idxOrdinal);
												// echo "<br/>idxOrdinal <br/>";
												$row4 = GetMedia($idxOrdinal);
												$sql3 = "SELECT * FROM media$idxOrdinal WHERE campaign_id = $campaignId AND cue = 1 ORDER BY id ASC;";
												// echo "row4 <br/>";
												// print_r($row4);
												// echo "<br/>row4 <br/>";
												$result3 = mysql_query($sql3);
												if (mysql_num_rows($result3) > 0) {
													while ($row3 = mysql_fetch_array($result3)) {
														?>
															<tr bgcolor="#<?= $bgcolor[$row3['id'] % 33]; ?>">
																<td style="text-align: left;">
																	<?= $row4['name']; ?>
																	<?= in_array($idxOrdinal, $parameter['extra_media_id']) ? ($row3['media_type'] .'-'. $row3['website']) : '';?>
																</td>
																<td>$<?= number_format(empty($row3['totalprice'])?"0":$row3['totalprice']); ?></td>
																<td>
																	<? if ((in_array($idxOrdinal, $parameter['extra_media_id']) && $row3['media_type'] == 'CPM') || in_array($idxOrdinal, $parameter['cpm_media_list'])) : ?>
																		<?= number_format(empty($row3['quantity2'])?"0":$row3['quantity2']); ?>
																	<? else : ?>
																		<?= $row3['quantity'] ? number_format($row3['quantity']) : ''; ?>
																	<? endif; ?>
																</td>
																<td><?= $idxOrdinal; ?>-<?= $row3['id']; ?></td>
															</tr>
														<?php
													}
												}
											}
										
											$extCueTotalPrice = round($extCueTotalPrice);
										?>
										<tr>
											<td>Total</td>
											<td>$<?= number_format($extCueTotalPrice); ?></td>
											<td><?= number_format($extCueQuantity); ?></td>
											<td></td>
										</tr>
									</tbody>
								</table>
								<h4>含稅金額(不會匯出)</h4>
								<input type="text" readonly="true" id="out_cue_total" style="width: 290px;" value="<?= round($extCueTotalPrice * ($GLOBALS['env']['flag']['pos'] == 'hk' ? 1 : 1.05)); ?>">
							</div>
						</div>

						<div class="box span8">
							<div class="box-header well" data-original-title>
								<h2><i class="icon-th"></i> 對內CUE表</h2>
							</div>

							<div class="box-content">
								<table class="table table-bordered bootstrap-datatable ">
									<thead>
										<tr>
											<th>編號</th>
											<th>媒體別</th>
											<th>Total</th>
											<th>佣金</th>
											<th>現折</th>
											<th>預扣</th>
											<th>利潤</th>
											<th>操作預算</th>
											<th>總數量</th>
											<th>狀態</th>
											<? if ($isGreaterPMLevel) : ?>
												<th>狀態變更</th>
											<? endif; ?>
										</tr>
									</thead>
									<tbody>
										<?php
											$Ary_end = [];	//2014 12 26 財務關已填成本及結案的媒體
											foreach ($rowsOrdinal as $extCueOrdinalIdx) {
												$sql31 = "SELECT * FROM media$extCueOrdinalIdx WHERE campaign_id = $campaignId AND cue = 1 ORDER BY id DESC;";
												$result31 = mysql_query($sql31);

												if (mysql_num_rows($result31) > 0) {
													while ($row31 = mysql_fetch_array($result31)) {
														foreach ($rowsOrdinal as $i) {
															$row4 = GetMedia($i);
															$sql3 = "SELECT * FROM media$i WHERE campaign_id = $campaignId AND cue = 2 ORDER BY id DESC;";

															$result3 = mysql_query($sql3);
															if (mysql_num_rows($result3) > 0) {
																while ($row3 = mysql_fetch_array($result3)) {
																	if ($row31['id'] == $row3['a0'] && $row3['a'] == $extCueOrdinalIdx) {
																		$a1t += $row3['a1'];
																		$a2t += $row3['a2'];
																		$a3 += $row3['a3'];
																		$a4 += $row3['a4'];

																		if(is_null($row3['a3']) || empty($row3['a4'])){
																			$row3['a4'] = 0;
																		}
																		if(is_null($row3['a4']) || empty($row3['a4'])){
																			$row3['a4'] = 0;
																		}
																		//額外媒體判斷 
																		if (in_array($i, $parameter['extra_media_id'])) {
																			if ($row3['media_type'] == 'CPM') {		//CPM顯示為 曝光數
																				$intCueQuantity += $row3['quantity2'];
																			} else {
																				$intCueQuantity += $row3['quantity'];
																			}
																		} else {
																			if (in_array($i, $parameter['cpm_media_list'])) {
																				//CPM顯示為 曝光數
																				$intCueQuantity += $row3['quantity2'];
																			} else {
																				$intCueQuantity += (empty($row3['quantity']) ? $row3['click1'] : $row3['quantity'] );
																			}
																		}
																		
																		if ($row3['status'] == 3) {
																			//2014 12 26 財務關已填成本及結案的媒體
																			$Ary_end[] = $row3['a'] ."-". $row3['a0'];
																		}

																		?>
																			<tr bgcolor="#<?= $bgcolor[$row3['a0']%33]; ?>">
																				<td><?= $row3['a']; ?>-<?= $row3['a0']; ?></td>
																				<td style="text-align: left;">
																					<?= $row4['name']; ?>
																					<?= in_array($i, $parameter['extra_media_id']) ? ($row3['media_type'] .'-'. $row3['website']) : '';?>
																					<?= $row3['itemname'] ? "-{$row3['itemname']}" : ''; ?>
																					<?= $row3['items'] ? "-{$row3['items']}" : ''; ?>
																					<?= in_array($i, $parameter['extra_fee_media']) && $row3['channel'] ? " ({$row3['channel']})" : '';?>
																				</td>
																				<?php
																					$abc = '否';
																					$a1 = $row3['a1'];
																					$a2 = $row3['a2'];
																					if ($row2['agency_id'] != 0) {
																						$sql5 = "SELECT * FROM commission WHERE media = $i AND agency = {$row2['agency_id']};";
																						$result5 = mysql_query($sql5);
																						$row5 = mysql_fetch_array($result5);
																						
																						if ($row5['commission5'] == 1) {
																							$abc = '是';
																						} else {
																							$a1 = (($row3['a1'] + $row3['a2'] + $row3['a3'] + $row3['a4']) * $row5['commission1']) / 100;
																							$a2 = (($row3['a1'] + $row3['a2'] + $row3['a3'] + $row3['a4']) * $row5['commission4']) / 100;
																						}
																					}

																					$intCueTotalPrice += $row3['totalprice'];
																				?>
																				<td>$<?= in_array($i, $parameter['direct_income']) ? number_format($row3['totalprice']) : number_format($row3['a1'] + $row3['a2'] + $row3['a3'] + $row3['a4']); ?></td>
																				<td><!-- 佣金-->$<?= number_format($a1); ?></td> 
																				<td><!-- 現折-->$<?= number_format($a2); ?></td> 
																				<td><?= $abc; ?></td>
																				<td><!-- 利潤-->$<?= number_format($row3['a3']); ?></td>
																				<td><!-- 預算-->$<?= number_format($row3['a4']); ?></td>
																				<td>
																					<? if ((in_array($i, $parameter['extra_media_id']) && $row3['media_type'] == 'CPM') || in_array($i, $parameter['cpm_media_list'])) : ?>
																						<?= number_format(empty($row3['quantity2']) ? "0" : $row3['quantity2']); ?>
																					<? elseif (in_array($i, $parameter['extra_media_id']) || $row3['quantity']) : ?>
																						<?= number_format(empty($row3['quantity']) ? "0" : $row3['quantity']); ?>
																					<? else : ?>
																						<?= number_format(empty($row3['click1']) ? "0" : $row3['click1']); ?>
																					<? endif; ?>
																				</td>
																				<td><!-- 狀態--></td>

																				<? if ($isGreaterPMLevel) : ?>
																					<td id="internal-media-item-<?= $i; ?>-<?= $row3['id']; ?>">
																						<?php
																							$mediaItemStatus[$i][$row3['id']] = $row3['status'];
																						?>
																						<div id="media_status_action"></div>
																						<i id="item_loading" class="fa fa-repeat fa-spin" style="font-size: 2em; display: none;"></i>
																						<div id="item_success" style="display: none; color: green;">
																							<i class="fa fa-check-circle-o" style="font-size: 2em;"></i><br/>
																							<b>變更成功</b>
																						</div>
																					</td>
																				<? endif; ?>
																			</tr>
																		<?php
																	}
																}
															}
														}
													}
												}
											}

											$intCueTotalPrice = round($intCueTotalPrice);
										?>
										<tr>
											<td></td>
											<td>Total</td>
											<td>$<?= number_format($intCueTotalPrice); ?></td>
											<td>$<?= number_format($a1t); ?></td>
											<td>$<?= number_format($a2t); ?></td>
											<td>預扣</td>
											<td>$<?= number_format($a3); ?></td>
											<td>$<?= number_format($a4); ?></td>
											<td><?= number_format($intCueQuantity); ?></td>
										</tr>
										<tr bgcolor="#A9C68E">
											<td colspan="4">已排金額：<?= number_format($intCueTotalPrice); ?></td>
											<td colspan="4">尚未排金額：<?= number_format($extCueTotalPrice - $intCueTotalPrice); ?></td>
											<? if ($isGrantedForFinancial) : ?>
												<td colspan="4" style="color: blue; font-weight: bold;">媒體總收入：<span id="total_income"></span><br/>媒體總成本：<span id="total_cost"></span></td>
											<? endif; ?>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<? if ($extCueTotalPrice != $intCueTotalPrice) : ?>
						<?php $isRequireExceptionMessage = true; ?>
						<div class="alert alert-error">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<strong>警告!</strong> 對外CUE表金額($<?= number_format($extCueTotalPrice); ?>)及對內CUE表金額($<?= number_format($intCueTotalPrice); ?>)不符
						</div>
					<? endif; ?>

					<? if ($pr == 1) : ?>
						<div class="alert alert-error">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<strong>警告!</strong> 此案件有PR或有總金額為0的媒體，送審需先經過主管同意
						</div>
					<? endif; ?>
			
					<? if ($a5 == 1) : ?>
						<div class="alert alert-error">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<strong>警告!</strong> 有媒體利潤小於系統預設值，送審需先經過主管同意
						</div>
					<? endif; ?>

					<?php
						$deprice = 0;
						$deprice2 = 0;
						$deprice3 = 0;
						foreach ($rowsOrdinal as $i) {
							$sql3 = "SELECT * FROM media$i WHERE campaign_id = $campaignId;";
							$result3 = mysql_query($sql3);
							if (mysql_num_rows($result3) > 0) {
								while ($row3 = mysql_fetch_array($result3)) {
									if ($i == 72) {
										if ($row3["position"] == "回饋獎金型-i-CPM") {
											if (($row3["price1"] < 85 && $row3["price1"] >0) ||($row3["price2"] < 85&& $row3["price2"] >0)||($row3["price3"] < 85&& $row3["price3"] >0)||($row3["price4"] < 85&& $row3["price4"] >0)||($row3["price5"] < 85&& $row3["price5"] >0)) {
												$deprice = 1;
											}
										}

										if ($row3["position"] == "非回饋獎金型-CPM") {
											if (($row3["price1"] < 100 && $row3["price1"] >0) ||($row3["price2"] < 100 && $row3["price2"] >0)||($row3["price3"] < 100 && $row3["price3"] >0)||($row3["price4"] < 100 && $row3["price4"] >0)||($row3["price5"] < 100 && $row3["price5"] >0)) {
												$deprice = 1;
											}
										}
									}

									if ($i == 73) {
										if ($row3["position"] == "回饋獎金型-i-CPC") {
											if (($row3["price1"] < 3 && $row3["price1"] >0) ||($row3["price2"] < 3&& $row3["price2"] >0)||($row3["price3"] < 3&& $row3["price3"] >0)||($row3["price4"] < 3&& $row3["price4"] >0)||($row3["price5"] < 3&& $row3["price5"] >0)) {
												$deprice = 1;
											}
										}

										if ($row3["position"] == "非回饋獎金型-CPC") {
											if (($row3["price1"] < 7.5 && $row3["price1"] >0) ||($row3["price2"] < 7.5 && $row3["price2"] >0)||($row3["price3"] < 7.5 && $row3["price3"] >0)||($row3["price4"] < 7.5 && $row3["price4"] >0)||($row3["price5"] < 7.5 && $row3["price5"] >0)) {
												$deprice = 1;
											}
										}
									}

									if ($i == 74) {
										if ($row3["position"] == "回饋獎金型-CPE") {
											if (($row3["price1"] < 5 && $row3["price1"] >0) ||($row3["price2"] < 5&& $row3["price2"] >0)||($row3["price3"] < 5&& $row3["price3"] >0)||($row3["price4"] < 5&& $row3["price4"] >0)||($row3["price5"] < 5&& $row3["price5"] >0)) {
												$deprice = 1;
											}
										}

										if ($row3["position"] == "回饋獎金型-CPY") {
											if (($row3["price1"] < 4.5 && $row3["price1"] >0) ||($row3["price2"] < 4.5 && $row3["price2"] >0)||($row3["price3"] < 4.5 && $row3["price3"] >0)||($row3["price4"] < 4.5 && $row3["price4"] >0)||($row3["price5"] < 4.5 && $row3["price5"] >0)) {
												$deprice = 1;
											}
										}

										if ($row3["position"] == "回饋獎金型-CPB" || $row3["position"] == "回饋獎金型-CPL") {
											if (($row3["price1"] < 6 && $row3["price1"] >0) ||($row3["price2"] < 6 && $row3["price2"] >0)||($row3["price3"] < 6 && $row3["price3"] >0)||($row3["price4"] < 6 && $row3["price4"] >0)||($row3["price5"] < 6 && $row3["price5"] >0)) {
												$deprice = 1;
											}
										}

										if ($row3["position"] == "回饋獎金型-CPY+B") {
											if (($row3["price1"] < 8 && $row3["price1"] >0) ||($row3["price2"] < 8 && $row3["price2"] >0)||($row3["price3"] < 8 && $row3["price3"] >0)||($row3["price4"] < 8 && $row3["price4"] >0)||($row3["price5"] < 8 && $row3["price5"] >0)) {
												$deprice = 1;
											}
										}
									}

									if ($i == 87 ) {
										if ($row3["price1"] != '') {
											if ($row3["price1"] < 180 || $row3["price1"] > 250 ) {						
													$deprice2 = 2;
													
											}
										}

										if ($row3["price2"] != '') {
											if ($row3["price2"] < 180 || $row3["price2"] > 250 ) {						
													$deprice2 = 2;
											}
										}

										if ($row3["price3"] != '') {
											if ($row3["price3"] < 180 || $row3["price3"] > 250 ) {						
													$deprice2 = 2;
											}
										}

										if ($row3["price4"] != '') {
											if ($row3["price4"] < 180 || $row3["price4"] > 250 ) {						
													$deprice2 = 2;
											}
										}

										if ($row3["price5"] != '') {
											if ($row3["price5"] < 180 || $row3["price5"] > 250 ) {						
													$deprice2 = 2;
											}
										}
									}

									if ($i == 125) {
										if ($row3["position"] == "小豬啦啦隊-粉絲團按讚CPF") {
											if (($row3["price1"] < 2.5 && $row3["price1"] >0) ||($row3["price2"] < 2.5&& $row3["price2"] >0)||($row3["price3"] < 2.5&& $row3["price3"] >0)||($row3["price4"] < 2.5&& $row3["price4"] >0)||($row3["price5"] < 2.5&& $row3["price5"] >0)) {
												$deprice3 = 3;
											}
										}

										if ($row3["position"] == "口碑培養皿-文章分享CPS") {
											if (($row3["price1"] < 7.5 && $row3["price1"] >0) ||($row3["price2"] < 7.5  && $row3["price2"] >0)||($row3["price3"] < 7.5  && $row3["price3"] >0)||($row3["price4"] < 7.5  && $row3["price4"] >0)||($row3["price5"] < 7.5  && $row3["price5"] >0)) {
												$deprice3 = 3;
											}
										}

										if ($row3["position"] == "小豬特派員-心得撰寫CPA") {
											if (($row3["price1"] < 150 && $row3["price1"] >0) ||($row3["price2"] < 150  && $row3["price2"] >0)||($row3["price3"] < 150  && $row3["price3"] >0)||($row3["price4"] < 150  && $row3["price4"] >0)||($row3["price5"] < 150  && $row3["price5"] >0)) {
												$deprice3 = 3;
											}
										}
									}
								}
							}
						}
					?>
					<? if ($deprice == 1) : ?>
						<div class="alert alert-error">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<strong>警告!</strong> 此案件Honeyscreen媒體有定價低於基本預設，送審需先經過主管同意
						</div>
					<? endif; ?>

					<? if ($deprice2 == 2) : ?>
						<div class="alert alert-error">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<strong>警告!</strong> 此案件雪豹科技 CPM 媒體有定價有不合標準，送審需先經過主管同意
						</div>
					<? endif; ?>

					<? if ($deprice3 == 3) : ?>
						<div class="alert alert-error">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<strong>警告!</strong> 此案件錢包小豬(任務型) 媒體有定價有不合標準，送審需先經過主管同意
						</div>
					<? endif; ?>

					<style>
						#external-cue-head::-webkit-scrollbar {
							height: 0px;
						}

						#external-cue-head {
							height: 38px;
							width: inherit;
							overflow-x: auto;
							overflow-y: hidden;
							white-space: nowrap;
						}

						#internal-cue-head::-webkit-scrollbar {
							height: 0px;
						}

						#internal-cue-head {
							height: 38px;
							width: inherit;
							overflow-x: auto;
							overflow-y: hidden;
							white-space: nowrap;
						}

						.internal-cue-tab, .external-cue-tab {
							display: inline-block;
							border-top-left-radius: 5px;
							border-top-right-radius: 5px;
							border: 1px solid #ddd;
							margin-right: 3px;
							cursor: pointer;
						}

						.internal-cue-tab-active, .external-cue-tab-active {
							background: white;
							border-bottom: 2px solid white;
						}

						.internal-cue-tab-inactive, .external-cue-tab-inactive {
							background: linear-gradient(to bottom, rgba(255, 255, 255, 1) 0%, rgba(222, 222, 222, 1) 100%);
						}

						.internal-cue-tab-inactive:hover, .external-cue-tab-inactive:hover {
							background: linear-gradient(to bottom, rgba(255, 255, 255, 0) 0%, rgba(0, 0, 0, 0.5) 100%);
						}
					</style>
					<script>
						function AddTab(mediaOrdinal, mediaName, mediaType)
						{
							if (mediaType == 'internal') {
								$('<div id="internal_media'+ mediaOrdinal +'" class="box-header well internal-cue-tab-inactive internal-cue-tab" onclick="OpenTab('+ mediaOrdinal +', \'internal\');"><h2><i class="fa fa-linode"></i>&nbsp;'+ mediaName  +'</h2></div>').insertBefore('div#internal-cue-head span#internal-last-padding');
							} else if (mediaType == 'external') {
								$('<div id="external_media'+ mediaOrdinal +'" class="box-header well external-cue-tab-inactive external-cue-tab" onclick="OpenTab('+ mediaOrdinal +', \'external\');"><h2><i class="fa fa-linode"></i>&nbsp;'+ mediaName  +'</h2></div>').insertBefore('div#external-cue-head span#external-last-padding');
							} 
						}

						var tabShift = 15;
						var tabSlider = null;
						var prevTabPosition = null;

						function SlideTab(position, item)
						{
							if (position > 0) {
								if (tabSlider === null) {
									tabSlider = setInterval(function() {
										if (prevTabPosition === $('#'+ item +'-cue-head').scrollLeft() || $('#'+ item +'-cue-head').scrollLeft() <= 0) {
											SlideTab(0);
										} else {
											console.log('stop right');
											prevTabPosition = $('#'+ item +'-cue-head').scrollLeft();
											$('#'+ item +'-cue-head').scrollLeft($('#'+ item +'-cue-head').scrollLeft() - tabShift);
										}
									}, 20);
								}
							} else if (position < 0) {
								if (tabSlider === null) {
									tabSlider = setInterval(function() {
										if (prevTabPosition === $('#'+ item +'-cue-head').scrollLeft()) {
											SlideTab(0);
										} else {
											console.log('stop left');
											prevTabPosition = $('#'+ item +'-cue-head').scrollLeft();
											$('#'+ item +'-cue-head').scrollLeft($('#'+ item +'-cue-head').scrollLeft() + tabShift);
										}
									}, 20);
								}
							} else {
								console.log('stop slide');
								clearInterval(tabSlider);
								tabSlider = null;
								prevTabPosition = null;
							}
						}

						function ToggleSlider()
						{
							var rowWidth = $('div#external-cue-head').width();
							var externalContentWidth = 0;
							$('div#external-cue-head').find('div.external-cue-tab').each(function() {
								externalContentWidth += $(this).outerWidth() + 3;
							});

							if (externalContentWidth > rowWidth) {
								$('.external-slide-element').show();
							} else {
								$('.external-slide-element').hide();
							}


							rowWidth = $('div#internal-cue-head').width();
							var internalContentWidth = 0;
							$('div#internal-cue-head').find('div.internal-cue-tab').each(function() {
								internalContentWidth += $(this).outerWidth() + 3;
							});

							if (internalContentWidth > rowWidth) {
								$('.internal-slide-element').show();
							} else {
								$('.internal-slide-element').hide();
							}
						}

						function OpenTab(mediaOrdinal, mediaType)
						{
							if (mediaType == 'internal') {
								$('div.internal-cue-body').hide();
								$('div.internal-cue-body#internal-cue-media-'+ mediaOrdinal).show();
								$('div.internal-cue-tab').removeClass('internal-cue-tab-active').addClass('internal-cue-tab-inactive');
								$('div.internal-cue-tab#internal_media'+ mediaOrdinal).removeClass('internal-cue-tab-inactive').addClass('internal-cue-tab-active');
							} else if (mediaType == 'external') {
								$('div.external-cue-body').hide();
								$('div.external-cue-body#external-cue-media-'+ mediaOrdinal).show();
								$('div.external-cue-tab').removeClass('external-cue-tab-active').addClass('external-cue-tab-inactive');
								$('div.external-cue-tab#external_media'+ mediaOrdinal).removeClass('external-cue-tab-inactive').addClass('external-cue-tab-active');
							}
						}

						function SaveAccounting(campaignId, mediaOrdinal, mediaItem, accountingMonth)
						{
							var rowId = 'row_accounting_'+ campaignId +'_'+ mediaOrdinal +'_'+ mediaItem +'_'+ accountingMonth;

							//ken,要往後傳參數
							if ($('#'+ rowId).length) {
								var postData = {
									accounting_campaign: campaignId,
									accounting_media_ordinal: mediaOrdinal,
									accounting_media_item: mediaItem,
									accounting_month: accountingMonth,
									accounting_revenue: $('#'+ rowId).find('#accounting_revenue').val(),
									curr_cost: $('#'+ rowId).find('#curr_cost').val(),
									currency_id: $('#'+ rowId).find('#currency_id').val(),
									accounting_cost: $('#'+ rowId).find('#accounting_cost').val(),
									invoice_number: $('#'+ rowId).find('#invoice_number').val(),
									invoice_date: $('#'+ rowId).find('#invoice_date').val(),
									accounting_comment: $('textarea#accounting_comment_'+ campaignId +'_'+ mediaOrdinal +'_'+ mediaItem).val()
								}

								$.ajax({
									url: 'campaign_execute.php?ajax&action=save_media_accounting&id='+ campaignId,
									type: 'POST',
									data: postData,
									beforeSend: function() {
										$('#'+ rowId).find('#item_save').hide();
										$('#'+ rowId).find('#item_success').hide();
										$('#'+ rowId).find('#item_loading').show();
									},
									success: function(feedback) {
										if (typeof feedback == 'object' && feedback.success == 1) {
											setTimeout(function() {
												LoadCampaignHistory();

												if (typeof feedback == 'object' && 'data' in feedback) {
													for (var idx in feedback.data) {
														//$('#'+ rowId).find('#'+ idx).val(feedback.data[idx]);
														$('#'+ rowId).find('#'+ idx).text(feedback.data[idx]);//ken,改成text
													}
												}

												$('#'+ rowId).find('#item_save').hide();
												$('#'+ rowId).find('#item_success').show();
												$('#'+ rowId).find('#item_loading').hide();
												ValidateBloggerToatlCost();

												setTimeout(function() {
													$('#'+ rowId).find('#item_save').show();
													$('#'+ rowId).find('#item_success').hide();
													$('#'+ rowId).find('#item_loading').hide();
												}, 1000);
											}, 1000);
										} else if ('message' in feedback && feedback.message && feedback.message.length) {
											alert(feedback.message);
											$('#'+ rowId).find('#item_save').show();
											$('#'+ rowId).find('#item_success').hide();
											$('#'+ rowId).find('#item_loading').hide();
											return;
										} else {
											alert('發生錯誤');
											$('#'+ rowId).find('#item_save').show();
											$('#'+ rowId).find('#item_success').hide();
											$('#'+ rowId).find('#item_loading').hide();
										}
									},
									error: function() {
										alert('error');
										$('#'+ rowId).find('#item_save').show();
										$('#'+ rowId).find('#item_success').hide();
										$('#'+ rowId).find('#item_loading').hide();
									}
								});
							}
						}

						function ToggleAccountingDetail(toggler)
						{
							if ($(toggler).data('tag') == 'up') {
								$(toggler).data('tag', 'down');
								$(toggler).find('span').hide();
								$(toggler).find('span#down').show();
								$(toggler).parent().prev().hide();
							} else if ($(toggler).data('tag') == 'down') {
								$(toggler).data('tag', 'up');
								$(toggler).find('span').hide();
								$(toggler).find('span#up').show();
								$(toggler).parent().prev().show();
							}
						}
					</script>
					<br/>
					<div class="progress progress-danger progress-striped" style="margin-bottom: 9px;">
						<div class="bar" style="width: 100%">對外CUE表</div>
					</div>
					<div class="row-fluid external-cue-head">
						<div id="external-cue-head">
							<a class="box-header well external-slide-element external-cue-tab external-cue-tab-active" onmouseover="SlideTab(1, 'external');" onmouseout="SlideTab(0);" style="padding-left: 8px; padding-right: 8px; position: absolute; display: none;"><h2 style="padding-top: 2px;"><i class="fa fa-chevron-left"></i></h2></a>
							<a class="box-header well external-slide-element external-cue-tab external-cue-tab-active" onmouseover="SlideTab(-1, 'external');" onmouseout="SlideTab(0);" style="padding-left: 8px; padding-right: 8px; position: absolute; display: none; right: 17px;"><h2 style="padding-top: 2px;"><i class="fa fa-chevron-right"></i></h2></a>
							<span id="external-first-padding" class="external-slide-element" style="display: none;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
							<span id="external-last-padding" class="external-slide-element" style="display: none;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
						</div>
					</div>

					<?php 
						foreach ($rowsOrdinal as $i) {
							$row4 = GetMedia($i);
							$result = mysql_query('SELECT * FROM media'.$i.' WHERE campaign_id = '.$campaignId.' AND cue=1 ORDER BY id');
							$total = mysql_num_rows($result);
							if ($total > 0) {
								?>
									<script>AddTab(<?= $i; ?>, "<?= addslashes($row4['name']); ?><?= $row4['costper'] ? addslashes(" ({$row4['costper']})") : ''; ?>", 'external');</script>
									<div class="row-fluid external-cue-body" id="external-cue-media-<?= $i; ?>" style="margin-top: -1px;">
										<div class="box span12" style="margin-top: 0px; border-top-left-radius: 0px; border-top-right-radius: 0px;">
											<div class="box-content">
												<table class="table table-striped table-bordered bootstrap-datatable ">
													<thead>
														<tr>
															<th>流水號</th>
															<th>編號</th>
															<th>網站</th>
															<? if (in_array($i, $parameter['detail_head_simple_media'])) : ?>
															<? elseif (in_array($row4['typename'], $parameter['detail_head_reverve_type'])) : ?>
																<th>類別</th>
																<th>廣告類型</th>
																<th>項目</th>
															<? else : ?>
																<th>手機系統</th>
																<th>廣告類型</th>
															<? endif; ?>
															<? if (!in_array($i, $parameter['detail_head_simple_media'])) : ?>
																<th>版位</th>
															<? endif; ?>
															<th>刊登日期</th>
															<th>天數</th>
															<th>定價</th>
															<? if (!in_array($i, $parameter['detail_head_simple_media'])) : ?>
																<th>數量</th>
															<? endif; ?>
															<th>總價</th>
															<th style="width: 200px;">Action</th>
														</tr>
													</thead>
												<tbody>
												<?php
														$total = 0;
														$sql3 = 'SELECT * FROM media'. $i .' WHERE campaign_id = '. $campaignId .' AND cue = 1 ORDER BY id ASC;';
														$result3 = mysql_query($sql3);
														if (mysql_num_rows($result3) > 0) {
															while ($row3 = mysql_fetch_array($result3)) {
																$total += $row3['totalprice'];
																?>
																	<tr>
																		<td>
																			<!-- 流水號 -->
																			<?=$row3['item_seq']; ?> </td>
																		<td>
																			<!-- 編號 -->
																			<?= $i .'-'. $row3['id']; ?>
																		</td>
																		<td title="<?= $row3['others']; ?>">
																			<!-- 網站 -->
																			<?= in_array($i, $parameter['extra_media_id']) ? ($row3['media_type'].'-'.$row3['website']) : $row3['website']; ?>
																		</td>
																		<? if (!in_array($i, $parameter['detail_head_simple_media'])) : ?>
																			<? if (in_array($row4['typename'], $parameter['detail_head_reverve_type'])) : ?>
																				<td>
																					<!-- 類別 -->
																					<?= $row3['taget']; ?>
																				</td>
																			<? endif; ?>
																			<td>
																				<!-- 手機系統 -->
																				<?= in_array($row4['typename'], $parameter['detail_head_reverve_type']) ? $row3['channel'] : (empty($row3['phonesystem']) ? $row3['items3'] : $row3['phonesystem']); ?>
																			</td>
																			<td>
																				<!-- 廣告類型 -->
																				<?= in_array($row4['typename'], $parameter['detail_head_reverve_type']) ? $row3['phonesystem'] : $row3['channel']; ?>
																			</td>
																			<td>
																				<!-- 版位 -->
																				<?= nl2br($row3['position']); ?>
																			</td>
																		<? endif; ?>
																		<td nowrap>
																			<!-- 刊登日期 -->
																			<?php
																				for ($idx=1; $idx<=9; $idx=$idx+2) {
																					if (isset($row3['date'. $idx]) && $row3['date'. $idx]) {
																						echo date('Y/m/d', $row3['date'. $idx]).' ~ '.date('Y/m/d', $row3['date'. ($idx + 1)]) .'<br/>';
																					}
																				}
																			?>
																		</td>
																		<td>
																			<!-- 天數 -->
																			<?php
																			
																				$totalDAY = 0;
																				for ($idx=1; $idx<=5; $idx++) {
																					if (isset($row3['days'. $idx]) && $row3['days'. $idx]) {
																						echo $row3['days'. $idx] .'<br/>';
																						$totalDAY++;
																					}
																				}

																				if (isset($row3['days']) && $row3['days'] && empty($totalDAY)) {
																					echo $row3['days'];
																				}
																			?>
																		</td>
																		<td>
																			<!-- 定價 -->
																			<?php
																				if (isset($row3['price']) && $row3['price']) {
																					echo '$'. number_format(round($row3['price'] * 100) / 100, 2); 
																				} else {
																					for ($idx=1; $idx<=5; $idx++) {
																						if (isset($row3['price'. $idx]) && $row3['price'. $idx]) {
																							echo '$'. number_format(round($row3['price'. $idx] * 100) / 100, 2) .'<br/>';
																						}
																					}
																				}
																			?>
																		</td>
																		<? if (!in_array($i, $parameter['detail_head_simple_media'])) : ?>
																			<td>
																				<!-- 數量 -->
																				<?php
																					$totalQTY = 0;
																					if (in_array($i, $parameter['extra_media_id']) && $row3['media_type'] == 'CPM') {
																						echo number_format($row3['quantity2']);
																					} else if (in_array($i, $parameter['cpm_media_list'])) {		//CPM
																						for ($idx=1; $idx<=5; $idx++) {
																							if (isset($row3['impression'. $idx]) && $row3['impression'. $idx]) {
																								echo number_format($row3['impression'. $idx]) .'<br/>';
																								$totalQTY++;
																							}
																						}
																					} else if (in_array($i, [36, 47, 54])) {
																						echo number_format($row3['quantity']);
																					} else if ($i == 45) {
																						//顯示為總曝光的媒體
																						echo number_format($row3['quantity2']);
																					} else {
																						for ($idx=1; $idx<=5; $idx++) {
																							if (isset($row3['click'. $idx]) && $row3['click'. $idx]) {
																								echo number_format($row3['click'. $idx]) .'<br/>';
																								$totalQTY++;
																							}
																						}

																						if (isset($row3['quantity']) && $row3['quantity'] && empty($totalQTY)) {
																							echo number_format($row3['quantity']);
																						}
																					}
																				?>
																			</td>
																		<? endif; ?>
																		<td>
																			<!-- 總價 -->
																			<?php
																				if ((in_array($i, $parameter['extra_media_id']) && $row3['media_type'] != 'CPM') || in_array($i, [19, 36, 47, 54])) {
																					echo '$'. number_format($row3['totalprice']);
																				} else {
																					$money = 0;
																					for ($idx=1; $idx<=5; $idx++) {
																						if (isset($row3['totalprice'. $idx]) && $row3['totalprice'. $idx]) {
																							echo '$'. number_format($row3['totalprice'. $idx]) .'<br/>';
																							$money++;
																						}
																					}

																					if (isset($row3['totalprice']) && $row3['totalprice'] && empty($money)) {
																						echo '$'. number_format($row3['totalprice']);
																					}
																				}
																			?>
																		</td>
																		<td>
																			<?php 
																				if (in_array($row2['status'], [1, 5]) || $_SESSION['usergroup'] == 6) {
																					if (empty($isReceipted)) {
																						if (!in_array($i, $parameter['direct_income'])) {
																							?>
																								<a class="btn btn-info" href="medias_add.php?id=<?= $campaignId; ?>&cue=2&media2=<?= $i; ?>&mediaid=<?= $row3['id']; ?>">
																									<i class="icon-edit icon-white"></i>
																									新增媒體 (對內)
																								</a>
																								<? if (in_array($i, $parameter['blogger_media_id'])) : ?>
																									<a class="btn btn-info" href="mtype_medias_edit.php?campaign_id=<?= $campaignId; ?>&cue=1&media_id=<?= $row3['id']; ?>&media=<?= $i; ?>">
																										<i class="icon-edit icon-white"></i>
																										編輯
																									</a>
																								<? else : ?>
																									<a class="btn btn-info" href="mtype_medias_edit.php?campaign=<?= $campaignId; ?>&cue=1&id=<?= $row3['id']; ?>&media=<?= $i; ?>&media_id=<?= $i; ?>">
																										<i class="icon-edit icon-white"></i>
																										編輯
																									</a>
																								<? endif; ?>
																							<?php
																						}
																					?>
																						<div class="dropdown">
																							<button class="btn btn-danger" onclick="if (window.confirm('確定要刪除對外媒體「<?= addslashes($row3['website']); ?>」')) location.href='mtype_medias_del.php?campaign=<?= $campaignId; ?>&DEcue=1&media=<?= $i; ?>&item_seq=<?= $row3['item_seq']; ?>';">
																								<i class="fa fa-trash"></i>&nbsp;刪除&nbsp;&nbsp;<i class="fa fa-caret-down"></i>
																							</button>
																							<div class="dropdown-content" style="display: none;" data-media-ordinal="<?= $i; ?>" data-media-id="<?= $row3['id']; ?>">
																								<a href="#" onclick="if (window.confirm('確定要同時刪除內外媒體「<?= addslashes($row3['website']); ?>」')) location.href='mtype_medias_del.php?campaign=<?= $campaignId; ?>&DEcue=2&media=<?= $i; ?>&item_seq=<?= $row3['item_seq']; ?>';">
																									<i class="fa fa-trash"></i>&nbsp;一併刪除對內媒體
																								</a>
																							</div>
																						</div>
																					<?php 
																					}
																				}
																				
																				//海外
																				if (($row2['status'] >= 3 && $row2['status']<=5) || ($_SESSION['usergroup']==6) ||($_SESSION['usergroup']==3)) {
																					$view_io = array(95,96,97,98,99,100,101,103);
																					if (in_array($i, $view_io)) {
																						?>
																							<a class="btn btn-info" href="pdf/print1_io_htm.php?campaign=<?= $campaignId; ?>&cue=1&id=<?= $row3['id']; ?>&media=<?= $i; ?>">
																								<i class="icon-edit icon-white"></i>
																								IO單
																							</a>
																						<?php
																					}
																				}
																			?>
																		</td>
																	</tr>
																<?php
															}
														}
													?>
												</tbody>
											</table>
											<div class="pull-right" style="margin-bottom: 18px;"><h4 style="font-size: 1.6em;">Total: <?= number_format($total); ?>&nbsp;&nbsp;</h4></div>
											</div>
										</div>
									</div>
								<?php
							}
						}
					?>

					<br/>
					<div class="progress progress-striped progress-success" style="margin-bottom: 9px;">
						<div class="bar" style="width: 100%">對內CUE表</div>
					</div>
					<div class="row-fluid internal-cue-head">
						<div id="internal-cue-head">
							<a class="box-header well internal-slide-element internal-cue-tab internal-cue-tab-active" onmouseover="SlideTab(1, 'internal');" onmouseout="SlideTab(0);" style="padding-left: 8px; padding-right: 8px; position: absolute; display: none;"><h2 style="padding-top: 2px;"><i class="fa fa-chevron-left"></i></h2></a>
							<a class="box-header well internal-slide-element internal-cue-tab internal-cue-tab-active" onmouseover="SlideTab(-1, 'internal');" onmouseout="SlideTab(0);" style="padding-left: 8px; padding-right: 8px; position: absolute; display: none; right: 17px;"><h2 style="padding-top: 2px;"><i class="fa fa-chevron-right"></i></h2></a>
							<span id="internal-first-padding" class="internal-slide-element" style="display: none;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
							<span id="internal-last-padding" class="internal-slide-element" style="display: none;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
						</div>
					</div>

					<?php 
						foreach ($rowsOrdinal as $i) {
							$row4 = GetMedia($i);
							$result = mysql_query("SELECT * FROM media$i WHERE campaign_id = $campaignId AND cue = 2 ORDER BY id ASC;");
							$total = mysql_num_rows($result);
							if ($total > 0) {
								?>
									<script>AddTab(<?= $i; ?>, "<?= addslashes($row4['name']); ?><?= $row4['costper'] ? addslashes(" ({$row4['costper']})") : ''; ?>", 'internal');</script>
									<div class="row-fluid internal-cue-body" id="internal-cue-media-<?= $i; ?>" style="display: none; margin-top: -1px;">
										<div class="box span12" style="margin-top: 0px; border-top-left-radius: 0px; border-top-right-radius: 0px;">
											<div class="box-content">
												<table class="table table-striped table-bordered bootstrap-datatable ">
													<?php
														$total = 0;
														$sql3 = "SELECT * FROM media$i WHERE campaign_id = $campaignId AND cue = 2 ORDER BY id ASC;";
														$result3 = mysql_query($sql3);

														if (mysql_num_rows($result3) > 0) {
															while ($row3 = mysql_fetch_array($result3)) {
																$mediaid = $row3["id"];
																$total += $row3['totalprice'];
																?>
																<thead class="internal_media_<?= $i; ?>_column_head">
																	<tr>
																		<th>流水號</th>
																		<th>編號</th>
																		<th>網站</th>
																		<? if (in_array($i, $parameter['extra_fee_media'])) :?>
																			<th>服務費百分比</th>
																		<? endif; ?>
																		<? if (in_array($i, $parameter['detail_head_simple_media'])) : ?>
																		<? elseif (in_array($row4['typename'], $parameter['detail_head_reverve_type'])) : ?>
																			<th>類別</th>
																			<th>廣告類型</th>
																			<th>項目</th>
																		<? else : ?>
																			<th>手機系統</th>
																			<th>廣告類型</th>
																		<? endif; ?>
																		<? if (!in_array($i, $parameter['detail_head_simple_media'])) : ?>
																			<th>版位</th>
																		<? endif; ?>
																		<th>刊登日期</th>
																		<th>天數</th>
																		<th>參考定價</th>
																		<? if (!in_array($i, $parameter['detail_head_simple_media'])) : ?>
																			<th>數量</th>
																		<? endif; ?>
																		<th>操作預算</th>
																		<th>總價</th>
																		<th>異常</th>
																		<th style="width: 100px;">Action</th>
																	</tr>
																</thead>
																<tbody>
																	<tr class="row-yellow">
																		<td>
																			<!-- 流水號 -->
																			<?= $row3['item_seq'];?> </td>
																		<td>
																			<!-- 編號 -->
																			<?= $row3['a'] .'-'. $row3['a0']; ?>
																		</td>
																		<td title="<?= $row3['others']; ?>">
																			<!-- 網站 -->
																			<? if (in_array($i, $parameter['extra_media_id'])) : ?>
																				<?= $row3['media_type']; ?>-<?= $row3['website']; ?>
																			<? elseif (in_array($i, $parameter['extra_fee_media'])) : ?>
																				<?= $row3['website']; ?> <?= empty($row3['channel']) ? '' : "({$row3['channel']})" ; ?>
																			<? else : ?>
																				<?= $row3['website']; ?>
																			<? endif; ?>
																		</td>
																		<? if (in_array($i, $parameter['extra_fee_media'])) :?>
																			<td>
																				<!-- 服務費百分比 -->
																				<?= empty($row3['action']) ? '' : "{$row3['action']}%" ; ?>
																			</td>
																		<? endif; ?>
																		<? if (!in_array($i, $parameter['detail_head_simple_media'])) : ?>
																			<? if (in_array($row4['typename'], $parameter['detail_head_reverve_type'])) : ?>
																				<td><?= $row3['taget']; ?></td>
																			<? endif; ?>
																			<td>
																				<?= in_array($row4['typename'], $parameter['detail_head_reverve_type']) ? $row3['channel'] : (empty($row3['phonesystem']) ? $row3['items3'] : $row3['phonesystem']); ?>
																			</td>
																			<td>
																				<?= in_array($row4['typename'], $parameter['detail_head_reverve_type']) ? $row3['phonesystem'] : $row3['channel']; ?>
																			</td>
																			<td>
																				<?= nl2br($row3['position']); ?>
																			</td>
																		<? endif; ?>
																		<td nowrap>
																			<!-- 刊登日期 -->
																			<?php
																				for ($idx=1; $idx<=9; $idx=$idx+2) {
																					if (isset($row3['date'. $idx]) && $row3['date'. $idx]) {
																						echo date('Y/m/d', $row3['date'. $idx]) .' ~ '. date('Y/m/d', $row3['date'. ($idx + 1)]) .'<br/>';
																					}
																				}
																			?>
																		</td>
																		<td>
																			<!-- 天數 -->
																			<?php
																				$totalDAY = 0;
																				for ($idx=1; $idx<=5; $idx++) {
																					if (isset($row3['days'. $idx]) && $row3['days'. $idx]) {
																						echo $row3['days'. $idx] .'<br/>';
																						$totalDAY++;
																					}
																				}

																				if (isset($row3['days']) && $row3['days'] && empty($totalDAY)) {
																					echo $row3['days'];
																				}
																			?>
																		</td>
																		<td>
																			<!-- 參考定價 -->
																			$<?php
																				if(is_null($row3['a4']) || empty($row3['a4'])){
																					$row3['a4'] = 0;
																				}
																				if (in_array($i, $parameter['direct_income'])) {
																					echo number_format($row3['totalprice']);
																				} else if (in_array($i, $parameter['extra_media_id']) && $row3['media_type'] == 'CPM') {
																					echo number_format(round(($row3['a4'] * 1000 / $row3['quantity2']) * 100) / 100, 2);
																				} else if (in_array($i, $parameter['cpm_media_list']) || $i == 123) {	//CPM
																					for ($idx=1; $idx<=5; $idx++) {
																						if (isset($row3['price'. $idx]) && $row3['price'. $idx]) {
																							echo number_format(round($row3['price'. $idx] * 100) / 100, 2) .'<br/>';
																						}
																					}
																				} else if ($row3['quantity'] == 0) {
																					echo number_format($row3['a4']);
																				} else {
																					echo number_format(round(($row3['a4'] / $row3['quantity']) * 100) / 100, 2);
																				}
																			?>
																		</td>
																		<? if (!in_array($i, $parameter['detail_head_simple_media'])) : ?>
																			<td>
																				<!-- 數量 -->
																				<?php
																					$totalQTY = 0;
																					if (in_array($i, $parameter['extra_media_id']) && $row3['media_type'] == 'CPM') {
																						echo number_format($row3['quantity2']);
																					} else if (in_array($i, $parameter['cpm_media_list'])) {		//CPM
																						for ($idx=1; $idx<=5; $idx++) {
																							if (isset($row3['impression'. $idx]) && $row3['impression'. $idx]) {
																								echo number_format($row3['impression'. $idx]) .'<br/>';
																							}
																						}
																					} else if (in_array($i, [36, 47, 54])) {
																						echo number_format($row3['quantity']);
																					} else if ($i == 45) {
																						// 顯示為總曝光的媒體
																						echo number_format($row3['quantity2']);
																					} else {
																						for ($idx=1; $idx<=5; $idx++) {
																							if (isset($row3['click'. $idx]) && $row3['click'. $idx]) {
																								echo number_format($row3['click'. $idx]) .'<br/>';
																								$totalQTY++;
																							}
																						}
																						
																						if (isset($row3['quantity']) && $row3['quantity'] && empty($totalQTY)) {
																							echo number_format($row3['quantity']);
																						}
																					}
																				?>
																			</td>
																		<? endif; ?>
																		<td>
																			<!-- 操作預算 -->
																			$<?= number_format(empty($row3['a4'])?"0":$row3['a4']); ?>
																		</td>
																		<td>
																			<!-- 總價 -->
																			$<?= number_format(empty($row3['totalprice'])?"0":$row3['totalprice']); ?>
																		</td>
																		<td>
																			<!-- 異常 -->
																			<?= $row3['a5'] == 1 ? '<font color="#FF0000">異常</font>' : '無'; ?>
																		</td>
																		<td>
																			<?php 
																				//2014 12 26 財務關已填成本及結案媒體
																				$displayActionBtns = true;
																				if (!$isGreaterPMLevel) {
																					if (in_array($row3['a'] ."-". $row3['a0'], $Ary_end)) {
																						foreach ($rowsAccounting[$i][$row3['id']] as $itemAccounting) {
																							if (!empty($itemAccounting['accounting_cost']) || !empty($itemAccounting['accounting_revenue'])) {
																								$displayActionBtns = false;
																								break;
																							}
																						}
																					}
																				}

																				$isDeal = false;
																				// echo($row3['id']);
																				// echo "i=".$i."</br>";
																				// print_r($rowsAccounting[$i][$row3['id']]);
																				foreach ((array)$rowsAccounting[$i][$row3['id']] as $itemAccounting) {
																					if (!empty($itemAccounting['accounting_revenue']) || !empty($itemAccounting['accounting_revenue'])) {
																						$isDeal = true;
																						break;
																					}
																				}
																				// echo "isDeal=</br>";
																				// echo($isDeal);
																			?>
																			<? if ($displayActionBtns && in_array($row2['status'], [1, 5]) || $_SESSION['usergroup'] == 6) : ?>
																				<? if (in_array($i, $parameter['blogger_media_id'])) : ?>
																					<a class="btn btn-info"  href="mtype_medias_edit.php?campaign_id=<?= $campaignId; ?>&cue=2&media_id=<?= $row3['id']; ?>&media=<?= $i; ?>">
																						<i class="icon-edit icon-white"></i>
																						編輯
																					</a>
																				<? elseif (!in_array($i, $parameter['direct_income'])) : ?>
																					<a class="btn btn-info"  href="mtype_medias_edit.php?campaign=<?= $campaignId; ?>&cue=2&id=<?= $row3['id']; ?>&media=<?= $i; ?>">
																						<i class="icon-edit icon-white"></i>
																						編輯
																					</a>
																				<? endif; ?>

																				<? if ($isDeal === false) : ?>
																					<div class="dropdown">
																						<button class="btn btn-danger" onclick="if (window.confirm('確定要刪除對內媒體「<?= addslashes($row3['website']); ?>」')) location.href='mtype_medias_del.php?campaign=<?= $campaignId; ?>&DEcue=1&media=<?= $i; ?>&item_seq=<?= $row3['item_seq']; ?>';">
																							<i class="fa fa-trash"></i>&nbsp;刪除&nbsp;&nbsp;<i class="fa fa-caret-down"></i>
																						</button>
																						<div class="dropdown-content">
																							<a href="#" onclick="if (window.confirm('確定要同時刪除內外媒體「<?= addslashes($row3['website']); ?>」')) location.href='mtype_medias_del.php?campaign=<?= $campaignId; ?>&DEcue=2&media=<?= $i; ?>&item_seq=<?= $row3['item_seq']; ?>';">
																								<i class="fa fa-trash"></i>&nbsp;一併刪除對外媒體
																							</a>
																						</div>
																					</div>
																				<? else : ?>
																					<script>
																						$('div.dropdown-content[data-media-ordinal="<?= $i; ?>"][data-media-id=<?= $row3['a0']; ?>]').prev().find('i.fa-caret-down').remove();
																						$('div.dropdown-content[data-media-ordinal="<?= $i; ?>"][data-media-id=<?= $row3['a0']; ?>]').remove();
																					</script>
																				<? endif; ?>
																			<? endif; ?>
																		</td>
																	</tr>
																	<? if ($isGreaterPMLevel) : ?>
																		<tr class="row-white">
																			<td colspan="13">
																				<table class="table table-striped table-bordered bootstrap-datatable" style="margin-bottom: 0px;">
																					<thead>
																						<tr class="row-grey">
																							<th style="background-color: #f0f0f0 !important;">月份</th>
																							<th style="background-color: #f0f0f0 !important;">收入</th>
																							<th style="background-color: #f0f0f0 !important;">預估金額欄</th>
																							<th style="background-color: #f0f0f0 !important;">幣別</th>
																							<th style="background-color: #f0f0f0 !important;">實際成本</th>
																							<th style="background-color: #f0f0f0 !important;">發票編號</th>
																							<th style="background-color: #f0f0f0 !important;">發票日期</th>
																							<th style="background-color: #f0f0f0 !important;white-space:nowrap;">實際發票<br/>輸入日期</th>
																							<th style="background-color: #f0f0f0 !important; width: 260px;">公式備註</th>
																							<th style="background-color: #f0f0f0 !important;">Action</th>
																						</tr>
																					</thead>
																					<tbody>
																						<? //ken,新增幾個欄位(如果有新增欄位,記得儲存時候會呼叫javascript函數SaveAccounting,那邊也要一併調整)
																						foreach ($accountingMonth as $order => $specificMonth) : ?>
																							<tr id="row_accounting_<?= $row2['id']; ?>_<?= $i; ?>_<?= $row3['id']; ?>_<?= $order; ?>" class="<?= ($closeEntryFlag >= $order ? ($isNonGrantedForCampaignEntry ? 'jsadways-closed-entry' : 'jsadways-entry-for-finacial') : ''); ?>">
																								<td>
																									<span style="font-size: 1em;"><?= (int)$specificMonth; ?>月</span>
																									<small style="font-size: .9em; color: #999;">(<?= $order; ?>)</small>
																								</td>
																								<td style="padding-top: 19px; padding-bottom: 19px;">
																									<input name="accounting_revenue" id="accounting_revenue" type="text" style="margin-bottom: 0;" class="large_text_input" value="<?= $rowsAccounting[$i][$row3['id']][$order]['accounting_revenue']; ?>" required>
																								</td>
																								<td class="jsadways-media-cost" data-month="<?= $order; ?>" data-media="<?= $i; ?>" data-item="<?= $row3['id']; ?>">
																									<input name="curr_cost" id="curr_cost" type="text" style="margin-bottom: 0;" class="large_text_input" value="<?= $rowsAccounting[$i][$row3['id']][$order]['curr_cost'];?>" >
																								</td>


																								<td class="jsadways-media-cost" data-month="<?= $order; ?>" data-media="<?= $i; ?>" data-item="<?= $row3['id']; ?>">
																									<select name="currency_id" id="currency_id" style="width: 60px;">
																									<?php 
																									//ken,read lookup currency
																									$sqlLookup = "select trim(item) as item,value from lookup where lookup_type='currency' order by sort ";
																									$dsCurr = mysql_query($sqlLookup);
																									while($drCurr = mysql_fetch_array($dsCurr)){
																										if($rowsAccounting[$i][$row3['id']][$order]['currency_id']==$drCurr['value']){
																											echo "<option value='".$drCurr['value']."' selected>".$drCurr['item']."</option>";
																										}else{
																											echo "<option value='".$drCurr['value']."'>".$drCurr['item']."</option>";
																										}																										
																									} ?>
																									</select>
																								</td>
																								<td class="jsadways-media-cost" data-month="<?= $order; ?>" data-media="<?= $i; ?>" data-item="<?= $row3['id']; ?>">
																									<input name="accounting_cost" id="accounting_cost" type="text" style="margin-bottom: 0;" class="large_text_input" value="<?= $rowsAccounting[$i][$row3['id']][$order]['accounting_cost']; $cost += $rowsAccounting[$i][$row3['id']][$order]['accounting_cost'];  ?>" >
																								</td>

																								<td class="jsadways-media-cost" data-month="<?= $order; ?>" data-media="<?= $i; ?>" data-item="<?= $row3['id']; ?>">
																									<input name="invoice_number" id="invoice_number" type="text" style="margin-bottom: 0;" class="large_text_input" value="<?= $rowsAccounting[$i][$row3['id']][$order]['invoice_number']; ?>" >
																								</td>
																								<td class="jsadways-media-cost" data-month="<?= $order; ?>" data-media="<?= $i; ?>" data-item="<?= $row3['id']; ?>">
																									<input name="invoice_date" id="invoice_date" type="text" style="margin-bottom: 0;" class="large_text_input" value="<?= $rowsAccounting[$i][$row3['id']][$order]['invoice_date']; ?>" >
																								</td>
																								<td class="jsadways-media-cost" data-month="<?= $order; ?>" data-media="<?= $i; ?>" data-item="<?= $row3['id']; ?>">
																									<small name="input_invoice_month" id="input_invoice_month" style="font-size: .9em; color: #999;"><?= $rowsAccounting[$i][$row3['id']][$order]['input_invoice_month']; ?></small>
																								</td>

																								<? if (date('Ym', $row2['date11']) == $order) : ?>
																									<td rowspan="<?= count($accountingMonth); ?>">
																										<textarea name="accounting_comment_<?= $row2['id']; ?>_<?= $i; ?>_<?= $row3['id']; ?>" id="accounting_comment_<?= $row2['id']; ?>_<?= $i; ?>_<?= $row3['id']; ?>" style="margin: 0px; resize: none; height: <?= (73 * count($accountingMonth)) - 10; ?>px;"><?= $row3['text13']; ?></textarea>
																									</td>
																								<? endif; ?>
																								<td>
																									<input name="accounting_month" id="accounting_month" type="hidden" value="<?= $order; ?>">
																									<input id="item_save" class="btn btn-primary" value="儲存" type="button" onclick="SaveAccounting(<?= $row2['id']; ?>, <?= $i; ?>, <?= $row3['id']; ?>, <?= $order; ?>);" style="">
																									<i id="item_loading" class="fa fa-repeat fa-spin" style="font-size: 2em; display: none;"></i>
																									<div id="item_success" style="display: none; color: green; ">
																										<i class="fa fa-check-circle-o" style="font-size: 2em;"></i><br/>
																										<b>儲存成功</b>
																									</div>
																								</td>
																							</tr>
																						<? endforeach; ?>
																					</tbody>
																				</table>
																				<p style="margin-top: 14px; margin-bottom: 9px;">
																					<a style="color: grey;" onclick="ToggleAccountingDetail(this);" data-tag="up">
																						<span id="up"><i class="fa fa-chevron-circle-up" style="font-size: 1.4em;"></i>&nbsp;隱藏收入成本資訊</span>
																						<span id="down" style="display: none;"><i class="fa fa-chevron-circle-down" style="font-size: 1.4em;"></i>&nbsp;顯示收入成本資訊</span>
																					</a>
																				</p>
																			</td>
																		</tr>
																	<? else : ?>
																		<?php
																			$rowsViewAcccounting = [];
																			foreach ($accountingMonth as $order => $specificMonth) {
																				if (!empty($rowsAccounting[$i][$row3['id']][$order]['accounting_cost']) || !empty($rowsAccounting[$i][$row3['id']][$order]['accounting_revenue'])) {
																					$rowsViewAcccounting[$order] = $specificMonth;
																				}
																			}
																		?>
																		<? if (count($rowsViewAcccounting)) : ?>
																			<tr class="row-white accounting-detail-for-view">
																				<td colspan="13">
																					<table class="table table-striped table-bordered bootstrap-datatable" style="margin-bottom: 0px; display: none;">
																						<thead>
																							<tr class="row-grey">
																								<th style="background-color: #f0f0f0 !important;">月份</th>
																								<th style="background-color: #f0f0f0 !important;">收入</th>
																								<th style="background-color: #f0f0f0 !important;">成本</th>
																								<th style="background-color: #f0f0f0 !important;">月份</th>
																								<th style="background-color: #f0f0f0 !important;">收入</th>
																								<th style="background-color: #f0f0f0 !important;">成本</th>
																							</tr>
																						</thead>
																						<tbody>
																							<?php $rowsAccountingMonth = array_keys($rowsViewAcccounting); ?>
																							<? for ($index=0; $index<count($rowsAccountingMonth); $index=$index+2) : ?>
																								<tr style="font-size: 1.5em;">
																									<td width="20%">
																										<span style="font-size: .9em; font-weight: bold;"><?= isset($rowsViewAcccounting[$rowsAccountingMonth[$index]]) ? (int)$accountingMonth[$rowsAccountingMonth[$index]] .'月' : ''; ?></span>
																										<small style="font-size: .7em; color: #999;"><?= isset($rowsAccountingMonth[$index] ) ? '('. $rowsAccountingMonth[$index]. ')' : ''; ?></small>
																									</td>
																									<td width="15%" style="color: red; opacity: .4; font-weight: bold;"><?= empty($rowsAccounting[$i][$row3['id']][$rowsAccountingMonth[$index]]['accounting_revenue']) ? '' : ('$'. number_format($rowsAccounting[$i][$row3['id']][$rowsAccountingMonth[$index]]['accounting_revenue'])); ?></td>
																									<td width="15%" style="color: red; opacity: .4; font-weight: bold;"><?= empty($rowsAccounting[$i][$row3['id']][$rowsAccountingMonth[$index]]['accounting_cost']) ? '' : ('$'. number_format($rowsAccounting[$i][$row3['id']][$rowsAccountingMonth[$index]]['accounting_cost'])); ?></td>
																									<td width="20%">
																										<span style="font-size: .9em; font-weight: bold;"><?= isset($rowsViewAcccounting[$rowsAccountingMonth[$index+1]]) ? (int)$accountingMonth[$rowsAccountingMonth[$index+1]] .'月' : ''; ?></span>
																										<small style="font-size: .7em; color: #999;"><?= isset($rowsAccountingMonth[$index+1] ) ? '('. $rowsAccountingMonth[$index+1]. ')' : ''; ?></small>
																									</td>
																									<td width="15%" style="color: red; opacity: .4; font-weight: bold;"><?= empty($rowsAccounting[$i][$row3['id']][$rowsAccountingMonth[$index+1]]['accounting_revenue']) ? '' : ('$'. number_format($rowsAccounting[$i][$row3['id']][$rowsAccountingMonth[$index+1]]['accounting_revenue'])); ?></td>
																									<td width="15%" style="color: red; opacity: .4; font-weight: bold;"><?= empty($rowsAccounting[$i][$row3['id']][$rowsAccountingMonth[$index+1]]['accounting_cost']) ? '' : ('$'. number_format($rowsAccounting[$i][$row3['id']][$rowsAccountingMonth[$index+1]]['accounting_cost'])); ?></td>
																								</tr>
																							<? endfor; ?>
																						</tbody>
																					</table>
																					<p style="margin-top: 14px; margin-bottom: 9px;">
																						<a style="color: grey;" onclick="ToggleAccountingDetail(this);" data-tag="down">
																							<span id="up" style="display: none;"><i class="fa fa-chevron-circle-up" style="font-size: 1.4em;"></i>&nbsp;隱藏財報資訊</span>
																							<span id="down"><i class="fa fa-chevron-circle-down" style="font-size: 1.4em;"></i>&nbsp;顯示財報資訊</span>
																						</a>
																					</p>
																				</td>
																			</tr>
																		<? endif; ?>
																	<? endif; ?>
																</tbody>
																<?php
															}
														}
													?>
												</table>
												<div class="pull-right" style="margin-bottom: 18px;"><h4 style="font-size: 1.6em;">Total: <?= number_format($total); ?>&nbsp;&nbsp;</h4></div>
												<? if (in_array($i, $parameter['blogger_media_id'])) : ?>
													<table class="table table-striped table-bordered">
														<thead>
															<tr>
																<? if ($isGreaterPMLevel) : ?>
																	<? foreach ($accountingMonth as $order => $specificMonth) : ?>
																		<th style="vertical-align: middle;">
																			<?= (int)$specificMonth; ?>月&nbsp;<small style="font-size: .9em; color: #999;">(<?= $order; ?>)</small>
																			<? if ($isGrantedForFinancial) : ?>
																				<br/><small style="display: block; margin-top: 4px; color: blue;">總成本: $<span class="jsadways-blogger-month-total-cost" id="blogger_month_total_cost_<?= $order; ?>">0</span></small>
																			<? endif; ?>
																		</th>
																	<? endforeach; ?>
																<? endif; ?>
																<th style="vertical-align: middle;">寫手<?= $isGreaterPMLevel ? ' / 匯款帳戶' : ''; ?></th>
																<? if (!$isGreaterPMLevel) : ?>
																	<th style="width: 320px; vertical-align: middle;" class="blogger-accounting-detail-head">結帳明細</th>
																<? endif; ?>
																<th style="width: 100px; vertical-align: middle;">對外報價</th>
																<th style="width: 100px; vertical-align: middle;">成本</th>
																<th style="width: 100px; vertical-align: middle;">利潤</th>
															</tr>
														</thead>
														<tbody>
															<?php
																$sql_blogger = "SELECT MD.*,B.shared_bank_id FROM media19_detail MD 
																				LEFT JOIN blogger B ON MD.blogid = B.id 
																				WHERE MD.campaign_id = $campaignId;";
																$result_blogger = mysql_query($sql_blogger);
																if (mysql_num_rows($result_blogger) > 0) {
																	while ($row_blogger = mysql_fetch_array($result_blogger)) {
																		$blogger_bank = 0;
																		$totalprice += $row_blogger['price'];
																		$intCueTotalPrice += $row_blogger['price2'];
																		$totalprice3 += $row_blogger['price3'];
																		?>
																			<tr id="row_blogger_detail_<?= $row_blogger['id']; ?>">
																				<? if ($isGreaterPMLevel) : ?>
																					<? foreach ($accountingMonth as $order => $specificMonth) : ?>
																						<?php
																							$sql_chargeoff = "SELECT * FROM blogger_chargeoff 
																											WHERE campaign_id = {$row2['id']}
																											AND blogger_detail_id = {$row_blogger['id']}
																											AND cost_date = '". date('Y-m-05', strtotime($order."01")) ."';";
																							$result_cost = mysql_query($sql_chargeoff);
																							$row_cost = mysql_fetch_array($result_cost);

																							if (empty($blogger_bank) && IsId($row_cost["bankId"])) {
																								$blogger_bank = $row_cost["bankId"];
																							}
																						?>
																						<td style="padding-top: 20px;" class="jsadways-blogger-cost" data-item="<?= $mediaid; ?>" data-month="<?= $order; ?>">
																							<form id="form_blogger_chargeoff_<?= $row_blogger['id']; ?>_<?= $order; ?>">
																								成本：&nbsp;&nbsp;
																								<input name="cost" type="text" style="width: 90px;" required value="<?= $row_cost["price"]; ?>"><br/>
																								備註：&nbsp;&nbsp;
																								<input name="remark" type="text" style="width: 90px;" value="<?= $row_cost["remark"]; ?>"><br/>
																								付款：&nbsp;&nbsp;
																								<select name="chargeoff_date" style="width: 100px;">
																									<?php 
																										// 起始時間
																										$select_start = strtotime(date('Y-m-05', strtotime($order ."01")) .'-2 months');
																										// 結束時間
																										$select_end = strtotime(date('Y-m-05', strtotime($order ."01")) .'+14 months');
																										for (;date("Ym", $select_start) < date("Ym", $select_end);) {
																											$selected = '';
																											if ($row_cost['chargeoff_date'] == date("Y-m-d",$select_start)) {
																												$selected = 'selected';
																											} else if (date("Ym", strtotime(date('Y-m-05', strtotime($order ."01")))) == date("Ym", $select_start)) {
																												$selected = 'selected';
																											}

																											echo "<option $selected value='". date("Y-m-d", $select_start) ."'>". date("Y-m", $select_start) ."</option>";
																											$select_start = strtotime(date("Y-m-d", $select_start) .'+1 month');
																										}
																										
																									?>
																								</select><br/>
																								<input name="media_id" type="hidden" value="<?= $mediaid; ?>">
																								<input name="campaign_id" type="hidden" value="<?= $row2['id']; ?>">
																								<input name="blogger_id" type="hidden" value="<?= $row_blogger['blogid']; ?>">
																								<input name="blogger_name" type="hidden" value="<?= $row_blogger['blog']; ?><? if ($row_blogger['blog']==NULL) {echo $row_blogger['blog2'];} ?>">
																								<input name="blogger_detail_id" type="hidden" value="<?= $row_blogger['id']; ?>">
																								<input name="media" type="hidden" value="<?= $i; ?>">
																								<input name="accounting_month" type="hidden" value="<?= $order; ?>">
																								<input class="btn btn-primary" value="儲存" type="button" style="margin-top: 8px;" onclick="UpdateChargeOff('<?= $row_blogger['id'] ?>', '<?= $order; ?>');">
																								<span style="display: none;" id="loader">
																									<i class="fa fa-repeat fa-spin" style="font-size: 1.6em; margin-top: 8px; color: #555555;"></i>
																								</span>
																								<span style="display: none; color: green; font-weight: bold;" id="result"><i class="fa fa-check-circle-o" style="font-size: 1.4em;"></i>&nbsp;儲存成功</span>
																							</form>
																						</td>
																					<? endforeach; ?>
																				<? endif; ?>
																				<td>
																					<a href="blogger_view.php?id=<?= $row_blogger['blogid']; ?>" target="_blank"><?= $row_blogger['blog']; ?><? if ($row_blogger['blog']==NULL) {echo $row_blogger['blog2'];} ?></a>
																					<? if ($isGreaterPMLevel) : ?>
																						<?php
																							$sql_bank = "SELECT * FROM blogger_bank WHERE states = 1 AND ( id in ({$row_blogger['shared_bank_id']}) OR  blogger_id = {$row_blogger['blogid']});";
																							$result_bank = mysql_query($sql_bank);
																						?>
																						<br/><br/>
																						<select name="blogger_bank" id="blogger_bank_<?= $row_blogger['blogid'] ?>">
																							<option value="0">--- 請選擇 ---</option>
																							<? while ($row_bank = mysql_fetch_array($result_bank, MYSQL_ASSOC)) : ?>
																								<option value="<?= $row_bank["id"]; ?>" <?= $blogger_bank == $row_bank["id"] ? 'selected' : ''; ?>><?= $row_bank["bankUserName"]; ?> - <?= $row_bank["bankName"]; ?></option>
																							<? endwhile; ?>
																						</select>
																					<? endif; ?>
																				</td>
																				<? if (!$isGreaterPMLevel) : ?>
																					<td style="font-size: 1.1em;" class="blogger-accounting-detail-outer">
																						<? if (count($accountingMonth)) : ?>
																							<table style="background-color: white; border: 1px solid #ddd; width: 320px;" class="blogger-accounting-detail">
																								<thead>
																									<tr>
																										<th style="background-color: white;" nowrap>月份</th>
																										<th style="background-color: white;" nowrap>成本</th>
																										<th style="background-color: white; text-align: left;">匯款帳戶</th>
																									</tr>
																								</thead>
																								<tbody>
																									<? foreach ($accountingMonth as $order => $specificMonth) : ?>
																										<?php
																											$sql_chargeoff = "SELECT * FROM blogger_chargeoff 
																															WHERE campaign_id = {$row2['id']}
																															AND blogger_detail_id = {$row_blogger['id']}
																															AND cost_date = '". date('Y-m-05', strtotime($order ."01")) ."';";
																											$result_cost = mysql_query($sql_chargeoff);
																											$row_cost = mysql_fetch_array($result_cost);

																											if ($row_cost["price"]) {
																												$sql_bank = "SELECT * FROM blogger_bank WHERE id = {$row_cost["bankId"]};";
																												$result_bank = mysql_query($sql_bank);
																												$row_bank = mysql_fetch_array($result_bank, MYSQL_ASSOC);
																												?>
																													<tr>
																														<td style="background-color: #ffffe5;" nowrap>
																															<?= (int)$specificMonth; ?>月 <small style="font-size: .9em; color: #999;">(<?= $order; ?>)</small>
																														</td>
																														<td style="background-color: #ffffe5;">
																															<span style="color: red; opacity: .4; font-weight: bold;">$<?= number_format($row_cost["price"]); ?></span>
																														</td>
																														<td style="background-color: #ffffe5; text-align: left;">
																															<? if ($row_bank["bankName"]) : ?>
																																<?= $row_bank["bankUserName"]; ?> - <?= $row_bank["bankName"]; ?>
																															<? endif; ?>
																														</td>
																													</tr>
																												<?php
																											}
																										?>
																									<? endforeach; ?>
																								</tbody>
																							</table>
																						<? endif; ?>
																					</td>
																				<? endif; ?>
																				<td><?= number_format($row_blogger['price2']); ?></td>
																				<td><?= number_format($row_blogger['price']); ?></td>
																				<td><?= number_format($row_blogger['price3']); ?></td>
																			</tr>
																		<?php
																	}
																}
															?>
														</tbody>
													</table>
													<script>
														function UpdateChargeOff(detailId, paymentDate)
														{
															var rowId = 'row_blogger_detail_'+ detailId;
															var formId = 'form_blogger_chargeoff_'+ detailId +'_'+ paymentDate;
															
															if ($.isNumeric($('#'+ formId).find(':text[name="cost"]').val())) {
																if ($('#'+ formId).find('select[name="chargeoff_date"]').val() != '') {
																	var updateData = $('#'+ formId).serialize() + '&bloggerBankId='+ $('#'+ rowId).find('select[name="blogger_bank"]').val();
																	$.ajax({
																		url: 'campaign_execute.php?ajax&action=save_blogger_chargeoff',
																		type: 'POST',
																		data: updateData,
																		beforeSend: function() {
																			$('#'+ formId).find(':button').hide();
																			$('#'+ formId).find('#result').hide();
																			$('#'+ formId).find('#loader').show();
																		}, success: function(feedback) {
																			if (typeof feedback == 'object' && feedback.success == 1) {
																				setTimeout(function() {
																					if ('data' in feedback && feedback.data) {
																						$('tr#row_accounting_<?= $campaignId; ?>_19_'+ $('#'+ formId).find('input[name="media_id"]').val() +'_'+ $('#'+ formId).find('input[name="accounting_month"]').val()).find('#accounting_cost:not([readonly]):not(.jsadways-media-cost-grey)').val(feedback.data);
																					}
																					
																					$('#'+ formId).find(':button').hide();
																					$('#'+ formId).find('#loader').hide();
																					$('#'+ formId).find('#result').show();
																					ValidateBloggerToatlCost();

																					setTimeout(function() {
																						$('#'+ formId).find(':button').show();
																						$('#'+ formId).find('#loader').hide();
																						$('#'+ formId).find('#result').hide();
																					}, 1000);
																				}, 1000);
																			} else if ('message' in feedback && feedback.message && feedback.message.length) {
																				alert(feedback.message);
																				$('#'+ formId).find(':button').show();
																				$('#'+ formId).find('#loader').hide();
																				return;
																			} else {
																				$('#'+ formId).find(':button').show();
																				$('#'+ formId).find('#loader').hide();
																				$('#'+ formId).find('#result').hide();
																				alert('發生未知的錯誤');
																			}

																			LoadCampaignHistory();
																		}, error: function() {
																			$('#'+ formId).find(':button').show();
																			$('#'+ formId).find('#loader').hide();
																			$('#'+ formId).find('#result').hide();
																		}
																	});
																}
															} else {
																alert('請輸入成本');
															}
														}
													</script>
												<? endif; ?>
												<script>
													<? if (getCampaignClosedEntryStatus($campaignId)) : ?>
														$('.jsadways-closed-entry').each(function() {
															$(this).find(':text').css({'background-color': '#f0f0f0'}).addClass('jsadways-media-cost-grey');
															$(this).find('.btn-primary').removeClass('btn-primary').addClass('btn-inverse');
														});
													<? else : ?>
														$('.jsadways-closed-entry').each(function() {
															$(this).find(':text').attr('readonly', 'readonly').css({color: '#ccc'});
															$(this).find('.btn-primary').removeAttr('onclick').removeClass('btn-primary').css({color: '#ccc'});
														});
													<? endif; ?>

													$('.jsadways-entry-for-finacial').each(function() {
														$(this).find(':text').css({'background-color': '#f0f0f0'}).addClass('jsadways-media-cost-grey');
														$(this).find('.btn-primary').removeClass('btn-primary').addClass('btn-inverse');
													});

													function ValidateBloggerToatlCost()
													{
														var bloggerAccounting = {};

														if ($('.jsadways-blogger-cost').length) {
															$('.jsadways-blogger-cost').each(function() {
																try {
																	if (!($(this).data('item') in bloggerAccounting)) {
																		bloggerAccounting[$(this).data('item')] = {};
																	}

																	if (!($(this).data('month') in bloggerAccounting[$(this).data('item')])) {
																		bloggerAccounting[$(this).data('item')][$(this).data('month')] = 0;
																	}

																	bloggerAccounting[$(this).data('item')][$(this).data('month')] += $(this).find('input[name="cost"]').val() ? parseFloat($(this).find('input[name="cost"]').val()) : 0;
																} catch (e) {
																	return;
																}
															});

															try {
																for (var idxMediaItem in bloggerAccounting) {
																	for (var idxAccountingMonth in bloggerAccounting[idxMediaItem]) {
																		var objCost = $('.jsadways-media-cost[data-media="19"][data-item="'+ idxMediaItem +'"][data-month="'+ idxAccountingMonth +'"]');
																		if ($(objCost).length) {
																			if (bloggerAccounting[idxMediaItem][idxAccountingMonth] == $(objCost).find('input#accounting_cost').val()) {
																				$(objCost).removeClass('cell-warning');
																			} else {
																				$(objCost).addClass('cell-warning');
																			}
																		}
																	}
																}
															} catch (e) {

															}

															$('.jsadways-blogger-month-total-cost').each(function() {
																$(this).html('0');
															});

															for (var idxMediaItem in bloggerAccounting) {
																for (var idxAccountingMonth in bloggerAccounting[idxMediaItem]) {
																	var totalMonthAmount = parseFloat($('.jsadways-blogger-month-total-cost#blogger_month_total_cost_'+ idxAccountingMonth).html()) + parseFloat(bloggerAccounting[idxMediaItem][idxAccountingMonth]);
																	$('.jsadways-blogger-month-total-cost#blogger_month_total_cost_'+ idxAccountingMonth).html(totalMonthAmount);
																}
															}
														}

														delete bloggerAccounting;
													}

													$('.jsadways-blogger-cost').find('[name="cost"][value=""]').each(function() {
														$(this).val('0');
													});

													ValidateBloggerToatlCost();
												</script>
											</div>
										</div>
									</div>
							
								<?php
								if (!$isGreaterPMLevel) {
									$isPaymentInMedia = false;

									foreach ($rowsAccounting[$i] as $mediaItemId => $mediaItemDetail) {
										foreach ($mediaItemDetail as $mediaMonthOrder => $mediaMonthAccounting) {
											if ($mediaMonthAccounting['accounting_revenue'] || $mediaMonthAccounting['accounting_cost']) {
												$isPaymentInMedia = true;
												break;
											}
										}

										if ($isPaymentInMedia) {
											break;
										}
									}

									if ($isPaymentInMedia === false) {
										?>
											<script>
												$('thead.internal_media_<?= $i; ?>_column_head').each(function(idx) {
													if (idx > 0) {
														$(this).remove();
													}
												});
											</script>
										<?
									}
								}
							}
						}
					?>

					<? if ($isGreaterPMLevel) : ?>
						<br/>
						<div class="progress progress-danger progress-striped" style="margin-bottom: 9px;">
							<div class="bar" style="width: 100%">案件操作記錄</div>
						</div>
						<div class="row-fluid">
							<div class="box span12">
								<div class="box-header well" data-original-title><h2><i class="fa fa-clock-o"></i>&nbsp;案件操作記錄</h2></div>

								<div class="box-content">
									<table class="table">
										<thead>
											<tr>
												<th>操作記錄</th>
												<th>操作人員</th>
												<th>操作時間</th>
											</tr>
										</thead>
										<tbody id="campaign_history_content"></tbody>
										<tbody id="campaign_history_loader">
											<tr>
												<td colspan="3">
													<center style="padding-top: 20px;">
														<i class="fa fa-refresh fa-spin" style="font-size: 3em;"></i>
													</center>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>

						<br/>
						<div class="progress progress-danger progress-striped" style="margin-bottom: 9px;">
							<div class="bar" style="width: 100%">案件暫停媒體修改記錄</div>
						</div>
						<div class="row-fluid">
							<div class="box span12">
								<div class="box-header well" data-original-title><h2><i class="icon-edit"></i>案件暫停媒體修改記錄</h2></div>

								<div class="box-content">
									<table class="table">
										<thead>
											<tr>
												<th>操作記錄</th>
												<th>操作人員</th>
												<th>操作時間</th>
											</tr>
										</thead>
										<tbody id="campaign_history2_content"></tbody>
										<tbody id="campaign_history2_loader">
											<tr>
												<td colspan="3">
													<center style="padding-top: 20px;">
														<i class="fa fa-refresh fa-spin" style="font-size: 3em;"></i>
													</center>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>

						<script>
							function LoadCampaignHistory()
							{
								$.ajax({
									url: 'campaign_execute.php?id=<?= $campaignId; ?>&action=load_history',
									beforeSend: function() {
										$('#campaign_history_content').html('').hide();
										$('#campaign_history_loader').show();
									},
									success: function(feedback) {
										$('#campaign_history_loader').hide();

										if (typeof feedback == 'object') {
											if ('action' in feedback && feedback.action == 'reload') {
												window.location.reload();
												return;
											}

											if (feedback.success && feedback.data) {
												for (var idx in feedback.data) {
													var itemHistory = '<tr>'+
																		'<td>'+ feedback.data[idx]['data'] +'</td>'+ 
																		'<td>'+ feedback.data[idx]['name'] +'</td>'+ 
																		'<td>'+ feedback.data[idx]['times'] +'</td>'+ 
																	'</tr>';
													
													$('#campaign_history_content').append(itemHistory);
												}
											}
										}

										$('#campaign_history_content').show();
									},
									error: function() {
										$('#campaign_history_loader').hide();
										$('#campaign_history_content').append('<tr><td colspan="3">'+ '發生錯誤' +'</td></tr>').show();
									}
								});
							}

							function LoadCampaignHistory2()
							{
								$.ajax({
									url: 'campaign_execute.php?id=<?= $campaignId; ?>&action=load_history2',
									beforeSend: function() {
										$('#campaign_history2_content').html('').hide();
										$('#campaign_history2_loader').show();
									},
									success: function(feedback) {
										$('#campaign_history2_loader').hide();

										if (typeof feedback == 'object') {
											if ('action' in feedback && feedback.action == 'reload') {
												window.location.reload();
												return;
											}

											if (feedback.success && feedback.data) {
												for (var idx in feedback.data) {
													var itemHistory = '<tr>'+
																		'<td>'+ feedback.data[idx]['data'] +'</td>'+ 
																		'<td>'+ feedback.data[idx]['name'] +'</td>'+ 
																		'<td>'+ feedback.data[idx]['times'] +'</td>'+ 
																	'</tr>';
													$('#campaign_history2_content').append(itemHistory);
												}
											}
										}

										$('#campaign_history2_content').show();
									},
									error: function() {
										$('#campaign_history2_loader').hide();
										$('#campaign_history2_content').append('<tr><td colspan="3">'+ '發生錯誤' +'</td></tr>').show();
									}
								});
							}

							LoadCampaignHistory();
							LoadCampaignHistory2();
						</script>
					<? endif; ?>
				</div>
			</div>
			<hr/>

			<?php include("public/footer.php"); ?>
		</div>

		<script>
			$('div.external-cue-tab').first().click();
			$('div.internal-cue-tab').first().click();

			$(document).ready(function() {
				<? if (empty($cost)) : ?>
					ShowDelBtn();
				<? endif; ?>

				$(window).resize(function() {
					ToggleSlider();
				});
				
				ToggleSlider();
				$('table.blogger-accounting-detail').each(function() {
					if (!$(this).find('tbody tr').length) {
						$(this).remove();
					}
				});

				if (!$('td.blogger-accounting-detail-outer').find('table.blogger-accounting-detail').length) {
					$('th.blogger-accounting-detail-head').remove();
					$('td.blogger-accounting-detail-outer').remove();
				}

				var mediaItemStatus = <?= json_encode($mediaItemStatus); ?>;
				for (var mediaOrdinal in mediaItemStatus) {
					for (var itemId in mediaItemStatus[mediaOrdinal]) {
						GenMediaStatusAction(mediaOrdinal, itemId, mediaItemStatus[mediaOrdinal][itemId]);
					}
				}

				<? if ($isGrantedForFinancial) : ?>
					var totalIncome = 0;
					$('input[name="accounting_revenue"]').each(function() {
						if ($.isNumeric($(this).val())) {
							totalIncome += parseFloat($(this).val());
						}
					});
					$('span#total_income').html(NumberFormat(totalIncome));

					var totalCost = 0;
					$('input[name="accounting_cost"]').each(function() {
						if ($.isNumeric($(this).val())) {
							totalCost += parseFloat($(this).val());
						}
					});
					$('span#total_cost').html(NumberFormat(totalCost));
				<? endif; ?>
			});

			function GenMediaStatusAction(ordinal, item, status)
			{
				var section = $('td#internal-media-item-'+ ordinal +'-'+ item);

				if ($(section).length) {
					switch (status) {
						case '0':
							$(section).find('div#media_status_action').html('<a href="javascript:ChangeMediaStatus('+ ordinal +', '+ item +', 1);">執行</a>');
							$(section).prev().html('<?= getMediaStatusText(0); ?>');
							break;
						case '1':
							$(section).find('div#media_status_action').html('<a href="javascript:ChangeMediaStatus('+ ordinal +', '+ item +', 2);">暫停</a>、<a href="javascript:ChangeMediaStatus(\''+ ordinal +'\', \''+ item +'\', 3);">結案</a>');
							$(section).prev().html('<?= getMediaStatusText(1); ?>');
							break;
						case '2':
							$(section).find('div#media_status_action').html('<a href="javascript:ChangeMediaStatus('+ ordinal +', '+ item +', 1);">執行</a>、<a href="javascript:ChangeMediaStatus(\''+ ordinal +'\', \''+ item +'\', 3);">結案</a>');
							$(section).prev().html('<?= getMediaStatusText(2); ?>');
							break;
						case '3':
							$(section).find('div#media_status_action').html('');
							$(section).prev().html('<?= getMediaStatusText(3); ?>');
					}
				}
			}

			function ChangeMediaStatus(ordinal, item, status)
			{
				var section = $('td#internal-media-item-'+ ordinal +'-'+ item);

				if ($(section).length) {
					$.ajax({
						url: 'campaign_execute.php?ajax&action=change_media_status&id=<?= $campaignId; ?>',
						type: 'POST',
						data: {media: ordinal, mid: item, status: status},
						beforeSend: function() {
							$(section).find('div#media_status_action').hide();
							$(section).find('#item_success').hide();
							$(section).find('#item_loading').show();
						},
						success: function(feedback) {
							if (typeof feedback == 'object' && feedback.success == 1) {
								if (feedback.data.closed) {
									alert('所有媒體均已結案, 將自動關閉此案件');
									window.location.reload();
								} else {
									setTimeout(function() {
										$(section).find('div#media_status_action').hide();
										$(section).find('#item_success').show();
										$(section).find('#item_loading').hide();

										setTimeout(function() {
											$(section).find('div#media_status_action').show();
											$(section).find('#item_success').hide();
											$(section).find('#item_loading').hide();
										}, 700);

										GenMediaStatusAction(feedback.data.ordinal, feedback.data.item, feedback.data.status);
									}, 500);

									LoadCampaignHistory();
								}
							} else {
								alert('發生錯誤');
							}
						},
						error: function() {
							$(section).find('div#media_status_action').show();
							$(section).find('#item_success').hide();
							$(section).find('#item_loading').hide();
						}
					});
				}
			}

			function ShowDelBtn()
			{
				$('#del_case').fadeIn(0, function() {
					
				});
			}

			<? if ($isRequireExceptionMessage) : ?>
				isRequireExceptionMessage = true;

				<? if (empty($row2['others'])) : ?>
					$('#exception_tip').show();
				<? else : ?>
					$('#btn-exception-approve').show();
					$('#btn-exception-reject').show();
				<? endif; ?>	
			<? elseif ($isGrantedForExceptionApproval) :?>
				<? if (empty($row2['others'])) : ?>
					$('#exception_tip').show();
				<? else : ?>
					$('#btn-exception-approve').show();
					$('#btn-exception-reject').show();
				<? endif; ?>	
			<? endif; ?>
		</script>
	</body>
</html>