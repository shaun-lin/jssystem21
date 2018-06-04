<?php
	session_start();
	include('include/db.inc.php');


     $str_remark = '';
     $str_pay = '';
     $str_en_remark = '';
     $str_en_pay = '';
     $campaign_sql = "SELECT client_id from campaign where id=".$_GET['id'];
     $result = mysql_query($campaign_sql);
     $row=mysql_fetch_array($result);
     if($row['client_id']=='770'){
        $str_remark = "廣告費每月150萬以上 月結費用外加服務費6%（NET）廣告費每月150萬以下 月結費用外加服務費7%（NET）。";
        $str_pay = '付款為每月月底結帳後，寄出發票，收到發票日後30日內以現金支票或匯款方式付款。';

        $str_en_remark = 'If the advertising fee is over or equal to 1.5 million NT dollars per month, the monthly bill will be charged extra 6%(net) for service. If the advertising fee is under 1.5 million NT dollars, the monthly bill will be charged extra 7%(net) for service.';
        $str_en_pay = 'The bill and invoice will be enclosed to you in the end of month. After receiving the invoice, please kindly complete the payment with cheque or remittance in 30 days.';
     }
     
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Home</title>
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
				<a class="btn btn-success" href="pdf/print1.php?id=<?php echo $_GET['id']; ?>" target="_blank">
                    <i class="icon-zoom-in icon-white"></i>
                    列印基本版型委刊單                                          
                </a>
				<a class="btn btn-success" href="pdf/print2.php?id=<?php echo $_GET['id']; ?>" target="_blank">
                    <i class="icon-zoom-in icon-white"></i>
                    列印基本版型委刊單英文版                                         
                </a>
				<a class="btn btn-primary" href="pdf/print3.php?id=<?php echo $_GET['id']; ?>" target="_blank">
                    <i class="icon-zoom-in icon-white"></i>
                    凱絡版型委刊單                                          
                </a>
                <a class="btn btn-primary" href="pdf/print4.php?id=<?php echo $_GET['id']; ?>" target="_blank">
                    <i class="icon-zoom-in icon-white"></i>
                    fetch版型委刊單                                          
                </a>
			</div>

			
			<div class="row-fluid ">
				<div class="box span12">
					<div class="box-header well" data-original-title>
						<h2><i class="icon-edit"></i> 客製化委刊單</h2>
					</div>
					<div class="box-content">
                   		<form class="form-horizontal" action="pdf/print1.php" method="get" target="_blank">
                         <div class="control-group">
                            <label class="control-label">輸入廣告刊登費格式</label>
                            <div class="controls">
                              <input  id="text1" name="text1" type="text" value="" style="width:200px"> 原格式：$100,000(未稅)
                            </div>
                          </div>
                          <div class="control-group">
                            <label class="control-label">輸入廣告製作費格式</label>
                            <div class="controls">
                              <input  id="text2" name="text2" type="text" value="" style="width:200px"> 原格式：$100,000(未稅)
                            </div>
                          </div>
                          <div class="control-group">
                            <label class="control-label">輸入總價格式</label>
                            <div class="controls">
                              <input  id="text3" name="text3" type="text" value="" style="width:200px"> 原格式：$100,000(含稅)
                            </div>
                          </div>
                          <div class="control-group">
                            <label class="control-label">輸入付款方式</label>
                            <div class="controls">
                              <input  id="text4" name="text4" type="text" value="<?= $str_pay?>" style="width:200px"> 原格式：匯款(90天)
                            </div>
                          </div>

                          <div class="control-group">
                            <label class="control-label">輸入備註內容</label>
                            <div class="controls">
                              <input  id="text5" name="text5" type="text" value="<?= $str_remark?>" style="width:200px"> 原格式：案件名稱
                            </div>
                          </div>
                          <div class="control-group">
                            <label class="control-label">輸入地址內容</label>
                            <div class="controls">
                              <input  id="address" name="address" type="text" value="" style="width:200px"> 原格式：地址 <br>(凱絡 Aster:台北市民生東路三段138號8樓)<br>(凱絡 Janis:台北市民生東路3段132號5樓)
                            </div>
                          </div>
                          <input name="id" type="hidden" value="<?php echo $_GET['id']; ?>">
                          <div class="form-actions">
							  <button type="submit" class="btn btn-primary">列印客製化委刊單</button>
							</div>
                         </form>
					</div>
				</div><!--/span-->

			</div><!--/row-->
			
            <div class="row-fluid ">
				<div class="box span12">
					<div class="box-header well" data-original-title>
						<h2><i class="icon-edit"></i> 客製化委刊單英文版</h2>
					</div>
					<div class="box-content">
                   		<form class="form-horizontal" action="pdf/print2.php" method="get" target="_blank">
                         <div class="control-group">
                            <label class="control-label">輸入廣告刊登費格式</label>
                            <div class="controls">
                              <input  id="text1" name="text1" type="text" value="" style="width:200px"> 原格式：$100,000(net)
                            </div>
                          </div>
                          <div class="control-group">
                            <label class="control-label">輸入廣告製作費格式</label>
                            <div class="controls">
                              <input  id="text2" name="text2" type="text" value="" style="width:200px"> 原格式：$100,000(net)
                            </div>
                          </div>
                          <div class="control-group">
                            <label class="control-label">輸入總價格式</label>
                            <div class="controls">
                              <input  id="text3" name="text3" type="text" value="" style="width:200px"> 原格式：$100,000 USD(net)
                            </div>
                          </div>
                          <div class="control-group">
                            <label class="control-label">輸入付款方式</label>
                            <div class="controls">
                              <input  id="text4" name="text4" type="text" value="<?= $str_en_pay?>" style="width:200px"> 原格式：匯款(90天)
                            </div>
                          </div>
                          <div class="control-group">
                            <label class="control-label">輸入備註內容</label>
                            <div class="controls">
                              <input  id="text5" name="text5" type="text" value="<?= $str_en_remark?>" style="width:200px"> 原格式：案件名稱
                            </div>
                          </div>
                           <div class="control-group">
                            <label class="control-label">輸入地址</label>
                            <div class="controls">
                              <input  id="text6" name="text6" type="text" value="" style="width:200px"> 原格式：台北市大安區光復南路260巷24號2樓 
                            </div>
                          </div>
                          <input name="id" type="hidden" value="<?php echo $_GET['id']; ?>">
                          <div class="form-actions">
							  <button type="submit" class="btn btn-primary">列印客製化委刊單英文版</button>
							</div>
                         </form>
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
