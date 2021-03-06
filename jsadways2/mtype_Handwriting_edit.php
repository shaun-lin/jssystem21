<?php

    require_once dirname(__DIR__) .'/autoload.php';
    session_start();
	include('include/db.inc.php');
    
    $dbBloggerDetail = clone($GLOBALS['app']->db);

    $cue = GetVar('cue');
    $mediaId = GetVar('mediaid');
    $media_id = GetVar('media_id');
    $campaignId = GetVar('campaign');
    
    $objPagination = CreateObject('Pagination');
    $objPagination->setConfig(['start' => 0, 'total' => 0, 'url' => "blogger_list.php?mode=media_detail_edit&campaign_id=$campaignId&media_id={$mediaId}"]);

    $totalPrice = 0;
    $totalCost = 0;
    $totalProfit = 0;

    $itemMedia = [
        'id' => 0,
        'price' => 0,
        'cost' => 0,
        'profit' => 0,
    ];

    if (IsId($mediaId)) {
        $sqlMediaItem = sprintf("SELECT * FROM `media162` WHERE `id` = %d;", $mediaId);
        $dbBloggerDetail->query($sqlMediaItem);
        $item = $dbBloggerDetail->next_record();

        if (isset($item['cue'])) {
            if ($item['cue'] == 1) {
                $itemMedia = [
                    'id' => 0,
                    'price' => $item['totalprice'],
                    'cost' => $item['totalprice2'],
                    'profit' => $item['totalprice3'],
                    'type' =>  $item['items2'],
                    'system' =>  $item['items3'],
                    'others' =>  $item['others'],
                ];
            } else if ($item['cue'] == 2) {
                $itemMedia += [
                    'type' =>  $item['items2'],
                    'system' =>  $item['items3'],
                    'others' =>  $item['others'],
                ];
            }
        }

        unset($item);
    }

    $sqlBloggerDetail = sprintf("SELECT * FROM `media162_detail` WHERE `campaign_id` = %d;", $campaignId);
    $dbBloggerDetail->query($sqlBloggerDetail);

    $isEditExtCue = ($cue == 1 && IsId($mediaId));

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>【<?= $GLOBALS['env']['flag']['name']; ?>】寫手列表</title>
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
                        <div class="box span12" id="addList">
                            <div class="box-header well" data-original-title>
                                <h2><i class="fa fa-users"></i> 已增加寫手列表</h2>
                            </div>
                            <script>
            <? if ($isEditExtCue) : ?>
                function ChangePrice()
                {
                    var price = parseFloat($('#totalprice').val()) ? parseFloat($('#totalprice').val()) : 0;
                    var cost = parseFloat($('#totalprice2').val()) ? parseFloat($('#totalprice2').val()) : 0;

                    $('#totalprice3').val(price - cost);

                    delete price, cost;
                }
            <? endif; ?>

            $(document).ready(function() {
                Page_Init();

                <? if (isset($itemMedia['type']) && $itemMedia['type']) : ?>
                    $('select#SelectType').val('<?= $itemMedia['type']; ?>');
                <? endif; ?>

                <? if (isset($itemMedia['system']) && $itemMedia['system']) : ?>
                    $('select#SelectSystem').val('<?= $itemMedia['system']; ?>');
                <? endif; ?>
            });

            function Page_Init()
            {
                <?php 
                    include('campaign_required_select.php');
                    echo $Select_str;
                ?>
            }

            function LoadBlogger(bloggerId)
            {
                if (bloggerId) {
                    $.ajax({
                        url: 'blogger_action.php?method=load',
                        type: 'POST',
                        data: {blogger_id: bloggerId},
                        beforeSend: function() {

                        },
                        success: function() {

                        }, 
                        error: function() {
                            
                        }
                    });
                }
            }

            orderby = '`blogger`.`ac_id`';
            orderdir = 'DESC';
            keyword = '';
            jQuery('#content_loader').hide();
            jQuery('#content_list').hide();
            //gg1goToPage(1);
        </script>
                            <div class="box-content">
                                <? if (empty($isEditExtCue)) : ?>
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>寫手</th>
                                                <th>對外報價</th>
                                                <th>成本</th>
                                                <th>利潤</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <? while ($itemDetail = $dbBloggerDetail->next_record()) : ?>
                                                <tr>
                                                    <td><a href="blogger_view.php?id=<?= $itemDetail['blogid']; ?>" target="_blank"><?= empty($itemDetail['blog']) ? $itemDetail['blog2'] : $itemDetail['blog']; ?></a></td>
                                                    <td><?= $itemDetail['price2']; $itemMedia['price'] += $itemDetail['price2']; ?></td>
                                                    <td><?= $itemDetail['price']; $itemMedia['cost'] += $itemDetail['price'];  ?></td>
                                                    <td><?= $itemDetail['price3']; $itemMedia['profit'] += $itemDetail['price3']; ?></td>
                                                    <td class="center">
                                                        <!-- <a class="btn btn-info" href="mtype_Handwriting_detail_edit.php?campaign_id=<?= $campaignId; ?>&detail_id=<?= $itemDetail['id']; ?>&blog_id=<?= $itemDetail['blogid']; ?>&media_id=<?= $mediaId; ?>">
                                                            <i class="icon-edit icon-white"></i>
                                                            編輯
                                                        </a> -->
                                                        <a class="btn btn-danger" data-target="#dialog" id="PersonDel" name="PersonDel[]" href="mtype_Handwriting_detail_delete.php?campaign=<?= $itemDetail['campaign_id']; ?>&blogid=<?= $itemDetail['id'];?>&media_id=<?= $mediaId; ?>';">
                                                            <i class="fa fa-trash"></i>
                                                            刪除
                                                        </a>
                                                    </td>
                                                </tr>
                                            <? endwhile; ?>
                                        </tbody>
                                    </table>
                                <? endif; ?>
                                <div class="box-content">
                                    <form class="form-horizontal" action="mtype_Handwriting_save.php?campaign=<?= $campaignId; ?>&media_id=<?= $media_id; ?>" method="post" style="margin-bottom: 0px;">
                                        <div class="row-fluid">
                                            <div class="span5">
                                                <div class="control-group">
                                                    <label class="control-label">對外報價總金額</label>
                                                    <div class="controls">
                                                        <input class="input-xlarge" id="totalprice" name="totalprice" type="text" style="width: 200px;" value="<?= $itemMedia['price']; ?>" <?= ($isEditExtCue ? '' : 'readonly'); ?> <?= ($isEditExtCue ? 'onchange="ChangePrice();"' : ''); ?> />
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                    <label class="control-label">成本</label>
                                                    <div class="controls">
                                                        <input class="input-xlarge" id="totalprice2" name="totalprice2" type="text" style="width: 200px;" value="<?= $itemMedia['cost']; ?>" <?= ($isEditExtCue ? '' : 'readonly'); ?> <?= ($isEditExtCue ? 'onchange="ChangePrice();"' : ''); ?> />
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                    <label class="control-label">利潤</label>
                                                    <div class="controls">
                                                        <input class="input-xlarge" id="totalprice3" name="totalprice3" type="text" style="width: 200px;" value="<?= $itemMedia['profit']; ?>" readonly />
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                    <div class="form-actions" style="padding-top: 12px;">
                                                        <button type="submit" class="btn btn-danger">完成寫手費</button>
                                                        <? if (IsId($mediaId) && empty($isEditExtCue)) : ?>
                                                            <br/><br/><label><input type="checkbox" name="sync" value="1" />一併更新「對外cue」金額</label>
                                                        <? endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="span5">
                                                <div class="control-group">
                                                    <label class="control-label" for="SelectType">類別(Type)</label>
                                                    <div class="controls">
                                                        <select id="SelectType" name="SelectType"></select>
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                    <label class="control-label" for="SelectSystem">系統(System)</label>
                                                    <div class="controls">
                                                        <select id="SelectSystem" name="SelectSystem"></select>
                                                    </div>
                                                </div>
                                                <div class="control-group">
                                                    <label class="control-label" for="others">備註</label>
                                                    <div class="controls">
                                                        <textarea id="others" name="others" style="width: 100%;"><?= isset($itemMedia['others']) ? htmlspecialchars($itemMedia['others'], ENT_QUOTES) : ''; ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid" >
                        <div class="box span12" id="addForm">
                        </div>
                    </div>
                    <? if (empty($isEditExtCue)) : ?>
                        <div class="row-fluid">
                            <div class="box span12" id="list">
                                <div class="box-header well" data-original-title>
                                    <h2><i class="fa fa-search-plus"></i> 寫手列表</h2>
                                </div>
                                <div class="box-content">
                                <table class="table table-striped table-bordered bootstrap-datatable datatable">
						  <thead>
							  <tr>
									<th class="ui-state-default" style="width: 140px;">照片</th>
									<th class="ui-state-default" style="text-align: left;">名稱</th>
                  <th class="ui-state-default" style="text-align: left;">分類</th>
                  <th class="ui-state-default" style="width: 90px;">Actions</th>
							  </tr>
						  </thead>
						  <tbody>
                            <?php 
                                    $sqlCampaignStatus = "SELECT * FROM blogger";
                                    $resultCampaign = mysql_query($sqlCampaignStatus);
                                    while($Rows = mysql_fetch_array($resultCampaign)){ ?>
                            <tr>
                                <td><img src='<?= $Rows['photo'] ?>' style='height: auto; width: 140px; max-width: 140px;'></td>
        
                            <td style='text-align:  left';>
                        <? if (!empty($Rows["blog_name"])){ ?>
                            <b><img src='images/blogspot.png' style='width: auto; height: 13px; border-radius: 2px;'>&nbsp;Blog</b>：<?=$Rows['blog_name'];?> <br/>
                                <? }
                                if (!empty($Rows["fb_name"])){ ?>
                                <b><i class='fa fa-facebook-official' style='color: #5a5aff; font-size: 1em !important;'></i>&nbsp;FB</b>：<?=$Rows['fb_name'];?><br/>
                                <? }
                                if (!empty($Rows["ig_name"])){ ?>
                            <b><i class='fa fa-instagram' style='color: #fe99ff; font-size: 1em !important;'></i>&nbsp;Instagram</b>：<?=$Rows['ig_name'];?><br/>
                                <? }
                                if (!empty($Rows["youtube_name"])){ ?>
                                        <b><i class='fa fa-youtube-play' style='color: #ff4c4c; font-size: 1em !important;'></i>&nbsp;YouTube</b>：<?=$Rows['youtube_name'];?><br/>
                                <? } ?>
                            </td>
                            <td><input type='hidden' name='blogid' value='<?=$Rows['id']; ?>'></td>
                            <td>
                                    <a class="btn btn-success" id="PersonAdd" name="PersonAdd[]" href="mtype_Handwriting_add3.php?id=<?php echo $_GET['id']; ?>&blogid=<?php echo $Rows['id']; ?>" >
                                        <i class="icon-zoom-in icon-white"></i>
                                        Add
                                    </a>
                        
                            </td>
                            </tr>
                        <?  }?>
                          </tbody>
                      </table>
                                </div>
                            </div>
                        </div>
                    <? endif; ?>
                </div>
            </div>
            <hr/>

            <?php include("public/js.php"); ?>
        </div>

        
    </body>
</html>