<?php 
	
	// 2017-10-18 (Jimmy): 傑思jsadways2/campaign_listall4.php, 香港jsadways2hk/campaign_listall4.php, 豐富媒體jsadways2ff/campaign_listall4.php 共用此檔案
	require_once dirname(__DIR__) .'/autoload.php';
	
	$db = clone($GLOBALS['app']->db);

	$objMediaAccounting = CreateObject('MediaAccounting');
	$dateYearMonth = sprintf('%04d%02d', $_REQUEST['search3'], $_REQUEST['search4']);

	global $requireCharisma;
	$requireCharisma = false;

	include_once(__DIR__ .'/include/pagination.inc.php');
	$pagination = new pagination();

	$isAjaxRequest = isset($_REQUEST['ajax']) ? true : false;
	$orderby = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : '`campaign`.`date1`';
	$orderdir = isset($_REQUEST['orderdir']) ? $_REQUEST['orderdir'] : 'DESC';

	if ($isAjaxRequest) {
		ini_set('display_errors', 1);
        include_once(__DIR__ .'/include/twig.inc.php');

		$keyword = isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
        $page = (!is_numeric($page) || $page <= 0) ? 1 : (int)$page;
        $rowsStart = 0;
        $rowsMaxNum = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 15;
        $rowsStart = ($page - 1) * $rowsMaxNum;

		$extraCondition = isset($_POST['extraCondition']) ? $_POST['extraCondition'] : [];

		$memberId = isset($extraCondition['search2']) ? $extraCondition['search2'] : $_SESSION['userid'];
		$sqlCondition = sprintf(' `memberid` IN (%s) AND `status` <> 8 AND `status` >= 2 AND `status` <= 5', $memberId);

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
                '`campaign`.`contact1`',
                '`campaign`.`contact2`',
                '`campaign`.`contact3`',
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
                        WHERE '. $sqlCondition .' AND '. $sqlFilter .'
                        GROUP BY `campaign`.`id`';

            $sqlCondition .=  sprintf(' AND `campaign`.`id` IN (%s)', $sqlSearch);
        }

		$sqlTotalCampaign = 'SELECT COUNT(*) as `total` FROM `campaign` WHERE '. $sqlCondition;
        $sqlRowsCampaign = 'SELECT * FROM `campaign` WHERE '. $sqlCondition . sprintf(' ORDER BY %s %s ', $orderby, $orderdir) . sprintf(' LIMIT %d, %d', $rowsStart, $rowsMaxNum);

		$db->query($sqlTotalCampaign);
		$itemTotalCampaign = $db->next_record();
        $totalCampaignNum = $itemTotalCampaign['total'];

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

	if (isset($_POST['search4'])) {
		$extraCondition['search3'] = isset($_POST['search3']) ? $_POST['search3'] : '';
		$extraCondition['search4'] = $_POST['search4'];

		if (isset($_POST['search2'])) {
			$extraCondition['search2'] = $_POST['search2'];
		}
	}

	$campaignOwner = $_SESSION['userid'];
	
	require_once dirname(__DIR__) .'/classes/Permission.php';
	$objPermission = new Permission();    
	$listCampaignGroup = $objPermission->getData('backend_campaign_list_group', $_SESSION['userid']);

	if ($listCampaignGroup) {
		$campaignOwner .= ','. $listCampaignGroup;
	}

	$objMrbsUser = CreateObject('MrbsUsers');
	$rowsUser = $objMrbsUser->searchAll("`id` IN ($campaignOwner)");

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>【<?= $GLOBALS['env']['flag']['name']; ?>】月報表</title>
		<?php include("public/head.php"); ?>
	</head>
	<body>
		<?php include("public/topbar.php"); ?>
		
		<div class="container-fluid">
			<div class="row-fluid">
				<?php include("public/left.php"); ?>
				
				<div id="content" class="span10">
					<div class="row-fluid">
						<div class="box span6">
							<div class="box-header well" data-original-title>
								<h2><i class="icon-edit"></i> 查詢條件</h2>
							</div>
							<div class="box-content">
								<form class="form-horizontal" action="campaign_listall4.php" method="post">
									<fieldset> 
										<div class="control-group">
											<label class="control-label">搜尋條件：</label>
										</div>

										<? if ($listCampaignGroup) : ?>
											<div class="control-group">
												<label class="control-label"></label>
												<div class="controls">
													<select id="search2" name="search2">
														<? foreach ($rowsUser as $itemUser) : ?>
															<option value="<?= $itemUser['id']; ?>" <?= $_POST['search2'] == $itemUser['id'] ? 'selected' : ''; ?>><?= ucfirst($itemUser['name']) .' '. $itemUser['username']; ?></option>
														<? endforeach; ?>
														<option value="<?= $campaignOwner; ?>" <?= $_POST['search2'] == $campaignOwner ? 'selected' : ''; ?>>全部組員</option>
													</select>年
												</div>
											</div>
										<? endif; ?>

										<div class="control-group">
											<label class="control-label"></label>
											<div class="controls">
												<select id="search3" name="search3">
													<? for ($idx=$GLOBALS['env']['flag']['begin_year']; $idx<=date('Y'); $idx++) : ?>
														<option value="<?= $idx; ?>" <?= isset($_POST['search3']) ? ($_POST['search3'] == $idx ? 'selected' : '') : ($idx == date('Y') ? 'selected' : ''); ?>><?= $idx; ?></option>
													<? endfor; ?>
												</select>年
											</div>
										</div>

										<div class="control-group">
											<label class="control-label"></label>
											<div class="controls">
												<select id="search4" name="search4">
													<? for ($idx=1; $idx<=12; $idx++) : ?>
														<option value="<?= $idx; ?>" <?= isset($_POST['search4']) ? ($_POST['search4'] == $idx ? 'selected' : '') : ($idx == date('m') ? 'selected' : ''); ?>><?= $idx; ?></option>
													<? endfor; ?>
												</select>月
											</div>
										</div>

										<div class="control-group">
											<div class="controls">
												<button type="submit" class="btn btn-primary">查詢</button>
											</div>
										</div>

										<div class="control-group">
											<div class="controls">
												<a  href="excel/print3.php?search3=<?= $_POST['search3']; ?>&search4=<?= $_POST['search4']; ?><?= isset($_POST['search2']) ? "&search2={$_POST['search2']}" : '' ?>" target="_blank">輸出excel</a>
											</div>
										</div>
									</fieldset>
								</form>
							</div>
						</div>
						
						<? if (in_array($_SESSION['userid'], [30, 27, 22]) || $_SESSION['usergroup'] == 6) : ?>
							<div class="box span6">
								<div class="box-header well" data-original-title>
									<h2><i class="icon-edit"></i> 查詢條件</h2>
								</div>
								<div class="box-content">
									<form class="form-horizontal" action="campaign_listall3.php" method="post">
										<fieldset> 
											<div class="control-group">
												<label class="control-label">搜尋：AE</label>
												<div class="controls">
													<select id="search2" name="search2">
														<? foreach ($objMrbsUser->searchAll("`status` = 1 AND `Sec_manage` = {$_SESSION['userid']}") as $row6) : ?>
															<option value="<?= $row6['id']; ?>" <?= ($_POST['search2'] == $row6['id']) ? 'selected' : ''; ?>><?= $row6['name']; ?>【<?= $row6['username']; ?>】</option>
														<? endforeach; ?>
													</select>
													<input type="hidden" value="1" id="search5" name="search5">
												</div>
											</div>
											
											<div class="control-group">
												<label class="control-label"></label>
												<div class="controls">
													<select id="search3" name="search3">
														<? for ($idx=$GLOBALS['env']['flag']['begin_year']; $idx<=date('Y'); $idx++) : ?>
															<option value="<?= $idx; ?>" <?= isset($_POST['search3']) ? ($_POST['search3'] == $idx ? 'selected' : '') : ($idx == date('Y') ? 'selected' : ''); ?>><?= $idx; ?></option>
														<? endfor; ?>
													</select>年
												</div>
											</div>
											
											<div class="control-group">
												<label class="control-label"></label>
												<div class="controls">
													<select id="search4" name="search4">
														<? for ($idx=1; $idx<=12; $idx++) : ?>
															<option value="<?= $idx ?>" <?= isset($_POST['search4']) ? ($_POST['search4'] == $idx ? 'selected' : '') : ($idx == date('m') ? 'selected' : ''); ?>><?= $idx; ?></option>
														<? endfor; ?>
													</select>月
												</div>
											</div>
											
											<div class="control-group">
												<div class="controls">
													<button type="submit" class="btn btn-primary">查詢</button>
												</div>
											</div>
											
											<? if ($_POST['search5'] == 1) : ?>
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
						<? endif; ?>
					</div>
			
					<? if (isset($_POST['search4'])) : ?>
						<div class="row-fluid">
							<div class="box span12">
								<div class="box-header well" data-original-title>
									<h2><i class="icon-edit"></i> 案件列表</h2>
								</div>
								<div class="box-content">
									<div id="top_pagination"><?= $pagination->setConfig(array('start' => 0, 'total' => 0))->getTopContent(); ?></div>
									<link rel="stylesheet" href="../css/font-awesome-4.7.0/css/font-awesome.min.css">
									<table class="table table-striped table-bordered bootstrap-datatable datatable">
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
											<td colspan="7">No data available in table</td>
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