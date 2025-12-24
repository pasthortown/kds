<?php

///////////////////////////////////////////////////////////////////
////////DESARROLLADO POR: Alex Merino///////////////////////////////
////////DESCRIPCION: Clase para configuracion de autoimpresoras///
///////FECHA CREACION: 14-03-2018///////////////////////////////////
///////////////////////////////////////////////////////////////////

class autoimpresoras extends sql {

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
            case 'infRestaurantesCadena':
                $lc_query = "EXECUTE [config].[USP_administracionautoimpresoras] $lc_datos[0], $lc_datos[1],$lc_datos[2],'$lc_datos[3]'";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "rst_id" => $row['rst_id'],
                            "rst_cod_tienda" => $row['rst_cod_tienda'],
                            "rst_descripcion" => utf8_encode($row['rst_descripcion'])
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case "infRestaurante":
                $lc_query = "EXECUTE [config].[USP_administracionautoimpresoras] $lc_datos[0],$lc_datos[1],$lc_datos[2],'$lc_datos[3]'";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "orden" => $row['orden'],
                            "ID_ColeccionDeDatosRestaurante" => $row['ID_ColeccionDeDatosRestaurante'],
                            "ID_ColeccionRestaurante" => $row['ID_ColeccionRestaurante'],
                            "Descripcion" => utf8_encode($row['Descripcion']),
                            "especificarValor" => $row['especificarValor'],
                            "obligatorio" => $row['obligatorio'],
                            "tipodedato" => utf8_encode($row['tipodedato']),
                            "isActive" => $row['isActive'],
                            "dato" => utf8_encode($row['dato']),
                            "intDescripcion" => $row['intDescripcion']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;

            case 'infAutoImpresor':
                $lc_query = "EXECUTE [config].[USP_administracionautoimpresoras] $lc_datos[0],$lc_datos[1],$lc_datos[2],'$lc_datos[3]'";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs['orden'] = $row["orden"];
                        $this->lc_regs['ID_ColeccionDeDatosRestaurante'] = $row["ID_ColeccionDeDatosRestaurante"];
                        $this->lc_regs['ID_ColeccionRestaurante'] = $row["ID_ColeccionRestaurante"];
                        $this->lc_regs['Descripcion'] = utf8_encode($row['Descripcion']);
                        $this->lc_regs['especificarValor'] = $row["especificarValor"];
                        $this->lc_regs['obligatorio'] = $row["obligatorio"];
                        $this->lc_regs['tipodedato'] = utf8_encode($row["tipodedato"]);
                        $this->lc_regs['isActive'] = $row["isActive"];
                        $this->lc_regs['dato'] = utf8_encode($row["dato"]);
                        $this->lc_regs['intDescripcion'] = $row['intDescripcion'];
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                }
                return json_encode($this->lc_regs);
                $this->fn_liberarecurso();
                break;
            case 'loadModalNuevo':
                $lc_query = "EXECUTE [config].[USP_administracionautoimpresoras] $lc_datos[0],$lc_datos[1],$lc_datos[2],'$lc_datos[3]'";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "titulo" => utf8_encode($row['titulo']),
                            "Descripcion" => utf8_encode($row['Descripcion']),
                            "descripcionCompleta" => utf8_encode($row['descripcionCompleta']),
                            "tipodedato" => $row['tipodedato'],
                            "obligatorio" => $row['obligatorio']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
                break;
            case 'infAutoimpresorDatos':
                $lc_query = "EXECUTE [config].[USP_administracionautoimpresoras] $lc_datos[0],$lc_datos[1],$lc_datos[2],'$lc_datos[3]'";
                $lc_datos = $this->fn_ejecutarquery($lc_query);
                if ($lc_datos) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array(
                            "intDescripcion" => utf8_encode($row['intDescripcion']),
                            "orden" => utf8_encode($row['orden']),
                            "Descripcion" => utf8_encode($row['Descripcion']),
                            "valor" => $row['valor']
                        );
                    }
                    $this->lc_regs['str'] = $this->fn_numregistro();
                    return json_encode($this->lc_regs);
                }
                $this->fn_liberarecurso();
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
            case 'guardarDatosModificarAutoImpresoras':
                $lc_query = "EXECUTE [config].[IAE_administracionguardarautoimpresoras] '$lc_datos[0]','$lc_datos[1]','$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]','$lc_datos[6]','$lc_datos[7]'";
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
            case 'guardarDatosAutoImpresoras':
                $lc_query = "EXECUTE [config].[IAE_administracionAutoImpresor] '$lc_datos[0]','$lc_datos[1]','$lc_datos[2]','$lc_datos[3]','$lc_datos[4]','$lc_datos[5]','$lc_datos[6]'";
                //echo 'SQL: ' . $lc_query;
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
