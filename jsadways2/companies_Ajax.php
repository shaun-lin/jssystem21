<?php
    include('include/db.inc.php');
    //copanies 新、修、刪
    if(isset($_POST["Companies_contact"])){
        $Into_Lines = split(",", $_POST["Companies_contact"]);
        switch ($_POST["I_U"]) {
            case 0: //UPDATA
                $T_Sql_Info = "UPDATE `companies_contact` SET";
                $T_Sql_Info .= " `contact_title` ='" . $Into_Lines[1] ."'";
                $T_Sql_Info .= ",`contact_name` ='" . $Into_Lines[2] ."'";
                $T_Sql_Info .= ",`contact_tel` ='" . $Into_Lines[3] ."'";
                $T_Sql_Info .= ",`contact_email` ='" . $Into_Lines[4] ."'";
                $T_Sql_Info .= " WHERE `contact_id` ='" . $Into_Lines[0] ."';";
                $sqlCampaignStatus = $T_Sql_Info;
                $resultCampaign = mysql_query($sqlCampaignStatus);
                echo("Success");
                break;
            
            case 1: //Insert
                $T_Sql_Info = "INSERT INTO  `companies_contact` ( `contact_id` ,`contact_agency` ,`contact_title` ,`contact_name` ,`contact_tel` ,`contact_email`) VALUES (";
                $T_Sql_Info .= "NULL ,";
                $T_Sql_Info .= "'" . $_POST["id"] . "',";
                $T_Sql_Info .= "'"  . $Into_Lines[1] ."',";
                $T_Sql_Info .= "'"  . $Into_Lines[2] ."',";
                $T_Sql_Info .= "'"  . $Into_Lines[3] ."',";
                $T_Sql_Info .= "'"  . $Into_Lines[4] ."'";
                $T_Sql_Info .= ");";
                $sqlCampaignStatus = $T_Sql_Info;
                $resultCampaign = mysql_query($sqlCampaignStatus);
                break;

            case 2: //Delete
                $T_Sql_Info = "DELETE FROM `companies_contact` ";
                $T_Sql_Info .= " WHERE `contact_id` ='" . $Into_Lines[0] ."';";
                $sqlCampaignStatus = $T_Sql_Info;
                $resultCampaign = mysql_query($sqlCampaignStatus);
                echo("Success");
                break;
            default:
                # code...
                break;
       }
    }
    
    //重新讀取表格資料
    if(isset($_POST["Reload_Contact"])){
        $sqlCampaignStatus = "SELECT * FROM companies_contact WHERE companies_contact.contact_agency =". $_POST["id"];
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
    }
    // 比對資料是否有在資料庫
    if (isset($_POST["Check_Haved"])){
        $sql = "";
        switch ($_POST["T_N"]){
            case 0: //聯絡人資料
                $sql = "SELECT * FROM `companies_contact` WHERE `contact_agency`=".$_POST["id"] . "and `contact_name`='" . $_POST["Check_Haved"]  ."'";
                break;
            
            case 1: //公司名稱，新增資料用
                $sql = "SELECT * FROM `companies` WHERE `name`='". $_POST["Check_Haved"]  ."'";
                break;
            
            case 2: //修改公司名稱，在其他ID有無重複。
                $sql = $sql = "SELECT * FROM `companies` WHERE `name`='". $_POST["Check_Haved"]  ."' and `id` <>'" .$_POST["id"] . "'";
                break;
            
            default:
                # code...
                break;
        }
        //echo $sql;
        $result=mysql_query($sql);
        $nums=mysql_num_rows($result);
        if($nums > 0){
            echo "true";
        }else{
            echo "fales";
        }

    }

    
?>