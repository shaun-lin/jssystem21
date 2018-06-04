<?php

	// 2018-03-14 (Jimmy): 傑思jsadways2/media_list.php, 香港jsadways2hk/media_list.php, 豐富媒體jsadways2ff/media_list.php 共用此檔案
	require_once dirname(__DIR__) .'/autoload.php';
	
	$mediaGroup = [];

	$objMedia = CreateObject('Medias');
	$meidaOrdinal = GetVar('media');
    $sizeformatConditions =IsId($meidaOrdinal) ? sprintf('`id` = %d', $meidaOrdinal) : 'id IN (SELECT `id` FROM `medias`) order by name' ;
    $objSizeformat = CreateObject('medias');

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>【<?= $GLOBALS['env']['flag']['name']; ?>】媒體維護作業</title>
		<?php include("public/head.php"); ?>

		<style>
			.dropbtn {
				background-color: #ddd;
				color: white;
				padding: 5px 12px;
				font-size: 16px;
				border: none;
				cursor: pointer;
			}

			.dropdown {
				position: relative;
				display: inline-block;
			}

			.dropdown-content {
				display: none;
				position: absolute;
				background-color: #f9f9f9;
				min-width: 160px;
				box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
				z-index: 1;
			}

			.dropdown-content a {
				color: black;
				padding: 12px 16px;
				text-decoration: none;
				display: block;
				top: 2px;
			}

			.dropdown-content a:hover {
				background-color: #f1f1f1
			}

			.dropdown:hover .dropdown-content {
				display: block;
			}

			.dropdown:hover .dropbtn {
				background-color: #cddc39;
			}
		</style>
	</head>
    <body>
        <?php include("public/topbar.php"); ?>
        
        <div class="container-fluid">
            <div class="row-fluid">
                <?php include("public/left.php"); ?>
                
                <div id="content" class="span10">
                    <div class="row-fluid">
                        <div class="box span12">
                            <div class="box-header well" data-original-title>
                                <h2><i class="fa fa-cubes"></i> 媒體維護作業</h2>
                            </div>

                            <div class="box-content">
                                <div class="dropdown">
                                    <button class="dropbtn" style="border-radius: 4px; margin-right: 12px; margin-bottom: 12px;" onclick="window.location.href='medias_list.php';">
                                        <i class="fa fa-globe"></i>&nbsp;全部
                                    </button>
                                </div>
                                <? foreach ($mediaGroup as $groupName => $groupRows) :?>
                                    <div class="dropdown">
                                        <button class="dropbtn" style="border-radius: 4px; margin-right: 12px; margin-bottom: 12px;"><i class="fa fa-caret-down"></i>&nbsp;<?= $groupName; ?></button>
                                        <ul class="dropdown-menu dropdown-content" style="top: 20px;">
                                            <? foreach ($groupRows as $itemGroup) : ?>
                                                <li><a href="medias_list.php?media=<?= $itemGroup['id']; ?>"><?= $itemGroup['name']; ?></a></li>
                                            <? endforeach; ?>
                                        </ul>
                                    </div>
                                <? endforeach; ?>
                                <br/><br/>
                                <a class="btn btn-primary" href="medias_edit.php">
                                                            <i class="icon-plus icon-white"></i>  
                                                            Add                                            
                                                        </a><br/><br/>
                                <table class="table table-striped table-bordered bootstrap-datatable datatable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>媒體名稱</th>
                                            <th>媒體簡稱</th>
                                            <th>顯示</th>
                                            <? if (IsPermitted('superuser') || in_array($_SESSION['departmentid'], [21, 22])) : ?>
                                                <th>Actions</th>
                                            <? endif;?>
                                        </tr>
                                    </thead>   
                                    <tbody>
                                        <? foreach ($objSizeformat->searchAll($sizeformatConditions) as $itemFormat) : ?>
                                            <tr>
                                                <td><?= $itemFormat['id']; ?></td>
                                                <td><?= $itemFormat['name']; ?></td>
                                                <td><?= $itemFormat['crop']; ?></td>
                                                <td class="center"><?= str_replace('\\n', "<br/>", ($itemFormat['display']))=="1"?"顯示":"不顯示"; ?></td>
                                                <? if (IsPermitted('superuser') || in_array($_SESSION['departmentid'], [21, 22])) : ?>
                                                    <td class="center">
                                                        <a class="btn btn-info" href="medias_edit.php?id=<?= $itemFormat['id']; ?>">
                                                            <i class="icon-edit icon-white"></i>  
                                                            Edit                                            
                                                        </a>
                                                        <a class="btn btn-danger" onclick='return confirm("點擊「確定」即刪除所選擇的資料。")'  href="medias_del.php?id=<?= $itemFormat['id']; ?>">
                                                            <i class="icon-trash icon-white"></i>  
                                                            Del                                             
                                                        </a>
                                                    </td>
                                                <? endif; ?>
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


        <?php include("public/js.php"); ?>
        <script type="text/javascript" >
    console.log('11');
            $(document).ready(function(){
            
        });
        </script>
    </body>
</html>