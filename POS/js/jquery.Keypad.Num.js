$(document).ready(function() {

	// Add  key actions
	// ***************************
	$.extend( $.keyboard.keyaction, {

		});

	// Initialize keyboard
	// ********************
	$('#calc').keyboard({
		layout: 'custom',
		
		customLayout: {
			'default' : [
			' 7 8 9 {b} ',
			' 4 5 6 {c} ',
			' 1 2 3 {a} ',
			' 0 {dec} '
			]
		},

		// Turning restrictInput on (true), prevents yroot and but it
		restrictInput : true,  // Prevent keys not in the displayed keyboard from being typed in
		useCombos     : false, // don't want A+E to become a ligature
		wheelMessage  : '',    // clear tooltips

		
	})
	.addTyping()
	.getkeyboard().reveal();
});
