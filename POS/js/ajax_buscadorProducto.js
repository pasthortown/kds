

function fn_modalBuscador(){
		$("#txt_busca").val('');
		$( "#cuadro_buscador" ).dialog({
	      modal: true,
		  autoOpen: false,
		  position: { my: 'top', at: 'top+100' },
		  show: {
			effect: "blind",
			duration: 500
		  },
		  hide: {
			effect: "explode",
			duration: 500
		  },
		  width:600,
		  height: 500,
		  buttons: {
        	Cancelar: function() {
				$("#txt_busca").val('');
          		$( this ).dialog( "close" );
				$("#keyboard").hide();
        	}
		  },
		  open: function(event, ui)
			{ 
				$(".ui-dialog-titlebar").hide();
				fn_alfaNumerico('#txt_busca');
			}
    });	
	
      $( "#cuadro_buscador" ).dialog( "open");
	  	$("#txt_busca").val('');

	document.getElementById("hide_pluId").value="";
	fn_focusLector();
}

function fn_listarPro()
{	$("#txt_busca").val();
	if($("#btn_salonLlevar").val()=='Salon')
	{
		lc_control=1;
	}
	else
	{
		lc_control=2;
	}
	//$("#barraProducto").show();
	$("#agregarCantidad").show();
	//$("#barraProducto").empty();	
	send={"cargarProductoBuscador":1};
	send.mag_idBusca=$("#codigoCategoria").val();
	send.controlaBusca=lc_control;
	send.descBusca=$("#txt_busca").val();
	$.getJSON("config_ordenPedido.php",send,function(datos) 	
	{
		if(datos.str>0)
		{	
			$("#buscaProducto").empty();
			for(i=0;i<datos.str;i++)
			{
				var posicionTotal=""+datos[i]['magp_orden']+"";
				var separar=posicionTotal.split(",");
				//left=separar[0];
				//arriba=separar[1];
				
				html="<button id='btn_p"+datos[i]['magp_id']+"' onclick='fn_verificarElemntoBusqueda("+datos[i]['magp_id']+","+datos[i]['plu_id']+","+datos[i]['plu_gramo']+")' style='width: 130px; font-size: 12px; background-color:"+datos[i]['magp_color']+"; color:"+datos[i]['magp_colortexto']+";'>"+datos[i]['magp_desc_impresion']+"</button>";
				$("#buscaProducto").append(html);
			}
		}
		else
		{
			$("#buscaProducto").empty();
		}
	}); 
	fn_focusLector();
}


function fn_verificarElemntoBusqueda(magp_id, plu_id, plu_gramos){
	var cantidad = $("#cantidadOK").val();
	
	$("#cuadro_buscador").dialog("destroy");
	$("#cuadro_buscador").hide();
	$("#buscaProducto").empty();	
	$("#keyboard").hide();
	$("#keyboard").empty();	
	
	if(plu_gramos==0 && cantidad.indexOf('.') > 0){
		alertify.alert("No se puede aplicar cantidad en gramos para este producto.")
		return false;
	}
	
	if($("#btn_salonLlevar").val()=='Salon'){
		lc_control=1;
	}else{
		lc_control=2;
	}
	$('#btn_p'+magp_id+'').prop("disabled", true);
	$('#magpAgregar').val('');
	$('#magpAgregar').val(magp_id);
	fn_verificarPreguntaSugerida(plu_id);
	$("#listado ul").empty();
	var odp_id = document.getElementById("hide_odp_id").value;
	var dop_id = document.getElementById("hide_dop_id").value;
	send={"verificarElemento":1};
	send.magp_id = magp_id;
	send.plu_id = plu_id;
	send.odp_id = odp_id;
	send.dop_id = dop_id;
	send.lc_cantidad = lc_cantidad;
	send.mesa_id = $("#hide_mesa_id").val();
	send.control = lc_control;
	$.getJSON("config_ordenPedido.php",send,function(datos){
		if(datos.str>0){
			$("#listado ul").empty();
			var subTotalFinal=0;
			var basedoceFinal=0;
			var baseceroFinal=0;
			var IvaFinal=0;
			var TotalFinal=0;
			for(i=0;i<datos.str;i++){
				if($("li#"+datos[i]['plu_id']+"").length == 0) {
					html="<li id='"+datos[i]['plu_id']+"' onclick='fn_modificarLista("+datos[i]['plu_id']+")'>"+
						"<div class='listaproductosDescTomaPedido'>"+datos[i]['magp_desc_impresion']+"</div>"+
						"<div class='listaproductosValTomaPedido'>$"+(datos[i]['dop_precio_unitario']*datos[i]['dop_cantidad']).toFixed(2)+"</div>"+
						"<div class='listaproductosCantTomaPedido'>"+datos[i]['dop_cantidad']+"</div>"+
						"</li>";
				  $("#listadoPedido").append(html);
				}else{
					var pluCantidadAnt = $("li#"+datos[i]['plu_id']+" .listaproductosCantTomaPedido").text();
					var pluCantidadNew = pluCantidadAnt + datos[i]['dop_cantidad'];
					var pluPrecio = datos[i]['dop_precio_unitario']*pluCantidadNew;
					$("li#"+datos[i]['plu_id']+" .listaproductosCantTomaPedido").empty();
					$("li#"+datos[i]['plu_id']+" .listaproductosValTomaPedido").empty();
					$("li#"+datos[i]['plu_id']+" .listaproductosCantTomaPedido").append(pluCantidadNew);
					$("li#"+datos[i]['plu_id']+" .listaproductosValTomaPedido").append(pluPrecio.toFixed(2));
				}
				
				var subTotal=(datos[i]['dop_precio_unitario']*datos[i]['dop_cantidad']);
				subTotalFinal=subTotalFinal + subTotal;
				if(datos[i]['plu_impuesto']==0){
					var basecero=(datos[i]['dop_precio_unitario']*datos[i]['dop_cantidad']);
					baseceroFinal=baseceroFinal + basecero;	
				}else if(datos[i]['plu_impuesto']==1){
					var basedoce=(datos[i]['dop_precio_unitario']*datos[i]['dop_cantidad']);
					basedoceFinal=basedoceFinal + basedoce;
				}
				var Iva=(datos[i]['dop_cantidad']*datos[i]['dop_iva']);
				IvaFinal=IvaFinal + Iva;
				var Total=(datos[i]['dop_cantidad']*datos[i]['dop_total']);
				TotalFinal=TotalFinal + Total;
			}
			$(".subTotal").empty();
			$(".Iva").empty();
			$(".Total").empty();
			$(".baseCero").empty();
			$(".baseDoce").empty();
			$(".baseCero").append(baseceroFinal.toFixed(2));
			$(".baseDoce").append(basedoceFinal.toFixed(2));			
			$(".subTotal").append(subTotalFinal.toFixed(2));
			$(".Iva").append(IvaFinal.toFixed(2));
			$(".Total").append(TotalFinal.toFixed(2));			
			$("#cantidad").val(0);	
			$("#cantidadOK").val(1);
			lc_cantidad = 1;
			fn_kds();
		}
	});
	$('#btn_p'+magp_id+'').prop("disabled", false);
	fn_focusLector();
	Accion = 0;
	lc_cantidad = 1;
	
}