<?php

//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: 	Darwin Mora  /////////////////////
////////DESCRIPCION: destruye las sesiones///////////////////
///////TABLAS INVOLUCRADAS: Perfil_Pos, Users_Pos ////////////
///////FECHA CREACION: 13-07-2015////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: /////////////////////// //////
//////////////////////////////////////////////////////////////
//Iniciar session
session_start(); 

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>

<script type="text/javascript">
function redireccionar(){
	window.location="../index.php";
} 
//setTimeout ("redireccionar()", 500); //tiempo expresado en milisegundos


</script>

</head>

<body>

<?php 

//Destruir session
if(session_destroy()){
?>	
	<script type="text/javascript">
		redireccionar();
	</script>
<?php
}

?>
</body>
</html>