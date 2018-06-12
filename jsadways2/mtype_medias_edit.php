<?php 
    
    session_start();
    //include('include/db.inc.php');

    require_once dirname(__DIR__) .'/autoload.php';

    $objCampaign = CreateObject('Campaign', GetVar('campaign'));
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

                $(window).on('load', function(){
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
                            var mtype_number="";
                             $.ajax({
                            url: 'medias_add_option.php',
                            type: 'post',
                            data: {id:media,group:"templateedit"},
                            dataType: 'json',
                            success:function(response){
                                var len = response.length;
                                for( var i = 0; i<len; i++){
                                    var mtype_number = response[i]['key'];
                                    var media_url = response[i]['name'];
                                }
                                if (mtype_number == "0"){
                                    alert(media_url);
                                    return false;
                                }
                            media_url = "mtype_" + media_url.trim() + ".php?campaign="+campaign+"&id="+id+"&cue="+cue+"&media="+media+"&media2=&mediaid="+"&media_id="+media;
                                $.get(media_url, function(data) {
                                    //抓取模板form表單
                                        //Youtuber及寫手費特殊處理
                                    if (media=="166" || media=="162"){
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
                                         $('form :input:visible:enabled:first').focus();
                                         //處理寫手模板Youtuber新增按鈕
                                          if (media == "166" || media == "162"){
                                            $('#addForm').hide();
                                            //新增寫手Youtuber新增按鈕
                                            $('a[name^="PersonAdd"]').each(function(){

                                                // var idx = $(this).attr('href').indexOf('href');
                                                //  getVal[k++] = $(this).attr('href').substring(idx);
                                                var getVal = $(this).attr('href')+"&campaign="+<?= $_GET['campaign'];?>;
                                                $(this).removeAttr('href');
                                                // console.log("getVal " + getVal);
                                                $(this).unbind().click(function(){
                                                    $.get(getVal,function(data){
                                                        var new_html_index = data.indexOf('box-content');
                                                        var new_html = data.substring(new_html_index-12);
                                                        $('#addForm').append(new_html);
                                                        $('#addForm').show();
                                                        $('#addList .form-actions').hide();
                                                        // $('fieldset').empty();
                                                        // $('fieldset').append(new_html);
                                                        $('#addForm .form-horizontal').submit(false);
                                                        $('#list').hide();
                                                        $('#complete').click(function(){
                                                            $('input[name="isEdit"]').val("Y");
                                                            $mediaform = $('#addForm .form-horizontal');
                                                            // console.log($mediaform);
                                                            var valid = $mediaform[0].checkValidity();
                                                            // console.log(valid);
                                                            if( valid){
                                                            var post_url= $('#addForm').find('form').attr('action');
                                                            var formdata=$mediaform.serialize();
                                                            console.log(post_url);
                                                            console.log(formdata);
                                                            console.log(JSON.stringify(formdata));
                                                            
                                                           //return false;
                                                            $.ajax({
                                                            url: post_url,
                                                            type: 'post',
                                                            data: formdata,
                                                            dataType: 'json',
                                                            success:function(response){
                                                                // alert("success");
                                                                // return false;
                                                                 var len = response.length;
                                                                 // console.log("post_url:"+media_url);
                                                                 // console.log("res:"+ response.data);
                                                                    var msg="";
                                                                    var msgval="";
                                                                    for( var i = 0; i<len; i++){
                                                                         msg = response[i]['key'];
                                                                         msgval = response[i]['name'];
                                                                    }
                                                                    if(msgval == "OK"){
                                                                        alert("新增成功");
                                                                       $.get(media_url, function(data){
                                                                        var new_html_index = data.indexOf('addList');
                                                                        var new_html_end = data.indexOf('addForm');
                                                                        var new_html_end = data.indexOf('addForm');
                                                                                if(media == "162"){
                                                                        var new_html = data.substring(new_html_index + 9, new_html_end - 54);
                                                                                }else if(media == "166"){
                                                                        var new_html = data.substring(new_html_index);
                                                                            }
                                                                        // console.log(new_html);
                                                                        $('#addList').empty();
                                                                        $('#addList').append(new_html);
                                                                        $('#addList .form-actions').hide();
                                                                        $('#addForm').empty();
                                                                        $('#addForm').hide();
                                                                       $('#list').show();
                                                                       });

                                                                    }
                                                            },
                                                            error: function(xhr, status, error) {
                                                             console.log(xhr.responseText);
                                                             console.log(status);
                                                             console.log(error);
                                                         }
                                                        });
                                                        }
                                                        // console.log(new_html_index);
                                                    });//$('#complete').click(function()
                                                    $('#cancel').click(function(){
                                                        $(this).submit(false);
                                                         $('#addForm').empty();
                                                         $('#addForm').hide();
                                                         $('#list').show();
                                                    });
                                                });
                                            });//click
                                            });//each
                                            //刪除按鈕
                                            PersonDel(media_url,mtype_name,mtype_number,media);
                                            
                                            //each
                                        }//if
                                  
                                });//$.get
                            },
                            error: function(xhr, status, error) {
                                     console.log(xhr.responseText);
                                     console.log(status);
                                     console.log(error);
                            }
                        });
                        PersonDel(media_url,mtype_name,mtype_number);
                    }//function TemplateLoad

                });//$(window).load
                function PersonDel(media_url,mtype_name,mtype_number,media){
                    $('a[name^="PersonDel"]').each(function(){
                         var getVal = $(this).attr('href');
                         // console.log(getVal);
                        $(this).removeAttr('href');
                        $(this).unbind().click(function(){
                            if(!confirm("確定要刪除")){
                                return false;
                            }else{
                                $.ajax({
                                url:getVal,
                                type: 'post',
                                data: {},
                                dataType: 'json',
                                success:function(response){
                                     var len = response.length;
                                      console.log(media_url);
                                        var msg="";
                                        var msgval="";
                                        for( var i = 0; i<len; i++){
                                             msg = response[i]['key'];
                                             msgval = response[i]['name'];
                                        }
                                        if(msgval == "OK"){
                                            PersonDelReload(media_url,mtype_name,mtype_number,media);
                                            $('.box-content .form-horizontal:eq(1)').attr('action',$('.box-content .form-horizontal:eq(1)').attr('action')+"&mtypename="+mtype_name+"&mtypenumber="+mtype_number);
                                           alert("刪除成功");
                                        }
                                    },
                                // complete: function(XMLHttpRequest, textStatus) { 
                                //     //$(this).dialog("close");
                                // },
                                error: function(xhr, status, error) {
                                    console.log(xhr.responseText);
                                    console.log(status);
                                    console.log(error);
                                    }
                                });
                            }
                          });//click
                    });
                  }//PersonDel
                  function PersonDelReload(media_url,mtype_name,mtype_number,media){
                    $.get(media_url, function(data){
                    var new_html_index = data.indexOf('addList');
                     var new_html_end = data.indexOf('addForm');
                    if(media == "162"){
                        var new_html = data.substring(new_html_index + 9, new_html_end - 54);
                    }else if(media == "166"){
                        var new_html = data.substring(new_html_index);
                        }
                    // console.log(new_html);
                    $('#addList').empty();
                    $('#addList').append(new_html);
                    $('#addList .form-actions').hide();
                    $('.box-content .form-horizontal:eq(1)').attr('action',$('.box-content .form-horizontal:eq(1)').attr('action')+"&mtypename="+mtype_name+"&mtypenumber="+mtype_number);
                    PersonDel(media_url,mtype_name,mtype_number);
                    });//$.get
                  }//PersonDelReload
                //儲存後關閉按鈕
                $('#saveExit').click(function(){
                    // console.log('submit');
                    $('.box-content .form-actions button').trigger('click');
                });
            });//$(document).ready
        </script>
    </body>
</html>