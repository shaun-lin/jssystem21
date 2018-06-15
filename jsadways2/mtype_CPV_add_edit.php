
<script type="text/javascript">
    var jsonScenery = [];
    var jsonHotel = [];

    $(document).ready(function() {
        Page_Init();
    });

    function Page_Init()
    {
        var jsonData = <?= json_encode(array_values($mediaCategoryDefinition)); ?>;

        $('#SelectCategory').empty().append($('<option></option>').val('').text('------'));

        $.each(jsonData, function(i, item) {
            $('#SelectCategory').append($('<option></option>').val(item.categoryId).text(item.categoryName));
        });

        $('#SelectSubCategory').empty().append($('<option></option>').val('').text('------'));

        $('#SelectCategory').change(function() {
            ChangeCategory();
        });

        $('#SelectThreeCategory').empty().append($('<option></option>').val('').text('------'));

        $('#SelectSubCategory').change(function() {
            ChangeSubCategory();
        });

        $('#SelectThreeCategory').change(function() {
            ChangeThreeCategory();
        });

        <?php

            if (isset($editMode)) {
                include dirname(__DIR__) .'/jsadways2/campaign_required_select_edit.php';
            } else {
                include dirname(__DIR__) .'/jsadways2/campaign_required_select.php';
            }

            echo $Select_str;

        ?>

        <? if (isset($editMode)) : ?>
            $('#SelectCategory option').each(function() {
                if ($(this).html() == '<?= $row2['channel']; ?>') {
                    $(this).attr('selected', 'selected');
                    $('#SelectCategory').change();
                    return false;
                }
            });

            $('#SelectSubCategory option').each(function() {
                if ($(this).html() == '<?= $row2['phonesystem']; ?>') {
                    $(this).attr('selected', 'selected');
                    $('#SelectSubCategory').change();
                    return false;
                }
            });

            $('#SelectThreeCategory option').each(function() {
                if ($(this).html() == '<?= $row2['position']; ?>') {
                    $(this).attr('selected', 'selected');
                    $('#SelectThreeCategory').change();
                    return false;
                }
            });
        <? endif; ?>
    }


    function ChangeCategory()
    {
        $('#SelectSubCategory').empty().append($('<option></option>').val('').text('------'));
        $('#SelectThreeCategory').empty().append($('<option></option>').val('').text('------'));

        var categoryId = $.trim($('#SelectCategory option:selected').val());

        var jsonData = [];

        if (categoryId == '1') {
            <?php 
                $subCategoryData = $mediaSubCategoryDefinition;
                echo "jsonData = ". json_encode(array_values($subCategoryData)) .";";
            ?>
        }

        if (categoryId.length != 0) {
            $.each(jsonData , function(i, item) {
                $('#SelectSubCategory').append($('<option></option>').val(item.subCategoryId).text(item.subCategoryName));
            });
        }
    }

    function ChangeSubCategory()
    {
        $('#SelectThreeCategory').empty().append($('<option></option>').val('').text('------'));

        var jsonData = [];
        var subCategoryId = $.trim($('#SelectCategory option:selected').val());

        <?php
            $jsStatement = [];

            foreach ($positionDefinition as $subCategoryId => $threeCategoryId) {
                $jsStatement[] = "if (subCategoryId == '{$subCategoryId}') {
                    jsonData = [". json_encode($mediaThreeCategoryDefinition[$threeCategoryId]) ."];
                }";
            }

            echo implode(' else ', $jsStatement);
        ?>

        if (subCategoryId.length != 0) {
            $.each(jsonData , function(i, item) {
                $('#SelectThreeCategory').append($('<option></option>').val(item.threeCategoryId).text(item.threeCategoryName));
            });
        }
    }

    function ChangeThreeCategory()
    {
        var categoryId = $.trim($('#SelectCategory option:selected').val());
        var categoryName = $.trim($('#SelectCategory option:selected').text());
        var subCategoryId = $.trim($('#SelectThreeCategory option:selected').val());
        var subCategoryName = $.trim($('#SelectThreeCategory option:selected').text());

        <?php
            $jsVariable = [];
            $jsStatement = [];
            $sqlSize = sprintf("SELECT * FROM `sizeformat` WHERE `mediaid` = %d;", $mediaOrdinal);

            $resultSize = mysql_query($sqlSize);

            while ($rowSize = mysql_fetch_array($resultSize)) {
                
                $jsVariable[$mediaSizeformatDefinition[$rowSize['id']]] = [
                    'format1' => $rowSize['format1'],
                    'format2' => $rowSize['format2']
                ];

                $jsStatement[] = "if (subCategoryId == '{$mediaSizeformatDefinition[$rowSize['id']]}') {
                    document.getElementById('format1').value = dataSizeFormat['{$mediaSizeformatDefinition[$rowSize['id']]}']['format1'];
                    document.getElementById('format2').value = dataSizeFormat['{$mediaSizeformatDefinition[$rowSize['id']]}']['format2'];
                }";
            }

            echo "var dataSizeFormat = ". json_encode($jsVariable) .";";
            echo implode(' else ', $jsStatement);
        ?>
    }

    <? for ($i=1; $i<=5; $i++) : ?>
        function compare<?= $i; ?>()
        {
            var f = document.getElementById('date<?= ($i * 2 - 1); ?>').value,
            e = document.getElementById('date<?= ($i * 2); ?>').value;

            if (Date.parse(f.valueOf()) > Date.parse(e.valueOf())) {
                alert('警告！到期日期不能小於起始日期');
                document.getElementById('date<?= ($i * 2); ?>').value = document.getElementById('date<?= ($i * 2 - 1); ?>').value;
                document.getElementById('days<?= $i; ?>').value = 1;
                document.getElementById('days').value = Number(document.getElementById('days1').value) + Number(document.getElementById('days2').value) + Number(document.getElementById('days3').value) + Number(document.getElementById('days4').value) + Number(document.getElementById('days5').value);
            } else {
                var ff = new Date(f);
                var ee = new Date(e);
                var d = ((ee - ff) / 86400000) + 1;
                document.getElementById('days<?= $i; ?>').value = d;
                document.getElementById('days').value = Number(document.getElementById('days1').value) + Number(document.getElementById('days2').value) + Number(document.getElementById('days3').value) + Number(document.getElementById('days4').value) + Number(document.getElementById('days5').value);
            }
        }

        function change<?= $i.$i; ?>()
        {
            document.getElementById('price<?= $i; ?>').value = document.getElementById('impression<?= $i; ?>').value / document.getElementById('totalprice<?= $i; ?>').value;
            change();
        }

        function change<?= $i; ?>()
        {
            <? if ($_GET['cue'] == 1) : ?>
                document.getElementById('click<?= $i; ?>').value = Math.round(document.getElementById('totalprice<?= $i; ?>').value / document.getElementById('price<?= $i; ?>').value);
                document.getElementById('impression<?= $i; ?>').value = Math.round((document.getElementById('click<?= $i; ?>').value / document.getElementById('ctr').value)*100);
            <? endif; ?>

            <? if ($_GET['cue'] == 2) : ?>
                document.getElementById('price<?= $i; ?>').value = document.getElementById('impression<?= $i; ?>').value / document.getElementById('totalprice<?= $i; ?>').value;
            <? endif; ?>

            change();
        }
    <? endfor; ?>
              
    <?php
        if ($row1['agency_id'] != 0) {
            $sql4 = sprintf("SELECT * FROM `commission` WHERE `agency` = %d AND `media` = %d;", $row1['agency_id'], $_GET['media']);
            $result4 = mysql_query($sql4);
            $row4 = mysql_fetch_array($result4);
            if ($row4['commission5'] != 0) {
                $commission1 = $row4['commission1'];
                $commission4 = $row4['commission4'];
            } else {
                $commission1 = 0;
                $commission4 = 0;
            }
        } else {
            $commission1 = 0;
            $commission4 = 0;
        }

        $sqlmedia = sprintf("SELECT * FROM `media` WHERE `id` = %d;", $_GET['media']);
        $resultmedia = mysql_query($sqlmedia);
        $rowmedia = mysql_fetch_array($resultmedia);
        $profit = $rowmedia['profit'];
    ?>

    function change()
    {
        document.getElementById('totalprice').value = Number(document.getElementById('totalprice1').value) + Number(document.getElementById('totalprice2').value) + Number(document.getElementById('totalprice3').value) + Number(document.getElementById('totalprice4').value) + Number(document.getElementById('totalprice5').value);
        document.getElementById('number1').value = Number(document.getElementById('click1').value) + Number(document.getElementById('click2').value) + Number(document.getElementById('click3').value) + Number(document.getElementById('click4').value) + Number(document.getElementById('click5').value);
        
        <? if ($_GET['cue'] == 1) : ?>
            document.getElementById('number2').value = Number(document.getElementById('impression1').value) + Number(document.getElementById('impression2').value) + Number(document.getElementById('impression3').value) + Number(document.getElementById('impression4').value) + Number(document.getElementById('impression5').value);
        <? endif; ?>

        <? if ($_GET['cue'] == 2) : ?>
            document.getElementById('a1').value = Math.round((Number(document.getElementById('totalprice').value) * <?= $commission1; ?>) / 100);
            document.getElementById('a2').value = Math.round((Number(document.getElementById('totalprice').value) * <?= $commission4; ?>) / 100);
            document.getElementById('a3').value = Math.round((Number(document.getElementById('totalprice').value) * Number(document.getElementById('profit').value)) / 100);
            document.getElementById('a4').value = Math.round(document.getElementById('totalprice').value - document.getElementById('a1').value - document.getElementById('a2').value - document.getElementById('a3').value);
        <? endif; ?>
    }
</script>
<fieldset>
    <div class="control-group">
        <label class="control-label" for="SelectType">類別(Type)</label>
        <div class="controls">
            <select id="SelectType" name="SelectType">
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="SelectSystem">系統(System)</label>
        <div class="controls">
            <select id="SelectSystem" name="SelectSystem">
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="SelectCategory">頻道(Channel)</label>
        <div class="controls">
            <select id="SelectCategory" name="SelectCategory" style="width:400px">
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="SelectSubCategory">系統(System)</label>
        <div class="controls">
            <select id="SelectSubCategory" name="SelectSubCategory" style="width:400px">
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="SelectThreeCategory">版位(Position)</label>
        <div class="controls">
            <select id="SelectThreeCategory" name="SelectThreeCategory" style="width:500px">
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label">規格(Size)</label>
        <div class="controls">
            <textarea id="format1" name="format1" readonly><?= isset($row2['format1']) ? $row2['format1'] : ''; ?></textarea>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label">格式(Format)</label>
        <div class="controls">
            <textarea id="format2" name="format2" readonly><?= isset($row2['format2']) ? $row2['format2'] : ''; ?></textarea>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="wheel">輪替/固定(R/F)</label>
        <div class="controls">
            <select id="wheel" name="wheel">
                <option value="R">R</option>
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="typeahead">預估觀看率(Est. CTR)</label>
        <div class="controls">
            <input class="input-xlarge" id="ctr" name="ctr" type="text" onChange="number()" value="<?= isset($row2['ctr']) ? $row2['ctr'] : '18'; ?>" style="width:100px">%
        </div>
    </div>

    <? for ($i=1; $i<=5; $i++) : ?>
        <?php
            $varDateAlias1 = 'varDate'. (($i * 2) - 1);
            $varDateAlias2 = 'varDate'. ($i * 2);
            $varDaysAlias = 'varDays'. $i;
            $varPrice1Alias = 'varPrice1'. $i;
            $varTotalprice1Alias = 'varTotalprice1'. $i;
            $varClick1Alias = 'varClick1'. $i;
            $varImpression1 = 'varImpression1'. $i;

            $$varDateAlias1 = (isset($row2['date'. (($i * 2) - 1)]) && $row2['date'. (($i * 2) - 1)] != 0) ? (date('m', $row2['date'. (($i * 2) - 1)]) .'/'. date('d',$row2['date'. (($i * 2) - 1)]) .'/'. date('Y', $row2['date'. (($i * 2) - 1)])) : '';
            $$varDateAlias2 = (isset($row2['date'. ($i * 2)]) && $row2['date'. ($i * 2)] != 0) ? (date('m', $row2['date'. ($i * 2)]) .'/'. date('d', $row2['date'. ($i * 2)]) .'/'. date('Y', $row2['date'. ($i * 2)])) : '';
            $$varDaysAlias = isset($row2['days'. $i]) ? $row2['days'. $i] : '';
            $$varPrice1Alias = isset($row2['price'. $i]) ? $row2['price'. $i] : '';
            $$varTotalprice1Alias = isset($row2['totalprice'. $i]) ? $row2['totalprice'. $i] : '';
            $$varClick1Alias = isset($row2['click'. $i]) ? $row2['click'. $i] : '';
            $$varImpression1 = isset($row2['impression'. $i]) ? $row2['impression'. $i] : '';
        ?>
        <div class="control-group">
            <label class="control-label" for="date01">刊登期間<?= $i; ?>(Period)</label>
            <div class="controls">
                <input type="text" class="datepicker" id="date<?= ($i * 2) - 1; ?>" name="date<?= ($i * 2) - 1; ?>" value="<?= $$varDateAlias1; ?>" style="width: 100px;">
                ~
                <input type="text" name="date<?= $i * 2; ?>" class="input-xlarge datepicker" id="date<?= $i * 2; ?>" value="<?= $$varDateAlias2; ?>" style="width: 100px;" onChange="compare<?= $i; ?>();">
                共
                <input class="input-xlarge" id="days<?= $i; ?>" name="days<?= $i; ?>" type="text" value="<?= $$varDaysAlias; ?>" style="width: 30px;" readonly>
                天&nbsp;&nbsp;&nbsp;CPV定價
                <input class="input-xlarge" id="price<?= $i; ?>" name="price<?= $i; ?>" type="text" value="<?= $$varPrice1Alias; ?>" onchange="change<?= $i; ?>();" style="width: 30px;" <?= ($_GET['cue'] == 2 ? 'readonly' : ''); ?>>
                &nbsp;&nbsp;&nbsp;總價
                <input class="input-xlarge" id="totalprice<?= $i; ?>" name="totalprice<?= $i; ?>" type="text" value="<?= $$varTotalprice1Alias; ?>" onchange="change<?= $i; ?>();" style="width: 70px;">
                <? if ($_GET['cue'] == 1) : ?>
                    &nbsp;&nbsp;&nbsp;預估觀看
                    <input class="input-xlarge" id="click<?= $i; ?>" name="click<?= $i; ?>" type="text" onChange="change<?= $i . $i; ?>();" value="<?= $$varClick1Alias; ?>" style="width: 70px;">
                <? endif; ?>
                &nbsp;&nbsp;&nbsp;預估曝光
                <input class="input-xlarge" id="impression<?= $i; ?>" name="impression<?= $i; ?>" onChange="change();" type="text" value="<?= $$varImpression1; ?>"  style="width: 70px;">
                
                <? if (isset($editMode) && $_GET['cue'] == 2 && (strlen($$varClick1Alias) || strlen($$varTotalprice1Alias))) : ?>
                    <font color="#FF0000">*總價及預估觀看欄位非系統自動換算，請自行確認數字是否正確</font>
                <? endif; ?>
            </div>
        </div>
    <? endfor; ?>

    <div class="control-group">
        <label class="control-label" for="days">天數(Days)</label>
        <div class="controls">
            <input class="input-xlarge" id="days" name="days" type="text" value="<?= isset($row2['days']) ? $row2['days'] : ''; ?>" style="width:100px">天
        </div>
    </div>

    <div class="control-group">
        <label class="control-label">素材提供期限(Material Due)</label>
        <div class="controls">
            <input class="datepicker" id="due" name="due" type="text" value="<?= isset($row2['due']) ? $row2['due'] : '上線日前3天'; ?>" style="width:100px">
        </div>
    </div>

    <? if ($_GET['cue'] == 2) : ?>
        <div class="control-group">
        <label class="control-label" for="profit">修改利潤%</label>
            <div class="controls">
                <input class="input-xlarge" id="profit" name="profit" type="text" value="<?= $profit; ?>" style="width:100px" onChange="change()">
            </div>
        </div>
    <? endif; ?>

    <div class="control-group">
        <label class="control-label" for="totalprice">總投放金額(Total)</label>
        <div class="controls">
            $<input class="input-xlarge" id="totalprice" name="totalprice" type="text" value="<?= isset($row2['totalprice']) ? $row2['totalprice'] : ''; ?>" style="width:100px" required>元
        </div>
    </div>

    <? if ($_GET['cue'] == 2) : ?>
        <div class="control-group">
            <label class="control-label" for="a1">佣金<?= $commission1; ?>%</label>
            <div class="controls">
                <input class="input-xlarge" id="a1" name="a1" type="text" value="<?= isset($row2['a1']) ? $row2['a1'] : ''; ?>" style="width:100px" readonly>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="a2">現折<?= $commission4; ?>%</label>
            <div class="controls">
                <input class="input-xlarge" id="a2" name="a2" type="text" value="<?= isset($row2['a2']) ? $row2['a2'] : ''; ?>" style="width:100px" readonly>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="a3">利潤</label>
            <div class="controls">
                <input class="input-xlarge" id="a3" name="a3" type="text" value="<?= isset($row2['a3']) ? $row2['a3'] : ''; ?>" style="width:100px" readonly>
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="a4">行政發媒體金額</label>
            <div class="controls">
                <input class="input-xlarge" id="a4" name="a4" type="text" value="<?= isset($row2['a4']) ? $row2['a4'] : ''; ?>" style="width:100px" readonly>
            </div>
        </div>
    <? endif; ?>

    <? if ($_GET['cue'] == 1) : ?>
        <div class="control-group">
            <label class="control-label" for="number1">預估觀看數(Est. Clicks)</label>
            <div class="controls">
                <input class="input-xlarge" id="number1" name="number1" type="text" value="<?= isset($row2['quantity']) ? $row2['quantity'] : ''; ?>" style="width:100px" readonly>
            </div>
        </div>
    <? endif; ?>

    <div class="control-group">
        <label class="control-label" for="number2">預估曝光數(Est. Impressions)</label>
        <div class="controls">
            <input class="input-xlarge" id="number2" name="number2" type="text" value="<?= isset($row2['quantity2']) ? $row2['quantity2'] : ''; ?>" style="width:100px" readonly>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="others">備註</label>
        <div class="controls">
            <textarea id="others" name="others" ><?= isset($row2['others']) ? $row2['others'] : ''; ?></textarea>
        </div>
    </div>

    <? if ($_GET['cue'] == 1) : ?>
        <div class="control-group">
            <label class="control-label" for="pr">是否為PR</label>
            <div class="controls">
                <input type="checkbox" name="pr" value="1">勾選後此筆記錄將會是PR案件，總金額為0
            </div>
        </div>

        <div class="control-group">
            <label class="control-label" for="samecue">是否同時產生對內媒體</label>
            <div class="controls">
                <input type="checkbox" name="samecue" value="1" checked>勾選後即會在對內cue表產生同筆媒體
            </div>
        </div>
    <? endif; ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?= empty($editMode) ? '新增媒體' : '確定修改'; ?></button>
    </div>
</fieldset>
