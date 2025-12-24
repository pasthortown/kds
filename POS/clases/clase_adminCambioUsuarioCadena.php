<?php

/*
  FECHA CREACION   : 07/05/2018
  DESARROLLADO POR : Daniel Llerena
  DESCRIPCION      : Pantalla que realiza el cambio de cadena a usuarios MP
 */

class CambioUsuarioCadena extends sql {

    function __construct() {
        parent::__construct();
    }

    public function obtenerPerfiles($param) {
        $lc_sql = "EXECUTE [config].[USP_CambioUsuarioCadena] " . $param[0] . ", '" . $param[1] . "', '" . $param[2] . "', " . $param[3] . ", " . $param[4];
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "IDPerfil" => $row['IDPerfil']
                    , "perfil" => utf8_encode($row['perfil'])
                );
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    public function obtenerUsuarios($param) {
        $lc_sql = "EXECUTE [config].[USP_CambioUsuarioCadena] " . $param[0] . ", '" . $param[1] . "', '" . $param[2] . "', " . $param[3] . ", " . $param[4];
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "IDUsersPos" => $row['IDUsersPos']
                    , "usuario" => utf8_encode($row['usuario'])
                    , "descripcion" => utf8_encode($row['descripcion'])
                    , "telefono" => $row['telefono']
                    , "email" => utf8_encode($row['email'])
                    , "direccion" => utf8_encode($row['direccion'])
                    , "nombrePos" => utf8_encode($row['nombrePos'])
                    , "identificacion" => $row['identificacion']
                    , "perfil" => utf8_encode($row['perfil'])
                );
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    public function obtenerRestaurantesAsignados($param) {
        $lc_sql = "EXECUTE [config].[USP_CambioUsuarioCadena] " . $param[0] . ", '" . $param[1] . "', '" . $param[2] . "', " . $param[3] . ", " . $param[4];
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "idRestaurante" => $row['idRestaurante']
                    , "tienda" => utf8_encode($row['tienda'])
                );
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    public function obtenerCadena($param) {
        $lc_sql = "EXECUTE [config].[USP_CambioUsuarioCadena] " . $param[0] . ", '" . $param[1] . "', '" . $param[2] . "', " . $param[3] . ", " . $param[4];
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "IDCadena" => $row['IDCadena']
                    , "cadena" => utf8_encode($row['cadena'])
                );
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    public function obtenerRegion($param) {
        $lc_sql = "EXECUTE [config].[USP_CambioUsuarioCadena] " . $param[0] . ", '" . $param[1] . "', '" . $param[2] . "', " . $param[3] . ", " . $param[4];
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "idRegion" => $row['idRegion']
                    , "region" => utf8_encode($row['region'])
                );
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    public function obtenerRestaurantes($param) {
        $lc_sql = "EXECUTE [config].[USP_CambioUsuarioCadena] " . $param[0] . ", '" . $param[1] . "', '" . $param[2] . "', " . $param[3] . ", " . $param[4];
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "idRestaurante" => $row['idRestaurante']
                    , "restaurante" => utf8_encode($row['restaurante'])
                    , "region" => utf8_encode($row['region'])
                );
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    public function administracionUsuarioRestaurante($param) {
        $lc_sql = "EXECUTE [config].[IAE_CambioUsuarioCadena] " . $param[0] . ", '" . $param[1] . "', '" . $param[2] . "', '" . $param[3] . "'";
        $result = $this->fn_ejecutarquery($lc_sql);
        if ($result) {
            return true;
        }else{
            return false;
        }
    }

}