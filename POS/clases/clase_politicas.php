<?php

class Politicas extends sql {

    function __construct() {
        parent ::__construct();
    }

    public function consultar($opcion, $datos) {
        $this->lc_regs = array();
        try {
            switch ($opcion) {
                case 'cargarPoliticasPorCadena':
                    $query = "EXEC seguridad.POLITICAS_cargaColeccionDinamico " . $datos[0] . ", '" . $datos[1] . "'";
                    if ($this->fn_ejecutarquery($query)) {
                        while ($row = $this->fn_leerarreglo()) {
                            $this->lc_regs[] = array("idColeccion" => $row['idColeccion'],
                                "descripcion" => utf8_encode($row['descripcion']),
                                "idModulo" => $row['idModulo'],
                                "modulo" => utf8_encode($row['modulo']),
                                "activo" => intval($row['activo']),
                                "descripcionIntegracion" => utf8_encode($row['descripcionIntegracion']),
                                "configuracion" => intval($row['configuracion']),
                                "reporte" => intval($row['reporte']),
                                "cubo" => intval($row['cubo']),
                                "repetirConfiguracion" => intval($row['repetirConfiguracion']),
                                "estado1" => intval($row['estado1']),
                                "estado2" => intval($row['estado2']),
                                "fechaModificado" => $row['fechaModificacion'],
                                "horaModificado" => $row['horaModificacion'],
                                "usuarioModifico" => utf8_encode($row['usuarioModifico']),
                                "idIntegracion" => $row['idIntegracion'],
                                "observaciones" => utf8_encode($row['observaciones'])
                            );
                        }
                    }
                    return json_encode($this->lc_regs);
                    break;
                case 'cargarParametrosPolitica':
                    $query = "EXEC seguridad.POLITICAS_cargaParametroDinamico " . $datos[2] . ", '" . $datos[0] . "', '" . $datos[1] . "'";
                    if ($this->fn_ejecutarquery($query)) {
                        while ($row = $this->fn_leerarreglo()) {
                            $this->lc_regs[] = array("idColeccion" => $row['idColeccion'],
                                "idParametro" => $row['idParametro'],
                                "descripcion" => utf8_encode($row['descripcion']),
                                "especificarValor" => intval($row['especificarValor']),
                                "obligatorio" => intval($row['obligatorio']),
                                "tipoDato" => $row['tipoDato'],
                                "descripcionTipoDato" => utf8_encode($row['descripcionTipoDato']),
                                "estado1" => intval($row['estado1']),
                                "estado2" => intval($row['estado2']),
                                "usuarioModifico" => utf8_encode($row['usuarioModifico']),
                                "fechaModificado" => $row['fechaModificado'],
                                "horaModificado" => $row['horaModificado'],
                                "idIntegracion" => $row['idIntegracion'],
                                "descripcionIntegracion" => $row['descripcionIntegracion'],
                                "activo" => intval($row['activo'])
                            );
                        }
                    }
                    return json_encode($this->lc_regs);
                    break;
                case 'cargarModulosConfiguracion':
                    $query = "EXEC seguridad.POLITICAS_cargarConfiguraciones " . $datos[0] . ", 0";
                    if ($this->fn_ejecutarquery($query)) {
                        while ($row = $this->fn_leerarreglo()) {
                            $this->lc_regs[] = array("idModulo" => $row['idModulo'], "descripcion" => utf8_encode($row['descripcion'])
                            );
                        }
                    }
                    return json_encode($this->lc_regs);
                    break;
                case 'updatePolitica':
                    $query = "EXEC seguridad.POLITICAS_modificarColeccionDinamico " . $datos[0] . ", '" . $datos[1] . "', '" . $datos[2] . "', '" . $datos[3] . "', '" . utf8_decode($datos[4]) . "', '" . $datos[5] . "', '" . $datos[6] . "', '" . $datos[7] . "', '" . $datos[8] . "', '" . $datos[9] . "', '" . $datos[10] . "', '" . $datos[11] . "', '" . $datos[12] . "', '" . utf8_decode($datos[13]) . "', '" . $datos[14] . "', '" . $datos[15] . "'";
                    if ($this->fn_ejecutarquery($query)) {
                        if ($row = $this->fn_leerarreglo()) {
                            $this->lc_regs = array("confirmar" => $row['confirmar']);
                        } else {
                            $this->lc_regs = array("confirmar" => 0);
                        }
                    }
                    return json_encode($this->lc_regs);
                    break;
                case 'createPolitica':
                    $query = "EXEC seguridad.POLITICAS_insertarColeccionDinamico " . $datos[0] . ", '" . $datos[1] . "', '" . $datos[2] . "', '" . $datos[3] . "', '" . utf8_decode($datos[4]) . "', '" . $datos[5] . "', '" . $datos[6] . "', '" . $datos[7] . "', '" . $datos[8] . "', '" . $datos[9] . "', '" . $datos[10] . "', '" . $datos[11] . "', '" . $datos[12] . "','" . utf8_decode($datos[13]) . "', '" . $datos[14] . "', '" . $datos[15] . "'";
                    if ($this->fn_ejecutarquery($query)) {
                        while ($row = $this->fn_leerarreglo()) {
                            $this->lc_regs[] = array("idColeccion" => $row['idColeccion'],
                                "descripcion" => utf8_encode($row['descripcion']),
                                "idModulo" => $row['idModulo'],
                                "modulo" => utf8_encode($row['modulo']),
                                "activo" => $row['activo'],
                                "descripcionIntegracion" => utf8_encode($row['descripcionIntegracion']),
                                "configuracion" => $row['configuracion'],
                                "reporte" => $row['reporte'],
                                "cubo" => $row['cubo'],
                                "repetirConfiguracion" => $row['repetirConfiguracion'],
                                "estado1" => $row['estado1'],
                                "estado2" => $row['estado2'],
                                "fechaModificacion" => $row['fechaModificacion'],
                                "horaModificacion" => $row['horaModificacion'],
                                "usuarioModifico" => utf8_encode($row['usuarioModifico']),
                                "idIntegracion" => $row['idIntegracion'],
                                "observaciones" => utf8_encode($row['observaciones'])
                            );
                        }
                    }
                    return json_encode($this->lc_regs);
                    break;
                case 'updateParametro':
                    $query = "EXEC seguridad.POLITICAS_modificarParametroDinamico " . $datos[0] . ", '" . $datos[1] . "', '" . $datos[2] . "', '" . $datos[3] . "', '" . $datos[4] . "', '" . utf8_decode($datos[5]) . "', '" . $datos[6] . "', '" . $datos[7] . "', '" . $datos[8] . "', '" . $datos[9] . "', '" . $datos[10] . "', '" . $datos[11] . "', '" . $datos[12] . "', '" . $datos[13] . "'";
                    if ($this->fn_ejecutarquery($query)) {
                        if ($row = $this->fn_leerarreglo()) {
                            $this->lc_regs = array("confirmar" => $row['confirmar']);
                        } else {
                            $this->lc_regs = array("confirmar" => 0);
                        }
                    }
                    return json_encode($this->lc_regs);
                    break;
                case 'createParametro':
                    $query = "EXEC seguridad.POLITICAS_insertarParametroDinamico " . $datos[0] . ", '" . $datos[1] . "', '" . $datos[2] . "', '" . $datos[3] . "', '" . $datos[4] . "', '" . utf8_decode($datos[5]) . "', '" . $datos[6] . "', '" . $datos[7] . "', '" . $datos[8] . "', '" . $datos[9] . "', '" . $datos[10] . "', '" . $datos[11] . "', '" . $datos[12] . "', '" . $datos[13] . "'";
                    if ($this->fn_ejecutarquery($query)) {
                        while ($row = $this->fn_leerarreglo()) {
                            $this->lc_regs[] = array("idColeccion" => $row['idColeccion'],
                                "idParametro" => $row['idParametro'],
                                "descripcion" => utf8_encode($row['descripcion']),
                                "especificarValor" => $row['especificarValor'],
                                "obligatorio" => $row['obligatorio'],
                                "tipoDato" => $row['tipoDato'],
                                "descripcionTipoDato" => utf8_encode($row['descripcionTipoDato']),
                                "estado1" => $row['estado1'],
                                "estado2" => $row['estado2'],
                                "usuarioModifico" => utf8_encode($row['usuarioModifico']),
                                "fechaModificado" => $row['fechaModificado'],
                                "horaModificado" => $row['horaModificado'],
                                "idIntegracion" => $row['idIntegracion'],
                                "descripcionIntegracion" => $row['descripcionIntegracion'],
                                "activo" => $row['activo']
                            );
                        }
                    }
                    return json_encode($this->lc_regs);
                    break;
                case 'cargarTablasIntegracion':
                    $query = "EXEC seguridad.POLITICAS_cargarConfiguraciones " . $datos[0] . ", 0";
                    if ($this->fn_ejecutarquery($query)) {
                        $this->lc_regs[] = array("tabla" => "-- Seleccione una Tabla --", "idTabla" => null);
                        while ($row = $this->fn_leerarreglo()) {
                            $this->lc_regs[] = array("tabla" => $row['tabla'], "idTabla" => $row['tabla']);
                        }
                    }
                    return json_encode($this->lc_regs);
                    break;
                case 'cargarIdTablasIntegracion':
                    $query = "EXEC seguridad.POLITICAS_cargarIdsTablaIntegracion '" . $datos[0] . "'";
                    if ($this->fn_ejecutarquery($query)) {
                        $this->lc_regs[] = array("descripcion" => "-- Seleccione una Fila --", "id" => null);
                        while ($row = $this->fn_leerarreglo()) {
                            $this->lc_regs[] = array("id" => $row['id'], "descripcion" => utf8_encode($row['descripcion']));
                        }
                    }
                    return json_encode($this->lc_regs);
                    break;
                case 'cargarTiposDatosParametros':
                    $query = "EXEC seguridad.POLITICAS_cargarConfiguraciones " . $datos[0] . ", " . $datos[1];
                    if ($this->fn_ejecutarquery($query)) {
                        while ($row = $this->fn_leerarreglo()) {
                            $this->lc_regs[] = array("tipoDato" => $row['tipoDato'], "descripcionTipoDato" => utf8_encode($row['descripcionTipoDato'])
                            );
                        }
                    }
                    return json_encode($this->lc_regs);
                    break;
            }
        } catch (Exception $e) {
            print json_encode($e);
        }
    }

}

