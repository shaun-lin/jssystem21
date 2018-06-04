<?php
	
	// 2018-03-14 (Jimmy): 傑思jsadways2/media_edit.php, 香港jsadways2hk/media_edit.php, 豐富媒體jsadways2ff/media_edit.php 共用此檔案
	require_once dirname(__DIR__) .'/autoload.php';

	include('include/db.inc.php');

	$objSizeformat = CreateObject('Items', GetVar('id'));
	$objmtype = CreateObject('Mtype');
	$objRelItemsType=CreateObject('RelItemsType');
	$arrRelItemsType=array();
	if(IsId($objSizeformat->getId())){
	foreach($objRelItemsType->searchAll('`items_id`='.GetVar('id')) as $RelItemsType){
			$arrRelItemsType[]=$RelItemsType['type_id'];
			
	}
	echo $RelItemsType['type_id'];
	}
	if ((!IsPermitted('superuser') && !in_array($_SESSION['departmentid'], [21, 22]))) {
		RedirectLink('item_list.php');

	}


?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>【<?= $GLOBALS['env']['flag']['name']; ?>】品項</title>
		<?php include("public/head.php"); ?>
	</head>
	<body>
		<?php include("public/topbar.php"); ?>
		
		<div class="container-fluid">
			<div class="row-fluid">
				<?php include("public/left.php"); ?>
				
				<div id="content" class="span10">
					<form class="form-horizontal" action="item_edit2.php" method="POST">
						<div class="row-fluid">	
							<div class="box span7">
								<div class="box-header well" data-original-title>
									<h2><i class="fa fa-cube"></i>&nbsp;品項 - <?= $objSizeformat->getVar('item_name'); ?></h2>
								</div>


								<div class="box-content">
									<? if (IsId($objSizeformat->getId())){?>
									<table class="table table-bordered table-striped">
										<tr>
											<td><h4>最近更新</h4></td>
											<td>於 <b>
											<?php if(!empty($objSizeformat->getVar('time'))==1)
											{echo date('Y-m-d H:i',$objSizeformat->getVar('time')); 
											?></b> 由 <b>
											<?= $objSizeformat->getVar('creator'); }
											?></b> 更新</td>
										</tr>
										<tr>
											<td><h4>品項名稱</h4></td>
											<td style="text-align:left;"><input type="text" style="font-size:24px;height:30px;width:40%;margin-left: 20%;" name="name" value="<? echo str_replace("\\n", "\n", $objSizeformat->getVar('name'));?>"></td>
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
									<table class="table table-striped table-bordered bootstrap-datatable  ">
									<thead>
										<tr>
											<td><h4>模板設定</h4></td>
										</tr>
										<tr>
											<th>選擇</th>
											<th>模板名稱</th>
											<th>模板</th>
										</tr>
									</thead>
									<tbody>
                                    <?php
									$sqlselect="SELECT (case when r.items_id is null then '' else 'Y' end) as checkp
									,mtype.name
									,(case when r.dashboard is null then mtype.dashboard else r.dashboard end) as dashboard
									,r.items_id
									,mtype.id as type_id 
									FROM mtype 
									left join `rel_items_type` r on mtype.id = r.type_id and r.items_id ='".GetVar('id')."' 
									where mtype.display='1'
									order by mtype.name";
									$dsType=mysql_query($sqlselect);
									$ints = 0;
									while($dr = mysql_fetch_array($dsType)){  ?>
									
                                    <tr>
										<td><input class="checkbox" type="checkbox" name=mtype[] 
											<?php 
											echo 'value="'.$dr['type_id'].'_'. $ints .'" ';
											$ints++;
											if($dr['checkp']=='Y'){
												echo 'checked="checked"';
											}?> 
											>
										<td><? echo $dr['name'] ?></td>
										<td><input class="text" type="text" style="font-size:20px;height:24px;width:40%;margin-left: 15%;" name=dashboard[]
										value="<? echo $dr['dashboard'] ?>">
                                    </tr>
                                    
                                    <?}?>
                                    </tbody>
                                </table>         
									<?}else if (!IsId($objSizeformat->getId())){?>
										<table class="table table-bordered table-striped">
										<tr>
											<td><h4>媒體名稱</h4></td>
											<td style="text-align:left;"><input type="text"style="font-size:24px;height:30px;width:40%;margin-left: 15%;" name="name"></td>
										</tr>
										<tr>
											<td><h4>是否顯示</h4></td>
											<td>
											<label class="radio-inline control-label"> 
												<input type="radio" name="display" value="1" checked="checked"> 顯示<br>
											</label>
											<label class="radio-inline control-label">
  												<input type="radio" name="display" value="0"> 不顯示<br>
  											</label>
  											</td>
										</tr>
										</tr>
									</table>
										<? } ?>
										
									<div class="form-actions">
										<input name="id" type="hidden" value="<?= $objSizeformat->getId(); ?>" />
										<input name="mediaid" type="hidden" value="<?= $objSizeformat->getVar('Item'); ?>" />
										<button type="submit" class="btn btn-primary">確定修改</button>
										<a href="item_list.php" target="_self" class="btn btn-primary">取消離開</a>
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
	</body>
</html>