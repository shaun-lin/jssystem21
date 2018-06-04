<?php 
    
    require_once dirname(__DIR__) .'/autoload.php';

    $objCampaign = CreateObject('Campaign', GetVar('id'));
    if (!IsId($objCampaign->getId())) {
        RedirectLink('campaign_list.php');
    }

    $db = clone($GLOBALS['app']->db);
    $objMedias = CreateObject('Medias');
    $objCompanies = CreateObject('Companies');
    $objItems = CreateObject('Items');
//echo $objMedia;
    // $mediaTypeList = [
    //     'CPC' => '`type` = 1 AND `display` = 1',
    //     'CPI' => '`type` = 2 AND `display` = 1',
    //     'CPM' => '`type` = 0 AND `display` = 1',
    //     'CPV' => '`type` = 9 AND `display` = 1',
    //     'CPT' => '`type` = 10 AND `display` = 1',
    //     '網站廣告' => '`type` = 3 AND `display` = 1',
    // ];

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>【<?= $GLOBALS['env']['flag']['name']; ?>】新增媒體</title>
        <?php include("public/head.php"); ?>
        <?php include("public/js.php"); ?>
        <style>
            select {
                width: 200px;
                /*margin-right: 8px;*/
            }
            option:hover { 
                background-color: LightCyan;
            }
        </style>
    </head>
    <body>
        <?php include("public/topbar.php"); ?>
        <div class="container-fluid">
            <div class="row-fluid">
                <?php include("public/left.php"); ?>
                <div id="content" class="span10">	
                    <div class="h-25 row-fluid">
                        <div class="box span12">
                            <div class="box-header well" data-original-title>
                                <h2><i class="icon-search"></i> 查詢條件</h2>
                            </div>
                            <div class="box-content">
                                 <div class="control-group span4">
                                    <label class="control-label">媒體</label>
                                    <div class="controls">
                                        <select id="media" name="media" size="<?= count($objMedias->searchAll('', 'id', 'ASC'));?>" >
                                            <? foreach ($objMedias->searchAll('', 'id', 'ASC') as $itemMedias) : ?>
                                                <option value="<?= $itemMedias['id']; ?>"><?= $itemMedias['name']; ?></option>
                                                <? endforeach; ?> 
                                        </select>
                                    </div>
                                </div>
                                <div class="control-group span4">
                                    <label class="control-label">項目</label>
                                    <div class="controls">
                                        <select id="items" name="items">
                                                <option value="">-- 請選擇媒體 --</option>
                                        </select>  
                                    </div>
                                </div>
                                <div class="control-group span4">
                                    <label class="control-label">賣法</label>
                                    <div class="controls">
                                        <select id="mtype" name="mtype" >
                                          <option value="">-- 請選擇項目 --</option> 
                                        </select>   
                                    </div>
                                </div>
                            </div>
                        </div>  
                    </div>
            <div class="row-fluid">
                <div class="box span12">
                    <div class="box-header well" data-original-title>
                         <h2><i class="icon-edit"></i> <?= $objCampaign->getVar('name'); ?> - 新增媒體</h2>
                    </div>
                     <div class="box-content">
                        <form class="form-horizontal">
                            <fieldset>
                            </fieldset>
                            <button id="save" type="button" class="btn btn-primary">儲存後繼續新增</button> 
                            <button id="saveExit" type="button" class="btn btn-primary">儲存後離開</button>
                            <button type="submit" class="hide"></button> 
                            <a class="btn btn-danger" href="campaign_view.php?id=<?= $_GET['id']; ?>">離開</a>
                        </form>   
                    </div>
                </div>
            </div>
           </div>  
            <hr/>
            <?php include("public/footer.php"); ?>
        </div>
        <script type="text/javascript" >
            $(document).ready(function(){
                // $('#labCompany').hide();
                // $('#labMedia').hide();
                // $('#labItems').hide();
                $('#save').hide();
                $('#saveExit').hide();
                //依據選擇媒體載入商品
                
                $('#media').change(function(){
                     $('fieldset:eq(3)').empty();
                     var media_id = $(this).val();
                    $.ajax({
                        url: 'medias_add_option.php',
                        type: 'post',
                        data: {id:media_id,group:"media"},
                        dataType: 'json',
                        success:function(response){

                            var len = response.length;
                            // console.log(len);
                            $("#items").empty();
                            $('#items').attr('size',len);
                            for( var i = 0; i<len; i++){
                                var id = response[i]['key'];
                                var name = response[i]['name'];
                                // console.log(id);
                                // console.log(name);
                                $("#items").append("<option value='"+id+"'>"+name+"</option>");

                            }
                        },
                        error: function(xhr, status, error) {
                                 console.log(xhr.responseText);
                                 console.log(status);
                                 console.log(error);
                        }
                    });
                    console.log($('#media option:selected').text());

                    //隱藏選單
                    // $('#media').hide();
                    // $('#labMedia').show();
                    // $('#labMedia').text($('#media option:selected').text());
                });
                //依據選擇商品載入賣法
                $('#items').change(function(){
                    $('fieldset:eq(3)').empty();
                    var item_id = $(this).val();
                      $.ajax({
                        url: 'medias_add_option.php',
                        type: 'post',
                        data: {id:item_id,group:"items"},
                        dataType: 'json',
                        success:function(response){

                            var len = response.length;
                            // console.log(len);
                            $("#mtype").empty();
                            $("#mtype").attr("size",len+1);
                            $("#mtype").append("<option value=' '>請選擇賣法</option>");
                            for( var i = 0; i<len; i++){
                                var id = response[i]['key'];
                                var name = response[i]['name'];
                                // console.log(id);
                                // console.log(name);
                                $("#mtype").append("<option value='"+id+"'>"+name+"</option>");
                            }
                        },
                        error: function(xhr, status, error) {
                                 console.log(xhr.responseText);
                                 console.log(status);
                                 console.log(error);
                        }
                    });
                    //隱藏選單  
                    // $('#items').hide();
                    // $('#labItems').show();
                    // $('#labItems').text($('#items option:selected').text());
                });
                //載入Template Form
                $('#mtype').change(function(){
                         $('fieldset').empty();

                    var mtype_text = $('#mtype option:selected').text();
                    var company_id = $('#company').val();
                    var mtype_id = $('#mtype').val();
                    var media_id = $('#media').val();
                    var id = <?= GetVar('id'); ?>;
                    var cue = <?= GetVar('cue'); ?>;
                    var media_url = "";
                    var mtype_number = "";
                    console.log($('#mtype').prop('selectedIndex'));
                    if($('#mtype').prop('selectedIndex')!="0"){
                        switch(mtype_text){
                        case "CPV":
                            media_url = "CPV_add.php";
                            mtype_number="154";
                            break;
                        case "CPC":
                            media_url = "CPC_add.php";
                            mtype_number="151";
                            break;
                        case "CPM":
                            media_url = "CPM_add.php";
                            mtype_number="153";
                            break;
                        case"CPI":
                            media_url = "CPI_add.php";
                            mtype_number="152";
                            break;
                        case"檔期":
                            media_url = "Schedule_add.php";
                            mtype_number="157";
                            break;
                        case"CPA":
                            media_url = "CPA_add.php";
                            mtype_number="170";
                            break;
                        case"CPE":
                            media_url = "CPE_add.php";
                            mtype_number="171";
                            break;
                        case"CPS":
                            media_url = "CPS_add.php";
                            mtype_number="156";
                            break;
                        case"CPT":
                            media_url = "CPT_add.php";
                            mtype_number="155";
                            break;
                        case"網誌廣告":
                            media_url = "WebADV_add.php";
                            mtype_number="158";
                            break;
                        case"【任務型】Line(企業贊助貼圖)":
                            media_url = "LineCorpMap_add.php";
                            mtype_number="159";
                            break;
                        case"【其他】手機簡訊":
                            media_url = "MMS_add.php";
                            mtype_number="160";
                            break;
                        case"【機制費】廣告素材製作":
                            media_url = "Creative_add.php";
                            mtype_number="161";
                            break;
                        case"寫手費":
                            media_url = "Handwriting_edit.php";
                            mtype_number="162";
                            break;
                        case"Facebook代操服務費":
                            media_url = "FBFee_add.php";
                            mtype_number="163";
                            break;
                        case"SHIRYOUKO STUDIO":
                            media_url = "ShiryoukoStudio_add.php";
                            mtype_number="164";
                            break;
                        case"HappyGo MMS":
                            media_url = "HappyGoMMS_add.php";
                            mtype_number="165";
                            break;
                        case"Youtuber":
                            media_url = "Youtuber_add.php";
                            mtype_number="166";
                            break;
                        case"【行動下載計費CPI】LINE(3DM)":
                            media_url = "LINE3DM_add.php";
                            mtype_number="167";
                            break;
                        case"預約TOP10":
                            media_url = "TOPTen_add.php";
                            mtype_number="168";
                            break;
                        case"錢包小豬(任務型)":
                            media_url = "Mission_add.php";
                            mtype_number="169";
                            break;
                        default:
                            break;
                    }
                        media_url = "mtype_"+media_url + "?id="+id+"&cue="+cue+"&media="+media_id[0]+"&media2=&" + "copmpanies=" + company_id + "&mediaid=";
                        var media_id = $('#media').val();
                        var item_id = $('#items').val();
                        var mtype_name = $('#mtype option:selected').text();
                        console.log("1.　" + media_url);
                        $.get(media_url, function(data) {
                            //抓取模板form表單
                            if (mtype_text=="Youtuber" || mtype_text=="寫手費"){
                                var new_html_index = data.indexOf('content');
                                var new_html = data.substring(new_html_index-12);
                            }else{
                                var new_html_index = data.indexOf('box-content');
                                var new_html = data.substring(new_html_index-12);
                            }
                                //console.log(new_html);
                                 // $('fieldset:eq(3)').append(new_script);
                                 //將表單放入fieldset中
                                $('fieldset').append(new_html);
                                //隱藏原模板新增媒體按鈕
                                $('.box-content .form-actions').hide();
                                //隱藏模板的footer
                                $('.box-content footer').hide();
                                    console.log("2.　" + $('.box-content .form-horizontal:eq(1)').attr('action'));
                                //修改模板form的action
                                $('.box-content .form-horizontal:eq(1)').attr('action',$('.box-content .form-horizontal:eq(1)').attr('action')+media_id+"&itemid="+item_id+"&mtypename="+mtype_name+"&mtypenumber="+mtype_number+"&mtypeid="+mtype_id);
                                    console.log("3.　" + $('.box-content .form-horizontal:eq(1)').attr('action'));
                            //將儲存按鈕show出來
                             $('#save').show();
                             $('#saveExit').show();
                        });
                    }else{
                        $('#save').hide();
                        $('#saveExit').hide();
                    }
                });
                //儲存後繼續新增按鈕
                $('#save').click(function(e){
                    $mediaform=$('form:eq(1)');
                    var valid = $mediaform[0].checkValidity(); 
                    console.log(valid);
                        if(valid){
                        var formdata=$('.box-content .form-horizontal:eq(1)').serialize();
                        console.log(JSON.stringify(formdata));
                        var ajax_url=$('.box-content .form-horizontal:eq(1)').attr('action')+"&goon=Y";
                        console.log( ajax_url);
                        $.ajax({
                            url: ajax_url,
                            type: 'post',
                            data: JSON.stringify(formdata),
                            dataType: 'json',
                            success:function(response){
                                // console.log(response);
                                var len = response.length;
                                var msg="";
                                var msgval="";
                                for( var i = 0; i<len; i++){
                                    var msg = response[i]['key'];
                                    var msgval = response[i]['name'];
                                }
                                if(msgval=="OK"){
                                    alert("新增媒體規格成功");
                                    $('fieldset:eq(0)').empty();
                                    $('#save').hide();
                                    $('#saveExit').hide();
                                    $('#mtype').focus();
                                }
                            },
                            error: function(xhr, status, error) {
                                     console.log(xhr.responseText);
                                     console.log(status);
                                     console.log(error);
                            }
                        });
                    }else {
                        $mediaform[0].reportValidity(); 
                    }
                });
                //儲存後關閉按鈕
                $('#saveExit').click(function(){
                    console.log('submit');
                    $('.box-content .form-actions button').trigger('click');
                });
            });
        </script>
    </body>
</html>