<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Documento sin título</title>

    <script type="text/javascript" src="../js/jquery183.js"></script>    
    <script type="text/javascript" src="../js/jquery-ui.js"></script>
    <script type="text/javascript" src="../js/ajax.js"></script>
    <script type="text/javascript" src="../js/alertify.js"></script> 
	<script type="text/javascript" src="../js/ajax_xml.js"> </script>
    
    <link rel="stylesheet" type="text/css" href="../css/est_botones.css" />
    <link rel="stylesheet" type="text/css" href="../css/est_modal.css" />
	<link rel="stylesheet" type="text/css" href="../css/est_pantallas.css" />
    <link rel="stylesheet" type="text/css" href="../css/alertify.core.css" />
	<link rel="stylesheet" type="text/css" href="../css/alertify.default.css" />
</head>

<body>
<form action="anexo_xml.php" method="post" enctype="multipart/form-data">
    <label class="titulo"> factura Id: </label><input inputmode="none"  type="text" id="txtNumFactura" name="txtNumFactura"/>
    <br/>    <br/>
    <input inputmode="none"  type="submit" id="enviarFct" name="enviarFct" value="Enviar"  />
    <input inputmode="none"  type="reset" id="cancelar" name="cancelar" value="Cancelar"  />
</form>
<br/>
<br/>
<br/>
<br/>

</body>
</html>