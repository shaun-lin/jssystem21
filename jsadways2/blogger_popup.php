
<div id="panel_block_category" style="display: none;">
    <center>
        <div style="background-color: white; padding: 30px;">
            <div style="position: absolute; font-size: 2em; top: 0; left: 0; border: 1px solid #ccc; border-radius: 12px; margin-top: 2px; margin-left: 2px; padding: 0 3px;">
                <a href="javascript:CloseBlock();"><i class="fa fa-times" style="color: #ccc;"></i></a>
            </div>

            <div style="font-size: 1em;">
                <h3 style="text-align: right; color: #676767;">
                    <i class="fa fa-list-ol"></i>&nbsp;寫手分類
                </h3>
            </div>

            <form id="category_form" style="margin-bottom: 0; background-color: rgba(0, 0, 0, 0.03); border-radius: 3px; border: 1px solid #e3e3e3; ">
                <div id="category_list" style="max-height: 480px; overflow-y: auto;">
                    <ul id="category_items" style="margin: 0px; background-color: white;">
                    </ul>
                </div>
            </form>
            <br/>
            <a class="btn btn-success" style="float: left;" href="javascript:AddItem('category', '', '');"><i class="fa fa-plus-circle"></i>  增加</a>
            <a class="btn btn-info" style="float: right;" href="javascript:SaveItem('category');"><i class="fa fa-floppy-o"></i>  儲存</a>
            <br/>
        </div>
    </center>
</div>

<div id="panel_block_tag" style="display: none;">
    <center>
        <div style="background-color: white; padding: 30px;">
            <div style="position: absolute; font-size: 2em; top: 0; left: 0; border: 1px solid #ccc; border-radius: 12px; margin-top: 2px; margin-left: 2px; padding: 0 3px;">
                <a href="javascript:CloseBlock();"><i class="fa fa-times" style="color: #ccc;"></i></a>
            </div>

            <div style="font-size: 1em;">
                <h3 style="text-align: right; color: #676767;">
                    <i class="fa fa-tags"></i>&nbsp;寫手標籤
                </h3>
            </div>

            <form id="tag_form" style="margin-bottom: 0; background-color: rgba(0, 0, 0, 0.03); border-radius: 3px; border: 1px solid #e3e3e3; ">
                <div id="tag_list" style="max-height: 520px; overflow-y: auto;">
                    <ul id="tag_items" style="margin: 0px; background-color: white;">
                    </ul>
                </div>
            </form>
            <br/>
            <a class="btn btn-success" style="float: left;" href="javascript:AddItem('tag', '', '', '#FFE7CD');"><i class="fa fa-plus-circle"></i>  增加</a>
            <a class="btn btn-info" style="float: right;" href="javascript:SaveItem('tag');"><i class="fa fa-floppy-o"></i>  儲存</a>
            <br/>
        </div>
    </center>
</div>

<style>
    div.category-item:hover,
    div.tag-item:hover {
        background-color: #ffe4b2;
    }
    
    #category_items,
    #tag_items { 
        list-style-type: none;
    }

    #category_items li,
    #tag_items li {
        border: none;
        background-color: #f8f8f8;
    }

    input.blogger-category-editor,
    input.blogger-tag-editor {
        margin: 0; 
        padding: 12px; 
        font-size: 1.2em;
    }

    input.blogger-tag-color-picker {
        margin-top: 8px;
        width: 20px;
        height: 20px;
        margin-left: -36px;
    }
</style>

<script src="../js/jquery.blockUI-2.70.0.js"></script>
<script>
    var isChrome = false;
    try {
        isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
    } catch(e) {

    }
    
    var idxItemSort = 1;
    var deletedItem = new Array();

    $("#category_items").sortable();
    $("#tag_items").sortable();

    $(document).keyup(function(e) {
        if (e.keyCode == 27) {
            CloseBlock();
        }
    });

    function CloseBlock()
    {
        $.unblockUI();
    }

    function OpenBlock(type)
    {
        CloseBlock();

        idxItemSort = 1;
        deletedItem = new Array();

        $('div#category_list ul').html('');
        $('div#tag_list ul').html('');

        if (type == 'category' || type == 'tag') {
            LoadDetail(type);
        } else {
            CloseBlock();
            return;
        }
        
        $.blockUI({
            message: $('#panel_block_'+ type),
            css: {
                top: '10%',
                left: '32%',
                width: '36%',
                background: 'none',
                border: 'none',
            }
        });
    }

    function LoadDetail(type)
    {
        $.ajax({
            url: type +'_action.php?method=load&'+ type +'_relation=blogger',
            type: 'POST',
            beforeSend: function() {

            },
            success: function(feedback) {
                if (typeof feedback == 'object') {
                    if ('success' in feedback && feedback.success) {
                        if (feedback.data) {
                            if (feedback.data.length) {
                                for (var idx in feedback.data) {
                                    AddItem(type, feedback.data[idx][type +'_id'], feedback.data[idx][type +'_name'], type == 'tag' ? feedback.data[idx][type +'_color'] : null);
                                }
                            } else {
                                AddItem(type, '', '', type == 'tag' ? '#FFE7CD' : '');
                            }
                        }
                    } else if ('message' in feedback && feedback.message.toString().length) {
                        alert(feedback.message);
                    }
                }
            },
            error: function() {

            }
        });
    }

    function AddItem(type, id, name, extra)
    {
        var listItemHtml = '<li class="ui-state-default" id="blogger_'+ type +'_'+ (id ? id : '') +'">'+
                                '<div class="'+ type +'-item" style="font-size: 1.4em; padding: 8px;">'+
                                    '<input type="text" name="'+ type +'_name[]" value="'+ name +'" class="blogger-'+ type +'-editor">'+ 
                                    (isChrome && type == 'tag' ? '<input type="color" name="'+ type +'_color[]" value="'+ extra +'" class="blogger-tag-color-picker">' : '') + 
                                    '<input type="hidden" name="'+ type +'_id[]" value="'+ id +'">'+
                                    '<input type="hidden" name="'+ type +'_sort[]" value="'+ idxItemSort +'">'+
                                    '<a onclick="DeleteItem(this);" style="cursor: pointer;">'+
                                        '<i class="fa fa-trash" style="color: red; opacity: .7; font-size: 1.4em; position: relative; top: 5px; margin-left: 16px;"></i>'+
                                    '</a>'+
                            '   </div>'+
                            '</li>';

        $('div#'+ type +'_list ul').append(listItemHtml);
        idxItemSort++;

        if (!id) {
            document.getElementById(type +'_list').scrollTop = document.getElementById(type +'_list').scrollHeight;
            $('div#'+ type +'_list ul').find(':text:last').focus();
        }
    }

    function SaveItem(type)
    {
        $('#'+ type +'_form ul').find('li').each(function(idxItem) {
            $(this).find('[name^="'+ type +'_sort"]').val(idxItem + 1);
        });

        var postData = $('#'+ type +'_form').serialize();
        
        if (deletedItem.length) {
            postData += "&"+ type +"_deleted="+ deletedItem.join(',');
        }
    
        $.ajax({
            url: type +'_action.php?method=save&'+ type +'_relation=blogger',
            type: 'POST',
            data: postData,
            success: function(feedback) {
                if (typeof feedback == 'object') {
                    if ('success' in feedback && feedback.success) {
                        alert(feedback.message);
                        CloseBlock();
                        window.location.reload();
                    } else if ('message' in feedback && feedback.message.toString().length) {
                        alert(feedback.message);

                        if ('id' in feedback.data) {
                            $('#blogger_'+ type +'_'+ feedback.data.id).find(':text').focus();
                        } else if ('value' in feedback.data) {
                            var isSecond = false;

                            $('ul#'+ type +'_items').find(':text[name^="'+ type +'_name"]').each(function() {
                                if (this.value == feedback.data.value) {
                                    if (isSecond === true) {
                                        $(this).focus();
                                        return false;
                                    }
                                    
                                    isSecond = true;
                                }
                            });
                        }
                    }
                } else {
                    alert('發生未知的錯誤');    
                }
            },
            error: function() {
                alert('發生錯誤');
            }
        });
    }

    function DeleteItem(obj)
    {
        deletedItem.push($(obj).parent().parent().find(':hidden').val());
        $(obj).parent().parent().remove();
    }
</script>
