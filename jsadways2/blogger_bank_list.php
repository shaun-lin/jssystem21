<?php
	
	require_once dirname(__DIR__) .'/autoload.php';

	$db = clone($GLOBALS['app']->db);

	if (isset($_GET['list'])) {
		$back_search = explode(",", $_GET['list']);
	}

	if (isset($_GET['youtube'])) {
		$back_search_youtube = explode(",", $_GET['youtube']);
	}

	$isGrantEdit = $_SESSION['usergroup'] == 4 || IsPermitted('backend_blogger', null, 'edit');

	include_once(__DIR__ .'/include/pagination.inc.php');
	$pagination = new pagination();
	
	$isAjaxRequest = isset($_REQUEST['ajax']) ? true : false;
    $orderby = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : '`blogger_bank`.`id`';
	$orderdir = isset($_REQUEST['orderdir']) ? $_REQUEST['orderdir'] : 'DESC';
	
	if ($isAjaxRequest) {
        include_once(__DIR__ .'/include/twig.inc.php');

        $keyword = isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
        $page = (!is_numeric($page) || $page <= 0) ? 1 : (int)$page;
        $rowsStart = 0;
        $rowsMaxNum = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 30;
        $rowsStart = ($page - 1) * $rowsMaxNum;

		$sqlCondition = '`blogger_bank`.`states` = 1';

		if (!empty($keyword)) {
            $columnSearch = [
				'`blogger_bank`.`bankUserName`',
				'`blogger_bank`.`bankName`',
				'`blogger_bank`.`bankAC`',
			];

            $sqlFilter = [];
            foreach ($columnSearch as $name) {
                $sqlFilter[] = $name ." LIKE ". SqlQuote($keyword, true);
			}

            $sqlCondition .= ' AND ('. implode(' OR ', $sqlFilter) .') ';
		}
		
		$sqlTotalBloggerBank = 'SELECT COUNT(*) as `total` FROM `blogger_bank`  WHERE '. $sqlCondition;
		$sqlRowsBloggerBank = 'SELECT * FROM `blogger_bank` WHERE '. $sqlCondition . sprintf(' ORDER BY %s %s ', $orderby, $orderdir) . sprintf(' LIMIT %d, %d', $rowsStart, $rowsMaxNum);
		
		$db->query($sqlTotalBloggerBank);
        $itemTotalBloggerBank = $db->next_record();
		$totalBloggerBankNum = $itemTotalBloggerBank['total'];
		
		$rowsBloggerBank = [];
		$db->query($sqlRowsBloggerBank);
		while ($itemBloggerBank = $db->next_record()) {
			$rowsBloggerBank[] = $itemBloggerBank;
		}

		$pagination->setConfig([
            'start' => ($page - 1) * $rowsMaxNum,
            'total' => $totalBloggerBankNum,
            'limit' => $rowsMaxNum
		]);
        $sectionBottomPagination = $pagination->getBottomContent();

        $twig = new twig('blogger_bank_list.html', [
            'session' => $_SESSION,
            'rowsBloggerBank' => $rowsBloggerBank,
            'sectionBottomPagination' => $sectionBottomPagination,
			'isGrantEdit' => $isGrantEdit,
		]);

        $output = [
            'top_pagination' => '',
            'content_list' => $twig->getContent(),
            'bottom_pagination' => $sectionBottomPagination
		];

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($output));
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>【<?= $GLOBALS['env']['flag']['name']; ?>】寫手帳戶列表</title>
		<?php include("public/head.php"); ?>
	</head>
	<body>
		<?php include("public/topbar.php"); ?>

		<div class="container-fluid">
			<div class="row-fluid">
				<?php include("public/left.php"); ?>
					
				<div id="content" class="span10">
					<? if ($_SESSION['usergroup'] >= 3) : ?>
						<div class="row-fluid" style="display:none;">
							<div class="box span12">
								<div class="box-header well" data-original-title>
									<h2><i class="icon-th"></i> Action</h2>
								</div>
								<div class="box-content">
									<div class="row-fluid">
										<a class="btn btn-info" href="blogger_bank_add.php">
											<i class="icon-edit icon-white"></i>  
											新增銀行資料
										</a>
									</div>                   
								</div>
							</div>
						</div>
					<? endif; ?>

					<div class="row-fluid">
						<div class="box span12">
							<div class="box-header well " data-original-title>
								<h2><i class="fa fa-credit-card"></i>&nbsp;&nbsp;&nbsp;帳戶列表</h2>
							</div>
							<div class="box-content">
								<link rel="stylesheet" type="text/css" href="js/jquery-ui.css">
								<script type="text/javascript" charset="utf8" language="javascript" src="../resources/js/jquery-1.9.1.min.js"></script>
								<style>
									div#top_pagination {
										border: 1px solid #aaaaaa;
										background: #cccccc url(images/ui-bg_highlight-soft_75_cccccc_1x100.png) 50% 50% repeat-x;
										color: #222222;
										margin-top: 5px;
										border-top-left-radius: 5px;
										border-top-right-radius: 5px;
									}

									div#top_pagination div.span6 {
										padding-top: 6px;
										padding-left: 6px;
										padding-right: 6px;
									}

									div#top_pagination div.span6:nth-child(1) {
										padding-top: 8px;
									}

									.ui-buttonset .ui-button {
										margin-left: 0;
										margin-right: 0em;
									}
								</style>
								<div id="top_pagination">
                                    <?= $pagination->setConfig(['start' => 0, 'total' => 0, 'limit' => 30])->getTopContent(); ?>
                                </div>
                                <link rel="stylesheet" href="../css/font-awesome-4.7.0/css/font-awesome.min.css">
								<table class="table table-striped table-bordered" id="example">
									<thead>
										<tr>
											<th style="width: 100px;" onclick="checkOrder('`blogger_bank`.`bankUserName`');">戶名</th>
											<th onclick="checkOrder('`blogger_bank`.`bankName`');">銀行別</th>
											<th style="width: 50px;" onclick="checkOrder('`blogger_bank`.`bankAC`');">銀行帳號</th>
											<th style="width: 20px;" onclick="checkOrder('`blogger_bank`.`invoice`');">發票</th>
											<th onclick="checkOrder('`blogger_bank`.`health`');">工會證明(免扣二代健保)</th>
											<th onclick="checkOrder('`blogger_bank`.`rt`');">實拿</th>
											<th onclick="checkOrder('`blogger_bank`.`shared`');">是否為共用</th>
											<th style="width: 150px;">Actions</th>
										</tr>
									</thead>   
									<tbody id="content_empty" style="display: none;"><tr><td colspan="7">No data available in table</td></tr></tbody>
                                    <tbody id="content_loader"><tr><td colspan="7"><i class="fa fa-spin fa-refresh" style="font-size: 3em; padding: 20px;"></i></td></tr></tbody>
                                    <tbody id="content_list"></tbody>
								</table>   
                                <div id="bottom_pagination">
                                    <?= $pagination->setConfig(['start' => 0, 'total' => 0])->getBottomContent(); ?>
                                </div>         
							</div>
						</div>
					</div>
				</div>
			</div>
			<hr/>

			<?php include("public/footer.php"); ?>
		</div>
		<script>
            orderby = '<?= $orderby; ?>';
            orderdir = '<?= $orderdir; ?>';
            keyword = '';
            jQuery('#content_loader').hide();
            jQuery('#content_list').hide();
			goToPage(1);
			
			$.ajax({
				url: 'public/left_detail.php',
				type: 'GET',
				success: function(data) {
					if (typeof data == 'object') {
						var nav = $('ul.nav.nav-tabs');

						for (var idx in data) {
							$(nav).find('span.badge-num.'+ idx).each(function() {
								$(this).html(data[idx]);
							});
							
						}
					}
				},
				error: function() {

				}
			});
        </script>
	</body>
</html>