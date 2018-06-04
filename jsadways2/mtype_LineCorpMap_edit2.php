<?php

    require_once dirname(__DIR__) .'/autoload.php';

	require_once __DIR__ .'/include/function.inc.php';
	
	$db = clone($GLOBALS['app']->db);
	require_once 'mtype_LineCorpMap_definition.php';

	for ($i=1; $i<=10; $i++) {
		if ($_POST['date'. $i] != NULL) {
			$date[$i] = mktime (0, 0, 0, substr($_POST['date'.$i], 0, 2), substr($_POST['date'. $i], 3, 2), substr($_POST['date'. $i], 6, 4));
		} else {
			$date[$i] = 0;
		}
	}

	if ($_GET['cue'] == 2) {
		$a0 = $_GET['mediaid'];
		$a1 = $_POST['a1'];
		$a2 = $_POST['a2'];
		$a3 = $_POST['a3'];
		$a4 = $_POST['a4'];
		
        $sqlMedia = sprintf("SELECT * FROM `media` WHERE `id` = %d;", $mediaOrdinal);
		$db->query($sqlMedia);
		$rowMedia = $db->next_record();

		$profit = ($_POST['totalprice'] * $rowmedia['profit']) / 100;
		if ($a3 < $profit) {
			$a5 = '1';
		}
	}
	
    $sql1 = sprintf("SELECT * FROM `campaign` WHERE `id` = %d;", $_GET['campaign']);
	$db->query($sql1);
	$row1 = $db->next_record();

	if ($row1['status'] == 5) {
		$sql3 = sprintf("SELECT * FROM `media%d` WHERE `id` = %d;", $mediaOrdinal, $_GET['id']);
		$db->query($sql3);
		$row3 = $db->next_record();

        $data = '';
		if ($row3['totalprice'] != $_POST['totalprice']) {
			$data = $mediaName .'媒體總金額由'. $row3['totalprice'] .'改成'. $_POST['totalprice'] .'<br />';
		}

        for ($i=1; $i<=10; $i++) {
			if ($row3['date'. $i] != $date[$i]) {
				$data .= $mediaName .'媒體走期'. ((int)(($i + 1) / 2)) .'由'. date('Ymd', $row3['date'. $i]) .'改成'. date('Ymd', $date[$i]) .'<br />';
			}
		}
        
		if ($data == NULL) {
			$data = $mediaName .'媒體我也不知道改了什麼，有可能是版位喔';
		}

		$sql2 = "INSERT INTO `campaignstatus2` (`name`, `data`, `times`, `campaignid`) 
				VALUES (". $db->quote($_SESSION['username']) .", ". $db->quote($data) .", ". time() .", ". ((int)$_GET['campaign']) .");";
		$db->query($sql2);
	}

    $updateData = [
        'channel' => GetVar('channel', ''),
        'phonesystem' => GetVar('phonesystem', ''),
        'position' => $_POST['position'],
        'format1' => $_POST['format1'],
        'format2' => $_POST['format2'],
        'wheel' => $_POST['wheel'],
        'date1' => $date[1],
        'date2' => $date[2],
        'date3' => $date[3],
        'date4' => $date[4],
        'date5' => $date[5],
        'date6' => $date[6],
        'date7' => $date[7],
        'date8' => $date[8],
        'date9' => $date[9],
        'date10' => $date[10],
        'days' => $_POST['days'],
        'due' => $_POST['due'],
        'quantity' => $_POST['quantity'],
        'totalprice' => $_POST['totalprice'],
        'times' => time(),
        'others' => $_POST['others'],
        'days1' => $_POST['days1'],
        'days2' => $_POST['days2'],
        'days3' => $_POST['days3'],
        'days4' => $_POST['days4'],
        'days5' => $_POST['days5'],
        'price1' => $_POST['price1'],
        'price2' => $_POST['price2'],
        'price3' => $_POST['price3'],
        'price4' => $_POST['price4'],
        'price5' => $_POST['price5'],
        'totalprice1' => $_POST['totalprice1'],
        'totalprice2' => $_POST['totalprice2'],
        'totalprice3' => $_POST['totalprice3'],
        'totalprice4' => $_POST['totalprice4'],
        'totalprice5' => $_POST['totalprice5'],
        'click1' => $_POST['click1'],
        'click2' => $_POST['click2'],
        'click3' => $_POST['click3'],
        'click4' => $_POST['click4'],
        'click5' => $_POST['click5'],
        'a1' => $_POST['a1'],
        'a2' => $_POST['a2'],
        'a3' => $_POST['a3'],
        'a4' => $_POST['a4'],
        'a5' => $a5,
    ];

    $fieldsStatement = [];
    foreach ($updateData as $dataName => $dataVar) {
        if (is_numeric($dataVar)) {
            $fieldsStatement[] = "`{$dataName}` = ". $dataVar;
        } else {
            $fieldsStatement[] = "`{$dataName}` = ". $db->quote($dataVar);
        }
    }

	if ($_POST['gearing'] == 1) {
		$sql_campaign = sprintf("SELECT * FROM `media%d` WHERE `campaign_id` = %d;", $mediaOrdinal, $_GET['campaign']);
		
		$db->query($sql_campaign);
		$campaign_row = $db->get_num_rows();

		if ($campaign_row == 2) {
			$sql2 = "UPDATE `media". $mediaOrdinal ."` SET ". implode(', ', $fieldsStatement) ." WHERE `campaign_id`	= ". ((int)$_GET['campaign']) .";";
			$db->query($sql2);
		}
	}
	
	$sql2 = "UPDATE `media". $mediaOrdinal ."` SET ". implode(', ', $fieldsStatement) ." WHERE `id`	= ". ((int)$_GET['id']) .";";
	$db->query($sql2);

	ShowMessageAndRedirect('修改媒體成功', 'campaign_view.php?id='.$_GET['campaign'], false);
