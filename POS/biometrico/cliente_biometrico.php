<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Clientes</title>
    
    
    <script type="text/javascript" src="../js/prototype1_7_1.js"></script>
    <script type="text/javascript" src="../js/jquery.min.js"></script>
    <script type="text/javascript" src="../js/d2Lector9.js"></script>
    <script type="text/javascript">

    function fn_Identificar()
    {	

        respuesta='';
	var cedula=$("#ClienteCI").val();/*'1720758364';//*/
        
        
        
	CapturaHuellaCr(cedula, 6, 2);	
        //$("#huella").val(Mensaje);
	if (Respuesta == 0) 
	{	
		send={"biometrika":1};	
		send.hid_bio = Mensaje;//$("#d2trama").val();//
		send.cedula_bio = cedula;
		$.ajax({async:false,type:"POST",dataType: "json",contentType: "application/x-www-form-urlencoded",url:"../biometrico/config_cliente_biometrico.php",data:send,
		success:function(datos)
		{
                        respuesta=datos[0]['respuesta'];	
			alert("La respuesta es "+datos[0]['respuesta']);
		}
		});		
                //alert('test111');
		return respuesta;
	}
	else 
	{
		alert("Error "+Mensaje);
	}
	return false;
       
    }
    </script>

</head>

<body>


    <input inputmode="none"  type="button" onclick="fn_Identificar()" value="Test Huella">
        <input inputmode="none"  type="text" id="ClienteCI"></input>
</body>
