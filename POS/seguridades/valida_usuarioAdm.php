<?php
//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: 	WORMAN ANDRADE C./////////////////
////////DESCRIPCION: VALIDA INGRESO DE INFORMACION ///////////
/////////////////////DE CREDENCIALES//////////////////////////
///////TABLAS INVOLUCRADAS: Perfil_Pos, Users_Pos ////////////
///////FECHA CREACION: 20-Dic-2013////////////////////////////
///////USUARIO QUE MODIFICO: Daniel Llerena //////////////////
///////FECHA ULTIMA MODIFICACION: 25/05/2015 /////////////////
///////DECRIPCION ULTIMO CAMBIO: Se agrega la variable //////
///////de sesion 'rstId' para la ADMINISTRACION DE ///////////
///////MENU en la pag, manteminiento. ////////////////////////
//////////////////////////////////////////////////////////////


require_once "../system/conexion/clase_sql.php";
include_once "../clases/clase_seguridades.php";
include_once "../clases/clase_direccion.php";

/////////////////////////RECUPERACION DE INFORMACION DE LOS CUADROS DE TEXTO//////////////////////////////////////////////////////////////
$txtClave = $_POST['txtClave'];
$txtUsuario = $_POST['txtUsuario'];

////////////////////////DIRECCIONAMINETO DE LAS PANTALLAS EN CASO DE SER CORRECTO O FALLIDO EL ACCESO/////////////////////////////////////
$failPage = "../mantenimiento/index.php";
$correcto = "../mantenimiento/inicio/home_seleccion.php";

////////////////////////INSTANCIACION DE VARIABLES//////////////////////////////////////////////////////////////////////////////////////////
$ip = new direccion();
$usuario = new seguridades();
////////////////////////INICIACION DE VARIABLES DE SESION//////////////////////////////////////////////////////////////////////////////////
if ($usuario->fn_getUsrAdmin($txtUsuario, $txtClave, 'Usuario_Id')) {
    //session_start(); 
    $_SESSION['validado'] = TRUE;
    $_SESSION['usuarioId'] = $usuario->fn_getUsrAdmin($txtUsuario, $txtClave, 'Usuario_Id');
    $_SESSION['usuario'] = $usuario->fn_getUsrAdmin($txtUsuario, $txtClave, 'Usuario');
    $_SESSION['nombre'] = $usuario->fn_getUsrAdmin($txtUsuario, $txtClave, 'Usuario_Nombre');
    $_SESSION['perfil'] = $usuario->fn_getUsrAdmin($txtUsuario, $txtClave, 'Perfil_Id');
    $_SESSION['rstId'] = $usuario->fn_getUsrAdmin($txtUsuario, $txtClave, 'Resturante_Id');
    $_SESSION['direccionIp'] = $ip->fn_getIp();
    $_SESSION['numPiso'] = $usuario->fn_getCdn($_SESSION['rstId'], 'Resturante_NumPiso');
    $_SESSION['numMesa'] = $usuario->fn_getCdn($_SESSION['rstId'], 'Resturante_NumMesa');
    $_SESSION['cadenaId'] = $usuario->fn_getCdn($_SESSION['rstId'], 'Cadena_Id');
    $_SESSION['cadenaNombre'] = $usuario->fn_getCdn($_SESSION['rstId'], 'Cadena_Nombre');
    $_SESSION['logo'] = $usuario->fn_getCdn($_SESSION['rstId'], 'Logotipo');
    $_SESSION['rstNombre'] = $usuario->fn_nombrelocal($_SESSION['rstId']);
	$_SESSION['userPassword'] = $txtClave;
    header("Location: " . $correcto);
} else {
    ?>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="../css/style_home_seleccion.css">
        <link rel="stylesheet" type="text/css" href="../css/alertify.core.css"/>
        <link rel="stylesheet" type="text/css" href="../css/alertify.default.css"/>
        <script src="../js/jquery1.11.1.js"></script>
        <script src="../js/jquery-ui.js"></script>
        <script language="javascript1.1"  src="../js/alertify.js"></script>

    </head>
    <body class="fondobarra">
        <div>
            <div>
                <p class="nombremax" style="text-align:center">Max<span class="nombrepoint">Point</span></p>
            </div>
        </div>
        <br>
        <script type="text/javascript">
            alertify.alert("Sus credenciales son incorrectas, vuelva a intentarlo.");
            document.location.href = "<?php echo $failPage ?>";
        </script> 
        </body>
        </html>
<?php
}
?>
