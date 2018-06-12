<?php 
	session_start();
	include('include/db.inc.php');
?>			
			<div id="content" class="span10">
			<!-- content starts -->
			<?php
				$sql1 = "SELECT * FROM campaign WHERE id= ".$_GET['id'];
				$result1 = mysql_query($sql1);
				$row1 = mysql_fetch_array($result1);
			?>		
			<div class="row-fluid sortable">
				<div class="box span12">
					<div class="box-header well" data-original-title>
						<h2><i class="icon-edit"></i> <?php echo $row1['name2']; ?>-新增寫手</h2>
					</div>
                    <?php
						$sql2 = "SELECT * FROM youtuber WHERE id= ".$_GET['blogid'];
						$result2 = mysql_query($sql2);
						$row2 = mysql_fetch_array($result2);
					?>
					<div class="box-content">
						<p><h2>新增寫手</h2></p>
						<form class="form-horizontal" action="mtype_Youtuber_add4.php" method="post">
						  <fieldset> 
                            <script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
                            <script type="text/javascript" src="js/jquery.xml2json.js"></script>
							<script type="text/javascript" src="js/JSLINQ.js"></script>
                            <script type="text/javascript">
							
							
							function number3(){
								var obj=document.getElementById('blog1');
								var index=obj.selectedIndex;
								var val = obj.options[index].value;
								if(val == '部落格')
								{
									document.getElementById('blog2').value='<?php echo addslashes($row2['name2']); ?>';
									document.getElementById('blog3').value='<?php echo $row2['link1']; ?>';
								}
								 if(val == '粉絲團')
								{
									document.getElementById('blog2').value='<?php echo addslashes($row2['name3']); ?>';
									document.getElementById('blog3').value='<?php echo $row2['link2']; ?>';
								}
								
							}
							function number4(){
								var totalprice2=0;
								if(document.getElementById('price1').checked == true)
								{
									totalprice2=totalprice2+Number(document.getElementById('price1').value);
								}
								if(document.getElementById('price2').checked == true)
								{
									totalprice2=totalprice2+Number(document.getElementById('price2').value);
								}
								if(document.getElementById('price3').checked == true)
								{
									totalprice2=totalprice2+Number(document.getElementById('price3').value);
								}
								if(document.getElementById('price4').checked == true)
								{
									totalprice2=totalprice2+Number(document.getElementById('price4').value);
								}
								if(document.getElementById('price5').checked == true)
								{
									totalprice2=totalprice2+Number(document.getElementById('price5').value);
								}
								if(document.getElementById('price6').checked == true)
								{
									totalprice2=totalprice2+Number(document.getElementById('price6').value);
								}
								if(document.getElementById('price7').checked == true)
								{
									totalprice2=totalprice2+Number(document.getElementById('price7').value);
								}
								if(document.getElementById('price8').checked == true)
								{
									totalprice2=totalprice2+Number(document.getElementById('price8').value);
								}
								if(document.getElementById('price9').checked == true)
								{
									totalprice2=totalprice2+Number(document.getElementById('price9').value);
								}
								document.getElementById('totalprice').value=totalprice2;
							}
							function number(){
							 var a = document.getElementById('quantity').value,
							 	b = document.getElementById('price').value;
							 var g = a*b;
							 
							 document.getElementById('totalprice').value=Math.round(g);
							}
							function change(){
								document.getElementById('totalprice2').value=Math.round(document.getElementById('totalprice').value*1.25);
							 var a = document.getElementById('totalprice2').value,
							 	b = document.getElementById('totalprice').value;
							 var g = a-b;
							 
							
							 
							  document.getElementById('totalprice3').value=Math.round(g);
							}
							function change2(){
							 var a = document.getElementById('totalprice2').value,
							 	b = document.getElementById('totalprice').value;
							 var g = a-b;
							 
							 document.getElementById('totalprice3').value=Math.round(g);
							}
							</script>
											  
							  <div class="control-group">
								<label class="control-label">分類</label>
								<div class="controls">
                                   <select id="blog1" name="blog1" onChange="number3()" >
									<option value="部落格">部落格</option>
                                    <option value="粉絲團">粉絲團</option>
							    </select>
								</div>
							  </div>
							  <div class="control-group">
								<label class="control-label">名稱</label>
								<div class="controls">
                                <input class="input-xlarge" id="blog2" name="blog2" type="text" value="<?php echo $row2['name2']; ?>"  required>
								</div>
							  </div>
                               <div class="control-group">
								<label class="control-label">URL</label>
								<div class="controls">
                                <textarea id="blog3" name="blog3"><?php echo $row2['link1']; ?></textarea>
								</div>
							  </div>
							  
							  <div class="control-group">
							  <label class="control-label" for="blogtype">報價類型</label>
							  <div class="controls">
							  
								 <input type="checkbox" id="price1" name="price1" value="<?php echo $row2['price1']; ?>" onChange="number4()">BLOG文章價格($<?php echo $row2['price1']; ?>)<br />
                                 <input type="checkbox" id="price2" name="price2" value="<?php echo $row2['price2']; ?>" onChange="number4()" >fb文章連結轉po費用($<?php echo $row2['price2']; ?>)<br />
                                 <input type="checkbox" id="price3" name="price3" value="<?php echo $row2['price3']; ?>" onChange="number4()">官網識別圖引用費($<?php echo $row2['price3']; ?>)<br />
                                 <input type="checkbox" id="price4" name="price4" value="<?php echo $row2['price4']; ?>" onChange="number4()" >出席費($<?php echo $row2['price4']; ?>)<br />
                                 <input type="checkbox" id="price5" name="price5" value="<?php echo $row2['price5']; ?>" onChange="number4()">fb操作費($<?php echo $row2['price5']; ?>)<br />
                                 <input type="checkbox" id="price6" name="price6" value="<?php echo $row2['price6']; ?>" onChange="number4()" >平面廣編引用費($<?php echo $row2['price6']; ?>)<br />
                                 <input type="checkbox" id="price7" name="price7" value="<?php echo $row2['price7']; ?>" onChange="number4()">網路全平台引用費($<?php echo $row2['price7']; ?>)<br />
                                 <input type="checkbox" id="price8" name="price8" value="<?php echo $row2['price8']; ?>" onChange="number4()" >棚拍費($<?php echo $row2['price8']; ?>)<br />
                                 <input type="checkbox" id="price9" name="price9" value="<?php echo $row2['price9']; ?>" onChange="number4()" >Youtuber費($<?php echo $row2['price9']; ?>)
							  </div>
							</div>
                             <div class="control-group">
							   <label class="control-label" for="blogtype">其它費用備註</label>
							  <div class="controls"><?php echo $row2['other1']; ?></div>
							</div>   
                             <div class="control-group">
							   <label class="control-label" for="blogtype">製作規範</label>
							  <div class="controls"><?php echo $row2['other3']; ?></div>
							</div>  
                            <div class="control-group">
							  <label class="control-label" for="totalprice2">對外報價</label>
							  <div class="controls">
								$<input class="input-xlarge" id="totalprice2" name="totalprice2" type="text" value="" style="width:100px"  onChange="change2()"  required>元
							  </div>
							</div>  
                            <div class="control-group">
							  <label class="control-label" for="totalprice">成本金額</label>
							  <div class="controls">
								$<input class="input-xlarge" id="totalprice" name="totalprice" type="text" value="" style="width:100px" onChange="change()"  required>元(若無自動帶出金額，先請洽詢行政部該部落客的成本金額)
							  </div>
							</div>        
                              
                            <div class="control-group">
							  <label class="control-label" for="totalprice3">利潤</label>
							  <div class="controls">
								$<input class="input-xlarge" id="totalprice3" name="totalprice3" type="text" value="" style="width:100px" readonly>元
							  </div>
							</div>                    
							<div class="control-group">
							  <label class="control-label" for="others">備註</label>
							  <div class="controls">
                                <textarea id="others" name="others" ></textarea>
							  </div>
							</div>
                            <input name="id" type="hidden" value="<?php echo $_GET['id']; ?>">
                            <input name="campaign" type="hidden"  value="<?php echo $_GET['campaign']; ?>">
                            <input name="isEdit" type="hidden" value="N">
                            <input name="blogid" type="hidden" value="<?php echo $_GET['blogid']; ?>">
                            <input name="blog" type="hidden" value="<?php echo $row2['name2']; ?>">
                            <?php if($_GET['edit']==1){ ?>
                            <input name="edit" type="hidden" value="1">
							<input name="editid" type="hidden" value="<?php echo $_GET['editid']; ?>">
							<?php } ?>
							<div class="form-control">
							  <button id="complete" class="btn btn-primary">新增寫手費</button>
							  <button id="cancel" class="btn btn-danger">取消</button>
							</div>
						  </fieldset>
						</form>   

					</div>
				</div><!--/span-->
			</div><!--/row-->

		
    
					<!-- content ends -->
			</div><!--/#content.span10-->
				</div><!--/fluid-row-->
		
	</div><!--/.fluid-container-->
	<?php include("public/js.php"); ?>
