<?php

namespace Maxpoint\Mantenimiento\adminReplicacion\Clases;

use Doctrine\DBAL\Connection;

class ReplicacionDistribuidorController extends Controller
{

    public function anularLoteDistribuidor($idUsuario, $idUsuarioAprobacion, $idCadena, $idLote, $observacion)
    {
        $query = "UPDATE LotesReplica set  
                  lastUser='$idUsuario', 
                  lastUpdate=GETDATE(),
                  usrAprobacionAnulacion ='$idUsuarioAprobacion',
                  observacion = N'$observacion',
                  IDStatus = config.fn_estado('Replicacion','Inactivo')
                  where IDLoteReplica = $idLote and cdn_id=$idCadena";
        $res = $this->ejecutarUpdate($query);
        return $res;
    }

    public function cargarTramasLote($idUsuario, $idCadena, $numeroLote = 0, $IDLote)
    {
        $query = "EXEC [dbo].[USP_ReplicacionLotesxCadena] 'cargarTramasLote', $idCadena,'$idUsuario', $IDLote, '$numeroLote',''";
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function cargarTramasLoteTienda($idUsuario, $idCadena, $IDLote, $idRestaurante)
    {
        $query = "EXEC [replica].[USP_ReplicacionLotesxTienda] 'cargarTramasLoteTienda', $idCadena, $idRestaurante, $IDLote";
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function cargarConexionesLote($idUsuario, $idCadena, $IDLote, $IdRestaurante)
    {
        $query = "EXEC [replica].[USP_ReplicacionLotesxTienda] 'cargarConexionesLote', $idCadena, $IdRestaurante, $IDLote";
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function cargarNumeroTramasLote($idUsuario, $idCadena, $IDLote, $IdRestaurante)
    {
        $query = "EXEC [replica].[USP_ReplicacionLotesxTienda] 'numeroTramasLote', $idCadena, $IdRestaurante, $IDLote";
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function cargarConexionesCadena($idCadena)
    {
        $query = "EXEC [replica].[USP_ReplicacionLotesxTienda] 'cargarConexionesCadena', $idCadena, 0, 0";
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function actualizarTramasReplicadasTienda($idUsuario, $idCadena, $IDLote, $IdRestaurante)
    {
        $query = "EXEC [replica].[USP_ReplicacionLotesxTienda] 'actualizarTramasReplicadasTienda', $idCadena, $IdRestaurante, $IDLote";
        error_log($query . "\n", 3, "actualizacionUpdateStore.txt");
        $res = $this->conexion->executeQuery($query);
        return $res;
    }

    public function actualizarTramasReplicaTiendaRollback($idUsuario, $idCadena, $IDLote, $IdRestaurante)
    {
        $query = "EXEC [replica].[USP_ReplicacionLotesxTienda] 'actualizarTramasReplicaTiendaRollback', $idCadena, $IdRestaurante, $IDLote";
        error_log($query . "\n", 3, "actualizacionUpdateStore.txt");
        $res = $this->conexion->executeQuery($query);
        return $res;
    }


    public function cargarTramasErrorLoteParcial($idUsuario, $idCadena, $numeroLote = 0, $IDLote)
    {
        $query = "EXEC [dbo].[USP_ReplicacionLotesxCadena] 'cargarTramasErrorLoteParcial', $idCadena,'$idUsuario', $IDLote, '$numeroLote',''";
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function aplicarReplicacionDistribuidor($idUsuario, $idCadena, $numeroLote, $IDLote)
    {
        $query = "EXEC [dbo].[USP_ReplicacionLotesxCadena] 'aplicarReplicacionDistribuidor',$idCadena, '$idUsuario', $IDLote, '$numeroLote',''";
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function cargarBasesReplicacionLote($idUsuario, $idCadena, $numeroLote = 0, $IDLote)
    {
        $query = "EXEC [dbo].[USP_ReplicacionLotesxCadena] 'cargarBasesReplicacionLote', $idCadena, '$idUsuario', $IDLote, '$numeroLote',''";
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function cargarLote($idUsuario, $idCadena, $numeroLote = 0, $IDLote)
    {
        $query = "EXEC [dbo].[USP_ReplicacionLotesxCadena] 'cargarLote',$idCadena, '$idUsuario',$IDLote, '$numeroLote',''";
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function actualizarEstadoLote($idUsuario, $idCadena, $numeroLote = 0, $IDLote, $nuevoEstado)
    {
        $query = "EXEC [dbo].[USP_ReplicacionLotesxCadena] 'actualizarEstadoLote', $idCadena, '$idUsuario', $IDLote, '$numeroLote', '$nuevoEstado'";
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function cargarTrama($idTrama)
    {
        $query = "SELECT ust.IDLoteReplica , ust.IDUpdateStore,ust.tabla,ust.trama,
                ust.mdl_id,ust.cdn_id,ust.rst_id,ust.usr_id,ust.Fecha,Hora,0 as replica,count(lt.estado) AS numeroTramasError
                FROM dbo.UpdateStoreTiendas ust INNER	JOIN	maxpoint_log.dbo.logtramas lt ON ust.IDLoteReplica = lt.IDLOTE		
                WHERE ust.IDUpdateStore	= $idTrama
				AND lt.ESTADO='ERROR'
				GROUP BY ust.IDLoteReplica , ust.IDUpdateStore,ust.tabla,ust.trama,
                ust.mdl_id,ust.cdn_id,ust.rst_id,ust.usr_id,ust.Fecha,Hora,replica";
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function cargarParametrosConexionTrama($idTrama)
    {
        $query = "SELECT br.IP,br.Instancia,br.DatabaseName as Databasename
                FROM dbo.BasesReplicacion br inner join dbo.UpdateStoreTiendas ust
                    ON ust.rst_id = br.rst_id	
                WHERE ust.IDUpdateStore	= $idTrama
                AND br.tipo=2";
        $res = $this->cargarDatos($query);
        return $res;
    }

    private function crearConsultaLogTramaTienda($trama)
    {
        $consulta = "INSERT INTO [MAXPOINT_LOG].[dbo].[LOGTRAMAS] (
                      IDLOTE,	IDTRAMA,	IDRESTAURANTE,	USR_ID,	
                      ERRORNUMBER,	ERRORMESSAGE,	LASTUSER,
                      LASTUPDATE,	ESTADO) values(
                      :IDLOTE, :IDTRAMA,	:IDRESTAURANTE,	:USR_ID,	
                      :ERRORNUMBER,	:ERRORMESSAGE, :LASTUSER,
                      :LASTUPDATE, :ESTADO)";

        $stmtTrama = $this->conexion->prepare($consulta);
        $stmtTrama->bindValue("IDLoteReplica", $trama["IDLoteReplica"]);
        $stmtTrama->bindValue("tabla", $trama["tabla"]);
        $stmtTrama->bindValue("trama", $trama["trama"]);
        $stmtTrama->bindValue("mdl_id", $trama["mdl_id"]);
        $stmtTrama->bindValue("cdn_id", $trama["cdn_id"]);
        $stmtTrama->bindValue("rst_id", $trama["rst_id"]);
        $stmtTrama->bindValue("usr_id", $trama["usr_id"]);
        $stmtTrama->bindValue("replica", $trama["replica"]);
        return $stmtTrama;
    }


    public function cargarBasesLocales($cadena)
    {
        $query = "SELECT br.IP,br.Instancia,br.DatabaseName as Databasename, r.rst_cod_tienda
                FROM dbo.BasesReplicacion br 	
                INNER JOIN dbo.Restaurante r on br.rst_id=r.rst_id
                WHERE br.cdn_id	=" . $cadena . " 
                AND br.tipo=2
                order by r.rst_cod_tienda";
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function prepararConsultaInsercionLote()
    {
        $sql = "SET DATEFORMAT YMD
            INSERT INTO [LotesReplica](
                IDLoteReplica, mdl_id, cdn_id, rst_id, 
                numeroLote, FechaCreacion, HoraCreacion, 
                FechaUpdate, HoraUpdate, UserCreacion, 
                UserUpdate, IDStatus)
                values(
                    :IDLoteReplica, :mdl_id, :cdn_id, :rst_id, 
                    :numeroLote, :FechaCreacion, :HoraCreacion, 
                    :FechaUpdate, :HoraUpdate, :UserCreacion, 
                    :UserUpdate, :IDStatus
                )";
        return $this->conexion->prepare($sql);
    }

    public function prepararConsultaInsercionTramas()
    {
        $sql = "SET DATEFORMAT YMD
            INSERT INTO 
              UpdateStore(
                IDUpdateStore, IDLoteReplica,
                tabla, trama,mdl_id, 
                cdn_id,rst_id, usr_id,
                Fecha, Hora, replica) 
              values(
                :IDUpdateStore, :IDLoteReplica,
                :tabla, :trama,:mdl_id, 
                :cdn_id,:rst_id, :usr_id,
                :Fecha, :Hora, :replica)";

        return $this->conexion->prepare($sql);
    }

    public function consultarBasesInactivas($cadena)
    {
        $query = "SELECT br.IP,br.Instancia,br.DatabaseName as Databasename, r.rst_cod_tienda
                FROM dbo.BasesReplicacion br 	
                INNER JOIN dbo.Restaurante r on br.rst_id=r.rst_id
                WHERE br.cdn_id	=" . $cadena . " 
                AND br.tipo=-1";
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function consultarProductosCadena($cadena)
    {
        $query = "SELECT count(plu_id) as Total
                FROM dbo.Plus p 
                where cdn_id=" . $cadena;
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function consultarBotones($cadena)
    {

        $query = "select COUNT(*) as Total
                from Menu_AgrupacionProducto map
                inner join Plus p on map.plu_id=p.plu_id
                where cdn_id =" . $cadena;
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function consultarBotonesMenu($cadena)
    {

        $query = "select COUNT(*) as Total
            from CategoriasBotones cb
            inner join Menu_AgrupacionProducto map on cb.IDMenuAgrupacionProducto=map.IDMenuAgrupacionProducto
            inner join Plus p on map.plu_id=p.plu_id
            where cdn_id=" . $cadena;

        $res = $this->cargarDatos($query);
        return $res;
    }

    public function consultarProductosSinPrecio($cadena)
    {
        $query = "Select count(*) as Total
            from Plus p 
            left join Precio_Plu pp on p.plu_id=pp.plu_id 
            where pp.plu_id is null
            and cdn_id=" . $cadena;
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function consultarPregunta($cadena)
    {

        $query = "select COUNT(*) as Total
                from Pregunta_Sugerida
                where cdn_id=" . $cadena;
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function consultarRespuesta($cadena)
    {

        $query = "select COUNT(*) as Total
                from Respuestas r
                inner join Plus p on r.plu_id=p.plu_id
                where p.cdn_id=" . $cadena;
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function consultarPluPregunta($cadena)
    {

        $query = "select COUNT(*) as Total
                from Plu_Pregunta pp
                inner join Plus p on pp.plu_id=p.plu_id
                where p.cdn_id=" . $cadena;
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function cargarBasesLocalesDrive($cadena)
    {

        $query = "SELECT br.IP,br.Instancia,br.DatabaseName as Databasename, r.rst_cod_tienda
                FROM dbo.BasesReplicacion br
                INNER JOIN dbo.Restaurante r on br.rst_id=r.rst_id
    			INNER JOIN dbo.Estacion e ON e.rst_id = r.rst_id
    			INNER JOIN dbo.EstacionColeccionDeDatos dc ON dc.IDEstacion = e.IDEstacion
    			INNER JOIN dbo.ColeccionDeDatosEstacion cdd ON cdd.ID_ColeccionDeDatosEstacion = dc.ID_ColeccionDeDatosEstacion
    			INNER JOIN dbo.ColeccionEstacion col ON col.ID_ColeccionEstacion = dc.ID_ColeccionEstacion
    			INNER JOIN dbo.Menu m ON CONVERT(VARCHAR(40), m.IDMenu) = cdd.idIntegracion
                WHERE br.cdn_id	=" . $cadena . "
                AND br.tipo=2
    			AND col.Descripcion = 'MENUS'
    			AND cdd.Descripcion = 'DRIVE'
    			AND m.menu_Nombre = 'DRIVE'
                order by r.rst_cod_tienda";
        $res = $this->cargarDatos($query);
        return $res;
    }

}
