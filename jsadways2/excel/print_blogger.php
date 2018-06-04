<?php

	ini_set('memory_limit', '384M');
	require_once dirname(dirname(__DIR__)) .'/autoload.php';

	IsPermitted();

	function genHeaderColumn(&$objPHPExcel, $increase, &$columnPrefix, &$columnSuffix, $color='000000', $text='')
	{
		$excelActiveSheet = &$objPHPExcel->getActiveSheet();
		
		$startColumn = AsciiCharToColumnName($columnPrefix, $columnSuffix);
		
		SetExcelCellMiddle($excelActiveSheet, ["{$startColumn}1"]);
		SetExcelCellValue($objPHPExcel, ["{$startColumn}1" => $text]);
		SetExcelCellCenter($excelActiveSheet, [AsciiCharToColumnName($columnPrefix, $columnSuffix) .'1']);
		$excelActiveSheet->mergeCells(GetExtendCellPosition($columnPrefix, $columnSuffix, count($increase)));
		$endColumn = AsciiCharToColumnName($columnPrefix, $columnSuffix);
		SetExcellCellBorder($excelActiveSheet, ["{$startColumn}1" => 'all']);
		SetCellBackgroundColor($excelActiveSheet, ["{$startColumn}2:{$endColumn}2"], $color);
	}

	$db = clone($GLOBALS['app']->db);

	$data = [];

	$exportBlog = GetVar('blog') || GetVar('all');
	$exportFB = GetVar('blog') || GetVar('fb') || GetVar('all');
	$exportInstagram = GetVar('fb') || GetVar('ig') || GetVar('all');
	$exportYouTube = GetVar('youtube') || GetVar('all');
	$exportFBAds = GetVar('blog') || GetVar('fb') || GetVar('ig') || GetVar('youtube') || GetVar('all');
	$exportAuth =  true;
	$exportOther = true;

	$bloggerList = [];
	if (isset($_SESSION['blogger']) && is_array($_SESSION['blogger']) && count($_SESSION['blogger'])) {
		foreach ($_SESSION['blogger'] as $idxBlogger => $bloggerId) {
			if (!IsId($idxBlogger)) {
				unset($_SESSION['blogger'][$idxBlogger]);
			}
		}
	}

	if (count($_SESSION['blogger'])) {
		IncludeFunctions('jsadways');
		IncludeFunctions('excel');
		$objPHPExcel = CreateExcelFile();
		$objPHPExcel->getDefaultStyle()->getFont()->setName('微軟正黑體');
		$objPHPExcel->getDefaultStyle()->getFont()->setSize(16);

		$excelActiveSheet = &$objPHPExcel->getActiveSheet();
		$excelActiveSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
		$excelActiveSheet->getDefaultColumnDimension()->setWidth(18);

		$data[0] = [];
		$objTagMap = CreateObject('TagMap');
		$objBlogger = CreateObject('Blogger');
		$rowsBlogger = $objBlogger->searchAll(sprintf("`id` IN (%s)", implode(', ', array_keys($_SESSION['blogger']))));
		
		$excelActiveSheet->getColumnDimension(NumberToColumnName(5))->setWidth(28);
		$excelActiveSheet->getColumnDimension(NumberToColumnName(1))->setWidth(14);
		$excelActiveSheet->getColumnDimension(NumberToColumnName(3))->setWidth(5);
		$data[0] += $increase = [
			'photo' => '照片',
			'display_name' => '名稱',
			'sex' => '性別',
			'class' => '類別',
			'description' => '簡介',
			'tag' => '標籤',
		];
		$startColumn = 'A';
		SetExcelCellMiddle($excelActiveSheet, ["{$startColumn}1"]);
		SetExcelCellValue($objPHPExcel, ["{$startColumn}1" => 'About']);
		SetExcelCellCenter($excelActiveSheet, ["{$startColumn}1"]);
		$excelActiveSheet->mergeCells(GetExtendCellPosition($columnPrefix, $columnSuffix, count($increase)));
		$endColumn = AsciiCharToColumnName($columnPrefix, $columnSuffix);
		SetExcellCellBorder($excelActiveSheet, ["{$startColumn}1" => 'all']);
		SetCellBackgroundColor($excelActiveSheet, ["{$startColumn}2:{$endColumn}2"], 'cbe5cb');
		

		if ($exportBlog) {
			$excelActiveSheet->getColumnDimension(NumberToColumnName(count($data[0]) + 1))->setWidth(28);
			$excelActiveSheet->getColumnDimension(NumberToColumnName(count($data[0]) + 2))->setWidth(40);
			$excelActiveSheet->getColumnDimension(NumberToColumnName(count($data[0]) + 6))->setWidth(30);
			$excelActiveSheet->getColumnDimension(NumberToColumnName(count($data[0]) + 7))->setWidth(30);
			$data[0] += $increase = [
				'blog_name' => 'Blog名稱',
				'blog_link' => 'Blog連結',
				'blog_flow' => 'Blog流量',
				'blog_article_price' => 'Blog文章',
				'blog_article_attend_price' => 'Blog文章(出席體驗)',
				'blog_other_price' => 'Blog其它費用',
				'blog_definition' => 'Blog製作規範',
			];
			genHeaderColumn($objPHPExcel, $increase, $columnPrefix, $columnSuffix, 'ffedcc', 'Blog');
		}

		if ($exportFB) {
			$excelActiveSheet->getColumnDimension(NumberToColumnName(count($data[0]) + 1))->setWidth(28);
			$excelActiveSheet->getColumnDimension(NumberToColumnName(count($data[0]) + 2))->setWidth(40);
			$excelActiveSheet->getColumnDimension(NumberToColumnName(count($data[0]) + 12))->setWidth(30);
			$excelActiveSheet->getColumnDimension(NumberToColumnName(count($data[0]) + 13))->setWidth(30);
			$data[0] += $increase = [
				'fb_name' => 'FB名稱',
				'fb_link' => 'FB連結',
				'fb_fans' => 'FB流量',
				'fb_post_price' => 'FB靜態圖文',
				'fb_video_price' => 'FB影片',
				'fb_live_price' => 'FB直播',
				'fb_share_price' => 'FB轉分享客戶素材(僅撰文)',
				'fb_checkin_attend_price' => 'FB打卡(出席體驗)',
				'fb_post_attend_price' => 'FB靜態圖文(出席體驗)',
				'fb_video_attend_price' => 'FB影片(出席體驗)',
				'fb_live_attend_price' => 'FB直播(出席體驗)',
				'fb_other_price' => 'FB其它費用',
				'fb_definition' => 'FB製作規範',
			];
			genHeaderColumn($objPHPExcel, $increase, $columnPrefix, $columnSuffix, 'ccbadc', 'FB');
		}

		if ($exportInstagram) {
			$excelActiveSheet->getColumnDimension(NumberToColumnName(count($data[0]) + 1))->setWidth(28);
			$excelActiveSheet->getColumnDimension(NumberToColumnName(count($data[0]) + 2))->setWidth(40);
			$excelActiveSheet->getColumnDimension(NumberToColumnName(count($data[0]) + 14))->setWidth(30);
			$excelActiveSheet->getColumnDimension(NumberToColumnName(count($data[0]) + 15))->setWidth(30);
			$data[0] += $increase = [
				'ig_name' => 'Instagram名稱',
				'ig_link' => 'Instagram連結',
				'ig_fans' => 'Instagram流量',
				'ig_image_price' => 'Instagram靜態單圖文',
				'ig_sidecar_price' => 'Instagram靜態多圖文',
				'ig_video_price' => 'Instagram影片',
				'ig_live_price' => 'Instagram直播(保存至限時動態)',
				'ig_limited_post_price' => 'Instagram限時動態',
				'ig_image_attend_price' => 'Instagram靜態單圖文(出席體驗)',
				'ig_sidecar_attend_price' => 'Instagram靜態多圖文(出席體驗)',
				'ig_video_attend_price' => 'Instagram影片(出席體驗)',
				'ig_live_attend_price' => 'Instagram直播(保存至限時動態)(出席體驗)',
				'ig_limited_post_attend_price' => 'Instagram限時動態(出席體驗)',
				'ig_other_price' => 'Instagram其它費用',
				'ig_definition' => 'Instagram製作規範',
			];
			genHeaderColumn($objPHPExcel, $increase, $columnPrefix, $columnSuffix, 'e5ffff', 'Instagram');
		}

		if ($exportYouTube) {
			$excelActiveSheet->getColumnDimension(NumberToColumnName(count($data[0]) + 1))->setWidth(28);
			$excelActiveSheet->getColumnDimension(NumberToColumnName(count($data[0]) + 2))->setWidth(40);
			$excelActiveSheet->getColumnDimension(NumberToColumnName(count($data[0]) + 10))->setWidth(30);
			$excelActiveSheet->getColumnDimension(NumberToColumnName(count($data[0]) + 11))->setWidth(30);
			$data[0] += $increase = [
				'youtube_name' => 'YouTube名稱',
				'youtube_link' => 'YouTube連結',
				'youtube_fans' => 'YouTube流量',
				'youtube_video_price' => 'YouTube影片',
				'youtube_live_price' => 'YouTube直播',
				'youtube_post_to_fb_price' => 'YouTube影片上傳FB(非轉發)',
				'youtube_auth_to_net_price' => 'YouTube授權引用網路全平台',
				'youtube_raw_editable_auth_price' => 'YouTube原檔授權(可剪輯)',
				'youtube_raw_readable_auth_price' => 'YouTube原檔授權(不可剪輯)',
				'youtube_other_price' => 'YouTube其它費用',
				'youtube_definition' => 'YouTube製作規範',
			];
			genHeaderColumn($objPHPExcel, $increase, $columnPrefix, $columnSuffix, 'ffcccc', 'YouTube');
		}

		if ($exportFBAds) {
			$excelActiveSheet->getColumnDimension(NumberToColumnName(count($data[0]) + 9))->setWidth(30);
			$data[0] += $increase = [
				'fbads_share_to_self_fb_price' => '轉分享至部落客FB',
				'fbads_share_to_self_ig_price' => '轉分享至部落客IG',
				'fbads_share_to_customer_fb_price' => '轉分享至客戶FB',
				'fbads_share_to_client_fb_with_ad_price' => '轉分享至客戶FB並下廣告',
				'fbads_client_with_js_price' => '加傑思為廣告主',
				'fbads_client_with_customer_price' => '加客戶為廣告主',
				'fbads_do_it_self_price' => '寫手自行操作廣告',
				'fbads_to_sponsor_price' => 'Add sponsor',
				'fbads_definition' => '廣告規範',
			];
			genHeaderColumn($objPHPExcel, $increase, $columnPrefix, $columnSuffix, 'e5f2fb', 'FB廣告');
		}

		if ($exportAuth) {
			$excelActiveSheet->getColumnDimension(NumberToColumnName(count($data[0]) + 9))->setWidth(30);
			$data[0] += $increase = [
				'auth_quote_to_website_with_feedback_price' => "引用至官網/campaign(連回)\n*一張識別圖及部份文字",
				'auth_quote_to_website_without_feedback_price' => "引用至官網/campaign(不連回)\n*一張識別圖及部份文字",
				'auth_quote_to_ec_with_feedback_price' => "引用至EC(連回)\n*一張識別圖及部份文字",
				'auth_quote_to_ec_without_feedback_price' => "引用至EC(不連回)\n*一張識別圖及部份文字",
				'auth_quote_to_dm_price' => "引用至平面/DM\n*一張識別圖及部份文字",
				'auth_single_photo_price' => '照片授權(單張)',
				'auth_dispaly_network_price' => '聯播網廣告',
				'auth_native_ads_price' => '原生廣告',
				'auth_definition' => '相關規範',
			];
			genHeaderColumn($objPHPExcel, $increase, $columnPrefix, $columnSuffix, 'ffffef', '引用授權');
		}

		if ($exportOther) {
			$excelActiveSheet->getColumnDimension(NumberToColumnName(count($data[0]) + 4))->setWidth(30);
			$data[0] += $increase = [
				'other_attend_without_interview_price' => '靠櫃/活動出席 *不含媒體曝光、受訪等',
				'other_shoot_price' => '拍攝',
				'other_annual_endorse' => '年度代言',
				'other_more_cooperation' => '更多合作項目',
			];
			genHeaderColumn($objPHPExcel, $increase, $columnPrefix, $columnSuffix, 'fff3e4', '其他');
		}

		$excelActiveSheet->getColumnDimension(NumberToColumnName(count($data[0]) + 1))->setWidth(30);
		$excelActiveSheet->getColumnDimension(NumberToColumnName(count($data[0]) + 2))->setWidth(30);
		$data[0] += $increase = [
			'personnel_info' => "",
			'comment' => ''
		];

		$endColumn = AsciiCharToColumnName($columnPrefix, $columnSuffix);
		SetCellBackgroundColor($excelActiveSheet, ["{$endColumn}2"], 'ffffff');

		$excelActiveSheet->mergeCells(NumberToColumnName(count($data[0]) - 1) .'1:'. NumberToColumnName(count($data[0]) - 1) .'2');
		$excelActiveSheet->mergeCells(NumberToColumnName(count($data[0])) .'1:'. NumberToColumnName(count($data[0])) .'2');
		
		SetExcelCellCenter($excelActiveSheet, [NumberToColumnName(count($data[0]) - 1) .'1', NumberToColumnName(count($data[0])) .'1']);
		SetExcelCellMiddle($excelActiveSheet, [NumberToColumnName(count($data[0]) - 1) .'1', NumberToColumnName(count($data[0])) .'1']);
		SetExcelCellTextWrap($excelActiveSheet, [NumberToColumnName(count($data[0]) - 1) .'1']);
		SetExcellCellBorder($excelActiveSheet, [
			NumberToColumnName(count($data[0]) - 1) .'1' => 'all', 
			NumberToColumnName(count($data[0])) .'1' => 'all'
		]);
		SetExcelCellValue($objPHPExcel, [
			NumberToColumnName(count($data[0]) - 1) .'1' => "個人資訊\n*尺寸、膚況、耳洞、小孩、感情婚姻等",
			NumberToColumnName(count($data[0])) .'1' => '備註'
		]);
		

		foreach ($rowsBlogger as $itemBlogger) {
			$row = [];

			$influencerName = $itemBlogger['fb_name'];

            if (empty($influencerName)) {
                $influencerName = $itemBlogger['blog_name'];
            }

            if (empty($influencerName)) {
                $influencerName = $itemBlogger['ig_name'];
            }

            if (empty($influencerName)) {
                $influencerName = $itemBlogger['youtube_name'];
            }

            if (empty($influencerName)) {
                $influencerName = $itemBlogger['display_name'];
			}
			
			$row += [
				'photo' => $itemBlogger['photo'] && file_exists(dirname(__DIR__) .'/'. $itemBlogger['photo']) ? ("image://". dirname(__DIR__) ."/{$itemBlogger['photo']}") : '',
				'display_name' => $influencerName,
				'sex' => $itemBlogger['sex'],
				'class' => $itemBlogger['class'],
				'description' => RemoveEmoji($itemBlogger['description']),
				'tag' => []
			];

			foreach ($objTagMap->searchAll(sprintf("`map_relation` = 'blogger' AND `map_item_id` = %d", $itemBlogger['id']), '', '', '', 'LEFT JOIN `tag` ON `tag_id` = `map_tag`', 'tag_name') as $itemTag) {
				$row['tag'][] = $itemTag['tag_name'];
			}

			$row['tag'] = implode(', ', $row['tag']);

			if ($exportBlog) {
				$row += [
					'blog_name' => $itemBlogger['blog_name'],
					'blog_link' => $itemBlogger['blog_link'],
					'blog_flow' => $itemBlogger['blog_flow'],
					'blog_article_price' => getInfluencerCostPriceText($itemBlogger['blog_article_price'], $itemBlogger['payment_method']),
					'blog_article_attend_price' => getInfluencerCostPriceText($itemBlogger['blog_article_attend_price'], $itemBlogger['payment_method']),
					'blog_other_price' => $itemBlogger['blog_other_price'],
					'blog_definition' => RemoveEmoji($itemBlogger['blog_definition']),
				];
			}

			if ($exportFB) {
				$row += [
					'fb_name' => $itemBlogger['fb_name'],
					'fb_link' => $itemBlogger['fb_link'],
					'fb_fans' => $itemBlogger['fb_fans'],
					'fb_post_price' => getInfluencerCostPriceText($itemBlogger['fb_post_price'], $itemBlogger['payment_method']),
					'fb_video_price' => getInfluencerCostPriceText($itemBlogger['fb_video_price'], $itemBlogger['payment_method']),
					'fb_live_price' => getInfluencerCostPriceText($itemBlogger['fb_live_price'], $itemBlogger['payment_method']),
					'fb_share_price' => getInfluencerCostPriceText($itemBlogger['fb_share_price'], $itemBlogger['payment_method']),
					'fb_checkin_attend_price' => getInfluencerCostPriceText($itemBlogger['fb_checkin_attend_price'], $itemBlogger['payment_method']),
					'fb_post_attend_price' => getInfluencerCostPriceText($itemBlogger['fb_post_attend_price'], $itemBlogger['payment_method']),
					'fb_video_attend_price' => getInfluencerCostPriceText($itemBlogger['fb_video_attend_price'], $itemBlogger['payment_method']),
					'fb_live_attend_price' => getInfluencerCostPriceText($itemBlogger['fb_live_attend_price'], $itemBlogger['payment_method']),
					'fb_other_price' => $itemBlogger['fb_other_price'],
					'fb_definition' => RemoveEmoji($itemBlogger['fb_definition']),
				];
			}

			if ($exportInstagram) {
				$row += [
					'ig_name' => $itemBlogger['ig_name'],
					'ig_link' => $itemBlogger['ig_link'],
					'ig_fans' => $itemBlogger['ig_fans'],
					'ig_image_price' => getInfluencerCostPriceText($itemBlogger['ig_image_price'], $itemBlogger['payment_method']),
					'ig_sidecar_price' => getInfluencerCostPriceText($itemBlogger['ig_sidecar_price'], $itemBlogger['payment_method']),
					'ig_video_price' => getInfluencerCostPriceText($itemBlogger['ig_video_price'], $itemBlogger['payment_method']),
					'ig_live_price' => getInfluencerCostPriceText($itemBlogger['ig_live_price'], $itemBlogger['payment_method']),
					'ig_limited_post_price' => getInfluencerCostPriceText($itemBlogger['ig_limited_post_price'], $itemBlogger['payment_method']),
					'ig_image_attend_price' => getInfluencerCostPriceText($itemBlogger['ig_image_attend_price'], $itemBlogger['payment_method']),
					'ig_sidecar_attend_price' => getInfluencerCostPriceText($itemBlogger['ig_sidecar_attend_price'], $itemBlogger['payment_method']),
					'ig_video_attend_price' => getInfluencerCostPriceText($itemBlogger['ig_video_attend_price'], $itemBlogger['payment_method']),
					'ig_live_attend_price' => getInfluencerCostPriceText($itemBlogger['ig_live_attend_price'], $itemBlogger['payment_method']),
					'ig_limited_post_attend_price' => getInfluencerCostPriceText($itemBlogger['ig_limited_post_attend_price'], $itemBlogger['payment_method']),
					'ig_other_price' => $itemBlogger['ig_other_price'],
					'ig_definition' => RemoveEmoji($itemBlogger['ig_definition']),
				];
			}

			if ($exportYouTube) {
				$row += [
					'youtube_name' => $itemBlogger['youtube_name'],
					'youtube_link' => $itemBlogger['youtube_link'],
					'youtube_fans' => $itemBlogger['youtube_fans'],
					'youtube_video_price' => getInfluencerCostPriceText($itemBlogger['youtube_video_price'], $itemBlogger['payment_method']),
					'youtube_live_price' => getInfluencerCostPriceText($itemBlogger['youtube_live_price'], $itemBlogger['payment_method']),
					'youtube_post_to_fb_price' => getInfluencerCostPriceText($itemBlogger['youtube_post_to_fb_price'], $itemBlogger['payment_method']),
					'youtube_auth_to_net_price' => getInfluencerCostPriceText($itemBlogger['youtube_auth_to_net_price'], $itemBlogger['payment_method']),
					'youtube_raw_editable_auth_price' => getInfluencerCostPriceText($itemBlogger['youtube_raw_editable_auth_price'], $itemBlogger['payment_method']),
					'youtube_raw_readable_auth_price' => getInfluencerCostPriceText($itemBlogger['youtube_raw_readable_auth_price'], $itemBlogger['payment_method']),
					'youtube_other_price' => $itemBlogger['youtube_other_price'],
					'youtube_definition' => RemoveEmoji($itemBlogger['youtube_definition']),
				];
			}

			if ($exportFBAds) {
				$row += [
					'fbads_share_to_self_fb_price' => getInfluencerCostPriceText($itemBlogger['fbads_share_to_self_fb_price'], $itemBlogger['payment_method'], $itemBlogger['fbads_share_to_self_fb_unit']),
					'fbads_share_to_self_ig_price' => getInfluencerCostPriceText($itemBlogger['fbads_share_to_self_ig_price'], $itemBlogger['payment_method'], $itemBlogger['fbads_share_to_self_ig_unit']),
					'fbads_share_to_customer_fb_price' => getInfluencerCostPriceText($itemBlogger['fbads_share_to_customer_fb_price'], $itemBlogger['payment_method'], $itemBlogger['fbads_share_to_customer_fb_unit']),
					'fbads_share_to_client_fb_with_ad_price' => getInfluencerCostPriceText($itemBlogger['fbads_share_to_client_fb_with_ad_price'], $itemBlogger['payment_method'], $itemBlogger['fbads_share_to_client_fb_with_ad_unit']),
					'fbads_client_with_js_price' => getInfluencerCostPriceText($itemBlogger['fbads_client_with_js_price'], $itemBlogger['payment_method'], $itemBlogger['fbads_client_with_js_unit']),
					'fbads_client_with_customer_price' => getInfluencerCostPriceText($itemBlogger['fbads_client_with_customer_price'], $itemBlogger['payment_method'], $itemBlogger['fbads_client_with_customer_unit']),
					'fbads_do_it_self_price' => getInfluencerCostPriceText($itemBlogger['fbads_do_it_self_price'], $itemBlogger['payment_method'], $itemBlogger['fbads_do_it_self_unit']),
					'fbads_to_sponsor_price' => getInfluencerCostPriceText($itemBlogger['fbads_to_sponsor_price'], $itemBlogger['payment_method'], $itemBlogger['fbads_to_sponsor_unit']),
					'fbads_definition' => RemoveEmoji($itemBlogger['fbads_definition']),
				];
			}

			if ($exportAuth) {
				$row += [
					'auth_quote_to_website_with_feedback_price' => getInfluencerCostPriceText($itemBlogger['auth_quote_to_website_with_feedback_price'], $itemBlogger['payment_method'], $itemBlogger['auth_quote_to_website_with_feedback_unit']),
					'auth_quote_to_website_without_feedback_price' => getInfluencerCostPriceText($itemBlogger['auth_quote_to_website_without_feedback_price'], $itemBlogger['payment_method'], $itemBlogger['auth_quote_to_website_without_feedback_unit']),
					'auth_quote_to_ec_with_feedback_price' => getInfluencerCostPriceText($itemBlogger['auth_quote_to_ec_with_feedback_price'], $itemBlogger['payment_method'], $itemBlogger['auth_quote_to_ec_with_feedback_unit']),
					'auth_quote_to_ec_without_feedback_price' => getInfluencerCostPriceText($itemBlogger['auth_quote_to_ec_without_feedback_price'], $itemBlogger['payment_method'], $itemBlogger['auth_quote_to_ec_without_feedback_unit']),
					'auth_quote_to_dm_price' => getInfluencerCostPriceText($itemBlogger['auth_quote_to_dm_price'], $itemBlogger['payment_method'], $itemBlogger['auth_quote_to_dm_unit']),
					'auth_single_photo_price' => getInfluencerCostPriceText($itemBlogger['auth_single_photo_price'], $itemBlogger['payment_method'], $itemBlogger['auth_single_photo_unit']),
					'auth_dispaly_network_price' => getInfluencerCostPriceText($itemBlogger['auth_dispaly_network_price'], $itemBlogger['payment_method'], $itemBlogger['auth_dispaly_network_unit']),
					'auth_native_ads_price' => getInfluencerCostPriceText($itemBlogger['auth_native_ads_price'], $itemBlogger['payment_method'], $itemBlogger['auth_native_ads_unit']),
					'auth_definition' => RemoveEmoji($itemBlogger['auth_definition']),
				];
			}

			if ($exportOther) {
				$row += [
					'other_attend_without_interview_price' => getInfluencerCostPriceText($itemBlogger['other_attend_without_interview_price'], $itemBlogger['payment_method']),
					'other_shoot_price' => getInfluencerCostPriceText($itemBlogger['other_shoot_price'], $itemBlogger['payment_method']),
					'other_annual_endorse' => $itemBlogger['other_annual_endorse'],
					'other_more_cooperation' => $itemBlogger['other_more_cooperation'],
				];
			}

			$row += [
				'personnel_info' => RemoveEmoji($itemBlogger['personnel_info']),
				'comment' => RemoveEmoji($itemBlogger['comment']),
			];

			if ($row) {
				$data[] = $row;
			}
		}
	}

	$excelActiveSheet->getRowDimension(1)->setRowHeight(34);
	for ($idxRow=2; $idxRow<=count($data) + 1; $idxRow++) {
		$excelActiveSheet->getRowDimension($idxRow)->setRowHeight(90);
	}

	SetExcellCellFromArray($objPHPExcel, $data, 0, 2, ['border']);
	SendExcellFile($objPHPExcel, '寫手成本報價 ('. date('Y-m-d') .')');
