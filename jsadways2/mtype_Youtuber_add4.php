<?php
	session_start();
	include('include/db.inc.php');
	if($_POST['price1']!=null){
		$type=$type.'BLOG文章價格、';
	}
	if($_POST['price2']!=null){
		$type=$type.'fb文章連結轉po費用、';
	}
	if($_POST['price3']!=null){
		$type=$type.'官網識別圖引用費、';
	}
	if($_POST['price4']!=null){
		$type=$type.'出席費、';
	}
	if($_POST['price5']!=null){
		$type=$type.'fb操作費、';
	}
	if($_POST['price6']!=null){
		$type=$type.'平面廣編引用費、';
	}
	if($_POST['price7']!=null){
		$type=$type.'網路全平台引用費、';
	}
	if($_POST['price8']!=null){
		$type=$type.'棚拍費、';
	}
	if($_POST['price9']!=null){
		$type=$type.'Youtuber費、';
	}
	$sql2='INSERT INTO media71_detail(campaign_id,blogid,blog,blog1,blog2,blog3,type,price,price2,price3,times,others,status) VALUES('.$_POST['id'].','.$_POST['blogid'].',"'.$_POST['blog'].'","'.$_POST['blog1'].'","'.$_POST['blog2'].'","'.$_POST['blog3'].'","'.$type.'",'.$_POST['totalprice'].','.$_POST['totalprice2'].','.$_POST['totalprice3'].','.time().',"'.$_POST['others'].'",0)';
	mysql_query($sql2);
	AddMediaMapping(__FILE__, $_GET['id'], mysql_insert_id());
	
	//echo $sql2;
	if($_POST['edit']==1){
		echo '<meta http-equiv=REFRESH CONTENT=1;url=media71_edit.php?campaign='.$_POST['id'].'&id='.$_POST['editid'].'>';
	}else{
		echo '<meta http-equiv=REFRESH CONTENT=1;url=media71_add.php?id='.$_POST['id'].'>';
	}
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
		<title>Add</title>
	</head>
	<body>
		<p class="error-code">
			
		</p>
		<p class="not-found"><br/></p>
		<div class="clear"></div>
		<div class="content">新增媒體成功</div>
	</body>
</html>
