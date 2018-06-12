
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>【傑思】編輯媒體</title>
        <?php include("public/head.php"); ?>
    </head>

    <body>
        <?php include("public/topbar.php"); ?>
        
        <div class="container-fluid">
            <div class="row-fluid">
                <?php include("public/left.php"); ?>

                <noscript>
                    <div class="alert alert-block span10">
                        <h4 class="alert-heading">Warning!</h4>
                        <p>You need to have <a href="http://en.wikipedia.org/wiki/JavaScript" target="_blank">JavaScript</a> enabled to use this site.</p>
                    </div>
                </noscript>

                <div id="content" class="span10">
                    <div class="row-fluid sortable">
                        <div class="box span12">
                            <div class="box-header well" data-original-title>
                                <h2><i class="icon-edit"></i> <?= $row1['name']; ?>-編輯媒體-<?= $mediaName; ?></h2>

                            </div>
                            <div class="box-content">
                                <?php
    
                                $editMode = true;

                                session_start();
                                include('include/db.inc.php');
                                include('mtype_CPC_definition.php');

                                $sql1 = sprintf("SELECT * FROM `campaign` WHERE `id` = %d;", $_GET['campaign']);
                                $result1 = mysql_query($sql1);
                                $row1 = mysql_fetch_array($result1);

                                $sql2 = sprintf("SELECT * FROM `media%d` WHERE `id` = %d;", $mediaOrdinal, $_GET['id']);
                                $result2 = mysql_query($sql2);
                                $row2 = mysql_fetch_array($result2);
                                echo $sql2;

                            ?>
                                <form class="form-horizontal" action="mtype_CPC_add_edit2.php?campaign=<?= $_GET['campaign']; ?>&id=<?= $_GET['id']; ?>&cue=<?= $_GET['cue']; ?>&media=<?= $_GET['media']; ?>" method="post">
                                    <?php require ('mtype_CPC_add_edit.php'); ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr>
            <?php include("public/footer.php"); ?>
        </div>

        <?php include("public/js.php"); ?>
    </body>
</html>
