<?php
/**
 * Created by PhpStorm.
 * User: fabricio.sierra
 * Date: 7/31/2018
 * Time: 10:50 AM
 */

namespace Maxpoint\Mantenimiento\promociones\Clases;
use Maxpoint\Mantenimiento\adminReplicacion\Clases\Controller;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Doctrine\DBAL\Connection;
use PDO;


error_reporting(0);
ini_set('display_errors', 0);
// Se ocultan los warning para evitar conflicto en la data
class PromocionesController extends Controller
{
    const NOMBRE_MODULO_CUPONES = 'Cupon';
    const ESTADO_ACTIVO = "ActivoMasterdata";
    const ESTADO_INACTIVO = "InactivoMasterdata";
    const ESTADO_CANJEADO = "CanjeadoMasterdata";
    const ESTADO_ANULADO = "AnuladoMasterdata";

    public $configuraciones;

    /**
     * Permite seleccionar todas las categorias creadas para una promoción
     * @param cdn_id - ID de la cadena que se está consultando
     * Date: 13/09/2018
     * Time: 17:38 PM
     * @versión  3.0
     * @author Eduardo Valencia <educristo@gmail.com>
     * @copyright KFC
     */
    public function buscarCategoriasCupon($cdn_id){
        $query = "EXEC [promociones].[USP_Promociones] 'categoriaCupon', $cdn_id,0,'0'";
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function __construct(Connection $conn)
    {
        parent::__construct($conn);
        $query = "EXEC [promociones].[USP_Promociones] 'cargarConfiguracionesPromociones',".$_SESSION["cadenaId"].", '0',''";
        $res = $this->cargarDatos($query);
        if($res["estado"]!=1){
            $this->configuraciones=false;
            return;
        }
        if(count($res["datos"])<1){
            $this->configuraciones=false;
            return;
        }
        $this->configuraciones=$res["datos"][0];
    }

    public function getConfiguraciones(){
        return $this->configuraciones;
    }

    public function buscarPromocionPorID($idPromocion){
        $query = "EXEC [promociones].[USP_Promociones] 'buscarPromocionPorID', 0,0, '$idPromocion'";
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function validacionesOrdenFacturaPromocion($idPromocion,$idOrdenPedido,$idFactura,$dop_cuenta,$parametros = []){
        /*
         * 	 Restriccion Productos
         *   Monto Bruto Factura
         *   Numero Productos Factura
         *   Promocion Sobre Promocion
         *   Descuento Sobre Descuento
         *   Canal($promocion,$idFactura,$idOrdenPedido){--fact,ordenpedido
         *   Restaurante?
         * */
        $condicion='';
        if(count($parametros)>0){
            $condicion=',:par8';
        }
        
        $sql="EXEC [promociones].[USP_ValidacionesOrdenFacturaPromocion] :par1,:par2,:par3,:par4,:par5,:par6,:par7".$condicion;
        $stmt = $this->conexion->prepare($sql);
        $par6=null;
        $par7=null;
		$cobro = 0;

        $stmt->bindParam(":par1",$idPromocion);
        $stmt->bindParam(":par2",$idOrdenPedido);
        $stmt->bindParam(":par3",$idFactura);
        $stmt->bindParam(":par4",$dop_cuenta);
        $stmt->bindParam(":par5",$cobro);
        $stmt->bindParam(":par6",$par6,PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT,5);
        $stmt->bindParam(":par7",$par7,PDO::PARAM_STR|PDO::PARAM_INPUT_OUTPUT,300);
        if(count($parametros) > 0)
        {
            $stmt->bindParam(":par8",  json_encode($parametros));
        }
        $res=$stmt->execute();
        return ["estado"=>trim($par6),"mensaje"=>trim($par7)];
    }

    function validarGeneroClienteCanje($promocion,$cliente){
        //TODO: Validación Restricción Género
        //Buscar la dirección del WebService ( género del cliente )(WS Trade)
        //Ejecutar consulta
        //Guardar datos
        //Revisar
    }

    function validarCodigoUnico($promocion){
        if($promocion["Tiene_codigo_unico"]){
            //Crear validación de código único en SP
        }
        return true;
        //TODO: Validación Restricción código único
        // Si la promoción requiere un código único, revisar si el código ingresado ya fue canjeado o no
    }

    function cargarListadoPromociones($cdnId){
        $query = "EXEC [promociones].[USP_Promociones] 'cargarListadoPromociones',$cdnId, '0',''";
        $res = $this->cargarDatos($query);
        return $res;
    }

    function cargarConfiguraciones($cdnId){
        $query = "EXEC [promociones].[USP_Promociones] 'cargarConfiguracionesPromociones',$cdnId, '0',''";
        $res = $this->cargarDatos($query);

        return $res;
    }

    function cargarConfiguracionesJSON($cdnId){
        $query = "EXEC [promociones].[USP_Promociones] 'cargarConfiguracionesPromociones',$cdnId, '0',''";
        //die($query);
        $res = $this->cargarDatosJSON($query);
        if($res["estado"]==0) {
            $res["errores"]=["Error: No se pudo cargar las configuraciones del  módulo de cupones"];
            $this->enviarRespuestaJson($res);
        }

        return $res["datos"][0];
    }

    function ejecutarCanje($Id_Promociones,$CodigoCanjeado,$cfac_id,$Id_cliente,$Cantidad,$idUsuario,$IDCabeceraOrdenPedido,$dop_cuenta,$idCanjeMasterData, $nombrePromocion,$beneficiosPromocion){
        $Plu_id = $beneficiosPromocion['Plu_id'];
        $Cantidad_plu = $beneficiosPromocion['Cantidad_plu'];
        $Tipo_aplica = $beneficiosPromocion['Tipo_aplica'];
        $Id_BeneficiosPromociones = $beneficiosPromocion['Id_BeneficiosPromociones'];

        $query = "SET DATEFORMAT YMD
                  EXEC [promociones].[IAE_CanjeCupon]
                    'insertarCanje','','$Id_Promociones','$CodigoCanjeado',
                    '$cfac_id','$Id_cliente','',NULL,'$Cantidad',
                    '','$idUsuario','$IDCabeceraOrdenPedido', $dop_cuenta,'$idCanjeMasterData','$nombrePromocion','$Plu_id','$Cantidad_plu','$Tipo_aplica','$Id_BeneficiosPromociones'";
                    
        $res = $this->cargarDatos($query);

        return $res;
    }

    // TODO: Insertar Política de Servidor de Webservices Trade
    function guardarUrlServidorTrade($cdn_id,$urlServidor,$usuarioAdm){
        $query = "EXEC [config].[IAE_Politica_Cadena] 
            @cdn_id=$cdn_id,
            @accion='insertarValor',
            @idModulo=1,
            @descripcionPolitica = 'WS SERVIDOR',
            @descripcionParametro = 'TRADE CUPONES',
            @lastUser = '$usuarioAdm',
            @valorVarchar = '$urlServidor',
            @configuracion = 0
            ";
        $res = $this->cargarDatos($query);
        return $res;
    }

    function guardarRutaEndpointCanjeTrade($cdn_id,$rutaEndpoint,$usuarioAdm){
        $query = "EXEC [config].[IAE_Politica_Cadena] 
            @cdn_id=$cdn_id,
            @accion='insertarValor',
            @idModulo=1,
            @descripcionPolitica = 'WS RUTA SERVICIO',
            @descripcionParametro = 'TRADE CUPONES CANJE',
            @lastUser = '$usuarioAdm',
            @valorVarchar = '$rutaEndpoint',
            @configuracion = 0
            ";
        $res = $this->cargarDatos($query);
        return $res;
    }


    function guardarClaveJWTTrade($cdn_id,$claveJWT,$usuarioAdm){
        $query = "EXEC [config].[IAE_Politica_Cadena] 
            @cdn_id=$cdn_id,
            @accion='insertarValor',
            @idModulo=1,
            @descripcionPolitica = 'PROMOCIONES',
            @descripcionParametro = 'CLAVE JWT',
            @lastUser = '$usuarioAdm',
            @valorVarchar = '$claveJWT',
            @configuracion = 0
            ";
        $res = $this->cargarDatos($query);
        return $res;
    }

    // TODO: Insertar Política de Endpoint Canje Azure
    function guardarEndpointCanjeAzure($cdn_id,$urlServidor,$usuarioAdm){
        $query = "EXEC [config].[IAE_Politica_Cadena] 
            @cdn_id=$cdn_id,
            @accion='insertarValor',
            @idModulo=1,
            @descripcionPolitica = 'WS RUTA SERVICIO',
            @descripcionParametro = 'PROMOCIONES CANJE AZURE',
            @lastUser = '$usuarioAdm',
            @valorVarchar = '$urlServidor',
            @configuracion = 0
            ";
        $res = $this->cargarDatos($query);
        return $res;
    }

    // TODO: Insertar política de Endpoint Validaciones Azure
    function guardarEndpointValidacionesAzure($cdn_id,$urlServidor,$usuarioAdm){
        $query = "EXEC [config].[IAE_Politica_Cadena] 
            @cdn_id=$cdn_id,
            @accion='insertarValor',
            @idModulo=1,
            @descripcionPolitica = 'WS RUTA SERVICIO',
            @descripcionParametro = 'PROMOCIONES VALIDACIONES AZURE',
            @lastUser = '$usuarioAdm',
            @valorVarchar = '$urlServidor',
            @configuracion = 0
            ";
        $res = $this->cargarDatos($query);
        return $res;
    }

    function fn_consultar($lc_sqlQuery, $lc_datos) {
        $this->lc_regs = array();
        switch ($lc_sqlQuery) {
            case "cargarBeneficiosPromociones":
                $lc_sql = "EXEC [promociones].[Regiones]  ";
                // echo($lc_sql);
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("rgn_id" => $row['rgn_id'],
                            "rgn_descripcion" => utf8_encode(trim($row['rgn_descripcion'])),
                            "agregado" => $row['agregado'],
                            "pais_id" => $row['pais_id']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;

        }
    }

	function guardarPromocion(
							$Id_Promociones,
							$cdn_id,
							$Codigo_externo,
							$Nombre,
							$Nombre_imprimible,
							$Codigo_amigable,
							$Limite_canjes_total,
							$Limite_canjes_cliente,
							$Total_canjeados,
							$Caduca_con_tiempo,
							$Unidad_Tiempo_validez,
							$Tiempo_validez,
							$Activo_desde,
							$Activo_Hasta,
							$Requiere_productos,
							$Requiere_forma_Pago,
							$Puntos_Acumulables,
							$Saldo_Acumulable,
							$Bruto_minimo_factura,
							$Bruto_maximo_factura,
							$Cantidad_minima_productos_factura,
							$Permite_otras_promociones,
							$Maximo_canje_multiple,
							$Requiere_dias,
							$Dias_canjeable,
							$Requiere_horario,
							$Horario_canjeable,
							$Requiere_rango_edad,
							$Rango_edad,
							$Requiere_genero,
							$genero,
							$Tiene_codigo_unico,
							$Activo,
							$Motivo_inactivacion,
							$Permite_descuento_sobre_descuento,
							$Concatenador_beneficios,
							$Concatenador_plus_promocion,
							$Requiere_canal,
							$Requiere_restaurante,
							$LastUser,
							$Beneficios_Promocion,
							$Productos_Requeridos_Promocion,
							$Forma_Pago_Promocion,
							$Restaurantes_Requeridos_Promocion,
							$Canales_Requeridos_Promocion,
							$IDColeccionPromociones,
							$categoriasSeleccionadasPromocion){
	    $query = "EXEC [promociones].[IAE_insertarNuevaPromocion]
		'$Id_Promociones',
		$cdn_id,
		'$Codigo_externo',
		'$Nombre',
		'$Nombre_imprimible',
		'$Codigo_amigable',
		$Limite_canjes_total,
		$Limite_canjes_cliente,
		$Total_canjeados,
		$Caduca_con_tiempo,
		'$Unidad_Tiempo_validez',
		$Tiempo_validez,
		'$Activo_desde',
		'$Activo_Hasta',
		$Requiere_productos,
		$Requiere_forma_Pago,
		$Puntos_Acumulables,
		$Saldo_Acumulable,
		$Bruto_minimo_factura,
		$Bruto_maximo_factura,
		$Cantidad_minima_productos_factura,
		$Permite_otras_promociones,
		$Maximo_canje_multiple,
		$Requiere_dias,
		'$Dias_canjeable',
		$Requiere_horario,
		'$Horario_canjeable',
		$Requiere_rango_edad,
		'$Rango_edad',
		$Requiere_genero,
		'$genero',
		$Tiene_codigo_unico,
		$Activo,
		'$Motivo_inactivacion',
		$Permite_descuento_sobre_descuento,
		'$Concatenador_beneficios',
		'$Concatenador_plus_promocion',
		$Requiere_canal,
		$Requiere_restaurante,
		'$LastUser',
		'$Beneficios_Promocion',
		'$Productos_Requeridos_Promocion',
		'$Forma_Pago_Promocion',
		'$Restaurantes_Requeridos_Promocion',
		'$Canales_Requeridos_Promocion',
		'$IDColeccionPromociones',
		'$categoriasSeleccionadasPromocion'";

        $res = $this->cargarDatos($query);
        return $res;
	}

	public function refrescarTokenTrade(){
       // $cliente=
    }

    public function obtenerRestaurantesPromocion($idPromocion){
        $sql="EXEC [promociones].[USP_Promociones] 'restaurantesPromocion',".$_SESSION["cadenaId"].", '0','$idPromocion'";
        $res = $this->cargarDatos($sql);
        if($res["estado"]==0){
            return [];
        }
        return $res["datos"][0]["variableV"];
    }

    function extraerClaveConfiguraciones($clave){
        if(!isset($this->configuraciones[$clave])) {
            $this->enviarRespuestaJson([
                "estadoPromocion"=>"ERROR",
                "mensajePromocion"=>"Error en configuraciones de cupones ($clave)",
                "estadoCanje"=>"ERROR",
                "mensajeCanje"=>"No se realiza el canje"
            ]);
        }
        return $this->configuraciones[$clave];
    }

    function cargarDetallesCupon($idCadena,$idCupon){
        $sql="EXEC [promociones].[USP_Promociones] 'detallesCupon',".$idCadena.", '0','$idCupon'";
        $res = $this->cargarDatosMultiResultset($sql);

        $retorno=[
            "estado"=>$res["estado"],
            "mensaje"=>"No se pudo cargar los datos del cupón",
            "datos"=>[],
        ];

        if($res["estado"]==0){
            return $retorno;
        }

        $datosPromocion=$res["datos"][0];
        $beneficios=$res["datos"][1];
        $plusRequeridos=$res["datos"][2];

        if(count($datosPromocion)<1){
            return $retorno;
        }

        $promocion = $datosPromocion[0];
        $promocion["beneficios"] = $beneficios;
        $promocion["plus_requeridos"] = $plusRequeridos;

        $retorno["datos"]=$promocion;
        //return $res["datos"][0]["variableV"];
        return $retorno;
    }

    function retornaCanjesPendientesSincronizacionMasterData(){
        $sql="EXEC [promociones].[USP_Promociones] 'sincronizacionMasterData',0, '0',''";
        $res = $this->cargarDatos($sql);
        return $res;
    }

    function confirmarSincronizacionCanjesMasterData($parametrosConsulta){
        $sql = "EXEC [promociones].[USP_Promociones] 'confirmarSincronizacionMasterData',0, '0','','".$parametrosConsulta["idUsuario"]."'";
        $res = $this->ejecutarUpdate($sql);
        return $res;
    }

    public function getURLSincronizacionCanjeMastarData(){
        return "http://".$this->configuraciones["direccionServidorMasterData"].$this->configuraciones["endpointMasterDataSincronizarCanje"];
    }

    public function getURLInsercionCanjeMastarData(){
        return "http://".$this->configuraciones["direccionServidorMasterData"].$this->configuraciones["endpointMasterDataInsertarCanje"];
    }

    public function getURLSincronizacionAnulacionMastarData(){
        return "http://".$this->configuraciones["direccionServidorMasterData"].$this->configuraciones["endpointMasterDataAnulacion"];
    }

    public function getURLCanjesClienteMastarData(){
        return "http://".$this->configuraciones["direccionServidorMasterData"].$this->configuraciones["endpointMasterDataCanjesCliente"];
    }

    public function getURLInactivarCanjeMastarData(){
        return "http://".$this->configuraciones["direccionServidorMasterData"].$this->configuraciones["endpointMasterDataInactivarCanje"];
    }

    function sincronizarCanjesPromociones(){
        $idUsuario = $_SESSION['usuarioId'];

        //SINCRONIZACION DE CANJES
        //Buscar los canjes que no se han sincronizado
        $resultsetCanjesPendientes = $this->retornaCanjesPendientesSincronizacionMasterData();

        if($resultsetCanjesPendientes["estado"] === 1 && count($resultsetCanjesPendientes["datos"])>0){
            $dataPeticion=[
                "canjes" => $resultsetCanjesPendientes["datos"],
                "rst_id" => $_SESSION["rstId"],
                "cdn_id" => $_SESSION["cadenaId"]
            ];

            $data_string=json_encode($dataPeticion);
           // error_log("Sincronizacion Canjes\n".$data_string,3,"sincronizaciones.log");
            $url = $this->getURLSincronizacionCanjeMastarData();
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
            curl_setopt($ch, CURLOPT_TIMEOUT, 2);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
            //execute post
            $result = curl_exec($ch);
            $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if($http_status!==200){
                return;
                /*
                $resultado = array(
                    "estado"=>"ERROR",
                    "mensaje"=>"Error al conectar con el servidor de canjes."
                );

                enviarRespuestaJson($resultado);
                */
            }

            $respuesta=json_decode($result);
            //close connection
            curl_close($ch);

            if($respuesta->estado!=1){
                //$resultado = array(
                //    "estado"=>"ERROR",
                //    "mensaje"=>"Error al conectar con el servidor de canjes."
                //);
                //enviarRespuestaJson($resultado);
                return;
            }

            $parametrosConsulta=[
                "idUsuario"=>$idUsuario
            ];
            //Cambiar el estado de los canjes en el local
            $resultsetCambiarEstado = $this->confirmarSincronizacionCanjesMasterData($parametrosConsulta);
            return;
        }

        //Crear un proceso para avisar a soporte que hay un problema al sincronizar los canjes
        //Ejecutar la sincronización

    }

    function sincronizarAnulacionCanjesFactura($parametros){
        $dataPeticion=[
            "cfacId"=>$parametros["cfac_id"],
            "ncreId"=>$parametros["ncre_id"],
            'IDCanjeMasterData'=>$parametros['IDCanjeMasterData']
        ];

        $data_string=json_encode($dataPeticion);
        $ch = curl_init($this->getURLSincronizacionAnulacionMastarData());
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_TIMEOUT, 40);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 40);
        //execute post
        $result = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($http_status!==200){
            return;
        }

        $respuesta=json_decode($result);
        //close connection
        curl_close($ch);

        if($respuesta->estado!=1){
            return;
        }

        //Cambiar el estado de los canjes en el local
        $resultsetCambiarEstado = $this->confirmarAnulacionCanjesMasterData($parametros);
        return $resultsetCambiarEstado;
    }

    function anularCanjes($parametros){
        $sql = "EXEC [promociones].[USP_Anulacion_Canjes] 
            'anularCanjesFactura',
            ".$parametros["rst_id"].",
            '".$parametros["cfac_id"]."',
            '".$parametros["ncre_id"]."',
            '".$parametros["usr_id"]."'";
        $res = $this->cargarDatos($sql);
        return $res;
    }

    function confirmarAnulacionCanjesMasterData($parametrosConsulta){
        $sql = "EXEC [promociones].[USP_Anulacion_Canjes] 
            'confirmarAnulacionCanjesFactura',
            ".$parametrosConsulta["rst_id"].",
            '".$parametrosConsulta["cfac_id"]."',
            '".$parametrosConsulta["ncre_id"]."',
            '".$parametrosConsulta["usr_id"]."'";
        $res = $this->cargarDatos($sql);
        return $res;
    }

    function guardarTiposCupon($parametrosConsulta){
        $sql = "EXEC [promociones].[IAE_tiposCupon] 
            ".$parametrosConsulta["idPromocion"].",
            '".$parametrosConsulta["nombresTipos"]."',
            ".$parametrosConsulta["cdn_id"].",
            '".$parametrosConsulta["usr_id"]."'";
        $res = $this->cargarDatos($sql);
        return $res;
    }

    function insertarCanjeMasterData($datosCupon,$datosCliente){
        $fecha = Carbon::now()->format("Y-m-d H:i:s");
        $dataPeticion = [
            "canje"=>[
                "tipoIdentificacion" => $datosCliente["tipoIdentificacion"],
                "identificacion" => "".$datosCliente["identificacion"],
                "uidTrade" => "".$datosCliente["uid_trade"],
                "rstId" => $datosCupon["rstId"],
                "idPromociones" => $datosCupon["idPromociones"],
                "codigoExterno" => $datosCupon["Codigo_externo"],
                "fechaCreacion" => $fecha,
                "estatus" => self::ESTADO_ACTIVO,
            ]
        ];
        $data_string=json_encode($dataPeticion);
        $urlInsercionCanjeMd=$this->getURLInsercionCanjeMastarData();
        $ch = curl_init($urlInsercionCanjeMd);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_TIMEOUT, 40);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 40);
        //execute post
        $result = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if($http_status!==200){
            $resultado = (object)array(
                "estado"=>"ERROR",
                "mensaje"=>"Error al conectar con el servidor de canjes. (Insertar MD)"
            );

            return $resultado;
        }

        $respuesta=json_decode($result);

        //close connection
        curl_close($ch);

        return $respuesta;
    }

    function desactivarCanjeCuponOrdenMasterData($parametros){
        // desactivarCanjeCuponOrdenAzure
        $respuestaError=(object)[
            "estado"=>"error",
            "mensaje"=>"No se pudo desactivar el canje"
        ];

        //Encontrar el ID del canje de MasterData a partir de la cabecera de pedido y el id de promoción
        $consultaSQL = "SELECT pc.IDCanjeMasterData FROM dbo.Promociones_Canjeados pc
         WHERE pc.Id_Promociones = (
            SELECT dop.Detalle_Orden_PedidoVarchar3 
            FROM dbo.Detalle_Orden_Pedido dop	
            WHERE dop.Detalle_Orden_PedidoVarchar3 IS NOT NULL
            AND dop.IDDetalleOrdenPedido	='".$parametros["idDetalleOrden"]."')
        AND pc.IDCabeceraOrdenPedido	='".$parametros["idOrden"]."'
        AND pc.IDStatus=config.fn_estado('Cupon','Inactivo')";

        $cargarCanjes = $this->cargarDatos($consultaSQL);
        if($cargarCanjes["estado"]!==1){
            $respuestaError->mensaje="Error al consultar los canjes de la factura";
            return($respuestaError);
        }

        $respuesta=(object)[
            "estado"=>1,
            "mensaje"=>"OK",
            "datos"=>["OK"]
        ];

        $canjesDesactivar = $cargarCanjes["datos"];
        if(count($canjesDesactivar) < 1){
            return $respuesta;
        }

        $resultadoSincronizacion = $this->consumirWSInactivarCanjeMasterData($canjesDesactivar);
        $resultadoInactivacionLocal = $this->inactivarCanjesLocalmente($canjesDesactivar);
        return $resultadoInactivacionLocal;

    }

    function desactivarCanjeCuponOrdenTextoMasterData($parametros){
       // desactivarCanjeCuponOrdenAzure
        $respuestaError=(object)[
            "estado"=>"error",
            "mensaje"=>"No se pudo desactivar el canje"
        ];

        //Encontrar el ID del canje de MasterData a partir de la cabecera de pedido y el id de promoción
        $consultaSQL = "SELECT * FROM 
                dbo.Promociones_Canjeados pc	
                WHERE pc.Id_Promociones	='".$parametros["idCupon"]."' 
                AND pc.IDCabeceraOrdenPedido	='".$parametros["idOrden"]."'
                AND pc.IDStatus=config.fn_estado('Cupon','Inactivo')";

        $cargarCanjes = $this->cargarDatos($consultaSQL);
        if($cargarCanjes["estado"]!==1){
            $respuestaError->mensaje="Error al consultar los canjes de la factura";
            return($respuestaError);
        }

        $respuesta=(object)[
            "estado"=>1,
            "mensaje"=>"OK",
            "datos"=>["OK"]
        ];

        $canjesDesactivar = $cargarCanjes["datos"];
        if(count($canjesDesactivar) < 1){
            return $respuesta;
        }

        $resultadoSincronizacion = $this->consumirWSInactivarCanjeMasterData($canjesDesactivar);
        $resultadoInactivacionLocal = $this->inactivarCanjesLocalmente($canjesDesactivar);

        return $respuesta;

    }

    function consumirWSInactivarCanjeMasterData($canjesDesactivar){
        $dataPeticion = ["CanjesInactivar"=>$canjesDesactivar];
        $data_string=json_encode($dataPeticion);
        $ch = curl_init($this->getURLInactivarCanjeMastarData());

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        //execute post
        $result = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if($http_status!==200){
            $resultado = (object)array(
                "estado"=>"ERROR",
                "mensaje"=>"Error al conectar con el servidor de canjes (Inactivar MD)."
            );
            return $resultado;
        }

        $respuesta = json_decode($result);
        //close connection
        curl_close($ch);

        return $respuesta;
    }

    public function inactivarCanjesLocalmente($canjes)
    {
        // TODO: Ejecutar sentencia de actualización de estado de el/los canjes recibidos por parámetros
        $varCanjes = print_r($canjes,true);

        $idsCanjesAnular=[];
        foreach($canjes as $canje){
            $idsCanjesAnular[]=$canje["IDCanjeMasterData"];
        }
        $strIdsCanjesAnular = implode(",", $idsCanjesAnular);
        $consultaSQL =  $sql="EXEC [promociones].[USP_Promociones] 'confirmarInactivacionMasterData',0, 0,'','','$strIdsCanjesAnular'";
        error_log( $consultaSQL.PHP_EOL ,3 ,"inactivacionCanje.log");
        $resultadoInactivar = $this->cargarDatos($consultaSQL);
        return $resultadoInactivar;
    }

}