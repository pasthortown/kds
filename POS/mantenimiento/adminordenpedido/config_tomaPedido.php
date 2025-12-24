<?php
session_start();
include_once"../../system/conexion/clase_sql.php";
include_once "../../clases/clase_tomapedidoMenu.php";

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

$lc_config = new menuPedido();
// -------------------------------------------------
//  FECHA ULTIMA MODIFICACION	: 10:33 2/3/2017
//  USUARIO QUE MODIFICO	:  Mychael Castro
//  DECRIPCION ULTIMO CAMBIO	: Controlar drag and drop  con el zoom + y - del navegador.
//  -------------------------------------------------

//carga las cadenas
if (htmlspecialchars(isset($_GET["cargaCadena"]))) {
    print $lc_config->fn_consultar("cargaCadena", "");
} else if (htmlspecialchars(isset($_GET["validaPosicionCategoria"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["opcionValida"]); 
    $lc_condiciones[1] = htmlspecialchars($_GET["menuID"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["cadenaId"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["magOrden"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["magIDvalida"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["codigoProducto"]);
    print $lc_config->fn_consultar("validaPosicionCategoria", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["cargaClasificacion"]))) {
    $lc_condiciones[0] = 'CLA';
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = 't';
    $lc_condiciones[5] = 0;
    print $lc_config->fn_consultar("cargaClasificacion", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["listaCategoria"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]); 
    $lc_condiciones[1] = htmlspecialchars($_GET["cdn_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["menu_id"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["mag_id"]);
    $lc_condiciones[4] = 't';
    $lc_condiciones[5] = 0;
    print $lc_config->fn_consultar("listaCategoria", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["listaProductos"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]); 
    $lc_condiciones[1] = htmlspecialchars($_GET["cdn_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["menu_id"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["mag_id"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["canal"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["idcla"]);
    print $lc_config->fn_consultar("listaProductos", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["cargarMenu"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["cdn_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["menu_id"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["mag_id"]);
    $lc_condiciones[4] = 't';
    $lc_condiciones[5] = 0;
    print $lc_config->fn_consultar("cargarMenu", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["cargarCategoria"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["cdn_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["menu_id"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["mag_id"]);
    $lc_condiciones[4] = 't';
    $lc_condiciones[5] = 0;
    print $lc_config->fn_consultar("cargarCategoria", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["cargarProducto"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["cdn_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["menu_id"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["mag_id"]);
    $lc_condiciones[4] = 't';
    $lc_condiciones[5] = 0;
    print $lc_config->fn_consultar("cargarProducto", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["autollenarCategoria"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["mag_orden"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["magp_id"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["cat_id"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["bandera"]);
    $lc_condiciones[6] = $_SESSION['usuarioId']; // htmlspecialchars($_GET["usr_id"]);
    $lc_condiciones[7] = htmlspecialchars($_GET["idDlMenu"]);
    print $lc_config->fn_consultar("autollenarCategoria", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["actualizarCategoria"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["posicionTotal"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["magp_id"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["cat_id"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["bandera"]);
    $lc_condiciones[6] = $_SESSION['usuarioId']; //$_GET["usr_id"];
    $lc_condiciones[7] = htmlspecialchars($_GET["acMenuId"]);
    print $lc_config->fn_consultar("actualizarCategoria", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["eliminarCategoria"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["mag_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["mag_orden"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["magp_id"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["cat_id"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["bandera"]);
    $lc_condiciones[6] = $_SESSION['usuarioId']; //$_GET["usr_id"];
    $lc_condiciones[7] = htmlspecialchars($_GET["eMenId"]);
    print $lc_config->fn_consultar("eliminarCategoria", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["autollenarProducto"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["mag_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["mag_orden"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["id"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["cat_id"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["bandera"]);
    $lc_condiciones[6] = $_SESSION['usuarioId']; //$_GET["usr_id"];
    $lc_condiciones[7] = htmlspecialchars($_GET["autoMeId"]);
    print $lc_config->fn_consultar("autollenarProducto", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["actualizarProducto"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]); 
    $lc_condiciones[1] = htmlspecialchars($_GET["mag_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["posicionTotal"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["id"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["cat_id"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["bandera"]);
    $lc_condiciones[6] = $_SESSION['usuarioId']; //$_GET["usr_id"];
    $lc_condiciones[7] = htmlspecialchars($_GET["aMenId"]);
    print $lc_config->fn_consultar("actualizarProducto", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["eliminarProducto"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]); 
    $lc_condiciones[1] = htmlspecialchars($_GET["mag_id"]); 
    $lc_condiciones[2] = htmlspecialchars($_GET["mag_orden"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["magp_id"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["cat_id"]); 
    $lc_condiciones[5] = htmlspecialchars($_GET["bandera"]);
    $lc_condiciones[6] = $_SESSION['usuarioId']; //$_GET["usr_id"];
    $lc_condiciones[7] = htmlspecialchars($_GET["eMenIdP"]); 
    print $lc_config->fn_consultar("eliminarProducto", $lc_condiciones);
} else if (htmlspecialchars(isset($_GET["actualizaMenuAgrupacion"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["mag_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["mag_orden"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["magp_id"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["mag_idd"]);  
    $lc_condiciones[5] = htmlspecialchars($_GET["banderamantiene"]);
    $lc_condiciones[6] = $_SESSION['usuarioId']; //$_GET["usr_id"];
    $lc_condiciones[7] = htmlspecialchars($_GET["actMaMenuId"]);
    print $lc_config->fn_consultar("actualizaMenuAgrupacion", $lc_condiciones);
}