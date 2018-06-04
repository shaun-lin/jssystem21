<?php
	
	// 2018/5/12 ken chien,新增對內的SAP成本表
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
		<title>【<?= $GLOBALS['env']['flag']['name']; ?>】_SAP成本表</title>
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

			function ExportExcel(){
				//var search2 = $("#search2").val();
				//var search3 = $("#search3").val();
				//var search4 = $("#search4").val();

				var link = 'excel/print_invoice.php?search2='+ $('#search2').val()+'&search3='+$('#search3').val()+'&search4='+$('#search4').val()+'&Finance=1';
				//alert(link);
				window.open(link, '_blank');

			}
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
									<h2><i class="fa fa-search"></i>&nbsp;&nbsp;SAP成本表_查詢條件</h2>
								</div>

								<div class="box-content">
									<form class="form-horizontal" id="frmMain" name="frmMain" method="post">
										<fieldset> 
											<div class="control-group" style="display:none;">
												<label class="control-label">搜尋：媒體</label>
												<div class="controls">
													<select id="search2" name="search2" style="width:200px">
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
													<select id="search3" name="search3" style="width:80px">
														<? for ($i=$GLOBALS['env']['flag']['begin_year']; $i<=date('Y'); $i++) : ?>
															<option value="<?= $i; ?>" <?= isset($_POST['search3']) ? ($_POST['search3'] == $i ? 'selected' : '') : ($i == date('Y') ? 'selected' : ''); ?>><?= $i; ?></option>
														<? endfor; ?>
													</select>&nbsp;&nbsp;年
												</div>
											</div>

											<div class="control-group">
												<label class="control-label"></label>
												<div class="controls">
													<select id="search4" name="search4" style="width:80px">
														<? for ($i=1; $i<=12; $i++) : ?>
															<option value="<?= $i; ?>" <?= isset($_POST['search4']) ? ($_POST['search4'] == $i ? 'selected' : '') : ($i == date('m') ? 'selected' : ''); ?>><?= $i; ?></option>
														<? endfor; ?>
													</select>&nbsp;&nbsp;月
												</div>
											</div>

											
											<div class="control-group">
												<div class="controls">
													<button type="button" class="btn btn-primary" onclick="ExportExcel();">輸出excel</button>													
												</div>
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

			<?php include("public/footer.php"); ?>
		</div>

		<?php include("public/js.php"); ?>
		<script>
			orderby = '<?= $orderby; ?>';
			orderdir = '<?= $orderdir; ?>';
			keyword = '';
			jQuery('#content_loader').hide();
			jQuery('#content_list').hide();
		</script>
	</body>
</html>
