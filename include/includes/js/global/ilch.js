//Activate console for Firebug and other Browsers
if (window['loadFirebugConsole']) {
	window.loadFirebugConsole();
} else {
	if (!window['console']) {
		window.console = {};
		window.console.info = alert;
		window.console.log = alert;
		window.console.warn = alert;
		window.console.error = alert;
	}
}

//ic Objekt, für ilch eigene Funktionen, um Konflikte zu vermeiden
var ic = {}

//Ein Konfiguration von Dialog von jqueryui für die Nutzung mit Iframes
ic.modalDialogContainer = [];
ic.modalDialog = function (options) {
    if (typeof options == 'string') {
        string = eval(options);
    }
    var settings = {
        width: 400,
        height: 300,
        realiframe: true, //true -> nutzt <iframe> ; false -> läd inhalt in ein <div>
        resizable: true,
        title: 'Modal Dialog',
        url: ''
    };
    $.extend(settings, options);
    count = ic.modalDialogContainer.length + 1;
    var div = $('<div id="icModalDialog' + count + '" />').css('overflow','hidden').appendTo(document.body);

    if (settings.realiframe) {
        var ifrm = $('<iframe id="icModalIframe' + count + '" name="icModalIframe' + count + '" width="100%" height="100%" marginwidth="0" marginheight="0" frameborder="0" scrolling="auto" title="Dialog Title"></iframe>').
        appendTo(div);
        ifrm.attr('src', settings.url);
    } else {
        $.ajax({
            async: false,
            dataType: 'html',
            url: settings.url,
            success: function(data) {
                div.append(data);
            }
        });
    }

    ic.modalDialogContainer[ic.modalDialogContainer.length] = $(div).dialog({
        modal: true,
        autoOpen: true,
        height: settings.height,
        width: settings.width,
        draggable: true,
        resizeable: settings.resizable,
        title: settings.title
    });
};
ic.modalDialogClose = function() {
    ic.modalDialogContainer[ic.modalDialogContainer.length-1].dialog('close').dialog('destroy');
    ic.modalDialogContainer.pop();
}

//Möglichkeit Funktionen für DocumentReady, Ajaxload oder beides einzuhängen
//Aufruf:   ic.documentReadyAdd(func, type);
//          func: Funktion
//          type (String): both, domready, ajaxload
//          Gibt an, wann die Funktion geladen wird
//Array in das man Funktionen einhängen kann die bei DocumentReady und Ajaxreload aufgerufen werden
ic.documentReadyFuncs = {count: 0};
ic.documentReadyAdd = function(func) {
    if ($.isFunction(func)) {
        var type = 'both';
        if (arguments[1] == 'ajax' || arguments[1] == 'domready') {
            type = arguments[1];
        }
        ic.documentReadyFuncs[ic.documentReadyFuncs.count] = {
            'func': func,
            'type': type
        }
        ic.documentReadyFuncs.count++;
    }
}
ic.documentReady = function() {
    for (var i in ic.documentReadyFuncs) {
        if ($.isFunction(ic.documentReadyFuncs[i].func)) {

            ic.documentReadyFuncs[i].func();
        }
    }
}

//icAjaxload, Möglichkeit Links oder Forumlare als Ajaxload zu konfigurieren, Links (oder src des form) müssen relativ sein, als z.B. index.php?forum
//Aufruf:   $('a.ajaxload').icAjaxload();
//Zum Reload eines einzelnen Containers, im Grunde für Boxen (kann aber auch anders verwendet werden),
//kann die Id des Elements angegeben werden, dessen Inhalt verändert werden soll, dabei wird dann aber automatisch die Box geladen,
//also aus dem includes/boxes Ordnder -> index.php?shoutbox -> include/boxes/shoutbox.php
$.fn.icAjaxload = function() {
    var BoxLoad = arguments.length == 1 ? arguments[0] : false;
    return this.each(function(arg) {
        console.lo
        var tag = this.tagName.toLowerCase();
        var linkadd = '&ajax=true';
        var successFunc = function(data) {
            $('#icHmenu').html(data.hmenu);
            $('#icContent').html(data.content);
            document.title = data.title;
            ic.documentReady();
        };

        if (BoxLoad !== false) {
            successFunc = function(data) {
                $('#' + BoxLoad).html(data.content);
            };
            linkadd = linkadd + '&boxreload=true';
        }
        if (tag == 'a') {
            $(this).click(function() {
                $.ajax({
                    url: $(this).attr('href') + linkadd,
                    dataType: 'json',
                    success: successFunc
                });
                return false;
            });
        } else if (tag == 'form') {
            $(this).submit(function() {
                $.ajax({
                    url: $(this).attr('action') + linkadd,
                    dataType: 'json',
                    type: $(this).attr('method').toLowerCase() == 'post' ? 'post' : 'get',
                    data: $(this).serialize(),
                    success: successFunc
                });
                return false;
            });
        }
    });
}


ic.documentReadyAdd(function() {
    //ajaxlinks und ajaxforms
    $('a.ajaxload').icAjaxload();
    $('form.ajaxload').icAjaxload();
});
$(document).ready(ic.documentReady);