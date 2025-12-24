<?php

session_start();

////////////////////////////////////////////////////////////////////////////////////////////
///////CREADO POR: Jorge Tinoco ////////////////////////////////////////////////////////////
///////FECHA CREACION: 06-02-2016 //////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////
include_once "../parametros.php";
include_once "../system/conexion/clase_sql.php";
include_once "../clases/clase_ordenPedido.php";
include_once "../clases/clase_webservice.php";
include_once "../clases/clase_execWebService.php";

use Maxpoint\Mantenimiento\promociones\Clases\PromocionesController;

$lc_config = new menuPedido();
$objExecuteWs = new ExecWebService();
$servicioWebObj = new webservice();
$cdn_id = $_SESSION['cadenaId'];
$ip = $_SESSION['direccionIp'];
$usuario = $_SESSION['usuarioIdAdmin'];
$cajero = $_SESSION['usuarioId'];
$restaurante = $_SESSION['rstId'];
$estacion = $_SESSION['estacionId'];
$tipo_servicio = $_SESSION['TipoServicio'];
$nombre_usuario = $_SESSION['nombre'];
$perfil = $_SESSION['perfil'];
$control = $_SESSION['IDControlEstacion'];

if (htmlspecialchars($_GET["cargarConfiguracionRestaurante"])) {
    $lc_condiciones[0] = $restaurante;
    $lc_condiciones[1] = $tipo_servicio;
    $lc_condiciones[2] = $estacion;
    $lc_condiciones[3] = $cdn_id;
    print $lc_config->fn_consultar("cargarConfiguracionRestaurante", $lc_condiciones);

} else if (htmlspecialchars($_GET["cargarMenuEstacionDinamico"])) {
    $lc_condiciones[0] = $cdn_id;
    $lc_condiciones[1] = $restaurante;
    $lc_condiciones[2] = $estacion;
    $lc_condiciones[3] = htmlspecialchars($_GET["fidelizacionActivo"]) ? htmlspecialchars($_GET["vitalityActivo"]) : 0;
    $lc_condiciones[4] = htmlspecialchars($_GET["vitalityActivo"]) ? htmlspecialchars($_GET["vitalityActivo"]) : 0;
	print $lc_config->fn_consultar("cargarMenuEstacionDinamico", $lc_condiciones);

} else if (htmlspecialchars($_GET["agregarComentarioOrdenPedido"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["odp_id"]);
    $lc_condiciones[1] = utf8_decode(htmlspecialchars($_GET["comentario"]));
    print $lc_config->fn_consultar("agregarComentarioOrdenPedido", $lc_condiciones);

}
else if (isset($_POST["insertarOrdenPedidoCupon"])) {
    $lc_condiciones[0] = $_POST["cdn_id"];
    $lc_condiciones[1] = $_POST["rst_id"];
    $lc_condiciones[2] = $_POST["usr_id"];
    $lc_condiciones[3] = $_POST["plu_id"];
    $lc_condiciones[4] = $_POST["codigo_cupon"];


    print $lc_config->fn_consultar("insertarOrdenPedidoCupon", $lc_condiciones);

}
else if (htmlspecialchars($_GET["configuracionOrdenPedido"])) {
    $lc_condiciones[0] = $restaurante;
    $lc_condiciones[1] = htmlspecialchars($_GET["mesa_id"]);
    $lc_condiciones[2] = $usuario;
    $lc_condiciones[3] = $estacion;
    $lc_condiciones[4] = htmlspecialchars($_GET["num_Pers"]) ? htmlspecialchars($_GET["num_Pers"]) : 0;
    $lc_condiciones[5] = htmlspecialchars($_GET["estado"]) ? 1 : 0;
	print $lc_config->fn_consultar("configuracionOrdenPedido", $lc_condiciones);

} else if (htmlspecialchars($_GET["validaFechaOtroPeriodo"])) {
    $lc_condiciones[0] = "1";
    $lc_condiciones[1] = $ip;
    $lc_condiciones[2] = htmlspecialchars($_GET["fechaPeriodoAbierto"]);
	print $lc_config->fn_validaFechaAtencionPeriodo($lc_condiciones);

} else if (htmlspecialchars($_GET["insertaQsr"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["odpQsr"]);
    $lc_condiciones[1] = $ip;
    $lc_condiciones[2] = $usuario;
    print $lc_config->fn_consultar("insertaQsr", $lc_condiciones);

} else if (htmlspecialchars($_GET["cargarMenuCategoria"])) {
    $lc_condiciones[0] = $cdn_id;
    $lc_condiciones[1] = $restaurante;
    $lc_condiciones[2] = htmlspecialchars($_GET["menu_id"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["cla_id"]);
	print $lc_config->fn_consultar("cargarMenuCategoria", $lc_condiciones);

} else if (htmlspecialchars($_GET["cargarProducto"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cat_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["mag_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["cla_id"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["menu_id"]);
    $lc_condiciones[4] = $restaurante;
    print $lc_config->fn_consultar("cargarProducto", $lc_condiciones);

} else if (htmlspecialchars($_GET["cargarProductoBuscador"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cat_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["mag_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["cla_id"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["descripcion"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["menu_id"]);
    $lc_condiciones[5] = $restaurante;
    print $lc_config->fn_consultar("cargarProductoBuscador", $lc_condiciones);

} else if (htmlspecialchars($_GET["insertarComentario"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["odp_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["dop_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["comentario"]);    
    $lc_condiciones[3] = htmlspecialchars($_GET["dop_cuenta"]);
	print $lc_config->fn_consultar("insertarComentario", $lc_condiciones);

} else if (htmlspecialchars($_GET["agregarPlusOrdenPedido"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cat_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["odp_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["plu_id"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["cantidad"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["menuId"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["numSplit"]) ? htmlspecialchars($_GET["numSplit"]) : 1;
    $lc_condiciones[6] = htmlspecialchars($_GET["plus_puntos"]);
    $lc_condiciones[7] = htmlspecialchars($_GET["idProductoBase"]);
    /*
      //print $lc_config->fn_consultar("agregarPlusOrdenPedido", $lc_condiciones);
      if (isset($_GET["numSplit"]) != 0) {
      $lc_condiciones[5] = $_GET["numSplit"];
      print $lc_config->fn_consultar("agregarPlusOrdenPedido_FS", $lc_condiciones);
      } else {
      print $lc_config->fn_consultar("agregarPlusOrdenPedido", $lc_condiciones);
      }
     */
    print $lc_config->fn_consultar("agregarPlusOrdenPedido", $lc_condiciones);

} else if (htmlspecialchars($_GET["verificarPreguntasSugerida"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cat_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["plu_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["menu_id"]);
	print $lc_config->fn_consultar("verificarPreguntasSugerida", $lc_condiciones);

} else if (htmlspecialchars($_GET["verificarRespuestaPreguntasSugeridas"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cat_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["psug_id"]);
	print $lc_config->fn_consultar("verificarRespuestaPreguntasSugeridas", $lc_condiciones);

} else if (htmlspecialchars($_GET["verificarUltimoElemento"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["dop_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["cantidadAnular"]);
    if (isset($_GET["elimina"]) && ($_GET["elimina"] != '')){
        $lc_condiciones[2] = $_GET["elimina"];
    }
    print $lc_config->fn_consultar("verificarUltimoElemento", $lc_condiciones);

} else if (htmlspecialchars($_GET["agregarPreguntaSugerida"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cat_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["odp_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["plus"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["cantidad"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["plus_puntos"]);
    $lc_condiciones[5] = htmlspecialchars($_GET["dop_cuenta"]) ? htmlspecialchars($_GET["dop_cuenta"]) : 1;
    $lc_condiciones[6] = htmlspecialchars($_GET["idProductoBase"]);
    $lc_condiciones[7] = isset($_GET["dop_varchar1"]) ? $_GET["dop_varchar1"] : '';
    $lc_condiciones[8] = isset($_GET["jsonUpselling"]) ? $_GET["jsonUpselling"] : '';
    /*
      if (isset($_GET['dop_cuenta'])) {
      $lc_condiciones[5] = $_GET["dop_cuenta"];
      print $lc_config->fn_consultar("agregarPreguntaSugerida_FS", $lc_condiciones);
      } else {
      print $lc_config->fn_consultar("agregarPreguntaSugerida", $lc_condiciones);
      }
     */
    print $lc_config->fn_consultar("agregarPreguntaSugerida", $lc_condiciones);

} else if (isset($_GET["pluSeparadorPresas"])) {
    print $lc_config->fn_consultar("pluSeparadorPresas", '');

} else if (isset($_GET["pluIdSeparadorPresas"])) {
    $lc_condiciones[0] = $_GET["psug_id"];
    $lc_condiciones[1] = $_GET["plu_id"];
    print $lc_config->fn_consultar("pluIdSeparadorPresas", $lc_condiciones);

} else if (htmlspecialchars($_GET["eliminarUltimoElemento"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["dop_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["odp_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["cantidadAnular"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["gramos"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["tipoBeneficioCupon"]) ? htmlspecialchars($_GET["tipoBeneficioCupon"]) : 0;
    $lc_condiciones[5] = $usuario;
    $lc_condiciones[6] = $restaurante;
    print $lc_config->fn_consultar("eliminarUltimoElemento", $lc_condiciones);

} else if (htmlspecialchars($_GET["eliminarunElemento"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["dop_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["odp_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["cantidadAnular"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["gramos"]);
    $lc_condiciones[4] = htmlspecialchars($_GET["tipoBeneficioCupon"]) ? htmlspecialchars($_GET["tipoBeneficioCupon"]) : 0;
    $lc_condiciones[5] = $usuario;
    $lc_condiciones[6] = $restaurante;
    print $lc_config->fn_consultar("eliminarunElemento", $lc_condiciones);

} else if (htmlspecialchars($_GET["cargar_ordenPedidoPendiente"])) {
    $lc_condiciones[0] = $restaurante;
    $lc_condiciones[1] = htmlspecialchars($_GET["odp_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["cat_id"]);
    $lc_condiciones[3] = htmlspecialchars($_GET["numSplit"]) ? htmlspecialchars($_GET["numSplit"]) : 1;
    /* if (htmlspecialchars($_GET["numSplit"])) != 0) {
      $lc_condiciones[3] = htmlspecialchars($_GET["numSplit"]);
      print $lc_config->fn_consultar("cargar_ordenPedidoPendiente_FS", $lc_condiciones);
      } else {
      print $lc_config->fn_consultar("cargar_ordenPedidoPendiente", $lc_condiciones);
      }
     */
    print $lc_config->fn_consultar("cargar_ordenPedidoPendiente", $lc_condiciones);

} else if (htmlspecialchars($_GET["validarUsuario"])) {
    $lc_condiciones[0] = $restaurante;
    $lc_condiciones[1] = $_GET["usr_clave"];
    $lc_condiciones[2] = $_GET["usr_tarjeta"];
    print $lc_config->fn_consultar("validarUsuario", $lc_condiciones);

} else if (isset($_GET["validarTiempoPlu"])) {
    $lc_condiciones[0] = $_GET["dop_id"];
    print $lc_config->fn_consultar("validarTiempoPlu", $lc_condiciones);

} else if (isset($_POST["validarTiempoPluListaProductos"])) {    
    $responseArray = [];
    foreach ($_POST["listProduct"] as $key => $value) {        
        $lc_condiciones[0] = $value['id'];
        $rowCondiciones = $lc_config->fn_consultar("validarTiempoPlu", $lc_condiciones);
        $rowCondiciones = json_decode($rowCondiciones, true);
        $final_array = array_merge($value, [
            'plu_creacionfecha' => $rowCondiciones[0]["plu_creacionfecha"],
            'str_tiempopedido' => $rowCondiciones["str"]
        ]);
        $responseArray[] = $final_array;
    }
    print json_encode($responseArray );
} else if (isset($_GET["lectorBarras"])) {
    $lc_condiciones[0] = $restaurante;
    $lc_condiciones[2] = $_GET["cat_id"];
    $lc_condiciones[3] = $_GET["num_plu"];
    $lc_condiciones[4] = $_GET["idClasificacion"];
    $lc_condiciones[5] = $_GET["menu_id"];
    print $lc_config->fn_consultar("lectorBarras", $lc_condiciones);

} else if (htmlspecialchars($_GET["obtenerMesa"])) {
    $lc_condiciones[0] = $restaurante;
    $lc_condiciones[1] = $estacion;
    print $lc_config->fn_consultar("obtenerMesa", $lc_condiciones);

} else if (isset($_GET["ipEstacion"])) {
    $lc_condiciones[0] = $_GET["est_id"];
    print $lc_config->fn_consultar("ipEstacion", $lc_condiciones);

} else if (isset($_GET["canalesImpresion"])) {
    $lc_condiciones[0] = $_GET["odp_id"];
    print $lc_config->fn_consultar("canalesImpresion", $lc_condiciones);

} else if (htmlspecialchars($_GET["enviarImpresion"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["imp_ip_estacion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["imp_url"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["imp_impresora"]);
    $lc_condiciones[3] = $usuario;
    print $lc_config->fn_consultar("enviarImpresion", $lc_condiciones);

} else if (htmlspecialchars($_GET["eliminarTextoPlu"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["dop_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["odp_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["dop_cuenta"]) ? htmlspecialchars($_GET["dop_cuenta"]) : 1;
	print $lc_config->fn_consultar("eliminarTextoPlu", $lc_condiciones);

} else if (isset($_POST["impresionDetalleCuponOrdenPedido"])) {
    $lc_condiciones[0] = $_POST["cupon"];
    $lc_condiciones[1] = $usuario;
    $lc_condiciones[2] = $control;
    $lc_condiciones[3] = $restaurante;
    $lc_condiciones[4] = $nombre_usuario;
    $lc_condiciones[5] = $_POST["mensaje"];
    $lc_condiciones[6] = $_POST["estado"];
    $lc_condiciones[7] = $_POST["cantidad"];
    $lc_condiciones[8] = $_POST["bruto"];
    $lc_condiciones[9] = $_POST["neto"];
    $lc_condiciones[10] = $_POST["iva"];
    $lc_condiciones[11] = $_POST["descriptionTipo"];
    $lc_condiciones[12] = $_POST["codigoTipo"];
    print $lc_config->fn_consultar("impresionDetalleCuponOrdenPedido", $lc_condiciones);

} else if (isset($_POST["itemsVerificaEstaImpreso"])) {
    $responseArray = [];
    foreach ($_POST["listProduct"] as $key => $value) {
        $lc_condiciones[0] = $value["id"];
        $rowCondiciones = $lc_config->fn_consultar("estaImpreso", $lc_condiciones);
        $rowCondiciones = json_decode($rowCondiciones, true);
       // var_dump($rowCondiciones);

        $final_array = array_merge($value, [
            'estaImpreso' => $rowCondiciones[0]["respuesta"],
            'strEstaImpreso' => $rowCondiciones["str"]
        ]);
        $responseArray[] = $final_array;
    }    
    print json_encode($responseArray);   
} elseif (isset($_POST["verificaPluCuponDescuentoListProductos"])) {
    $IDCabeceraOrdenPedido = $_POST["IDCabeceraOrdenPedido"];
    $responseArray = [];
    foreach ($_POST["IDDetalleOrdenPedido"] as $key => $value) {
        $verificaPluCuponDescuento = $lc_config->verificaPluCuponDescuento($IDCabeceraOrdenPedido, $value['id']);
        $final_array = array_merge($value, $verificaPluCuponDescuento);
        $responseArray[] = $final_array;
    }
    print json_encode($responseArray);
} else if (isset($_GET["estaImpreso"])) {
    $lc_condiciones[0] = $_GET["dop_id"];
    print $lc_config->fn_consultar("estaImpreso", $lc_condiciones);

} else if (htmlspecialchars($_GET["impresionOrdenPedido"])) {
    $lc_condiciones[1] = htmlspecialchars($_GET["odp_id"]);
    $lc_condiciones[2] = $usuario;
    $lc_condiciones[3] = $restaurante;
    if (isset($_GET["dop_cuenta"])) {
        $lc_condiciones[4] = $_GET["dop_cuenta"];
    } else {
        $lc_condiciones[4] = 1;
    }
    $lc_condiciones[5] = 1;
    print $lc_config->fn_consultar("impresionOrdenPedido", $lc_condiciones);

} else if (htmlspecialchars($_GET["impresionOrdenPedidoTodas"])) {
    $lc_condiciones[1] = htmlspecialchars($_GET["odp_id"]);
    $lc_condiciones[2] = $usuario;
    $lc_condiciones[3] = $restaurante;
    if (htmlspecialchars($_GET["dop_cuenta"])) {
        $lc_condiciones[4] = htmlspecialchars($_GET["dop_cuenta"]);
    } else {
        $lc_condiciones[4] = 1;
    }
    $lc_condiciones[5] = 1;
    $lc_condiciones[6] = $_GET['todas'];
    print $lc_config->fn_consultar("impresionOrdenPedidoTodas", $lc_condiciones);

} else if (htmlspecialchars($_GET["impresionPrecuenta"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["est_ipd"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["odp_id"]);
    $lc_condiciones[2] = $usuario;
    $lc_condiciones[3] = $restaurante;

    if (isset($_GET["dop_cuenta"])) {
        $lc_condiciones[4] = $_GET["dop_cuenta"];
    } else {
        $lc_condiciones[4] = 1;
    }
    $lc_condiciones[5] = $estacion;
    $lc_condiciones[6] = htmlspecialchars($_GET["opcionImpresion"]);
	print $lc_config->fn_consultar("impresionPrecuenta", $lc_condiciones);

} else if (htmlspecialchars($_GET["cargarAccesosPerfil"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["pnt_id"]);
    $lc_condiciones[1] = $usuario;
    $lc_condiciones[2] = $perfil;
    print $lc_config->fn_consultar("cargarAccesosPerfil", $lc_condiciones);

} else if (htmlspecialchars(isset($_POST["validaAdministradorBuscar"]))) {
    $lc_condiciones[1] = $perfil;
    print $lc_config->fn_consultar("validaAdministradorBuscar", $lc_condiciones);

} else if (isset($_POST["buscarValidacionesPLU"])) {
    $lc_condiciones[0] = isset($_POST["plu_id"]) ? $_POST["plu_id"] : 0;
    $lc_condiciones[1] = $cdn_id;
    print $lc_config->fn_consultar("buscarValidacionesPLU", $lc_condiciones);

} else if (isset($_POST["split_cuentas"])) {
    $lc_condiciones[0] = 1;
    $lc_condiciones[1] = $_POST["mesa_id"];
    $lc_condiciones[2] = $_POST["estado"];
    print $lc_config->fn_consultar("split_cuentas", $lc_condiciones);

} else if (htmlspecialchars($_GET["informacionMesa"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["odp_id"]);
    $lc_condiciones[1] = $ip;
    $lc_condiciones[2] = $_GET["IDMesa"];
    print $lc_config->fn_consultar("informacionMesa", $lc_condiciones);

} else if (isset($_GET["informacionMesaAll"])) {
    $lc_condiciones[0] = $_GET["odp_id"];
    print $lc_config->fn_consultar("informacionMesaAll", $lc_condiciones);

} else if (isset($_GET["dividirProductos"])) {
    $lc_condiciones[0] = $_GET["accion"];
    $lc_condiciones[1] = $_GET["odp_id"];
    $lc_condiciones[2] = $_GET["IDMesa"];
    $lc_condiciones[3] = $_GET["cantidad"];
    print $lc_config->fn_consultar("dividirProductos", $lc_condiciones);

} else if (htmlspecialchars($_GET["obtenerDatosRegresar"])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["mesa_id"]);
    $lc_condiciones[1] = $estacion;
    $lc_condiciones[2] = isset($_GET["idOrden"]) ? $_GET["idOrden"] : '';
    print $lc_config->fn_consultar("obtenerDatosRegresar", $lc_condiciones);

} else if (isset($_GET["obtenerDatosRegresarG"])) {
    $lc_condiciones[0] = $_GET["mesa_id"];
    $lc_condiciones[1] = $_GET["rst_id"];
    $lc_condiciones[2] = $_GET["IDUserPos"];
    print $lc_config->fn_consultar("obtenerDatosRegresarG", $lc_condiciones);

} else if (isset($_GET["guardarCuenta"])) {
    $lc_condiciones[0] = $_GET["mesa_id"];
    $lc_condiciones[1] = $_GET["idEstacion"];
    $lc_condiciones[2] = $_GET["IDUsersPos"];
    $lc_condiciones[3] = $_GET["rst_id"];
    print $lc_config->fn_consultar("guardarCuenta", $lc_condiciones);

} else if (isset($_GET["validacionGuardanOrden"])) {
    $lc_condiciones[0] = $_GET["rst_id"];
    $lc_condiciones[1] = $_GET["odp_id"];
    print $lc_config->fn_consultar("validacionGuardanOrden", $lc_condiciones);

} else if (isset($_GET["ActualizaEstadosDop"])) {
    $lc_condiciones[0] = $_GET['opcion'];
    $lc_condiciones[1] = $_GET["odp_id"];
    $lc_condiciones[2] = $_GET["dop_cuenta"];
    print $lc_config->fn_ActualizaEstadosDop("ActualizaEstadosDop", $lc_condiciones);

} else if (isset($_POST["limite_min_max"])) {
    $lc_condiciones[0] = $_POST["rst_id"];
    print $lc_config->fn_consultar("limite_min_max", $lc_condiciones);

} else if (htmlspecialchars($_GET['NumeroPersonas'])) {
    $lc_condiciones[0] = htmlspecialchars($_GET['opcion']);
    $lc_condiciones[1] = htmlspecialchars($_GET['odp_id']);
    $lc_condiciones[2] = 0;
    print $lc_config->fn_ActualizaEstadosDop("CargaNumPersonas", $lc_condiciones);

} else if (isset($_POST['cargarClienteDistrib'])) {
    $lc_condiciones[0] = $_POST['cdn_id'];
    $lc_condiciones[1] = $_POST['rst_id'];
    $urlWSRetornaPrecios = $servicioWebObj->retorna_rutaWS($lc_condiciones[1], 'VOUCHER', 'OBTIENE CLIENTES');
    $urlWSRetornaPrecios = $urlWSRetornaPrecios["urlwebservice"] . "cdn_id=" . $lc_condiciones[0]; //$urlWSRetornaPrecios["urlwebservice"] . "cdn_id=" . $lc_condiciones[0] . "&rst_id=" . $lc_condiciones[1] . "";

    $resultJSON = $objExecuteWs->executeWs($urlWSRetornaPrecios);
    $respuesta["respuesta"] = json_decode($resultJSON, true);
    $respuesta["str"] = count(json_decode($resultJSON, true));
    print json_encode(($respuesta));

} else if (isset($_POST['VisualizaBoton'])) {
    $lc_condiciones[0] = $restaurante;
    $lc_condiciones[1] = $estacion;
    print $lc_config->fn_consultar("VisualizaBoton", $lc_condiciones);

} else if (isset($_POST['verificarPoliticaCodigo'])) {
    $lc_condiciones[0] = $restaurante;
    $lc_condiciones[1] = $_POST['idFormaPago'];
    print $lc_config->fn_consultar("verificarPoliticaCodigo", $lc_condiciones);

} else if (isset($_POST['ConsultaClienteExterno'])) {
    $lc_condiciones[0] = $_POST['cdn_id'];
    $lc_condiciones[1] = $_POST['rst_id'];
    $urlWSRetornaPrecios = $servicioWebObj->retorna_rutaWS($lc_condiciones[1], 'VOUCHER', 'OBTIENE FP');
    $urlWSRetornaPrecios = $urlWSRetornaPrecios["urlwebservice"] . "cdn_id=" . $lc_condiciones[0] . "&rst_id=" . $lc_condiciones[1] . "";
    $resultJSON = $objExecuteWs->executeWs($urlWSRetornaPrecios);

    $respuesta["respuesta"] = json_decode($resultJSON, true);
    $respuesta["str"] = count(json_decode($resultJSON, true));
    print json_encode(($respuesta));

} else if (isset($_POST['obtienePatronesVocuher'])) {
    $lc_condiciones[0] = 1;
    $lc_condiciones[1] = $_POST['cdn_id'];
    $lc_condiciones[2] = '';
    print $lc_config->fn_ActualizaEstadosDop("obtienePatronesVocuher", $lc_condiciones);

} else if (isset($_POST['obtieneClienteSegunPatronesVocuher'])) {
    $lc_condiciones[0] = 2;
    $lc_condiciones[1] = $_POST['cdn_id'];
    $lc_condiciones[2] = $_POST['IDColeccionDeDatosPais'];
    print $lc_config->fn_ActualizaEstadosDop("obtieneClienteSegunPatronesVocuher", $lc_condiciones);

} else if (isset($_POST['obtenerTiposdeCupo'])) {
    $lc_condiciones[0] = $_POST['cdn_id'];
    $lc_condiciones[1] = $_POST['documento_cliente'];
    print $lc_config->fn_ActualizaEstadosDop("obtenerTiposdeCupo", $lc_condiciones);

} else if (isset($_POST['queAlcanza'])) {
    $lc_condiciones[0] = $_POST['rst_id'];
    print $lc_config->fn_ActualizaEstadosDop("queAlcanza", $lc_condiciones);

} else if (isset($_GET['FiltroQueMeAlcanza'])) {
    $lc_condiciones[0] = $_GET['rst_id'];
    $lc_condiciones[1] = $_GET['cla_id'];
    $lc_condiciones[2] = $_GET['num_puntos'];
    print $lc_config->fn_ActualizaEstadosDop("FiltroQueMeAlcanza", $lc_condiciones);

} else if (isset($_GET['transferirMesas'])) {
    $lc_condiciones[0] = $_GET['mesa_id_origen'];
    $lc_condiciones[1] = $_GET['nombre_mesa_destino'];
    $lc_condiciones[2] = $_GET['IDPeriodo'];
    print $lc_config->fn_transferirMesas('transferirMesas', $lc_condiciones);

} else if (htmlspecialchars($_GET['cancelarOrdenPedido'])) {
    $_SESSION['fdznDocumento'] = null;
    $_SESSION['fdznNombres'] = null;

    $_SESSION['vitality'] = null;
    $_SESSION['idClienteVitality'] = null;
    $_SESSION['balanceVitality'] = null;

    $lc_condiciones[0] = htmlspecialchars($_GET['odp_id']);
	print $lc_config->fn_consultar("cancelarOrdenPedido", $lc_condiciones);

} else if (isset($_POST['insertaOrdenPedidoFidelizacion'])) {
    $lc_condiciones[0] = $_POST['odp_id'];
    $lc_condiciones[1] = $_SESSION['fb_document'];
    print $lc_config->fn_ActualizaEstadosDop("insertaOrdenPedidoFidelizacion", $lc_condiciones);

} else if (htmlspecialchars(isset($_POST["visualizarCantidadProducto"]))) {
    $lc_condiciones[0] = "1";
    $lc_condiciones[1] = $restaurante;
    print $lc_config->visualizarCantidadProducto($lc_condiciones);

} else if (htmlspecialchars(isset($_POST["insertarBeneficiosOrdenPedido"]))) {
    $param[0] = 1;
    $param[1] = htmlspecialchars($_POST['idCabeceraOrdenPedido']);
    $param[2] = htmlspecialchars($_POST['Id_Promociones']);
    $param[3] = htmlspecialchars($_POST['idUsuario']);
    $param[4] = $restaurante;
    $param[5] = '';
    $param[6] = htmlspecialchars($_POST["dop_cuenta"]);
    $param[7] = $_POST["beneficioPromocion"];
    print $lc_config->insertarBeneficiosOrdenPedido($param);

} else if (htmlspecialchars(htmlspecialchars($_GET["ValidarPromocion"]))) {
    $lc_condiciones[0] = htmlspecialchars($_GET["opcion"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["odp_id"]);
    $lc_condiciones[2] = htmlspecialchars($_GET["dop_cuenta"]);
	print $lc_config->fn_ValidarPromocion("ValidarPromocion", $lc_condiciones);

} else if (htmlspecialchars(isset($_GET["statusPromociones"]))) {
    $lc_condiciones[0] = $_GET["cdn_id"];
    $lc_condiciones[1] = $_GET["rst_id"];
    print $lc_config->habilitarBotonQR($lc_condiciones[0], $lc_condiciones[1]);

} else if (isset($_POST['guadarDescripcionVoucher'])) {
    $lc_condiciones[0] = $_POST['opcion'];
    $lc_condiciones[1] = $_POST['IDCabeceraOrdenPedido'];
    $lc_condiciones[2] = $_POST['observacion'];
    print $lc_config->fn_ActualizaEstadosDop('guadarDescripcionVoucher', $lc_condiciones);

} else if (isset($_POST['verificarPoliticaTipoVenta'])) {
    $restaurante = $_SESSION['rstId'];
    print $lc_config->fn_VerificarEstadoTipoVenta($restaurante);

} else if (isset($_POST['ConsultarCanalVenta'])) {
    $restaurante = $_SESSION['rstId'];
    print $lc_config->fn_ConsultarCanalVenta($restaurante);

} else if (isset($_POST['insertarTipoCanalVenta'])) {
    $restaurante = $_SESSION['rstId'];
    $idcabecerapedido = $_POST['cabeceraordenpedido'];
    $idTipoPedido = $_POST['idTipoVentaCanal'];
    print $lc_config->fn_insertarTipoPedido($restaurante, $idcabecerapedido, $idTipoPedido);

}else if (isset($_POST['insertarCodigoBarras'])) {
    //$restaurante = $_SESSION['rstId'];
    $idcabecerapedido = $_POST['cabeceraordenpedido'];
    //$idCadena = $_SESSION['cadenaId'];
    $codigoBarras = $_POST['codigo_barras'];
    print $lc_config->fn_insertarCodigoBarras($idcabecerapedido,$codigoBarras);
    
}else if (isset($_POST['borrarCodigoBarras'])) {
    //$restaurante = $_SESSION['rstId'];
    $idcabecerapedido = $_POST['cabeceraordenpedido'];
    //$idCadena = $_SESSION['cadenaId'];
    $codigoBarras = $_POST['codigo_barras'];
    print $lc_config->fn_borrarCodigoBarras( $idcabecerapedido,$codigoBarras);




} else if (htmlspecialchars(isset($_POST['solicitudHorneado']))) {
    $accion = 1;
    $idCadena = $cdn_id;
    $plu_id = htmlspecialchars($_POST["plu_id"]);
    $idControlEstacion = htmlspecialchars($_POST["idControlEstacion"]);
    $idEstacion = htmlspecialchars($_POST["idEstacion"]);
    print $lc_config->solicitudHorneado($accion, $idCadena, $plu_id, $idControlEstacion, $idEstacion);

}else if (isset($_POST['guadarDescripcionVoucher'])) {
    $lc_condiciones[0] = $_POST['opcion'];
    $lc_condiciones[1] = $_POST['IDCabeceraOrdenPedido'];
    $lc_condiciones[2] = $_POST['observacion'];
    print $lc_config->fn_ActualizaEstadosDop('guadarDescripcionVoucher', $lc_condiciones);

} else if (htmlspecialchars($_GET['ValidaSecuencial'])) {
    $lc_condiciones[0] = htmlspecialchars($_GET['rst_id']);
    print $lc_config->fn_consultar("ValidaSecuencial", $lc_condiciones);

} else if (isset($_POST['obtenerTotalOrdenPedido'])) {
    $idOrdenPedido = $_POST['idOrdenPedido'];
    print $lc_config->obtenerTotalOrdenPedido($idOrdenPedido);

} else if (isset($_POST['desactivarCanjeCuponOrdenMasterData'])) {
    /*
      desactivarCanjeCuponOrdenAzure: 1
      idOrden: E86A0BE4-14E8-E811-810E-00505686417C
      idDetalleOrden: CD1EC5CF-27E8-E811-810E-00505686417C
     */
    $respuestaError = (object) [
                "estado" => "error",
                "mensaje" => "No se pudo desactivar el canje"
    ];

    if (!(isset($_POST['idOrden']) && ($_POST['idOrden'] !== ''))) {
        $respuestaError->mensaje = "Falta el parámetro 'idOrden'";
        $promocionesControllerObj->enviarRespuestaJson($respuestaError);
    }

    if (!(isset($_POST['idDetalleOrden']) && ($_POST['idDetalleOrden'] !== ''))) {
        $respuestaError->mensaje = "Falta el parámetro 'idDetalleOrden'";
        $promocionesControllerObj->enviarRespuestaJson($respuestaError);
    }

    $idOrden = $_POST['idOrden'];
    $idDetalleOrden = $_POST['idDetalleOrden'];

    $conexionTienda = $conexionDinamica->conexionTienda();
    $promocionesControllerObj = new PromocionesController($conexionTienda);
    $parametrosDesactivar = ["idOrden" => $idOrden, "idDetalleOrden" => $idDetalleOrden];
    $respuestaDesactivacion = $promocionesControllerObj->desactivarCanjeCuponOrdenMasterData($parametrosDesactivar);
    $promocionesControllerObj->enviarRespuestaJson($respuestaDesactivacion);

} else if (isset($_POST['desactivarCanjeCuponOrdenTextoMasterData'])) {
    /*
      desactivarCanjeCuponOrdenAzure: 1
      idOrden: E86A0BE4-14E8-E811-810E-00505686417C
      idDetalleOrden: CD1EC5CF-27E8-E811-810E-00505686417C
     */
    $respuestaError = (object) [
                "estado" => "error",
                "mensaje" => "No se pudo desactivar el canje"
    ];

    if (!(isset($_POST['idOrden']) && ($_POST['idOrden'] !== ''))) {
        $respuestaError->mensaje = "Falta el parámetro 'idOrden'";
        $promocionesControllerObj->enviarRespuestaJson($respuestaError);
    }

    if (!(isset($_POST['idCupon']) && ($_POST['idCupon'] !== ''))) {
        $respuestaError->mensaje = "Falta el parámetro 'idCupon'";
        $promocionesControllerObj->enviarRespuestaJson($respuestaError);
    }

    $idOrden = $_POST['idOrden'];
    $idDetalleOrden = $_POST['idCupon'];

    $conexionTienda = $conexionDinamica->conexionTienda();
    $promocionesControllerObj = new PromocionesController($conexionTienda);
    $parametrosDesactivar = ["idOrden" => $idOrden, "idCupon" => $idDetalleOrden];
    $respuestaDesactivacion = $promocionesControllerObj->desactivarCanjeCuponOrdenTextoMasterData($parametrosDesactivar);
    $promocionesControllerObj->enviarRespuestaJson($respuestaDesactivacion);

// Kiosko y Pickup
} else if (isset($_POST['retomarOrdenKiosko'])) {
    $Factura = $_POST['codigoFactura'];
    $_SESSION['reimpresionKiosko'] = $_POST['reimpresionkiosko'];
    $tipoOrden = $_POST['tipoOrden'];
    $respuesta = $lc_config->fn_retomarOrdenKiosko($Factura, $ip, $usuario, $tipoOrden);

    $resp = new stdClass();
    $resp = json_decode($respuesta, true);
    if ($resp[0]['mensajes'] == "") {
        if ($tipoOrden == 'PICKUP') {
            $_SESSION['pickupActivo'] = 1;
            $_SESSION['kioskoActivo'] = 0;
        } else {
            $_SESSION['kioskoActivo'] = 1;
            $_SESSION['pickupActivo'] = 0;
        }
    } else {
        $_SESSION['kioskoActivo'] = 0;
        $_SESSION['pickupActivo'] = 0;
    }
    print $respuesta;

} else if (isset($_POST['anularOrdenKioskoMaxpoint'])) {
    $Factura = $_POST['codigoFactura'];
    $respuesta = $lc_config->fn_anularOrdenKioskoMaxpoint($Factura, $usuario);
    $resp = new stdClass();
    $resp = json_decode($respuesta, true);
    print $respuesta;

} else if (isset($_POST['cargarPedidosEfectivo'])) {
    print $lc_config->fn_cargarPedidosEfectivo($cdn_id, $restaurante);
} else if (isset($_POST["verificarCodigoOrdenApp"])) {
    $codigo = $_POST["codigo"];
    $longCod = $lc_config->fn_verificarLongitudCodigoOrdenApp($cdn_id);
    $codApp = str_pad($codigo, $longCod, "0", STR_PAD_LEFT) . $lc_config->fn_verificarCodigoOrdenApp($cdn_id);
    print json_encode($codApp);
} else if (isset($_POST["lecturaCodigosManualPickup"])) {
    $rst_id = $_POST['rst_id'];
    $statusCodPickup = $lc_config->fn_verificarlecturaCodigosManualPickup($cdn_id, $rst_id);
    $codApp = $statusCodPickup;
    print json_encode($codApp);

// Cargar Url de Valida correo Plugthem
} else if (isset($_POST['cargarUrlApiValidaPlugthem'])) {
    $datosWebservice = $lc_config->fn_cargarUrlApiValidaPlugthem($restaurante);
    $url= $datosWebservice["url"];
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_AUTOREFERER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>json_encode(array(
            "CustomerEmail" => $_POST['CustomerEmail'],
        )),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Accept: application/json'        ),
    ));
    $response = curl_exec($curl);
    if (curl_error($curl)) {
        $response = json_encode(["status"=>"error", "message"=>curl_error($curl)]);
    }
    curl_close($curl);
    print $response;

} else if (isset($_POST['cancelarOrdenKiosko'])) {
    $ordenPedido = $_POST['ordenPedido'];
    $_SESSION['kioskoActivo'] = null;
    $_SESSION['pickupActivo'] = null;
    print $lc_config->fn_cancelarOrdenKiosko($ordenPedido);

} else if (isset($_POST['PayPhoneFormularioEnOrdenPedido'])) {
    $lc_condiciones[0] = $restaurante;
    $lc_condiciones[1] = $_POST['IDUsuario'];
    print $lc_config->fn_consultar("PayPhoneFormularioEnOrdenPedido", $lc_condiciones);
} else if (isset($_POST['PayPhoneCargarTipoInmueble'])) {
    $lc_condiciones[0] = $cdn_id;
    print $lc_config->fn_consultar("PayPhoneCargarTipoInmueble", $lc_condiciones);

} else if (isset($_POST['actualizarOrdenPedidoApp'])) {
    $lc_condiciones[0] = $_POST['odp_id'];
    $lc_condiciones[1] = $_POST['cliente'];
    $lc_condiciones[2] = $_POST['telefono'];
    $lc_condiciones[3] = $_POST['direccion'];
    $lc_condiciones[4] = $_POST['direccion2'];
    $lc_condiciones[5] = $_POST['cedulaCliente'];
    $lc_condiciones[6] = $_POST['accion'];
    $lc_condiciones[7] = $_POST['observaciones'];
    $lc_condiciones[8] = $cajero;
    if (isset($_POST['medio'])){
        $lc_condiciones[9] = $_POST['medio'];
    }else{
        $lc_condiciones[9] = '';
    }
    $lc_condiciones[10] = $_POST['tipoInmueble'];
    $lc_condiciones[11] = $_POST['numeroCallePrincipal'];
    $lc_condiciones[12] = $_POST['numeroInmueble'];
    $lc_condiciones[13] = $_POST['observaciones'];
    if (isset($_POST['latitud'])){
        $lc_condiciones[14] = $_POST['latitud'];
    }else{
        $lc_condiciones[14] = '';
    }
    if (isset($_POST['longitud'])){
        $lc_condiciones[15] = $_POST['longitud'];
    }else{
        $lc_condiciones[15] = '';
    }
    $lc_condiciones[16] = $_POST['acepta_beneficio'];
    $lc_condiciones[17] = $_POST['email'];

        if( isset( $_POST['accion'] ) && $_POST['accion'] == 'A' )
        {

            if( isset( $_POST["argvTDI"] ) && $_POST["argvTDI"] != 'CONSUMIDOR FINAL' )
            {

                if( isset( $_SESSION['rstId'] ) && $_SESSION['rstId'] != '' && $_SESSION['rstId'] != null )
                {

                    $lcConfig = new menuPedido();

                    $auxBinPHP = $lcConfig->rutaBinarioPHP( $_SESSION['rstId'] );

                    if( $auxBinPHP["str"] === 1 && $auxBinPHP["rutaBinarioPHP"] != '' && $auxBinPHP["rutaBinarioPHP"] != null )
                    {
                       
                        // ejecutarSistemaDeDatosCentralizados
                        $ventana                        = 'start ';
                        $titulo                         = '"SistemaDeDatosCentralizados" ';
                        $parametrosStart                = '/b ';
                        $binPHP                         = trim( $auxBinPHP["rutaBinarioPHP"] ).' ';
                        $parametrosPHP                  = '-f ';
                        $SistemaDeDatosCentralizados    = '"'.str_replace( 'ordenpedido\config_ordenPedido.php', '', str_replace( '/', '\\', $_SERVER['SCRIPT_FILENAME'] ) ) . 'facturacion\sistemaDeDatosClienteCentralizados.php'.'" ';
                        $argumentosSDC                  = '-- ';
                        $argvdocumentoIdentidad         = '"'.$_POST["cedulaCliente"].'" ';                                                                  
                        $argvtipoDocumentoIdentidad     = '"'.$_POST["argvTDI"].'" '; 
                        $argvnombres                    = '"'.$_POST["cliente"].'" ';                                                                          
                        $argvcorreoElectronico          = '"'.$_POST["email"].'" ';                                                                           
                        $argvnumeroTelefono             = '"'.$_POST["telefono"].'" ';                                                                          
                        $argvautorizacion               = '"'.'PL'.'" ';
                        $argvsession_name               = '"'.session_name().'" ';
                        $argvsession_id                 = '"'.session_id().'" ';
                        $argvOperacion                  = '"'.'L.A.'.'" ';
                        $salidaSDC                      = '1> nul 2> nul';
                                                        
                        $sentencia = $ventana.$titulo.$parametrosStart.$binPHP.$parametrosPHP.$SistemaDeDatosCentralizados.$argumentosSDC.$argvdocumentoIdentidad.$argvtipoDocumentoIdentidad.$argvnombres.$argvcorreoElectronico.$argvnumeroTelefono.$argvautorizacion.$argvsession_name.$argvsession_id.$argvOperacion.$salidaSDC;
                        //Ejecuta la sentencia en un hilo hijo. El hilo padre no tendrá que esperar por el hilo hijo para concluir su proceso.
                        /*if( strtoupper( substr( PHP_OS, 0, 3 ) ) === 'WIN' )
                        {

                            pclose( popen( $sentencia, 'r' ) );

                        }
                        else 
                        {
                            //  UNIX
                            //          Definir binario PHP                         
                            $sentencia = $binPHP.$parametrosPHP.$SistemaDeDatosCentralizados.$argumentosSDC.$argvdocumentoIdentidad.$argvtipoDocumentoIdentidad.$argvnombres.$argvcorreoElectronico.$argvnumeroTelefono.$argvautorizacion.$argvsession_name.$argvsession_id.$argvOperacion;

                            exec( $sentencia . " > /dev/null &");
                        
                        }*/
                        //

                    }

                }

            }
            
        }

    $actualizaOrdenPedido = $lc_config->fn_consultar("actualizarOrdenPedidoApp", $lc_condiciones);
    if($lc_condiciones[14] !== "" && $lc_condiciones[15] !== "" ){
        $lc_condiciones_LatLon[0] = $_POST['odp_id'];
        $lc_condiciones_LatLon[1] = $lc_condiciones[14];
        $lc_condiciones_LatLon[2] = $lc_condiciones[15];
        print $lc_config->fn_consultar("actualizarCabOrdPedLatLon", $lc_condiciones_LatLon);
    }
    print $actualizaOrdenPedido;
    
} else if (isset($_POST['actualizarOrdenPedidoAppFac'])) {

    $lc_condiciones[0] = $_POST['odp_id'];
    $lc_condiciones[1] = $_POST['nombre'];
    $lc_condiciones[2] = "";
    $lc_condiciones[3] = "";
    $lc_condiciones[4] = "";
    $lc_condiciones[5] = "";
    $lc_condiciones[6] = "FP";
    $lc_condiciones[7] =  $_POST['cfac_id'];
    $lc_condiciones[8] = $cajero;

    print $lc_config->fn_consultar("actualizarOrdenPedidoApp", $lc_condiciones);

} else if (isset($_POST['obtieneInformacionFormulario'])) {
    $lc_condiciones[0] = $_POST['odp_id'];
    print $lc_config->fn_consultar("obtieneInformacionFormulario", $lc_condiciones);
} else if (isset($_POST['ValidarFacturaTarjeta'])) {
    $cabecera_odp = $_POST["odp_id"];
    print $lc_config->fn_ValidarFacturaTarjeta($cabecera_odp);
}else if (isset($_POST['ValidarControlEstacionActivo'])) {
    $lc_condiciones['est_id'] = $_POST['hide_est_id'];
    $lc_condiciones['idControlEstacion'] = $_POST['idControlEstacion'];
    $lc_condiciones['cdn_id'] = $_POST['hide_cdn_id'];
    $lc_condiciones['IDPeriodo'] = $_SESSION['IDPeriodo'];
    $respuesta=$lc_config->fn_ValidarControlEstacionActivo($lc_condiciones);
    echo json_encode(array('repuesta'=>$respuesta));
    unset($lc_condiciones,$respuesta);
} else if (isset($_POST['ValidarControlEstacionActivoEliminar'])) {
    $lc_condiciones['IDCabeceraOrdenPedido'] = $_POST['IDCabeceraOrdenPedido'];
    $respuesta=$lc_config->fn_eliminar_Cabecera_Orden_Pedido($lc_condiciones);
    echo json_encode(array('repuesta'=>$respuesta));
    unset($lc_condiciones,$respuesta);
}else if (isset($_POST['mostrarBotonCobrarEnEstacionTomaPedido'])) {
    $lc_condiciones[0] = $_POST['IDEstacion'];
    $lc_condiciones[1] = $_POST['rst_id'];
    print $lc_config->fn_consultar("mostrarBotonCobrarEnEstacionTomaPedido", $lc_condiciones);
}
else if (isset($_POST['filtrarPedidosMedios'])) {
    $lc_condiciones[0] = $_POST['accion'];
    $lc_condiciones[1] = $cdn_id;
    $lc_condiciones[2] = $estacion;
    print $lc_config->fn_filtrarPedidosMedios($lc_condiciones);
}

else if (htmlspecialchars($_GET['politicaPayCard'])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cdn_id"]);
    $lc_condiciones[1] = 0;
    print $lc_config->fn_consultar("politicaPayCard", $lc_condiciones);
} else if (htmlspecialchars($_GET['obtenerProductoPaycard'])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cdn_id"]);
    $lc_condiciones[1] = htmlspecialchars($_GET["plu_id"]);
    $lc_condiciones[2] = 0;
    print $lc_config->fn_consultar("obtenerProductoPaycard", $lc_condiciones);
}
else if (htmlspecialchars($_GET['politicaPayCardRestaurante'])) {
    $lc_condiciones[0] = htmlspecialchars($_GET["cdn_id"]);
    $lc_condiciones[1] = 0;
    print $lc_config->fn_consultar("politicaPayCardRestaurante", $lc_condiciones);
}else if (htmlspecialchars($_GET["verificarCodigoBarras"])) {
    $lc_condiciones[0] = $usuario;
    $lc_condiciones[1] = "";
    $lc_condicionesWS[0] = $cdn_id;
    $lc_condicionesWS[1] = $usuario;
    $lc_condicionesWS[2] = htmlspecialchars($_GET["rst_id"]);
    $codigoBarras = htmlspecialchars($_GET["codigoBarras"]);
    $restaurante = htmlspecialchars($_GET["rst_id"]);
    
    $datoUsuario=$lc_config->fn_consultar("retorna_cedula_user",$lc_condicionesWS);
    $cedula_user=$datoUsuario["cedula_user"];
        
    $datosWebservice=$lc_config->fn_consultar("retorna_WS_URL_Cashless",$lc_condicionesWS);
    $url=$datosWebservice["urlwebservice"];
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 1,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>json_encode(array(
            "accion" => 1,
            "restaurante"=> $restaurante,
            "codigo_barra"=> $codigoBarras,
            "nombre_user"=> $_SESSION['nombre'],
            "cedula_user"=> $cedula_user
        )),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    if (curl_error($curl)) {
        $response = ["status"=>"error", "message"=>curl_error($curl)];
        $lc_condiciones[1] = "Error Cambio de EStado Producto:".curl_error($curl);
        $lc_config->fn_consultar("insertaAuditoria", $lc_condiciones);
    }else{
        $lc_condiciones[1] = "Cambio de EStado Producto:".$response;
        $lc_config->fn_consultar("insertaAuditoria", $lc_condiciones);
    }
    curl_close($curl);
    print $response;
}
else if (htmlspecialchars($_GET["desactivarCodigoBarras"])) {
    $lc_condiciones[0] = $usuario;
    $lc_condiciones[1] = "";
    $lc_condicionesWS[0] = $cdn_id;
    $lc_condicionesWS[1] = $usuario;
    $lc_condicionesWS[2] = htmlspecialchars($_GET["rst_id"]);
    $codigoBarras = htmlspecialchars($_GET["codigoBarras"]);
    $restaurante = htmlspecialchars($_GET["rst_id"]);
    
    $datoUsuario=$lc_config->fn_consultar("retorna_cedula_user",$lc_condicionesWS);
    $cedula_user=$datoUsuario["cedula_user"];
        
    $datosWebservice=$lc_config->fn_consultar("retorna_WS_URL_Cashless",$lc_condicionesWS);
    $url=$datosWebservice["urlwebservice"];
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 1,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>json_encode(array(
            "accion" => 0,
            "restaurante"=> $restaurante,
            "codigo_barra"=> $codigoBarras,
            "nombre_user"=> $_SESSION['nombre'],
            "cedula_user"=> $cedula_user
        )),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    if (curl_error($curl)) {
        $response = ["status"=>"error", "message"=>curl_error($curl)];
        $lc_condiciones[1] = "Error Cambio de EStado Producto:".curl_error($curl);
        $lc_config->fn_consultar("insertaAuditoria", $lc_condiciones);
    }else{
        $lc_condiciones[1] = "Cambio de Estado Producto Cashless:".$response;
        $lc_config->fn_consultar("insertaAuditoria", $lc_condiciones);
    }
    curl_close($curl);
    print $response;
}
else if( isset( $_POST["IDCabeceraOrdenPedidoCFOP"] ) ) 
{
    $IDCabeceraOrdenPedido = $_POST["IDCabeceraOrdenPedidoCFOP"];

    print $lc_config->condicionFacturacionOrdenPedido( $IDCabeceraOrdenPedido );
}
elseif( isset( $_POST["verificaPluCuponDescuento"] ) ) 
{
    $IDCabeceraOrdenPedido = $_POST["IDCabeceraOrdenPedido"];
    $IDDetalleOrdenPedido = $_POST["IDDetalleOrdenPedido"];

    print json_encode( $lc_config->verificaPluCuponDescuento( $IDCabeceraOrdenPedido, $IDDetalleOrdenPedido ) );
}
else if( isset( $_POST["activarEsDomicilio"] ) )
{   
    $cadena = $cdn_id;
    print $lc_config->activarEsDomicilio( $cadena );
}
else if( isset( $_POST["ObtenerCliente"] ) )
{   
    $cadena = $cdn_id;
    $idRestaurante = $restaurante;
    $cedula = $_POST['cedula'];
    $usuario = $_POST['usuario'];
    if (isset($_POST["tipoInmueble"])){
        $tipoInmueble = $_POST["tipoInmueble"];
    }else{
        $tipoInmueble = '';
    }
    if (isset($_POST["accion"])){
        $accion = $_POST["accion"];
    }else{
        $accion = '';
    }
    if (isset($_POST["datos"])){
        $datos = $_POST["datos"];
    }else{
        $datos = '';
    }
    $odp_id = $_POST['odp_id'];
    print $lc_config->ObtenerGuardarCliente( $cadena, $idRestaurante, $cedula , $accion, $datos, $usuario, $tipoInmueble, $odp_id );
}
else if( isset( $_POST["obtenerBeneficioCliente"] ) )
{   
    $cadena = $cdn_id;
    $idRestaurante = $restaurante;

    $lc_condiciones[0] = $_POST["cat_id"];
    $lc_condiciones[1] = $_POST["odp_id"];
    $lc_condiciones[4] = $_POST["menuId"];
    $lc_condiciones[5] = 1;
    $lc_condiciones[6] = 0;
    $lc_condiciones[7] = $_POST["idProductoBase"];
    $lc_condiciones[8] = $_POST["admin"];
    $lc_condiciones[9] = $_POST["cuenta"];

    print $lc_config->obtenerBeneficioCliente( $cadena, $lc_condiciones );
}
else if( isset( $_POST["obtenerBeneficioPluId"] ) )
{   
    print $lc_config->agregarBeneficioCliente( $cdn_id )["producto"];
}
else if( isset( $_POST["guardarVariables"] ) )
{
    $lc_condiciones[0] = $_POST["odp_id"];
    $lc_condiciones[1] = $_POST["cuenta"];
    $lc_condiciones[2] = $_POST["json"];
    print $lc_config->guardarVariables( $lc_condiciones );
}
else if( isset( $_POST["obtenerVariables"] ) )
{
    $lc_condiciones[0] = $_POST["odp_id"];
    print $lc_config->obtenerVariables( $lc_condiciones );
}
else if( isset( $_POST["consultarTimeOut"] ) )
{
    try {
        $datosWebservice = $servicioWebObj->buscarTimeOut($cdn_id);

        print json_encode($datosWebservice);
    } catch (Exception $e) {
        print json_encode($e);
    }
} else if (isset($_GET["temporalSession"])) {
    
    $_SESSION['tmp_menu'] = $_GET["menu"];
    $_SESSION['tmp_clacificacion'] = $_GET["clacificacion"];
    $_SESSION['tmp_categoria'] = $_GET["categoria"];
    $_SESSION['tmp_item'] = $_GET["item"];
    $_SESSION['tmp_quantity'] = $_GET["quantity"];

    print true;
}
else if( isset( $_POST["ValidarRevocatoria"] ) )
{
    try {
        $cliente = $_POST["cliente"];

        $datos = $lc_config->validarRevocatoria($cdn_id, $restaurante, $cliente);

        print json_encode($datos);
    } catch (Exception $e) {
        print json_encode($e);
    }
} else if( isset( $_POST["verificarSiLaOrdenEsDeAgregador"] ) )
{
   $lc_condiciones[0] = $_POST["odp_id"];
   print $lc_config->fn_consultar("verificarSiLaOrdenEsDeAgregador", $lc_condiciones);

}
else if( isset( $_POST["consultarCodigoAppedirMasivo"])){
    try {
        $headers = apache_request_headers();$headers = apache_request_headers();
		if (isset($headers['Authorization'])) {
			$barer = explode(' ', $headers['Authorization']);
			$authorization = $barer[1];
		}else{
			$authorization=$_POST['authorization'];
		}

        $codigo = $_POST['codigo'];

        $_SESSION['marcaMasivo']=$_POST['marca'];
        $_SESSION['estadoMasivo']=$_POST['estadoMasivo'];
        $_SESSION['documentoCliente']=$_POST['documentoCliente'];

        $cadena = $lc_config->fn_obtenerCandena();

        if($cadena > 0){

            print $lc_config->fn_ejecutaLlamadoMasivo($cadena,$codigo,$authorization);
        }

    } catch (\Exception $e) {
        print json_encode($e);
    }

    
}else if (isset($_GET['GetUpselling'])) {
    $url= 'http://localhost:5004';
    if (stripos(PHP_OS, 'Linux') !== false || file_exists('/etc/os-release')) {
        $url= 'http://upselling-api:5004';
    } 
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_AUTOREFERER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Accept: application/json'        ),
    ));
    $response = curl_exec($curl);
    if (curl_error($curl)) {
        $response = json_encode(["status"=>"error", "message"=>curl_error($curl)]);
    } else {
        $response = json_encode(["status"=>"success", "message"=>"FUNCIONANDO"]);
    }
    curl_close($curl);
    print $response;
} else if(isset($_POST["nombrarPicada"])) {
    print $lc_config->nombrarPicada($_POST["nombrarPicada"], $_POST["IDCabeceraOrdenPedido"], $_POST["nombrePicada"]);
}