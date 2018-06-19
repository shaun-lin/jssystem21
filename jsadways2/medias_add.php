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
    $objCpdetail = CreateObject('Cp_detail');
    $cue = GetVar('cue');
    if($cue == "2"){
    $result = $objCpdetail->searchAll('item_seq = '. $_GET['item_seq']);
    }
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
                background-color: #00bfff;
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
                $(window).on('load', function(){
                    //新增對內表時需要鎖定選單
                    var media_id = "<?=$_GET['media2'];?>";
                    var cue = "<?= $_GET['cue'];?>";



                    if(cue == "2"){

                        $('#media').val("<?= $result[0]['media_id']?>").attr("disabled","disabled");
                        var media_id = $('#media').val();
                        //set item select
                        $.ajax({
                            url: 'medias_add_option.php',
                            type: 'post',
                            data: {id:media_id,group:"media"},
                            dataType: 'json',
                            success:function(response){

                                var len = response.length;

                                $("#items").empty();
                                $('#items').attr('size',len + 1);
                                $("#items").append("<option value=' '>-- 請選擇媒體 --</option>");
                                for( var i = 0; i<len; i++){
                                    var id = response[i]['key'];
                                    var name = response[i]['name'];
                                    $("#items").append("<option value='"+id+"'>"+name+"</option>");
                                }
                                var campign_id = "<?= $_GET['id'];?>";
                                $('#items').val("<?= $result[0]['item_id']?>").attr("disabled","disabled");
                                var item_id = $('#items').val();
                                  $.ajax({
                                    url: 'medias_add_option.php',
                                    type: 'post',
                                    data: {id:item_id,media_id:media_id,campign_id:campign_id,group:"items"},
                                    dataType: 'json',
                                    success:function(response){

                                        var len = response.length;
                                        $("#mtype").empty();
                                        $("#mtype").attr("size",len + 1);
                                        $("#mtype").append("<option value=' '>-- 請選擇賣法 --</option>");
                                        for( var i = 0; i<len; i++){
                                            var id = response[i]['key'];
                                            var name = response[i]['name'];
                                            $("#mtype").append("<option value='"+id+"'>"+name+"</option>");
                                        }
                                        $('#mtype').val("<?= $result[0]['mtype_number']?>").attr('disabled','disabled');
                                        LoadMtype();
                                    },
                                    error: function(xhr, status, error) {
                                             console.log(xhr.responseText);
                                             console.log(status);
                                             console.log(error);
                                    }
                                });
                            },
                            error: function(xhr, status, error) {
                                     console.log(xhr.responseText);
                                     console.log(status);
                                     console.log(error);
                            }
                        });

                    }
                });
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

                            $("#items").empty();
                            $('#items').attr('size',len + 1);
                            $("#items").append("<option value=' '>-- 請選擇媒體 --</option>");
                            for( var i = 0; i<len; i++){
                                var id = response[i]['key'];
                                var name = response[i]['name'];
                                $("#items").append("<option value='"+id+"'>"+name+"</option>");


                            }
                        },
                        error: function(xhr, status, error) {
                                 console.log(xhr.responseText);
                                 console.log(status);
                                 console.log(error);
                        }
                    });
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
                            $("#mtype").empty();
                            $("#mtype").attr("size",len + 1);
                            $("#mtype").append("<option value=' '>-- 請選擇賣法 --</option>");
                            for( var i = 0; i<len; i++){
                                var id = response[i]['key'];
                                var name = response[i]['name'];
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
                });
                //載入Template Form
                $('#mtype').change(function(){
                  LoadMtype();
                });
                function LoadMtype(){
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

                    if($('#mtype').prop('selectedIndex')!="0"){
                            $.ajax({
                            url: 'medias_add_option.php',
                            type: 'post',
                            data: {id:mtype_id,group:"template"},
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
                                media_url = "mtype_" + media_url.trim() + ".php?id="+id+"&cue="+cue+"&media=" + mtype_id +  "&media2=" + mtype_number + "&copmpanies=" + company_id + "&campaign=" + id;

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
                                     //將表單放入fieldset中
                                    $('fieldset').append(new_html);
                                    $('#addForm').hide();
                                    //隱藏原模板新增媒體按鈕
                                    $('.box-content .form-actions').hide();
                                    //隱藏模板的footer
                                    $('.box-content footer').hide();
                                    //修改模板form的action
                                    $('.box-content .form-horizontal:eq(1)').attr('action',$('.box-content .form-horizontal:eq(1)').attr('action')+media_id+"&itemid="+item_id+"&mtypename="+mtype_name+"&mtypenumber="+mtype_number+"&mtypeid="+mtype_id+"&media_name="+media_name+"&mediaid="+media_id);
                                //將儲存按鈕show出來
                                 $('#save').show();
                                 $('#saveExit').show();
                                 $('form :input:visible:enabled:first').focus();
                                 //處理寫手模板Youtuber新增按鈕
                                  if (mtype_id == "166" || mtype_id == "162"){
                                    // console.log(mtype_id);
                                    //新增寫手Youtuber新增按鈕
                                    $('a[name^="PersonAdd"]').each(function(){
                                        var getVal = $(this).attr('href');
                                        $(this).removeAttr('href');
                                        $(this).unbind().click(function(){
                                            $.get(getVal,function(data){
                                                var new_html_index = data.indexOf('box-content');
                                                var new_html = data.substring(new_html_index-12);
                                                $('#addForm').append(new_html);
                                                $('#addForm').show();
                                                $('#addList .form-actions').hide();

                                                $('#addForm .form-horizontal').submit(false);
                                                $('#list').hide();
                                                //新增寫手費
                                                $('#complete').click(function(){
                                                    $mediaform = $('#addForm .form-horizontal');
                                                    // 檢查html5必填欄位
                                                    var valid = $mediaform[0].checkValidity();
                                                    if( valid){
                                                    var post_url= $('#addForm').find('form').attr('action');
                                                    var formdata=$mediaform.serialize();
                                                    
                                                    $.ajax({
                                                    url: post_url,
                                                    type: 'post',
                                                    data: formdata,
                                                    dataType: 'json',
                                                    success:function(response){
                                                         var len = response.length;
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
                                                                var new_html = data.substring(new_html_index+9);
                                                            }
                                                                $('#addForm').empty();
                                                                $('#addForm').hide();
                                                                $('#list').show();
                                                                $('#addList').empty();
                                                                $('#addList').append(new_html);
                                                                $('#addList .form-actions').hide();
                                                                PersonDel(media_url,media_id,item_id,mtype_name,mtype_number,mtype_id,media_name);
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
                }
                    //寫手與Youtuber刪除按鈕動作
                  function PersonDel(media_url,media_id,item_id,mtype_name,mtype_number,mtype_id,media_name){
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

                  //寫手與Youtuber刪除後畫面重新整理
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
                    //檢查html5必填欄位
                    var valid = $mediaform[0].checkValidity(); 
                        if(valid){
                        var formdata = $('.box-content .form-horizontal:eq(1)').serialize();
                        var ajax_url = $('.box-content .form-horizontal:eq(1)').attr('action')+"&goon=Y";
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
                    console.log($('.box-content .form-actions button'));
                    $('.box-content .form-actions button').trigger('click');
                });
            });
        </script>
    </body>
</html>