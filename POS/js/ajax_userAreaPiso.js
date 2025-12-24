//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade//////////////////////
////////DESCRIPCION: Clase para validaci�n de combos//////////
//////////////////////////////////////////////////////////////
///////FECHA CREACION: 20-Enero-2014//////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////// 

var cod_piso=0;
var cod_area=0;

$(document).ready(function(){
	cargarPiso();
});

//Cargar combo de la cadena
  function cargarPiso()
  {
   var codRestaurante = $("#txtRest").val();
   send={"cargarPiso":1};
   send.codigo = codRestaurante;
    $.ajax({
	    async:false,type:"GET",dataType: "json",contentType: "application/x-www-form-urlencoded",url:"config_UserMesas.php",data:send,success:function(datos)
      	{
		   if(datos.str>0)
		   {			  
				$("#piso").html("");
				for(i=0; i<datos.str; i++) 
				{
					$("#piso").append("<button onclick='CargarArea("+datos[i]['pis_id']+")'>"+datos[i]['pis_numero']+"</button>");
				}
			}     
		}
	}); 
}

function CargarArea(codigopiso)
  {
   setPiso(codigopiso);
   send={"CargarArea":1}; 
   send.codigo=codigopiso;
   $.ajax
    ({
    async:false,type:"GET",dataType: "json",contentType: "application/x-www-form-urlencoded",url:"config_UserMesas.php",data:send,success:function(datos)
     {
      if(datos.str>0)
      {
       $("#area").html("");
       for(i=0; i<datos.str; i++) {
        $("#area").append("<input type='button' class='area_button' onclick='val("+datos[i]['arp_id']+",\""+datos[i]['arp_imagen']+"\")' value='"+datos[i]['arp_descripcion'].substr(0,1)+"' ></input>");
       }
      }
    
     }
    }); 
  }
  
 

/////////////////////////////CARGAR LA IMAGEN////////////////////////////////////////////

function val(area,imagen){
	setArea(area);
	$( ".mesa" ).empty();
	var codRestaurante = $("#txtRest").val();
	var codCadena = $("#txtCadena").val();
	var codPiso = $("#piso").val();
	var codArea = $("#area").val();
	var cadena = codCadena + "_" + codRestaurante + "_" + codPiso + "_" + codArea;
	var objeto= document.getElementById('mesas');
	objeto.style.backgroundImage='url(../imagenes/planos/'+imagen+')';
	objeto.style.backgroundRepeat = 'no-repeat';
	objeto.style.width = (screen.width - (screen.width*0.3))+'px';
	objeto.style.height =(screen.height - (screen.height*0.1))+'px';
	
	fn_cargaMesas(codRestaurante,getPiso(),getArea());
}
		
 /////////////////////////////CARGA LAS MESAS DE ACUERDO A LA BASE DE DATOS//////////////		
function fn_cargaMesas(codRestaurante, codPiso, codArea){
	
	send={"CargarMesa":1}; 
	send.rest=codRestaurante;
	send.piso=codPiso;
	send.area=codArea;
   $.ajax
    ({
    async:false,type:"GET",dataType: "json",contentType: "application/x-www-form-urlencoded",url:"config_UserMesas.php",data:send,success:function(datos)
     {
	  if(datos.str>0)
      {
		  $('#mesas').html("");
       for(i=0; i<datos.str; i++) {
		 	$('#mesas').append("<div class='mesa' align='center' id='"+datos[i]['mesa_id']+"' style='left:"+datos[i]['mesa_coordenadax']+"px; top:"+datos[i]['mesa_coordenaday']+"px; position:absolute;'><a onMouseOver=\"MM_swapImage('mesa"+datos[i]['mesa_id']+"','','../imagenes/mesa/"+datos[i]['std_descripcion']+"_E.png',1)\" onMouseOut='MM_swapImgRestore()' href='tomaPedido.php?numMesa="+datos[i]['mesa_id']+"'><img name='mesa"+ datos[i]['mesa_id'] +"' src='../imagenes/mesa/"+datos[i]['std_descripcion']+".png' border='0' ><br/><label class='tituloMesa'>"+datos[i]['mesa_descripcion']+"</label></a></div>");  
       }
      }
     }
    }); 
}

function setArea(area){
	cod_area=area;
}

function getArea(){
	return cod_area;
}

function setPiso(piso){
	cod_piso=piso;
}

function getPiso(){
	return cod_piso;
}

function fn_alertaPermisosPerfil(mns){
	alertify.alert(mns);
}

function fn_irReservas()
{
	window.location.href = "reservas/reservas.php";
}

function fn_irCorteCaja()
{
	window.location.href = "../corteCaja/corteCaja.php";
}

function fn_irFuncionesGerente()
{	
	window.location.href = "../funciones/funciones_gerente.php";
}