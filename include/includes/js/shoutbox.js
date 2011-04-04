$(document).ready(function() {
	$dialog = $('#shoutboxsmiliesdiv')
		.dialog({
			autoOpen: false,
			title: 'Smilies',
			width: 200
	    });
	//Add Styles
	css = [
    	'<style type="text/css">',
    	'#shb_form input { float: right; }',
    	'</style>'
    ];
	$(document.head).append($(css.join('')));
});

ic.shoutboxInsert = function (aTag,eTag) {
    var input = document.forms['shoutboxform'].elements['shoutbox_textarea'];
    input.focus();
    if(typeof document.selection != 'undefined') {
        var range = document.selection.createRange();
        var insText = range.text; range.text = aTag + insText + eTag;
        range = document.selection.createRange();
        if (insText.length == 0) {
            range.move('character', -eTag.length);
        } else {
            range.moveStart('character', aTag.length + insText.length + eTag.length);
        }
        range.select();
    } else if (typeof input.selectionStart != 'undefined') {
        var start = input.selectionStart;
        var end = input.selectionEnd;
        var insText = input.value.substring(start, end);
        input.value = input.value.substr(0, start) + aTag + insText + eTag + input.value.substr(end);
        var pos;
        if (insText.length == 0) {
            pos = start + aTag.length;
        } else {
            pos = start + aTag.length + insText.length + eTag.length;
        }
        input.selectionStart = pos; input.selectionEnd = pos;
    } else {
        var pos = input.value.length;
        var insText = prompt("Bitte geben Sie den zu formatierenden Text ein:");
        input.value = input.value.substr(0, pos) + aTag + insText + eTag + input.value.substr(pos);
    }
}

ic.shoutboxArchiveDelete = function(ids) {
    if (typeof ids == 'string' && ids.length > 4) {
        $.ajax({
            url     : 'index.php?shoutbox',
            type    : 'POST',
            data    : 'del=true&' + ids + '& antispam_id=' +
                      $('#shb_form input[name="antispam_id"]').val(),
            dataType: 'text json',
            success : function(data, status) {
                if (data == 'reload') {
                    ic.Ajaxload('index.php?shoutbox');
                } else if (data != 'error') {
                    $.each(data, function(i, v) {
                        $('#shb_tr_' + v).remove();
                    });
                }
            }
        });
    }
}

ic.shoutboxArchiveOnload = function() {
    $('#shb_delall').click(function (e) {
        //if (anz = prompt(unescape("Wie viele Eintr%E4ge sollen erhalten bleiben%3F\n(Es werden die zuletzt geschriebenen erhalten)", "0"))) {
        if (anz = prompt("Wie viele Einträge sollen erhalten bleiben?\n(Es werden die zuletzt geschriebenen erhalten)", "0")) {
            if (anz >= 0) {
                ic.shoutboxArchiveDelete('&all=' + anz);
            } else {
                alert("Du musst eine Zahl größer gleich 0 eingeben");
            }
        }
        e.preventDefault();
    });
    $('#shb_form input').dblclick(function() {
        if (confirm('Wirklich löschen?')) {
            ic.shoutboxArchiveDelete('chk=' + $(this).val());
        }
    });
    $('#shb_delsel').click(function(e) {
        if (confirm('Wirklich löschen?')) {
            ic.shoutboxArchiveDelete($('#shb_form').serialize());
        }
        e.preventDefault();
    });
    $('#shb_multipages a').icAjaxload();
};