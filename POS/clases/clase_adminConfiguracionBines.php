<?php

/* * *******************************************************
 *          DESARROLLADO POR: Alex Merino                  *
 *          DESCRIPCION: Clase controlador empresa BBD     *
 *          FECHA CREACION: 16/04/2018                     *
 * ******************************************************* */

class bines extends sql {

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
            case 'lstBines':
                $lc_query = "EXECUTE [config].[USP_administracionbines] $lc_datos[0],$lc_datos[1],'$lc_datos[2]'";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "ID_ColeccionCadena" => $row['ID_ColeccionCadena'],
                            "id_colecciondedatoscadena" => $row['id_colecciondedatoscadena'],
                            "cdn_descripcion" => $row["cdn_descripcion"],
                            "fmp_descripcion" => $row['fmp_descripcion'],
                            "min" => $row['min'],
                            "max" => $row['max'],
                            "estado" => $row['estado']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;

            case 'infBines':
                $lc_query = "EXECUTE [config].[USP_administracionbines] $lc_datos[0],$lc_datos[1],'$lc_datos[2]'";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['ID_ColeccionCadena'] = $row["ID_ColeccionCadena"];
                        $this->lc_regs['id_colecciondedatoscadena'] = $row["id_colecciondedatoscadena"];
                        $this->lc_regs['IDFormapago'] = $row["IDFormapago"];
                        $this->lc_regs['cdn_descripcion'] = $row["cdn_descripcion"];
                        $this->lc_regs['fmp_descripcion'] = $row["fmp_descripcion"];
                        $this->lc_regs['min'] = $row["min"];
                        $this->lc_regs['max'] = $row["max"];
                        $this->lc_regs['estado'] = $row["estado"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;
            case 'infFormaPago':
                $lc_query = "EXECUTE [config].[USP_administracionbines] $lc_datos[0],$lc_datos[1],'$lc_datos[2]'";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "IDFormapago" => $row['IDFormapago'],
                            "fmp_descripcion" => $row['fmp_descripcion']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;
            case 'lsPoliticas':
                $lc_query = "EXECUTE [config].[USP_administracionbines] $lc_datos[0],$lc_datos[1],'$lc_datos[2]'";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['ID_ColeccionCadena'] = $row["ID_ColeccionCadena"];
                        $this->lc_regs['Descripcion'] = $row["Descripcion"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;
            case 'lstDefiniciones':
                $lc_query = "EXECUTE [config].[USP_administracionbines] $lc_datos[0],$lc_datos[1],'$lc_datos[2]'";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "ID_ColeccionDeDatosCadena" => $row['ID_ColeccionDeDatosCadena'],
                            "Descripcion" => $row['Descripcion']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;
            case 'validateMinimo':
                $lc_query = "EXECUTE [config].[USP_administracionbines] $lc_datos[0],$lc_datos[1],'$lc_datos[2]'";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['valor'] = $row["valor"];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                break;

            case 'validateMaximo':
                $lc_query = "EXECUTE [config].[USP_administracionbines] $lc_datos[0],$lc_datos[1],'$lc_datos[2]'";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['valor'] = $row["valor"];
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
        }
    }

    /*
     * Funcion para guardar nuevos datos o datos modificados para auto impresora del restaurante
     * @param type $lc_opcion
     * @param type $lc_datos
     * @return type
     */

    function fn_ingresarBines($lc_opcion, $lc_datos) {
        switch ($lc_opcion) {
            case 'guardarDatosModificadosBines':
                $lc_query = "EXECUTE [config].[IAE_administracionbines] $lc_datos[0],$lc_datos[1],'$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]','$lc_datos[6]','$lc_datos[7]','$lc_datos[8]'";
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
            case 'guardarNuevoBin':
                $lc_query = "EXECUTE [config].[IAE_administracionbines] $lc_datos[0],$lc_datos[1],'$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]','$lc_datos[6]','$lc_datos[7]','$lc_datos[8]'";
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
