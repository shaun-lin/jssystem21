<?php 
	session_start();
	include('include/db.inc.php');

	// 2017-05-05 (Jimmy): perform loading balance
	global $requireCharisma;
	$requireCharisma = false;

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

		$sqlCondition = '';

		$extraCondition = isset($_POST['extraCondition']) ? $_POST['extraCondition'] : [];
		if (isset($extraCondition['search0']) && $extraCondition['search0']) {
			$sqlCondition = ' AND idnumber = "'. $extraCondition['search0'] .'"';
		}

		if (isset($extraCondition['search1']) && $extraCondition['search1']) {
			$sqlCondition .= " AND name LIKE '%". mysql_real_escape_string($extraCondition['search1']). "%'";
		}

		if (isset($extraCondition['search2']) && $extraCondition['search2']) {
			$sqlCondition .= " AND date1 LIKE '%". mysql_real_escape_string($extraCondition['search2']) ."%'";	
		}

		if (isset($extraCondition['search3']) && $extraCondition['search3']) {
			$sqlCondition .= " AND tagtext LIKE '%". mysql_real_escape_string($extraCondition['search3']) ."%'";
		}

		if (!isset($extraCondition['search4']) || $extraCondition['search4'] == 0) {
			$sqlCondition .= ' AND status >= 2 AND status <= 7';	
		} else {
			$sqlCondition .= ' AND status = '. (int)$extraCondition['search4'];
		}

		if (!empty($keyword)) {
            $columnSearch = array(
                '`campaign`.`name`',
                '`campaign`.`agency`',
                '`campaign`.`client`',
                '`campaign`.`member`',
                '`campaign`.`contact1`',
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

		$resultTotalCampaign = mysql_query($sqlTotalCampaign);
        $itemTotalCampaign = mysql_fetch_array($resultTotalCampaign);
        $totalCampaignNum = $itemTotalCampaign['total'];

		$idsCampaign = array(0);
        $rowsCampaign = array();
        $resultRowsCampaign = mysql_query($sqlRowsCampaign);
        if (mysql_num_rows($resultRowsCampaign) > 0) {
            while ($itemCampaign = mysql_fetch_array($resultRowsCampaign)) {
                $rowsCampaign[] = $itemCampaign;
            }
        }

		$pagination->setConfig(array(
            'start' => ($page - 1) * $rowsMaxNum,
            'total' => $totalCampaignNum,
            'limit' => $rowsMaxNum
        ));
        $sectionBottomPagination = $pagination->getBottomContent();

        $twig = new twig('campaign_listall2.html', array(
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>案件列表<?php echo $_SESSION['version']; ?></title>
	<?php include("public/head.php"); ?>
		
</head>

<body>
	<?php include("public/topbar.php"); ?>
		<div class="container-fluid">
		<div class="row-fluid">
			<?php include("public/left.php"); ?>
			
			<noscript>
				<div class="alert alert-block span10">
					<h4 class="alert-heading">Warning!</h4>
					<p>You need to have <a href="http://en.wikipedia.org/wiki/JavaScript" target="_blank">JavaScript</a> enabled to use this site.</p>
				</div>
			</noscript>
			
			<div id="content" class="span10">
			<!-- content starts -->
           	<div class="row-fluid">
				<div class="box span12">
					<div class="box-header well" data-original-title>
						<h2><i class="icon-edit"></i> 查詢條件</h2>
						
					</div>
					<div class="box-content">
                    <form class="form-horizontal" action="campaign_listall2.php" method="post">
                      <fieldset> 
                      	<div class="control-group">
                            <label class="control-label">搜尋：委刊編號</label>
                            <div class="controls">
                              <input class="input-xlarge" id="search0" name="search0" type="text" value="<?php echo isset($_POST['search0']) ? $_POST['search0'] : '' ?>">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">搜尋：案件名稱關鍵字</label>
                            <div class="controls">
                              <input class="input-xlarge" id="search1" name="search1" type="text" value="<?php echo isset($_POST['search1']) ? $_POST['search1'] : '' ?>">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label">搜尋：案件執行期間</label>
                            <div class="controls">
                              <input class="input-xlarge" id="search2" name="search2" type="text" value="<?php echo isset($_POST['search2']) ? $_POST['search2'] : '' ?>"> EX:12/09/2013
                            </div>
                        </div>
                        <div class="control-group">
                          <label class="control-label">搜尋：分類</label>
                          <div class="controls">
                             <select id="search3" name="search3">
                             	<option value="">全部</option>
                                <?php
                                    $sql6='SELECT * FROM class2';
                                    $result6=mysql_query($sql6); 
                                    if (mysql_num_rows($result6)>0){
                                        while($row6=mysql_fetch_array($result6)){
                                ?>
                                <option value="<?php echo $row6['name']; ?>" <?php echo isset($_POST['search3']) && $_POST['search3'] == $row6['name'] ? 'selected' : '' ?>><?php echo $row6['name']; ?></option>
                                <?php
                                        }
                                    }
                                ?>
                            </select>
                          </div>
                        </div>
                        <div class="control-group">
                          <label class="control-label">搜尋：案件狀態</label>
                          <div class="controls">
                             <select id="search4" name="search4">
                                <option value="0">全部</option>
                                <option value="4" <?php echo isset($_POST['search4']) && $_POST['search4'] == 4 ? 'selected' : '' ?>>結案</option>
                                <option value="3" <?php echo isset($_POST['search4']) && $_POST['search4'] == 3 ? 'selected' : '' ?>>執行中</option>
                                <option value="2" <?php echo isset($_POST['search4']) && $_POST['search4'] == 2 ? 'selected' : '' ?>>送審中</option>
                            </select>
                          </div>
                        </div>
                        <div class="control-group">
                        	<div class="controls">
                          		<button type="submit" class="btn btn-primary">查詢</button>
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
						<h2><i class="icon-edit"></i> 案件列表</h2>
						
					</div>
					<div class="box-content">
						<div id="top_pagination">
                            <?php
                                echo $pagination->setConfig(array('start' => 0, 'total' => 0))->getTopContent();
                            ?>
                        </div>
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
									  <a href="#" onclick="checkOrder('`campaign`.`tagtext`');">分類</a>
								  </th>
								  <th>
									  <a href="#" onclick="checkOrder('`campaign`.`member`');">負責業務</a>
								  </th>
								  <th>
									  <a href="#" onclick="checkOrder('`campaign`.`status`');">狀態</a>
								  </th>
                                  <th>是否歸檔</th>
                                  <?php if($_SESSION['usergroup']==4){ ?><th>開發票</th><?php }?>
								  <th>Actions</th>
							  </tr>
						  </thead>   
						  <tbody id="content_empty" style="display: none;">
							<tr>
                              <td colspan="9">
                                No data available in table
                              </td>
                            </tr>
						  </tbody>
						  <tbody id="content_loader">
						  	<tr>
                              <td colspan="9">
                                <i class="fa fa-spin fa-refresh" style="font-size: 3em; padding: 20px;"></i>
                              </td>
                            </tr>
						  </tbody>
						  <tbody id="content_list">
						  </tbody>
					  </table>            
					  <div id="bottom_pagination">
                          <?php
                              echo $pagination->setConfig(array('start' => 0, 'total' => 0))->getBottomContent();
                          ?>
                      </div>
					</div>
				</div><!--/span-->

			</div><!--/row-->

		
    
					<!-- content ends -->
			</div><!--/#content.span10-->
				</div><!--/fluid-row-->
				
		<hr>

		<?php include("public/footer.php"); ?>
		
	</div><!--/.fluid-container-->

	<?php include("public/js.php"); ?>
	<script>
        orderby = '<?php echo $orderby; ?>';
        orderdir = '<?php echo $orderdir; ?>';
        keyword = '';
		<?php
			if (isset($_POST['search4'])) {
				?>
					extraCondition = <?php echo json_encode($extraCondition); ?>;
					goToPage(1);
				<?php
			} else {
				?>
					jQuery('#content_loader').hide();
					jQuery('#content_list').hide();
					jQuery('#content_empty').show();
				<?php
			}
		?>
    </script>
</body>
</html>
