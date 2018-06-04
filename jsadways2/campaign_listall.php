<?php

	require_once dirname(__DIR__) .'/autoload.php';
	include('include/db.inc.php');

    include_once(__DIR__ .'/include/pagination.inc.php');
    $pagination = new pagination();
    
    $groupList = [];
    if (isset($_REQUEST['group'])) {
        $groupList = $GLOBALS['app']->permission->getData('backend_campaign_list_group', $_SESSION['userid']);
	}    
    
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

        $extraJoin = '';
        
        $sqlCondition = '';
        
        if (isset($_REQUEST['group'])) {
            $specificId = isset($_REQUEST['extraCondition']['specific_id']) ? $_REQUEST['extraCondition']['specific_id'] : 0;
            
            if ($specificId == 'all') {
                if ($groupList) {
                    $sqlCondition .= " `campaign`.`memberid` IN ($groupList) AND ";
                } else {
                    $sqlCondition .= " `campaign`.`memberid` IN (-1) AND ";
                }
            } else {
                $specificId = base64_decode($specificId);

                if (IsId($specificId)) {
                    $sqlCondition .= " `campaign`.`memberid` = $specificId AND ";
                }
            }
        }

        if(isset($_GET['is_receipt'])){
            $sqlCondition .= ' `campaign`.`status` ='. (int)$_GET['status'] .' AND `campaign`.`is_receipt` = '. (int)$_GET['is_receipt'] .' AND `receipt`.`receipt3id` <> 0 ';
            $extraJoin = ' LEFT JOIN `receipt` ON `campaign`.`id` = `receipt`.`campaign_id` ';
        } else {
            if (isset($_GET['status'])) {
                if ($_GET['status'] == 90) {
                    $sqlCondition .= ' `campaign`.`exchang_math` <> 0 ';
                } else {
                    $sqlCondition .= ' `campaign`.`status` ='. (int)$_GET['status'] .' AND `campaign`.`version` = 2 ';
                }
            } else {
                $sqlCondition .= ' `campaign`.`status` >= 2 AND `campaign`.`status` <= 6'.' AND `campaign`.`version` = 2 ';
            }
        }

        if (!empty($keyword)) {
            $columnSearch = [
                '`campaign`.`name`',
                '`campaign`.`agency`',
                '`campaign`.`client`',
                '`campaign`.`member`',
                '`campaign`.`contact1`',
                '`campaign`.`tagtext`',
                '`receipt`.`receipt_number`'
            ];

            $sqlFilter = [];
            foreach ($columnSearch as $name) {
                $sqlFilter[] = $name ."LIKE '%". mysql_real_escape_string($keyword) ."%'";
            }

			if ($keyword == '直客') {
				$sqlFilter[] = " `campaign`.`agency` = '' ";
				$sqlFilter[] = " `campaign`.`agency` IS NULL ";
			}

            $sqlFilter = ' ('. implode(' OR ', $sqlFilter) .') ';

            $sqlSearch = 'SELECT `campaign`.`id` FROM `campaign`
                        LEFT JOIN `receipt` ON `receipt`.`campaign_id` = `campaign`.`id`
                        WHERE '. $sqlCondition .' AND '. $sqlFilter .'
                        GROUP BY `campaign`.`id`';

            $sqlCondition .=  sprintf(' AND `campaign`.`id` IN (%s)', $sqlSearch);
        }

        $sqlTotalCampaign = 'SELECT COUNT(*) as `total` FROM `campaign` '. $extraJoin .' WHERE'. $sqlCondition;
        $sqlRowsCampaign = 'SELECT `campaign`.* FROM `campaign` '. $extraJoin .' WHERE '. $sqlCondition . sprintf(' ORDER BY %s %s ', $orderby, $orderdir) . sprintf(' LIMIT %d, %d', $rowsStart, $rowsMaxNum);

        $resultTotalCampaign = mysql_query($sqlTotalCampaign);
        $itemTotalCampaign = mysql_fetch_array($resultTotalCampaign);
        $totalCampaignNum = $itemTotalCampaign['total'];

        $idsCampaign = [0];
        $rowsCampaign = [];
        $resultRowsCampaign = mysql_query($sqlRowsCampaign);
        if (mysql_num_rows($resultRowsCampaign) > 0) {
            while ($itemCampaign = mysql_fetch_array($resultRowsCampaign)) {
                $rowsCampaign[] = $itemCampaign;

                if (is_numeric($itemCampaign['id'])) {
                    array_push($idsCampaign, $itemCampaign['id']);
                }
            }
        }

        $sqlReceipt = 'SELECT `receipt`.`campaign_id`, `receipt`.`status`, `receipt`.`receipt3id`, 
                        `receipt`.`totalprice1`, `receipt`.`receipt_number`, 
                        `r3`.`times1` as `receipt3_times1`, `r3`.`times2` as `receipt3_times2`
                        FROM `receipt`
                        LEFT JOIN `receipt3` `r3` ON `receipt`.`receipt3id` = `r3`.`id`
                        WHERE `receipt`.`campaign_id` IN ('. implode(',', $idsCampaign) .')';

        $idsReceipt = [0];
        $rowsReceipt = [];
        $rowsReceipt3 = [];
        $resultReceipt = mysql_query($sqlReceipt);
        if (mysql_num_rows($resultReceipt) > 0) {
            while ($itemReceipt = mysql_fetch_array($resultReceipt)) {
                $columnReceipt = [];
                $columnReceipt3 = [];
                foreach ($itemReceipt as $columnName => $columnValue) {
                    if (startsWith($columnName, 'receipt3_')) {
                        $columnReceipt3[$columnName] = $columnValue;
                    } else {
                        $columnReceipt[$columnName] = $columnValue;
                    }
                }

                $rowsReceipt[$itemReceipt['campaign_id']][] = $columnReceipt;
                $rowsReceipt3[$itemReceipt['receipt3id']] = $columnReceipt3;
            }
        }

        $pagination->setConfig([
            'start' => ($page - 1) * $rowsMaxNum,
            'total' => $totalCampaignNum,
            'limit' => $rowsMaxNum
        ]);
        $sectionBottomPagination = $pagination->getBottomContent();

        $twig = new twig('campaign_listall.html', [
            'session' => $_SESSION,
            'rowsCampaign' => $rowsCampaign,
            'rowsReceipt' => $rowsReceipt,
            'rowsReceipt3' => $rowsReceipt3,
            'sectionBottomPagination' => $sectionBottomPagination
        ]);

        $output = [
            'top_pagination' => '',
            'content_list' => $twig->getContent(),
            'bottom_pagination' => $sectionBottomPagination
        ];

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($output));
    }
    
    $objMrbsUsers = CreateObject('MrbsUsers');

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>案件列表<?= $_SESSION['version']; ?></title>
        <?php include("public/head.php"); ?>
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
                            <h2><i class="icon-edit"></i> 案件列表</h2>
                        </div>
                        
                        <div class="box-content">
                            <? if ($groupList) : ?>
                                <select style="background-color: #fffff2;" onchange="ChangeGroupUser(this);">
                                    <option value="all">全部組員</option>
                                    <? foreach ($objMrbsUsers->searchAll("`id` IN ($groupList) AND (`user_resign_date` IS NULL OR `user_resign_date` = '0000-00-00')", '', '', '', '', 'id, name, username') as $itemUser) : ?>
                                        <option value="<?= base64_encode($itemUser['id']); ?>"><?= ucfirst($itemUser['name']) . $itemUser['username']; ?></option>
                                    <? endforeach; ?>
                                </select>
                            <? endif; ?>

                            <div id="top_pagination">
                                <?= $pagination->setConfig(['start' => 0, 'total' => 0])->getTopContent(); ?>
                            </div>
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
                                        <th width="140px">
                                            <a href="#" onclick="checkOrder('`campaign`.`date1`');">期間</a>
                                        </th>
                                        <th width="65px">
                                            <a href="#" onclick="checkOrder('`campaign`.`time`');">建立日期</a>
                                        </th>
                                        <th>
                                            <a href="#" onclick="checkOrder('`campaign`.`member`');">負責業務</a>
                                        </th>
                                        <th>
                                            發票
                                        </th>
                                        <th>
                                            <a href="#" onclick="checkOrder('`campaign`.`status`');">狀態</a>
                                        </th>
                                        <th width="80px">
                                            <a href="#" onclick="checkOrder('`campaign`.`tagtext`');">分類</a>
                                        </th>
                                        <th width="180px">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="content_empty" style="display: none;">
                                    <tr>
                                        <td colspan="10">
                                            No data available in table
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody id="content_loader">
                                    <tr>
                                    <td colspan="10">
                                        <i class="fa fa-spin fa-refresh" style="font-size: 3em; padding: 20px;"></i>
                                    </td>
                                    </tr>
                                </tbody>
                                <tbody id="content_list">
                                </tbody>
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

	<?php include("public/js.php"); ?>
    <script>
        function ChangeGroupUser(groupMemberSelector)
        {
            extraCondition['specific_id'] = groupMemberSelector.value;
            goToPage(1);
        }

        orderby = '<?= $orderby; ?>';
        orderdir = '<?= $orderdir; ?>';
        keyword = '';
        extraCondition['specific_id'] = 'all';
        jQuery('#content_loader').hide();
		jQuery('#content_list').hide();
        goToPage(1);
    </script>
</body>
</html>
