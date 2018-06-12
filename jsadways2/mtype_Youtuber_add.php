<?php
	session_start();
	include('include/db.inc.php');
?>
		<div class="container-fluid">
		<div class="row-fluid">

			<div id="content" class="span10">
	
			<div class="row-fluid" >
				<div class="box span12" id="list" >
					<div class="box-header well" data-original-title>
						<h2><i class="icon-edit"></i> Youtuber列表</h2>
					</div>
					<br/>
					<div class="box-content">
						<table class="table table-striped table-bordered bootstrap-datatable datatable">
						  <thead>
							  <tr>
                                  <th>部落格名稱</th>
                                  <th>粉絲團名稱</th>
								  <th>分類</th>
								  <th>Actions</th>
							  </tr>
						  </thead>
						  <tbody>
						  	<?php
								$sql2='SELECT * FROM youtuber ';
								$result2=mysql_query($sql2);
								if (mysql_num_rows($result2)>0){
									while($row2=mysql_fetch_array($result2)){
							?>
							<tr>
								<td><a href="blogger_view.php?id=<?php echo $row2['id']; ?>&youtuber=1" target="_blank"><?php echo $row2['name2']; ?></a></td>
                                <td><a href="blogger_view.php?id=<?php echo $row2['id']; ?>&youtuber=1" target="_blank"><?php echo $row2['name3']; ?></a></td>
								<td class="center"><?php echo $row2['class']; ?></td>
								<td class="center">
									<a class="btn btn-success" id="PersonAdd" name="PersonAdd[]" href="mtype_Youtuber_add3.php?id=<?php echo $_GET['id']; ?>&blogid=<?php echo $row2['id']; ?>">
										<i class="icon-zoom-in icon-white"></i>
										Add
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
				</div><!--/span-->
			</div><!--/row-->
			<div class="row-fluid" >
				<div class="box span12" id="addForm">
				</div>
			</div>
            <div class="row-fluid">
				<div class="box span12" id="addList">
					<script>
								$(document).ready(function()
								{
									Page_Init();
								});
								function Page_Init()
								{
									//Abow Start
									<?php 
										include('campaign_required_select.php');
										echo $Select_str;
									?>

									$('#SelectType').change(function(){
										//ChangeSelectType();
									});
									//Abow end
								}
					</script>
					<div class="box-header well" data-original-title>
						<h2><i class="icon-edit"></i> 已增加Youtuber列表</h2>

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
								$sql2='SELECT * FROM media166_detail WHERE campaign_id = '.$_GET['id'];
								$result2=mysql_query($sql2);
								if (mysql_num_rows($result2)>0){
									while($row2=mysql_fetch_array($result2)){
										$totalprice=$totalprice+$row2['price'];
										$totalprice2=$totalprice2+$row2['price2'];
										$totalprice3=$totalprice3+$row2['price3'];
							?>
							<tr>
								<td><a href="blogger_view.php?id=<?php echo $row2['id']; ?>&youtuber=1" target="_blank"><?php echo $row2['blog']; ?><?php if($row2['blog']==NULL){echo $row2['blog2'];} ?></a></td>
                                <td><?php echo $row2['price2']; ?></td>
                                <td><?php echo $row2['price']; ?></td>
                                <td><?php echo $row2['price3']; ?></td>
								<td class="center">

                                    <a class="btn btn-danger" data-toggle="modal" data-target="#dialog" id="PersonDel" name="PersonDel" href="mtype_Youtuber_del.php?campaign=<?php echo $_GET['id']; ?>&id=<?php echo $row2['id'];?>">
										<i class="icon-trash icon-white"></i>
										刪除
                                    </a>
								</td>
							</tr>
							<?php
									}
								}
							?>
						  </tbody>
					  </table>
                      <div class="box-content">
						<form class="form-horizontal" id="templateForm" action="mtype_Youtuber_add2.php?id=<?php echo $_GET['id']; ?>&cue=<?php echo $_GET['cue']; ?>&mediaid=" method="post">
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
                            <label class="control-label">對外報價總金額</label>
                            <div class="controls">
                            <input class="input-xlarge" id="totalprice" name="totalprice" type="text" style="width:200px" value="<?php echo $totalprice2; ?>"  readonly>(<font color="#FF0000">請抓成本+2.5成利潤</font>)可往上再增加報價，不得低於金額<font color="#FF0000"><?php echo $totalprice*1.25; ?></font>
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
                          <div class="control-group">
                          <label class="control-label" for="others">備註</label>
                          <div class="controls">
							<textarea id="others" name="others" ></textarea>
							<input type='hidden' name='bloggerid' value="A" />
                          </div>
                        </div>
                        <div class="form-actions">
                          <button type="submit" class="btn btn-primary">完成Youtuber費</button>
                        </div>
                      </form>
                      </div>
					</div>
				</div><!--/span-->
			</div><!--/row-->
			</div><!--/#content.span10-->
				</div><!--/fluid-row-->
	</div><!--/.fluid-container-->

