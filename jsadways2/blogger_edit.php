<?php
	
	$editMode = true;
	require_once dirname(__DIR__) .'/autoload.php';

	$isGrantSubSupervise = IsPermitted('backend_blogger', null, 'sub-supervise');
	$isGrantEdit = IsPermitted('backend_blogger', 'blogger_list.php', Permission::ACL['backend_blogger_edit']);
	$isGrantViewAccount = IsPermitted('backend_blogger', null, Permission::ACL['backend_blogger_view_account']);
	
	$objBlogger = CreateObject('Blogger', GetVar('id'));
	$objTag = CreateObject('Tag', null, 'blogger');
	$objCategory = CreateObject('Category');

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>【<?= $GLOBALS['env']['flag']['name']; ?>】<?= IsId($objBlogger->getId()) ? '編輯' : '新增'; ?>寫手</title>
		<?php include("public/head.php"); ?>
		<?php include("public/js.php"); ?>
	</head>
	<body>
		<?php include("public/topbar.php"); ?>
		
		<div class="container-fluid">
			<div class="row-fluid">
				<?php include("public/left.php"); ?>
				
				<?php require_once __DIR__ .'/blogger_fields.php'; ?>
			</div>	
			<hr/>

			<?php include("public/footer.php"); ?>
		</div>
	</body>
</html>