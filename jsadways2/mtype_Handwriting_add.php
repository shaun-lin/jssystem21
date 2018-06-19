<?php
	session_start();
	include('include/db.inc.php');

?>
		<div id="content" class="span10">
			<!-- content starts -->
			<div class="row-fluid">
				<div class="box span12" id="addList">
					<div class="box-header well" data-original-title>
						<h2><i class="icon-edit"></i> 已增加寫手列表</h2>

					</div>
					<div class="box-content">
						<table class="table table-striped table-bordered">
						  <thead>
							  <tr>
								<th>寫手</th>
								<th>對外報價</th>
								<th>成本</th>
								<th>利潤</th>
								<th>Actions</th>
							  </tr>
						  </thead>
						  <tbody>
							<?php
								if (isset($_GET['campaign']) && ($_GET['campaign'] < 0 || $_GET['campaign'] !="")){
									$sql2='SELECT * FROM media162_detail WHERE campaign_id = '.$_GET['campaign'].' AND item_seq =""' ;
									$result2=mysql_query($sql2);
									$bloggerid=array();
									if (mysql_num_rows($result2)>0){
										while($row2=mysql_fetch_array($result2)){
											$totalprice=$totalprice+$row2['price'];
											$totalprice2=$totalprice2+$row2['price2'];
											$totalprice3=$totalprice3+$row2['price3'];
											array_push($bloggerid,$row2['id']);
								
							?>
							<tr>
								<td><a href="blogger_view.php?id=<?php echo $row2['id']; ?>&youtuber=1" target="_blank"><?php echo $row2['blog']; ?><?php if($row2['blog']==NULL){echo $row2['blog2'];} ?></a></td>
                                <td><?php echo $row2['price2']; ?></td>
                                <td><?php echo $row2['price']; ?></td>
                                <td><?php echo $row2['price3']; ?></td>
								<td class="center">
									<a class="btn btn-danger" data-toggle="modal" data-target="#dialog" id="PersonDel" name="PersonDel[]" href="mtype_Handwriting_detail_delete.php?campaign=<?php echo $_GET['id']; ?>&blogid=<?php echo $row2['id'];?>">
										<i class="icon-trash icon-white"></i>
										刪除
                                    </a>
								</td>
							</tr>
							<?php
								 		}
									}
								}
							?>
						  </tbody>
					  </table>
                      <?php
                      	$totalprice3=$totalprice2-$totalprice;
					  	$sql4 = "SELECT * FROM media162 WHERE id= ".$_GET['id'];
						$result4 = mysql_query($sql4);
						$row4 = mysql_fetch_array($result4);
					  ?>
					  <script>
								$(document).ready(function()
								{
									Page_Init();
								});
								function Page_Init()
								{
									//Abow Start
									<?php 
										include('campaign_required_select_edit.php');
										echo $Select_str;
									?>

									$('#SelectType').change(function(){
										//ChangeSelectType();
									});
									//Abow end
								}
						</script>
                      <div class="box-content">
						<form class="form-horizontal" action="mtype_Handwriting_save.php?id=<?php echo $_GET['id']; ?>&campaign=<?php echo $_GET['campaign']; ?>&cue=<?php echo $_GET['cue']; ?>" method="post">
                           <div class="control-group">
							  <label class="control-label" for="SelectType">類別(Type)</label>
							  <div class="controls">
								 <select id="SelectType" name="SelectType">
							    </select>
							  </div>
							</div>
							<div class="control-group">
							  <label class="control-label" for="SelectSystem">系統(System)</label>
							  <div class="controls">
								 <select id="SelectSystem" name="SelectSystem">
							    </select>
							  </div>
							</div>
                           <div class="control-group">
                            <label class="control-label">報價總金額</label>
                            <div class="controls">
                            <input class="input-xlarge" id="totalprice" name="totalprice" type="text" style="width:200px" value="<?php echo $totalprice2; ?>"   readonly>
                            </div>
                          </div>
                          <div class="control-group">
                            <label class="control-label">成本</label>
                            <div class="controls">
                            <input class="input-xlarge" id="totalprice2" name="totalprice2" type="text" style="width:200px" value="<?php echo $totalprice; ?>" readonly>
                            </div>
                          </div>
                          <div class="control-group">
                            <label class="control-label">利潤</label>
                            <div class="controls">
                            <input class="input-xlarge" id="totalprice3" name="totalprice3" type="text" style="width:200px" value="<?php echo $totalprice3; ?>" readonly>
                            </div>
                          </div>
                          <?php if($_GET['cue']==1){ ?>
							<!--
							<div class="control-group">
							  <label class="control-label" for="gearing">連動修改對內CUE</label>
							  <div class="controls">
								<input type="checkbox" name="gearing" value="1" ><p style="color:red">注意。若為對外一個媒體，對內多個媒體的情況，請逐個修改對內CUE</p>
							  </div>
							</div>
							-->
                            <?php } ?>
                          <div class="control-group">
                          <label class="control-label" for="others">備註</label>
                          <div class="controls">
                            <textarea id="others" name="others" ><?php echo $row4['others']; ?></textarea>
                            <input type="hidden" id="bloggerlist" name="bloggerlist" value="<?=implode(",",$bloggerid)?>"/>
                          </div>
                        </div>
                         <div class="form-actions">
                          <button type="submit" class="btn btn-primary">完成寫手費</button>
                        </div>
                      </form>
                      </div>
					</div>

				</div><!--/span-->
			</div><!--/row-->
			<div class="row-fluid" >
				<div class="box span12" id="addForm">
				</div>
			</div>
			<div class="row-fluid">
				<div class="box span12" id="list">
					<div class="box-header well" data-original-title>
						<h2><i class="icon-edit"></i> 寫手列表</h2>

					</div>
					<div class="box-content">
						<table class="table table-striped table-bordered bootstrap-datatable datatable">
						  <thead>
							  <tr>
									<th class="ui-state-default" style="width: 140px;">照片</th>
									<th class="ui-state-default" style="text-align: left;">名稱</th>
                  <th class="ui-state-default" style="text-align: left;">分類</th>
                  <th class="ui-state-default" style="width: 90px;">Actions</th>
							  </tr>
						  </thead>
						  <tbody>
							<?php 
									$sqlCampaignStatus = "SELECT * FROM blogger";
									$resultCampaign = mysql_query($sqlCampaignStatus);
									while($Rows = mysql_fetch_array($resultCampaign)){ ?>
							<tr>
								<td><img src='<?= $Rows['photo'] ?>' style='height: auto; width: 140px; max-width: 140px;'></td>
        
        					<td style='text-align:  left';>
        				<? if (!empty($Rows["blog_name"])){ ?>
            				<b><img src='images/blogspot.png' style='width: auto; height: 13px; border-radius: 2px;'>&nbsp;Blog</b>：<?=$Rows['blog_name'];?> <br/>
								<? }
								if (!empty($Rows["fb_name"])){ ?>
					         	<b><i class='fa fa-facebook-official' style='color: #5a5aff; font-size: 1em !important;'></i>&nbsp;FB</b>：<?=$Rows['fb_name'];?><br/>
								<? }
								if (!empty($Rows["ig_name"])){ ?>
            				<b><i class='fa fa-instagram' style='color: #fe99ff; font-size: 1em !important;'></i>&nbsp;Instagram</b>：<?=$Rows['ig_name'];?><br/>
								<? }
								if (!empty($Rows["youtube_name"])){ ?>
										<b><i class='fa fa-youtube-play' style='color: #ff4c4c; font-size: 1em !important;'></i>&nbsp;YouTube</b>：<?=$Rows['youtube_name'];?><br/>
								<? } ?>
        					</td>
        					<td><input type='hidden' name='blog_id' value='<?=$Rows['id']. $_GET['campaign']; ?>'></td>
        					<td>
									<a class="btn btn-success" id="PersonAdd" name="PersonAdd[]" href="mtype_Handwriting_add3.php?campaign=<?php echo $_GET['id']; ?>&blogid=<?php echo $Rows['id']; ?>" >
										<i class="icon-zoom-in icon-white"></i>
										Add
									</a>
						
							</td>
        					</tr>
    					<?	}?>
						  </tbody>
					  </table>

					</div>
				</div><!--/span-->
			</div><!--/row-->
					<!-- content ends -->
			</div><!--/#content.span10-->
            <?php include("public/js.php"); ?>