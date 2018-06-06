<?php
	
	require_once dirname(__DIR__) .'/autoload.php';

    $isModuleManager = IsPermitted('backend_blogger', 'blogger_list.php', 'sub-supervise');

    $db = clone($GLOBALS['app']->db);
    
    $objBloggerBank = CreateObject('BloggerBank');

    $rowsBloggerBank = $objBloggerBank->searchAll("`states` = 1 AND `shared` = 1");

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>【<?= $GLOBALS['env']['flag']['name']; ?>】管理共用帳戶</title>
		<?php include("public/head.php"); ?>
		<?php include("public/js.php"); ?>
	</head>
	<body>
		<?php include("public/topbar.php"); ?>
		
		<div class="container-fluid">
			<div class="row-fluid">
				<?php include("public/left.php"); ?>
				
				<div id="content" class="span10">
					<div class="row-fluid">
						<div class="box span12">
							<div class="box-header well " data-original-title>
								<h2><i class="fa fa-usd"></i> 管理共用帳戶</h2>
                            </div>

                            <div class="box-content">
                                <link rel="stylesheet" type="text/css" href="js/jquery-ui.css">
                                <div style="display: block; width: 100%; margin: -10px 0 12px -10px; padding-right: 20px; background-color: #f6f6f6; height: 40px; box-shadow: 1px 1px #eee;">
                                    <a href="#" onclick="EditSharedBank(null);" style="float: left; display: block; height: 20px; margin-left: 16px; margin-top: 10px; font-size: 1.2em; text-decoration: none; color: #008900; font-weight: bold; text-shadow: none;">
                                        <i class="fa fa-plus-circle"></i> 新增帳戶
                                    </a>

                                    <a href="#" onclick="DelSharedBank(null);" style="float: left; display: block; height: 20px; margin-left: 16px; margin-top: 10px; font-size: 1.2em; text-decoration: none; color: #d41e24; font-weight: bold; text-shadow: none;">
                                        <i class="fa fa-trash"></i> 刪除帳戶
                                    </a>

                                    <a href="blogger_list.php" style="float: right; display: block; height: 20px; margin-top: 10px; font-size: 1.2em; text-decoration: none; color: #1919ff; font-weight: bold; text-shadow: none;">
										<i class="fa fa-users"></i> 回寫手列表
									</a>
                                </div>

                                <table class="table table-striped table-bordered bootstrap-datatable datatable">
                                    <thead>
                                        <tr>
                                            <th class="ui-state-default" style="width: 30px;">勾選</th>
                                            <th class="ui-state-default">戶名</th>
                                            <th class="ui-state-default">銀行</th>
                                            <th class="ui-state-default">帳號</th>
                                            <th class="ui-state-default" style="width: 90px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="content_list">
                                        <? foreach ($rowsBloggerBank as $itemBank) : ?>
                                            <tr>
                                                <td><input type="checkbox" class="shared-account-id" value="<?= $itemBank['id']; ?>" onclick="AddToQueue(this);"></td>
                                                <td><?= $itemBank['bankUserName']; ?></td>
                                                <td><?= is_numeric($itemBank['bankCode']) ? sprintf('(%03d)', $itemBank['bankCode']) : ''; ?> <?= $itemBank['bankName']; ?></td>
                                                <td>
                                                    <?= $itemBank['bankAC']; ?>
                                                </td>
                                                <td>
                                                    <a class="btn btn-info" style="font-size: 1.5em; margin-top: 3px;" onclick="EditSharedBank(<?= $itemBank['id']; ?>);">
                                                        <i class="fa fa-pencil"></i> <span style="font-size: .6em;">編輯</span>
                                                    </a>
                                                    <br/>
                                                    <a class="btn btn-danger" style="font-size: 1.5em; margin-top: 3px;" onclick="if (window.confirm('確定要刪除')) { DelSharedBank(<?= $itemBank['id']; ?>, this); }">
                                                        <i class="fa fa-trash-o"></i> <span style="font-size: .6em;">刪除</span>
                                                    </a>
                                                </td>
                                            </tr>
                                        <? endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
			</div>	
			<hr/>

			<?php include("public/footer.php"); ?>
        </div>

        <div id="panel_block_bank" style="display: none;">
            <center>
                <div style="background-color: white; padding: 30px;">
                    <div style="position: absolute; font-size: 2em; top: 0; left: 0; border: 1px solid #ccc; border-radius: 12px; margin-top: 2px; margin-left: 2px; padding: 0 3px;">
                        <a href="javascript:CloseBlock();"><i class="fa fa-times" style="color: #ccc;"></i></a>
                    </div>

                    <div style="font-size: 1em;">
                        <h3 style="text-align: right; color: #676767;">
                            <i class="fa fa-usd"></i>&nbsp;共用帳戶
                        </h3>
                    </div>

                    <div style="font-size: 8em;" id="loader">
                        <i class="fa fa-spin fa-refresh"></i>
                    </div>

                    <form id="shared_bank_form" style="display: none; margin-bottom: 0; border-radius: 3px;">
                        <table class="table table-bordered table-striped influencer-bank-account" style="background-color: rgba(0, 0, 0, 0.03);">
                            <tr>
                                <td style="width: 180px;"><h4>銀行別</h4></td>
                                <td>
                                    <input type="text" id="bankName" name="bankFields[bankName]" />
                                </td>
                            </tr>
                            <tr>
                                <td><h4>銀行代號</h4></td>
                                <td>
                                    <input type="text" id="bankCode" name="bankFields[bankCode]" />
                                </td>
                            </tr>
                            <tr>
                                <td><h4>銀行帳號</h4></td>
                                <td>
                                    <input type="text" id="bankAC" name="bankFields[bankAC]" />
                                </td>
                            </tr>
                            <tr>
                                <td><h4>銀行檢查碼</h4></td>
                                <td>
                                    <input type="text" id="bankCheckCode" name="bankFields[bankCheckCode]" />
                                </td>
                            </tr>
                            <tr>
                                <td><h4>戶名</h4></td>
                                <td>
                                    <input type="text" id="bankUserName" name="bankFields[bankUserName]" />
                                </td>
                            </tr>
                            <tr>
                                <td><h4>戶名身份證字號</h4></td>
                                <td>
                                    <input type="text" id="bankIdNum" name="bankFields[bankIdNum]" />
                                    <input type="hidden" id="id" name="bankFields[id]" />
                                    <input type="hidden" id="blogger_id" name="bankFields[blogger_id]" />
                                </td>
                            </tr>
                        </table>
                        <br/>
                        <a class="btn btn-info" style="float: right;" href="javascript:SaveSharedBank();"><i class="fa fa-floppy-o"></i>  儲存</a>
                    </form>
                    <br/>
                </div>
            </center>
        </div>
        
        
        <script src="../js/jquery.blockUI-2.70.0.js"></script>
        <script>
            function CloseBlock()
            {
                $.unblockUI();
            }

            function OpenBlock()
            {
                $('#panel_block_bank').find('input').each(function() {
                    $(this).val('');
                });

                $('#panel_block_bank').find('div#loader').show();
                $('#panel_block_bank').find('form#shared_bank_form').hide();

                $.blockUI({
                    message: $('#panel_block_bank'),
                    css: {
                        top: '10%',
                        left: '20%',
                        width: '60%',
                        background: 'none',
                        border: 'none',
                    }
                });
            }

            function EditSharedBank(bankId)
            {
                if (bankId) {
                    $.ajax({
                        url: 'blogger_action.php?method=load_shared_bank',
                        type: 'POST',
                        data: {bank_id: bankId},
                        beforeSend: function() {
                            OpenBlock();
                        },
                        success: function(feedback) {
                            if ('success' in feedback && feedback.success) {
								for (var idx in feedback.data) {
                                    $('#panel_block_bank').find('#'+ idx).val(feedback.data[idx]);
                                }

                                $('#panel_block_bank').find('div#loader').hide();
                                $('#panel_block_bank').find('form#shared_bank_form').show();
                                return;
							} else if ('message' in feedback) {
                                alert(feedback.message);
                                CloseBlock();
                                return;
							}
                        },
                        error: function() {
                            alert('發生錯誤');
                            CloseBlock();
                        }
                    })
                } else {
                    OpenBlock();
                    $('#panel_block_bank').find('div#loader').hide();
                    $('#panel_block_bank').find('form#shared_bank_form').show();
                }
            }

            function SaveSharedBank()
            {
                $.ajax({
                    url: 'blogger_action.php?method=save_shared_bank',
                    type: 'POST',
                    data: $('#panel_block_bank').find('form#shared_bank_form').serialize(),
                    beforeSend: function() {
                        $('#panel_block_bank').find('div#loader').show();
                        $('#panel_block_bank').find('form#shared_bank_form').hide();
                    }, 
                    success: function(feedback) {
                        if ('success' in feedback && feedback.success) {
                            window.location.reload();
                            return;
                        } else if ('message' in feedback) {
                            alert(feedback.message);
                        }

                        $('#panel_block_bank').find('div#loader').hide();
                        $('#panel_block_bank').find('form#shared_bank_form').show();
                    }, 
                    error: function() {
                        $('#panel_block_bank').find('div#loader').hide();
                        $('#panel_block_bank').find('form#shared_bank_form').show();
                        alert('發生錯誤');
                    }
                });
            }

            function DelSharedBank(bankId)
            {
                if (bankId) {
                    $.ajax({
                        url: 'blogger_action.php?method=delete_shared_bank',
                        type: 'POST',
                        data: {bank_id: bankId},
                        success: function(feedback) {
                            if ('success' in feedback && feedback.success) {
                                window.location.reload();
                                return;
                            } else if ('message' in feedback) {
                                alert(feedback.message);
                            }
                        }, 
                        error: function() {
                            alert('發生錯誤');
                        }
                    });
                } else {
                    if (delQueueLength > 0) {
                        if (confirm('確定要刪除已勾選的共用帳戶')) {
                            DelSharedBank(delQueueData);
                        }
                    } else {
                        alert('請勾選要刪除的共用帳戶');
                    }
                }
            }

            function AddToQueue(bankChecker)
            {
                if (bankChecker.checked) {
                    delQueueData[bankChecker.value] = bankChecker.value;
                    delQueueLength++;
                } else {
                    delete delQueueData[bankChecker.value];
                    delQueueLength--;
                }
            }

            var delQueueData = {};
            var delQueueLength = 0;
        </script>
	</body>
</html>