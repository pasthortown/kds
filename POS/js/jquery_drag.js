/*$(function() {
	$( "#tabs-1" ).accordion();
	$( "#tabs-1 li" ).draggable({
		containment: "#menuCategoria",
		scroll: true,
		appendTo: "body",
		helper: "clone",
		cursor: "move"
		
	});
	
	$( "#grillaCategoria td" ).droppable({
		activeClass: "ui-state-default",
		hoverClass: "ui-state-hover",
		accept: ":not(.ui-sortable-helper)",
		drop: function( event, ui ) {
			$( this ).find( ".placeholder" ).remove();
			$( "<button></button>" ).text( ui.draggable.text() ).appendTo( this );
		}
	}).sortable({
		items: "li:not(.placeholder)",
			sort: function() {
			// gets added unintentionally by droppable interacting with sortable
			// using connectWithSortable fixes this, but doesn't allow you to customize active/hoverClass options
			$( this ).removeClass( "ui-state-default" );
		}
	});
	
});*/
	/*$( "#tabs-2" ).accordion();
		$( "#tabs-2 li" ).draggable({
			containment: "#menuPlu",
			scroll: true,
			appendTo: "body",
			helper: "clone",
			cursor: "move"
		});
	$( "#grillaPlu td" ).droppable({
		activeClass: "ui-state-default",
		hoverClass: "ui-state-hover",
		accept: ":not(.ui-sortable-helper)",
		drop: function( event, ui ) {
			$( this ).find( ".placeholder" ).remove();
			$( "<button></button>" ).text( ui.draggable.text() ).appendTo( this );
		}
	}).sortable({
		items: "li:not(.placeholder)",
			sort: function() {
			// gets added unintentionally by droppable interacting with sortable
			// using connectWithSortable fixes this, but doesn't allow you to customize active/hoverClass options
			$( this ).removeClass( "ui-state-default" );
		}
	});*/