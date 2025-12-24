<?php
////////////////////////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: CHRISTIAN PINTO///////////////////////////////////////////
///////////DESCRIPCION: CANALES DE IMPRESION, CREAR MODIFICAR CANAL DE IMPRESION////
/////////////////////// POR CADENA /////////////////////////////////////////////////
////////////////TABLAS: canal_impresion, cadena/////////////////////////////////////
////////FECHA CREACION: 18/06/2015//////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////


class adminwebservices extends sql
{
    //private $lc_regs;
    //constructor de la clase
    function __construct()
    {
        parent::__construct();
    }

    //funcion que permite armar la sentencia sql de consulta
    function cargarServidoresConfigurados($lc_cadena = 0)
    {
        $accion = 1;
        $lc_sql = "EXECUTE config.USP_administracionwebservices $accion,$lc_cadena,1";
        if (true == $this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $parametro = utf8_encode(trim($row['parametro']));
                $lc_regs[$parametro][] = utf8_encode(trim($row['valor']));
                $lc_regs[$parametro][] = utf8_encode(trim($row['idcoleccioncadena']));
                $lc_regs[$parametro][] = utf8_encode(trim($row['idcolecciondedatoscadena']));
            }
        }
        return ($lc_regs);
    }

    function cargarRutasConfiguradas($lc_cadena = 0)
    {
        $accion = 2;
        $lc_sql = "EXECUTE config.USP_administracionwebservices $accion,$lc_cadena,1";
        if (true == $this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $parametro = utf8_encode(trim($row['parametro']));
                $lc_regs[$parametro][] = utf8_encode(trim($row['valor']));
                $lc_regs[$parametro][] = utf8_encode(trim($row['idcoleccioncadena']));
                $lc_regs[$parametro][] = utf8_encode(trim($row['idcolecciondedatoscadena']));
            }
        }
        return ($lc_regs);
    }

    function cargarTipoAmbienteConfigurado($lc_cadena = 0)
    {
        $accion = 3;
        $lc_sql = "EXECUTE config.USP_administracionwebservices $accion,$lc_cadena,1";
        if (true == $this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $lc_regs["tipoambiente"] = utf8_encode(trim($row['tam_descripcion']));
            }
        }
        return ($lc_regs["tipoambiente"]);
    }

    function administrarColeccionWebService($lc_parametros)
    {
        $lc_sql = "EXECUTE [config].[IAE_AdministracionWebServices] 
                " . $lc_parametros['accion'] . ",
                '" . $lc_parametros['nombrecoleccion'] . "',
                '" . $lc_parametros['usuario'] . "',
                '" . $lc_parametros['idcolecciondedatoscadena'] . "',
                " . $lc_parametros['cadena'] . ",
                '" . $lc_parametros['nombrecolecciondedatoscadena'] . "',
                '" . $lc_parametros['valor'] . "',
                " . $lc_parametros['estado'];
        if (true == $this->fn_ejecutarquery($lc_sql)) {
            $row = $this->fn_leerarreglo();
            $lc_regs['estado'] = $row['estado'];
            $lc_regs['mensaje'] = $row['mensaje'];
            $lc_regs['error'] = $row['error'];
        }
        return $lc_regs;
    }
}
