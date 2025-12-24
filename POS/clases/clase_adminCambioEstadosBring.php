<?php
/**
 * Created by PhpStorm.
 * User: nathaly.sanchez
 * Date: 25/9/2020
 * Time: 12:50
 */

class clase_adminCambioEstadosBring extends sql
{

    //constructor de la clase
    function __construct(){
        parent ::__construct();
    }

    function cargarPeriodoAbierto( $idCadena, $idRestaurante ) {
        $this->lc_regs = [];
        $query = "EXEC dbo.DOMICILIO_cargar_periodo_abierto $idCadena, $idRestaurante";
        try {
            $this->fn_ejecutarquery( $query );
            if ( $this->fn_numregistro() > 0 ) {
                $row = $this->fn_leerarreglo();
                $this->lc_regs = array( "idPeriodo" => $row['idPeriodo'],
                    "idUsuario" => $row['idUsuario'],
                    "usuario" => utf8_encode($row['usuario']),
                    "fecha" => $row['fecha'],
                    "hora" => $row['hora'] );
            }
        } catch (Exception $e) {
            return $e;
        }
        return $this->lc_regs;
    }

    function cargaTablaCambioEstadosBringg(){
        $lc_query ="  EXEC [config].[USP_admin_estado_listaPedidos]  ";

        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("codFactura" => $row['codFactura'],
                    "codigoApp" => utf8_encode(trim($row['codigoApp'])),
                    "cliente" => utf8_encode(trim($row['cliente'])),
                    "estado" => utf8_encode(trim($row['estado'])),
                    "tipoAsignacion" => utf8_encode(trim($row['tipoAsignacion'])),
                    "idMotorolo" => $row['idMotorolo'],
                    "nombreMotorizado" => utf8_encode(trim($row['nombreMotorizado'])),
                    "medio" => utf8_encode(trim($row['medio'])),
                    "motivo" => utf8_encode(trim($row['motivo'])),
                    "estadoMotorizado" => utf8_encode(trim($row['estadoMotorizado']))
                );
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function cargaTablaMotivosCambioEstadosBringg(){
        $lc_query = "EXEC [config].[USP_admin_cambio_estados_listaMotivos] ";

        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("num" => $row['num'],
                    'idMotivo' => utf8_encode(trim($row['idMotivo'])),
                    'descripcion' => utf8_encode(trim($row['descripcion'])));
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function cargaMotorolos($lc_condicion){
        $lc_query = "EXEC dbo.App_cargar_motorizado_interno_externo_periodo '$lc_condicion[0]','$lc_condicion[1]', '$lc_condicion[2]', '$lc_condicion[3]'";

        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( "idMotorizado" => $row['idMotorizado'],
                                          "motorizado"   => utf8_encode($row['motorizado']),
                                          "documento"    => $row['documento'],
                                          "tipo"         => $row['tipo'],
                                          "empresa"      => utf8_encode($row['empresa']),
                                          "total"         => $row['total'],
                                          "maximo_ordenes" => $row['maximo_ordenes'],
                                          "estado"         => $row['estado']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);

    }

    function fn_asignarMotorizado($lc_condicion){

        $lc_query = "EXEC dbo.App_asignar_pedido_motorolo '$lc_condicion[0]','$lc_condicion[1]', '$lc_condicion[2]'";

        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( "mensaje" => utf8_encode($row['mensaje']),
                                            "estado" => $row['estado']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function fn_asignarMotorizadoPedido($lc_condicion){
        $lc_query = "EXEC [dbo].[App_asignar_motorolo]  '$lc_condicion[0]','$lc_condicion[1]', '$lc_condicion[2]', '$lc_condicion[4]'";

        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( "mensaje" => utf8_encode($row['mensaje']),
                    "estado" => $row['estado']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function guardaAuditoriaCambioEstado($lc_condicion){

        $lc_query = "EXEC [config].[IAE_auditoriaCambioEstadosBringg] '$lc_condicion[4]','$lc_condicion[0];$lc_condicion[3];$lc_condicion[1];$lc_condicion[2]', '$lc_condicion[5]','$lc_condicion[3]'";

        $respuesta = $this->fn_ejecutarquery($lc_query);
        return $respuesta;
    }

    function cambioEstadoEnCamino($lc_condicion){

        $lc_query = "EXEC [config].[IAE_admin_cambio_estados_cambiar_pedido_en_camino] '$lc_condicion[0]','$lc_condicion[1]', '$lc_condicion[2]', '$lc_condicion[3]'";

        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( "mensaje" => utf8_encode($row['mensaje']),
                    "estado" => $row['estado']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function cambioEstadoEntregado($lc_condicion){
        $lc_query = "EXEC [config].[IAE_admin_cambio_estados_cambiar_pedido_entregado] '$lc_condicion[0]','$lc_condicion[1]', '$lc_condicion[2]', '$lc_condicion[3]'";

        if ($this->fn_ejecutarquery($lc_query)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array( "mensaje" => utf8_encode($row['mensaje']),
                    "estado" => $row['estado']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function cambioTipoAsignacion($lc_condicion){
        $lc_query = "EXEC [config].[IAE_admin_cambio_estados_cambioTipoAsignacion] '$lc_condicion[0]','$lc_condicion[1]'";

        $respuesta = $this->fn_ejecutarquery($lc_query);
        return $respuesta;
    }

    function getRiderAgregador($lc_condicion){
        // $lc_query = "EXEC [config].[IAE_admin_cambio_estados_cambioTipoAsignacion] '$lc_condicion[0]','$lc_condicion[1]'";

        // $respuesta = $this->fn_ejecutarquery($lc_query);
        // return $respuesta;
        $medio = strtoupper($lc_condicion[0]);
        $sql = new sql();
        $query="DECLARE @idColeccionCadena VARCHAR (50)
        SELECT m.IDMotorolo
        FROM ColeccionCadena cc
        INNER JOIN ColeccionDeDatosCadena cdc on cc.ID_ColeccionCadena =cdc.ID_ColeccionCadena
        INNER JOIN [dbo].[CadenaColeccionDeDatos] dcc on cdc.ID_ColeccionDeDatosCadena = dcc.ID_ColeccionDeDatosCadena
        INNER JOIN Motorolo M on cdc.Descripcion= m.empresa_motorolo
        WHERE cc.Descripcion = 'LISTA AGREGADORES' AND cc.isActive = '1' AND cdc.isActive = 1 and variableB='1' AND m.empresa_motorolo='$medio'";
        // echo "--->";
        // var_dump($query);
        // echo "--->";
        // exit;
        if(!$sql->fn_ejecutarquery($query)){
            // $this->customError(400,"failed getting RiderID Agregador :" .$medio);
        }
        $row = $sql->fn_leerarreglo();
        return isset($row['IDMotorolo']) ? $row['IDMotorolo'] : null;
    }

    function cambioEstadoOrdenEntregada($lc_condicion){
        $sql = new sql();
        $query="UPDATE Cabecera_App 
        SET IDMotorolo = '$lc_condicion[0]', 
        estado = 'ENTREGADO' WHERE codigo_app = '$lc_condicion[1]'";
        if(!$sql->fn_ejecutarquery($query)){
            http_response_code(500);
            die("Error");
        }
        $row = $sql->fn_leerarreglo();
        return true;
    }

}