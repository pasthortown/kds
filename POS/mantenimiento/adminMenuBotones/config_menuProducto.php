<?php

session_start();
//////////////////////////////////////////////////////////////
///////DESARROLLADO POR: Juan Méndez /////////////////////////
///////DESCRIPCION: Modifica informacion del boton Producto ////
///////TABLAS INVOLUCRADAS: Restaurante///////////////////////
///////FECHA CREACION: 17-12-2013/////////////////////////////
///////FECHA ULTIMA MODIFICACION: 17-02-2014////////////////////
///////USUARIO QUE MODIFICO:  Cristhian Castro/////////////////
///////DECRIPCION ULTIMO CAMBIO: Corrección de errores/////////
///////FECHA ULTIMA MODIFICACION: 01/06/2015///////////////////
///////USUARIO QUE MODIFICO: Jose Fernandez///////////////////
///////DECRIPCION ULTIMO CAMBIO: Buscador todos los campos////
//////////////////////////////////////////////////////////////  


include_once"../../system/conexion/clase_sql.php";
include_once"../../clases/clase_menuProducto.php";

if (empty($_SESSION['rstId']) OR empty($_SESSION['usuarioId']) OR empty($_SESSION['cadenaId'])) {
    die(json_encode((object) [
        "estado" => "ERROR",
        "mensaje" => "Faltan variables de sesión, por favor loguearse nuevamente"
    ]));
}

$lc_config = new plu();
$lc_cadena = $_SESSION['cadenaId'];
$lc_usuario = $_SESSION['usuarioId'];
$lc_restaurante = $_SESSION['rstId'];

if (htmlspecialchars(isset($_GET["cargarCaracteristica"]))) {
    $lc_condiciones[0] = $_SESSION['cadenaId'];
    $lc_condiciones[1] = htmlspecialchars($_GET["Accion"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["inicio"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["fin"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["filtro"]);
    print $lc_config->fn_consultar("cargarCaracteristica", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["cargarCaracteristicaPorEstado"]))) {
    $lc_condiciones[0] = $_SESSION['cadenaId'];
    $lc_condiciones[2] = htmlspecialchars($_GET["inicio"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["fin"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["filtro"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["opcion"]);
    print $lc_config->fn_consultar("cargarCaracteristicaPorEstado", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["cargarDatosPluMenu"]))) {
    $lc_condiciones[0] = $_SESSION['cadenaId'];
    $lc_condiciones[1] = htmlspecialchars($_GET["magi_Id"]);
    print $lc_config->fn_consultar("cargarDatosPluMenu", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["cargarPlus"]))) {
    $lc_condiciones[0] = $_SESSION['cadenaId'];
    print $lc_config->fn_consultar("cargarPlus", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["guardaInfoPluMenu"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["plu_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["color"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["fondo"]);
    $lc_condiciones[4] = htmlspecialchars(utf8_decode($_GET["nomImpresion"]));
    $lc_condiciones[5] = htmlspecialchars($_GET["fechainicio"]);
    $lc_condiciones[6] = htmlspecialchars($_GET["fechafin"]);
    $lc_condiciones[7] = htmlspecialchars($_GET["usr_id"]);
    $lc_condiciones[8] = htmlspecialchars($_GET["mag"]);
    $lc_condiciones[9] = htmlspecialchars($_GET["estado"]);
    $lc_condiciones[10] = htmlspecialchars(utf8_decode($_GET["impresionfactuta"]));
    print $lc_config->fn_consultar("actualizaInfoPluMenu", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["guardaInfoPluMenuNuevo"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["plu_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["colorNuevo"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["fondoNuevo"]);
    $lc_condiciones[4] = htmlspecialchars(utf8_decode($_GET["nomImpresionNuevo"]));
    $lc_condiciones[5] = htmlspecialchars($_GET["fechainicioNuevo"]);
    $lc_condiciones[6] = htmlspecialchars($_GET["fechafinNuevo"]);
    $lc_condiciones[7] = htmlspecialchars($_GET["usr_id"]);
    $lc_condiciones[8] = htmlspecialchars($_GET["magp_id"]);
    $lc_condiciones[9] = htmlspecialchars($_GET["estado"]);
    $lc_condiciones[10] = htmlspecialchars(utf8_decode($_GET["impresionfactuta"]));
    print $lc_config->fn_consultar("guardaInfoPluMenuNuevo", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["traerRestaurantes"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["aplica"]);
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = $lc_usuario;
    $lc_condiciones[4] = htmlspecialchars($_GET["magp_id"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["restaurante"]);
    $lc_condiciones[6] = 0;
    print $lc_config->fn_consultar("traerRestaurantes", $lc_condiciones);

} else if (isset($_POST["aplica_restaurante_nuevo"])) {
    $lc_condiciones[0] = 2;
    $lc_condiciones[1] = $lc_cadena;
    if (isset($_POST["id_restaurante"])) {
        $lc_condiciones[2] = 0;
        foreach ($_POST['id_restaurante'] as $lc_resttaurantes) {
            $lc_condiciones[2] = $lc_condiciones[2] . ',' . $lc_resttaurantes;
        }
    } else {
        $lc_condiciones[2] = 0;
    }
    $lc_condiciones[3] = $lc_usuario;
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = $_POST["restaurante"];
    $lc_condiciones[6] = $_POST["id_nuevo"];
    print $lc_config->fn_consultar("traerRestaurantes", $lc_condiciones);

} else if (isset($_POST["aplica_restaurante"])) {
    $lc_condiciones2[0] = 4; //ELIMINA
    $lc_condiciones2[1] = $lc_cadena;
    $lc_condiciones2[2] = 0;
    $lc_condiciones2[3] = $lc_usuario;
    $lc_condiciones2[4] = $_POST["magp_id"];
    $lc_condiciones2[5] = $_POST["restaurante"];
    $lc_condiciones2[6] = 0;
    $lc_config->fn_consultar("traerRestaurantes", $lc_condiciones2);
    $lc_condiciones = $_POST["restaurante"];
    for ($i = 0; $i < count($lc_condiciones); $i++) {
        if (isset($_POST["id_restaurante"])) {
            $lc_condiciones = 0;
            foreach ($_POST['id_restaurante'] as $lc_resttaurantes) {
                $lc_condiciones = $lc_condiciones . ',' . $lc_resttaurantes;
            }
        } else {
            $lc_condiciones = 0;
        }

        $lc_condiciones1[0] = 5; //INSERTA
        $lc_condiciones1[1] = $lc_cadena;
        $lc_condiciones1[2] = $lc_condiciones;
        $lc_condiciones1[3] = $lc_usuario;
        $lc_condiciones1[4] = $_POST["magp_id"];
        $lc_condiciones1[5] = $_POST["restaurante"];
        $lc_condiciones1[6] = 0;
        print $lc_config->fn_consultar("traerRestaurantes", $lc_condiciones1);
    }
}