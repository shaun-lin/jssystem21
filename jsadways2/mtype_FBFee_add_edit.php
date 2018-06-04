<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="js/jquery.xml2json.js"></script>

<script>
    $(document).ready(function() {
        Page_Init();
    });
    
    function Page_Init()
    {
        <?php

            if (isset($editMode)) {
                include __DIR__ .'/campaign_required_select_edit.php';
            } else {
                include __DIR__ .'/campaign_required_select.php';
            }

            echo $Select_str;

        ?>
    }

    <?php for ($i=1; $i<=5; $i++) : ?>
        function compare<?php echo $i; ?>()
        {
            var f = document.getElementById('date<?php echo ($i * 2 - 1); ?>').value,
            e = document.getElementById('date<?php echo ($i * 2); ?>').value;

            if (Date.parse(f.valueOf()) > Date.parse(e.valueOf())) {
                alert('警告！到期日期不能小於起始日期');
                document.getElementById('date<?php echo ($i * 2); ?>').value = document.getElementById('date<?php echo ($i * 2 - 1); ?>').value;
                document.getElementById('days<?php echo $i; ?>').value = 1;
                document.getElementById('days').value = Number(document.getElementById('days1').value) + Number(document.getElementById('days2').value) + Number(document.getElementById('days3').value) + Number(document.getElementById('days4').value) + Number(document.getElementById('days5').value);
            } else {
                var ff = new Date(f);
                var ee = new Date(e);
                var d = ((ee - ff) / 86400000) + 1;
                document.getElementById('days<?php echo $i; ?>').value = d;
                document.getElementById('days').value = Number(document.getElementById('days1').value) + Number(document.getElementById('days2').value) + Number(document.getElementById('days3').value) + Number(document.getElementById('days4').value) + Number(document.getElementById('days5').value);
            }
        }

        function change<?php echo $i; ?>()
        {
            document.getElementById('totalprice<?php echo $i; ?>').value = document.getElementById('price<?php echo $i; ?>').value;
            change();
        }
    <?php endfor; ?>

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
        
        <?php if ($_GET['cue'] == 2) : ?>
            document.getElementById('a1').value = Math.round((Number(document.getElementById('totalprice').value) * <?php echo $commission1; ?>) / 100);
            document.getElementById('a2').value = Math.round((Number(document.getElementById('totalprice').value) * <?php echo $commission4; ?>) / 100);
            document.getElementById('a3').value = Math.round((Number(document.getElementById('totalprice').value) * Number(document.getElementById('profit').value)) / 100);
            document.getElementById('a4').value = Math.round(document.getElementById('totalprice').value - document.getElementById('a1').value - document.getElementById('a2').value - document.getElementById('a3').value);
        <?php endif; ?>
    }
</script>

<fieldset>
    <div class="control-group">
        <label class="control-label" for="SelectType">類別&nbsp;(Type)</label>
        <div class="controls">
            <select id="SelectType" name="SelectType">
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="SelectSystem">系統&nbsp;(System)</label>
        <div class="controls">
            <select id="SelectSystem" name="SelectSystem">
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="SelectSystem"></label>
        <div class="controls">
            <div style="display: inline-block;">
                <label>
                    <input type="checkbox" name="channel" value="客戶帳號" <?= isset($row2['channel']) && $row2['channel'] == '客戶帳號' ? 'checked' : ''; ?>>帳號是否為客戶自有
                </label>
            </div>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="SelectSystem">服務費百分比</label>
        <div class="controls">
            <input type="text" name="action" style="width: 30px;" value="<?= isset($row2['action']) ? $row2['action'] : ''; ?>"> %
        </div>
    </div>

    <?php for ($i=1; $i<=5; $i++) : ?>
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
            <label class="control-label" for="date01">刊登期間<?php echo $i; ?>(Period)</label>
            <div class="controls">
                <input type="text" class="datepicker" id="date<?php echo ($i * 2) - 1; ?>" name="date<?php echo ($i * 2) - 1; ?>" value="<?php echo $$varDateAlias1; ?>" style="width: 100px;">
                ~
                <input type="text" name="date<?php echo $i * 2; ?>" class="input-xlarge datepicker" id="date<?php echo $i * 2; ?>" value="<?php echo $$varDateAlias2; ?>" style="width: 100px;" onChange="compare<?php echo $i; ?>();">
                &nbsp;&nbsp;&nbsp;共
                <input class="input-xlarge" id="days<?php echo $i; ?>" name="days<?php echo $i; ?>" type="text" value="<?php echo $$varDaysAlias; ?>" style="width: 30px;" readonly>
                天&nbsp;&nbsp;&nbsp;費用
                <input class="input-xlarge" id="price<?php echo $i; ?>" name="price<?php echo $i; ?>" type="text" value="<?php echo $$varPrice1Alias; ?>" style="width: 90px;" <?php if ($_GET['cue'] == 2) { echo 'readonly'; } ?>  onChange="change<?php echo $i; ?>();">
                <input id="totalprice<?php echo $i; ?>" name="totalprice<?php echo $i; ?>" type="hidden" value="<?php echo $$varTotalprice1Alias; ?>">
            </div>
        </div>
    <?php endfor; ?>

    <div class="control-group">
        <label class="control-label" for="days">天數&nbsp;(Days)</label>
        <div class="controls">
            <input class="input-xlarge" id="days" name="days" type="text" value="<?php echo isset($row2['days']) ? $row2['days'] : ''; ?>" style="width: 100px;">天
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="totalprice">總金額&nbsp;(Total)</label>
        <div class="controls">
            $<input class="input-xlarge" id="totalprice" name="totalprice" type="text" value="<?php echo isset($row2['totalprice']) ? $row2['totalprice'] : ''; ?>" style="width: 100px;" required>元
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="others">備註</label>
        <div class="controls">
            <textarea id="others" name="others" ><?php echo isset($row2['others']) ? $row2['others'] : ''; ?></textarea>
        </div>
    </div>

    <?php if (!isset($editMode) && $_GET['cue'] == 1) : ?>
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
    <?php endif; ?>

    <?php if (isset($editMode)) : ?>
        <input type="hidden" name="website" value="<?php echo $row2['website']; ?>">
    <?php endif; ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?php echo isset($editMode) ? '確定修改' : '新增媒體' ?></button>
    </div>
</fieldset>