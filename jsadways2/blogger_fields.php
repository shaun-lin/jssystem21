<?php
	
	IncludeFunctions('jsadways');

	$bloggerFields = IsId($objBlogger->getId()) ? $objBlogger->fields : Blogger::DEFAULT_FIELDS;
	if ($objBlogger->getVar('history')) {
		$bloggerFields['history'] = json_decode($objBlogger->getVar('history'), true);
	} else {
		$bloggerFields['history'] = [];
	}
	
	$bloggerSharedBank = $objBlogger->getVar('shared_bank_id') ? explode(',', $objBlogger->getVar('shared_bank_id')) : [];

	$objBloggerBank = CreateObject('BloggerBank');
	
	$objTagMap = CreateObject('TagMap');
	$rowsTagMap = $objTagMap->searchAll(sprintf("`map_relation` = 'blogger' AND `map_item_id` = %d", $objBlogger->getId()), '', '', 'map_tag', 'LEFT JOIN `tag` ON `map_tag` = `tag_id`');

?>
<style>
	.platform-tab-content {
		background-color: #fffff2;
	}

	.platform-tab-content table {
		background-color: white;
		margin-bottom: 0px;
	}

	.platform-tab-content table tr td:nth-child(1) {
		width: 200px;
	}

	b.red-opacity {
		color: red;
		opacity: 0.5;
	}

	input[type="text"] {
		width: 85%;
		text-align: center;
	}

	input[type="text"].influencer-price {
		color: red;
		font-size: 1.4em;
	}

	p.blogger-price-tip {
		text-align: right; 
		margin: 0; 
		margin-top: 8px; 
		color: #999;
	}

	textarea {
		width: 95%; 
		height: 70px; 
		resize: none;
	}

	div.unit-price-detail select {
		width: 50px;
	}

	table.influencer-bank-account {
		font-size: 1.2em; 
		background-color: white; 
		margin-bottom: 36px;
	}

	td[data-field="textarea"] {
		text-align: left;
	}

	td[data-field="invite-quota"] div.row-fluid div.span6 div.modified-detail {
		width: 70%;
		float: right;
	}

	td[data-field="unit-quota-with-invite"] > div.row-fluid > div.span6 > div.modified-detail {
		width: 70%;
		float: right;
	}

	table.influencer-bank-account td div.modified-detail {
		font-size: .75em; 
	}
</style>

<? if (isset($editMode) && $editMode) : ?>
	<style>
		td[data-field="unit-quota-with-invite"],
		td[data-field="invite-quota"] {
			background: linear-gradient(90deg, #fff0f7 50%, #efffef 50%);
		}
	</style>
<? endif; ?>

<div id="content" class="span10" style="text-shadow: none;">
	<form method="POST" action="blogger_save.php" id="form_blogger" enctype="multipart/form-data" onsubmit="return ValidateForm();">
		<input type="hidden" name="submitted" value="1">
		<input type="hidden" name="id" id="id" value="<?= $objBlogger->getId();?>">
		<div class="row-fluid">
			<div class="span8">
				<div class="box">
					<div class="box-header well" data-original-title>
						<h2><i class="fa fa-info-circle"></i> 關於寫手</h2>
					</div>
					<div class="box-content">
						<table class="table table-bordered table-striped" style="margin-bottom: 0px;">
							<tr>
								<td style="width: 120px;"><h4>名稱</h4></td>
								<td>
									<? if (isset($editMode) && $editMode) : ?>
										<input type="text" name="display_name" value="<?= htmlspecialchars($objBlogger->getVar('display_name'), ENT_QUOTES); ?>" />
									<? else : ?>
										<?= htmlspecialchars(empty($objBlogger->getVar('display_name')) ? $objBlogger->getName() : $objBlogger->getVar('display_name'), ENT_QUOTES); ?>
									<? endif; ?>
								</td>
								<td rowspan="4" style="width: 200px;">
									<center>
										<? if (empty($objBlogger->getVar('photo')) || !file_exists(__DIR__ .'/'. $objBlogger->getVar('photo'))) : ?>
											<div style="font-size: 10em; margin-bottom: 10px; opacity: .2;">
												<i class="fa fa-user-circle"></i>
											</div>
										<? else : ?>
											<img src="<?= $objBlogger->getVar('photo'); ?>" style="width: 180px; height: auto; display: block; border-radius: 6px; box-shadow: 3px 3px 2px #ccc;" />
										<? endif; ?>

										<? if (isset($editMode) && $editMode) : ?>
											<input type="file" name="attachment">
										<? endif; ?>
									</center>
								</td>
							</tr>
							<tr>
								<td><h4>性別</h4></td>
								<td>
									<? if (isset($editMode) && $editMode) : ?>
										<select name="sex">
											<option value=""> -- 選擇性別 -- </option>
											<option value="男" <?= $objBlogger->getVar('sex') == '男' ? 'selected' : ''; ?>>男</option>
											<option value="女" <?= $objBlogger->getVar('sex') == '女' ? 'selected' : ''; ?>>女</option>
										</select>
									<? else : ?>
										<? if ($objBlogger->getVar('sex') == '男') : ?>
											<i class="fa fa-male" style="color: blue;"></i>
										<? elseif ($objBlogger->getVar('sex') == '女') : ?>
											<i class="fa fa-female" style="color: red;"></i>
										<? endif; ?>
										<?= $objBlogger->getVar('sex'); ?>
									<? endif; ?>
								</td>
							</tr>
							<tr>
								<td><h4>類別</h4></td>
								<td>
									<? if (isset($editMode) && $editMode) : ?>
										<select multiple data-rel="chosen" name="class[]" id="class" style="width: 100%;">
											<? foreach ($objCategory->searchAll("`category_relation` = 'blogger'", 'category_sort', 'ASC') as $itemCategory) : ?>
												<option value="<?= $itemCategory['category_name']; ?>" <?= strpos("、". $objBlogger->getVar('class') ."、", "、{$itemCategory['category_name']}、") === false ? '' : 'selected'; ?>><?= $itemCategory['category_name']; ?></option>
											<? endforeach; ?>
										</select>
									<? else : ?>
										<?= htmlspecialchars($objBlogger->getVar('class'), ENT_QUOTES); ?>	
									<? endif; ?>
								</td>
							</tr>
							<tr>
								<td><h4>標籤</h4></td>
								<td>
									<? if (isset($editMode) && $editMode) : ?>
										<select data-rel="chosen" name="tag_id[]" multiple style="width: 100%;">
											<? foreach ($objTag->getRelationData('blogger') as $itemTag) : ?>
												<option value="<?= $itemTag['tag_id']; ?>" <?= array_key_exists($itemTag['tag_id'], $rowsTagMap) ? 'selected' : ''; ?>><?= htmlspecialchars($itemTag['tag_name'], ENT_QUOTES); ?></option>
											<? endforeach; ?>
										</select>
									<? else : ?>
										<? foreach ($rowsTagMap as $itemBloggerTag) : ?>
											<span class="blogger-tag" style="background-color: <?= $itemBloggerTag['tag_color']; ?>;">&nbsp;<i class="fa fa-tag"></i>&nbsp;<?= htmlspecialchars($itemBloggerTag['tag_name'], ENT_QUOTES); ?>&nbsp;</span>
										<? endforeach; ?>	
									<? endif; ?>
								</td>
							</tr>
							<tr>
								<td><h4>簡介</h4></td>
								<td id="column_description" data-field="textarea" colspan="2"></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>

		<div class="row-fluid">
			<div class="span8">
				<div class="box">
					<div class="box-content">
						<table class="table table-bordered" style="margin-bottom: 0px;">
							<tr>
								<td style="width: 200px;"><h4>費用條件</h4></td>
								<td id="column_payment_method">
									<? if (empty($editMode)) : ?>
										<div style="margin-top: 10px; color: #ccc; text-shadow: none;">
											<p style="margin-bottom: 0px;">含稅 = 報價</p>
											<p style="margin-bottom: 0px;">實拿 = 報價 / 0.8809</p>
											<p style="margin-bottom: 0px;">未稅開發票 = 報價 * 1.05</p>
											<p style="margin-bottom: 0px;">實拿(免二代健保) = 報價 / 0.9</p>
										</div>
									<? else : ?>
										<select name="payment_method" id="payment_method">
											<option value=""> --- </option>
											<? foreach (Blogger::PAYMENT_METHOD_TEXT as $varMethod => $textMethod) : ?>
												<option value="<?= $varMethod; ?>"><?= $textMethod; ?></option>
											<? endforeach; ?>
										</select>
									<? endif; ?>
								</td>
							</tr>
							<tr>
								<td><h4>付款規則</h4></td>
								<td id="column_payment_ticket">
									<? if (empty($editMode)) : ?>
										<?= array_key_exists($objBlogger->getVar('payment_ticket'), Blogger::PAYMENT_TICKET_TEXT) ? htmlspecialchars(Blogger::PAYMENT_TICKET_TEXT[$objBlogger->getVar('payment_ticket')], ENT_QUOTES) : ''; ?>
									<? else : ?>
										<select name="payment_ticket" id="payment_ticket">
											<option value=""> --- </option>
											<? foreach (Blogger::PAYMENT_TICKET_TEXT as $varTicket => $textTicket) : ?>
												<option value="<?= $varTicket; ?>"><?= $textTicket; ?></option>
											<? endforeach; ?>
										</select>
									<? endif; ?>
								</td>
							</tr>
						</table>
					</div>
				</div>
				<hr/>

				<div class="box platform-info">
					<div class="box-header well" data-original-title>
						<h2 style="font-size: 1.2em;"><img src="images/blogspot.png" style="width: auto; height: 24px; border-radius: 2px;">&nbsp;Blog</h2>
					</div>
					<div class="box-content platform-tab-content">
						<table class="table table-bordered table-striped">
							<tr>
								<td><h4>名稱</h4></td>
								<td id="column_blog_name" data-field="text"></td>
							</tr>
							<tr>
								<td><h4>連結</h4></td>
								<td id="column_blog_link" data-field="text" data-action="link"></td>
							</tr>
							<tr>
								<td><h4>流量</h4></td>
								<td id="column_blog_flow" data-field="text"></td>
							</tr>
							<tr>
								<td><h4>文章</h4></td>
								<td id="column_blog_article_price" data-field="invite-quota"></td>
							</tr>
							<tr>
								<td><h4>文章 (出席體驗)</h4></td>
								<td id="column_blog_article_attend_price" data-field="invite-quota"></td>
							</tr>
							<tr>
								<td><h4>其他費用</h4></td>
								<td id="column_blog_other_price" data-field="textarea"></td>
							</tr>
							<tr>
								<td><h4>製作規範</h4></td>
								<td id="column_blog_definition" data-field="textarea"></td>
							</tr>
						</table>
						<p class="blogger-price-tip"></p>
					</div>
				</div>

				<div class="box platform-info">
					<div class="box-header well" data-original-title>
						<h2 style="font-size: 1.2em;"><i class="fa fa-facebook-official" style="font-size: 1.4em; color: #5a5aff;"></i> FB</h2>
					</div>
					<div class="box-content platform-tab-content">
						<table class="table table-bordered table-striped">
							<tr>
								<td><h4>名稱</h4></td>
								<td id="column_fb_name" data-field="text"></td>
							</tr>
							<tr>
								<td><h4>連結</h4></td>
								<td id="column_fb_link" data-field="text" data-action="link"></td>
							</tr>
							<tr>
								<td><h4>流量</h4></td>
								<td id="column_fb_fans" data-field="text"></td>
							</tr>
							<tr>
								<td><h4>靜態圖文</h4></td>
								<td id="column_fb_post_price" data-field="invite-quota"></td>
							</tr>
							<tr>
								<td><h4>影片</h4></td>
								<td id="column_fb_video_price" data-field="invite-quota"></td>
							</tr>
							<tr>
								<td><h4>直播</h4></td>
								<td id="column_fb_live_price" data-field="invite-quota"></td>
							</tr>
							<tr>
								<td><h4>轉分享客戶素材 (僅撰文)</h4></td>
								<td id="column_fb_share_price" data-field="invite-quota"></td>
							</tr>
							<tr>
								<td><h4>打卡 (出席體驗)</h4></td>
								<td id="column_fb_checkin_attend_price" data-field="invite-quota"></td>
							</tr>
							<tr>
								<td><h4>靜態圖文 (出席體驗)</h4></td>
								<td id="column_fb_post_attend_price" data-field="invite-quota"></td>
							</tr>
							<tr>
								<td><h4>影片 (出席體驗)</h4></td>
								<td id="column_fb_video_attend_price" data-field="invite-quota"></td>
							</tr>
							<tr>
								<td><h4>直播 (出席體驗)</h4></td>
								<td id="column_fb_live_attend_price" data-field="invite-quota"></td>
							</tr>
							<tr>
								<td><h4>其他費用</h4></td>
								<td id="column_fb_other_price" data-field="textarea"></td>
							</tr>
							<tr>
								<td><h4>製作規範</h4></td>
								<td id="column_fb_definition" data-field="textarea"></td>
							</tr>
						</table>
						<p class="blogger-price-tip"></p>
					</div>
				</div>
				
				<div class="box platform-info">
					<div class="box-header well" data-original-title>
						<h2 style="font-size: 1.2em;"><i class="fa fa-instagram" style="font-size: 1.4em; color: #fe99ff;"></i> Instagram</h2>
					</div>
					<div class="box-content platform-tab-content">
						<table class="table table-bordered table-striped">
							<tr>
								<td><h4>名稱</h4></td>
								<td id="column_ig_name" data-field="text"></td>
							</tr>
							<tr>
								<td><h4>連結</h4></td>
								<td id="column_ig_link" data-field="text" data-action="link"></td>
							</tr>
							<tr>
								<td><h4>追蹤人數</h4></td>
								<td id="column_ig_fans" data-field="text"></td>
							</tr>
							<tr>
								<td><h4>靜態單圖文</h4></td>
								<td id="column_ig_image_price" data-field="invite-quota"></td>
							</tr>
							<tr>
								<td><h4>靜態多圖文</h4></td>
								<td id="column_ig_sidecar_price" data-field="invite-quota"></td>
							</tr>
							<tr>
								<td><h4>影片</h4></td>
								<td id="column_ig_video_price" data-field="invite-quota"></td>
							</tr>
							<tr>
								<td><h4>直播 (保存至限時動態)</h4></td>
								<td id="column_ig_live_price" data-field="invite-quota"></td>
							</tr>
							<tr>
								<td><h4>限時動態</h4></td>
								<td id="column_ig_limited_post_price" data-field="invite-quota"></td>
							</tr>
							<tr>
								<td><h4>靜態單圖文 (出席體驗)</h4></td>
								<td id="column_ig_image_attend_price" data-field="invite-quota"></td>
							</tr>
							<tr>
								<td><h4>靜態多圖文 (出席體驗)</h4></td>
								<td id="column_ig_sidecar_attend_price" data-field="invite-quota"></td>
							</tr>
							<tr>
								<td><h4>影片 (出席體驗)</h4></td>
								<td id="column_ig_video_attend_price" data-field="invite-quota"></td>
							</tr>
							<tr>
								<td><h4>直播 (保存至限時動態)(出席體驗)</h4></td>
								<td id="column_ig_live_attend_price" data-field="invite-quota"></td>
							</tr>
							<tr>
								<td><h4>限時動態 (出席體驗)</h4></td>
								<td id="column_ig_limited_post_attend_price" data-field="invite-quota"></td>
							</tr>
							<tr>
								<td><h4>其他費用</h4></td>
								<td id="column_ig_other_price" data-field="textarea"></td>
							</tr>
							<tr>
								<td><h4>製作規範</h4></td>
								<td id="column_ig_definition" data-field="textarea"></td>
							</tr>
						</table>
						<p class="blogger-price-tip"></p>
					</div>
				</div>

				<div class="box platform-info">
					<div class="box-header well" data-original-title>
						<h2 style="font-size: 1.2em;"><i class="fa fa-youtube-play" style="font-size: 1.4em; color: #ff4c4c;"></i> YouTube</h2>
					</div>
					<div class="box-content platform-tab-content">
						<table class="table table-bordered table-striped">
							<tr>
								<td><h4>名稱</h4></td>
								<td id="column_youtube_name" data-field="text"></td>
							</tr>
							<tr>
								<td><h4>連結</h4></td>
								<td id="column_youtube_link" data-field="text" data-action="link"></td>
							</tr>
							<tr>
								<td><h4>流量</h4></td>
								<td id="column_youtube_fans" data-field="text"></td>
							</tr>
							<tr>
								<td><h4>影片</h4></td>
								<td id="column_youtube_video_price" data-field="invite-quota"></td>
							</tr>
							<tr>
								<td><h4>直播</h4></td>
								<td id="column_youtube_live_price" data-field="invite-quota"></td>
							</tr>
							<tr>
								<td><h4>影片上傳FB (非轉發)</h4></td>
								<td id="column_youtube_post_to_fb_unit" data-field="unit-quota" data-unit="option-2-with-single-unit"></td>
							</tr>
							<tr>
								<td><h4>授權引用網路全平台</h4></td>
								<td id="column_youtube_auth_to_net_unit" data-field="unit-quota" data-unit="option-2-with-basic-unit"></td>
							</tr>
							<tr>
								<td><h4>原檔授權 (可剪輯)</h4></td>
								<td id="column_youtube_raw_editable_auth_unit" data-field="unit-quota" data-unit="option-2-with-basic-unit"></td>
							</tr>
							<tr>
								<td><h4>原檔授權 (不可剪輯)</h4></td>
								<td id="column_youtube_raw_readable_auth_unit" data-field="unit-quota" data-unit="option-2-with-basic-unit"></td>
							</tr>
							<tr>
								<td><h4>其他費用</h4></td>
								<td id="column_youtube_other_price" data-field="textarea"></td>
							</tr>
							<tr>
								<td><h4>製作規範</h4></td>
								<td id="column_youtube_definition" data-field="textarea"></td>
							</tr>
						</table>
						<p class="blogger-price-tip"></p>
					</div>
				</div>

				<div class="box platform-info">
					<div class="box-header well" data-original-title>
						<h2 style="font-size: 1.2em;"><i class="fa fa-facebook-square" style="font-size: 1.4em; color: #5a5aff;"></i> FB廣告</h2>
					</div>
					<div class="box-content platform-tab-content">
						<table class="table table-bordered table-striped">
							<tr>
								<td><h4>轉分享至部落客FB</h4></td>
								<td id="column_fbads_share_to_self_fb_unit" data-field="unit-quota-with-invite" data-unit="option-3-with-basic-unit"></td>
							</tr>
							<tr>
								<td><h4>轉分享至部落客IG</h4></td>
								<td id="column_fbads_share_to_self_ig_unit" data-field="unit-quota-with-invite" data-unit="option-3-with-basic-unit"></td>
							</tr>
							<tr>
								<td><h4>轉分享至客戶FB</h4></td>
								<td id="column_fbads_share_to_customer_fb_unit" data-field="unit-quota-with-invite" data-unit="option-3-with-basic-unit"></td>
							</tr>
							<tr>
								<td><h4>轉分享至客戶FB並下廣告</h4></td>
								<td id="column_fbads_share_to_client_fb_with_ad_unit" data-field="unit-quota-with-invite" data-unit="option-3-with-professional-unit"></td>
							</tr>
							<tr>
								<td><h4>加傑思為廣告主</h4></td>
								<td id="column_fbads_client_with_js_unit" data-field="unit-quota" data-unit="option-3-with-professional-unit"></td>
							</tr>
							<tr>
								<td><h4>加客戶為廣告主</h4></td>
								<td id="column_fbads_client_with_customer_unit" data-field="unit-quota" data-unit="option-2-with-basic-unit"></td>
							</tr>
							<tr>
								<td><h4>寫手自行操作廣告</h4></td>
								<td id="column_fbads_do_it_self_unit" data-field="unit-quota" data-unit="option-2-with-single-unit"></td>
							</tr>
							<tr>
								<td><h4>Add sponsor</h4></td>
								<td id="column_fbads_to_sponsor_unit" data-field="unit-quota" data-unit="option-3-with-empty-unit"></td>
							</tr>
							<tr>
								<td><h4>廣告規範</h4></td>
								<td id="column_fbads_definition" data-field="textarea"></td>
							</tr>
						</table>
						<p class="blogger-price-tip"></p>
					</div>
				</div>

				<div class="box platform-info">
					<div class="box-header well" data-original-title>
						<h2 style="font-size: 1.2em;"><i class="fa fa-file-text-o" style="font-size: 1.4em; color: #666;"></i> 引用授權</h2>
					</div>
					<div class="box-content platform-tab-content">
						<table class="table table-bordered table-striped">
							<tr>
								<td><h4>引用至官網/campaign(連回)</h4><small>*一張識別圖及部份文字</small></td>
								<td id="column_auth_quote_to_website_with_feedback_unit" data-field="unit-quota-with-invite" data-unit="option-3-with-ultimate-unit"></td>
							</tr>
							<tr>
								<td><h4>引用至官網/campaign(不連回)</h4><small>*一張識別圖及部份文字</small></td>
								<td id="column_auth_quote_to_website_without_feedback_unit" data-field="unit-quota-with-invite" data-unit="option-3-with-ultimate-unit"></td>
							</tr>
							<tr>
								<td><h4>引用至EC(連回)</h4><small>*一張識別圖及部份文字</small></td>
								<td id="column_auth_quote_to_ec_with_feedback_unit" data-field="unit-quota-with-invite" data-unit="option-3-with-ultimate-unit"></td>
							</tr>
							<tr>
								<td><h4>引用至EC(不連回)</h4><small>*一張識別圖及部份文字</small></td>
								<td id="column_auth_quote_to_ec_without_feedback_unit" data-field="unit-quota-with-invite" data-unit="option-3-with-ultimate-unit"></td>
							</tr>
							<tr>
								<td><h4>引用至平面/DM</h4><small>*一張識別圖及部份文字</small></td>
								<td id="column_auth_quote_to_dm_unit" data-field="unit-quota-with-invite" data-unit="option-3-with-ultimate-unit"></td>
							</tr>
							<tr>
								<td><h4>照片授權(單張)</h4></td>
								<td id="column_auth_single_photo_unit" data-field="unit-quota-with-invite" data-unit="option-3-with-expert-unit"></td>
							</tr>
							<tr>
								<td><h4>聯播網廣告</h4></td>
								<td id="column_auth_dispaly_network_unit" data-field="unit-quota-with-invite" data-unit="option-3-with-novice-unit"></td>
							</tr>
							<tr>
								<td><h4>原生廣告</h4></td>
								<td id="column_auth_native_ads_unit" data-field="unit-quota-with-invite" data-unit="option-3-with-novice-unit"></td>
							</tr>
							<tr>
								<td><h4>相關規範</h4></td>
								<td id="column_auth_definition" data-field="textarea"></td>
							</tr>
						</table>
						<p class="blogger-price-tip"></p>
					</div>
				</div>

				<div class="box platform-info">
					<div class="box-header well" data-original-title>
						<h2 style="font-size: 1.2em;"><i class="fa fa-question-circle-o" style="font-size: 1.4em; color: #666;"></i> 其他</h2>
					</div>
					<div class="box-content platform-tab-content">
						<table class="table table-bordered table-striped">
							<tr>
								<td><h4>靠櫃/活動出席</h4><small>*不含媒體曝光、受訪等</small></td>
								<td id="column_other_attend_without_interview_price" data-field="invite-quota"></td>
							</tr>
							<tr>
								<td><h4>拍攝</h4></td>
								<td id="column_other_shoot_price" data-field="invite-quota"></td>
							</tr>
							<tr>
								<td><h4>年度代言</h4></td>
								<td id="column_other_annual_endorse" data-field="textarea"></td>
							</tr>
							<tr>
								<td><h4>更多合作項目</h4></td>
								<td id="column_other_more_cooperation" data-field="textarea"></td>
							</tr>
						</table>
						<p class="blogger-price-tip"></p>
					</div>
				</div>
				<hr/>

				<div class="box">
					<div class="box-content">
						<table class="table table-bordered" style="margin-bottom: 0px;">
							<tr>
								<td style="width: 200px;"><h4>個人資訊</h4><small>*尺寸、膚況、耳洞、小孩、感情婚姻等</small></td>
								<td id="column_personnel_info" data-field="textarea"></td>
							</tr>
							<tr>
								<td><h4>備註</h4></td>
								<td id="column_personnel_comment" data-field="textarea"></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>

		<? if (empty($editMode)) : ?>
			<hr/>
			<div class="row-fluid">
				<div class="span8">
					<div class="box">
						<?php
							$db = clone($GLOBALS['app']->db);
							$sqlCampaign = sprintf("SELECT `campaign`.`id`, `campaign`.`name`, `campaign`.`date11`, `campaign`.`date22`, `campaign`.`memberid`, `campaign`.`member`, `mrbs_users`.`name` AS `ae_en_name` FROM (SELECT `campaign_id` FROM `media19_detail` WHERE `blogid` = %d GROUP BY `campaign_id`) `campaign_list`
													LEFT JOIN `campaign` ON `campaign_list`.`campaign_id` = `campaign`.`id` 
													LEFT JOIN `{$GLOBALS['env']['db_master']}`.`mrbs_users` ON `mrbs_users`.`id` = `campaign`.`memberid` 
													WHERE `campaign`.`status` IN (3, 4, 5, 6, 7) 
													ORDER BY `date11` DESC;", $objBlogger->getId());
							$db->query($sqlCampaign);
						?>
						<div class="box-header well" data-original-title>
							<h2><i class="fa fa-history"></i> 過往合作案件</h2>
						</div>
						<div class="box-content">
							<table class="table table-striped table-bordered">
								<thead>
									<tr>
										<th style="text-align: left;">案件名稱</th>
									</tr>
								</thead>
								<tbody>
									<? if ($db->get_num_rows()) : ?>
										<? while ($itemCampaign = $db->next_record()) : ?>
											<tr>
												<td style="text-align: left;">
													<? if ($isGrantEdit || GetCurrentUserId() == $itemCampaign['memberid']) : ?>
														<a href="campaign_view.php?id=<?= $itemCampaign['id']; ?>" target="_blank"><?= $itemCampaign['name']; ?></a>
													<? else : ?>
														<?= $itemCampaign['name']; ?>
													<? endif; ?>
													&nbsp;&nbsp;(<i>＊負責AE:</i>&nbsp;&nbsp;<?= ucfirst($itemCampaign['ae_en_name']) . $itemCampaign['member']; ?>, <i>＊走期:</i>&nbsp;&nbsp;<?= date('Y/m/d', $itemCampaign['date11']) .' ~ '. date('Y/m/d', $itemCampaign['date22']); ?>)
												</td>
											</tr>
										<? endwhile; ?>
									<? else : ?>
										<tr><td>沒有任何記錄</td></tr>
									<? endif; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		<? endif; ?>
		
		
		<? if ($isGrantViewAccount) : ?>
			<hr/>
			<div class="row-fluid">	
				<div class="box span6">
					<div class="box-header well" data-original-title>
						<h2><i class="fa fa-list-alt"></i> 詳細資料</h2>
					</div>
					<div class="box-content">
						<table class="table table-bordered table-striped" style="font-size:  1.2em;">
							<tr>
								<td style="width: 180px;"><h4>帳戶ID</h4></td>
								<td id="column_ac_id" data-field="text"></td>
							</tr>
							<tr>
								<td><h4>真實姓名</h4></td>
								<td id="column_true_name" data-field="text"></td>
							</tr>
							<tr>
								<td><h4>聯繫窗口</h4></td>
								<td id="column_contact" data-field="text"></td>
							</tr>
							<tr>
								<td><h4>聯絡電話</h4></td>
								<td id="column_telephone1" data-field="text"></td>
							</tr>
							<tr>
								<td><h4>聯絡電話2</h4></td>
								<td id="column_telephone2" data-field="text"></td>
							</tr>
							<tr>
								<td><h4>通訊地址1</h4></td>
								<td id="column_address1" data-field="text"></td>
							</tr>
							<tr>
								<td><h4>通訊地址2</h4></td>
								<td id="column_address2" data-field="text"></td>
							</tr>
							<tr>
								<td><h4>通訊地址3</h4></td>
								<td id="column_address3" data-field="text"></td>
							</tr>
							<tr>
								<td><h4>戶籍地</h4></td>
								<td id="column_registration" data-field="text"></td>
							</tr>
							<tr>
								<td><h4>Email</h4></td>
								<td id="column_email1" data-field="text"></td>
							</tr>
							<tr>
								<td><h4>Email2</h4></td>
								<td id="column_email2" data-field="text"></td>
							</tr>
							<tr>
								<td><h4>出生年月日</h4></td>
								<td id="column_birthday" data-field="text"></td>
							</tr>
							<tr>
								<td><h4>身份證字號</h4></td>
								<td id="column_idnumber" data-field="text"></td>
							</tr>
							<tr>
								<td><h4>備註</h4></td>
								<td id="column_comment" data-field="textarea" style="text-align: left;"></td>
							</tr>
						</table>
						<? if ($isGrantEdit) : ?>
							<? if (empty($editMode)) : ?>
								<a class="btn btn-info" href="blogger_edit.php?id=<?= $objBlogger->getVar('id'); ?>">
									<i class="fa fa-pencil"></i>&nbsp;編輯
								</a>
							<? else : ?>
								<button type="submit" class="btn btn-info"><i class="fa fa-floppy-o"></i>&nbsp;儲存</button>
							<? endif; ?>
						<? endif; ?>
					</div>
					
				</div>
				<?php
					$rowsSharedBank = $objBloggerBank->searchAll(" `states` = 1 AND `shared` =1 ", '', '', 'id');
					$rowsBloggerBank = IsId($objBlogger->getId()) ? $objBloggerBank->searchAll(sprintf(" `states` = 1 AND `blogger_id` = %d ", $objBlogger->getId()), '', '', 'id') : [];
					
					foreach ($rowsBloggerBank as $idxBank => $itemBank) {
						if ($itemBank['history']) {
							$rowsBloggerBank[$idxBank]['history'] = json_decode($itemBank['history'], true);
						}
					}
				?>

				<div class="box span6">
					<div class="box-header well" data-original-title>
						<h2><i class="fa fa-usd"></i> 銀行帳戶資訊</h2>
					</div>
					<script type="text/template" id="bank_account_template">
						<table class="table table-bordered table-striped influencer-bank-account" id="blogger_bank_{id}">
							<tr>
								<td colspan="2" style="text-align: left;">
									<input type="hidden" name="bankFields[id][{idxArray}]" value="{id}" />
									<h4>●&nbsp;&nbsp;個人銀行資訊 <span class="idx-bank-account">{numBankAccount}</span>
										<? if (isset($editMode) && $editMode) : ?>
											<span class="pull-right btn-del-account" onclick="DelBankAccount('{id}', this);"><i class="fa fa-trash"></i>刪除</span>
										<? endif; ?>
									</h4>
								</td>
							</tr>
							<tr>
								<td style="width: 180px;"><h4>銀行別</h4></td>
								<td>
									<? if (isset($editMode) && $editMode) : ?>
										<input type="text" name="bankFields[bankName][{idxArray}]" value="{bankName}" {attr} />
									<? else : ?>
										{bankName}
									<? endif; ?>
									{bankNameHistory}
								</td>
							</tr>
							<tr>
								<td><h4>銀行代號</h4></td>
								<td>
									<? if (isset($editMode) && $editMode) : ?>
										<input type="text" name="bankFields[bankCode][{idxArray}]" value="{bankCode}" {attr} />
									<? else : ?>
										{bankCode}
									<? endif; ?>
									{bankCodeHistory}
								</td>
							</tr>
							<tr>
								<td><h4>銀行帳號</h4></td>
								<td>
									<? if (isset($editMode) && $editMode) : ?>
										<input type="text" name="bankFields[bankAC][{idxArray}]" value="{bankAC}" {attr} />
									<? else : ?>
										{bankAC}
									<? endif; ?>
									{bankACHistory}
								</td>
							</tr>
							<tr>
								<td><h4>銀行檢查碼</h4></td>
								<td>
									<? if (isset($editMode) && $editMode) : ?>
										<input type="text" name="bankFields[bankCheckCode][{idxArray}]" value="{bankCheckCode}" {attr} />
									<? else : ?>
										{bankCheckCode}
									<? endif; ?>
									{bankCheckCodeHistory}
								</td>
							</tr>
							<tr>
								<td><h4>戶名</h4></td>
								<td>
									<? if (isset($editMode) && $editMode) : ?>
										<input type="text" name="bankFields[bankUserName][{idxArray}]" value="{bankUserName}" {attr} />
									<? else : ?>
										{bankUserName}
									<? endif; ?>
									{bankUserNameHistory}
								</td>
							</tr>
							<tr>
								<td><h4>戶名身份證字號</h4></td>
								<td>
									<? if (isset($editMode) && $editMode) : ?>
										<input type="text" name="bankFields[bankIdNum][{idxArray}]" value="{bankIdNum}" {attr} />
									<? else : ?>
										{bankIdNum}
									<? endif; ?>
									{bankIdNumHistory}
								</td>
							</tr>
							<? if (isset($editMode) && $editMode) : ?>
								<tr>
									<td><h4>設為主要帳戶</h4></td>
									<td>
										<input type="checkbox" class="influencer-account-main" name="bankFields[main][{idxArray}]" value="1" onclick="ChangeMainAccount(this);"/>
									</td>
								</tr>
							<? endif; ?>
							<tr>
								<td><h4>帳戶指定付款方式</h4></td>
								<td>
									<? if (isset($editMode) && $editMode) : ?>
										<select  name="bankFields[account_payment_method][{idxArray}]">
											<option value=""> --- </option>
											<? foreach (Blogger::PAYMENT_METHOD_TEXT as $varMethod => $textMethod) : ?>
												<option value="<?= $varMethod; ?>" [selected<?= $varMethod; ?>]><?= $textMethod; ?></option>
											<? endforeach; ?>
										</select>
									<? else : ?>
										[account_payment_method]
									<? endif; ?>
									{account_payment_methodHistory}
								</td>
							</tr>
						</table>
					</script>
					<style>
						span.btn-del-account {
							color: red;
							opacity: .7;
							border: 1px solid red;
							padding: 2px 3px;
							margin-right: 2px;
							border-radius: 4px;
							display: block;
							cursor: pointer;
						}
					</style>
					<div class="box-content" style="background-color: #f2fffc;" id="account_bank_list">
						<table class="table table-bordered table-striped" style="background-color: white; margin-bottom: 0px;" id="bank_empty">
							<tr>
								<td>沒有任何資料</td>
							</tr>
						</table>
					</div>
					<? if (isset($editMode) && $editMode) : ?>
						<div style="background-color: #f2fffc; text-align: center; padding-bottom: 16px;">
							<table class="" style="background-color: #f2fffc; margin-bottom: 0px; width: 100%;">
								<tr>
									<td>
										<button type="button" class="btn btn-warning" onclick="AddBankAccount(null);"><i class="fa fa-usd"></i>&nbsp;新增個人銀行帳戶</button>
										<br/><br/>
									</td>
								</tr>
								<tr>
									<td>
										<div style="background-color: #f9f9f9; margin: 0 10px; padding: 8px; border-radius: 8px; border: 1px solid #dddddd;">
										<select id="shared_selector" style="margin-top: 8px; margin-right: 24px; width: 80%;">
											<option value=""> --- </option>
											<? foreach ($rowsSharedBank as $itemSharedBank) : ?>
												<option value="<?= $itemSharedBank['id']; ?>"><?= $itemSharedBank['bankUserName']; ?> - <?= $itemSharedBank['bankName']; ?></option>
											<? endforeach; ?>
										</select>
										<br/>
											<button type="button" class="btn btn-inverse" onclick="AddBankAccount($('#shared_selector').val(), true);"><i class="fa fa-usd"></i>&nbsp;加入共用銀行帳戶</button>
										</div>
									</td>
								</tr>
							</table>
						</div>
					<? endif; ?>
					<script>
						var tplBankAccount = $('#bank_account_template');
						var listBankAccount = $('#account_bank_list');
						var idxArray = 1;
						var numBankAccount = 1;
						var rowsBankAccount = <?= json_encode($rowsBloggerBank); ?>;
						var rowsSharedBank = <?= json_encode($rowsSharedBank); ?>;

						function AddBankAccount(bankId, shared)
						{
							$(listBankAccount).find('table#bank_empty').hide();
							var bankAccountHTML = $(tplBankAccount).html().replace(/\{numBankAccount\}/g, numBankAccount);
							bankAccountHTML = bankAccountHTML.replace(/\{idxArray\}/g, idxArray);

							if (shared) {
								if (bankId && bankId in rowsSharedBank) {
									for (var idxBankAccount in rowsSharedBank[bankId]) {
										bankAccountHTML = bankAccountHTML.replace(new RegExp('\\{'+ idxBankAccount +'\\}', "g"), rowsSharedBank[bankId][idxBankAccount]);

										if (typeof rowsSharedBank[bankId]['history'] === 'object' && idxBankAccount in rowsSharedBank[bankId]['history']) {
											bankAccountHTML = bankAccountHTML.replace(new RegExp('\\{'+ idxBankAccount +'History\\}', "g"), DisplayModification(rowsSharedBank[bankId]['history'][idxBankAccount]['modified'], rowsSharedBank[bankId]['history'][idxBankAccount]['modifier']));
										}
									}

									try {
										<? if (empty($editMode)) : ?>
											if (rowsSharedBank[bankId]['account_payment_method'] in paymentMethod) {
												bankAccountHTML = bankAccountHTML.replace(/\[account_payment_method\]/g, paymentMethod[rowsSharedBank[bankId]['account_payment_method']]);
											} else {
												console.log(rowsSharedBank[bankId]['account_payment_method']);
												bankAccountHTML = bankAccountHTML.replace(/\[account_payment_method\]/g, '');
											}
										<? else : ?>
											if (bankAccountHTML.indexOf('[selected') > 0 && rowsSharedBank[bankId]['account_payment_method']) {
												bankAccountHTML = bankAccountHTML.replace('[selected'+ rowsSharedBank[bankId]['account_payment_method'] +']', 'selected');
											}
										<? endif; ?>
									} catch (e) {

									}
									
									bankAccountHTML = bankAccountHTML.replace(/\{attr\}/g, 'readonly');
								} else {
									console.log('ddd');
									return;
								}
							} else {
								if (bankId && bankId in rowsBankAccount) {
									for (var idxBankAccount in rowsBankAccount[bankId]) {
										bankAccountHTML = bankAccountHTML.replace(new RegExp('\\{'+ idxBankAccount +'\\}', "g"), rowsBankAccount[bankId][idxBankAccount]);

										if (typeof rowsBankAccount[bankId]['history'] === 'object' && idxBankAccount in rowsBankAccount[bankId]['history']) {
											bankAccountHTML = bankAccountHTML.replace(new RegExp('\\{'+ idxBankAccount +'History\\}', "g"), DisplayModification(rowsBankAccount[bankId]['history'][idxBankAccount]['modified'], rowsBankAccount[bankId]['history'][idxBankAccount]['modifier']));
										}
									}
								}
							}

							try {
								<? if (empty($editMode)) : ?>
									if (rowsBankAccount[bankId]['account_payment_method'] in paymentMethod) {
										bankAccountHTML = bankAccountHTML.replace(/\[account_payment_method\]/g, paymentMethod[rowsBankAccount[bankId]['account_payment_method']]);
									} else {
										bankAccountHTML = bankAccountHTML.replace(/\[account_payment_method\]/g, '');
									}
								<? else : ?>
									if (bankAccountHTML.indexOf('[selected') > 0 && rowsBankAccount[bankId]['account_payment_method']) {
										bankAccountHTML = bankAccountHTML.replace('[selected'+ rowsBankAccount[bankId]['account_payment_method'] +']', 'selected');
									}
								<? endif; ?>
							} catch (e) {

							}

							bankAccountHTML = bankAccountHTML.replace(/\[selected\d+\]/ig, '');

							bankAccountHTML = bankAccountHTML.replace(new RegExp('\\{[A-Za-z_]+\\}', "g"), '');
							$(listBankAccount).append(bankAccountHTML);
							
							idxArray++;
							numBankAccount++;
							delete bankAccountHTML;
						}

						function DelBankAccount(bankId, obj)
						{
							numBankAccount--;

							if (bankId) {
								$('form#form_blogger').append('<input type="hidden" name="disableBankAccount[]" value="'+ bankId +'" />');
								$('table#blogger_bank_'+ bankId).remove();
							} else {
								$(obj).parent().parent().parent().parent().parent().remove();
							}

							if ($('span.idx-bank-account').length) {
								$('span.idx-bank-account').each(function(idx) {
									$(this).html(idx + 1);
									numBankAccount = idx + 1 + 1;
								});
							} else {
								numBankAccount = 1;
								$(listBankAccount).find('table#bank_empty').show();
							}
						}

						function ChangeMainAccount(mainChecker)
						{
							var status = mainChecker.checked;

							$('.influencer-account-main').each(function() {
								this.checked = false;
							});

							mainChecker.checked = status ? true : false;
							delete status;
						}

						$(document).ready(function() {
							<? if ($mainBankId = $objBlogger->getVar('main_bank_id')) : ?>
								<? if (strpos(','. $objBlogger->getVar('shared_bank_id') .',', $mainBankId) === false) : ?>
									AddBankAccount(<?= (int)$mainBankId; ?>);
								<? else : ?>
									AddBankAccount(<?= (int)$mainBankId; ?>, true);
								<? endif; ?>
							<? endif; ?>
							
							<? foreach ($rowsBloggerBank as $itemBank) : ?>
								<? if (IsId($itemBank['id']) && $itemBank['id'] != $mainBankId) : ?>
									AddBankAccount(<?= (int)$itemBank['id']; ?>);
								<? endif; ?>
							<? endforeach; ?>

							<? foreach ($bloggerSharedBank as $sharedBankId) : ?>
								<? if (IsId($sharedBankId) && $sharedBankId != $mainBankId) : ?>
									AddBankAccount(<?= (int)$sharedBankId; ?>, true);
								<? endif; ?>
							<? endforeach; ?>

							<? if (isset($editMode) && $editMode) : ?>
								$('.influencer-account-main').each(function() {
									this.checked = true;
									return false;
								});
							<? endif; ?>
						});
					</script>
				</div>
			</div>
		<? endif; ?>
	</form>
</div>

<script>
	function DisplayModification(modified, modifier, prefix)
	{
		return [
			'<div class="modified-detail">',
				(prefix ? ('＊'+ prefix) : ''), 
				'&nbsp;(上次更新日期',
				modified,
				' By ',
				modifier,
				' )',
			'</div>'
		].join('');
	}

	function ChangeUnit(unitSelector)
	{
		if (unitSelector) {
			if (unitSelector.value == 'Y+') {
				$(unitSelector).find('option:last-child').html('可，');
				$(unitSelector).parent().removeClass('span12').addClass('span3 unit-price-detail').next().show().next().show();
			} else {
				$(unitSelector).find('option:last-child').html($(unitSelector).data('detail-text'));
				$(unitSelector).parent().removeClass('span3 unit-price-detail').addClass('span12').next().hide().next().hide();
			}
		}
	}

	function DisplayUnitQuotaWithInviteValue(unitType, unitId)
	{
		DisplayUnitQuotaValue(unitType, unitId);
		DisplayUnitQuotaValue(unitType, unitId.replace('_unit', '_invite_unit'));
	}

	function DisplayUnitQuotaValue(unitType, unitId, returnText)
	{
		var priceId = unitId.replace('_unit', '_price');
		
		if (unitType in priceUnit) {
			if (unitId && blogger[unitId] && blogger[unitId].length) {
				if (blogger[unitId] in priceUnit[unitType]['option'] && blogger[unitId] != 'Y+') {
					<? if (empty($editMode)) : ?>
						var text = priceUnit[unitType]['option'][blogger[unitId]].replace('可，', '');

						if (returnText) {
							return text;
						}
						$('td#column_'+ unitId +'').html(text);
					<? else : ?>
						$('select#'+ unitId +'_check').val(blogger[unitId]);
					<? endif; ?>
				} else if (priceUnit[unitType]['unit'].indexOf(blogger[unitId]) != -1) {
					<? if (empty($editMode)) : ?>
						var text = '<i class="fa fa-usd"></i>'+ (isNaN(blogger[priceId]) ? blogger[priceId] : NumberFormat(blogger[priceId])) +'/'+ blogger[unitId];

						if (returnText) {
							return text;
						}
						$('td#column_'+ unitId +'').html(text);
					<? else : ?>
						$('select#'+ unitId +'_check').val('Y+');
						$('select#'+ unitId).val(blogger[unitId]);
					<? endif; ?>
				}

				<? if (isset($editMode) && $editMode) : ?>
					$('select#'+ unitId +'_check').change();
				<? endif; ?>
			}
		}
	}
	
	function PriceProfitExcluded(price)
	{
		return isNaN(price) ? 0 : ('<i class="fa fa-usd" profit-excluded></i>'+ NumberFormat(price * <?= $objBlogger->getVar('payment_method') == Blogger::PAYMENT_METHOD['tax_excluded_with_invoice'] ? 1 : getInfuencerPriceRate('inner_tax_included', $objBlogger->getVar('payment_method')); ?>));
	}

	function PriceProfitIncluded(price)
	{
		return isNaN(price) ? (price ? price : 0) : ('<i class="fa fa-usd" profit-included></i>'+ NumberFormat(price * <?= getInfuencerPriceRate('outer_tax_included', $objBlogger->getVar('payment_method')); ?>));
	}

	function DisplayPrice(price, priceInvite, returnArray)
	{
		var result = new Array();

		<? if ($isGrantEdit) : ?>
			if (isNaN(priceInvite)) {
				result.push(priceInvite);
			} else if (parseFloat(priceInvite) > 0) {
				result.push('<i class="fa fa-usd" invite></i>'+ NumberFormat(priceInvite));
			} else if (parseFloat(priceInvite) == 0) {
				result.push('<i class="fa fa-usd" invite></i>'+ NumberFormat(price));
			} else {
				result.push(isNaN(priceInvite) ? priceInvite : ('<i class="fa fa-usd" invite></i>'+ NumberFormat(priceInvite)));
			}
		<? endif; ?>

		<? if ($isGrantViewAccount) : ?>
			result.push(isNaN(price) ? price : ('<i class="fa fa-usd" tax-included></i>'+ NumberFormat(price)));
		<? endif; ?>

		result.push(PriceProfitExcluded(price));
		// result.push(PriceProfitIncluded(price));

		return returnArray ? result : result.join('&nbsp;、&nbsp;');
	}

	<? if (empty($editMode)) : ?>
		var bloggerPriceTipHTML = '';
		
		<? if ($isGrantEdit) : ?>
			bloggerPriceTipHTML = '＊<b class="red-opacity">費用</b>顯示分別為「<b class="red-opacity">邀約未稅</b>&nbsp;、&nbsp;<b class="red-opacity">對內未稅</b>&nbsp;、&nbsp;<b class="red-opacity">寫手成本(未稅)</b>」';
		<? elseif ($isGrantViewAccount) : ?>
			bloggerPriceTipHTML = '＊<b class="red-opacity">費用</b>顯示分別為「<b class="red-opacity">對內未稅</b>&nbsp;、&nbsp;<b class="red-opacity">寫手成本(未稅)</b>」';
		<? endif; ?>

		$('p.blogger-price-tip').each(function() {
			$(this).html(bloggerPriceTipHTML);
		});
	<? endif; ?>

	var paymentMethod = <?= json_encode(Blogger::PAYMENT_METHOD_TEXT); ?>;
	var blogger = <?= json_encode($bloggerFields); ?>;
	var priceUnit = {
		'option-3-with-ultimate-unit': <?= json_encode(getInfluencerPriceUnit('option-3-with-ultimate-unit')); ?>,
		'option-3-with-expert-unit': <?= json_encode(getInfluencerPriceUnit('option-3-with-expert-unit')); ?>,
		'option-3-with-advance-unit': <?= json_encode(getInfluencerPriceUnit('option-3-with-advance-unit')); ?>,
		'option-3-with-professional-unit': <?= json_encode(getInfluencerPriceUnit('option-3-with-professional-unit')); ?>,
		'option-3-with-novice-unit': <?= json_encode(getInfluencerPriceUnit('option-3-with-novice-unit')); ?>,
		'option-3-with-basic-unit': <?= json_encode(getInfluencerPriceUnit('option-3-with-basic-unit')); ?>,
		'option-3-with-empty-unit': <?= json_encode(getInfluencerPriceUnit('option-3-with-empty-unit')); ?>,
		'option-2-with-basic-unit': <?= json_encode(getInfluencerPriceUnit('option-2-with-basic-unit')); ?>,
		'option-2-with-single-unit': <?= json_encode(getInfluencerPriceUnit('option-2-with-single-unit')); ?>
	};

	<? if (empty($editMode)) : ?>
		$('td[data-field="text"]').each(function() {
			try {
				var fieldId = this.id.replace('column_', '');
				
				if ($(this).data('action') == 'link') {
					$('td#'+ this.id).prepend('<a target="_blank" href="'+ blogger[fieldId].toString() +'" style="overflow-wrap: break-word; word-wrap: break-word; word-break: break-word; hyphens: auto;">'+ Htmlspecialchars(blogger[fieldId].toString()) +'</a>');
				} else {
					$('td#'+ this.id).prepend(Htmlspecialchars(blogger[fieldId].toString()));
				}
				
				delete fieldId;
			} catch (e) {

			}
		});

		$('td[data-field="textarea"]').each(function() {
			try {
				var fieldId = this.id.replace('column_', '');
				$('td#'+ this.id).prepend(Htmlspecialchars(blogger[fieldId].toString()).replace(/\n/g, '<br/>'));
				delete fieldId;
			} catch (e) {

			}
		});

		$('td[data-field="invite-quota"]').each(function() {
			try {
				var fieldId = this.id.replace('column_', '');
				var idxInvite = fieldId.replace('_price', '_invite_price');

				if (blogger[fieldId] != '0' || blogger[idxInvite] != '0') {
					$('td#'+ this.id).prepend(DisplayPrice(blogger[fieldId] == -1 || blogger[fieldId] == '-1' ? 'Free' : blogger[fieldId], blogger[idxInvite] == -1 || blogger[idxInvite] == '-1' ? 'Free' : blogger[idxInvite]));
				}
				delete fieldId, idxInvite;
			} catch (e) {

			}
		});

		$('td[data-field="unit-quota-with-invite"]').each(function() {
			try {
				var fieldId = this.id.replace('column_', '');
				var unit = blogger[fieldId];
				var unitPrice = blogger[fieldId.replace('_unit', '_price')];
				var unitInviteUnit = blogger[fieldId.replace('_unit', '_invite_unit')];
				var unitInvitePrice = blogger[fieldId.replace('_unit', '_invite_price')];
				
				if (unit != '' || unitInviteUnit != '') {
					var priceArray = DisplayPrice(unitPrice, unitInvitePrice, true);

					var idxStart = 0;
					if (priceArray.length == 3) {
						idxStart = 1;

						if (unitInviteUnit == 'N') {
							priceArray[0] = priceUnit[$(this).data('unit')]['option'][unitInviteUnit];
						} else if (unitInviteUnit == 'Y') {
							priceArray[0] = priceUnit[$(this).data('unit')]['option'][unitInviteUnit];
						} else {
							priceArray[0] += '/'+ (unitInviteUnit.toString().length ? unitInviteUnit : unit);
						}
					}
					
					if (unit) {
						for (var idx=idxStart; idx<priceArray.length; idx++) {
							if (unit == 'N') {
								priceArray[idx] = priceUnit[$(this).data('unit')]['option'][unit];
							} else if (unit == 'Y') {
								priceArray[idx] = priceUnit[$(this).data('unit')]['option'][unit];
							} else {
								priceArray[idx] += '/'+ unit;
							}
						}
					}
					
					$('td#'+ this.id).prepend(priceArray.join('&nbsp;、&nbsp;'));
				}
				
				delete fieldId, unitPrice, unitInviteUnit, unitInvitePrice;
			} catch (e) {

			}
		});

		$('td[data-field="unit-quota"]').each(function() {
			try {
				var fieldId = this.id.replace('column_', '');
				DisplayUnitQuotaValue($('td#'+ this.id).data('unit'), fieldId);
				delete fieldId;
			} catch (e) {

			}
		});

		<? if (array_key_exists($objBlogger->getVar('payment_method'), Blogger::PAYMENT_METHOD_TEXT)) : ?>
			$('td#column_payment_method').prepend('<?= Blogger::PAYMENT_METHOD_TEXT[$objBlogger->getVar('payment_method')]; ?>');
		<? endif; ?>
	<? else : ?>
		$('td[data-field="text"]').each(function() {
			try {
				var fieldId = this.id.replace('column_', '');
				$('td#'+ this.id).prepend('<input type="text" name="'+ fieldId +'" id="'+ fieldId +'" value="'+ Htmlspecialchars(blogger[fieldId].toString()) +'" />');
				delete fieldId;
			} catch (e) {

			}
		});

		$('td[data-field="textarea"]').each(function() {
			try {
				var fieldId = this.id.replace('column_', '');
				$('td#'+ this.id).prepend('<textarea name="'+ fieldId +'" id="'+ fieldId +'">'+ Htmlspecialchars(blogger[fieldId].toString()) +'</textarea>');
				delete fieldId;
			} catch (e) {

			}
		});

		$('td[data-field="invite-quota"]').each(function() {
			try {
				var fieldId = this.id.replace('column_', '');
				var idxInvite = fieldId.replace('_price', '_invite_price');

				var priceHTML = [
					'<div class="row-fluid">',
						'<div class="span6 pull-left">',
							'邀&nbsp;&nbsp;約<br/><input class="influencer-price" type="text" name="'+ idxInvite +'" id="'+ idxInvite +'" value="'+ (blogger[idxInvite] == -1 || blogger[idxInvite] == '-1' ? 'Free' : Htmlspecialchars(blogger[idxInvite].toString())) +'" />',
						'</div>',
						'<div class="span6 pull-left">',
							'報&nbsp;&nbsp;價<input class="influencer-price" type="text" name="'+ fieldId +'" id="'+ fieldId +'" value="'+ (blogger[fieldId] == -1 || blogger[fieldId] == '-1' ? 'Free' : Htmlspecialchars(blogger[fieldId].toString())) +'" />',
						'</div>',
					'</div>'
				];

				$('td#'+ this.id).prepend(priceHTML.join(''));
				delete fieldId, idxInvite, priceHTML;
			} catch (e) {

			}
		});

		$('td[data-field="unit-quota-with-invite"]').each(function() {
			var unitType = $(this).data('unit');
			var unitId = this.id.replace('column_', '');
			var unitInviteId = unitId.replace('_unit', '_invite_unit');
			var idxUnitPrice = unitId.replace('_unit', '_price');
			var idxUnitInvitePrice = unitId.replace('_unit', '_invite_price');

			var selectorUnit = new Array();
			var selectorInviteUnit = new Array();
			var width = 'full1';

			if (unitType == 'option-3-with-basic-unit' || unitType == 'option-3-with-novice-unit' || unitType == 'option-3-with-professional-unit' || unitType == 'option-3-with-advance-unit' || unitType == 'option-3-with-expert-unit' || unitType == 'option-3-with-ultimate-unit') {
				for (var idxBasicUnit in priceUnit[unitType].unit) {
					selectorUnit.push('<option value="'+ priceUnit[unitType].unit[idxBasicUnit] +'">'+ priceUnit[unitType].unit[idxBasicUnit] +'</option>');
					selectorInviteUnit.push('<option value="'+ priceUnit[unitType].unit[idxBasicUnit] +'">'+ priceUnit[unitType].unit[idxBasicUnit] +'</option>');
				}
			} else if (unitType == 'option-3-with-empty-unit') {
				width = '1half1';
			} else {
				return;
			}
			
			var selectorInviteUnitHTML = new Array();
			if (selectorInviteUnit.length) {
				selectorInviteUnitHTML = [
					'<div class="span3" style="display: none;">',
						'<select name="'+ unitInviteId +'" id="'+ unitInviteId +'" style="width: 95%;">',
							selectorInviteUnit.join(''),
						'</select>',
					'</div>',
				];
			}

			var selectorUnitHTML = new Array();
			if (selectorUnit.length) {
				selectorUnitHTML = [
					'<div class="span3" style="display: none;">',
						'<select name="'+ unitId +'" id="'+ unitId +'" style="width: 95%;">',
							selectorUnit.join(''),
						'</select>',
					'</div>',
				];
			}


			var unitHTML = [
				'<div class="row-fluid">',
					'<div class="span6 pull-left">',
						'邀&nbsp;&nbsp;約<br/>', 
						'<div class="row-fluid" style="text-align: left;">',
							'<div class="span12" data-width="'+ width +'">',
								'<select name="'+ unitInviteId +'_check" id="'+ unitInviteId +'_check" onchange="ChangeUnit(this);" data-detail-text="'+ priceUnit[unitType]['option']['Y+'] +'" style="width: 95%;">',
									'<option value=""> --- </option>',
									'<option value="N">'+ priceUnit[unitType]['option']['N'] +'</option>',
									'<option value="Y">'+ priceUnit[unitType]['option']['Y'] +'</option>',
									'<option value="Y+">'+ priceUnit[unitType]['option']['Y+'] +'</option>',
								'</select>',
							'</div>',
							'<div class="span6" style="display: none; margin-left: 0px;">',
								'<input type="text" class="influencer-price" name="'+ idxUnitInvitePrice +'" id="'+ idxUnitInvitePrice +'" value="'+ (blogger[idxUnitInvitePrice] == -1 ? 'Free' : blogger[idxUnitInvitePrice]) +'" style="width: 90%;" />',
							'</div>',
							selectorInviteUnitHTML.join(''),
						'</div>',
					'</div>',
					'<div class="span6 pull-left">',
						'報&nbsp;&nbsp;價', 
						'<div class="row-fluid">',
							'<div class="span12" data-width="'+ width +'">',
								'<select name="'+ unitId +'_check" id="'+ unitId +'_check" onchange="ChangeUnit(this);" data-detail-text="'+ priceUnit[unitType]['option']['Y+'] +'" style="width: 95%;">',
									'<option value=""> --- </option>',
									'<option value="N">'+ priceUnit[unitType]['option']['N'] +'</option>',
									'<option value="Y">'+ priceUnit[unitType]['option']['Y'] +'</option>',
									'<option value="Y+">'+ priceUnit[unitType]['option']['Y+'] +'</option>',
								'</select>',
							'</div>',
							'<div class="span6" style="display: none; margin-left: 0px;">',
								'<input type="text" class="influencer-price" name="'+ idxUnitPrice +'" id="'+ idxUnitPrice +'" value="'+ (blogger[idxUnitPrice] == -1 ? 'Free' : blogger[idxUnitPrice]) +'" style="width: 90%;" />',
							'</div>',
							selectorUnitHTML.join(''),
						'</div>',
					'</div>',
				'</div>'
			];


			$('td#column_'+ unitId).prepend(unitHTML.join(''));

			DisplayUnitQuotaWithInviteValue(unitType, unitId);
			delete unitHTML;
		});

		$('td[data-field="unit-quota"]').each(function() {
			try {
				var unitType = $(this).data('unit');

				var fieldId = this.id.replace('column_', '');
				var idxUnitPrice = fieldId.replace('_unit', '_price');
				
				switch ($('td#'+ this.id).data('unit')) {
					case 'option-3-with-professional-unit':
					case 'option-3-with-basic-unit':
						if (idxUnitPrice in blogger) {
							var unitHTML = [
								'<div class="row-fluid">',
									'<div class="span12">',
										'<select name="'+ fieldId +'_check" id="'+ fieldId +'_check" onchange="ChangeUnit(this);" data-detail-text="'+ priceUnit[$('td#'+ this.id).data('unit')]['option']['Y+'] +'">',
											'<option value=""> --- </option>',
											'<option value="N">'+ priceUnit[$('td#'+ this.id).data('unit')]['option']['N'] +'</option>',
											'<option value="Y">'+ priceUnit[$('td#'+ this.id).data('unit')]['option']['Y'] +'</option>',
											'<option value="Y+">'+ priceUnit[$('td#'+ this.id).data('unit')]['option']['Y+'] +'</option>',
										'</select>',
									'</div>',
									'<div class="span6" style="display: none;">',
										'<input type="text" class="influencer-price" name="'+ idxUnitPrice +'" id="'+ idxUnitPrice +'" value="'+ (blogger[idxUnitPrice] == -1 ? 'Free' : blogger[idxUnitPrice]) +'" />',
									'</div>',
									'<div class="span3 unit-price-detail" style="display: none;">',
										'<select name="'+ fieldId +'" id="'+ fieldId +'">',
							];

							for (var idxBasicUnit in priceUnit[$('td#'+ this.id).data('unit')].unit) {
								unitHTML.push('<option value="'+ priceUnit[$('td#'+ this.id).data('unit')].unit[idxBasicUnit] +'">'+ priceUnit[$('td#'+ this.id).data('unit')].unit[idxBasicUnit] +'</option>');
							}

							unitHTML.push('</select></div></div>');

							$('td#column_'+ fieldId).prepend(unitHTML.join(''));
							delete unitHTML;
						}
						
						break;
					case 'option-3-with-empty-unit':
						if (idxUnitPrice in blogger) {
							var unitHTML = [
								'<div class="row-fluid">',
									'<div class="span12">',
										'<select name="'+ fieldId +'_check" id="'+ fieldId +'_check" onchange="ChangeUnit(this);" data-detail-text="'+ priceUnit['option-3-with-empty-unit']['option']['Y+'] +'">',
											'<option value=""> --- </option>',
											'<option value="N">'+ priceUnit['option-3-with-empty-unit']['option']['N'] +'</option>',
											'<option value="Y+">'+ priceUnit['option-3-with-empty-unit']['option']['Y+'] +'</option>',
										'</select>',
									'</div>',
									'<div class="span6" style="display: none;">',
										'<input type="text" class="influencer-price" name="'+ idxUnitPrice +'" id="'+ idxUnitPrice +'" value="'+ blogger[idxUnitPrice] +'" />',
									'</div>',
									'<div class="span3 unit-price-detail" style="display: none;">',
									'</div>',
								'</div>'
							];
							
							$('td#column_'+ fieldId).prepend(unitHTML.join(''));
							delete unitHTML;
						}

						break;
					case 'option-2-with-basic-unit':
						if (idxUnitPrice in blogger) {
							var unitHTML = [
								'<div class="row-fluid">',
									'<div class="span12">',
										'<select name="'+ fieldId +'_check" id="'+ fieldId +'_check" onchange="ChangeUnit(this);" data-detail-text="'+ priceUnit['option-2-with-basic-unit']['option']['Y+'] +'">',
											'<option value=""> --- </option>',
											'<option value="N">'+ priceUnit['option-2-with-basic-unit']['option']['N'] +'</option>',
											'<option value="Y+">'+ priceUnit['option-2-with-basic-unit']['option']['Y+'] +'</option>',
										'</select>',
									'</div>',
									'<div class="span6" style="display: none;">',
										'<input type="text" class="influencer-price" name="'+ idxUnitPrice +'" id="'+ idxUnitPrice +'" value="'+ blogger[idxUnitPrice] +'" />',
									'</div>',
									'<div class="span3 unit-price-detail" style="display: none;">',
										'<select name="'+ fieldId +'" id="'+ fieldId +'">',
							];

							for (var idxBasicUnit in priceUnit['option-2-with-basic-unit'].unit) {
								unitHTML.push('<option value="'+ priceUnit['option-2-with-basic-unit'].unit[idxBasicUnit] +'">'+ priceUnit['option-2-with-basic-unit'].unit[idxBasicUnit] +'</option>');
							}

							unitHTML.push('</select></div></div>');

							$('td#column_'+ fieldId).prepend(unitHTML.join(''));
							delete unitHTML;
						}

						break;
					case 'option-2-with-single-unit':
						if (idxUnitPrice in blogger) {
							var unitHTML = [
								'<div class="row-fluid">',
									'<div class="span12">',
										'<select name="'+ fieldId +'_check" id="'+ fieldId +'_check" onchange="ChangeUnit(this);" data-detail-text="'+ priceUnit['option-2-with-single-unit']['option']['Y+'] +'">',
											'<option value=""> --- </option>',
											'<option value="N">'+ priceUnit['option-2-with-single-unit']['option']['N'] +'</option>',
											'<option value="Y+">'+ priceUnit['option-2-with-single-unit']['option']['Y+'] +'</option>',
										'</select>',
									'</div>',
									'<div class="span6" style="display: none;">',
										'<input type="text" class="influencer-price" name="'+ idxUnitPrice +'" id="'+ idxUnitPrice +'" value="'+ blogger[idxUnitPrice] +'" />',
									'</div>',
									'<div class="span3 unit-price-detail" style="display: none;">',
										'<select name="'+ fieldId +'" id="'+ fieldId +'">',
							];

							for (var idxBasicUnit in priceUnit['option-2-with-single-unit'].unit) {
								unitHTML.push('<option value="'+ priceUnit['option-2-with-single-unit'].unit[idxBasicUnit] +'">'+ priceUnit['option-2-with-single-unit'].unit[idxBasicUnit] +'</option>');
							}

							unitHTML.push('</select></div></div>');

							$('td#column_'+ fieldId).prepend(unitHTML.join(''));
							delete unitHTML;
						}

						break;
					default:
						return;
				}

				DisplayUnitQuotaValue($(this).data('unit'), fieldId);
				delete fieldId, idxUnitPrice;
			} catch (e) {

			}
		});

		$('select#payment_method').val('<?= $objBlogger->getVar('payment_method'); ?>');
		$('select#payment_ticket').val('<?= $objBlogger->getVar('payment_ticket'); ?>');
	<? endif; ?>

	for (var idxHistory in blogger.history) {
		if ($('#column_'+ idxHistory +'[data-field="invite-quota"], #column_'+ idxHistory.replace('_invite_price', '_price') +'[data-field="invite-quota"]').length) {
			var actualId = idxHistory.indexOf('_invite_price') > 0 ? idxHistory.replace('_invite_price', '_price') : idxHistory;

			<? if (empty($editMode)) : ?>
				<? if ($isGrantEdit) : ?>
					$('#column_'+ actualId +'[data-field="invite-quota"]').append(DisplayModification(blogger.history[idxHistory]['modified'], blogger.history[idxHistory]['modifier'], (idxHistory.indexOf('_invite_price') > 0 ? '邀約' : '報價')));
				<? else : ?>
					if (idxHistory.indexOf('_invite_price') == -1) {
						$('#column_'+ actualId +'[data-field="invite-quota"]').append(DisplayModification(blogger.history[idxHistory]['modified'], blogger.history[idxHistory]['modifier']));
					}
				<? endif; ?>
			<? else : ?>
				$('#column_'+ actualId +'[data-field="invite-quota"]').find('div.span6:nth-child('+ (idxHistory.indexOf('_invite_price') > 0 ? 1 : 2) +')').append(DisplayModification(blogger.history[idxHistory]['modified'], blogger.history[idxHistory]['modifier']));
			<? endif; ?>

			delete actualId;
		} else if ($('#column_'+ idxHistory +'[data-field="unit-quota-with-invite"], #column_'+ idxHistory.replace('_invite_unit', '_unit') +'[data-field="unit-quota-with-invite"]').length) {
			var actualId = idxHistory.indexOf('_invite_unit') > 0 ? idxHistory.replace('_invite_unit', '_unit') : idxHistory;
			
			<? if (empty($editMode)) : ?>
				<? if ($isGrantEdit) : ?>
					$('#column_'+ actualId +'[data-field="unit-quota-with-invite"]').append(DisplayModification(blogger.history[idxHistory]['modified'], blogger.history[idxHistory]['modifier'], (idxHistory.indexOf('_invite_price') > 0 ? '邀約' : '報價')));
				<? else : ?>
					if (idxHistory.indexOf('_invite_unit') == -1) {
						$('#column_'+ actualId +'[data-field="unit-quota-with-invite"]').append(DisplayModification(blogger.history[idxHistory]['modified'], blogger.history[idxHistory]['modifier']));
					}
				<? endif; ?>
			<? else : ?>
				$('#column_'+ actualId +'[data-field="unit-quota-with-invite"]').children(':first-child').children(':nth-child('+ (idxHistory.indexOf('_invite_unit') > 0 ? 1 : 2) +')').append(DisplayModification(blogger.history[idxHistory]['modified'], blogger.history[idxHistory]['modifier']));
			<? endif; ?>

			delete actualId;
		} else if ($('#column_'+ idxHistory +':not([data-field*="invite"]):not([data-field="unit-quota-with-invite"])').length) {
			$('#column_'+ idxHistory +':not([data-field*="invite"])').append(DisplayModification(blogger.history[idxHistory]['modified'], blogger.history[idxHistory]['modifier']));
		}
	}

	function ValidateForm()
	{
		var success = true;
		
		if ($('input[name="display_name"]').val() == '' && $('input[name="blog_name"]').val() == '' && $('input[name="fb_name"]').val() == '' && $('input[name="ig_name"]').val() == '' && $('input[name="youtube_name"]').val() == '') {
			success = false;
			$('input[name="display_name"]').focus();
			alert('名稱不可空白');
		}

		if (success && $('input[name="ac_id"]').val() != '') {
			if (!$('input[name="ac_id"]').val().toString().match(/^[0-9]{2}\-[0-9]{3}$/)) {
				success = false;
				$('input[name="ac_id"]').focus();
				alert('帳戶ID錯誤');
			}
		}

		return success;
	}
</script>
