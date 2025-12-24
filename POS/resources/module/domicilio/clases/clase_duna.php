<?php

class Duna extends sql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function updateOrdenDuna($idOrden, $idCliente, $lat, $lng, $codFactura, $codCliente, $telefono, $inmuenble, $codigoApp, $tramaDuna)
    {
        $query = "  UPDATE Cabecera_App SET codigo_externo = '$idOrden', latitud = $lat, longitud = $lng, cfac_id = '$codFactura', respuesta_duna = '$tramaDuna' 
                      WHERE codigo_app = '$codigoApp';";
        $result = $this->fn_ejecutarquery($query);
        if ($result){ return true; }else{ return false; };
    }

    public function eliminarUltimoEstadoPorAsignarDuna($codigoApp)
    {

        $query = "DELETE FROM Estado_Pedido_App WHERE IDEstadoPedido =
        (SELECT TOP 1 epa.IDEstadoPedido FROM Estado_Pedido_App epa WHERE epa.codigo_app = '$codigoApp' AND epa.estado = 'POR ASIGNAR' ORDER BY epa.IDEstadoPedido DESC);";
        $result = $this->fn_ejecutarquery($query);
        if ($result){ return true; }else{ return false; };
    }

    
    public function consultaIdDuna($codFactura, $estado)
    {
        $query = "SELECT cod_duna 
        FROM Factura_Domicilio 
        where Cod_Factura = '$codFactura' and estado in  ($estado)";

        $result = $this->fn_ejecutarquery($query);
        if ($result){ return true; }else{ return false; };
    }

    public function insertaAuditoriaDuna($url, $peticion, $estado, $mensaje)
    {
        $query = "INSERT INTO dbo.Auditoria_EstadosApp
            ( url, peticion, estado, mensaje, fecha )
        VALUES  
            ( '$url', '$peticion', '$estado', '$mensaje', GETDATE())";

        $result = $this->fn_ejecutarquery($query);
        if ($result){ return true; }else{ return false; };
    }
}
