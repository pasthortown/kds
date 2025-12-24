<?php

class Cadenas extends sql {

    function cargarTokenSeguridadVitality($idCadena) {
        $lc_sql = "EXEC creditos.SEGURIDAD_consultarTokenVitality " . $idCadena;
        try {
            $this->fn_ejecutarquery($lc_sql);
            $row = $this->fn_leerarreglo();
            $this->lc_regs = array("claveSeguridad" => $row['claveSeguridad']);
            if ($this->fn_numregistro() > 0) {
                $result = $this->lc_regs;
            } else {
                $result = null;
            }
        } catch (Exception $e) {
            return $e;
        }
        return $result;
    }

    //En caso de no existir, primero se inserta
    function obtenerIdClienteExterno($idCiudad, $nombre, $apellido, $tipoDocumento, $documento, $telefono, $direccion, $email, $tipoCliente, $idUsuario) {
        $lc_sql = "EXEC creditos.I_clienteExterno $idCiudad, '$nombre', '$apellido', '$tipoDocumento', '$documento', '$telefono', '$direccion', '$email', '$tipoCliente', '$idUsuario'";

        try {
            $this->fn_ejecutarquery($lc_sql);

            $row = $this->fn_leerarreglo();

            if ($this->fn_numregistro() > 0 && isset($row['idCliente'])) {
                $this->lc_regs = array("idCliente" => $row["idCliente"]);
                $result = $this->lc_regs;
            } else {
                $result = null;
            }
        } catch (Exception $e) {
            return $e;
        }

        return $result;
    }


}
