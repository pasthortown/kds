<?php
/**
 * Created by PhpStorm.
 * User: fabricio.sierra
 * Date: 4/10/2018
 * Time: 9:55 AM
 */

namespace Maxpoint\Mantenimiento\adminReplicacion\Clases;
use Doctrine\DBAL\Connection;

class ReplicacionAzureController extends Controller
{


    public function replicacionAzure($idUsuario, $idCadena, $idModulo)
    {
        //$query = "EXEC dbo.USP_ReplicacionDownDistribuidorPorModulo " . $idCadena . ", " . $idModulo;
        $query = "EXEC dbo.USP_ReplicacionDownDistribuidorPorModulo_V2 'crearLoteAzure'," . $idCadena . ", " . $idModulo . ",'','" . $idUsuario . "'";
        error_log($query . "\n", 3, "queryLoteAzure.txt");
        $res = $this->cargarDatosMultiResultset($query);
        return $res;
    }

    public function actualizarEstados($idUsuario, $idCadena, $idModulo, $idLoteReplica)
    {
        $query = "EXEC dbo.USP_ReplicacionDownDistribuidorPorModulo_V2 'actualizarEstadoLoteAzure'," . $idCadena . ", " . $idModulo . ",'" . $idLoteReplica . "','" . $idUsuario . "'";
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function consultarProductosAzure($cadena)
    {
        $query = "SELECT count(plu_id) as Total
                FROM dbo.Plus p 
                where cdn_id=" . $cadena;
        $res = $this->cargarDatos($query);
        return $res;        
        
    }

    public function consultarCadenas()
    {

        $query = "SELECT c.cdn_id as cadena, c.cdn_descripcion as nombreCadena
            FROM dbo.BasesReplicacion br 	
            inner join Cadena c on c.cdn_id=br.cdn_id
            WHERE br.tipo=2
            group by c.cdn_id,c.cdn_descripcion
            order by c.cdn_id";
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function consultarBotonesAzure($cadena)
    {

        $query = "select COUNT(*) as Total
                from Menu_AgrupacionProducto map
                inner join Plus p on map.plu_id=p.plu_id
                where cdn_id =" . $cadena;
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function consultarBotonesMenuAzure($cadena)
    {

        $query = "select COUNT(*) as Total
            from CategoriasBotones cb
            inner join Menu_AgrupacionProducto map on cb.IDMenuAgrupacionProducto=map.IDMenuAgrupacionProducto
            inner join Plus p on map.plu_id=p.plu_id
            where cdn_id=" . $cadena;

        $res = $this->cargarDatos($query);
        return $res;
    }

    public function consultarProductosSinPrecioAzure($cadena)
    {
        $query = "Select count(*) as Total
            from Plus p 
            left join Precio_Plu pp on p.plu_id=pp.plu_id 
            where pp.plu_id is null
            and cdn_id=" . $cadena;
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function consultarPreguntaAzure($cadena)
    {

        $query = "select COUNT(*) as Total
                from Pregunta_Sugerida
                where cdn_id=" . $cadena;
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function consultarRespuestaAzure($cadena)
    {

        $query = "select COUNT(*) as Total
                from Respuestas r
                inner join Plus p on r.plu_id=p.plu_id
                where p.cdn_id=" . $cadena;
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function consultarPluPreguntaAzure($cadena)
    {

        $query = "select COUNT(*) as Total
                from Plu_Pregunta pp
                inner join Plus p on pp.plu_id=p.plu_id
                where p.cdn_id=" . $cadena;
        $res = $this->cargarDatos($query);
        return $res;
    }


    //
    //public function aplicarReplicacionDistribuidor($idCadena, $lote)
    //{
    //    $query = "EXEC [dbo].[USP_ReplicacionLotesxCadena] 2, '03602BCD-E0CE-E711-80D0-000D3A019254', " . $idCadena . ", '" . $lote . "'";
    //    $res = $this->cargarDatos($query);
    //    return $res;
    //}

}