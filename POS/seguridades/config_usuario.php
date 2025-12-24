<?php

////////////////////////////////////////////////////////////////////////////////////////////
////////MODIFICADO POR: CHRISTIAN PINTO/////////////////////////////////////////////////////
////////DECRIPCION: VALIDACION DE USUARIO PERFIL Y PERIODO /////////////////////////////////
////////TABLAS INVOLUCRADAS: Users_Pos,Perfil_Pos, Periodo//////////////////////////////////
////////FECHA CREACION: 26/08/2015//////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////


require_once '../system/conexion/clase_sql.php';
include_once '../clases/clase_seguridades.php';
include_once '../clases/clase_direccion.php';
include '../ordenpedido/Validacion.php';

$ip = new direccion();
$usuario = new seguridades();
$validador = new Validacion();

$lc_seguridad = new seguridadesUsuarioPerfilPeriodo();
//$lc_cadena	 = $_SESSION['cadenaId'];
//$lc_usuario = $_SESSION['usuarioId'];
///$lc_restaurante = $_SESSION['rstId'];
//$clave = htmlspecialchars($_GET['usr_clave']);
//$tipo = htmlspecialchars($_GET['txtTipo']);
//$estacion = htmlspecialchars($_GET['txtEstacion']);
//$ip_dir = htmlspecialchars($_GET['txtIp']);

if (htmlspecialchars(isset($_POST['validarRUC']))) {
    $lc_datos[0] = htmlspecialchars($_POST['documento']);

    $estado = $validador->validarRuc($lc_datos[0]);
    if ($estado == true) {
        print '{"estado": "1"}';
    } else {
        print '{"estado": "0"}';
    }
} else if (htmlspecialchars(isset($_POST["validarCedula"]))) {
    $lc_datos[0] = htmlspecialchars($_POST["documento"]);
    $estado = $validador->validarCedula($lc_datos[0]);
    if ($estado == true) {
        print '{"estado": "1"}';
    } else {
        print '{"estado": "0"}';
    }
}

if (htmlspecialchars(isset($_POST['cargarPermisos']))) {
    $lc_datos[0] = htmlspecialchars($_POST['usuario_id']);
    $lc_datos[1] = htmlspecialchars($_POST['pantalla']);
    print $usuario->fn_consultar('cargarPermisos', $lc_datos);
}

if (htmlspecialchars(isset($_POST['cargarUsuario']))) {
    $lc_datos[0] = htmlspecialchars($_POST['usuario_id']);
    print $usuario->fn_consultar('cargarUsuario', $lc_datos);
}

if (htmlspecialchars(isset($_GET['bandera']))) {
    $bandera = htmlspecialchars($_GET['bandera']);
} else {
    $bandera = 'Inicio';
}

if (htmlspecialchars(isset($_GET['validaUsuarioPerfil']))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['accion']);
    $lc_condiciones[1] = htmlspecialchars($_GET['usr_clave']);
    $lc_condiciones[2] = htmlspecialchars($_GET['est_ip']);
    print $lc_seguridad->fn_consultar('validaUsuarioPerfil', $lc_condiciones);
}

if (htmlspecialchars(isset($_GET['traerUsuario']))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['accion']);
    $lc_condiciones[1] = htmlspecialchars($_GET['usr_clave']);
    $lc_condiciones[2] = htmlspecialchars($_GET['est_ip']);
    print $lc_seguridad->fn_consultar('traerUsuario', $lc_condiciones);
}

if (htmlspecialchars(isset($_GET['validaAccesoPerfil']))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['accion']);
    $lc_condiciones[1] = htmlspecialchars($_GET['usr_clave']);
    $lc_condiciones[2] = htmlspecialchars($_GET['est_ip']);
    print $lc_seguridad->fn_consultar('validaAccesoPerfil', $lc_condiciones);
}

if (htmlspecialchars(isset($_GET['validaestacionenuso']))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['accion']);
    $lc_condiciones[1] = htmlspecialchars($_GET['est_ip']);
    $lc_condiciones[2] = htmlspecialchars($_GET['usr_clave']);
    print $lc_seguridad->fn_consultar('validaestacionenuso', $lc_condiciones);
}

if (htmlspecialchars(isset($_GET['validafondo']))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['accion']);
    $lc_condiciones[1] = htmlspecialchars($_GET['est_ip']);
    $lc_condiciones[2] = htmlspecialchars($_GET['usr_clave']);
    print $lc_seguridad->fn_consultar('validafondo', $lc_condiciones);
}

if (htmlspecialchars(isset($_GET['confirmarfondo']))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['accion']);
    $lc_condiciones[1] = htmlspecialchars($_GET['est_ip']);
    $lc_condiciones[2] = htmlspecialchars($_GET['usr_clave']);
    print $lc_seguridad->fn_consultar('confirmarfondo', $lc_condiciones);
}

if (htmlspecialchars(isset($_GET['validaclaveadmin']))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['accion']);
    $lc_condiciones[1] = htmlspecialchars($_GET['est_ip']);
    $lc_condiciones[2] = htmlspecialchars($_GET['usr_clave']);
    print $lc_seguridad->fn_consultar('validaclaveadmin', $lc_condiciones);
}

if (htmlspecialchars(isset($_GET['grabacontrolestacion']))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['accion']);
    $lc_condiciones[1] = htmlspecialchars($_GET['est_ip']);
    $lc_condiciones[2] = htmlspecialchars($_GET['usr_clave']);
    $lc_condiciones[3] = htmlspecialchars($_GET['fondo']);
    $lc_condiciones[4] = htmlspecialchars($_GET['usr_claveAdmin']);
    print $lc_seguridad->fn_consultar('grabacontrolestacion', $lc_condiciones);
}

if (htmlspecialchars(isset($_GET['tiposervicio']))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['accion']);
    $lc_condiciones[1] = htmlspecialchars($_GET['est_ip']);
    print $lc_seguridad->fn_consultar('tiposervicio', $lc_condiciones);
}

if (htmlspecialchars(isset($_GET['validaEstacionEnUsoUsuario']))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['accion']);
    $lc_condiciones[1] = htmlspecialchars($_GET['est_ip']);
    $lc_condiciones[2] = htmlspecialchars($_GET['usr_clave']);
    print $lc_seguridad->fn_consultar('validaEstacionEnUsoUsuario', $lc_condiciones);
}

if (htmlspecialchars(isset($_GET['validaUsuarioLogueadoEstacion']))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['accion']);
    $lc_condiciones[1] = htmlspecialchars($_GET['est_ip']);
    $lc_condiciones[2] = htmlspecialchars($_GET['usr_clave']);
    print $lc_seguridad->fn_consultar('validaUsuarioLogueadoEstacion', $lc_condiciones);
}

if (htmlspecialchars(isset($_GET['verificaCajaAsignada']))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['accion']);
    $lc_condiciones[1] = htmlspecialchars($_GET['est_ip']);
    $lc_condiciones[2] = htmlspecialchars($_GET['usr_clave']);
    print $lc_seguridad->fn_consultar('verificaCajaAsignada', $lc_condiciones);
}

if (htmlspecialchars(isset($_GET['verificaUsuarioCorrecto']))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['accion']);
    $lc_condiciones[1] = htmlspecialchars($_GET['est_ip']);
    $lc_condiciones[2] = htmlspecialchars($_GET['usr_clave']);
    print $lc_seguridad->fn_consultar('verificaUsuarioCorrecto', $lc_condiciones);
}

if (htmlspecialchars(isset($_GET['obtenerMesa']))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['rst_id']);
    print $lc_seguridad->fn_consultar('obtenerMesa', $lc_condiciones);
}

if (htmlspecialchars(isset($_GET['inicioVariablesDeSesion']))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['accion']);
    $lc_condiciones[1] = htmlspecialchars($_GET['usr_clave']);
    $lc_condiciones[2] = htmlspecialchars($_GET['est_ip']);
    $lc_condiciones[3] = htmlspecialchars($_GET['usr_id_asignado']);
    $lc_condiciones[4] = htmlspecialchars($_GET['bandera']);
    print $lc_seguridad->fn_getDatosUser('variablesSesion', $lc_condiciones);
}

if (htmlspecialchars(isset($_GET['inicioVariablesSesionDesmontarCajero']))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['accion']);
    $lc_condiciones[1] = htmlspecialchars($_GET['usr_clave']);
    $lc_condiciones[2] = htmlspecialchars($_GET['est_ip']);
    $lc_condiciones[3] = htmlspecialchars($_GET['usr_id_cajero']);
    $lc_condiciones[4] = htmlspecialchars($_GET['bandera']);
    print $lc_seguridad->fn_getDatosUser('inicioVariablesSesionDesmontarCajero', $lc_condiciones);
}

if (htmlspecialchars(isset($_GET['inicioVariablesSesionFinDia']))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['accion']);
    $lc_condiciones[1] = htmlspecialchars($_GET['usr_clave']);
    $lc_condiciones[2] = htmlspecialchars($_GET['est_ip']);
    $lc_condiciones[3] = htmlspecialchars($_GET['usr_id_cajero']);
    $lc_condiciones[4] = htmlspecialchars($_GET['bandera']);
    print $lc_seguridad->fn_getDatosUser('inicioVariablesSesionFinDia', $lc_condiciones);
}

if (htmlspecialchars(isset($_GET['inicioVariablesDeSesionUserReportes']))) {
    $lc_condiciones[0] = htmlspecialchars($_GET['accion']);
    $lc_condiciones[1] = htmlspecialchars($_GET['usr_clave']);
    $lc_condiciones[2] = htmlspecialchars($_GET['est_ip']);
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = htmlspecialchars($_GET['bandera']);
    print $lc_seguridad->fn_getDatosUser('inicioVariablesDeSesionUserReportes', $lc_condiciones);
}

if (htmlspecialchars(isset($_POST['validaAperturaPeriodo']))) {
    $lc_condiciones[0] = htmlspecialchars($_POST['accion']);
    $lc_condiciones[1] = htmlspecialchars($_POST['est_ip']);
    $lc_condiciones[2] = htmlspecialchars($_POST['fechaAperturaPeriodo']);
    print $lc_seguridad->fn_consultar('validaAperturaPeriodo', $lc_condiciones);
}

if (htmlspecialchars(isset($_POST['consultarEstacionTomaPedido']))) {
    $lc_condiciones[0] = htmlspecialchars($_POST['est_ip']);
    print $lc_seguridad->fn_consultar('consultarEstacionTomaPedido', $lc_condiciones);
}

if (htmlspecialchars(isset($_POST['consultarClavePerfil']))) {
    $lc_condiciones[0] = htmlspecialchars($_POST['est_ip']);
    $lc_condiciones[1] = htmlspecialchars($_POST['clave']);
    print $lc_seguridad->fn_consultar('consultarClavePerfil', $lc_condiciones);
}

if (htmlspecialchars(isset($_POST['obtieneMesaPredeterminada']))) {
    $lc_condiciones[0] = htmlspecialchars($_POST['est_id']);
    $lc_condiciones[1] = htmlspecialchars($_POST['cdn_id']);
    print $lc_seguridad->fn_consultar('obtieneMesaPredeterminada', $lc_condiciones);
}

if (htmlspecialchars(isset($_POST['FidelizacionActiva']))) {
    $lc_condiciones[0] = $_SESSION['rstId'];
    print $lc_seguridad->fn_consultar('FidelizacionActiva', $lc_condiciones);
}

if (htmlspecialchars(isset($_POST['ConusltarExistente']))) {
    $lc_condiciones[0] = htmlspecialchars($_POST['accion']);
    $lc_condiciones[1] = htmlspecialchars($_POST['numeroDocumento']);
    $lc_condiciones[2] = htmlspecialchars($_POST['cdn_id']);
    print $lc_seguridad->fn_consultar('ConusltarExistente', $lc_condiciones);
}

if (htmlspecialchars(isset($_POST['CRUD_Cliente']))) {
    $lc_condiciones[0] = htmlspecialchars($_POST['accion']);
    $lc_condiciones[1] = utf8_decode($_POST['nombresApellidos']);
    $lc_condiciones[2] = htmlspecialchars($_POST['tipoDocumento']);
    $lc_condiciones[3] = htmlspecialchars($_POST['numeroDocumento']);
    $lc_condiciones[4] = htmlspecialchars($_POST['telefono']);
    $lc_condiciones[5] = utf8_decode($_POST['direccion']);
    $lc_condiciones[6] = utf8_decode($_POST['mail']);
    $lc_condiciones[7] = htmlspecialchars($_POST['usuario']);
    $lc_condiciones[8] = NULL;
    $lc_condiciones[9] = NULL;
    print $lc_seguridad->fn_consultar('CRUD_Cliente', $lc_condiciones);
}

if (htmlspecialchars(isset($_POST['cargaTeclasEmail']))) {
    $lc_condiciones[0] = htmlspecialchars($_POST['cdn_id']);
    print $lc_seguridad->fn_consultar('cargaTeclasEmail', $lc_condiciones);
}

if (htmlspecialchars(isset($_POST['ordenPedidoPuntos']))) {
    $lc_condiciones[0] = htmlspecialchars($_POST['op_id']);
    print $lc_seguridad->fn_consultar('ordenPedidoPuntos', $lc_condiciones);
}

if (htmlspecialchars(isset($_POST['noEnrrolar']))) {
    $_SESSION['fb_document'] = null;
    $_SESSION['fdznDocumento'] = null; // para cuando se cierre erroneamente en la pantalla facturacin.
    $_SESSION['fb_name'] = null;
    $_SESSION['fb_status'] = null;
    $_SESSION['fb_points'] = null;
    $_SESSION['fdznNombres'] = null;
    $_SESSION['fdznDireccion'] =   null;
    $_SESSION['fb_econtroDatos'] = 0;
    $_SESSION['fb_money'] = 0;
    print (
    '{
        "mensaje": "ok"
    }'
    );
}

if (htmlspecialchars(isset($_POST["obtenerLimitesRecarga"]))) {
    $idCadena = htmlspecialchars($_POST['idCadena']);    
    print $lc_seguridad->obtenerLimitesRecarga($idCadena);
}
?>
