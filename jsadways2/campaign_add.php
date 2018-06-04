<?php
    
    // 2018-02-22 (Jimmy): 傑思jsadways2/campaign_add.php, 香港jsadways2hk/campaign_add.php, 豐富媒體jsadways2ff/campaign_add.php 共用此檔案
    require_once dirname(__DIR__) .'/autoload.php';
    
    CreateNativeDbConnector();

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>【<?= $GLOBALS['env']['flag']['name']; ?>】新增案件</title>
        <?php include("public/head.php"); ?>
        <script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="js/jquery.xml2json.js"></script>
        <script type="text/javascript" src="js/JSLINQ.js"></script>
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

        <?php include("public/js.php"); ?>
    </body>
</html>