
	$(document).ready(function() {
	
		var date = new Date();
		var d = date.getDate();
		var m = date.getMonth();
		var y = date.getFullYear();
		var calendar = $('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			selectable: false,
			selectHelper: true,
			select: function(start, end, allDay) {
				var title = prompt('Event Title:');
				if (title) {
					calendar.fullCalendar('renderEvent',
						{
							title: title,
							start: start,
							end: end
						},
						true // make the event "stick"
					);
				}
				calendar.fullCalendar('unselect');
			},
					
			editable: false, 
			events: "include/includes/func/kalender.php",
			timeFormat: 'H(:mm)',
      eventRender: function(calEvent, element) {
					var tipContent = "<strong>" +
          calEvent.title + " | Startet um: " + calEvent.time + "</strong><br/>";
					
					if (typeof calEvent.location != 'undefined') {
						tipContent +=  '<br/>' + calEvent.location;
					}
					if (typeof calEvent.description != 'undefined') {
						tipContent +=  '<br/>' + calEvent.description;
					}
					$(element).qtip({
						content: tipContent,
						position: {
							corner: {
								target: 'leftMiddle',
								tooltip: 'rightMiddle'
							}
						},
					border: {
						radius: 4,
						width: 3
					},
					style: {
						name: 'dark',
						tip: 'rightMiddle'
					}
			});
	}
		});
	});

