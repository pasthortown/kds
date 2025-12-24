//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade//////////////////////
////////DESCRIPCION: Clase para Reservas de mesas/////////////
//////////////////////////////////////////////////////////////
///////FECHA CREACION: 28-Enero-2014//////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////// 

$(document).ready(function(){
			cargarPiso();
			muestraFecha();
			$("#piso").change(function(){CargarArea();});
			$("#txtfechaI").datepicker({
				defaultDate: "",
				changeMonth: true,
				changeYear: true,
				numberOfMonths: 1,
				showButtonPanel: true,
				showOtherMonths: true,
				selectOtherMonths: true,
				showWeek: true,
				firstDay: 1
			});
			$("#txtHoraI,#txtHoraF").timepicker();
			fn_consultarCliente();
		});


 /////////////////////////////CARGAR PISO//////////////////////////////////////////////////
  function cargarPiso()
  {
   var codRestaurante = $("#txtRest").val();
   send={"cargarPiso":1};
   send.codigo = codRestaurante;
    $.ajax
     ({
     async:false,type:"GET",dataType: "json",contentType: "application/x-www-form-urlencoded",url:"config_reservas.php",data:send,success:function(datos)
      {
       if(datos.str>0)
       {
        //$('#piso').html("<option selected value='"+datos[0]['pis_id']+"'>"+datos[0]['pis_numero']+"</option>");
	   
        
		for(i=0; i<datos.str; i++) {
			if(i==0)
			{
         		$("#piso").append("<option selected  value="+datos[i]['pis_id']+">"+datos[i]['pis_numero']+"</option>");
				CargarArea();
	   			val();
			}
			else
				$("#piso").append("<option value="+datos[i]['pis_id']+">"+datos[i]['pis_numero']+"</option>");
        }
       }
     
      }
     });  
}

 /////////////////////////////CARGAR AREA////////////////////////////////////////////////////////////

function CargarArea()
  {
   var codigopiso = $("#piso").val();
   
   send={"CargarArea":1}; 
   send.codigo=codigopiso;
   $.ajax
    ({
    async:false,type:"GET",dataType: "json",contentType: "application/x-www-form-urlencoded",url:"config_reservas.php",data:send,success:function(datos)
     {
      if(datos.str>0)
      {
       $('#area').html("<option selected value="+datos[0]['arp_id']+">"+datos[0]['arp_descripcion']+"</option>");
       for(i=1; i<datos.str; i++) {
        $("#area").append("<option value="+datos[i]['arp_id']+">"+datos[i]['arp_descripcion']+"</option>");
       }
      }
    
     }
    }); 
  
  }
 
  /////////////////////////////CARGAR LA IMAGEN////////////////////////////////////////////

function validarCampos() {
			if((document.getElementById('piso').value.length==0) || (document.getElementById('area').value.length==0)  || (document.getElementById('txtfechaI').value.length==0)
																 || (document.getElementById('txtHoraI').value.length==0) || (document.getElementById('txtHoraF').value.length==0)
																 || (document.getElementById('txtClienteB').value.length==0) || (document.getElementById('txtMesa').value.length==0))
                {alert("Estimado usuario, debe seleccionar los valores en cada casillero");
				return 0;
			}
			else
				{document.frmReserva.submit();}
        }
 

 /////////////////////////////CARGAR LA IMAGEN////////////////////////////////////////////

function val() {
			$( ".mesa" ).empty();
			var codRestaurante = $("#txtRest").val();
			var codCadena = $("#txtCadena").val();
			var codPiso = $("#piso").val();
			var codArea = $("#area").val();
			var cadena = codCadena + "_" + codRestaurante + "_" + codPiso + "_" + codArea;
			var objeto= document.getElementById('imagen');
			objeto.style.backgroundImage='url(../../imagenes/planos/'+cadena+'.jpg)';
			objeto.style.backgroundRepeat = 'no-repeat';
			objeto.style.width = (screen.width - (screen.width*0.3))+'px';
			objeto.style.height =(screen.height - (screen.height*0.1))+'px';
			
			fn_cargaMesas(codRestaurante, codPiso, codArea);
        }
		
 /////////////////////////////CARGA LAS MESAS DE ACUERDO A LA BASE DE DATOS//////////////		
function fn_cargaMesas(codRestaurante, codPiso, codArea){
	
	send={"CargarMesa":1}; 
	send.rest=codRestaurante;
	send.piso=codPiso;
	send.area=codArea;

   $.ajax
    ({
    async:false,type:"GET",dataType: "json",contentType: "application/x-www-form-urlencoded",url:"config_reservas.php",data:send,success:function(datos)
     {
	  if(datos.str>0)
      {
       for(i=0; i<datos.str; i++) {
		   	$('body').append("<div class='mesa' align='center' onclick='fnMesaId("+i+","+datos[i]['mesa_id']+")' id='"+datos[i]['mesa_id']+"' style='left:"+datos[i]['mesa_coordenadax']+"px; top:"+datos[i]['mesa_coordenaday']+"px; position:absolute;'><a href='#'><img src='../../imagenes/mesa/"+datos[i]['std_descripcion']+".png' border='0' ><br/><label class='tituloMesa'>"+datos[i]['mesa_descripcion']+"</label></a></div>");
       }
      }
     }
    }); 

}

 /////////////////////////////VALIDACION DE NUMEROS/////////////////////////////////	
function validarNumeros(e)  // 1
{ 
    tecla = (document.all) ? e.keyCode : e.which; // 2
    if (tecla==8) return true; // backspace
    if (tecla==109) return true; // menos
    if (tecla==110) return true; // punto
    if (tecla==189) return true; // guion
    if (e.ctrlKey && tecla==86) { return true}; //Ctrl v
    if (e.ctrlKey && tecla==67) { return true}; //Ctrl c
    if (e.ctrlKey && tecla==88) { return true}; //Ctrl x
    if (tecla>=96 && tecla<=105) { return true;} //numpad
 
    patron = /[0-9]/; // patron
 
    te = String.fromCharCode(tecla); 
    return patron.test(te); // prueba
}
  
 /////////////////////////////VALIDACION DE LETRAS/////////////////////////////////
function soloLetras(e)
{
       key = e.keyCode || e.which;
       tecla = String.fromCharCode(key).toLowerCase();
       letras = " áéíóúabcdefghijklmnñopqrstuvwxyz";
       especiales = [8,37,39,46];

       tecla_especial = false
       for(var i in especiales)
	   {
            if(key == especiales[i])
			{
                tecla_especial = true;
                break;
            }
        }

        if(letras.indexOf(tecla)==-1 && !tecla_especial)
		{ return false; }
}

 /////////////////////////////MUESTRA FECHA ACTUAL /////////////////////////////////
function muestraFecha() {
	var today = new Date();
	var anio = today.getYear()+1900;
	var mes = today.getMonth()+1;
	
	var fechaHoy = mes+'/'+today.getDate()+'/'+anio;
	 
	$("#txtfechaI").val(fechaHoy);
}


 /////////////////////////////MUESTRA FECHA ACTUAL /////////////////////////////////
function fnMesaId(mesa, idMesa) {
	var mesaDescripcion = "mesa"+(mesa+1);
	$("#txtMesa").val(mesaDescripcion);
	$("#txtMesaId").val(idMesa);
}


/*---------------------------------------------
Carga los productos de una Cadena selecvcionada
-----------------------------------------------*/

function aMays(e, elemento) 
{
	tecla=(document.all) ? e.keyCode : e.which; 
	elemento.value = elemento.value.toUpperCase();
}

/*------------------------
Funcion Buscar 
-------------------------*/
function fn_consultarCliente()
{
	//cadena= $("#codigoCadena").val();
	$("#txtClienteB").autocomplete({
		source: "config_reservas.php?autoCompletar",
		minLength: 2,

	select: function( event, ui ) {
			$("#txtClienteB").focus();
			$(".ui-helper-hidden-accessible").hide();
		},
		create : function(event, ui){
		}
	});
}
