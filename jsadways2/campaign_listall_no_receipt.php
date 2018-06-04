<?php 
	session_start();
	include('include/db.inc.php');
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
						<h2><i class="icon-edit"></i> 案件列表</h2>
					</div>
					<div class="box-content" style="text-align: center;" id="loader">
                        <i class="fa fa-spin fa-circle-o-notch" style="font-size: 3em; padding: 20px;"></i><br/>Loading...
                    </div>
					<div class="box-content">
						<table class="table table-striped table-bordered bootstrap-datatable datatable" >
						  <thead>
							  <tr>
								  <th>代理商</th>
								  <th>廣告主</th>
                                  <th>活動名稱</th>
								  <th width="140px">期間</th>
								  <th width="65px">建立日期</th>
								  <th>負責業務</th>
								  <th>發票</th>
								  <th>狀態</th>
                                  <th width="80px">分類</th>
								  <th width="180px">Actions</th>
							  </tr>
						  </thead>   
						  <tbody>
						  	<?php
								if(isset($_GET['no_receipt'])){
									$sqlmedia = "SELECT * FROM media WHERE id=0";
									$resultmedia = mysql_query($sqlmedia);
									$rowmedia = mysql_fetch_array($resultmedia);
									$medianumber=$rowmedia['name'];

									$campaign_id_ary = array();	//id 列表
									$int_this_month = strtotime($_GET['year']."-".$_GET['month']."-".$_GET['day']);
									$before_3_month = strtotime($_GET['year']."-".$_GET['month']."-".$_GET['day']."-3 months");
									//echo date("Y-m-d",$before_3_month);
									//$sql2 ='SELECT ca.* FROM campaign ca LEFT JOIN receipt re ON ca.id = re.campaign_id WHERE ca.status in(3,4,5,6,8) and re.receipt_number like 0 and date22 <'.$int_this_month;
									// $sql_ca ='SELECT * FROM `campaign` WHERE `campaign`.`status` in(3,4,5,6) and `campaign`.`date22` <'.$int_this_month.' and `campaign`.`date22` >= '.$before_3_month;
                                    $condition = ' `c`.`status` in(3,4,5,6) and `c`.`date22` <'.$int_this_month.' and `c`.`date22` >= '.$before_3_month;
									if($_GET['no_receipt'] == 'all'){
										// $sql_ca ='SELECT * FROM `campaign` WHERE `campaign`.`status` in(3,4,5,6) and `campaign`.`date22` <'.$int_this_month;
                                        $condition = ' `c`.`status` in(3,4,5,6) and `c`.`date22` <'.$int_this_month;
									}

                                    $totalSql = 'SELECT COUNT(*) as `total` FROM `campaign` `c` WHERE '. $condition;

                                    $totalResult = mysql_query($totalSql);
                                    $itemResult = mysql_fetch_array($totalResult);
                                    $total = $itemResult['total'];
                                    $campaignExchange = [];
                                    
                                    if ($total) {
                                        
                                        $rowsSql = 'SELECT `c`.`id`, `c`.`exchang_math`, 
                                                    `r`.`id` as `receipt_ai_id`,
                                                    `r`.`status` as `receipt_status`,
                                                    `r`.`receipt3id`, `r`.`totalprice1`
                                                    FROM `campaign` `c` 
                                                    LEFT JOIN `receipt` `r` ON `r`.`campaign_id` = `c`.`id` 
                                                    WHERE '. $condition;
                                        
                                        $totalReceipt = [];
                                        $totalprice1 = [];
                                        $cId = [0];
                                        $rowsResult = mysql_query($rowsSql);
                                        if (mysql_num_rows($rowsResult) > 0) {
                                            while ($rowsItem = mysql_fetch_array($rowsResult)) {
                                                $cId[$rowsItem['id']] = $rowsItem['id'];
                                                $campaignExchange[$rowsItem['id']] = $rowsItem['exchang_math'];

                                                if ($rowsItem['receipt_status'] == 0 || $rowsItem['receipt_status'] == 1) {
                                                    $totalReceipt[$rowsItem['id']] += $rowsItem['totalprice1'];
                                                }
                                            }
                                        }

										$totalprice1 = [];
										$rowsOrdinal = GetUsedMediaOrdinal(array_keys($cId));
										foreach ($rowsOrdinal as $mediaId) {
                                            $sql3='SELECT * FROM media'.$mediaId.' WHERE campaign_id IN ('. implode(',', array_keys($cId)) .') AND cue=1 ORDER BY id';
                                            $result3=mysql_query($sql3);
                                            if (mysql_num_rows($result3)>0){
                                                while($row3 = mysql_fetch_array($result3)){
                                                    $totalprice1[$row3['campaign_id']] = $totalprice1[$row3['campaign_id']] + $row3['totalprice'];
                                                }
                                            }
                                        }

                                        foreach ($cId as $id) {
                                            if (isset($totalReceipt[$id])) {
                                                if (($totalprice1[$id] + $campaignExchange[$id]) != $totalReceipt[$id]) {
													$campaign_id_ary[] = $id;
												}
                                            } else {
                                                if (($totalprice1[$id] + $campaignExchange[$id]) != 0) {
													$campaign_id_ary[] = $id;
												}
                                            }
                                        }
                                    }
                                    
                                    $campaign_id_ary[] = 0;
									$sql2 = "SELECT * FROM campaign WHERE id in (". implode(',', $campaign_id_ary) .")";
									//echo $sql2;
								}

								$result2=mysql_query($sql2); 
								if (mysql_num_rows($result2)>0){
									while($row2=mysql_fetch_array($result2)){
							?>
							<tr>
								<td class="center"><?php if($row2['agency']!=NULL){echo $row2['agency'];}else{echo '直客';}  ?></td>
								<td class="center"><?php echo $row2['client']; ?></td>

                                <td><?php echo $row2['name']; ?>【<?php if($row2['version']==2){echo '2.0';}else{echo '1.0';} ?>】</td>
								<td class="center"><?php echo $row2['date1']; ?>~<?php echo $row2['date2']; ?></td>
								<td class="center"><?php echo date('Y-m-d',$row2['time']); ?></td>
								<td class="center"><span class="<?php if($row2['sex']=='男'){echo 'label label-success';} ?><?php if($row2['sex']=='女'){echo 'label label-important';} ?>"><?php echo $row2['member']; ?></span></td>
								<td class="center">
                                <?php
									$sql3='SELECT r.receipt_number, r.status, r.receipt3id, r.totalprice1, r3.times1, r3.times2 FROM receipt r 
											LEFT JOIN receipt3 r3 ON r3.id = r.receipt3id
											WHERE r.campaign_id = '.$row2['id'];
									$result3=mysql_query($sql3); 
									if (mysql_num_rows($result3)>0){
										while($row3=mysql_fetch_array($result3)){
											if($row3['receipt_number']!=NULL){
												if($row3['receipt3id']=="0"){
													$receipt3id='未收款';
												}else{
													// $sql4 = "SELECT * FROM receipt3 WHERE id= ".$row3['receipt3id'];
													// $result4 = mysql_query($sql4);
													// $row4 = mysql_fetch_array($result4);
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
												if($row3['status']==2){		//by abow
													$receipt_number=$row3['receipt_number'];
													$receipt3id='<font color="#FF0000">作廢</font>';
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
											case 8:
											 echo '<span class="label label-inverse">作廢</span>';
											break;
											case 9:
											 echo '<span class="label label-inverse">作廢待審中</span>';
											break;
										}
									?>
								</td>
                                <td class="center"><?php echo $row2['tagtext']; ?></td>
								<td class="center">
                                <?php if($row2['action1']>=1){?>
                                <form action="campaign_action.php?id=<?php echo $row2['id']; ?>" method="post">
                                <?php } ?>
									<a class="btn btn-success" href="campaign_view.php?id=<?php echo  $row2['id']; ?>">
										<i class="icon-zoom-in icon-white"></i>  
										View                                            
									</a>
                                    <?php if($row2['action1']==null && $row2['status'] != 8 && ($_SESSION['usergroup'] > 2 || $_SESSION['name'] == 'nana')){?>
									<a class="btn btn-info" href="campaign_action.php?id=<?php echo $row2['id']; ?>">
										<i class="icon-edit icon-white"></i>  
										回簽                                            
									</a>
									<?php } ?>
                                     <?php if($row2['action1']==1){?>
										已回簽                                            
									<?php } ?>
                                    <?php if($row2['action1']>=1){?>
                                    <input style="width:80px" id="text" name="text" value="<?php echo $row2['action3']; ?>">
                                    </form>
                                    <?php } ?>
								</td>
							</tr>
							<?php
									}
								}
							?>
						  </tbody>
					  </table>            

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
		
</body>
</html>
