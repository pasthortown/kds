<?php

////////////////////////////////////////////////////////////////////////////////////////////
////////MODIFICADO POR: CHRISTIAN PINTO/////////////////////////////////////////////////////
////////DECRIPCION: NUEVOS ESTILOS INGRESO Y ACTUALIZACION FORMAS DE PAGO///////////////////
///////////////////////////////// TABLA MODAL, COLECCION DE DATOS ATRIBUTO FORMA PAGO///////
////////TABLAS INVOLUCRADAS: Formapago,Cadena///////////////////////////////////////////////
////////FECHA CREACION: 09/06/2015//////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////

include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_formaspago.php';

if (empty($_SESSION['rstId']) OR empty($_SESSION['usuarioId']) OR empty($_SESSION['cadenaId'])) {
    die(json_encode((object) [
        "estado" => "ERROR",
        "mensaje" => "Faltan variables de sesión, por favor loguearse nuevamente"
    ]));
}

$lc_config = new categoria();
$lc_cadena = $_SESSION['cadenaId'];
$lc_usuario = $_SESSION['usuarioId'];
$lc_restaurante = $_SESSION['rstId'];

if (htmlspecialchars(isset($_GET["cargarFormasPago"]))) {
    $lc_condiciones[0] = 0;
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = "";
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = "";
    $lc_condiciones[6] = 0;
    $lc_condiciones[7] = $lc_cadena;
    $lc_condiciones[8] = 0;
    $lc_condiciones[9] = $lc_usuario;
    $lc_condiciones[10] = $lc_restaurante;
    $lc_condiciones[11] = 0;
    $lc_condiciones[12] = 0;
    print $lc_config->fn_consultar("administrarFormasPago", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["cargarFormaPago"]))) {
    $lc_condiciones[0] = 0;
    $lc_condiciones[1] = 1;
    $lc_condiciones[2] = htmlspecialchars($_GET["fmp_id"]);
    $lc_condiciones[3] = "";
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = "";
    $lc_condiciones[6] = 0;
    $lc_condiciones[7] = 0;
    $lc_condiciones[8] = 0;
    $lc_condiciones[9] = $lc_usuario;
    $lc_condiciones[10] = $lc_restaurante;
    $lc_condiciones[11] = 0;
    $lc_condiciones[12] = 0;
    print $lc_config->fn_consultar("administrarFormasPago", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["cargarFormasPagoActivoInactivo"]))) {
    $lc_condiciones[0] = 0;
    $lc_condiciones[1] =htmlspecialchars($_GET["resultado"]);
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = "";
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = "";
    $lc_condiciones[6] = 0;
    $lc_condiciones[7] = $lc_cadena;
    $lc_condiciones[8] = 0;
    $lc_condiciones[9] = $lc_usuario;
    $lc_condiciones[10] = $lc_restaurante;
    $lc_condiciones[11] = 0;
    $lc_condiciones[12] = 0;
    print $lc_config->fn_consultar("administrarFormasPago", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["cargarTipoFormaPago"]))) {
    $lc_condiciones[0] = 1;
    print $lc_config->fn_consultar("cargarTipoFormaPago", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["cargarTipoAdquiriente"]))) {
    $lc_condiciones[0] = 2;
    print $lc_config->fn_consultar("cargarTipoAdquiriente", $lc_condiciones);
}

if (htmlspecialchars(isset($_POST["agregarFormasPago"]))) {
    $lc_condiciones[0] = 1;
    $lc_condiciones[1] = -1;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] =htmlspecialchars($_POST["fmp_descripcion"]);
    $lc_condiciones[4] =htmlspecialchars($_POST["std_id"]);
    $lc_condiciones[5] =htmlspecialchars($_POST["fpf_codigo"]);
    $lc_condiciones[6] =htmlspecialchars($_POST["tfp_id"]);
    $lc_condiciones[7] = $lc_cadena;
    $lc_condiciones[8] =htmlspecialchars($_POST["rda_id"]);
    $lc_condiciones[9] = $lc_usuario;
    $lc_condiciones[10] = $lc_restaurante;
    $lc_condiciones[11] =htmlspecialchars($_POST['fmp_imagen']);
    print $lc_config->fn_consultar("agregarFormasPago", $lc_condiciones);
}

if (htmlspecialchars(isset($_POST["modificarFormasPago"]))) {
    $lc_condiciones[0] = 2;
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] =htmlspecialchars($_POST["fmp_id"]);
    $lc_condiciones[3] =htmlspecialchars($_POST["fmp_descripcion"]);
    $lc_condiciones[4] =htmlspecialchars($_POST["std_id"]);
    $lc_condiciones[5] =htmlspecialchars($_POST["fpf_codigo"]);
    $lc_condiciones[6] =htmlspecialchars($_POST["tfp_id"]);
    $lc_condiciones[7] = $lc_cadena;
    $lc_condiciones[8] =htmlspecialchars($_POST["rda_id"]);
    $lc_condiciones[9] = $lc_usuario;
    $lc_condiciones[10] = $lc_restaurante;
    $lc_condiciones[11] = 0;
    print $lc_config->fn_consultar("administrarFormasPago", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["cargaCadena"]))) {
    $lc_condiciones[0] = 3;
    print $lc_config->fn_consultar("cargaCadena", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["traerRestaurantes"]))) {
    $lc_condiciones[0] = $lc_cadena;
    $lc_condiciones[1] =htmlspecialchars($_GET["Cod_Forma_Pago"]);
    $lc_condiciones[2] =htmlspecialchars($_GET["aplica"]);
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = $lc_usuario;
    $lc_condiciones[5] =htmlspecialchars($_GET["descripcionformapago"]);
    print $lc_config->fn_consultar("traerRestaurantes", $lc_condiciones);
}

if (htmlspecialchars(isset($_POST["aplica_restaurante"]))) {
    $lc_condiciones[0] = $lc_cadena;
    $lc_condiciones[1] =htmlspecialchars($_POST["Cod_Forma_Pago"]);
    $lc_condiciones[2] =htmlspecialchars($_POST["aplica"]);
    if (htmlspecialchars(isset($_POST["id_restaurante"]))) {
        $lc_condiciones[3] = 0;
        foreach ($_POST['id_restaurante'] as $lc_resttaurantes) {
            $lc_condiciones[3] = $lc_condiciones[3] . ',' . $lc_resttaurantes;
        }
    } else {
        $lc_condiciones[3] = 0;
    }
    $lc_condiciones[4] = $lc_usuario;
    $lc_condiciones[5] =htmlspecialchars($_POST["descripcionformapago"]);
    print $lc_config->fn_consultar("traerRestaurantes", $lc_condiciones);
}

if (htmlspecialchars(isset($_POST["aplica_restaurante_nuevo"]))) {
    $lc_condiciones[0] = $lc_cadena;
    $lc_condiciones[1] =htmlspecialchars($_POST["fmp_id"]);
    $lc_condiciones[2] = 4;
    if (htmlspecialchars(isset($_POST["id_restaurante"]))) {
        $lc_condiciones[3] = 0;
        foreach ($_POST['id_restaurante'] as $lc_resttaurantes) {
            $lc_condiciones[3] = $lc_condiciones[3] . ',' . $lc_resttaurantes;
        }
    } else {
        $lc_condiciones[3] = 0;
    }
    $lc_condiciones[4] = $lc_usuario;
    $lc_condiciones[5] =htmlspecialchars($_POST["descripcionformapago"]);
    print $lc_config->fn_consultar("traerRestaurantes", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["traerconfigformapagocoleccion"]))) {
    $lc_condiciones[0] =htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] =htmlspecialchars($_GET["idformapago"]);
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = $lc_usuario;
    $lc_condiciones[5] = $lc_cadena;
    $lc_condiciones[6] = 0;
    $lc_condiciones[7] = 0;
    print $lc_config->fn_consultar("configuracionformaspagocoleccion", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["guardarModificaConfiguracion"]))) {
    $lc_condiciones[0] =htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] =htmlspecialchars($_GET["idformapago"]);
    $lc_condiciones[2] =htmlspecialchars($_GET["idconfiguracion"]);
    $lc_condiciones[3] =htmlspecialchars($_GET["bit"]);
    $lc_condiciones[4] = $lc_usuario;
    $lc_condiciones[5] = $lc_cadena;
    $lc_condiciones[6] =htmlspecialchars($_GET["idporcentajepropina"]);
    $lc_condiciones[7] =htmlspecialchars($_GET["porcentajepropina"]);
    print $lc_config->fn_consultar("configuracionformaspagocoleccion", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["guardarNuevaConfiguracion"]))) {
    $lc_condiciones[0] = 4;
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] =htmlspecialchars($_GET["idconfiguracion"]);
    $lc_condiciones[3] =htmlspecialchars($_GET["bit"]);
    $lc_condiciones[4] = $lc_usuario;
    $lc_condiciones[5] = $lc_cadena;
    $lc_condiciones[6] =htmlspecialchars($_GET["idporcentajepropina"]);
    $lc_condiciones[7] =htmlspecialchars($_GET["porcentajepropina"]);
    print $lc_config->fn_consultar("configuracionformaspagocoleccion", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["agregarConfiguracionesFormasPago"]))) {
    $lc_condiciones[0] = 5;
    $lc_condiciones[1] =htmlspecialchars($_GET['fmp_id']);
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = $lc_usuario;
    $lc_condiciones[5] = $lc_cadena;
    $lc_condiciones[6] =htmlspecialchars($_GET["idporcentajepropina"]);
    $lc_condiciones[7] =htmlspecialchars($_GET["porcentajepropina"]);
    print $lc_config->fn_consultar("configuracionformaspagocoleccion", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["guardaImagenNuevo"]))) {
    $imagen =htmlspecialchars($_POST['fmp_imagen']);
    $lc_condiciones[0] = 3;
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = 0;
    $lc_condiciones[6] = 0;
    $lc_condiciones[7] = 0;
    $lc_condiciones[8] = 0;
    $lc_condiciones[9] = $lc_usuario;
    $lc_condiciones[10] = $lc_restaurante;
    $lc_condiciones[11] = 0;
    $lc_condiciones[12] = $imagen;
    print $lc_config->fn_consultar("administrarFormasPago", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["eliminarConfiguracionesNull"]))) {
    $lc_condiciones[0] = 6;
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = 0;
    $lc_condiciones[3] = 0;
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = $lc_cadena;
    $lc_condiciones[6] = 0;
    $lc_condiciones[7] = 0;
    print $lc_config->fn_consultar("configuracionformaspagocoleccion", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["cargarPerfilesNivelSeguridad"]))) {
    $lc_condiciones[0] =htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = 0;
    print $lc_config->fn_consultar("cargarPerfilesNivelSeguridad", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["agregarNivelSeguridad"]))) {
    $lc_condiciones[0] =htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] =htmlspecialchars($_GET["fmp_id"]);
    $lc_condiciones[2] =htmlspecialchars($_GET["prf_id"]);
    $lc_condiciones[3] = $lc_usuario;
    $lc_condiciones[4] = $lc_cadena;
    print $lc_config->fn_consultar("agregarNivelSeguridad", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["cargarPerfilesNivelSeguridadModificar"]))) {
    $lc_condiciones[0] =htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] =htmlspecialchars($_GET["fmp_id"]);
    print $lc_config->fn_consultar("cargarPerfilesNivelSeguridadModificar", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["agregarNivelSeguridadModificar"]))) {
    $lc_condiciones[0] =htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] =htmlspecialchars($_GET["fmp_id"]);
    $lc_condiciones[2] =htmlspecialchars($_GET["prf_id"]);
    $lc_condiciones[3] = $lc_usuario;
    $lc_condiciones[4] = $lc_cadena;
    print $lc_config->fn_consultar("agregarNivelSeguridadModificar", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["cargarSimboloMoneda"]))) {
    $lc_condiciones[0] =htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = 0;
    print $lc_config->fn_consultar("cargarSimboloMoneda", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["guardarSimboloMoneda"]))) {
    $lc_condiciones[0] =htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] =htmlspecialchars($_GET["pais_descripcion"]);
    $lc_condiciones[2] =htmlspecialchars($_GET["pais_moneda"]);
    $lc_condiciones[3] =htmlspecialchars($_GET["pais_desc_modeda"]);
    $lc_condiciones[4] =htmlspecialchars($_GET["pais_base_factura"]);
    $lc_condiciones[5] =htmlspecialchars($_GET["pais_moneda_simbolo"]);
    $lc_condiciones[6] = 0;
    $lc_condiciones[7] = $lc_usuario;
    $lc_condiciones[8] = $lc_restaurante;
    print $lc_config->fn_consultar("guardarSimboloMoneda", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["traerModificaSimboloMoneda"]))) {
    $lc_condiciones[0] =htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] =htmlspecialchars($_GET["pais_id"]);
    print $lc_config->fn_consultar("traerModificaSimboloMoneda", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["guardarModificaSimboloMoneda"]))) {
    $lc_condiciones[0] =htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] =htmlspecialchars($_GET["pais_descripcion"]);
    $lc_condiciones[2] =htmlspecialchars($_GET["pais_moneda"]);
    $lc_condiciones[3] =htmlspecialchars($_GET["pais_desc_modeda"]);
    $lc_condiciones[4] =htmlspecialchars($_GET["pais_base_factura"]);
    $lc_condiciones[5] =htmlspecialchars($_GET["pais_moneda_simbolo"]);
    $lc_condiciones[6] =htmlspecialchars($_GET["pais_id"]);
    $lc_condiciones[7] = $lc_usuario;
    $lc_condiciones[8] = $lc_restaurante;
    print $lc_config->fn_consultar("guardarModificaSimboloMoneda", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["cargarClientes"]))) {
    $lc_condiciones[0] =htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_restaurante;
    $lc_condiciones[2] = $lc_cadena;
    print $lc_config->fn_consultar("cargarClientes", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["traerTipoDocumento"]))) {
    $lc_condiciones[0] =htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_restaurante;
    $lc_condiciones[2] = $lc_cadena;
    print $lc_config->fn_consultar("traerTipoDocumento", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["traerCiudad"]))) {
    $lc_condiciones[0] =htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_restaurante;
    $lc_condiciones[2] = $lc_cadena;
    print $lc_config->fn_consultar("traerCiudad", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["traerAplicaFormasPago"]))) {
    $lc_condiciones[0] =htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_restaurante;
    $lc_condiciones[2] = $lc_cadena;
    print $lc_config->fn_consultar("traerAplicaFormasPago", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["guardarClienteFormasPago"]))) {
    $lc_condiciones[0] =htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_usuario;
    $lc_condiciones[2] = $lc_restaurante;
    $lc_condiciones[3] = $lc_cadena;
    $lc_condiciones[4] =htmlspecialchars($_GET["sel_tipodocumento"]);
    $lc_condiciones[5] =htmlspecialchars($_GET["sel_ciudad"]);
    $lc_condiciones[6] =htmlspecialchars($_GET["cli_nombres"]);
    $lc_condiciones[7] =htmlspecialchars($_GET["cli_apellidos"]);
    $lc_condiciones[8] =htmlspecialchars($_GET["cli_documento"]);
    $lc_condiciones[9] =htmlspecialchars($_GET["cli_telefono"]);
    $lc_condiciones[10] =htmlspecialchars($_GET["cli_direccion"]);
    $lc_condiciones[11] =htmlspecialchars($_GET["cli_email"]);
    $lc_condiciones[12] =htmlspecialchars($_GET["sel_formapago"]);
    $lc_condiciones[13] = 0;
    print $lc_config->fn_consultar("guardarClienteFormasPago", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["guardarClienteAplicaFormasPago"]))) {
    $lc_condiciones[0] =htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_usuario;
    $lc_condiciones[2] = $lc_restaurante;
    $lc_condiciones[3] = $lc_cadena;
    $lc_condiciones[4] = 0;
    $lc_condiciones[5] = 0;
    $lc_condiciones[6] = 0;
    $lc_condiciones[7] = 0;
    $lc_condiciones[8] = 0;
    $lc_condiciones[9] = 0;
    $lc_condiciones[10] = 0;
    $lc_condiciones[11] = 0;
    $lc_condiciones[12] =htmlspecialchars($_GET["sel_formapago"]);
    $lc_condiciones[13] = 0;
    print $lc_config->fn_consultar("guardarClienteAplicaFormasPago", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["cargarClientesAplicaFormaPago"]))) {
    $lc_condiciones[0] =htmlspecialchars($_GET["aplica"]);
    $lc_condiciones[1] = $lc_cadena;
    $lc_condiciones[2] =htmlspecialchars($_GET["Cod_Forma_Pago"]);
    print $lc_config->fn_consultar("cargarClientesAplicaFormaPago", $lc_condiciones);
}

if (htmlspecialchars(isset($_POST["aplica_cliente"]))) {
    $lc_condiciones[0] =htmlspecialchars($_POST["aplica"]);
    if (htmlspecialchars(isset($_POST["id_cliente"]))) {
        $lc_condiciones[1] = 0;
        foreach ($_POST['id_cliente'] as $lc_clientes) {
            $lc_condiciones[1] = $lc_condiciones[1] . ',' . $lc_clientes;
        }
    } else {
        $lc_condiciones[1] = 0;
    }
    $lc_condiciones[2] = $lc_usuario;
    $lc_condiciones[3] =htmlspecialchars($_POST["descripcionformapago"]);
    $lc_condiciones[4] =htmlspecialchars($_POST["Cod_Forma_Pago"]);
    $lc_condiciones[5] = $lc_cadena;
    print $lc_config->fn_consultar("aplica_cliente", $lc_condiciones);

    //print $lc_config->fn_consultar("traerRestaurantes",$lc_condiciones);
}

if (htmlspecialchars(isset($_POST["aplica_cliente_nuevo"]))) {
    $lc_condiciones[0] =htmlspecialchars($_POST["aplica"]);
    if (htmlspecialchars(isset($_POST["id_cliente"]))) {
        $lc_condiciones[1] = 0;
        foreach ($_POST['id_cliente'] as $lc_clientes) {
            $lc_condiciones[1] = $lc_condiciones[1] . ',' . $lc_clientes;
        }
    } else {
        $lc_condiciones[1] = 0;
    }
    $lc_condiciones[2] = $lc_usuario;
    $lc_condiciones[3] =htmlspecialchars($_POST["descripcionformapago"]);
    $lc_condiciones[4] =htmlspecialchars($_POST["fmp_id"]);
    $lc_condiciones[5] = $lc_cadena;
    print $lc_config->fn_consultar("aplica_cliente", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["cargarTipoFacturacion"]))) {
    $lc_condiciones[0] =htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_restaurante;
    $lc_condiciones[2] = '0';
    $lc_condiciones[3] = $lc_cadena;
    print $lc_config->fn_consultar("cargarTipoFacturacion", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["guardarTipoFacturacionColeccion"]))) {
    $lc_condiciones[0] =htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] =htmlspecialchars($_GET["IDTipoFacturacion"]);
    $lc_condiciones[2] = $lc_cadena;
    $lc_condiciones[3] = $lc_usuario;
    $lc_condiciones[4] =htmlspecialchars($_GET["fmp_id"]);
    $lc_condiciones[5] =htmlspecialchars($_GET["url"]);
    print $lc_config->fn_consultar("guardarTipoFacturacionColeccion", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["cargarTipoFacturacionModifica"]))) {
    $lc_condiciones[0] =htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_restaurante;
    $lc_condiciones[2] =htmlspecialchars($_GET["Cod_Forma_Pago"]);
    $lc_condiciones[3] = $lc_cadena;
    print $lc_config->fn_consultar("cargarTipoFacturacionModifica", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["guardarUrlImprieTicket"]))) {
    $lc_condiciones[0] =htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = 0;
    $lc_condiciones[2] = $lc_cadena;
    $lc_condiciones[3] = $lc_usuario;
    $lc_condiciones[4] =htmlspecialchars($_GET["fmp_id"]);
    $lc_condiciones[5] =htmlspecialchars($_GET["URL_imprimeTicket"]);
    print $lc_config->fn_consultar("guardarUrlImprieTicket", $lc_condiciones);
}

if (htmlspecialchars(isset($_GET["cargarUrlImprimeTicket"]))) {
    $lc_condiciones[0] =htmlspecialchars($_GET["accion"]);
    $lc_condiciones[1] = $lc_restaurante;
    $lc_condiciones[2] =htmlspecialchars($_GET["Cod_Forma_Pago"]);
    $lc_condiciones[3] = $lc_cadena;
    print $lc_config->fn_consultar("cargarUrlImprimeTicket", $lc_condiciones);
}

//COLECCION FORMAS DE PAGO
if (htmlspecialchars(isset($_POST["administrarColeccionFormasPago"]))) {
    $lc_condiciones[0] =htmlspecialchars($_POST['accion']);
    $lc_condiciones[1] =htmlspecialchars($_POST['IDCadena']);
    $lc_condiciones[2] =htmlspecialchars($_POST['IDFormaPago']);
    $lc_condiciones[3] = '0';
    $lc_condiciones[4] = '0';
    print $lc_config->fn_consultar("administrarColeccionFormasPago", $lc_condiciones);
}

if (htmlspecialchars(isset($_POST["detalleColeccionFormasPago"]))) {
    $lc_condiciones[0] =htmlspecialchars($_POST['accion']);
    $lc_condiciones[1] =htmlspecialchars($_POST['IDCadena']);
    $lc_condiciones[2] =htmlspecialchars($_POST['IDFormaPago']);
    $lc_condiciones[3] = '0';
    $lc_condiciones[4] = '0';
    print $lc_config->fn_consultar("detalleColeccionFormasPago", $lc_condiciones);
}

if (htmlspecialchars(isset($_POST["datosColeccionFormasPago"]))) {
    $lc_condiciones[0] =htmlspecialchars($_POST['accion']);
    $lc_condiciones[1] =htmlspecialchars($_POST['IDCadena']);
    $lc_condiciones[2] =htmlspecialchars($_POST['IDFormaPago']);
    $lc_condiciones[3] =htmlspecialchars($_POST['IDColeccionFormaPago']);
    $lc_condiciones[4] = '0';
    print $lc_config->fn_consultar("datosColeccionFormasPago", $lc_condiciones);
}

if (htmlspecialchars(isset($_POST["guardarFormasPagoColeccion"]))) {
    $lc_condiciones[0] =htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] =htmlspecialchars($_POST["IDColecciondeDatosFormasPago"]);
    $lc_condiciones[2] =htmlspecialchars($_POST["IDColeccionFormasPago"]);
    $lc_condiciones[3] =htmlspecialchars($_POST["IDFormaPago"]);
    $lc_condiciones[4] = htmlspecialchars(utf8_decode($_POST["varchar"]));
    $lc_condiciones[5] =htmlspecialchars($_POST["entero"]);
    $lc_condiciones[6] =htmlspecialchars($_POST["fecha"]);
    $lc_condiciones[7] =htmlspecialchars($_POST["seleccion"]);
    $lc_condiciones[8] =htmlspecialchars($_POST["numerico"]);
    $lc_condiciones[9] =htmlspecialchars($_POST["fecha_inicio"]);
    $lc_condiciones[10] =htmlspecialchars($_POST["fecha_fin"]);
    $lc_condiciones[11] =htmlspecialchars($_POST["minimo"]);
    $lc_condiciones[12] =htmlspecialchars($_POST["maximo"]);
    $lc_condiciones[13] =htmlspecialchars($_POST["IDUsuario"]);
    $lc_condiciones[14] = 0;
    print $lc_config->fn_consultar("guardarFormasPagoColeccion", $lc_condiciones);
}

if (htmlspecialchars(isset($_POST["cargaFormaPagoColeccion_edit"]))) {
    $lc_condiciones[0] =htmlspecialchars($_POST['accion']);
    $lc_condiciones[1] =htmlspecialchars($_POST['IDCadena']);
    $lc_condiciones[2] =htmlspecialchars($_POST['IDFormaPago']);
    $lc_condiciones[3] =htmlspecialchars($_POST['IDColeccionFormaPago']);
    $lc_condiciones[4] =htmlspecialchars($_POST['IDColecciondeDatosFormaPago']);
    print $lc_config->fn_consultar("cargaFormaPagoColeccion_edit", $lc_condiciones);
}

if (htmlspecialchars(isset($_POST["modificarFormaPagoColeccion"]))) {
    $lc_condiciones[0] =htmlspecialchars($_POST["accion"]);
    $lc_condiciones[1] =htmlspecialchars($_POST["IDColecciondeDatosFormasPago"]);
    $lc_condiciones[2] =htmlspecialchars($_POST["IDColeccionFormasPago"]);
    $lc_condiciones[3] =htmlspecialchars($_POST["IDFormaPago"]);
    $lc_condiciones[4] = htmlspecialchars(utf8_decode($_POST["varchar"]));
    $lc_condiciones[5] =htmlspecialchars($_POST["entero"]);
    $lc_condiciones[6] =htmlspecialchars($_POST["fecha"]);
    $lc_condiciones[7] =htmlspecialchars($_POST["seleccion"]);
    $lc_condiciones[8] =htmlspecialchars($_POST["numerico"]);
    $lc_condiciones[9] =htmlspecialchars($_POST["fecha_inicio"]);
    $lc_condiciones[10] =htmlspecialchars($_POST["fecha_fin"]);
    $lc_condiciones[11] =htmlspecialchars($_POST["minimo"]);
    $lc_condiciones[12] =htmlspecialchars($_POST["maximo"]);
    $lc_condiciones[13] =htmlspecialchars($_POST["IDUsuario"]);
    $lc_condiciones[14] =htmlspecialchars($_POST["estado"]);
    print $lc_config->fn_consultar("guardarFormasPagoColeccion", $lc_condiciones);
}