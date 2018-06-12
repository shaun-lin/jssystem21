<?php

    // 2018-03-14 (Jimmy): 傑思jsadways2/media_list.php, 香港jsadways2hk/media_list.php, 豐富媒體jsadways2ff/media_list.php 共用此檔案
    require_once dirname(__DIR__) .'/autoload.php';
    
    $mediaGroup = [];

    $objMedia = CreateObject('companies');
    foreach ($objMedia->searchAll("`id` > 0", 'name', 'ASC') as $itemMedia) {
        $firstChar = $itemMedia['name2']{0};
        if (preg_match('/[0-9]/', $firstChar)) {
            $mediaGroup['0 ~ 9'][] = [
                'name' => $itemMedia['name'],
            ];
        } else if (preg_match('/[a-zA-Z]/', $firstChar)) {
            $mediaGroup[strtoupper($itemMedia['name']{0})][] = [
                'name' => $itemMedia['name'],
            ];
        } else {
            $mediaGroup['其他'][] = [
                'name' => $itemMedia['name'],
            ];
        }
    }

    $companiesOrdinal = GetVar('id');
    $sizeformatConditions =IsId($companiesOrdinal) ? sprintf('`id` = %d', $companiesOrdinal) : '1=1' ;
    // echo $sizeformatConditions;

    $objSizeformat = CreateObject('companies');

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>【<?= $GLOBALS['env']['flag']['name']; ?>】公司維護作業</title>
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
                                <h2><i class="fa fa-cubes"></i>公司維護作業</h2>
                            </div>

                            <div class="box-content">
                                <div class="dropdown">
                                    <button class="dropbtn" style="border-radius: 4px; margin-right: 12px; margin-bottom: 12px;" onclick="window.location.href='companies_list.php';">
                                        <i class="fa fa-globe"></i>&nbsp;全部
                                    </button>
                                </div>
                                <? foreach ($mediaGroup as $groupName => $groupRows) :?>
                                    <div class="dropdown">
                                        <button class="dropbtn" style="border-radius: 4px; margin-right: 12px; margin-bottom: 12px;"><i class="fa fa-caret-down"></i>&nbsp;<?= $groupName; ?></button>
                                        <ul class="dropdown-menu dropdown-content" style="top: 20px;">
                                            <? foreach ($groupRows as $itemGroup) : ?>
                                            <li><a href="companies_list.php?id=<?= $itemGroup['id']; ?>"><?= $itemGroup['name']; ?></a></li>
                                            <? endforeach; ?>
                                        </ul>
                                    </div>
                                <? endforeach; ?>
                                <br/><br/>
                                <a class="btn btn-primary" href="companies_edit.php?id=0">
                                    <i class="icon-plus icon-white"></i>  
                                    Add                                            
                                </a>
                                <table class="table table-striped table-bordered bootstrap-datatable datatable">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>公司簡稱</th>
                                            <th>統一編號</th>
                                            <th>公司名稱</th>
                                            <th>英文名稱</th>
                                            <th>地址</th>
                                            <th>電話</th>
                                            <th>城市</th>
                                            <th>國家簡碼</th>

                                            <? if (IsPermitted('superuser') || in_array($_SESSION['departmentid'], [21, 22])) : ?>
                                                <th>Actions</th>
                                            <? endif;?>
                                        </tr>
                                    </thead>   
                                    <tbody>
                                        <? foreach ($objSizeformat->searchAll($sizeformatConditions) as $itemFormat) : ?>
                                            <tr>
                                                <td><?= $itemFormat['id']; ?></td>
                                                <td><?= $itemFormat['name2']; ?></td>
                                                <td><?= $itemFormat['tax_id']; ?></td>
                                                <td><?= $itemFormat['name']; ?></td>
                                                <td><?= $itemFormat['eng_name']; ?></td>

                                                <td><?= $itemFormat['address']; ?></td>
                                                <td><?= $itemFormat['tel']; ?></td>
                                                <td><?= $itemFormat['city_name']; ?></td>
                                                <td><?= $itemFormat['country_code']; ?></td>

                                                <? if (IsPermitted('superuser') || in_array($_SESSION['departmentid'], [21, 22])) : ?>
                                                    <td class="center">
                                                        <a class="btn btn-info" href="companies_edit.php?id=<?= $itemFormat['id']; ?>">
                                                            <i class="icon-edit icon-white"></i>  
                                                            Edit                                            
                                                        </a>
                                                        <a class="btn btn-danger" onclick='return confirm("點擊「確定」即刪除所選擇的資料。")' href="companies_del.php?id=<?= $itemFormat['id']; ?>">
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
    </body>
</html>