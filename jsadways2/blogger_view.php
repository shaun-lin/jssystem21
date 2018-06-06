<?php
	
	require_once dirname(__DIR__) .'/autoload.php';

	$isGrantSubSupervise = IsPermitted('backend_blogger', null, 'sub-supervise');
	$isGrantEdit = IsPermitted('backend_blogger', null, Permission::ACL['backend_blogger_edit']);
	$isGrantViewAccount = IsPermitted('backend_blogger', null, Permission::ACL['backend_blogger_view_account']);

	$objBlogger = CreateObject('Blogger', GetVar('id'));
	
	if (!IsId($objBlogger->getId())) {
		RedirectLink('blogger_list.php');
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>【<?= $GLOBALS['env']['flag']['name']; ?>】寫手資料</title>
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