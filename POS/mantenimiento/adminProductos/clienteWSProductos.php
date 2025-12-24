<?php

session_start();

include_once("../../system/conexion/clase_sql.php");
include_once("../../clases/clase_productos.php");
include_once "../../clases/clase_webservice.php";

$servicioWebObj=new webservice();
$producto = new Producto();

if (empty($_SESSION['cadenaId']) OR empty($_SESSION['usuarioId'])) {
    die(json_encode((object)["Error" => "Error en Variables de sesión"]));
}
$idCadena = $_SESSION['cadenaId'];
$idUsuario = $_SESSION['usuarioId'];

$request = (object) $_POST;

if ($request->metodo === "guardarProducto") {
    try {

        $array_ini = parse_ini_file("../../serviciosweb/interface/config.ini");

        //Cargar Información Producto
        $plu = $producto->guardarProducto($idCadena, $request->accion, $request->idProducto, $request->descripcion, $request->preparacion, $request->idTipoProducto, $request->idClasificacion, $request->codigoBarras, $request->anulacion, $request->gramo, $request->qsr, $request->cantidad, $request->impuesto1, $request->impuesto2, $request->impuesto3, $request->impuesto4, $request->impuesto5, $request->masterPlu, $request->preciosPorCategoria, $request->canales, $request->preguntas, $request->contenido, $request->departamento, $idUsuario, $request->idModificador, $request->estado);
        //Cargar Precio por Categoría del Producto Modificado

        $preciosPlu = $producto->cargarPreciosPorCategoriasPorProductoObjeto($idCadena, $plu["idProducto"]);

        $estado = 0;
        if ($plu["estado"] === "Activo") {
            $estado = 1;
        }

        $pluEnviar = array(
            "codClasificacion" => $request->idIntegracionClasificacion,
            "codDeptoPlu" => $request->idIntegracionDepartamento,
            "descripcion" => utf8_decode($plu["descripcion"]),
            "estado" => $estado,
            "impuesto" => $plu["impuesto"],
            "plusPK" => array(
                "codCadena" => $idCadena,
                "codPlu" => $plu["idProducto"],
                "numPlu" => $plu["numPlu"]));
        $data_string = json_encode($pluEnviar);

        //Servidor WS Gerente Nacional
        $restaurante = $_SESSION['rstId'];
        $datosWebservice=$servicioWebObj->retorna_WS_Productos_Modificar($restaurante);
        $url=$datosWebservice["urlwebservice"];
        //        $servidorGerenteNacional = $array_ini["servidorWSGerenteNacional"];
        //        $url = $servidorGerenteNacional . "GerenteNacional.ServiciosWeb/webresources/productos/modificarproducto/";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));

        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        $respuesta = json_decode($result);

        //Creación y Actualización de Precios
        if ($respuesta->estado > 0) {
            $data_string = json_encode($preciosPlu);
            //$url = $servidorGerenteNacional . "GerenteNacional.ServiciosWeb/webresources/precios/agregar";
            $datosWebservicePrecios = $servicioWebObj->retorna_WS_Precios_Agregar($restaurante);
            $url = $datosWebservice["urlwebservice"];
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
            //execute post
            $resultPrecios = curl_exec($ch);
            //close connection
            curl_close($ch);
            $respuestaPrecios = json_decode($resultPrecios);
            if ($respuestaPrecios->estado > 0) {
                print $producto->cargarProductosPorCadena($idCadena);
            } else {
                print $resultPrecios;
            }
        } else {
            print $result;
        }
    } catch (Exception $e) {
        print json_encode($e);
    }
}