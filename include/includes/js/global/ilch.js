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

//"Simpler" Script und CSS Loader, damit Scripts nur einmal geladen werden
//Beispiel: ic.loadFile('pfad/script.js');
//Komplex: ic.loadFile({
//  file: 'pfad/loadscript.php',
//  type: 'js',
//  reload: true,
//  onload: function() { //wird nach dem laden geladen };
//});
// Die möglichen Optionen bei settings schauen
ic.loadFileData = {};
ic.loadFile = function(options) {
    if (typeof options == 'string') {
        options = {
            file: options
        };
    }
    var settings = {
        file: null,     //Dateiname des zu ladenden Datei
        reload: false,  //Gibt an ob die Datei nochmals geladen werden soll
        onload: null,   //Eine Funktion die nach Laden der Datei ausgeführt wird
        type: null      //Typ, js oder css, wenn die Dateiendung nicht .js oder .css ist
    }
    $.extend(settings, options);
    if (settings.type == null) {
        if (settings.file.match(/\.js$/) != null) {
            settings.type = 'js';
        } else if(settings.file.match(/\.css$/) != null) {
            settings.type = 'css';
        } else {
            console.log('ic.loadFile-Error: Could not detect Filetype!');
            return false;
        }
    }
    if (settings.reload) {
        //Aus DOM + ic.loadFileData löschen?
        if (settings.type == 'js') {
            $('script src="'+settings.file+'"').remove();
        } else if (settings.type == 'css') {
            $('link href="'+settings.file+'"').remove();
        }
        if (settings.file in ic.loadFileData) {
            delete ic.loadFileData[settings.file];
        }
    }
    if (!(settings.file in ic.loadFileData)) {
        ic.loadFileData[settings.file] = {
            onload: [],
            loaded: false
        }
        if (typeof settings.onload == 'function') {
            ic.loadFileData[settings.file].onload.push(settings.onload);
        }
        if (document.head == null) {
            document.head = document.getElementsByTagName('head')[0];
        }
        if (settings.type == 'js') {
            $.ajax({
                url: settings.file,
                success: function (){
                    ns = document.createElement('script');
                    ns.src = settings.file;
                    ns.type= 'text/javascript';
                    document.head.appendChild(ns);
                    ic.loadFileData[settings.file].loaded = true;
                    $.each(ic.loadFileData[settings.file].onload, function(k,v) {
                        v();
                    });
                }
            });
        } else if (settings.type == 'css') {
            var ncss = $('<link type="text/css" rel="stylesheet" href="' + file + '" />');
            if (typeof settings.onload == 'function') {
                ncss.load(settings.onload);
            }
            $(document.head).append(ncss);
        }
    } else if (settings.onload != null && typeof settings.onload == 'function') {
        if (settings.file in ic.loadFileData && !ic.loadFileData[settings.file].loaded) {
            ic.loadFileData[settings.file].onload.push(settings.onload);
        } else {
            settings.onload();
        }
    }
}

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
ic.documentReadyFuncs = {
    count: 0
};
ic.documentReadyAdd = function(func) {
    if ($.isFunction(func) && $.inArray(func, ic.documentReadyFuncs) == -1) {
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

/* ic.Ajaxload
   Funktion um einen einfachen Ajaxcall abzusenden, um den Inhalt der Seite ohne Refresh zu verändern
   Zu den Optionen die Kommentare bei settings beachten
   Beispiel zum (Neu-)Laden der Shoutbox:
        ic.Ajaxload({url:'index.php?shoutbox', elementId: 'icShoutbox', type: 'box'});
*/
ic.Ajaxload = function(options) {
    if (typeof options == 'string') {
        options = {
            url: options
        };
    }
    var settings = {
        url: '',        //Url von wo Inhalt geladen wird z.B. index.php?forum
        elementId: '',  //Id eines Elements dessen Inhalt neu geladen wird, wenn nicht angegeben wird Content neu geladen, title und hmenu neu gesetzt
        type: 'content',//[content|box] bei box wird bei index.php?shoutbox, die include/boxes/shoutbox.php geladen, ansonsten normal im contents Ordner
        postData: null, //Parameter für einen POST Aufruf, Object {name:value, name2:value2, ...} (wie Formular mit method="post")
        onload: null,    //Funktion die nach dem Laden aufgerufen wird
        showLoading: false, //Wenn true dann wird loadingContent im Container während des Ladens angezeigt
        loadingContent: '<img src="include/images/icons/ajax-loader-arrows.gif" />' //Wenn gesetzt wird dies als Inhalt während des Ladens angezeigt
    };
    $.extend(settings, options);
    if (settings.showLoading) {
        var destEl = (settings.elementId != undefined && settings.elementId != '') ?
        $('#'+settings.elementId) : $('#icContent');
        destEl.html(settings.loadingContent);
    }
    $.ajax({
        url: settings.url + '&ajax=true' + (settings.type == 'box' ? '&boxreload=true' : ''),
        dataType: 'text json',
        type: settings.postData ? 'POST' : 'GET',
        data: settings.postData,
        success: function(data) {
            if (!data || !data.content) {
                return;
            }
            if (settings.elementId != undefined && settings.elementId != '') {
                $('#' + settings.elementId).html(data.content);
                if (settings.type != 'box') {
                    settings.type = 'element';
                }
                ic.documentReady('ajax' + settings.type, settings.elementId);
            } else {
                document.title = data.title;
                $(document.head).append(data.headerAdds);
                $(document.body).append(data.bodyendAdds);
                $('#icHmenu').html(data.hmenu);
                $('#icContent').html(data.content);
                ic.documentReady('ajaxcontent');
            }
            if (settings.onload) {
                settings.onload();
            }
        }
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

/*icAjaxload
Möglichkeit Links oder Forumlare als Ajaxload zu konfigurieren, Links (oder src des form) müssen relativ sein, als z.B. index.php?forum
Wobei ic.Ajaxload verwendet wird und dabei automatisch die url vom Link bzw Forumlar gesetzt wird,
bei Forumlaren wird natürlich auch postData gesetzt, alles weitere zu den Optionen, siehe ic.Ajaxload
*/
$.fn.icAjaxload = function(options) {
    //options siehe ic.Ajaxload
    return this.each(function() {
        var tag = this.tagName.toLowerCase();
        if (tag == 'a') {
            $(this).click(function() {
                ic.Ajaxload($.extend({
                    url:$(this).attr('href')
                    }, options));
                return false;
            });
        } else if (tag == 'form') {
            $(this).submit(function() {
                ic.Ajaxload($.extend({
                    url: $(this).attr('action'),
                    postData: $(this).serialize()
                }, options));
                return false;
            });
        }
    });
}

/* ic.Alert
Dialogvorlage für Alerts und andere Benachrichtigungen/Fragen, mehr zu den Optionen sie jQueryUI.Dialog
Es ist wesentlich mehr möglich als mit einem alert() aber der einfache Aufruf "entspricht" dem alert,
allerdings mit besserer Optik
Bsp : ic.Alert('Dies ist ein Satz, den man erst wegklicken muss');
*/
ic.Alert = function(options) {
    if (typeof options == 'string') {
        options = {
            content: options
        };
    }
    var interval, closemsg;
    var settings = {
        height: 200,
        title: 'Modal Dialog',
        buttons: {
            'OK': function () {
                if (interval != undefined) {
                    window.clearInterval(interval);
                }
                $(this).dialog('close');
            }
        },
        content: '',
        modal: true,
        resizable: false,
        symbol: 'alert',
        autoclose: false, //false or integer for seconds
        autocloseButton: ''
    };
    $.extend(settings, options);

    if (settings.symbol == 'alert') {
        if (options.title == undefined) {
            settings.title = 'Alert';
        }
        settings.content = '<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>' + settings.content;
    }

    var closemsg = '';

    if (settings.autoclose !== false) {
        closemsg = 'Meldung schließt sich in <span class="timercount">'+settings.autoclose+'</span> Sekunden';
        interval = window.setInterval(function() {
            var s = $('span.timercount', div);
            var n = Number(s.html());
            if (n > 0) {
                s.html(n-1)
            } else {
                if (settings.autocloseButton != '') {
                    $(div).dialog("option","buttons")[settings.autocloseButton].call(div);
                } else {
                    $(div).dialog('close');
                }
                window.clearInterval(interval);
            }
        }, 1000);
    }

    var div = $('<div id="dialog-confirm" title="'+settings.title+'"><p>'+settings.content+'</p>'+closemsg+'</div>');
    $(div).dialog({
        modal: settings.modal,
        height: settings.height,
        buttons: settings.buttons,
        resizable: settings.resizable
    });
}