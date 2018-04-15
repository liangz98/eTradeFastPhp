function submitDraft(type) {
    if (jQuery('#editSeedNote').val().length != 0) {
        jQuery('#draftBox_saveType').val(type);
        jQuery('#draftBox_wdis').val(jQuery('#editSeedNote').val());
        if (initNotesLength()) {
            jQuery('#draftBoxForm').submit();
            window.setTimeout('ajaxDraftBoxHis(true)', 3000);
        }
    }
}
function rollbackWareNotes() {
    var hisText = $("#hisDraftBox").find("option:selected").attr('title');
    if (jQuery('#hisDraftBox').val()) {
        if (confirm("确认恢复 " + hisText + " 的编辑历史")) {
            jQuery.ajax({url: "/b/vmall/draft/note?mid=" + jQuery('#hisDraftBox').val() + "&rand=" + Math.random(),
                success: function(data) {
                    jQuery('#editSeedNote').val(data.wdis);
                    initNotesLength();
                },
                dataType: "json",
                async: true,
                timeout: 30000
            });
        }
    }
}
function ajaxDraftBoxHis(isappend) {
    jQuery.ajax({
        url: "/b/vmall/draft/query?pin=" + jQuery('#draftbox_pin').val() + "&rand=" + Math.random(),
        success: function(data) {
            if (data != null && data != '') {
                jQuery('#hisDraftBox').empty();
                // jQuery('#hisDraftBox').append('<option>恢复编辑历史</option>');
                jQuery.each(data, function(index, d) {
                    if (d.saveType == 1) {
                        if (isappend && index == 0) {
                            jQuery('#saveInfo').empty().append("手动保存于:" + d.modifyDataStr);
                        }
                        jQuery('#hisDraftBox').append('<option onclick="rollbackWareNotes()" title="' + d.modifyDataStr + '" value="' + d.mid + '">手动保存于:' + d.modifyDataStr + '</option>');
                    } else {
                        if (isappend && index == 0) {
                            jQuery('#saveInfo').empty().append("自动保存于:" + d.modifyDataStr);
                        }
                        jQuery('#hisDraftBox').append('<option onclick="rollbackWareNotes()" title="' + d.modifyDataStr + '" value="' + d.mid + '">自动保存于:' + d.modifyDataStr + '</option>');
                    }
                });
            }
        },
        dataType: "json",
        async: true,
        timeout: 30000
    });
}
function optionClickForIE(obj) {
    var evt = window.event;
    var selectObj = evt ? evt.srcElement : null;
    if (evt && selectObj && evt.offsetY && evt.button != 2 && (evt.offsetY > selectObj.offsetHeight || evt.offsetY < 0)) {
        var oldIdx = selectObj.selectedIndex;
        setTimeout(function() {
            var option = selectObj.options[selectObj.selectedIndex];
            obj();
        }, 60);
    }
}

function initNotesLength() {
    var readLength = jQuery('#editSeedNote').val().length;
    jQuery('#inNotesLength').empty().append(readLength);
    if (readLength > 20000) {
        jQuery('#editSeedNoteErro').show();
        return false;
    }
    jQuery('#editSeedNoteErro').hide();
    return true;
}
jQuery(function() {
    ajaxDraftBoxHis(false);
    window.setInterval("submitDraft(0)", 300000);
    initNotesLength();
    jQuery(editor.doc).bind('keyup', function() {
        initNotesLength();
    });
});
