<?php

/////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Darwin Mora ///////////////////////////////
////////DESCRIPCION: Clase que permite realizar algunas /////////////
///////////////////  sentencias SQL (sentencia inicial SELECT) //////
///////TABLAS INVOLUCRADAS: Diferentes de acuerdo a la consulta /////
///////FECHA CREACION: 25-11-2013////////////////////////////////////
///////USUARIO QUE MODIFICO:  ///////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Creaci�n de la funci�n  ////////////
////fn_numcampo (devuelve el numero de campos de un aconsulta sql) //
/////////////////////////////////////////////////////////////////////
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);

include("clase_conexion.php");

//Clase para realizar las diferentes sentencias SQL
class sql {

    private $lc_conec;
    private $lc_datos;

    //constructor de la clase  
    function __construct() {
        //if(isset($lc_usuario))$lc_usuario=$lc_usuario; else $lc_usuario=NULL; 
        $this->lc_conec = new conexion();
        $this->lc_datos = NULL;
    }

    //funcion que permite armar la sentencia sql
    public function fn_ejecutarquery($lc_query) {
        if ($lc_conec = $this->lc_conec->fn_conectarse()) {
            $this->lc_datos = $lc_conec->prepare($lc_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL, PDO::SQLSRV_ATTR_CURSOR_SCROLL_TYPE => PDO::SQLSRV_CURSOR_BUFFERED));
            $this->lc_datos->execute();
            if ($this->lc_datos) {
                return $this->lc_datos;
            } else {
                return false;
            }
        }
    }

    //funcion  devuelve dataset por objeto
    public function fn_leerobjeto() {
        return $this->lc_datos->fetch(PDO::FETCH_OBJ);
    }

    //funcion  devuelve dataset por arreglo
    public function fn_leerarreglo() {
        return $this->lc_datos->fetch(PDO::FETCH_ASSOC);
    }

    //devolvuelve el numero de registros
    public function fn_numregistro() {
        return $this->lc_datos->rowCount();
    }

    //devuelve el numero de campos de un aconsulta sql
    public function fn_numcampo() {
        return $this->lc_datos->columnCount();
    }

    //liberar consulta y conexion es decir los recursos que esta utilizando
    public function fn_liberarecurso() {
        //@mssql_free_result($this->lc_datos);
        $this->lc_conec->fn_cerrarconec();
    }

    public function fn_registrosafectados() {
        return $this->lc_datos->rowCount();
    }

    public function fn_ejecutarquery2($lc_query) {
        $resultado = new stdClass();
        $resultado->estado = false;
        $lc_conec = $this->lc_conec->fn_conectarse();
        $errores = array();
        $warnings = array();
        $resultado->numRegistros = 0;

        if (!$lc_conec) {
            $e = sqlsrv_errors(SQLSRV_ERR_ERRORS);
            $w = sqlsrv_errors(SQLSRV_ERR_WARNINGS);
            if ($e) {
                array_push($errores, $e);
            }
            if ($w) {
                array_push($warnings, $w);
            }
            $resultado->errores = $errores;
            $resultado->warnings = $warnings;
            $resultado->datos = "Sin Conexion a la base de datos";
            return $resultado;
        }
        $this->lc_datos = sqlsrv_query($lc_conec, $lc_query, array(), array("Scrollable" => "buffered"));
        if (!is_resource($this->lc_datos)) {
            $e = sqlsrv_errors(SQLSRV_ERR_ERRORS);
            $w = sqlsrv_errors(SQLSRV_ERR_WARNINGS);
            if ($e) {
                array_push($errores, $e);
            }
            if ($w) {
                array_push($warnings, $w);
            }
            $resultado->errores = $errores;
            $resultado->warnings = $warnings;
            $resultado->datos = "Sin resultado desde la base de datos";
            return $resultado;
        } else {
            $resultado->datos = $this->crearArrayResultset();
            $resultado->numRegistros = sqlsrv_num_rows($this->lc_datos);
        }
        if ((count($errores) == 0) && (count($warnings) == 0)) {
            $resultado->estado = true;
        }

        $resultado->errores = $errores;
        $resultado->warnings = $warnings;

        return $resultado;
    }

    public function crearArrayResultset($tipo = SQLSRV_FETCH_ASSOC) {
        $resultados = [];
        $reg = array_map("utf8_encode", sqlsrv_fetch_array($this->lc_datos, $tipo));
        while ($reg) {
            $resultados[] = $reg;
            $reg = array_map("utf8_encode", sqlsrv_fetch_array($this->lc_datos, $tipo));
        }
        return $resultados;
    }


    public function fn_ejecutarquery3($lc_query) {
        $resultado = new stdClass();
        $resultado->estado = false;
        $lc_conec = $this->lc_conec->fn_conectarse();
        $errores = array();
        $warnings = array();
        $resultado->numRegistros = 0;

        if (!$lc_conec) {
            $e = sqlsrv_errors(SQLSRV_ERR_ERRORS);
            $w = sqlsrv_errors(SQLSRV_ERR_WARNINGS);
            if ($e) {
                array_push($errores, $e);
            }
            if ($w) {
                array_push($warnings, $w);
            }
            $resultado->errores = $errores;
            $resultado->warnings = $warnings;
            $resultado->datos = "Sin Conexion a la base de datos";
            return $resultado;
        }
        $this->lc_datos = sqlsrv_query($lc_conec, $lc_query, array(), array("Scrollable" => "buffered"));
        if (!is_resource($this->lc_datos)) {
            $e = sqlsrv_errors(SQLSRV_ERR_ERRORS);
            $w = sqlsrv_errors(SQLSRV_ERR_WARNINGS);
            if ($e) {
                array_push($errores, $e);
            }
            if ($w) {
                array_push($warnings, $w);
            }
            $resultado->errores = $errores;
            $resultado->warnings = $warnings;
            $resultado->datos = "Sin resultado desde la base de datos";
            return $resultado;
        } else {
            $resultado->datos = $this->crearArrayResultset2();
            $resultado->numRegistros = sqlsrv_num_rows($this->lc_datos);
        }
        if ((count($errores) == 0) && (count($warnings) == 0)) {
            $resultado->estado = true;
        }

        $resultado->errores = $errores;
        $resultado->warnings = $warnings;

        return $resultado;
    }

    /**
     * Funcion para consultar a sql server datos
     * @param  $lc_sql String SQL a ejecutar
     * @param  ...$values Valores a pasar en base al SPRINTF de php
     */
    function fn_consulta_generica_escalar($sql_secuencia, ...$values)
    {
        try {
            $sql_secuencia = sprintf($sql_secuencia, ...$values);
            $dataRows = [];
            if ($this->fn_ejecutarquery($sql_secuencia)) {
                while ($row = $this->fn_leerarreglo()) {
                    $dataRows[] = $row;
                }
            }
            if(!empty($dataRows)){
                return $dataRows[0];
            }
        } catch (\Exception $exception) {
        }
        return null;
    }


    public function crearArrayResultset2($tipo = SQLSRV_FETCH_ASSOC) {
        $resultados = [];
        $regTmp=sqlsrv_fetch_array($this->lc_datos,$tipo);
        $reg = is_array($regTmp)?array_map("utf8_encode",$regTmp):$regTmp;
        while ($reg) {
            $resultados[] = $reg;
            $regTmp=sqlsrv_fetch_array($this->lc_datos,$tipo);
            $reg = is_array($regTmp)?array_map("utf8_encode",$regTmp):$regTmp;
            //$reg = array_map("utf8_encode",sqlsrv_fetch_array($this->lc_datos,$tipo));
        }
        return $resultados;
    }

    public function ifNum($param){
        if(is_numeric($param)){
            return (float) $param;
        }else{
            return $param;
        }
    }

    public function mbConvertString($string, $codificacion = 'utf-8'){
        return mb_convert_encoding($string, $codificacion);
    }

}