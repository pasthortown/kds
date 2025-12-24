<?php

////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: FRANCISCO SIERRA///////////////////////////////////////////
///////////DESCRIPCION: CLASE PARA WEBSERVICES ///////////////////////////////////////////
////////////////TABLAS: /////////////////////////
////////FECHA CREACION: 05/04/2017//////////////////////////////////////////////////
///* DESCRIPCION:
//  Agrupa funciones relacionadas a los web services que se utilizan en la aplicación
//  Changelog:
//      - 20/04/2017:
//          Se añaden funciones específicas para cada webservice
////////////////////////////////////////////////////////////////////////////////////
class webservice extends sql {

    function __construct() {
        parent ::__construct();
    }

    function retorna_Direccion_Webservice($lc_datos) {
        $lc_sql = "exec [config].[USP_Retorna_Direccion_Webservice] $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', $lc_datos[3]";
        if ($this->fn_ejecutarquery($lc_sql)) {
            $row = $this->fn_leerarreglo();
            $this->lc_regs['datos'] = [
                'estado' => $row['estado'],
                'urlwebservice' => utf8_decode($row['direccionws'])
            ];
        }
        return $this->lc_regs['datos'];
    }

    /*
     * Función genérica, se puede utilizar si 
     * no se quiere crear una función específica para 
     * encontrar la direccion del web service
     */

    function retorna_WS($rst_id, $coleccionws, $parametro, $primario = 0) {
        $lc_datos_ws = [
            $rst_id,
            $coleccionws,
            $parametro,
            $primario
        ];
        return $this->retorna_Direccion_Webservice($lc_datos_ws);
    }

    /*
     * Devuelve los datos del webservice para validación de 
     * cajero activo
     */

    function retorna_WS_Trans_Venta_ValidaCajero($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'TRANS VENTA', 'VALIDA CAJERO', $primario);
    }

    function retorna_WS_Trans_Venta_ValidaCajeroAutomatico($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'TRANS VENTA', 'VALIDA CAJERO AUTOMATICO', $primario);
    }

    function retorna_WS_Trans_Venta_InyeccionIngresoDestino($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'TRANS VENTA', 'INYECCION INGRESO DESTINO', $primario);
    }

    function retorna_WS_Trans_Venta_CalculoInterfaceDestino($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'TRANS VENTA', 'CALCULO INTERFACE DESTINO', $primario);
    }

    function retorna_WS_Trans_Venta_ActualizacionPrecios($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'TRANS VENTA', 'ACTUALIZACION PRECIOS', $primario);
    }

    function retorna_WS_Trans_Venta_RetornaPrecios($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'TRANS VENTA', 'RETORNA PRECIOS', $primario);
    }

    function retorna_WS_Trans_Venta_ValidacionTransferencia($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'TRANS VENTA', 'VALIDA TRANSFERENCIA', $primario);
    }

    function retorna_WS_Go_Trade_Canje($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'GO TRADE', 'CANJE', $primario);
    }

    function retorna_WS_Go_Trade_Anulacion($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'GO TRADE', 'ANULACION', $primario);
    }

    function retorna_WS_Clientes_Cliente($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'CLIENTES', 'CLIENTE', $primario);
    }

    function retorna_WS_Clientes_InsertarActualizar($rst_id,$primario=0){
        return $this->retorna_WS($rst_id,'SOA','CLIENTES INSERTARACTUALIZAR',$primario);
    }

    function retorna_WS_Clientes_Clientes_Externos($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'CLIENTES', 'CLIENTE EXTERNOS', $primario);
    }

    function retorna_WS_Clientes_Actualiza($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'CLIENTES', 'ACTUALIZA', $primario);
    }

    function retorna_WS_Modificadores_Cargar($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'SOA', 'MODIFICADORES CARGAR', $primario);
    }

    function retorna_WS_Departamentos_Actualizar($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'SOA', 'DEPARTAMENTOS ACTUALIZAR', $primario);
    }

    function retorna_WS_Restaurantes_Cadena($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'SOA', 'RESTAURANTES POR CADENA', $primario);
    }

    function retorna_WS_Precios_Agregar($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'SOA', 'PRECIOS AGREGAR', $primario);
    }

    function retorna_WS_Productos_Modificar($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'SOA', 'PRODUCTOS MODIFICAR', $primario);
    }

    function retorna_WS_Caja_Chica_Verificar($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'CCL', 'VERIFICAR', $primario);
    }

    function retorna_WS_Caja_Chica_GuardarIngresoEgreso($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'CCL', 'GUARDAR INGRESO EGRESO', $primario);
    }

    function retorna_WS_Caja_Chica_Cancelacion($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'GERENTE', 'CCL CANCELACION', $primario);
    }

    function retorna_WS_Cupones_CanjearAutomatico($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'CUPONES PREPAGADOS', 'CANJE AUTOMATICO', $primario);
    }

    function retorna_WS_Cupones_CanjearManual($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'CUPONES PREPAGADOS', 'CANJE MANUAL', $primario);
    }

    function retorna_WS_Cupones_Digitales_ObtenerEstado($rst_id, $primario = 0){
        return $this->retorna_WS($rst_id, 'CUPONES PREPAGADOS', 'DIGITALES OBTENER CUPON', $primario);
    }

    function retorna_WS_Cupones_Digitales_ActualizarEstado($rst_id, $primario = 0){
        return $this->retorna_WS($rst_id, 'CUPONES PREPAGADOS', 'DIGITALES ACTUALIZAR CUPON', $primario);
    }

    function retorna_WS_Cupones_ImpresionHTML($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'CUPONES PREPAGADOS', 'IMPRESION HTML', $primario);
    }

    function retorna_WS_CuponesMultimarca_Canjear($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'SOA', 'CUPONES MULTIMARCA CANJEAR', $primario);
    }

    function retorna_WS_CuponesMultimarca_Verificar($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'SOA', 'CUPONES MULTIMARCA VERIFICAR', $primario);
    }

    function retorna_WS_CuponesMultimarca_Reversar($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'SOA', 'CUPONES MULTIMARCA REVERSAR', $primario);
    }

    function retorna_WS_CodigosGerente_CargarDLL($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'SOA', 'FORMASPAGO CADENA', $primario);
    }

    function retorna_WS_Cupones_NumeroCanjes($rst_id,$primario=0){
        return $this->retorna_WS($rst_id,'SOA','CUPONES NUMEROCANJES',$primario);
     }
    function retorna_rutaWSRecargas($rst_id, $valor) {
        return $this->retorna_WS($rst_id, 'RECARGAS', $valor, 0);
    }

    function retorna_Direccion_Webservice_Cadena($lc_datos) {
        $lc_sql = "exec [config].[USP_Retorna_Direccion_Webservice] $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', $lc_datos[3], $lc_datos[4]";
        if ($this->fn_ejecutarquery($lc_sql)) {
            $row = $this->fn_leerarreglo();
            $this->lc_regs['datos'] = [
                'estado' => $row[0],
                'urlwebservice' => utf8_decode($row[1])
            ];
        }
        return $this->lc_regs['datos'];
    }

    //Daniel Llerena
    function retorna_WS_PlugThem_Post($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'PLUG THEM', 'POST', $primario);
    }

    function retorna_WS_PlugThem_Get($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'PLUG THEM', 'GET', $primario);
    }

    function retorna_WS_Cupones_CanjesCliente($rst_id,$primario=0){
        return $this->retorna_WS($rst_id,'MASTERDATA','CUPONES CANJES CLIENTE',$primario);
    }

    function retorna_WS_Cupones_Canje($rst_id,$primario=0){
        return $this->retorna_WS($rst_id,'MASTERDATA','CUPONES INSERTAR CANJE',$primario);
    }

    function retorna_ws_notas_credito_cliente($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id,'SAP', 'NOTAS CREDITO', $primario);    
    }

    function retorna_ws_fifteam($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id,'FIFTEAM', 'CANJE', $primario);    
    }
    function retorna_WS_Eventos($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'EVENTOS', 'SINCRONIZAR', $primario);
    }
    function retorna_WS_Eventos_Valida_WS($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'EVENTOS', 'VALIDA WS', $primario);
    }
    function retorna_WS_Eventos_Conexion($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'EVENTOS', 'DATOS CONEXION', $primario);
    }
    function retorna_WS_Qualtrics($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'QUALTRICS DOMINIO', 'ENDPOINT', $primario);
    }
    function retorna_WS_NotificarPedido($rst_id, $primario = 0) {
        return $this->retorna_WS($rst_id, 'APP NOTIFICAR LISTO', 'ENDPOINT', $primario);
    }
    // MM
    function retorna_rutaWS( $rst_id, $name, $valor, $primario = 0 ){
        return $this->retorna_WS($rst_id, $name, $valor, $primario);
    }

    function buscarTimeOut($cadena)
    {
        $this->fn_ejecutarquery("SELECT [config].[fn_ColeccionCadena_VariableV](" . $cadena . ", 'WS CONFIGURACIONES', 'TIMEOUT')");
        $row = $this->fn_leerarreglo();

        if (isset($row) && isset($row[0])) {
            return ['timeout' => $row[0]];
        } else {
            return 'ERROR';
        }
    }

}