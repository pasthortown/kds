<?php

//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Worman Andrade//////////////////////
////////DESCRIPCION: Clase para Carga de Combos///////////////
/////////////////////En las Mesas ////////////////////////////
///////FECHA CREACION: 27-01-2016 ////////////////////////////
///////FECHA MODIFICACIÓN: 22-12-2016 ////////////////////////
///////USUARIO MODIFICO: Juan Estévez ////////////////////////
///////DESCRIPCION: Se agrego panel mesa /////////////////////
////////////////////////////////////////////////////////////// 

include("../../system/conexion/clase_sql.php");
include("../../clases/clase_mesa.php");

if (empty($_SESSION['rstId']) OR empty($_SESSION['usuarioId']) OR empty($_SESSION['cadenaId'])) {
    die(json_encode((object) [
        "estado" => "ERROR",
        "mensaje" => "Faltan variables de sesión, por favor loguearse nuevamente"
    ]));
}

$lc_objeto = new mesa();
$lc_cadena = $_SESSION['cadenaId'];
$lc_usuario = $_SESSION['usuarioId'];

if (htmlspecialchars(isset($_GET["cargarRestaurante"]))) {
    $lc_condiciones[0] = 1;
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = $lc_usuario;
    $lc_condiciones[4] = 0;
    print $lc_objeto->fn_consultar("cargarRestaurante", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["cargarPiso"]))) {
    $lc_condiciones[0] = 2;
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = htmlspecialchars($_GET["codigo"]);
    $lc_condiciones[3] = $lc_usuario;
    $lc_condiciones[4] = 0;
    print $lc_objeto->fn_consultar("cargarPiso", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["cargarArea"]))) {
    $lc_condiciones[0] = 3;
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = $lc_usuario;
    $lc_condiciones[4] = htmlspecialchars($_GET["codigo"]);
    print $lc_objeto->fn_consultar("cargarArea", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET['cargarMesa']))) {
    $lc_condiciones[0] = $_SESSION['cadenaId'];
    $lc_condiciones[1] = htmlspecialchars($_GET['restaurante']);
    $lc_condiciones[2] = htmlspecialchars($_GET['piso']);
    $lc_condiciones[3] = htmlspecialchars($_GET['area']);
    $lc_condiciones[4] = htmlspecialchars($_GET['filtro']);
    $lc_condiciones[5] = htmlspecialchars($_GET['buscar']);
    print $lc_objeto->fn_consultar('cargarMesa', $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["guardarMesa"]))) {

    $lc_condiciones[0] = htmlspecialchars($_GET["mesaId"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["valoresX"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["valoresY"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["opc"]);
    $lc_condiciones[4] = $lc_usuario;
    print $lc_objeto->fn_ejecutar("guardarMesa", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["accionMenu"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["mesa_descripcion"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["arp_id"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["tmes_id"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["std_id"]);
    $lc_condiciones[6] = htmlspecialchars($_GET["cdn_id"]);
    $lc_condiciones[7] = htmlspecialchars($_GET["user"]);
    $lc_condiciones[8] = htmlspecialchars($_GET["rst"]);
    $lc_condiciones[9] = htmlspecialchars($_GET["pis_id"]);
    print $lc_objeto->fn_consultar("accionMenu", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["cargarCantidad"]))) {
    $lc_condiciones[0] = 4;
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = 0;
    print $lc_objeto->fn_consultar("cargarCantidad", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["cargaMesaModificar"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["mesa_id"]);
    $lc_condiciones[1] = 'informacion';
    print $lc_objeto->fn_consultar("cargaMesaModificar", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["consultarListaMesas"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["resultado"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["mesa_id"]);
    $lc_condiciones[2] = $_SESSION['cadenaId'];
    $lc_condiciones[3] = htmlspecialchars($_GET["restaurante"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["piso"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["area"]);
    $lc_condiciones[6] = htmlspecialchars($_GET["parametro"]);
    $lc_condiciones[7] = htmlspecialchars($_GET["pagina"]);
    $lc_condiciones[8] = htmlspecialchars($_GET["registros"]);
    $lc_condiciones[9] = htmlspecialchars($_GET["std_id"]);
    print $lc_objeto->fn_consultar("consultarListaMesas", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["cargarPanelMesa"]))) {
    $lc_condiciones[0] = $_SESSION['cadenaId'];
    $lc_condiciones[1] = htmlspecialchars($_GET["rest"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["piso"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["area"]);
    $lc_condiciones[4] = 'PanelMesa';
    $lc_condiciones[5] = '';
    print $lc_objeto->fn_consultar("cargarPanelMesa", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["cargarTipoMesa"]))) {
    $lc_condiciones[0] = $_SESSION['cadenaId'];
    $lc_condiciones[1] = htmlspecialchars($_GET["restaurante"]); //nuevo
    print $lc_objeto->fn_consultar("cargarTipoMesa", $lc_condiciones);

} else if (htmlspecialchars(isset($_POST["VerificarMisMesa"]))) {
    $lc_condiciones[0] = htmlspecialchars($_POST['mesa_id']);
    $lc_condiciones[1] = htmlspecialchars($_POST["rst_id"]);
    print $lc_objeto->fn_consultar("VerificarMisMesa", $lc_condiciones);

}