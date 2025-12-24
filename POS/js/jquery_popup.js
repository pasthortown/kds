 $(function() {
	$( "#popup" ).dialog({
		autoOpen: false,
		show: {
			effect: "blind",
			duration: 1000
		},
		hide: {
			effect: "explode",
			duration: 1000
		}
	});
	$( "#opener" ).click(function() {
		$( "#popup" ).dialog( "open" );
	});
});
     
