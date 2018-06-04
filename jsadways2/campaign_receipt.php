<?php

    // 2017-10-30 (Jimmy): 傑思jsadways2/campaign_receipt.php, 香港jsadways2hk/campaign_receipt.php, 豐富媒體jsadways2ff/campaign_receipt.php 共用此檔案    
    
    if (!isset($parameter)) {
		$parameter = [
			'material_media_id' => [18, 19]
		];
    }
    
    require_once dirname(__DIR__) .'/autoload.php';
    
    IsPermitted();

    $db = clone($GLOBALS['app']->db);

    $campaignId = GetVar('id');

    $sqlCampaign = sprintf("SELECT * FROM `campaign` WHERE `id` = %d;", $campaignId);
    $db->query($sqlCampaign);
    $itemCampaign = $db->next_record();

    if ($itemCampaign['agency'] == null) {
        $objAgencyClient = CreateObject('Client', $itemCampaign['client_id']);
        $receipt1 = $objAgencyClient->getVar('name');
        $receipt2 = $objAgencyClient->getVar('taxid');
        $usertype = 'client';
        $userid2 = $objAgencyClient->getId();
    } else {
        $objAgencyClient = CreateObject('Agency', $itemCampaign['agency_id']);
        $receipt1 = $objAgencyClient->getVar('name');
        $receipt2 = $objAgencyClient->getVar('taxid');
        $usertype = 'agency';
        $userid2 = $objAgencyClient->getId();
    }

    $priceConfirmForTotal = 0;
    $priceConfirmForCommon = 0;
    $priceConfirmForMaterial = 0;

    foreach (GetUsedMediaOrdinal($campaignId) as $idxOridnal) {
        $sqlMediaDetail = sprintf("SELECT * FROM `media%d` WHERE  `campaign_id` = %d AND `cue` = 1 ORDER BY `id` ASC;", $idxOridnal, $campaignId);
        $db->query($sqlMediaDetail);
        while ($row3 = $db->next_record()) {
            if (in_array($idxOridnal, $parameter['material_media_id'])) {
                $priceConfirmForMaterial += $row3['totalprice'];
            } else {
                $priceConfirmForCommon += $row3['totalprice'];
            }
        }
    }

    $priceConfirmForTotal = $priceConfirmForMaterial + $priceConfirmForCommon;
    
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>【<?= $GLOBALS['env']['flag']['name']; ?>】提出開發票需求</title>
        <?php include("public/head.php"); ?>
    </head>
    <body>
        <?php include("public/topbar.php"); ?>
        
        <div class="container-fluid">
            <div class="row-fluid">
                <?php include("public/left.php"); ?>
                
                <div id="content" class="span10">
                    <script type="text/javascript">
                        function ChangeCategory()
                        {
                            var categoryName = $.trim($('#class option:selected').text());
                            
                            if (categoryName == '廣告費') {
                                document.getElementById('totalprice1').value = '<?= $priceConfirmForCommon; ?>';
                                document.getElementById('totalprice2').value = '<?= $priceConfirmForCommon * 1.05; ?>';
                            } else if (categoryName == '製作費') {
                                document.getElementById('totalprice1').value = '<?= $priceConfirmForMaterial; ?>';
                                document.getElementById('totalprice2').value = '<?= $priceConfirmForMaterial * 1.05; ?>';
                            } else if (categoryName == '廣告費+製作費') {
                                document.getElementById('totalprice1').value = '<?= $priceConfirmForTotal; ?>';
                                document.getElementById('totalprice2').value = '<?= $priceConfirmForTotal * 1.05; ?>';
                            }
                        }
                    </script>
                
                    <div class="row-fluid ">
                        <div class="box span12">
                            <div class="box-header well" data-original-title>
                                <h2><i class="icon-edit"></i> 提出開發票需求-<?= $itemCampaign['name']; ?></h2>
                            </div>
                            
                            <div class="box-content">
                                <form class="form-horizontal" action="campaign_receipt2.php?id=<?= $campaignId; ?>" method="post">
                                    <div class="control-group">
                                        <label class="control-label">案件名稱</label>
                                        <div class="controls">
                                            <input id="name" name="name" type="text" value="<?= $itemCampaign['name']; ?>" style="width:200px" readonly>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">代理商(廣告主)名稱</label>
                                        <div class="controls">
                                            <input id="user1" name="user1" type="text" value="<?= $receipt1; ?>" style="width:200px" readonly>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">申請者姓名</label>
                                        <div class="controls">
                                            <input id="user2" name="user2" type="text" value="<?= $_SESSION['username']; ?>" style="width:200px" readonly>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">統一編號</label>
                                        <div class="controls">
                                            <input id="taxid" name="taxid" type="text" value="<?= $receipt2; ?>" style="width:200px" readonly>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">委刊編號</label>
                                        <div class="controls">
                                            <input id="numberid" name="numberid" type="text" value="<?= $itemCampaign['idnumber']; ?>" style="width:200px" readonly>
                                        </div>
                                    </div>
                                    
                                    <div class="control-group">
                                        <label class="control-label">費用類型</label>
                                        <div class="controls">
                                            <select name="class" id="class" onChange="ChangeCategory();">
                                                <option value="廣告費">廣告費</option>
                                                <option value="製作費">製作費</option>
                                                <option value="廣告費+製作費">廣告費+製作費</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">金額(未稅)</label>
                                        <div class="controls">
                                            <input id="totalprice1" name="totalprice1" type="text" value="<?= $priceConfirmForCommon; ?>" style="width:200px">
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">金額(含稅)</label>
                                        <div class="controls">
                                            <input id="totalprice2" name="totalprice2" type="text" value="<?= $priceConfirmForCommon*1.05; ?>" style="width:200px">
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">發票月份</label>
                                        <div class="controls">
                                            <input id="datemonth" name="datemonth" type="text" value="<?= date('Ym',mktime()); ?>" style="width:200px">
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">備註(可以寫下你對財務部想說的話)</label>
                                        <div class="controls">
                                            <textarea name="others" cols="" rows=""></textarea>
                                        </div>
                                    </div>

                                    <input name="id" type="hidden" value="<?= $campaignId; ?>">
                                    <input name="usertype" type="hidden" value="<?= $usertype; ?>">
                                    <input name="userid2" type="hidden" value="<?= $userid2; ?>">
                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary">提出開發票需求</button>
                                    </div>
                                </form>
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