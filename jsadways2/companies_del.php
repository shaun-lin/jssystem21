<?php

	require_once dirname(__DIR__) .'/autoload.php';

	require_once __DIR__ .'/include/function.inc.php';

	$db = clone($GLOBALS['app']->db);

	$message = '';
	$sql2 = sprintf("DELETE FROM `companies` WHERE `id` = %d", $_GET['id']);
	$db->query($sql2);
	$sql2 = sprintf("DELETE FROM `companies_contact` WHERE `contact_agency` = %d", $_GET['id']);
	$db->query($sql2);
	$sql2 = sprintf("DELETE FROM `rel_media_companies` WHERE `companies_id` = %d", $_GET['id']);
    $db->query($sql2);
	ShowMessageAndRedirect('刪除媒體成功', 'companies_list.php', false);