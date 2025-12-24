<?php

////////////////////////////////////////////////////////////////////////
///////DESARROLLADO POR: Jorge Tinoco //////////////////////////////////
///////Fecha Creacion: 06/02/2016 //////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 05-01-2017////////////////////////////
///////USUARIO QUE MODIFICO: Juan Estévez///////////////////////////////
////////////////////////////////////////////////////////////////////////

class Movimiento extends sql {

    function _construct() {
        parent ::_construct();
    }

    function configuraciones($datos) {
        $query = "EXEC movimientos.EGRESOS_INGRESOS_configuraciones " . $datos[0] . ", " . $datos[1] . ",'".$datos[3]."'";
        $this->fn_ejecutarquery($query);
        while ($row = $this->fn_leerarreglo()) {
            $this->lc_regs[] = array(
                "idMotivo" => $row['idMotivo'],
                "descripcion" => utf8_encode($row['descripcion']),
                "signo" => $row['signo'],
                "localizacion" => $row['localizacion'],
                "ingresoCodigo" => $this->ifNum($row['ingresoCodigo']),
                "mensaje" => $row['mensaje']
            );
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function agregarMovimiento($datos) {
        $query = "EXEC movimientos.EGRESOS_INGRESOS_crearMovimiento " . $datos[0] . ", " . $datos[1] . ", '" . $datos[2] . "', '" . $datos[3] . "', '" . $datos[4] . "', " . $datos[5] . ", '" . $datos[6] . "', '" . $datos[7] . "', '" . $datos[8] . "', '" . $datos[9] . "'";
        $this->fn_ejecutarquery($query);
        $row = $this->fn_leerarreglo();
        $regs = array(
            "estado" => $row['estado'],
            "mensaje" => $row['mensaje']
        );
        return json_encode($regs);
    }
    
}