<?php

class CambioDatosCliente extends sql {

    function _construct() {
        parent ::_construct();
    }
    
    function fn_obtenerDatosClientes($lc_datos){
        $lc_sql = "exec [facturacion].[TRANSACCIONES_USP_NotaCreditoCambioDatosCliente] '$lc_datos[0]', '$lc_datos[1]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "IDCliente" => $row['IDCliente'],
                    "TipoDocumento" => $row['TipoDocumento'],
                    "Documento" => $row['Documento'],
                    "Cliente" => utf8_encode($row['Cliente']),
                    "Telefono" => $row['Telefono'],
                    "Direccion" => utf8_encode($row['Direccion']),
                    "Email" => $row['Email'],
                    "Factura" => $row['Factura'],
                    "tipoCliente" => $row['tipoCliente']
                );
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }
        
    function fn_duplicarFacturaActual($lc_datos){
        $lc_sql = "EXEC [facturacion].[TRANSACCIONES_IAE_NotaCreditoCambioDatosCliente] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs['NuevaFactura'] = $row["NuevaFactura"];
            }
        }
        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);
    }
    
    function fn_anulacionFormasPago($lc_datos){
        $lc_sql = "EXEC [facturacion].[TRANSACCIONES_IAE_NotaCreditoCambioDatosCliente] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]'";
        //todo: Guillermo verificar si se debe retornar algo
        if ($this->fn_ejecutarquery($lc_sql)) {
            $this->lc_regs['Respuesta'] = 1;
        } else {
            $this->lc_regs['Respuesta'] = 0;
        }
        return json_encode($this->lc_regs);
    }
    
    function fn_crearNotaDeCredito($lc_datos) {
        $lc_sql = "EXEC [facturacion].[TRANSACCIONES_IAE_NotaCreditoCambioDatosCliente] '$lc_datos[0]', '$lc_datos[1]', '$lc_datos[2]', '$lc_datos[3]'";
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs['cfac_id'] = $row["cfac_id"];
                $this->lc_regs['tf_descripcion'] = utf8_encode($row["tf_descripcion"]);
                if($lc_datos[0] ==  'N'){
                    $this->lc_regs['aplicaEnEstacion'] = $row["aplicaEnEstacion"];
                    $this->lc_regs['idEstacion'] = $row["idEstacion"];
                    $this->lc_regs['servidorUrlApi'] = $row["servidorUrlApi"];
                }
            }
            $this->lc_regs['str'] = 1;
        }else{
            $this->lc_regs['str'] = 0;
        }
        
        return json_encode($this->lc_regs);
    }
    
    function informacion_sri($lc_datos) {
        $lc_sql = "EXEC [facturacion].[USP_Informacion_SRI] $lc_datos[0]";
        
        if ($this->fn_ejecutarquery($lc_sql)) {
            while ($row = $this->fn_leerarreglo()) {
                $this->lc_regs[] = array(
                    "campo" => $row['campo'],
                    "dato" => utf8_encode($row['dato'])                    
                );
            }
        }

        $this->lc_regs['str'] = $this->fn_numregistro();
        return json_encode($this->lc_regs);    
    }
}


