//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade//////////////////////
////////DESCRIPCION: Clase para validación de combos//////////
//////////////////////////////////////////////////////////////
///////FECHA CREACION: 20-Enero-2014//////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////// 
$(document).ready(function(){
			cargarPiso();
			$("#piso").change(function(){CargarArea();});
			
		});

//Cargar combo de la cadena
  function cargarPiso()
  {
   var codRestaurante = $("#txtRest").val();
   send={"cargarPiso":1};
   send.codigo = codRestaurante;
    $.ajax
     ({
     async:false,type:"GET",dataType: "json",contentType: "application/x-www-form-urlencoded",url:"config_UserMesas.php",data:send,success:function(datos)
      {
		  
       if(datos.str>0)
       {
		  
        $("#piso").html("");
        $('#piso').html("<option selected value='0'>--Seleccione--</option>");
        for(i=0; i<datos.str; i++) {
         $("#piso").append("<option value="+datos[i]['pis_id']+">"+datos[i]['pis_numero']+"</option>");
        }
       }
     
      }
     }); 
}

function CargarArea()
  {
   var codigopiso = $("#piso").val();
   
   send={"CargarArea":1}; 
   send.codigo=codigopiso;
   $.ajax
    ({
    async:false,type:"GET",dataType: "json",contentType: "application/x-www-form-urlencoded",url:"config_UserMesas.php",data:send,success:function(datos)
     {
      if(datos.str>0)
      {
       $("#area").html("");
       $('#area').html("<option selected value='0'>[--Seleccione--]</option>");
       for(i=0; i<datos.str; i++) {
        $("#area").append("<option value="+datos[i]['arp_id']+">"+datos[i]['arp_descripcion']+"</option>");
       }
      }
    
     }
    }); 
  
  }
  
 

 /////////////////////////////CARGA LOS DATOS DE LA CONSULTA//////////////

function val() {
			
			var codRestaurante = $("#txtRest").val();
			var codCadena = $("#txtCadena").val();
			var codPiso = $("#piso").val();
			var codArea = $("#area").val();
			var cadena = codCadena + "_" + codRestaurante + "_" + codPiso + "_" + codArea;

			var objeto= document.getElementById('imagen');
			objeto.style.backgroundImage='url(../imagenes/planos/'+cadena+'.jpg)';
			
			fn_cargaMesas(codRestaurante, codPiso, codArea);
        }
		
		
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
       for(i=0; i<datos.str; i++) {
		 	$('body').append("<div align='center' id='"+datos[i]['mesa_descripcion']+"' style='left:"+datos[i]['mesa_coordenadax']+"px; top:"+datos[i]['mesa_coordenaday']+"px; position:absolute;'><img src='../imagenes/mesa/"+datos[i]['std_descripcion']+".png' border='0' ><br/><label class='tituloMesa'>"+datos[i]['mesa_descripcion']+"</label></div>");  
		   
       }
      }
    
     }
    }); 

}

