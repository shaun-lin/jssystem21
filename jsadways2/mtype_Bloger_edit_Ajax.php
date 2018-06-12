<?php

require_once dirname(__DIR__) .'/autoload.php';
require_once __DIR__ .'/include/function.inc.php';
include('include/db.inc.php');

if(isset($_POST["init"])){
    $sqlCampaignStatus = "SELECT * FROM blogger";
    $resultCampaign = mysql_query($sqlCampaignStatus);
	while($Rows = mysql_fetch_array($resultCampaign)){
		echo "\t<tr>\n";
		echo "\t\t<td><img src='".$Rows['photo']."' style='height: auto; width: 140px; max-width: 140px;'></td> \n";
        
        echo "\t\t<td style='text-align:  left';>";
        if (!empty($Rows["blog_name"])){
            echo "<b><img src='images/blogspot.png' style='width: auto; height: 13px; border-radius: 2px;'>&nbsp;Blog</b>：".$Rows['blog_name']."<br/>";
        }
        if (!empty($Rows["fb_name"])){
            echo "<b><i class='fa fa-facebook-official' style='color: #5a5aff; font-size: 1em !important;'></i>&nbsp;FB</b>：".$Rows['fb_name']."<br/>";
        }
        if (!empty($Rows["ig_name"])){
            echo "<b><i class='fa fa-instagram' style='color: #fe99ff; font-size: 1em !important;'></i>&nbsp;Instagram</b>：".$Rows['ig_name']."<br/>";
        }
        if (!empty($Rows["youtube_name"])){
            echo "<b><i class='fa fa-youtube-play' style='color: #ff4c4c; font-size: 1em !important;'></i>&nbsp;YouTube</b>：".$Rows['youtube_name']."<br/>";
        }
        echo "</td>\n";
        echo "\t\t<td><input type='hidden' name='bloggerid' value=" .$Rows['id']. "></td>";
        echo "\t\t<td>";
		echo "<a class='btn btn-success' id='PersonAdd' name='PersonAdd[]' href='mtype_Youtuber_add3.php?id=";
        echo $_GET['id']; 
        echo "&blogid=";
        echo $row2['id'];
        echo "'>";
        echo "<i class='icon-zoom-in icon-white'></i>Add</a>";
						
		echo "</td>\n";
        echo "\t</tr>\n";
    }
}

if(isset($_POST["Reload_Contact"])){

    

}