<?php

require_once dirname(__DIR__) . '/autoload.php';

include 'include/db.inc.php';

$db = clone ($GLOBALS['app']->db);
$TypeItem = null;
$TypeItem = $_POST['SelectType'];
$member = $_POST["member"];
$unitcost = $_POST["unitcost"];

$cp_id = $_GET['id'];
$media_id = $_GET['mediaid'];
$item_id = $_GET['itemid'];
$mtype_name = $_GET['mtypename'];
$mtype_number = $_GET['mtypenumber'];
$mtype_id = $_GET['mtypeid'];
$unit = $_POST["unit"];
$other = $_POST["other"];
if ($_GET['cue'] == 2) {
    $a = $_GET['media2'];
    $a0 = $_GET['mediaid'];
    $a4 = $_POST['a4'];
}
for ($i = 0; $i < count($member); $i++) {
    $totalprice = $unitcost[$i] * $unit[$i];
    if ($_GET['cue'] == 2) {
        $a = $_GET['media2'];
        $a0 = $_GET['mediaid'];
        $a4 = $totalprice;
    }
    $autoSerialNumberA = autoSerialNumber();
    $sql2 = 'INSERT INTO media161(campaign_id,
item_seq,
cue,
website,
itemname,
price,
quantity,
totalprice,
other,
times,
a4,
a0,
a,
items2,
items3) VALUES(' . $_GET['id'] . ',
' . $autoSerialNumberA . ',
' . $_GET['cue'] . ',
"廣告素材",
"' . $member[$i] . '",
"' . $unitcost[$i] . '",
"' . $unit[$i] . '",
"' . $totalprice . '",
"' . $other[$i] . '",
' . time() . ',
"' . $a4 . '",
"' . $a0 . '",
"' . $a . '",
"' . $TypeItem . '",
"' . $_POST['SelectSystem'] . '")';

    mysql_query($sql2);
    AddMediaMapping('media161', $_GET['id'], mysql_insert_id());
    
    $item_id2=mysql_insert_id();
    $sql3 = "INSERT INTO `cp_detail`( `cp_id`, `media_id`, `comp_id`, `item_id`, `mtype_name`, `mtype_number`, `mtype_id`,`item_seq`,`cue`)
    VALUES ('" . $cp_id . "','" . $media_id . "','0','" . $item_id . "','" . $mtype_name . "','" . $mtype_number . "','" . $item_id2 . "','" . $autoSerialNumberA . "','1')";
    $db->query($sql3);
    if ($_POST['samecue'] == 1) {
        $sqlnew = "SELECT * FROM media161 ORDER BY id DESC LIMIT 1;";
        $resultnew = mysql_query($sqlnew);
        $rownew = mysql_fetch_array($resultnew);
        $a0 = $rownew['id'];
        $totalprice = $unitcost[$i] * $unit[$i];
        $autoSerialNumberB = autoSerialNumber();
        $sql2 = 'INSERT INTO media161(campaign_id,
item_seq,
cue,
website,
itemname,
price,
quantity,
totalprice,
other,
times,
a4,
a0,
a,
items2,
items3) VALUES(' . $_GET['id'] . ',
"' . $autoSerialNumberB . '",
2,
"廣告素材",
"' . $member[$i] . '",
"' . $unitcost[$i] . '",
"' . $unit[$i] . '",
"' . $totalprice . '",
"' . $other[$i] . '",
' . time() . ',
"' . $totalprice . '",
"' . $a0 . '",
"18",
"' . $TypeItem . '",
"' . $_POST['SelectSystem'] . '")';
        mysql_query($sql2);
        AddMediaMapping('media161', $_GET['id'], mysql_insert_id());

        $item_id1=mysql_insert_id();
        $sql4 = "INSERT INTO `cp_detail`( `cp_id`, `media_id`, `comp_id`, `item_id`, `mtype_name`, `mtype_number`, `mtype_id`,`item_seq`,`cue`)
			VALUES ('" . $cp_id . "','" . $media_id . "','0','" . $item_id . "','" . $mtype_name . "','" . $mtype_number . "','" . $item_id1 . "','" . $autoSerialNumberB . "','2')";
        $db->query($sql4);
    }
}

/*if($_POST['samecue']==1){
for($i=0;$i<count($member);$i++){
$totalprice=$unitcost[$i]*$unit[$i];
$sql2='INSERT INTO media161(campaign_id,cue,website,itemname,price,quantity,totalprice,other,times) VALUES('.$_GET['id'].',2,"廣告素材","'.$member[$i].'","'.$unitcost[$i].'","'.$unit[$i].'","'.$totalprice.'","'.$other[$i].'",'.time().')';
mysql_query($sql2);
}
}*/
$sql1 = "SELECT * FROM campaign WHERE id= " . $_GET['id'];
$result1 = mysql_query($sql1);
$row1 = mysql_fetch_array($result1);
if ($row1['status'] == 5) {
    $sql2 = 'INSERT INTO campaignstatus2(name,data,times,campaignid) VALUES("' . $_SESSION['username'] . '","新增廣告素材媒體",' . time() . ',' . $_GET['id'] . ')';
    mysql_query($sql2);
}

$goon = GetVar('goon');

if ($goon == "Y") {

    $arrItems = array();
    $arrItems[] = array("key" => "result", "name" => "OK");

    echo json_encode($arrItems);
} else {
    ShowMessageAndRedirect('新增媒體成功', 'campaign_view.php?id=' . $_GET['id'], false);
}
