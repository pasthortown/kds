<?php

/* * *******************************************************
 *          DESARROLLADO POR: Alex Merino                  *
 *          DESCRIPCION: Clase controlador pais BBD        *
 *          FECHA CREACION: 06/06/2018                     *
 * ******************************************************* */

class pais extends sql {

    //constructor de la clase
    function __construct() {
        //con herencia 
        parent::__construct();
    }

    /**
     * Funcion principal para obtener datos de los procedimientos de la base de datos
     * @param type $lc_opcion
     * @param type $lc_datos
     * @return type
     */
    function fn_consultar($lc_opcion, $lc_datos) {
        switch ($lc_opcion) {
            case 'lstPais':
                $lc_query = "EXECUTE [config].[USP_administracionpais] $lc_datos[0],$lc_datos[1]";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "pais_id" => $row['pais_id'],
                            "pais_descripcion" => $row['pais_descripcion'],
                            "pais_desc_modeda" => $row['pais_desc_modeda'],
                            "pais_base_factura" => $row["pais_base_factura"],
                            "pais_moneda_simbolo" => $row['pais_moneda_simbolo']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;

            case 'infPais':
                $lc_query = "EXECUTE [config].[USP_administracionpais] $lc_datos[0],$lc_datos[1]";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['pais_id'] = $row["pais_id"];
                        $this->lc_regs['pais_descripcion'] = $row["pais_descripcion"];
                        $this->lc_regs['pais_desc_modeda'] = $row["pais_desc_modeda"];
                        $this->lc_regs['pais_base_factura'] = $row["pais_base_factura"];
                        $this->lc_regs['pais_moneda_simbolo'] = $row["pais_moneda_simbolo"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;

            case 'infConfiguracionPais':
                $lc_query = "EXECUTE [config].[USP_administracionpais] $lc_datos[0],$lc_datos[1]";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['pais_id'] = $row["pais_id"];
                        $this->lc_regs['emp_id'] = $row["emp_id"];
                        $this->lc_regs['pais_descripcion'] = $row["pais_descripcion"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;

            case 'infValorConfiguracion':
                $lc_query = "EXECUTE [config].[USP_administracionbines] $lc_datos[0],$lc_datos[1],'$lc_datos[2]'";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['valor'] = $row["variableI"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;

            case 'cargarColeccionPais':
                $lc_query = "EXECUTE [config].[USP_cargarPaisColeccion] $lc_datos[0],$lc_datos[1],'',''";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("IDColeccionPais" => $row['IDColeccionPais'],
                            "IDColeccionDeDatosPais" => $row['IDColeccionDeDatosPais'],
                            "descripcion_coleccion" => utf8_encode($row['descripcion_coleccion']),
                            "descripcion_dato" => utf8_encode($row['descripcion_dato']),
                            "especificarValor" => $row['especificarValor'],
                            "obligatorio" => $row['obligatorio'],
                            "tipodedato" => $row['tipodedato'],
                            "caracter" => utf8_encode($row['caracter']),
                            "entero" => $row['entero'],
                            "fecha" => $row['fecha'],
                            "bitt" => $row['bitt'],
                            "numerico" => $row['numerico'],
                            "fechaIni" => $row['fechaIni'],
                            "fechaFin" => $row['fechaFin'],
                            "min" => $row['min'],
                            "max" => $row['max'],
                            "isActive" => $row['isActive'],
                            "mdl_descripcion" => utf8_encode($row['mdl_descripcion']),
                            "mdl_id" => $row['mdl_id']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;

            case 'ListarColeccionxPais':
                $lc_query = "EXEC [config].[USP_cargarPaisColeccion] " . $lc_datos[0] . ", " . $lc_datos[1] . ", '" . $lc_datos[2] . "', '" . $lc_datos[3] . "'";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                while ($row = $this->fn_leerarreglo()) {
                    if ($lc_datos) {
                        $this->lc_regs[] = array("IDColeccionPais" => $row['IDColeccionPais'],
                            "Descripcion" => utf8_encode($row['Descripcion']),
                            "IDColeccionDeDatosPais" => $row['IDColeccionDeDatosPais'],
                            "datos" => utf8_encode($row['datos']),
                            "especificarValor" => $row['especificarValor'],
                            "obligatorio" => $row['obligatorio'],
                            "tipodedato" => $row['tipodedato'],
                            "caracter" => utf8_encode($row['caracter']),
                            "entero" => $row['entero'],
                            "fecha" => $row['fecha'],
                            "seleccion" => $row['seleccion'],
                            "numerico" => $row['numerico'],
                            "fechaInicio" => $row['fechaInicio'],
                            "fechaFin" => $row['fechaFin'],
                            "minimo" => $row['minimo'],
                            "maximo" => $row['maximo'],
                            "estado" => $row['estado'],
                            "mdl_descripcion" => utf8_encode($row['mdl_descripcion']),
                            "mdl_id" => $row['mdl_id']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;

            case 'modificarPaisColeccion':
                $lc_sql = "EXEC [config].[IAE_PaisColeccionDatos] " . $lc_datos[0] . ", '" . $lc_datos[1] . "', '" . $lc_datos[2] . "',  '" . $lc_datos[3] . "', 
                " . $lc_datos[4] . ", '" . $lc_datos[5] . "', " . $lc_datos[6] . ", " . $lc_datos[7] . ", '" . $lc_datos[8] . "', '" . $lc_datos[9] . "', " . $lc_datos[10] . ", " . $lc_datos[11] . ", '" . $lc_datos[12] . "', " . $lc_datos[13] . "";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $this->lc_regs['str'] = 1;
                } else {
                    $this->lc_regs['str'] = 0;
                }

                return json_encode($this->lc_regs);
                break;

            case 'detalleColeccionPais':
                $lc_sql = "EXEC [config].[USP_cargarPaisColeccionDatos] " . $lc_datos[0] . ", " . $lc_datos[1] . ", '0', '0'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("IDColeccionPais" => $row['IDColeccionPais'],
                            "Descripcion" => utf8_encode($row['Descripcion']),
                            "Observaciones" => utf8_encode($row['observaciones']));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;

            case 'datosColeccionPais':
                $lc_sql = "EXEC [config].[USP_cargarPaisColeccionDatos] " . $lc_datos[0] . ", " . $lc_datos[1] . ", '" . $lc_datos[2] . "', '0'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("IDColeccionPais" => $row['IDColeccionPais'],
                            "Descripcion" => utf8_encode($row['Descripcion']),
                            "IDColeccionDeDatosPais" => $row['IDColeccionDeDatosPais'],
                            "datos" => utf8_encode($row['datos']),
                            "especificarValor" => utf8_encode($row['especificarValor']),
                            "obligatorio" => utf8_encode($row['obligatorio']),
                            "tipodedato" => utf8_encode($row['tipodedato']));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;
            case 'insertarPaisColeccion':
                $lc_sql = "EXEC [config].[IAE_PaisColeccionDatos] " . $lc_datos[0] . ", '" . $lc_datos[2] . "', '" . $lc_datos[1] . "',
                  '" . $lc_datos[4] . "', " . $lc_datos[5] . ", '" . $lc_datos[6] . "', " . $lc_datos[7] . ", " . $lc_datos[8] . ",
                  '" . $lc_datos[9] . "', '" . $lc_datos[10] . "', " . $lc_datos[11] . ", " . $lc_datos[12] . ", '" . $lc_datos[13] . "', " . $lc_datos[14] . "";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $this->lc_regs['str'] = 1;
                } else {
                    $this->lc_regs['str'] = 0;
                }

                return json_encode($this->lc_regs);
                break;
        }
    }

    /*
     * Funcion para guardar nuevos datos o datos modificados para pais
     * @param type $lc_opcion
     * @param type $lc_datos
     * @return type
     */

    function fn_ingresarBines($lc_opcion, $lc_datos) {
        switch ($lc_opcion) {
            case 'guardarDatosPais':
                $lc_query = "EXECUTE [config].[IAE_administracionpais] $lc_datos[0],$lc_datos[1],'$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]'";
                if ($this->fn_ejecutarquery($lc_query)) {
                    $this->lc_regs['str'] = 1;
                } else {
                    $this->lc_regs['str'] = 0;
                }
                return json_encode($this->lc_regs);
        }
    }

}
