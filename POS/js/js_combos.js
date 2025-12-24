//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade//////////////////////
////////DESCRIPCION: Clase para validación de combos//////////
//////////////////////////////////////////////////////////////
///////FECHA CREACION: 16-Enero-2014//////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////// 
$(document).ready(function(){
			cargarCadena();
			$("#cadena").change(function(){dependenciaRestaurante(); $("#piso").attr("disabled",true);});
			$("#restaurante").change(function(){dependenciaPiso();});
			$("#piso").change(function(){dependenciaArea();});
			$("#restaurante").attr("disabled",true);
			$("#piso").attr("disabled",true);
			$("#area").attr("disabled",true);
		});

/////////////////////////////VALIDA ENVIO DE SUBMIT//////////////////////

	function valida_envia(){
			if (document.frmMesas.area.value=="0")
			{
				alert("Estimado usuario, debe seleccionar en todos los combos la informacion");
				return 0;
			} else
			{	document.frmMesas.submit(); }
		};


/////////////////////////////CARGA LOS DATOS DE LA CONSULTA//////////////

function val() {
			var cdn = document.getElementById("cadena").value;
			var rst = document.getElementById("restaurante").value;
			var pis = document.getElementById("piso").value;
			var are = document.getElementById("area").value;
			var cadena = cdn + "_" + rst + "_" + pis + "_" + are;

			var objeto= document.getElementById('imagen');
			objeto.style.backgroundImage='url(../../imagenes/planos/'+cadena+'.jpg)';
        }

/////////////////////////////CARGA LOS DATOS DE LA CONSULTA//////////////

		function cargarCadena()
		{
			$.get("cargarCadena.php", function(resultado){
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$('#cadena').append(resultado);
				}
			});	
		}
		
/////////////////////////////CARGA LOS DATOS DE LA CONSULTA//////////////////////
/////////////////////////////Y VALIDA SI EXISTIO UNA CADENA SELECIONADA//////////
		function dependenciaRestaurante()
		{
			var code = $("#cadena").val();
			$.get("dependenciaRestaurante.php?",{ code: code }, function(resultado)	{
					if(resultado == false)
					{
						alert("Error");
					}
					else
					{
						$("#restaurante").attr("disabled",false);
						document.getElementById("restaurante").options.length=1;
						$('#restaurante').append(resultado);			
					}
				}

			);
		}

		
/////////////////////////////CARGA LOS DATOS DE LA CONSULTA//////////////////////
/////////////////////////////Y VALIDA SI EXISTIO UN RESTAURANTE SELECIONADO///////

		function dependenciaPiso()
		{
			var code = $("#restaurante").val();
			$.get("dependenciaPiso.php?", { code: code }, function(resultado){
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#piso").attr("disabled",false);
					document.getElementById("piso").options.length=1;
					$('#piso').append(resultado);			
				}
			});	
			
		}

		
/////////////////////////////CARGA LOS DATOS DE LA CONSULTA//////////////////////
/////////////////////////////Y VALIDA SI EXISTIO UN PISO SELECIONADO/////////////

		function dependenciaArea()
		{
			var code = $("#restaurante").val();
			$.get("dependenciaArea.php?", { code: code }, function(resultado){
				if(resultado == false)
				{
					alert("Error");
				}
				else
				{
					$("#area").attr("disabled",false);
					document.getElementById("area").options.length=1;
					$('#area').append(resultado);			
				}
			});	
		
		}
		
			