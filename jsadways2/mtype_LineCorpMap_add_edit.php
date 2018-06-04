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

            if (isset($editMode)) {
                ?>
                    $('select[name="channel"]').find('option[value="<?= $row2['channel']; ?>"]').attr('selected', 'selected');
                    $('select[name="phonesystem"]').find('option[value="<?= $row2['phonesystem']; ?>"]').attr('selected', 'selected');
                    $('select[name="channel"]').val('<?= $row2['channel']; ?>');
                    $('select[name="phonesystem"]').val('<?= $row2['phonesystem']; ?>');
                <?php
            }
        ?>
    }

    <?php for ($i=1; $i<=1; $i++) : ?>
        function compare<?php echo $i; ?>()
        {
            var f = document.getElementById('date<?php echo ($i * 2 - 1); ?>').value,
            e = document.getElementById('date<?php echo ($i * 2); ?>').value;

            if (Date.parse(f.valueOf()) > Date.parse(e.valueOf())) {
                alert('警告！到期日期不能小於起始日期');
                document.getElementById('date<?php echo ($i * 2); ?>').value = document.getElementById('date<?php echo ($i * 2 - 1); ?>').value;
                document.getElementById('days<?php echo $i; ?>').value = 1;
                document.getElementById('days').value = Number(document.getElementById('days1').value);
            } else {
                var ff = new Date(f);
                var ee = new Date(e);
                var d = ((ee - ff) / 86400000) + 1;
                document.getElementById('days<?php echo $i; ?>').value = d;
                document.getElementById('days').value = Number(document.getElementById('days1').value);
            }
        }

        function change<?php echo $i.$i; ?>()
        {
            document.getElementById('price<?php echo $i; ?>').value = document.getElementById('totalprice<?php echo $i; ?>').value / document.getElementById('click<?php echo $i; ?>').value;
            change();
        }

        function change<?php echo $i; ?>()
        {
            <?php if ($_GET['cue'] == 1) : ?>
                document.getElementById('totalprice<?php echo $i; ?>').value = Math.round(document.getElementById('click<?php echo $i; ?>').value * document.getElementById('price<?php echo $i; ?>').value);
            <?php endif; ?>
            <?php if ($_GET['cue'] == 2) : ?>
                document.getElementById('price<?php echo $i; ?>').value = (document.getElementById('totalprice<?php echo $i; ?>').value * 1000) / document.getElementById('click<?php echo $i; ?>').value;
            <?php endif; ?>

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
        document.getElementById('totalprice').value = Number(document.getElementById('totalprice1').value);
        document.getElementById('quantity').value = Number(document.getElementById('click1').value);
        
        <?php if ($_GET['cue'] == 2) : ?>
            document.getElementById('a1').value = Math.round((Number(document.getElementById('totalprice').value) * <?= $commission1; ?>) / 100);
            document.getElementById('a2').value = Math.round((Number(document.getElementById('totalprice').value) * <?= $commission4; ?>) / 100);
            document.getElementById('a3').value = Math.round((Number(document.getElementById('totalprice').value) * Number(document.getElementById('profit').value)) / 100);
            document.getElementById('a4').value = Math.round(document.getElementById('totalprice').value - document.getElementById('a1').value - document.getElementById('a2').value - document.getElementById('a3').value);
        <?php endif; ?>
    }
</script>

<fieldset>
    <div class="control-group">
        <label class="control-label" for="SelectSystem">系統&nbsp;(System)</label>
        <div class="controls">
            <select id="phonesystem" name="phonesystem">
                <option> ----- </option>
                <option value="android">Android</option>
                <option value="iOS">iOS</option>
                <option value="android / iOS 雙系統">Android / iOS 雙系統</option>
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="SelectSystem">執行內容&nbsp;(Action)</label>
        <div class="controls">
            <select name="channel">
                <option> ----- </option>
                <option value="靜態貼圖">靜態貼圖</option>
                <option value="動態貼圖">動態貼圖</option>
                <option value="靜態聲音貼圖">靜態聲音貼圖</option>
                <option value="動態聲音貼圖">動態聲音貼圖</option>
                <option value="全螢幕動態貼圖">全螢幕動態貼圖</option>
                <option value="全螢幕動態聲音踢圖">全螢幕動態聲音踢圖</option>
                <option value="影音貼圖Mustview">影音貼圖Mustview</option>
            </select>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="SelectThreeCategory">版位&nbsp;(Position)</label>
        <div class="controls">
            <input class="input-xlarge" id="position" name="position" type="text"  value="Line 貼圖小舖" readonly>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label">規格&nbsp;(Size)</label>
        <div class="controls">
            <textarea id="format1" name="format1" readonly>請參照Line提供最新版上線與素材規範</textarea>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label">格式&nbsp;(Format)</label>
        <div class="controls">
            <textarea id="format2" name="format2" readonly>請參照Line提供最新版上線與素材規範</textarea>
        </div>
    </div>

    <?php for ($i=1; $i<=1; $i++) : ?>
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
                天 定價
                <input class="input-xlarge" id="price<?= $i; ?>" name="price<?= $i; ?>" type="text" value="<?= $$varPrice1Alias; ?>" style="width: 30px;" <?php if ($_GET['cue'] == 2) { echo 'readonly'; } ?>>
                數量
                <input class="input-xlarge" id="click<?= $i; ?>" name="click<?= $i; ?>" type="text" value="<?= $$varClick1Alias; ?>" onChange="change<?= $i; ?>()" style="width: 70px;">
                總價
                <input class="input-xlarge" id="totalprice<?= $i; ?>" name="totalprice<?= $i; ?>" type="text" value="<?= $$varTotalprice1Alias; ?>" onChange="change<?= $i; ?>()" style="width: 70px;">
            </div>
        </div>
    <?php endfor; ?>

    <div class="control-group">
        <label class="control-label" for="days">天數&nbsp;(Days)</label>
        <div class="controls">
            <input class="input-xlarge" id="days" name="days" type="text" value="<?= isset($row2['days']) ? $row2['days'] : ''; ?>" style="width: 100px;">天
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="quantity">數量(Est. Actions)</label>
        <div class="controls">
            <input class="input-xlarge" id="quantity" name="quantity" type="text" value="<?= isset($row2['quantity']) ? $row2['quantity'] : '' ; ?>" style="width:100px" required>
        </div>
    </div>

    <?php if ($_GET['cue'] == 2) : ?>
        <div class="control-group">
            <label class="control-label" for="profit">修改利潤%</label>
            <div class="controls">
                <input class="input-xlarge" id="profit" name="profit" type="text" value="<?= $profit; ?>" style="width:100px" onChange="change()">
            </div>
        </div>
    <?php endif; ?>

    <div class="control-group">
        <label class="control-label" for="totalprice">總價</label>
        <div class="controls">
            $<input class="input-xlarge" id="totalprice" name="totalprice" type="text" value="<?= isset($row2['totalprice']) ? $row2['totalprice'] : ''; ?>" style="width:100px" required>元
        </div>
    </div>

    <?php if ($_GET['cue'] == 2) : ?>
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
    <?php endif; ?>

    <div class="control-group">
        <label class="control-label" for="others">備註</label>
        <div class="controls">
            <textarea id="others" name="others" ><?= isset($row2['others']) ? $row2['others'] : ''; ?></textarea>
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
        <input type="hidden" name="website" value="<?= $row2['website']; ?>">
    <?php endif; ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?= isset($editMode) ? '確定修改' : '新增媒體' ?></button>
    </div>
</fieldset>