<?php
	
	require_once dirname(__DIR__) .'/autoload.php';

	IncludeFunctions('jsadways');

	$objCategory = CreateObject('Category');
	$objTag = CreateObject('Tag', null, 'blogger');

	$db = clone($GLOBALS['app']->db);

	$isModuleManager = IsPermitted('backend_blogger', null, 'sub-supervise');
	$isGrantDelete = IsPermitted('backend_blogger', null, Permission::ACL['backend_blogger_delete']);
	$isGrantEdit = IsPermitted('backend_blogger', null, Permission::ACL['backend_blogger_edit']);

	include_once(__DIR__ .'/include/pagination.inc.php');
	$pagination = new pagination();
	
	$isAjaxRequest = isset($_REQUEST['ajax']) ? true : false;
    $orderby = '`blogger`.`ac_id`';
    $orderdir = isset($_REQUEST['orderdir']) && in_array($_REQUEST['orderdir'], ['ASC', 'DESC']) ? $_REQUEST['orderdir'] : 'DESC';

    if ($isAjaxRequest) {
		include_once(__DIR__ .'/include/twig.inc.php');
		
		$listMode = GetVar('mode');

        $keyword = isset($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;
        $page = (!is_numeric($page) || $page <= 0) ? 1 : (int)$page;
        $rowsStart = 0;
        $rowsMaxNum = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 30;
        $rowsStart = ($page - 1) * $rowsMaxNum;

		$sqlCondition = '1=1';

		$extraCondition = isset($_REQUEST['extraCondition']) ? $_REQUEST['extraCondition'] : ['advance' => [], 'class' => '', 'tag_id' => [], 'name' => ['blog_name', 'fb_name', 'ig_name', 'youtube_name']];
		if (isset($extraCondition['class']) && $extraCondition['class']) {
			$sqlCondition .= " AND `blogger`.`class` LIKE ". SqlQuote($extraCondition['class'], true);
		}

		if (isset($extraCondition['tag_id'])) {
			if (is_array($extraCondition['tag_id'])) {
				foreach ($extraCondition['tag_id'] as $idxTag => $itemTag) {
					if (!IsId($itemTag)) {
						unset($extraCondition['tag_id'][$idxTag]);
					}
				}
			} else if (IsId($extraCondition['tag_id'])) {
				$extraCondition['tag_id'] = [$extraCondition['tag_id']];
			}

			if (is_array($extraCondition['tag_id']) && count($extraCondition['tag_id'])) {
				$sqlCondition .= sprintf(" AND `blogger`.`id` IN (SELECT `map_item_id` FROM `tag_map` WHERE `map_relation` = 'blogger' AND `map_tag` IN (%s)) ", implode(', ', $extraCondition['tag_id']));
			}
		}

		$allowNames = ['blog_name' => 'blog_flow', 'fb_name' => 'fb_fans', 'ig_name' => 'ig_fans', 'youtube_name' => 'youtube_fans'];
		if (isset($extraCondition['name']) && $extraCondition['name']) {
			$nameConditions = [];

			if (is_array($extraCondition['name'])) {
				foreach ($extraCondition['name'] as $idxName => $itemName) {
					if (!array_key_exists($itemName, $allowNames)) {
						unset($extraCondition['name'][$idxName]);
					}
				}
			} else if (array_key_exists($extraCondition['name'], $allowNames)) {
				$extraCondition['name'] = [$extraCondition['name']];
			}

			foreach ($extraCondition['name'] as $itemName) {
				$nameConditions[] = " `$itemName` != '' ";
			}

			if (count($nameConditions)) {
				$sqlCondition .= " AND (". implode(' OR ', $nameConditions) .") ";
			}
		} else {
			$extraCondition['name'] = array_keys($allowNames);
		}

		if (!empty($extraCondition['advance']['name'])) {
			$nameConditions = [];

			if (isset($extraCondition['advance']['platform'])) {
				foreach ($extraCondition['advance']['platform'] as $name => $enabled) {
					if (in_array("{$name}_name", $extraCondition['name'])) {
						$nameConditions[] = "`{$name}_name` LIKE ". SqlQuote($extraCondition['advance']['name'], true);
					}
				}

				$sqlCondition .= " AND (". implode(' OR ', $nameConditions) .") ";
			}
		}

		if (!empty($extraCondition['advance']['description'])) {
			$sqlCondition .= " AND `description` LIKE ". SqlQuote($extraCondition['advance']['description'], true);
		}

		if (!empty($extraCondition['advance']['region'])) {
			$sqlCondition .= " AND (
				`address1` LIKE ". SqlQuote($extraCondition['advance']['region'], true) ."
				OR `address2` LIKE ". SqlQuote($extraCondition['advance']['region'], true) ." 
				OR `address3` LIKE ". SqlQuote($extraCondition['advance']['region'], true) ."
			)";
		}

		if (!empty($extraCondition['advance']['sex'])) {
			$sexCondition = [];

			if ($extraCondition['advance']['sex']['男']) {
				$sexCondition[] = "`sex` = '男'";
			}

			if ($extraCondition['advance']['sex']['女']) {
				$sexCondition[] = "`sex` = '女'";
			}

			if ($sexCondition) {
				$sqlCondition .= " AND (". implode(' OR ', $sexCondition) .") ";
			}
		}

		if (!empty($extraCondition['advance']['benchmark'])) {
			$benchCondition = [];

			foreach ($extraCondition['advance']['benchmark'] as $name => $data) {
				if (IsId($extraCondition['advance']['benchmark'][$name]['minimun']) || IsId($extraCondition['advance']['benchmark'][$name]['maximun'])) {
					$benchCondition[$name] = " CAST(`". $allowNames[$name .'_name'] ."` AS INTEGER) > 0 ";;
				}

				if (IsId($extraCondition['advance']['benchmark'][$name]['minimun'])) {
					$benchCondition[$name] .= " AND CAST(`". $allowNames[$name .'_name'] ."` AS INTEGER) >= {$extraCondition['advance']['benchmark']['blog']['minimun']} ";
				}
	
				if (IsId($extraCondition['advance']['benchmark'][$name]['maximun'])) {
					$benchCondition[$name] .= " AND CAST(`". $allowNames[$name .'_name'] ."` AS INTEGER) <= {$extraCondition['advance']['benchmark']['blog']['maximun']} ";
				}
			}

			if ($benchCondition) {
				foreach ($benchCondition as $name => $data) {
					$benchCondition[$name] = "( $data )";
				}

				$sqlCondition .= " AND (". implode(' OR ', $benchCondition) .") ";
			}
		}

		if (!empty($extraCondition['advance']['price'])) {
			$priceFieldMaps = ['blog' => 'blog_article_price', 'fb' => 'fb_post_price', 'ig' => 'ig_image_price', 'youtube' => 'youtube_video_price'];

			$priceCondition = [];
			foreach ($extraCondition['advance']['price'] as $name => $data) {
				if (IsId($extraCondition['advance']['price'][$name]['minimun']) || IsId($extraCondition['advance']['price'][$name]['maximun'])) {
					$priceCondition[$name] = " `{$priceFieldMaps[$name]}` > 0 ";
				}

				if (IsId($extraCondition['advance']['price'][$name]['minimun'])) {
					$priceCondition[$name] .= " AND `{$priceFieldMaps[$name]}` >= ". $extraCondition['advance']['price'][$name]['minimun'];
				}
	
				if (IsId($extraCondition['advance']['price'][$name]['maximun'])) {
					$priceCondition[$name] .= " AND `{$priceFieldMaps[$name]}` <= ". $extraCondition['advance']['price'][$name]['maximun'];
				}
			}

			if ($priceCondition) {
				foreach ($priceCondition as $name => $data) {
					$priceCondition[$name] = "( $data )";
				}

				$sqlCondition .= " AND (". implode(' OR ', $priceCondition) .") ";
			}
		}

        if (!empty($keyword)) {
            $columnSearch = [
				'`blogger`.`ac_id`',
				'`blogger`.`display_name`',
                '`blogger`.`blog_name`',
                '`blogger`.`fb_name`',
				'`blogger`.`ig_name`',
				'`blogger`.`youtube_name`'
			];

            $sqlFilter = [];
            foreach ($columnSearch as $name) {
                $sqlFilter[] = $name ." LIKE ". SqlQuote($keyword, true);
			}
			
			if (is_numeric($keyword)) {
				$sqlFilter[] = "`blog_article_price` = $keyword";
				$sqlFilter[] = "`fb_post_price` = $keyword";
				$sqlFilter[] = "`ig_image_price` = $keyword";
				$sqlFilter[] = "`youtube_video_price` = $keyword";
			}

            $sqlCondition .= ' AND ('. implode(' OR ', $sqlFilter) .') ';
        }

        $sqlTotalBlogger = 'SELECT COUNT(`id`) as `total` FROM `blogger`  WHERE '. $sqlCondition;
		$sqlRowsBlogger = 'SELECT `id`, `ac_id`, `blog_name`, `fb_name`, `ig_name`, `youtube_name`, `blog_article_price`, `fb_post_price`, `ig_sidecar_price`, `youtube_video_price`, `photo` FROM `blogger` WHERE '. $sqlCondition . sprintf(' ORDER BY %s %s ', $orderby, $orderdir) . sprintf(' LIMIT %d, %d', $rowsStart, $rowsMaxNum);

		$db->query($sqlTotalBlogger);
        $itemTotalBlogger = $db->next_record();
        $totalBloggerNum = $itemTotalBlogger['total'];

		$rowsBlogger = [];
		$db->query($sqlRowsBlogger);
		while ($itemBlogger = $db->next_record()) {
			$itemBlogger['blog_cost'] = $itemBlogger['blog_article_price'] ? number_format(calcInfluenceCostPrice($itemBlogger['blog_article_price'], $itemBlogger['payment_method'])) : 0;
			$itemBlogger['fb_cost'] = $itemBlogger['fb_post_price'] ? number_format(calcInfluenceCostPrice($itemBlogger['fb_post_price'], $itemBlogger['payment_method'])) : 0;
			$itemBlogger['ig_cost'] = $itemBlogger['ig_sidecar_price'] ? number_format(calcInfluenceCostPrice($itemBlogger['ig_sidecar_price'], $itemBlogger['payment_method'])) : 0;
			$itemBlogger['youtube_cost'] = $itemBlogger['youtube_video_price'] ? number_format(calcInfluenceCostPrice($itemBlogger['youtube_video_price'], $itemBlogger['payment_method'])) : 0;
			$itemBlogger['photo'] = $itemBlogger['photo'] && file_exists(__DIR__ ."/{$itemBlogger['photo']}") ? $itemBlogger['photo'] : '';
			$rowsBlogger[$itemBlogger['id']] = $itemBlogger;
		}

		$rowsBloggerTag = [];
		if (count($rowsBlogger)) {
			$objTagMap = CreateObject('TagMap');
			foreach ($objTagMap->searchAll(" `map_relation` = 'blogger' AND `map_item_id` IN (". implode(', ', array_keys($rowsBlogger)) .") ", '', '', '', "LEFT JOIN `tag` ON `tag_id` = `map_tag`") as $itemTagMap) {
				if (!isset($rowsBloggerTag[$itemTagMap['map_item_id']])) {
					$rowsBloggerTag[$itemTagMap['map_item_id']] = [];
				}

				$rowsBloggerTag[$itemTagMap['map_item_id']][] = [
					'tag_name' => $itemTagMap['tag_name'],
					'tag_color' => $itemTagMap['tag_color'],
				];
			}
		}
        
        $pagination->setConfig([
            'start' => ($page - 1) * $rowsMaxNum,
            'total' => $totalBloggerNum,
            'limit' => $rowsMaxNum
		]);
        $sectionBottomPagination = $pagination->getBottomContent();

		$twig = new twig('blogger_list.html', [
			'session' => $_SESSION,
			'listMode' => $listMode,
			'mediaId' => GetVar('media_id'),
			'campaignId' => GetVar('campaign_id'),
			'rowsBlogger' => $rowsBlogger,
			'rowsBloggerTag' => $rowsBloggerTag,
            'sectionBottomPagination' => $sectionBottomPagination,
			'isGrantEdit' => $isGrantEdit,
			'isGrantDelete' => $isGrantDelete,
			'displayName' => isset($extraCondition['name']) ? $extraCondition['name'] : [],
		]);

        $output = [
            'top_pagination' => '',
            'content_list' => $twig->getContent(),
            'bottom_pagination' => $sectionBottomPagination
		];

        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($output));
	}
	
	$extraCondition = ['advance' => [], 'class' => '', 'tag_id' => [], 'name' => ['blog_name', 'fb_name', 'ig_name', 'youtube_name']];
	$objCategory = CreateObject('Category');

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>【<?= $GLOBALS['env']['flag']['name']; ?>】寫手列表</title>
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
							<div class="box-header well " data-original-title>
								<h2><i class="fa fa-list"></i> 寫手列表</h2>
							</div>
							<div class="box-content">
								<link rel="stylesheet" type="text/css" href="js/jquery-ui.css">
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
								<div style="display: block; width: 100%; margin: -10px 0 12px -10px; padding-right: 20px; background-color: #f6f6f6; height: 40px; box-shadow: 1px 1px #eee;">
									<? if ($isGrantEdit) : ?>
										<a href="blogger_edit.php" id="btn_add_blogger" style="float: left; display: block; height: 20px; margin-left: 16px; margin-top: 10px; font-size: 1.2em; text-decoration: none; color: #008900; font-weight: bold; text-shadow: none;" onmouseover="$('#export_detail').hide();">
											<i class="fa fa-user-plus"></i> 新增寫手
										</a>
									<? endif; ?>

									<a style="float: left; display: block; height: 20px; margin-left: 16px; margin-top: 10px; font-size: 1.2em; text-decoration: none; color: #885ead; font-weight: bold; text-shadow: none;" onmouseover="$('#export_detail').show();">
										<i class="fa fa-cloud-download"></i> 匯出
										<div class="row-fluid" style="position: absolute; width: auto; height: auto; display: none; border: 1px solid #ddd; background-color: white; padding: 24px; color: #666; box-shadow: 2px 2px 30px #ddd; z-index: 30;" id="export_detail" onmouseout="$('#export_detail').hide();">
											<style>
												.row-check {
													margin-bottom: 16px;
													max-width: 460px;
													font-size: 1em;
												}
											</style>
											
											<form id="export_form" style="margin: 0px;">
												<label class="row-check"><input type="checkbox" name="blog" value="1">&nbsp;&nbsp;BLOG報價(含轉發、引用授權)</label>
												<label class="row-check"><input type="checkbox" name="fb" value="1">&nbsp;&nbsp;FB報價(含轉發、引用授權)</label>
												<label class="row-check"><input type="checkbox" name="ig" value="1">&nbsp;&nbsp;Instagram報價(含轉發、引用授權)</label>
												<label class="row-check"><input type="checkbox" name="youtube" value="1">&nbsp;&nbsp;YouYube報價(含轉發、引用授權)</label>
												<label class="row-check"><input type="checkbox" name="all" value="1">&nbsp;&nbsp;全選</label>
											</form>
											
											<div class="btn btn-success pull-right" id="clickAll">
												<i class="fa fa-cloud-download"></i> 匯出
											</div>
										</div>
									</a>

									<? if ($isModuleManager) : ?>
										<a href="javascript:OpenBlock('category');" style="float: left; display: block; height: 20px; margin-left: 16px; margin-top: 10px; font-size: 1.2em; text-decoration: none; color: #e59400; font-weight: bold; text-shadow: none;" onmouseover="$('#export_detail').hide();">
											<i class="fa fa-th-list"></i> 管理分類
										</a>

										<a href="javascript:OpenBlock('tag');" style="float: left; display: block; height: 20px; margin-left: 16px; margin-top: 10px; font-size: 1.2em; text-decoration: none; color: #7a5230; font-weight: bold; text-shadow: none;" onmouseover="$('#export_detail').hide();">
											<i class="fa fa-tag"></i> 管理標籤
										</a>

										<a href="blogger_shared_account.php" style="float: left; display: block; height: 20px; margin-left: 16px; margin-top: 10px; font-size: 1.2em; text-decoration: none; color: #551a8b; font-weight: bold; text-shadow: none;" onmouseover="$('#export_detail').hide();">
											<i class="fa fa-usd"></i> 管理共用帳戶
										</a>
									<? endif; ?>
									
									<a href="javascript:ToggleAdvanceSearch();" style="float: right; display: block; height: 20px; margin-top: 10px; font-size: 1.2em; text-decoration: none; color: #1919ff; font-weight: bold; text-shadow: none;">
										<i class="fa fa-search"></i> 進階搜尋
									</a>
								</div>
								<div style="display: inline-block; width: 100%;">
									<div class="span7" style="margin-left: 0px;">
										<div>已勾選總數：<span id="select_total" style="color: red; margin-bottom: 3px;"></span></div>	
										<div>已勾選名單：<span id="select_name" style="color: red;"></span></div>	
										<span id="checkclean" style="display: none;">
											<a class="btn btn-danger" href="#" id='cleanAll'>
												<i class="icon-trash icon-white"></i>  
												清除勾選
											</a>
										</span>
									</div>
									
									<div class="span5" style="">
										<div style="display: inline-block; float: right;">
											名稱: <select data-rel="chosen" multiple style="width: 330px;" onchange="ChangeName(this);">
												<option value="blog_name" <?= (in_array('blog_name', $extraCondition['name']) ? 'selected' : ''); ?>>Blog</option>
												<option value="fb_name" <?= (in_array('fb_name', $extraCondition['name']) ? 'selected' : ''); ?>>FB</option>
												<option value="ig_name" <?= (in_array('ig_name', $extraCondition['name']) ? 'selected' : ''); ?>>Instagram</option>
												<option value="youtube_name" <?= (in_array('youtube_name', $extraCondition['name']) ? 'selected' : ''); ?>>YouTube</option>
											</select>
										</div>
										<br/>
										<br/>
										<div style="display: inline-block; float: right;">
											標籤: <select data-rel="chosen" multiple style="width: 330px;" onchange="ChangeTag(this);">
												<? foreach ($objTag->getRelationData('blogger') as $itemTag) : ?>
													<option value="<?= $itemTag['tag_id']; ?>"  <?= isset($extraCondition['tag_id']) && is_array($extraCondition['tag_id']) && in_array($itemTag['tag_id'], $extraCondition['tag_id']) ? 'selected' : ''; ?>><?= $itemTag['tag_name']; ?></option>
												<? endforeach; ?>
											</select>
										</div>
										<br/>
										<br/>
										<div style="display: inline-block; float: right;">
											分類: <select data-rel="chosen" style="width: 330px;" onchange="ChangeCategory(this.value);">
												<option value="">全部</option>
												<? foreach ($objCategory->searchAll("`category_relation` = 'blogger'", 'category_sort', 'ASC') as $itemCategory) : ?>
													<option value="<?= $itemCategory['category_name']; ?>" <?= isset($extraCondition['class']) && $extraCondition['class'] == $itemCategory['category_name'] ? 'selected' : ''; ?>><?= $itemCategory['category_name']; ?></option>
												<? endforeach; ?>
											</select>
										</div>
									</div>
								</div>
								<div style="display: block; width: 100%; height: 280px; margin-bottom: 5px; background-color: #f6f6f6; box-shadow: 1px 1px #eee; display: none;" id="advance_panel">
									<div style="display: block; padding-left: 16px;">
										<form id="advance_form" style="margin: 0;">
											<div style="display: block; width: 100%; height: 40px;">
												名稱:&nbsp;&nbsp;
												<input type="text" name="advance[name]" style="width: 200px; margin-top: 5px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												<input type="checkbox" name="advance[platform][blog]" value="1" checked>部落格&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												<input type="checkbox" name="advance[platform][fb]" value="1" checked>Facebook&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												<input type="checkbox" name="advance[platform][youtube]" value="1" checked>YouTube&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												<input type="checkbox" name="advance[platform][ig]" value="1" checked>Instagram
											</div>
											<div style="display: block; width: 100%; height: 40px;">
												<table style="width: 100%;">
													<tr>
														<td style="width: 33%;">
															簡介:&nbsp;&nbsp;
															<input type="text" name="advance[description]" style="width: 200px; margin-top: 5px;">
														</td>
														<td style="width: 34%;">
															地區:&nbsp;&nbsp;
															<select data-rel="chosen" name="advance[region]">
																<option value=""> -- </option>
																<option value="台北市">台北市</option>
																<option value="基隆市">基隆市</option>
																<option value="新北市">新北市</option>
																<option value="連江縣">連江縣</option>
																<option value="宜蘭縣">宜蘭縣</option>
																<option value="新竹市">新竹市</option>
																<option value="新竹縣">新竹縣</option>
																<option value="桃園市">桃園市</option>
																<option value="苗栗縣">苗栗縣</option>
																<option value="台中市">台中市</option>
																<option value="彰化縣">彰化縣</option>
																<option value="南投縣">南投縣</option>
																<option value="嘉義市">嘉義市</option>
																<option value="嘉義縣">嘉義縣</option>
																<option value="雲林縣">雲林縣</option>
																<option value="台南市">台南市</option>
																<option value="高雄市">高雄市</option>
																<option value="澎湖縣">澎湖縣</option>
																<option value="金門縣">金門縣</option>
																<option value="屏東縣">屏東縣</option>
																<option value="台東縣">台東縣</option>
																<option value="花蓮縣">花蓮縣</option>
															<select>
														</td>
														<td style="width: 33%;">
															性別:&nbsp;&nbsp;
															<input type="checkbox" name="advance[sex][女]" value="1">女&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
															<input type="checkbox" name="advance[sex][男]" value="1">男
														</td>
													</tr>
												</table>
											</div>
											<div style="display: block; width: 100%; height: 40px;">
												<table style="width: 100%;">
													<tr>
														<td style="width: 50%;">
															部落格流量:&nbsp;&nbsp;
															<input type="text" name="advance[benchmark][blog][minimun]" style="width: 50px; margin-top: 5px;">
															&nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;
															<input type="text" name="advance[benchmark][blog][maximun]" style="width: 50px; margin-top: 5px;">
														</td>
														<td style="width: 50%;">
															部落格文章價格:&nbsp;&nbsp;
															<input type="text" name="advance[price][blog][minimun]" style="width: 50px; margin-top: 5px;">
															&nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;
															<input type="text" name="advance[price][blog][maximun]" style="width: 50px; margin-top: 5px;">
														</td>
													</tr>
												</table>
											</div>
											<div style="display: block; width: 100%; height: 40px;">
												<table style="width: 100%;">
													<tr>
														<td style="width: 50%;">
															粉絲團人數:&nbsp;&nbsp;
															<input type="text" name="advance[benchmark][fb][minimun]" style="width: 50px; margin-top: 5px;">
															&nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;
															<input type="text" name="advance[benchmark][fb][maximun]" style="width: 50px; margin-top: 5px;">
														</td>
														<td style="width: 50%;">
															FB圖文價格:&nbsp;&nbsp;
															<input type="text" name="advance[price][fb][minimun]" style="width: 50px; margin-top: 5px;">
															&nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;
															<input type="text" name="advance[price][fb][maximun]" style="width: 50px; margin-top: 5px;">
														</td>
													</tr>
												</table>
											</div>
											<div style="display: block; width: 100%; height: 40px;">
												<table style="width: 100%;">
													<tr>
														<td style="width: 50%;">
															YouTube訂閱人數:&nbsp;&nbsp;
															<input type="text" name="advance[benchmark][ig][minimun]" style="width: 50px; margin-top: 5px;">
															&nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;
															<input type="text" name="advance[benchmark][ig][maximun]" style="width: 50px; margin-top: 5px;">
														</td>
														<td style="width: 50%;">
															YouTube影片價格:&nbsp;&nbsp;
															<input type="text" name="advance[price][youtube][minimun]" style="width: 50px; margin-top: 5px;">
															&nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;
															<input type="text" name="advance[price][youtube][maximun]" style="width: 50px; margin-top: 5px;">
														</td>
													</tr>
												</table>
											</div>
											<div style="display: block; width: 100%; height: 40px;">
												<table style="width: 100%;">
													<tr>
														<td style="width: 50%;">
															Instagram追蹤人數:&nbsp;&nbsp;
															<input type="text" name="advance[benchmark][youtube][minimun]" style="width: 50px; margin-top: 5px;">
															&nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;
															<input type="text" name="advance[benchmark][youtube][maximun]" style="width: 50px; margin-top: 5px;">
														</td>
														<td style="width: 50%;">
															Instagram影片價格:&nbsp;&nbsp;
															<input type="text" name="advance[price][ig][minimun]" style="width: 50px; margin-top: 5px;">
															&nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;
															<input type="text" name="advance[price][ig][maximun]" style="width: 50px; margin-top: 5px;">
														</td>
													</tr>
												</table>
											</div>
											<div style="display: block; width: 100%; height: 40px; text-align: right;">
												<a class="btn btn-info" style="font-size: 1.5em; margin-top: 3px; margin-right: 8px;" onclick="searchForAdance();" target="_blank">
													<i class="fa fa-search"></i> <span style="font-size: .7em;">搜尋</span>
												</a>
											</div>
										</form>
									</div>
								</div>

								<style>
									p i {
										font-size: 1.5em !important;
									}

									table#example tbody tr td p:last-child {
										margin-bottom: 0px;
									}
								</style>
								<div id="top_pagination">
                                    <?= $pagination->setConfig(['start' => 0, 'total' => 0, 'limit' => 50])->getTopContent(); ?>
                                </div>
								<table class="table table-striped table-bordered" id="example">
									<thead>
										<tr>
											<th class="ui-state-default" style="width: 30px;">勾選</th>
											<th class="ui-state-default" style="width: 50px;" onclick="checkOrder('`blogger`.`ac_id`');" nowrap>帳戶ID</th>
											<th class="ui-state-default" style="text-align: left;">名稱</th>
											<th class="ui-state-default" style="width: 140px;">照片</th>
											<th class="ui-state-default" nowrap>成本</th>
											<th class="ui-state-default" style="width: 90px;">Actions</th>
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

		<? if ($isModuleManager) : ?>
			<?php require_once __DIR__ .'/blogger_popup.php'; ?>
		<? endif; ?>
		<script>
			var total = 0;
			$(document).ready(function() {
				addID(null, null);

				$('#cleanAll').click(function(event) {
					$.ajax({
						url: 'blogger_action.php?method=clean_cache',
						type: 'POST',
						beforeSend: function() {
							$('span#select_total').html('0');
							$('span#select_name').html('');
							$('span#checkclean').hide();
							$('input:checkbox[name*="select_id"]:checked').removeAttr('checked', 'checked');
						},
						success: function() {
							total = 0;
						}
					});
				});

				$('#clickAll').click(function(event) {
					if ($('form#export_form').find(':checkbox:checked').length) {
						if (total > 30) {
							alert('最多一次匯出30人喔！');
						} else if (total) {
							window.open('excel/print_blogger.php?'+ $('form#export_form').serialize());
						} else {
							alert('尚未勾選要匯出的寫手');
						}
					} else {
						alert('請選擇要匯出的項目');
					}
				});
			});

			function searchForAdance()
			{
				var advance = {
					sex: {
						'女': 0,
						'男': 0
					},
					platform: {
						blog: 0,
						fb: 0,
						youtube: 0,
						ig: 0
					},
					benchmark: {
						blog: {
							minimun: '',
							maximun: ''
						},
						fb: {
							minimun: '',
							maximun: ''
						},
						ig: {
							minimun: '',
							maximun: ''
						},
						youtube: {
							minimun: '',
							maximun: ''
						}
					},
					price: {
						blog: {
							minimun: '',
							maximun: ''
						},
						fb: {
							minimun: '',
							maximun: ''
						},
						ig: {
							minimun: '',
							maximun: ''
						},
						youtube: {
							minimun: '',
							maximun: ''
						}
					}
				};
				
				var formContent = $('#advance_form').serializeArray();
				for (var idx in formContent) {
					eval(formContent[idx]['name'].toString().replace(/\[/g, "['").replace(/\]/g, "']") +" = '"+ formContent[idx]['value'].toString().replace(/\'/g, "\\'") +"';");
				}
				
				extraCondition['advance'] = advance;
				
				goToPage(1);
			}

			function ToggleAdvanceSearch()
			{
				if ($('div#advance_panel').hasClass('panel-visibility')) {
					$('div#advance_panel').removeClass('panel-visibility').hide();
				} else {
					$('div#advance_panel').addClass('panel-visibility').show();
				}
			}
			
			function addID (id)
			{	
				$.ajax({
					url: 'blogger_action.php?method=toggle_blogger_selected&blogger_id='+ id,
					type: 'POST',
				}).done(function (obj) {
					console.log("success");
					
					$('#select_total').html(obj.total);
					$('#select_name').html(obj.blogger);
					total = obj.total;
					if (total > 0) {
						$('#checkclean').fadeIn();
					} else if (total == 0) {
						$('#checkclean').fadeOut();
					}

					if (total > 50 ) {
						alert('超過50筆無法匯出喔！');
					}
				}).fail(function() {
					console.log("error");
				}).always(function() {
					console.log("complete");
					console.log("total="+total);
				});
			}
			
            orderby = '<?= $orderby; ?>';
            orderdir = '<?= $orderdir; ?>';
			extraCondition = <?= json_encode($extraCondition); ?>;
            keyword = '';
            $('#content_loader').hide();
            $('#content_list').hide();
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

			function ChangeCategory(cateoryName)
			{
				extraCondition['class'] = cateoryName;
				goToPage(1);
			}

			function ChangeTag(obj)
			{
				extraCondition['tag_id'] = $(obj).val();
				goToPage(1);
			}

			function ChangeName(obj)
			{
				extraCondition['name'] = $(obj).val() ? $(obj).val() : [];
				goToPage(1);
			}

			function DelBlogger(bloggerId, btn)
			{
				if (bloggerId) {
					$.ajax({
						url: 'blogger_action.php?method=delete&blogger_id='+ bloggerId,
						type: 'POST',
						beforeSend: function() {
							$(btn).hide().next().show();
						},
						success: function(feedback) {
							if ('success' in feedback && feedback.success) {
								setTimeout(function() {
									$(btn).next().removeClass('fa-refresh fa-spin').addClass('fa-check-circle-o').css({color: 'green'});

									setTimeout(function() {
										goToPage(page);
									}, 500);
								}, 500);
								return;
							} else if ('message' in feedback) {
								alert(feedback.message);
							}

							$(btn).show().next().hide();
						},
						error: function() {
							$(btn).show().next().hide();
						}
					});
				}
			}
        </script>
	</body>
</html>