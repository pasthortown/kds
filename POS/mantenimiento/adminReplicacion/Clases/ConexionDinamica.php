<?php
/**
 * Created by PhpStorm.
 * User: fabricio.sierra
 * Date: 4/10/2018
 * Time: 12:34 PM
 */

namespace Maxpoint\Mantenimiento\adminReplicacion\Clases;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Driver\PDOException;

class ConexionDinamica
{
    private $parametros;

    public function __construct()
    {
        $rutaArchivo = join(DIRECTORY_SEPARATOR, array(dirname(dirname(dirname(dirname(__FILE__)))), 'system', 'conexion', 'replica.ini'));// ['system','conexion','replica.ini']);
        $this->parametros = parse_ini_file($rutaArchivo, true);
    }

    function conexionAzure()
    {
        $parametrosConexion = array(
            'dbname' => $this->parametros['azure']['db.config.dbname'],
            'host' => $this->parametros['azure']['db.config.host'] . '\\' . $this->parametros['azure']['db.config.instancia'],
            'user' => $this->parametros['azure']['db.config.username'],
            'password' => $this->parametros['azure']['db.config.password'],
            'driver' => 'pdo_sqlsrv',
        );
        $conexion = DriverManager::getConnection($parametrosConexion);
        try {
            $conexion->connect();
        } catch (PDOException $ex) {
            die("No se pudo conectar con la base de datos Azure");
        }
        return $conexion;
    }

    function conexionLogDistribuidor()
    {
        $parametrosConexion = array(
            'dbname' => $this->parametros['logonpremise']['db.config.dbname'],
            'host' => $this->parametros['logonpremise']['db.config.host'] . '\\' . $this->parametros['onpremise']['db.config.instancia'],
            'user' => $this->parametros['logonpremise']['db.config.username'],
            'password' => $this->parametros['logonpremise']['db.config.password'],
            'driver' => 'pdo_sqlsrv',
        );
        $conexion = DriverManager::getConnection($parametrosConexion);
        try {
            $conexion->connect();
        } catch (PDOException $ex) {
            die("No se pudo conectar con la base de datos de Log del distribuidor: " . $ex->getMessage());
        }
        return $conexion;
    }

    function conexionDistribuidor()
    {
        $parametrosConexion = array(
            'dbname' => $this->parametros['onpremise']['db.config.dbname'],
            'host' => $this->parametros['onpremise']['db.config.host'] . '\\' . $this->parametros['onpremise']['db.config.instancia'],
            'user' => $this->parametros['onpremise']['db.config.username'],
            'password' => $this->parametros['onpremise']['db.config.password'],
            'driver' => 'pdo_sqlsrv',
        );
        $conexion = DriverManager::getConnection($parametrosConexion);
        try {
            $conexion->connect();
        } catch (PDOException $ex) {
            die("No se pudo conectar con la base de datos Distribuidor");
        }
        return $conexion;
    }

    function conexionTienda(){
        if (!isset($_SESSION["lc_host"]) || !isset($_SESSION["Database"]) || !isset($_SESSION["UID"]) || !isset($_SESSION["PWD"])) {
            $rutaWebservice = $this->parametros['credencial']['urlCredenciales'];
            try {
                $response = file_get_contents($rutaWebservice . ".php");
                $response = json_decode($response);
                $_SESSION["lc_host"] = $response->servidor;
                $_SESSION["Database"] = $response->base;
                $_SESSION["UID"] = $response->usuario;
                $_SESSION["PWD"] = $response->clave;
                $this->lc_conec = NULL;
            } catch (Exception $e) {
                return $e;
            }
        }

        $parametrosConexion = array(
            'dbname' => $_SESSION["Database"],
            'host' => $_SESSION["lc_host"],
            'user' => $_SESSION["UID"],
            'password' => $this->descifrar($_SESSION["PWD"], 5),
            'driver' => 'pdo_sqlsrv',
        );
        $conexion = DriverManager::getConnection($parametrosConexion);
        try {
            $conexion->connect();
        } catch (PDOException $ex) {
            die("No se pudo conectar con la base en la tienda");
        }
        return $conexion;

    }

    function conexion($parametrosConexion)
    {
        $conexion = DriverManager::getConnection($parametrosConexion);
        return $conexion;
        /* try {
            $conexion->connect();
            return $conexion;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
        */
    }

    private function descifrar($original, $clave) {
        // Proceso inverso del cifrado
        $descifrado = '';
        $ASCIIcifrado = 0;
        //$n = 0;
        for ($i = 0; $i < strlen($original); $i++) {
            $ASCIIcifrado = ord(substr($original, $i));
            $ASCIIcifrado = $ASCIIcifrado - $clave % 255;
            $descifrado = $descifrado . chr($ASCIIcifrado);
        }
        return $descifrado;
    }

}