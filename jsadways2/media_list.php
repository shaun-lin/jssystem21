<?php

	// 2018-03-14 (Jimmy): 傑思jsadways2/media_list.php, 香港jsadways2hk/media_list.php, 豐富媒體jsadways2ff/media_list.php 共用此檔案
	require_once dirname(__DIR__) .'/autoload.php';
	
	$mediaGroup = [];

	$objMedia = CreateObject('Media');
	foreach ($objMedia->searchAll("`display` = 1 AND `id` > 0", 'name', 'ASC') as $itemMedia) {
		$firstChar = $itemMedia['name']{0};

		if (preg_match('/[0-9]/', $firstChar)) {
			$mediaGroup['0 ~ 9'][] = [
				'id' => $itemMedia['id'],
				'name' => $itemMedia['name'],
				'costper' => $itemMedia['costper']
			];
		} else if (preg_match('/[a-zA-Z]/', $firstChar)) {
			$mediaGroup[strtoupper($itemMedia['name']{0})][] = [
				'id' => $itemMedia['id'],
				'name' => $itemMedia['name'],
				'costper' => $itemMedia['costper']
			];
		} else {
			$mediaGroup['其他'][] = [
				'id' => $itemMedia['id'],
				'name' => $itemMedia['name'],
				'costper' => $itemMedia['costper']
			];
		}
	}

	$meidaOrdinal = GetVar('media');
    $sizeformatConditions = IsId($meidaOrdinal) ? sprintf('`mediaid` = %d', $meidaOrdinal) : 'mediaid IN (SELECT `id` FROM `media` WHERE `display` = 1)' ;
    
	$objSizeformat = CreateObject('Sizeformat');

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>【<?= $GLOBALS['env']['flag']['name']; ?>】媒體規格表</title>
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
                                <h2><i class="fa fa-cubes"></i> 媒體規格表</h2>
                            </div>

                            <div class="box-content">
                                <div class="dropdown">
                                    <button class="dropbtn" style="border-radius: 4px; margin-right: 12px; margin-bottom: 12px;" onclick="window.location.href='media_list.php';">
                                        <i class="fa fa-globe"></i>&nbsp;全部
                                    </button>
                                </div>

                                <? foreach ($mediaGroup as $groupName => $groupRows) :?>
                                    <div class="dropdown">
                                        <button class="dropbtn" style="border-radius: 4px; margin-right: 12px; margin-bottom: 12px;"><i class="fa fa-caret-down"></i>&nbsp;<?= $groupName; ?></button>
                                        <ul class="dropdown-menu dropdown-content" style="top: 20px;">
                                            <? foreach ($groupRows as $itemGroup) : ?>
                                                <li><a href="media_list.php?media=<?= $itemGroup['id']; ?>"><?= $itemGroup['name']; ?>【<?= $itemGroup['costper']; ?>】</a></li>
                                            <? endforeach; ?>
                                        </ul>
                                    </div>
                                <? endforeach; ?>
                                <br/><br/>
                                
                                <table class="table table-striped table-bordered bootstrap-datatable datatable">
                                    <thead>
                                        <tr>
                                            <th>媒體名稱</th>
                                            <th>Adtype</th>
                                            <th>System</th>
                                            <th>Position</th>
                                            <th>Size</th>
                                            <th>Format</th>
                                            <? if (IsPermitted('superuser') || in_array($_SESSION['departmentid'], [21, 22])) : ?>
                                                <th>Actions</th>
                                            <? endif;?>
                                        </tr>
                                    </thead>   
                                    <tbody>
                                        <? foreach ($objSizeformat->searchAll($sizeformatConditions) as $itemFormat) : ?>
                                            <tr>
                                                <td><?= $itemFormat['medianame']; ?></td>
                                                <td align="left"><?= str_replace('\\n', "<br/>", ($itemFormat['adtype'])); ?></td>
                                                <td class="center"><?= str_replace('\\n', "<br/>", ($itemFormat['phonesystem'])); ?></td>
                                                <td class="center"><?= str_replace('\\n', "<br/>", ($itemFormat['position'])); ?></td>
                                                <td class="center"><?= str_replace('\\n', "<br/>", ($itemFormat['format1'])); ?></td>
                                                <td class="center"><?= str_replace('\\n', "<br/>", ($itemFormat['format2'])); ?></td>
                                                <? if (IsPermitted('superuser') || in_array($_SESSION['departmentid'], [21, 22])) : ?>
                                                    <td class="center">
                                                        <a class="btn btn-info" href="media_edit.php?id=<?= $itemFormat['id']; ?>">
                                                            <i class="icon-edit icon-white"></i>  
                                                            Edit                                            
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
    </body>
</html>