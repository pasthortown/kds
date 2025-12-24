<?php

class Ambiente {

    public function cargarAmbiente() {
        $ambiente = array();
        try {
            $configuraciones = parse_ini_file('../../system/conexion/replica.ini', true);
            $ambiente['tipoambiente'] = $configuraciones['tipoambiente']['db.config.tipoambiente'];
        } catch (Exception $e) {
            $ambiente['tipoambiente'] = "0";
        }
        return json_encode($ambiente);
    }

}