<?php 
    
    require_once dirname(__DIR__) .'/autoload.php';

    $objCampaign = CreateObject('Campaign', GetVar('id'));
    if (!IsId($objCampaign->getId())) {
        RedirectLink('campaign_list.php');
    }

    $db = clone($GLOBALS['app']->db);
    $objMedia = CreateObject('Media');

    $mediaTypeList = [
        'CPC' => '`type` = 1 AND `display` = 1',
        'CPI' => '`type` = 2 AND `display` = 1',
        'CPM' => '`type` = 0 AND `display` = 1',
        'CPV' => '`type` = 9 AND `display` = 1',
        'CPT' => '`type` = 10 AND `display` = 1',
        '網站廣告' => '`type` = 3 AND `display` = 1',
    ];

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>【<?= $GLOBALS['env']['flag']['name']; ?>】新增媒體</title>
        <?php include("public/head.php"); ?>
        <style>
            select {
                width: 320px;
                margin-right: 8px;
            }
        </style>
    </head>
    <body>
        <?php include("public/topbar.php"); ?>

        <div class="container-fluid">
            <div class="row-fluid">
                <?php include("public/left.php"); ?>
                
                <div id="content" class="span10">	
                    <div class="row-fluid sortable">
                        <div class="box span12">
                            <div class="box-header well" data-original-title>
                                <h2><i class="icon-edit"></i> <?= $objCampaign->getVar('name'); ?> - 新增媒體</h2>
                            </div>

                            <? foreach ($mediaTypeList as $mediaType => $mediaCondition) : ?>
                                <div class="box-content">
                                    <form class="form-horizontal" action="media_add2.php?id=<?= GetVar('id'); ?>&cue=<?= GetVar('cue'); ?>&media2=<?= GetVar('media2'); ?>&mediaid=<?= GetVar('mediaid');?>" method="post">
                                        <fieldset>
                                            <div class="control-group">
                                                <label class="control-label"><?= $mediaType; ?></label>
                                                <div class="controls">
                                                    <select name="media">
                                                        <? foreach ($objMedia->searchAll($mediaCondition, 'sortid', 'ASC') as $itemMedia) : ?>
                                                            <option value="<?= $itemMedia['id']; ?>"><?= '【'. $itemMedia['typename'] .'】'. $itemMedia['name']; ?></option>
                                                        <? endforeach; ?>
                                                    </select>
                                                    <button type="submit" class="btn btn-primary">下一步</button>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </form>   
                                </div>
                            <? endforeach; ?>

                            <div class="box-content">
                                <form class="form-horizontal" action="media_add2.php?id=<?= GetVar('id'); ?>&cue=<?= GetVar('cue'); ?>&media2=<?= GetVar('media2'); ?>&mediaid=<?= GetVar('mediaid');?>" method="post">
                                    <fieldset>
                                        <div class="control-group">
                                            <label class="control-label">其他</label>
                                            <div class="controls">
                                                <select name="media">
                                                    <? foreach ($objMedia->searchAll("`type` IN (4, 5, 6, 7, 8, 11) AND `id` <> 20 AND `display` = 1", 'sortid', 'ASC') as $itemMedia) : ?>
                                                        <? if ($itemMedia['id'] == 19 && GetVar('cue') == 2 && GetVar('media2') == 19) : ?>
                                                            <? continue; ?>
                                                        <? else : ?>
                                                            <? if ($itemMedia["type"] != 11 || ($itemMedia["type"] == 11 && (in_array($_SESSION['usergroup'], [3, 6]) || $_SESSION['userid']== 16))) : ?>
                                                                <option value="<?= $itemMedia['id']; ?>"><?= '【'. $itemMedia['typename'] .'】'. $itemMedia['name']; ?></option>
                                                            <? endif; ?>
                                                        <? endif; ?>
                                                    <? endforeach; ?>
                                                </select>
                                                <button type="submit" class="btn btn-primary">下一步</button>
                                            </div>
                                        </div>
                                    </fieldset>
                                </form>   
                            </div>

                            <div class="box-content">
                                <form class="form-horizontal" action="media_add2.php?id=<?= GetVar('id'); ?>&cue=<?= GetVar('cue'); ?>&media2=<?= GetVar('media2'); ?>&mediaid=<?= GetVar('mediaid');?>" method="post">
                                    <fieldset>
                                        <div class="control-group">
                                            <label class="control-label">海外媒體</label>
                                            <div class="controls">
                                                <select name="media">
                                                    <? foreach ($objMedia->searchAll("`type` IN (20, 21, 22, 29) AND `display` = 1", 'sortid', 'ASC') as $itemMedia) : ?>
                                                        <option value="<?= $itemMedia['id']; ?>"><?= '【'. $itemMedia['typename'] .'】'. $itemMedia['name']; ?></option>
                                                    <? endforeach; ?>
                                                </select>
                                                <button type="submit" class="btn btn-primary">下一步</button>
                                            </div>
                                        </div>
                                    </fieldset>
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