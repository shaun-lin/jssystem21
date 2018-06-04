<?php

	require_once dirname(__DIR__) .'/autoload.php';

	require_once __DIR__ .'/include/function.inc.php';

	$db = clone($GLOBALS['app']->db);

	$message = '';
	$sql2 = sprintf("DELETE FROM `items` WHERE `id` = %d", $_GET['id']);
	$sql3 = sprintf("DELETE FROM `rel_items_type` WHERE `items_id`=%d", $_GET['id']);

	$db->query($sql2);
	$db->query($sql3);
	ShowMessageAndRedirect('刪除媒體成功', 'item_list.php', false);