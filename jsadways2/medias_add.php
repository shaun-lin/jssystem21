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
                                            <? foreach ($objMedias->searchAll('`display` = 1', 'name', 'ASC') as $itemMedias) : ?>
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
                $('#save').hide();
                $('#saveExit').hide();
                //依據選擇媒體載入商品
                $('#media').change(function(){
                    $('#save').hide();
                    $('#saveExit').hide();
                    $('fieldset').empty();
                    $('#item').empty();
                    $('#mtype').empty().append("<option value=' '>-- 請選擇項目 --</option>").attr('size',1);
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
                            $('#items').attr('size',len + 1);
                            $("#items").append("<option value=' '>-- 請選擇媒體 --</option>");
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
                    $('#save').hide();
                    $('#saveExit').hide();
                    $('fieldset').empty();
                    $('#item').empty();
                    $('#mtype').empty();
                    var item_id = $(this).val();
                    var media_id = $('#media').val();
                    var campign_id = <?= $_GET["id"];?>;
                      $.ajax({
                        url: 'medias_add_option.php',
                        type: 'post',
                        data: {id:item_id,media_id:media_id,campign_id:campign_id,group:"items"},
                        dataType: 'json',
                        success:function(response){

                            var len = response.length;
                            // console.log(len);
                            $("#mtype").empty();
                            $("#mtype").attr("size",len + 1);
                            $("#mtype").append("<option value=' '>-- 請選擇賣法 --</option>");
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
                    var media_name = $('#media option:selected').text();
                    var id = <?= GetVar('id'); ?>;
                    var cue = <?= GetVar('cue'); ?>;
                    var media_url = "";
                    var mtype_number = "";
                    var media_id = $('#media').val();
                    var item_id = $('#items').val();
                    var mtype_name = $('#mtype option:selected').text();

                    //已做過設定的模板不做動作
                    // if(mtype_name.indexOf("已設定")>0){
                    //     return false;
                    // }

                    // console.log($('#mtype').prop('selectedIndex'));
                    if($('#mtype').prop('selectedIndex')!="0"){
                            $.ajax({
                            url: 'medias_add_option.php',
                            type: 'post',
                            data: {id:mtype_id,group:"template"},
                            dataType: 'json',
                            success:function(response){
                                // console.log(response);
                                var len = response.length;
                                 // console.log(len);
                                for( var i = 0; i<len; i++){
                                    var mtype_number = response[i]['key'];
                                    var media_url = response[i]['name'];
                                }
                                if (mtype_number == "0"){
                                    alert(media_url);
                                    return false;
                                }
                                media_url = "mtype_" + media_url.trim() + ".php?id="+id+"&cue="+cue+"&media=" + mtype_id +  "&media2=" + mtype_number + "&copmpanies=" + company_id + "&campaign=" + id;
                           
                            // console.log("1.　" + media_url);
                            $.get(media_url, function(data) {
                                //抓取模板form表單
                                //Youtuber及寫手費特殊處理
                                if (mtype_id=="166" || mtype_id=="162"){
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
                                    $('#addForm').hide();
                                    //隱藏原模板新增媒體按鈕
                                    $('.box-content .form-actions').hide();
                                    //隱藏模板的footer
                                    $('.box-content footer').hide();
                                        //console.log("2.　" + $('.box-content .form-horizontal:eq(1)').attr('action'));
                                    //修改模板form的action
                                    $('.box-content .form-horizontal:eq(1)').attr('action',$('.box-content .form-horizontal:eq(1)').attr('action')+media_id+"&itemid="+item_id+"&mtypename="+mtype_name+"&mtypenumber="+mtype_number+"&mtypeid="+mtype_id+"&media_name="+media_name+"&mediaid="+media_id);
                                    // console.log('2.'+$('#templateForm').attr('action'));
                                    // $('#templateForm').attr('action', $('#templateForm').attr('action')+"&mediaid="+media_id+"&itemid="+item_id+"&mtypename="+mtype_name+"&mtypenumber="+mtype_number+"&mtypeid="+mtype_id+"&media_name="+media_name);
                                        console.log("3.　" + $('.box-content .form-horizontal:eq(1)').attr('action'));
                                        // console.log('3.'+$('#templateForm').attr('action'));
                                //將儲存按鈕show出來
                                 $('#save').show();
                                 $('#saveExit').show();
                                 $('form :input:visible:enabled:first').focus();
                                 //處理寫手模板Youtuber新增按鈕
                                  if (mtype_id == "166" || mtype_id == "162"){
                                    // console.log(mtype_id);
                                    //新增寫手Youtuber新增按鈕
                                    $('a[name^="PersonAdd"]').each(function(){

                                        // var idx = $(this).attr('href').indexOf('href');
                                        //  getVal[k++] = $(this).attr('href').substring(idx);
                                        var getVal = $(this).attr('href');
                                        $(this).removeAttr('href');
                                        // console.log(getVal);
                                        $(this).unbind().click(function(){
                                            console.log("ajax");
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
                                                //新增寫手費
                                                $('#complete').click(function(){
                                                    // console.log('complete');
                                                    $mediaform = $('#addForm .form-horizontal');
                                                    console.log($mediaform);
                                                    var valid = $mediaform[0].checkValidity();
                                                    // console.log(valid);
                                                    if( valid){
                                                    var post_url= $('#addForm').find('form').attr('action');
                                                    var formdata=$mediaform.serialize();
                                                    // console.log("post" + post_url);
                                                    // console.log(formdata);
                                                    // console.log(JSON.stringify(formdata));
                                                    
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
                                                         // console.log(media_url);
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
                                                                if(mtype_id == "162"){
                                                                var new_html = data.substring(new_html_index + 9, new_html_end - 54);
                                                            }else if(mtype_id == "166"){
                                                                var new_html = data.substring(new_html_index);
                                                            }
                                                                // console.log(new_html);
                                                                $('#addForm').empty();
                                                                $('#addForm').hide();
                                                                $('#list').show();
                                                                $('#addList').empty();
                                                                $('#addList').append(new_html);
                                                                $('#addList .form-actions').hide();
                                                               
                                                               });

                                                            }
                                                    },
                                                    error: function(xhr, status, error) {
                                                     console.log(xhr.responseText);
                                                     console.log(status);
                                                     console.log(error);
                                                    }
                                                });//ajax
                                            }//if(valid)
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
                                    PersonDel(media_url,media_id,item_id,mtype_name,mtype_number,mtype_id,media_name);
                                    
                                    //each
                                }//if
                            }); 
                            },
                            error: function(xhr, status, error) {
                                     console.log(xhr.responseText);
                                     console.log(status);
                                     console.log(error);
                            }
                        });
                    }else{
                        $('#save').hide();
                        $('#saveExit').hide();
                    }
                });
                  function PersonDel(media_url,media_id,item_id,mtype_name,mtype_number,mtype_id,media_name){
                    $('a[name^="PersonDel"]').each(function(){
                         var getVal = $(this).attr('href');
                         console.log(getVal);
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
                                     // console.log(media_url);
                                        var msg="";
                                        var msgval="";
                                        for( var i = 0; i<len; i++){
                                             msg = response[i]['key'];
                                             msgval = response[i]['name'];
                                        }
                                        if(msgval == "OK"){
                                            PersonDelReload(media_url,media_id,item_id,mtype_name,mtype_number,mtype_id,media_name);
                                            $('.box-content .form-horizontal:eq(1)').attr('action',$('.box-content .form-horizontal:eq(1)').attr('action')+media_id+"&itemid="+item_id+"&mtypename="+mtype_name+"&mtypenumber="+mtype_number+"&mtypeid="+mtype_id+"&media_name="+media_name);
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
                  function PersonDelReload(media_url,media_id,item_id,mtype_name,mtype_number,mtype_id,media_name){
                    $.get(media_url, function(data){
                    var new_html_index = data.indexOf('addList');
                    var new_html = data.substring(new_html_index+9);
                       var new_html_end = data.indexOf('addForm');
                    if(mtype_id == "162"){
                        var new_html = data.substring(new_html_index + 9, new_html_end - 54);
                    }else if(mtype_id == "166"){
                        var new_html = data.substring(new_html_index);
                    }
                    // console.log(new_html);
                    $('#addList').empty();
                    $('#addList').append(new_html);
                    $('#addList .form-actions').hide();
                    $('.box-content .form-horizontal:eq(1)').attr('action',$('.box-content .form-horizontal:eq(1)').attr('action')+media_id+"&itemid="+item_id+"&mtypename="+mtype_name+"&mtypenumber="+mtype_number+"&mtypeid="+mtype_id+"&media_name="+media_name);
                    PersonDel(media_url,media_id,item_id,mtype_name,mtype_number,mtype_id,media_name );
                    });//$.get
                  }//PersonDelReload
                //儲存後繼續新增按鈕
                $('#save').click(function(e){
                    $mediaform=$('form:eq(1)');
                    var valid = $mediaform[0].checkValidity(); 
                    // console.log(valid);
                        if(valid){
                        var formdata = $('.box-content .form-horizontal:eq(1)').serialize();
                        // console.log(JSON.stringify(formdata));
                        var ajax_url = $('.box-content .form-horizontal:eq(1)').attr('action')+"&goon=Y";
                        // console.log( ajax_url);
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
                                    $('#mtype option:selected').text($('#mtype option:selected').text()+"(已設定)");
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
                    console.log($('.box-content .form-actions button'));
                    $('.box-content .form-actions button').trigger('click');
                });
            });
        </script>
    </body>
</html>