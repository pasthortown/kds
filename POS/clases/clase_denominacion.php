<?php

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Jorge Tinoco ////////////////////////////////////////////
///////DESCRIPCION: Pantalla denominacion de billetes /////////////////////////
///////TABLAS INVOLUCRADAS: Billete_Denominacion, /////////////////////////////
///////FECHA CREACION: 20-02-2015 /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: 04/06/2015 //////////////////////////////////
///////USUARIO QUE MODIFICO: Daniel Llerena ///////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: Se incluyo la denominación de moneda , ///////
///////campo billete o moneda y se adapta alos nuevos estilos con Modales /////
///////////////////////////////////////////////////////////////////////////////


class categoria extends sql {

    //private $lc_regs;
    //constructor de la clase
    function __construct() {
        parent ::__construct();
    }

    //funcion que permite armar la sentencia sql de consulta

    function fn_consultar($lc_sqlQuery, $lc_datos) {
        switch ($lc_sqlQuery) {

            case "administrarDenominacionesBilletes":
                $lc_sql = "EXEC config.USP_administraciondenominacionbilletes " . $lc_datos[0] . ", '" . $lc_datos[2] . "', '" . utf8_decode(trim($lc_datos[3])) . "', " . $lc_datos[4] . ", '" . utf8_decode(trim($lc_datos[5])) . "', " . $lc_datos[6] . ", " . $lc_datos[1] . ",'" . $lc_datos[7] . "'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("btd_rank" => $row['btd_rank'],
                            "btd_id" => $row['btd_id'],
                            "btd_Descripcion" => $row['btd_Descripcion'],
                            "btd_Valor" => $row['btd_Valor'],
                            "btd_Tipo" => $row['btd_Tipo'],
                            "Simbolo" => $row['Simbolo'],
                            "std_id" => $row['std_id']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
        }
    }

}