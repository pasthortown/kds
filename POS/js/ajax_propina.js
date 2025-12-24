
function fn_abrirModalPropina()
{
	$("#hid_bandera_propina").val(1);
	$( "#aumentarContador" ).dialog({
		  title:'INGRESO DE PROPINA',				
		  modal:true,
		  autoOpen: false,
		  show: {
			effect: "blind",
			duration: 500
		  },
		  hide: {
			effect: "explode",
			duration: 500
		  },
		  width:"auto",
		   buttons: {
        	Cancelar: function() {
				//$("#cantidad").val('');
          		$( this ).dialog( "close" );
				//$("#keyboard").hide();
        	}
		  },
		  /*open: function(event, ui)
			{ 
				$(".ui-dialog-titlebar").hide();
			}*/
    });		
      $( "#aumentarContador" ).dialog( "open" );
}