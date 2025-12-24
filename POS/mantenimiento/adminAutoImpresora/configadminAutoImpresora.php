<?php

//////////////////////////////////////////////////////////////
///////DESARROLLADO POR: Alex Merino//////////////////////////
///////DESCRIPCION: Clase auto impresores de restaunrante/////
///////FECHA CREACION: 13-03-2018 ////////////////////////////
////////////////////////////////////////////////////////////// 

include("../../system/conexion/clase_sql.php");
include("../../clases/clase_adminAutoImpresora.php");

$lc_objeto = new autoimpresoras();
$lc_cadena = $_SESSION['cadenaId'];
$lc_usuario = $_SESSION['usuarioId'];

if (isset($_GET["infRestaurantesCadena"])) {
    $lc_condiciones[0] = 1;
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = "";
    print $lc_objeto->fn_consultar("infRestaurantesCadena", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["infRestaurante"]))) {
    $lc_condiciones[0] = 2;
    $lc_condiciones[1] = htmlspecialchars($lc_cadena);
    $lc_condiciones[2] = htmlspecialchars($_GET['lc_res']);
    $lc_condiciones[3] = "";
    print $lc_objeto->fn_consultar("infRestaurante", $lc_condiciones);
} else if (isset($_GET["infAutoImpresor"])) {
    $lc_condiciones[0] = 3;
    $lc_condiciones[1] = htmlspecialchars($lc_cadena);
    $lc_condiciones[2] = htmlspecialchars($_GET['rst_id']);
    $lc_condiciones[3] = htmlspecialchars($_GET['cod_RST']);
    print $lc_objeto->fn_consultar("infAutoImpresor", $lc_condiciones);
} else if (isset($_GET["loadModalNuevo"])) {
    $lc_condiciones[0] = 4;
    $lc_condiciones[1] = htmlspecialchars($lc_cadena);
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = "";
    print $lc_objeto->fn_consultar("loadModalNuevo", $lc_condiciones);
} else if (isset($_GET["infAutoimpresorDatos"])) {
    $lc_condiciones[0] = 5;
    $lc_condiciones[1] = htmlspecialchars($lc_cadena);
    $lc_condiciones[2] = htmlspecialchars($_GET['rst_id']);
    $lc_condiciones[3] = "";
    print $lc_objeto->fn_consultar("infAutoimpresorDatos", $lc_condiciones);
} else if (isset($_GET["guardarDatosModificarAutoImpresoras"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET['activo']);
    $lc_condiciones[1] = htmlspecialchars($_GET['id_rest']);
    $lc_condiciones[2] = utf8_decode($_GET['valores']);
    $lc_condiciones[3] = htmlspecialchars($_GET['idcoleccionrestaurante']);
    $lc_condiciones[4] = htmlspecialchars($_GET['idColecciondedatosRestaurante']);
    $lc_condiciones[5] = htmlspecialchars($_GET['numFormulario']);
    $lc_condiciones[6] = htmlspecialchars($_GET['identificadorSecuencial']);
    $lc_condiciones[7] = htmlspecialchars($lc_usuario);
    print $lc_objeto->fn_ingresarAutoImpresora("guardarDatosModificarAutoImpresoras", $lc_condiciones);
} else if (isset($_GET["guardarDatosAutoImpresoras"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET['activo']);
    $lc_condiciones[1] = htmlspecialchars($_GET['identificador']);
    $lc_condiciones[2] = utf8_decode($_GET['valores']);
    $lc_condiciones[3] = htmlspecialchars($_GET['id_rest']);
    $lc_condiciones[4] = htmlspecialchars($lc_cadena);  
    $lc_condiciones[5] = htmlspecialchars($_GET['campos_adicionales']);
    $lc_condiciones[6] = htmlspecialchars($lc_usuario);
    print $lc_objeto->fn_ingresarAutoImpresora("guardarDatosAutoImpresoras", $lc_condiciones);
}
?>