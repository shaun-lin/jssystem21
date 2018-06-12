<?php

require_once dirname(__DIR__) . '/autoload.php';

include 'include/db.inc.php';
$db = clone ($GLOBALS['app']->db);
$TypeItem = 'GAME APP';
$website = '【實況主】詩涼子SHIRYOUKO STUDIO';
$totalprice = $_POST['totalprice'];

$TypeItem = null;
$TypeItem = $_POST['SelectType'];
$autoSerialNumberA = autoSerialNumber();
$autoSerialNumberB = autoSerialNumber();

$cp_id = $_GET['id'];
$media_id = $_GET['mediaid'];
$item_id = $_GET['itemid'];
$mtype_name = $_GET['mtypename'];
$mtype_number = $_GET['mtypenumber'];
$mtype_id = $_GET['mtypeid'];

$total_days = 0;
$days = array(0, 0, 0, 0, 0);
for ($i = 1; $i <= 5; $i++) {
    if ($_POST['date' . $i] != null) {
        $date[$i] = mktime(0, 0, 0, substr($_POST['date' . $i], 0, 2), substr($_POST['date' . $i], 3, 2), substr($_POST['date' . $i], 6, 4));
        $total_days++;
        $days[$i - 1] = 1;
    } else {
        $date[$i] = 0;
    }
}
$sqlmedia = "SELECT * FROM media WHERE id=83";
$resultmedia = mysql_query($sqlmedia);
$rowmedia = mysql_fetch_array($resultmedia);
$profit = ($_POST['totalprice'] * $rowmedia['profit']) / 100;
if ($_GET['cue'] == 2) {
    $a = $_GET['media2'];
    $a0 = $_GET['mediaid'];

}

$sql2 = 'INSERT INTO media164(campaign_id,
item_seq,
cue,
website,
date1,
date2,
date3,
date4,
date5,
date6,
date7,
date8,
date9,
date10,
days,
days1,
days2,
days3,
days4,
days5,
a,
a0,
a4,
totalprice,
others,
items2,
items3) VALUES(' . $_GET['id'] . ',
' . $autoSerialNumberA . ',
' . $_GET['cue'] . ',
"' . $_GET['media_name'] . '",
' . $date[1] . ',
' . $date[1] . ',
' . $date[2] . ',
' . $date[2] . ',
' . $date[3] . ',
' . $date[3] . ',
' . $date[4] . ',
' . $date[4] . ',
' . $date[5] . ',
' . $date[5] . ',
' . $total_days . ',
' . $days[0] . ',
' . $days[1] . ',
' . $days[2] . ',
' . $days[3] . ',
' . $days[4] . ',
"' . $a . '",
"' . $a0 . '",
' . $totalprice . ',
' . $totalprice . ',
"' . $_POST['others'] . '",
"' . $TypeItem . '",
"' . $_POST['SelectSystem'] . '")';
mysql_query($sql2);
AddMediaMapping('media164', $_GET['id'], mysql_insert_id());

$item_id2=mysql_insert_id();
$sql3 = "INSERT INTO `cp_detail`( `cp_id`, `media_id`, `comp_id`, `item_id`, `mtype_name`, `mtype_number`, `mtype_id`,`item_seq`,`cue`)
	VALUES ('" . $cp_id . "','" . $media_id . "','0','" . $item_id . "','" . $mtype_name . "','" . $mtype_number . "','" . $item_id2 . "','" . $autoSerialNumberA . "','1')";
mysql_query($sql3);
//echo $sql2;
if ($_POST['samecue'] == 1) {
    $sqlnew = "SELECT * FROM media164 ORDER BY id DESC LIMIT 1;";
    $resultnew = mysql_query($sqlnew);
    $rownew = mysql_fetch_array($resultnew);

    $a = $_GET['media'];
    $a0 = $rownew['id'];
    $sql2 = 'INSERT INTO media164(campaign_id,
item_seq,
cue,
website,
date1,
date2,
date3,
date4,
date5,
date6,
date7,
date8,
date9,
date10,
days,
days1,
days2,
days3,
days4,
days5,
a,
a0,
a4,
totalprice,
others,
items2,
items3) VALUES(' . $_GET['id'] . ',
' . $autoSerialNumberB . ',
2,
"' . $_GET['media_name'] . '",
' . $date[1] . ',
' . $date[1] . ',
' . $date[2] . ',
' . $date[2] . ',
' . $date[3] . ',
' . $date[3] . ',
' . $date[4] . ',
' . $date[4] . ',
' . $date[5] . ',
' . $date[5] . ',
' . $total_days . ',
' . $days[0] . ',
' . $days[1] . ',
' . $days[2] . ',
' . $days[3] . ',
' . $days[4] . ',
"' . $a . '",
"' . $a0 . '",
' . $totalprice . ',
' . $totalprice . ',
"' . $_POST['others'] . '",
"' . $TypeItem . '",
"' . $_POST['SelectSystem'] . '")';
    mysql_query($sql2);
    AddMediaMapping('media164', $_GET['id'], mysql_insert_id());

    $item_id1=mysql_insert_id();
    $sql4 = "INSERT INTO `cp_detail`( `cp_id`, `media_id`, `comp_id`, `item_id`, `mtype_name`, `mtype_number`, `mtype_id`,`item_seq`,`cue`)
	VALUES ('" . $cp_id . "','" . $media_id . "','0','" . $item_id . "','" . $mtype_name . "','" . $mtype_number . "','" . $item_id1 . "','" . $autoSerialNumberB . "','2')";
    mysql_query($sql4);
}

$sql1 = "SELECT * FROM campaign WHERE id= " . $_GET['id'];
$result1 = mysql_query($sql1);
$row1 = mysql_fetch_array($result1);
if ($row1['status'] == 5) {
    $sql2 = 'INSERT INTO campaignstatus2(name,data,times,campaignid) VALUES("' . $_SESSION['username'] . '","新增詩涼子SHIRYOUKO STUDIO媒體",' . time() . ',' . $_GET['id'] . ')';
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
