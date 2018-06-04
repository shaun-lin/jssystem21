<?php
	
	// 2017-10-18 (Jimmy): 傑思jsadways2/campaign_listall3.php, 香港jsadways2hk/campaign_listall3.php, 豐富媒體jsadways2ff/campaign_listall3.php 共用此檔案
	require_once dirname(__DIR__) .'/autoload.php';

	$db = clone($GLOBALS['app']->db);
	
	$objMediaAccounting = CreateObject('MediaAccounting');
	$dateYearMonth = sprintf('%04d%02d', GetVar('search3', date('Y')), GetVar('search4', date('m')));

	include_once(__DIR__ .'/include/pagination.inc.php');
	$pagination = new pagination();

	$isAjaxRequest = isset($_REQUEST['ajax']) ? true : false;
	$orderby = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : '`campaign`.`date1`';
	$orderdir = isset($_REQUEST['orderdir']) ? $_REQUEST['orderdir'] : 'DESC';

	if ($isAjaxRequest) {
        include_once(__DIR__ .'/include/twig.inc.php');

		$keyword = isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
        $page = (!is_numeric($page) || $page <= 0) ? 1 : (int)$page;
        $rowsStart = 0;
        $rowsMaxNum = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 15;
        $rowsStart = ($page - 1) * $rowsMaxNum;

		$sqlCondition = ' AND `status` <> 8 AND `status` >= 2 AND `status` <=5';

		$extraCondition = isset($_POST['extraCondition']) ? $_POST['extraCondition'] : [];
								
		if (isset($extraCondition['search2']) && $extraCondition['search2']) {
			if (isset($extraCondition['search5'])) {
				if ($extraCondition['search5'] == 1) {
					$sqlCondition .= ' AND memberid = "'. $extraCondition['search2'] .'"';
				} else if ($extraCondition['search5'] == 2) {
					$sqlCondition .= sprintf(' AND `id` IN (SELECT `campaign_id` FROM `media%d` GROUP BY `campaign_id`) ', $extraCondition['search2']);
				} else if ($extraCondition['search5'] == 'pm') {
					$pmId = $extraCondition['search2'];
					
					if (StartsWith($pmId, 'wom-')) {
						$pmId = str_replace('wom-', '', $pmId);
						if (IsId($pmId)) {
							$sqlCondition .= sprintf(" AND wommId = %d ", $pmId);
						}
					} else if (StartsWith($pmId, 'media-')) {
						$pmId = str_replace('media-', '', $pmId);
						if (IsId($pmId)) {
							$sqlCondition .= sprintf(" AND media_leader = %d ", $pmId);
						}
					}
				}
			}
		}

		if (isset($extraCondition['search4'])) {
			if ($extraCondition['search4'] == 2) {
				$endday = 28;
			} else if (in_array($extraCondition['search4'], [1, 3, 5, 7, 8, 10, 12])) {
				$endday = 31;
			} else if (in_array($extraCondition['search4'], [4, 6, 9, 11])) {
				$endday = 30;
			}

			$sqlCondition .= ' AND ((date11>='.mktime(0,0,0,$extraCondition['search4'],1,$extraCondition['search3']).' AND date11<='.mktime(0,0,0,$extraCondition['search4'],$endday,$extraCondition['search3']).')OR(date22>='.mktime(0,0,0,$extraCondition['search4'],1,$extraCondition['search3']).' AND date22<='.mktime(0,0,0,$extraCondition['search4'],$endday,$extraCondition['search3']).')OR(date11<='.mktime(0,0,0,$extraCondition['search4'],1,$extraCondition['search3']).' AND date22>='.mktime(0,0,0,$extraCondition['search4'],$endday,$extraCondition['search3']).'))';
		}

		if (!empty($keyword)) {
            $columnSearch = array(
                '`campaign`.`name`',
                '`campaign`.`agency`',
                '`campaign`.`client`',
                '`campaign`.`member`',
                '`campaign`.`tagtext`'
            );

            $sqlFilter = array();
            foreach ($columnSearch as $name) {
                $sqlFilter[] = $name ."LIKE '%". mysql_real_escape_string($keyword) ."%'";
            }

			if ($keyword == '直客') {
				$sqlFilter[] = " `campaign`.`agency` = '' ";
				$sqlFilter[] = " `campaign`.`agency` IS NULL ";
			}

            $sqlFilter = ' ('. implode(' OR ', $sqlFilter) .') ';

            $sqlSearch = 'SELECT `campaign`.`id` FROM `campaign`
                        WHERE 1=1 '. $sqlCondition .' AND '. $sqlFilter .'
                        GROUP BY `campaign`.`id`';

            $sqlCondition .=  sprintf(' AND `campaign`.`id` IN (%s)', $sqlSearch);
        }

		$sqlTotalCampaign = 'SELECT COUNT(*) as `total` FROM `campaign` WHERE 1=1 '. $sqlCondition;
        $sqlRowsCampaign = 'SELECT * FROM `campaign` WHERE 1=1 '. $sqlCondition . sprintf(' ORDER BY %s %s ', $orderby, $orderdir) . sprintf(' LIMIT %d, %d', $rowsStart, $rowsMaxNum);

		$db->query($sqlTotalCampaign);
		$itemTotalCampaign = $db->next_record();
        $totalCampaignNum = $itemTotalCampaign['total'];

		$idsCampaign = array(0);
        $rowsCampaign = array();
		$db->query($sqlRowsCampaign);
		while ($itemCampaign = $db->next_record()) {
			$rowsCampaign[] = $itemCampaign;
		}

		$pagination->setConfig(array(
            'start' => ($page - 1) * $rowsMaxNum,
            'total' => $totalCampaignNum,
            'limit' => $rowsMaxNum
        ));
        $sectionBottomPagination = $pagination->getBottomContent();

        // file `campaign_listall3.html` share for route /campaign_listall4.php
		$twig = new twig('campaign_listall3.html', array(
            'session' => $_SESSION,
            'rowsCampaign' => $rowsCampaign,
            'sectionBottomPagination' => $sectionBottomPagination
        ));

        $output = array(
            'top_pagination' => '',
            'content_list' => $twig->getContent(),
            'bottom_pagination' => $sectionBottomPagination
        );

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($output));
	}

	$extraCondition = [];

	if (isset($_POST['search4'])) {
		if (isset($_POST['search0'])) {
			$extraCondition['search0'] = $_POST['search0'];
		}

		if (isset($_POST['search1'])) {
			$extraCondition['search1'] = $_POST['search1'];
		}

		if (isset($_POST['search2'])) {
			$extraCondition['search2'] = $_POST['search2'];
		}

		$extraCondition['search3'] = isset($_POST['search3']) ? $_POST['search3'] : '';
		$extraCondition['search4'] = $_POST['search4'];
	}

	if (isset($_POST['search5'])) {
		$extraCondition['search5'] = $_POST['search5'];
	}

	$enableUsers = [];
	$disableUsers = [];
	$enableWomUsers = [];
	$disableWomUsers = [];
	$enableMediaUsers = [];
	$disableMediaUsers = [];
	
	$objMrbsUser = CreateObject('MrbsUsers');
	foreach ($objMrbsUser->searchAll() as $itemUser) {
		if (isset($itemUser['listall3']) && $itemUser['listall3']) {
			if ($GLOBALS['env']['flag']['pos'] == 'ff') {
				if ($itemUser['departmentid'] == 18) {

				} else if ($itemUser['departmentid'] == 13 && $itemUser['usergroup'] == MrbsUsers::ROLE['SALES']) {

				} else {
					continue;
				}
			} else if ($itemUser['departmentid'] == 13) {
				continue;
			}
			
			if (empty($itemUser['user_resign_date']) || $itemUser['user_resign_date'] == '0000-00-00') {
				$enableUsers[] = $itemUser;
			} else {
				$disableUsers[] = $itemUser;
			}
		}

		if (empty($itemUser['user_resign_date']) || $itemUser['user_resign_date'] == '0000-00-00') {
			if (in_array($itemUser['departmentid'], [19, 20])) {
				$enableWomUsers[] = $itemUser;
			} else if (in_array($itemUser['departmentid'], [21, 22])) {
				$enableMediaUsers[] = $itemUser;
			}
		} else {
			if (in_array($itemUser['departmentid'], [19, 20])) {
				$disableWomUsers[] = $itemUser;
			} else if (in_array($itemUser['departmentid'], [21, 22])) {
				$disableMediaUsers[] = $itemUser;
			}
		}
	}

	$enableWomUsers[] = [
		'id' => '18',
		'name' => 'kingblack',
		'username' => '陳芳儀',
	];

	if (IsPermitted('finacial', null, Permission::ACL['finacial_close_entry'])) {
		$closeEntryFlag = $GLOBALS['app']->preference->get('close_entry_flag');
		$closeEntryDateTime = date('Y-m-d H:i:s', $GLOBALS['app']->preference->get('close_entry_stamp'));
		$closeEntryUser = $GLOBALS['app']->preference->get('close_entry_user');
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>【<?= $GLOBALS['env']['flag']['name']; ?>】月報表</title>
		<?php include("public/head.php"); ?>
		<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
		<script type="text/javascript">
			$(function() {
				$.datepicker.setDefaults( $.datepicker.regional[ "zh-TW" ] );
				$("#start_time").datepicker({ dateFormat: 'yy-mm-dd' });
				$("#end_time").datepicker({ dateFormat: 'yy-mm-dd' });

				$('#by_rewrite').click(function(event) {
					if (($('#start_time').val() != '') && $('#end_time').val() != '') {
						var link = 'excel/print_rewrite.php?start_time='+ $('#start_time').val()+'&end_time='+$('#end_time').val();
						window.open(link, '_blank');
					} else {
						alert('請選日期');
					}
				});
			});
		</script>
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
									<h2><i class="fa fa-search"></i>&nbsp;&nbsp;查詢條件</h2>
								</div>

								<div class="box-content">
									<form class="form-horizontal" action="campaign_listall3.php" method="post">
										<fieldset> 
											<div class="control-group">
												<label class="control-label">搜尋：AE</label>
												<div class="controls">
													<select id="search2" name="search2">
														<option value="">全部</option>
														<? foreach ($enableUsers as $rowUser) : ?>
															<option value="<?= $rowUser['id']; ?>" <?= (isset($_POST['search2']) && $_POST['search2'] == $rowUser['id']) ? 'selected' : ''; ?>><?= ucfirst($rowUser['name']); ?>【<?= $rowUser['username']; ?>】</option>;
														<? endforeach; ?>
														<optgroup label="已停權員工">
															<? foreach ($disableUsers as $rowUser) : ?>
																<option value="<?= $rowUser['id']; ?>" <?= (isset($_POST['search2']) && $_POST['search2'] == $rowUser['id']) ? 'selected' : ''; ?>><?= ucfirst($rowUser['name']); ?>【<?= $rowUser['username']; ?>】</option>';
															<? endforeach; ?>
														</optgroup>
													</select>
													<?php unset($enableUsers); unset($disableUsers); ?>
													<input type="hidden" value="1" id="search5" name="search5">
												</div>
											</div>

											<div class="control-group">
												<label class="control-label"></label>
												<div class="controls">
													<select id="search3" name="search3">
														<? for ($i=$GLOBALS['env']['flag']['begin_year']; $i<=date('Y'); $i++) : ?>
															<option value="<?= $i; ?>" <?= isset($_POST['search3']) ? ($_POST['search3'] == $i ? 'selected' : '') : ($i == date('Y') ? 'selected' : ''); ?>><?= $i; ?></option>
														<? endfor; ?>
													</select>&nbsp;&nbsp;年
												</div>
											</div>

											<div class="control-group">
												<label class="control-label"></label>
												<div class="controls">
													<select id="search4" name="search4">
														<? for ($i=1; $i<=12; $i++) : ?>
															<option value="<?= $i; ?>" <?= isset($_POST['search4']) ? ($_POST['search4'] == $i ? 'selected' : '') : ($i == date('m') ? 'selected' : ''); ?>><?= $i; ?></option>
														<? endfor; ?>
													</select>&nbsp;&nbsp;月
												</div>
											</div>

											<div class="control-group">
												<div class="controls">
													<button type="submit" class="btn btn-primary">查詢</button>

													<? if (IsPermitted('finacial', null, Permission::ACL['finacial_close_entry'])) : ?>
														<br/><br/><br/>
														<select style="width: 120px;" id="close_entry_flag">
															<option value="<?= date('Ym', strtotime('-2 month')); ?>"><?= date(' Y 年 m 月', strtotime('-2 month')); ?></option>
															<option value="<?= date('Ym', strtotime('-1 month')); ?>" selected><?= date(' Y 年 m 月', strtotime('-1 month')); ?></option>
															<option value="<?= date('Ym'); ?>"><?= date(' Y 年 m 月'); ?></option>
														</select>&nbsp;
														<button type="button" class="btn btn-danger" onclick="CloseEntry();">關帳</button>
														<span id="close_entry_outer" style="display: <?= $closeEntryFlag ? 'block' : 'none'; ?>; padding-top: 8px; color: #ccc; text-shadow: none;">關帳日期 <span style="color: red; font-weight: bold;" id="close_entry_flag"><?= $closeEntryFlag; ?></span> <br/>By <span id="close_entry_username"><?= $closeEntryUser; ?></span>&nbsp;&nbsp;At&nbsp;<span id="close_entry_datetime"><?= $closeEntryDateTime; ?></span></span>
														<script>
															function CloseEntry()
															{
																$.ajax({
																	url: 'campaign_execute.php?action=close_entry',
																	type: 'POST',
																	data: {close_entry_flag: $('select#close_entry_flag').val()},
																	beforeSend: function() {

																	},
																	success: function(feedback) {
																		if ('data' in feedback && feedback.data) {
																			for (var idx in feedback.data) {
																				$('span#close_entry_'+ idx).html(feedback.data[idx]);
																			}
																		}

																		if ('success' in feedback && feedback.success) {
																			$('span#close_entry_outer').show();
																			$('span#close_entry_outer').css({display: 'block'});
																		}
																		
																		alert(feedback.message);
																	},
																	error: function() {
																		alert('發生錯誤');
																	}
																})
															}
														</script>
													<? endif; ?>
												</div>
											</div>
											
											<? if (GetVar('search5') == 1) : ?>
												<div class="control-group">
													<div class="controls">
														<a  href="excel/print2.php?search2=<?= $_POST['search2']; ?>&search3=<?= $_POST['search3']; ?>&search4=<?= $_POST['search4']; ?>" target="_blank">輸出excel</a>
														<? if ($_SESSION['usergroup'] >= 4) : ?> 
															&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="excel/print2.php?search2=<?= $_POST['search2']; ?>&search3=<?= $_POST['search3']; ?>&search4=<?= $_POST['search4']; ?>&Finance=1" target="_blank">輸出excel(外匯調整)</a>
														<? endif; ?>
													</div>
												</div>
											<? endif; ?>
										</fieldset>
									</form>
								</div>
							</div>
						</div>

						<div class="row-fluid">
							<div class="box">
								<div class="box-header well" data-original-title>
									<h2><i class="fa fa-search"></i>&nbsp;&nbsp;查詢條件</h2>
								</div>

								<div class="box-content">
									<form class="form-horizontal" action="campaign_listall3.php" method="post">
										<fieldset> 
											<div class="control-group">
												<label class="control-label">搜尋：PM</label>
												<div class="controls">
													<select id="search2" name="search2">
														<option value="">全部</option>
														<? if (empty($GLOBALS['env']['flag']['pos'])) : ?>
															<optgroup label="口碑部">
																<? foreach ($enableWomUsers as $rowUser) : ?>
																	<option value="wom-<?= $rowUser['id']; ?>" <?= (isset($_POST['search2']) && $_POST['search2'] == "wom-{$rowUser['id']}") ? 'selected' : ''; ?>><?= ucfirst($rowUser['name']); ?>【<?= $rowUser['username']; ?>】</option>;
																<? endforeach; ?>
															</optgroup>
														<? endif; ?>
														<optgroup label="媒體部">
															<? foreach ($enableMediaUsers as $rowUser) : ?>
																<option value="media-<?= $rowUser['id']; ?>" <?= (isset($_POST['search2']) && $_POST['search2'] == "media-{$rowUser['id']}") ? 'selected' : ''; ?>><?= ucfirst($rowUser['name']); ?>【<?= $rowUser['username']; ?>】</option>;
															<? endforeach; ?>
														</optgroup>
														<? if (empty($GLOBALS['env']['flag']['pos'])) : ?>
															<optgroup label="口碑部 - 已停權員工">
																<? foreach ($disableWomUsers as $rowUser) : ?>
																	<option value="wom-<?= $rowUser['id']; ?>" <?= (isset($_POST['search2']) && $_POST['search2'] == "wom-{$rowUser['id']}") ? 'selected' : ''; ?>><?= ucfirst($rowUser['name']); ?>【<?= $rowUser['username']; ?>】</option>';
																<? endforeach; ?>
															</optgroup>
														<? endif; ?>
														<optgroup label="媒體部 - 已停權員工">
															<? foreach ($disableMediaUsers as $rowUser) : ?>
																<option value="media-<?= $rowUser['id']; ?>" <?= (isset($_POST['search2']) && $_POST['search2'] == "media-{$rowUser['id']}") ? 'selected' : ''; ?>><?= ucfirst($rowUser['name']); ?>【<?= $rowUser['username']; ?>】</option>';
															<? endforeach; ?>
														</optgroup>
													</select>
													<?php unset($enableWomUsers); unset($disableWomUsers); unset($enableMediaUsers); unset($disableMediaUsers); ?>
													<input type="hidden" value="pm" id="search5" name="search5">
												</div>
											</div>

											<div class="control-group">
												<label class="control-label"></label>
												<div class="controls">
													<select id="search3" name="search3">
														<? for ($i=$GLOBALS['env']['flag']['begin_year']; $i<=date('Y'); $i++) : ?>
															<option value="<?= $i; ?>" <?= isset($_POST['search3']) ? ($_POST['search3'] == $i ? 'selected' : '') : ($i == date('Y') ? 'selected' : ''); ?>><?= $i; ?></option>
														<? endfor; ?>
													</select>&nbsp;&nbsp;年
												</div>
											</div>

											<div class="control-group">
												<label class="control-label"></label>
												<div class="controls">
													<select id="search4" name="search4">
														<? for ($i=1; $i<=12; $i++) : ?>
															<option value="<?= $i; ?>" <?= isset($_POST['search4']) ? ($_POST['search4'] == $i ? 'selected' : '') : ($i == date('m') ? 'selected' : ''); ?>><?= $i; ?></option>
														<? endfor; ?>
													</select>&nbsp;&nbsp;月
												</div>
											</div>

											<div class="control-group">
												<div class="controls">
													<button type="submit" class="btn btn-primary">查詢</button>
												</div>
											</div>

											<? if (GetVar('search5') == 'pm') : ?>
												<div class="control-group">
													<div class="controls">
														<a href="excel/print2.php?search2=<?= $_POST['search2']; ?>&search3=<?= $_POST['search3']; ?>&search4=<?= $_POST['search4']; ?>&search5=pm" target="_blank">輸出excel</a>
													</div>
												</div>
											<? endif; ?>
										</fieldset> 
									</form>
								</div>
							</div>
						</div>
					</div>

					<div class="span6">
						<div class="row-fluid">
							<div class="box">
								<div class="box-header well" data-original-title>
									<h2><i class="fa fa-search"></i>&nbsp;&nbsp;查詢條件</h2>
								</div>

								<div class="box-content">
									<form class="form-horizontal" action="campaign_listall3.php" method="post">
										<fieldset> 
											<div class="control-group">
												<label class="control-label">搜尋：媒體</label>
												<div class="controls">
													<select id="search2" name="search2">
														<option value="">全部</option>
														<?php
															$objMedia = CreateObject('Media');
															foreach ($objMedia->searchAll('id <> 0') as $row6) {
																echo sprintf('<option value="%d" %s>%s</option>', $row6['id'], isset($_POST['search2']) && $_POST['search2'] == $row6['id'] ? 'selected' : '', $row6['name']);
															}
														?>
													</select>
													<input type="hidden" value="2" id="search5" name="search5">
												</div>
											</div>

											<div class="control-group">
												<label class="control-label"></label>
												<div class="controls">
													<select id="search3" name="search3">
														<? for ($i=$GLOBALS['env']['flag']['begin_year']; $i<=date('Y'); $i++) : ?>
															<option value="<?= $i; ?>" <?= isset($_POST['search3']) ? ($_POST['search3'] == $i ? 'selected' : '') : ($i == date('Y') ? 'selected' : ''); ?>><?= $i; ?></option>
														<? endfor; ?>
													</select>&nbsp;&nbsp;年
												</div>
											</div>

											<div class="control-group">
												<label class="control-label"></label>
												<div class="controls">
													<select id="search4" name="search4">
														<? for ($i=1; $i<=12; $i++) : ?>
															<option value="<?= $i; ?>" <?= isset($_POST['search4']) ? ($_POST['search4'] == $i ? 'selected' : '') : ($i == date('m') ? 'selected' : ''); ?>><?= $i; ?></option>
														<? endfor; ?>
													</select>&nbsp;&nbsp;月
												</div>
											</div>

											<div class="control-group">
												<div class="controls">
													<button type="submit" class="btn btn-primary">查詢</button>
												</div>
											</div>

											<? if (GetVar('search5') == 2) : ?>
												<div class="control-group">
													<div class="controls">
														<a  href="excel/print2_media.php?search2=<?= $_POST['search2']; ?>&search3=<?= $_POST['search3']; ?>&search4=<?= $_POST['search4']; ?>" target="_blank">輸出excel</a>
														<? if ($_SESSION['usergroup'] >= 4) : ?> 
															&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="excel/print2_media.php?search2=<?= $_POST['search2']; ?>&search3=<?= $_POST['search3']; ?>&search4=<?= $_POST['search4']; ?>&Finance=1" target="_blank">輸出excel(外匯調整)</a>
														<? endif; ?>
													</div>
												</div>
											<? endif; ?>
										</fieldset>
									</form>
								</div>
							</div>
						</div>

						<div class="row-fluid">
							<div class="box">
								<div class="box-header well" data-original-title>
									<h2><i class="fa fa-search"></i>&nbsp;&nbsp;查詢條件</h2>
								</div>

								<div class="box-content">
									<form class="form-horizontal">
										<fieldset> 
											<div class="control-group">
												<label class="control-label">搜尋：委刊單單號</label>
												<div class="controls">
													<input type="text" value="" id="idnumber" placeholder="">
													<p>ex: 1703001 <br/>多筆以逗號區隔 ex: 1703001,1703002</p>
												</div>
											</div>

											<div class="control-group">
												<div class="controls">
													<a href="javascript:exportExcelForIdnumber();">輸出excel</a>
												</div>
											</div>

											<script>
												function exportExcelForIdnumber()
												{
													var idnumber = document.getElementById('idnumber').value.toString().replace(/\s/g, '');

													if (!idnumber.length) {
														return;
													}
													
													var link = 'excel/print_for_idnumber.php?idnumber='+ idnumber;
													
													window.open(link, '_blank');
													return false;
												}
											</script>
										</fieldset>
									</form>
								</div>
							</div>
						</div>

						<div class="row-fluid">
							<div class="box">
								<div class="box-header well" data-original-title>
									<h2><i class="fa fa-search"></i>&nbsp;&nbsp;查詢條件</h2>
								</div>

								<div class="box-content">
									<form class="form-horizontal">
										<fieldset> 
											<div class="control-group">
												<label class="control-label">搜尋：回簽日期</label>
												<div class="controls">
													<input id="start_time" required="required" class="datepicker" type="text" style="width:100px" value="" name="start_time"> ~ <input id="end_time" required="required" class="datepicker" type="text" style="width:100px" value="" name="end_time">
												</div>
											</div>

											<div class="control-group">
												<div class="controls">
													<button type="button" class="btn btn-primary" id="by_rewrite">查詢輸出excel</button>
												</div>
											</div>
										</fieldset>
									</form>
								</div>
							</div>
						</div>
					</div>
					
					<? if (isset($_POST['search4'])) : ?>
						<?php
							if (isset($_POST['search2']) && $_POST['search2']) {
								if (isset($_POST['search5'])) {
									if ($_POST['search5'] == 1) {
										$search2 = sprintf(' AND memberid = %d', $_POST['search2']);
									} else if ($_POST['search5'] == 2) {
										$search2 = sprintf(' AND `id` IN (SELECT `campaign_id` FROM `media%d` GROUP BY `campaign_id`) ', $_POST['search2']);;
									} else if ($_POST['search5'] == 'pm') {
										$search2 = '';
										$pmId = $_POST['search2'];
					
										if (StartsWith($pmId, 'wom-')) {
											$pmId = str_replace('wom-', '', $pmId);
											if (IsId($pmId)) {
												$search2 = sprintf(" AND wommId = %d ", $pmId);
											}
										} else if (StartsWith($pmId, 'media-')) {
											$pmId = str_replace('media-', '', $pmId);
											if (IsId($pmId)) {
												$search2 = sprintf(" AND media_leader = %d ", $pmId);
											}
										}
									} else {
										$search2 = '';
									}
								}
							} else {
								$search2 = '';
							}

							if (in_array($_POST['search4'], [1, 3, 5, 7, 8,10, 12])) {
								$endday = 31;
							} else if (in_array($_POST['search4'], [4, 6, 9, 11])) {
								$endday = 30;
							} else if ($_POST['search4'] == 2) {
								$endday = 28;
							}

							$a31 = 0;
							$a32 = 0;
							$a33 = 0;
							$a34 = 0;
							$a41 = 0;
							$a42 = 0;
							$a43 = 0;
							$a44 = 0;

							$dbMedia = clone($GLOBALS['app']->db);
							
							$search3 = ' AND (
											( date11 >= '. mktime(0, 0, 0, $_POST['search4'], 1, $_POST['search3']) .' AND date11 <= '. mktime(0, 0, 0, $_POST['search4'], $endday, $_POST['search3']) .') 
											OR ( date22 >= '. mktime(0, 0, 0, $_POST['search4'], 1, $_POST['search3']) .' AND date22 <= '. mktime(0, 0, 0, $_POST['search4'], $endday, $_POST['search3']) .') 
											OR ( date11 <= '. mktime(0, 0, 0, $_POST['search4'], 1, $_POST['search3']) .' AND date22 >= '. mktime(0, 0, 0, $_POST['search4'], $endday, $_POST['search3']) .') 
										)';
							$sqlCampaign = 'SELECT * FROM `campaign` 
											WHERE `status` <> 8 AND `status` >= 2 AND `status` <= 5 '.$search3 . $search2;
							
							$db->query($sqlCampaign);
							while ($row2=$db->next_record()) {
								$rowsAccounting = $objMediaAccounting->getList($row2['id']);
								
								$rowsMediaOrdinal = GetUsedMediaOrdinal($row2['id']);
								foreach ($rowsMediaOrdinal as $i) {
									$sqlMediaItem = sprintf("SELECT * FROM `media%d` WHERE `cue` = 2 AND `campaign_id` = %d;", $i, $row2['id']);
									$dbMedia->query($sqlMediaItem);
									while ($row22 = $dbMedia->next_record()) {
										$rowmediamedia = GetMedia($i);

										$media[$i] = (isset($media[$i]) ? $media[$i] : 0) + $row22['totalprice'];

										if (isset($rowsAccounting[$i][$row22['id']][$dateYearMonth]['accounting_revenue'])) {
											if (!isset($a3[$i])) {
												$a3[$i] = 0;
											}

											if (!isset($a4[$i])) {
												$a4[$i] = 0;
											}

											$a3[$i] += $rowsAccounting[$i][$row22['id']][$dateYearMonth]['accounting_revenue'];
											$a4[$i] += $rowsAccounting[$i][$row22['id']][$dateYearMonth]['accounting_cost'];
										}
										
										
										if ($rowmediamedia['type2'] == 'SP広告売上') {
											if (isset($rowsAccounting[$i][$row22['id']][$dateYearMonth]['accounting_revenue'])) {
												$a31 += $rowsAccounting[$i][$row22['id']][$dateYearMonth]['accounting_revenue'];
												$a41 += $rowsAccounting[$i][$row22['id']][$dateYearMonth]['accounting_cost'];
											}
										}

										if ($rowmediamedia['type2'] == 'SP APP売上') {
											if (isset($rowsAccounting[$i][$row22['id']][$dateYearMonth]['accounting_revenue'])) {
												$a32 += $rowsAccounting[$i][$row22['id']][$dateYearMonth]['accounting_revenue'];
												$a42 += $rowsAccounting[$i][$row22['id']][$dateYearMonth]['accounting_cost'];
											}
										}

										if ($rowmediamedia['type2'] == 'PC広告売上') {
											if (isset($rowsAccounting[$i][$row22['id']][$dateYearMonth]['accounting_revenue'])) {
												$a33 += $rowsAccounting[$i][$row22['id']][$dateYearMonth]['accounting_revenue'];
												$a43 += $rowsAccounting[$i][$row22['id']][$dateYearMonth]['accounting_cost'];
											}
										}

										if ($rowmediamedia['type2'] == 'その他売上') {
											if (isset($rowsAccounting[$i][$row22['id']][$dateYearMonth]['accounting_revenue'])) {
												$a34 += $rowsAccounting[$i][$row22['id']][$dateYearMonth]['accounting_revenue'];
												$a44 += $rowsAccounting[$i][$row22['id']][$dateYearMonth]['accounting_cost'];
											}
										}
									}
								}
							}
						?>
						<div class="row-fluid">
							<div class="box span12">
								<div class="box-header well" data-original-title>
									<h2><i class="icon-edit"></i> <?= $_POST['search3']; ?>年<?= $_POST['search4']; ?>月統計資料</h2>
								</div>

								<div class="box-content">
									<? if ($_POST['search5'] == 1 || $_POST['search5'] == 'pm') : ?>
										<?php
											$total1 = $a31 + $a32 + $a33 + $a34;
											
											if ($total1 != 0) {
												$total2 = $a41 + $a42 + $a43 + $a44;
												$total3 = $total1 - $total2;
												$total4 = round((($total3 / $total1) * 100), 2);
											} else {
												$total2 = 0;
												$total3 = 0;
												$total4 = 0;
											}

											$b31 = $a31 ? round(((($a31 - $a41) / $a31) * 100), 2) : 0;
											$b32 = $a32 ? round(((($a32 - $a42) / $a32) * 100), 2) : 0;
											$b33 = $a33 ? round(((($a33 - $a43) / $a33) * 100), 2) : 0;
											$b34 = $a34 ? round(((($a34 - $a44) / $a34) * 100), 2) : 0;
										?>
										<table>
											<tr>
												<td>
													<table border="1" >
														<tr bgcolor="#E6FFDF">
															<td></td>
															<td align="center">總計</td>
															<td align="right"><?= number_format($total1); ?></td>
															<td align="right"><?= number_format($total2); ?></td>
															<td align="right"><?= number_format($total3); ?></td>
															<td align="right"><?= number_format($total4); ?>%</td>
														</tr>
														<tr>
															<td></td>
															<td></td>
															<td align="center">銷售</td>
															<td align="center">成本</td>
															<td align="center">毛利</td>
															<td align="center">毛利率</td>
														</tr>
														<tr bgcolor="#A8A8FF">
															<td>SP広告売上</td>
															<td></td>
															<td align="right"></td>
															<td align="right"></td>
															<td align="right"></td>
															<td align="right"></td>
														</tr>
														<?php 
															for ($i=1; $i<=$GLOBALS['env']['flag']['media_number']; $i++) {
																if ((isset($a3[$i]) && $a3[$i]) || (isset($a4[$i]) && $a4[$i])) {
																	$rowmediamedia = GetMedia($i);

																	if ($rowmediamedia['type2'] == 'SP広告売上') {
																		echo '<tr>
																					<td></td>
																					<td align="center">'. $rowmediamedia['name'] .'</td>
																					<td align="right">'. number_format($a3[$i]) .'</td>
																					<td align="right">'. number_format($a4[$i]) .'</td>
																					<td align="right">'. number_format($a3[$i] - $a4[$i]) .'</td>
																					<td align="right">'. round(((($a3[$i] - $a4[$i]) / $a3[$i]) * 100), 2) .'%</td>
																				</tr>';
																	}
																}
															}
														?>
														<tr bgcolor="#D4A8FF">
															<td>SP APP売上</td>
															<td></td>
															<td align="right"></td>
															<td align="right"></td>
															<td align="right"></td>
															<td align="right"></td>
														</tr>
														<?php
															for ($i=1; $i<=$GLOBALS['env']['flag']['media_number']; $i++) {
																if ((isset($a3[$i]) && $a3[$i]) || (isset($a4[$i]) && $a4[$i])) {
																	$rowmediamedia = GetMedia($i);

																	if ($rowmediamedia['type2'] == 'SP APP売上') {
																		echo '<tr>
																					<td></td>
																					<td align="center">'. $rowmediamedia['name'] .'</td>
																					<td>'. number_format($a3[$i]) .'</td>
																					<td>'. number_format($a4[$i]) .'</td>
																					<td>'. number_format($a3[$i] - $a4[$i]) .'</td>
																					<td align="right">'. round(((($a3[$i] - $a4[$i]) / $a3[$i]) * 100), 2) .'%</td>
																				</tr>';
																	}
																}
															}
														?>
														<tr bgcolor="#99E8E6">
															<td>PC広告売上</td>
															<td></td>
															<td align="right"></td>
															<td align="right"></td>
															<td align="right"></td>
															<td align="right"></td>
														</tr>
														<?php
															for ($i=1; $i<=$GLOBALS['env']['flag']['media_number']; $i++) {
																if ((isset($a3[$i]) && $a3[$i]) || (isset($a4[$i]) && $a4[$i])) {
																	$rowmediamedia = GetMedia($i);

																	if ($rowmediamedia['type2'] == 'PC広告売上') {
																		echo '<tr>
																					<td></td>
																					<td align="center">'. $rowmediamedia['name'] .'</td>
																					<td align="right">'. number_format($a3[$i]) .'</td>
																					<td align="right">'. number_format($a4[$i]) .'</td>
																					<td align="right">'. number_format($a3[$i] - $a4[$i]) .'</td>
																					<td align="right">'. round(((($a3[$i] - $a4[$i]) / $a3[$i]) * 100), 2) .'%</td>
																				</tr>';
																	}
																}
															}
														?>
														<tr bgcolor="#EAC9FF">
															<td>その他売上</td>
															<td></td>
															<td align="right"></td>
															<td align="right"></td>
															<td align="right"></td>
															<td align="right"></td>
														</tr>
														<?php
															for ($i=1; $i<=$GLOBALS['env']['flag']['media_number']; $i++) {
																if ((isset($a3[$i]) && $a3[$i]) || (isset($a4[$i]) && $a4[$i])) {
																	$rowmediamedia = GetMedia($i);

																	if ($rowmediamedia['type2'] == 'その他売上') {
																		echo '<tr>
																					<td></td>
																					<td align="center">'. $rowmediamedia['name'] .'</td>
																					<td align="right">'. number_format($a3[$i]) .'</td>
																					<td align="right">'. number_format($a4[$i]) .'</td>
																					<td align="right">'. number_format($a3[$i] - $a4[$i]) .'</td>
																					<td align="right">'. round(((($a3[$i] - $a4[$i]) / $a3[$i]) * 100), 2) .'%</td>
																				</tr>';
																	}
																}
															}
														?>
													</table>
												</td>
												<td>
													<table border="1" >
														<tr>
															<td></td>
															<td align="center">銷售</td>
															<td align="center">成本</td>
															<td align="center">毛利</td>
															<td align="center">毛利率</td>
														</tr>
														<tr bgcolor="#A8A8FF">
															<td>SP広告売上</td>
															<td align="right"><?= number_format($a31); ?></td>
															<td align="right"><?= number_format($a41); ?></td>
															<td align="right"><?= number_format($a31 - $a41); ?></td>
															<td align="right"><?= $b31; ?>%</td>
														</tr>
														<tr bgcolor="#D4A8FF">
															<td>SP APP売上</td>
															<td align="right"><?= number_format($a32); ?></td>
															<td align="right"><?= number_format($a42); ?></td>
															<td align="right"><?= number_format($a32 - $a42); ?></td>
															<td align="right"><?= $b32; ?>%</td>
														</tr>
														<tr bgcolor="#99E8E6">
															<td>PC広告売上</td>
															<td align="right"><?= number_format($a33); ?></td>
															<td align="right"><?= number_format($a43); ?></td>
															<td align="right"><?= number_format($a33 - $a43); ?></td>
															<td align="right"><?= $b33; ?>%</td>
														</tr>
														<tr bgcolor="#EAC9FF">
															<td>その他売上</td>
															<td align="right"><?= number_format($a34); ?></td>
															<td align="right"><?= number_format($a44); ?></td>
															<td align="right"><?= number_format($a34 - $a44); ?></td>
															<td align="right"><?= $b34; ?>%</td>
														</tr>
														<tr bgcolor="#E6FFDF">
															<td align="center">總計</td>
															<td align="right"><?= number_format($total1); ?></td>
															<td align="right"><?= number_format($total2); ?></td>
															<td align="right"><?= number_format($total3); ?></td>
															<td align="right"><?= number_format($total4); ?>%</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									<? else : ?>
										<? if (isset($_POST['search2'])) : ?>
											<?php
												$revenue = [];
												$cost = [];
												
												$dbMedia = clone($GLOBALS['app']->db);

												$sqlCampaign = 'SELECT * FROM campaign 
																WHERE status <> 8 AND status >= 2 AND status <= 5 '. $search3;
												
												$db->query($sqlCampaign);
												while ($row2 = $db->next_record()) {
													$rowsAccounting = $objMediaAccounting->getList($row2['id']);
													$rowsOrdinal = GetUsedMediaOrdinal($row2['id'], 'id', $_POST['search2']);
													foreach ($rowsOrdinal as $ordinal) {
														$sqlMediaItem = sprintf('SELECT * FROM `media%d` WHERE `cue` = 2 AND `campaign_id` = %d;', $ordinal, $row2['id']);
														$dbMedia->query($sqlMediaItem);
														while ($row22 = $dbMedia->next_record()) {
															if (isset($rowsAccounting[$ordinal][$row22['id']][$dateYearMonth])) {
																$mediaInfo[$ordinal] = GetMedia($ordinal);

																if (!isset($revenue[$ordinal])) {
																	$revenue[$ordinal] = 0;
																}
																$revenue[$ordinal] += isset($rowsAccounting[$ordinal][$row22['id']][$dateYearMonth]['accounting_revenue']) ? $rowsAccounting[$ordinal][$row22['id']][$dateYearMonth]['accounting_revenue'] : 0;
																
																if (!isset($cost[$ordinal])) {
																	$cost[$ordinal] = 0;
																}
																$cost[$ordinal] += isset($rowsAccounting[$ordinal][$row22['id']][$dateYearMonth]['accounting_cost']) ? $rowsAccounting[$ordinal][$row22['id']][$dateYearMonth]['accounting_cost'] : 0;
															}
														}
													}
												}
												ksort($revenue);
												foreach ($revenue as $ordinal => $price) {
													$profit[$ordinal] = $revenue[$ordinal] - $cost[$ordinal];
													$grossMargin[$ordinal] = $revenue[$ordinal] ? round((($profit[$ordinal] / $revenue[$ordinal]) * 100), 2) : 0;
												}
											?>
											<table border="1" >
												<tr>
													<td>媒體</td>
													<td align="center">銷售</td>
													<td align="center">成本</td>
													<td align="center">毛利</td>
													<td align="center">毛利率</td>
												</tr>
												<? foreach ($revenue as $ordinal => $price) : ?>
													<tr>
														<td>
															<!-- 媒體 -->
															<?= $mediaInfo[$ordinal]['name']; ?>
														</td>
														<td align="right">
															<!-- 銷售 -->
															<?= number_format($revenue[$ordinal]); ?>
														</td>
														<td align="right">
															<!-- 成本 -->
															<?= number_format($cost[$ordinal]); ?>
														</td>
														<td align="right">
															<!-- 毛利 -->
															<?= number_format($profit[$ordinal]); ?>
														</td>
														<td align="right">
															<!-- 毛利率 -->
															<?= $grossMargin[$ordinal]; ?>%
														</td>
													</tr>
												<? endforeach; ?>
											</table>
										<? endif; ?>
									<? endif; ?>
								</div>
							</div>
						</div>
					<? endif; ?>
				
					<? if (isset($_POST['search4'])) : ?>
						<div class="row-fluid">
							<div class="box span12">
								<div class="box-header well" data-original-title>
									<h2><i class="icon-edit"></i> 案件列表</h2>
								</div>
								<div class="box-content">
									<div id="top_pagination"><?= $pagination->setConfig(array('start' => 0, 'total' => 0))->getTopContent(); ?></div>
									<link rel="stylesheet" href="../css/font-awesome-4.7.0/css/font-awesome.min.css">
									<table class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>
													<a href="#" onclick="checkOrder('`campaign`.`agency`');">代理商</a>
												</th>
												<th>
													<a href="#" onclick="checkOrder('`campaign`.`client`');">廣告主</a>
												</th>
												<th>
													<a href="#" onclick="checkOrder('`campaign`.`name`');">活動名稱</a>
												</th>
												<th>
													<a href="#" onclick="checkOrder('`campaign`.`date1`');">期間</a>
												</th>
												<th>
													<a href="#" onclick="checkOrder('`campaign`.`member`');">負責業務</a>
												</th>
												<th>
													<a href="#" onclick="checkOrder('`campaign`.`status`');">狀態</a>
												</th>
												<th>Actions</th>
											</tr>
										</thead>   
										<tbody id="content_empty" style="display: none;">
											<tr>
											<td colspan="7">No data available in tables</td>
											</tr>
										</tbody>
										<tbody id="content_loader">
											<tr>
											<td colspan="7"><i class="fa fa-spin fa-refresh" style="font-size: 3em; padding: 20px;"></i></td>
											</tr>
										</tbody>
										<tbody id="content_list"></tbody>
									</table>            
									<div id="bottom_pagination"><?= $pagination->setConfig(array('start' => 0, 'total' => 0))->getBottomContent(); ?></div>
								</div>
							</div>
						</div>
					<? endif; ?>
				</div>
			</div>
			<hr/>

			<?php include("public/footer.php"); ?>
		</div>

		<?php include("public/js.php"); ?>
		<script>
			orderby = '<?= $orderby; ?>';
			orderdir = '<?= $orderdir; ?>';
			keyword = '';
			<? if (isset($_POST['search4'])) : ?>
				extraCondition = <?= json_encode($extraCondition); ?>;
				goToPage(1);
			<? else : ?>
				jQuery('#content_loader').hide();
				jQuery('#content_list').hide();
			<? endif; ?>
		</script>
	</body>
</html>
