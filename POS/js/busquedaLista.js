$(document).ready(function(){
						   
	$("#tabs-1").busquedaLista();//el tabs-1 es el id de la lista que deseas buscar
	$("#tabs-2").busquedaLista();		
	  	  	 
});

jQuery.fn.busquedaLista = function() { 	
	input = $('<input type="text" class="search">');	
	$(input).attr("placeholder","Búscar ...");	
	$(this).prepend(input);
	var list	= $(this);
    $(input)
	      .change( function () {
	        var filter = $(this).val();	        
	        if(filter) {	        	
	        $("li",$(list)).hide();
	          $("li:Contains(" + filter + ")",$(list)).show();
	        } else {	        	
	        	$("li",$(list)).show();
	        }
	        return false;
	      })
	    .keyup( function () {	        
	        $(this).change();
	    });	  
	    
	    // Creamos la pseudo-funcion Contains
	    jQuery.expr[":"].Contains = jQuery.expr.createPseudo(function(arg) {
			return function( elem ) {
				return jQuery(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
			};
		});
  
};