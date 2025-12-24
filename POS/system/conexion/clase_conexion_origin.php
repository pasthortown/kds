<?php

if (!isset($_SESSION)) {
    session_start();
}

//////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Darwin Mora/////////////////////////
////////DESCRIPCION: Clase que permite la conexi�n con ///////
///////////////////  la base de datos                 ////////
///////TABLAS INVOLUCRADAS: No hay tablas solo exite  ////////
///////////////////  la base de datos en SQLServer2008 R2/////
///////FECHA CREACION: 25-11-2013/////////////////////////////
///////FECHA ULTIMA MODIFICACION:   //////////////////////////
///////USUARIO QUE MODIFICO: /////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: /////////////////////////////
//////////////////////////////////////////////////////////////
//Clase para realizar la conexión
class conexion {

    private $lc_host;
    private $lc_base;
    private $lc_conec;

//Constructor de la clase
    public function __construct() {
        $configuraciones = parse_ini_file('replica.ini', true);
        $rutaWebservice = $configuraciones['credencial']["urlCredenciales"];
        if (!isset($_SESSION["lc_host"]) || !isset($_SESSION["Database"]) || !isset($_SESSION["UID"]) || !isset($_SESSION["PWD"])) {
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
    }

//Función que permite conectarse a la base de datos
    public function fn_conectarse() {
        $lc_host = $_SESSION["lc_host"];
        $connectionInfo = array("Database" => $_SESSION["Database"], "UID" => $_SESSION["UID"], "PWD" => $this->descifrar($_SESSION["PWD"], 5));

        if (is_null($this->lc_conec)) {
            if (!($this->lc_conec = sqlsrv_connect($lc_host, $connectionInfo)
                //or die("<div style='text-align:center;'><div style='font:14px;color:white;background:#137DE8;width:50%;margin: 0 auto;'>ERROR!! al intentar conectarse con la base de datos <br> Confirmar el estado del Servicio de Credenciales en el servidor</div></div>"))) {
                )){
				
				echo "<pre><div style='text-align:justify;'><div style='font:14px;color:white;background:#137DE8;width:75%;margin: 0 auto;'>"; 
				print_r($this->fn_errorconec());
				echo "</div></div></pre>";
				exit();
            } elseif (!(sqlsrv_query($this->lc_conec, $this->lc_base))) {
				echo "<pre><div style='text-align:justify;'><div style='font:14px;color:white;background:#137DE8;width:75%;margin: 0 auto;'>"; 
				print_r($this->fn_errorconec());
				echo "</div></div></pre>";
				exit();
            }
        }
        return $this->lc_conec;
    }

//Generar un error en caso de que no se pueda realizar la conexión
    private function fn_errorconec() {
        return sqlsrv_errors();
    }

//Función que permite desconectarse a la base de datos
    public function fn_cerrarconec() {
        if (sqlsrv_close($this->lc_conec)) {
            return true;
        } else {
            return false;
        }
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