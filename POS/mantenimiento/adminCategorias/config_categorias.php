<?php

session_start();

include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_admincategorias.php';

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

$categoria = new Categoria();
$request = (object)(array_map("utf8_decode", $_POST));

$idCadena = $_SESSION['cadenaId'];
//Cargar todas las categorías de precios
if ($request->metodo === "cargarCategoriasPreciosTodos") {
    print json_encode($categoria->cargarCategoriasPreciosTodos($idCadena));
//Cargar las categorías de precios activos
} else if ($request->metodo === "cargarCategoriasPreciosActivos") {
    print $categoria->cargarCategoriasPreciosActivos($idCadena);
//Cargar las categorías de precios inactivos
} else if ($request->metodo === "cargarCategoriasPreciosInactivos") {
    print $categoria->cargarCategoriasPreciosInactivos($idCadena);
//Guardar categoría de precios
} else if ($request->metodo === "guardarCategoriaPrecios") {

    //Url Archivo Config Servicios Web
    $array_ini = parse_ini_file("../../serviciosweb/interface/config.ini");

    //Servidor WS Gerente Nacional
    $servidorGerenteNacional = $array_ini["servidorWSGerenteNacional"];
    
    $categoriaEnviar = array();
    $metodo = "";
    $estado = 0;
    if ($request->estado === "Activo") {
        $estado = 1;
    }
    if ($request->accion == 0) {
        $categoriaSeleccionada = $categoria->guardarCategoriaPrecios($request->accion, $request->idCategoria, $request->descripcion, $request->abreviatura, $request->nivel, $request->idIntegracion, $request->estado, $idCadena, '', $_SESSION['usuarioId']);
        $metodo = "modificarcategoria";
        $categoriaEnviar = array("abr" => $request->abreviatura, "codCadena" => $idCadena, "codCategoria" => $request->idIntegracion, "descripcion" => $request->descripcion, "estado" => $estado);
        $dataString = json_encode($categoriaEnviar);
        $url = $servidorGerenteNacional . "GerenteNacional.ServiciosWeb/webresources/categoria/" . $metodo . "/";
    } else {
        $categoriaSeleccionada = $categoria->guardarCategoriaPrecios($request->accion, $request->idCategoria, $request->descripcion, $request->abreviatura, $request->nivel, $request->idCategoria, $request->estado, $idCadena, $request->integracion, $_SESSION['usuarioId']);
        $metodo = "agregarcategoria";
        //$categoriaEnviar = array("abr" => $request->abreviatura, "codCadena" => $idCadena, "codCategoria" => $categoriaSeleccionada["idIntegracion"], "descripcion" => $request->descripcion, "estado" => $estado);
        $categoriaEnviar = array("abr" => $request->abreviatura, "codCadena" => $idCadena, "codCategoria" => $request->idIntegracion, "descripcion" => $request->descripcion, "estado" => $estado);
        $dataString = json_encode($categoriaEnviar);
        $url = $servidorGerenteNacional . "GerenteNacional.ServiciosWeb/webresources/categoria/" . $metodo . "/?hereda=" . $request->idIntegracion;        
    }

    //Webservice encargado de sincronizar en gerente el registro ingresado o modificado
    // $result=consumirWS( $url,$dataString,$metodo="POST" );
    $respuesta = json_decode($result);
    
    $listaCategorias = $categoria->cargarCategoriasPreciosTodos($idCadena);
    $listaCategorias['mensaje'] = "Realizado Correctamente"; //$respuesta->mensaje;
    $listaCategorias['estado'] = "1";

    print json_encode($listaCategorias);
    //Cargar categorías de precios para cargar Select
} else if ($request->metodo === "cargarCategoriasPreciosSelect") {
    print $categoria->cargarCategoriasPreciosPorEstado($idCadena, "Activo");
}

function consumirWS($url, $data, $metodo = "POST")
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metodo);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
    $result = curl_exec($ch);
    return $result;
}