<?php
	
	// 2018-03-14 (Jimmy): 傑思jsadways2/media_edit.php, 香港jsadways2hk/media_edit.php, 豐富媒體jsadways2ff/media_edit.php 共用此檔案
	require_once dirname(__DIR__) .'/autoload.php';

	$objSizeformat = CreateObject('Medias', GetVar('id'));
	$objCompanies = CreateObject('Companies');
	$objRelMediaCompanies=CreateObject('RelMediaCompanies');
	$arrRelMediaCompanies=array();
	if(IsId($objSizeformat->getId())){
	foreach($objRelMediaCompanies->searchAll('`medias_id`='.GetVar('id')) as $itemRelMediaCompanies){
			$arrRelMediaCompanies[]=$itemRelMediaCompanies['companies_id'];
		}
	}
	if ((!IsPermitted('superuser') && !in_array($_SESSION['departmentid'], [21, 22]))) {
		RedirectLink('medias_list.php');
	
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>【<?= $GLOBALS['env']['flag']['name']; ?>】媒體維護作業</title>
		<?php include("public/head.php");
			  include("include/db.inc.php"); ?>
	</head>
	<body>
		<?php include("public/topbar.php"); ?>
		
		<div class="container-fluid">
			<div class="row-fluid">
				<?php include("public/left.php"); ?>
				
				<div id="content" class="span10">
					<form class="form-horizontal" action="medias_edit2.php" method="post">
						<div class="row-fluid">	
							<div class="box span7 autogrow">
								<div class="box-header well" data-original-title>
									<h2><i class="fa fa-cube"></i>&nbsp;媒體 - <?= $objSizeformat->getVar('name'); ?></h2>
								</div>
								<!--編輯-->
								<div class="box-content autogrow">
									<? if (IsId($objSizeformat->getId())){?>
									<table class="table table-bordered table-striped">
										<tr>
											<td><h4>最近更新</h4></td>
											<td>
											<?php if(!empty($objSizeformat->getVar('time'))==1)
											{echo "於 <b>".date('Y-m-d H:i',$objSizeformat->getVar('time')); }
											?></b> 由 <b>
											<?=$objSizeformat->getVar('creator'); 
											?></b> 更新</td>
										</tr>
										<tr>
											<td><h4>媒體名稱</h4></td>
											<td style="text-align:left;"><input type="text" style="font-size:24px;height:30px;width:40%;margin-left: 20%;" name="name" value="<? echo str_replace("\\n", "\n", $objSizeformat->getVar('name'));?>"></td>
										</tr>
										<tr>
											<td><h5>媒體簡稱</th></td>
											<td style="text-align:left;"><input type="text" style="font-size:24px;height:30px;width:40%;margin-left: 20%;" name="crop" maxlength="10" value="<? echo str_replace("\\n","\n", $objSizeformat->getVar('crop'));?>"></td>
										</tr>
										<tr>
											<td><h4>是否顯示</h4></td>
											<td>
												<label class="radio-inline control-label">
												<input class="radio" type="radio" name="display" value="1" <?php if (str_replace("\\n", "\n", $objSizeformat->getVar('display'))=="1"):?>checked="checked"<?endif;?>> 顯示
											</label>
											<label class="radio-inline control-label">
  												<input class="radio" type="radio" name="display" value="0" <?php if (str_replace("\\n", "\n", $objSizeformat->getVar('display'))=="0"):?>checked="checked"<?endif;?>> 不顯示
  												</label>
											</td>
										</tr>
									</table>
									<div>
									<?php
										$sqlClass2 = "SELECT id,name FROM `items` where display='1' order by name ";
										$resultClass2 = mysql_query($sqlClass2);
										while ($itemClass2 = mysql_fetch_array($resultClass2)) {
											$rowsClass2[] = $itemClass2;
										}
										$sqlClass = "SELECT `item_id` AS ids FROM rel_media_item WHERE  `media_id` =" . GetVar('id') ;
											$resultClass = mysql_query($sqlClass);
											while ($itemClass = mysql_fetch_array($resultClass)) {
												$rowsClass[] = $itemClass['ids'];
										}?>
                                        <select id="selectError2" multiple data-rel="chosen" name="tagtext2[]" style = "width : 100%;" >
                                        	<? foreach ($rowsClass2 as $row6) : ?>
                                              <? 
												foreach ($rowsClass as $row7) { 
												if ($row6['id'] === $row7){$Selecteds = 'selected';}
												}?>
                                         		<option value="<?= $row6['id']; ?>" <?= $Selecteds ?>><?= $row6['name']; ?></option>
                                         	<?= $Selecteds=''; ?>
                                         <? endforeach; ?>
                                         </select>
									</div>
									<!--  新增  -->
									<?}else if (!IsId($objSizeformat->getId())){?>
										<table class="table table-bordered table-striped">
										<tr>
											<td><h4>媒體名稱</h4></td>
											<td style="text-align:left;"><input type="text" style="font-size:24px;height:30px;width:40%;margin-left: 20%;" name="name"></td>
										</tr>
										<tr>
											<td><h5>媒體簡稱</th></td>
											<td style="text-align:left;"><input type="text" style="font-size:24px;height:30px;width:40%;margin-left: 20%;" name="crop" maxlength="10" value="<? echo str_replace("\\n","\n", $objSizeformat->getVar('crop'));?>"></td>
										</tr>
										<tr>
											<td><h4>是否顯示</h4></td>
											<td style="text-align: left;">
											<label class="radio-inline control-label"> 
												<input type="radio" name="display" value="1" checked="checked"> 顯示<br />
											</label>
											<label class="radio-inline control-label">
  												<input type="radio" name="display" value="0"> 不顯示<br />
  											</label>
  											</td>
										</tr>
										<!-- <tr>
											<td colspan=2><center><h4>所屬公司</h4></center></td>
											<table class="table table-bordered table-striped">											
											<?php 
												$arrCompanies=$objCompanies->searchAll();
												$arrCount=count($arrCompanies);
												$tdcount=floor($arrCount/10);
												for($k=0;$k<$tdcount+1;$k++){
													echo "<td style='text-align: left;'>";
													for($i=($k*10)+1;$i<($k+1)*10;$i++){
															if(!empty($arrCompanies[$i]['id'])){
															echo "<input class='checkbox' type='checkbox' name='companies[]' value='";
															echo $arrCompanies[$i]['id'];
															echo "'";
																if(in_array($arrCompanies[$i]['id'],$arrRelMediaCompanies)){
																echo "checked='checked'  />";
																echo $arrCompanies[$i]['name'];
															}
																else{
																echo "/>";
																echo $arrCompanies[$i]['name'];
																}
																echo "<br>";
																;}
															}
													echo "</td>";
												}
											?>
											</table>
										</tr> -->
									</table>
										<?}?>

									<div class="form-actions">
										<input name="id" type="hidden" value="<?= $objSizeformat->getId(); ?>" />
										<input name="mediaid" type="hidden" value="<?= $objSizeformat->getVar('mediaid'); ?>" />
										<input name="status" type="hidden" value="
													<?
														 if (IsId($objSizeformat->getId())){
															 echo "1";
														 }
														 else if (!IsId($objSizeformat->getId())){
															 echo "2";
														 }
													?>
																				 " />
										<button type="submit" class="btn btn-primary">確定修改</button>
										<a href="medias_list.php" target="_self" class="btn btn-primary">取消離開</a>
									</div>
								</div>	
							</div>
						</div>
					</form>
				</div>
			</div>		
			<hr/>

			<?php include("public/footer.php"); ?>
		</div>

		<?php include("public/js.php"); ?>
		<script>
		function Check_medias(r, T_N){ //r：0為新增，1為修改   T_N：0為查詢聯絡資料 1為查詢公司名稱
				var item_id = document.getElementById("media_id").value;
				var Check ;
				var T_Ns = T_N;
				if (r == 0){
					Check = document.getElementById("media_name").value;
				}else if(r == 1){
					Check = document.getElementById("media_Name_Change").value;
					T_Ns = 2;
					}
				if (Check.replace(/(^s*)|(s*$)/g, "").length ==0){
					alert ("媒體名稱為必填，請輸入資料");
					return;
				}	
			}

			function Leave_Page(){
				document.location.href="medias_list.php";
			}
		</script>
	</body>
</html>