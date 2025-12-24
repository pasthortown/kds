<?php
class DragonTailConfig  {

    private function getCadenasConfing($collection, $parameter, $variable) {
        $sql = new sql();
        $lc_sql = "EXEC [config].[getConfigCadena] '$collection','$parameter'";
        try {
            $sql->fn_ejecutarquery($lc_sql);
            $row = $sql->fn_leerarreglo();
            $result = $row[$variable];
        
        } catch (Exception $e) {
            http_response_code(500);
            die("politica : " . $collection . " " . $parameter . " not found");
        }
        return $result;
    }
    private function getRestaurantConfing($collection, $parameter, $variable) {
        $sql = new sql();
        $lc_sql = "select $variable from ColeccionRestaurante as cr
            INNER JOIN ColeccionDeDatosRestaurante as cdr on cr.ID_coleccionRestaurante = cdr.ID_coleccionRestaurante
            INNER JOIN RestauranteColeccionDeDatos as rcd on cdr.ID_ColeccionDeDatosRestaurante =
                rcd.ID_ColeccionDeDatosRestaurante
            where cr.descripcion = '$collection' AND cdr.descripcion = '$parameter' AND cr.isActive = 1 AND
                cdr.isActive = 1 AND rcd.isActive = 1";
        try {
            if (!$sql->fn_ejecutarquery($lc_sql)) {
                throw new GeneralException("can not get restaurant config:  " . $collection . " " . $parameter);
            }
            $row = $sql->fn_leerarreglo();
            if (!$row[$variable]) {
                throw new GeneralException("restaurant config: " . $collection . " " . $parameter .
                    " does not exist");
            }
            return $row[$variable];


        } catch (Exception $e) {
            return $e;
        }
    }
    static function getUrlDragontail($route) {
        $mainRoute = (new DragonTailConfig)->getRestaurantConfing('DRAGONTAIL RUTE','MAIN RUTE',
            'variableV');
        $enPoint = (new DragonTailConfig)->getRestaurantConfing('DRAGONTAIL RUTE', $route,'variableV');
        return $mainRoute.$enPoint;
    }
    function getBaseUrl() {
        return self::getCadenasConfing('CONFIGURACION WEBSERVICES','AGREGADOR - CONTROLADOR',
            'variableV');
    }
    function getsubRoutes($route) {
        return self::getCadenasConfing('WEBHOOK MOTORIZADO', $route,'variableV');
    }
    public static function getRestaurantConfig($idCadena, $idRestaurante, $descriptionCR, $descriptionCDR, $config) {
        $sql = new sql();
        $lc_regs = [];
        $lc_sql = "EXEC [dbo].[getConfigPoliticaRestaurante] '$idCadena', $idRestaurante,'$descriptionCR',
            '$descriptionCDR'";
        try {
            $sql->fn_ejecutarquery($lc_sql);
            $row = $sql->fn_leerarreglo();
            if ($sql->fn_numregistro() > 0) {
                return $row[$config];
            } else {
                return "not found";
            }
        } catch (Exception $e) {
            return $e;
        }
    }

    public static function getDataOrder ($codApp, $restauranId, $cadenaId, $accion) {
        $sql = new sql();
        $lc_sql = "EXEC [pedido].[getOrderDragontail] '$codApp', $cadenaId, $restauranId, $accion";
        try {
            $sql->fn_ejecutarquery($lc_sql);
            while ($row = $sql->fn_leerarreglo()) {
                $result['fecha']                    = $row['fecha'];
                $result['cod_cabeceraApp']          = $row['cod_cabeceraApp'];
                $result['total_Factura']            = $row['total_Factura'];
                $result['telefono_cliente']         = utf8_decode($row['telefono_cliente']);
                $result['identificacion_cliente']   = $row['identificacion_cliente'];
                $result['nombres_cliente']          = utf8_decode($row['nombres_cliente']);
                $result['direccion_cliente']        = utf8_decode($row['direccion_cliente']);
                $result['observacion_pedido']       = utf8_decode($row['observacion_pedido']);
                $result['observaciones_domicilio']  = utf8_decode($row['observaciones_domicilio']);
                $result['calle1_domicilio']         = utf8_decode($row['calle1_domicilio']);
                $result['calle2_domicilio']         = utf8_decode($row['calle2_domicilio']);
                $result['numDireccion_domicilio']   = utf8_decode($row['numDireccion_domicilio']);
                $result['email_cliente']            = $row['email_cliente'];
                $result['cod_ZipCode']              = $row['cod_ZipCode'];
                $result['latitud']                  = $row['latitud'];
                $result['longitud']                 = $row['longitud'];
                $result['cfac_id']                  = $row['cfac_id'];
                $result['medio']                    = $row['medio'];
                $result['fmp_descripcion']          = $row['fmp_descripcion'];
                $result['city']                     = $row['ciu_nombre'];
                $result['items'][] = array('pluId'             => $row['plu_id'],
                                           'codModificador'    => $row['Detalle_FacturaVarchar2'],
                                           'belongs'           => $row['belongs'],
                                           'precio_Bruto'      => $row['precio_Bruto'],
                                           'cantidad'          => $row['cantidad'],
                                           'mainItem'          => $row['mainItem'],
                                           'plu_descripcion'   => utf8_decode($row['plu_descripcion']),
                                           'accion'            => $accion);
                $result['seguimientoPedido']        = $row['seguimientoPedido'];
            }
        } catch (Exception $e) {
            return $e;
        }
        if (isset($result)) {
            return $result;
        }
        throw new GeneralException("error al obtener los datos de la order: " . $codApp);
    }

    public static function getRider($motoroloID) {
        $sql = new sql();
        $lc_sql = "select * from motorolo where IDMotorolo = '$motoroloID'";
        $sql->fn_ejecutarquery($lc_sql);
        $row = $sql->fn_leerarreglo();
        $result = array(
            'documento' => $row['documento'],
            'phone'     => $row['telefono'],
            'nombres'   => iconv("windows-1252", "utf-8", $row['nombres']),
            'apellidos' => iconv("windows-1252", "utf-8", $row['apellidos']),
            'clockIn'   => self::getClockIn($motoroloID),
            'clockOut'  => self::getClockOut($motoroloID),
        );
        return $result;
    }

    static function saveAuditoria($url, $peticion, $messege, $body) {
        $sql = new sql();
        $lc_sql = "INSERT INTO Auditoria_EstadosApp (url, peticion, estado, mensaje, fecha)
            VALUES ('$url', '$peticion', '$messege', '$body', GETDATE())";
        try {
            $sql->fn_ejecutarquery($lc_sql);
        } catch (Exception $e) {
            print $e;
        }
    }

    static function getClockIn($motoroloID) {
        $sql = new sql();
        $lc_sql = "select top 1 fecha_asignacion from Periodo_Motorolo where fecha_asignacion is not null and
            IDMotorolo = '$motoroloID' order by fecha_asignacion desc";
        try {
            $sql->fn_ejecutarquery($lc_sql);
            $row = $sql->fn_leerarreglo();
            if (!isset($row['fecha_asignacion'])) {
                return null;
            }
            return (new DateTimeImmutable($row['fecha_asignacion']))->format('H:i:s');
        } catch (Exception $e) {
            print $e;
        }
    }

    static function getClockOut($motoroloID) {
        $sql = new sql();
        $lc_sql = "select top 1 fecha_retiro from Periodo_Motorolo where fecha_retiro is not null and IDMotorolo =
            '$motoroloID' order by fecha_retiro desc";
        try {
            $sql->fn_ejecutarquery($lc_sql);
            $row = $sql->fn_leerarreglo();
            if (!isset($row['fecha_retiro'])) {
                return null;
            }
            return (new DateTimeImmutable($row['fecha_retiro']))->format('H:i:s');
        } catch (Exception $e) {
            print $e;
        }
    }

    static function getStyle($param) {
        $sql = new sql();
        $lc_sql = "select variableV FROM ColeccionCadena cc
            INNER JOIN ColeccionDeDatosCadena cdc on cc.ID_ColeccionCadena =cdc.ID_ColeccionCadena
            INNER JOIN dbo.CadenaColeccionDeDatos dcc on cdc.ID_ColeccionDeDatosCadena = dcc.ID_ColeccionDeDatosCadena
            WHERE cc.Descripcion = 'DRAGONTAIL ORDER STYLE' AND cdc.Descripcion = '$param' AND cc.isActive = '1'
                AND cdc.isActive = 1";
        try {
            $sql->fn_ejecutarquery($lc_sql);
            $row = $sql->fn_leerarreglo();
            return $row['variableV'];
        } catch (Exception $e) {
            print $e;
        }
    }

    static function getMediosAgregador() {
        $response=[];
        $sql = new sql();
        $query = "select Descripcion from MedioMenu";
        if (!$sql->fn_ejecutarquery($query)) {
            throw new GeneralException("can not get medios agregadores");
        }
        while($row = $sql->fn_leerarreglo()) {
            $response[]=array(
                'agregadores'=>$row['Descripcion'],
            );
        }
        return  $response;
    }

    static function changeOrderStatus($codApp, $status) {
        $sql = new sql();
        $query="UPDATE Cabecera_App SET estado = '$status' where codigo_app = '$codApp'";
        if (!$sql->fn_ejecutarquery($query)) {
            throw new GeneralException("can not change estado del pediodo " . $codApp . " a " . $status);
        }
    }

    static function getCodAppFromCfac($cfac_id) {
        $sql = new sql();
        $query = "select ca.codigo_app as codigo_app, ca.medio as medio FROM Cabecera_App AS ca WITH(NOLOCK)
            INNER JOIN Cabecera_Orden_Pedido AS cop WITH(NOLOCK) ON ca.codigo_app = cop.Cabecera_Orden_PedidoVarchar4
            INNER JOIN Cabecera_Factura AS cf WITH(NOLOCK) ON cf.[IDCabeceraOrdenPedido] = cop.[IDCabeceraOrdenPedido]		
            where cf.cfac_id = '$cfac_id'";
        if (!$sql->fn_ejecutarquery($query)) {
            throw new GeneralException("can not get codApp de la factura:  " . $cfac_id);
        }
        $row = $sql->fn_leerarreglo();
        return json_encode($row);
    }

    static function validStatusEntregado($codApp) {
        $sql = new sql();
        $result = [];
        $query = "select estado from Estado_Pedido_App where codigo_app = '$codApp' and estado = 'ENTREGADO'";
        if (!$sql->fn_ejecutarquery($query)) {
            throw new GeneralException("error, invalid status ENTREGADO".$codApp);
        }
        $row = $sql->fn_leerarreglo();
        if (!$row['estado']) {
            throw new GeneralException("error, it does not allow to change order status to ENTREGADO: " . $codApp);
        }
    }

    static function validateOrderIsBilled($codApp) {
        $sql = new sql();
        $result = [];
        $query = "select cfac_id from cabecera_app where codigo_app = '$codApp'";
        if (!$sql->fn_ejecutarquery($query)) {
            throw new GeneralException("error, invalid status ENTREGADO" . $codApp);
        }
        $row = $sql->fn_leerarreglo();
        if (!$row['cfac_id']) {
            throw new GeneralException("ERROR: pedido no facturado: " . $codApp);
        }
    }

    static function getActiveOrders($restaurantID) {
        $sql = new sql();
        $result = [];
        $query = "exec [pedido].[getDragontailOrders] ";
        if (!$sql->fn_ejecutarquery($query)) {
            throw new GeneralException("can not get codApp de la factura.");
        }
        while ($row = $sql->fn_leerarreglo()) {
            $result[]= array(
                'codigo_app'        => $row['codigo_app'],
                'nombres_cliente'   => utf8_decode($row['nombres_cliente']),
                'estado'            => $row['estado'],
                'medio'             => $row['medio'],
                'cfac_id'           => $row['cfac_id'],
                'motivo'            => $row['motivo'],
                'estadoMotorizado'  => $row['estadoMotorizado'],
                'idMotorolo'        => $row['IDMotorolo'],
                'nombreMotorizado'  => iconv("windows-1252", "utf-8", $row['nombres']) . ' ' .
                    iconv("windows-1252", "utf-8", $row['apellidos'])
            );
        }
        $result['str'] = $sql->fn_numregistro();
        return $result;
    }

    static function getAllParamVariableI($collection, $medio) {
        $sql = new sql();
        $query = "DECLARE @idColeccionCadena VARCHAR (50)
            select cdc.Descripcion, variableI
            FROM ColeccionCadena cc
            INNER JOIN ColeccionDeDatosCadena cdc on cc.ID_ColeccionCadena =cdc.ID_ColeccionCadena
            INNER JOIN dbo.CadenaColeccionDeDatos dcc on cdc.ID_ColeccionDeDatosCadena = dcc.ID_ColeccionDeDatosCadena
            WHERE cc.Descripcion = '$collection' AND cdc.Descripcion = '$medio' AND cc.isActive = '1' AND cdc.isActive = 1";
        if (!$sql->fn_ejecutarquery($query)) {
            throw new GeneralException("error al obtener la politica de cadena ".$collection);
        }
        try {
            $sql->fn_ejecutarquery($query);
            $row = $sql->fn_leerarreglo();
            $response = $row['variableI'];
        } catch (Exception $e) {
            return $e;
        }
        return $response;
    }

    static function getDragontailToken() {
        return (new DragonTailConfig)->getRestaurantConfing('DRAGONTAIL TOKEN','TOKEN',
            'variableV');
    }

    static function getCadenaId() {
        $sql = new sql();
        $query = "select cdn_id from Cadena";
        if (!$sql->fn_ejecutarquery($query)) {
            throw new GeneralException("can not cadena ID :" .$query);
        }
        $row = $sql->fn_leerarreglo();
        return $row['cdn_id'];
    }

    static function updateResponseToDB($response, $orderId) {
        $sql = new sql();
        $query = "update cabecera_app SET respuesta_bringg = '$response', codigo_externo = 1
            where cod_cabeceraApp = '$orderId'";
        if (!$sql->fn_ejecutarquery($query)) {
            throw new GeneralException("can not update cabecera app :" . $query);
        }
    }

    static function getAllParamVariableV($collection) {
        $sql = new sql();
        $query = "select cdr.Descripcion, rcd.variableV 
            from ColeccionRestaurante as cr
            INNER JOIN ColeccionDeDatosRestaurante as cdr on cr.ID_coleccionRestaurante = cdr.ID_coleccionRestaurante
            INNER JOIN RestauranteColeccionDeDatos as rcd on cdr.ID_ColeccionDeDatosRestaurante =
                rcd.ID_ColeccionDeDatosRestaurante
            where cr.descripcion = '$collection' AND cr.isActive = 1 AND cdr.isActive=1 AND rcd.isActive = 1";
        if (!$sql->fn_ejecutarquery($query)) {
            throw new GeneralException("error al obtener la politica de restaurante ".$collection);
        }
        while ($row = $sql->fn_leerarreglo()) {
            if (!isset($row['variableV']) && $row['variableV'] != '') {
                throw new GeneralException("error al obtener la politica de restaurante ".$collection);
            }
            $response[$row['Descripcion']]=$row['variableV'];
        }
        return $response;
    }

    static function UpdateToken($token) {
        $sql = new sql();
        $query = "update RestauranteColeccionDeDatos set variableV = '$token'
            FROM ColeccionRestaurante as cr
            INNER JOIN ColeccionDeDatosRestaurante as cdr on cr.ID_coleccionRestaurante = cdr.ID_coleccionRestaurante
            INNER JOIN RestauranteColeccionDeDatos as rcd on cdr.ID_ColeccionDeDatosRestaurante =
                rcd.ID_ColeccionDeDatosRestaurante
            where cr.descripcion = 'DRAGONTAIL TOKEN' AND cdr.descripcion='TOKEN' AND cr.isActive = 1 AND
                cdr.isActive = 1 AND rcd.isActive = 1";
        if (!$sql->fn_ejecutarquery($query)) {
            throw new GeneralException("error al obtener al guardar token en politica: DRAGONTAIL TOKEN");
        }
    }

    static function getRestaurantId() {
        $sql = new sql();
        $query = " select top 1 rst_id from Restaurante";
        if (!$sql->fn_ejecutarquery($query)) {
            throw new GeneralException("error al obtener ID de restaurante ");
        }
        while ($row = $sql->fn_leerarreglo()) {
            if (!isset($row['rst_id'])) {
                throw new GeneralException("error al obtener ID de restaurante ");
            }
            $restauranId=$row['rst_id'];
        }
        return  $restauranId;
    }

    static function updateSeguimientoPedido($seguimientoPedidoAgregador,$cod_cabeceraApp) {
        $sql = new sql();
        $query = "UPDATE Cabecera_App SET seguimientoPedido= '$seguimientoPedidoAgregador'
            WHERE cod_cabeceraApp='$cod_cabeceraApp'";
        if (!$sql->fn_ejecutarquery($query)) {
            throw new GeneralException("Cannot update cabecera app :" . $query);
        }
    }

    function getDragonTailActive($rst_id, $cadena_id)
    {
        $sql = new sql();
        $query = "EXEC [dbo].[getConfigPoliticaRestaurante] $cadena_id, $rst_id, 'DRAGONTAIL CONFIGS', 'ACTIVE'";
        try {
            $sql->fn_ejecutarquery($query);
            while ($row = $sql->fn_leerarreglo()) {
                $data[] = array(
                    "active" => $row['variableB']
                );
            }
            $data['registros'] = $sql->fn_numregistro();
        } catch (Exception $e) {
            return $e;
        }
        return $data;
    }
}
