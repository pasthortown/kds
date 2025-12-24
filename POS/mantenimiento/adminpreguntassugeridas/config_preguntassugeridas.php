<?php
session_start();
///////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR JOSE FERNANDEZ////////////////////////////////////
///////////DESCRIPCION: PANTALLA ADMINISTRACION DE PREGUNTAS SUGERIDAS/////
////////FECHA CREACION: 20/05/2015/////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 01/06/2015///////////////////////////////////
///////USUARIO QUE MODIFICO: Jose Fernandez////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Aplicacion nuevo estilo, configuraciones en //
////////////////////////////////pantalla modal, cambio de etiquetas///////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
include_once '../../system/conexion/clase_sql.php';
include_once '../../clases/clase_preguntasSugeridas.php';

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

$lc_estacion = new preguntas();
$lc_rest = $_SESSION['rstId'];
$lc_usuarioId = $_SESSION['usuarioId'];
$idCadena = $_SESSION['cadenaId'];

if (htmlspecialchars(isset($_GET['cargarcadena']))) {
    print $lc_estacion->fn_consultar('Cargar_Cadena', '');
} else if (htmlspecialchars(isset($_GET['cargaPlus']))) {
    print $lc_estacion->cargaPlus(
                    htmlspecialchars($_GET['cdn']), htmlspecialchars($_GET['tipo_producto'])
    );
} else if (htmlspecialchars(isset($_GET['insertaCabeceraPregunta']))) {
    print $lc_estacion->insertaCabeceraPregunta(
        htmlspecialchars($_GET['accion']), htmlspecialchars($_GET['cdnCabecera']), utf8_decode(htmlspecialchars($_GET['descCabecera'])), htmlspecialchars($_GET['maxCabecera']), htmlspecialchars($_GET['minCabecera']), utf8_decode(htmlspecialchars($_GET['descPos'])), $lc_rest, $lc_usuarioId, htmlspecialchars($_GET['respID']), htmlspecialchars($_GET['preElimina']), htmlspecialchars($_GET['plu']), utf8_decode(htmlspecialchars($_GET['estado'])), utf8_decode(htmlspecialchars($_GET['respuesta']), '$lc_usuarioId')
    );
} else if (htmlspecialchars(isset($_GET['actualizaCabeceraPregunta']))) {
    print $lc_estacion->actualizaCabeceraPregunta(
        htmlspecialchars($_GET['accion']), htmlspecialchars($_GET['cdnCabecera']), utf8_decode(htmlspecialchars($_GET['cabDes'])), htmlspecialchars($_GET['cabMax']), htmlspecialchars($_GET['cabMin']), utf8_decode(htmlspecialchars($_GET['desPos'])), $lc_rest, $lc_usuarioId, htmlspecialchars($_GET['respID']), htmlspecialchars($_GET['cabPregunta']), htmlspecialchars($_GET['plu']), htmlspecialchars($_GET['cabStd']), utf8_decode(htmlspecialchars($_GET['respuesta']), '$lc_usuarioId')
    );
} else if (htmlspecialchars(isset($_GET['grabaRespuesta']))) {
    print $lc_estacion->grabaRespuesta(
                    htmlspecialchars($_GET['accion']), htmlspecialchars($_GET['cdnCabecera']), utf8_decode(htmlspecialchars($_GET['descCabecera'])), htmlspecialchars($_GET['maxCabecera']), htmlspecialchars($_GET['minCabecera']), utf8_decode(htmlspecialchars($_GET['descPos'])), $lc_rest, htmlspecialchars($_GET['usuario']), htmlspecialchars($_GET['respID']), htmlspecialchars($_GET['preID']), htmlspecialchars($_GET['pluID']), utf8_decode(htmlspecialchars($_GET['estado'])), utf8_decode(htmlspecialchars($_GET['respuesta']))
    );
} else if (htmlspecialchars(isset($_GET['grabaPluModificado']))) {
    print $lc_estacion->grabaPluModificado(
                    htmlspecialchars($_GET['accion']), htmlspecialchars($_GET['cdnCabecera']), utf8_decode(htmlspecialchars($_GET['descCabecera'])), htmlspecialchars($_GET['maxCabecera']), htmlspecialchars($_GET['minCabecera']), utf8_decode(htmlspecialchars($_GET['descPos'])), $lc_rest, htmlspecialchars($_GET['usuario']), htmlspecialchars($_GET['respID']), htmlspecialchars($_GET['preIDModificado']), htmlspecialchars($_GET['pluModificado']), utf8_decode(htmlspecialchars($_GET['estado'])), utf8_decode(htmlspecialchars($_GET['respuesta']))
    );
} else if (htmlspecialchars(isset($_GET['eliminaRespuesta']))) {
    print $lc_estacion->eliminaRespuesta(
                    htmlspecialchars($_GET['accion']), htmlspecialchars($_GET['cdnCabecera']), utf8_decode(htmlspecialchars($_GET['descCabecera'])), htmlspecialchars($_GET['maxCabecera']), htmlspecialchars($_GET['minCabecera']), utf8_decode(htmlspecialchars($_GET['descPos'])), $lc_rest, $lc_usuarioId, htmlspecialchars($_GET['respID']), htmlspecialchars($_GET['preElimina']), htmlspecialchars($_GET['plu']), utf8_decode(htmlspecialchars($_GET['estado'])), utf8_decode(htmlspecialchars($_GET['respuesta']))
    );
} else if (htmlspecialchars(isset($_GET['cargaLosDetalles']))) {
    print $lc_estacion->cargaLosDetalles(
                    htmlspecialchars($_GET['preIDdetalle'])
    );
} else if (htmlspecialchars(isset($_GET['cargarDetallePreguntasInicio']))) {
    print $lc_estacion->cargarDetallePreguntasInicio(
                    0, 0, htmlspecialchars($_GET['cadenaDetalle']), htmlspecialchars($_GET['aBuscar']), 0, false
    );
} else if (htmlspecialchars(isset($_GET['cargarDetallePreguntasInicioPorEstado']))) {
    print $lc_estacion->cargarDetallePreguntasInicio(
                    htmlspecialchars($_GET['inicio']), htmlspecialchars($_GET['fin']), htmlspecialchars($_GET['cadenaPorEstado']), htmlspecialchars($_GET['aBuscarEstado']), htmlspecialchars($_GET['opcionEstado']), true
    );
} else if (htmlspecialchars(isset($_GET['cargaPreguntaModificada']))) {
    print $lc_estacion->cargaPreguntaModificada(
                    htmlspecialchars($_GET['psuModificado'])
    );
} else if (htmlspecialchars(isset($_GET['validaNumeroDerespuestas']))) {
    print $lc_estacion->validaNumeroDerespuestas(
                    htmlspecialchars($_GET['cdnNumeroRespuestas']), htmlspecialchars($_GET['psugNumero'])
    );
} else if (htmlspecialchars(isset($_GET['validaConfiguracionRespuestasMaximas']))) {
    print $lc_estacion->validaConfiguracionRespuestasMaximas(
                    htmlspecialchars($_GET['cdnvalidaConfiguracionRespuestasMaximas'])
    );
} else if (htmlspecialchars(isset($_GET['eliminaPregunta']))) {
    print $lc_estacion->eliminaPregunta(
                    htmlspecialchars($_GET['preElimina'])
    );
} else if (htmlspecialchars(isset($_GET['cargaPreguntaaaproducto']))) {
    print $lc_estacion->cargaPreguntaEliminaPreguntaEliminaProducto(
                    2, 0, 0, 0, htmlspecialchars($_GET['psug_id']), htmlspecialchars($_GET['usuario']), 0
    );
} else if (htmlspecialchars(isset($_GET['eliminaPreguntadeproducto']))) {
    print $lc_estacion->cargaPreguntaEliminaPreguntaEliminaProducto(
                    0, 3, 0, 0, htmlspecialchars($_GET['psug_id']), htmlspecialchars($_GET['usuario']), 0
    );
} else if (htmlspecialchars(isset($_GET['eliminaproductodepregunta']))) {
    print $lc_estacion->cargaPreguntaEliminaPreguntaEliminaProducto(
                    0, 2, 0, htmlspecialchars($_GET['plu_id']), htmlspecialchars($_GET['psug_id']), htmlspecialchars($_GET['usuario']), 0
    );
} else if (htmlspecialchars(isset($_GET['pk'])) == 'modificarRespuesta') {
    $name = htmlspecialchars($_GET['name']);
    $res_id = substr($name, 11, strlen($name));
    print $lc_estacion->modificarRespuesta(
                    $lc_usuarioId, $res_id, utf8_decode(htmlspecialchars($_GET['value']))
    );
//Merge Pregunta Sugerida
} else if (htmlspecialchars(isset($_POST['mergePreguntaSugerida']))) {
     print $lc_estacion->mergePreguntaSugerida(
                    htmlspecialchars($_POST['accion'])
                    , $idCadena
                    , htmlspecialchars($_POST['idPregunta'])
                    , htmlspecialchars($_POST['descripcion'])
                    , htmlspecialchars($_POST['minimo'])
                    , htmlspecialchars($_POST['maximo'])
                    , htmlspecialchars($_POST['descripcionPos'])
                    , htmlspecialchars($_POST['nivel'])
                    , htmlspecialchars($_POST['estado'])
                    , htmlspecialchars($_POST['respuestas'])
         , $lc_usuarioId
         , htmlspecialchars($_POST['idGrupo'])
    );
} else if (htmlspecialchars(isset($_POST['cargarGrupoPreguntasSugeridas']))) {
    print $lc_estacion->cargarGrupoPreguntasSugeridas($idCadena);
}

