/**
 * Auto Complete v3.0
 * July 26, 2009
 * Released under the MIT License @ http://www.codenothing.com/license
 * Corey Hart @ http://www.codenothing.com
 */ 
;(function($){
	$.fn.autoComplete = function(options){
		// Set up autocomplete on all possible elements
		return this.each(function(){
			// Cache objects
			var $input = $(this).attr('autocomplete', 'off'), $li, timeid, blurid, 
				// Set defaults and include metadata support
				settings = $.extend({
					// Inner Function Defaults (Best to leave alone)
					opt: -1,
					inputval: '',
					mouseClick: false,
					// Drop List CSS
					list: 'ajax-list',
					rollover: 'ajax-rollover',
					width: $input.outerWidth(),
					top: $input.offset().top + $input.outerHeight(),
					left: $input.offset().left,
					// Post Data
					postVar: 'value',
					postData: {},
					// Limitations
					minChars: 1,
					maxRequests: 0,
					requests: 0, // Inner Function Default
					// Events
					onMaxRequest: function(){},
					onSelect: function(){},
					onRollover: function(){},
					onBlur: function(){},
					preventEnterSubmit: false,
					enter: false, // Inner Function Default
					delay: 100,
					selectFuncFire: true, // Inner Function Default
					// Caching Options
					useCache: true,
					cacheLimit: 50,
					cacheLength: 0, // Inner Function Default
					cache: {} // Inner Function Default
				}, options||{}, $.metadata?$input.metadata():{});

			// Create the drop list (Use an existing one if possible)
			var $ul = $('ul.'+settings.list)[0] ? $('ul.'+settings.list) : $('<ul/>').appendTo('body').addClass(settings.list).hide();

			// Run on keyup
			$input.keyup(function(e){
				var key = e.keyCode;
				settings.enter = false;
				settings.mouseClick = false;
				if ((key > 47 && key < 91) || key == 8){ // Input Keys and Backspace
					settings.opt = -1;
					settings.inputval = $input.val();
					if (settings.inputval.length >= settings.minChars){
						if (timeid) clearTimeout(timeid);
						timeid = setTimeout(function(){ sendRequest(settings); }, settings.delay);
					} else if (key == 8) { // Remove list on backspace of small string
						$ul.html('').hide();
					}
				}
				else if (key == 13 && $li){ // Enter
					settings.opt = -1;
					settings.enter = true;
					if (settings.selectFuncFire) {
						settings.selectFuncFire = false;
						settings.onSelect($li.data('data'), $li);
						setTimeout(function(){ settings.selectFuncFire = true; }, 1000);
					}
					$ul.hide();
				}
				else if (key == 38){ // Up arrow
					if (settings.opt > 0){
						settings.opt--;
						$li = $('li', $ul).removeClass(settings.rollover).eq(settings.opt).addClass(settings.rollover);
						$input.val($li.data('data').value||'');
						settings.onRollover($li.data('data'), $li);
					}else{
						settings.opt = -1;
						$input.val(settings.inputval);
						$ul.hide();
					}
				}
				else if (key == 40){ // Down arrow
					if (settings.opt < $('li', $ul).length-1){
						settings.opt++;
						$li = $('li', $ul.show()).removeClass(settings.rollover).eq(settings.opt).addClass(settings.rollover);
						$input.val($li.data('data').value||'');
						settings.onRollover($li.data('data'), $li);
					}
				}
			}).blur(function(){
				blurid = setTimeout(function(){
					if (settings.mouseClick) return false;
					settings.opt = -1;
					settings.onBlur(settings.inputval, $ul);
					$ul.hide();
				}, 150);
			}).parents('form').eq(0).submit(function(){
				return settings.preventEnterSubmit ? settings.enter : true;
			});
	
			// Ajax/Cache Request
			function sendRequest(settings){
				// Check Max reqests first
				if (settings.maxRequests && settings.requests > settings.maxRequests){
					return settings.onMaxRequest();
				}else if (settings.maxRequests)
					settings.requests++;

				// Load from cache if possible
				if (settings.useCache && settings.cache[settings.inputval])
					return loadResults(settings.cache[settings.inputval]);

				// Send request server side
				settings.postData[settings.postVar] = settings.inputval
				$.post(settings.ajax, settings.postData, function(json){
					json = json && json != '' ? eval('('+json+')') : {};
					// Store results into the cache if need be
					if (settings.useCache){
						settings.cacheLength++;
						settings.cache[settings.inputval] = json;
						// Shift out old cache if necessary
						if (settings.cacheLength > settings.cacheLimit){
							settings.cache = {};
							settings.cacheLength = 0;
						}
					}
					// Show the list if there is a return, else hide it
					if (json.length > 0)
						loadResults(json);
					else
						$ul.html('').hide();
				});
			}

			// List Functionality
			function loadResults(list){
				// Clear the List and align it properly
				$ul.html('').css({top: settings.top, left: settings.left, width: settings.width});
				// Add new rows to the list
				for (i in list)
					if (list[i].value) $('<li/>').appendTo($ul).html(list[i].display||list[i].value).data('data', list[i]);
				// Start mouse actions after list is set and shown
				$ul.show().children('li').mouseover(function(){
					$li = $(this);
					$('li.'+settings.rollover, $ul).removeClass(settings.rollover);
					$input.val( $li.addClass(settings.rollover).data('data').value );
					settings.onRollover($li.data('data'), $li);
				}).click(function(){
					settings.mouseClick = true;
					if (blurid) clearTimeout(blurid);
					settings.onSelect($li.data('data'), $li);
					$ul.hide();
					// Bring the focus back to the input when clicking a list member
					$input.focus();
				});
	
				// Return orignal val when not hovering
				$ul.mouseout(function(){
					$('li.'+settings.rollover, $ul).removeClass(settings.rollover);
					if (! settings.mouseClick) $input.val(settings.inputval);
				});
			}
		});
	};
})(jQuery);
