<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////////////////////////
///////////DESCRIPCION: ADMINISTRACION DE USUARIOS POR TIENDA, CREACION DE PERFILES CAJEROS ////////////
////////////////TABLAS: Users_Pos, Perfil_Pos //////////////////////////////////////////////////////////
////////FECHA CREACION: 27/07/2015//////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////

include_once"../../system/conexion/clase_sql.php"; 
include_once "../../clases/clase_adminusuariostienda.php";

if (
    empty($_SESSION['rstId'])
    OR empty($_SESSION['usuarioId'])
    OR empty($_SESSION['cadenaId'])
) {
    die(json_encode((object)[
        "estado" => "ERROR",
        "mensaje" => "Faltan variables de sesión, por favor loguearse nuevamente"
    ]));
}

$lc_config      = new configuracionUsuariosTienda();	
$lc_cadena      = $_SESSION['cadenaId'];
$lc_usuario     = $_SESSION['usuarioId'];
$lc_restaurante = $_SESSION['rstId'];

if (htmlspecialchars(isset($_POST["administracionUsuariosTienda"]))) {
    $lc_condiciones[0] = utf8_decode($_POST["accion"]);
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = $lc_usuario;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = 0;    
    print $lc_config->fn_administracionUsuariosTienda($lc_condiciones);
} else if (htmlspecialchars(isset($_GET["cargarLocales"]))) {
    $lc_condiciones[0] = htmlspecialchars(trim($_GET["accion"]));
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = $lc_usuario;
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET["std_id"]));
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = 0;    
    print $lc_config->fn_cargarLocales($lc_condiciones);
} else if (htmlspecialchars(isset($_GET["cargarPerfiles"]))) {
    $lc_condiciones[0] = htmlspecialchars(trim($_GET["accion"]));
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET["std_id"]));
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = 0;    
    print $lc_config->fn_cargarPerfiles($lc_condiciones);
} else if (htmlspecialchars(isset($_GET["verificarUsuarioSistema"]))) {
    $lc_condiciones[0] = htmlspecialchars(trim($_GET["accion"]));
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = htmlspecialchars(utf8_decode($_GET["usuario"]));
    $lc_condiciones[5] = 0;    
    print $lc_config->fn_verificarUsuarioSistema($lc_condiciones);
} else if (htmlspecialchars(isset($_GET["validarPais"]))) {
    $lc_condiciones[0] = htmlspecialchars(trim($_GET["accion"]));
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = $lc_usuario;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = 0;    
    print $lc_config->fn_validarPais($lc_condiciones);
} else if (htmlspecialchars(isset($_GET["guardarUsuario"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_GET['accion']));
    $lc_condiciones[1] = 0; 
    $lc_condiciones[2] = $lc_cadena;
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET['prf_id']));
    $lc_condiciones[4] = htmlspecialchars(utf8_decode($_GET['std_id']));
    $lc_condiciones[5] = htmlspecialchars(utf8_decode($_GET['usr_nombre_en_pos']));
    $lc_condiciones[6] = htmlspecialchars(utf8_decode($_GET['usr_usuario']));
    $lc_condiciones[7] = utf8_decode(trim($_GET['usr_iniciales']));
    $lc_condiciones[8] = utf8_decode(trim($_GET['usr_descripcion']));
    $lc_condiciones[9] = utf8_decode(trim($_GET['usr_tarjeta']));
    $lc_condiciones[10] = htmlspecialchars(utf8_decode($_GET['usr_fecha_ingreso']));
    $lc_condiciones[11] = htmlspecialchars(utf8_decode($_GET['usr_fecha_salida']));
    $lc_condiciones[12] = utf8_decode(trim($_GET['usr_telefono']));
    $lc_condiciones[13] = utf8_decode(trim($_GET['usr_email']));
    $lc_condiciones[14] = utf8_decode(trim($_GET['usr_direccion']));
    $lc_condiciones[15] = $lc_usuario;
    $lc_condiciones[16] = 0;
    $lc_condiciones[17] = utf8_decode(trim($_GET['usr_cedula']));

    if (htmlspecialchars(isset($_GET['rst_id']))) {
        foreach($_GET['rst_id'] as $lc_restaurantes){
            $lc_condiciones[18] = $lc_restaurantes;
        }
    } else {
        $lc_condiciones[18] = 0;	
    }
    $lc_condiciones[19] = utf8_decode(trim($_GET['usr_clave']));
        print $lc_config->fn_guardarUsuario($lc_condiciones);
} else if (htmlspecialchars(isset($_GET["guardaUserRestaurante"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_GET['accion']));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET['usr_id'])); 
    $lc_condiciones[2] = $lc_cadena; 
    $lc_condiciones[3] = 0; 
    $lc_condiciones[4] = 0; 
    $lc_condiciones[5] = 0; 
    $lc_condiciones[6] = 0; 
    $lc_condiciones[7] = 0; 
    $lc_condiciones[8] = 0; 
    $lc_condiciones[9] = 0; 
    $lc_condiciones[10] = 0; 
    $lc_condiciones[11] = 0; 
    $lc_condiciones[12] = 0; 
    $lc_condiciones[13] = 0;
    $lc_condiciones[14] = 0;
    $lc_condiciones[15] = $lc_usuario;
    $lc_condiciones[16] = 0;
    $lc_condiciones[17] = 0;
    $lc_condiciones[18] = (isset($_GET['rst_id']) && !empty($_GET['rst_id']))? htmlspecialchars(utf8_decode($_GET['rst_id'])) : $lc_restaurante;
    $lc_condiciones[19] = 0;    
    print $lc_config->fn_guardaUserRestaurante($lc_condiciones);
} else if (htmlspecialchars(isset($_GET["traerDatosUsuario"]))) {
    $lc_condiciones[0] = htmlspecialchars(trim($_GET["accion"]));
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = $lc_usuario;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = htmlspecialchars(utf8_decode($_GET["usr_id"]));
    print $lc_config->fn_traerDatosUsuario($lc_condiciones);
} else if (htmlspecialchars(isset($_GET["traerRestauranteUser"]))) {
    $lc_condiciones[0] = htmlspecialchars(trim($_GET["accion"]));
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = $lc_usuario;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = htmlspecialchars(utf8_decode($_GET["usr_id"]));
    print $lc_config->fn_traerRestauranteUser($lc_condiciones);
} else if (htmlspecialchars(isset($_GET["guardarUsuarioMod"]))) {
    $lc_condiciones[0] = htmlspecialchars(utf8_decode($_GET['accion']));
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET['usr_id'])); 
    $lc_condiciones[2] = $lc_cadena;
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET['prf_id']));
    $lc_condiciones[4] = htmlspecialchars(utf8_decode($_GET['std_id']));
    $lc_condiciones[5] = utf8_decode(trim($_GET['usr_nombre_en_pos']));
    $lc_condiciones[6] = utf8_decode(trim($_GET['usr_usuario']));
    $lc_condiciones[7] = utf8_decode(trim($_GET['usr_iniciales']));
    $lc_condiciones[8] = utf8_decode(trim($_GET['usr_descripcion']));
    $lc_condiciones[9] = utf8_decode(trim($_GET['usr_tarjeta']));
    $lc_condiciones[10] = utf8_decode(trim($_GET['usr_fecha_ingreso']));
    $lc_condiciones[11] = utf8_decode(trim($_GET['usr_fecha_salida']));
    $lc_condiciones[12] = utf8_decode(trim($_GET['usr_telefono']));
    $lc_condiciones[13] = utf8_decode(trim($_GET['usr_email']));
    $lc_condiciones[14] = utf8_decode(trim($_GET['usr_direccion']));
    $lc_condiciones[15] = $lc_usuario;
    $lc_condiciones[16] = 0;
    $lc_condiciones[17] = utf8_decode(trim($_GET['usr_cedula']));
    $lc_condiciones[18] = htmlspecialchars(utf8_decode($_GET['usr_rst']));
    $lc_condiciones[19] = utf8_decode(trim($_GET['usr_clave']));
    print $lc_config->fn_guardarUsuario($lc_condiciones);
} else if (htmlspecialchars(isset($_GET["restablecerClaveUsuario"]))) {
    $lc_condiciones[0] = $_GET["accion"];
    $lc_condiciones[1] = htmlspecialchars(utf8_decode($_GET["usr_id"]));
    $lc_condiciones[2] = '';
    //$lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET["usr_clave"]));
    $lc_condiciones[3] = $lc_usuario;    
    print $lc_config->fn_restablecerClaveUsuario($lc_condiciones);
} else if (htmlspecialchars(isset($_GET["ValidaDocumento"]))) {
    $lc_condiciones[0] = htmlspecialchars(trim($_GET["accion"]));
    $lc_condiciones[1] = $lc_restaurante;
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET["documento"]));
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET["usuario"]));    
    print $lc_config->fn_ValidaDocumento($lc_condiciones);
} else if (htmlspecialchars(isset($_GET["ValidaUsuario"]))) {
    $lc_condiciones[0] = htmlspecialchars(trim($_GET["accion"]));
    $lc_condiciones[1] = $lc_restaurante;
    $lc_condiciones[2] = htmlspecialchars(utf8_decode($_GET["documento"]));
    $lc_condiciones[3] = htmlspecialchars(utf8_decode($_GET["usuario"]));
    print $lc_config->fn_ValidaUsuario($lc_condiciones);
}

