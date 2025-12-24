<?php

/**
 * Created by PhpStorm.
 * User: fabricio.sierra
 * Date: 4/10/2018
 * Time: 9:55 AM
 */

namespace Maxpoint\Mantenimiento\adminReplicacion\Clases;

class ReplicacionTiendaController extends Controller
{

    public function insertarLoteTienda($lote){
        $resultado = array();
        $estado = false;
        $mensaje = "No se pudo insertar el lote.";
        //Crear el insert de [dbo].[LotesReplica] de la tienda
        $stmtInsercionLoteTienda = $this->crearConsultaInsercionLotesReplicaTienda($lote);
        //$conexion=print_r($this->conexion,true);
        //error_log($conexion."\n\n\n",3,"insercionLotes.txt");
        //Insertar el lote en la tienda
        try {
            $stmtInsercionLoteTienda->execute();
            $estado = true;
            $resultado["mensaje"] = "Lote insertado correctamente";
        } catch (PDOException $ex) {
            $resultado["mensaje"] = "Falló la inserción del lote: " . $ex->getMessage();
            error_log("RESTAURANTE:" . $lote["rst_id"] . "\n", 3, "insercionLotes.txt");
        } finally {
            $resultado["estado"] = $estado;
            $resultado["mensaje"] = $mensaje;

        }
        return $resultado;
    }

    public function insertarTramasTienda($tramasRestaurante)
    {
        $estadostramas = array();
        $numeroTramasCorrectas = 0;
        $estaConectado = $this->conexion->isConnected();
        if (!$estaConectado) {
            foreach ($tramasRestaurante as $trama) {
                $estadostramas[] = array(
                    "IDLOTE" => $trama["IDLoteReplica"],
                    "IDTRAMA" => $trama["IDUpdateStore"],
                    "IDRESTAURANTE" => $trama["rst_id"],
                    "USR_ID" => 0,
                    "ESTADO" => "ERROR",
                    "ERRORNUMBER" => 0,
                    "ERRORMESSAGE" => "No se pudo conectar con la base del local",
                    "LASTUSER" => '',
                    "LASTUPDATE" => "GETDATE()",
                );
            }
        } else {
            $this->conexion->beginTransaction();
            try {
                $stmtInsercionTramaUpdateStore = $this->crearConsultaInsercionUpdateStoreTienda($trama);
                foreach ($tramasRestaurante as $trama) {
                    //Crear el statement de insercion de la trama en el UpdateStore de la tienda
                    $stmtInsercionTramaUpdateStore->bindValue("IDLoteReplica", $trama["IDLoteReplica"]);
                    $stmtInsercionTramaUpdateStore->bindValue("tabla", $trama["tabla"]);
                    $stmtInsercionTramaUpdateStore->bindValue("trama", $trama["trama"]);
                    $stmtInsercionTramaUpdateStore->bindValue("mdl_id", $trama["mdl_id"]);
                    $stmtInsercionTramaUpdateStore->bindValue("cdn_id", $trama["cdn_id"]);
                    $stmtInsercionTramaUpdateStore->bindValue("rst_id", $trama["rst_id"]);
                    $stmtInsercionTramaUpdateStore->bindValue("usr_id", $trama["usr_id"]);
                    $stmtInsercionTramaUpdateStore->bindValue("fecha", $trama["Fecha"]);
                    $stmtInsercionTramaUpdateStore->bindValue("hora", $trama["Hora"]);
                    $stmtInsercionTramaUpdateStore->bindValue("replica", $trama["replica"]);

                    try {
                        $stmtInsercionTramaUpdateStore->execute();
                        $numeroTramasCorrectas++;
                        $estadostramas[] = array(
                            "IDLOTE" => $trama["IDLoteReplica"],
                            "IDTRAMA" => $trama["IDUpdateStore"],
                            "IDRESTAURANTE" => $trama["rst_id"],
                            "USR_ID" => 0,
                            "ESTADO" => "OK",
                            "ERRORNUMBER" => 0,
                            "ERRORMESSAGE" => "Insertada correctamente",
                            "LASTUSER" => '',
                            "LASTUPDATE" => "GETDATE()",
                        );
                    } catch (\PDOException $e) {
                        $estadostramas[] = array(
                            "IDLOTE" => $trama["IDLoteReplica"],
                            "IDTRAMA" => $trama["IDUpdateStore"],
                            "IDRESTAURANTE" => $trama["rst_id"],
                            "USR_ID" => 0,
                            "ESTADO" => "ERROR",
                            "ERRORNUMBER" => 0,
                            "ERRORMESSAGE" => $e->getMessage(),
                            "LASTUSER" => '',
                            "LASTUPDATE" => "GETDATE()",
                        );
                    }
                }
                $this->conexion->commit();
            } catch (\Exception $e) {
                $this->conexion->rollBack();
            }
        }
        return array($estadostramas,$numeroTramasCorrectas);
    }

    public function crearConsultaInsercionLotesReplicaTienda($lote, $idRestaurante)
    {
        $consulta = "SET DATEFORMAT YMD
                    IF  NOT EXISTS(
                            SELECT IDLOTEREPLICA  FROM LotesReplica 
                            WHERE IdLoteReplica  = ?
                        )
                        BEGIN
                            INSERT INTO [dbo].[LotesReplica](
                            IDLoteReplica, mdl_id, cdn_id, rst_id, 
                            numeroLote, FechaCreacion, HoraCreacion, 
                            FechaUpdate, HoraUpdate, UserCreacion, 
                            UserUpdate, IDStatus) 
                            values (?, ?, ?, ?,?, ?, ?,?, ?, ?,?, 
                                    (SELECT config.fn_estado( 'Replicacion', 'Pendiente' ))
                            )
                        SELECT 1 as resultado
                        END
                        ELSE SELECT 0 as resultado";
        return $this->conexion->prepare($consulta);
    }


    public function crearConsultaInsercionUpdateStoreTienda()
    {
        $consulta = "SET DATEFORMAT YMD
            INSERT INTO [dbo].[UpdateStore] (IDLoteReplica, tabla, 
              trama, mdl_id, cdn_id, rst_id, usr_id, Fecha, Hora, replica) 
              values(:IDLoteReplica, :tabla, :trama, :mdl_id, :cdn_id, 
              :rst_id, :usr_id, :fecha, :hora, :replica)";

        $stmtTrama = $this->conexion->prepare($consulta);
        return $stmtTrama;
    }

    private function crearConsultaInsercionTramaTienda()
    {
        
    }

    public function consultarProductosTienda()
    {
        $query = "SELECT count(plu_id) as Total
                FROM dbo.Plus p ";
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function consultarProductosSinPrecioTienda()
    {
        $query = "Select count(*) as Total
            from Plus p 
            left join Precio_Plu pp on p.plu_id=pp.plu_id 
            where pp.plu_id is null";
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function consultarLotesTienda()
    {

        $query = "select lr.numeroLote, s.std_descripcion as Estado, lr.FechaCreacion as Fecha
            from LotesReplica lr
            inner join Status s on lr.IDStatus = s.IDStatus
            where (lr.IDStatus=(SELECT config.fn_estado('Replicacion', 'Error'))
            OR lr.IDStatus=(SELECT config.fn_estado('Replicacion', 'Pendiente')))
            AND (lr.FechaCreacion > DATEADD(MONTH,-1,GETDATE()))
            order by numeroLote";

        $res = $this->cargarDatos($query);
        return $res;
    }

    public function consultarBotonesTienda()
    {

        $query = "select COUNT(*) as Total
                from Menu_AgrupacionProducto map
                inner join Plus p on map.plu_id=p.plu_id
                ";
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function consultarBotonesMenuTienda()
    {

        $query = "select COUNT(*) as Total
            from CategoriasBotones cb
            inner join Menu_AgrupacionProducto map on cb.IDMenuAgrupacionProducto=map.IDMenuAgrupacionProducto
            inner join Plus p on map.plu_id=p.plu_id
            ";

        $res = $this->cargarDatos($query);
        return $res;
    }

    public function consultarPeriodoTienda()
    {
        $query = "
            set dateformat YMD
            select (case when pr.idstatus='67039503-85CF-E511-80C6-000D3A3261F3' then 'CERRADO'
            else 'ABIERTO' end) as Estado
            from Periodo pr
            inner join Status st on st.IDStatus=pr.IDStatus
            where DATEADD(DAY,-1, convert(varchar,GETDATE(),23))=convert(varchar,prd_fechaapertura,23)
            order by pr.prd_fechaapertura desc";

        $res = $this->cargarDatos($query);
        return $res;


    }

    public function consultarPreguntaTienda()
    {

        $query = "select COUNT(*) as Total
                from Pregunta_Sugerida
                ";
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function consultarRespuestaTienda()
    {

        $query = "select COUNT(*) as Total
                    from Respuestas r
                ";
        $res = $this->cargarDatos($query);
        return $res;
    }

    public function consultarPluPreguntaTienda()
    {

        $query = "select COUNT(*) as Total
                from Plu_Pregunta pp
                ";
        $res = $this->cargarDatos($query);
        return $res;
    }
    public function consultarVentaDriveTienda()
    {
        $query = "--declaracion de variables
                DECLARE @porcentajeIVA FLOAT ,
                    @cadena INT ,
                    @restaurante INT,
                	@fecha		DATE;
                
                --seteo de variables
                SELECT  @cadena = cdn_id ,
                        @restaurante = rst_id
                FROM    dbo.Restaurante;
                
                SET @fecha =GETDATE()
                --select final. Se recalcula la venta neta.
                SELECT  cab.rst_id AS Cod_Restaurante ,
                        COUNT(DISTINCT cab.cfac_id) AS Transaccion ,
                        SUM(det.dtfac_total) AS VentaBruta ,
                        --( SUM(det.dtfac_total) ) / @porcentajeIVA AS VentaNeta ,
                             SUM(det.dtfac_total) / 
                                    (CASE
                                           WHEN   FLOOR((SELECT   CAST(MAX(porcentaje) AS DECIMAL(38, 12)) AS porcentaje
                                                                  FROM     ( SELECT TOP ( 1 )
                                                                                                        valorImpuesto1 ,
                                                                                                        valorImpuesto2 ,
                                                                                                        valorImpuesto3 ,
                                                                                                        valorImpuesto4 ,
                                                                                                        valorImpuesto5
                                                                                     FROM      Cabecera_Factura
                                                                                     WHERE     CONVERT(DATE, cfac_fechacreacion) > CONVERT(DATE, per.prd_fechaapertura)
                                                                                                        AND IDStatus = config.fn_estado('facturación','Entregada')
                                                                                   ) AS a UNPIVOT 
                                                                                                 ( porcentaje FOR valorImpuestos IN ( valorImpuesto1,
                                                                                                                                    valorImpuesto2,
                                                                                                                                    valorImpuesto3,
                                                                                                                                    valorImpuesto4,
                                                                                                                                    valorImpuesto5 ) ) AS u
                                                               )) != ceiling((SELECT   CAST(MAX(porcentaje) AS DECIMAL(38, 12)) AS porcentaje
                                                                                      FROM     ( SELECT TOP ( 1 )
                                                                                                                            valorImpuesto1 ,
                                                                                                                            valorImpuesto2 ,
                                                                                                                            valorImpuesto3 ,
                                                                                                                            valorImpuesto4 ,
                                                                                                                            valorImpuesto5
                                                                                                          FROM      Cabecera_Factura
                                                                                                          WHERE     CONVERT(DATE, cfac_fechacreacion) > CONVERT(DATE, per.prd_fechaapertura)
                                                                                                                            AND IDStatus = config.fn_estado('facturación','Entregada')
                                                                                                        ) AS a UNPIVOT 
                                                                                                                     ( porcentaje FOR valorImpuestos IN ( valorImpuesto1,
                                                                                                                                                         valorImpuesto2,
                                                                                                                                                         valorImpuesto3,
                                                                                                                                                         valorImpuesto4,
                                                                                                                                                         valorImpuesto5 ) ) AS u
                                                                                   )) THEN ( SELECT   (porcentaje / 100 ) + 1
                                                                                                    FROM              config.fn_ColeccionRestaurante_Impuestos(@cadena,@restaurante)
                                                                                                 )
                                           ELSE ((SELECT   CAST(MAX(porcentaje) AS DECIMAL(38, 12)) AS porcentaje
                                                        FROM     ( SELECT TOP ( 1 )
                                                                                          valorImpuesto1 ,
                                                                                          valorImpuesto2 ,
                                                                                          valorImpuesto3 ,
                                                                                          valorImpuesto4 ,
                                                                                          valorImpuesto5
                                                                             FROM      Cabecera_Factura
                                                                             WHERE     CONVERT(DATE, cfac_fechacreacion) > CONVERT(DATE, per.prd_fechaapertura)
                                                                                          AND IDStatus = config.fn_estado('facturación','Entregada')
                                                                      ) AS a UNPIVOT 
                                                                                   ( porcentaje FOR valorImpuestos IN ( valorImpuesto1,
                                                                                                                            valorImpuesto2,
                                                                                                                            valorImpuesto3,
                                                                                                                            valorImpuesto4,
                                                                                                                            valorImpuesto5 ) ) AS u
                                                        ) / 100) + 1
                                    END) AS VentaNeta,
                        per.prd_fechaapertura AS FechaPeriodo
                FROM    [dbo].[Cabecera_Factura] cab
                        INNER JOIN [dbo].[Detalle_Factura] det ON cab.cfac_id = det.cfac_id
                        INNER JOIN [dbo].[Plus] plu ON det.plu_id = plu.plu_id
                        INNER JOIN [dbo].[Clasificacion] cla ON plu.IDClasificacion = cla.IDClasificacion
                        INNER JOIN [dbo].[Periodo] per ON per.IDPeriodo = cab.IDPeriodo
                WHERE   cla.cla_Nombre = 'DRIVE'
                        AND cab.IDStatus = config.fn_estado('facturación', 'Entregada')
                             and per.prd_fechaapertura < CONVERT(VARCHAR(20), @fecha, 112) 
                             and not exists(select      1 
                                                        from   [SRVV-ZEUS\SQL2016].[MAXPOINT_DISTRIB_19].dbo.RegaliasYUM ry 
                                                        where  ry.Cod_Restaurante = cab.rst_id 
                                                                      and ry.FechaPeriodo = per.prd_fechaapertura) 
                GROUP BY cab.rst_id ,
                        per.prd_fechaapertura
                ORDER BY cab.rst_id ,
                        per.prd_fechaapertura;

                ";
        $res = $this->cargarDatos($query);
        return $res;
    }

}
