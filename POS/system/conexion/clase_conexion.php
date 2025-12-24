<?php

if (!isset($_SESSION)) {
    session_start();
}
class Conexion{
    private $server;
    private $user;
    private $password;
    private $database;
    private $conexion;
    const DEBUG = false;

    public function __construct()
    {
        error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);
        if (!self::DEBUG){

            $configuraciones = parse_ini_file('replica.ini', true);
            $rutaWebservice = $configuraciones['credencial']["urlCredenciales"];

            if (!isset($_SESSION["lc_host"]) || !isset($_SESSION["Database"]) || !isset($_SESSION["UID"]) || !isset($_SESSION["PWD"])) {

                try {

                    $response = file_get_contents($rutaWebservice);
                    $response = json_decode($response);
                    $this->server   = $_SESSION["lc_host"]  = $response->servidor;
                    $this->database = $_SESSION["Database"] = $response->base;
                    $this->user     = $_SESSION["UID"]      = $response->usuario;
                    $this->password = $_SESSION["PWD"]      = $response->clave;
                    $this->conexion = NULL;
                    
                }catch (Exception $e){
                    echo "ErrorC: " . $e->getMessage();
                }

            }else{

                    $this->server   = $_SESSION["lc_host"];
                    $this->database = $_SESSION["Database"];
                    $this->user     = $_SESSION["UID"];
                    $this->password = $_SESSION["PWD"];
                    $this->conexion = NULL;
            }

        }else{
            $this->server   = $_SESSION["lc_host"]  = "192.168.101.30\maxpoint16";//"192.168.101.30\maxpoint16";//$response->servidor;
            $this->database = $_SESSION["Database"] = "MAXPOINT_E019_LINUX";//"maxpoint_1.9.16.3.3";//$response->base;
            $this->user     = $_SESSION["UID"]      = "sis_maxpoint";//"sis_maxpoint";//$response->usuario;
            $this->password = $_SESSION["PWD"]      = "maxpoint*88";//"maxpoint*88";//$response->clave;
        }
    }

public function fn_conectarse()
{

    try {
        $this->conexion = new PDO("sqlsrv:server=$this->server;Database=$this->database", $this->user, $this->descifrar($this->password,5));
        $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $this->conexion;
    } catch (PDOException $e) {
        echo "ErrorC: " . $e->getMessage();
    }
}


//Función que permite desconectarse a la base de datos
    public function fn_cerrarconec() {
        $this->conexion = NULL;
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