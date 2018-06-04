<?php

    // 2018-02-22 (Jimmy): 傑思jsadways2/campaign_edit.php, 香港jsadways2hk/campaign_edit.php, 豐富媒體jsadways2ff/campaign_edit.php 共用此檔案
    require_once dirname(__DIR__) .'/autoload.php';
    
    CreateNativeDbConnector();

    $editMode = true;

    $campaignId = GetVar('id');
    if (!IsId($campaignId)) {
        RedirectLink('campaign_list.php');
    }

    $sql3 = sprintf("SELECT * FROM campaign WHERE id = %d;", $campaignId);
    $result3 = mysql_query($sql3);
    $row3 = mysql_fetch_array($result3);

    if (empty($row3) || !IsId($row3['id'])) {
        RedirectLink('campaign_list.php');
    }
    
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>【<?= $GLOBALS['env']['flag']['name']; ?>】編輯案件</title>
        <?php include("public/head.php"); ?>
        <?php include("public/js.php"); ?>
        <?php require_once __DIR__ .'/campaign_add_edit_common.php'; ?>
    </head>
    <body>
        <?php include("public/topbar.php"); ?>

        <div class="container-fluid">
            <div class="row-fluid">
                <?php include("public/left.php"); ?>
                
                <div id="content" class="span10">
                    <?php require_once __DIR__ .'/campaign_add_edit_fields.php'; ?>
                </div>
            </div>
            <hr/>

            <?php include("public/footer.php"); ?>
        </div>
        
        <script>
            <? if ($row3['date11'] > $row3['date22']) : ?>
                $(document).ready(function() {
                    setTimeout(function() {
                        alert('警告！到期日期不能小於起始日期');
                        $('#date2').focus();
                    }, 1000);
                });
            <? endif; ?>
        </script>
    </body>
</html>