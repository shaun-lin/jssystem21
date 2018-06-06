<?php

    require_once dirname(__DIR__) .'/autoload.php';
    
    $db = clone($GLOBALS['app']->db);

    IncludeFunctions('jsadways');

    $mediaId = GetVar('media_id');
    $detailId = GetVar('detail_id');

    $objCampaign = CreateObject('Campaign', GetVar('campaign_id'));
    $objBlogger = CreateObject('Blogger', GetVar('blog_id'));

    $otherCost = 0;
    if (IsId($detailId)) {
        $sqlDetail = sprintf("SELECT * FROM `media162_detail` WHERE `id` = %d AND `campaign_id` = %d AND `blogid` = %d;", $detailId, $objCampaign->getId(), $objBlogger->getId());
        $db->query($sqlDetail);
        $itemDetail = $db->next_record();
        $otherCost = $itemDetail && strpos($itemDetail['type'], '其他') !== false ? $itemDetail['price'] : 0;
    }

    $priceType = getInfluencerPriceType();
    foreach ($priceType as $idxType => $itemType) {
        if (isset($itemType['name']) && $itemType['name']) {
            $priceType[$idxType]['price_text'] = $objBlogger->getVar($itemType['name']) > 0 ? (number_format(calcInfluenceCostPrice($objBlogger->getVar($itemType['name']), $objBlogger->getVar('payment_method')))) : 0;
            $priceType[$idxType]['price_cost'] = $objBlogger->getVar($itemType['name']) > 0 ? (calcInfluenceCostPrice($objBlogger->getVar($itemType['name']), $objBlogger->getVar('payment_method'))) : 0;
            $priceType[$idxType]['price_profit_included'] = $objBlogger->getVar($itemType['name']) > 0 ? ($objBlogger->getVar($itemType['name']) * getInfuencerPriceRate('outer_tax_included')) : 0;

            if ($otherCost > 0 && strpos($itemDetail['type'], $itemType['text']) !== false) {
                $otherCost -= $priceType[$idxType]['price_cost'];
            }
        }
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>【<?= $GLOBALS['env']['flag']['name']; ?>】<?= empty($editMode) ? '新增寫手' : '修改寫手'; ?></title>
        <?php include("public/head.php"); ?>
        <?php include("public/js.php"); ?>
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
                                <h2><i class="fa fa-pencil"></i>&nbsp;&nbsp;<?= $objCampaign->getVar('name'); ?> - <?= empty($editMode) ? '增加寫手' : '修改寫手'; ?></h2>
                            </div>

                            <div class="box-content">
                                <form class="form-horizontal" action="mtype_Handwriting_edit_detail_save.php" method="post">
                                    <fieldset> 
                                        <div class="control-group">
                                            <label class="control-label">分類</label>
                                            <div class="controls">
                                                <select id="blog1" name="blog1" onchange="ChangePlatform(this);">
                                                    <option value="部落格" <?= isset($itemDetail['blog1']) && $itemDetail['blog1'] == '部落格' ? ' selected' : ''; ?>>部落格</option>
                                                    <option value="粉絲團" <?= isset($itemDetail['blog1']) && $itemDetail['blog1'] == '粉絲團' ? ' selected' : ''; ?>>粉絲團</option>
                                                    <option value="Instagram" <?= isset($itemDetail['blog1']) && $itemDetail['blog1'] == 'Instagram' ? ' selected' : ''; ?>>Instagram</option>
                                                    <option value="YouTube" <?= isset($itemDetail['blog1']) && $itemDetail['blog1'] == 'YouTube' ? ' selected' : ''; ?>>YouTube</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label">名稱</label>
                                            <div class="controls">
                                                <input class="input-xlarge" id="blog2" name="blog2" type="text" value="<?= empty($itemDetail['blog2']) ? '' : htmlspecialchars($itemDetail['blog2'], ENT_QUOTES); ?>"  required>               
                                            </div>
                                        </div>
                                        
                                        <div class="control-group">
                                            <label class="control-label">URL</label>
                                            <div class="controls">
                                                <textarea id="blog3" name="blog3"><?= empty($itemDetail['blog3']) ? '' : htmlspecialchars($itemDetail['blog3'], ENT_QUOTES); ?></textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="control-group">
                                            <label class="control-label" for="blogtype">報價類型</label>
                                            <div class="controls">
                                                <?php foreach ($priceType as $idxType => $itemType) : ?>
                                                    <? if (isset($itemType['name']) && $itemType['name']) : ?>
                                                        <label><input type="checkbox" class="influencer-price" name="price<?= $idxType; ?>" id="price<?= $idxType; ?>" onchange="ChangePriceType();" data-cost="<?= $itemType['price_cost']; ?>" data-profit="<?= $itemType['price_profit_included']; ?>" <?= (!isset($itemDetail['type']) || (strpos($itemDetail['type'], $itemType['text']) === false && strpos($itemDetail['type'], $itemType['old']) === false) ? '' : 'checked'); ?>><?= $itemType['text']; ?>&nbsp;($<?= $itemType['price_text']; ?>)</label>
                                                    <? endif; ?>
                                                <?php endforeach; ?>
                                                <label><input type="checkbox" name="price10" id="price10" onchange="ChangePriceType();" <?= (strpos($itemDetail['type'], '其他') === false && !StrPosWithArray($itemDetail['type'], $priceType[10]['old']) ? '' : 'checked'); ?>>其他&nbsp;<input type="text" id="other_price" onchange="ChangePriceType();" style="width: 120px;" placeholder="0" value="<?= $otherCost > 0 ? $otherCost : 0; ?>"></label>
                                            </div>
                                        </div>

                                        <div class="control-group">
                                            <label class="control-label" for="totalprice2">對外報價</label>
                                            <div class="controls">
                                                $<input class="input-xlarge" id="totalprice2" name="totalprice2" type="text" value="<?= empty($itemDetail['price2']) ? '' : $itemDetail['price2']; ?>" style="width:100px"  onChange="ChangeQuotation();"  required>元
                                            </div>
                                        </div>  

                                        <div class="control-group">
                                            <label class="control-label" for="totalprice">成本金額</label>
                                            <div class="controls">
                                                $<input class="input-xlarge" id="totalprice" name="totalprice" type="text" value="<?= empty($itemDetail['price']) ? '' : $itemDetail['price']; ?>" style="width:100px" onChange="ChangeCost();"  required>元
                                            </div>
                                        </div>        
                                        
                                        <div class="control-group">
                                            <label class="control-label" for="totalprice3">利潤</label>
                                            <div class="controls">
                                                $<input class="input-xlarge" id="totalprice3" name="totalprice3" type="text" value="<?= empty($itemDetail['price3']) ? '' : $itemDetail['price3']; ?>" style="width:100px" readonly>元
                                            </div>
                                        </div>      

                                        <div class="control-group">
                                            <label class="control-label" for="others">備註</label>
                                            <div class="controls">
                                                <textarea id="others" name="others" ><?= empty($itemDetail['others']) ? '' : htmlspecialchars($itemDetail['others'], ENT_QUOTES); ?></textarea>
                                            </div>
                                        </div>
                                        
                                        <input name="media_id" type="hidden" value="<?= $mediaId; ?>">
                                        <input name="campaign_id" type="hidden" value="<?= $objCampaign->getId(); ?>">
                                        <input name="blog_id" type="hidden" value="<?= $objBlogger->getId(); ?>">
                                        <input name="detail_id" type="hidden" value="<?= $detailId; ?>">
                                        <input name="blog" type="hidden" value="<?= $objBlogger->getName(); ?>">

                                        <div class="form-actions">
                                            <button type="submit" class="btn btn-primary"><?= IsId($detailId) ? '修改寫手費' : '新增寫手費'; ?></button>
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

        <script>
            $(document).ready(function() {
                <? if (empty($itemDetail)) : ?>
                    ChangePlatform($('select#blog1'));
                <? endif; ?>
            });

            function ChangePlatform(platformSelector)
            {
                switch ($(platformSelector).val()) {
                    case '部落格':
                        $('#blog2').val(Htmlspecialchars(blogger.blog_name));
                        $('#blog3').val(Htmlspecialchars(blogger.blog_link));
                        break;
                    case '粉絲團':
                        $('#blog2').val(Htmlspecialchars(blogger.fb_name));
                        $('#blog3').val(Htmlspecialchars(blogger.fb_link));
                        break;
                    case 'Instagram':
                        $('#blog2').val(Htmlspecialchars(blogger.ig_name));
                        $('#blog3').val(Htmlspecialchars(blogger.ig_link));
                        break;
                    case 'YouTube':
                        $('#blog2').val(Htmlspecialchars(blogger.youtube_name));
                        $('#blog3').val(Htmlspecialchars(blogger.youtube_link));
                        break;
                }
            }
            
            function ChangePriceType()
            {
                $('input#totalprice').val(0);
                $('input#totalprice2').val(0);
                $('input#totalprice3').val(0);

                for (var idx=1; idx<=9; idx++) {
                    var item = '#price'+ idx;

                    var priceTaxIncluded = $(item).data('cost');
                    var priceProfitIncluded = $(item).data('profit');

                    if ($(item).is(':checked')) {
                        var cost = NumberFormat(parseFloat($('input#totalprice').val() ? $('input#totalprice').val() : 0) + parseFloat(priceTaxIncluded), '', '', '');
                        var price = NumberFormat(parseFloat($('input#totalprice2').val() ? $('input#totalprice2').val() : 0) + parseFloat(priceProfitIncluded), '', '', '');
                        
                        $('input#totalprice').val(cost);
                        $('input#totalprice2').val(price);
                        
                        delete cost, price;
                    }

                    var profit = NumberFormat(parseFloat($('input#totalprice2').val() ? $('input#totalprice2').val() : 0) - parseFloat($('input#totalprice').val() ? $('input#totalprice').val() : 0), '', '', '');
                    $('input#totalprice3').val(profit);

                    delete item, priceTaxIncluded, priceProfitIncluded, profit;
                }

                if ($('#price10').is(':checked') && parseFloat($('#other_price').val())) {
                    var otherPrice = parseFloat($('#other_price').val()) ? parseFloat($('#other_price').val()) : 0;
                    var otherPriceProfitIncluded = NumberFormat(parseFloat(otherPrice) * <?= getInfuencerPriceRate('outer_tax_included', $objBlogger->getVar('payment_method')); ?>, '', '', '');
                    var cost = NumberFormat(parseFloat($('input#totalprice').val() ? $('input#totalprice').val() : 0) + parseFloat(otherPrice), '' , '', '');
                    var price = NumberFormat(parseFloat($('input#totalprice2').val() ? $('input#totalprice2').val() : 0) + parseFloat(otherPriceProfitIncluded), '' , '', '');
                    
                    $('input#totalprice').val(cost);
                    $('input#totalprice2').val(price);
                    
                    var profit = NumberFormat(parseFloat($('input#totalprice2').val() ? $('input#totalprice2').val() : 0) - parseFloat($('input#totalprice').val() ? $('input#totalprice').val() : 0), '', '', '');
                    $('input#totalprice3').val(profit);

                    delete otherPrice, otherPriceProfitIncluded, cost, price, profit;
                }
            }

            function ChangeQuotation()
            {
                var quotation = parseFloat($('input#totalprice2').val()) ? parseFloat($('input#totalprice2').val()) : 0;
                var cost = parseFloat($('input#totalprice').val()) ? parseFloat($('input#totalprice').val()) : 0;
                var profit = NumberFormat(quotation - cost, '', '', '');
                $('input#totalprice3').val(profit);
                delete quotation, cost, profit;
            }

            function ChangeCost()
            {
                var cost = parseFloat($('input#totalprice').val()) ? parseFloat($('input#totalprice').val()) : 0;
                var quotation = cost * <?= getInfuencerPriceRate('outer_tax_included', $objBlogger->getVar('payment_method')); ?>;
                $('input#totalprice2').val(quotation);
                delete cost, quotation;
                
                ChangeQuotation();
            }

            var blogger = <?= json_encode($objBlogger->fields); ?>;
        </script>
    </body>
</html>