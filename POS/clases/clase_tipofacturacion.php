<?php

///////////////////////////////////////////////////////////////////////////////
//FECHA CREACION: 22/02/2016
//DESARROLLADOR: Daniel Llerena
//DESCRIPCION: Mantenimiento Tipo Facturacion
//FECHA ULTIMA MODIFICACION: 
///USUARIO QUE MODIFICO: 
//DECRIPCION ULTIMO CAMBIO: 
///////////////////////////////////////////////////////////////////////////////

class tipofacturacio extends sql {

    //constructor de la clase
    function __construct() {
        parent ::__construct();
    }

    //funcion que permite armar la sentencia sql de consulta

    function fn_consultar($lc_sqlQuery, $lc_datos) {
        switch ($lc_sqlQuery) {

            case "CargaTipoFacturacionXestado":
                $lc_sql = "EXEC [config].[USP_Tipo_Facturacion] $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("IDTipoFacturacion" => $row['IDTipoFacturacion'],
                            "tf_descripcion" => utf8_encode(trim($row['tf_descripcion'])),
                            "url" => utf8_encode(trim($row['url'])),
                            "estado" => utf8_encode(trim($row['estado'])));
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);

            case "AccionNuevo":
                $lc_sql = "EXEC [config].[IAE_Tipo_Facturacion] $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]', '$lc_datos[4]', $lc_datos[5]";
                $result = $this->fn_ejecutarquery($lc_sql);
                if ($result) {
                    return true;
                }else{
                    return false;
                }

            case "CargaModificarTipoFacturacion":
                $lc_sql = "EXEC [config].[USP_Tipo_Facturacion] $lc_datos[0], '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]'";
                if ($this->fn_ejecutarquery($lc_sql)) {
                    while ($row = $this->fn_leerarreglo()) {
                        $this->lc_regs[] = array("IDTipoFacturacion" => $row['IDTipoFacturacion'],
                            "tf_descripcion" => utf8_encode(trim($row['tf_descripcion'])),
                            "url" => utf8_encode(trim($row['url'])),
                            "estado" => $row['estado']);
                    }
                }
                $this->lc_regs['str'] = $this->fn_numregistro();
                return json_encode($this->lc_regs);
        }
    }

}