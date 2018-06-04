<?php
	session_start();
	include('include/db.inc.php');
	include("mail/class.phpmailer.php"); //匯入PHPMailer類別     
	$sql2 = "SELECT * FROM campaign WHERE id= ".$_GET['id'];
	$result2 = mysql_query($sql2);
	$row2 = mysql_fetch_array($result2);  
	$action=date('Ymd',time());
	if($row2['action1']==null){
		$sql2='UPDATE campaign SET action1="1" , action2="'.$action.'"   WHERE id ='.$_GET['id'];
		mysql_query($sql2);

		$sql_log='INSERT INTO campaignstatus2(name,data,times,campaignid) VALUES("'.$_SESSION['username'].'","按下回簽",'.time().','.$_GET['id'].')';
		mysql_query($sql_log);
	}
	if($row2['action1']==2){
		$sql2='UPDATE campaign SET action1="3" , action2="'.$action.'"   WHERE id ='.$_GET['id'];
		mysql_query($sql2);

		$sql_log='INSERT INTO campaignstatus2(name,data,times,campaignid) VALUES("'.$_SESSION['username'].'","按下回簽",'.time().','.$_GET['id'].')';
		mysql_query($sql_log);
	}
	if($_POST['text']!=null){
		if($_POST['text']=='helen'){
			$sql2='UPDATE campaign SET action1 = NULL ,action2 = NULL ,action3 = NULL   WHERE id ='.$_GET['id'];
			mysql_query($sql2);
		}else{
			$sql2='UPDATE campaign SET action3="'.$_POST['text'].'" , action2="'.$action.'"  WHERE id ='.$_GET['id'];
			mysql_query($sql2);
		}

		$sql_log='INSERT INTO campaignstatus2(name,data,times,campaignid) VALUES("'.$_SESSION['username'].'","修改回簽備註：'.$_POST['text'].'",'.time().','.$_GET['id'].')';
		mysql_query($sql_log);
	}
	echo '<meta http-equiv=REFRESH CONTENT=1;url=campaign_listall.php>';
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
		<link href='http://fonts.googleapis.com/css?family=Creepster|Audiowide' rel='stylesheet' type='text/css'>
		
		<style>
			*{
				margin:0;
				padding:0;
			}
			body{
				font-family: 'Audiowide', cursive, arial, helvetica, sans-serif;
				background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAAUElEQVQYV2NkYGAwBuKzQAwDID4IoIgxIikAMZE1oRiArBDdZBSNMIXoJiFbDZYDKcSmCOYimDuNSVKIzRNYrUYOFuQgweoZbIoxgoeoAAcAEckW11HVTfcAAAAASUVORK5CYII=) repeat;
				background-color:#212121;
				color:white;
				font-size: 18px;
				padding-bottom:20px;
			}
			.error-code{
				font-family: 'Creepster', cursive, arial, helvetica, sans-serif;
				font-size: 200px;
				color: white;
				color: rgba(255, 255, 255, 0.98);
				width: 50%;
				text-align: right;
				margin-top: 5%;
				text-shadow: 5px 5px hsl(0, 0%, 25%);
				float: left;
			}
			.not-found{
				width: 47%;
				float: right;
				margin-top: 5%;
				font-size: 50px;
				color: white;
				text-shadow: 2px 2px 5px hsl(0, 0%, 61%);
				padding-top: 70px;
			}
			.clear{
				float:none;
				clear:both;
			}
			.content{
				text-align:center;
				line-height: 30px;
			}
			input[type=text]{
				border: hsl(247, 89%, 72%) solid 1px;
				outline: none;
				padding: 5px 3px;
				font-size: 16px;
				border-radius: 8px;
			}
			a{
				text-decoration: none;
				color: #9ECDFF;
				text-shadow: 0px 0px 2px white;
			}
			a:hover{
				color:white;
			}

		</style>
		<title>Loading</title>
	</head>
	<body>
		<p class="error-code">
			
		</p>
		<p class="not-found"></p>
		<div class="clear"></div>
		<div class="content">已回簽</div>
	</body>
</html>
