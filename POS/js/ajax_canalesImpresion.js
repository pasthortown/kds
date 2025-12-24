///////////////////////////////////////////////////////////
///////DESARROLLADO POR: Cristhian Castro ////////////////////
///////DESCRIPCION: ///////////////////
///////TABLAS INVOLUCRADAS: Menu_Agrupacion, ////////
////////////////Menu_Agrupacionproducto////////
////////////////Detalle_Orden_Pedido////////
///////////////////Plus, Precio_Plu, Mesas///////////////////
///////FECHA CREACION: 16-04-2014//////////////////////////
///////FECHA ULTIMA MODIFICACION: 07-05-2014////////////////
///////USUARIO QUE MODIFICO:  Cristhian Castro///////////////
///////DECRIPCION ULTIMO CAMBIO: Documentación y validación en Internet Explorer 9+///////
/////////////////////////////////////////////////////////// 

/*----------------------------------------------------------------------------------------------------
Carga inicial al momento de iniciar la solicitud de impresion para obtener la orden
-----------------------------------------------------------------------------------------------------*/
$(document).ready(function(){						   
	send={"obtenerOrden":1};
	send.odp_id=$("#hide_odp_id").val();
	send.cprn_id=$("#hide_cprn_id").val();
	$.ajax({
	async: false,
	url: "config_canalesImpresion.php",
	data: send,
	dataType: "json",
	success: function(datos) 	
	{
		if(datos.str>0)
		{	
			var myDate = new Date();
			var fecha = (myDate.getDate()) + '/' + (myDate.getMonth()+1) + '/' + myDate.getFullYear() + ' ' + myDate.getHours() + ":" + myDate.getMinutes();
			var orden= datos[0]['odp_id'];
			var separador='<div class="separador"></div>';
			html=""+separador+"<div class='listaproductosUsuario'>"+datos[0]['usr_descripcion']+"</div><div class='listaproductosFecha'>"+fecha+" - Orden #"+orden+"</div><div class='listaproductosMesa'>Mesa #"+datos[0]['mesa_id']+"</div>"+separador+"";
			$("#listadoCabecera").append(html);
					
			for(i=0;i<datos.str;i++)
			{											
				if (datos[i]['dop_anulacion']==1&&datos[i]['dop_impresion']==1)
				{
					html="<li id='"+datos[i]['plu_id']+"'><div class='listaproductosCant'>"+datos[i]['dop_cantidad']+"</div><div class='listaproductosDesc'>"+datos[i]['magp_desc_impresion']+"</div></li>";
					$("#listadoPedido").append(html);
					fn_actualizarImpresion(datos[i]['plu_id'],datos[i]['dop_id']);
				}
				if (datos[i]['dop_anulacion']==0&&datos[i]['dop_impresion']==0)
				{
					html="<li id='"+datos[i]['plu_id']+"'><div class='listaproductosCant'>"+datos[i]['dop_cantidad']+"</div><div class='listaproductosDesc'>#ANULADO# "+datos[i]['magp_desc_impresion']+"</div></li>";
					$("#listadoPedido").append(html);
					fn_actualizarImpresionAnulacion(datos[i]['plu_id'],datos[i]['dop_id']);
				}	
			}						
			$("#impresionOrden").append(separador);	
		}
		else
		{
			alertify.alert('No existen productos en la orden para esta impresora');
		}
	}
	});				
});
/*----------------------------------------------------------------------------------------------------
Función para actualizar los items que fueron impresos
-----------------------------------------------------------------------------------------------------*/
function fn_actualizarImpresion(plu_id,dop_id)
{
	send={"actualizarImpresion":1};
	send.odp_id=$("#hide_odp_id").val();
	send.dop_id=dop_id;
	send.plu_id=plu_id;
	$.ajax({
		async: false,
		url: "config_canalesImpresion.php",
		data: send,
		dataType: "json",
		success: function(datos){}
	});	
}
/*----------------------------------------------------------------------------------------------------
Función para actualizar los items anulados que fueron impresos
-----------------------------------------------------------------------------------------------------*/
function fn_actualizarImpresionAnulacion(plu_id,dop_id)
{
	send={"actualizarImpresionAnulacion":1};
	send.odp_id=$("#hide_odp_id").val();
	send.dop_id=dop_id;
	send.plu_id=plu_id;
	$.ajax({
		async: false,
		url: "config_canalesImpresion.php",
		data: send,
		dataType: "json",
		success: function(datos){}
	});
}