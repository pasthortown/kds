<?php
/////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade/////////////////////////
////////DESCRIPCION: valida el acceso con variables de sesi�n////
////////FECHA CREACION: 12-12-2013///////////////////////////////
/////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////// 

if(!isset($_SESSION['validado'])) {
	$failPage = "../index.php";
	session_destroy();
	$parametros_cookies = session_get_cookie_params(); 
	setcookie(session_name(),0,1,$parametros_cookies["path"]);
	?>
	<script type="text/javascript">
		alert("Debe ingresar al sistema de manera correcta, por favor digite sus credenciales");
		document.location.href="<?php echo $failPage?>";
	</script> <?php
		die;
}