<?php 
	
	session_start();
	include('include/db.inc.php');

	$campaignOwner = $_SESSION['userid'];
	
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

							<div class="box-content" style="text-align: center;" id="loader">
								<i class="fa fa-spin fa-circle-o-notch" style="font-size: 3em; padding: 20px;"></i><br/>Loading...
							</div>
							<div class="box-content">
								<table class="table table-striped table-bordered bootstrap-datatable datatable">
									<thead>
										<tr>
											<th>活動名稱</th>
											<th>代理商</th>
											<th>廣告主</th>
											<th>期間</th>
											<th>建立日期</th>
											<th>總預算</th>
											<th>發票</th>
											<th>狀態</th>
											<th>回簽</th>
											<th>Actions</th>
										</tr>
									</thead>   
									<tbody>
										<?php
											if (empty($_GET['status'])) {
												$sql2 = 'SELECT * FROM campaign WHERE memberid IN ('. $campaignOwner .') AND status <> 8 AND version = 2';
												$sql2_sub = 'SELECT id FROM campaign WHERE memberid IN ('. $campaignOwner .') AND status <> 8 AND version = 2';
											}else{
												$sql2 = 'SELECT * FROM campaign WHERE memberid IN ('. $campaignOwner .') AND status = '. $_GET['status'] .' AND version = 2';
												$sql2_sub = 'SELECT id FROM campaign WHERE memberid IN ('. $campaignOwner .') AND status = '. $_GET['status'] .' AND version = 2';
											}

											$totalprice1 = [];
											$rowsOrdinal = GetUsedMediaOrdinal($sql2_sub, 'sql');
											foreach ($rowsOrdinal as $mediaId) {
												$sql3 = 'SELECT `campaign_id`, SUM(`totalprice`) AS `totalprice` FROM `media'. $mediaId .'` WHERE `campaign_id` IN ('. $sql2_sub .') AND `cue` = 1 GROUP BY `campaign_id`;';
													
												$result3 = mysql_query($sql3); 
												if (mysql_num_rows($result3) > 0) {
													while ($row3 = mysql_fetch_array($result3)) {
														if (!isset($totalprice1[$row3['campaign_id']])) {
															$totalprice1[$row3['campaign_id']] = 0;
														}

														$totalprice1[$row3['campaign_id']] = $totalprice1[$row3['campaign_id']] + $row3['totalprice'];
													}
												}
											}

											$result2=mysql_query($sql2); 
											if (mysql_num_rows($result2)>0){
												while($row2=mysql_fetch_array($result2)){
										?>
										<tr>
											<td><?= $row2['name']; ?>【<?= $row2['version']==2 ? '2.0' : '1.0'; ?>】</td>
											<td class="center"><?= empty($row2['agency']) ? '直客' : $row2['agency']; ?></td>
											<td class="center"><?= $row2['client']; ?></td>
											<td class="center"><?= $row2['date1']; ?>~<?= $row2['date2']; ?></td>
											<td class="center"><?= date('Y-m-d',$row2['time']); ?></td>
											<td class="center">$<?= isset($totalprice1[$row2['id']]) ? number_format($totalprice1[$row2['id']]) : 0; ?></td>
											<td class="center">
											<?php
												$sql3_new = 'SELECT r.receipt_number, r.receipt3id, r.totalprice1, r3.times1, r3.times2 FROM `receipt` r 
															LEFT JOIN receipt3 r3 ON r3.id = r.receipt3id
															WHERE r.campaign_id = '.$row2['id'].' AND r.status < 2';
												
												$result3 = mysql_query($sql3_new); 
												if (mysql_num_rows($result3)>0){
													while($row3=mysql_fetch_array($result3)){
														if($row3['receipt_number']!=NULL){
															if($row3['receipt3id']=="0"){
																$receipt3id='未收款';
															}else{
																if($row3['times1']==0){
																	if($row3['times2']==0){
																		$receipt3id='未收款';
																	}else{
																		$receipt3id='<font color="#00FF00">已收到支票</font>';
																	}
																}else{
																	$receipt3id='<font color="#FF0000">已收款</font>';
																}
															}
															if($row3['receipt_number']=="0"){
																$receipt_number='未開發票';
															}else{
																$receipt_number=$row3['receipt_number'];
															}
															echo  $receipt_number.'('.$receipt3id.')'.$row3['totalprice1'].'<br />';
														}
													}
												}
											?>
											</td>
											<td class="center">
												<?php 
													switch ($row2['status']){
														case 1:
															echo '<span class="label label-warning">尚未送審</span>';
															break;
														case 2:
															echo '<span class="label label-info">送審中</span>';
															break;
														case 3:
															echo '<span class="label label-success">執行中</span>';
															break;
														case 4:
															echo '<span class="label label-important">已結案</span>';
															break;
														case 5:
															echo '<span class="label">暫停</span>';
															break;
														case 6:
															echo '<span class="label label-inverse">中止</span>';
															break;
														case 7:
															echo '<span class="label label-inverse">異常</span>';
															break;
													}
												?>
											</td>
											<td><?= ($row2['action1'] >= 1 ? '已回簽' : ''); ?></td>
											<td class="center">
												<a class="btn btn-success" href="campaign_view.php?id=<?=  $row2['id']; ?>">
													<i class="icon-zoom-in icon-white"></i>  
													View                                            
												</a>
											</td>
										</tr>
										<?php
												}
											}
										?>
									</tbody>
								</table>            
							</div>
						</div>
					</div>
				</div>
			</div>	
			<hr/>

			<?php include("public/footer.php"); ?>
		</div>

		<?php include("public/js.php"); ?>
	</body>
</html>
