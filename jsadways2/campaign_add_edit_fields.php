<?php

    $objMrbsUsers = CreateObject('MrbsUsers');
    $rowsMediaMember = $objMrbsUsers->searchAll([
        "(`id` = 18 OR `departmentid` IN (21, 22))",
        "(`user_resign_date` IS NULL OR `user_resign_date` = '0000-00-00')"
    ], '', '', '', '', 'id, name, username');

    if (empty($excludeWomm)) {
        $rowsWomm = $objMrbsUsers->searchAll([
            "(`id` = 18 OR `departmentid` IN (19, 20)) AND `id` NOT IN (142, 145)",
            "(`user_resign_date` IS NULL OR `user_resign_date` = '0000-00-00')"
        ], '', '', '', '', 'id, name, username');
    }

?>
<script>
    function CheckForm()
    {
        if ($('#date1').val() == '') {
            alert('請輸入案件起始日期');
            return false;
        }

        if ($('#date2').val() == '') {
            alert('請輸入案件到期日期');
            return false;
        }

        return compare();
    }

    function ChangeEndDateDefault()
    {
        if ($("#date1").val() != '') {
            var startDate = document.getElementById('date1').value, endDate = document.getElementById('date2').value;

            if ($("#date2").val() == '' || Date.parse(startDate.valueOf()) > Date.parse(endDate.valueOf())) {
                $("#date2").datepicker("option", "defaultDate", $("#date1").val());
                setTimeout(function() {$("#date2").focus()}, 300);
            } else {
                $("#date2").datepicker("option", "defaultDate", $("#date2").val());
            }
        }
    }
</script>
<div class="row-fluid">
    <div class="box span8">
        <div class="box-header well" data-original-title>
            <h2><i class="icon-edit"></i> <?= empty($editMode) ? '新增' : '編輯'; ?>案件</h2>
        </div>
        <div class="box-content">
            <form class="form-horizontal" action="campaign_<?= empty($editMode) ? 'add' : 'edit'; ?>2.php" method="post" onsubmit="return CheckForm();">
                <fieldset>
                    <div class="control-group">
                        <label class="control-label" for="typeahead">案件名稱(Campaign)</label>
                        <div class="controls">
                            <input class="input-xlarge" id="focusedInput" type="text" name="name" value="<?= isset($row3['name']) ? $row3['name'] : ''; ?>" required>
                        </div>
                    </div>

                    <?php if (in_array($_SESSION['userid'], [30, 65, 25, 71]) || in_array($_SESSION['usergroup'], [6])) : ?>
                        <div class="control-group">
                            <label class="control-label" for="date01">是否為日企</label>
                            <div class="controls">
                                <input type="checkbox" id="is_jp" name="is_jp" <?= isset($row3['is_jp']) && $row3['is_jp'] ? 'checked' : ''; ?>>For Mame
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="control-group">
                        <label class="control-label" for="date01">廣告代理商(Agency)</label>
                        <div class="controls">
                            <select id="agency" name="agency" data-rel="chosen" style="width:400px">
                                <option value="0">直客</option>
                            </select>&nbsp;&nbsp;&nbsp;&nbsp;<a href="agency_edit.php?add=1">新增代理商</a>
                            <script>
                                var selectAgency = document.getElementById('agency');
                                var agencyJsonData = <?= json_encode($selectorAgencyOptions); ?>;
                                for (var idx in agencyJsonData) {
                                    var el = document.createElement('option'); 
                                    el.value = agencyJsonData[idx]['value'];
                                    el.text = agencyJsonData[idx]['text'];
                                    selectAgency.appendChild(el);
                                }
                                <?php if (!empty($editMode)) : ?>
                                    selectAgency.value = '<?= $row3['agency_id']; ?>';
                                <?php endif; ?>
                                <?php unset($selectorAgencyOptions); ?>
                            </script>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="SelectSubCategory">代理商窗口選擇</label>
                        <div class="controls">
                            <select id="SelectSubCategory" name="SelectSubCategory">
                            </select>&nbsp;&nbsp;&nbsp;&nbsp;選擇後會將窗口資料帶到下方的欄位
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" for="fileInput">廣告主(Client)</label>
                        <div class="controls">
                            <select id="client" name="client" data-rel="chosen" style="width:400px">
                                <option value="0"> -- 請選擇廣告主 -- </option>
                            </select>&nbsp;&nbsp;&nbsp;&nbsp;<a href="client_edit.php?add=1">新增廣告主</a>
                            <script>
                                var selectClient = document.getElementById('client');
                                
                                var clientJsonDataPrepend = <?= json_encode($selectorClientOptions['prepend']); ?>;
                                for (var idx in clientJsonDataPrepend) {
                                    var el = document.createElement('option'); 
                                    el.value = clientJsonDataPrepend[idx]['value'];
                                    el.text = clientJsonDataPrepend[idx]['text'];
                                    selectClient.appendChild(el);
                                }

                                var clientJsonDataAppend = <?= json_encode($selectorClientOptions['append']); ?>;
                                for (var idx in clientJsonDataAppend) {
                                    var el = document.createElement('option'); 
                                    el.value = clientJsonDataAppend[idx]['value'];
                                    el.text = clientJsonDataAppend[idx]['text'];
                                    selectClient.appendChild(el);
                                }
                                <?php if (!empty($editMode)) : ?>
                                    selectClient.value = '<?= $row3['client_id']; ?>';
                                <?php endif; ?>
                                <?php unset($selectorClientOptions); ?>
                            </script>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="SelectSubCategory2">廣告主窗口選擇</label>
                        <div class="controls">
                            <select id="SelectSubCategory2" name="SelectSubCategory2">
                            </select>&nbsp;&nbsp;&nbsp;&nbsp;選擇後會將窗口資料帶到下方的欄位
                        </div>
                    </div>

                    <div class="control-group" style="background-color: #f4f4f4; margin-bottom: 2px; padding-bottom: 8px; padding-top: 8px;">
                        <label class="control-label" for="media_leader">媒體部PM</label>
                        <div class="controls">
                            <select id="media_leader" data-rel="chosen" name="media_leader">
                                <option value=""> -- </option>
                                <?php foreach ($rowsMediaMember as $userMedia) : ?>
                                    <option value="<?= $userMedia["id"]; ?>" <?= isset($row3['media_leader']) && $row3['media_leader'] == $userMedia['id'] ? 'selected' : ''; ?>><?= sprintf("%s【%s】", $userMedia['username'], ucfirst($userMedia['name'])); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <?php if (empty($excludeWomm)) : ?>
                        <div class="control-group" style="background-color: #f4f4f4; margin-bottom: 18px; padding-bottom: 8px; padding-top: 8px;">
                            <label class="control-label" for="SelectWomm">口碑部PM</label>
                            <div class="controls">
                                <select id="SelectWomm" data-rel="chosen" name="SelectWomm">
                                    <option value=""> -- </option>
                                    <?php foreach ($rowsWomm as $itemWomm) : ?>
                                        <option value="<?= sprintf("%d,%s %s", $itemWomm["id"], $itemWomm['username'], ucfirst($itemWomm['name'])); ?>" <?= isset($row3['wommId']) && $row3['wommId'] == $itemWomm['id'] ? 'selected' : ''; ?>><?= sprintf("%s【%s】", $itemWomm['username'], ucfirst($itemWomm['name'])); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="control-group">
                        <label class="control-label" for="date01">期間(Period)</label>
                        <div class="controls">
                            <input type="text" class="datepicker" id="date1" name="date1" value="<?= isset($row3['date1']) ? $row3['date1'] : ''; ?>" style="width: 100px;" onchange="ChangeEndDateDefault();">~<input type="text" name="date2" class="input-xlarge datepicker" id="date2" value="<?= isset($row3['date2']) ? $row3['date2'] : ''; ?>" style="width:100px" onChange="compare()">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" for="receipt">發票月份</label>
                        <div class="controls">
                            <input class="input-xlarge" id="receipt1" type="text" name="receipt1" value="<?= isset($row3['receipt1']) ? $row3['receipt1'] : date("Y"); ?>" style="width:50px">年<input class="input-xlarge" id="receipt2" type="text" name="receipt2" value="<?= isset($row3['receipt2']) ? $row3['receipt2'] : date("m"); ?>" style="width:50px">月
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" for="receipt">付款方式</label>
                        <div class="controls">
                            <select name="pay1" id="pay1">
                                <option value="支票" <?= isset($row3['pay1']) && $row3['pay1'] == '匯款' ? 'selected' : ''; ?>>支票</option>
                                <option value="匯款" <?= isset($row3['pay1']) && $row3['pay1'] == '支票' ? 'selected' : ''; ?>>匯款</option>
                                <option value="其他" <?= isset($row3['pay1']) && $row3['pay1'] == '其他' ? 'selected' : ''; ?>>其他</option>
                            </select>
                            <input class="input-xlarge" id="pay2" type="text" name="pay2" value="90" style="width:50px">天
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" for="contact1">聯絡窗口姓名</label>
                        <div class="controls">
                            <input class="input-xlarge" id="contact1" type="text" name="contact1" value="<?= isset($row3['contact1']) ? $row3['contact1'] : ''; ?>">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" for="contact2">聯絡窗口電話</label>
                        <div class="controls">
                            <input class="input-xlarge" id="contact2" type="text" name="contact2" value="<?= isset($row3['contact2']) ? $row3['contact2'] : ''; ?>">
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="contact3">聯絡窗口Email</label>
                        <div class="controls">
                            <input class="input-xlarge" id="contact3" type="text" name="contact3" value="<?= isset($row3['contact3']) ? $row3['contact3'] : ''; ?>">
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" for="title">聯絡窗口職務名稱</label>
                        <div class="controls">
                            <input class="input-xlarge" id="title" type="text" name="title" value="<?= isset($row3['title']) ? $row3['title'] : ''; ?>">
                        </div>
                    </div>

                    <?php if (!empty($editMode)) : ?>
                        <div class="control-group">
                            <label class="control-label" for="rate">匯率</label>
                            <div class="controls">
                                <input class="input-xlarge" id="rate" type="text" name="rate" value="<?= $row3['rate']; ?>">ex：  31.3 、  0.2811
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="ratetime">匯率日期</label>
                            <div class="controls">
                                <input class="input-xlarge" id="ratetime" type="text" name="ratetime" value="<?= $row3['ratetime']; ?>">
                            </div>
                        </div>

                        <?php if (empty($excludeDraw))  : ?>
                            <div class="control-group">
                                <label class="control-label" for="draw">委刊比率(是否被抽%)</label>
                                <div class="controls">
                                    <input class="input-xlarge" id="draw" type="text" name="draw" value="<?= $row3['draw']; ?>">
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div class="control-group">
                        <label class="control-label" for="others">備註</label>
                        <div class="controls">
                            <textarea id="others" name="others"><?= isset($row3['others']) ? $row3['others'] : ''; ?></textarea>
                        </div>
                    </div>

                    <?php if (!empty($editMode)) : ?>
                        <input name="id" type="hidden" value="<?= $_GET['id']; ?>">
                    <?php endif; ?>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">儲存</button>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        <? if (!empty($editMode)) : ?>
            ChangeCategory1();
            ChangeCategory3();

            $('#pay2').val('<?= $row3['pay2']; ?>');
            $('#pay1 option[value="<?= $row3['pay1']; ?>"]').attr('selected');
            $('#pay1').val('<?= $row3['pay1']; ?>');
        <? endif; ?>

        ChangeEndDateDefault();
    });
</script>