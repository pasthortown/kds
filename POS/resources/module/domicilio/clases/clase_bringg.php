<?php

/*/////////////////////////////////////////////////////
/// DESARROLLADO POR: Vanessa Soria                 ///
/// DESCRIPCION: clase que contiene sentencias sql  ///
/// para el proceso de bringg                       ///
/// FECHA CREACION: 09/11/2018                      ///
/// FECHA ULTIMA MODIFICACION:   06/08/2020        ///
/// USUARIO QUE MODIFICO:  Aldo Navarrete          ///
/// DECRIPCION ULTIMO CAMBIO: Adaptacion para      /// 
///              funcionamiento con MAXPOINT       ///
/////////////////////////////////////////////////////
*/

class Bringg extends sql
{
    public function __construct()
    {
        parent::__construct();
    }


    public function datosDetalle($codFactura)
    {
        $query = "SELECT p.plu_descripcion as Descripcion, df.dtfac_cantidad*dtfac_precio_unitario as precio, df.dtfac_cantidad as Cantidad, p.plu_id as Cod_Plu
            FROM Cabecera_Factura		f
            INNER JOIN Detalle_Factura	df	ON	df.cfac_id = f.cfac_id 
            INNER JOIN Restaurante		r	ON	r.rst_id = f.rst_id
            INNER JOIN Plus				p	ON	p.plu_id = df.plu_id
            WHERE f.cfac_id = '$codFactura' ";

        $result = $this->fn_ejecutarquery($query);
        if ($result){ return true; }else{ return false; };
    }

    public function updateOrden($idOrden, $idCliente, $lat, $lng, $codFactura, $codCliente, $telefono, $inmuenble, $codigoApp, $tramaBringg)
    {
        $query = "  UPDATE Cabecera_App SET codigo_externo = '$idOrden', latitud = $lat, longitud = $lng, cfac_id = '$codFactura', respuesta_bringg = '$tramaBringg' 
                      WHERE codigo_app = '$codigoApp';";
        $result = $this->fn_ejecutarquery($query);
        if ($result){ return true; }else{ return false; };
    }

    public function eliminarUltimoEstadoPorAsignar($codigoApp)
    {

        $query = "DELETE FROM Estado_Pedido_App WHERE IDEstadoPedido =
        (SELECT TOP 1 epa.IDEstadoPedido FROM Estado_Pedido_App epa WHERE epa.codigo_app = '$codigoApp' AND epa.estado = 'POR ASIGNAR' ORDER BY epa.IDEstadoPedido DESC);";
        $result = $this->fn_ejecutarquery($query);
        if ($result){ return true; }else{ return false; };
    }

    
    public function consultaIdBringg($codFactura, $estado)
    {
        $query = "SELECT cod_Bringg 
        FROM Factura_Domicilio 
        where Cod_Factura = '$codFactura' and estado in  ($estado)";

        $result = $this->fn_ejecutarquery($query);
        if ($result){ return true; }else{ return false; };
    }

    public function insertaAuditoria($url, $peticion, $estado, $mensaje)
    {
        $query = "INSERT INTO dbo.Auditoria_EstadosApp
            ( url, peticion, estado, mensaje, fecha )
        VALUES  
            ( '$url', '$peticion', '$estado', '$mensaje', GETDATE())";

        $result = $this->fn_ejecutarquery($query);
        if ($result){ return true; }else{ return false; };
    }
}
