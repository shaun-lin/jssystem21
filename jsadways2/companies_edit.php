<?php
	session_start();
	// 2018-03-14 (Jimmy): 傑思jsadways2/media_edit.php, 香港jsadways2hk/media_edit.php, 豐富媒體jsadways2ff/media_edit.php 共用此檔案
	require_once dirname(__DIR__) .'/autoload.php';

	$objSizeformat = CreateObject('Companies', GetVar('id'));
	$obj_contacct = CreateObject('Companies_contacct',GetVar('contact_id'));

	// if (!IsId($objSizeformat->getId()) || (!IsPermitted('superuser') && !in_array($_SESSION['departmentid'], [21, 22]))) {
	// 	RedirectLink('medias_list.php');
	// }
	if ((!IsPermitted('superuser') && !in_array($_SESSION['departmentid'], [21, 22]))) {
		RedirectLink('companies_list.php');
	}
	//導入資料庫 jackie
	include('include/db.inc.php');
	//
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>【<?= $GLOBALS['env']['flag']['name']; ?>媒體公司規格表</title>
		<?php include("public/head.php"); ?>
	</head>
	<style>
		.companies_text{
			width : 50%;
		}
	</style>
	<body>
		<?php include("public/topbar.php"); ?>
		
		<div class="container-fluid">
			<div class="row-fluid">
				<?php include("public/left.php");
					if(GetVar('id') == 0){
						$Hidden_Dom = "style = 'display: none'";
						$Hidden_Insert = "";
					}else{
						$Hidden_Dom = "";
						$Hidden_Insert = "style = 'display: none'";
					}
				?>
				<div id="content" class="span10">
				<!-- 新增資料區塊。網址的ID為空或0時顯示，其他狀況屏蔽 -->
					<form class="form-horizontal_Insert" action="companies_edit2.php?I_U=0" method="post" id= "companies_Insert_Form">
						<div class="row-fluid" <?=$Hidden_Insert ?>>
							<div class="box span7">
								<div class="box-header well" data-original-title>
									<h2><i class="fa fa-cube"></i>&nbsp;媒體公司 - <?= $objSizeformat->getVar('name'); ?></h2>
								</div>
								<div class="box-content">
								<table class="table table-bordered table-striped" style="text-align:right" id= "companies_Insert_table">
									<tr>
										<td><h4>媒體公司名稱</h4></td>
										<td><input type="text" name="name" id = "Companies_Name" class="companies_text"></td>
									</tr>
									<tr>
										<td><h4>公司縮寫</h4></td>
										<td><input type="text" name="name2" class="companies_text"></td>
									</tr>
									<tr>
										<td><h4>英文名稱</h4></td>
										<td><input type="text" name="eng_name" class="companies_text" ></td>
									</tr>
									<tr>
										<td><h4>統編</h4></td>
										<td><input type="text" name="tax_id" class="companies_text"></td>
									</tr>
									<tr>
										<td><h4>電話</h4></td>
										<td><input type="text" name="tel" class="companies_text"></td>
									</tr>
									<tr>
										<td><h4>傳真</h4></td>
										<td><input type="text" name="fax" class="companies_text"></td>
									</tr>
									<tr>
										<td><h4>國家簡碼</h4></td>
										<td><input type="text" name="country_code" class="companies_text" ></td>
									</tr>
									<tr>
										<td><h4>語言</h4></td>
										<td><input type="text" name="language" class="companies_text" ></td>
									</tr>
									<tr>
										<td><h4>郵遞區號</h4></td>
										<td><input type="text" name="area_code" class="companies_text"></td>
									</tr>
									<tr>
										<td><h4>所在區域</h4></td>
										<td><input type="text" name="city_name" class="companies_text"></td>
									</tr>
									<tr>
										<td><h4>地址</h4></td>
										<td><input type="text" name="address" class="companies_text"></td>
									</tr>
									<tr>
										<td><h4>付款資料</h4></td>
										<td><input type="text" name="payinfo" class="companies_text"></td>
									</tr>
									<tr>
										<td><h4>付款天數</h4></td>
										<td><input type="text" name="paydays" class="companies_text"></td>
									</tr>										
									<tr>
										<td><h4>退傭%數</h4></td>
										<td><input type="text" name="refund" class="companies_text"></td>
									</tr>
									<tr>
										<td></td>
										<td><input type="button" class="btn btn-primary" value="確定新增" onclick="Check_Companies(0, 1)"></td>
									</tr>
								</table>
								</div>
							</div>
						</div>
					</form>
					<!-- 修改資料區塊。依照網址的ID不為空或大於0顯示，並依ID搜尋資料顯示 -->
					<form class="form-horizontal" action="companies_edit2.php?I_U=1&id=<?= GetVar('id'); ?>"  method="post" id= "companies_UP_Form"> 
						<div class="row-fluid" <?=$Hidden_Dom ?>>	
							<div class="box span7">
								<div class="box-header well" data-original-title>
									<h2><i class="fa fa-cube"></i>&nbsp;媒體公司 - <?= $objSizeformat->getVar('name'); ?></h2>
								</div>

								<div class="box-content">
								<table class="table table-bordered table-striped" style="text-align:right" id= "companies_Info_table">
										<tr>
										<td><h4>最近更新</h4></td>
											<td>
											<!-- 當日其資料無法傳換時，隱藏訊息不顯示 -->
											<?php if(!empty($objSizeformat->getVar('time'))==1)
											{echo "於 <b>".date('Y-m-d H:i',$objSizeformat->getVar('time')); }
											?></b> 由 <b>
											<?=$objSizeformat->getVar('update_user'); 
											?></b> 更新</td>
										</tr>
										<tr>
											<td><h4>媒體公司名稱<h4></td>
											<td><input type="text" name="name" value=<?=str_replace("\\n","\n",$objSizeformat->getVar('name')); ?> id= "Companies_Name_Change" class="companies_text"></td>
										</tr>
										<tr>
											<td><h4>公司縮寫</h4></td>
											<td><input type="text" name="name2" class="companies_text" value=<?=str_replace("\\n","\n",$objSizeformat->getVar('name2')); ?> ></td>
										</tr>
										<tr>
											<td><h4>英文名稱</h4></td>
											<td><input type="text" name="eng_name" class="companies_text" value=<?= '"' .$objSizeformat->getVar('eng_name') .'"'; ?> ></td>
										</tr>
										<tr>
											<td><h4>統編</h4></td>
											<td><input type="text" name="tax_id" class="companies_text" value=<?=str_replace("\\n","\n",$objSizeformat->getVar('tax_id')); ?> ></td>
										</tr>
										<tr>
											<td><h4>電話</h4></td>
											<td><input type="text" name="tel" class="companies_text" value=<?=str_replace("\\n","\n",$objSizeformat->getVar('tel')); ?> ></td>
										</tr>
										<tr>
											<td><h4>傳真</h4></td>
											<td><input type="text" name="fax" class="companies_text" value=<?=str_replace("\\n","\n",$objSizeformat->getVar('fax')); ?> ></td>
										</tr>
										<tr>
											<td><h4>國家簡碼</h4></td>
											<td><input type="text" name="country_code" class="companies_text" value=<?= '"' .$objSizeformat->getVar('country_code') .'"'; ?> ></td>
										</tr>
										<tr>
											<td><h4>語言</h4></td>
											<td><input type="text" name="language" class="companies_text" value=<?= '"' .$objSizeformat->getVar('language') .'"'; ?> ></td>
										</tr>
										<tr>
											<td><h4>郵遞區號</h4></td>
											<td><input type="text" name="area_code" class="companies_text" value=<?= '"' .$objSizeformat->getVar('area_code') .'"'; ?> ></td>
										</tr>
										<tr>
											<td><h4>所在區域</h4></td>
											<td><input type="text" name="city_name" class="companies_text" value=<?= '"' .$objSizeformat->getVar('city_name') .'"'; ?> ></td>
										</tr>
										<tr>
											<td><h4>地址</h4></td>
											<td><textarea class="autogrow" name="address" class="companies_text"><?= str_replace("\\n", "\n", $objSizeformat->getVar('address')); ?>
											</textarea></td>
										</tr>
										<tr>
											<td><h4>付款資訊</h4></td>
											<td><textarea class="autogrow" name="payinfo" class="companies_text"><?= str_replace("\\n", "\n", $objSizeformat->getVar('payinfo')); ?></textarea></td>
										</tr>
										<tr>
											<td><h4>付款天數</h4></td>
											<td><input type="text" name="paydays" class="companies_text" value=<?=str_replace("\\n","\n",$objSizeformat->getVar('paydays')); ?> ></td>
										</tr>
										<tr>
											<td><h4>退傭%數</h4></td>
											<td><input type="text" name="refund" class="companies_text" value=<?=str_replace("\\n","\n",$objSizeformat->getVar('refund')); ?> ></td>
										</tr>
																			
									</table>
									<!-- companies contact table jackie -->
									<div class="box-header well" data-original-title>
									<h2><i class="fa fa-cube"></i>&nbsp;聯絡資訊 - <?= $objSizeformat->getVar('name'); ?></h2>
									</div>
									<!-- companies contact table -->
									<table class="table table-bordered table-striped" id="Companies_contact">
									
									<tr>
										<th class="td01">職位名稱</td>
										<th class="td01">姓名</td>
										<th class="td01">電話</td>
										<th class="td01">電子郵件</td>
										<th class="td01"><input class="btn btn-primary" type="button" value="新增資料" onclick="Add_New_Tr(this)"></td>
									</tr>
									<tbody id="contact_Info">
									<!-- 顯示資料庫的聯絡人資料 -->
									<?php
										$sqlCampaignStatus = "SELECT * FROM companies_contact WHERE companies_contact.contact_agency =". GetVar('id');
										$resultCampaign = mysql_query($sqlCampaignStatus);
										while($Rows = mysql_fetch_array($resultCampaign)){
											echo "\t<tr>\n";
											echo "\t\t<td>".$Rows['contact_title']."</td> \n";
											echo "\t\t<td>".$Rows['contact_name']."</td>\n";
											echo "\t\t<td>".$Rows['contact_tel']."</td>\n";
											echo "\t\t<td>".$Rows['contact_email']."</td>\n";
											echo "\t\t<td>";
											echo "<input type='hidden' id='contact_id' value=" .$Rows['contact_id']. ">";
											echo "<input type='button' value='修改' onclick='edit_data(this)'> ";
											echo "<input type='button' value='確定' onclick='Final_UPData(this)' style = 'display: none'>";
											echo "<input type='button' value='刪除' onclick='remove_data(this)'>";
											echo "<input type='button' value='取消' onclick='Cancel_data(this)' style = 'display: none'>";										
											echo "</td>\n";
											echo "\t</tr>\n";
										}	
									?>
									</tbody>
									</table>

									<div class="box-header well" data-original-title>
									<h2><i class="fa fa-cube"></i>&nbsp;媒體 - <?= $objSizeformat->getVar('name'); ?></h2>
									</div>
									<div>
									<!-- 顯示媒體資料 -->
									<?php
										$sqlClass2 = "SELECT id,name FROM `medias` where display='1' order by name";
										$resultClass2 = mysql_query($sqlClass2);
										while ($itemClass2 = mysql_fetch_array($resultClass2)) {
											$rowsClass2[] = $itemClass2;
										}
										$sqlClass = "SELECT `medias_id` AS ids FROM rel_media_companies WHERE  `companies_id` =" . GetVar('id') ;

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
									<div class="form-actions">
										<input name="id" type="hidden" value="<?= $objSizeformat->getId(); ?>" />
										<input type="button" class="btn btn-primary" value="確定修改" onclick="Check_Companies(1, 1)">
										<input type="button" class="btn btn-primary" value="取消離開" onclick="Leave_Page()">
									</div>
								</div>
							</div>
						</div>
						<input type="hidden" id="Companies_id" value=" <?= GetVar('id'); ?> ">
					</form>

				</div>
			</div>
			<hr/>
			<?php include("public/footer.php"); ?>
		</div>

		<?php include("public/js.php"); ?>
		<script>
		// 處理聯絡人區塊
			// 修改聯絡人資料
			function edit_data(r) {
				var Company_Id = document.getElementById("Companies_id").value;
				var num = r.parentNode.parentNode.rowIndex;
				var Companies_contact_Valus = document.getElementById("Companies_contact");
				for (var i = 0 ; i<4 ; i++){																								
					var vals = Companies_contact_Valus.rows[num].cells[i].innerText;
					Companies_contact_Valus.rows[num].cells[i].innerHTML = "<input type='text' value='" + vals + "'>";	
				}
				Companies_contact_Valus.rows[num].cells[4].children[2].style.display = "";
				Companies_contact_Valus.rows[num].cells[4].children[4].style.display = "";
				Companies_contact_Valus.rows[num].cells[4].children[1].style.display = "none";
				Companies_contact_Valus.rows[num].cells[4].children[3].style.display = "none";	
			}
			// 取消聯絡人資料
			function Cancel_data(r) {
				var Company_Id = document.getElementById("Companies_id").value;
				var num = r.parentNode.parentNode.rowIndex;											
				var Companies_contact_Valus = document.getElementById("Companies_contact");
				for (var i = 0 ; i<4 ; i++){																								
					var vals = Companies_contact_Valus.rows[num].cells[i].firstChild.value;
					Companies_contact_Valus.rows[num].cells[i].innerText = vals;	
				}
				Companies_contact_Valus.rows[num].cells[4].children[2].style.display = "none";
				Companies_contact_Valus.rows[num].cells[4].children[4].style.display = "none";
				Companies_contact_Valus.rows[num].cells[4].children[1].style.display = "";
				Companies_contact_Valus.rows[num].cells[4].children[3].style.display = "";
			}
			// 取消聯絡人資料
			function Final_UPData(r){
				var Company_Id = document.getElementById("Companies_id").value;
				var num = r.parentNode.parentNode.rowIndex;
				var Companies_contact_Valus = document.getElementById("Companies_contact");											
				var All_Table_Values = "";
				All_Table_Values += Companies_contact_Valus.rows[num].cells[4].firstChild.value + ",";
				All_Table_Values += Companies_contact_Valus.rows[num].cells[0].firstChild.value + ",";
				All_Table_Values += Companies_contact_Valus.rows[num].cells[1].firstChild.value + ",";
				All_Table_Values += Companies_contact_Valus.rows[num].cells[2].firstChild.value + ",";
				All_Table_Values += Companies_contact_Valus.rows[num].cells[3].firstChild.value;
					$.ajax({url:"companies_Ajax.php",
						cache: false,
						dataType: 'text',
						type:'POST',
						data:  { Companies_contact : All_Table_Values , id : Company_Id , I_U : 0 },
							success:function(result){
								//alert('Final_UPData 成功' + result);
								alert("資料更新成功");
								var Companies_contact_Valus = document.getElementById("Companies_contact");
								for (var i = 0 ; i<4 ; i++){																								
									var vals = Companies_contact_Valus.rows[num].cells[i].firstChild.value;
									Companies_contact_Valus.rows[num].cells[i].innerText = vals;	
								}
								Companies_contact_Valus.rows[num].cells[4].children[2].style.display = "none";
								Companies_contact_Valus.rows[num].cells[4].children[4].style.display = "none";
								Companies_contact_Valus.rows[num].cells[4].children[1].style.display = "";
								Companies_contact_Valus.rows[num].cells[4].children[3].style.display = "";
							},
							error:function(){alert('Final_UPData 發生錯誤');}
					});
			}
			// 刪除聯絡人資料
			function remove_data(r){
				var Company_Id = document.getElementById("Companies_id").value;
				var num = r.parentNode.parentNode.rowIndex;
				var Companies_contact_Valus = document.getElementById("Companies_contact");
				var All_Table_Values = "";
				All_Table_Values += Companies_contact_Valus.rows[num].cells[4].firstChild.value;
				if (confirm("確定要刪除嗎？")) {
					$.ajax({url:"companies_Ajax.php",
						cache: false,
						dataType: 'text',
						type:'POST',
						data:  { Companies_contact : All_Table_Values , id : Company_Id , I_U : 2 },
							success:function(result){
							//alert('remove_data 成功' + result);
							alert ("資料刪除成功");
								document.getElementById("Companies_contact").deleteRow(num);
							},
								error:function(){alert('remove_data 發生錯誤');}
					});
				}
			}
			// 取消新增聯絡人資料列
			function Cancel_New_Tr(r){
				var num = r.parentNode.parentNode.rowIndex;
				document.getElementById("Companies_contact").deleteRow(num);
			}
			// 新增輸入聯絡人資料的資料列
			function Add_New_Tr(r) {
				var num = document.getElementById("contact_Info").rows.length;
				var Tr = document.getElementById("contact_Info").insertRow(num);
				Td = Tr.insertCell(Tr.cells.length);
					Td.innerHTML='<input type="text">';
					Td = Tr.insertCell(Tr.cells.length);
					Td.innerHTML='<input type="text">';
					Td = Tr.insertCell(Tr.cells.length);
					Td.innerHTML='<input type="text">';
					Td = Tr.insertCell(Tr.cells.length);
					Td.innerHTML='<input type="text">';
					Td = Tr.insertCell(Tr.cells.length);
					Td.innerHTML='<input type="hidden" id="contact_id" value=' + num + '><input type="button" value="確認" onclick="Check_Emply(this,0)"><input type="button" value="取消" onclick="Cancel_New_Tr(this)">';
			}
			// 將聯絡人資料寫到資料庫(Ajax request)，清空聯絡人表單後，重新寫入查詢後的資料(Ajax request Second)
			function Insert_New_Data(r){
				var Company_Id = document.getElementById("Companies_id").value;
				var num = r.parentNode.parentNode.rowIndex;
				var Companies_contact_Valus = document.getElementById("Companies_contact");
				var All_Table_Values = "";
				All_Table_Values += Companies_contact_Valus.rows[num].cells[4].firstChild.value + ",";
				All_Table_Values += Companies_contact_Valus.rows[num].cells[0].firstChild.value + ",";
				All_Table_Values += Companies_contact_Valus.rows[num].cells[1].firstChild.value + ",";
				All_Table_Values += Companies_contact_Valus.rows[num].cells[2].firstChild.value + ",";
				All_Table_Values += Companies_contact_Valus.rows[num].cells[3].firstChild.value;
					$.ajax({url:"companies_Ajax.php",
						cache: false,
						dataType: 'text',
						type:'POST',
						data:  { Companies_contact : All_Table_Values , id : Company_Id , I_U : 1 },
							success:function(result){
								//alert('Ajax request 成功' + result);
								$("#contact_Info").html(""); //清空聯絡人表單資料

								$.ajax({url:"companies_Ajax.php",
									cache: false,
									dataType: 'text',
									type:'POST',
									data:  { Reload_Contact : "Companies_contact" , id : Company_Id},
										success:function(result){
											//alert('Ajax request Second 成功' + result);
											$("#contact_Info").html(result);
										},
										error:function(){alert('Ajax request 發生錯誤');}
								});
							},
							error:function(){alert('Ajax request 發生錯誤');}
					});
			}
			// 確認聯絡人的姓名資料是否為空，再比對名字是否重複，沒重複就寫到資料庫。
			function Check_Emply(r, T_N){
				var Company_Id = document.getElementById("Companies_id").value;
				var num = r.parentNode.parentNode.rowIndex;
				var Companies_contact_Valus = document.getElementById("Companies_contact");
				var Check = Companies_contact_Valus.rows[num].cells[1].firstChild.value;
				if (Check.replace(/(^s*)|(s*$)/g, "").length ==0){
					alert ("姓名為必填，請輸入資料");
					return;
				}	
				$.ajax({url:"companies_Ajax.php",
						cache: false,
						dataType: 'text',
						type:'POST',
						data:  { Check_Haved : Check, id : Company_Id, T_N : T_N},
							success:function(result){
								//alert('Check_Emply 成功' + result);
								if (result == "true"){
									alert("姓名不能重複，請更改姓名");
								}else{
									Insert_New_Data(r);
								}
							},
							error:function(){alert('Check_Emply 發生錯誤');}
				});
			}

			// 處理公司資料區塊
			function Check_Companies(r, T_N){ //r：0為新增，1為修改   T_N：0為查詢聯絡資料 1為查詢公司名稱
				var Company_Id = document.getElementById("Companies_id").value;
				var Check ;
				var T_Ns = T_N;
				if (r == 0){
					Check = document.getElementById("Companies_Name").value;
				}else if(r == 1){
					Check = document.getElementById("Companies_Name_Change").value;
					T_Ns = 2;
					}
				if (Check.replace(/(^s*)|(s*$)/g, "").length ==0){
					alert ("媒體公司名稱為必填，請輸入資料");
					return;
				}
				if(isNaN($('input:[name="tax_id"]:eq('+ r +')').val()) || $('input:[name="tax_id"]:eq('+ r +')').val().length > 20 ){alert("統編請輸入數字或是字數過多");return;}
				if(isNaN($('input:[name="tel"]:eq('+ r +')').val()) || $('input:[name="tel"]:eq('+ r +')').val().length > 15 ){alert("電話請輸入數字或是字數過多");return;}
				if(isNaN($('input:[name="fax"]:eq('+ r +')').val()) || $('input:[name="fax"]:eq('+ r +')').val().length > 15 ){alert("傳真請輸入數字或是字數過多");return;}
				if(isNaN($('input:[name="paydays"]:eq('+ r +')').val()) || $('input:[name="paydays"]:eq('+ r +')').val().length > 3 ){alert("付款天數請輸入數字或是字數過多");return;}
				if(isNaN($('input:[name="refund"]:eq('+ r +')').val()) || $('input:[name="refund"]:eq('+ r +')').val().length > 3 ){alert("退傭%數請輸入數字或是字數過多");return;}
				$.ajax({url:"companies_Ajax.php",
						cache: false,
						dataType: 'text',
						type:'POST',
						data:  { Check_Haved : Check, T_N : T_Ns, id : Company_Id},
							success:function(result){
								//alert('Check_Emply 成功' + result);
								if (result == "true"){
									alert("媒體公司名稱不能重複，請更改媒體公司名稱");
								}else{
									if (r == 0){
										$('#companies_Insert_Form').submit();
									}else if(r == 1){
										$('#companies_UP_Form').submit();
									}
								}
						},
							error:function(){alert('Check_Emply 發生錯誤');}
				});
			}

			function Leave_Page(){
				document.location.href="companies_list.php";
			}
		</script>
	</body>
</html>