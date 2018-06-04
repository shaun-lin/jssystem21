<?php
ob_start();
ini_set( "memory_limit", "256M");
include('../include/db.inc.php');
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
header("Content-Type:text/html; charset=utf-8");

$sqlmedia = "SELECT * FROM media WHERE id=0";
$resultmedia = mysql_query($sqlmedia);
$rowmedia = mysql_fetch_array($resultmedia);
$medianumber=$rowmedia['name']; //媒體數量




$sql2='SELECT * FROM campaign WHERE status <> 8 AND status>=2 AND status <=5  AND date11>='.strtotime("2016-01-01");

$total = 0;
$isError=0;
$result2=mysql_query($sql2); 
if (mysql_num_rows($result2)>0){
	while($row2=mysql_fetch_array($result2)){
		// echo $row2["id"].$row2["name"].'<br>';
		for($j=1;$j<=$medianumber;$j++){
			$sql3='SELECT * FROM media'.$j.' WHERE campaign_id = '.$row2['id'].' AND cue=2 ORDER BY id';
			$result3=mysql_query($sql3); 
			$isError=0;
			if (mysql_num_rows($result3)>0){
				while($row3=mysql_fetch_array($result3)){
					// $totalprice2=$totalprice2+$row3['totalprice'];
					// if($row2["id"] == 4685){
					// 	echo 'text1='.$row3["text1"];
					// }
					
					if($row3["text1"] == NULL && $row3["text5"] != NULL){
						echo "<a href='http://172.16.1.16/jsadways2/campaign_view.php?id=".$row2["id"]."' target='_blank'>".$row2["id"].'&nbsp'.$row2["name"]."</a>";
						// echo $row2["id"].$row2["name"];
						echo 'text1 = NULL,text5='.$row3["text5"].'<br>';
						$isError = 1;
					}

					if($row3["text1"] == NULL && $row3["text9"] != NULL){
						echo "<a href='http://172.16.1.16/jsadways2/campaign_view.php?id=".$row2["id"]."' target='_blank'>".$row2["id"].'&nbsp'.$row2["name"]."</a>";
						// echo $row2["id"].$row2["name"];
						echo 'text1 = NULL,text9='.$row3["text9"].'<br>';
						$isError = 1;
					}
					
					if($row3["text5"] == NULL && $row3["text9"] != NULL){
						echo "<a href='http://172.16.1.16/jsadways2/campaign_view.php?id=".$row2["id"]."' target='_blank'>".$row2["id"].'&nbsp'.$row2["name"]."</a>";
						// echo $row2["id"].$row2["name"];
						echo 'text5 = NULL,text9='.$row3["text9"].'<br>';
						$isError = 1;
					}


				}
			}
			$total += $isError;
		}

		
	}
}
echo '<br>total:'.$total;
?>