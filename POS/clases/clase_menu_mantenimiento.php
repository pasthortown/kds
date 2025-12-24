<?php

///////////////////////////////////////////////////////////////////////////////
///////DESARROLLADOR: Darwin Mora//////////////////////////////////////////////
///////DESCRIPCION: Pantalla de configuración de menú /////////////////////////
///////TABLAS INVOLUCRADAS: Menu, /////////////////////////////////////////////
///////FECHA CREACION: 10-06-2015 /////////////////////////////////////////////
///////FECHA ULTIMA MODIFICACION: /////////////////////////////////////////////
///////USUARIO QUE MODIFICO: //////////////////////////////////////////////////
///////DECRIPCION ULTIMO CAMBIO: //////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////

class mantenimiento extends sql {

    //constructor de la clase
    function __construct() {
        parent ::__construct();
    }

    //funcion que permite armar la sentencia sql de consulta        
    function fn_capturarTrafico($lc_datos) {
        $lc_sql = "exec [seguridad].[AIE_capturatrafico] '" . $lc_datos[0] . "' , '" . $lc_datos[1] . "', " . $lc_datos[2];
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("ctra_id" => $row['ctra_id']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function fn_muestraTrafico($lc_datos) {
        $lc_sql = "exec [seguridad.AIE_capturatrafico] " . $lc_datos[0] . " , " . $lc_datos[1] . "," . $lc_datos[2];
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("ctra_id" => $row['ctra_id'],
                    "pnt_id" => $row['pnt_id'],
                    "pnt_Ruta" => $row['pnt_Ruta'],
                    "ctra_numero" => $row['ctra_numero'],
                    "pnt_Nombre_Mostrar" => utf8_encode(trim($row['pnt_Nombre_Mostrar'])));
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function fn_muestra_menusuperior($lc_datos) {
        $lc_sql = "exec [config].[IAE_Muestra_Menusuperior] " . $lc_datos[0];
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("pnt_id" => $row['pnt_id'],
                    "pnt_Imagen" => $row['pnt_Imagen'],
                    "pnt_Ruta" => utf8_encode(trim($row['pnt_Ruta'])),
                    "pnt_Nombre_Mostrar" => utf8_encode(trim($row['pnt_Nombre_Mostrar'])));
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function fn_muestra_restaurantes($lc_datos) {
        $lc_sql = "exec [seguridad].[IAE_consultarRestaurantesUsuarioCadena] '" . $lc_datos[0] . "' , '" . $lc_datos[1] . "'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("rst_id" => $row['rst_id'],
                    "cdn_id" => $row['cdn_id'],
                    "rst_cod_tienda" => utf8_encode($row['rst_cod_tienda']),
                    "rst_descripcion" => utf8_encode($row['rst_descripcion']));
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

    function fn_CargarSitioMaxpoint($lc_datos) {
        $lc_sql = "exec [config].[USP_CargarSitioMaxpoint] '" . $lc_datos[0] . "' , " . $lc_datos[1];
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array("rst_id" => $row['rst_id'],
                    "Sitio" => $row['Sitio']);
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }

}