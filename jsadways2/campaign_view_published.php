<?php

    require_once dirname(__DIR__) .'/autoload.php';

    $category = CreateObject('Category');
    
    $category->load($category->getCategoryId('campaign', '上刊畫面-媒體'));
    $categoryIdForMedia = $category->getId();
    $categoryAclForMedia = 'category.'. $category->getVar('category_relation') .'.'. $category->getId();
    $grantedReadForMedia = IsPermitted($categoryAclForMedia, null, 'read');
    $grantedEditForMedia = IsPermitted($categoryAclForMedia, null, 'edit');
    $grantedDeleteForMedia = IsPermitted($categoryAclForMedia, null, 'delete');

    $category->reset();
    $category->load($category->getCategoryId('campaign', '上刊畫面-口碑'));
    $categoryIdForSocial = $category->getId();
    $categoryAclForSocial = 'category.'. $category->getVar('category_relation') .'.'. $category->getId();
    $grantedReadForSocial = IsPermitted($categoryAclForSocial, null, 'read');
    $grantedEditForSocial = IsPermitted($categoryAclForSocial, null, 'edit');
    $grantedDeleteForSocial = IsPermitted($categoryAclForSocial, null, 'delete');

	$rowsFile = [];
	$allowCategories = [];

	if ($grantedReadForMedia) {
		$allowCategories[] = $categoryIdForMedia;
	}
	
	if ($grantedReadForSocial) {
		$allowCategories[] = $categoryIdForSocial;
	}

	if (count($allowCategories)) {
		$conditions = [];
		$conditions[] = sprintf("`file_category` IN (%s)", implode(', ', $allowCategories));
		$conditions[] = sprintf("`file_relation` = %d", $_GET['id']);
		$conditions[] = "`file_deleted_by` = 0";

		$joinStatement = "LEFT JOIN `{$GLOBALS['env']['db_master']}`.`mrbs_users` ON `file_creator` = `id`";
		
		$fileManager = CreateObject('Filemanager');
        $rowsFile = $fileManager->searchAll($conditions, '', '', '', $joinStatement, '`filemanager`.*, `mrbs_users`.`username`');
        
        $rowsThumb = [];
        $rowsExtract = [];
        foreach ($rowsFile as $itemFile) {
            if ($files = $fileManager->getThumbFile($itemFile)) {
                $rowsThumb[$itemFile['file_id']] = $files;
            } else if ($files = $fileManager->getExtractFile($itemFile)) {
                $rowsExtract[$itemFile['file_id']] = $files;
            }
        }
    }

    $twig = CreateObject('Twig', dirname(__DIR__));
    $twig->setFile('jsadways2/templates/published_public.html', [
        'flag' => $GLOBALS['env']['flag'],
        'rowsThumb' => isset($rowsThumb) ? $rowsThumb : [],
        'rowsExtract' => isset($rowsExtract) ? $rowsExtract : [],
        'grantedDeleteForMedia' => $grantedDeleteForMedia,
        'grantedDeleteForSocial' => $grantedDeleteForSocial
    ]);
    $twig->display();

?>
<style>
    .uploader#uniform-undefined {
        display: none;
    }
</style>
<? if ($grantedReadForMedia || $grantedReadForSocial) : ?>
    <div class="row-fluid" id="section-published" style="<?php echo count($rowsFile) ? '' : 'display: none;' ?>">
        <div class="box span12">
            <div class="box-header well" data-original-title>
                <h2><i class="fa fa-file-text-o" style="color: #333;"></i> 上刊畫面</h2>
            </div>
            <div class="box-content">
                <script>
                    <?php if ($grantedDeleteForMedia || $grantedDeleteForSocial) : ?>
                        function DeleteFile(id)
                        {
                            var filename = $('#filename_'+ id).html();

                            if (confirm('確定要刪除 '+ filename)) {
                                $.ajax({
                                    url: '../filemanager/delete.php?<?php echo $GLOBALS['env']['flag']['url_suffix']; ?>file_id='+ id,
                                    type: 'GET',
                                    beforeSend: function() {

                                    },
                                    success: function(feedback) {
                                        try {
                                            if (feedback.success) {
                                                $('tr#file_'+ feedback.data).remove();
                                            }
                                        } catch (e) {

                                        }

                                        if (!$('tbody#rows-published tr').length) {
                                            $('div#section-published').hide();
                                        }
                                    },
                                    error: function() {

                                    }
                                });
                            }
                        }
                    <?php endif; ?>
                </script>
                <table class="table table-bordered bootstrap-datatable ">
                    <thead>
                        <tr>
                            <th style="text-align: left; padding-left: 18px;">檔案名稱</th>
                            <th>上傳部門</th>
                            <th>更新日期</th>
                            <th>動作</th>
                        </tr>
                    </thead>
                    <tbody id="rows-published">
                        <?php foreach ($rowsFile as $itemFile) : ?>
                        <tr id="file_<?php echo $itemFile['file_id']; ?>">
                            <td style="text-align: left; padding-left: 18px;">
                                <a target="_blank" href="../filemanager/download.php?<?php echo $GLOBALS['env']['flag']['url_suffix']; ?>file_id=<?php echo $itemFile['file_id']; ?>&verify=<?php echo md5($itemFile['file_name']); ?>" id="filename_<?php echo $itemFile['file_id']; ?>"><?php echo $itemFile['file_name']; ?></a>
                                <?php if (count($rowsThumb[$itemFile['file_id']])) : ?>
                                    <a href="javascript:OpenBlocker(<?php echo $itemFile['file_id']; ?>, <?php echo htmlspecialchars(json_encode($rowsThumb[$itemFile['file_id']])); ?>);">
                                        <i class="fa fa-search" style="color: #e91e63;font-size: 1em;">預覽</i>
                                    </a>
                                <?php elseif (count($rowsExtract[$itemFile['file_id']])) : ?>
                                    <a href="javascript:OpenBlocker(<?php echo $itemFile['file_id']; ?>, <?php echo htmlspecialchars(json_encode($rowsExtract[$itemFile['file_id']])); ?>, <?php echo $itemFile['file_relation']; ?>);">
                                        <i class="fa fa-film" style="color: #e91e63;font-size: 1em;">&nbsp;預覽</i>
                                    </a>
                                <?php endif; ?>
                                <br/>
                                <?php echo $itemFile['file_description']; ?>
                            </td>
                            <td>
                                <?php  
                                    switch ($itemFile['file_category']) {
                                        case $categoryIdForMedia:
                                        echo '<b style="color: brown;">媒體部</b>';
                                            break;
                                        case $categoryIdForSocial:
                                            echo '<b style="color: blue;">口碑部</b>';
                                            break;
                                    }
                                ?>
                            </td>
                            <td><?php echo $itemFile['username'] .' 於<br/>'. $itemFile['file_created'] .'<br/>上傳'; ?></td>
                            <td>
                                <a class="btn btn-info" href="../filemanager/download.php?<?php echo $GLOBALS['env']['flag']['url_suffix']; ?>file_id=<?php echo $itemFile['file_id']; ?>&verify=<?php echo md5($itemFile['file_name']); ?>" target="_blank">
                                    <i class="fa fa-download"></i>
                                    下載
                                </a>
                                <?php if (($itemFile['file_category'] == $categoryIdForMedia && $grantedDeleteForMedia) || ($itemFile['file_category'] == $categoryIdForSocial && $grantedDeleteForSocial)) : ?>
                                    <a class="btn btn-danger" href="javascript:DeleteFile(<?php echo $itemFile['file_id']; ?>);">
                                        <i class="fa fa-trash"></i>
                                        刪除
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<? if ($grantedEditForMedia || $grantedEditForSocial) : ?>
    <link href="../resources/dropzone/dropzone.css" rel="stylesheet">
    <script src="../resources/dropzone/dropzone.min.js"></script>
    <script>
        <?php if ($grantedEditForMedia) : ?>
            $('#action-row').append('\n<a class="btn btn-info" href="javascript:OpenBlock(\'Media\');" title="上傳上刊畫面 (媒體部)"  data-rel="tooltip"><i class="fa fa-file-text-o"></i>上傳上刊畫面 (媒體部)</a>');
        <?php endif; ?>

        <?php if ($grantedEditForSocial) : ?>
            $('#action-row').append('\n<a class="btn btn-warning" href="javascript:OpenBlock(\'Social\');" title="上傳上刊畫面 (口碑部)"  data-rel="tooltip"><i class="fa fa-file-text-o"></i>上傳上刊畫面 (口碑部)</a>');
        <?php endif; ?>

        function OpenBlock(dept)
        {
            $('#uploadZone'+ dept).show();

            $.blockUI({
                message: $('#panel_block'),
                css: {
                    top: '10%',
                    left: '20%',
                    width: '60%',
                    background: 'none',
                    border: 'none',
                }
            });
        }

        function CloseBlock()
        {
            $.unblockUI();

            if (uploaded === true) {
                window.location.reload();
            }

            $('#uploadZoneMedia').hide();
            $('#uploadZoneSocial').hide();
        }

        onload = function() {
            var scriptBlockUI = document.createElement("script");
            scriptBlockUI.type = "text/javascript";
            scriptBlockUI.src = "../js/jquery.blockUI-2.70.0.js";
            $('body').append(scriptBlockUI);
        }
    </script>

    <div id="panel_block" style="display: none;">
        <center>
            <div style="background-color: white; padding: 30px;">
                <div style="position: absolute; font-size: 2em; top: 0; left: 0; border: 1px solid #ccc; border-radius: 12px; margin-top: 2px; margin-left: 2px; padding: 0 3px;">
                    <a href="javascript:CloseBlock();"><i class="fa fa-times" style="color: #ccc;"></i></a>
                </div>
                <p>將欲上傳的檔案拖曳到下方圖示或點選下方圖示選擇欲上傳的檔案</p>
                <div>
                    <div id="uploadZoneMedia" class="dropzone dz-clickable" style="width: 100%; height: 240px; display: none;">            
                        <div class="dz-default dz-message"><span>Drop files here to upload (媒體)</span></div>
                    </div>
                </div>

                <div>
                    <div id="uploadZoneSocial" class="dropzone dz-clickable" style="width: 100%; height: 240px; display: none;">            
                        <div class="dz-default dz-message"><span>Drop files here to upload (口碑)</span></div>
                    </div>
                </div>
                
                <div>
                    <div id="attachment_list" style="max-height: 300px; overflow-y: auto; overflow-x: none;">
                    </div>
                </div>
                
                <script>
                    var uploaded = false;

                    <?php foreach (['uploadZoneMedia' => $categoryIdForMedia, 'uploadZoneSocial' => $categoryIdForSocial] as $dropzoneId => $dropzoneCategory) : ?>
                        Dropzone.options.<?php echo $dropzoneId; ?> = {
                            url: '../filemanager/upload.php?<?php echo $GLOBALS['env']['flag']['url_suffix']; ?>ajax&replace&file_relation=<?php echo $_GET['id']; ?>&file_dir=<?php echo urlencode('campaign'. $GLOBALS['env']['flag']['storage_suffix'] .'/'. $_GET['id']); ?>&category_id=<?php echo $dropzoneCategory; ?>&file_storage=<?php echo urlencode('預設'); ?>', 
                            paramName: 'file[]',
                            maxFilesize: 120,
                            addRemoveLinks: true, 
                            dictFileTooBig: '上傳檔案大於上傳限制',
                            dictCancelUpload: '取消上傳', 
                            dictCancelUploadConfirmation: '確定要取消嗎？',
                            dictRemoveFile: '移除',
                            init: function() {
                                this.on("success", function(file,resText) { 
                                    this.removeFile(file);

                                    if ('message' in resText) {
                                        alert(resText.message);
                                    } else {
                                        UploadSuccess(resText);
                                    }
                                });
                            },
                            fallback: function(){
                                $('#<?php echo $dropzoneId; ?>').hide();
                            }
                        };
                    <?php endforeach; ?>

                    function UploadSuccess(resText)
                    {
                        for (var idx in resText.data) {
                            var append = '<div id="upload_'+ resText.data[idx]['file_id'] +'" style="width: 100%; height: 50px; display: block; border: 1px solid #ccc; text-align: left;">' +
                                            '<p style="padding-top: 10px;">' +
                                                '<a style="margin-left: 5px; margin-right: 10px; width: 30px; height: 30px; text-decoration: none; color: green; font-size: 1.8em; border: 1px solid #aaa; background-color: rgba(255, 255, 255, 0.7); border-radius: 15px;">' +
                                                    '<i class="fa fa-check" style="padding-top: 3px; padding-left: 2px;"></i>' +
                                                '</a>' +
                                            resText.data[idx]['file_name'] +'&nbsp;&nbsp;<small>(<span style="color: green;">上傳成功</span>)</small></p>' +
                                        '</div>';
                            $('#attachment_list').append(append);
                            uploaded = true;
                        }
                    }
                </script>
            </div>
        </center>
    </div>
<?php endif; ?>
