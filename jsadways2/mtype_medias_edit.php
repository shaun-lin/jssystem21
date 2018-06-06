<?php 
    
    session_start();
    //include('include/db.inc.php');

    require_once dirname(__DIR__) .'/autoload.php';

    $objCampaign = CreateObject('Campaign');
    $objMedia = CreateObject('Media', GetVar('media'));

    $db = clone($GLOBALS['app']->db);
    $objMedias = CreateObject('Medias');
    $objCompanies = CreateObject('Companies');
    $objItems = CreateObject('Items');
    $objCpdetail = CreateObject('Cp_detail');
    $condition = 'cp_id ='.GetVar('campaign').' and mtype_number='.getVar('media').' and cue='.GetVar('cue');
    $resCpdetail = $objCpdetail->searchAll($condition);
    $countCptedail = count($resCpdetail);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>【<?= $objCampaign->getVar('name'); ?>】-編輯媒體-<?= $objMedia->getVar('name');?></title>
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
            <div class="row-fluid">
                <div class="box span12">
                    <div class="box-header well" data-original-title>
                         <h2><i class="icon-edit"></i> <?= $objCampaign->getVar('name'); ?> - 編輯媒體-<?= $objMedia->getVar('name');?></h2>
                    </div>
                     <div class="box-content">
                        <form class="form-horizontal">
                            <fieldset>
                            </fieldset>
                            <button id="saveExit" type="button" class="btn btn-primary">確定修改</button>
                            <button type="submit" class="hide"></button> 
                            <a class="btn btn-danger" href="campaign_view.php?id=<?= $_GET['campaign']; ?>">離開</a>
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

                $(window).load( function(){
                    var campaign = <?= GetVar('campaign'); ?> ;
                    var cue = <?= GetVar('cue'); ?> ;
                    var id = <?= GetVar('id'); ?> ;
                    var media = <?= GetVar('media'); ?> ;
                    var mtype_name = "<?= $resCpdetail[$countCptedail-1]['mtype_name'];?>";
                    
                    TemplateLoad(mtype_name);

                    //載入Template Form
                    function TemplateLoad( mtype_name){
                        $('fieldset').empty();
                        var media_url = "";
                        // console.log(mtype_name);
                            switch(mtype_name){
                                     case "CPV":
                                        media_url = "CPV_edit.php";
                                        mtype_number="154";
                                        break;
                                    case "CPC":
                                        media_url = "CPC_edit.php";
                                        mtype_number="151";
                                        break;
                                    case "CPM":
                                        media_url = "CPM_edit.php";
                                        mtype_number="153";
                                        break;
                                    case"CPI":
                                        media_url = "CPI_edit.php";
                                        mtype_number="152";
                                        break;
                                    case"檔期":
                                        media_url = "Schedule_edit.php";
                                        mtype_number="157";
                                        break;
                                    case"CPA":
                                        media_url = "CPA_edit.php";
                                        mtype_number="170";
                                        break;
                                    case"CPE":
                                        media_url = "CPE_edit.php";
                                        mtype_number="171";
                                        break;
                                    case"CPS":
                                        media_url = "CPS_edit.php";
                                        mtype_number="156";
                                        break;
                                    case"CPT":
                                        media_url = "CPT_edit.php";
                                        mtype_number="155";
                                        break;
                                    case"網誌廣告":
                                        media_url = "WebADV_edit.php";
                                        mtype_number="158";
                                        break;
                                    case"【任務型】Line(企業贊助貼圖)":
                                        media_url = "LineCorpMap_edit.php";
                                        mtype_number="159";
                                        break;
                                    case"【其他】手機簡訊":
                                        media_url = "MMS_edit.php";
                                        mtype_number="160";
                                        break;
                                    case"【機制費】廣告素材製作":
                                        media_url = "Creative_edit.php";
                                        mtype_number="161";
                                        break;
                                    case"寫手費":
                                        media_url = "Handwriting_edit.php";
                                        mtype_number="162";
                                        break;
                                    case"Facebook代操服務費":
                                        media_url = "FBFee_edit.php";
                                        mtype_number="163";
                                        break;
                                    case"SHIRYOUKO STUDIO":
                                        media_url = "ShiryoukoStudio_edit.php";
                                        mtype_number="164";
                                        break;
                                    case"HappyGo MMS":
                                        media_url = "HappyGoMMS_edit.php";
                                        mtype_number="165";
                                        break;
                                    case"Youtuber":
                                        media_url = "Youtuber_edit.php";
                                        mtype_number="166";
                                        break;
                                    case"【行動下載計費CPI】LINE(3DM)":
                                        media_url = "LINE3DM_edit.php";
                                        mtype_number="167";
                                        break;
                                    case"預約TOP10":
                                        media_url = "TOPTen_edit.php";
                                        mtype_number="168";
                                        break;
                                    case"錢包小豬(任務型)":
                                        media_url = "Mission_edit.php";
                                        mtype_number="169";
                                        break;
                                    default:
                                        break;
                                }
                        media_url = "mtype_" + media_url;
                        media_url = media_url + "?campaign="+campaign+"&id="+id+"&cue="+cue+"&media="+media+"&media2=&mediaid=";
                        // console.log(media_url);
                        $.get(media_url, function(data) {
                            //抓取模板form表單
                            if (mtype_name=="Youtuber" || mtype_name=="寫手費"){
                                var new_html_index = data.indexOf('content');
                                var new_html = data.substring(new_html_index-12);
                            }else{
                                var new_html_index = data.indexOf('box-content');
                                var new_html = data.substring(new_html_index-12);
                            }
                                 //將表單放入fieldset中
                                $('fieldset').append(new_html);
                                //隱藏原模板新增媒體按鈕
                                $('.box-content .form-actions').hide();
                                //隱藏模板的footer
                                $('.box-content footer').hide();
                                console.log($('.box-content .form-horizontal:eq(1)').attr('action'));
                        });//$.get
                    }//function TemplateLoad
                });//$(window).load
                //儲存後關閉按鈕
                $('#saveExit').click(function(){
                    console.log('submit');
                    $('.box-content .form-actions button').trigger('click');
                });
            });//$(document).ready
        </script>
    </body>
</html>