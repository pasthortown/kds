var opcionReporte='';
var visualizacion='';

$(document).ready(function()
{		
		$("#tbl_fecha").hide();
		$("#modal_reportes").hide();
		//$("#ok").attr("disabled",false);
		//$("#ok").css({ color: "#FFFFFF", background: "#70903B",'font-size':"2.0em"});
		
		//visualiza el nombre del reporte en la parte superior
		$("#btn_cash").click(function(){
          var cashout = $("#btn_cash").val();
		  $("#tituloreporte").text(cashout);         
    	}); 
		
		$("#btn_ventas").click(function(){
          var venta = $("#btn_ventas").val();
		  $("#tituloreporte").text(venta);          
    	}); 
		
		$("#btn_transacciones").click(function(){
          var resumentra = $("#btn_transacciones").val();
		  $("#tituloreporte").text(resumentra);          
    	}); 
		
		$("#btn_tax").click(function(){
          var impuestos = $("#btn_tax").val();
		  $("#tituloreporte").text(impuestos);          
    	}); 
		
		$("#btn_anulaciones").click(function(){
          var anulacion = $("#btn_anulaciones").val();
		  $("#tituloreporte").text(anulacion);          
    	}); 
		
});

function fn_validarReporte(opcion)
{
	$("#tbl_fecha").show();	
	opcionReporte = opcion.substr(4);	
	$("#txtfechaI,#txtfechaF").datepicker({
		changeMonth: true,
		changeYear: true,
		numberOfMonths: 1,
		showButtonPanel: true,
		showOtherMonths: true,
		selectOtherMonths: true,
		showWeek: true,
		firstDay: 1
		
	});
	//$("#ui-datepicker-div").css({"background": "black"});
	
}

//Funcion que dadas dos fechas, valida que la fecha final sea superior a la fecha inicial.
function fn_validar_fecha(fechaInicial,fechaFinal)
{
	valuesStart = fechaInicial.split("/");
	valuesEnd = fechaFinal.split("/");

	// Verificamos que la fecha no sea posterior a la actual
	var dateStart = new Date(valuesStart[2],(valuesStart[1]-1),valuesStart[0]);
	var dateEnd = new Date(valuesEnd[2],(valuesEnd[1]-1),valuesEnd[0]);
	if(dateStart > dateEnd)
	{
		return 0;
	}
	return 1;
}

function fn_tipoVisualizacion()
{
	var fechaInicial=$("#txtfechaI").val();
	var fechaFinal=$("#txtfechaF").val();
	if(fn_validar_fecha(fechaInicial,fechaFinal))
	{
		$( "#modal_reportes" ).dialog
			({
				width: 500,
				autoOpen: false,
				resizable: false,			
				modal: true,
				position: "center",
				closeOnEscape: false,
			});	    
						
			$( "#modal_reportes" ).dialog( "open" );	
	
	}else{
			alertify.alert('La Fecha Final no puede ser menor a la Fecha Inicial');
		 }
}

function fn_generarReporte(id){
	fechaIni = $("#txtfechaI").val();
	fechaFn = $("#txtfechaF").val();	
	visualizacion = id.substr(4);
	//REPORTE CASHOUT
	if(opcionReporte=='cash'){
		window.open('cashout/reporte.php?lc_opcion='+opcionReporte+'&inicio='+fechaIni+'&fin='+fechaFn+'&visualizar='+visualizacion,'','width=1200,height=800,menubar=on,scrollbars=yes,toolbar=no,status=yes,location=no,directories= no,resizable=yes,left= 0,top= 0');
		$( "#modal_reportes" ).dialog( "destroy" );
		//$("#tbl_fecha").hide();
	}else if(opcionReporte=='ventas'){
		window.open('plus/reporte.php?lc_opcion='+opcionReporte+'&inicio='+fechaIni+'&fin='+fechaFn+'&visualizar='+visualizacion,'','width=1200,height=800,menubar=on,scrollbars=yes,toolbar=no,status=yes,location=no,directories= no,resizable=yes,left= 0,top= 0');
		$( "#modal_reportes" ).dialog( "destroy" );
		//$("#tbl_fecha").hide();
	}
	
	/*
	if(visualizacion!='pdf'){
		
		window.open('reporte_normal.php?lc_opcion='+opcionReporte+'&inicio='+fechaIni+'&fin='+fechaFn+'&visualizar='+visualizacion,'','width=830,height=800,menubar=on,scrollbars=yes,toolbar=no,status=yes,location=no,directories= no,resizable=yes,left= 0,top= 0');
	$( "#modal_reportes" ).dialog( "destroy" );
	$("#tbl_fecha").hide();			
	
	}
	
	if(visualizacion=='pdf'){
		
		window.open("reporte_pdf.php?lc_opcion="+opcionReporte+'&inicio='+fechaIni+'&fin='+fechaFn,'','width=830,height=800,menubar=on,scrollbars=yes,toolbar=no,status=yes,location=no,directories= no,resizable=yes,left= 0,top= 0');
	$( "#modal_reportes" ).dialog( "destroy" );
	$("#tbl_fecha").hide();
	
	}
	*/
}

function fn_regresar()
{	
	history.back();
	return false;
}

function fn_cerrarModal()
{
	$( "#modal_reportes" ).dialog( "destroy" );
}

