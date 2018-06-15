<?
session_start();
require_once dirname(__DIR__) .'/autoload.php';
	include('include/db.inc.php');
	date_default_timezone_set('Asia/Taipei');
	if(isset($_GET["I_U"])){
		$I_U = $_GET["I_U"];
		// 新增公司資料
		if ($I_U == "0"){
			$Dates=time();
			$T_Sql_Info = "INSERT INTO  `companies` ( ";
			$T_Sql_Info .= "`id`, `name`, `name2`, `eng_name`, `tax_id`, `tel`, `fax`,`area_code`,`city_name`,`country_code`,`language`, `address`, `payinfo`, `paydays`, `refund`, `update_user`, `update_date`)";
			$T_Sql_Info .= " VALUES (";
    	    $T_Sql_Info .= "NULL ,";
			$T_Sql_Info .= "'" . $_POST["name"] ."',";
			$T_Sql_Info .= "'" . $_POST["name2"] ."',";
			$T_Sql_Info .= "'" . $_POST["eng_name"] ."',";
			$T_Sql_Info .= "'" . $_POST["tax_id"] ."',";
			$T_Sql_Info .= "'" . $_POST["tel"] ."',";
			$T_Sql_Info .= "'" . $_POST["fax"] ."',";
			$T_Sql_Info .= "'" . $_POST["area_code"] ."',";
			$T_Sql_Info .= "'" . $_POST["city_name"] ."',";
			$T_Sql_Info .= "'" . $_POST["country_code"] ."',";
			$T_Sql_Info .= "'" . $_POST["language"] ."',";
			$T_Sql_Info .= "'" . $_POST["address"] ."',";
			$T_Sql_Info .= "'" . $_POST["payinfo"] ."',";
			$T_Sql_Info .= "'" . $_POST["paydays"] ."',";
			$T_Sql_Info .= "'" . $_POST["refund"] ."',";
			$T_Sql_Info .= "'" . $_SESSION['name'] ."',";
			$T_Sql_Info .= "'" . $Dates ."'";
			$T_Sql_Info .= ");";
			$resultCampaign = mysql_query($T_Sql_Info);

			//echo $T_Sql_Info;
			$New_Sql_Info = "SELECT id FROM companies WHERE companies.name ='". $_POST["name"]."'"; 
			$resultCampaign = mysql_query($New_Sql_Info);
			$ids;
			while($Rows = mysql_fetch_array($resultCampaign)){$ids = $Rows['id'];}
			$Url = "Location: companies_edit.php?id=". $ids;
			//echo $Url;
			header("$Url");
			exit;
		}
		// 更新公司資料和媒體清單資料
		if ($I_U == "1"){
			$Dates=time();
			$T_Sql_Info = "UPDATE `companies` SET";
			$T_Sql_Info .= "`name` ='" . $_POST["name"] ."',";
			$T_Sql_Info .= "`name2` ='" . $_POST["name2"] ."',";
			$T_Sql_Info .= "`eng_name` ='" . $_POST["eng_name"] ."',";
			$T_Sql_Info .= "`tax_id` ='" . $_POST["tax_id"] ."',";
			$T_Sql_Info .= "`tel` ='" . $_POST["tel"] ."',";
			$T_Sql_Info .= "`fax` ='" . $_POST["fax"] ."',";
			$T_Sql_Info .= "`area_code` ='" . $_POST["area_code"] ."',";
			$T_Sql_Info .= "`city_name` ='" . $_POST["city_name"] ."',";
			$T_Sql_Info .= "`country_code`='" . $_POST["country_code"] ."',";
			$T_Sql_Info .= "`language`='" . $_POST["language"] ."',";
			$T_Sql_Info .= "`address` ='" . $_POST["address"] ."',";
			$T_Sql_Info .= "`payinfo` ='" . $_POST["payinfo"] ."',";
			$T_Sql_Info .= "`paydays` ='" . $_POST["paydays"] ."',";
			$T_Sql_Info .= "`refund` ='" . $_POST["refund"] ."',";
			$T_Sql_Info .= "`update_user` ='" . $_SESSION['name'] ."'";
			$T_Sql_Info .= " WHERE `id` ='" . $_GET["id"] ."';";
			mysql_query($T_Sql_Info);

			//echo $T_Sql_Info;
			// 移除公司在rel_media_companies的所有資料
			$T_Sql_Info2 = "DELETE FROM `rel_media_companies` ";
            $T_Sql_Info2 .= " WHERE `companies_id` ='" . $_GET["id"] ."';";
            mysql_query($T_Sql_Info2);
			
			// 將所選取的資料全部新增進去
			$id = $_GET["id"];
			$Items = $_POST["tagtext2"];
			if(isset($Items)){
			$T_Sql_Info3 = "INSERT INTO  `rel_media_companies` ( ";
			$T_Sql_Info3 .= "`companies_id`, `medias_id`)";
			$T_Sql_Info3 .= " VALUES ";
			foreach ($Items as $value) {
				$T_Sql_Info3 .= "('". $id ."','". $value ."'),";
				}
			$T_Sql_Info3 = trim($T_Sql_Info3, ",");
			}
			
			mysql_query($T_Sql_Info3);
			//echo $T_Sql_Info3;
			//echo $_POST["Companies_id"];

			ShowMessageAndRedirect('媒體公司修改成功', 'companies_list.php' , false);
			
		}


	}


?>