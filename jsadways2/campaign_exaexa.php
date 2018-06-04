<?php
	session_start();
	include('include/db.inc.php');
	$medianumber=31; //媒體數量
	$sql2 = "SELECT * FROM campaign WHERE id= ".$_GET['id'];
	$result2 = mysql_query($sql2);
	$row2 = mysql_fetch_array($result2);
	$medianame1='==========對外投放媒體==========';
	$price1=0;
	$medianame2='==========對內投放媒體==========';
	$price2=0;
	for($i=1;$i<=$medianumber;$i++){
		$sql3='SELECT * FROM media'.$i.' WHERE campaign_id = '.$_GET['id'].' AND cue=1 ORDER BY id';
		$result3=mysql_query($sql3); 
		if (mysql_num_rows($result3)>0){
			while($row3=mysql_fetch_array($result3)){
				$price1=$price1+$row3['totalprice'];
				$sql5 = "SELECT * FROM media WHERE id= ".$i;
				$result5 = mysql_query($sql5);
				$row5 = mysql_fetch_array($result5);
				$medianame1=$medianame1.'<br />'.$row5['name'].'：'.number_format($row3['totalprice']);
			}
		}
		$sql3='SELECT * FROM media'.$i.' WHERE campaign_id = '.$_GET['id'].' AND cue=2 ORDER BY id';
		$result3=mysql_query($sql3); 
		if (mysql_num_rows($result3)>0){
			while($row3=mysql_fetch_array($result3)){
				$price2=$price2+$row3['totalprice'];
				$sql5 = "SELECT * FROM media WHERE id= ".$i;
				$result5 = mysql_query($sql5);
				$row5 = mysql_fetch_array($result5);
				$medianame2=$medianame2.'<br />'.$row5['name'].'：'.number_format($row3['totalprice']);
			}
		}
	}
	//判斷是否有委刊編號，若無則給予委刊編號  
	if($row2['idnumber']==null){
		$ym=date('ym',time());
		$sql7 = "SELECT * FROM campaign ORDER BY idnumber DESC LIMIT 1 ";
		$result7 = mysql_query($sql7);
		$row7 = mysql_fetch_array($result7);
		if(substr($row7['idnumber'],0,4)==$ym){
			$idnumber=substr($row7['idnumber'],0,4).str_pad((substr($row7['idnumber'],4,3)+1),3,'0',STR_PAD_LEFT);
		}else{
			$idnumber=$ym.'001';
		}
		$sql8='UPDATE campaign SET idnumber="'.$idnumber.'"  WHERE id ='.$_GET['id'];
		mysql_query($sql8);
	}
	
	$totalprice1=0;
	$totalprice2=0;
	for($i=1;$i<=$medianumber;$i++){
		$sql3='SELECT * FROM media'.$i.' WHERE campaign_id = '.$_GET['id'].' AND cue=1';
		$result3=mysql_query($sql3); 
		if (mysql_num_rows($result3)>0){
			while($row3=mysql_fetch_array($result3)){
				$totalprice1=$totalprice1+$row3['totalprice'];
				$sql4 = "SELECT * FROM commission WHERE agency= ".$row2['agency_id']." AND media=".$i;
				$result4 = mysql_query($sql4);
				$row4 = mysql_fetch_array($result4);
				/*if(($row3['totalprice']>=$row4['commission2'])&&($row4['commission2']!=0)){
					if(($row3['totalprice']>=$row4['commission6'])&&($row4['commission6']!=0)){
						if(($row3['totalprice']>=$row4['commission8'])&&($row4['commission8']!=0)){
							$commi=(($row3['totalprice']*$row4['commission9'])/100);
						}else{
							$commi=(($row3['totalprice']*$row4['commission7'])/100);
						}
					}else{
						$commi=(($row3['totalprice']*$row4['commission3'])/100);
					}
				}else{
					$commi=(($row3['totalprice']*$row4['commission1'])/100);
				}*/
				$commi=(($row3['totalprice']*$row4['commission1'])/100);
				if($row4['commission5']==1){
					$commission=$commission+$commi+(($row3['totalprice']*$row4['commission4'])/100);
				}
			}
		}
	}
	
	$text='';
	for($i=1;$i<=$medianumber;$i++){
		$sql3='SELECT * FROM media'.$i.' WHERE campaign_id = '.$_GET['id'].' AND cue=2';
		$result3=mysql_query($sql3); 
		if (mysql_num_rows($result3)>0){
			while($row3=mysql_fetch_array($result3)){
				$totalprice2=$totalprice2+$row3['totalprice'];
			}
		}
	}
	if($commission!=0){
		$totalprice2=$totalprice2+$commission;
		$commission='(此案件代理商佣金為$'.$commission.'元)';
	}else{
		$commission='';
	}
	$totalprice1=round($totalprice1);
	$totalprice2=round($totalprice2);
	if($totalprice1!=$totalprice2){
		$text='<br />異常原因為對內外cue表總金額不符';
		echo $text;
	}
	for($i=1;$i<=$medianumber;$i++){
		$sql3='SELECT * FROM media'.$i.' WHERE campaign_id = '.$_GET['id'].' AND totalprice = 0 ';
		$result3=mysql_query($sql3); 
		if (mysql_num_rows($result3)>0){
			while($row3=mysql_fetch_array($result3)){
				$pr=1;
			}
		}
	}
	if($pr==1){
		$text=$text.'<br />異常原因為此案件有PR或媒體金額為0';
		echo $text;
	}
	
	if($_GET['status']==2){
		if(($totalprice1!=$totalprice2)||($pr==1)){
			$sql2='UPDATE campaign SET status=7  WHERE id ='.$_GET['id'];
			//mysql_query($sql2);
			$sql2='INSERT INTO campaignstatus(name,data,times,campaignid) VALUES("'.$_SESSION['username'].'","進行送審，發生異常'.$commission.$text.'",'.time().','.$_GET['id'].')';
			//mysql_query($sql2);
			
		}else{
			$sql2='UPDATE campaign SET status=2  WHERE id ='.$_GET['id'];
			//mysql_query($sql2);	
			$sql2='INSERT INTO campaignstatus(name,data,times,campaignid) VALUES("'.$_SESSION['username'].'","進行送審'.$commission.'",'.time().','.$_GET['id'].')';
			//mysql_query($sql2);
			
		}
		echo $sql2;
	}
	if($_GET['status']==5){
		$sql2='UPDATE campaign SET status=5  WHERE id ='.$_GET['id'];
		mysql_query($sql2);
		$sql2 = "SELECT * FROM campaign WHERE id= ".$_GET['id'];
		$result2 = mysql_query($sql2);
		$row2 = mysql_fetch_array($result2);  
		if($row2['action1']!=null){
			$sql2='UPDATE campaign SET action1="2"  WHERE id ='.$_GET['id'];
			mysql_query($sql2);
		}
		$sql2='INSERT INTO campaignstatus(name,data,times,campaignid) VALUES("'.$_SESSION['username'].'","案件暫停",'.time().','.$_GET['id'].')';
		mysql_query($sql2);
		
	}
	if($_GET['status']==6){
		$sql2='UPDATE campaign SET status=6  WHERE id ='.$_GET['id'];
		mysql_query($sql2);
		$sql2='INSERT INTO campaignstatus(name,data,times,campaignid) VALUES("'.$_SESSION['username'].'","案件中止",'.time().','.$_GET['id'].')';
		mysql_query($sql2);
		
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
		<title>Loading</title>
	</head>
	<body>
		<p class="error-code">
			
		</p>
		<p class="not-found"></p>
		<div class="clear"></div>
		<div class="content">案件送審中</div>
	</body>
</html>
