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
//Der Funktion werden 2 Paramenter übergeben, der erste gibt an, welcher Art der Reload war:
// 1. Parameter (string): domready, ajaxcontent, ajaxelement, ajaxbox
// ajaxcontent ist ein Ajaxreload ohne spezielle Parameter,
// ajaxelement ist der Reload eines Elments und ajaxbox bei einem Boxreload
// 2. Parameter gibt im Falle von ajaxelement und ajaxbox die ElementId des neugeladenen Containers

//Array in das man Funktionen einhängen kann die bei DocumentReady und Ajaxreload aufgerufen werden
ic.documentReadyFuncs = {count: 0};
ic.documentReadyAdd = function(func) {
    if ($.isFunction(func)) {
        var type = 'both';
        if (arguments[1] == 'ajaxload' || arguments[1] == 'domready') {
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
    var type = 'domready';
    var ftype = 'domready';
    var elemid = null;
    if (arguments.length > 0 && $.inArray(arguments[0], ['ajaxcontent', 'ajaxelement', 'ajaxbox']) != -1) {
        type = 'ajaxload';
        ftype = arguments[0];
        if (arguments.length > 1) {
            elemid = arguments[1];
        }
    }
    for (var i in ic.documentReadyFuncs) {
        if ($.isFunction(ic.documentReadyFuncs[i].func) && (ic.documentReadyFuncs[i].type == 'both' || ic.documentReadyFuncs[i].type == type)) {
            ic.documentReadyFuncs[i].func(ftype, elemid);
        }
    }
}

//icAjaxload, Möglichkeit Links oder Forumlare als Ajaxload zu konfigurieren, Links (oder src des form) müssen relativ sein, als z.B. index.php?forum
//Aufruf:   $('a.ajaxload').icAjaxload();
//Zum Reload eines einzelnen Containers kann man die ElementId des Containers angeben
//Aufruf:   $('a#meinlink').icAjaxload('meindiv');
//Für Boxen gibts noch eine spezielle Möglichkeit, damit der Inhalt der Box neugeladen werden kann ohne Extra Dateien im include/contents Ordner anlegen zu müssen
//es wird mit folgender Option eine Datei im include/boxes Ordner geladen, also bei index.php?shoutbox z.B. die include/boxes/shoutbox.php
//Aufruf:   $('#shoutboxlink').icAjaxload('shoutbox', 'box');
//          Im Container mit der Id shoutbox wird der Inhalt der include/boxes/shoutbox.php geladen
$.fn.icAjaxload = function() {
    var elementId = arguments.length >= 1 ? arguments[0] : false;
    var BoxLoad = arguments.length >= 2 ? arguments[1] : false;
    return this.each(function(arg) {
        var tag = this.tagName.toLowerCase();
        var linkadd = '&ajax=true';
        var successFunc = function(data) {
            $('#icHmenu').html(data.hmenu);
            $('#icContent').html(data.content);
            document.title = data.title;
            ic.documentReady('ajaxcontent');
        };

        if (elementId !== false) {
            var type = 'ajaxelement';
            if (BoxLoad !== false && BoxLoad == 'box') {
                linkadd = linkadd + '&boxreload=true';
                type = 'ajaxbox';
            }
            successFunc = function(data) {
                $('#' + elementId).html(data.content);
                ic.documentReady(type, elementId);
            };

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
//Möglichkeit Ajaxload ohne Link/Form aufzurufen
//Aufruf:   ic.Ajaxload('index.php?news-2');   -> Wie bei einem Link mit ajaxload Klasse
//          ic.Ajaxload({url:'index.php?shoutbox', elementId: 'icShoutbox', type: 'box'});  -> Wie beim Laden einer Box, siehe weiter oben
ic.Ajaxload = function(options) {
    if (typeof options == 'string') {
        options = {url: options};
    }
    var settings = {
        url: '',        //Url von wo Inhalt geladen wird z.B. index.php?forum
        elementId: '',  //Id eines Elements dessen Inhalt neu geladen wird, wenn nicht angegeben wird Content neu geladen, title und hmenu neu gesetzt
        type: 'content' //content oder box, bei box wird bei index.php?shoutbox, die include/boxes/shoutbox.php geladen
    };
    $.extend(settings, options);
    var linkadd = '&ajax=true';
    var successFunc = function(data) {
        $('#icHmenu').html(data.hmenu);
        $('#icContent').html(data.content);
        document.title = data.title;
        ic.documentReady('ajaxcontent');
    };
    var type = 'ajaxelement';
    if (options.type == 'box') {
        linkadd = linkadd + '&boxreload=true';
        type = 'ajaxbox';
    }
    if (options.elementId != undefined && options.elementId != '') {
        successFunc = function(data) {
            $('#' + options.elementId).html(data.content);
            ic.documentReady(type, options.elementId);
        };
    }

    $.ajax({
        url: options.url + linkadd,
        dataType: 'json',
        success: successFunc
    });
}

ic.documentReadyAdd(function(type, elemid) {
    //ajaxlinks und ajaxforms
    if (type != 'domready') {
        elemid = '#' + elemid + ' ';
    } else {
        elemid = '';
    }
    $(elemid + 'a.ajaxload').icAjaxload();
    $(elemid + 'form.ajaxload').icAjaxload();
});
$(document).ready(ic.documentReady);