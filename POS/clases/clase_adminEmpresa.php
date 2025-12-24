<?php

/* * *******************************************************
 *          DESARROLLADO POR: Alex Merino                  *
 *          DESCRIPCION: Clase controlador empresa BBD     *
 *          FECHA CREACION: 14/04/2018                     *
 * ******************************************************* */

class empresa extends sql {

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
    function fn_consultar($lc_opcion, $lc_datos)
    {
        switch ($lc_opcion) {
            case 'lstEmpresas':
                $lc_query = "EXECUTE [config].[USP_administracionempresa] $lc_datos[0],$lc_datos[1]";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "emp_id" => $row['emp_id'],
                            "emp_ruc" => $row['emp_ruc'],
                            "emp_nombre" => $row['emp_nombre'],
                            "emp_fechainicio" => $row['emp_fechainicio'],
                            "emp_fechafin" => $row['emp_fechafin'],
                            "emp_ciudad" => $row['emp_ciudad'],
                            "emp_direccion" => $row['emp_direccion'],
                            "emp_razon_social" => $row['emp_razon_social'],
                            "emp_fono" => $row['emp_fono'],
                            "emp_tipo_contribuyente" => $row['emp_tipo_contribuyente'],
                            "emp_resolucion" => $row['emp_resolucion'],
                            "emp_fecha_resolucion" => $row['fecha_resolucion'],
                            "estado" => $row['estado']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;

            case 'infPais':
                $lc_query = "EXECUTE [config].[USP_administracionempresa] $lc_datos[0],$lc_datos[1]";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['pais_id'] = $row["pais_id"];
                        $this->lc_regs['pais_descripcion'] = $row["pais_descripcion"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;
            case 'infTipoAmbiente':
                $lc_query = "EXECUTE [config].[USP_administracionempresa] $lc_datos[0],$lc_datos[1]";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "IDTipoAmbiente" => $row['IDTipoAmbiente'],
                            "tam_descripcion" => $row['tam_descripcion']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;
            case 'infTipoEmision':
                $lc_query = "EXECUTE [config].[USP_administracionempresa] $lc_datos[0],$lc_datos[1]";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "tem_id" => $row['tem_id'],
                            "tem_descripcion" => $row['tem_descripcion']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;
            case 'infEmpresa':
                $lc_query = "EXECUTE [config].[USP_administracionempresa] $lc_datos[0],$lc_datos[1]";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['emp_id'] = $row["emp_id"];
                        $this->lc_regs['emp_ruc'] = $row["emp_ruc"];
                        $this->lc_regs['emp_nombre'] = $row["emp_nombre"];
                        $this->lc_regs['pais_id'] = $row["pais_id"];
                        $this->lc_regs['emp_fechainicio'] = $row["emp_fechainicio"];
                        $this->lc_regs['emp_fechafin'] = $row["emp_fechafin"];
                        $this->lc_regs['emp_ciudad'] = $row["emp_ciudad"];
                        $this->lc_regs['emp_direccion'] = $row["emp_direccion"];
                        $this->lc_regs['emp_razon_social'] = $row["emp_razon_social"];
                        $this->lc_regs['emp_fono'] = $row["emp_fono"];
                        $this->lc_regs['emp_tipo_contribuyente'] = $row["emp_tipo_contribuyente"];
                        $this->lc_regs['emp_resolucion'] = $row["emp_resolucion"];
                        $this->lc_regs['fecha_resolucion'] = $row["fecha_resolucion"];
                        $this->lc_regs['estado'] = $row["estado"];
                        $this->lc_regs['tem_id'] = $row["tem_id"];
                        $this->lc_regs['IDTipoAmbiente'] = $row["IDTipoAmbiente"];
                        $this->lc_regs['tam_descripcion'] = $row["tam_descripcion"];
                        $this->lc_regs['tem_descripcion'] = $row["tem_descripcion"];
                        $this->lc_regs['emp_obligado_contabilidad'] = $row["emp_obligado_contabilidad"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;

            case 'cargarColeccionEmpresa':
                $lc_query = "EXECUTE [config].[USP_cargarEmpresaColeccionDatos] $lc_datos[0],$lc_datos[1],'',''";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("ID_ColeccionEmpresa" => $row['ID_ColeccionEmpresa'],
                            "ID_ColeccionDeDatosEmpresa" => $row['ID_ColeccionDeDatosEmpresa'],
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
                            "isActive" => $row['isActive']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;

            case 'ListarColeccionEmpresa':
                $lc_query = "EXEC [config].[USP_cargarEmpresaColeccionDatos] " . $lc_datos[0] . ", " . $lc_datos[1] . ", '" . $lc_datos[2] . "', '" . $lc_datos[3] . "'";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                while ($row = $this->fn_leerarreglo()) {
                    if ($lc_datos) {
                        $this->lc_regs[] = array("ID_ColeccionEmpresa" => $row['ID_ColeccionEmpresa'],
                            "Descripcion" => utf8_encode($row['Descripcion']),
                            "ID_ColeccionDeDatosEmpresa" => $row['ID_ColeccionDeDatosEmpresa'],
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
                            "estado" => $row['estado']);
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;

            case 'modificarEmpresaColeccion':
                $lc_sql = "EXEC [config].[IAE_EmpresaColeccionDatos] " . $lc_datos[0] . ", '" . $lc_datos[1] . "', '" . $lc_datos[2] . "',  '" . $lc_datos[3] . "', 
                " . $lc_datos[4] . ", '" . $lc_datos[5] . "', " . $lc_datos[6] . ", " . $lc_datos[7] . ", '" . $lc_datos[8] . "', '" . $lc_datos[9] . "', " . $lc_datos[10] . ", " . $lc_datos[11] . ", '" . $lc_datos[12] . "', " . $lc_datos[13] . "";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $this->lc_regs['str'] = 1;
                } else {
                    if (($errors = sqlsrv_errors()) != null) {
                        foreach ($errors as $error) {
                            $this->lc_regs['str'] = utf8_encode($error['message']);
                        }
                    }
                }
                return json_encode($this->lc_regs);
                break;

            case 'detalleColeccionEmpresa':
                $lc_sql = "EXEC [config].[USP_cargarEmpresaColeccionDatos] " . $lc_datos[0] . ", " . $lc_datos[1] . ", '0', '0'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("ID_ColeccionEmpresa" => $row['ID_ColeccionEmpresa'],
                            "Descripcion" => utf8_encode($row['Descripcion']));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;

            case 'datosColeccionEmpresa':
                $lc_sql = "EXEC [config].[USP_cargarEmpresaColeccionDatos] " . $lc_datos[0] . ", " . $lc_datos[1] . ", '" . $lc_datos[2] . "', '0'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("ID_ColeccionEmpresa" => $row['ID_ColeccionEmpresa'],
                            "Descripcion" => utf8_encode($row['Descripcion']),
                            "ID_ColeccionDeDatosEmpresa" => $row['ID_ColeccionDeDatosEmpresa'],
                            "datos" => utf8_encode($row['datos']),
                            "especificarValor" => utf8_encode($row['especificarValor']),
                            "obligatorio" => utf8_encode($row['obligatorio']),
                            "tipodedato" => utf8_encode($row['tipodedato']));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
                break;
            case 'insertarEmpresaColeccion':
                $lc_sql = "EXEC [config].[IAE_EmpresaColeccionDatos] " . $lc_datos[0] . ", '" . $lc_datos[2] . "', '" . $lc_datos[1] . "',
                  '" . $lc_datos[4] . "', " . $lc_datos[5] . ", '" . $lc_datos[6] . "', " . $lc_datos[7] . ", " . $lc_datos[8] . ",
                  '" . $lc_datos[9] . "', '" . $lc_datos[10] . "', " . $lc_datos[11] . ", " . $lc_datos[12] . ", '" . $lc_datos[13] . "', " . $lc_datos[14] . "";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    $this->lc_regs['str'] = 1;
                } else {
                    if (($errors = sqlsrv_errors()) != null) {
                        foreach ($errors as $error) {
                            $this->lc_regs['str'] = utf8_encode($error['message']);
                        }
                    }
                }
                return json_encode($this->lc_regs);
                break;

        }
    }


    /*
     * Funcion para guardar nuevos datos o datos modificados para auto impresora del restaurante
     * @param type $lc_opcion
     * @param type $lc_datos
     * @return type
     */

    function fn_ingresarAutoImpresora($lc_opcion, $lc_datos) {
        switch ($lc_opcion) {
            case 'guardarDatosModificadosEmpresa':
                if ($lc_datos[12] === 'NULL' || $lc_datos[13] === 'NULL') {
                    $lc_query = "EXECUTE [config].[IAE_administracionmodificarempresa] $lc_datos[0],'$lc_datos[1]','$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]','$lc_datos[6]','$lc_datos[7]','$lc_datos[8]','$lc_datos[9]','$lc_datos[10]',$lc_datos[11],$lc_datos[12],$lc_datos[13],'$lc_datos[14]'";
                } else {
                    $lc_query = "EXECUTE [config].[IAE_administracionmodificarempresa] $lc_datos[0],'$lc_datos[1]','$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]','$lc_datos[6]','$lc_datos[7]','$lc_datos[8]','$lc_datos[9]','$lc_datos[10]',$lc_datos[11],'$lc_datos[12]','$lc_datos[13]','$lc_datos[14]'";
                }
                if ($this->fn_ejecutarquery($lc_query)) {
                    $this->lc_regs['str'] = 1;
                } else {
                    if (($errors = sqlsrv_errors() ) != null) {
                        foreach ($errors as $error) {
                            $this->lc_regs['str'] = utf8_encode($error['message']);
                        }
                    }
                }
                return json_encode($this->lc_regs);
        }
    }


}
