<?php
/**
 * Created by PhpStorm.
 * User: fabricio.sierra
 * Date: 4/10/2018
 * Time: 9:55 AM
 */

namespace Maxpoint\Mantenimiento\adminReplicacion\Clases;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOException;
use Doctrine\DBAL\DBALException;

class Controller
{
    protected $conexion;

    public function __construct(Connection $conn)
    {
        $this->conexion = $conn;
    }

    public function cargarDatosMultiResultset($consulta){
        $estado = 1;
        $errores = array();
        $datos = array();
        try {
            $res = $this->conexion->query($consulta);
        } catch (DBALException $ex) {
            $estado = 0;
            $errores[] = $ex->getMessage();
        } catch (PDOException $ex) {
            $estado = 0;
            $errores[] = $ex->getMessage();
        }

        do {
            try {
                $datos[] = $res->fetchAll(\PDO::FETCH_ASSOC);
            }
            catch (\Exception $ex) {
                $estado = 0;
                $errores[] = $ex->getMessage();
            }
        } while($res->nextRowset());

        return [
            "estado" => $estado,
            "errores" => $errores,
            "datos" => $datos,
        ];
    }

    public function cargarDatos($consulta)
    {
        $estado = 1;
        $errores = array();
        $datos = array();
        try {
            $res = $this->conexion->query($consulta);
            $datos = $res->fetchAll();
        } catch (DBALException $ex) {
            $estado = 0;
            $errores[] = $ex->getMessage();
        } catch (PDOException $ex) {
            $estado = 0;
            $errores[] = $ex->getMessage();
        }

        return [
            "estado" => $estado,
            "errores" => $errores,
            "datos" => $datos,
        ];
    }

    public function cargarDatosJSON($consulta,$detenerEnError = false)
    {
        $res=$this->cargarDatos($consulta);

        if($detenerEnError&&(0===$res["estado"])){
            $this->enviarRespuestaJson($res);
        }

        return $res;
    }


    public function ejecutarUpdate($sqlUpdate){
        $estado = 1;
        $errores = array();
        $datos = array();
        try {
            $res = $this->conexion->executeUpdate($sqlUpdate);
            $datos["filasAfectadas"]=$res;
        } catch (DBALException $ex) {
            $estado = 0;
            $errores[] = $ex->getMessage();
        } catch (PDOException $ex) {
            $estado = 0;
            $errores[] = $ex->getMessage();
        }

        return [
            "estado" => $estado,
            "errores" => $errores,
            "datos" => $datos,
        ];
    }

    /**
     * @return Connection
     */
    public function getDateTimeFormatString()
    {
        return $this->conexion->getDatabasePlatform()->getDateTimeFormatString();
    }

    function enviarRespuestaJson($resultadoFinal){
        header('Content-Type: application/json');
        print(json_encode($resultadoFinal));
        die();
    }
}