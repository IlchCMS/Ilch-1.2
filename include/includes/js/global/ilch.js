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