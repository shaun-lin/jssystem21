<?php
	
    $editMode = true;

    session_start();
    include('include/db.inc.php');
    include 'mtype_CPM_definition.php';

    $sql1 = sprintf("SELECT * FROM `campaign` WHERE `id` = %d;", $_GET['campaign']);
    $result1 = mysql_query($sql1);
    $row1 = mysql_fetch_array($result1);

    $sql2 = sprintf("SELECT * FROM `media%d` WHERE `id` = %d;", $mediaOrdinal, $_GET['id']);
    $result2 = mysql_query($sql2);
    $row2 = mysql_fetch_array($result2);

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>編輯媒體</title>
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
                                <h2><i class="icon-edit"></i> <?php echo $row1['name']; ?>-編輯媒體-<?php echo $mediaName; ?></h2>

                            </div>
                            <div class="box-content">
                                <form class="form-horizontal" action="mtype_CPM_edit2.php?campaign=<?php echo $_GET['campaign']; ?>&id=<?php echo $_GET['id']; ?>&cue=<?php echo $_GET['cue']; ?>&media=<?php echo $_GET['media']; ?>" method="post">
                                    <?php require __DIR__ .'/media129_add_edit.php'; ?>
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
