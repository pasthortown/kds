<?php


/////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Hugo Mera /////////////////////////////////
////////DESCRIPCION: Clase que permite realizar conexiones //////////
//////// en tiempo de ejecución          ////////////////////////////
///////////////////  sentencias SQL (sentencia inicial SELECT) //////
///////FECHA CREACION: 15-06-2016////////////////////////////////////
///////USUARIO QUE MODIFICO:  ///////////////////////////////////////
/////////////////////////////////////////////////////////////////////

include("clase_multiconexion.php");
//include("clase_conexion.php");

//Clase para realizar las diferentes sentencias SQL
class SqlMultiple {

    private $lc_conec;
    private $lc_datos;

    //constructor de la clase  
    function __construct($ambiente) {
        //if(isset($lc_usuario))$lc_usuario=$lc_usuario; else $lc_usuario=NULL;
        $this->lc_conec = new ConexionMultiple($ambiente);
        //$this->lc_conec = new conexion();
        $this->lc_datos = NULL;
    }

    //funcion que permite armar la sentencia sql
    public function fn_ejecutarquery($lc_query) {
        if ($lc_conec = $this->lc_conec->fn_conectarse()) {         
            if ($this->lc_datos = sqlsrv_query($lc_conec, $lc_query, array(), array("Scrollable" => "buffered"))) {
                $confirm = $this->lc_datos;
                if (is_resource($confirm)) {
                    return true;
                } else {
                    return $this->lc_datos;
                }
            } else{
                //die (print_r(sqlsrv_errors(), true));
                return false;
            }
        }
    }

    //funcion  devuelve dataset por objeto
    public function fn_leerobjeto() {
        return sqlsrv_fetch_object($this->lc_datos);
    }

    //funcion  devuelve dataset por arreglo
    public function fn_leerarreglo() {
        return sqlsrv_fetch_array($this->lc_datos);
    }

    //devolvuelve el numero de registros
    public function fn_numregistro() {
        return sqlsrv_num_rows($this->lc_datos);
    }

    //devuelve el numero de campos de un aconsulta sql
    public function fn_numcampo() {
        return sqlsrv_num_fields($this->lc_datos);
    }

    //liberar consulta y conexion es decir los recursos que esta utilizando
    public function fn_liberarecurso() {
        @mssql_free_result($this->lc_datos);
        $this->lc_conec->fn_cerrarconec();
    }

    public function fn_registrosafectados() {
        return sqlsrv_rows_affected($this->lc_datos);
    }

    public function crearArrayResultset()
    {
        $resultados = [];
        $reg = sqlsrv_fetch_array($this->lc_datos);
        while ($reg) {
            $resultados[] = $reg;
            $reg = sqlsrv_fetch_array($this->lc_datos);
        }
        return $resultados;
    }

    //funcion que ejecuta una sentencia y retorna los resultados como array
    public function fn_ejecutarquery2($lc_query)
    {
        $resultado = new stdClass();
        $resultado->estado = false;
        $lc_conec = $this->lc_conec->fn_conectarse();
        $errores = array();
        $warnings = array();

        if (!$lc_conec) {
            $e = sqlsrv_errors(SQLSRV_ERR_ERRORS);
            $w = sqlsrv_errors(SQLSRV_ERR_WARNINGS);
            if ($e) array_push($errores, $e);
            if ($w) array_push($warnings, $w);
            $resultado->errores = $errores;
            $resultado->warnings = $warnings;
            $resultado->datos[] = "Sin Conexion a la base de datos";
            return $resultado;
        }
        $this->lc_datos = sqlsrv_query($lc_conec, $lc_query, array(), array("Scrollable" => "buffered"));
        if (!is_resource($this->lc_datos)) {
            $e = sqlsrv_errors(SQLSRV_ERR_ERRORS);
            $w = sqlsrv_errors(SQLSRV_ERR_WARNINGS);
            if ($e) array_push($errores, $e);
            if ($w) array_push($warnings, $w);
            $resultado->errores = $errores;
            $resultado->warnings = $warnings;
            $resultado->datos[] = "Sin resultado desde la base de datos";
            return $resultado;
        }
        if ($this->lc_datos) {
            $resultado->datos[] = $this->crearArrayResultset();
            $next_result = sqlsrv_next_result($this->lc_datos);
            while ($next_result) {
                $resultado->datos[] = $this->crearArrayResultset();
                $next_result = sqlsrv_next_result($this->lc_datos);

                $e = sqlsrv_errors(SQLSRV_ERR_ERRORS);
                $w = sqlsrv_errors(SQLSRV_ERR_WARNINGS);
                if ($e) array_push($errores, $e);
                if ($w) array_push($warnings, $w);
            }
        }
        if ((count($errores) == 0) && (count($warnings) == 0)) {
            $resultado->estado = true;
        }

        $resultado->errores = $errores;
        $resultado->warnings = $warnings;

        return $resultado;
    }
}