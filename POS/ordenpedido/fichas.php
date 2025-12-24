<?php 

//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade//////////////////////
////////DESCRIPCION: Pantalla de acceso///////////////////////
///////TABLAS INVOLUCRADAS: ////////////////////////////////// 
///////FECHA CREACION: 7-Octubre-2013/////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////// 


//verificación de sesión iniciada
session_start();
if(!isset($_SESSION['validado'])){										// en caso de no existir sesión iniciada, se destruye toda información
	include_once'../seguridades/seguridad.inc';
	}
else {

// Inicio de página para presentación de fichas
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="icon" type="image/png" href="../imagenes/ico/<?php echo $_SESSION['cdnId']; ?>.ico"/>
    <title><?php echo strtoupper($_SESSION['cadenaNombre']); ?> - Mesas</title>
    <link type="text/css" href="../css/est_pantallas.css" rel="stylesheet">
    <script type="text/javascript">
    function nombre(boton){
	    document.getElementById("numMesa").value=boton.value;
    }		
	</script>
</head>

<body>
<table width="550px" align="center" border="0">
	<tr><td width="40%"><img src="../imagenes/cadena/<?php echo $_SESSION['logo']; ?>" width="120" height="60" /><td width="58%" class="titulo">Mesa</td></td></tr>
</table>
<table width="550px" align="center">
<tr class="trFichas">
<?php 
// despliegue de fichas, de acuerdo a las mesas ingresadas en el sistema, por restaurante
	$i=1;
	while($i<($_SESSION['numMesa']+1)){
		echo '<td class="tdFichas"><a href="tomaPedido.php?numMesa='.$i.'"><img src="../imagenes/fichas/'.$i.'.png" border="0" ></a></td>';
		
		if (($i==8) or ($i==16) or ($i==24) or ($i==32) or ($i==40) or ($i==48) or ($i==56) or ($i==64)) {
			echo '</tr><tr class="trFichas">';}
		$i++;
	}
?>
	</tr>
</table>
</body>
</html>
<?php } ?>